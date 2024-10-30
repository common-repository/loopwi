<?php
/*
 
Plugin Name: Loopwi - Get paid to show adverts on your website.
Plugin URI: https://loopwi.com/partners
Description: Create and manage adverts on your website.
Version: 1.0
Author: Loopwi
Author URI: https://loowi.com/
License: GPLv2 or later
Text Domain: loopwi_p2r
}
 
*/
//Declare
define( 'loopwi_ads', 'Loopwi' );


// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

//Load jQuery
function loopwi_ads_load_jquery() {
    if ( ! wp_script_is( 'jquery', 'enqueued' )) {

        //Enqueue
        wp_enqueue_script( 'jquery' );

    }
}
add_action( 'wp_enqueue_scripts', 'loopwi_ads_load_jquery' );

//Declare global
global $wpdb;
//
$table_name = $wpdb->prefix . "loopwi_adsplugins_settings";
$my_products_db_version = '1.0.0';
$charset_collate = $wpdb->get_charset_collate();

//first table for settings
if ( $wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name ) {

    $sql = "CREATE TABLE $table_name (
            ID int(11) NOT NULL AUTO_INCREMENT,
            `pid` text NOT NULL,
            `domain` text NOT NULL,
            `site_code` text NOT NULL,  
            `date` text NOT NULL,
            PRIMARY KEY  (ID)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
    add_option('my_db_version', $my_products_db_version);
}

//Install intial data
//Check if it exists first
global $wpdb; 
$results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}loopwi_adsplugins_settings WHERE pid = 1" );
if ( null == $results->pid ) { 
//Default data
$pid = "1";
$domain = $_SERVER['SERVER_NAME'];
$date = date("D d, M, Y");

$table_name = $wpdb->prefix . 'loopwi_adsplugins_settings';

$wpdb->insert( 
    $table_name, 
    array(   
        'pid' => $pid, 
        'domain' => $domain,
        'date' => $date, 
    ) 
);

}
 
//

//Enqueue styles
function loopwi_ads_styles() {
    wp_enqueue_style( 'loopwi_ads',  plugin_dir_url( __FILE__ ) . 'css/ads.css');
    wp_enqueue_style( 'loopwi_ads_fonts',  plugin_dir_url( __FILE__ ) . 'css/opensans-font.css');
    wp_enqueue_style( 'loopwi_ads_fonts2',  plugin_dir_url( __FILE__ ) . 'fonts/line-awesome/css/line-awesome.min.css');        
    wp_enqueue_style( 'loopwi_ads_styles',  plugin_dir_url( __FILE__ ) . 'css/style.css');
    wp_enqueue_style( 'loopwi_ads_bootsrap',  plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css');
    wp_enqueue_style( 'loopwi_ads_plugins',  plugin_dir_url( __FILE__ ) . 'css/plugins.css'); 
    wp_enqueue_style( 'loopwi_ads_style_report',  plugin_dir_url( __FILE__ ) . 'css/style_report.css');             
}
add_action( 'wp_enqueue_scripts', 'loopwi_ads_styles' );

//Enqueue Scripts
function loopwi_ads_add_theme_scripts() { 
  wp_enqueue_script( 'script', get_template_directory_uri() . '/js/jquery.min.js', array ( 'jquery' ), '3.6.0', 'true');
  wp_enqueue_script( 'script', get_template_directory_uri() . '/js/popper.min.js', array ( 'popper' ), '2.9.3', 'true');
  wp_enqueue_script( 'script', get_template_directory_uri() . '/js/bootstrap.min.js', array ( 'bootstrap' ), '5.1.3', 'true');
}
add_action( 'wp_enqueue_scripts', 'loopwi_ads_add_theme_scripts' );


//Create menu item  
add_action('admin_menu', 'loopwi_ads_plugin_setup_menu'); 

function loopwi_ads_plugin_setup_menu(){
    add_menu_page('Loopwi', 'Loopwi', 'manage_options', 'loopwi_ads', 'loopwi_ads_init', 'dashicons-welcome-widgets-menus', 29 );

    add_submenu_page('loopwi_ads', 'Loopwi Settings', 'Settings', 'manage_options', 'loopwi_ads_settings', 'loopwi_ads_settings_init' ); 
}

//Acton links
function loopwi_ads_action_links( $links ) {

    $links = array_merge( array(
        '<a href="' . esc_url( admin_url( '/admin.php?page=loopwi_ads' ) ) . '">' . __( 'Reports', 'textdomain' ) . '</a>',
         '<a href="' . esc_url( admin_url( '/admin.php?page=loopwi_ads_settings' ) ) . '">' . __( 'Settings', 'textdomain' ) . '</a>' 
    ), $links );

    return $links;

}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'loopwi_ads_action_links' );

  
//Print main page 
function loopwi_ads_init(){
    include( plugin_dir_path( __FILE__ ) . 'includes/dashboard.php');
}

//Print settings page
function loopwi_ads_settings_init(){
    include( plugin_dir_path( __FILE__ ) . 'includes/settings.php');
}


//Initiate plugin
global $wpdb; 
$results = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}loopwi_adsplugins_settings WHERE pid = 1" );
    if ( null == $results->site_code ) {
//Do nothing
      }else{
        //get datas
    $site_code = $results->site_code;  
//
//Shortcode
// The shortcode function
function loopwi_loopwi__shortcode() { 
 //Declare wthin fnction now
    global $site_code;   
//pull Ads
    $response = wp_remote_request( "https://loopwi.com/api/ads/ad?site_code=".$site_code."");
                $body = trim(wp_remote_retrieve_body($response), "\xEF\xBB\xBF"); 
                if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                    $body = trim($body, "\xEF\xBB\xBF"); 
                    $json = json_decode($body);
                    $status = $json->status; 
                    $message = $json->message; 
                    $aid = $json->aid; 
                    $title = $json->title; 
                    $banner = $json->banner;
                    $des = $json->des;
                    $link = $json->link;
                    $link_text = $json->link_text;
                    $price = $json->price;
                    $type = $json->type;
                   }
    if($status == "error"){
        $string = "<div class='loopwi_ad_area'>".esc_html($message)." <hr><small><a href='https://loopwi.com'>Need help?</a></small></div>";
    }//
    if($status == "success"){
        if($type == "product_display"){
        $string = "<div class='loopwi_ad_area'><a href='".esc_html($link)."'><div class='loopwi_banner'><img src='".esc_html($banner)."'></div><div class='loopwi_title'>".esc_html($title)."</div> <div class='loopwi_des'>".esc_html($des)."</div><div class='price'>".esc_html($price)."</div><button class='loopwi_btn'>".esc_html($link_text)."</button></a><hr><small><img src='".plugin_dir_url( __FILE__ )."images/logo_small.png'> <a href='https://loopwi.com/dashboard/register'>Run adverts like this</a></small></div>"; 
         }
         if($type == "bannertext"){
        $string = "<div class='loopwi_ad_area'><a href='".esc_html($link)."'><div class='loopwi_banner'><img src='".esc_html($banner)."'></div><div class='loopwi_title'>".esc_html($title)."</div> <div class='loopwi_des'>".esc_html($des)."</div><button class='loopwi_btn'>".esc_html($link_text)."</button></a><hr><small><img src='".plugin_dir_url( __FILE__ )."images/logo_small.png'> <a href='https://loopwi.com/dashboard/register'>Run adverts like this</a></small></div>"; 
         }
    }//


// Ad code returned
return $string; 
 
}
// Register shortcode
add_shortcode('Loopwi', 'loopwi_loopwi__shortcode');
//
}

//Display the marquee
function loopwi_adscroll() {
    //Declare wthin fnction now
    global $site_code;   
//pull Ads
    $response = wp_remote_request( "https://loopwi.com/api/ads/ad_adscroll?site_code=".$site_code."");
                $body = trim(wp_remote_retrieve_body($response), "\xEF\xBB\xBF"); 
                if ( is_array( $response ) && ! is_wp_error( $response ) ) {
                    $body = trim($body, "\xEF\xBB\xBF"); 
                    $json = json_decode($body);
                    $status = $json->status; 
                    $message = $json->message; 
                    $aid = $json->aid; 
                    $adcontent = $json->adcontent; 
                    $type = $json->type;
                   }
    if($status == "error"){
        $string = "<div id='loopwi_adscroll'><marquee scrollamount='6' onmouseover='this.stop();' onmouseout='this.start();'>".esc_html($adcontent)."</marquee></div>";
    }//
    if($status == "success"){

    $string = "<div id='loopwi_adscroll'><marquee scrollamount='6' onmouseover='this.stop();' onmouseout='this.start();'>".esc_html($adcontent)."</marquee><span id='clickto'><a href='https://loopwi.com/dashboard/register'>Click to Advertise</a></span></div>";

    }

    // Ad code returned
echo $string; 

}
add_action( 'wp_footer', 'loopwi_adscroll', 100 );