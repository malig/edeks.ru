<?
$f = array();
$f[0] = "";
$f[1] = "";

if($filter != ""){
	$f[$filter] = "selected";
}
?>

<script src="http://api-maps.yandex.ru/2.0/?load=package.standard&lang=ru-RU" type="text/javascript"></script>
<script src="/mg-templates/imagTheme/js/OpenLayers.js"></script>

<div class="wrap">
  <div class="over_bg" >
    <div class="m-panel grid_5">
      <div class="panel-header" >
        <span class="m-order-24">Заказы</span>
      </div>
	  
		<div>
			</br>Показывать заказы: 
			<select id="order_filter" name="category">
				<option <?php echo $f[1] ?> value="1">Незакрытые</option>
				<option <?php echo $f[0] ?> value="0">Все</option>
			</select>
			</br></br>
		</div>

      <?php echo $tableOrders ?>
	  <div><?php echo $pagination?></div>
    </div>
  </div>
</div>

<div class="edit_order order_border">

  <div class="popwindow">
    <div class="title_popwindow">
      <span class="m-cat-24">Редактирование заказа</span>
      <div class="close_popwindow">
        <a href="#" rel="cancel_edit_order" >

        </a>
      </div>
    </div>

  </div>
  
	<table style="margin-left:10px">
		<tr>
			<td style = "color:red; font-size:18px; font-weight:bold;font-family:arial">В работе: </td><td id="print"><input type='checkbox' name='print'></td>
			<td rowspan = 2>
				<div style="margin:10px; margin-left:350px; float: left;">
				  <a href="#" rel="save_edit_order" class="button">Сохранить</a>
				</div>
				<div style="margin:10px;float: left;">
					<span id = "pr" class="button" >Печать</span>
				</div>
			</td>
		</tr>
		<tr>
			<td style = "color:red; font-size:18px; font-weight:bold;font-family:arial">Закрыт: </td><td id="close"><input type='checkbox' name='close'></td>
		</tr>
  		<tr>
			<td style = "color:red; font-size:18px; font-weight:bold;font-family:arial">Примечание: </td>
			<td id="node_string" colspan = 2>
				<input type='text' name='node_string' style = "width:555px" value = "">
			</td>
		</tr>
	</table>

	<div  class="creat_category_table" style="width:710px; padding-left:10px;">
</br>
		<div class="contact" style="margin-bottom:10px;">

		</div>
		
		<!--h1 style="margin:0px;">Состав заказа:</h1-->
		
		<div class="content_order">

		</div>
</br>
		<!--div style="page-break-after: always;"></div-->
		<div id="map" class = "pagePrint" style="width:700px; height:950px;clear:both""></div>
	</div>

	<div style="margin:10px; float: left;">
	  <a href="#" rel="save_edit_order" class="button">Сохранить</a>
	</div>
	<div style="margin:10px; float: left;" >
	  <a href="#" rel="order_del" class="button">Удалить</a>
	</div>
	<div style="margin:10px;float: left;">
		<span id = "pr" class="button" >Печать</span>
	</div>
	<div class="close_popwindow_bottom">
		<a href="#" rel="cancel_edit_order" ></a>
	</div>

</div>
