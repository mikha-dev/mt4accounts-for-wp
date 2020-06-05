<h3><?php _e('Your MT4Subscriptions', 'mt4subscriptions-for-wp'); ?></h3>

<div class="mt4wp-lists-overview">
    <form method="post" action="">
		<input type="hidden" name="_mt4wp_action" value="connect_roles_subscriptions" />

        <?php if (empty($email_items) && empty($copier_items)) {
        ?>
            <p><?php _e('No lists were found in your MT4Subscriptions', 'mt4subscriptions-for-wp'); ?>.</p>
        <?php
        } else {
        printf('<p>' . __('Email subscriptions total: %d.', 'mt4subscriptions-for-wp') . '</p>', count($email_items));

        echo '<table class="widefat striped">';

        $headings = array(
            __('ID', 'mt4subscriptions-for-wp'),
            __('Name', 'mt4subscriptions-for-wp'),
            //__('Subscribers', 'mt4subscriptions-for-wp'),
            __('Role', 'mt4subscriptions-for-wp')
        );

        echo '<thead>';
        echo '<tr>';
        foreach ($headings as $heading) {
            echo sprintf('<th>%s</th>', $heading);
        }
        echo '</tr>';
        echo '</thead>';

        foreach ($email_items as $item) {
            echo '<tr>';
            echo sprintf('<td><code>%s</code></td>', esc_html($item->id));
            echo sprintf('<td>%s</td>', esc_html($item->title));
            //echo sprintf('<td>%s</td>', esc_html($item->subscriber_count));//todo:: do count
            //echo sprintf('<td>%s</td>', esc_html($item->id));
            echo sprintf( '<td><select id="emailrole" name="emailrole_%d">', $item->id );

            foreach($roles as $role) {
                echo sprintf('<option %s value="%s">%s</option>', 
                    $item->role == $role->role ? 'selected': '', esc_html($role->role), esc_html($role->name));
            }
            echo '</select></td>';
            echo '</tr>';
        } // end foreach $items
        echo '</table>';

        printf('<p>' . __('Copier subscriptions total: %d.', 'mt4subscriptions-for-wp') . '</p>', count($copier_items));

        echo '<table class="widefat striped">';

        $headings = array(
            __('ID', 'mt4subscriptions-for-wp'),
            __('Name', 'mt4subscriptions-for-wp'),
            //__('Subscribers', 'mt4subscriptions-for-wp'),
            __('Role', 'mt4subscriptions-for-wp')
        );

        echo '<thead>';
        echo '<tr>';
        foreach ($headings as $heading) {
            echo sprintf('<th>%s</th>', $heading);
        }
        echo '</tr>';
        echo '</thead>';

        foreach ($copier_items as $item) {
            echo '<tr>';
            echo sprintf('<td><code>%s</code></td>', esc_html($item->id));
            echo sprintf('<td>%s</td>', esc_html($item->title));
            //echo sprintf('<td>%s</td>', esc_html($item->subscriber_count));//todo:: do count
            //echo sprintf('<td>%s</td>', esc_html($item->id));
            echo sprintf( '<td><select id="copierrole" name="copierrole_%d">', $item->id );

            foreach($roles as $role) {
                echo sprintf('<option %s value="%s">%s</option>', 
                    $item->role == $role->role ? 'selected': '', esc_html($role->role), esc_html($role->name));
            }
            echo '</select></td>';
            echo '</tr>';
        } // end foreach $items
        echo '</table>';

        echo '<p>';
        echo '<input type="submit" value="Save Changes" class="button" />';
        echo '</p>';
    } // end if empty?>
    </form>
</div>
