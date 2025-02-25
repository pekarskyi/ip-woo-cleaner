<form method="post">
                <div class="section_actions">

                    <div class="action_categories">
                        <h2><?php _e('Product Categories:', 'ip-woo-cleaner'); ?></h2>

                        <ul>
                            <li><?php _e('Product Categories:', 'ip-woo-cleaner'); ?> <?php echo $product_category_count; ?></li>
                        </ul>

                        <div class="block-inner">
                            <input type="submit" name="ip_woo_delete_product_categories" class="button button-primary" value="<?php _e('Delete all categories', 'ip-woo-cleaner'); ?>" <?php echo ($product_category_count === 0) ? 'disabled' : ''; ?>>
                        </div>                       
                    </div>
                
                    <div class="action_product-attributes">
                        <h2><?php _e('Product Attributes:', 'ip-woo-cleaner'); ?></h2>
                        
                        <ul>
                            <li><?php _e('Product attributes:', 'ip-woo-cleaner'); ?> <?php echo $attribute_count; ?></li>
                            <li><?php _e('Public (archive) attributes:', 'ip-woo-cleaner'); ?> <?php echo $archived_attribute_count; ?></li>
                        </ul>

                        <div class="block-inner">
                            <input type="submit" name="ip_woo_delete_attributes" class="button button-primary" value="<?php _e('Delete all attributes', 'ip-woo-cleaner'); ?>"<?php echo ($attribute_count === 0) ? 'disabled' : ''; ?>>
                            <input type="submit" name="ip_woo_set_attributes_not_archives" class="button button-primary" value="<?php _e('Set all attributes as Not public', 'ip-woo-cleaner'); ?>" 
                                <?php echo ($archived_attribute_count === 0) ? 'disabled' : ''; ?>>
                        </div>
                    </div>

                    <div class="action_products">
                        <h2><?php _e('Products:', 'ip-woo-cleaner'); ?></h2>

                        <ul>
                            <li><?php _e('Products:', 'ip-woo-cleaner'); ?> <?php echo $product_count; ?></li>
                            <li><?php _e('Trashed products:', 'ip-woo-cleaner'); ?> <?php echo $trashed_product_count; ?></li>
                        </ul>

                        <div class="block-inner">
                            <input type="submit" name="ip_woo_delete_products" class="button button-primary" value="<?php _e('Delete all products', 'ip-woo-cleaner'); ?>" <?php echo ($product_count === 0) ? 'disabled' : ''; ?>>

                            <input type="submit" name="ip_woo_delete_products_trashed" class="button button-primary" value="<?php _e('Delete all trashed products', 'ip-woo-cleaner'); ?>" <?php echo ($trashed_product_count === 0) ? 'disabled' : ''; ?>>
                        </div>                       
                    </div>

                    <div class="action_product-tags">
                        <h2><?php _e('Product Tags:', 'ip-woo-cleaner'); ?></h2>
                        <div class="block-inner">
                            <ul>
                                <li><?php _e('Product tags:', 'ip-woo-cleaner'); ?> <?php echo $product_tags_count; ?></li>
                            </ul>
                            <input type="submit" name="ip_woo_delete_tags" class="button button-primary" value="<?php _e('Delete all tags', 'ip-woo-cleaner'); ?>"
                            <?php echo ($product_tags_count === 0) ? 'disabled' : ''; ?>>
                            </div>
                    </div>

                    <div class="action_coupons">
                        <h2><?php _e('Coupons:', 'ip-woo-cleaner'); ?></h2>
                        <div class="block-inner">
                            <ul>
                                <li><?php _e('Coupons:', 'ip-woo-cleaner'); ?> <?php echo $coupons_count; ?></li>
                            </ul>
                            <input type="submit" name="ip_woo_delete_coupons" class="button button-primary" value="<?php _e('Delete all coupons', 'ip-woo-cleaner'); ?>"
                            <?php echo ($coupons_count === 0) ? 'disabled' : ''; ?>>
                        </div>
                    </div>

                    <div class="action_orders">
                        <h2><?php _e('Orders:', 'ip-woo-cleaner'); ?></h2>
                        <ul>
                            <li><?php _e('Order storage type:', 'ip-woo-cleaner'); ?> <?php echo $hpos_status; ?></li>
                            <li><?php _e('Orders:', 'ip-woo-cleaner'); ?> <?php echo $orders_count; ?></li>
                        </ul>
                        
                        <div class="block-inner">
                            <input type="submit" name="ip_woo_delete_orders" class="button button-primary" value="<?php _e('Delete all orders', 'ip-woo-cleaner'); ?>" 
                            <?php echo ($orders_count === 0) ? 'disabled' : ''; ?>>
                        </div>
                        
                        <div class="block-inner">
                            <ul>
                                <li><?php _e('Order notes:', 'ip-woo-cleaner'); ?> <?php echo $order_notes_count; ?></li>
                            </ul>
                            
                            <input type="submit" name="ip_woo_delete_orders_notes" class="button button-primary" value="<?php _e('Delete all order notes', 'ip-woo-cleaner'); ?>"
                            <?php echo ($order_notes_count === 0) ? 'disabled' : ''; ?>>
                        </div>
                    </div>

                    <div class="action_remove-table-woo">
    <h2><?php _e('Remove WooCommerce Tables:', 'ip-woo-cleaner'); ?></h2>
    <div class="block-inner">
        <form method="post" action="">
            <?php wp_nonce_field('ip_woo_remove_tables_action', 'ip_woo_remove_tables_nonce'); ?>
            <input type="submit" name="ip_woo_remove_tables" class="button button-primary" value="<?php _e('Remove Tables', 'ip-woo-cleaner'); ?>" 
                onclick="return confirm('<?php _e('WARNING: This will permanently delete all WooCommerce tables from the database. This cannot be undone. Are you sure?', 'ip-woo-cleaner'); ?>');">
        </form>
    </div>
</div>

<?php
// Обробник кнопки видалення таблиць WooCommerce
function handle_woo_remove_tables_button() {
    if (isset($_POST['ip_woo_remove_tables'])) {
        // Перевіряємо nonce для безпеки
        if (isset($_POST['ip_woo_remove_tables_nonce']) && wp_verify_nonce($_POST['ip_woo_remove_tables_nonce'], 'ip_woo_remove_tables_action')) {
            
            // Викликаємо функцію очищення
            $result = clean_woocommerce_database();
            
            if ($result) {
                // Додаємо повідомлення про успіх
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-success is-dismissible"><p>' . 
                          __('WooCommerce tables have been removed successfully.', 'ip-woo-cleaner') . 
                          '</p></div>';
                });
            } else {
                // Додаємо повідомлення про помилку
                add_action('admin_notices', function() {
                    echo '<div class="notice notice-error is-dismissible"><p>' . 
                          __('Failed to remove WooCommerce tables.', 'ip-woo-cleaner') . 
                          '</p></div>';
                });
            }

            // Перенаправлення, щоб уникнути повторної відправки форми
            wp_redirect(admin_url('admin.php?page=woo-cleaner'));
            exit;
        }
    }
}

// Додаємо обробник до хуку admin_init
add_action('admin_init', 'handle_woo_remove_tables_button');
?>

                </div>
            </form>