<?php mgTitle('Как это работает');?>
<a style = "float: left;" href="<?php echo SITE?>">Главная страница / </a>
<a style = "float: left; margin-left:5px;" href="">Как это работает?</a>

<div style = "clear:both;" class="mainCont">
<div class="mainContText">

	<ul>
		<li>Вы выбирает необходимые вам товары и оформляете заказ</li>
		<li>Наш оператор перезванивает вам, подтверждает заказ и уточняет время доставки</li>
		<li>Доставки осуществляется с 18-00 по 22-00, без выходных</li>
		<li>Заказы, сформированные после 18-00, переносятся на следующий день</li>
		<li>Оплата осуществляется наличными, в момент доставки товаров</li>
		<li>Мы не накручиваем цены на товары. Таким образом, вы получаете лучшие цены в городе, оплачивая лишь доставку.</li>
		<li>Стоимость доставки по городу <strong>ТОМСК</strong> - <?=MG::getOption('price_delivery');?> руб.,
			если расчёт без сдачи - <?=MG::getOption('price_delivery')-10;?> руб.
		</li>
	</ul>

	</br><p><strong>Рекомендации:</strong></p>

	<ul>
		<li>Регистрируйтесь. Зарегистрированные пользователи имеют ряд преимуществ:</li>
		<ul>
			<li>Возможность зарабатывать <a href="bonus">БОНУСЫ</a>, которыми можно расплачиваться за заказ</li>
			<li>Доступ к истории заказов</li>
			<li>Формирование заказа из истории (не нужно заново искать однажды найденные товары)</li>
		</ul>
		<li>Если вы обнаружили расхождение в названии товара с его изображением, добавлять товар в заказ следует с учётом названия</li>
		<li>Активно пользуйтесь обратной связью. Сообщайте обо всех найденных вами ошибках.<br>
		Это позволит нам сделать наш сайт лучше, а вам заработать дополнительные бонусы
		</li>
		<li>Обращайте внимание на указанное кол-во товара, некоторые лоты продаются мелким оптом</li>
	</ul>	
	
</div>	
</div>

<div class="mainContLink">
	<a href="<?php echo SITE?>">На главную</a></br></br>
	<a href="motivation">Почему это выгодно?</a></br></br>
	<a href="budget">Планирование покупок</a></br></br>
	<a href="bonus">Бонусы</a></br></br>
	<a href="planning">Ближайшие задачи</a></br></br>
</div>