<?php

/**
 * Класс Moguta - запускает движок и выполняет роль маршрутизатора, определяет контролер и представление.
 * Если не находит контролер, то подбирает другой доступный вариант
 * вывода информации, такой как вывод страницы из папки mg-pages/ или получение HTML из базы сайта.
 */
class Moguta {


  // Конструктор запускает маршрутизатор и получает запрашиваемый путь.
  public function __construct() {
    $this->getRoute();
  }


  /**
   * Запускает движок системы.
   *
   * @return array массив с результатами содержащий:
   * -тип файла который нажо открыть
   * -данные для этого файла
   * -вид
   */
  public function run() {

    // Если найден контролер.
    if ($controller = $this->getController()) {
      $contr = new $controller;
      $type = 'view';
      $variables = $contr->variables;
      $view = $this->getView();
    } elseif ($data = MG::getPhpContent()) {

      // Если найден пользовательский php файл.
      $type = 'php';
    } elseif ($data = MG::getHtmlContent()) {

      // Если найден статический контент в БД.
      $type = 'html';
    }

    // Если не существует запрашиваемых данных.
    $type = $type ? $type : '404';
    $result = array(
      'type' => $type,
      'data' => $data,
      'view' => $view,
      'variables' => $variables
    );
    return $result;
  }


  /**
   * Проверяет, может ли контролер 'Catalog' обработать ЧПУ ссылку.
   * Если ссылка действительно запрашивает какую-то существующую категорию,
   * то метод возвращает в качестве названия контролера строку "catalog".
   * В противном случае именем контролера считается последняя секция в ссылке.
   *
   * @return string название контролера.
   */
  public function convertCpuCatalog() {
    $result = DB::query("
      SELECT  url as category_url, id
      FROM category
      WHERE url = '%s'
    ", $this->route);

    if ($obj = DB::fetchObject($result)) {
      URL::setQueryParametr('category_id', $obj->id);
      return 'catalog';
    }

    return URL::getRoute();
  }


  /**
   * Проверяет, может ли контролер 'Product' обработать ЧПУ ссылку.
   * Если ссылка действительно запрашивает какой-то существующий продукт
   * в имеющейся категории, то метод возвращает в качестве названия контролера строку "product".
   * В противном случае метод считает, что именем контролера должена являться
   * последняя секция в ссылке.
   *
   * @return string - имя контролера.
   */
  public function convertCpuProduct() {

    // Изначально контролером будет последняя часть URI.
    $arraySections = URL::getSections();

    // Получает id продукта по заданной секции.
    $sql = '
      SELECT  c.url as category_url, p.url as product_url, p.id
      FROM product p
      LEFT JOIN category c
        ON c.id=p.cat_id
      WHERE p.url like "%s"
    ';

    $result = DB::query($sql, URL::getRoute());

    if ($obj = DB::fetchObject($result)) {

      // Для товаров без категорий формируется ссылка [site]/vse/[product].
      $obj->category_url = ($obj->category_url !== NULL) ? $obj->category_url : 'vse';

      if ($arraySections[1] == $obj->category_url) {
        URL::setQueryParametr('id', $obj->id);
        return 'product';
      }
    }
    return URL::getRoute();
  }


  /**
   * Получает название класса контролера, который будет обрабатывать текущий запрос.
   * @return string название класса нужного контролера.
   */
  private function getController() {
    if (file_exists(CORE_DIR.'controllers/'.$this->route.'.php')) {
      return 'controllers_'.$this->route;
    }
    return false;
  }


  /**
   * Получает маршрут исходя из URL.
   * Интерпретирует ЧПУ ссылку в понятную движку форму.
   *
   * @return string возвращает полученный маршрут.
   */
  private function getRoute() {
    $this->route = URL::getRoute();

    if (empty($this->route)) {
      $this->route = 'index';
      return $this->route;
    }


    /**
     * По умолчанию движок поддерживает ЧПУ только для каталога и карточки товара, 
     * поэтому проверяем не адресован ли запрос к контролерам catalog или product.
     * 
     * Если ссылка состоит из одной секции:
     * <code>/monitoryi<code>
     * То обработать ее может только контролер каталога 'Catalog', 
     * при условии что  найдет соответствующую категорию в 
     * таблице с полем URL содержащим /monitoryi.
     */
    if (URL::getCountSections() === 1) {
      $this->route = $this->convertCpuCatalog();
    } else {

      /**
       * Если ссылка состоит из двух секций:         
       * <code>/monitoryi/samsungSA700<code>
       * то попробуем обработать ее контролером 'product'.
       */
      $this->route = $this->convertCpuProduct();
    }

    /**
     * Если ссылка не может быть обработана
     * ни контролером 'Catalog',
     * ни контролером 'Product', то ищется контролер
     * по последней секции в ссылке
     * <code>/monitoryi/order<code>
     * в этом примере запрос обработает контролер 'Order', если он существует.
     */
    return $this->route;
  }


  /**
   * Получает путь до файла представления, который выведет 
   * на страницу полученные из контролера данные.
   * @return string путь до представление.
   */
  public function getView() {
    $route = $this->route;

    /** 
     * Если работал контролер аякса, то в реестре переменных должна
     * существовать переменная 'view' содержащая 
     * путь до представления в админке mg-admin/section/views/[название файла представления].php.       
     */
    $view = URL::get('view');

    // Если запрос не аяксовый, то представление будет 
    // взято из папки views/ шаблона сайта расположенного в PATH_TEMPLATE.
    // Также представление может находиться в папке ядра mg-core/views/.
    if (!$view) {
      $pathView = PATH_TEMPLATE.'/views/';
      $view = $pathView.$route.'.php';

      if (!file_exists($view)) {
        $view = 'views/'.$route.'.php';
        ;
      }
    }
    return $view;
  }

}