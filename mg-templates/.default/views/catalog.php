<?php mgTitle($data['titeCategory']); ?>

<h1> <?php echo $data['titeCategory'] ?> </h1>

<?php 
foreach($data['items'] as $item){
  if(0 == $i % 3) :
?>

<div style="clear:both;"></div>
<?php endif; ?>
<div class="product">
  <div class="product_image">
    <a href="<?php echo SITE?>/<?php echo isset($item["category_url"])? $item["category_url"] : 'vse' ?>/<?php echo htmlspecialchars($item["product_url"]) ?>"><image src="<?php echo SITE?>/uploads/<?php echo $item["image_url"] ? $item["image_url"] : "none.png" ?>" /></a>
  </div>
  <h2>
    <a href="<?php echo SITE?>/<?php echo isset($item["category_url"])? $item["category_url"] : 'vse' ?>/<?php echo htmlspecialchars($item["product_url"]) ?>"><?php echo $item["name"] ?></a>
  </h2>
  <div class="product_price">
    <span class="prdPrice"><?php echo $item["price"] ?> руб.<span>
        </div>
		
        <div class="product_buy">
          <a href="<?php echo SITE?>/catalog?inCartProductId=<?php echo $item["id"] ?>">В корзину</a>
	    </div>
        </div>

<?php
  $i++;
}

echo $data['pager'];