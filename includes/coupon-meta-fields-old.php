<?php
	
	if ( ! defined( 'ABSPATH' ) )
		exit; // Exit if accessed directly

	add_action( 'woocommerce_coupon_options_usage_restriction', 'users_meta' );
	add_action( 'woocommerce_coupon_options_save',  			'save_meta' );

	function save_meta( $post_id )
	{
		$posted_ids = $_POST[ 'wcum_users_id' ];

		$posted_ids = apply_filters( 'wcum_pre_save_users_ids', implode( ',', $posted_ids ) );
		update_post_meta( $post_id, 'wcum_users_id', $posted_ids );
	}

	function users_meta()
	{
		global $post;
		
		echo '<div class="options_group">';
		?>

		<p class="form-field">
			<label for="wcum_users_id"><?php _e( 'User restriction', 'woocommerce' ); ?></label>

			<select id="wcum_users_id" name="wcum_users_id[]" class="ajax_chosen_select_customer" multiple="multiple" data-placeholder="Search for a users&hellip;">
				<?php
					$users_id 	= explode( ',', get_post_meta( $post->ID, 'wcum_users_id', true ) );

					foreach( $users_id as $user_id )
					{
						$user = get_userdata( $user_id );
						if( $user !== false )
						{
							echo '<option value="' . esc_attr( $user_id ) . '" ' . selected( 1, 1, false ) . '>';
							echo esc_html( $user->display_name ) . ' (#' . absint( $user_id ) . ' &ndash; ' . esc_html( $user->user_email ) . ')';
							echo '</option>';
						}
					}
				?>
			</select>

			<img class="help_tip" data-tip='<?php _e( 'Select users allowed to use this coupon', 'woocommerce' ); ?>' src="<?php echo WC()->plugin_url(); ?>/assets/images/help.png" height="16" width="16" />

		</p>

		<?php

		echo '</div>';
	}

	// Ajax Chosen Customer Selectors JS
	wc_enqueue_js( "
		jQuery( 'select.ajax_chosen_select_customer' ).ajaxChosen({
			method:         'GET',
			url:            '" . admin_url( 'admin-ajax.php' ) . "',
			dataType:       'json',
			afterTypeDelay: 100,
			data:           {
				action:   'woocommerce_json_search_customers',
				security: '" . wp_create_nonce( 'search-customers' ) . "'
			}
		}, function ( data ) {

			var terms = {};

			$.each( data, function ( i, val ) {
				terms[i] = val;
			});

			return terms;
		});
	" );
?>