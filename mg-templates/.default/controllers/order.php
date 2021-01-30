<?php
/**
 * Контроллер: Order
 *
 * Класс Controllers_Order обрабатывает действия пользователей на странице оформления заказа.
 * - Производит проверку ввденых данных в форму оформления заказа;
 * - Добавляет заказ в базу данных сайта;
 * - Отправляет письмо на указанный адрес покупателя и администратору сайта;
 * - Очищает корзину товаров, при успешно оформлении заказа;
 * - Перенаправляет на страницу с сообщеним об успешном оформлении заказа;
 * - Генерирует данные для страниц успешной и неудавшейся електронной оплаты товаров.
 */
class Controllers_Order extends BaseController{

  function __construct(){

    $error = null;
    $data = array(
     'error' => $error,
     'dislpayForm' => true,
    );

    // Если пришли данные с формы.
    if(isset($_POST['toOrder'])){

      // Создает модель для работы заказом
      $model = new Models_Order;

      // Проверяет на корректность вода.
      $error = $model->isValidData($_POST);

      // Если ошибок нет, то добавляет заказ в БД.
      if(!$error){
        $orderId = $model->addOrder();

        // Пересчитывает маленькую корзину.
        SmalCart::setCartData();
        MG::redirect('/order?thanks='.$orderId.'&pay='.$model->payment."&summ=".$model->summ);
        exit;
      }

    }

    if(isset($_REQUEST['thanks']) && !$error){
      $data = array(
        'id' => $_REQUEST['thanks'],
        'summ' => $_REQUEST['summ'],
        'pay' => $_REQUEST['pay'],
        'dislpayForm' => false,
      );
    }

    if(isset($_REQUEST['payment']) && !$error){


      if('success' == $_REQUEST['payment']){
            
        $message = 'Вы успешно оплатили заказ!';
      }
      if('fail' == $_REQUEST['payment']){ 
      
        $message = 'Платеж не удался!<br/> Попробуйте снова, перейдя по ссылке из письма с уведомлением о принятии вашего заказа.';
          
      }

      $data = array(
        'dislpayForm' => false,
        'message' => $message,
        'pay' => $_REQUEST['pay'],
        'payment'=> $_REQUEST['payment']
      );

    }

    $this->data = $data;
  }

}