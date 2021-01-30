<?php

/**
 * Модель: Personal
 *
 * Класс Models_Personal реализует логику взаимодействия с личным кабинетом пользователя.
 * - Находится в состоянии разработки;
 */

class Models_Personal{

  /**
   * Заготовочная функция, возвращает данные.
   */
  public function getData(){
    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, false, $args);
  }

}