<?php
// Видалення всіх даних WooCommerce

function clean_woocommerce_database() {
    global $wpdb;

    // Видаляємо всі таблиці WooCommerce
    $tables = [
        'woocommerce_api_keys',
        'woocommerce_attribute_taxonomies',
        'woocommerce_downloadable_product_permissions',
        'woocommerce_log',
        'woocommerce_order_items',
        'woocommerce_order_itemmeta',
        'woocommerce_payment_tokenmeta',
        'woocommerce_payment_tokens',
        'woocommerce_sessions',
        'woocommerce_shipping_zones',
        'woocommerce_shipping_zone_methods',
        'woocommerce_tax_rates',
        'woocommerce_tax_rate_locations',
        'wc_admin_notes',
        'wc_admin_note_actions',
        'wc_category_lookup',
        'wc_customer_lookup',
        'wc_download_log',
        'wc_order_coupon_lookup',
        'wc_order_product_lookup',
        'wc_order_stats',
        'wc_order_tax_lookup',
        'wc_product_attributes_lookup',
        'wc_product_meta_lookup',
        'wc_rate_limits',
        'wc_reserved_stock',
        'wc_webhooks',
    ];

    foreach ($tables as $table) {
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}{$table}");
    }

    // Видаляємо всі мета-дані, пов'язані з WooCommerce
    $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE 'woocommerce_%'");
    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'woocommerce_%'");

    // Видаляємо всі записи, пов'язані з WooCommerce
    $wpdb->query("DELETE FROM {$wpdb->posts} WHERE post_type IN ('product', 'shop_order', 'shop_coupon')");
    $wpdb->query("DELETE FROM {$wpdb->termmeta} WHERE meta_key LIKE 'woocommerce_%'");

    return true;
}

// Викликаємо очищення після видалення WooCommerce
register_deactivation_hook(__FILE__, 'clean_woocommerce_database');
