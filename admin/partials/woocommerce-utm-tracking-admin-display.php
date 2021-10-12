<?php

/**
 * Provide an admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://nickjeffers.com
 * @since      1.0.0
 *
 * @package    Woocommerce_Utm_Tracking
 * @subpackage Woocommerce_Utm_Tracking/admin/partials
 */

	$post_id = (int) $_GET['post'];

?>

<table class="styled-table">
    <thead>
    <tr>
        <th>Key</td>
        <th>Value</td>
        </tr>
    </thead>
    <tbody>
    <?php
    foreach( Woocommerce_Utm_Tracking::get_tracking_meta_keys()  as $meta_key ) {

        $value = get_post_meta( $post_id, Woocommerce_Utm_Tracking::$meta_key_prefix . $meta_key, true );

        if( $value ){
    ?>
        <tr>
            <td class="styled-table-key"><?php echo $meta_key; ?></td>
            <td class="styled-table-value"><?php echo $value; ?></td>
        </tr>
    <?php
        }

    }
    ?>
    </tbody>
</table>