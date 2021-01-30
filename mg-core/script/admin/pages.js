
$('a[rel=preview_page]').live('click', function(){
  var title=$('input[name=title_page]').val();
  var filename_page=$('input[name=filename_page]').val()+".html";
  var content_page=$('.nicEdit-main').html();
  var id=$('#page_id').val();
  $('#previewContent').val(content_page);
  $('#previewer').submit();
});

$('a[rel=save_page]').live('click', function(){
  var title=$('input[name=title_page]').val();
  var filename_page=$('input[name=filename_page]').val()+".html";
  var content_page=$('.nicEdit-main').html();
  var id=$('#page_id').val();
  var status="create_page";
  if (id)status="update_page";
  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url:"action/savePage",
      status:status,
      title:title,
      filename:filename_page,
      content_page:content_page,
      id:id
    },
    cache: false,
    success: function(data){
      var response = eval("(" + data + ")");
      indication(response.msg, response.status);
      if(id) {
        $("#table_page tr[id=page_"+id+"] td[class=title]").text(title);
        $("#table_page tr[id=page_"+id+"] td[class=url]").html("<a href='"+SITE+"/"+filename_page+"' title='Перейти на страницу "+title+"'>"+filename_page+"</a>");
      }
      else{

        $("#table_page").append("<tr  id='page_"+response.data.id+"'><td>"+title+"</td><td><a href='"+SITE+"/"+filename_page+"' title='Перейти на страницу "+title+"'>"+filename_page+"</a></td><td><a href='#' title='Редактировать' class='editBtn' rel='page_edit' id='"+response.data.id+"'></a></td><td><a href='#' title='Удалить' class='delBtn' rel='page_del' id='"+response.data.id+"'></a></td><tr/>");
      }

      $(".creat_page").animate({
        opacity: "hide"
      }, 500 );
    }
  });
});

function initPageEditor(){

  $('.page_editor_textarea').html("<textarea id='elm1' name='elm1' rows='15' cols='80' style='width: 100%' ></textarea>");
  $('input[name=title_page]').val('');
  $('input[name=filename_page]').val('');
  $('#page_id').val('');
  $('#elm1').text('');
  $('#page_editor').css('display','block');
  $(".creat_page").hide();//скрываем открытые окна
  centerPosition($(".creat_page"));
  $(".creat_page").animate({
    opacity: "show"
  }, 500 ); // показываем блок для создания нового товара
}

$('a[rel=create_page]').live('click', function(){
  initPageEditor();
  $('.m-page-24').text('Новая страница');
  new nicEditor({fullPanel : true}).panelInstance('elm1');
});

$('a[rel=page_edit]').live('click', function(){
  initPageEditor();
  $('.m-page-24').text('Редактирование страницы');
  var id = $(this).attr('id');

  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url:"action/getPage",
      id:id
    },
    cache: false,
    success: function(data){

      var response = eval("(" + data + ")");
      if(response.status!="succes")
      {
        indication(response.msg, response.status);
        return false;
      }
      else{
        $('#page_id').val(id);
        $('input[name=title_page]').val(response.title);
        $('input[name=filename_page]').val(response.url);
        $('#elm1').text(response.html_content);
        new nicEditor({fullPanel : true}).panelInstance('elm1');

      }
    }
  });

});


$('a[rel=page_del]').live('click', function(){
  var id = $(this).attr('id');
  if(!confirm("Вы подтверждаете удаление страницы ?")){
    return false;
  }
  $.ajax({
    type:"POST",
    url: "ajax",
    data: {
      url:"action/deletePage",
      id:id
    },
    cache: false,
    success: function(data){
      var response = eval("(" + data + ")");
      indication(response.msg, response.status);
      if(response.status=="succes"){
        $("#table_page tr[id=page_"+id+"]").remove();
      }
    }
  });
});


$('input[name=title_page]').live('blur keyup',function() {
  var text =$(this).val();
  if(text) {
    text=urlLit(text,1);
    $('input[name=filename_page]').val(text);
  }
});

$('a[rel=cancel_creat_new_page]').live('click', function(){
  $(".creat_page").animate({
    opacity: "hide"
  }, 500 );
});