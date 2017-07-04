var $ = jQuery;
var dir = $('#originalDir').val();
var ajaxurl;
var hwindow;
var wwindow;
var htopbar;
var wmenu;
function isset(selector){
  return ($(selector).length ? true : false);
}
function onR(){
   hwindow = $(window).height();
   wwindow = $(window).width();

   if(isset('#menu')){
     wmenu = $('#menu').outerWidth();
   }
   if(isset('#topbar')){
     htopbar = $('#topbar').outerHeight();
     var hmfoot = $('#logout-bottom').outerHeight();
     $('#menu').css('padding-top', htopbar);
     $('#realmenu').height(hwindow - htopbar - hmfoot);
   }

   if(isset('.binder')){

     $('.binder').css({paddingLeft: wmenu, minHeight: hwindow});


   }
}
$(document).ready(function(){
  onR();
  toggleize();
  togglesOk();
  ajaxurl = $('#ajaxurl').val();
})
$(window).resize(function(){
  onR();
})
function rand(min,max){
    var random =  Math.floor(Math.random() * max) + min;
    return random;
}
function alertS(msg, title){

  notie.alert({
    type: 'info', // optional, default = 4, enum: [1, 2, 3, 4, 5, 'success', 'warning', 'error', 'info', 'neutral']
    text: msg,
    time: 2,
    position: 'top' // optional, default = 'top', enum: ['top', 'bottom']
  })
}
function togglesOk(){
  if(isset('.toggle')){
    $('.toggle').each(function(){
      var i = $(this).find('input');
      var is = i.is(':checked');
      if(is){
        $(this).addClass('toggle_checked');
      }else{
        $(this).removeClass('toggle_checked');
      }
    })
  }
}
function toggleize(){
  if(isset('.toggle')){
    $('.toggle').each(function(){
      var html = '<div class="circle"></div><div class="bar"></div>';
      $(this).append(html);
    })
  }
}
$(document).on('change', '.toggle input', function(){
  var p = $(this).closest('.toggle');
  var i = $(this);
  var is = i.is(':checked');
  if(is){
    p.addClass('toggle_checked');
  }else{
    p.removeClass('toggle_checked');
  }
})
$(document).on('click', '.alert_close', function(){
  $('#alert').hide();
})
$(document).on('change', '.toggle-active', function(){
    var group = $(this).attr('data-group');
    var id = $(this).val();
    var i = $(this);
    i.prop('disabled', true);
    $.post(ajaxurl, {id: id, group: group, action: 'toggle_user'}, function(msg){
      i.prop('disabled', false);
    })
})
$(document).on('change', '.toggle-entregable', function(){
    var group = $(this).attr('data-group');
    var id = $(this).val();
    var i = $(this);
    i.prop('disabled', true);
    $.post(ajaxurl, {id: id, group: group, action: 'toggle_entregable'}, function(msg){
      i.prop('disabled', false);
    })
})
$(document).on('click', '#addservicetype', function(){
  var cl = $('.tt-group').find('.tt').last();
  var clone = cl.clone();
      clone.removeClass('tt-first-child');
      clone.find('input').val('');
      clone.find('.portier_win span').text('-');
      cl.after(clone);
})
$(document).on('click', '.button_remove', function(){
  var parent = $(this).attr('data-parent');
      parent = $(this).closest(parent);
      parent.remove();
})
$(document).on('change', '.service_price_input', function(){
  var fee = $('input[name=fee]').val();
  var t = $(this).val();
  if(!fee || !t){
    var str = '-';
  }else{
    var str = Math.ceil(t * (fee / 100) * 5);
  }
  $(this).closest('.tt').find('.portier_win').find('span').text(str);
})
$(document).on('change', 'input[name=fee]', function(){
  $('.service_price_input').each(function(){
    $(this).change();
  })
})
function isimage(file){
  if(!file.name.match(/.(jpg|jpeg|png|gif)$/i)){
    return false;
  }else{
    return true;
  }
}
function bytesToSize(bytes) {
   var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
   if (bytes == 0) return '0 Byte';
   var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
   return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
};
function previewFile(file, thumb) {
  var reader  = new FileReader();

  reader.addEventListener("load", function () {
    var b64 = reader.result;
    thumb.css('background-image', 'url('+b64+')');
  }, false);

  if (file) {
    reader.readAsDataURL(file);
  }
}
$(document).on('change', '.fc input', function(){
  var file = $(this)[0].files[0];
  var isimg= isimage(file);
  if(!isimg){
    alertS('Por favor una imagen válida.', 'Error');
  }else{
    if(2000000 < file.size){
      alertS('Por favor una imagen menor a 2 MB de tamaño.', 'Error');

    }else{
      var name = file.name;
      var size = bytesToSize(file.size);
      var str = name + ' ('+size+')';
      $(this).closest('.fc').find('.button').text(str);
      previewFile(file, $(this).closest('.fc').find('.thumb'));
    }

  }
})
$(document).on('click', '.searchTrigger', function(){
  $('body').toggleClass('toggleSearch');
});
$(document).on('submit', '#ajaxnotification', function(e){
  e.preventDefault();
  console.log($(this).serializeArray());
  var data = {
    action: 'send_notification',
    msg: $(this).find('textarea').val(),
    title: $(this).find('input[name=name]').val()
  }
})

$(document).on('change', '#visita_single textarea', function(){
  var id = $('#idnegocio').val();
  var text = $(this).val();
  var to = $(this).attr('data-to');

  var data = {
    id: id,
    text: text,
    to: to,
    action: 'ajax_save_analisis'
  };

  $.post(ajaxurl, data, function(){
    
  })

})
