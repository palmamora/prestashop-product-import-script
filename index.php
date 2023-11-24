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
$categoryId = 10;

if ($data !== null) {
    $webService = new PrestaShopWebservice(API_URL, API_KEY, false);

    //get id list of products of store
    $xml = $webService->get(['resource' => 'products', 'display' => '[id, name, description, description_short, price, active, type, product_type]']);
    $products = $xml->products->children();
    $products_arr = [];


    foreach ($data as $key => $producto) {
        continue;
        $producto['id'];
        $producto['codigo'];
        $producto['producto'];
        $producto['unidad'];
        $producto['precio'];
        $producto['imagen'];
        $producto['deleted'];
        $producto['activo'];
        $producto['archivo'];
        $producto['cantidad'];
        $producto['oferta'];
        $producto['categoria'];
        $producto['porcdescuento'];
    }

    foreach ($products as $p) {
        //$attributes = $p->attributes();

        $product = $webService->get([
            'resource' => 'products',
            'id' => 49,
        ]);

        $productFields = $product->children()->children();
        //var_dump($productFields);
        //exit;

        // echo 'id->' . $productFields->id;
        // echo 'id_manufacturer->' . $productFields->id_manufacturer;
        // echo 'id_supplier->' . $productFields->id_supplier;
        // echo 'id_category_default->' . $productFields->id_category_default;
        // echo 'position_in_category->' . $productFields->position_in_category;
        // echo 'price->' . $productFields->price;
        // echo 'product_type->' . $productFields->product_type;
        // echo 'language->' . $productFields->name->language;
        // echo 'language->' . $productFields->description->language;
        // echo 'online_only->' . $productFields->online_only;
        // echo 'active->' . $productFields->active;
        // echo 'product_feature->' . $productFields->associations->product_features->product_feature->id;
        // echo 'product_feature_value->' . $productFields->associations->product_features->product_feature->id_feature_value;
        // echo '<hr>';
    }

    //post new product
    $blankXml = $webService->get(['url' => API_URL . '/api/products?schema=blank']);
    $fields = $blankXml->children()->children();

    $fields->id;
    $fields->id_manufacturer = $manufacturerId;
    $fields->id_supplier = $supplierId;
    $fields->id_category_default = $categoryId;
    $fields->new;
    $fields->cache_default_attribute;
    $fields->id_default_image = 1;
    $fields->id_default_combination;
    $fields->id_tax_rules_group;
    $fields->position_in_category = 1;
    $fields->type = 'virtual';
    $fields->id_shop_default;
    $fields->reference = 'ref_product';
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
    $fields->price = 2000;
    $fields->wholesale_price;
    $fields->unity;
    $fields->unit_price_ratio;
    $fields->additional_shipping_cost;
    $fields->customizable;
    $fields->text_fields;
    $fields->uploadable_files;
    $fields->active = 1;
    $fields->redirect_type;
    $fields->id_product_redirected;
    $fields->available_for_order = 1;
    $fields->available_date;
    $fields->condition;
    $fields->show_price = 1;
    $fields->indexed;
    $fields->visibility;
    $fields->advanced_stock_management;
    $fields->date_add;
    $fields->date_upd;
    $fields->state = '1';

    $meta_description = $fields->meta_description->addChild('language', 'description');
    $meta_description->addAttribute('id', 2);

    $meta_keywords = $fields->meta_keywords->addChild('language', 'keywords');
    $meta_keywords->addAttribute('id', 2);

    $meta_title = $fields->meta_title->addChild('language', 'metatitle');
    $meta_title->addAttribute('id', 2);

    $link_rewrite = $fields->link_rewrite->addChild('language', 'my-product');
    $link_rewrite->addAttribute('id', 2);

    $name = $fields->name->addChild('language', 'Product Name');
    $name->addAttribute('id', 2);

    $description = $fields->description->addChild('language', 'Product description');
    $description->addAttribute('id', 2);

    $description_short = $fields->description_short->addChild('language', 'Resume');
    $description_short->addAttribute('id', 2);

    $category = $fields->associations->categories->addChild('category')->addChild('id', '10');

    $product_feature = $fields->associations->product_features->addChild('product_feature');
    $tags = $fields->associations->tags->addChild('tag', 'zapatillas');

    $createdXml = $webService->add([
        'resource' => 'products',
        'postXml' => $blankXml->asXML(),
    ]);

    $newProductFields = $createdXml->children()->children();
    //echo 'product created with id ' . $newProductFields->id;
    var_dump($newProductFields);
    exit;

} else {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'null data']);
}

function uploadImg($productId, $imagePath = IMAGES_FOLDER, $imageMime = 'image/jpg')
{
    /**
     * Auxiliar function to upload image resources to prestashop.
     *
     * @return void
     */

    $apiUrl = API_URL . '/api/images/products/' . $productId;
    $imgContent = file_get_contents($imagePath);
    $imgBase64 = base64_encode($imgContent);
    $imgData = base64_decode($imgBase64);
    $tempFilePath = __DIR__ . '/temp_img.jpg';

    file_put_contents($tempFilePath, $imgData);

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
        echo 'Product image was successfully created.';
    } else {
        echo 'error: ' . $httpCode;
    }
}
