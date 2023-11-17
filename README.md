# PrestaShop Product Import Script (Testing Purposes) v1.0.0-beta

**Note: This script is intended for testing purposes and may contain errors. Use it in a controlled environment and review the code before deploying in a production setting.**

This PHP script is designed to import products into a PrestaShop store using the PrestaShop Web Service API. It leverages the `prestashop-webservice-lib` library for seamless interaction with the PrestaShop Web Service API, allowing the creation of products via the API using the HTTP POST method. The script also includes an auxiliary function for uploading product images.

## Overview

1. **Receiving Product Data:**
   - The script starts by listening for a POST request, extracting product data from the request body.

2. **Fetching Existing Products:**
   - It then communicates with the PrestaShop store to retrieve a list of existing products using the Web Service API.

3. **Creating a Blank XML Schema:**
   - The script obtains a blank XML schema for a new product using the API. This schema serves as a template that will be populated with the new product's data.

4. **Populating the XML Schema:**
   - The script fills in the XML schema with the product data obtained from the POST request. Default attributes such as `manufacturerId`, `supplierId`, `languageId`, and `categoryId` are set, and specific product fields are customized.

5. **Posting the New Product:**
   - The script then posts the populated XML schema to the PrestaShop store, creating a new product.

6. **Image Upload Function:**
   - Additionally, the script includes a function called `uploadImg` that facilitates the uploading of product images to the PrestaShop store. This function uses cURL to send image data to the API endpoint responsible for handling product images.

## Requirements
- PHP 7.x
- [Composer](https://getcomposer.org/) for managing dependencies
- [PrestaShop](https://www.prestashop.com/) store with Web Service API enabled

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/your/repository.git
   cd repository

2. Install dependencies:

   composer install

3. Create a .env file in the project root (review .env.example) and add the following environment variables:

env
API_KEY=your_prestashop_api_key
API_URL=https://your-prestashop-store.com
IMAGES_FOLDER=/path/to/images

## Usage

Execute the script by running:
php -S localhost:8000
The script retrieves product data from the JSON payload, fetches existing products from the PrestaShop store, and posts new products accordingly.

Ensure that the script has the necessary permissions to read/write files and execute cURL requests.

## Configuration
Modify default attributes such as manufacturerId, supplierId, languageId, and categoryId as needed.

Adjust product fields and associations based on your specific requirements.

## Additional Notes
The script includes a function uploadImg to upload product images to PrestaShop. Customize the function based on your image upload needs.

Ensure that your PrestaShop store allows API access and the necessary permissions for product manipulation.

For troubleshooting, check the error handling and response messages in the script.

## Integration with `prestashop-webservice-lib`

This script leverages the `prestashop-webservice-lib` library for seamless interaction with the PrestaShop Web Service API. The library is responsible for handling API requests, authentication, and communication with the PrestaShop store.

## Image Upload Function

The script includes a function called `uploadImg` that facilitates the uploading of product images to the PrestaShop store. This function uses cURL to send image data to the API endpoint responsible for handling product images.
