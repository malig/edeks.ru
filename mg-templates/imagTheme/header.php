<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<?php mgMeta(); ?>
		<script type="text/javascript" src="<?php echo SITE ?>/mg-core/script/jquery-1.7.2.min.js"></script>
		<script type="text/javascript" src="<?php echo PATH_SITE_TEMPLATE ?>/js/hoverIntent.js"></script>
		<script type="text/javascript" src="<?php echo PATH_SITE_TEMPLATE ?>/js/jquery.dropdown.js"></script>	
		<script type="text/javascript" src = "<?php echo PATH_SITE_TEMPLATE ?>/js/check_open.js"></script>		
		<script type="text/javascript" src="<?php echo PATH_SITE_TEMPLATE ?>/js/accordion.js"></script>
		<script type="text/javascript" src="<?php echo PATH_SITE_TEMPLATE ?>/js/orders.js"></script>
		
		<link rel="icon" type="image/vnd.microsoft.icon" href="<?php echo SITE ?>/favicon.ico">
		<link rel="SHORTCUT ICON" href="<?php echo SITE ?>/favicon.ico">
		
		<script language="JavaScript">

			$(document).ready(function() {
				$(".topnav").accordion({
					accordion:true,
					speed: 500,
					closedSign: '<img src="<?php echo PATH_SITE_TEMPLATE ?>/images/down-arrow.png" />',
					openedSign: '<img src="<?php echo PATH_SITE_TEMPLATE ?>/images/up-arrow.png" />'
				});
			});

		</script>
		
	</head>


<body>
	<div id="im_wraper">
		<div id="im_header">
			<a id="im_logo" href="<?php echo SITE?>"></a>
			<div id="im_title_squares"></div>
				
			<div id="im_auth">
				<a id = "im_info" href="map">Карта сайта</a>
				<a id = "im_feedback" href="<?php echo SITE?>/feedback">Обратная связь</a>
				<div style="clear:both;"></div> 
				
				<?
				if(isset($_SESSION['user'])){
					$enter = '<a id = "im_login" href="enter?logout=1">Выход</a><a style = "border:0; "href = "'.
								SITE.'/personal" ><span>['.$_SESSION['user']->name.']</span></a>';
				}else{
					$enter = '<a id = "im_login" href="enter">Вход</a><span>в личный кабинет</span>';
				}	
				?>	

				<?echo $enter;?>
				<div style="clear:both;"></div> 
				<a id = "im_registr" href="<?php echo SITE?>/registration">Зарегистрироваться</a>
				<a href="bonus" style = "float:left; margin:2px 0 0 21px">Бонусы</a>                 
			</div>
				
			<div id="im_search_squares"></div> 
			
			<div id="im_search">
					<span>Найдите нужные продукты</span>
					<input id = "inputString" type = "text"><a id="lookup" href="#"></a>	
			</div>			
				
			<div id="im_check">
				<div>
					<div class = "im_arrow" id = "im_up"></div>
					<div class = "im_arrow" id = "im_down"></div>
				</div>
				
				<div id = "im_check_close">
					<div class = "im_check_header">
						<div class = "im_check_description">
							<h2>Корзина</h2>
							<div class = "im_check_description_v">Итоговая сумма:<span id = "totalSumm"><?php echo $data['cartPrice'] ?> руб.</span></div>
						</div>
					</div>
					
				</div>
				
				<div id = "im_check_open">
				
				</div>
			</div><!--check-->
		</div><!--header-->

		<div id="im_sidebar">
			<ul class="im_vert_menu topnav">
				<?php echo $data['categoryList'] ?>
			</ul>
		</div>
				
		<div id="im_content">
		
			<div id="msg_alert" class="message_alert alert">
			  <span>Товар добавлен!</span>
			</div>
			
			<div class = "im_product_list">
