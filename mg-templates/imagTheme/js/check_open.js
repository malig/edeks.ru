$(document).ready(function(){
	$(".im_arrow").toggle(
		function() {
			//$("#im_check_open").addClass("checked");
			$("#im_check_open, #im_check_close, #im_up, #im_down").toggle(); 			
		},
		function() {
			$("#im_check_open, #im_check_close, #im_up, #im_down").toggle();                     
		}
	);	
	
	$.ajax({
		type:"POST",
		url: "imajax",
		cache: false,
		success: function(data){
			var arrRes = data.split('[total]');
			if (arrRes[0] == '')
				$("#totalSumm").html(0+ ' руб.');
			else
				$("#totalSumm").html(arrRes[0]+ ' руб.');
			$("#im_check_open").html(arrRes[1]);
		}
	});
	
	$("#inputString").keyup(function(e) {
		switch(e.keyCode) { 
			case 13:
					lookup();
				break;
		}
	});
});

$('.im_product_submit').live("click", function(){
	
	/*if(!$("#im_check_open").hasClass("checked")){
		$("#im_check_open, #im_check_close, #im_up, #im_down").toggle();
		$("#im_check_open").addClass("checked"); 
	}*/
	
	indication('<span>Товар добавлен!</span>','alert');
	
	$.ajax({
		type:"POST",
		url: "imajax",
		data: {inCartProductId :this.id},
		cache: false,
		success: function(data){
			var arrRes = data.split('[total]');
			$("#totalSumm").html(arrRes[0]+ ' руб.');
			$("#im_check_open").html(arrRes[1]);
		}
	});
});

$('.im_check_price img').live("click", function(){
		$.ajax({
		type:"POST",
		url: "imajax",
		data: {delCartProductId :this.id},
		cache: false,
		success: function(data){
			var arrRes = data.split('[total]');
			$("#totalSumm").html(arrRes[0]+ ' руб.');
			$("#im_check_open").html(arrRes[1]);
		}
	});
});

$('#lookup').live("click", function(event){
	event.preventDefault();
	lookup();
});

function lookup(){
	$.ajax({
		type:"POST",
		url: "search",
		data: {inputString :$('#inputString').val()},
		cache: false,
		success: function(data){
			$("#im_content .im_product_list").html(data);
		}
	});
}

function indication(text,status)
{
  var background = "#9abb8b";
  var bordercolor = "#588a41";
  var object = "";

  if(status == "error"){
    object = $('#msg_error');
  }

  if(status == "succes"){
    object = $('#msg_succes');
  }

  if(status == "alert"){
    object = $('#msg_alert');
  }

  if(status == "information"){
    object = $('#msg_information');
  }

  object.animate({ opacity: "show" }, 300 );
  object.html(text);
  object.animate({ opacity: "hide" }, 300 );
}

