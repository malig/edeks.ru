<?php mgTitle($data['titeCategory']); ?>

<!--a href=""><?php echo $data['titeCategory'] ?> </a-->

<a style = "float: left;" href="<?php echo SITE?>">Главная страница / </a>
<a style = "float: left; margin-left:5px;" href=""><?php echo $data['titeCategory'] ?></a>

<?
echo "<pre>";
//print_r($data['items']);
echo "</pre>";
?>

<?php 
foreach($data['items'] as $item){
  if(0 == $i % 2) :
?>

<div style="clear:both;"></div>
<?php endif; ?>

<div class = "im_product">
	<table>
		<tr>
			<td class = "im_product_description" >
				<!--a href="<?php echo SITE?>/<?php echo isset($item["category_url"])? $item["category_url"] : 'vse' ?>/<?php echo htmlspecialchars($item["product_url"]) ?>?backurl=search/<?php echo $data['activPage']?>/<?php echo $data['inputString']?>"><?php echo $item["name"] ?></a-->
				<span><?php echo $item["name"] ?></span>
				<ul>
					<li>цена:<strong> <?php echo $item["price"] ?> р.</strong></li>	
					<li> <?php echo $item["box"] ?></li>	
					<li>кол-во: <?php echo $item["count_me"] ?></li>
					<li style = "margin-top:7px;"><a class = "twinsProduct" href="<?php echo SITE?>/<?php echo isset($item["category_url"])? $item["category_url"] : 'vse' ?>">Похожие товары</a></li>	
					<!--li>артикул: <?php echo $item["article"] ?></li-->					
				</ul>
			</td>
			<td class = "im_product_icon" rowspan="2">
			
				<div style = 'background: url("<?php echo SITE?>/uploads/<?php echo $item["image_url"] ? $item["image_url"] : "none.png" ?>") no-repeat scroll 50% 50%;background-size:contain;width:206px;height:170px;'></div>
				
				<!--a href="<?php echo SITE?>/<?php echo isset($item["category_url"])? $item["category_url"] : 'vse' ?>/<?php echo htmlspecialchars($item["product_url"]) ?>?backurl=search/<?php echo $data['activPage']?>/<?php echo $data['inputString']?>"><image src="<?php echo SITE?>/uploads/<?php echo $item["image_url"] ? $item["image_url"] : "none.png" ?>" /></a-->
			</td>
		</tr>
		<tr>
			<td class = "im_product_submit" id = '<?php echo $item["id"] ?>'>
				Добавить в заказ
			</td>
		</tr>
	</table>
</div>       
		
<?php
  $i++;
}

echo $data['pager'];

/*echo "<pre>";
print_r($data);
echo "</pre>";*/