<?php 
  global $wpdb;
  // $order = new shop86Order();
  $status = '';
  if(isset($_GET['status'])) {
    $status = $_GET['status'];
  }
?>
<h2><?php _e('shop86 Orders', 'shop86'); ?></h2>

<div class="wrap">
  
  <table class="widefat shop86HighlightTable" id="orders_table">
    <tr>
      <thead>
      	<tr>
      	  <th><?php _e('ID', 'shop86'); ?></th>
    			<th><?php _e( 'Order Number' , 'shop86' ); ?></th>
    			<th><?php _e( 'Name' , 'shop86' ); ?></th>
    			<th><?php _e( 'Name' , 'shop86' ); ?></th>
      		<th><?php _e( 'Amount' , 'shop86' ); ?></th>
      		<th><?php _e( 'Date' , 'shop86' ); ?></th>
          <th><?php _e( 'Delivery' , 'shop86' ); ?></th>
      		<th><?php _e( 'Status' , 'shop86' ); ?></th>
      		<th><?php _e( 'Actions' , 'shop86' ); ?></th>
      	</tr>
      </thead>
      <tfoot>
      	<tr>
      		<th><?php _e('ID', 'shop86'); ?></th>
    			<th><?php _e( 'Order Number' , 'shop86' ); ?></th>
    			<th><?php _e( 'Name' , 'shop86' ); ?></th>
    			<th><?php _e( 'Name' , 'shop86' ); ?></th>
      		<th><?php _e( 'Amount' , 'shop86' ); ?></th>
      		<th><?php _e( 'Date' , 'shop86' ); ?></th>
          <th><?php _e( 'Delivery' , 'shop86' ); ?></th>
      		<th><?php _e( 'Status' , 'shop86' ); ?></th>
      		<th><?php _e( 'Actions' , 'shop86' ); ?></th>
      	</tr>
      </tfoot>
    </tr>
  </table>
</div>
<script type="text/javascript">
  (function($){
    $(document).ready(function(){
      var orders_table = $('#orders_table').dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "bPagination": true,
        "iDisplayLength": 30,
        "aLengthMenu": [[30, 60, 150, -1], [30, 60, 150, "All"]],
        "sPaginationType": "bootstrap",
        "bAutoWidth": false,
        "sAjaxSource": ajaxurl + "?action=orders_table",
        "aaSorting": [[5, 'desc']],
        "aoColumns": [
          { "bVisible": false },
          { "bSortable": true, "fnRender": function(oObj) { return '<a href="?page=shop86_admin&task=view&id=' + oObj.aData[0] + '">' + oObj.aData[1] + '</a>' }},
          { "fnRender": function(oObj) { return oObj.aData[2] + ' ' + oObj.aData[3] }},
          { "bVisible": false },
          null,
          { "bSearchable": false },
          { "bSearchable": false },
          null,
          { "bSearchable": false, "bSortable": false, "fnRender": function(oObj) { return oObj.aData[8] != "" ? '<a href="#" onClick="printView(' + oObj.aData[0] + ')" id="print_version_' + oObj.aData[0] + '"><?php _e( "Receipt" , "shop86" ); ?></a> | <a href="?page=shop86_admin&task=view&id=' + oObj.aData[0] + '"><?php _e( "View" , "shop86" ); ?></a> | <a class="delete" href="?page=shop86_admin&task=delete&id=' + oObj.aData[0] + '"><?php _e( "Delete" , "shop86" ); ?></a> | <a href="#" class="shop86ViewOrderNote" rel="note_' + oObj.aData[0] + '"><?php _e( "Notes" , "shop86" ); ?></a><div class="shop86OrderNote" id="note_' + oObj.aData[0] + '"><a href="#" class="shop86CloseNoteView" rel="note_' + oObj.aData[0] + '" alt="Close Notes Window"><img src="<?php echo shop86_URL ?>/images/window-close.png" /></a><h3>' + oObj.aData[1] + '</h3><p>' + oObj.aData[8] + '</p></div>' : '<a href="#" onClick="printView(' + oObj.aData[0] + ')" id="print_version_' + oObj.aData[0] + '"><?php _e( "Receipt" , "shop86" ); ?></a> | <a href="?page=shop86_admin&task=view&id=' + oObj.aData[0] + '"><?php _e( "View" , "shop86" ); ?></a> | <a class="delete" href="?page=shop86_admin&task=delete&id=' + oObj.aData[0] + '"><?php _e( "Delete" , "shop86" ); ?></a>'; },"aTargets": [ 9 ] }
        ],
        "oLanguage": { 
          "sZeroRecords": "<?php _e('No matching Orders found', 'shop86'); ?>",
          "sSearch": "<?php _e('Search', 'shop86'); ?>:",
          "sInfo": "<?php _e('Showing', 'shop86'); ?> _START_ <?php _e('to', 'shop86'); ?> _END_ <?php _e('of', 'shop86'); ?> _TOTAL_ <?php _e('entries', 'shop86'); ?>",
          "sInfoEmpty": "<?php _e('Showing 0 to 0 of 0 entries', 'shop86'); ?>",
          "oPaginate": {
            "sNext": "<?php _e('Next', 'shop86'); ?>",
            "sPrevious": "<?php _e('Previous', 'shop86'); ?>",
            "sLast": "<?php _e('Last', 'shop86'); ?>",
            "sFirst": "<?php _e('First', 'shop86'); ?>"
          }, 
          "sInfoFiltered": "(<?php _e('filtered from', 'shop86'); ?> _MAX_ <?php _e('total entries', 'shop86'); ?>)",
          "sLengthMenu": "<?php _e('Show', 'shop86'); ?> _MENU_ <?php _e('entries', 'shop86'); ?>",
          "sLoadingRecords": "<?php _e('Loading', 'shop86'); ?>...",
          "sProcessing": "<?php _e('Processing', 'shop86'); ?>..."
        }
      }).css('width','');
      $('.shop86ViewOrderNote').live('click', function () {
        $(".shop86OrderNote").hide();
        var id = $(this).attr('rel');
        $('#' + id).show();
        return false;
      });
      $('.shop86CloseNoteView').live('click', function () {
        var id = $(this).attr('rel');
        $('#' + id).hide();
        return false;
      });
      $('.delete').live('click', function() {
        return confirm('Are you sure you want to delete this item?');
      });
      orders_table.fnFilter( '<?php echo $status ?>', 7 );
    } );    
  })(jQuery);
  function printView(id) {
    var url = ajaxurl + '?action=print_view&order_id=' + id
    myWindow = window.open(url,"Your_Receipt","resizable=yes,scrollbars=yes,width=550,height=700");
    return false;
  }
</script>
