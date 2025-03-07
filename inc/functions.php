<?php

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// FUNC: Check HPOS status
function ip_woo_check_hpos_status() {
    if (class_exists('\Automattic\WooCommerce\Utilities\OrderUtil')) {
        if (\Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()) {
            return 'HPOS';
        } else {
            return 'pre-HPOS';
        }
    }
    return __('Missing', 'ip-woo-cleaner'); // Локалізоване слово "Відсутнє"
}


//FUNC: Check count attributes
function ip_woo_count_attributes() {
    global $wpdb;

    // Перевіряємо, чи існує таблиця
    $table_name = $wpdb->prefix . 'woocommerce_attribute_taxonomies';
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

    if (!$table_exists) {
        return 0; // Якщо таблиці немає, повертаємо 0
    }

    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table_name}");
}


//FUNC: Function to count attributes that are Public (attribute_public = 1)
function ip_woo_count_archived_attributes() {
    global $wpdb;

    // Перевіряємо, чи існує таблиця
    $table_name = $wpdb->prefix . 'woocommerce_attribute_taxonomies';
    $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");

    if (!$table_exists) {
        return 0; // Якщо таблиці немає, повертаємо 0
    }

    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table_name} WHERE attribute_public = 1");
}


//FUNC: Function to count all product tags
function ip_woo_count_product_tags() {
    global $wpdb;

    // Перевіряємо, чи існує таблиця term_taxonomy
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}term_taxonomy'") == $wpdb->prefix . 'term_taxonomy') {
        // Якщо таблиця існує, виконуємо запит
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = 'product_tag'");
    } else {
        // Якщо таблиця не існує, повертаємо 0 або інше значення
        return 0;
    }
}


//FUNC: Function to count all coupons in WooCommerce
function ip_woo_count_coupons() {
    global $wpdb;

    // Перевіряємо, чи існує таблиця posts
    if ($wpdb->get_var("SHOW TABLES LIKE '{$wpdb->prefix}posts'") == $wpdb->prefix . 'posts') {
        // Якщо таблиця існує, виконуємо запит
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'shop_coupon'");
    } else {
        // Якщо таблиця не існує, повертаємо 0 або інше значення
        return 0;
    }
}

//FUNC: Function to count order
function ip_woo_count_orders() {
    global $wpdb;

    // Перевіряємо, чи HPOS активний
    $hpos_enabled = get_option('woocommerce_custom_orders_table_enabled', 'no') === 'yes';

    if ($hpos_enabled) {
        // Якщо HPOS активний, використовуємо нові таблиці для підрахунку
        $query = "SELECT COUNT(*) FROM {$wpdb->prefix}wc_orders";
    } else {
        // Якщо HPOS не активний, використовуємо старий запит
        // Тут потрібно буде просто підрахувати кількість записів у таблиці wp_posts, де пост тип 'shop_order'
        $query = "SELECT COUNT(*) 
                  FROM {$wpdb->prefix}posts 
                  WHERE post_type = 'shop_order'";
    }

    return (int) $wpdb->get_var($query);
}

//FUNC: Function to count order notes
function ip_woo_count_order_notes() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}comments WHERE comment_type = 'order_note'");
}

//FUNC: Function to count trashed products
function ip_woo_count_trashed_products() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'product' AND post_status = 'trash'");
}

//FUNC: Count Products
function ip_woo_count_products() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'product'");
}

//FUNC: Count Categories
function ip_woo_count_product_categories() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = 'product_cat'");
}

//FUNC: Count Failed Actions
function ip_woo_count_failed_actions() {
    global $wpdb;
    return $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->prefix}actionscheduler_actions WHERE status = 'failed'"
    );
}

//FUNC: Count Completed Actions
function ip_woo_count_completed_actions() {
    global $wpdb;
    return $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->prefix}actionscheduler_actions WHERE status = 'complete'"
    );
}

//FUNC: Count Pending Actions
function ip_woo_count_pending_actions() {
    global $wpdb;
    return $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->prefix}actionscheduler_actions WHERE status = 'pending'"
    );
}



//FUNC: Output Info notices
function ip_woo_admin_page() {
    if (isset($_POST['ip_woo_delete_attributes'])) {
        ip_woo_delete_attributes();
        echo '<div class="updated"><p>' . __('Product attributes deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['ip_woo_set_attributes_not_archives'])) {
        ip_woo_set_attributes_not_archives();
        echo '<div class="updated"><p>' . __('Product attributes set to not Public!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['ip_woo_delete_tags'])) {
        ip_woo_delete_tags();
        echo '<div class="updated"><p>' . __('Product tags deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
      if (isset($_POST['ip_woo_delete_product_categories'])) {
        ip_woo_delete_product_categories();
        echo '<div class="updated"><p>' . __('Product categories deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['ip_woo_delete_products'])) {
        ip_woo_delete_products();
        echo '<div class="updated"><p>' . __('Products deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['ip_woo_delete_orders'])) {
        ip_woo_delete_orders();
        echo '<div class="updated"><p>' . __('All orders deleted!', 'ip-woo-cleaner') . '</p></div>';
    }

    if (isset($_POST['ip_woo_delete_products_trashed'])) {
        ip_woo_delete_products_trashed();
        echo '<div class="updated"><p>' . __('All trashed products deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['ip_woo_delete_coupons'])) {
        ip_woo_delete_coupons();
        echo '<div class="updated"><p>' . __('All coupons deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['ip_woo_delete_orders_notes'])) {
        ip_woo_delete_orders_notes();
        echo '<div class="updated"><p>' . __('All order notes deleted!', 'ip-woo-cleaner') . '</p></div>';
    }

    //Variables
    $hpos_status = ip_woo_check_hpos_status();
    $attribute_count = ip_woo_count_attributes(); // Get the number of attributes
    $archived_attribute_count = ip_woo_count_archived_attributes(); // How many of them are archived
    $product_tags_count = ip_woo_count_product_tags(); // Number of product tags
    $coupons_count = ip_woo_count_coupons(); // Number of coupons
    $orders_count = ip_woo_count_orders(); // Number of orders
    $order_notes_count = ip_woo_count_order_notes(); // Number of order notes
    $trashed_product_count = ip_woo_count_trashed_products(); // Get the number of trashed products
    $product_count = ip_woo_count_products(); // Count Products
    $product_category_count = ip_woo_count_product_categories(); // Count Product Categories
    $actions_failed_count = ip_woo_count_failed_actions();
    $actions_complete_count = ip_woo_count_completed_actions();
    $actions_pending_count = ip_woo_count_pending_actions();
    ?>

        <!-- HTML: Output content for the page -->
        <div class="wc-wrap">          
            <?php          
            //INC: Section HTML content for the page
            require_once IP_WOO_CLEANER_PLUGIN_PATH . '/inc/html-output.php';

            //INC: Section Information about plugin
            require_once IP_WOO_CLEANER_PLUGIN_PATH . '/inc/sidebar.php';
            ?>
        </div>    
    
    <?php
}

//INC: Queries
require_once IP_WOO_CLEANER_PLUGIN_PATH . '/inc/queries.php';