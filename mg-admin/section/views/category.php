<ul id="contextMenu">
	<li><a href="#" rel="edit_category"><img src="<?php echo SITE?>/mg-admin/design/images/icons/edit.png">&nbsp;&nbsp;Редактировать</a></li>
	<li><a href="#" rel="creat_new_category"><img src="<?php echo SITE?>/mg-admin/design/images/icons/plus.png">&nbsp;&nbsp;Добавить подкатегорию</a></li>
	<li><a href="#" rel="delete_category"><img src="<?php echo SITE?>/mg-admin/design/images/icons/del.png">&nbsp;&nbsp;Удалить</a></li>
	<li style="border-top: gray 1px solid; margin-top:5px;"><a href="#" rel="cansel_context"><img src="<?php echo SITE?>/mg-admin/design/images/icons/cansel.png">&nbsp;&nbsp;Отмена</a></li>
</ul>
<div class="wrap">
<div class="over_bg">
<div class="m-panel grid_5">
                	<div class="panel-header">
                    	<span class="m-categ-24">Редактор категорий</span>
                    </div>
					 <div class="panel-body">
                    	<div class="panel-content">
	                    	<div style="width:100%;">
 <div id="category-editor">
<div class="toolbar">
<a href="#" rel="creat_new_category" id="0" class="add_good" style="float:left;"><span>Добавить категорию</span></a>
</div>
<!--<div id="category-head">

</div>-->
<div class="category_list">

<?php echo $categories ?>

<div class="cleaner"></div>
</div>
</div>
<div class="cleaner"></div>
</div>
</div>
</div>
</div>
</div>

</div>
</div>
<div class="creat_category">
		<div class="popwindow">
			<div class="title_popwindow">
				<span class="m-categ-24">Новая категория</span>
				<div class="close_popwindow">
				<a href="#" rel="cancel_creat_new_category" >
				</a>
			</div>
			</div>
		</div>

		<div class="creat_category_table">
		<table>

		<tr>
			<td>Родительская категория: </td><td id="parent_name"></td>
		</tr>
		<tr>
			<td>Название:</td><td><input type="text" name="category_name"/></td>
		</tr>

		<tr>
			<td colspan="3" style="height:40px; text-align:right;">
				<a href="#" rel="save_new_category" class="button" >Сохранить</a>
			</td>
		</tr>
		</table>
		<div style="display:none" id="parent_id">0</div>
		</div>
</div>


<div class="edit_category">
		<div class="popwindow">
			<div class="title_popwindow">
				<span class="m-categ-24">Редактировать категорию</span>
				<div class="close_popwindow">
				<a href="#" rel="cancel_edit_category" >
				</a>
			</div>
			</div>
		</div>

		<div class="creat_category_table">
		<table>

		<tr>
			<td>Родительская категория: </td><td id="parent_name"><?php echo $select_categories?></td>
		</tr>
		<tr>
			<td>Название:</td><td><input type="text" name="edit_name"/></td>
		</tr>

		<tr>
			<td colspan="3" style="height:40px; text-align:right;">
				<a href="#" rel="save_edit_category" class="button" >Сохранить</a>
			</td>
		</tr>
		</table>
		</div>
		<div style="display:none" id="edit_id"></div>
</div>

