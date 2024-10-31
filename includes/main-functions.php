<?php
/**
 * Get the product's like count.
 *
 * @param $product_id
 *
 * @return int
 */
function plah_get_product_likes( $product_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . PLAH_PLUGIN_LIKES_TABLE_NAME;


    $product_id = absint( $product_id );
    if ( $product_id > 0 ) {
        $query           = $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE product_id = %d", $product_id );
        $product_counter = $wpdb->get_var( $query );
    }

    if ( empty( $product_counter ) ) {
        $product_counter = 0;
    }

    return absint( $product_counter );
}

/**
 * Get the product's hide count.
 *
 * @param $product_id
 *
 * @return int
 */
function plah_get_product_hides( $product_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . PLAH_PLUGIN_HIDES_TABLE_NAME;


    $product_id = absint( $product_id );
    if ( $product_id > 0 ) {
        $query           = $wpdb->prepare( "SELECT COUNT(*) FROM $table_name WHERE product_id = %d", $product_id );
        $product_counter = $wpdb->get_var( $query );
    }

    if ( empty( $product_counter ) ) {
        $product_counter = 0;
    }

    return absint( $product_counter );
}

/**
 * Get a user's hidden product IDs
 *
 * @param $user_id
 *
 * @return mixed
 */
function plah_get_hidden_products( $user_id ) {
    global $wpdb;
    $table_name = $wpdb->prefix . PLAH_PLUGIN_HIDES_TABLE_NAME;

    $user_id = absint( $user_id );
    if ( $user_id > 0 ) {
        $query       = $wpdb->prepare( "SELECT product_id FROM $table_name WHERE user_id = %d", $user_id );
        $product_ids = $wpdb->get_col( $query );
    }

    return apply_filters( '', (array) $product_ids, $user_id );
}

/**
 * Get the Allow Like setting.
 *
 * @param $product_id
 *
 * @return mixed
 */

function plah_is_allowed_to_like( $product_id ) {
    $is_allowed = plah_get_setting( 'allow_likes' );

    return apply_filters( 'plah_is_allowed_to_like', $is_allowed, $product_id );
}

function plah_is_allowed_to_hide( $product_id ) {

    $is_allowed = false;

    // Check the setting only if the user is logged in
    if ( is_user_logged_in() ) {
        $is_allowed = plah_get_setting( 'allow_hides' );
    }

    return apply_filters( 'plah_is_allowed_to_hide', $is_allowed, $product_id );
}

/**
 * Get a specific setting
 *
 * @param $setting
 */
function plah_get_setting( $setting, $boolean = true ) {
    if ( ! class_exists( 'PLAH_Product_Like_And_Hide_Settings' ) ) {
        return;
    }

    $settings      = get_option( PLAH_Product_Like_And_Hide_Settings::$plah_option_name, array() );
    $setting_value = isset( $settings[ $setting ] ) ? $settings[ $setting ] : '';

    if ( $boolean ) {
        $setting_value = (bool) $setting_value;
    }

    return apply_filters( 'plah_get_setting', $setting_value, $settings, $setting );

}

/**
 * Get the like icon.
 *
 * @param $product_id
 *
 * @return string $icon
 */
function plah_get_like_icon( $product_id = 0 ) {
    $icon = '';

    if ( empty( $icon ) ) {
        $icon = PLAH_PLUGIN_URL . 'assets/images/like.png';
    }

    return apply_filters( 'plah_get_like_icon', $icon, $product_id );
}

/**
 * Get the hide icon.
 *
 * @param $product_id
 *
 * @return string $icon
 */
function plah_get_hide_icon( $product_id = 0 ) {
    $icon = '';

    if ( empty( $icon ) ) {
        $icon = PLAH_PLUGIN_URL . 'assets/images/hide.png';
    }

    return apply_filters( 'plah_get_hide_icon', $icon, $product_id );
}

/**
 * Has user liked the product?
 *
 * @param $product_id
 * @param $user_id
 *
 * @return bool
 */
function plah_is_product_liked( $product_id, $user_id = 0 ) {
    global $wpdb;

    $product_id = absint( $product_id );
    $user_id    = absint( $user_id );

    if ( $product_id <= 0 || $user_id <= 0 ) {
        return false;
    }

    $query = $wpdb->prepare( "SELECT  count(ID) as counter 
        FROM {$wpdb->prefix}" . PLAH_PLUGIN_LIKES_TABLE_NAME . " 
        WHERE product_id = %d",
        $product_id );

    if ( $user_id > 0 ) {
        $query .= $wpdb->prepare( " AND user_id = %d", $user_id );
    }
    $likes = $wpdb->get_var( $query );

    $has_user_liked_product = false;
    if ( $likes > 0 ) {
        $has_user_liked_product = true;
    }

    return apply_filters( 'plah_has_user_liked_product',
        $has_user_liked_product, $product_id, $user_id );
}

/**
 * Has user Hidden the product?
 *
 * @param $product_id
 * @param $user_id
 *
 * @return bool
 */
function plah_is_product_hidden( $product_id, $user_id = 0 ) {
    global $wpdb;

    $product_id = absint( $product_id );
    $user_id    = absint( $user_id );

    if ( $product_id <= 0 || $user_id <= 0 ) {
        return false;
    }

    $query = $wpdb->prepare( "SELECT  count(ID) as counter 
        FROM {$wpdb->prefix}" . PLAH_PLUGIN_HIDES_TABLE_NAME . " 
        WHERE product_id = %d",
        $product_id );

    if ( $user_id > 0 ) {
        $query .= $wpdb->prepare( " AND user_id = %d", $user_id );
    }
    $hides = $wpdb->get_var( $query );

    $has_user_liked_product = false;
    if ( $hides > 0 ) {
        $has_user_liked_product = true;
    }

    return apply_filters( 'plah_has_user_liked_product',
        $has_user_liked_product, $product_id, $user_id );
}
