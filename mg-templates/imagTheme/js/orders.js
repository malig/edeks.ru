$('#table_order tr').live("click", function(){
	var idOrder = $(this).attr('order_id');
	$('#table_order #tr'+idOrder).toggle();
});