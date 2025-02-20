<?php
/*
 * Plugin Name:       WooCommerce Cleaner
 * Plugin URI:        https://github.com/pekarskyi/WooCommerce-Cleaner
 * Description:       The plugin deletes attributes, tags, products, categories, and orders in WooCommerce.
 * Version:           1.0
 * Requires at least: 6.7.1
 * Requires PHP:      8.0
 * Author:            Mykola Pekarskyi
 * Author URI:        https://inwebpress.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/pekarskyi/WooCommerce-Cleaner
 * Text Domain:       ip-woo-cleaner
 * Domain Path:       /lang
 */
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'IP_WOO_CLEANER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'IP_WOO_CLEANER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

//FUNC: Add page to admin menu
function ip_woo_add_admin_menu() {
    add_menu_page(
        'WooCommerce Cleaner',
        'WooCommerce Cleaner',
        'manage_options',
        'woo-cleaner',
        'ip_woo_admin_page'
    );
}
add_action('admin_menu', 'ip_woo_add_admin_menu');

//FUNC: Додаємо посилання на сторінку налаштувань в таблиці плагінів
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links');
function my_plugin_action_links($links) {
    $settings_link = '<a href="admin.php?page=woo-cleaner">' . __('Settings', 'ip-woo-cleaner') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

//INC:Init
require_once IP_WOO_CLEANER_PLUGIN_PATH . '/inc/functions.php';

//CSS:Admin CSS
function ip_woo_cleaner_admin_assets() {
  wp_enqueue_style('ip-woo-cleaner-admin-css', IP_WOO_CLEANER_PLUGIN_URL . 'assets/css/admin.css', '', time());
  }
  add_action('admin_init', 'ip_woo_cleaner_admin_assets');

//FUNC:Localization
add_action( 'plugins_loaded', 'ip_woo_cleaner_load_textdomain' );
function ip_woo_cleaner_load_textdomain() {
load_plugin_textdomain( 'ip-woo-cleaner', false, dirname( plugin_basename( __FILE__ ) ) .
'/lang/' );
}