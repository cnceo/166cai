<div class="pop-kstx" id="tip-form">
    <h3 class="pop-kstx-title tip-txt">留下手机号开售早知道</h3>
    <a href="javascript:;" target = "_self" class="pop-close" title="关闭"></a>
    <form action="" class="form tip-form" method = "post">
        <div class="form-item tip-form" id="tip-form">
            <div class="form-item-con ">
                <input class="form-item-ipt vcontent" id = "phone" type="text"  c-placeholder="请输入您的手机号码" autocomplete="off" name="phone" value=""/>
                <div class="form-tip form-tip-error ">
                    <span class="form-tip-con  phone tip hide" >请输入正确的手机号码！</span>
                    <s></s>
                </div>
            </div>
        </div>
        <div class="form-item form-vcode vcode-img" id="tip-form">
            <div class="form-item-con">
                <input class="form-item-ipt vcontent " id = "imageCaptcha" name="imgCaptcha"  type="text" c-placeholder="验证码" value="" ><img id='imgCaptcha' src="/mainajax/captcha?v=<?php echo time();?>" alt=""><a class="lnk-txt" id="change_imgCaptcha" href="javascript:;">换一张</a>
                <div class="form-tip form-tip-error ">
                    <span class="form-tip-con   imgCaptcha  hide">请输入正确的验证码！</span>
                    <s></s>
                </div>
            </div>
        </div>
        <div class="form-item form-vcode vcode-yuyin" id="tip-form">
            <div class="form-item-con">
                <input type="text" name="newphoneyzm" id = "yyCaptcha" c-placeholder="手机验证码" id = "vyzm" value=""  class="form-item-ipt vyzm vcontent">
                <a href="javascript:;" target = "_self" id="btn-getYzm" data-freeze="phone" class="lnk-getvcode _timer">获取语音验证码</a>
                <span href="javascript:;" class="lnk-getvcode-disb hide">重新发送(<em id="_timer">60</em>秒)</span>
                <div class="form-tip form-tip-error">
                    <span class="form-tip-con  newphoneyzm  hide">请输入正确的验证码！</span>
                    <s></s>
                </div>
            </div>
        </div>
        <div class="form-item btn-group">
            <div class="form-item-con">
                <a class="btn btn-confirm submit" target = "_self"  href="javascript:;">确定</a>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    cPlaceholder();
    $(function() {
	$("#change_imgCaptcha").on('click', function(){
        console.log('/mainajax/captcha?v=' + Math.random());
		$('#imgCaptcha').attr('src', '/mainajax/captcha?v=' + Math.random());
		return false;
    });

    $("#phone").click( function(){
        $('input[name="phone"]').attr('target','1');
        var targetimgCaptcha = $('input[name="imgCaptcha"]').attr('target');
        var targetyyCaptcha =  $('input[name="newphoneyzm"]').attr('target');
        var imgCaptcha = $('input[name="imgCaptcha"]').val();
        var yyCaptcha = $('input[name="newphoneyzm"]').val();
        if(!imgCaptcha.match(/^[0-9a-zA-Z]{4}$/) && (targetimgCaptcha == 1) ) {
            $('.imgCaptcha').removeClass('hide');
        }else{
            $('.imgCaptcha').addClass('hide');
        }
        if(!yyCaptcha.match(/^[0-9a-zA-Z]{4}$/) && (targetyyCaptcha == 1) ) {
            $('.newphoneyzm').removeClass('hide');
        }else{
            $('.newphoneyzm').addClass('hide');
        }
    });

    $("#imageCaptcha").click( function(){
       $('input[name="imgCaptcha"]').attr('target','1');
       var targetphone =  $('input[name="phone"]').attr('target');
       var targetyyCaptcha =  $('input[name="newphoneyzm"]').attr('target');
       var phone = $('input[name="phone"]').val();
       var yyCaptcha = $('input[name="newphoneyzm"]').val();
       if(!phone.match(/^\d{11}$/) && (targetphone == 1) ) {
           $('.phone').removeClass('hide');
        }else{
           $('.phone').addClass('hide');
       }
        if(!yyCaptcha.match(/^[0-9a-zA-Z]{4}$/) && (targetyyCaptcha == 1) ) {
            $('.newphoneyzm').removeClass('hide');
        }else{
            $('.newphoneyzm').addClass('hide');
        }
    });

    $("#yyCaptcha").click( function(){
        $('input[name="newphoneyzm"]').attr('target','1');
        var targetphone =  $('input[name="phone"]').attr('target');
        var targetimgCaptcha = $('input[name="imgCaptcha"]').attr('target');
        var phone = $('input[name="phone"]').val();
        var imgCaptcha = $('input[name="imgCaptcha"]').val();
            if(!phone.match(/^\d{11}$/) && (targetphone == 1) ) {
                $('.phone').removeClass('hide');
            }else{
                $('.phone').addClass('hide');
            }
            if(!imgCaptcha.match(/^[0-9a-zA-Z]{4}$/) && (targetimgCaptcha == 1) ) {
                $('.imgCaptcha').removeClass('hide');
                // $('.imgCaptcha').attr('style','display:block');
            }else{
                //$('.imgCaptcha').attr('style','display:none');
                $('.imgCaptcha').addClass('hide');
            }

    });

	$('#btn-getYzm').click(function(){
        var targetphone =  $('input[name="phone"]').attr('target');
        var targetimgCaptcha = $('input[name="imgCaptcha"]').attr('target');
        var phone = $('input[name="phone"]').val();
        var imgCaptcha = $('input[name="imgCaptcha"]').val();
        if(!phone.match(/^\d{11}$/) && (targetphone == 1) ) {
            $('.phone').removeClass('hide');
        }else{
            $('.phone').addClass('hide');
        }
        if(!imgCaptcha.match(/^[0-9a-zA-Z]{4}$/) && (targetimgCaptcha == 1) ) {
            $('.imgCaptcha').removeClass('hide');
            // $('.imgCaptcha').attr('style','display:block');
        }else{
            //$('.imgCaptcha').attr('style','display:none');
            $('.imgCaptcha').addClass('hide');
        }
        var phone = $('input[name="phone"]').val();
        if(!$(this).hasClass('disabled') && phone.match(/^\d{11}$/))
        {
            var code = $('input[name="imgCaptcha"]').val() || false;
            $.ajax({
                type: 'post',
                url:  '/safe/getPhoneCode/newphoneyzm',
                data: {'phone':phone,'code':code},
                dataType: 'json',
                success: function(response) {
                    if(response.status)
                    {
                    	timer();
                        //cx.Alert({content:'验证码已发送你的手机！'});
                    }
                    else
                    {
                       	if(response.msg){
                           	$('.imgCaptcha').closest('.form-tip').addClass('form-tip-error').removeClass('hide');
                           	$('input[name="imgCaptcha"]').val('');
                           	$('.imgCaptcha').show().html(response.msg);
                           	$('#imgCaptcha').attr('src', '/mainajax/captcha?v=' + Math.random());
                        }else{
                           	$('.imgCaptcha').closest('.form-tip').addClass('form-tip-true').removeClass('hide');
                           	//cx.Alert({content:'验证码发送失败，请联系我们的客服！'});
                            $('.newphoneyzm').removeClass('hide').html('验证码发送失败，请联系我们的客服！');
                            closeTimer(1);
                       	}
                    }
               }
            });
        }
    });


	new cx.vform('.tip-form', {
        renderTip: 'renderTips',
        submit: function(data) {
           // console.log(data);
            var self = this;
            $.ajax({
                type: 'post',
                url:  '/main/savePhone',
                data: data,
                success: function(response) {
                    if( response['status'] == 1 ){
                        cx.Alert({content:'系统异常'});
                    }else if (response['status'] == 2 ) {
                        $('.phone').removeClass('hide');
                    }else if (response['status'] == 3 ) {
                        $('.newphoneyzm').removeClass('hide');
                    }else {
                    	$('input[name="phone"]').val('');
                    	$('input[name="newphoneyzm"]').val('');
                    	$('#tip-form').addClass('pop-kstx-result');
                        $.cookie('kaishou','2',{ expires: 365 });
//                        setTimeout(function(){
//                            $('.pop-kstx-result').show().animate({bottom: 0}, 800);
//                        }, 400)
//                        $('.pop-kstx-result').on('click', '.pop-close', function(){
//                            $(this).parents('.pop-kstx-result').hide();
//                        })
                    }

                }
            });
        }
    });
    $(function(){
        $('.pop-kstx').on('click', '.pop-close', function(){
            var popKstx =  $(this).parents('.pop-kstx');
            popKstx.remove();
            if(popKstx.hasClass('pop-kstx-alc')){
                $('.pop-mask').addClass('hidden');
            }
//            function setCookie(name, value, iDay)
//            {
//                var oDate=new Date();
//                oDate.setDate(oDate.getDate()+iDay);
//                document.cookie=name+'='+encodeURIComponent(value)+';expires='+oDate;
//            }
//            setCookie('kaishou','2',365);
//            $.cookie('kaishou','2',{ expires: 60*60*24*100 });
        });
    })
    })
</script>
