<?php

require __DIR__ . '/../vendor/autoload.php';


use Automattic\WooCommerce\Client;

$woocommerce = new Client(
    'https://aurelza.store/bruno/',
    'ck_8ac348ec770d316aed108ed3a40a584fc3482606',
    'cs_96c10dc33bfa1fd17aad39078ca4a56d05692312',
    [
        'version' => 'wc/v3',
    ]
);


function getNumberOfPages()
{
    $HMACKey = "xRf+eD76Gk=YZb*7YcT6cGAPyZAM5dvbqDwK.2Ut9v7Hf15/Klid/PEYVbfBK0jRkA/v0f/Ik4tRIOSdLvh=voZBg";


    function GetHmacHex2($request_params, $ts, $HMACKey)
    {


        if (!is_array($request_params) || !isset($request_params)) {
            return false;
        }

        $req_str = "";
        foreach ($request_params as $param) {
            $req_str .= $param . "*15DkMaO32T*";
        }

        $req_str .= $ts . "*15DkMaO32T*";
        $hm = hash_hmac('sha512', $req_str, $HMACKey, false);

        return $hm;
    }



    $curl = curl_init();


    $page = 1;
    $ts = time();
    $hmac = GetHmacHex2([$page], $ts, $HMACKey);



    $data = array(
        "ts" => $ts,
        "page" => $page,
        "h" => $hmac
    );

    $data_string = json_encode($data);
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => 'https://ceraspoly.net/tyto/f14588c9/products.php',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data_string,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        )
    );

    $response = curl_exec($curl);



    curl_close($curl);




    // Decode the JSON data
    $data = json_decode($response, true);

    return $data['total_pages'];
}


/************ SAVES ALL THE PRODUCTS TO THE DATABASE TABLES *************/
function productsToDB()
{

    global $wpdb, $table_prefix;
    $productTable = $table_prefix . "woorest_products";

    //creating connection with WooCommerce



    //adding products to the database
    $HMACKey = "xRf+eD76Gk=YZb*7YcT6cGAPyZAM5dvbqDwK.2Ut9v7Hf15/Klid/PEYVbfBK0jRkA/v0f/Ik4tRIOSdLvh=voZBg";


    function GetHmacHex($request_params, $ts, $HMACKey)
    {


        if (!is_array($request_params) || !isset($request_params)) {
            return false;
        }

        $req_str = "";
        foreach ($request_params as $param) {
            $req_str .= $param . "*15DkMaO32T*";
        }

        $req_str .= $ts . "*15DkMaO32T*";
        $hm = hash_hmac('sha512', $req_str, $HMACKey, false);

        return $hm;
    }






    $totalNumberOfPages = getNumberOfPages();


    for ($i = 1; $i <= $totalNumberOfPages; $i++) {
        $curl = curl_init();
        $ts = time();
        $hmac = GetHmacHex([$i], $ts, $HMACKey);

        $data = array(
            "ts" => $ts,
            "page" => $i,
            "h" => $hmac
        );

        $data_string = json_encode($data);


        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://ceraspoly.net/tyto/f14588c9/products.php',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $data_string,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        // Decode the JSON data
        $APIdata = json_decode($response, true);


        if (isset($APIdata['data']) && is_array($APIdata['data'])) {
            // Iterate over the "data" array
            foreach ($APIdata['data'] as $item) {
                $code = $item['Code'];
                $parentCode = $item['ParentCode'];
                $description = $item['Description'];
                $variationDescription = $item['VariationDescription'];
                $packageNumberOfItems = $item['PackageNumberOfItems'];
                $imageName = $item['ImageName'];
                $imageLink = $item['ImageLink'];
                $thumbnailLink = $item['ThumbnailLink'];
                $family = $item['Family'];
                $subFamily = $item['SubFamily'];
                $unitPrice7 = $item['UnitPrice7'];
                $unitPrice7IncludingVAT = $item['UnitPrice7IncludingVAT'];
                $unitPrice8 = $item['UnitPrice8'];
                $unitPrice8IncludingVAT = $item['UnitPrice8IncludingVAT'];
                $isVariation = $item['IsVariation'];
                $IsSimple = $item['IsSimple'];
                $sortingCode = $item['SortingCode'];
                $vatPercentage = $item['VATPercentage'];

                //making array of the data for a product
                $productData = array(
                    "Code" => $code,
                    "ParentCode" => $parentCode,
                    "Description" => $description,
                    "VariationDescription" => $variationDescription,
                    "PackageNumberOfItems" => $packageNumberOfItems,
                    "ImageName" => $imageName,
                    "ImageLink" => $imageLink,
                    "ThumbnailLink" => $thumbnailLink,
                    "Family" => $family,
                    "SubFamily" => $subFamily,
                    "UnitPrice7" => $unitPrice7,
                    "UnitPrice7IncludingVAT" => $unitPrice7IncludingVAT,
                    "UnitPrice8" => $unitPrice8,
                    "UnitPrice8IncludingVAT" => $unitPrice8IncludingVAT,
                    "IsVariation" => $isVariation,
                    "IsSimple" => $IsSimple,
                    "SortingCode" => $sortingCode,
                    "VATPercentage" => $vatPercentage
                );

                $wpdb->insert($productTable, $productData);
            }
        }
        echo "<pre>";
        print_r($APIdata);
        echo "</pre>";
        exit();
    }
}

/////////cerated Product ID : 18 
function createVariableProduct($woocommerce)
{

    $data = [
        'name' => 'Premium Quality 2',
        'type' => 'variable',
        'description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.',
        'short_description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
        'categories' => [
            [
                'id' => 9
            ],
            [
                'id' => 14
            ]
        ],
        'images' => [
            [
                'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg'
            ],
            [
                'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_back.jpg'
            ]
        ]
    ];

    return $woocommerce->post('products', $data);
}



//1->Color  , 2->Quantity 
function addAttributes($woocommerce)
{

    $data = [
        'attributes' => [
            [
                'id' => 1,
                'visible' => true,
                'variation' => true,
                'options' => [
                    'Black',
                    'Green'
                ]
            ],
            [
                'name' => 'Quantity',
                'visible' => true,
                'variation' => true,
                'options' => [
                    'S',
                    'M'
                ]
            ]
        ]
    ];

    return $woocommerce->put('products/24', $data);
}


function addVariations($woocommerce)
{

    $data = [
        'regular_price' => '9.00',
        'image' => [
            'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_back.jpg'
        ],
        'attributes' => [
            [
                'id' => 1,
                'option' => '1'
            ]
        ]
    ];

    $createdVariation = $woocommerce->post('products/346/variations', $data);
    update_post_meta($createdVariation->id, 'min_max_rules', 'yes');
    update_post_meta($createdVariation->id, 'variation_group_of_quantity', '25');
}



function createAllCategories($woocommerce)
{

    global $wpdb, $table_prefix;
    $productTable = $table_prefix . "woorest_products";
    $q = "SELECT DISTINCT Family FROM {$productTable}";

    $parentCategories = $wpdb->get_results($q);
    foreach ($parentCategories as $parentCategory) {

        ////Create Parent Category
        $categoryData = [
            'name' => $parentCategory->Family,
        ];

        $parentCategoryID = $woocommerce->post('products/categories', $categoryData)->id;


        $familyValue = $parentCategory->Family;


        $q2 = "SELECT DISTINCT SubFamily FROM {$productTable} WHERE Family = '{$familyValue}'";

        $subCategories = $wpdb->get_results($q2);

        foreach ($subCategories as $subCategory) {


            $subCategoryData = [
                'name' => $subCategory->SubFamily,
                'parent' => $parentCategoryID
            ];

            $subCategoryID = $woocommerce->post('products/categories', $subCategoryData)->id;
        }
    }
}


function allSimpleProducts($woocommerce)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'woorest_products';
    $query = $wpdb->prepare("SELECT * FROM $table_name where IsSimple is true");
    $results = $wpdb->get_results($query);

    if ($results) {
        foreach ($results as $item) {


            //saving the product info in variable
            $code = $item->Code;
            $parentCode = $item->ParentCode;
            $description = $item->Description;
            $variationDescription = $item->VariationDescription;
            $packageNumberOfItems = $item->PackageNumberOfItems;
            $imageName = $item->ImageName;
            $imageLink = $item->ImageLink;
            $thumbnailLink = $item->ThumbnailLink;
            $family = $item->Family;
            $subFamily = $item->SubFamily;
            $unitPrice7 = $item->UnitPrice7;
            $unitPrice7IncludingVAT = $item->UnitPrice7IncludingVAT;
            $unitPrice8 = $item->UnitPrice8;
            $unitPrice8IncludingVAT = $item->UnitPrice8IncludingVAT;
            $isVariation = $item->IsVariation;
            $IsSimple = $item->IsSimple;
            $sortingCode = $item->SortingCode;
            $vatPercentage = $item->VATPercentage;


            //getting the category ID
            $subCategory = get_term_by('name', $subFamily, 'product_cat');
            // return $subCategory;

            //creating variable product

            $productData = [
                'name' => $code,
                'type' => 'variable',
                'description' => $description,
                'short_description' => $description,
                'categories' => [
                    [
                        'id' => $subCategory->term_id
                    ]
                ],
                'images' => [
                    [
                        'src' => $imageLink
                    ]
                ]
            ];

            $product = $woocommerce->post('products', $productData);

            ///adding the attributes to the product
            $attributeData = [
                'attributes' => [
                    [
                        'name' => 'Quantity',
                        'visible' => true,
                        'variation' => true,
                        'options' => [
                            '1',
                            'Pack Of ' . $packageNumberOfItems
                        ]
                    ]
                ]
            ];

            $attributeMade = $woocommerce->put('products/' . $product->id, $attributeData);


            //Creating the variations

            $SingleVariatoinData = [
                'regular_price' => $unitPrice7IncludingVAT,
                'image' => [
                    'src' => $imageLink
                ],
                'attributes' => [
                    [
                        'name' => 'Quantity',
                        'option' => '1'
                    ]
                ]
            ];

            $createdSingleVariation = $woocommerce->post('products/' . $product->id . '/variations', $SingleVariatoinData);

            $PackVariatoinData = [
                'regular_price' => $unitPrice8IncludingVAT,
                'image' => [
                    'src' => $imageLink
                ],
                'attributes' => [
                    [
                        'name' => 'Quantity',
                        'option' => 'Pack Of ' . $packageNumberOfItems
                    ]
                ]
            ];

            $createdPackVariation = $woocommerce->post('products/' . $product->id . '/variations', $PackVariatoinData);


            update_post_meta($createdPackVariation->id, 'min_max_rules', 'yes');
            update_post_meta($createdPackVariation->id, 'variation_group_of_quantity', $packageNumberOfItems);
        }
    }
    return "Hello";
}

function inserttoWooProduct()
{
    global $wpdb, $table_prefix;
    $productTable = $table_prefix . "woorest_products";

    $query = "SELECT * FROM $productTable LIMIT 10";
    $APIdata = $wpdb->get_results($query, ARRAY_A);

    foreach ($APIdata as $product_data) {
        if (empty($product_data['IsVariation']) && empty($product_data['IsSimple'])) {
            $product = new WC_Product_Simple();
            $product->set_name($product_data['ParentCode']); //Product Name
            $product->set_regular_price($product_data['UnitPrice7']); //Product Name
            $product->save();

            $product_id = $product->get_id();

            // Check if the main category exists, and create it if it doesn't
            $main_category_name = $product_data['Family']; // Replace with the actual main category name
            if (!term_exists($main_category_name, 'product_cat')) {
                wp_insert_term($main_category_name, 'product_cat');
            }

            // Assign the main category to the product
            $main_term_id = get_term_by('name', $main_category_name, 'product_cat')->term_id;
            wp_set_post_terms($product_id, $main_term_id, 'product_cat');

            // Check if the subcategory exists, and create it if it doesn't
            $subcategory_name = $product_data['SubFamily']; // Replace with the actual subcategory name
            if (!term_exists($subcategory_name, 'product_cat')) {
                wp_insert_term($subcategory_name, 'product_cat', array('parent' => $main_term_id));
            }

            // Assign the subcategory to the product
            $subcategory_term_id = get_term_by('name', $subcategory_name, 'product_cat')->term_id;
            wp_set_post_terms($product_id, $subcategory_term_id, 'product_cat', true);


            // Make sure to update the product after assigning the category
            $product->save();
        } 
        else if(!empty($product_data['IsSimple'])){
            $product = new WC_Product_Simple();
            $product->set_name($product_data['ParentCode']); //Product Name
            $product->set_regular_price($product_data['UnitPrice7']); //Product Name
            $product->save();

            $product_id = $product->get_id();

            $main_category_name = $product_data['Family']; // Replace with the actual main category name
            if (!term_exists($main_category_name, 'product_cat')) {
                wp_insert_term($main_category_name, 'product_cat');
            }

            // Assign the main category to the product
            $main_term_id = get_term_by('name', $main_category_name, 'product_cat')->term_id;
            wp_set_post_terms($product_id, $main_term_id, 'product_cat');

            // Check if the subcategory exists, and create it if it doesn't
            $subcategory_name = $product_data['SubFamily']; // Replace with the actual subcategory name
            if (!term_exists($subcategory_name, 'product_cat')) {
                wp_insert_term($subcategory_name, 'product_cat', array('parent' => $main_term_id));
            }

            // Assign the subcategory to the product
            $subcategory_term_id = get_term_by('name', $subcategory_name, 'product_cat')->term_id;
            wp_set_post_terms($product_id, $subcategory_term_id, 'product_cat', true);


            // Make sure to update the product after assigning the category
            $product->save();
        }
    }
    echo "<pre>";
    print_r($APIdata);
    echo "</pre>";
}

// Check if the form is submitted and call the function
if (isset($_POST['productToDB'])) {
    echo productsToDB();
}

if (isset($_POST['CreateVariableProduct'])) {
    print_r(createVariableProduct($woocommerce));
}

if (isset($_POST['addAttributes'])) {
    print_r(addAttributes($woocommerce));
}

if (isset($_POST['addVariations'])) {
    print "<pre>";
    print_r(addVariations($woocommerce));
    print "</pre>";
}

if (isset($_POST['createAllCategories'])) {
    print "<pre>";
    print_r(createAllCategories($woocommerce));
    print "</pre>";
}
if (isset($_POST['allSimpleProducts'])) {
    print "<pre>";
    print_r(allSimpleProducts($woocommerce));
    print "</pre>";
}

if (isset($_POST['inserttoWooProduct'])) {
    inserttoWooProduct();
}



ob_start()
?>
<div class="wrap">

    <form method="post">
        <button type="submit" name="productToDB">All Products To DB</button>
    </form>
    <form method="post">
        <button type="submit" name="CreateVariableProduct">Create Variable Product</button>
    </form>
    <form method="post">
        <button type="submit" name="addAttributes">addAttributes</button>
    </form>
    <form method="post">
        <button type="submit" name="addVariations">addVariations</button>
    </form>
    <form method="post">
        <button type="submit" name="createAllCategories">createAllCategories</button>
    </form>
    <form method="post">
        <button type="submit" name="allSimpleProducts">allSimpleProducts</button>
    </form>

    <form method="post">
        <button type="submit" name="inserttoWooProduct">Insert to woocommerce product</button>
    </form>

</div>

<?php
echo ob_get_clean();

?>