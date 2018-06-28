$(function(){
	//头部我的彩票，下拉
    $(".top_bar").on('mouseenter', '.myaccount', function () {
		$(this).addClass("hover_down");
	});
    $(".top_bar").on('mouseleave', '.myaccount', function () {
		$(this).removeClass("hover_down");
	});
    // 导航快速菜单
    $(".nav-ticket").hover(function(){
        var cate = $(this).find(".lottery-categorys");
        $(this).addClass('nav-ticket-hover');
        cate.show();
        cate.hover(function(){
            $(this).show();
        },function(){
            $(this).hide();
        })
    },function(){
        $(this).removeClass('nav-ticket-hover').find(".lottery-categorys").hide();
    })

});
function YzmClick()
{
	$(".vyzm").on('keydown', function(){
		$(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
	});
    $('form .simu-select-med').on('click', function(){
        $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
    })
}
//倒计时
var _ss;//计算剩余的秒数
var time;
var disabled;
function timer()  
{  
	_ss = 60;
	name = $('._timer').data('freeze');
	$(".vyzm").focus();
	$('._timer').addClass('disabled').hide();
	$('#_timer').parents('.lnk-getvcode-disb').removeClass('hide');
	$(".ui-poptip-yuyin").show().parents('.form-item-con').addClass('zindex10');
	YzmClick();
	closeTimer();
	time = setInterval("_timer()",1000);
}

function closeTimer(show)
{
	clearInterval(time);
	if(show){
		$('#_timer').parents('.lnk-getvcode-disb').addClass('hide');
		$(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
		$('._timer').removeClass('disabled').show();
		YzmClick();
	}
}

function _timer()
{
	_ss -= 1;
	if(_ss >= 0)
	{
		$('#_timer').html(_ss);
		return false;
	}
	$('#_timer').parents('.lnk-getvcode-disb').addClass('hide');
	$(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
	$('._timer').removeClass('disabled').show();
	closeTimer();
	YzmClick();
	return true;
}

//语音验证码修改
function timer2(obj){  
    var _this = obj;
    var formVcode = _this.parents('.form-vcode');
    _ss = 60;
    $(".vyzm").focus();
    _this.addClass('disabled').hide();
    formVcode.find('.lnk-getvcode-disb').removeClass('hide');
    formVcode.find(".ui-poptip-yuyin").show().parents('.form-item-con').addClass('zindex10');
    YzmClick();
    clearInterval(time);
    time = setInterval(function(){
        _timer2(_this);
    },1000);
}

function _timer2(obj){
    var _this = obj;
    var formVcode = _this.parents('.form-vcode');
    _ss -= 1;
    if(_ss >= 0){
        formVcode.find(".timer").html(_ss);
        return false;
    }else{
        formVcode.find('.lnk-getvcode-disb').addClass('hide');
        formVcode.find(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
        _this.removeClass('disabled').show();
    }
}

function recaptcha()
{
	$('#captcha').attr('src', '/mainajax/captcha?v=' + Math.random());
}

function recaptcha_reg()
{
	$('#captcha_reg').attr('src', '/mainajax/captcha?v=' + Math.random());
}

jQuery.cookie = function(name, value, options) {
	if (typeof value != 'undefined') {
	   options = options || {};
	   if (value === null) {
		    value = '';
		    options = $.extend({}, options);
		    options.expires = -1;
	   }
	   var expires = '';
	   if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
		    var date;
		    if (typeof options.expires == 'number') {
			     date = new Date();
			     date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
		    } else {
		    	date = options.expires;
		    }
		    expires = '; expires=' + date.toUTCString();
	   }
	   var path = options.path ? '; path=' + (options.path) : '';
	   var domain = options.domain ? '; domain=' + (options.domain) : '';
	   var secure = options.secure ? '; secure' : '';
	   document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
	} else {
	   var cookieValue = null;
	   if (document.cookie && document.cookie != '') {
		    var cookies = document.cookie.split(';');
		    for (var i = 0; i < cookies.length; i++) {
			     var cookie = jQuery.trim(cookies[i]);
			     if (cookie.substring(0, name.length + 1) == (name + '=')) {
				      cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
				      break;
			     }
		    }
	   }
	   return cookieValue;
	}
};
function get_uname()
{
	var uName = $.cookie('name_ie');
	data = uName.split('%');
	if (data.length > 1) {
	    uName = '';
	    for (i = 0; i < data.length; i++) {
	        if (data[i] > 0) {
	            uName += String.fromCharCode(data[i]);
	        }
	    }
	}else {
	    uName = data;
	}
	return uName;
}

if (!window.console) {
	window.console = {};
}
if (!console.log) {
	console.log = function () {};
}

var betInfo = {
    number:function( LotteryCnName, PlayTypeName, Issue, money, remain_money ){
        return  '<ul class="form order-form-list pt10">' + 
                    '<li class="form-item"><label class="form-item-label">订单信息:</label>' + 
                        '<div class="form-item-con fz14"><span class="form-item-txt">' + LotteryCnName + ', ' + PlayTypeName + ' , 第' + Issue + '期</span></div>' + 
                    '</li>' + 
                    '<li class="form-item"><label class="form-item-label">应付金额:</label>' + 
                        '<div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + money + '</b>元</span></div>' + 
                    '</li>' + 
                    '<li class="form-item"><label class="form-item-label">账户余额:</label>' + 
                        '<div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + remain_money + '</b>元' + ( parseFloat(remain_money.replace(/,/g, '')) < parseFloat(money.replace(/,/g, '')) ? '(余额不足)' : '' ) + '</span></div>' + 
                    '</li>';
    },
    jc: function( typeCnName, money, remain_money ){
        return  '<ul class="form-list order-form-list pt10">' +
                    '<li class="form-item"><label class="form-item-label">订单信息:</label>' + 
                        '<div class="form-item-con fz14"><span class="form-item-txt">' + typeCnName + '</span></div></li>' + 
                    '<li class="form-item"><label class="form-item-label">应付金额:</label>' + 
                        '<div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + money + '</b>元</span></div></li>'+
                    '<li class="form-item"><label class="form-item-label">账户余额:</label>' + 
                        '<div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + remain_money + '</b>元'  + ( parseFloat(remain_money.replace(/,/g, '')) < parseFloat(money.replace(/,/g, '')) ? '(余额不足)' : '' ) + '</span></div></li>';
    }
};

function checkDate(){
	if($('.start_time').val() > $('.end_time').val()){
		new cx.Alert({
            	content: '查询开始时间大于结束时间'
          	});
      	return true;
    }
	return false;
}

// 有边框提示条关闭
$('.ptips-bd').on('click', '.ptips-bd-close', function(){
    $(this).parents('.ptips-bd').remove();
})



// IE6 悬停弹出提示、下拉框
var ie6=!-[1,]&&!window.XMLHttpRequest;
if(ie6){
    $('.league-filter').on('mouseover', function(){
        $(this).addClass('hover');
    }).on('mouseout', function(){
        $(this).removeClass('hover');
    })
    $('.mod-tips').on('mouseover', function(){
        $(this).addClass('mod-tips-hover');
    }).on('mouseout', function(){
        $(this).removeClass('mod-tips-hover');
    })
}
