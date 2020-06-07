<script type="text/javascript">

  jQuery(document).ready(function() {

    Highcharts.chart('equity_<?php echo $account_number; ?>', {
        chart: {
            zoomType: 'x'
        },
        title: {
            text: '<?php echo $caption; ?>'
        },
        xAxis: {
            type: 'datetime'
        },
        yAxis: {
            title: {
                text: null
            }
        },
        legend: {
            enabled: false
        },
        credits: { 
            enabled: false 
          },        
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },

        series: [{
            type: 'area',
            name: '$',
            data: [
            <?php foreach ($items as $time => $pl ) {
                echo '['.$time.','.$pl.'],';
            } ?>
            ]
        }]
    });
});
</script>
<div id="equity_<?php echo $account_number; ?>">
</div>