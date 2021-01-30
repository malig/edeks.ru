<?php
/**
 * Файл mg-start.php расположен в корне ядрая, запускает движок и выводит на экран сгенерированные им днные.
 *
 * Инициализирует компоненты CMS, доступные из любой точки программы.
 * - DB - класс для работы с БД;
 * - MG - класс содердащий функционал системы;
 * - URL - класс для работы со ссылками;
 * - PM - класс для работы с плагинами.
 *
 */

// Если config.ini не существует, происходит попытка запустить инсталятор.
if(!MG::getConfigIni()){
  MG::instalMoguta();
}

// Инициализация компонентов CMS.
DB::init();
PM::init();
MG::init();
URL::init();
User::init();
Mailer::init();


if(MG::isDowntime()){
 /**
  * Если сайт временно закрыт выводитя заглушка, хранящаяся в корне двика.
  */
  require_once 'downTime.html';
  exit;
}

// Подключить index.php всех плагинов.
PM::includePlugins();

// Хук выполняющийся до запуска движка.
MG::createHook('mg_start');

// Запуст движка.
$moguta = new Moguta;
$moguta = $moguta->run();

// Вывод результата на экран, предварительно обработав все возможные шорткоды.
echo PM::doShortcode(MG::printGui($moguta));

// Хук выполняющийся после того как отработал движок.
MG::createHook('mg_end');



