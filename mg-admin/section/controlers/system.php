<?php
 $downtime = MG::getOption('downtime');

 if('Y' == $downtime){
   $checked = 'checked';
 }

 $this->checked = $checked;

 //$newVersionMsg = Updata::checkUpdata();
 $this->newVersionMsg = $newVersionMsg.'<br>Перед обновлением рекомендуется сделать резервную копию базы данных, а также всех файлов сайта. <br/> <a href="#" rel="updataSystem" class="button">Обновить</a>';
 if(!$newVersionMsg){
   $this->newVersionMsg = 'У Вас последняя версия';
 }
 