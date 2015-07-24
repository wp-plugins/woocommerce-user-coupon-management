<?php

/**
 * Plugin Name: WooCommerce Coupon User Management
 * Plugin URI: 
 * Description: WooCommerce Coupon User Management
 * Version: 1.1.1
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
			$this->variables();

			add_filter( 'woocommerce_coupon_is_valid', 				array( &$this, 'filter_coupons' ), 			10, 2 );

			add_filter( 'manage_edit-shop_coupon_columns', 			array( &$this, 'add_owner_column' ), 		99 );
			add_action( 'manage_shop_coupon_posts_custom_column', 	array( &$this, 'content_owner_column' ), 	99, 2 );
		}

		public function variables()
		{
			$this->wcum_meta_id = 'wcum_users_id';
		}

		public function init_hooks()
		{
			add_action( 'woocommerce_init', array( &$this, 'woocommerce_loaded' ) );
		}

		public function woocommerce_loaded()
		{
			if( version_compare( $this->get_woocoomerce_version(), '2.3.0', '<' ) )
				include_once( 'includes/coupon-meta-fields-old.php' );
			else
				include_once( 'includes/coupon-meta-fields.php' );
		}

		public function get_woocoomerce_version()
		{
			global $woocommerce;

			if( $woocommerce )
				return $woocommerce->version;

			return false;
		}

		public function filter_coupons( $tf, $coupon )
		{
			if( $tf == false ) return false;

			$user_ID = get_current_user_id();

			if( get_post_meta( $coupon->id, $this->wcum_meta_id, true ) )
			{
				$users = explode( ',', get_post_meta( $coupon->id, $this->wcum_meta_id, true ) );
				if( in_array( $user_ID, $users ) )
					return true;
			}

			return false;
		}

		public function add_owner_column( $defaults )
		{
			$defaults[ 'coupon_owner' ] = apply_filters( 'wcum_owner_column_title', 'Coupon Owner<sub>(s)</sub>' );
			return $defaults;
		}

		public function content_owner_column( $column_name, $post_ID )
		{
			if( $column_name == 'coupon_owner' )
			{
				$owners = get_post_meta( $post_ID, $this->wcum_meta_id, true );
				$owners = explode( ',', $owners );

				$output 	= '';

				$user_data 	= get_user_meta( $owners[0], 'nickname', true );
				$output 	= $user_data;

				if( count( $owners ) > 1 )
					$output .= sprintf(
						'<br> <a href="%s">+%s</a>',
						get_edit_post_link( $post_ID ),
						( count( $owners ) - 1 ) . ' more'
					);

				echo apply_filters( 'wcum_owner_column_content', $output, $owners, $post_ID );
			}
		}

	} // class WooCouponUserManagement

	$GLOBALS['wc_cum'] = new WooCouponUserManagement();

endif;

?>