//Обработка  нажатия кнопки сохранения редактированной информации категории
$('a[rel=save_settings]').live("click", function(){

  //собираем из таблицы все инпуты с данными, записываим их в виде нативного кода
  var obj ='{"url":"action/editSettings",';
  $('#table_settings td[id=data] .option').each(function(){
    obj+='"'+$(this).attr('name')+'":"'+$(this).val()+'",';
  });
  obj+='}';
  //преобразуем полученные данные в JS объект для передачи на сервер
  var data1 = eval("(" + obj + ")");

  $.ajax({
    type:"POST",
    url: "ajax",
    data: data1,
    cache: false,
    success: function(data){
      var response = eval("(" + data + ")");
      indication(response.msg, response.status);
      $('#table_settings').animate({
        opacity: "hide"
      }, 1000 );

      $('#table_settings').animate({
        opacity: "show"
      }, "slow" );
    }
  });

});
