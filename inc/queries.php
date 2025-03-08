<?php

//SQL: Function to delete attributes
function ip_woo_delete_attributes() {
    global $wpdb;
    
    // Видалення термінів атрибутів
    $wpdb->query("DELETE FROM {$wpdb->prefix}terms WHERE term_id IN (SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy LIKE 'pa_%')");
    
    // Видалення метаданих термінів атрибутів
    $wpdb->query("DELETE FROM {$wpdb->prefix}termmeta WHERE term_id IN (SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy LIKE 'pa_%')");
    $wpdb->query("DELETE FROM {$wpdb->prefix}termmeta WHERE meta_key LIKE 'order_pa_%'");
    
    // Видалення таксономій атрибутів
    $wpdb->query("DELETE FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy LIKE 'pa_%'");
    
    // Видалення осиротілих зв'язків
    $wpdb->query("DELETE FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM {$wpdb->prefix}term_taxonomy)");
    
    // Видалення метаданих атрибутів з товарів
    $wpdb->query("DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key LIKE 'attribute_%'");
    
    // Видалення самих атрибутів з таблиці атрибутів WooCommerce
    $wpdb->query("DELETE FROM {$wpdb->prefix}woocommerce_attribute_taxonomies");
    
    // Очищення кешу WooCommerce
    $wpdb->query("DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '_transient_wc_%'");
    $wpdb->query("DELETE FROM {$wpdb->prefix}options WHERE option_name LIKE '_transient_timeout_wc_%'");
    
    // Оновлення опції для сигналізації WooCommerce про необхідність оновлення
    update_option('woocommerce_attribute_lookup_regenerated', 0);
}

//SQL: Function to set attributes as non-archive
function ip_woo_set_attributes_not_archives() {
    global $wpdb;
    
    $wpdb->query("UPDATE {$wpdb->prefix}woocommerce_attribute_taxonomies SET attribute_public = '0' WHERE attribute_public = '1'");
}

//SQL: Function to delete tags
function ip_woo_delete_tags() {
    global $wpdb;
    
    $wpdb->query("DELETE FROM {$wpdb->prefix}terms WHERE term_id IN (SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = 'product_tag')");
    $wpdb->query("DELETE FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy = 'product_tag'");
    $wpdb->query("DELETE FROM {$wpdb->prefix}term_relationships WHERE term_taxonomy_id NOT IN (SELECT term_taxonomy_id FROM {$wpdb->prefix}term_taxonomy)");
}

//SQL: Function to delete Products
function ip_woo_delete_products() {
    global $wpdb;

    // Видалення зв'язків термінів із товарами
    $wpdb->query("DELETE relations.* 
                  FROM {$wpdb->prefix}term_relationships AS relations
                  INNER JOIN {$wpdb->prefix}term_taxonomy AS taxes ON relations.term_taxonomy_id = taxes.term_taxonomy_id
                  WHERE object_id IN (SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'product')");

    // Видалення метаданих товарів
    $wpdb->query("DELETE FROM {$wpdb->prefix}postmeta WHERE post_id IN (SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'product')");

    // Видалення самих товарів
    $wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_type = 'product'");

    // Видалення осиротілих метаданих
    $wpdb->query("DELETE pm FROM {$wpdb->prefix}postmeta pm LEFT JOIN {$wpdb->prefix}posts wp ON wp.ID = pm.post_id WHERE wp.ID IS NULL");

    // Видалення WooCommerce сесій
    $wpdb->query("DELETE FROM {$wpdb->prefix}woocommerce_sessions");
}

//SQL: Function to delete Product Categories
function ip_woo_delete_product_categories() {
    global $wpdb;

    // Видалення метаданих термінів для категорій товарів
    $wpdb->query("DELETE FROM {$wpdb->prefix}termmeta WHERE term_id IN (
                  SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy IN ('product_cat', 'product_type', 'product_visibility'))");

    // Видалення самих термінів (категорій товарів)
    $wpdb->query("DELETE FROM {$wpdb->prefix}terms WHERE term_id IN (
                  SELECT term_id FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy IN ('product_cat', 'product_type', 'product_visibility'))");

    // Видалення таксономій категорій товарів
    $wpdb->query("DELETE FROM {$wpdb->prefix}term_taxonomy WHERE taxonomy IN ('product_cat', 'product_type', 'product_visibility')");

    // Видалення осиротілих метаданих термінів
    $wpdb->query("DELETE meta FROM {$wpdb->prefix}termmeta meta 
                  LEFT JOIN {$wpdb->prefix}terms terms ON terms.term_id = meta.term_id 
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
        $wpdb->query("DELETE FROM {$wpdb->prefix}wc_orders_meta");
        $wpdb->query("DELETE FROM {$wpdb->prefix}wc_orders");
        $wpdb->query("DELETE FROM {$wpdb->prefix}wc_order_addresses");
        $wpdb->query("DELETE FROM {$wpdb->prefix}wc_order_operational_data");
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

    $wpdb->query("DELETE FROM {$wpdb->prefix}postmeta WHERE post_id IN (SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'product' AND post_status = 'trash')");
    $wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_type = 'product' AND post_status = 'trash'");
}

//SQL: Function to delete all coupons
function ip_woo_delete_coupons() {
    global $wpdb;

    $wpdb->query("DELETE FROM {$wpdb->prefix}postmeta WHERE post_id IN (SELECT ID FROM {$wpdb->prefix}posts WHERE post_type = 'shop_coupon')");
    $wpdb->query("DELETE FROM {$wpdb->prefix}posts WHERE post_type = 'shop_coupon'");
}

//SQL: Function to delete all order notes
function ip_woo_delete_orders_notes() {
    global $wpdb;

    // Видаляємо всі коментарі типу "order_note"
    $wpdb->query("DELETE FROM {$wpdb->commentmeta} WHERE comment_id IN (SELECT comment_ID FROM {$wpdb->comments} WHERE comment_type = 'order_note')");
    $wpdb->query("DELETE FROM {$wpdb->comments} WHERE comment_type = 'order_note'");
}

//SQL: Function to delete all logs and actions from Action Scheduler
function ip_woo_delete_actions_complete() {
    global $wpdb;
    
    // Додаємо транзакцію для забезпечення атомарності операції
    $wpdb->query('START TRANSACTION');
    
    try {
        // Видаляємо логи перед видаленням екшенів
        $logs_deleted = $wpdb->query("
            DELETE logs FROM {$wpdb->prefix}actionscheduler_logs AS logs
            INNER JOIN {$wpdb->prefix}actionscheduler_actions AS actions
            ON logs.action_id = actions.action_id
            WHERE actions.status = 'complete'
        ");
        error_log('Видалено логів (complete): ' . $logs_deleted);
        
        // Видаляємо самі екшени
        $actions_deleted = $wpdb->query("
            DELETE FROM {$wpdb->prefix}actionscheduler_actions 
            WHERE status = 'complete'
        ");
        error_log('Видалено дій (complete): ' . $actions_deleted);
        
        $wpdb->query('COMMIT');
        error_log('Транзакцію підтверджено (COMMIT)');
        
        return array(
            'logs_deleted' => $logs_deleted,
            'actions_deleted' => $actions_deleted
        );
    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
        error_log('ROLLBACK транзакції через помилку');
        error_log('Помилка видалення завершених дій: ' . $e->getMessage());
        return false;
    }
}

function ip_woo_delete_actions_failed() {
    global $wpdb;
    
    // Додаємо транзакцію для забезпечення атомарності операції
    $wpdb->query('START TRANSACTION');
    
    try {
        // Видаляємо логи перед видаленням екшенів
        $logs_deleted = $wpdb->query("
            DELETE logs FROM {$wpdb->prefix}actionscheduler_logs AS logs
            INNER JOIN {$wpdb->prefix}actionscheduler_actions AS actions
            ON logs.action_id = actions.action_id
            WHERE actions.status = 'failed'
        ");
        error_log('Видалено логів (failed): ' . $logs_deleted);
        
        // Видаляємо самі екшени
        $actions_deleted = $wpdb->query("
            DELETE FROM {$wpdb->prefix}actionscheduler_actions 
            WHERE status = 'failed'
        ");
        error_log('Видалено дій (failed): ' . $actions_deleted);
        
        $wpdb->query('COMMIT');
        error_log('Транзакцію підтверджено (COMMIT)');
        
        return array(
            'logs_deleted' => $logs_deleted,
            'actions_deleted' => $actions_deleted
        );
    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
        error_log('ROLLBACK транзакції через помилку');
        error_log('Помилка видалення невдалих дій: ' . $e->getMessage());
        return false;
    }
}
