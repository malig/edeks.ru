//Обработка  нажатия кнопки пагинации
$('a[rel=pagination_user]').live("click", function(event){
	event.preventDefault();
	var  page=$(this).attr('page');
	var filter=$('#user_filter_input').val();
	$.ajax({
		type:"POST",
		url: "ajax",
		data: {
			url: "users.php",
			page:page,
			filter:filter
		},
		cache: false,
		success: function(data){
			$("#content").html(data);
		}
	});
});

//Сохранение инфы о пользователе
$('a[rel=save_edit_user]').live("click", function(event){
	event.preventDefault();

	var obj ='{"url":"action/saveUser",';
	$('#table_user input').each(function(){
		obj+='"'+$(this).attr('name')+'":"'+$(this).val()+'",';
	});
	obj+='}';
	var data1 = eval("(" + obj + ")");

	$.ajax({
		type:"POST",
		url: "ajax",
		data: data1,
		cache: false,
		success: function(data){
			var response = eval("(" + data + ")");
			indication(response.msg, response.status);
			$('.edit_user').animate({
				opacity: "hide"
			}, "slow" );
			refrashUserPage();
		}
	});
});

//Обновить страницу
function refrashUserPage(){
var page=$("div.pagination").find("a[class=activ]").attr('page');
var filter_id=$('#order_filter').val();

	$.ajax({
		type:"POST",
		url: "ajax",
		data: {
			url: "users.php",
			page:page,
			filter_id:filter_id	  
		},
		cache: false,
		success: function(data){
			$("#content").html(data);
		}
	});
}

//Обработка  нажатия кнопки удаления заказа из карточки заказа
$('a[rel=user_del]').live('click', function(event){
	event.preventDefault();
	var id = $('#table_user #userID').text();
	if(userDelete(id)){
		$('.edit_user').animate({
			opacity: "hide"
		}, "slow" );
	}
	refrashUserPage();
});

function userDelete(id){
	if(!confirm("Вы подтверждаете удаление пользователя "+id+"?")){
		return false;
	}
	$.ajax({
		type:"POST",
		url: "ajax",
		data: {
			url:"action/deleteUser",
			id:id
		},
		cache: false,
		success: function(data){
			var response = eval("(" + data + ")");
			indication(response.msg, response.status);
			if(response.status=="succes"){
				$("#im_table_user tr[user_id="+id+"]").remove();
			}
		}
	});
	return true;
}

//Обработка  фильтра
$('a[rel=user_filter_link]').live('click', function(event){
	event.preventDefault();
	var page = 1;
	var filter=$('#user_filter_input').val();

	$.ajax({
		type:"POST",
		url: "ajax",
		data: {
			url: "users.php",
			page:page,
			filter:filter
		},
		cache: false,
		success: function(data){
			$("#content").html(data);
		}
	});
});

//Сброс  фильтра
$('a[rel=user_filter_cancel]').live('click', function(event){
	event.preventDefault();
	var page = 1;
	var filter='1=1';

	$.ajax({
		type:"POST",
		url: "ajax",
		data: {
			url: "users.php",
			page:page,
			filter:filter
		},
		cache: false,
		success: function(data){
			$("#content").html(data);
		}
	});
});

//Подсветка при наведение мыши
var select="#C9E6C0";
var  background;

$("#im_table_user tr").live("mouseover", function(){
  background=$(this).find("td").css("background");
  $(this).find("td").css('background',select);
});

$("#im_table_user tr").live("mouseout", function(){
  $(this).find("td").css('background',background);
});
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////
$('#im_table_user tr').live("click", function(){
	showEditUser = true;
	editUser($(this));
});

function editUser(myThis){
	if(!showEditUser) return false;
	var user_id = myThis.attr('user_id');
	$('.content_users').html(myThis.find('.user_content').html());
	$('.contact').html(myThis.find('.contactHide').html());
	$('.edit_user').animate({
		opacity: "show"
	}, "slow" );
	$('.edit_user #user_id').text(user_id);
}

$('a[rel=cancel_edit_user]').live("click", function(event){
	event.preventDefault();
	$('.edit_user').animate({
		opacity: "hide"
	}, "slow" );
});