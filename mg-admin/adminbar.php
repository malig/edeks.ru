<?php
/**
 * Панель администрирования, подключается в публичной части сайта,
 * если пользователь является администратором
 */
?>
<div id="admin-header">
  <div class="menu">
    <ul>
      <li><a href="<?php echo SITE?>/mg-admin" id="look"><span class="look">Управление сайтом</span></a></li>
    </ul>
  </div>
  <div class="user">
    <a href="#"><?php echo User::getThis()->name ?></a> (<a href="<?php echo SITE?>/enter?logout=1">Выход</a>)
  </div>
</div>
<div style="clear:both; height:50px;"></div>