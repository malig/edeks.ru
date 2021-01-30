<?php
/**
 * Контроллер: Ajax
 *
 * Класс Controllers_Ajax обрабатывает все AJAX запросы.
 * - Отключает вывод шаблона;
 * - Передает запрос в библиотеку Actionet.
 *
 */
class Controllers_Ajax extends BaseController{

  function __construct(){
    //Защита контролера от несанкционированного доступа вне админки
    if(!$this->checkAccess(User::getThis()->role)){
      return false;
    };

    MG::disableTemplate(); // Отключаем  вывод темы
    //если этот аякс запрос направлен на выполнение
    // действия с БД то пытаемся их выполнить
    // иначе подключается контролер из админки

    $url = URL::getQueryParametr('url');
    $type = URL::getQueryParametr('type');

    // если передана переменная $pluginFolder, то вся обработка
    // перекладывается на плечи стороннего плагина из этой папки
    $pluginHandler = URL::getQueryParametr('pluginHandler');


    if(empty($pluginHandler)){
      if(!$this->routeAction($url)){

        if('plugin' == $type){
          //MG::loger(print_r($_REQUEST, true));
          if(!empty($_POST['request'])){
            $_POST = $_POST['request'];
          }

          //require_once ADMIN_DIR.'section/views/plugintemplate.php';

          URL::setQueryParametr('view', ADMIN_DIR.'section/views/plugintemplate.php');
        }else{
          require_once ADMIN_DIR.'section/controlers/'.$url;
          URL::setQueryParametr('view', ADMIN_DIR.'section/views/'.$url);
        }
      }
    } else
    {

      //обработкой действия займется плагин папка которого передана в $pluginHandler
     $actioner = URL::getQueryParametr('actionerClass');
     // запускаем маршрутизатор действий
     $this->routeAction($actioner, $pluginHandler);
    }
  }

  /**
   * Если действие запрошенно стандартными  файлами движка, то
   * маршрутизирует действие в класс Actioner для дальнейшего выполнения.
   *
   * Если действие запрошено из страницы плгина, то передает действие в
   * пользовательский клас плагина. Класс плагина передается
   * в переменной  URL::getQueryParametr('action')
   *
   * @param string $url ссылка на действие
   * @param string $plugin папка с плагином
   */
  public function routeAction($url, $plugin = null){
    // если не плагин
    if(!$plugin ){
      $parts = explode('/', $url);
      if($parts[0] == 'action'){
        $act = new Actioner();
        $act->runAction($parts[1]);
        return true;
      }
    } else {

      // подключам пользовательский класс для обаботки
      $actioner = $url;

      // формируем путь до класса плагина, который обработает действие
      $pathPluginActioner = PLUGIN_DIR.$plugin."/".mb_strtolower($url).'.php';

      // подключаем класс плагина
      include  $pathPluginActioner;

      // создаем экземпля класа обработчика
      // (он обязательно должен наследоваться от стандартноко класса Actioner)
      $act = new $actioner();

      // выполняем стандартный метод класса Actioner
      $act->runAction(URL::getQueryParametr('action'));
      return true;
    }

    return false;
  }

  /**
   * Проверяет наличие прав администратора, на доступ к этому контролеру.
   * Защищает его от прямых ссылок таких как ajax?url=action/editProduct
   *
   * @param boolean $role флаг прав администратора
   * @return boolean
   */
  public function checkAccess($role){
    if(!$role){
      header('HTTP/1.0 404 Not Found');
      URL::setQueryParametr('view', PATH_TEMPLATE.'/404.php');
      return false;
    }
    return true;
  }
}