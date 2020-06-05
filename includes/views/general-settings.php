<?php
defined('ABSPATH') or exit;
?>
<div id="mt4wp-admin" class="wrap mt4wp-settings">

	<div class="row">

		<!-- Main Content -->
		<div class="main-content col col-4">

			<h1 class="page-title">
                MT4Subscriptions for WordPress: <?php _e('API Settings', 'mt4subscriptions-for-wp'); ?>
			</h1>

			<h2 style="display: none;"></h2>
			<?php
            settings_errors();
            $this->messages->show();
            ?>

			<form action="<?php echo admin_url('options.php'); ?>" method="post">
				<?php settings_fields('mt4wp_settings'); ?>

				<table class="form-table">

					<tr valign="top">
						<th scope="row">
							<?php _e('Status', 'mt4subscriptions-for-wp'); ?>
						</th>
						<td>
							<?php if ($connected) {
                ?>
								<span class="status positive"><?php _e('CONNECTED', 'mt4subscriptions-for-wp'); ?></span>
							<?php
            } else {
                ?>
								<span class="status neutral"><?php _e('NOT CONNECTED', 'mt4subscriptions-for-wp'); ?></span>
							<?php
            } ?>
						</td>
					</tr>


					<tr valign="top">
						<th scope="row"><label for="mailchimp_api_key"><?php _e('API Key', 'mt4subscriptions-for-wp'); ?></label></th>
						<td>
							<input type="text" class="widefat" placeholder="<?php _e('API key', 'mt4subscriptions-for-wp'); ?>" id="mailchimp_api_key" name="mt4wp[api_key]" value="<?php echo esc_attr($obfuscated_api_key); ?>" <?php echo defined('MT4WP_API_KEY') ? 'readonly="readonly"' : ''; ?> />
							<p class="help">
								<a target="_blank" href="<?php echo MT4WP_API_URL?>admin/user/setting"><?php _e('Get your API key here.', 'mt4subscriptions-for-wp'); ?></a>
							</p>

							<?php if (defined('MT4WP_API_KEY')) {
                                echo '<p class="help">'. __('You defined your API key using the <code>MT4WP_API_KEY</code> constant.', 'mt4subscriptions-for-wp') . '</p>';
                            } ?>
						</td>

					</tr>

				</table>

				<?php submit_button(); ?>

			</form>

			<?php

            /**
             * Runs right after general settings are outputted in admin.
             *
             * @since 3.0
             * @ignore
             */
            do_action('mt4wp_admin_after_general_settings');

            if (! empty($opts['api_key'])) {
                echo '<hr />';
                include dirname(__FILE__) . '/parts/lists-overview.php';
            }

            include dirname(__FILE__) . '/parts/admin-footer.php';

            ?>
		</div>


	</div>

</div>

