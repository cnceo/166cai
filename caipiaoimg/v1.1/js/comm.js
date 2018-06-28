(function ($) {
    $.fn.tabPlug = function (options) {
        return this.each(function (i) {
            var opts = $.extend({}, $.fn.tabPlug.defaults, options);
            var _this = $(this);
            var rule = _this.data('rule');
            var $menus = _this.children(opts.menuChildSel);
            var $container = $(opts.cntSelect).eq(i);
            var elItem = $container.children(opts.cntChildSel);
            if (rule) {  // 兼容3中混合情况
                opts.eventName = rule.eventType || 'click';
                if (rule.linkItem) {
                    opts.onStyle = rule.currentClass || 'current';
                    elItem = $('[tab=' + rule.linkItem + ']');
                    $menus = _this.find('li')
                }
            }
            if (!elItem) return;
            $menus.on(opts.eventName,function () {
                if ($(this).hasClass(opts.onStyle)) return;
                var index = $menus.index($(this));
                $(this).addClass(opts.onStyle).siblings().removeClass(opts.onStyle);
                if (!(elItem.eq(index).is(':animated'))) {
                    elItem.css('display', 'none').eq(index).fadeIn(opts.aniSpeed);
                    if(Boolean(opts.callbackFun)){
                      opts.callbackFun(index, $container.children(opts.cntChildSel).eq(index));
                    }
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
})(jQuery);
//气泡提示弹层
;(function($) {
    var max = Math.max,
        min = Math.min;
    $.bubble = $.pureToolTips = function(options) {
        var opts = $.extend({
            target      : null,     //目标元素，不能为空
            position    : 't',      //提示框相对目标元素位置 t=top,b=bottom,r=right,l=left
            align       : 'c',      //提示框与目标元素的对齐方式，自动调节箭头显示位置，指向目标元素中间位置，c=center, t=top, b=bottom, l=left, r=right [postion=t|b时，align=l|r有效][position=t|b时，align=t|d有效]
            arrow       : true,     //是否显示箭头
            content     : '',       //内容
            width       : 200,      //宽度
            height      : 'auto',   //高度
            autoClose   : true,     //是否自动关闭
            time        : 2000,     //自动关闭延时时长
            leaveClose  : false,    //提示框失去焦点后关闭
            close       : null, //关闭回调函数
            skin        : 1
        }, options || {}),
            $ao, $ai, w, h, $bubble = $('.bubble'),
            $target = $(opts.target),
            top = $target.offset().top,
            left = $target.offset().left,
            width = $target.outerWidth(),
            height = $target.outerHeight(),
            position = opts.position,
            align = opts.align,
            arrow = opts.arrow,
            //相对位置正好 and 箭头方向相反
            constant = {
                b: 'bubble-up',
                t: 'bubble-down',
                r: 'bubble-left',
                l: 'bubble-right'
            },
            arrowClass = constant[position] || constant.t;

        function init() {
            if (!opts.target) {
                return
            }
            if (!$bubble.length) {
                $bubble = $('<div class="bubble bubble-down"><div class="cont"></div><b class="arrow-out"></b><b class="arrow-in"></b></div>').appendTo(document.body)
            }
            $bubble.removeClass().addClass('bubble ' + (arrow ? arrowClass : '')).addClass('bubble-skin-' + opts.skin).find('.cont').html(opts.content).css({
                width: opts.width,
                height: opts.height
            });
            $ao = $bubble.find('.arrow-out').toggle(arrow);
            $ai = $bubble.find('.arrow-in').toggle(arrow);
            typeof opts.callback == 'function' && opts.callback();
            w = $bubble.outerWidth();
            h = $bubble.outerHeight();
            arrow && autoAdjust();          //设置箭头自动居中
            $bubble.css(setPos()).show();       //设置显示框位置和自动隐藏事件
            opts.leaveClose && leaveClose();//离开关闭
            opts.autoClose && !opts.leaveClose && autoClose(opts.timeout);  //默认自动关闭，优先离开关闭
            return $bubble
        }
        //计算提示框应该出现在目标元素位置
        function setPos() {
            var btw = arrow ? parseInt($ao.css('border-top-width'), 10) : 3,
                brw = arrow ? parseInt($ao.css('border-right-width'), 10) : 3,
                result = {};
            switch (align) {
            case 'c':
                break;
            case 't':
                result.top = top;
                break;
            case 'b':
                result.top = top + height - h;
                break;
            case 'l':
                result.left = left - 18;
                break;
            case 'r':
                result.left = left + width - w - 18;
                break
            }
            switch (position) {
            case 't':
                result.top = top - h - brw - 8;
                break;
            case 'b':
                result.top = top + height + brw + 8;
                break;
            case 'l':
                result.left = left - w - btw;
                break;
            case 'r':
                result.left = left + width + btw;
                break
            }
            result.top || (result.top = top + height / 2 - h / 2);
            result.left || (result.left = left + width / 2 - w / 2);
            return result
        }
        //设置箭头自动居中
        function autoAdjust() {
            var aop, aip, bw, auto = 'auto';
            switch (position) {
            case 't':
                bw = parseInt($ao.css('border-top-width'), 10);
                aop = {
                    bottom: -bw,
                    left: w / 2 - bw,
                    top: auto,
                    right: auto
                };
                alignLR();
                aip = {
                    top: auto,
                    left: aop.left,
                    bottom: aop.bottom + 1,
                    right: auto
                };
                break;
            case 'b':
                bw = parseInt($ao.css('border-bottom-width'), 10);
                aop = {
                    top: -bw,
                    left: w / 2 - bw,
                    right: auto,
                    bottom: auto
                };
                alignLR();
                aip = {
                    top: aop.top + 1,
                    left: aop.left,
                    bottom: auto,
                    right: auto
                };
                break;
            case 'l':
                bw = parseInt($ao.css('border-left-width'), 10);
                aop = {
                    top: h / 2 - bw,
                    right: -bw,
                    left: auto,
                    bottom: auto
                };
                alignTB();
                aip = {
                    top: aop.top + 1,
                    right: aop.right + 1,
                    left: auto,
                    bottom: auto
                };
                break;
            case 'r':
                bw = parseInt($ao.css('border-right-width'), 10);
                aop = {
                    top: h / 2 - bw,
                    left: -bw,
                    right: auto,
                    bottom: auto
                };
                alignTB();
                aip = {
                    top: aop.top + 1,
                    left: aop.left + 1,
                    right: auto,
                    bottom: auto
                };
                break
            }
            //上下侧，左右对齐
            function alignLR() {
                if (align === 'l' && width / 2 > bw && width / 2 < w - bw) {
                    aop.left = width / 2 - bw / 2 + 15
                } else if (align === 'r' && width / 2 > bw && width / 2 < w - bw) {
                    aop.left = w - width / 2 - bw / 2 + 15
                }
            }
            //左右侧，上下对齐
            function alignTB() {
                if (align === 't' && height / 2 > bw && height / 2 < h - bw) {
                    aop.top = height / 2 - bw
                } else if (align === 'b' && height / 2 > bw && height / 2 < h - bw) {
                    aop.top = h - height / 2 - bw
                }
            }
            $ao.css(aop);
            $ai.css(aip)
        }
        function autoClose() {
            window.ptt && clearTimeout(ptt);
            window.pta && clearTimeout(pta);
            window.pta = setTimeout(function() {
                $bubble.hide();
                $.isFunction(opts.close) && opts.close()
            }, opts.time)
        }
        function leaveClose() {
            $bubble.unbind('mouseleave').mouseleave(function(e) {
                $bubble.hide();
                $.isFunction(opts.close) && opts.close()
            }).unbind('mouseenter').mouseenter(function() {
                window.ptt && clearTimeout(ptt)
            })
        }
        return init()
    };
    $.fn.bubble = $.fn.pureToolTips = function(options) {
        var opts = $.extend({
            leaveClose: true
        }, options || {});
        return this.each(function() {
            $(this).mouseenter(function() {
                window.ptt && clearTimeout(ptt);
                window.pta && clearTimeout(pta);
                opts.target = this;
                $.bubble(opts)
            }).mouseleave(function() {
                window.ptt = setTimeout(function() {
                    $('.bubble').hide();
                    $.isFunction(opts.close) && opts.close()
                }, 500)
            })
        })
    }
})(jQuery);

;(function(){
    var navMainA = $('.nav-main > li > a');
    var navMainList = $('.nav-main-list');
    navMainA.on({
        'mouseover': function(){
            if(!$(this).parent('li').hasClass('phone')){
                $(this).parent('li').addClass('hover');
            }
        }, 
        'mouseout': function(){
            $(this).parent('li').removeClass('hover');
        }
    })

    navMainList.on({
        'mouseover': function(){
            $(this).parent('li').addClass('hover');
        },
        'mouseout': function(){
            $(this).parent('li').removeClass('hover');
        }
    })
})()

function slide(option) {
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
    that.trig.each(function (i) {
        if (that.trigType == "mouse") {
            $(this).bind({
                mouseenter: function () {
                    that.switchTo(i);
                }
            });
        }
        else if (that.trigType == "click") {
            $(this).bind({
                click: function () {
                    that.switchTo(i);
                }
            });
        }
    });
    if (that.autoPlay) {
        that.wrap.bind({
            mouseover: function () {
                clearInterval(that.intervalTimer);
            },
            mouseout: function () {
                that.autoPlayFn();
            }
        });
        that.autoPlayFn();
    }
}
slide.prototype = {
    switchTo: function (index) {
        var that = this;
        if (index < that.len) {
            if (index < 0) {
                that.cacheIdx = that.len - 1;
            }
            else {
                that.cacheIdx = index;
            }
        }
        else {
            that.cacheIdx = 0;
        }
        that.trig.removeClass("current");
        that.trig.eq(that.cacheIdx).addClass("current");
        that.prop.eq(that.cacheIdx).show().siblings().hide();
        that.pic.removeClass("current");
        that.pic.stop(true, true).animate({opacity: "hide"}, that.effectTime);
        that.pic.eq(that.cacheIdx).addClass("current").stop(true, true).animate({opacity: "show"}, that.effectTime);
    },
    autoPlayFn: function () {
        var that = this;
        that.intervalTimer = setInterval(function () {
            that.cacheIdx++;
            that.switchTo(that.cacheIdx);
        }, that.playTime);
    }
};

$(function () {
    //Tab切换
	$(".mod-tab").tabPlug({
        cntSelect: '.mod-tab-con',
        menuChildSel: 'li',
        onStyle: 'current',
        cntChildSel: '.mod-tab-item',
        eventName: 'mouseover'
    });

    $(".tab-nav ul").tabPlug({
        cntSelect: '.tab-content',
        menuChildSel: 'li',
        onStyle: 'active',
        cntChildSel: '.tab-item',
        eventName: 'click'
    });

    $(".tab-radio-hd ul").tabPlug({
        cntSelect: '.tab-radio-bd',
        menuChildSel: 'li',
        onStyle: 'cur',
        cntChildSel: '.tab-radio-inner',
        eventName: 'click'
    });

    //头部我的彩票，下拉
    $(".top_bar").on('mouseenter', '.myaccount', function () {
        $(this).addClass("hover_down");
    }).on('mouseleave', '.myaccount', function () {
        $(this).removeClass("hover_down");
    });

    // select
    $("body").on('click', 'dl[class^="simu-select"] dt', function () {
        var _this = $(this);
        setTimeout(function () {
            _this.parent().parent().addClass('pos');
            _this.parent().addClass('selected');
        }, 100)
    }).on('click', function (e) {
        var $simu_select = $('dl[class^="simu-select"]');
        $simu_select.each(function () {
            if (!$.contains($(this).get(0), e.target)) {
                $(this).removeClass('selected');
                $(this).parent().removeClass('pos');
            }
        });
    }).on('sheng', '.city_list', function (e, province) {
        $.ajax({
            type: 'post',
            url: '/safe/getCityList',
            data: {'province': province},
            success: function (response) {
                $('#city-container').parent().parent().find('._scontent').html('请选择');
                $('#city-container').parent().parent().find('.vcontent').val('');
                $('#city-container').html(response);
            }
        });
    }).on('pwdModifyMode', '.modify_mode', function (e, pwdModifyMode, pwdModifyModeName) {
        $('.modify_mode li').hide();
        $(".simu-select-med dt").html(pwdModifyModeName + '<i class="arrow"></i>');
        $('.modify_mode .' + pwdModifyMode).show();
        $('#channeltype').val(pwdModifyMode);
        if (pwdModifyMode == 'phoneType') {
            $("input[name='phone']").addClass('vcontent');
            $("input[name='phone']").attr('data-rule', 'phonenum');
            $("input[name='email']").attr('data-rule', '');
            $("input[name='newphoneyzm']").addClass('vcontent');
            $("input[name='email']").removeClass('vcontent');
            $("#change-submit-name").html("确认");
        }
        if (pwdModifyMode == 'emailType') {
            $("input[name='phone']").removeClass('vcontent');
            $("input[name='phone']").attr('data-rule', '');
            $("input[name='email']").attr('data-rule', 'email');
            $("input[name='newphoneyzm']").removeClass('vcontent');
            $("input[name='email']").addClass('vcontent');
            $("#change-submit-name").html("发送验证邮件");
        }
    }).on('click', '.select-opt-in a:not(.select-opt-canot), .bank-select-sp a', function () {
        var dl = $(this).closest('dl');
        var value = $(this).data('value');
        dl.find('._scontent').html($(this).html());
        dl.find('._scontent').attr('data-value', value);
        dl.find('.vcontent').val(value);
        dl.removeClass('selected');
        dl.parent().removeClass('pos');
        if (dl.data('target') == 'city_list') $('.city_list').trigger('sheng', [value]);
        if (dl.data('target') == 'modify_mode') $('.modify_mode').trigger('pwdModifyMode', [value, $(this).html()]);
		if( dl.data('target') == 'submit' ) $('.betlog-form .submit, .chaselog-form .submit, .hemai_form .submit,.gendanlog-form .submit').trigger('click');
    });
    
    //购彩红包选择操作
    $('body').on('click', '.hb-select a:not(.select-opt-canot)', function () {
    	var redpackMoney = $(this).data('money'), remainMoney = parseInt($('#remain_money').text().replace(/,|\./g, '')),
    	mustMoney = parseInt($('#buyMoney').text().replace(/,|\./g, '')) - parseInt(redpackMoney.toString().replace(/,|\./g, ''));
    	$('#redpackMoney').removeClass('main-color-s').html(redpackMoney);
    	if(redpackMoney > 0) $('#redpackMoney').addClass('main-color-s');
    	if(remainMoney < mustMoney){
    		var redpackId = $("input[name='redpackId']").val(), lid = $(this).parents('.form').find('.form-item').data('lid'),
    		href = $('.btn-group').find('.btn-pop-confirm').data('href'), target = $('.btn-group').find('.btn-pop-confirm').data('target');
    		$('#mustMoney').closest('.form-pop-tip').addClass('hide');
    		$('#mustRecharge').html(((mustMoney-remainMoney)/100).toFixed(2)).closest('.form-pop-tip').removeClass('hide');
        	$('.btn-group').find('.btn-pop-confirm').html('去充值').attr('href', href.replace(/&betRedpack=\d+/g, '&betRedpack=' + redpackId)).attr('target', target);
    		$('.btn-group').find('p').addClass('hide');
    	}else{
    		$('#mustRecharge').closest('.form-pop-tip').addClass('hide');
    		$('#mustMoney').html((mustMoney/100).toFixed(2)).closest('.form-pop-tip').removeClass('hide');
    		$('.btn-group').find('.btn-pop-confirm').html('付款到彩店').attr('href', 'javascript:;');
    		$('.btn-group').find('p').removeClass('hide');
    	}
    	$(this).addClass('select-opt-cur').siblings().removeClass('select-opt-cur');
    	return false;
    });

    // 导航快速菜单
    $(".nav-ticket[con!=main]").hover(function () {
        var cate = $(this).find(".lottery-categorys");
        $(this).addClass('nav-ticket-hover');
        cate.show();
        cate.hover(function () {
            $(this).show();
        }, function () {
            $(this).hide();
        })
    }, function () {
        $(this).removeClass('nav-ticket-hover').find(".lottery-categorys").hide();
    });
    // 首页幻灯片
    var slide_0 = new slide({
        wrap: "#J_slideWrap",
        pic: ".slide li",
        prop: ".slide-info > a",
        trig: "#slideDot > span",
        trigType: "click",
        playTime: 4000
    });
    // 收银台交互
    if (typeof document.body.style.maxHeight == 'undefined') {
        $(".balance").hover(function () {
            $(this).addClass('balance-hover');
        }, function () {
            $(this).removeClass('balance-hover');
        });
    }
    if ($("#checkbox-balance").prop("checked")) {
    	$(this).parents(".balance").addClass('balance-selected');
        $("#payPwd").show();
    }
    $("#checkbox-balance").click(function () {
        if ($(this).prop("checked")) {
            $(this).parents(".balance").addClass('balance-selected');
            $("#payPwd").show();
        } else {
            $(this).parents(".balance").removeClass('balance-selected');
            $("#payPwd").hide();
        }
    });
    $('.other_bank').click(function () {
        $(this).toggleClass('other_bank_shrink');
        $(this).parent(".line").siblings('.other_bank_detail').toggle();
    });
    // 用户中心头像交互
    $("#J_uc-avatar > a").hover(function () {
        $(this).siblings('.avatar-txt').show();
    }, function () {
        $(this).siblings('.avatar-txt').hide();
    });
    fnPlaceholder();

    $("#rip-price-swich").hover(function () {
        var ripPriceSwichOffset = $('#rip-price-swich').offset();
        var fundsOffset = $('.funds').offset();
        $("#rip-price").css({
            'left': ripPriceSwichOffset.left - fundsOffset.left - 38,
            'top': ripPriceSwichOffset.top - fundsOffset.top + 22
        }).show();
    }, function () {
        $("#rip-price").hide();
    });

    //select下拉弹窗定位
    $('.simu-select-med', '.uc-section').on('click', function () {
        var selectTop = $('.uc-section').height() - ($(this).offset().top - $('.uc-section').offset().top);
        var selectOpt = $(this).find('.select-opt');
        if (selectTop < 180) {
            selectOpt.css({'top': 'auto', 'bottom': '31px', 'z-index': 10});
        }
    });
});
function YzmClick() {
    $(".vyzm").on('keydown', function () {
        $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
    });
    $('form .simu-select-med').on('click', function () {
        $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
    })
}

//placeholder的兼容方案
var fnPlaceholder = function(){
    var hasPlaceholderSupport = function(){
      var attr = "placeholder";
      var input = document.createElement("input");
      return attr in input;
    }
    var support = hasPlaceholderSupport();
    if(!support){
      $('body').find("input[c-placeholder]").each(function(index) {
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
}

//倒计时
var _ss;//计算剩余的秒数
var time;
var disabled;
function timer() {
    _ss = 60;
    name = $('._timer').data('freeze');
    $(".vyzm").focus();
    $('._timer').addClass('disabled').hide();
    $('#_timer').parents('.lnk-getvcode-disabled').removeClass('hide');
    $(".ui-poptip-yuyin").show().parents('.form-item-con').addClass('zindex10');
    YzmClick();
    closeTimer();
    time = setInterval("_timer()", 1000);
}

function closeTimer(show) {
    clearInterval(time);
    if (show) {
        $('#_timer').parents('.lnk-getvcode-disabled').addClass('hide');
        $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
        $('._timer').removeClass('disabled').show();
        YzmClick();
    }
}

function _timer() {
    _ss -= 1;
    if (_ss >= 0) {
        $('#_timer').html(_ss);
        return false;
    }
    $('#_timer').parents('.lnk-getvcode-disabled').addClass('hide');
    $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
    $('._timer').removeClass('disabled').show();
    closeTimer();
    YzmClick();
    return true;
}

function timer3() {
    _ss = 60;
    name = $('._timer3').data('freeze');
    //$(".vyzm").focus();
    $('._timer3').addClass('hide').hide();
    $('#_timer3').parents('.hide').removeClass('hide');
    $(".ui-poptip-yuyin").show().parents('.form-item-con').addClass('zindex10');
    YzmClick();
    closeTimer();
    time = setInterval("_timer3()", 1000);
}

function _timer3() {
    _ss -= 1;
    if (_ss >= 0) {
        $('#_timer3').html(_ss);
        return false;
    }
    $('#_timer3').parents('.btn-getvcode-disabled').addClass('hide');
    $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
    $('._timer3').removeClass('hide').show();
    closeTimer();
    YzmClick();
    return true;
}

function recaptcha_reg() {
    $('#captcha_reg').attr('src', '/mainajax/captcha?v=' + Math.random());
}

jQuery.cookie = function (name, value, options) {
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
function get_uname() {
    var uName = $.cookie('name_ie');
    data = uName.split('%');
    if (data.length > 1) {
        uName = '';
        for (i = 0; i < data.length; i++) {
            if (data[i] > 0) {
                uName += String.fromCharCode(data[i]);
            }
        }
    } else {
        uName = data;
    }
    return uName;
}

//显示绑定弹窗
function showBind(e) {
    if ($.cookie('name_ie') != null) {
        cx.PopAjax.bind();
        e.stopImmediatePropagation();
    }
}

if (!window.console) {
    window.console = {};
}
if (!console.log) {
    console.log = function () {
    };
}

// 展示更多插件
!function ($) {
    $.fn.showMorePlug = function (options) {
        return this.each(function (i) {
            var _this = $(this);
            var defaults = {
                num: 5,         // 显示个数
                name: '',       // 显示名字，如：展开全部（银行＝name）
                eleItem: 'li',  // children的名字
                btnClass: '',   // 定义“展开／收起”的class，控制样式
                display: false  // 展开／收起，默认收起
            };
            var cfg = _this.data('rule');
            var opts = $.extend({}, defaults, cfg);
            if (options) {
                opts = $.extend({}, opts, options);
            }
            var _oItem = _this.find(opts.eleItem);
            var _oItemL = _oItem.length;

            if (_oItemL > opts.num) {
                _oItem.slice(0, opts.num).show(); // 显示
                _this.append('<div class="show-more ' + opts.btnClass + '"></div>');
                var _oItemC = _oItem.slice(opts.num); // 隐藏部分
                var _statusTxt = '';
                var _active = '';
                
                // 初始化
                status(!opts.display)

                // 绑定事件
                _this.on('click', '.show-more a', function () {
                    status(opts.display)
                    opts.display = !opts.display
                })
            } else {
                _oItem.show()
            }

            function status (display) {
                if (display) {
                    _oItemC.hide();
                    _statusTxt = '展开';
                } else {
                    _oItem.show();
                    _statusTxt = '收起';
                    _active = 'active';
                }
                _this.find('.show-more').html('<a href="javascript:;" class="' + _active + '">' + _statusTxt + '全部' + opts.name + '<i></i></a>');
            }
        })
    }
}(jQuery);

$(".m-choose").showMorePlug();

var betInfo = {
    number: function (LotteryCnName, Issue, money, remain_money, buyMoney, guarantee) {
    	var str = '<div class="form-item"><label class="form-item-label">订单信息:</label><div class="form-item-con fz14"><span class="form-item-txt">' + LotteryCnName;
    	str += (buyMoney !== undefined) ? ', '+(guarantee !== undefined ? '发起' : '参与')+'合买':', 自购';
    	str += ', 第' + Issue + '期</span></div></div><div class="form-item"><label class="form-item-label">';
    	if (buyMoney) {
    		str += '认购金额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + (parseInt(buyMoney, 10) + (guarantee !== undefined ? parseInt(guarantee, 10) : 0)) + '</b>元';
    	}else {
    		str += '订单金额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + money + '</b>元';
    	}
        if (buyMoney !== undefined && guarantee !== undefined) str += '<span style="font-size: 12px;">（认购'+buyMoney+'元+保底'+guarantee+'元）</span>';
        str += '</span></div></div><div class="form-item"><label class="form-item-label">账户余额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">'+remain_money+'</b>元</span></div></div>';
        if(buyMoney !== undefined){
        	str += ( parseFloat(remain_money.replace(/,/g, '')) < parseFloat(money.toString().replace(/,/g, '')) ? '<div class="form-pop-tip"><div>您的<b class="main-color-s">账户余额不足</b>，请充值完成支付！</div></div>' : '' );
        }else{
        	str += '<div class="form-pop-tip"><div>'+ ((parseFloat(remain_money.replace(/,/g, '')) < parseFloat(money.replace(/,/g, ''))) ? '还需充值：<b class="main-color-s">'+(parseFloat(money.replace(/,/g, '')) - parseFloat(remain_money.replace(/,/g, ''))).toFixed(2)+'</b>元' : '您需要支付：<b class="main-color-s">'+money+'</b>元') + '</div></div>';
        }
        return "<form class='form'>"+str+"</form>";
    },
    redpackNumber: function (LotteryCnName, lid, Issue, money, remain_money, redpack) {
    	var str = '<div class="form-item" data-lid="'+lid+'"><label class="form-item-label">订单信息:</label><div class="form-item-con fz14"><span class="form-item-txt">' + LotteryCnName + ', 自购, 第' + Issue + '期</span></div></div>';
    	str += '<div class="form-item"><label for="" class="form-item-label">订单金额：</label><div class="form-item-con"><span class="form-item-txt"><b class="main-color-s" id="buyMoney">' + money + '</b> 元</span></div></div>';
    	var redpackMoney = '0.00';
    	var redpackId = 0;
    	var redpackDesc = '不使用红包';
    	var moneyClass = '';
    	if(redpack[0].disable === 0){
    		redpackMoney = redpack[0].money;
    		redpackId = redpack[0].id;
    		moneyClass = 'main-color-s';
    		redpackDesc = redpack[0].c_name + redpack[0].use_desc;
    	}
    	str += '<div class="form-item"><label for="" class="form-item-label">购彩红包：</label><div class="form-item-con"><span class="form-item-txt"><b id="redpackMoney" class="'+moneyClass+'">'+redpackMoney+'</b> 元</span>';
    	str += '<dl class="simu-select select-small hb-select hb-select-has"><dt><span class="_scontent">'+ redpackDesc +'</span><i class="arrow"></i><input type="hidden" name="redpackId" class="vcontent" value="'+redpackId+'"></dt>';
    	str += '<dd class="select-opt"><div class="select-opt-in" data-name="redpackId">';
    	if(redpack.length > 0){
    		str += '<a href="javascript:;" data-value="0" data-money="0.00" class="'+(redpackId == 0 ? 'select-opt-cur' : '')+'"><i class="icon-font">&#xe646;</i>不使用红包</a>';
    		for (i = 0; i < redpack.length; i++) {
    			str += '<a href="javascript:;" data-value="'+redpack[i].id+'" data-money="'+redpack[i].money+'" class="'+(redpack[i].disable == '1' ? 'select-opt-canot' : (redpack[i].id == redpackId ? 'select-opt-cur' : ''))+'"><i class="icon-font">&#xe646;</i>'+ redpack[i].c_name + redpack[i].use_desc+'<s>有效期至'+redpack[i].valid_end + '</s>' +(redpack[i].ismobile_used =='1' ? '<s>(客户端专享)</s>' : '')+'</a>';
    		}
    	}
    	str += '</div></dd></dl></div></div>';
        str += '<div class="form-item"><label class="form-item-label">账户余额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money" id="remain_money">'+remain_money+'</b>元</span></div></div>';
        remain_money = parseInt(remain_money.replace(/,|\./g, ''));
        var mustMoney = parseInt(money.replace(/,|\./g, '')) - parseInt(redpackMoney.replace(/,|\./g, ''));
        if(remain_money < mustMoney){
        	str += '<div class="form-pop-tip"><div>还需充值：<b class="main-color-s" id="mustRecharge">'+(((mustMoney-remain_money)/100).toFixed(2))+'</b>元</div></div>';
        	str += '<div class="form-pop-tip hide"><div>您需要支付：<b class="main-color-s" id="mustMoney">0</b>元</div></div>';
        	$('.btn-group').find('.btn-pop-confirm').html('去充值');
        }else{
        	str += '<div class="form-pop-tip"><div>您需要支付：<b class="main-color-s" id="mustMoney">'+((mustMoney/100).toFixed(2))+'</b>元</div></div>';
        	str += '<div class="form-pop-tip hide"><div>还需充值：<b class="main-color-s" id="mustRecharge">0</b>元</div></div>';
        }
        
        return "<form class='form'>"+str+"</form>";
    },
    chase:function (LotteryCnName, money, totalIssue, followMoney, remain_money) {
    	return  '<form class="form"><div class="form-item"><label class="form-item-label">订单信息:</label><div class="form-item-con fz14"><span class="form-item-txt">' + LotteryCnName + ', 追号</span></div></div>' 
            +'<div class="form-item"><label class="form-item-label">方案金额:</label><div class="form-item-con fz14"><span class="form-item-txt">' + money + '元</span></div></div>' 
            +'<div class="form-item"><label class="form-item-label">共追号:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + totalIssue + '</b>期</span></div></div>' 
	        +'<div class="form-item"><label class="form-item-label">投注总额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + followMoney + '</b>元</span></div></div>' 
	        +'<div class="form-item"><label class="form-item-label">账户余额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + remain_money + '</b>元' + '</span></div></div>' 
            +( parseFloat(remain_money.replace(/,/g, '')) < parseFloat(followMoney.replace(/,/g, '')) ? '<div class="form-pop-tip"><div>您的<b class="main-color-s">账户余额不足</b>，请充值完成支付！</div></div>' : '' )+'</form>';
    },
    jc: function (typeCnName, money, remain_money, buyMoney, guarantee) {
    	var str = '<div class="form-item"><label class="form-item-label">订单信息:</label><div class="form-item-con fz14"><span class="form-item-txt">'+typeCnName+'</span></div></div><div class="form-item"><label class="form-item-label">';
    	if (buyMoney) {
    		str += '认购金额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + (parseInt(buyMoney, 10) + (guarantee !== undefined ? parseInt(guarantee, 10) : 0)) + '</b>元';
    	}else {
    		str += '应付金额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + money + '</b>元';
    	}
    	if (buyMoney !== undefined && guarantee !== undefined) str += '<span style="font-size: 12px;">（认购'+buyMoney+'元+保底'+guarantee+'元）</span>';
    	str += '</span></div></div><div class="form-item"><label class="form-item-label">账户余额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">'+remain_money + '</b>元</span></div></div>';
    	if(buyMoney !== undefined){
    		str += ( parseFloat(remain_money.replace(/,/g, '')) < parseFloat(money.toString().replace(/,/g, '')) ? '<div class="form-pop-tip"><div>您的<b class="main-color-s">账户余额不足</b>，请充值完成支付！</div></div>' : '' );
        }else{
        	str += '<div class="form-pop-tip"><div>'+ ((parseFloat(remain_money.replace(/,/g, '')) < parseFloat(money.replace(/,/g, ''))) ? '还需充值：<b class="main-color-s">'+(parseFloat(money.replace(/,/g, '')) - parseFloat(remain_money.replace(/,/g, ''))).toFixed(2)+'</b>元' : '您需要支付：<b class="main-color-s">'+money+'</b>元') + '</div></div>';
        }
    	return "<form class='form'>"+str+"</form>";
    },
    redpackJc: function (LotteryCnName, lid, money, remain_money, redpack) {
    	var str = '<div class="form-item" data-lid="'+lid+'"><label class="form-item-label">订单信息:</label><div class="form-item-con fz14"><span class="form-item-txt">' + LotteryCnName + '</span></div></div>';
    	str += '<div class="form-item"><label for="" class="form-item-label">方案金额：</label><div class="form-item-con"><span class="form-item-txt"><b class="main-color-s" id="buyMoney">' + money + '</b> 元</span></div></div>';
    	var redpackMoney = '0.00';
    	var redpackId = 0;
    	var redpackDesc = '不使用红包';
    	var moneyClass = '';
    	if(redpack[0].disable === 0){
    		redpackMoney = redpack[0].money;
    		redpackId = redpack[0].id;
    		moneyClass = 'main-color-s';
    		redpackDesc = redpack[0].c_name + redpack[0].use_desc;
    	}
    	str += '<div class="form-item"><label for="" class="form-item-label">购彩红包：</label><div class="form-item-con"><span class="form-item-txt"><b id="redpackMoney" class="'+moneyClass+'">'+redpackMoney+'</b> 元</span>';
    	str += '<dl class="simu-select select-small hb-select hb-select-has"><dt><span class="_scontent">'+ redpackDesc +'</span><i class="arrow"></i><input type="hidden" name="redpackId" class="vcontent" value="'+redpackId+'"></dt>';
    	str += '<dd class="select-opt"><div class="select-opt-in" data-name="redpackId">';
    	if(redpack.length > 0){
    		str += '<a href="javascript:;" data-value="0" data-money="0.00" class="'+(redpackId == 0 ? 'select-opt-cur' : '')+'"><i class="icon-font">&#xe646;</i>不使用红包</a>';
    		for (i = 0; i < redpack.length; i++) {
    			str += '<a href="javascript:;" data-value="'+redpack[i].id+'" data-money="'+redpack[i].money+'" class="'+(redpack[i].disable == '1' ? 'select-opt-canot' : (redpack[i].id == redpackId ? 'select-opt-cur' : ''))+'"><i class="icon-font">&#xe646;</i>'+ redpack[i].c_name + redpack[i].use_desc+'<s>有效期至'+redpack[i].valid_end + '</s>' +(redpack[i].ismobile_used =='1' ? '<s>(客户端专享)</s>' : '')+'</a>';
    		}
    	}
    	str += '</div></dd></dl></div></div>';
        str += '<div class="form-item"><label class="form-item-label">账户余额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money" id="remain_money">'+remain_money+'</b>元</span></div></div>';
        remain_money = parseInt(remain_money.replace(/,|\./g, ''));
        var mustMoney = parseInt(money.replace(/,|\./g, '')) - parseInt(redpackMoney.replace(/,|\./g, ''));
        if(remain_money < mustMoney){
        	str += '<div class="form-pop-tip"><div>还需充值：<b class="main-color-s" id="mustRecharge">'+(((mustMoney-remain_money)/100).toFixed(2))+'</b>元</div></div>';
        	str += '<div class="form-pop-tip hide"><div>您需要支付：<b class="main-color-s" id="mustMoney">0</b>元</div></div>';
        }else{
        	str += '<div class="form-pop-tip"><div>您需要支付：<b class="main-color-s" id="mustMoney">'+((mustMoney/100).toFixed(2))+'</b>元</div></div>';
        	str += '<div class="form-pop-tip hide"><div>还需充值：<b class="main-color-s" id="mustRecharge">0</b>元</div></div>';
        }
        
        return "<form class='form'>"+str+"</form>";
    },
    gendan:function(typeCnName,buyMoney,remain_money,totalMoney,followType,buyMoneyRate,buyMaxMoney,followTotalTimes){
        var str = '<div class="form-item"><label class="form-item-label">订单信息:</label><div class="form-item-con fz14"><span class="form-item-txt">' + typeCnName + '</span></div></div>';
        if(followType==0){
            str += '<div class="form-item"><label class="form-item-label">每次认购:</label><div class="form-item-con fz14"><span class="form-item-txt">' + buyMoney/100 + '元</span></div></div>';
        }else{
            str += '<div class="form-item"><label class="form-item-label">每次认购:</label><div class="form-item-con fz14"><span class="form-item-txt">' + buyMoneyRate + '%,但不超过'+buyMaxMoney/100+'元</span></div></div>';
        }
        str += '<div class="form-item"><label class="form-item-label">共定制:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec">' + followTotalTimes + '</b>次</span></div></div>';
        str += '<div class="form-item"><label class="form-item-label">预付总额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">' + totalMoney/100 + '</b>元</span></div></div>';
        str += '<div class="form-item"><label class="form-item-label">账户余额:</label><div class="form-item-con fz14"><span class="form-item-txt"><b class="spec money">'+remain_money+'</b>元</span></div></div>';
        if( parseFloat(remain_money.replace(/,/g, '')) < parseFloat(totalMoney.toString().replace(/,/g, ''))/100) {
            str += '<div class="form-pop-tip"><div>您的<b class="main-color-s">账户余额不足</b>，请充值完成支付！</div></div>';
        }
        return "<form class='form'>"+str+"</form>";
    }
};

function getCnName(lid) {
	lid = parseInt(lid, 10);
	return {
        23529: '大乐透',
        10022: '七星彩',
        23528: '七乐彩',
        51: '双色球',
        52: '福彩3D',
        21406: '11运夺金',
        21407: '新11选5', 
        21408: '惊喜11选5', 
        21421: '乐11选5',
        53: '经典快3', 
        54: '快乐扑克',
        55: '老时时彩',
        56: '易快3', 
        57: '红快3',
        42: '竞彩足球',
        43: '竞彩篮球',
        35: '排列5',
        33: '排列3',
        41:'北单',
        11: '胜负彩',
        19: '任选9'
    }[lid];
}

function checkDate() {
    if ($('.start_time').val() > $('.end_time').val()) {
        new cx.Alert({
            content: '查询开始时间大于结束时间'
        });
        return true;
    }
    return false;
}

// 有边框提示条关闭
$('.ptips-bd').on('click', '.ptips-bd-close', function () {
    $(this).parents('.ptips-bd').remove();
    return false;
}).on('click', function () {
    return false;
});

// IE6 悬停弹出提示、下拉框
//var ie6 = !-[1,] && !window.XMLHttpRequest;
if (typeof document.body.style.maxHeight == 'undefined') {
    $('.league-filter').on('mouseover', function () {
        $(this).addClass('hover');
    }).on('mouseout', function () {
        $(this).removeClass('hover');
    });
    $('.mod-tips').on('mouseover', function () {
        $(this).addClass('mod-tips-hover');
    }).on('mouseout', function () {
        $(this).removeClass('mod-tips-hover');
    });
}	