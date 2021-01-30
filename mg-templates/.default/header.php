<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
    <?php mgMeta(); ?>
    <script type="text/javascript" src="<?php echo PATH_SITE_TEMPLATE ?>/js/hoverIntent.js"></script>
    <script type="text/javascript" src="<?php echo PATH_SITE_TEMPLATE ?>/js/jquery.dropdown.js"></script>
  </head>
  <body>
    <div id="wrapper">
      <div id="header">
        <div class="logo">
          <a href=""><img src="<?php echo PATH_SITE_TEMPLATE ?>/images/logo.png"/></a>
        </div>
        <div class="smalcart">
          <div class="cartIcon"><span>Корзина</span><span class="icon"></span></div>
          <div class="smalcartCont">
            <strong>Товаров в корзине:</strong>	<?php echo $data['cartCount'] ?> шт.
            <br/>
            <strong>На сумму:</strong>	<?php echo $data['cartPrice'] ?> руб.
            <br/>
          </div>
          <?php if($data['cartCount'] > 0): ?>
            <a href='cart'>Оформить заказ</a>
          <?php endif; ?>
        </div>
        <div class="menu">
          <?php mgMenu() ?>
          <?php if($thisUser = $data['thisUser']): ?>
            <div class="login">
              <a class="enter" href="personal"><?php echo $thisUser->name ?></a>
              <a class="logOut" href="<?php echo SITE?>/enter?logout=1">
                <span style="font-size:10px">[ выйти ]</span>
              </a>
            </div>
          <?php else: ?>
            <div class="login">
              <a class="enterLog" href="enter">Вход</a>
              <a class="logOut" href="<?php echo SITE?>/registration">
                <span style="font-size:10px">[ регистрация ]</span>
              </a>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <div class="breadcrumbs">
        <?php //echo $data['breadcrumbs'] ?>
      </div>
      <div id="sidebar">
        <div class="sidebarmenu">
          <ul class="dropdown">
            <?php echo $data['categoryList'] ?>
          </ul>
        </div>
      </div>
      <div id="content">

