<?php

  /**
   * Класс Updata -  класc занимается обновлением системы.
   *  - Проверяет наличие обновлений на сервере
   */
class Updata {

  // Cервер обновлениq 
  private static $updataServer = 'http://moguta.ru/updata/';

  /**
   * Проверяет на сервере актуальность текущей системы.
   *
   * @return  bool|string $msg сообщение с описанием последней версии.
   */
  public static function checkUpdata() {
    $timeLastUpdata = MG::getOption('timeLastUpdata');

    if (time() < $timeLastUpdata + 60/* * 60 * 24 */) { // интервал проверки обновления 1 сутки.
      $res = MG::getOption('currentVersion');
    } else {
      $updataServer = self::$updataServer.'jsonUpdata.php';
      $post = 'version='.VER;
      $res = self::_sendCURL($updataServer, $post);

      DB::query("
        UPDATE `setting`
          SET `value`='%s'
        WHERE `option`='currentVersion'
      ", $res);

      DB::query("
      UPDATE `setting`
        SET `value`='%s'
      WHERE `option`='timeLastUpdata'
      ", time());
    }

    $data = json_decode($res, true);

    if ($data['last']) {
      $msg = '
      <b>Последняя версия системы: </b><span id="fVer">'.$data['final'].'</span>.<br>
      <b>Ближайшая версия для обновления: </b><span id="lVer">'.$data['last'].'</span>.<br>
      <b>Описание: </b>'.$data['disc'].'<br>
      <b>Автор: </b>'.$data['author'];
      $args = func_get_args();
      return MG::createHook(__CLASS__."_".__FUNCTION__, $msg, $args);
    }

    return MG::createHook(__CLASS__."_".__FUNCTION__, false, $args);
  }


  /**
   * Обновляет текущую версию системы.
   * @param string $version - версия последнего обновления
   * @return bool
   */
  public static function updataSystem($version) {

    $file = $version.'-m.zip';

    if (!file_exists(SITE_DIR.$file)) {
      $ch = curl_init(self::$updataServer.'history/'.$version.'.zip');
      $fp = fopen($file, "w");

      curl_setopt($ch, CURLOPT_FILE, $fp);
      curl_setopt($ch, CURLOPT_HEADER, 0);

      curl_exec($ch);
      curl_close($ch);
      fclose($fp);
    }
    $args = func_get_args();
    $return = false;

    if (self::extractZip($file)) {
      $return = true;
    }
    return MG::createHook(__CLASS__."_".__FUNCTION__, $return, $args);
  }


  /**
   * Распаковывает архив с обновлением, если он есть в корне сайта. 
   * После распаковки удаляет заданый архив.
   * 
   * @param $file - название архива, который нужно распаковать
   * @return bool
   */
  public static function extractZip($file) {

    if (file_exists($file)) {
      $zip = new ZipArchive;
      $res = $zip->open($file, ZIPARCHIVE::CREATE);

      if ($res === TRUE) {
        $zip->extractTo(SITE_DIR);
        $zip->close();
        unlink($file);

        // выполняет некоторые действия, для адаптации старой версии БД.
        self::_updataSubInfo('modificatoryInc.php');
        return true;
      } else {
        return false;
      }
    }
    return false;
  }


  /**
   * Отправляет запрос на сервер, с целью получить данные о последней версии.
   *
   * @param string $url адрес сервера.
   * @param string $post  параметры для POST запроса.
   * @return string ответ сервера.
   */
  private static function _sendCURL($url, $post) {

    // Иницализация библиотеки curl.
    $ch = curl_init();

    // Устанавливает URL запроса.
    curl_setopt($ch, CURLOPT_URL, $url);

    // При значении true CURL включает в вывод заголовки.
    curl_setopt($ch, CURLOPT_HEADER, false);

    // Куда помещать результат выполнения запроса:
    //  false – в стандартный поток вывода,
    //  true – в виде возвращаемого значения функции curl_exec.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Нужно явно указать, что будет POST запрос.
    curl_setopt($ch, CURLOPT_POST, true);

    // Здесь передаются значения переменных.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

    // Максимальное время ожидания в секундах.
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);

    // Выполнение запроса.
    $res = curl_exec($ch);

    // Освобождение ресурса.
    @curl_close($ch);
    $args = func_get_args();
    return MG::createHook(__CLASS__."_".__FUNCTION__, $res, $args);
  }


  /**
   * Выполняет набор MySQL запросов для адаптации страрой версии БД к новому виду. 
   * Удаляет необходимые файлы при обновлении системы.   * 
   * Файл модификтаор содерсит массивы $sqlQuery и $deleteArray, в которых перечисленны
   * запросы к БД и пути к удаляемым файлам.
   * 
   * @param string $modificatoryFile имя файла модификатора.
   * @return boolean
   */
  private static function _updataSubInfo($modificatoryFile) {

    if (!file_exists($modificatoryFile)) {
      return false;
    }

    require_once $modificatoryFile;

    if (is_array($sqlQuery)) {
      foreach ($sqlQuery as $sql) {
        DB::query($sql);
      }
    }

    if (is_array($deleteArray)) {
      foreach ($deleteArray as $deletedfile) {
        if (file_exists($deletedfile)) {
          unlink($deletedfile);
        }
      }
    }
    unlink($modificatoryFile);
    return true;
  }

}