<style>
#orders_history td {
  white-space:nowrap;
}
</style>
<script type="text/javascript">
    var dataHistory = <?php echo $items ?>
    var myApp = myApp || {};
            jQuery(document).ready(function() {
            myApp.oTable =  jQuery('#orders_history').DataTable( {
              "paging": false,
              "ordering": false,
              "info":     false,
              "bFilter":     false,
              "processing": false,
              "serverSide": true,
              "scrollX": true,
              "scrollY": 300,
              "scrollCollapse": true,
              data: dataHistory,
              columns: [
                {
                    'data': 'time'
                },
                {
                    'data': 'symbol'
                },
                {
                    'data': null,
                    'render': function(d){
                        return d.type == 0 ? 'Buy' : 'Sell';
                    }
                },
                {
                    'data': 'lots'
                },
                {
                    'data': 'price'
                },
                {
                    'data': 'sl'
                },
                {
                    'data': 'tp'
                },
                {
                    'data': 'pl'
                }
              ],
              rowId: 'time'
            } );

            setInterval( function () {
              myApp.oTable.ajax.reload();
            }, 8000 );
  } );
</script>
<div class="grid-orders_online">
  <table id="orders_history" class="display" cellspacing="0" width="100%">
    <thead>
    <tr>
      <th nowrap>Time</th>
      <th nowrap>Symbol</th>
      <th nowrap>Type</th>
      <th nowrap>Lots</th>
      <th nowrap>Price</th>
      <th nowrap>SL</th>
      <th nowrap>TP</th>
      <th nowrap>PL</th>
    </tr>
    </thead>
    <tbody>
      <tr>
        <td>loading...</td>
      </tr>
    </tbody>
  </table>
</div>