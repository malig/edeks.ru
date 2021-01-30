//Обработка  нажатия кнопки проверки обновления
$('a[rel=checkUpdata]').live('click', function(){
  $("#loader").css('display', 'block');

  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url: "action/checkUpdata"
    },
    cache: false,
    success: function(data){
      $("#loader").css('display', 'none');
      var response = eval("(" + data + ")");
      //indication(response.msg, response.status);
      if('alert' != response.status){
                $("#newVersion").css('display', 'block');
        $("#upSys").html('<span id="mess"></span>.<br>Перед обновлением рекомендуется сделать резервную копию базы данных, а также всех файлов сайта. <br/> <a href="#" rel="updataSystem" class="button">Обновить</a>');
      }else{
                $("#newVersion").css('display', 'none');
        $("#upSys").html('<span id="mess"></span>');
      }
      $("#mess").html(response.msg);
    }
  });
});
//Обработка  нажатия кнопки обновить

$('a[rel=updataSystem]').live("click", function(){
  var version = $("#lVer").text();
  $("#loader").css('display', 'block');
  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url: "action/updata",
      version: version
    },
    cache: false,
    success: function(data){
      $("#loader").css('display', 'none');
      var response = eval("(" + data + ")");
      indication(response.msg, response.status);
      show("system.php","adminpage");
    }
  });
});
//Обработка  нажатия кнопки Закрыть сайт для профилактики

$('input[name=downtime]').live("click", function(){

  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url: "action/downTime"
    },
    cache: false,
    success: function(data){
      var response = eval("(" + data + ")");
      indication(response.msg, response.status);
    }
  });
});
