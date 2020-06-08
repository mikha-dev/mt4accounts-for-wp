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
              "autoWidth": true,
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
<div style="width: 50%">
  <table id="orders_history" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0"></table>
</div>