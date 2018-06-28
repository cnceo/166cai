<!-- 新手帮助 start -->
<div class="guide-btn"><a href="javascript:void(0);" onclick="open_newuserhelp()">新手引导</a></div>
<div class="pop-mask pop-mask-guide" <?php if (!$this->is_new): ?> style="display:none;" <?php endif; ?> ></div>
<div class="guide-pop <?php echo $sub; ?>" <?php if (!$this->is_new): ?> style="display:none;" <?php endif; ?> >
  <!--step1-->
  <div class="guide-pop-inner guide-pop-number step1">
    <img class="img-des png_bg" src="/caipiaoimg/v1.0/img/guide/<?php echo $img; ?>" alt="">
    <div class="operate">
      <div class="inner">
        <a class="btn btn-blue-allBind" href="javascript:void(0);">下一步</a>
        <a class="closed" href="javascript:void(0)" onclick="close_newuserhelp()"></a>
      </div>
    </div>
  </div>
  <!--step2-->
  <div class="guide-pop-inner guide-pop-number-castbasket guide-pop-number-castbasket-addto" style="display:none;">
    <img class="img-des png_bg" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/guide/number-castbasket-addto.png');?>"  width="442" height="67" alt="">
    <div class="operate">
      <div class="inner">
        <a class="btn btn-blue-allBind step2" href="javascript:void(0);">下一步</a>
      </div>
      <a class="closed" href="javascript:void(0)" onclick="close_newuserhelp()"></a>
    </div>
  </div>
  <!--step3-->
  <div class="guide-pop-inner guide-pop-number-castbasket guide-pop-number-castbasket-list" style="display:none;">
    <img class="img-des png_bg" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/guide/number-castbasket-list.png');?>" width="890" height="440"  alt="">
    <div class="operate">
      <div class="inner">
        <a class="btn btn-blue-allBind" href="javascript:void(0);">下一步</a>
      </div>
      <a class="closed" href="javascript:void(0)" onclick="close_newuserhelp()"></a>
    </div>
  </div>
  <!--step4-->
  <div class="guide-pop-inner guide-pop-number-castbasket guide-pop-number-castbasket-submit" style="display:none;">
    <img class="img-des png_bg" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/guide/number-castbasket-submit.png');?>" width="348" height="148"  alt="">
    <div class="operate">
      <div class="inner">
        <a class="btn btn-blue-allBind" href="javascript:void(0);" onclick="close_newuserhelp()">立即体验</a>
      </div>
      <a class="closed" href="javascript:void(0)" onclick="close_newuserhelp()"></a>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/comm.js');?>"></script>
<script>
    var $inner = $(".guide-pop-inner");
    var $innerLength = $inner.length;
    $inner.find(".btn").on("click", function(e){
      var scrolltop;
      e.preventDefault();
      if ( $(this).parents().hasClass("step1") ){
        scrolltop = parseInt($('.guide-pop-number-castbasket-addto').css('top'), 10) - 100;
        $('body, html').animate({
          scrollTop: scrolltop
        }, 500)
      }
      if ( $(this).hasClass("step2") ){
        scrolltop = parseInt($('.guide-pop-number-castbasket-list').css('top'), 10) - 10;
        $('body, html').animate({
          scrollTop: scrolltop
        }, 500)
      }
      if ( $(this).html() == "立即体验" ) return false;
      $(this).parents(".guide-pop-inner").hide();
      $(this).parents(".guide-pop-inner").next().show();
    });
</script>
<!--[if IE 6]>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/DD_belatedPNG_0.0.8a-min.js'); ?>"></script>
<script>DD_belatedPNG.fix('.png_bg');</script>
<![endif]-->
<script>
    /**
     * 新手帮助打开
     */
    function open_newuserhelp(){
        // scrollTo(0,0);
        var scrolltop = parseInt($('.guide-pop-number').css('top'), 10);
        $('body, html').animate({
          scrollTop: scrolltop
        }, 500);
        $('.pop-mask-guide').height($(document).height()).show();
        $('.guide-pop').show();
        $('.guide-pop-inner').hide();       
        $('.step1').show();

        if (navigator.userAgent.indexOf('Firefox') >= 0) {
          $('body, html').bind('DOMMouseScroll', noScroll); 
        }else{
          $('body, html').bind('mousewheel', noScroll); 
        }

        // 修复ie6 select层级问题，插入iframe
        if(!-[1,]&&!window.XMLHttpRequest){
          $('<iframe class="iframe-fix-select"></iframe>').insertAfter('.guide-btn').css({'height': $(document).height()});
          $('.footer').css({'position': 'static'});
        }

    }
    /**
     * 新手帮助关闭
     */
    function close_newuserhelp(){
        $('.pop-mask').hide();
        $('.guide-pop').hide();
        if(!-[1,]&&!window.XMLHttpRequest){
          $('.iframe-fix-select').hide();
          $('.footer').css({'position': 'relative'});
        }
        $('.iframe-fix-select').hide();
        if (navigator.userAgent.indexOf('Firefox') >= 0) {
          $('body, html').unbind('DOMMouseScroll', noScroll); 
        }else{
          $('body, html').unbind('mousewheel', noScroll);
        }

        $.ajax({
          type: 'post',
          url:  '/main/refreshNewUser',
          data: {},
          success: function(response){}
        });
        
    }

    /**
     * 禁用鼠标滚轮
     */
    function noScroll(){
      return false; 
    }

</script>
<!-- 新手帮助 end -->
