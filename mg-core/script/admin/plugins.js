// открыть страницу плагина
$("a[rel=openPagePlugin]").live("click", function(){
  var pluginFolder = $(this).attr("pluginFolder");
  if($(this).attr('class') == 'plugin-settings-on'){
    show(pluginFolder, "plugin");
  }else{
    alert('Для данного плагина не предусмотренно никаких настроек.')
  }
});

// активировать плагин
$("a[rel=on]").live("click", function(){
  var pluginFolder = $(this).attr("pluginFolder");
  var checker = $(this);
  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url:"action/activatePlugin",
      pluginFolder: pluginFolder
    },
    cache: false,
    success: function(data){
      var response = eval("(" + data + ")");
      indication(response.msg, response.status);
      if(response.status=="succes"){
        checker.attr('rel', 'off');
        checker.attr('class', 'on-switch');

        var setting = $("a[rel=openPagePlugin][pluginFolder="+pluginFolder+"]");
        if(response.data){
          setting.attr('class', 'plugin-settings-on');
          setting.attr('title', 'Настроить плагин');
        } else{
          setting.attr('class', 'plugin-settings-off');
           setting.attr('title', 'Плагин не имеет страницу настроек');
        }
        $("a[pluginFolder="+pluginFolder+"][rel=openPagePlugin]").css('display', 'block');
        $("#settings_"+pluginFolder).css('display', 'block');
      }
    }
  });
});

// выключить
$("a[rel=off]").live("click", function(){
  var pluginFolder = $(this).attr("pluginFolder");
  var checker = $(this);
  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url:"action/deactivatePlugin",
      pluginFolder: pluginFolder
    },
    cache: false,
    success: function(data){
      var response = eval("(" + data + ")");
      indication(response.msg, response.status);
      if(response.status=="succes"){
        checker.attr('rel', 'on');
        checker.attr('class', 'off-switch');
        $("a[pluginFolder="+pluginFolder+"][rel=openPagePlugin]").css('display', 'none');
        $("#settings_"+pluginFolder).css('display', 'none');
      }
    }
  });
});

//Обработка  нажатия кнопки сохранения редактированной информации категории
$('.plagin-list li').live("click", function(){
  show($(this).attr("name")+".php","plugin");
});
