
<link rel="stylesheet" href="<?php echo SITE?>/mg-admin/design/style.css" type="text/css" />
<?php if(USER::isAuth() && ('1' == USER::getThis()->role || '12' == USER::getThis()->role)): ?>
<html>
 <?php MG::titlePage('Moguta.CMS');?>
  <head>
    <script type="text/javascript" src="<?php echo SITE?>/mg-core/script/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="<?php echo SITE?>/mg-core/script/admin/admin.js"></script>
	
		<style type="text/css" media="print">
			div.pagePrint
			{
				page-break-after: always;
				page-break-inside: avoid;
			}
		</style>
  </head>

  <body>

    <div id="admin-header">
      <div class="logo"></div>
      <div class="menu">
        <ul>
		
			<?php if('12' == USER::getThis()->role): ?>
				<li><a href="<?php echo SITE?>/" id="look"><span class="look">Просмотр</span></a></li>
				<li><a href="#" id="orders"><span class="archive">Заказы</span></a></li>
			<?php endif ?>
			
			<?php if('1' == USER::getThis()->role): ?>
				<li><a href="<?php echo SITE?>/" id="look"><span class="look">Просмотр</span></a></li>
				<li><a href="#" id="orders"><span class="archive">Заказы</span></a></li>
				<li><a href="#" id="product"><span class="products">Товары</span></a></li>
				<li><a href="#" id="category"><span class="category">Категории</span></a></li>
				<!--li><a href="#" id="page"><span class="page">Страницы</span></a></li-->
				<li><a href="#" id="settings"><span class="settings">Настройки</span></a></li>
				<!--li><a href="#" id="plugins"><span class="plugin">Плагины</span></a></li-->
				<li><a href="#" id="system"><span class="system">Система</span></a></li>
				<li><a href="#" id="users"><span class="archive">Пользователи</span></a></li>
			<?php endif ?>
			
        </ul>
      </div>
      <div class="user">
        <a href="#"><?php echo User::getThis()->name ?></a> (<a href="<?php echo SITE?>/enter?logout=1">Выход</a>)
      </div>
    </div>


    <div id="thisHostName" style="display:none">
      <?php echo SITE; ?>
    </div>

    <div id="msg_error" class="message_error error">
      <span>Сообщение об ошибке!</span>
    </div>

    <div id="msg_succes" class="message_succes succes">
      <span>Дейсвие выполнено!</span>
    </div>

    <div id="msg_alert" class="message_alert alert">
      <span>Предупреждение!</span>
    </div>

    <div id="msg_information" class="message_information inform">
      <span><b>MOGUTA.CMS</b> приветствует Вас!<br/>Начните управлять сайтом с раздела "Настройки", указав e-mail администратора и электронные кошельки для оплаты товаров! </span>
    </div>

    <?php if($newVersion){ ?>
      <div id ="newVersion" class="message_information inform" style="color:black; float: left; margin-left: 10px; padding: 17px 15px 0px 15px;">
        Доступна новая версия системы
      </div>
   <?php }?>

    <div id="content">
      <div class="data">

      </div>
    </div>


  </body>

</html>
<?php elseif(!$data['isIe']): ?>
<div class="login_form">
  <div class="login-box-wrap">
    <h2><span>Авторизация</span></h2>
    <div class="info">
      <?php
      if(! USER::isAuth()){
        echo 'Только администраторы могут пользоваться этим разделом!';
      }
      else{
        if(USER::getThis()->role > 1) echo 'У вас нет доступа к этой части сайта!';
      }
      ?>
      <br />
      <br />
      <span>Введите электронный адрес и пароль администратора:</span>
    </div>

    <div class="login-action">
      <form action="<?php echo SITE?>/enter" method="POST">
        <table id="login_form_table" style="margin-top:10px; width: 100%;">

          <tr>
            <td><input type="text" class="input_action user_ico" name="email" placeholder="E-mail" value="<?php echo $email;?>" /></td>
          </tr>

          <tr>
            <td><input type="password" class="input_action pass_ico" placeholder="Пароль" name="pass"  /></td>
          </tr>

          <tr>
            <td colspan="2">
              <input type="hidden" name="location" value="/mg-admin" />
              <input class="enter_but" type="submit" value="Вход" />
            </td>
          </tr>
        </table>
      </form>
    </div>
      </div>
</div>
    <?php else: ?>
     <style>

      .noIE {
        width: 700px;
        margin: 0 auto;
        padding: 50px 20px 20px 20px;
      }
      .panel-header span {
        padding-left: 40px;
        background-position: 8px center;
        color: #C5D52B;
        text-shadow: 0 0 6px rgba(197, 213, 42, 0.5);
        line-height: 24px;
        overflow: hidden;
        text-overflow: ellipsis;
        display: block;
        white-space: nowrap;
      }
      .panel-content {
        background: #EAEAEA;
        padding: 20px;
        text-shadow: 0 1px 0 #fff;
      }

      .panel-content p {
        margin: 0 0 20px 0;
        font-size: 15px;
        text-align: justify;
      }

     </style>

        <div class="noIE">
          <div class="m-panel grid_5">
            <div class="panel-header">
              <span>Ваш браузер: Internet Explorer</span>
            </div>
            <div class="panel-body">
              <div class="panel-content">
                <p>Пожалуйста, используйте другой браузер для управления сайтом.</p>
              </div>
            </div>
          </div>
          <div class="cleaner"></div>
        </div>

   <?php endif; ?>
