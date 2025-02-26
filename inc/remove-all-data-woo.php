<?php
// Видалення всіх даних WooCommerce

// Функція для видалення всіх даних WooCommerce
// Функція для видалення всіх даних WooCommerce з перевіркою
function ip_woo_delete_all_data() {
    // Отримуємо кількість об'єктів перед видаленням
    $attribute_count = ip_woo_count_attributes();
    $product_tags_count = ip_woo_count_product_tags();
    $coupons_count = ip_woo_count_coupons();
    $orders_count = ip_woo_count_orders();
    $order_notes_count = ip_woo_count_order_notes();
    $product_count = ip_woo_count_products();
    $product_category_count = ip_woo_count_product_categories();

    // Виконуємо функції видалення лише якщо значення більше 0

    if ($attribute_count > 0) {
        ip_woo_delete_attributes();
    }

    if ($product_tags_count > 0) {
        ip_woo_delete_tags();
    }

    if ($product_count > 0) {
        ip_woo_delete_products();
    }

    if ($product_category_count > 0) {
        ip_woo_delete_product_categories();
    }

    if ($orders_count > 0) {
        ip_woo_delete_orders();
    }

    if ($order_notes_count > 0) {
        ip_woo_delete_orders_notes();
    }

    if ($coupons_count > 0) {
        ip_woo_delete_coupons();
    }

}

// Обробник для натискання кнопки "Delete all data"
function handle_ip_woo_delete_all_data() {
    if (isset($_POST['ip_woo_delete_all_data'])) {
        if (isset($_POST['ip_woo_delete_all_data_nonce']) && 
            wp_verify_nonce($_POST['ip_woo_delete_all_data_nonce'], 'ip_woo_delete_all_data_action')) {
            
            ip_woo_delete_all_data();

            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . 
                     __('All WooCommerce data has been deleted successfully.', 'ip-woo-cleaner') . 
                     '</p></div>';
            });
        }
    }
}
add_action('admin_init', 'handle_ip_woo_delete_all_data');


// Функція для повного видалення WooCommerce і його таблиць
function ip_woo_delete_all_data_and_plugin() {
    global $wpdb;

    // Перевіряємо таблиці на наявність
    $tables = require_once IP_WOO_CLEANER_PLUGIN_PATH . '/inc/woo_tables_list.php';

    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
    }
   
    // Видаляємо всі WooCommerce мета-дані
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'woocommerce_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'woocommerce_%'");
    $wpdb->query("DELETE FROM {$wpdb->termmeta} WHERE meta_key LIKE 'woocommerce_%'");

    // Деактивація WooCommerce перед видаленням
    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    if (is_plugin_active('woocommerce/woocommerce.php')) {
        deactivate_plugins('woocommerce/woocommerce.php');
    }
    

// Видалення даних WooCommerce з Action Scheduler

// 1. Видалення задач WooCommerce з таблиці дій
global $wpdb;

// 1. Видалення записів претензій, пов'язаних з WooCommerce задачами
$wpdb->query(
    "DELETE FROM {$wpdb->prefix}actionscheduler_claims 
     WHERE claim_id IN (
         SELECT claim_id FROM {$wpdb->prefix}actionscheduler_actions 
         WHERE hook LIKE 'woocommerce_%' 
            OR hook LIKE '%wc%'
     )"
);

// 2. Видалення логів, пов'язаних з WooCommerce задачами
$wpdb->query(
    "DELETE FROM {$wpdb->prefix}actionscheduler_logs 
     WHERE action_id IN (
         SELECT action_id FROM {$wpdb->prefix}actionscheduler_actions 
         WHERE hook LIKE 'woocommerce_%' 
            OR hook LIKE '%wc%'
     )"
);

// 3. Видалення задач WooCommerce з таблиці дій
$wpdb->query(
    "DELETE FROM {$wpdb->prefix}actionscheduler_actions 
     WHERE hook LIKE 'woocommerce_%' 
        OR hook LIKE '%wc%' 
        OR group_id IN (
            SELECT group_id FROM {$wpdb->prefix}actionscheduler_groups 
            WHERE slug LIKE '%woocommerce%'
        )"
);

// 4. Видалення груп WooCommerce
$wpdb->query(
    "DELETE FROM {$wpdb->prefix}actionscheduler_groups 
     WHERE slug LIKE '%woocommerce%' 
        OR slug LIKE '%wc%'"
);

// 5. Видалення WooCommerce після очищення Action Scheduler
delete_plugins(['woocommerce/woocommerce.php']);


    // Чистимо кеш
    wp_cache_flush();
}

// Обробка натискання кнопки "Delete WooCommerce"
function ip_woo_handle_delete_woocommerce_request() {
    if (is_admin() && isset($_POST['ip_woo_delete_woocommerce'])) {
        if (function_exists('check_admin_referer')) {
            check_admin_referer('ip_woo_delete_woocommerce_action', 'ip_woo_delete_woocommerce_nonce');
            ip_woo_delete_all_data_and_plugin();

            // Повідомлення про успіх
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible"><p>' . 
                     __('WooCommerce has been completely removed.', 'ip-woo-cleaner') . 
                     '</p></div>';
            });
        }
    }
}

// Додаємо обробник до admin_init
add_action('admin_init', 'ip_woo_handle_delete_woocommerce_request');
