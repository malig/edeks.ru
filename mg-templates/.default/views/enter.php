<?php mgTitle('Авторизация'); ?>

<h1>Авторизация </h1>

<?php echo $data['msgError'] ?>

<div class="mainCont">
  <form action = "<?php echo SITE?>/enter" method = "POST">
    <table>
      <tr>
        <td>
          <label name = "login">E-mail:</label>
        </td>
        <td>
        <input type = "text" name = "email" value = "<?php echo $_POST['email']?>" />
        </td>
      </tr>
      <tr>
        <td>
          <label name = "pass">Пароль:</label>
        </td>
        <td>
          <input type="password" name="pass" />
        </td>
      </tr>
    </table>
    <br />
    <input type = "submit" class="btn" value = "Вход" />
  </form>
</div>


