
<div class="wrap">
  <div class="over_bg" >
    <div class="m-panel grid_5">
      <div class="panel-header" >
        <span class="m-cat-24">Каталог товаров</span>
      </div>
      <div class="panel-body">
        <div class="panel-content">
          <div style="width:100%;">
            <div class="toolbar">
              <div style="float: left; margin-top: 4px;"><a href="#" rel="creat_new_product" class="add_good"><span>Добавить товар</span></a></div>
              <div class="filter"><b>Категория товаров</b> <?php echo $categories ?></div>
            </div>
            <table class="catalog_table" >
              <tr>
                <th>ID</th>
                <th>Категория</th>
                <th>Изображение</th>
                <th>Артикул</th>
                <th>Название</th>
                <th>Описание</th>
                <th>Цена</th>
                <th></th>
              </tr>
              <?php foreach($catalog as $data){ ?>
                <tr id="<?php echo $data['id'] ?>">
                  <td class="id"><?php echo $data['id'] ?></td>
                  <td id="<?php echo $data['cat_id'] ?>" class="cat_id"><?php echo $listCategories[$data['cat_id']] ?></td>
                  <td class="image_url"><?php
              if(!$data['image_url']){
                $data['image_url'] = "none.png";
              }
              ?><img class="uploads" src="<?php echo SITE?>/uploads/<?php echo $data['image_url'] ?>"/></td>
                  <td class="code"><?php echo $data['article'] ?></td>
                  <td class="name"><?php echo $data['name'] ?></td>
                  <td class="desc" id="<?php echo $data['id'] ?>"><?php echo $data['desc'] ?></td>
                  <td class="price"><?php echo $data['price'] ?></td>

                  <td width="16"><a href="#" title="Удалить" class="delBtn" rel="del" id="<?php echo $data['id'] ?>"></a></td>
<?php  } ?>
              <tr class="pagination_box"><td colspan="9"><?php echo $pagination ?></td></tr>
            </table>



            <div class="creat_product">
              <div class="popwindow">
                <div class="title_popwindow">
                  <span class="m-cat-24">Новый товар</span>
                  <div class="close_popwindow">
                    <a href="#" rel="cancel_creat_new_product" >

                    </a>
                  </div>
                </div>

              </div>
              <div class="creat_product_table">
                <table>
                  <tr>
                    <td>Название:</td><td><input type="text" name="name"/></td>
                    <td rowspan="4">Изображение:
                      <div class="btn_load_img">
                        <form id="imageform" method="post" enctype="multipart/form-data" action="<?php echo SITE?>/ajax/?url=action/addImage">
                          <input type="file" name="photoimg" id="photoimg" />
                        </form>
                      </div>

                      <div class="btn_cansel_load_img">
                        <a href="#" id="form_del_img"  alt="Отменить" title="Отменить"><img  src="<?php echo SITE?>/mg-admin/design/images/cancal_upload.png"/></a>
                      </div>


                      <div id="preview"></div>
                    </td>
                  </tr>
                  <tr><td>Артикул:</td><td><input type="text" name="code"/></td></tr>
                  <tr><td>Цена:</td><td><input type="text" name="price"/> руб.</td></tr>
                  <tr><td>Категория:</td><td>

                      <select id='new_prod_category' name='category'>
                        <option selected value='0'>Все</option>
<?php echo MG::get('category')->getTitleCategory($arrayCategories); ?>
                      </select>

                    </td></tr>
                  <tr><td>Описание:</td><td colspan="2">
                        <div class="createProductDesc" style="background: #FFF"></div>
                    </td></tr>
                  <tr>
                    <td colspan="3" style="height:40px; text-align:right;">
                      <a href="#" rel="save_new_product" class="button" >Сохранить</a>
                    </td>
                  </tr>
                </table>
              </div>
            </div>

            <div class="edit_product">
              <div class="popwindow">
                <div class="title_popwindow">
                  <span class="m-cat-24">Редактировать товар</span>
                  <div class="close_popwindow">
                    <a href="#" rel="cancel_edit_product" >
                    </a>
                  </div>
                </div>
              </div>
              <div class="edit_product_table">
                <table>
                  <tr><td>Название:</td><td><input type="text" name="edit_name" /></td><td rowspan="4">Изображение:
                      <div class="edit_btn_load_img">
                        <form id="edit_imageform" method="post" enctype="multipart/form-data" action="/loadimage.php">
                          <input type="file" name="edit_photoimg" id="edit_photoimg" />
                        </form>
                      </div>

                      <div class="edit_btn_cansel_load_img">
                        <a href="#" id="edit_form_del_img"  alt="Отменить" title="Отменить"><img  src="<?php echo SITE?>/mg-admin/design/images/cancal_upload.png"/></a>
                      </div>

                      <div id="edit_preview">

                      </div>

                    </td></tr>
                  <tr><td>Артикул:</td><td><input type="text" name="edit_code"/></td></tr>
                  <tr><td>Цена:</td><td><input type="text" name="edit_price"/> руб.</td></tr>


                  <tr><td>Категория:</td><td>

                      <select id='edit_category' name='category'>
                        <option selected value='0'>Все</option>
<?php echo MG::get('category')->getTitleCategory($arrayCategories); ?>
                      </select>

                    </td></tr>

                  <tr><td>Описание:</td><td colspan="2">

                       <div id="editProductDesc" style="background: #FFF"></div>

                    </td></tr>
                  <tr><td colspan="3" style="height:40px; text-align:right;">
                      <a href="#" rel="save_edit_product" class="button" >Сохранить</a>
                    </td></tr>
                </table>
              </div>
              <input type="hidden" name="edit_id"/>
            </div>

          </div>
        </div>
      </div>

    </div>


  </div>
</div>
