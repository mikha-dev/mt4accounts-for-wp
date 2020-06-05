<?php defined('ABSPATH') or exit;

/**
 * @ignore
 */
function mt4wp_admin_translation_notice()
{

    // show for every language other than the default
    if (stripos(get_locale(), 'en_us') === 0) {
        return;
    }

    echo '<p class="help">' . 'MT4Subscriptions for WordPress is in need of translations. Is the plugin not translated in your language or do you spot errors with the current translations?</p>';
}

add_action('mt4wp_admin_footer', 'mt4wp_admin_translation_notice', 20);
?>

<div class="big-margin">

	<?php

    /**
     * Runs while printing the footer of every Mailchimp for WordPress settings page.
     *
     * @since 3.0
     */
    do_action('mt4wp_admin_footer'); ?>

</div>
