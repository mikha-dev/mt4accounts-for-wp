<h3><?php _e('Your MT4 Accounts', 'mt4accounts-for-wp'); ?></h3>

<div class="mt4accounts-lists-overview">
    <?php if (empty($accounts) ) {
        ?>
            <p><?php _e('No account were found', 'mt4accounts-for-wp'); ?>.</p>
        <?php
        } else {
        printf('<p>' . __('Accounts total: %d.', 'mt4accounts-for-wp') . '</p>', count($accounts));

        echo '<table class="widefat striped">';

        $headings = array(
            __('AccountNumber', 'mt4accounts-for-wp'),
//            __('Name', 'mt4accounts-for-wp'),
            __('Broker', 'mt4accounts-for-wp')
        );

        echo '<thead>';
        echo '<tr>';
        foreach ($headings as $heading) {
            echo sprintf('<th>%s</th>', $heading);
        }
        echo '</tr>';
        echo '</thead>';

        foreach ($accounts as $account) {
            echo '<tr>';
            echo sprintf('<td>%s</td>', esc_html($account->account_number));
  //          echo sprintf('<td>%s</td>', esc_html($account->name));
            echo sprintf('<td>%s</td>', esc_html($account->broker_server_name));
            echo '</tr>';
        }
        echo '</table>';

    } // end if empty?>
</div>
