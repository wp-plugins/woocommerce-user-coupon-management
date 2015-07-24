<?php
	
	if ( ! defined( 'ABSPATH' ) )
		exit; // Exit if accessed directly

	add_action( 'woocommerce_coupon_options_usage_restriction', 'users_meta' );
	add_action( 'woocommerce_coupon_options_save',  			'save_meta' );

	function save_meta()
	{
		global $post;

		$posted_ids = $_POST[ 'wcum_users_id' ];
		$posted_ids = apply_filters( 'wcum_pre_save_users_ids', $posted_ids );

		$user_ids = implode( ',', array_filter( array_map( 'intval', explode( ',', $posted_ids ) ) ) );
		update_post_meta( $post->ID, 'wcum_users_id', $user_ids );
	}

	function users_meta()
	{
		global $post;

		echo '<div class="options_group">';

		?>

		<p class="form-field">
			<label><?php _e( 'User restriction', 'woocommerce' ); ?></label>

			<input
				type="hidden"
				class="wc-customer-search"
				data-multiple="true"
				style="width: 50%;"
				name="wcum_users_id"
				data-placeholder="<?php _e( 'Search for a users&hellip;', 'woocommerce' ); ?>"
				data-action="woocommerce_json_search_customers"

				data-selected="<?php
						$users_id 	= array_filter( array_map( 'absint', explode( ',', get_post_meta( $post->ID, 'wcum_users_id', true ) ) ) );
						$json_ids	= array();

						foreach ( $users_id as $user_id )
						{
							$user = get_userdata( $user_id );
							if ( $user !== false )
							{
								$json_ids[ $user_id ] = esc_html( $user->display_name ) . ' (#' . absint( $user->ID ) . ' &ndash; ' . esc_html( $user->user_email );
							}
						}

						echo esc_attr( json_encode( $json_ids ) );
					?>"

				value="<?php echo implode( ',', array_keys( $json_ids ) ); ?>"
			/>

			<img class="help_tip" data-tip='<?php _e( 'Select users allowed to use this coupon', 'woocommerce' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />

		</p>

		<?php

		echo '</div>';
	}
?>