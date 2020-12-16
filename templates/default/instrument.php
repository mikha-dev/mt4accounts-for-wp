<style>
</style>
<script type="text/javascript">
  jQuery(document).ready(function() {

      Highcharts.chart('instrument_<?php echo $id; ?>', {
          chart: {
              plotBackgroundColor: null,
              plotBorderWidth: null,
              plotShadow: false,
              type: 'pie'
          },
          title: {
              text: 'Distribution of Instruments'
          },
          credits: {
            enabled: false
          },
          tooltip: {
              pointFormat: '<b>{point.percentage:.2f}%</b>'
          },
          plotOptions: {
              pie: {
                  allowPointSelect: true,
                  cursor: 'pointer',
                  dataLabels: {
                      enabled: true,
                      format: '<b>{point.name}</b>: {point.percentage:.2f} %',
                      style: {
                          color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                      }
                  }
              }
          },
          series: [{
              colorByPoint: true,
              data: [
                <?php foreach ($items as $key => $val) {
                 echo "{ name: '$key', y: $val },";
                } ?>
              ]
          }]
      });
  });
</script>
<div id="instrument_<?php echo $id; ?>">
</div>