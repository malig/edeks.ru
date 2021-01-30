<?php 
$data['totalSumm'] = $data['totalSumm'] + MG::getOption('price_delivery'); //Общая сумма заказа плюс доставка
echo $data['totalSumm'].'[total]';
?>
<div class = "im_check_header">
	<div class = "im_check_description">
		<h2>Корзина</h2>
		<div class = "im_check_description_v">Список выбранных товаров:</div>
	</div>
</div>

<div id = "im_check_product_contaner">

<?php foreach($data['productPositions'] as $product):?>
	<div class = "im_check_product">
		<div class = "im_check_description">
			<div class = "im_check_separator"></div>
			<div><a href=""><?php echo $product['name'] ?>,</a></div>
			<div class = "im_check_description_v"><?php echo $_SESSION['cart'][$product['id']]?> шт<span><?php echo ($_SESSION['cart'][$product['id']] * $product['price'])?> руб.</span></div>                    
		</div>
		<div class = "im_check_price">
			<div>
				<img id = '<?php echo $product['id']?>' alt = "close_icon" src="<?php echo PATH_SITE_TEMPLATE ?>/images/close.png">                        
			</div>
		</div>
	</div>
<?php endforeach;?>		
					
	<div class = "im_check_product">
		<div class = "im_check_description delivery">
			<div class = "im_check_separator"></div>
			<span>СТОИМОСТЬ ДОСТАВКИ</span>
			<span id = "delivery_check"><?=MG::getOption('price_delivery');?> руб. </span>
		</div>
	</div>		
			
</div> <!--check_product_contaner-->

<div id = "im_check_footer">
	<div class = "im_check_description">
		<div class = "im_check_separator"></div>
		<div class = "im_check_description_v">Итого:<span><?php if($data['totalSumm'] == '')echo '0'; else echo $data['totalSumm']?> руб.</span></div>
		<div class = "im_check_order">
			<a href='cart'>Оформить доставку</a>			
		</div>
	</div>
</div>
