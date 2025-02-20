<?php

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

//FUNC: Check HPOS status
function wac_check_hpos_status() {
    if (class_exists('\Automattic\WooCommerce\Utilities\OrderUtil') && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled()) {
        return 'HPOS';
    } else {
        return 'pre-HPOS';
    }
}

//FUNC: Check count attributes
function wac_count_attributes() {
    global $wpdb;
    $count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_attribute_taxonomies");
    return $count ? intval($count) : 0;
}

//FUNC: Function to count attributes that are Public (attribute_public = 1)
function wac_count_archived_attributes() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_public = 1");
}

//FUNC: Function to count all product tags
function wac_count_product_tags() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = 'product_tag'");
}

//FUNC: Function to count all coupons in WooCommerce
function wac_count_coupons() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'shop_coupon'");
}

//FUNC: Function to count orders in HPOS
function wac_count_orders_hpos() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}wc_orders");
}

//FUNC: Function to count orders in pre-HPOS
function wac_count_orders_pre_hpos() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'shop_order' AND ID NOT IN (SELECT post_id FROM {$wpdb->prefix}wc_orders)");
}

//FUNC: Function to count order notes
function wac_count_order_notes() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}comments WHERE comment_type = 'order_note'");
}

//FUNC: Function to count trashed products
function wac_count_trashed_products() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'product' AND post_status = 'trash'");
}

//FUNC: Count Products
function wac_count_products() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}posts WHERE post_type = 'product'");
}

//FUNC: Count Categories
function wac_count_product_categories() {
    global $wpdb;
    return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = 'product_cat'");
}

//FUNC: Output Info notices
function wac_admin_page() {
    if (isset($_POST['wac_delete_attributes'])) {
        wac_delete_attributes();
        echo '<div class="updated"><p>' . __('Product attributes deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['wac_set_attributes_not_archives'])) {
        wac_set_attributes_not_archives();
        echo '<div class="updated"><p>' . __('Product attributes set to not Public!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['wac_delete_tags'])) {
        wac_delete_tags();
        echo '<div class="updated"><p>' . __('Product tags deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
      if (isset($_POST['wac_delete_product_categories'])) {
        wac_delete_product_categories();
        echo '<div class="updated"><p>' . __('Product categories deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['wac_delete_products'])) {
        wac_delete_products();
        echo '<div class="updated"><p>' . __('Products deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['wac_delete_orders_hpos'])) {
        wac_delete_orders_hpos();
        echo '<div class="updated"><p>' . __('All orders (HPOS) deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['wac_delete_orders_pre_hpos'])) {
        wac_delete_orders_pre_hpos();
        echo '<div class="updated"><p>' . __('All orders (pre-HPOS) deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['wac_delete_products_trashed'])) {
        wac_delete_products_trashed();
        echo '<div class="updated"><p>' . __('All trashed products deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['wac_delete_coupons'])) {
        wac_delete_coupons();
        echo '<div class="updated"><p>' . __('All coupons deleted!', 'ip-woo-cleaner') . '</p></div>';
    }
    if (isset($_POST['wac_delete_orders_notes'])) {
        wac_delete_orders_notes();
        echo '<div class="updated"><p>' . __('All order notes deleted!', 'ip-woo-cleaner') . '</p></div>';
    }

    //Variables
    $hpos_status = wac_check_hpos_status();
    $attribute_count = wac_count_attributes(); // Get the number of attributes
    $archived_attribute_count = wac_count_archived_attributes(); // How many of them are archived
    $product_tags_count = wac_count_product_tags(); // Number of product tags
    $coupons_count = wac_count_coupons(); // Number of coupons
    $orders_hpos_count = wac_count_orders_hpos(); // Number of orders in HPOS
    $orders_pre_hpos_count = wac_count_orders_pre_hpos(); // Number of orders in pre-HPOS
    $order_notes_count = wac_count_order_notes(); // Number of order notes
    $trashed_product_count = wac_count_trashed_products(); // Get the number of trashed products
    $product_count = wac_count_products(); // Count Products
    $product_category_count = wac_count_product_categories(); // Count Product Categories
    ?>

        <!-- HTML: Output content for the page -->
        <div class="wc-wrap">          
            <?php          
            //INC: Section HTML content for the page
            require_once IP_WOO_CLEANER_PLUGIN_PATH . '/inc/html-output.php';

            //INC: Section Information about plugin
            require_once IP_WOO_CLEANER_PLUGIN_PATH . '/inc/section-info.php';
            ?>
        </div>    
    
    <?php
}

//SQL: Function to delete attributes
function wac_delete_attributes() {
    global $wpdb;
    
    $wpdb->query("DELETE FROM wp_terms WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE taxonomy LIKE 'pa_%')");
    $wpdb->query("DELETE FROM wp_term_taxonomy WHERE taxonomy LIKE 'pa_%'");
    $wpdb->query("DELETE FROM wp_term_relationships WHERE term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM wp_term_taxonomy)");
    $wpdb->query("DELETE FROM wp_woocommerce_attribute_taxonomies");
    $wpdb->query("DELETE FROM wp_termmeta WHERE meta_key LIKE 'order_pa_%'");
    $wpdb->query("DELETE FROM wp_options WHERE option_name LIKE ('_transient_wc_%')");
}

//SQL: Function to set attributes as non-archive
function wac_set_attributes_not_archives() {
    global $wpdb;
    
    $wpdb->query("UPDATE wp_woocommerce_attribute_taxonomies SET attribute_public = '0' WHERE attribute_public = '1'");
}

//SQL: Function to delete tags
function wac_delete_tags() {
    global $wpdb;
    
    $wpdb->query("DELETE FROM wp_terms WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE taxonomy = 'product_tag')");
    $wpdb->query("DELETE FROM wp_term_taxonomy WHERE taxonomy = 'product_tag'");
    $wpdb->query("DELETE FROM wp_term_relationships WHERE term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM wp_term_taxonomy)");
}

//SQL: Function to delete Products
function wac_delete_products() {
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
function wac_delete_product_categories() {
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


//SQL: Function to delete all orders (HPOS)
function wac_delete_orders_hpos() {
    global $wpdb;

    $wpdb->query("DELETE FROM wp_wc_orders_meta");
    $wpdb->query("DELETE FROM wp_wc_orders");
    $wpdb->query("DELETE FROM wp_wc_order_addresses");
    $wpdb->query("DELETE FROM wp_wc_order_operational_data");
    $wpdb->query("DELETE FROM wp_commentmeta WHERE comment_id IN (SELECT ID FROM wp_comments WHERE comment_type = 'order_note')");
    $wpdb->query("DELETE FROM wp_comments WHERE comment_type = 'order_note'");
}

//SQL: Function to delete all orders (pre-HPOS)
function wac_delete_orders_pre_hpos() {
    global $wpdb;

    $wpdb->query("DELETE FROM wp_woocommerce_order_itemmeta");
    $wpdb->query("DELETE FROM wp_woocommerce_order_items");
    $wpdb->query("DELETE FROM wp_commentmeta WHERE comment_id IN (SELECT ID FROM wp_comments WHERE comment_type = 'order_note')");
    $wpdb->query("DELETE FROM wp_comments WHERE comment_type = 'order_note'");
    $wpdb->query("DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type = 'shop_order')");
    $wpdb->query("DELETE FROM wp_posts WHERE post_type = 'shop_order'");
}

//SQL: Function to delete all products in the trash
function wac_delete_products_trashed() {
    global $wpdb;

    $wpdb->query("DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type = 'product' AND post_status = 'trash')");
    $wpdb->query("DELETE FROM wp_posts WHERE post_type = 'product' AND post_status = 'trash'");
}

//SQL: Function to delete all coupons
function wac_delete_coupons() {
    global $wpdb;

    $wpdb->query("DELETE FROM wp_postmeta WHERE post_id IN (SELECT ID FROM wp_posts WHERE post_type = 'shop_coupon')");
    $wpdb->query("DELETE FROM wp_posts WHERE post_type = 'shop_coupon'");
}

//SQL: Function to delete all order notes
function wac_delete_orders_notes() {
    global $wpdb;

    $wpdb->query("DELETE FROM wp_commentmeta WHERE comment_id IN (SELECT ID FROM wp_comments WHERE comment_type = 'order_note')");
    $wpdb->query("DELETE FROM wp_comments WHERE comment_type = 'order_note'");
}

