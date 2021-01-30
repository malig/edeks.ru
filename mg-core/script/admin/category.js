
$('a[rel=CategoryTree]').live("click", function(){
  var id=$(this).attr('id');
  var text=$(this).text();
  var parent_id=$(this).attr('parent_id');
  $('#contextMenu').css('position','absolute');
  $('#contextMenu').css('display','block');
  $('#contextMenu').offset($(this).offset());
  var top=$('#contextMenu').css('top').slice(0,-2);
  top=parseInt(top)+15;
  $('#contextMenu').css('top',top+'px');
  $('#contextMenu').find('a[rel=creat_new_category]').attr({
    'id':id,
    'name':text
  });
  $('#contextMenu').find('a[rel=delete_category]').attr({
    'id':id,
    'name':text
  });
  $('#contextMenu').find('a[rel=edit_category]').attr({
    'id':id,
    'editname':text,
    'parent_id':parent_id
  });
});


$('a[rel=cansel_context]').live("click", function(){
  $('#contextMenu').css('display','none');
});


//Обработка  нажатия кнопки создания новой подкатегории
$('a[rel=creat_new_category]').live("click", function(){
  var id=$(this).attr('id');
  var parent_name=$(this).attr('name');

  $('#parent_name').html(parent_name);
  $('#parent_id').html(id);

  $('#contextMenu').css('display','none');
  centerPosition($(".creat_category"));
  $(".creat_category").animate({
    opacity: "show"
  }, 500 ); // показываем блок для создания новой категории
});

//Обработка  нажатия кнопки отмены создания
$('a[rel=cancel_creat_new_category]').live("click", function(){
  $(".creat_category").animate({
    opacity: "hide"
  }, 500 );
});

//Обработка  нажатия кнопки сохранения новоq категории
$('a[rel=save_new_category]').live("click", function(){

  var name=$.trim($('input[name=category_name]').val());
  var parent_id=$.trim($('#parent_id').html());

  if(!name){
    err="Укажите название категории!";
    alert(err);
    indication(err, false);
  }
  else{
    $.ajax({
      type:"POST",
      url: "ajax",
      data: {
        url: "action/addCategory",
        title:name,
        parent:parent_id
      },
      cache: false,
      success: function(data){
        var response = eval("(" + data + ")");
        indication(response.msg, response.status);
        $(".creat_category").hide();
        $.ajax({
          type:"POST",
          url: "ajax",
          data: {
            url: "category.php"
          },
          cache: false,
          success: function(data){
            $("#content").html(data);
          }
        });
      }
    });
  }
});


//Обработка  нажатия кнопки удаления  категории
$('a[rel=delete_category]').live("click", function(){
  var id=$(this).attr('id');
  var name=$(this).attr('name');
  if(!confirm('Вы подтверждаете удаление категории "'+$.trim(name)+'" ?')){
    return false;
  }
  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url: "action/deleteCategory",
      id:id,
      title:name
    },
    cache: false,
    success: function(data){
      var response = eval("(" + data + ")");
      indication(response.msg, response.status);
      $('#contextMenu').hide();
      //переходим на последнюю страницу
      $.ajax({
        type:"POST",
        url: "ajax",
        data: {
          url: "category.php"
        },
        cache: false,
        success: function(data){
          $("#content").html(data);
        }
      });
    }
  });
});


//Обработка  нажатия кнопки отмены редактирования категорий
$('a[rel=cancel_edit_category]').live("click", function(){
  $(".edit_category").animate({
    opacity: "hide"
  }, 500 );
});

//Обработка  нажатия кнопки редактирования  категории
$('a[rel=edit_category]').live("click", function(){
  var parent_id=$(this).attr('parent_id');
  var id=$(this).attr('id');
  $("#edit_id").html(id);
  var editname=$(this).attr('editname');
  $("#category_edit_select [value='"+parent_id+"']").attr("selected", "selected");
  $('input[name=edit_name]').val(editname);
  $('#contextMenu').css('display','none');
  centerPosition($(".edit_category"));
  $(".edit_category").animate({
    opacity: "show"
  }, 500 );
});


//Обработка  нажатия кнопки сохранения редактированной информации категории
$('a[rel=save_edit_category]').live("click", function(){

  var name=$.trim($('input[name=edit_name]').val());
  var parent_id=$.trim($('#category_edit_select').val());
  var id=$("#edit_id").html();

  if(!name){
    err="Укажите название категории!";
    indication($("#message"),err, false);
  }
  else{
    $.ajax({
      type:"POST",
      url: "ajax",
      data: {
        url: "action/editCategory",
        title:name,
        id:id,
        parent:parent_id
      },
      cache: false,
      success: function(data){

        var response = eval("(" + data + ")");
        indication(response.msg, response.status);
        $(".edit_category").hide();

        $.ajax({
          type:"POST",
          url: "ajax",
          data: {
            url: "category.php"
          },
          cache: false,
          success: function(data){
            $("#content").html(data);
          }
        });
      }
    });
  }
});
