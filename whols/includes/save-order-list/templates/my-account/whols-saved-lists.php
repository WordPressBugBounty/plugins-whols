<?php
/**
 * Saved Lists template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/whols-saved-lists.php.
 */

defined('ABSPATH') || exit;

do_action('whols_before_saved_lists');
wp_enqueue_style('dashicons');

if (empty($saved_lists)) : ?>
    <div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
        <?php esc_html_e('You have no saved lists.', 'whols'); ?>
    </div>
<?php else : ?>

<table class="woocommerce-orders-table woocommerce-MyAccount-orders shop_table shop_table_responsive whols-saved-lists-table">
    <thead>
        <tr>
            <th><?php esc_html_e('List Name', 'whols'); ?></th>
            <th><?php esc_html_e('Description', 'whols'); ?></th>
            <th><?php esc_html_e('Items', 'whols'); ?></th>
            <th><?php esc_html_e('Date', 'whols'); ?></th>
            <th><?php esc_html_e('Actions', 'whols'); ?></th>
        </tr>
    </thead>

    <tbody>
        <?php foreach ($saved_lists as $list) : 
            $item_count = count($list['items']);
            ?>
            <tr>
                <td data-title="<?php esc_attr_e('List Name', 'whols'); ?>">
                    <?php echo esc_html($list['name']); ?>
                </td>
                
                <td data-title="<?php esc_attr_e('Description', 'whols'); ?>">
                    <?php echo !empty($list['description']) ? esc_html($list['description']) : '<em>' . esc_html__('No description', 'whols') . '</em>'; ?>
                </td>
                
                <td data-title="<?php esc_attr_e('Items', 'whols'); ?>">
                    <?php 
                    printf(
                        /* translators: %d: number of items */
                        _n('%d item', '%d items', $item_count, 'whols'), 
                        $item_count
                    ); 
                    ?>
                    <span class="whols-items-preview">
                        <?php 
                        $products = array();
                        foreach ($list['items'] as $item) {
                            $product = wc_get_product($item['product_id']);
                            if ($product) {
                                $products[] = $product->get_name() . ' × ' . $item['quantity'];
                            }
                        }
                        
                        echo '<div>';
                            foreach ($products as $product) {
                                echo '<span class="whols-save-order-list-product-badge">' . esc_html($product) . '</span>';
                            }
                        echo '</div>';
                        ?>
                    </span>
                </td>
                
                <td data-title="<?php esc_attr_e('Date', 'whols'); ?>">
                    <?php 
                        $date_timestamp = $list['date'];
                        echo esc_html(date_i18n(get_option('date_format'), $date_timestamp));
                    ?>
                </td>
                
                <td data-title="<?php esc_attr_e('Actions', 'whols'); ?>" class="whols-action-buttons">
                    <a href="#" 
                        class="whols-action-icon whols-add-list-to-cart" 
                        data-list-id="<?php echo esc_attr($list['id']); ?>"
                        title="<?php esc_attr_e('Add to Cart', 'whols'); ?>">
                        <span class="dashicons dashicons-cart"></span>
                    </a>
                    
                    <a href="#" 
                        class="whols-action-icon whols-delete-saved-list" 
                        data-list-id="<?php echo esc_attr($list['id']); ?>"
                        title="<?php esc_attr_e('Delete', 'whols'); ?>">
                        <span class="dashicons dashicons-trash"></span>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php endif;

do_action('whols_after_saved_lists'); ?>