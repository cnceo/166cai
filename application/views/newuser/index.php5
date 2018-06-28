<!-- 新手帮助 start -->
<div class="pop-mask pop-mask-guide" <?php if (!$this->is_new): ?> style="display:none;" <?php endif; ?> ></div>
<div class="guide-pop" <?php if (!$this->is_new): ?> style="display:none;" <?php endif; ?> >
    <!--login-->
    <div class="guide-pop-inner guide-pop-home guide-pop-home-login">
      <img class="img-des png_bg" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/guide/home-login.png');?>"  width="242" height="137" alt="">
      <div class="operate">
        <div class="img-bubble"></div>
        <div class="inner">
          <h6>此处可以查看您的账户<br>并充值提款</h6>
        </div>
      </div>
    </div>
    <!--main-->
    <div class="guide-pop-inner guide-pop-home guide-pop-home-main">
      <img class="img-des png_bg" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/guide/home-main.png');?>" width="471" height="405"  alt="">
      <div class="operate">
        <a class="experience" href="javascript:void(0);" target="_self" onclick="close_newuserhelp()"></a>
        <a class="closed" href="javascript:void(0)" target="_self" onclick="close_newuserhelp()"></a>
      </div>
    </div>
    <!--categorys-->
    <div class="guide-pop-inner guide-pop-home guide-pop-home-categorys">
      <img class="img-des png_bg" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/guide/home-categorys.png');?>" width="239" height="439"  alt="">
      <div class="operate">
        <div class="img-bubble"></div>
        <div class="inner">
          <h6>点击此处购彩</h6>
        </div>
      </div>
    </div>
</div>
<!--[if IE 6]>
<script src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/DD_belatedPNG_0.0.8a-min.js'); ?>"></script>
<script>DD_belatedPNG.fix('.png_bg');</script>
<![endif]-->
<script>
    /**
     * 新手帮助打开
     */
    function open_newuserhelp(){
        $('body, html').animate({
          scrollTop: 0
        }, 500);
        $('.pop-mask-guide').height($(document).height()).show();
        $('.guide-pop').show();
        if (navigator.userAgent.indexOf('Firefox') >= 0) {
          $('body, html').bind('DOMMouseScroll', noScroll); 
        }else{
          $('body, html').bind('mousewheel', noScroll); 
        }
    }

    /**
     * 新手帮助关闭
     */
    function close_newuserhelp(){
        $('.pop-mask-guide').hide();
        $('.guide-pop').hide();
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
