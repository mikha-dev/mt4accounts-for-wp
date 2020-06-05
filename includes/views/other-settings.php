<?php
defined('ABSPATH') or exit;

/** @var MC4WP_Debug_Log $log */
/** @var MC4WP_Debug_Log_Reader $log_reader */

/**
 * @ignore
 * @param array $opts
 */
function _mt4wp_usage_tracking_setting($opts)
{
    ?>
	<div class="medium-margin" >
		<h3><?php _e('Miscellaneous settings', 'mt4subscriptions-for-wp'); ?></h3>
		<table class="form-table">
			<tr>
				<th><?php _e('Logging', 'mt4subscriptions-for-wp'); ?></th>
				<td>
					<select name="mt4wp[debug_log_level]">
						<option value="warning" <?php selected('warning', $opts['debug_log_level']); ?>><?php _e('Errors & warnings only', 'mt4subscriptions-for-wp'); ?></option>
						<option value="debug" <?php selected('debug', $opts['debug_log_level']); ?>><?php _e('Everything', 'mt4subscriptions-for-wp'); ?></option>
					</select>
					<p class="help">
						<?php printf(__('Determines what events should be written to <a href="%s">the debug log</a> (see below).', 'mt4subscriptions-for-wp'), 'https://kb.mt4wp.com/how-to-enable-log-debugging/#utm_source=wp-plugin&utm_medium=mt4subscriptions-for-wp&utm_campaign=settings-page'); ?>
					</p>
				</td>
			</tr>
		</table>
	</div>
	<?php
}

add_action('mt4wp_admin_other_settings', '_mt4wp_usage_tracking_setting', 70);
?>
<div id="mt4wp-admin" class="wrap mt4wp-settings">

	<div class="row">

		<!-- Main Content -->
		<div class="main-content col col-4">

			<h1 class="page-title">
				<?php _e('Other Settings', 'mt4subscriptions-for-wp'); ?>
			</h1>

			<h2 style="display: none;"></h2>
			<?php settings_errors(); ?>

			<?php
            /**
             * @ignore
             */
            do_action('mt4wp_admin_before_other_settings', $opts);
            ?>

			<!-- Settings -->
			<form action="<?php echo admin_url('options.php'); ?>" method="post">
				<?php settings_fields('mt4wp_settings'); ?>

				<?php
                /**
                 * @ignore
                 */
                do_action('mt4wp_admin_other_settings', $opts);
                ?>

				<div style="margin-top: -20px;"><?php submit_button(); ?></div>
			</form>

			<!-- Debug Log -->
			<div class="medium-margin">
				<h3><?php _e('Debug Log', 'mt4subscriptions-for-wp'); ?> <input type="text" id="debug-log-filter" class="alignright regular-text" placeholder="<?php esc_attr_e('Filter..', 'mt4subscriptions-for-wp'); ?>" /></h3>

				<?php
                if (! $log->test()) {
                    echo '<p>';
                    echo __('Log file is not writable.', 'mt4subscriptions-for-wp') . ' ';
                    echo  sprintf(__('Please ensure %s has the proper <a href="%s">file permissions</a>.', 'mt4subscriptions-for-wp'), '<code>' . $log->file . '</code>', 'https://codex.wordpress.org/Changing_File_Permissions');
                    echo '</p>';

                    // hack to hide filter input
                    echo '<style type="text/css">#debug-log-filter { display: none; }</style>';
                } else {
                    ?>
					<div id="debug-log" class="mt4wp-log widefat">
						<?php
                        $line = $log_reader->read_as_html();

                    if (!empty($line)) {
                        while (is_string($line)) {
                            if (! empty($line)) {
                                echo '<div class="debug-log-line">' . $line . '</div>';
                            }
                                
                            $line = $log_reader->read_as_html();
                        }
                    } else {
                        echo '<div class="debug-log-empty">';
                        echo '-- ' . __('Nothing here. Which means there are no errors!', 'mt4subscriptions-for-wp');
                        echo '</div>';
                    } ?>
					</div>

					<form method="post">
						<input type="hidden" name="_mt4wp_action" value="empty_debug_log">
						<p>
							<input type="submit" class="button"
								   value="<?php esc_attr_e('Empty Log', 'mt4subscriptions-for-wp'); ?>"/>
						</p>
					</form>
					<?php
                } // end if is writable

                if ($log->level >= 300) {
                    echo '<p>';
                    echo __('Right now, the plugin is configured to only log errors and warnings.', 'mt4subscriptions-for-wp');
                    echo '</p>';
                }
                ?>

				<script>
					(function() {
						'use strict';
						// scroll to bottom of log
						var log = document.getElementById("debug-log"),
							logItems;
						log.scrollTop = log.scrollHeight;
						log.style.minHeight = '';
						log.style.maxHeight = '';
						log.style.height = log.clientHeight + "px";

						// add filter
						var logFilter = document.getElementById('debug-log-filter');
						logFilter.addEventListener('keydown', function(e) {
							if(e.keyCode == 13 ) {
								searchLog(e.target.value.trim());
							}
						});

						// search log for query
						function searchLog(query) {
							if( ! logItems ) {
								logItems = [].map.call(log.children, function(node) {
									return node.cloneNode(true);
								})
							}

							var ri = new RegExp(query.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&"), 'i');
							var newLog = log.cloneNode();
							logItems.forEach(function(node) {
								if( ! node.textContent ) { return ; }
								if( ! query.length || ri.test(node.textContent) ) {
									newLog.appendChild(node);
								}
							});

							log.parentNode.replaceChild(newLog,log);
							log = newLog;
							log.scrollTop = log.scrollHeight;
						}
					})();
				</script>
			</div>
			<!-- / Debug Log -->



			<?php include dirname(__FILE__) . '/parts/admin-footer.php'; ?>
		</div>

	</div>

</div>