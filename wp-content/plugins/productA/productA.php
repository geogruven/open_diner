<?php
/*
  Plugin Name: Product A
  Description: Product A Description Goes here
  Author: George Aguiar & Ciprian Turcu
  Version: 1.0
 */

function pA_save_postdata() {
    global $post;
    if ($_POST['pA-hidd'] == 'true') {
        $pA_price = get_post_meta($post->ID, 'pA_price', true);
        $pA_quantity = get_post_meta($post->ID, 'pA_quantity', true);
        $pA_currency = get_post_meta($post->ID, 'pA_currency', true);

        update_post_meta($post->ID, 'pA_quantity', $_POST['pA-quantity'], $pA_quantity);
        update_post_meta($post->ID, 'pA_price', $_POST['pA-price'], $pA_price);
        update_post_meta($post->ID, 'pA_currency', $_POST['pA-currency'], $pA_currency);
    }
}

function pA_inner_custom_box() {
    global $post;

    $pA_price = get_post_meta($post->ID, 'pA_price', true);
    $pA_quantity = get_post_meta($post->ID, 'pA_quantity', true);
    $pA_currency = get_post_meta($post->ID, 'pA_currency', true);
    ?>
    <table>
        <tr>
            <td align="center">
                <label>quantity:</label><br/>
                <input type="text" name="pA-quantity" style="width:50px;" value="<?php echo $pA_quantity; ?>" />  
            </td>
            <td align="center">
                <label>price:</label> <br/>
                <input type="text" name="pA-price" style="width:50px;" value="<?php echo $pA_price; ?>" />  
            </td>
        </tr>
        <tr>
            <td>
                currency: </td>
            <td><input <?php
    if ($pA_currency == "USD") {
        echo " checked ";
    }
    ?> type="radio" name="pA-currency" value="USD" />USD / 
                <input<?php
                if ($pA_currency == "GBP") {
                    echo " checked ";
                }
    ?> type="radio" name="pA-currency" value="GBP" />GBP / 
                <input<?php
                if ($pA_currency == "EUR") {
                    echo " checked ";
                }
    ?> type="radio" name="pA-currency" value="EUR" />EUR
            </td>
        <input type="hidden" name="pA-hidd" value="true" /> 
    </tr>
    </table>
    <?php
}


function pA_admin_init() {
    add_meta_box('pA_metaid', __('quantity/price', 'pA_textdomain'), 'pA_inner_custom_box', 'post', 'side', 'high');
}

/* Add custom column to post list */

function pA_manage_posts_columns($columns) {
    global $wp_query;
    unset(
            $columns['author'], $columns['tags'], $columns['comments']
    );
    $total = 0;

    foreach ($wp_query->posts as $post) {
        $total += get_post_meta($post->ID, 'pA_price', true) * get_post_meta($post->ID, 'pA_quantity', true);
    }

    $columns = array_merge($columns, array('price' => __('price'), 'currency' => __('currency'), 'quantity' => __('quantity'), 'featured_image' => __('Image'), 'total' => __('Total: ' . $total)));
    return $columns;
}

function pA_manage_posts_custom_column($column, $post_id) {
    switch ($column) {
        case 'price':
            $pA_val = get_post_meta($post_id, 'pA_price', true);
            break;
        case 'currency':
            $pA_val = get_post_meta($post_id, 'pA_currency', true);
            break;
        case 'quantity':
            $pA_val = get_post_meta($post_id, 'pA_quantity', true);
            break;
        case 'featured_image':
            if (has_post_thumbnail())
                $pA_val = get_the_post_thumbnail($post_id,'thumbnail');

            break;
        case 'total':
            $pA_price = get_post_meta($post_id, 'pA_price', true);
            $pA_quantity = get_post_meta($post_id, 'pA_quantity', true);
            $pA_val = $pA_price * $pA_quantity;
            break;
    }

    echo $pA_val;
}

add_action('admin_init', 'pA_admin_init');
add_action('save_post', 'pA_save_postdata');
add_action('manage_posts_custom_column', 'pA_manage_posts_custom_column', 10, 2);

add_filter('manage_posts_columns', 'pA_manage_posts_columns');
?>