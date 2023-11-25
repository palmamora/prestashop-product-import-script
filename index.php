<?php
//imports
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//define constants
define('API_KEY', $_ENV['API_KEY']);
define('API_URL', $_ENV['API_URL']);
define('IMAGES_FOLDER', $_ENV['IMAGES_FOLDER']);

//get post data and decode into associative array
$data = file_get_contents('php://input');
$data = json_decode($data, true);

//default attributes 
$manufacturerId = 4;
$supplierId = 1;
$languageId = 2;
$categoryId = 20;

$categories = [
    0 => 20,
    1 => 10,
    2 => 11,
    3 => 12,
    4 => 13,
    5 => 14,
    6 => 15,
    7 => 16,
    8 => 17,
    9 => 18,
    10 => 19
];

$result = [];
// $product = $webService->get([
//     'resource' => 'products',
//     'id' => $p->id,
// ]);

$webService = new PrestaShopWebservice(API_URL, API_KEY, false);

if (!is_null($data)) {
    //get id list of products of store
    $xml = $webService->get([
        'resource' => 'products',
        'display' => '[id, name, description, description_short, price, active, type, product_type, reference]'
    ]);
    $publishedProducts = $xml->children()->children();
    
    $cont = 0;

    foreach ($data as $key => $newProduct) {
        if ($newProduct['archivo'] == 'NN') {
            //continue;
        }
        /*campos: id, codigo, producto, unidad, precio, imagen, deleted, activo, archivo, cantidad, oferta, categoria, porcdescuento*/
        //search new product on published products
        $exists = 0;
        foreach ($publishedProducts as $publishedProduct) {
            if ($publishedProduct->reference == $newProduct['id']) {
                $exists = 1;
            }
        } //end foreach

        //if the product not exists, then create the product, if exists then update.
        if ($exists == 0) {
            echo 'producto no existe...creando';
            $id = createProduct(1, $newProduct['id'], $newProduct['precio'], $newProduct['activo'], 1, $newProduct['activo'], $newProduct['producto'], $newProduct['producto'], $newProduct['codigo'], $newProduct['categoria']);
            //if there is a image, then upload the image and associate it to the new product id.
            echo 'producto creado.';
            if (!is_null($id) && $newProduct['archivo'] !== 'NN' && isset($newProduct['imagen']) && !is_null($newProduct['imagen'])) {
                //uploadImg($id, IMAGES_FOLDER, 'image/jpg', $newProduct['imagen']);
            }
            $result[$newProduct['id']] = 'CREATED ' . $newProduct['id'];
        } else if ($exists == 1) {
            //code for update
            $result[$newProduct['id']] = 'UPDATED ' . $newProduct['id'];
        }
    } //end foreach

    var_dump($result);
}


if (is_null($data)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'null data']);
    exit;
}

function createProduct(
    $new = 1, $reference = 0, $price = 1000, $active = 1, $show_price = 1, $state = 1, $name = 'Producto sin nombre', $description = 'Producto sin descripci贸n', $resume = '', $category = 0) {
    global $webService, $manufacturerId, $supplierId, $categoryId, $categories;

    $blankXml = $webService->get(['url' => API_URL . '/api/products?schema=blank']);
    echo 'acá1';
    $fields = $blankXml->children()->children();
    echo 'acá2';
    $fields->id;
    $fields->id_manufacturer = $manufacturerId;
    $fields->id_supplier = $supplierId;
    $fields->id_category_default = $categoryId;
    $fields->new = $new;
    $fields->cache_default_attribute;
    $fields->id_default_image = 1;
    $fields->id_default_combination;
    $fields->id_tax_rules_group;
    //$fields->position_in_category = 1;
    $fields->type = 'virtual';
    $fields->id_shop_default;
    $fields->reference = $reference;
    $fields->supplier_reference;
    $fields->location;
    $fields->width;
    $fields->height;
    $fields->depth;
    $fields->weight;
    $fields->quantity_discount;
    $fields->ean13;
    $fields->upc;
    $fields->cache_is_pack;
    $fields->cache_has_attachments;
    $fields->is_virtual;
    $fields->on_sale;
    $fields->online_only = 1;
    $fields->ecotax;
    $fields->minimal_quantity;
    $fields->price = $price;
    $fields->wholesale_price;
    $fields->unity;
    $fields->unit_price_ratio;
    $fields->additional_shipping_cost;
    $fields->customizable;
    $fields->text_fields;
    $fields->uploadable_files;
    $fields->active = $active;
    $fields->redirect_type;
    $fields->id_product_redirected;
    $fields->available_for_order = 1;
    $fields->available_date;
    $fields->condition;
    $fields->show_price = $show_price;
    $fields->indexed;
    $fields->visibility;
    $fields->advanced_stock_management;
    $fields->date_add;
    $fields->date_upd;
    $fields->state = $state;

    $meta_description = $fields->meta_description->addChild('language', 'description');
    $meta_description->addAttribute('id', 2);

    $meta_keywords = $fields->meta_keywords->addChild('language', 'keywords');
    $meta_keywords->addAttribute('id', 2);

    $meta_title = $fields->meta_title->addChild('language', 'metatitle');
    $meta_title->addAttribute('id', 2);

    $link_rewrite = $fields->link_rewrite->addChild('language', 'my-product');
    $link_rewrite->addAttribute('id', 2);

    $name = $fields->name->addChild('language', $name);
    $name->addAttribute('id', 2);

    $description = $fields->description->addChild('language', $description);
    $description->addAttribute('id', 2);

    $description_short = $fields->description_short->addChild('language', $resume);
    $description_short->addAttribute('id', 2);

    $category = $fields->associations->categories->addChild('category')->addChild('id', $categories[$category]);

    //$product_feature = $fields->associations->product_features->addChild('product_feature');
    //$tags = $fields->associations->tags->addChild('tag', 'zapatillas');
    echo 'acá3';
    $createdXml = $webService->add([
        'resource' => 'products',
        'postXml' => $blankXml->asXML(),
    ]);
    echo 'acá4';
    $newProduct = $createdXml->children()->children();
    var_dump($createdXml);
    echo 'acá5';
    echo 'producto creado con id-> ' . $newProduct->id;
    return $newProduct->id;
}

function uploadImg($productId, $imagePath = IMAGES_FOLDER, $imageMime = 'image/jpg', $imageBase64 = '')
{
    /**
     * Auxiliar function to upload image resources to prestashop.
     *
     * @return void
     */

    $apiUrl = API_URL . '/api/images/products/' . $productId;
    //$imageContent = file_get_contents($imagePath);
    //$imageBase64 = base64_encode($imageContent);
    $imageData = base64_decode($imageBase64);
    $tempFilePath = __DIR__ . '/temp_img.jpg';

    file_put_contents($tempFilePath, $imageData);

    //$args['image'] = new CURLFile($imagePath, $imageMime);
    $args['image'] = new CURLFile($tempFilePath, $imageMime);
    $ch = curl_init();

    $options = [
        CURLOPT_HEADER => 1,
        CURLOPT_RETURNTRANSFER => true,
        CURLINFO_HEADER_OUT => 1,
        CURLOPT_URL => $apiUrl,
        CURLOPT_POST => 1,
        CURLOPT_USERPWD =>  API_KEY . ':',
        CURLOPT_POSTFIELDS => $args,
    ];

    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);

    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    unlink($tempFilePath);

    if (200 == $httpCode) {
        return true;
    } else {
        return false;
    }
}

function getMimeType($path)
{
    $extension = pathinfo($path, PATHINFO_EXTENSION);
    $extension = strtolower($extension);

    $mimeTypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        // agrega m谩s extensiones y MIME types seg煤n sea necesario.
    ];

    // verifica si la extensi贸n existe en el mapeo.
    if (array_key_exists($extension, $mimeTypes)) {
        // obtiene el MIME type correspondiente
        $mime_type = $mimeTypes[$extension];
        return $mime_type;
    } else {
        return $mimeTypes['jpg'];
    }
}
