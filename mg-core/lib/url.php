<?php
/**
 * Класс URL - предназначен для работы со ссылками, а также с адресной строкой.
 * Доступен из любой точки программы.
 *
 * Реализован в виде синглтона, что исключает его дублирование.
 * Имеет в себе реестр queryParams для хранения любых объектов.
 */
class URL{

  static private $_instance = null;
  static private $cutPath = '';
  static private $route = 'index';

  /**
   * Исключает XSS уязвимости для вех пользовательских данных.
   * Сохраняет все переданные параметры в реестр queryParams,
   * в дальнейшем доступный из любой точки программы.
   * Выявляет часть пути в ссылках, по $_SERVER['SCRIPT_NAME'],
   * которая не должна учитываться при выборе контролера.
   * Актуально когда файлы движка лежат не в корне сайта.
   */
  private function __construct(){
    self::$cutPath = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
    $route = self::getLastSection();
    $route = $route ? $route : 'index';

    if($route == 'mg-admin'){
      $route = 'mgadmin';
    }

    // Заполняем QUERY_STRING переменной route.
    $_SERVER['QUERY_STRING'] = 'route='.$route;
    $route = str_replace('.html', '', $route);

     // Конвертируем обращение к контролеру админки в подобающий вид.
    self::$route = $route;

    // Если данные пришли не из админки и не из плагинов а от пользователей то проверяим их на XSS.
    // Также исключение действует на просмотрщик страниц,
    // он защищен от стороннего использования в контролере, поэтому исключает опасность.
    if(strpos($route,'mgadmin')===false && strpos($route,'ajax')===false && strpos($route,'previewer')===false){
      $_REQUEST = MG::defenderXss($_REQUEST);
      $_POST = MG::defenderXss($_POST);
      $_GET = MG::defenderXss($_GET);
    }

    $this->queryParams = $_REQUEST;
  }

  private function __clone(){

  }

  private function __wakeup(){

  }


  /**
   * Конвертирует рускоязычны URL в транслит.
   * @param string $str рускоязычный url.
   * @return string|bool
   */
  public static function createUrl($urlstr){
    $result = false;
    if(preg_match('/[^A-Za-z0-9_\-]/', $urlstr)){
      $urlstr = translitIt($urlstr);
      $urlstr = preg_replace('/[^A-Za-z0-9_\-]/', '', $urlstr);
      $result = $urlstr;
    }

    $args = func_get_args();
    return MG::createHook( __CLASS__ ."_". __FUNCTION__, $result, $args);
  }

  /**
   * Возвращет защищенный параметр из массива $_GET.
   * @return object
   */
  public static function get($param){
    return self::getQueryParametr($param);
  }


  /**
   * Вовзращает чистый URI, без строки с get параметрами.
   * @return type
   */
  public static function getClearUri(){
    $data = self::getDataUrl();
    return str_replace(self::$cutPath, '', $data['path']);
  }


  /**
   * Вовзращает часть пути, до папки с CMS.
   * Например если движок расположен по этому пути http://sitname.ru/shop/index.php,
   * то  метод вернет строку "/shop"
   * @return string
   */
  public static function getCutPath(){
    return self::$cutPath;
  }


  /**
   * Вовзращает количество секций.
   * @return type
   */
  public static function getCountSections(){
    $sections = self::getSections();
    return count($sections) - 1;
  }


  /**
   * Вовзращает массив составных частей ссылки.
   * @return type
   */
  public static function getDataUrl($url = false){
    if(!$url){
      $url = URL::getUrl();
    }
    return parse_url($url);
  }


  /**
   * Возвращет единственный экземпляр данного класса.
   * @return object - объект класса URL.
   */
  static public function getInstance(){
    if(is_null(self::$_instance)){
      self::$_instance = new self;
    }
    return self::$_instance;
  }


  /**
   * Вовзращает последнюю часть uri.
   * @return type
   */
  public static function getLastSection(){
    $sections = self::getSections();
    $lastSections = end($sections);

    return str_replace('.html','',$lastSections);
  }


  /**
   * Вовзращает часть пути, до папки с CMS.
   * Например если движок расположен по этому пути http://sitname.ru/shop/index.php,
   * то  метод вернет строку "/shop"
   * @return string
   */
  public static function getCutSection(){
    return str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
  }


  /**
   * Вовзращает запрошенный request параметр.
   * @return type
   */
  public static function getQueryParametr($param){
    $params = self::getInstance()->queryParams;
    return $params[$param];
  }


  /**
   * Вовзращает запрошенную строку параметров.
   * @return string
   */
  public static function getQueryString(){
    return $_SERVER['QUERY_STRING'];
  }


  /**
   * Вовзращает массив секций URI.
   * @return string
   */
  public static function getSections(){
    $uri = self::getClearUri();
    $sections = explode('/', rtrim($uri, '/'));
    return $sections;
  }


  /**
   * Вовзращает  URI, с get параметров.
   * @return type
   */
  public static function getUri(){
    return $_SERVER['REQUEST_URI'];
  }


  /**
   * Вовзращает ссылку с хостом и протоколом.
   * @return string
   */
  public static function getUrl(){
    return 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
  }


  /**
   * Вовзращает имя для роутера.
   * @return string
   */
  public static function getRoute(){
    return self::$route;
  }


  /**
   * Инициализирует данный класс URL.
   * @return void
   */
  public static function init(){
    self::getInstance();
  }


  /**
   * Проверяет является ли полученное значение  - именем текущего раздела.
   * Пример:  isSection('catalog') вернет true если открыта страница каталога.
   * @param string $section название секции.
   * @return bool
   */
  public static function isSection($section){
    $sections = self::getSections();
    return ($sections[1] == $section)? true: false;
  }


  /**
   * Возвращает защищенный параметр из $_POST массива.
   * @return string
   * @param string $param запрошеный параметр.
   */
  public static function post($param){
    return self::getQueryParametr($param);
  }


  /**
   * Устанавливает параметр в реестр URL. Можно использовать как реестр переменных.
   * @param string $param наименование параметра.
   * $value string $param значение параметра.
   */
  public static function setQueryParametr($param, $value){
    self::getInstance()->queryParams[$param] = $value;
  }

}