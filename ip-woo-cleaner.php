<?php
/**
 * Plugin Name:       IP Woo Cleaner
 * Plugin URI:        https://github.com/pekarskyi/ip-woo-cleaner
 * Description:       The plugin deletes attributes, tags, products, categories, and orders in WooCommerce.
 * Version:           1.0.0
 * Requires at least: 6.7.1
 * Requires PHP:      8.0
 * Author:            Mykola Pekarskyi
 * Author URI:        https://inwebpress.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/pekarskyi/WooCommerce-Cleaner
 * Text Domain:       ip-woo-cleaner
 * Domain Path:       /lang
 **/
if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define( 'IP_WOO_CLEANER_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'IP_WOO_CLEANER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
// Отримуємо версію з опису плагіна
$plugin_data = get_file_data( __FILE__, array( 'Version' => 'Version' ) );
define( 'IP_WOO_CLEANER_PLUGIN_VERSION', $plugin_data['Version'] );

//FUNC: Add page to admin menu
function ip_woo_add_admin_menu() {
    add_menu_page(
        'IP Woo Cleaner',
        'IP Woo Cleaner',
        'manage_options',
        'ip-woo-cleaner',
        'ip_woo_admin_page'
    );
}
add_action('admin_menu', 'ip_woo_add_admin_menu');

//FUNC: Додаємо посилання на сторінку налаштувань в таблиці плагінів
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links');
function my_plugin_action_links($links) {
    $settings_link = '<a href="admin.php?page=ip-woo-cleaner">' . __('Settings', 'ip-woo-cleaner') . '</a>';
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

// Adding update check via GitHub
require_once plugin_dir_path( __FILE__ ) . 'updates/github-updater.php';

$github_username = 'pekarskyi'; // Вказуємо ім'я користувача GitHub
$repo_name = 'ip-woo-cleaner'; // Вказуємо ім'я репозиторію GitHub, наприклад ip-wp-github-updater
$prefix = 'ip_woo_cleaner'; // Встановлюємо унікальний префікс плагіну, наприклад ip_wp_github_updater

// Ініціалізуємо систему оновлення плагіну з GitHub
if ( function_exists( 'ip_github_updater_load' ) ) {
    // Завантажуємо файл оновлювача з нашим префіксом
    ip_github_updater_load($prefix);
    
    // Формуємо назву функції оновлення з префіксу
    $updater_function = $prefix . '_github_updater_init';   
    
    // Після завантаження наша функція оновлення повинна бути доступна
    if ( function_exists( $updater_function ) ) {
        call_user_func(
            $updater_function,
            __FILE__,       // Plugin file path
            $github_username, // Your GitHub username
            '',              // Access token (empty)
            $repo_name       // Repository name (на основі префіксу)
        );
    }
} 