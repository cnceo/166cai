// 基础配置
require.config({
	baseUrl: '/caipiaoimg/static/js',
    paths: {
        "zepto" : "/caipiaoimg/static/js/lib/zepto.min",
        "frozen": "/caipiaoimg/static/js/lib/frozen.min",
        'basic':'/caipiaoimg/static/js/lib/basic'
    }
})


// UI相关
require(["zepto",'basic','ui/loading/src/loading',"ui/tips/src/tips"], function(z, basic, loading, tips){
    var $ = z;
    var t = tips;

    var rechargeIpt = $('.recharge-num-ipt');
    var rechargeNum = $('.recharge-num').find('li');
    var rechargeNumTxt;
    $('.recharge-num').find('li').on('tap', function(){
    	rechargeNumTxt = parseInt($(this).text());
    	rechargeIpt.val(rechargeNumTxt);
    });

    //显示隐藏密码
    $('.ipt-psw').find('a').on('click', function(){
        var aPsw = $(this).parents('.ipt-psw').find('input');
        if($(this).text() == '显示密码'){
            $(this).text('隐藏密码');
            aPsw.attr('type', 'text');
        }else{
            $(this).text('显示密码');
            aPsw.attr('type', 'password');
        }
    })

 //    rechargeIpt.on('blur', function(){
 //        var val = $(this).val();
 //        if(val == ''){
 //            $.tips({
 //                content:'请输入提款金额',
 //                stayTime:2000,
 //                type:"warn"
 //            })
 //            return false;
 //        }
 //        if( /^\+?([0-9]+|[0-9]+\.[0-9]{0,2})$/.test(val) ) {
 //            if( val <= 0 ) { 
 //                $.tips({
 //                    content:'提款金额格式错误',
 //                    stayTime:2000,
 //                    type:"warn"
 //                })
 //            }
 //        }else{
 //            $.tips({
 //                content:'提款金额格式错误',
 //                stayTime:2000,
 //                type:"warn"
 //            })
 //        }
	// });
    $('.recharge-way').find('a').on('tap', function(){
        if(!flag){
            $.tips({
                content: '请输入正确的金额',
                stayTime: 2000
            })
        }
    }); 


    // $('.bankcard-box').on('tap', function(){
    //     $(this).addClass('bankcard-choose').parent().siblings().find('.bankcard-box').removeClass('bankcard-choose');
    // })
    
    var closeTag = true;

    // $('.ipt-tel').find('a').on('tap', sendOpt);

    //发送验证码
    // function sendOpt(){
    //     var phoneNum = $('input[name="phoneNum"]').val();

    //     // 手机号码规则判断
    //     if(phoneNum == '')
    //     {
    //         $.tips({
    //             content: '手机号码不能为空',
    //             stayTime: 2000
    //         })
    //         return false;
    //     }

    //     var reg = /^\d{11}$/;
    //     if(!reg.test(phoneNum))
    //     {
    //         $.tips({
    //             content: '手机号码格式不正确',
    //             stayTime: 2000
    //         })
    //         return false;
    //     }

    //     var tag = $(this);

    //     $.ajax({
    //         type: 'post',
    //         url: '/app/api/user/sendCaptcha',
    //         data: {phone:phoneNum},
    //         // beforeSend: loading,
    //         success: function (response) {
    //             var response = $.parseJSON(response);
    //             if(response.status == 1)
    //             {
    //                 count(tag);
    //                 $.tips({
    //                     content: '验证码已发送',
    //                     stayTime: 2000
    //                 })
    //             }else{
    //                 $.tips({
    //                     content: response.msg,
    //                     stayTime: 2000
    //                 })
    //             }
    //         },
    //         error: function () {
    //             $.tips({
    //                 content: '网络异常，请稍后再试',
    //                 stayTime: 2000
    //             })
    //         }
    //     }); 
    // }

    //发送验证码-倒计时60s
    function count(_this){
        var seconds = 60;
        var timer = setInterval(function(){
            if(seconds > 1){
                _this.off('tap', count);
                seconds -= 1;
                _this.html('' + seconds + '秒');
            }
            else{
                clearInterval(timer);
                _this.html('重新发送');
                _this.on('tap', count);
            }
        }, 1000);      

    }

    // 公用active背景
    // $('.cp-list').find('li:not(input)').on('tap', function(){
    //     alert(1)
    // });
    
})