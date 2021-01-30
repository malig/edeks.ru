<?
$filter = "";
if($filter = URL::getQueryParametr('filter')){
	if($filter == "1=1"){
		$filter = "";
	}	
}else{
	$filter = "";
}
?>
<div class="wrap">
  <div class="over_bg" >
    <div class="m-panel grid_5">
      <div class="panel-header" >
        <span class="m-order-24">Заказы</span>
      </div>
	  
		<div>
			</br>
				<div style="margin:5px; float: left;" >Фильтровать:</div> 
				
				<input id = "user_filter_input" style = "width:500px; float: left;" type="text" name="filter" value = "<?echo $filter;?>"/>
				
				<div style="margin:5px; float: left;" >
					<a href="#" rel="user_filter_link" class="button">Фильтровать</a>
				</div>
				<div style="margin:5px; float: left;" >
					<a href="#" rel="user_filter_cancel" class="button">Сбросить</a>
				</div>
			</br></br>
		</div>

      <?php echo $tableOrders ?>
	  <div><?php echo $pagination?></div>
    </div>
  </div>
</div>

<div class="edit_user user_border">

  <div class="popwindow">
    <div class="title_popwindow">
      <span class="m-cat-24">Редактирование пользователя</span>
      <div class="close_popwindow">
        <a href="#" rel="cancel_edit_user" >

        </a>
      </div>
    </div>

  </div>

	<div  class="creat_category_table" style="width:710px; padding-left:10px;">
</br>
		<div class="contact" id = "table_user" style="margin-bottom:10px;"></div>
		<div class="content_users"></div>
	</div>
	<div style="margin:10px; float: left;" >
	  <a href="#" rel="user_del" class="button">Удалить</a>
	</div>
	<div style="margin:10px; float: left;">
	  <a href="#" rel="save_edit_user" class="button">Сохранить</a>
	</div>
	<div class="close_popwindow_bottom">
		<a href="#" rel="cancel_edit_user" ></a>
	</div>

</div>
