<?php if ($zcpath) {?>
<div class="pop-spring pop-spring-side pop-spring-scale" style='background: url(/uploads/banner/<?php echo $zcpath?>) 50% 0 no-repeat'>
  <a href="<?php echo $zcurl?>" target="_blank" class="pop-spring-title"><?php echo $zctitle?></a><a class="pop-spring-side-close"></a>
</div>
<script>
$(function(){
	if ($.cookie('spring-small') == '1') {
		$('.pop-spring').hide();
	}
    // 关闭侧边栏
    $('.pop-spring-side').on('click', '.pop-spring-side-close', function(){
      $(this).parents('.pop-spring-side').remove();
      $.cookie('spring-small', '1', {expires:3600 * 24, path: '/'});
    })
})
</script>
<?php }
if (empty($this->uid) && !empty($wcpopurl) && !$this->input->cookie('worldCupLayer')) {?>
<style>
    .joinPop {
        position: fixed;
        left: 50%;
        top: 50%;
        z-index: 1000;
        width: 500px;
        height: 420px;
        margin: -210px 0 0 -250px;
    }
    .joinPop a {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
    }
    .joinPop .close {
        position: absolute;
        left: auto;
        right: 0;
        top: 0;
        width: 66px;
        height: 66px;
    }
</style>
<script>
//世界杯期间引导注册弹层 单例
!function (window, undefined) {
    var getSingle = function (fn) {
        var result = null
        return function () {
            return result || (result = fn.apply(this, arguments))
        }
    }
    var createRegisterLayer = function (url, source) {
        var div = document.createElement('div');
        div.className = 'joinPop'
        div.innerHTML = '\
            <a href="' + url + '" target="_blank" style="background: url(' + source + ') 50% 50% no-repeat;"></a>\
            <a href="javascript:" class="close"></a>\
        '
        document.body.appendChild(div)
        $('.pop-mask').removeClass('hidden').height($(document).height())
        $(document).on('click', '.joinPop a', function () {
            var nextTime = +new Date() + (2 * 60 * 60 * 1000)
            document.cookie = 'worldCupLayer=false;expires=' + new Date(nextTime).toUTCString();
            $(this).closest('.joinPop').hide()
            $('.pop-mask').addClass('hidden')
        })
        return div
    }
    window.showWorldCupRegisterLayer = getSingle(createRegisterLayer)
}(window)

if (document.cookie.indexOf('worldCupLayer') < 0) {
    showWorldCupRegisterLayer('<?php echo $wcpopurl?>', '<?php echo getStaticFile('/caipiaoimg/v1.1/images/img-join-pop.png');?>')
}
</script>
<?php } elseif ($uservpop) {?>
<!-- 购彩护航 start -->
<style>
  .pop-escort {
    position: fixed;
    left: 50%;
    top: 50%;
    z-index: 1999;
    width: 460px;
    height: 328px;
    margin: -164px 0 0 -230px;
    _position: absolute;
    _top: expression(eval(document.documentElement.scrollTop+300));
    background: #fff;
    text-align: center;
  }
  .pop-escort-title {
    height: 56px;
    margin-top: 44px;
    font-size: 38px;
    color: #525252;
  }
  .pop-escort .escort-list {
    margin-bottom: 22px;
    padding: 10px 0;
    font-size: 0;
  }
  .pop-escort .escort-list i {
    display: block;
    width: 70px;
    height: 70px;
    margin: 0 auto;
    background: url(../../caipiaoimg/v1.1/images/sprite-escort.png) 0 0 no-repeat;
  }
  .pop-escort .escort-item2 i {
    background-position: -70px 0;
  }
  .pop-escort .escort-item3 i {
    background-position: -140px 0;
  }
  .pop-escort .btn-escort {
    display: inline-block;
    *display: inline;
    *zoom: 1;
    width: 152px;
    height: 42px;
    margin-bottom: 10px;
    background: url(../../caipiaoimg/v1.1/images/sprite-escort.png) 0 -70px no-repeat;
    line-height: 42px;
    font-size: 16px;
    color: #fff;
  }
  .pop-escort .btn-escort:hover {
    background-position: 0 -126px;
    text-decoration: none;
  }
  .pop-escort .escort-list a {
    display: inline-block;
    *display: inline;
    *zoom: 1;
    width: 115px;
    font-size: 14px;
    color: #666;
    cursor: pointer;
  }
  .pop-escort label {
    display: block;
    cursor: pointer;
  }
  .pop-escort label input {
    margin-right: 2px;
    vertical-align: middle;
  }
  .pop-escort-close {
    position: absolute;
    right: 14px;
    top: 14px;
    width: 22px;
    height: 22px;
    background: url(../../caipiaoimg/v1.1/images/sprite-escort.png) -175px -70px no-repeat;
    text-indent: -150%;
    overflow: hidden;
    font-size: 0;
    cursor: pointer;
  }
  .pop-escort-close:hover  {
    background-position: -175px -105px;
  }
</style>

<script>
  $(function(){
    var esCookie = document.cookie;
    var esCookieValue;
    var esHTML = '<div class="pop-escort">' + '<h5 class="pop-escort-title">你购彩我护航</h5>' + '<div class="pop-escort-bd">' + '<div class="escort-list">' + '<a href="/activity/welcometo166?tc" target="_blank" class="escort-item1"><i></i>支付安全</a>' + '<a href="/activity/welcometo166?tc" target="_blank" class="escort-item2"><i></i>出票通知</a>' + '<a href="/activity/welcometo166?tc" target="_blank" class="escort-item3"><i></i>领奖无忧</a>' + '</div>' + '<a href="/activity/welcometo166?tc" target="_blank" class="btn-escort">查看服务承诺</a>' + '<label for="ck"><input id="ck" type="checkbox">勾选后不再弹出</label>' + '</div>' + '<span class="pop-escort-close">关闭</span>' + '</div>';
    var c_start = esCookie.indexOf('escort=');
    
    if (c_start < 0) {
      $('.pop-mask').removeClass('hidden').css({height: $(document).height()});
      $('body').append(esHTML);


      var nDate = new Date();
      var feDateS = +new Date() + 300*24*3600*1000;
      var nextDateS = Date.parse(new Date(nDate.getFullYear() + '/' + (nDate.getMonth() + 1) + '/' + nDate.getDate())) + 24 * 3600 * 1000
      $('.pop-escort').on('click', 'a', function(){
        $(this).parents('.pop-escort').remove();
        $('.pop-mask').addClass('hidden');
        document.cookie = 'escort=false;expires=' + new Date(feDateS).toUTCString();
      })

      // 关闭弹层
      $('.pop-escort').on('click', '.pop-escort-close', function(){
        if ($('.pop-escort').find('label input').prop('checked')) {
            $.post('/ajax/clickCount', {param: 'escort'}, function(){});
          document.cookie = 'escort=false;expires=' + new Date(feDateS).toUTCString();
        } else {
        	document.cookie = 'escort=false;expires=' + new Date(nextDateS).toUTCString();
        }
        $(this).parents('.pop-escort').remove();
        $('.pop-mask').addClass('hidden');
      })
    }

  })
</script>
<?php }?>