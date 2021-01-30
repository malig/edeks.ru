<?php
/**
 * Контроллер previewer
 *
 * Класс Controllers_Previewer показывает как будет выглядеть редактируемая страница. *
 */
class Controllers_Previewer extends BaseController{

  function __construct(){
    if(!USER::isAuth() || '1' != USER::getThis()->role || '12' != USER::getThis()->role){
		  MG::redirect('/');
	  }

    $this->data = array('content'=>$_POST['content']);
  }

}