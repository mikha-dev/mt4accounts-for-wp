<style>
#orders_history td {
  white-space:nowrap;
  padding: 2px 10px;
  font-size: 13px;
}

#orders_history thead th {
  white-space:nowrap;
  padding: 2px 8px;
  font-size: 13px;
}
</style>
<script type="text/javascript">
    var dataHistory = <?php echo $items ?>
    var myApp = myApp || {};
            jQuery(document).ready(function() {
            myApp.oTable =  jQuery('#orders_history').DataTable( {
              "paging": false,
              "bFilter":     false,
              responsive: true,
              data: dataHistory,
              rowReorder: {
                selector: 'td:nth-child(2)'
              },
              columns: [
                { 'title': 'Open Date' },
                { 'title': 'Close Date' },
                { 'title': 'Symbol' },
                { 'title': 'Type' },
                { 'title': 'Lots' },
                { 'title': 'SL' },
                { 'title': 'TP' },
                { 'title': 'Open' },
                { 'title': 'Close' },
                { 'title': 'Pips' },
                { 'title': 'Profit' },
              ]
            } );
  } );
</script>
<div style="width: 90%">
  <table id="orders_history" class="display nowrap"></table>
</div>