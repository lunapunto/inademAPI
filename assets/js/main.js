var hwindow;
var wwindow;
var dir;
var ajaxurl;
$(document).ready(function(){
    on_r();
    dir = $('#dir').val();
    ajaxurl = $('#ajaxurl').val();

});
$(window).resize(function(){
  on_r();
});
$(window).scroll(function(){

});
function isset(selector){
  if($(selector).length){
    return true;
  }else{
    return false;
  }
}
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/;";
}
function on_r(){
  hwindow = $(window).height();
  wwindow = $(window).width();

  if(isset('#home-content')){
    var htop = $('#top').outerHeight();
    var hfoo = $('#toolbar').outerHeight();
    var hHC = hwindow - htop - hfoo;
    $('#home-content').css({
                             paddingTop: htop+'px',
                             paddingBottom: hfoo+'px',
                             height: hwindow
                            });
    $('#service-view').css({
      height: hHC,
      top: htop +'px'
    })
    $('section').css('min-height', hHC+'px');
  }
  if(isset('#services-ul')){
    var services_x = $('#services-ul').find('.service-li').length;
    var service_height = hHC / services_x;
    $('.service-li').height(service_height);
  }
}


//Text slider
function moveSlider(to, selector){
  var parent = $(selector);
  var carret = parent.find('.mc-slider-carr');
  var slides = carret.find('.mc-slider-em').length;
  var pagina = parent.find('.mc-slider-pagination');
  var current = parent.attr('data-current');
      current = parseFloat(current) ? parseFloat(current) : 0;
  pagina.find('.current-bullet').removeClass('current-bullet');
  if($.isNumeric(to)){
    var moveto = to;
  }else if(to == 'next'){
    if(current + 1 < slides){
     var moveto = current +1;
    }else{
     var moveto = 0;
    }
  }else if(to == 'prev'){
    if(0 <= current - 1){
      var moveto = current - 1;
    }else{
      var moveto = slides - 1;
    }
  }
  carret.velocity({translateX: - (moveto * 100 / slides) + '%'});
  pagina.find('.mc-slider-bullet:nth-child('+(moveto + 1)+')').addClass('current-bullet');
  parent.attr('data-current', moveto);
}

function alertS(title,content,buttons,okbutton){
  var p = $('#mainAlert');
  p.find('.alert_title').html(title);
  p.find('.alert_content').html(content);
  if(buttons){
    var appendButtons = buttons;
  }else{
    var appendButtons = '<div class="alert_cta" onclick="alertClose()">'+ (okbutton ? okbutton : '¡Ok!') +'</div>'
  }
  p.find('.alert_ctas').html(appendButtons);
  p.fadeIn(100);
  p.addClass('showingAlert');
  history.pushState({}, window.title, getPathFromUrl(window.location.href));
}
function getPathFromUrl(url) {
  return url.split(/[?#]/)[0];
}
function alertClose(){
  var p = $('#mainAlert');
  p.removeClass('showingAlert');
  p.fadeOut(100);
}

/*
* First time functions
*/
$(function(){
  if(isset('.body_firstTime')){
    var carret = $('#firstTime-slider')[0];
    var carretHammer = new Hammer(carret);
    carretHammer.on('swipeleft', function(){
      moveSlider('next', '#firstTime-slider');
    });
    carretHammer.on('swiperight', function(){
      moveSlider('prev', '#firstTime-slider');
    });
  }
})

function setFirstTimeBarrier(){
  setCookie('portier_firsttime', true, 360);
  window.location.href = dir+'/userGuestFilter';
}

/*
* Form functions
*/
function formSubmit(selector){
  var p = $(selector);
  if(p.length){
    var pa = p.closest('.form_container');
    if(pa.hasClass('form_verification')){
      var errorStr = '';
      pa.find('.ic').each(function(){
        var input = $(this).find('input');
        var field = $(this).attr('data-field');
        if((input.val().length == 0) && field){
          errorStr += '-'+field+'<br>';
        }
      });
      if(errorStr.length){
        alertS('Error', 'Por favor introduce los siguientes campos:<br/><br/>'+errorStr)
      }else{
        p.submit();
      }
    }else{
      p.submit();
    }
  }else{
    return false;
  }
}
$(document).on('submit', '#signupForm', function(){
  var p = $(this).find('input[name="password"]').val();
  var rp = $(this).find('input[name="rpassword"]').val();
  if(p !== rp){
    alertS('Error', 'Las contraseñas no coinciden.');
    return false;
  }else{
    return true;
  }
})
$(document).on('change, keyup', '.digit_ic input', function(){
  var p = $(this).closest('.digit_ic');
  var e = $(this);
  var x = p.find('input').length;
  var y = e.index();
  var v = e.val().toString().split('').pop();
  if(v){
    v = parseInt(v);
    v = Math.abs(v);
    e.val(v);
    if(y < x){
    p.find('input:nth-child('+(y + 2)+')').focus();
    }
  }
})
$(document).on('submit', '#verifyForm', function(){
  var d = $(this).find('.digit_ic').find('input');
  var n = '';
  d.each(function(){
    n += $(this).val();
  })
    n = parseInt(n);
  if(n < 1000){
    alertS('Error', 'Por favor introduce los 4 dígitos de tu código.');
    return false;
  }else{
    $(this).find('input[name=code]').val(n);
    return true;
  }
});
$(document).on('click', '#triggerMenu', function(){
  $('body').toggleClass('showingSidebar');
})

$(function(){
  if(isset('#home-content')){
    var el = $('#home-content')[0];
    var hammer = new Hammer(el);
    hammer.on('panright', function(e){
      var x = e.deltaX;
      animateSidebar(x);
    })
    hammer.on('panleft', function(e){
      var x = e.deltaX;
      animateSidebar(x);
    })
    hammer.on('panend', function(e){
      var x = e.deltaX;
      isOkSidebar(x);
    })
  }
})
var masterPadding = 200;
function isOkSidebar(currentX){
  if(currentX <= (masterPadding * 0.75)){
    animateSidebar(0);
  }else{
    animateSidebar(masterPadding);
  }
}
function animateSidebar(currentX){
  var moveX = Math.min(masterPadding, currentX);
  if(moveX < 0){
  var moveX = 0;
  }
  var deg = moveX * 90 / masterPadding;
  $('#container, #toolbar').css({
    transform: 'translateX('+moveX+'px)'
  });
  $('#triggerMenu').css({
    transform: 'rotate('+deg+'deg)'
  })
  $('#sidebar').css({
    transform: 'translateX('+( moveX - masterPadding )+'px)'
  })
}
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
    function(m,key,value) {
      vars[key] = value;
    });
    return $.param(vars);
  }
$(document).on('click', '.get-part', function(e){
  e.preventDefault();



  var p = $('#home-content');
  var dirA = $(this).attr('data-folder');
  if(!dirA){
    dirA = 'user';
  }
  var part = $(this).attr('href');
  var title = $(this).attr('data-title');
  var queries = $(this).attr('data-queries');
  console.log(queries);

  if(queries){
    queries = $.parseJSON(queries);
  }else{
    queries = {};
  }

  var vars = $.param(queries);
  history.pushState({}, title, dir+'/home/'+part);
  var data = {action: 'get_front_part', part: part, dir: dirA, queries: queries};

  $('a.current-a').removeClass('current-a');
  $(this).addClass('current-a');

  $.post(ajaxurl, data, function(){}, 'json').always(function(msg){
    var html = msg.html;
    p.find('section').remove();
    p.html(html);
    $('#titleBinder').text(title);
    on_r();
    maskInput();
    servicePrice();
    $('.date-picker').pickadate({
      min: new Date()
    });
    $('.time-picker').pickatime({
      min: [8,0],
      max: [17,0]
    });
  }, 'json')
})

function baseName(str)
{
   var base = new String(str).substring(str.lastIndexOf('/') + 1);
    if(base.lastIndexOf(".") != -1)
        base = base.substring(0, base.lastIndexOf("."));
   return base;
}
$(function(){
  var cl = baseName(window.location.href);
  var isok = $('.get-part[href='+cl+']').length;
  if(!isok){
    $('#wrapper').append('<a href="'+cl+'" class="get-part get-part-hidden" data-title="Portier" ></a>');
  }
  window.onpopstate = function (event) {
     $('.get-part[href='+cl+']').click();

  }
})
$(document).on('click', '#goback', function(){
  window.history.back();
})
function maskInput(){
  $('.mask-cp').mask("7299");
  $('.maskdate').mask("99/99");
}
$(document).on('change', 'input[name=card]', function(){
  var val = $('input[name=card]:checked').val();
  if(val == 'new'){
    $('.card-form').show();
  }else{
    $('.card-form').hide();
  }
})
$(document).on('change', '#service-size-select',function(){
  servicePrice();
});
function servicePrice(){
  var price = $('#service-size-select').find('option:selected');
  var finalPrice = price.attr('data-price')+'$';
  $('#service-price').text(finalPrice);
}
$(document).on('change keyup', '#service-second-row input, #service-second-row select', function(){
  formatService();
});
function formatService(){
  var size = $('#service-size-select').find('option:selected').val();
  var date = $('#service-date').find('.date-picker').pickadate();
      date = date.pickadate('picker');
      date = date.get('select', 'yyyy/mm/dd');
  var time = $('#service-time').find('.time-picker').pickatime();
      time = time.pickatime('picker');
      time = time.get('select', 'H:i');
  var cp = $('#service-cp').find('input').val();
  var serviceName = $('#service-name').val();
  var price = $('#service-size-select').find('option:selected').attr('data-price');
  var queries = {service: serviceName, size: size, price: price, date: date, time: time, cp: cp};
  buildGoToPart('#service-cta a', 'Portiers', 'portiers', queries);
}
$(document).on('change keyup', '#streetConfirmation', function(){
  formatConfirmationService();
})
function formatConfirmationService(){
  var street = $('#streetConfirmation').val();
  var queries = {street: street};
  buildGoToPart('#finishConfirmationA', 'Pago', 'paymentFinish', queries);
}
function buildGoToPart(selector, title, part, queries){
  var target = $(selector);
  target.addClass('get-part');
  target.attr('href', part);
  target.attr('data-title', title);
  target.attr('data-queries', JSON.stringify(queries));
}
$(document).on('change', '#cupon', function(){
  if($(this).val()){
    $('#paymentTotal').text('100$ (MXN)');
  }else{
    $('#paymentTotal').text('150$ (MXN)');

  }
})
