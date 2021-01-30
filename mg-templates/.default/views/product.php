<?php
/**
 * Файл вида product.php
 */
mgTitle($data['name']); 
?>
<h1><?php echo $data['name'] ?></h1>

<div class="mainCont">
<a class="arrowLeft" href="<?php echo SITE?>/catalog">Назад</a>
<div class="card_product">
<div style="float:left;">
  <div class="product_image widthImg">
    <img src="<?php echo SITE?>/uploads/<?php echo $data["image_url"] ? $data["image_url"] : "none.png" ?>" alt="<?php echo $data['name'] ?>" title="<?php echo $data['name'] ?>" />
  </div>
  <div>
  
   <div class="price">
    <?php echo $data['price'] ?> руб.
  </div>  
  <div class="product_buy">
    <a href="<?php echo SITE?>/catalog?inCartProductId=<?php echo $data['id'] ?>">Купить</a>
  </div>
  </div>
</div>
  <div class="product_desc"> 
    <strong>Характеристики товара</strong>
	<ul>
		<li>		
		Описание: <span><?php echo $data['desc'] ?></span></li>
	</ul>
  </div>
   
</div>
</div>