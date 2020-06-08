<style>
#orders_history td {
  white-space:nowrap;
  padding: 2px 10px;
}

#orders_history thead th {
  white-space:nowrap;
  padding: 2px 10px;
}
</style>
<script type="text/javascript">
    var dataHistory = <?php echo $items ?>
    var myApp = myApp || {};
            jQuery(document).ready(function() {
            myApp.oTable =  jQuery('#orders_history').DataTable( {
              "paging": false,
              "ordering": false,
              "bFilter":     false,
              data: dataHistory,
              columns: [
                { 'title': 'Time' },
                { 'title': 'Symbol' },
                { 'title': 'Type' },
                { 'title': 'Lots' },
                { 'title': 'Price' },
                { 'title': 'P/L' },
              ]
            } );
  } );
</script>
<div class="grid-orders_online">
  <table id="orders_history" class="display" cellspacing="0"></table>
</div>