<h1>Личный кабинет</h1>
<?php mgTitle('Личный кабинет');?>
<?php if($userInfo = $data['userInfo']):?>
Личный кабинет пользователя <strong>"<?php echo $userInfo->name?>"<strong>
<?php else:?>
Личный кабинет доступен только авторизованым пользователям!

<?php endif;?>