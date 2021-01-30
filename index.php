<?php
/**
 * Файл index.php расположен в корне CMS является единственной точкой инициализирующей работу системы.
 *
 * В этом файле:
 *  - настраивается вывод ошибок;
 *  - устанавлюиваются константы;
 *  - массивом $includePath задаются пути для поиска библиотек
 *  при подключении файлов движка.
 */

//Не выводить предупреждения и ошибки.
Error_Reporting(E_ALL & ~E_NOTICE);

// Установка кодировки для вывода контента.
header('Content-Type: text/html; charset=utf-8');
/**
 * Корневая папка сайта.
 */
define('SITE_DIR', $_SERVER['DOCUMENT_ROOT'].'/');

/**
 * Папка ядра.
 */
define('CORE_DIR', 'mg-core/');

/**
 * Папка библиотек.
 */
define('CORE_LIB', CORE_DIR.'lib/');

/**
 * Папка JS скриптов.
 */
define('CORE_JS', CORE_DIR.'script/');

/**
 * Папка админки.
 */
define('ADMIN_DIR', 'mg-admin/');

/**
 * Папка плагинов.
 */
define('PLUGIN_DIR', 'mg-plugins/'); // Папка плагинов.

/**
 * Папка пользовательских php страниц.
 */
define('PAGE_DIR', 'mg-pages/');

/**
 * Текущая версия.
 */
define('VER', 'v2.2.0');

// Установка путей, для поиска подключаемых библиотек.
$includePath = array(CORE_DIR,CORE_LIB);
set_include_path('.'.PATH_SEPARATOR.implode(PATH_SEPARATOR, $includePath));


/**
 * Автоматически подгружает запрошенные классы.
 * @param type $className наименование класса.
 * @return void
 */
function __autoload($className){
  $path = str_replace('_', '/', strtolower($className));
  return include_once $path.'.php';
}


/**
 * Подключает движок и запускает CMS.
 */
require_once ('mg-start.php');



