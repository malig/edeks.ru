//Фильтр
$('#order_filter').live("change", function(){
  var page = 1;
  var filter_id=$(this).val();

  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url: "orders.php",
      page:page,
      filter_id:filter_id
    },
    cache: false,
    success: function(data){

      $("#content").html(data);
    }

  });

});

//Обработка  нажатия кнопки перехода на другую страницу каталога
$('a[rel=pagination_order]').live("click", function(event){
	event.preventDefault();
  var  page=$(this).attr('page');// интервал
  var filter_id=$('#order_filter').val();
  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url: "orders.php",
      page:page,
      filter_id:filter_id
    },
    cache: false,
    success: function(data){
      $("#content").html(data);
    }

  });

});

//Обработка  нажатия кнопки сохранения редактированной информации категории
$('a[rel=save_orders]').live("click", function(event){
	event.preventDefault();
  //собираем из таблицы все инпуты с данными, записываим их в виде нативного кода
  var obj ='{"url":"action/saveOrders",';
  $('#table_order td[id=data] input').each(function(){
    obj+='"'+$(this).attr('name')+'":"'+$(this).val()+'",';
  });
  obj+='}';
  //преобразуем полученные данные в JS объект для передачи на сервер
  var data1 = eval("(" + obj + ")");
  $.ajax({
    type:"POST",
    url: "ajax",
    data: data1,
    cache: false,
    success: function(data){
      var response = eval("(" + data + ")");
      indication(response.msg, response.status);
      $('.edit_order').animate({
        opacity: "hide"
      }, "slow" );
    }
  });
});

//Обработка  нажатия кнопки удаления заказа из карточки заказа
$('a[rel=order_del]').live('click', function(event){
	event.preventDefault();
  var id=$('.edit_order #order_id').text();
  if(orderDelete(id)){
    $('.edit_order').animate({
      opacity: "hide"
    }, "slow" );
  }
refrashOrderPage();
});

//Обработка  нажатия кнопки удаления заказа из таблицы заказов
$('a[rel=order_delFromTable]').live('click', function(event){
	event.preventDefault();
  showEditOrder = false;
  var id = $(this).attr('id');
  orderDelete(id);
  refrashOrderPage();

});

function orderDelete(id){
  if(!confirm("Вы подтверждаете удаление заказа "+id+"?")){
    return false;
  }
  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url:"action/deleteOrder",
      id:id
    },
    cache: false,
    success: function(data){
      var response = eval("(" + data + ")");
      indication(response.msg, response.status);
      if(response.status=="succes"){
        $("#table_order tr[order_id="+id+"]").remove();
      }
    }
  });
  return true;
}

function refrashOrderPage(){
var page=$("div.pagination").find("a[class=activ]").attr('page');
var filter_id=$('#order_filter').val();

  // перезагружает страницу.
  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url: "orders.php",
      page:page,
      filter_id:filter_id	  
    },
    cache: false,
    success: function(data){
      $("#content").html(data);
    }
  });
}

var select="#C9E6C0";
var  background;

$("#table_order tr").live("mouseover", function(){
  background=$(this).find("td").css("background");
  $(this).find("td").css('background',select);
});

$("#table_order tr").live("mouseout", function(){
  $(this).find("td").css('background',background);
});


$('#table_order tr').live("click", function(){
  showEditOrder = true;
  editOrder($(this));
});

function editOrder(myThis){
  if(!showEditOrder) return false;
  var order_id = myThis.attr('order_id');
  $('.content_order').html(myThis.find('.order_content').html());
  $('.contact').html(myThis.find('.contactHide').html());
  //centerPosition($('.edit_order'));
  $('.edit_order').animate({
    opacity: "show"
  }, "slow" );
  $('.edit_order #order_id').text(order_id);
  
  	if($('#table_order tr[order_id='+order_id+'] td[id=close]').text()=='Да'){
		$('.edit_order #close input[name=close]').attr('checked', 'checked');
	}else{
		//$('.edit_order #close input[name=close]').hide();
	}

	if($('#table_order tr[order_id='+order_id+'] td[id=print]').text()=='Да'){
		$('.edit_order #print input[name=print]').attr('checked', 'checked');
		$('.edit_order #close input[name=close]').show();
	}else{
		//$('.edit_order #print input[name=print]').hide();
	}

	ymaps.ready(initMap(myThis.find('.fullAdr').html()));
}

var map;
function drawMap(coorde){
	if(map != undefined){
		map.destroy();
	}
	var lat = coorde[0],
		lon = coorde[1],
		zoom = 16,
		osmMap = new OpenLayers.Layer.OSM("OpenStreetMap"),
		lonLat = new OpenLayers.LonLat(lon, lat).transform(new OpenLayers.Projection("EPSG:4326"), 
					new OpenLayers.Projection("EPSG:900913"));
		map = new OpenLayers.Map('map'),

		map.addLayers([osmMap]);
		map.setCenter (lonLat, zoom);

		MarkersLayer = new OpenLayers.Layer.Markers("Markers");
		map.addLayers([MarkersLayer]);
		var size = new OpenLayers.Size(40, 40);
		var RadarMarkerIcon = new OpenLayers.Icon('/mg-templates/imagTheme/images/radar.png', 
								size, new OpenLayers.Pixel( -(size.h/2),-(size.h) ));
		RadarMarker = new OpenLayers.Marker(lonLat, RadarMarkerIcon);
		MarkersLayer.addMarker(RadarMarker);
}

function initMap(valAdrr) {
    ymaps.geocode(valAdrr, { results: 1 }).then(function (res) {
        var firstGeoObject = res.geoObjects.get(0);
		var coorde = firstGeoObject.geometry.getCoordinates();
		drawMap(coorde);
    }, function (err) {
        alert(err.message);
    });
}

$('a[rel=cancel_edit_order]').live("click", function(event){
	event.preventDefault();
  $('.edit_order').animate({
    opacity: "hide"
  }, "slow" );
  $('.edit_order #print input[name=print]').removeAttr("checked");
  $('.edit_order #close input[name=close]').removeAttr("checked");
});


$('a[rel=save_edit_order]').live("click", function(event){
	event.preventDefault();
  if($('.edit_order #close input[name=close]').attr('checked')=='checked'){
    var close=1;
  }
  else{
    var close=0;
  }

  if($('.edit_order #print input[name=print]').attr('checked')=='checked'){
    var print=1;
  }
  else{
    var print=0;
  }

  var order_id=$('.edit_order #order_id').text();
  var node_string = $("input[name=node_string]").val();

  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url:"action/saveOrders",
      order_id:order_id,
      close:close,
      print:print,
      node:node_string
    },
    cache: false,
    success: function(data){
      var response = eval("(" + data + ")");
      indication(response.msg, response.status);
      $('.edit_order').animate({
        opacity: "hide"
      }, "slow" );
      $('.edit_order #order_id').text('');
      if(print)str_print='Да'; else str_print='Нет';
      $('#table_order tr[order_id='+order_id+'] td[id=print]').text(str_print);
      if(close)str_close='Да'; else str_close='Нет';
      $('#table_order tr[order_id='+order_id+'] td[id=close]').text(str_close);
      $('.edit_order #print input[name=print]').removeAttr("checked");
      $('.edit_order #close input[name=close]').removeAttr("checked");
	  refrashOrderPage();
    }
  });
  
});

$('#pr').live("click", function(){
  printBlock(".creat_category_table");
  $('.edit_order #print input[name=print]').show();
});

function printBlock(printLink)
{
    productDesc = $(printLink).html();//забираем контент нужного нам блока (в моем случае ссылка на печать находится внути его)
    $('body').addClass('printSelected');//добавляем класс к body
    $('body').append('<div class="printSelection">' + productDesc + '</div>');//создаем новый блок внутри body
    window.print();//печатаем
   
    window.setTimeout(pageCleaner, 0); //очищаем нашу страницу от "мусора"
   
    return false;//баним переход по ссылке, чтобы она не пыталась перейти по адресу, указанному внутри аттрибута href
}

function pageCleaner()
{
    $('body').removeClass('printSelected');//убираем класс у body
    $('.printSelection').remove();//убиваем наш только что созданный блок для печати
}