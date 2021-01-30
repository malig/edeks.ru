<?php
/**
 * Файл вида product.php
 */
mgTitle($data['name']); 
?>
<a href=""><?php echo $data['name'] ?></a>

<div class = "im_product">
	<table>
		<tr>
			<td class = "im_product_icon" rowspan="2"
				style = 'background: url("<?php echo SITE?>/uploads/<?php echo $item["image_url"] ? $item["image_url"] : "none.png" ?>") no-repeat scroll 50% 50%;'>
				<!--img src="<?php echo SITE?>/uploads/<?php echo $data["image_url"] ? $data["image_url"] : "none.png" ?>" alt="<?php echo $data['name'] ?>" title="<?php echo $data['name'] ?>" /-->
			</td>
			<td class = "im_product_description">
				<a href="">Характеристики товара</a>
				<ul>
					<!--li>описание: <span><?php echo $data['desc'] ?></span></li-->
					<li>цена: <?php echo $data['price'] ?> р.</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td class = "im_product_foot">
				<a class="arrowLeft" href="<?php echo SITE?>/<?php echo $data['backUri'] ?>">Назад</a>
			</td>
		</tr>
	</table>


</div> 