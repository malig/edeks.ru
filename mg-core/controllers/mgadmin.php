<?php
/**
 * Контроллер: Mgadmin
 *
 * Класс Controllers_Mgadmin предназначен для открытия панели администрирования.
 * - Получает данные о пользовательском браузере;
 * - Запрещает вывод админки для браузеров 'MSIE';
 * - Проверяет наличие обновлений движка на сервере.
 */
class Controllers_Mgadmin extends BaseController{

  function __construct(){
    MG::disableTemplate();
    $this->data = array(
      'isIe' => stristr($_SERVER['HTTP_USER_AGENT'], 'MSIE')
    );

    //$this->newVersion = Updata::checkUpdata();
  }

}