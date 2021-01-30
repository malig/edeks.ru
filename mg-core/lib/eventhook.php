<?php
/**
 * Класс EventHook - вешает обработчик для заданного хука.
 * Доступен из любой точки программы.
 * Реализован в виде синглтона, что исключает его дублирование.
 */
class EventHook implements Hook{

  // Наименование хука.
  private $_hookName;
  // Пользовательская функция, которая сработает при хуке.
  private $_functionName;
  // Количество агрументов которое ждет пользовательская функция.
  private $_countArg;
  // Приоритет выполнения.
  private $_priority;


  public function __construct($hookName, $functionName, $countArg = 0, $priority = 10){

    //Если имя хука является путем, то названием считается последняя директория в пути.
    //Необходимо для валидной работы страницы настроек плагина.
    $section = explode(DIRECTORY_SEPARATOR , dirname($hookName));

    $hookName = count($section)>1
      ? end($section)
      : $hookName;

    $this->_hookName = $hookName;
    $this->_functionName = $functionName;
    $this->_countArg = $countArg;
    $this->_priority = $priority;
  }


  /**
   * Запускает обработчик для хука.
   * @param type $arg массив параметров.
   * @return type результат работы пользовательской функции.
   */
  public function run($arg){

    if(function_exists($this->_functionName)){
      // Если  хук передал параметры, то передать их в пользовательскую функцию.
      if(empty($arg)){
        return call_user_func($this->_functionName);
      } else{
        $args[0] = $arg;
        return call_user_func_array($this->_functionName, $args);
      }
    }

  }


  /**
   * Возвращает название хука.
   */
  public function getHookName(){
    return $this->_hookName;
  }


  /**
   * Возвращает количество агрументов которое ожидает пользовательская функция.
   */
  public function getCountArg(){
    return $this->_countArg;
  }


  /**
   * Возвращает приоритет пользовательской функций.
   */
  public function getPriority(){
    return $this->_priority;
  }

}
