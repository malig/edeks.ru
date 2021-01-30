<?php

/**
 * Класс DB - предназначен для работы с базой.
 * Доступен из любой точки программы.
 * Реализован в виде синглтона, что исключает его дублирование.
 * Имеет механизм защищающий базу от SQL инъекций.
 */
class DB{

  static private $_instance = null;

  private function __construct(){

    //Подключается к хосту.
    $this->connect = mysql_connect(HOST, USER, PASSWORD) or die('Невозможно установить соединение'.mysql_error());

    //Выбирает базу.
    mysql_select_db(NAME_BD, $this->connect) or die('Невозможно выбрать указанную базу'.mysql_error());
  }

  private function __clone(){

  }

  private function __wakeup(){

  }

  /**
   * Строит часть запроса, из полученного ассоциаливного массива.
   * Как правило используется для оператора SET.
   * Пример:
   * $array = (
   *   'login' => 'admin',
   *   'pass' => '1',
   * );
   *
   * Данный метод преобразует массив строку: "'login' = 'admin', 'pass' => '1'"
   * @param array $array ассоциативный массив полей с данными.
   * @param string $devide разделитель.
   *
   * @return string
   */
  public static function buildPartQuery($array, $devide = ','){
    $partQuery = '';

    if(is_array($array)){
      $partQuery = '';
      foreach($array as $index => $value){
        $partQuery .= sprintf(' `%s` = "%s"'.$devide, $index, mysql_real_escape_string($value));
      }
      $partQuery = trim($partQuery, $devide);
      $partQuery = str_replace("%", "%%", $partQuery);
    }
    return $partQuery;
  }

  /**
   * Аналогичен методу buildPartQuery, но используется для целого запроса.
   * Как правило для WHERE.
   *
   * @param string SQL запрос.
   * @param array $array ассоциативный массив.
   * @param string $devide разделитель
   * @return obj|bool
   */
  public static function buildQuery($query, $array, $devide = ','){

    if(is_array($array)){
      $partQuery = '';

      foreach($array as $index => $value){
        $partQuery .= sprintf(' `%s` = "%s"'.$devide, $index, mysql_real_escape_string($value));
      }

      $partQuery = trim($partQuery, $devide);
      $partQuery = str_replace("%", "%%", $partQuery);
      $query .= $partQuery;

      return self::query($query);
    }
    return false;
  }

  /**
   * Возвращает запись в виде массива.
   * @param obj $object
   * @return array
   */
  public static function fetchArray($object){
    return @mysql_fetch_array($object);
  }

  /**
   * Возвращает ряд результата запроса в качестве ассоциативного массива.
   * @param obj $object
   * @return array
   */
  public static function fetchAssoc($object){
    return @mysql_fetch_assoc($object);
  }

  /**
   * Возвращает запись в виде объекта.
   * @param obj $object
   * @return obj
   */
  public static function fetchObject($object){
    return @mysql_fetch_object($object);
  }

  /**
   * Возвращет единственный экземпляр данного класса.
   * @return object - объект класса DB
   */
  static public function getInstance(){
    if(is_null(self::$_instance)){
      self::$_instance = new self;
    }
    return self::$_instance;
  }

  /**
   * Инициализирует единственный объект данного класса.
   * @return object - объект класса DB
   */
  public static function init(){
    self::getInstance();
    DB::query('SET names utf8');
  }

  /**
   * Возвращает сгенерированный колонкой с AUTO_INCREMENT
   * последним запросом INSERT к серверу.
   * @return int
   */
  public static function insertId(){
    return @mysql_insert_id();
  }

  /**
   * Возвращает количество рядов результата запроса.
   * @param obj $object
   * @return int
   */
  public static function numRows($object){
    return @mysql_num_rows($object);
  }

  /**
   * Выполняет запрос к БД.
   *
   * @param srting $sql запрос.( Может содержать дополнительные аргументы.)
   * @return obj|bool
   */
  public static function query($sql){

    if(($num_args = func_num_args()) > 1){
      $arg = func_get_args();
      unset($arg[0]);

      // Экранируем кавычки для всех входных параметров
      foreach($arg as $argument => $value){
        $arg[$argument] = mysql_real_escape_string($value);

      }
      $sql = vsprintf($sql, $arg);
    }
    $obj = self::$_instance;

    if(isset($obj->connect)){
      $obj->count_sql++;
      // $startTimeSql = microtime(true);
      // $log = "<br/><br/><span style='color:blue'> <span style='color:green'># Запрос номер ".$obj->count_sql.": </span>".$sql."</span> <span style='color:green'>(".round($time_sql,4)." msec )</span>";
      // echo $log;
      //loger($log);

      $result = mysql_query($sql) or die('<br/><span style="color:red">Ошибка в SQL запросе:</span> '.mysql_error());
      $timeSql = microtime(true) - $startTimeSql;
      return $result;
    }
    return false;
  }

  /**
   * Экранирует кавычки для части запроса.
   *
   * @param srting $sql часть запроса.
   */
  public static function quote($string){
    return "'".mysql_real_escape_string($string)."'";
  }
}