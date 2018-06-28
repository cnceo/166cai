<!-- 新手帮助 start -->
<div class="guide-btn"><a href="javascript:void(0);" onclick="open_newuserhelp()">新手引导</a></div>
<div class="pop-mask pop-mask-guide" <?php if (!$this->is_new): ?> style="display:none;" <?php endif; ?>></div>
<div class="guide-pop guide-pop-jczq <?php echo $sub; ?>" <?php if (!$this->is_new): ?> style="display:none;" <?php endif; ?> >
  <!--step1-->
  <div class="guide-pop-inner step1">
    <img class="img-des png_bg" src="/caipiaoimg/v1.0/img/guide/<?php echo $img; ?>" alt="">
    <div class="operate">
      <div class="img-bubble"></div>
      <div class="img-logo"></div>
      <div class="inner">
        <h6>1.选择你预测的比赛结果</h6>
        <p>请至少选择两场比赛</p>
        <p><?php echo $desc; ?></p>
        <ul class="step">
          <li class="active"></li>
          <li></li>
          <li></li>
          <li></li>
        </ul>
        <a class="btn btn-blue-allBind" href="javascript:void(0);">下一步</a>
      </div>
      <a class="closed" href="javascript:void(0)" onclick="close_newuserhelp()"></a>
    </div>
  </div>
  <!--step2-->
  <div class="guide-pop-inner guide-pop-jc-castpanel guide-pop-jc-castpanel-passway" style="display:none;">
    <img class="img-des png_bg" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/guide/jc-castpanel-passway.png');?>" width="120" height="77" alt="">
    <div class="operate">
      <div class="img-bubble"></div>
      <div class="img-logo"></div>
      <div class="inner">
        <h6>2.选择过关方式</h6>
        <p>单关：猜中一场即中奖</p>
        <p>2串1：猜中两场即中奖，以此类推</p>
        <ul class="step">
          <li></li>
          <li class="active"></li>
          <li></li>
          <li></li>
        </ul>
        <a class="btn btn-blue-allBind" href="javascript:void(0);">下一步</a>
      </div>
      <a class="closed" href="javascript:void(0)" onclick="close_newuserhelp()"></a>
    </div>
  </div>
  <!--step3-->
  <div class="guide-pop-inner guide-pop-jc-castpanel guide-pop-jc-castpanel-multiple" style="display:none;">
    <img class="img-des png_bg" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/guide/jc-castpanel-multiple.png');?>"  width="256" height="77" alt="">
    <div class="operate">
      <div class="img-bubble"></div>
      <div class="img-logo"></div>
      <div class="inner">
        <h6>3.选择投注倍数</h6>
        <p>“投注倍数”越高，奖金也会越高哦</p>
        <ul class="step">
          <li></li>
          <li></li>
          <li class="active"></li>
          <li></li>
        </ul>
        <a class="btn btn-blue-allBind" href="javascript:void(0);">下一步</a>
      </div>
      <a class="closed" href="javascript:void(0)" onclick="close_newuserhelp()"></a>
    </div>
  </div>
  <!--step4-->
  <div class="guide-pop-inner guide-pop-jc-castpanel guide-pop-jc-castpanel-submit" style="display:none;">
    <img class="img-des png_bg" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/guide/jc-castpanel-submit.png');?>" width="340" height="108"  alt="">
    <div class="operate">
      <div class="img-bubble"></div>
      <div class="img-logo"></div>
      <div class="inner">
        <h6>4.付款</h6>
        <p>“立即投注”付款，等着中奖吧！</p>
        <ul class="step">
          <li></li>
          <li></li>
          <li></li>
          <li class="active"></li>
        </ul>
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
      e.preventDefault();
      if ( $(this).hasClass("step2") ){
        $('body, html').animate({
          scrollTop: 500
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
        var scrolltop = parseInt($('.guide-pop-jczq .step1').css('top'), 10) - 140;
        if($('.guide-pop').hasClass('guide-pop-jczq-spf')){
          scrolltop -= 60;
        }
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

        // 当数据比较少，悬浮条没有fixed的时候。引导的定位
        var castPanel = $('#castPanel');
        var positionValue = castPanel.css('position');
        var castPanelTop = castPanel.offset().top;
        var elePassway = $('.guide-pop-jczq .guide-pop-jc-castpanel-passway');
        var eleMultiple = $('.guide-pop-jczq .guide-pop-jc-castpanel-multiple');
        var eleSubmit = $('.guide-pop-jczq .guide-pop-jc-castpanel-submit');
        var timer;

        setTimeout(castPanelAbout, 100);
        
        $(window).resize(function(){
          clearTimeout(timer);
          timer = setTimeout(function(){
            castPanelAbout();
          }, 100)
        })

        function castPanelAbout(){
          if(!castPanel.hasClass('fixed')){
            elePassway.css({top: castPanelTop - 7 + "px", 'bottom': 'auto'});
            eleMultiple.css({top: castPanelTop - 7 + "px", 'bottom': 'auto'});
            eleSubmit.css({top: castPanelTop - 6 + "px", 'bottom': 'auto'});
          }
        }
    }
    /**
     * 新手帮助关闭
     */
    function close_newuserhelp(){
        $('.pop-mask').hide();
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
