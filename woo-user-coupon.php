<?php

/**
 * Plugin Name: WooCommerce Coupon User Management
 * Plugin URI: 
 * Description: WooCommerce Coupon User Management
 * Version: 1.0.0
 * Author: Popov Argir
 * Author URI: 
 * License: GPL2
 *
 * @package WooCommerce Coupon User Management
 * @category Core
 * @author Popov Argir
 */

if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) )
	return; // Exit if WooCommerce plugin is not active

if ( ! class_exists( 'WooCouponUserManagement' ) ) :

	class WooCouponUserManagement
	{

		public function __construct()
		{
			$this->init_hooks();

			add_filter( 'woocommerce_coupon_is_valid', array( &$this, 'filter_coupons' ), 10, 2 );
		}

		public function init_hooks()
		{
			add_action( 'woocommerce_init', array( &$this, 'woocommerce_loaded' ) );
		}

		public function woocommerce_loaded()
		{
			include_once( 'includes/coupon-meta-fields.php' );
		}

		public function filter_coupons( $tf, $coupon )
		{
			if( $tf == false ) return false;

			$user_ID = get_current_user_id();

			if( get_post_meta( $coupon->id, 'wcum_users_id', true ) )
			{
				$users = explode( ',', get_post_meta( $coupon->id, 'wcum_users_id', true ) );
				if( in_array( $user_ID, $users ) )
					return true;
			}

			return false;
		}

	} // class WooCouponUserManagement

	$GLOBALS['wc_cum'] = new WooCouponUserManagement();

endif;

?>