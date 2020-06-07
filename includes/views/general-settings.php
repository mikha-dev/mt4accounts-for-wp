<?php
defined('ABSPATH') or exit;
?>
<div id="mt4accounts-admin" class="wrap mt4accounts-settings">

	<div class="row">

		<!-- Main Content -->
		<div class="main-content col col-4">

			<h1 class="page-title">
                MT4Subscriptions for WordPress: <?php _e('API Settings', 'mt4accounts-for-wp'); ?>
			</h1>

			<h2 style="display: none;"></h2>
			<?php
            settings_errors();
            $this->messages->show();
            ?>

			<form action="<?php echo admin_url('options.php'); ?>" method="post">
				<?php settings_fields('mt4accounts_settings'); ?>

				<table class="form-table">

					<tr valign="top">
						<th scope="row">
							<?php _e('Status', 'mt4accounts-for-wp'); ?>
						</th>
						<td>
				<?php if ($trusted) {
                ?>
								<span class="status positive"><?php _e('CONNECTED', 'mt4accounts-for-wp'); ?></span>
							<?php
            } else {
                ?>
								<span class="status neutral"><?php _e('NOT CONNECTED', 'mt4accounts-for-wp'); ?></span>
							<?php
            } ?>
						</td>
					</tr>

				</table>

				<?php submit_button(); ?>

			</form>

			<?php

            do_action('mt4accounts_admin_after_general_settings');

            if ($trusted) {
                echo '<hr />';
                include dirname(__FILE__) . '/parts/lists-overview.php';
            }

            ?>
		</div>


	</div>

</div>