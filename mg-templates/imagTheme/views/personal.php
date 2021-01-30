<?php mgTitle('Личный кабинет');?>

<h1>Личный кабинет</h1>

<?php 
	if($userInfo = $data['userInfo']){
		
		if($_REQUEST['addToCheck']){
			
			$listItemId = $_REQUEST;

			foreach($listItemId as $ItemId => $newCount){
				$id = '';

				if('item_' == substr($ItemId, 0, 5)){
					$id = substr($ItemId, 5);
					$count = $newCount;
					
				}elseif('del_' == substr($ItemId, 0, 4)){
					$id = substr($ItemId, 4);
					$count = 0;
				}

				if($id){
					$arrayProductId[$id] = (int) $count;
				}
			}
			
			$model = new Models_Cart;
			
			foreach($arrayProductId as $itemId => $Count){
				if($Count !== 0){
					$model -> addToCart($itemId, $Count);
				}
			}

			//$model->refreshCart($arrayProductId);
			SmalCart::setCartData();
			header('Location: '.SITE.'/personal');
			exit;
		}	
		
		echo "<pre>";
			//print_r($arrayProductId);
			//print_r($_SESSION);
		echo "</pre>";
?>
<div id = "personalOrders">

	<div class="mainCont" style = "width:877px; margin-left:17px;">
		<div class="mainContText">			
			<div id = "urluniq">
<?if(MG::getOption('piramida') == '1'):?>
				<table>
					<tr>
						<td>
							Разошли ссылку знакомым &#8212; получи <a href="bonus">бонусы</a> с их заказов:
						</td>
					</tr>
					<tr><td class = "uniqrow"><?echo "http://".MG::getOption('sitename')."?visit=".$data['userInfo']->user_hash?></td></tr>
					
				</table>
<?endif?>			
			</div>
			<div id = "bonus"><a href="bonus" style = "text-decoration: none;"><h1>Бонусов: <?echo $data['userInfo']->prize?></h1></a></div>
		</div>
	</div>

	<div class="wrap">
		<div class="over_bg" >
			<div class="m-panel grid_5">
				<div class="panel-header" >
					<span class="m-order-24">Ваши заказы</span>
				</div>
				
				<table id="table_order">
					<tr>
						<th>№</th>
						<th>Имя</th>
						<th>Тел.</th>
						<th>Дата</th>
						<th>Сумма</th>
						<th style="display:none;">Состав заказа</th>
					</tr>

				<?
					$odd = 1;
					foreach($data['arOrder'] as $order){
						$odd = !$odd;
						$odd ? $rowColor = 'odd' : $rowColor = 'event';
				?>		
						<tr class="<? echo $rowColor ?>" order_id = "<? echo $order['id']?>" >
							<td ><? echo $order['id']?></td>
							<td ><? echo $order['name']?></td>
							<td ><? echo $order['phone']?></td>
							<td ><? echo date("d/m/y G:i",strtotime($order['date']))?></td>
							<td ><? echo $order['summ']?></td>
							<td class="order_content" style="display:none;"><? echo $printOrderItems?></td>
						</tr>
						<tr id = "tr<? echo $order['id']?>" class = "trHide"><td colspan="5">		
								
					<!--Список товаров в заказе-->
						<form action="<?php echo SITE?>/personal" method="post">
							<table class="table_cart">
								<tr>
									<th>№</th>
									<th>Изображение</th>
									<th>Наименование</th>
									<th>Стоимость</th>
									<th>Количество</th>
									<th>Сумма</th>
									<th>Не добавлять</th>
								</tr>
					<?php 
						$i = 1;
		
						$orderItems = json_decode(str_replace('%%', '%',$order['order_content']),true);	
						//$totalSummm = $order['summ'] + MG::getOption('price_delivery');
						$totalSummm = $order['summ'];

							foreach($orderItems as $product):?>
								<tr class = "row">
									<td><?php echo $i++ ?></td>
									<td style="background: url('<?php echo SITE?>/uploads/<?php echo $product['article']?>.jpg') 50% 50% no-repeat; background-size:contain; width:60px; height:60px"></td>
									<td><?php echo $product['name'] ?></td>
									<td><?php echo $product['price'] ?></td>
									<td>
										<input type="text" style="text-align:center; color:#808080;" size="3" name="item_<?php echo $product['id'] ?>" value = "<?php echo $product['count']?>" />
									</td>
									<td><?php echo $product['summm']?></td>
									<td>
										<input type="checkbox" name="del_<?php echo $product['id'] ?>">
									</td>
								</tr>
								
							<?php endforeach;?>
		
								<tr class="totalRow">
									<td colspan = 4>
											<input type="submit" name="addToCheck" class="button" value="Добавить в чек"  style="margin:10px; float:left" />
						</form>

										<!--form action="<?php echo SITE?>/order" method="post" style="margin-left:0px;">
											<input type="submit" name="order" value="Далее" style="margin:10px" class="button" />
										</form-->
									</td>
									<td>Итого: </td>
									<td colspan = 4><strong><span style="color: #00ABC2"><?php echo $totalSummm ?></span></strong></td>									
								</tr>
							</table> <!--end table_cart-->
						</td></tr>
				<?	  
					} 	//end foreach($data['arOrder'] as $order
				?>  

				</table><!--end table_order-->

				<div><?php echo $data['pagination']?></div>
	<?					
		}			//end if($userInfo = $data['userInfo'])
	?> 				
			</div> 	<!--end m-panel grid_5--> 
		</div>  	<!-- endover_bg-->
	</div>  		<!-- end wrap-->

</div>  			<!--end personalOrders-->
