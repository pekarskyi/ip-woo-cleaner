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


//SQL: Function to delete attributes
function ip_woo_delete_attributes() {
    global $wpdb;
    
    // Видалення термінів атрибутів
    $wpdb->query("DELETE FROM wp_terms WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE taxonomy LIKE 'pa_%')");
    
    // Видалення метаданих термінів атрибутів
    $wpdb->query("DELETE FROM wp_termmeta WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE taxonomy LIKE 'pa_%')");
    $wpdb->query("DELETE FROM wp_termmeta WHERE meta_key LIKE 'order_pa_%'");
    
    // Видалення таксономій атрибутів
    $wpdb->query("DELETE FROM wp_term_taxonomy WHERE taxonomy LIKE 'pa_%'");
    
    // Видалення осиротілих зв'язків
    $wpdb->query("DELETE FROM wp_term_relationships WHERE term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM wp_term_taxonomy)");
    
    // Видалення метаданих атрибутів з товарів
    $wpdb->query("DELETE FROM wp_postmeta WHERE meta_key LIKE 'attribute_%'");
    
    // Видалення самих атрибутів з таблиці атрибутів WooCommerce
    $wpdb->query("DELETE FROM wp_woocommerce_attribute_taxonomies");
    
    // Очищення кешу WooCommerce
    $wpdb->query("DELETE FROM wp_options WHERE option_name LIKE '_transient_wc_%'");
    $wpdb->query("DELETE FROM wp_options WHERE option_name LIKE '_transient_timeout_wc_%'");
    
    // Оновлення опції для сигналізації WooCommerce про необхідність оновлення
    update_option('woocommerce_attribute_lookup_regenerated', 0);
}

//SQL: Function to set attributes as non-archive
function ip_woo_set_attributes_not_archives() {
    global $wpdb;
    
    $wpdb->query("UPDATE wp_woocommerce_attribute_taxonomies SET attribute_public = '0' WHERE attribute_public = '1'");
}

//SQL: Function to delete tags
function ip_woo_delete_tags() {
    global $wpdb;
    
    $wpdb->query("DELETE FROM wp_terms WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE taxonomy = 'product_tag')");
    $wpdb->query("DELETE FROM wp_term_taxonomy WHERE taxonomy = 'product_tag'");
    $wpdb->query("DELETE FROM wp_term_relationships WHERE term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM wp_term_taxonomy)");
}

//SQL: Function to delete Products
function ip_woo_delete_products() {
    global $wpdb;

    // Видалення зв'язків термінів із товарами
    $wpdb->query("DELETE relations.* 
                  FROM wp_term_relationships AS relations
                  INNER JOIN wp_term_taxonomy AS taxes ON relations.term_taxonomy_id = taxes.term_taxonomy_id
                  WHERE object_id IN (SELECT ID FROM wp_posts WHERE post_type = 'product')");

    // Видалення метаданих товарів
    $wpdb->query("DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type = 'product')");

    // Видалення самих товарів
    $wpdb->query("DELETE FROM wp_posts WHERE post_type = 'product'");

    // Видалення осиротілих метаданих
    $wpdb->query("DELETE pm FROM wp_postmeta pm LEFT JOIN wp_posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL");

    // Видалення WooCommerce сесій
    $wpdb->query("DELETE FROM wp_woocommerce_sessions");
}

//SQL: Function to delete Product Categories
function ip_woo_delete_product_categories() {
    global $wpdb;

    // Видалення метаданих термінів для категорій товарів
    $wpdb->query("DELETE FROM wp_termmeta WHERE term_id IN (
                  SELECT term_id FROM wp_term_taxonomy WHERE taxonomy IN ('product_cat', 'product_type', 'product_visibility'))");

    // Видалення самих термінів (категорій товарів)
    $wpdb->query("DELETE FROM wp_terms WHERE term_id IN (
                  SELECT term_id FROM wp_term_taxonomy WHERE taxonomy IN ('product_cat', 'product_type', 'product_visibility'))");

    // Видалення таксономій категорій товарів
    $wpdb->query("DELETE FROM wp_term_taxonomy WHERE taxonomy IN ('product_cat', 'product_type', 'product_visibility')");

    // Видалення осиротілих метаданих термінів
    $wpdb->query("DELETE meta FROM wp_termmeta meta 
                  LEFT JOIN wp_terms terms ON terms.term_id = meta.term_id 
                  WHERE terms.term_id IS NULL");
}

//SQL: Function to delete all orders
function ip_woo_delete_orders() {
    global $wpdb;

    // Перевіряємо, чи HPOS активний
    $hpos_enabled = get_option('woocommerce_custom_orders_table_enabled', 'no') === 'yes';

    // Якщо HPOS активний, використовуємо нові таблиці для видалення замовлень
    if ($hpos_enabled) {
        // Видаляємо всі дані про товари в замовленнях
        $wpdb->query("DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta");
        $wpdb->query("DELETE FROM {$wpdb->prefix}woocommerce_order_items");

        // Видаляємо записи з таблиці wc_order_product_lookup
        $wpdb->query("DELETE FROM {$wpdb->prefix}wc_order_product_lookup");

        // Видаляємо статистику замовлень
        $wpdb->query("DELETE FROM {$wpdb->prefix}wc_order_stats");

        // Видаляємо замовлення з таблиці wc_orders
        $wpdb->query("DELETE FROM {$wpdb->prefix}wc_orders");

        // Видаляємо пост з типом 'shop_order'
        $wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_type = 'shop_order'");
    } else {
        // Для старої структури даних видаляємо замовлення і мета-дані для старих таблиць
        $wpdb->query("DELETE FROM {$wpdb->prefix}wp_wc_orders_meta");
        $wpdb->query("DELETE FROM {$wpdb->prefix}wp_wc_orders");
        $wpdb->query("DELETE FROM {$wpdb->prefix}wp_wc_order_addresses");
        $wpdb->query("DELETE FROM {$wpdb->prefix}wp_wc_order_operational_data");
    }

    // Видаляємо коментарі та мета-дані для замовлень
    $wpdb->query("DELETE FROM {$wpdb->prefix}commentmeta WHERE comment_id IN (SELECT comment_id FROM {$wpdb->prefix}comments WHERE comment_type = 'order_note')");
    $wpdb->query("DELETE FROM {$wpdb->prefix}comments WHERE comment_type = 'order_note'");

    // Видаляємо мета-дані товарів у замовленнях
    $wpdb->query("DELETE FROM {$wpdb->prefix}woocommerce_order_itemmeta");
    $wpdb->query("DELETE FROM {$wpdb->prefix}woocommerce_order_items");

    // Видаляємо мета-дані для замовлень
    $wpdb->query("DELETE FROM {$wpdb->prefix}postmeta WHERE post_id IN (SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'shop_order')");
    
    // Видаляємо замовлення
    $wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_type = 'shop_order'");
}


//SQL: Function to delete all products in the trash
function ip_woo_delete_products_trashed() {
    global $wpdb;

    $wpdb->query("DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type = 'product' AND post_status = 'trash')");
    $wpdb->query("DELETE FROM wp_posts WHERE post_type = 'product' AND post_status = 'trash'");
}

//SQL: Function to delete all coupons
function ip_woo_delete_coupons() {
    global $wpdb;

    $wpdb->query("DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type = 'shop_coupon')");
    $wpdb->query("DELETE FROM wp_posts WHERE post_type = 'shop_coupon'");
}

//SQL: Function to delete all order notes
function ip_woo_delete_orders_notes() {
    global $wpdb;

    // Видаляємо всі коментарі типу "order_note"
    $wpdb->query("DELETE FROM {$wpdb->commentmeta} WHERE comment_id IN (SELECT comment_ID FROM {$wpdb->comments} WHERE comment_type = 'order_note')");
    $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_type = 'order_note'");
}