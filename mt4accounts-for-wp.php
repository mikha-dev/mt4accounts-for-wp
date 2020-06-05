<?php
/*
Plugin Name: MT4 Subscriptions
Plugin URI: http://dev4traders.com
Description: mt4subscriptions for WordPress.
Version: 1.0.0
Author: dev4traders
Author URI: http://dev4traders.com
Text Domain: mt4subscriptions-for-wp
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
function _mt4wp_load_plugin()
{
    global $mt4wp;

    if (defined('MT4WP_VERSION')) {
        return false;
    }

    // bootstrap the core plugin
    define('MT4WP_VERSION', '1.0.0');
    define('MT4WP_PLUGIN_DIR', dirname(__FILE__) . '/');
    define('MT4WP_PLUGIN_URL', plugins_url('/', __FILE__));
    define('MT4WP_PLUGIN_FILE', __FILE__);

    // load autoloader if function not yet exists (for compat with sitewide autoloader)
//    if (! function_exists('mt4wp')) {
  //      require_once MT4WP_PLUGIN_DIR . 'vendor/autoload_52.php';
    //}

    /**
     * @global MT4WP_Container $GLOBALS['mt4wp']
     * @name $mt4wp
     */
    $mt4wp = mt4wp();
    $mt4wp['api'] = 'mt4wp_get_api';
    $mt4wp['request'] = array( 'MT4WP_Request', 'create_from_globals' );
    $mt4wp['log'] = 'mt4wp_get_debug_log';

    if (is_admin()) {
        $admin_tools = new MT4WP_Admin_Tools();

        if (defined('DOING_AJAX') && DOING_AJAX) {
        } else {
            $messages = new MT4WP_Admin_Messages();
            $mt4wp['admin.messages'] = $messages;

            $admin = new MT4WP_Admin($admin_tools, $messages);
            $admin->add_hooks();
        }
    }

    return true;
}

function mt4_rcp_membership_post_activate($membership_id, $membership) {
    $user_id = $membership->get_customer()->get_user_id();
    $level_id = $membership->get_object_id();


}

function mt4_rcp_transition_membership_status_cancelled($old_status, $membership_id) {
    $membership = rcp_get_membership($membership_id);

    if(!$membership)
        return;

    $api = mt4wp_get_api();

    $user_id = $membership->get_customer()->get_user_id();
    $user = get_userdata($user_id);
    $email = $user->user_email;

    $api->delete_emailsubscription($email, 'all');
    $api->delete_copiersubscription($email, 'all');
}

function mt4_rcp_subscription_form(){
    $api = mt4wp_get_api();

    $rcpLevelId =  isset( $_GET['edit_subscription'] ) ?  $_GET['edit_subscription'] : 0;
    $copier_groups = $api->list_copiersubscription_groups();
    $email_groups = $api->list_emailsubscription_groups();

    $copier_group_id = get_option("mt4_copier_group_".$rcpLevelId);
    $email_group_id = get_option("mt4_email_group_".$rcpLevelId);
    $max_copier_subscriptions = get_option("mt4_max_copier_subscriptions_".$rcpLevelId);
    $max_copier_accounts = get_option("mt4_max_copier_accounts_".$rcpLevelId);
    $max_email_subscriptions = get_option("mt4_max_email_subscriptions_".$rcpLevelId);

    ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="mt4-copiersubscription-group"><?php _e( 'MT4Subscription Copier Group', 'mt4subscriptions' ); ?></label>
        </th>
        <td>
                <select name="mt4-copiersubscription-group" id="mt4-copiersubscription-group" style="width:400px">
                    <option value=""> -- </option>
                    <?php foreach($copier_groups as $group):  ?>
                    <option  <?php echo  ($group->id == $copier_group_id) ? "selected='selected'":"";   ?> value="<?php  echo $group->id ?>"> <?php  echo $group->title ?> </option>
                    <?php  endforeach; ?>
                </select>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="mt4-copiersubscription-max"><?php _e( 'MT4Subscription Max Copiers', 'mt4subscriptions' ); ?></label>
        </th>
        <td>
            <input type="number" name="mt4-copiersubscription-max" id="mt4-copiersubscription-max" value="<?php echo $max_copier_subscriptions ?>" style="width: 50px;" />
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="mt4-copieraccounts-max"><?php _e( 'MT4Subscription Max Accounts', 'mt4subscriptions' ); ?></label>
        </th>
        <td>
            <input type="number" name="mt4-copieraccounts-max" id="mt4-copieraccounts-max" value="<?php echo $max_copier_accounts ?>" style="width: 50px;" />
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="mt4-emailsubscription-group"><?php _e( 'MT4Subscription Email Group', 'mt4subscriptions' ); ?></label>
        </th>
        <td>
                <select name="mt4-emailsubscription-group" id="mt4-emailsubscription-group" style="width:400px">
                    <option value=""> -- </option>
                    <?php foreach($email_groups as $group):  ?>
                    <option  <?php echo  ($group->id == $email_group_id) ? "selected='selected'":"";   ?> value="<?php  echo $group->id ?>"> <?php  echo $group->title ?> </option>
                    <?php  endforeach; ?>
                </select>
        </td>
    </tr>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="mt4-emailsubscription-max"><?php _e( 'MT4Subscription Max Emails', 'mt4subscriptions' ); ?></label>
        </th>
        <td>
            <input type="number" name="mt4-emailsubscription-max" id="mt4-emailsubscription-max" value="<?php echo $max_email_subscriptions ?>" style="width: 50px;" />
        </td>
    </tr>
  <?php
}

function rcp_transition_membership_status_active($old_status, $membership_id) {
    $membership = rcp_get_membership($membership_id);

    if(!$membership)
        return;

    $level_id = $membership->get_object_id();
    $user_id = $membership->get_customer()->get_user_id();

    mt4_add_to_rcp_subscription( $user_id, $level_id );
}

function mt4_rcp_add_subscription($level_id = 0, $args){
    $api = mt4wp_get_api();

    if( isset( $_POST['mt4-emailsubscription-max'] ) ) {
        update_option( 'mt4_max_email_subscriptions_'.$level_id, $_POST['mt4-emailsubscription-max'] );
    }

    if( isset( $_POST['mt4-copiersubscription-max'] ) ) {
        update_option( 'mt4_max_copier_subscriptions_'.$level_id, $_POST['mt4-copiersubscription-max'] );
    }

    if( isset( $_POST['mt4-copieraccounts-max'] ) ) {
        update_option( 'mt4_max_copier_accounts_'.$level_id, $_POST['mt4-copieraccounts-max'] );
    }

    if( isset( $_POST['mt4-emailsubscription-group'] ) ) {
        $group_id = $_POST['mt4-emailsubscription-group'];
        $group = $api->get_copiersubscription_group($group_id);

        update_option( 'mt4_email_group_'.$level_id, $group_id );
        update_option( 'mt4_email_group_title_'.$level_id, $group->title );
    }

    if( isset( $_POST['mt4-copiersubscription-group'] ) ) {
        $group_id = $_POST['mt4-copiersubscription-group'];
        $group = $api->get_emailsubscription_group($group_id);

        update_option( 'mt4_copier_group_'.$level_id, $group_id );
        update_option( 'mt4_copier_group_title_'.$level_id, $group->title );
    }
}

function mt4_rcp_levels_page_table_column($level_id){
    echo
    get_option( 'mt4_copier_group_title_'.$level_id ).
        '(max: '.get_option( 'mt4_max_copier_subscriptions_'.$level_id ).'; '.
        'accounts: '.get_option( 'mt4_max_copier_accounts_'.$level_id ).');';

    echo get_option( 'mt4_email_group_title_'.$level_id ).
        '(max: '.get_option( 'mt4_max_email_subscriptions_'.$level_id ).')';
}

add_action( 'set_user_role', function( $user_id, $role, $old_roles )  {
    $api = mt4wp_get_api();

    $user = get_userdata($user_id);
    $email = $user->user_email;
    $name = $user->display_name;

    $copierSubscriptions = get_option('mt4wp_copiersubscriptions');
    $emailSubscriptions = get_option('mt4wp_emailsubscriptions');

    //var_dump($emailSubscriptions);
    //exit();

    foreach($old_roles as $old_role ) {
        $email_subscription_ids = '';
        $copier_subscription_ids = '';

        if(isset($emailSubscriptions[$old_role]))
            $email_subscription_ids = $emailSubscriptions[$old_role];

        if(isset($copierSubscriptions[$old_role]))
            $copier_subscription_ids = $copierSubscriptions[$old_role];

        if(!empty($email_subscription_ids))
            $api->delete_emailsubscription($email, $email_subscription_ids);

        if(!empty($copier_subscription_ids))
            $api->delete_copiersubscription($email, $copier_subscription_ids);
    }

    $email_subscription_ids = '';
    $copier_subscription_ids = '';
    if(isset($emailSubscriptions[$role]))
        $email_subscription_ids = $emailSubscriptions[$role];

    if(isset($copierSubscriptions[$role]))
        $copier_subscription_ids = $copierSubscriptions[$role];

    if(!empty($email_subscription_ids)) {
        $api->create_emailsubscription($name, $email, $email_subscription_ids);
    }


    if(!empty($copier_subscription_ids))
        $api->create_copiersubscription($name, $email, $copier_subscription_ids);

}, 10, 3 );


add_action( 'delete_user', function( $user_id )  {
    $api = mt4wp_get_api();

    $user = get_userdata($user_id);
    $email = $user->user_email;

    $api->delete_emailsubscription($email, 'all');
    $api->delete_copiersubscription($email, 'all');
}, 10, 3 );

// bootstrap custom integrations
function _mt4wp_bootstrap_integrations()
{
    require_once MT4WP_PLUGIN_DIR . 'integrations/bootstrap.php';
}

add_action('plugins_loaded', '_mt4wp_load_plugin', 8);
//add_action('plugins_loaded', '_mt4wp_bootstrap_integrations', 90);

/**
 * Flushes transient cache & schedules refresh hook.
 *
 * @ignore
 * @since 3.0
 */
function _mt4wp_on_plugin_activation()
{
    $time_string = sprintf("tomorrow 0%d:%d%d", rand(0, 8), rand(0, 5), rand(0, 9));
    wp_schedule_event(strtotime($time_string), 'daily', 'mt4wp_refresh_subscription_lists');
}

/**
 * Clears scheduled hook for refreshing Mailchimp lists.
 *
 * @ignore
 * @since 4.0.3
 */
function _mt4wp_on_plugin_deactivation()
{
    global $wpdb;
    wp_clear_scheduled_hook('mt4wp_refresh_subscription_lists');

    $wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE 'mt4wp_subscription_list_%'");
}

add_action( 'rcp_add_subscription_form', 'mt4_rcp_subscription_form' );
add_action( 'rcp_edit_subscription_form', 'mt4_rcp_subscription_form' );

add_action( 'rcp_add_subscription', 'mt4_rcp_add_subscription', 10, 2 );
add_action( 'rcp_edit_subscription_level', 'mt4_rcp_add_subscription', 10, 2 );

add_action('rcp_levels_page_table_column', "mt4_rcp_levels_page_table_column");

add_action("rcp_levels_page_table_header","mt4_rcp_levels_page_table_header");

add_action("rcp_membership_post_activate", "mt4_rcp_membership_post_activate", 10, 2 );

add_action("rcp_transition_membership_status_cancelled", "mt4_rcp_transition_membership_status_cancelled", 10, 2 );
add_action("rcp_transition_membership_status_expired", "mt4_rcp_transition_membership_status_cancelled", 10, 2 );
add_action("rcp_transition_membership_status_active", "rcp_transition_membership_status_active", 10, 2 );

add_action('rcp_new_membership_added', function($membership_id, $data) {

    $level_id = $data['object_id'];
    $customer = rcp_get_customer($data['customer_id']);
    $user_id = $customer->get_user_id();

    mt4_add_to_rcp_subscription($user_id, $level_id);
}
, 10, 2 );

register_activation_hook(__FILE__, '_mt4wp_on_plugin_activation');
register_deactivation_hook(__FILE__, '_mt4wp_on_plugin_deactivation');

function mt4_add_to_rcp_subscription($user_id, $level_id) {
    $copier_group_id = get_option("mt4_copier_group_".$level_id);
    $email_group_id = get_option("mt4_email_group_".$level_id);
    $max_email_subscriptions = get_option("mt4_max_email_subscriptions_".$level_id);
    $max_copier_subscriptions = get_option("mt4_max_copier_subscriptions_".$level_id);
    $max_copier_accounts = get_option("mt4_max_copier_accounts_".$level_id);

    $api = mt4wp_get_api();

    if(!empty($email_group_id)) {
        $user = get_userdata($user_id);
        $email = $user->user_email;
        $name = $user->display_name;

        $api->create_emailsubscription_for_group($email_group_id, $name, $email, $max_email_subscriptions);
    }

    if(!empty($copier_group_id)) {
        $user = get_userdata($user_id);
        $email = $user->user_email;
        $name = $user->display_name;

        $api->create_copiersubscription_for_group($copier_group_id, $name, $email,
            $max_copier_subscriptions, $max_copier_accounts);
    }
}