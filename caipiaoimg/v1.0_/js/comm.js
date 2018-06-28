
(function ($) {
	window.cx || (window.cx = {});
    $.fn.tabPlug = function (options) {
        var opts = $.extend({
        }, $.fn.tabPlug.defaults, options);
        return this.each(function (i) {
            var _this = $(this);
            var $menus = _this.children(opts.menuChildSel);
            var $container = $(opts.cntSelect).eq(i);
            if (!$container)
            return;
            $menus.on(opts.eventName,function () {
                var index = $menus.index($(this));
                $(this).addClass(opts.onStyle).siblings().removeClass(opts.onStyle);
                if (!($container.children(opts.cntChildSel).eq(index).is(':animated'))) {
                    $container.children(opts.cntChildSel).eq(index).siblings().css('display', 'none').end().stop(true, true).fadeIn(opts.aniSpeed);
                }
            });
        });
    };
    $.fn.tabPlug.defaults = {
        cntSelect: '',
        aniSpeed: 'fast',
        onStyle: 'selected',
        menuChildSel: '*',
        cntChildSel: '*',
        eventName: null
    };
}) (jQuery);
//fn_slide

function slide(option){
    var that = this;
    that.wrap = $(option.wrap);
    that.autoPlay = option.autoPlay || true;
    that.pic = that.wrap.find(option.pic);
    that.len = that.pic.length;
    that.playTime = option.playTime || 3000;
    that.trigType = option.trigType || "mouse";
    that.trig = that.wrap.find(option.trig);
    that.effectTime = option.effectTime || 500;
    that.cacheIdx = 0;
    that.prop = $(option.prop);
    that.trig.each(function(i){
        if(that.trigType == "mouse"){
            $(this).bind({
                mouseenter:function(){
                    that.switchTo(i);
                }
            });
        }
        else if(that.trigType == "click"){
            $(this).bind({
                click:function(){
                    that.switchTo(i);
                }
            });
        }
    });
    if(that.autoPlay){
            that.wrap.bind({
            mouseover:function(){
                clearInterval(that.intervalTimer);
            },
            mouseout:function(){
                that.autoPlayFn();
            }
        });
        that.autoPlayFn();
    }
}
slide.prototype = {
    switchTo:function(index){
        var that = this;
        if(index < that.len){
            if(index < 0){
                that.cacheIdx = that.len - 1;
            }
            else{
                that.cacheIdx = index;
            }
        }
        else{
            that.cacheIdx = 0;
        }
        that.trig.removeClass("current");
        that.trig.eq(that.cacheIdx).addClass("current");
        that.prop.eq(that.cacheIdx).show().siblings().hide();
        that.pic.removeClass("current");
        that.pic.stop(true,true).animate({opacity:"hide"},that.effectTime);
        that.pic.eq(that.cacheIdx).addClass("current").stop(true,true).animate({opacity:"show"},that.effectTime);
    },
    autoPlayFn:function(){
        var that  = this;
        that.intervalTimer = setInterval(function(){
            that.cacheIdx++;
            that.switchTo(that.cacheIdx);
        },that.playTime);
    }
}
$(function(){

	//Tab切换
    $(".tab-menu").tabPlug({
        cntSelect: '.rapid-bet',
        menuChildSel: 'li',
        onStyle: 'current',
        cntChildSel: '.rapid-bet-bd',
        eventName: 'mouseover'
    });
    $(".tab-nav ul").tabPlug({
        cntSelect: '.tab-content',
        menuChildSel: 'li',
        onStyle: 'active',
        cntChildSel: '.tab-item',
        eventName: 'click'
    });
    $(".n-tab").tabPlug({
        cntSelect: '.tabWrap',
        menuChildSel: 'li',
        onStyle: 'active',
        cntChildSel: '.n-cont',
        eventName: 'click'
    });
    $(".tab-nav-small ul").tabPlug({
        cntSelect: '.tab-small-cont',
        menuChildSel: 'li',
        onStyle: 'active',
        cntChildSel: '.small-item',
        eventName: 'click'
    });
    $(".n-tab").tabPlug({
        cntSelect: '.n-tabWrap',
        menuChildSel: 'li',
        onStyle: 'active',
        cntChildSel: '.n-cont',
        eventName: 'mouseover'
    })
	/*
	// 具体对待, 注释掉
	// 选中交互
    $('.money_list li,.bank_list li,.type_list li').click(function(){
        if($(this).parent("ul").parent('.bank_list').find('.other_bank_detail').length>0){
            $(this).parent("ul").parent(".bank_list").find('li').removeClass('selected');
            $(this).addClass('selected');
        }else{
        $(this).addClass('selected').siblings().removeClass('selected');
        }
    })
	*/

	//头部我的彩票，下拉
    $(".top_bar").on('mouseenter', '.myaccount', function () {
		$(this).addClass("hover_down");
	});
    $(".top_bar").on('mouseleave', '.myaccount', function () {
		$(this).removeClass("hover_down");
	});

    // select
	$("body").on('click', 'dl[class^="simu-select"] dt', function(){
        var _this = $(this);
        setTimeout(function(){
            _this.parent().parent().addClass('pos');
            _this.parent().addClass('selected'); 
        },100)
	});

    // 外层点击收起
    $('body').on('click', function(e){
        var $simu_select = $('dl[class^="simu-select"]');
        $simu_select.each(function(){
             if( !$.contains( $(this).get(0), e.target )) {
                 $(this).removeClass('selected');
                 $(this).parent().removeClass('pos');
             }
        });
    });

	$("body").on( 'sheng', '.city_list', function(e, province){
		$.ajax({
			type: 'post',
			url:  '/safe/getCityList',
			data: { 'province' : province },
			success: function(response) {
				$('#city-container').parent().parent().find('._scontent').html('请选择');
				$('#city-container').parent().parent().find('.vcontent').val('');
				$('#city-container').html(response);
			}
		});
	});

    $("body").on( 'pwdModifyMode', '.modify_mode', function(e, pwdModifyMode, pwdModifyModeName){
        $('.modify_mode li').hide();
        $(".simu-select-med dt").html(pwdModifyModeName + '<i class="arrow"></i>');
        $('.modify_mode .'+ pwdModifyMode).show();
        $('#channeltype').val(pwdModifyMode);
        if(pwdModifyMode == 'phoneType'){
            $("input[name='phone']").addClass('vcontent');
            $("input[name='phone']").attr('data-rule','phonenum');
            $("input[name='email']").attr('data-rule','');
            $("input[name='newphoneyzm']").addClass('vcontent');
            $("input[name='email']").removeClass('vcontent');
            $("#change-submit-name").html("确认");
        }
        if(pwdModifyMode == 'emailType'){
            $("input[name='phone']").removeClass('vcontent');
            $("input[name='phone']").attr('data-rule','');
            $("input[name='email']").attr('data-rule','email');
            $("input[name='newphoneyzm']").removeClass('vcontent');
            $("input[name='email']").addClass('vcontent');
            $("#change-submit-name").html("发送验证邮件");
        }
    });

    $("body").on('click', '.select-opt-in a,.bank-select-sp a', function(e) {
        var dl = $(this).closest('dl');
		var value = $(this).data('value');
        dl.find('._scontent').html( $(this).html() ); 
        dl.find('._scontent').attr('data-value', value);
        dl.find('.vcontent').val( value );
        dl.removeClass('selected');
        dl.parent().removeClass('pos');
		if( dl.data('target') == 'city_list' ){
			$('.city_list').trigger('sheng', [value]);
		}

        if( dl.data('target') == 'modify_mode' ){
            $('.modify_mode').trigger('pwdModifyMode', [value, $(this).html()]);
        }

		if( dl.data('target') == 'submit' ){
			$('.betlog-form .submit').trigger('click');
		} 
		/*
        if(dl.siblings('.submit'))
        {   var submit = dl.siblings('.submit');
        	submit.val($(this).parent().data('name'));
        	//set action
            target = dl.data('target');
            if(target)
            {
            	submit.attr('data-target', target);
            	submit.trigger('click');
            }
        }
		*/
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

    // 首页幻灯片
    var slide_0 = new slide({
        wrap:"#J_slideWrap",
        pic:".slide li",
        prop: ".slide-info > a",
        trig:"#slideDot > span",
        trigType : "click",
        playTime : 4000
    });
    // 收银台交互
    if(!-[1,]&&!window.XMLHttpRequest){
        $(".balance").hover(function(){
            $(this).addClass('balance-hover');
        },function(){
            $(this).removeClass('balance-hover');
        })
    }
    if($("#checkbox-balance").prop("checked")){
        $(".balance").addClass('balance-selected');
        $("#payPwd").show();
    }
    $("#checkbox-balance").click(function(){
        if($(this).prop("checked")){
            $(this).parents(".balance").addClass('balance-selected');
            $("#payPwd").show();
        }else{
            $(this).parents(".balance").removeClass('balance-selected');
            $("#payPwd").hide();
        }
    })
    $('.other_bank').click(function(){
        $(this).toggleClass('other_bank_shrink');
        $(this).parent(".line").siblings('.other_bank_detail').toggle();
    })
    // 用户中心头像交互
    $("#J_uc-avatar > a").hover(function(){
        $(this).siblings('.avatar-txt').show();
    },function(){
        $(this).siblings('.avatar-txt').hide();
    });

    // input 添加Placeholder方法
    cPlaceholder();
    //$("input[c-placeholder]").each(function(index) {
    //    var _this = $(this);
    //    if(_this.val() == ''){
    //        _this.val(_this.attr('c-placeholder')).addClass('placeholder');
    //    }
    //    _this.bind({
    //        focus : function(){
    //            if(($(this).val() == $(this).attr('c-placeholder')) && ($(this).hasClass('placeholder'))){
    //                $(this).val('').removeClass('placeholder');
    //            }else{
    //                return;
    //            }
    //        },
    //        blur : function(){
    //            if($(this).val() == ''){
    //                $(this).val($(this).attr('c-placeholder')).addClass('placeholder');
    //            }else{
    //                return;
    //            }
    //        }
    //    })
    //});

    $("#rip-price-swich").hover(function(){
        var ripPriceSwichOffset = $('#rip-price-swich').offset();
        var fundsOffset = $('.funds').offset();
        $("#rip-price").css({'left' : ripPriceSwichOffset.left - fundsOffset.left -38, 'top': ripPriceSwichOffset.top - fundsOffset.top + 22}).show();
    },function(){
        $("#rip-price").hide();
    });

    //select下拉弹窗定位
    $('.simu-select-med', '.uc-section').on('click', function(){
        var selectTop = $('.uc-section').height() - ($(this).offset().top - $('.uc-section').offset().top);
        var selectOpt =  $(this).find('.select-opt');
        console.log(selectOpt.height())
        if(selectTop < 180){
            selectOpt.css({'top': 'auto', 'bottom': '31px', 'z-index': 10});
        }
    });
});

function cPlaceholder()
{
    $("input[c-placeholder]").each(function(index) {
        var _this = $(this);
        if(_this.val() == ''){
            _this.val(_this.attr('c-placeholder')).addClass('placeholder');
        }
        _this.bind({
            focus : function(){
                if(($(this).val() == $(this).attr('c-placeholder')) && ($(this).hasClass('placeholder'))){
                    $(this).val('').removeClass('placeholder');
                }else{
                    return;
                }
            },
            blur : function(){
                if($(this).val() == ''){
                    $(this).val($(this).attr('c-placeholder')).addClass('placeholder');
                }else{
                    return;
                }
            }
        })
    });
}

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
	// disabled = $("input[name=" + name + "]");
	// if(disabled){
	// 	disabled.attr("disabled",true);
	// }
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
		// if(disabled){
		// 	disabled.removeAttr("disabled");
		// }
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
	// if(disabled){
	// 	disabled.removeAttr("disabled");
	// }
	closeTimer();
	YzmClick();
	return true;
}


// 语音验证码修改
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

$(function(){
	cx.PopBind = (function() {
		var me = {};
		var $wrapper = $('.bind-form');

		$wrapper.find('.pop-close').click(function() {
			$wrapper.hide();
			cx.Mask.hide();
		});

		$wrapper.find('.cancel').click(function() {
			$wrapper.hide();
			cx.Mask.hide();
		});

		me.show = function() {
			cx.Mask.show();
			$wrapper.css({marginTop : (-$wrapper.height()/2), marginLeft : (-$wrapper.width()/2) }).show();
			$wrapper.find('input[type="text"],input[type="password"]').val('');
			$wrapper.find('input').get(0).focus();
		};

		me.hide = function() {
			$wrapper.hide();
			cx.Mask.hide();
		};

		return me;
	})();

    cx.PopLogin = (function() {
        var me = {};
        var $wrapper = $('.loginPopWrap');

        $wrapper.find('.pop-close').click(function() {
            $wrapper.hide();
			cx.Mask.hide();
        });

        me.show = function() {
            cx.Mask.show();
			$wrapper.css({marginTop : (-$wrapper.height()/2), marginLeft : (-$wrapper.width()/2) }).show();
			$wrapper.find('input[type="text"],input[type="password"]').val('');
			$wrapper.find('input[type="text"]').get(0).focus();
        };

        me.hide = function() {
            $wrapper.hide();
			cx.Mask.hide();
        };

        return me;
    })();

    cx.PopRegister = (function() {
        var me = {};
        var $wrapper = $('.registerPopWrap');

        $wrapper.find('.pop-close').click(function() {
            $wrapper.hide();
			cx.Mask.hide();
        });

        me.show = function() {
            cx.Mask.show();
			$wrapper.css({marginTop : (-$wrapper.height()/2), marginLeft : (-$wrapper.width()/2) }).show();
			$wrapper.find('input[type="text"],input[type="password"]').val('');
			$wrapper.find('input[type="text"]').get(0).focus();
        };

        me.hide = function() {
            $wrapper.hide();
			cx.Mask.hide();
        };

        return me;
    })();

	$('body').on('click',  '.not-bind-bank', function (e){
		cx.Alert({
			content:'您尚未绑定银行卡',
			confirm: '去绑银行卡',
			confirmCb: function() {
				location.href = '/safe/bankcard';
			}
		});
		e.stopImmediatePropagation();
	});	

	$('body').on('click', '.not-bind', showBind);

});

//显示绑定弹窗
function showBind(e){
    cx.PopBind.show();
    e.stopImmediatePropagation();
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
