<?php

/**
 * Класс PM - плагин менеджер, управляет плагинами и
 * регистрирует их. Устанавливает взаимодействие пользовательских функций с системой.
 */
class PM implements PluginManager {

  static private $_instance = null;

  // Заристрированные обработчики хуков
  static private $_eventHook;

  // Список зарегистрированных шорткодов
  static public $listShortCode = array();

  public function __construct() {
    self::$_eventHook = array();
  }


  private function __clone() {

  }


  private function __wakeup() {

  }


  /**
   * Возвращет единственный экземпляр данного класса
   * @return object - объект класса PM
   */
  public static function getInstance() {
    if (is_null(self::$_instance)) {
      self::$_instance = new self;
    }
    return self::$_instance;
  }

  /**
   * Возвращает массив названий шорткодов.
   * Все хуки для шорткодов начинаются с префикса "shortcode_"
   * @return array - массив названий шорткодов
   */
  public static function getListShortCode() {
    if(empty(self::$listShortCode)){
      $result = array();
      if (sizeof(self::$_eventHook)) {
        foreach (self::$_eventHook as $eventHook) {
          $nameHook = $eventHook->getHookName();
          if(strpos($nameHook, 'shortcode_') === 0){
            $result[] = str_replace('shortcode_', '', $nameHook);
          }
        }
      }
      self::$listShortCode = $result;
    }
    return self::$listShortCode;
  }

  /**
   * Возвращает массив названий зарегистрированных хуков.
   * @return array -  массив названий зарегистрированных хуков.
   */
  public static function getListNameHooks() {
    $result = array();
      if (sizeof(self::$_eventHook)) {
        foreach (self::$_eventHook as $eventHook) {
          $result[] = strtolower($eventHook->getHookName());
        }
      }
     return $result;
  }


  /**
   * Проверяет зарегистрирован ли хук.
   * @param $hookname - имя хука, который надо проверить на регистрацию.
   * @return bool.
   */
  public static function isHookInReg($hookname) {
     return in_array(strtolower($hookname), self::getListNameHooks());
  }


  /**
   * Инициализирует объект данного класса
   */
  public static function init() {
    self::getInstance();
  }


  /**
   * Регистрирует обработчик для действия, занося его в реестр обработчиков.
   * @param Hook $eventHook - объект содержащий информацию об обработчике и событии.
   */
  public static function registration(Hook $eventHook) {
    self::$_eventHook[] = $eventHook;
  }


  /**
   * Удаляет из рееестра данные об обработчике.
   * @param Hook $eventHook  - объект содержащий информацию об обработчике и событии.
   */
  public static function delete(Hook $eventHook) {
    if ($id = array_search($eventHook, self::$_eventHook, TRUE)) {
      unset(self::$_eventHook[$id]);
    }
  }


  /**
   * Вычисляет приоритетность пользовательских функций, назначеных на обработку одного и тогоже события.
   * Используется для сравнения приоритетов в функции.
   *
   * @param $a - приоритет текущей функции.
   * @param $a - приоритет предыдущей функции usort  в методе 'PM::createHook'.
   */
  public static function prioritet($a, $b) {
    return $a['priority'] - $b['priority'];
  }


  /**
   * Инициализирует то или иное событие в коде программы,
   * сообщая об этом всем зарегистрированных обработчикам.
   * Если существуют обработчики назначенные на данное событие,
   * то запускает их пользовательские функции, в порядке очереди
   * определенной приоритетами.
   *
   * @param string $hookName - название  события.
   * @param type $arg - массив арументов.
   * @param type $result - флаг, определяющий, должена ли пользовательская
   *   функция вернуть результат для дальнейшей работы в месте инициализации события.
   * @return array
   */
  public static function createHook($hookName, $arg, $result = false) {
    $hookName = strtolower($hookName);
    if ($result) {
      if (sizeof(self::$_eventHook)) {
        foreach (self::$_eventHook as $eventHook) {

          // Если нашлись пользовательские функции которые хотя обработать событие.
          if ($eventHook->getHookName() == $hookName
            && $eventHook->getCountArg() == 1) {

            // В массив найденых обработчиков записываем все обработчики и их порядок выполнения.
            $handleEventHooks[] = array(
              'eventHook' => $eventHook,
              'priority' => $eventHook->getPriority()
            );
          }
        }


        // Запускает функции всех подходящих обработчиков.
        if (!empty($handleEventHooks)) {

          // Сортировка в порядке приоритетов.
          usort($handleEventHooks, array(__CLASS__, "prioritet"));

          foreach ($handleEventHooks as $handle) {
            $arg['result'] = $handle['eventHook']->run($arg);
          }
          return $arg['result'];
        }
      }
      return $arg['result'];
    } else {
      // для варианта  создания хука: createHook(__CLASS__."_".__FUNCTION__, '$title');

      $countArg = count($arg);
      //echo $hookName.$countArg;
      if (sizeof(self::$_eventHook)) {
        foreach (self::$_eventHook as $eventHook) {

          if ($eventHook->getHookName() == $hookName
            && $eventHook->getCountArg() == $countArg) {

            $eventHook->run($arg);
          }
        }
      }
    }
  }


  /**
   *  Подключает все плагины соответствующии требованиям.
   *  - Все плагины должны содержаться в каталоге mg-plugins/;
   *  - Название папки содержащей плагин, может быть любым;
   *  - Если в папке плагина есть файл index.php и в первом блоковом комментариии
   *    он содержит хотябы один из доступных параметров PluginName ,
   *    то плагин будет подключен.
   *    Пример мета информации в index.php
   *    <code>
   *     Plugin Name: Hello World
   *     Plugin URI: http://moguta.ru/plugins/HelloWorld/
   *     Description: Плагин для демонстрации функционала
   *     Author: mogutaTeam
   *     Version: 1.0
   *    </code>
   *  @return void
   */
  public static function includePlugins() {
    $pluginsInfo = self::getPluginsInfo();

    foreach ($pluginsInfo as $plugin) {
      // Подключает только активные плагины.
      if ("1" == $plugin['Active']) {
        require_once PLUGIN_DIR.$plugin['folderName'].'/index.php';
      }
    }
  }


  /**
   *  Подключает один конкретный плагин хранящийся в выбраной директории.
   *  @param string $folderName - наименование папки плагина.
   *  @return void
   */
  public static function includePluginInFolder($folderName) {
    require_once PLUGIN_DIR.$folderName.'/index.php';
  }


  /**
   *  Считывает информацию обо всех плагинах в дирректории PLUGIN_DIR
   *  @return void
   */
  public static function getPluginsInfo() {
    $result = array();
    $plugins = scandir(PLUGIN_DIR);
    foreach ($plugins as $folderName) {
      if (!is_dir($folderName)) {
        $result[] = self::readInfo($folderName);
      }
    }

    // Считываем активность плагинов из БД.
    $res = DB::query("SELECT *  FROM plugins");
    while ($row = DB::fetchArray($res)) {
      $pluginsActivity[$row['folderName']] = $row['active'];
    }

    // Дополняем массив найденых плагинов информацие их активности.
    foreach ($result as $id => $plugin) {
      $result[$id]['Active'] = isset($pluginsActivity[$plugin['folderName']]) ? $pluginsActivity[$plugin['folderName']] : 0;
    }

    // Сортировка в порядке активности.
    usort($result, array(__CLASS__, "sortByActivity"));

    return $result;
  }

  /**
   * Функция для сортировки плагинов по активности.
   *
   * @param $a - активность текущего плагина.
   * @param $a - активность предыдущего плагина (usort  в методе 'PM::getPluginsInfo').
   */
  public static function sortByActivity($a, $b) {
    return $b['Active'] - $a['Active'];
  }

  /**
   * Считывает информацию о конкретном плагине плагине.
   * @param string $folderName - путь к файлу index.php плагина
   * @return если соответствует стандартам то array иначе null.
   */
  public static function readInfo($folderName) {
    $pluginDirectory = PLUGIN_DIR.$folderName.'/index.php';

    // Считываем содержание index.php.
    if (file_exists($pluginDirectory)) {
      $contentIndex = file_get_contents($pluginDirectory);

      // Удаляем все переносы строк.
      $contentIndex = str_replace(array("\r\n", "\n", "\r"), 'infoParam', $contentIndex);

      // Ищем все блочные комментарии.
      preg_match_all('~/\*(.*?)\*/~i', $contentIndex, $plauginInfo);
      $result = array();

      // Определяем доступные информационные параметры.
      $parametr = array(
        'PluginName' => 'Plugin Name',
        'PluginURI' => 'Plugin URI',
        'Description' => 'Description',
        'Author' => 'Author',
        'Version' => 'Version'
      );

      // Ищем в первом блочном комментарии информацию, по доступным параметрам $parametr.
      foreach ($parametr as $key => $value) {
        preg_match('~'.$value.'\s?:\s?(.*?)infoParam~i', $plauginInfo[1][0], $plauginData);
        $result[$key] = $plauginData[1];
      }

      // Если не существует параметра PluginName, то файл не корректно задает плагин.
      if (!empty($result['PluginName'])) {
        $result['folderName'] = $folderName;
        return $result;
      }
    }
    return null;
  }


  /**
   * Получает название папки в которой хранится плагин.
   * @param type $dir - директория в коотрой хранится плагин.
   */
  public static function getFolderPlugin($dir) {
    $section = explode(DIRECTORY_SEPARATOR, dirname($dir));
    $folderName = count($section) > 1 ? end($section) : $dir;
    return strtolower($folderName);
  }

  /**
   * Ищет в контенте шорткоды и запускает их обработчики.
   * Если шотркод не определен или его плагин отключен, он будет возвращен без обработки.
   *
   * @param string $content - строка для поиска в ней шорткодов.
   * @return string исходную строку с результатами выполнения хуков для шорткодов.
   */
  public static function doShortcode($content) {

     $shortCodes = self::getListShortCode();
     if (empty($shortCodes)){
       return $content;
     }

    // Получает шаблон для поиска шорткодов.
    $pattern = self::getShortcodeRegex();

    return preg_replace_callback( "/$pattern/s", array (__CLASS__, 'doShortcodeTag'), $content );
  }

  /**
    * Возвращает шаблон для поиска по регулярному выражению.
    * Регулярное выражение содержит 6 различных частей,
    *  для обеспечения разбора контента, которые ищут:
    * 1 - Открывающую скобку [ исключая вложения их друг в друга [[]];
    * 2 - Название шорткода;
    * 3 - Список аргументов;
    * 4 - Закрывающий слешь /;
    * 5 - Содержимое шорткода, между тегами;
    * 6 - Закрывающую скобку [ исключая вложения их друг в друга [[]];
    *
    * @return string регулярное выражение для поиска шорткода.
    */
   public static function getShortcodeRegex() {

     $tagnames = self::getListShortCode();
     $tagregexp = join( '|', array_map('preg_quote', $tagnames) );

     // ВНИМАНИЕ! Не используйте это
     // выражение без методов do_shortcode_tag() и strip_shortcode_tag()
     return
         '\\['                              // Открывающая скобка
       . '(\\[?)'                           // 1: Дополнительная проверка на вложенность: [[tag]]
       . "($tagregexp)"                     // 2: Имя тега
       . '\\b'                              // Слово - граница
       . '('                                // 3: Проверка внутри открыторого тега
       .     '[^\\]\\/]*'                   //    - не закрывающий слеш или скобка
       .     '(?:'
       .         '\\/(?!\\])'               // нет последовательности - /]
       .         '[^\\]\\/]*'               // нет закрывающей скобки либо слеша
       .     ')*?'
       . ')'
       . '(?:'
       .     '(\\/)'                        // 4: Текущий тег - закрывающий ...
       .     '\\]'                          // ... и закрывающая скобка
       . '|'
       .     '\\]'                          // Закрывающая скобка
       .     '(?:'
       .         '('                        // 5: Содержимое между тегами [shotcode]$content[shotcode/]
       .             '[^\\[]*+'             // Нет открывающей скобки
       .             '(?:'
       .                 '\\[(?!\\/\\2\\])' // нет последовательности  закрывающий слеш со скобкой
       .                 '[^\\[]*+'         // нет открывающейся скобки
       .             ')*+'
       .         ')'
       .         '\\[\\/\\2\\]'             // Закрывающий тег кода
       .     ')?'
       . ')'
       . '(\\]?)';                          // 6: исключает вложение[[tag]]
   }

   /**
    * Проверка разобраных частей для передачи в обработчик хука
    * @param array $m массив полученный регулярным выражением
    */
   public static function doShortcodeTag( $m ) {

     if ( $m[1] == '[' && $m[6] == ']' ) {
       return substr($m[0], 1, -1);
     }

     $tag = $m[2];
     $attr = self::shortcodeParseAtts( $m[3] );


     // если между тегами есть содержимое, до записываем его в аргументы с ключем content
    if ( !empty( $m[5] ) ) {
      $attr['content'] = $m[5];
    }

    return self::createHook('shortcode_'.$tag, $attr, true);
   }

   /**
    * Возвращает список атрибутов внутри шорткода
    * в виде массива пар - {ключ:значение}
    * @param string строка
    * @return array массив атрибутов со значениями.
    */
   public static function shortcodeParseAtts($text) {
     $atts = array();
     $pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
     $text = preg_replace("/[\x{00a0}\x{200b}]+/u", " ", $text);
     if ( preg_match_all($pattern, $text, $match, PREG_SET_ORDER) ) {
       foreach ($match as $m) {
         if (!empty($m[1]))
           $atts[strtolower($m[1])] = stripcslashes($m[2]);
         elseif (!empty($m[3]))
           $atts[strtolower($m[3])] = stripcslashes($m[4]);
         elseif (!empty($m[5]))
           $atts[strtolower($m[5])] = stripcslashes($m[6]);
         elseif (isset($m[7]) and strlen($m[7]))
           $atts[] = stripcslashes($m[7]);
         elseif (isset($m[8]))
           $atts[] = stripcslashes($m[8]);
       }
     } else {
       $atts = ltrim($text);
     }
     return $atts;
   }

   /**
    * Очищает контент от всех шорткодов
    *
    * @param string $content строка с шорткодами.
    * @return string исходная строка уже без шорткодов.
    */
   function stripShortcodes( $content ) {
     $shortCodes = self::getListShortCode();
     if (empty($shortCodes)){
       return $content;
     }


     // Получает шаблон для поиска шорткодов.
     $pattern = self::getShortcodeRegex();

     return preg_replace_callback( "/$pattern/s", array (__CLASS__, 'stripShortcodeTag'), $content );
   }

   function stripShortcodeTag( $m ) {
     if ( $m[1] == '[' && $m[6] == ']' ) {
      return substr($m[0], 1, -1);
     }

     return $m[1] . $m[6];
   }

}
