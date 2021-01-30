<?php

/**
 * Класс MG - предназначен для  доступа к функционалу системы,
 * из любой точки программы.
 * Реализован в виде синглтона, что исключает его дублирование.
 * Имеет в себе реестр для хранения любых объектов.
 */
class MG {

  static private $_instance = null;
  private $_registry = array();


  private function __construct() {

    // Старт сессии
    session_start();

    // Включение пользовательского шаблона
    self::enableTemplate();
    define('SITE', 'http://'.$_SERVER['SERVER_NAME'].URL::getCutSection());

    /**
     * Резервируем переменную staticPage в реестре ,
     * в ней будет хранится массив соответствий.
     * [название таблицы] => [поле содержащей статические страницы]
     * В такой таблице обязательно должнно присутствовать поле url и title.
     * Это требуется для расширения источников статического контента (новости, блог...
     * )
     */
    $this->_registry['staticPage'] = array('page' => 'html_content');
    // инициализация библиотеки для работы с категориями.
    // далее в любом  месте движка можно будет работать с категориями через реестр.
    $category = new Category();
    $this->_registry['category'] = $category;
  }


  private function __clone() {

  }


  private function __wakeup() {

  }


  /**
   * Метод addAction Добавляет обработчик для заданного хука.
   * пример 1:
   * <code>
   * //Произвольная пользовательская функция в одном из плагинов
   * function userfunc($color, $text){
   *   echo '<span style = "color:'.$color.'">'.$text.'</span>';
   * }
   *
   * // на хук с именем 'printHeader'
   * // вешается обработчик в виде пользовательской функция 'userPrintHeader'
   * // функция ждет два параметра, поэтому хук должен их задавать
   * MG::addAction('printHeader', 'userfunc', 2);
   *
   * // как должен выглядеть хук
   * MG::createHook('printHeader', 'gray', 'text');
   * </code>
   *
   * Варианты вызова данного метода.
   * 1. MG::addAction([имя хука], [имя пользовательской функции]) - назначает пользовательскую функцию в качестве обработчика для хука.
   * 2. MG::addAction([имя хука], [имя пользовательской функции], [количество параметров для пользовательской функции]) - назначает пользовательскую функцию в качестве обработчика для хука, при этом указывается какое количество параметров функция ожидает от хука.
   * 3. MG::addAction([имя хука], [имя пользовательской функции], [количество параметров для пользовательской функции], [приоритет выполнения]) - назначает пользовательскую функцию в качестве обработчика для хука, при этом указывается какое количество параметров функция ожидает от хука и какой приоритет ее выполнения.
   *
   * @param  $hookName имя хука на который вешается обработчик.
   * @param  $userFunction пользовательская функци, которая сработает при объявлении хука.
   * @param  $countArg количество аргументов, которое ждет пользовательская функция.
   */
  public static function addAction($hookName, $userFunction, $countArg = 0, $priority = 10) {
    PM::registration(new EventHook($hookName, $userFunction, $countArg, $priority * 10));
  }

  /**
   * Создает shortcode и определяет пользовательскую функцию для его обработки.
   * @param $hookName - название шорткода.
   * @param $userFunction - название пользовательской функции, обработчика.
   */
  public static function addShortcode($hookName, $userFunction, $priority = 10) {
    $hookName = "shortcode_".$hookName;
    self::addAction($hookName, $userFunction, 1, $priority * 10);
  }

  /**
   * Добавляет обработчик для страницы плагина.
   * Назначенная в качестве обработчика пользовательская функция.
   * будет, отрисовывать страницу настроек плагина.
   *
   * @param  $plugin название папки в которой лежит плагин.
   * @param  $userFunction пользовательская функци,
   *         которая сработает при открытии страницы настроек данного плагина.
   */
  public static function pageThisPlugin($plugin, $userFunction) {
    self::addAction($plugin, $userFunction);
  }


  /**
   * Добавляет обработчик для активации плагина,
   * пользовательская функция будет срабатывать тогда когда
   * в панели администрирования будет активирован плагин.
   *
   * >Является не обязательным атрибутом плагина, при отсутствии этого
   * обработчика плагин тоже будет работать.
   *
   * Функция обрабатывающя событие
   * не должна производить вывод (echo, print, print_r, var_dump), это нарушит
   * логику работы AJAX.
   *
   * @param  $dirPlugin директория в которой хранится плагин.
   * @param  $userFunction пользовательская функци, которая сработает при объявлении хука.
   */
  public static function activateThisPlugin($dirPlugin, $userFunction) {
    $dirPlugin = PM::getFolderPlugin($dirPlugin);
    $hookName = "activate_".$dirPlugin;
    PM::registration(new EventHook($hookName, $userFunction));
  }


  /**
   * Добавляет обработчик для ДЕактивации плагина,
   * пользовательская функция будет срабатывать тогда когда
   * в панели администрирования будет выключен  плагин.
   *
   * >Является не обязательным атрибутом плагина, при отсутствии этого
   * обработчика плагин тоже будет работать.
   *
   * Функция обрабатывающя событие
   * не должна производить вывод (echo, print, print_r, var_dump), это нарушит
   * логику работы AJAX.
   *
   * @param  $dirPlugin директория в которой хранится плагин.
   * @param  $userFunction пользовательская функци, которая сработает при объявлении хука.
   */
  public static function deactivateThisPlugin($dirPlugin, $userFunction) {
    $dirPlugin = PM::getFolderPlugin($dirPlugin);
    $hookName = "deactivate_".$dirPlugin;
    PM::registration(new EventHook($hookName, $userFunction));
  }


  /**
   * Создает hook -  крючок, для  пользовательских функций и плагинов.
   * может быть вызван несколькими спообами:
   * 1. createHook('userFunction'); - в любом месте программы выполнится пользовательская функция userFunction() из плагина;
   * 2. createHook('userFunction', $args); - в любом месте программы выполнится пользовательская функция userFunction($args) из плагина с параметрами;
   * 3. return createHook('thisFunctionInUserEnviroment', $result, $args); - хук прописывается перед.
   *  возвращением результата какой либо функции,
   *  в качестве параметров передается результат работы текущей функции,
   *  и начальные параметры которые были переданы ей.
   *
   * @param array $arr параметры, которые надо защитить.
   * @return array $arr теже параметры, но уже безопасные.
   */
  public static function createHook($hookName) {

    // Вариант 1. createHook('userFunction');
    $arg = array();
    $result = false;

    // Вариант 2. createHook('userFunction', $args);
    //  Не удалять, он работает.
    //  Для случая:
    //    createHook(__CLASS__."_".__FUNCTION__, $title);
    //    mgAddAction('mg_titlepage', 'myTitle', 1);
    if (func_num_args() == 2) {
      $arg = func_get_args();
      $arg = $arg[1];
    }

    // Вариант 3. return createHook('thisFunctionInUserEnviroment', $result, $args);
    if (func_num_args() == 3) {
      $arg = func_get_args();
      $result = isset($arg[1]) ? true : false;
      if ($result) {
        $argumets = array(
          'result' => $arg[1],
          'args' => $arg[2]
        );
        $arg = $argumets;
      }
    }

    if ($result) {
      return PM::createHook($hookName, $arg, $result);
    }

    PM::createHook($hookName, $arg, $result);
  }


  /**
   * Создает хук activate_$folderName при активации заданного плагина.
   * Предварительно подключает index.php активируемого плагина,
   * для того, чтобы зарегистрировать его обработчики.
   * @param $folderName - название папки содержащей плагин.
   */
  public static function createActivationHook($folderName) {
    //подключает функции плагина
    PM::includePluginInFolder($folderName);
    $hookName = "activate_".$folderName;
    self::createHook($hookName);
  }


  /**
   * Создает хук deactivate_$folderName при активации заданного плагина.
   * Предварительно подключает index.php активируемого плагина,
   * для того, чтобы зарегистрировать его обработчики.
   * @param $folderName - название папки содержащей плагин.
   */
  public static function createDeactivationHook($folderName) {
    PM::includePluginInFolder($folderName);
    $hookName = "deactivate_".$folderName;
    self::createHook($hookName);
  }


  /**
   * Защита от XSS атак полученный массив параметров.
   *
   * @param array $arr параметры, которые надо защитить.
   * @return array $arr теже параметры, но уже безопасные.
   */
  public static function defenderXss($arr) {
    $filter = array('<', '>');

    foreach ($arr as $num => $xss) {
      $arr[$num] = str_replace($filter, '|', trim($xss));
    }

    return $arr;
  }


  /**
   * Отключает вывод элементов шаблона. Нужен при работе с AJAX.
   */
  public static function disableTemplate() {
    $_SESSION['noTemplate'] = true;
  }


  /**
   * Включает вывод элементов шаблона. Весь контент будет
   * выводиться внутри пользовательской темы оформления.
   */
  public static function enableTemplate() {
    $_SESSION['noTemplate'] = false;
  }


  /**
   * Возвращает переменную из реестра.
   * @param $key - имя перменной.
   */
  static public function get($key) {
    return self::getInstance()->_registry[$key];
  }


  /**
   * Возвращает буфер, который содержит весь,
   * полученый в ходе работы движка, контент.
   * @param $include - путь для полкючаемого файла (вид или пользовательский файл).
   * @param $html - флаг, вывода html контента.
   * @param $variables - массив переменных, которые должны быть доступны в файле вида.
   */
  public static function getBuffer($include, $html = false, $variables = false) {

    if (!empty($variables)) {
      extract($variables);
    }
    ob_start();

    if ($html) {
      // выводим контент, предварительно заменив все шорткоды, результатами их обработки.
      echo $include;
    } else {
      // не подключается вид если view = _NONE_, например, открывается страница плагина.
      if ($include != '_NONE_') {
        include $include;
      }
    }

    // делаем доступным массив $data в шаблоне.
    extract(self::templateData());

    self::templateFooter($data);

    $buffer = ob_get_contents();
    ob_end_clean();

    self::templateHeader($data);
    $args = func_get_args();
    return self::createHook(__CLASS__."_".__FUNCTION__, $buffer, $args);
  }


  /**
   * Получает настройки для доступа к БД, из конфигурационного файла config.ini.
   * @return boolean.
   */
  public static function getConfigIni() {
    if (file_exists('config.ini')) {
      $config = parse_ini_file('config.ini', true);
      define('HOST', $config['DB']['HOST']);
      define('USER', $config['DB']['USER']);
      define('PASSWORD', $config['DB']['PASSWORD']);
      define('NAME_BD', $config['DB']['NAME_BD']);
      return true;
    }
    return false;
  }


  /**
   * Получает контент статической HTML страницы из БД.
   * @todo Сделать поиск в статическом коде меток [имя_метки параметр1 параметр...]
   * @return string|boolean - возвращает либо HTML либо false.
   */
  public static function getHtmlContent() {
    $result = false;
    $arrayStaticPage = self::get('staticPage');
    foreach ($arrayStaticPage as $table => $content) {
      $res = DB::query('
      SELECT  *
      FROM '.$table.'
      WHERE url="'.URL::getRoute().'.html"
    ');

      if ($html = DB::fetchArray($res)) {

        $result = $html[$content];
        self::titlePage($html['title']);

      }
    }

    $args = func_get_args();
    return self::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }


  /**
   * добавляеn в реестр движка информацию,
   * о новой таблице, в которой можно искать статический контент.
   * @param string $table наименование новой таблицы.
   * @param string $table наименование поля в таблице с контентом.
   */
  public static function newTableContent($table, $contentField) {
    $newTablePage = MG::get('staticPage');
    $newTablePage[$table] = $contentField;
    self::set('staticPage', $newTablePage);
  }


  /**
   * Возвращет единственный экземпляр данного класса.
   * @return object - объект класса MG.
   */
  static public function getInstance() {
    if (is_null(self::$_instance)) {
      self::$_instance = new self;
    }
    return self::$_instance;
  }


  /**
   * Получить меню в HTML виде.
   * @return object - объект класса Menu.
   */
  public static function getMenu() {
    return Menu::getMenu();
  }


  /**
   * Получить путь до пользовательского файла, создающего контент страницы.
   * Файл должен находиться в папке mg-pages.
   * @return string - путь к php файлу.
   */
  public static function getPhpContent() {
    $result = false;

    if (file_exists(PAGE_DIR.URL::getRoute().'.php')) {
      $result = PAGE_DIR.URL::getRoute().'.php';
    }

    $args = func_get_args();
    return self::createHook(__CLASS__."_".__FUNCTION__, $result, $args);
  }


  /**
   * Получить параметры маленькой корзины.
   * @return object - объект класса SmalCart.
   */
  public static function getSmalCart() {
    return SmalCart::getCartData();
  }


  /**
   * Инициализация настроек сайта из таблицы settings в БД
   * Записывает в реестр все настройки из таблицы,
   * в последствии к ним осуществляется доступ из любой точки программы
   * @todo - скореее всего перепишем, а возмоно просто удалим, т.к. теперь есть опции.
   * @return void
   */
  public static function init() {

    // Бессмысленное создание переменной в регистре. Если удалить, рушится верстка. (Баг)
    self::set('mgInit', true);

    /**
     * Подключает файл адаптирующий вызовы статических методов, в обыкновенные функции.
     */
    require_once 'metodadapter.php';

    // Определяет константу PATH_TEMPLATE с путем до шаблона сайта.
    self::setDifinePathTemplate(self::getOption('templateName'));

    // Подключает файл с функциями шаблона, если таковой существует.
    if (file_exists(PATH_TEMPLATE.'/functions.php')) {
      require_once PATH_TEMPLATE.'/functions.php';
    }
  }


  /**
   * Запускает инсталятор CMS.
   * @return void
   */
  public static function instalMoguta() {

    if (file_exists('install/install.php')) {
      require_once 'install/install.php';
      exit;
    } else {
      echo '<span>ВНИМАНИЕ!! Файл конфигурации недоступен!!
              Повторите процедуру инсталяции</span>';
      exit;
    }
  }


  /**
   * Функция Downtime (временное отключение работоспособности сайта).
   * @return boolean
   */
  public static function isDowntime() {
    $route = URL::getRoute();
    if ('mgadmin' != $route &&
      'ajax' != $route &&
      'enter' != $route &&
      'Y' == self::getOption('downtime')) {
      return true;
    }
    return false;
  }


  /**
   * Полезная при отладке функция, создает лог в корне сайта.
   * @param string $text текст лога.
   * @param string $mode режим записи.
   * @return void
   */
  public static function loger($text, $mode = 'a+') {
    $date = date('Y_m_d');
    $fileName = 'log_'.$date.'.txt';
    $string = date('d.m.Y H:i:s').' =>'.$text."\r\n";
    $f = fopen($fileName, $mode);
    fwrite($f, $string);
    fclose($f);
  }


  /**
   * Возвращает созданую движком HTML страницу, для вывода на экран.
   * Имеет четыре типа вывода:
   * - представление из MVC;
   * - пользовательский php Файл;
   * - статическая HTML страница из БД;
   * - страница 404 ошибки, из пользовательского шаблона.
   *
   * @param mixed $data - массив с данными для вывода контента.
   * @return string - сгенерированный HTML код.
   */
  public static function printGui($data) {

    switch ($data['type']) {
      case 'view': {
          return self::getBuffer($data['view'], false, $data['variables']);
        }
      case 'php': {
          return self::getBuffer($data['data'], false);
        }
      case 'html': {
          return self::getBuffer($data['data'], true);
        }
      case '404': {
          header('HTTP/1.0 404 Not Found');
          self::titlePage('Ошибка 404');
          $path404 = PATH_TEMPLATE.'/404.php';

          if (!file_exists($path404)) {
            $path404 = 'mg-templates/.default/404.php';
          }
          return self::getBuffer($path404);
        }
    }

    return false;
  }


  /**
   * Устанавливает meta данные страницы.
   * @param string|bool $title заголовок страницы.
   * @return void.
   */
  public static function meta() {
    $title = self::getOption('title');
    $meta = '
      <!--Заголовки определенные движком-->
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <meta content="доставка, товары, продукты, еда, на дом, в томске, быстро, дешево" name="keywords">
      <meta content="Самая быстрая доставка любых продуктов и товаров в Томске! Купить продукты по самой выгодной цене!" name="description">
      <title>'.$title.'</title>
      <link rel="stylesheet" href="'.PATH_SITE_TEMPLATE.'/css/style.css" type="text/css" />';
      if(USER::getThis()->role == 1  || 12 == User::getThis()->role){
        $meta .= '
        <link rel="stylesheet" href="'.SITE.'/mg-admin/design/style-adminbar.css" type="text/css" />
        <script type="text/javascript" src="'.SITE.'/mg-core/script/jquery-1.7.2.min.js"></script>
      ';
      }
      $meta .= '<!--/Заголовки определенные движком-->';

    $args = func_get_args();
    return self::createHook(__CLASS__."_".__FUNCTION__, $meta, $args);
  }


  /**
   * Создает переменную в реестре, в последствии доступна из любой точки программы
   * @param $key - имя перменной.
   * @param $object - значение переменной.
   */
  static public function set($key, $object) {
    self::getInstance()->_registry[$key] = $object;
  }


  /**
   * Устанавливает константу пути, до папки с шаблоном.
   * @param $template - папка с шаблоном в mg-templates/.
   */
  public static function setDifinePathTemplate($template = '.default') {
    $pathTemplate = 'mg-templates/'.$template;
    $path = $pathTemplate.'/css/style.css';

    if (!file_exists($path)) {
      $pathTemplate = 'mg-templates/.default';
    }
    define('PATH_TEMPLATE', $pathTemplate);
    define('PATH_SITE_TEMPLATE', SITE.'/'.$pathTemplate);

    // Дописываем в includePath путь до шаблона,
    // для того чтобы дать влзможность изменять логику MVC локально, не трогая ядро.
    // Теперь  модель, вид и контролер в первую очередь
    // будут браться из пользовательского шаблона, при условии что они существуют.
    // *папки views, models и controlers - могут вовсе отсутствовать в шаблоне.
    set_include_path(PATH_TEMPLATE."/".PATH_SEPARATOR.get_include_path());

  }


  /**
   * Собирает массив данных, доступных в последствии из шаблоне через масиив $data.
   * @return void
   */
  public static function templateData() {
    $cart = self::getSmalCart();
    $data = array(
      'data' => array(
        'cartCount' => $cart['cart_count'],
        'cartPrice' => $cart['cart_price'],
        'categoryList' => self::get('category')->getCategoryListUl(),
        'breadcrumbs' => Breadcrumbs::getBreadcrumbs(),
        'menu' => self::getMenu(),
        'thisUser' => User::getThis()
      )
    );

    return $data;
  }


  /**
   * Подключает пользовательский подвал сайта из выбранного шаблона.
   * Если футер в текущем шаблоне отсутствует поставляется стандартный код из шаблона .default;
   * @todo - смутный функционал с подключением дефолтного футера.
   * @return void
   */
  public static function templateFooter($data = null) {
    $footerPath = PATH_TEMPLATE.'/footer.php';

    if (!file_exists($footerPath)) {
      $footerPath = 'mg-templates/.default/footer.php';
    }

    if (!$_SESSION['noTemplate']) {
      require_once $footerPath;
    }
  }


  /**
   * Подключает пользовательскую шапку сайта из темы.
   * Если шапки в текущем шаблоне поставляется стандартный код из шаблона .default;
   * @todo - смутный функционал с подключением дефолтного футера.
   * @return void
   */
  public static function templateHeader($data = null) {
    $headerPath = PATH_TEMPLATE.'/header.php';

    if (!file_exists($headerPath)) {
      $headerPath = 'mg-templates/.default/header.php';
    }

    if (!$_SESSION['noTemplate']) {

      // Подключение админ панели.
      if (1 == User::getThis()->role || 12 == User::getThis()->role) {
        require_once ADMIN_DIR.'/adminbar.php';
      }

      // Подключение файла шапки.
      require_once $headerPath;
    }
  }


  /**
   * Задает заголовок страницы.
   * @return void
   */
  public static function titlePage($title) {
    self::setOption('title', $title);

    // Инициализирует событие mg_titlePage.
    self::createHook(__CLASS__."_".__FUNCTION__);
    // Чтобы обработать его пользховательской функцией нужно добавить обработчик:
    // mgAddAction('mg_titlepage', 'userFunctionName');
  }


  /**
   * Перевдит кирилицу в латиницу.
   * @param string $str переводимая строка.
   * @return string
   */
  public static function translitIt($str) {
    $tr = array(
      'А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'g',
      'Д' => 'd', 'Е' => 'e', 'Ж' => 'j', 'З' => 'z', 'И' => 'i',
      'Й' => 'y', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n',
      'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't',
      'У' => 'u', 'Ф' => 'f', 'Х' => 'h', 'Ц' => 'ts', 'Ч' => 'ch',
      'Ш' => 'sh', 'Щ' => 'sch', 'Ъ' => '', 'Ы' => 'yi', 'Ь' => '',
      'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya', 'а' => 'a', 'б' => 'b',
      'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ж' => 'j',
      'з' => 'z', 'и' => 'i', 'й' => 'y', 'к' => 'k', 'л' => 'l',
      'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
      'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h',
      'ц' => 'ts', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => 'y',
      'ы' => 'yi', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
      ' ' => '_', '.' => '', '/' => '_', '1' => '1', '2' => '2',
      '3' => '3', '4' => '4', '5' => '5',
      '6' => '6', '7' => '7', '8' => '8', '9' => '9', '0' => '0');
    return strtr($str, $tr);
  }


  /**
   * Перенаправляет на другую страницу сайта.
   * @return void
   */
  public static function redirect($location, $redirect = '') {
    header('Location: '.$redirect.' '.SITE.$location);
    exit;
  }


  /**
   * Устанавливает значение для опции (настройки).
   * @param array $data -  может содержать значения для полей таблицы.
   * <code>
   * $data = array(
   *   option => 'идентификатор опции например: sitename'
   *   value  => 'значение опции например: moguta.ru'
   *   active => 'в будущем будет отвечать за автоподгрузку опций в кеш Y/N'
   *   name => 'Метка для опции например: Имя сайта'
   *   desc => 'Описание опции: Настройа задает имя для сайта'
   * )
   * </code>
   * @return void
   */
  public static function setOption($data) {
    // Если функция вызвана вот так: setOption('option', 'value');
    if (func_num_args() == 2) {
      $arg = func_get_args();
      $data = array();
      $data['option'] = $arg[0];
      $data['value'] = $arg[1];
    }

    $result = DB::query("
    SELECT *
    FROM setting
    WHERE `option` = '%s'
    ", $data['option']
    );

    if (!DB::numRows($result)) {
      $result = DB::query("
      INSERT INTO setting
      VALUES ('','%s','','N','','')"
          , $data['option']);
    }

    $result = DB::query("
    UPDATE setting
    SET ".DB::buildPartQuery($data)."
    WHERE `option` = '%s'
    ", $data['option']
    );
  }


  /**
   * Возвращает значение для запрошенной опции (настройки).
   * имеет два режима:
   * 1. getOption('optionName') - вернет только значени;
   * 2. getOption('optionName' , true) - вернет всю информацию об опции в
   * виде массива.
   * <code>
   * $data = array(
   *   option => 'идентификатор опции например: sitename'
   *   value  => 'значение опции например: moguta.ru'
   *   active => 'в будущем будет отвечать за автоподгрузку опций в кеш Y/N'
   *   name => 'Метка для опции например: Имя сайта'
   *   desc => 'Описание опции: Настройа задает имя для сайта'
   * )
   * </code>
   * @return void
   */
  public static function getOption($option, $data = false) {

    // Если функция вызвана вот так: getOption('option', true);
    if ($data) {
      $result = DB::query("
      SELECT *
      FROM setting
      WHERE `option` = '%s'
      ", $option
      );
      if ($option = DB::fetchAssoc($result)) {
        return $option;
      }
    }

    $result = DB::query("
      SELECT value
      FROM setting
      WHERE `option` = '%s'
    ", $option
    );

    if ($option = DB::fetchAssoc($result)) {
      return $option['value'];
    }
  }

}

