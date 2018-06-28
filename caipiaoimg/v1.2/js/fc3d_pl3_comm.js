$(function(){
  //切换
  $('._radio_selected li').click(function(){
      if($(this).attr('data-url'))
      {
        window.location.href = baseUrl + $(this).attr('data-url');
      }
  });
  if(_MplayType=='zx')
  {
    $('.bet-tab-hd li').eq(0).trigger('click');
  }
  if(_MplayType=='z3')
  {
    $('.bet-tab-hd li').eq(1).trigger('click');
  }
  if(_MplayType=='z6')
  {
    $('.bet-tab-hd li').eq(2).trigger('click');
  }

});