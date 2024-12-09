<?php
    if( !$products_data ){
        return;
    }
?>

<div class="whols-products-data">
    <table class="whols-products-data">
        <thead>
            <tr>
                <th><?php echo esc_html__('Product', 'whols') ?></th>
                <th><?php echo esc_html__('Price', 'whols') ?></th>
                <th><?php echo esc_html__('Quantity', 'whols') ?></th>
            </tr>
        </thead>
        
        <tbody>
            <?php foreach( $products_data as $data ): ?>
            <tr>
                <td><?php echo esc_html($data['name']) ?></td>
                <td><?php echo esc_html($data['price']) ?></td>
                <td><?php echo esc_html($data['quantity']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div> <!-- end whols-products-data -->