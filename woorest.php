<?php
/*
 * Plugin Name: WooCommerce Products from REST API
 * Author: Aurelza Softwares LLP
 * Author URI: https://aurelza.com
 * Version : 0.2
 */

if (!defined('ABSPATH')) {
   header("location:/");
   die("");
}


function woorest_activation_function()
{
   global $wpdb, $table_prefix;
   $productTable = $table_prefix . "woorest_products";
   $createProductTableQuery = "CREATE TABLE IF NOT EXISTS {$productTable} (
   Code VARCHAR(255),
   ParentCode VARCHAR(255),
   Description VARCHAR(255),
   VariationDescription VARCHAR(255),
   PackageNumberOfItems VARCHAR(255),
   ImageName VARCHAR(255),
   ImageLink VARCHAR(255),
   ThumbnailLink VARCHAR(255),
   Family VARCHAR(255),
   SubFamily VARCHAR(255),
   UnitPrice7 VARCHAR(255),
   UnitPrice7IncludingVAT VARCHAR(255),
   UnitPrice8 VARCHAR(255),
   UnitPrice8IncludingVAT VARCHAR(255),
   IsVariation VARCHAR(255),
   IsSimple VARCHAR(255),
   SortingCode VARCHAR(255),
   VATPercentage VARCHAR(255)
)";

   $wpdb->query($createProductTableQuery); //creating the table to store all products coming from the API


}




function woorest_deactivation_function()
{
   global $wpdb, $table_prefix;
   $productTable = $table_prefix . "woorest_products";
   $dropProductTableQuery = "DROP TABLE  {$productTable}";
   $wpdb->query($dropProductTableQuery);
}



register_activation_hook(
   __FILE__,
   'woorest_activation_function'
);
register_deactivation_hook(
   __FILE__,
   'woorest_deactivation_function'
);


function woorest_errors_shortcode($atts)
{

   $atts = shortcode_atts(
      array(
         'message' => 'this is the message'
      ),
      $atts
   );

   return "The message is : {$atts['message']}";
}
add_shortcode('woorest_errors', 'woorest_errors_shortcode');



function woorest_page_funtion()
{
   include 'admin/main.php';
}


function woorest_admin_menu()
{
   add_menu_page('WooRest - Manage Products', 'WooRest', 'manage_options', 'woorest', 'woorest_page_funtion', '', 6);
}
;


add_action('admin_menu', 'woorest_admin_menu');