function autoSize(image, size){
	var img = image,
	width = img.clientWidth,
	height = img.clientHeight;   
   
	if(width >= height && width > size){ 
		img.style.height = (height * size / width) + 'px';
		img.style.width = size +'px';
	}	 
	else if(height > size){        
		img.style.width = (size / height * width) + 'px';
		img.style.height = size + 'px';
	}	
}

$(document).ready(function(){

  // переменная поучает имя текущего хоста http://host/
  // Доступна во всех функциях админки.
  SITE = $.trim($("#thisHostName").html());

  //обработчики нажатий на ссылки в панеле
  $('a[id=product]').click(function(event){
  	event.preventDefault();
    show("catalog.php","adminpage");
    includeJS('../mg-core/script/admin/catalog.js');
    $('#msg_information span').html("Раздел \"<b>Товары</b>\" предназначен для создания и редактирования товаров каталога интернет магазина.<br/> Добавьте новый товар с помощью кнопки \"Добавить товар\"");
  });
  $('a[id=category]').click(function(event){
  	event.preventDefault();
    show("category.php","adminpage");
    includeJS('../mg-core/script/admin/category.js');
    $('#msg_information span').html("Раздел \"<b>Категории</b>\" предназначен для создания и редактирования списка категорий товаров.<br/> Добавьте категорию  спомощью кнопки \"Добавить категорию\".<br/>Для создания вложенных категорий и их редактирования кликните по названию нужной категории, и выберите нужный пункт в контекстном меню. ");
  });
  $('a[id=page]').click(function(event){
  	event.preventDefault();
    show("page.php","adminpage");
    includeJS('../mg-core/script/admin/pages.js');
    $('#msg_information span').html("Раздел \"<b>Страницы </b>\" предназначен для создания и редактирования статичных HTML страниц вашего сайта.<br/> После создания, страница будет доступна по ссылке http://[сайт]/[url страницы]");
  });
  $('a[id=settings]').click(function(event){
  	event.preventDefault();
    show("settings.php","adminpage");
    includeJS('../mg-core/script/admin/settings.js');
    $('#msg_information span').html("Раздел \"<b>Настройки </b>\" предназначен для задания параметров сайта, влияющих на его работоспособность.");
  });
  $('a[id=orders]').click(function(event){
  	event.preventDefault();
    show("orders.php","adminpage");
    includeJS('../mg-core/script/admin/orders.js');
    $('#msg_information span').html("Раздел \"<b>Заказы</b>\" предназначен для обработки поступивших заказов вашего интернет магазина.<br/> Чтобы увидеть содержимое заказа кликните на строку в таблице.");
  });
  $('a[id=users]').click(function(event){
  	event.preventDefault();
    show("users.php","adminpage");
    includeJS('../mg-core/script/admin/users.js');
    $('#msg_information span').html("Раздел \"<b>Пользователи</b>\" предназначен для редактирование пользователей.<br/>");
  });
  $('a[id=plugins]').click(function(event){
  	event.preventDefault();
    show("plugins.php","adminpage");
    includeJS('../mg-core/script/admin/plugins.js');
    $('#msg_information span').html("Раздел \"<b>Плагины</b>\" предназначен для расширения возможностей системы<br/>Некоторые плагины могут иметь свою страницу настроек, для перехода к настройкам плагина кликните по \"Зеленой шестеренке\"");
  });
  $('a[id=system]').click(function(event){
  	event.preventDefault();
    show("system.php","adminpage");
    includeJS('../mg-core/script/admin/system.js');
    $('#msg_information span').html("Раздел \"<b>Система</b>\" предназначен для обновления системы. <br/> Перед обновлением Вы можете закрыть сайт для посетителей.");

  });

});


//запрашивает страницу для вывода
function show(url,type,request)
{

  $.ajax({
    type: "POST",
    url: "ajax",
    data: "url="+url+"&type="+type+"&"+request,
    /*data: {
      url: url,
      type:type,
      request:request
    },*/
    cache: false,
    success: function(data){
      $("#content").html(data);
      if('plugin'==type){
        //Добавляем для пользовательских форм (они же формы плагинов) отличительные атрибуты
        $("form").each(function() {
          // если у формы плагина стоит атрибут noengine = rue,
          // то такая форма не будет обработана движком, а произведет обычную отправку данных
          if (!$(this).attr('noengine')){
            $(this).attr("plugin", url);
            $(this).attr("ajaxForm", 'true');
          }
        });
        $("form[ajaxForm=true]").submit(function() {
          //todo проверить передачу файлов
          var request = $(this).formSerialize();
          show(url, type, request);
          return false;
        });
      }
    }
  });
}

// Выводит информационное сообщение
function indication(text,status)
{
  var background = "#9abb8b";
  var bordercolor = "#588a41";
  var object = "";

  if(status == "error"){
    object = $('#msg_error');
  }

  if(status == "succes"){
    object = $('#msg_succes');
  }

  if(status == "alert"){
    object = $('#msg_alert');
  }

  if(status == "information"){
    object = $('#msg_information');
  }

  object.animate({ opacity: "show" }, "slow" );
  object.html(text);
  object.animate({ opacity: "hide" }, 3000 );
}

/**
 * Позиционированирует элемент по центру окна браузера
 */
function centerPosition(object)
{
  object.css('position', 'absolute');
  object.css('left', ($(window).width()-object.width())/2+ 'px');
  object.css('top', ($(window).height()-object.height())/2+ 'px');
}

/**
 * Транслитирирует строку
 */
function urlLit(w,v) {
  var tr='a b v g d e ["zh","j"] z i y k l m n o p r s t u f h c ch sh ["shh","shch"] ~ y ~ e yu ya ~ ["jo","e"]'.split(' ');
  var ww='';
  w=w.toLowerCase();
  for(i=0; i<w.length; ++i) {
    cc=w.charCodeAt(i);
    ch=(cc>=1072?tr[cc-1072]:w[i]);
    if(ch.length<3) ww+=ch; else ww+=eval(ch)[v];
  }
  return(ww.replace(/[^a-zA-Z0-9\-]/g,'-').replace(/[-]{2,}/gim, '-').replace( /^\-+/g, '').replace( /\-+$/g, ''));
}

//регистр подключенных скриптов в рамках одной сессии пребывания в админке
var javascripts = [];
function includeJS(path) {
  //alert('пробуем подключить'+path);
  for (var i=0; i<javascripts.length; i++) {
    if(path == javascripts[i]){
      //alert('JavaScript: ['+path+'] уже был подключен ранее!');
      return false;
    }
  }
  javascripts.push(path);
  $.getScript(path);
}

$.getScript('../mg-core/script/jquery.form.js');
//$.getScript('../mg-core/script/tiny_mce/jquery.tinymce.js');
$.getScript('../mg-core/script/nicEdit.js');

