<?php
/*
Plugin Name: MT4 Accounts
Plugin URI: http://dev4traders.com
Description: MT4 Accounts for WordPress.
Version: 1.0.0
Author: dev4traders
Author URI: http://dev4traders.com
Text Domain: mt4accounts-for-wp
Domain Path: /languages
 */

// Prevent direct file access
defined('ABSPATH') or exit;

include_once(dirname(__FILE__) . '/includes/functions.php');
include_once(dirname(__FILE__) . '/includes/class-container.php');
include_once(dirname(__FILE__) . '/includes/admin/class-admin-tools.php');
include_once(dirname(__FILE__) . '/includes/admin/class-admin-messages.php');
include_once(dirname(__FILE__) . '/includes/admin/class-admin.php');
include_once(dirname(__FILE__) . '/includes/admin/class-admin-texts.php');
include_once(dirname(__FILE__) . '/includes/api/class-api.php');
include_once(dirname(__FILE__) . '/includes/api/class-api-client.php');
include_once(dirname(__FILE__) . '/includes/api/class-exception.php');
include_once(dirname(__FILE__) . '/includes/api/class-connection-exception.php');
include_once(dirname(__FILE__) . '/includes/api/class-resource-not-found-exception.php');
include_once(dirname(__FILE__) . '/includes/class-debug-log.php');
include_once(dirname(__FILE__) . '/includes/class-debug-log-reader.php');


/** @ignore */
function mt4accounts_load_plugin()
{
    global $mt4accounts;

    if (defined('MT4_ACCOUNTS_VERSION')) {
        return false;
    }

    // bootstrap the core plugin
    define('MT4_ACCOUNTS_VERSION', '1.0.0');
    define('MT4_ACCOUNTS_PLUGIN_DIR', dirname(__FILE__) . '/');
    define('MT4_ACCOUNTS_PLUGIN_URL', plugins_url('/', __FILE__));
    define('MT4_ACCOUNTS_PLUGIN_FILE', __FILE__);

    $mt4accounts = mt4accounts();
    $mt4accounts['api'] = 'mt4accounts_get_api';
    $mt4accounts['request'] = array( 'MT4_AccountsRequest', 'create_from_globals' );
    $mt4accounts['log'] = 'mt4accounts_get_debug_log';

    if (is_admin()) {
        $admin_tools = new MT4_Accounts_Admin_Tools();

        if (defined('DOING_AJAX') && DOING_AJAX) {
        } else {
            $messages = new MT4_Accounts_Admin_Messages();
            $mt4accounts['admin.messages'] = $messages;

            $admin = new MT4_Accounts_Admin($admin_tools, $messages);
            $admin->add_hooks();
        }
    }

    return true;
}


add_action('plugins_loaded', 'mt4accounts_load_plugin', 8);