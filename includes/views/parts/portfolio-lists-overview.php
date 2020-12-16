<h3><?php _e('Your MT4 Portfolios', 'mt4accounts-for-wp'); ?></h3>

<div class="mt4accounts-lists-overview">
    <?php if (empty($portfolios) ) {
        ?>
            <p><?php _e('No portfolios were found', 'mt4accounts-for-wp'); ?>.</p>
        <?php
        } else {
        printf('<p>' . __('Portfolios total: %d.', 'mt4accounts-for-wp') . '</p>', count($portfolios));

        echo '<table class="widefat striped">';

        $headings = array(
            __('ID', 'mt4accounts-for-wp'),
            __('Title', 'mt4accounts-for-wp'),
        );

        echo '<thead>';
        echo '<tr>';
        foreach ($headings as $heading) {
            echo sprintf('<th>%s</th>', $heading);
        }
        echo '</tr>';
        echo '</thead>';

        foreach ($portfolios as $portfolio) {
            echo '<tr>';
            echo sprintf('<td>%s</td>', esc_html($portfolio->id));
            echo sprintf('<td>%s</td>', esc_html($portfolio->title));
            echo '</tr>';
        }
        echo '</table>';

    } // end if empty?>
</div>
