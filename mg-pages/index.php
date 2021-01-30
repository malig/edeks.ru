<?php mgTitle('Главная страница');?>
<a href="">Главная страница</a>

<?php
//Если акция пирамида еще работает, расчитать и начислить бонусы для всех участников
if(MG::getOption('piramida') == '1'){
	if(isset($_REQUEST['visit']) && empty($_COOKIE['visit'])){		
		SetCookie('visit', $_REQUEST['visit'], time() + 3600 * 24 * 365);			
	}	
}
?>

<div class="mainCont">
	<div class="mainContText">

		<p class = "first" style = "color:red"><strong>ВНИМАНИЕ!!! Сайт в процессе разработки</strong></p><br>

		<!--h1>Здравствуйте, уважаемый пользователь!</h1-->
		<p class = "first">Данный сайт предназначен для покупки товаров с доставкой на дом в городе <strong>ТОМСК</strong>.</p>
		
		<ul>
			<li>Надоело стоять в очередях?</li>
			<li>Тяжело носить пакеты?</li>
			<li>Нет желания выходить из дома? </li>
			<!--li>Но необходимо срочно пополнить запасы!</li-->	
		</ul></br></br>
		
		<p class = "slogan" style = "text-align:center">Сделай заказ, добавь комфорт в свою жизнь!</p></br></br>

		<div class = "sale_message">
			<strong>Внимание !!!</strong> 
			Зарегистрируйтесь на сайте до 31 декабря и получите возможность зарабатывать <a href="bonus">БОНУСЫ</a> по акции «Пирамида».
		</div>

	</div>

</div>

<div class="mainContLink">
	<a href="specification">Как это работает?</a></br></br>
	<a href="motivation">Почему это выгодно?</a></br></br>
	<a href="budget">Планирование покупок</a></br></br>
	<a href="bonus">Бонусы</a></br></br>
	<a href="planning">Ближайшие задачи</a></br></br>
</div>
