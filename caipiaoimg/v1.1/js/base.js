(function () {
    window.cx || (window.cx = {});
    
    cx.Lid = {DLT: 23529, SYXW: 21406, JXSYXW: 21407, HBSYXW: 21408, SSQ: 51, KS:53, JLKS:56, JXKS:57, KLPK:54, FCSD:52, PLS:33, PLW:35, QLC:23528, QXC:10022, CQSSC:55, GDSYXW:21421};
    
    var Money = cx.Money = (function () {
        var me = {};

        me.format = function (money) {
            if (!money) {
                return '0';
            }
            var unit = '';
            money = parseFloat(money, 10);
            if (money > 10000) {
                money /= 10000;
                unit = '万';
            }
            money = Math.round(money * 100) / 100;
            return money + unit;
        };

        me.round = function (money) {
            if (!money) {
                return '0';
            }
            money = Math.round(money * 10000 / 100) / 100;
            return money;
        };

        return me;
    })();

    var Datetime = cx.Datetime = (function () {
        var me = {},
            Time = {
                SECOND: 1000,
                MIN: 60000,
                HOUR: 3600000,
                DAY: 24 * 3600000
            };

        me.getWeekDay = function (timestamp) {
            return '星期' + ['日', '一', '二', '三', '四', '五', '六'][new Date(timestamp).getDay()];
        };

        me.formatTime = function (timestamp) {
            var day, hour, min, second;
            if (timestamp >= Time.DAY) {
                day = Math.floor(timestamp / Time.DAY);
                hour = Math.floor((timestamp % Time.DAY) / Time.HOUR);
                return {
                    day: day,
                    hour: hour
                };
            } else {
                hour = Math.floor(timestamp / Time.HOUR);
                min = Math.floor((timestamp % Time.HOUR) / Time.MIN);
                second = Math.floor((timestamp % Time.MIN) / Time.SECOND);
                return {
                    hour: hour,
                    min: min,
                    second: second
                };
            }
        };
        function padd(num) {
            return ('0' + num).slice(-2);
        }

        me.format = function (timestamp, options) {
            var datetime = new Date(timestamp);
            options = options || {};
            var year = datetime.getFullYear() + '-';
            var seconds = ':' + padd(datetime.getSeconds());
            if (options.year == false) {
                year = '';
            }
            if (options.seconds == false) {
                seconds = '';
            }
            return year + padd((datetime.getMonth() + 1)) + '-' + padd(datetime.getDate()) + ' ' + padd(datetime.getHours()) + ':' + padd(datetime.getMinutes()) + seconds;
        };

        me.padDate = function (str) {
            return ('0' + str).slice(-2);
        };

        me.getToday = function () {
            var datetime = new Date();
            var year = datetime.getFullYear();
            return year + padd((datetime.getMonth() + 1)) + padd(datetime.getDate());
        };

        return me;
    })();

    var Utils = cx.Utils = (function () {
        var me = {};

        me.padding = function (str, len, ph) {
            if (len <= str.length) {
                return str;
            }
            var padLen = len - str.length;
        };

        return me;
    })();

    $(function () {
    	//阴影对象,方法show,hide
        cx.Mask = (function () {
            var me = {};
            var $mask = $(".pop-mask:not('.pop-mask-guide')");
            var $iframeMask = $('.popIframe');
            me.show = function () {
                $mask.height($(document).height()).removeClass('hidden');
                $iframeMask.css({height: $(document).height(), width: '100%'}).removeClass('hidden'); //设置iframe遮罩层宽高
            };
            me.hide = function () {
                if ($('.pub-pop:visible').length < 1) {
                    $mask.addClass('hidden');
                    $iframeMask.addClass('hidden');
                }
            };

            return me;
        })();

    });
    
    /**
     * Alert弹层
     * 参数：title(标题) confirm(确认按钮文案) size(提示文字大小) cancel(是否有取消按钮) confirmCb(点击确定回调)
     */
    var Alert = cx.Alert = function (options) {
        var title = options.title || '提示',
            confirm = options.confirm || '确定',
            size = options.size || '18',
            self;
        var cancel = options.cancel;
        this.tpl = '<div class="pub-pop pop-alert"><div class="pop-in"><div class="pop-head">';
        this.tpl += '<h2>' + title + '</h2><span class="pop-close" title="关闭">&times;</span></div>';
        this.tpl += '<div class="pop-body"><div class="tac fz'+size+' pt10 yahei c333">' + options.content + '</div>';
        this.tpl += '</div><div class="pop-foot"><div class="btn-group"><a href="javascript:;" target="_self" class="btn btn-pop-confirm">' + confirm + '</a>';
        if (cancel) {
        	this.tpl += '<a href="javascript:;" target="_self" class="btn btn-pop-cancel">'+cancel+'</a></div>';
        }
        this.tpl += '</div></div></div>';
        cx.Mask.show();
        $('body').append(this.tpl);
        this.$el = $('.pop-alert');
        self = this;
        if ($.browser.msie && $.browser.version == '6.0') {
            //如果是IE6 就不添加MarginTop样式
            self.$el.css({marginLeft: (-$(self.$el).width() / 2)});
        } else {
            if ($.browser.msie && $.browser.version == '7.0') {
                //如果是IE7 给Box添加宽度
                self.$el.css({width: $(self.$el).width()});
            }
            self.$el.css({
                marginTop: (-$(self.$el).height() / 2),
                marginLeft: (-$(self.$el).width() / 2)
            });
        }
        self.$el.show();
        self = this;
        // 点击确认按钮-关闭弹窗
        this.$el.find('.btn-pop-confirm').click(function () {
            self.$el.remove();
            cx.Mask.hide();
            if ($.isFunction(options.confirmCb)) {
                options.confirmCb();
            } 
            return false;
        });
        
        this.$el.find('.btn-pop-cancel').click(function() {
            self.$el.remove();
            cx.Mask.hide();
            return false;
        });

        // 点击关闭按钮-关闭弹窗
        this.$el.find('.pop-close').click(function () {
            // self.$el.addClass('hidden').one('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function() {
            //     self.$el.remove();
            // });
            self.$el.remove();
            cx.Mask.hide();
            if (!options.addtion) {
                if ($.isFunction(options.confirmCb)) {
                    options.confirmCb();
                }
            }
        });
    };
    
    /**
     * Alert弹层
     * 参数：btns(按钮配置，格式参照函数里的默认设置) single(提示文字没有div) content(提示文字加div) wrapper(外层外面div) popIn(内层外面div) 
     * 		popHead(头部div) popBody(头部div) inlineBtn(按钮在body里面) append后加内容
     * 参数描述只是文字说明，建议把代码看一遍彻底了解功能
     */
    var Confirm = cx.Confirm = function (options) {
        var btns,
            input,
            btn,
            content,
            wrapper,
            popIn,
            popHead,
            popBody,
            btnWrapper,
            single,
            tplAry = [],
            i,
            tip,
            self;

        btns = options.btns || [{type: 'confirm', href: 'javascript:;', txt: '确定'}, {type: 'cancel', href: 'javascript:;', txt: '取消'}];
        single = '';
        if (options.single) single = '<p class="tac fz18 pt10 yahei c333">' + options.single + '</p>';

        wrapper = options.wrapper || {begin: '<div class="pub-pop pop-pay pop-confirm">', end: '</div>'};
        popIn = options.popIn || {begin: '<div class="pop-in">', end: '</div>'};
        popHead = options.popHead || '<div class="pop-head"><h2>'+(options.title || '提示')+'</h2><span class="pop-close" title="关闭">&times;</span></div>';
        popBody = options.body || {begin: '<div class="pop-body">', end: '</div>'};

        tplAry.push(wrapper.begin, popIn.begin, popHead);
        if (options.inlineBtn) {
            btnWrapper = {begin: '<div class="pop-foot"><div class="btn-group">', end: '</div></div>'};
            tplAry.push(popBody.begin, single, options.content || '', content, btnWrapper.begin);
            for (i = 0; i < btns.length; ++i) {
                btn = btns[i];
                tplAry.push('<a target="', btn.target || '_self', '" ');
                if (btn.href) tplAry.push('href="', btn.href, '"');
                if (btn.data) {
                	$.each(btn.data, function(key, val) {
                		tplAry.push('data-'+key+'="', val, '"');
                	})
                }
                tplAry.push(' class="btn btn-pop-', btn.type, '">', btn.txt, '</a>');
            }
            tplAry.push(btnWrapper.end, popBody.end);
        } else {
            btnWrapper = {begin: '<div class="pop-foot"><div class="btn-group">', end: '</div></div>'};
            tplAry.push(popBody.begin, single, options.content || '', content, popBody.end, btnWrapper.begin);
            for (i = 0; i < btns.length; ++i) {
                btn = btns[i];
                tplAry.push('<a target="', btn.target || '_self', '" ');
                if (btn.href) tplAry.push('href="', btn.href, '"');
                if (btn.data) {
                	$.each(btn.data, function(key, val) {
                		tplAry.push('data-'+key+'="', val, '"');
                	})
                }
                tplAry.push(' class="btn btn-pop-', btn.type, '">', btn.txt, '</a>');
            }
            if(options.tip) tplAry.push('<p>' + options.tip + '</p>');
            tplAry.push(btnWrapper.end);
        }
        if (options.append) tplAry.push(options.append);
        tplAry.push(popIn.end, wrapper.end);

        this.tpl = tplAry.join('');
        cx.Mask.show();
        $('body').prepend(this.tpl);

        this.$el = $('.pop-confirm');
        self = this;
        self.$el.css({
            marginTop: (-$(self.$el).height() / 2),
            marginLeft: (-$(self.$el).width() / 2)
        });
        self.$el.show();
        this.$el.find('.btn-pop-confirm').click(function () {
            if (options.confirmCb) options.confirmCb();
            if (!options.input) {
                self.$el.remove();
                cx.Mask.hide();
            }
        });
        self = this;
        this.$el.find('.btn-pop-cancel, .pop-close').click(function () {
            if (options.cancelCb) options.cancelCb();
            self.$el.remove();
            cx.Mask.hide();
        });
    };
    
    var AdderSubtractor = cx.AdderSubtractor = function (selector, options) {
        this.$el = $(selector);
        this.selector = selector;
        options = options || {};
        this.value = (options['default '] || this.$el.find('input').val()) || 1;
        this.step = options.step || 1;
        this.min = options.min || 1;
        this.cb = null;
        this.plusCb = null;
        this.minusCb = null;

        this.init();
    };

    AdderSubtractor.prototype = {
        init: function () {
            var self = this,
                max = self.$el.find('.plus').attr('data-max') || 0;
            this.value = parseInt(this.value, 10);
            this.$el.find('.number').blur(function () {
                var $this = $(this);
                if ((/^(.*)\D+(.*)$/.test($(this).val()))) {
                	self.value = $(this).val().replace(/\D+/, '');
                	$this.val(self.value);
                }
                if (!self.value) {
                    self.value = 1;
                    $this.val(1);
                }
                if (max) {
                    if (parseInt(self.value) >= parseInt(max)) {
                        $this.val(max);
                        self.value = max;
                    }
                }
                self.cb && self.cb.apply(self);
            });
            this.$el.find('.number').val(this.value);
            this.$el.find('.plus').click(function () {
                var selfIptVal = parseInt($(this).parents(this.selector).find('.number').val());
                if (max) {
                    if (selfIptVal >= parseInt(max)) {
                        self.value = max;
                    } else {
                        self.value = selfIptVal;
                        self.value += self.step;
                    }
                } else {
                    self.value += self.step;
                }
                self.render();
                self.plusCb && self.plusCb.apply(self);
                self.cb && self.cb.apply(self);
            });
            this.$el.find('.minus').click(function () {
                var selfIptVal = parseInt($(this).parents(this.selector).find('.number').val());
                if (selfIptVal == self.min) {
                    return;
                }
                self.value = selfIptVal;
                self.value -= self.step;
                self.render();
                self.minusCb && self.minusCb.apply(self);
                self.cb && self.cb.apply(self);
            });
            this.$el.find('.number').keyup(function (e) {
                var $this = $(this),
                    val = $this.val();
                if (max) {
                    if (parseInt(val) > parseInt(max)) {
                        val = max;
                        $this.val(max);
                        self.value = max;
                    }
                }
                if ((e.which >= 48 && e.which <= 57) || e.which == 8 || (e.which >= 96 && e.which <= 105)) {
                    if (val == '') {
                        self.value = 0;
                    } else {
                        self.value = parseInt(val, 10);
                    }
                } else {
                    if (!(/^\d+$/.test(val))) {
                        $this.val(1);
                        self.value = 1;
                    }
                }
                self.cb && self.cb.apply(self);
            });
        },
        setCb: function (cb) {
            this.cb = cb;
        },
        setPlusCb: function (cb) {
            this.plusCb = cb;
        },
        setMinusCb: function (cb) {
            this.minusCb = cb;
        },
        render: function () {
            this.$el.find('.number').val(this.value);
        },
        setValue: function (val) {
        	max = this.$el.find('.plus').attr('data-max') || 0;
        	if (max && parseInt(val) > parseInt(max)) val = max;
        	if (!(/^\d+$/.test(val))) val = 1;
        	this.$el.find('.number').val(val);
        	this.value = val;
        },
        getValue: function () {
            return this.value;
        }
    };

    var ajax = cx.ajax = (function () {
        var me = {};
        var success;
        var locks = {};

        me.get = function (options) {
            var url = baseUrl + 'ajax/get';
            options.data || (options.data = {});
            var data = options.data;
            data['url'] = options.url;
            return $.ajax({
                url: url,
                data: data,
                success: function (response) {
                    if ('success' in options) {
                        options.success(response);
                    }
                }
            });
        };

        me.post = function (options) {
            var url = baseUrl + 'ajax/post';
            var data = options.data;
            data['url'] = options.url;
            var isJson = data.isJson || 0;
            data.isJson = isJson;
            if (!locks[options.url]) {
                locks[options.url] = true;
                return $.ajax({
                    type: 'post',
                    url: url,
                    data: data,
                    success: function (response) {
                        options.success(response);
                    },
                    complete: function () {
                        locks[options.url] = false;
                    }
                });
            }
            return false;
        };

        return me;
    })();

    cx.url = (function () {
        var me = {};

        function _urlFactory(base, action, params) {
            var paramStr = '';
            if (params) {
                for (var key in params) {
                    paramStr += key + '=' + params[key] + '&';
                }
                paramStr = paramStr.slice(0, -1);
                return base + action + '?' + paramStr;
            }
            return base + action;
        }

        me.getBaseUrl = function (action, params) {
            return _urlFactory(baseUrl, action, params);
        };

        return me;
    })();
})();

$(function () {
    //弹层登
    $('.not-login').click(function (e) {
           if ($(this).hasClass('not-login') || !$.cookie('name_ie')) {
            cx.PopAjax.login();
            e.stopImmediatePropagation();
        }
        e.preventDefault();
    });
    //弹层注册
    $('.not-register').click(function (e) {
        if ($(this).hasClass('not-register') || !$.cookie('name_ie')) {
            cx.PopAjax.register();
            e.stopImmediatePropagation();
        }
        e.preventDefault();
    });
    //信息绑定弹层
    $('body').on('click', '.not-bind', function (e) {
        if ($(this).hasClass('not-bind') && $.cookie('name_ie') != null) {
            cx.PopAjax.bind();
            e.stopImmediatePropagation();
        }
        e.preventDefault();
    });
    //意见反馈
    $('.feedBack').click(function (e) {
        $.ajax({
            type: 'post',
            url: '/pop/getFeedBack',
            data: {version: version},
            success: function (response) {
                $('body').append(response);
                cx.PopCom.show('.feedbackPopWrap');
                cx.PopCom.close('.feedbackPopWrap');
            }
        });
        e.stopImmediatePropagation();
        e.preventDefault();
    });
    //历史开奖对比
    $('.historyCompare').click(function (e) {
        dn = 0;
        s = '';
        for (i in cx._basket_.strings) {
            s += cx._basket_.renderString(cx._basket_.strings[i], i, false, true);
            dn++;
        }
        if (cx._basket_.betNum == 0) {
            $.ajax({
                type: 'post',
                url: '/pop/historyNodata',
                data: {version: version},
                success: function (response) {
                    $('body').append(response);
                    cx.PopCom.show('.historyNodata');
                    cx.PopCom.close('.historyNodata');
                }
            });
        } else if (dn >= 1000) {
            cx.Alert({content: '<i class="icon-font">&#xe611;</i>您对比的期数过多，建议分多次<br>或通过复式选号的方式对比！'});
        } else {
            $.ajax({
                type: 'post',
                url: '/pop/historyCompare',
                data: {version: version},
                success: function (response) {
                    $('body').append(response);
                    cx.PopCom.show('.pop-contrast');
                    cx.PopCom.close('.pop-contrast');
                    $(".pop-contrast .cast-list").append(s);

                    // 历史开奖对比弹窗
                    var popContrast = $('.pop-contrast');
                    var windowHeight = $(window).height();
                    var docHeight = $('.pop-mask').height();
                    var ie6 = (typeof document.body.style.maxHeight == 'undefined');

                    if ($('.pop-contrast').outerHeight() > $(window).height()) {
                        popContrast.css({
                            'position': 'absolute',
                            'top': $(document).height() - $('.pop-contrast').outerHeight(),
                            'margin-top': -$('.pop-contrast').outerHeight() / 2 + 'px'
                        });

                        $('.btn-jsq').on('click', function () {
                            if (ie6) {
                                $('.pop-mask').css({
                                    'position': 'absolute'
                                })
                            } else {
                                $('.pop-mask').css({
                                    'position': 'fixed'
                                })
                            }

                        })
                    } else {
                        popContrast.css({
                            'position': 'absolute',
                            'top': $(window).scrollTop() + $(window).height() / 2 - $('.pop-contrast').outerHeight() / 2 + 'px',
                            'margin-top': 0
                        })
                    }
                }
            });
        }
        e.stopImmediatePropagation();
        e.preventDefault();
    });
    //用户委托/限号协议
    $('body').on('click', '.lottery_pro, .risk_pro', function (e) {
    	if ($(this).hasClass('lottery_pro')) {
    		var type = 'lottery_pro';
    	}else {
    		var type = 'risk_pro';
    	}
        $.ajax({
            type: 'post',
            url: '/pop/getAgreement',
            data: {version: version, type: type},
            success: function (response) {
                $('body').append(response);
                cx.PopCom.show('.articlePopWrap');
                cx.PopCom.close('.articlePopWrap');
            }
        });
        e.stopImmediatePropagation();
        e.preventDefault();
    });
    //公共弹层显示与隐藏
    cx.PopCom = (function () {
        var me = {};
        me.show = function (selector) {
            cx.Mask.show();
            $(selector).css({marginTop: (-$(selector).height() / 2), marginLeft: (-$(selector).width() / 2)}).show();
            if ($(selector).find('input[type="text"]').attr('placeholder')) {
                $(selector).find('input[type="text"],input[type="password"]').val('');
                fnPlaceholder();
            }
            var txtFocus = $(selector).find('input[type="text"],textarea').first();
            if (txtFocus && txtFocus.data('nofocus') == true) {
                txtFocus.focus();
            }
        };
        me.hide = function (selector) {
            $(selector).remove();
            cx.Mask.hide();
        };
        //右上角关闭事件
        me.close = function (selector, options) {
            $(selector).find('.pop-close').click(function () {
                cx.PopCom.hide(selector);
            });
            if (options && options.cb) options.cb();
        };
        //取消事件
        me.cancel = function (selector) {
            $(selector).find('.cancel').click(function () {
                cx.PopCom.hide(selector);
            });
        };

        return me;
    })();

    cx.PopAjax = (function () {
        var me = {};
        me.login = function (tigger) {
        	var rfsh = (typeof(loginrfsh)==="undefined") ? 0 : loginrfsh;
            $.ajax({
                type: 'post',
                url: '/pop/getLogin',
                data: {version: version, tigger:tigger, rfsh:rfsh},
                success: function (response) {
                    $('body').prepend(response);
                    cx.PopCom.show('.pop-login');
                    cx.PopCom.close('.pop-login');
                    cx.PopCom.cancel('.pop-login');
                }
            });
        };
        me.register = function () {
            $.ajax({
                type: 'post',
                url: '/pop/getRegister',
                data: {'version': version},
                success: function (response) {
                    $('body').prepend(response);
                    cx.PopCom.show('.pop-register');
                    cx.PopCom.close('.pop-register');
                    cx.PopCom.cancel('.pop-register');
                }
            });
        };
        me.bind = function () {
        	var rfsh = (typeof rfshbind == "undefined") ? 0 : rfshbind;
            $.ajax({
                type: 'post',
                url: '/pop/getBind',
                data: {'version': version, 'rfsh': rfsh},
                success: function (response) {
                    $('body').prepend(response);
                    cx.PopCom.show('.pop-perfect-info');
                    cx.PopCom.close('.pop-perfect-info');
                    cx.PopCom.cancel('.pop-perfect-info');
                }
            });
        };

        return me;
    })();
    
    cx.castCb = function(data, obj, aptype) {
    	switch (obj.ctype) {
    		case 'create':
    			cx.ajax.post({
                    url: "order/create",
                    data: data,
                    success: function(response) {
                    	if ($.inArray(obj.lotteryId, [42, 43]) > -1 && obj.orderType == 4) cx.PopCom.hide($('.pop-pay'));
                    	obj.ctype = 'pay';
                    	obj.fromcreate = 1;
                    	data = response.data;
                    	data.code = response.code;
                    	data.msg = response.msg;
                    	cx.castCb(data, obj, aptype);
                    }
                });
    			break;
    		case 'paysearch':
    			var datas = { orderId: data.orderId };
    			switch (obj.orderType) {
    				case 4:
    					var url = '/hemai/getOrderInfo';
    					datas.buyMoney = data.buyMoney;
    					break;
    				case 5:
    					var url = '/hemai/getGendanInfo';
    					break;                                        
    				case 1:
    					var url = '/chases/info';
    					break;
    				default:
    					var url = '/orders/info';
    					break;
    			}
    			$.ajax({
    		        type: 'post',
    		        url: url,
    		        data: datas,
    		        success: function(response) {
    		        	obj.ctype = 'pay';
    		        	obj.lotteryId = parseInt(response.lid, 10);
    		        	obj.issue = response.issue;
    		        	obj.typeCnName = response.typeCnName;
    		        	response.type = 1;
    		        	switch (obj.orderType) {
    		        		case 4:
    		        			response.money = obj.buyMoney;
    		        			break;
    		        		case 1:
    		        			obj.chaseLength = response.totalIssue;
    		        			obj.betMoney = response.betMoney;
    		        			break;
    		        		default:
    		        			break;
    		        	}
    		        	response.orderId = data.orderId;
    		        	cx.castCb(response, obj, aptype);
    		        }
    			})
    			break;
    		case 'pay':
    			var tip = '付款后，您的订单将会自动分配到空闲的投注站出票', txt = '付款到彩店';
    			if ($.inArray(data.code, [0, 12]) > -1) {
    				switch (obj.orderType) {
		        		case 1:
		        			var datas = {ctype: 'pay', orderType:obj.orderType, chaseId: data.orderId, money: data.money};
		            		var binfo = betInfo.chase(getCnName(obj.lotteryId), obj.betMoney, obj.chaseLength, data.money, data.remain_money);
		        			break;
		        		case 4:
		        			var datas = {ctype: 'pay', orderType:obj.orderType, type:(data.type ? data.type : 0), orderId: data.orderId, money: data.money};
		        			if ($.inArray(obj.lotteryId, [42, 43]) > -1) {
		        				var binfo = betInfo.jc(obj.typeCnName, data.money, data.remain_money, obj.buyMoney, obj.guarantee);
		        			}else {
		        				var binfo = betInfo.number(getCnName(obj.lotteryId), obj.issue, data.money, data.remain_money, obj.buyMoney, obj.guarantee);
		        			}
		        			tip = '付款后，合买订单满员将会自动分配到空闲的投注站出票';
		        			txt = obj.fromcreate !== undefined ? '发起合买' : '参与合买';
		        			break;
		        		case 5:
                             var datas= {ctype: 'pay',orderId:data.orderId, totalMoney:data.totalMoney,puid:data.puid,uid:data.uid,orderType:5,lid:data.lid};
                             var binfo = betInfo.gendan(data.typeCnName,data.buyMoney, data.remain_money,data.totalMoney,data.followType,data.buyMoneyRate,data.buyMaxMoney,data.followTotalTimes);
                             tip= "发起人发起方案时，系统会按定制跟单者的定制时间顺序去认购";
                             txt = '立即定制';
                             if(parseFloat(data.remain_money.replace(/,/g, '')) < parseFloat(data.totalMoney.toString().replace(/,/g, ''))/100){
                            	 tip = '';
                                 txt = '去充值';
                                 data.code = 12;
                             }
                             break;   
		        		case 0:
		        		default:
		        			if(data.redpack !== undefined){
		        				if ($.inArray(obj.lotteryId, [42, 43, 44, 45]) > -1) {
			        				var binfo = betInfo.redpackJc(obj.typeCnName, obj.lotteryId, data.money, data.remain_money, data.redpack);
			        			}else {
			        				var binfo = betInfo.redpackNumber(getCnName(obj.lotteryId), obj.lotteryId, obj.issue, data.money, data.remain_money, data.redpack);
			        			}
		        				var datas = {ctype: 'pay', orderType:obj.orderType, orderId: data.orderId, money: data.money, redpackId:data.redpackId};
		        			} else {
		        				var datas = {ctype: 'pay', orderType:obj.orderType, orderId: data.orderId, money: data.money};
			        			if ($.inArray(obj.lotteryId, [42, 43, 44, 45]) > -1) {
			        				var binfo = betInfo.jc(obj.typeCnName, data.money, data.remain_money);
			        			}else {
			        				var binfo = betInfo.number(getCnName(obj.lotteryId), obj.issue, data.money, data.remain_money);
			        			}
		        			}
			        		var cmoney = parseInt(data.money.replace(/,|\./g, ''));
		        			if(data.redpackId !== undefined){
		        				for (i = 0; i < data.redpack.length; i++) {
		        					if(data.redpack[i].id == data.redpackId){
		        						cmoney = cmoney - parseInt(data.redpack[i].money.replace(/,|\./g, ''));
		        						break;
		        					}
		        				}
		        			}
		        			if(parseInt(data.remain_money.replace(/,|\./g, '')) < cmoney){
		        				tip = '';
		        				txt = '去充值';
		        			} else {
		        				data.code = 0;
		        			}
		        			break;
		    		}
    				obj.binfo = binfo;
    			}
    			delete obj.ctype;
    			if (data.code == 0) {
    				if(data.redpackId !== undefined){
    					if ($.inArray(obj.lotteryId, [cx.Lid.SYXW, cx.Lid.JXSYXW, cx.Lid.HBSYXW, cx.Lid.KS, cx.Lid.JLKS, cx.Lid.JXKS, cx.Lid.KLPK, cx.Lid.CQSSC, cx.Lid.GDSYXW]) > -1) {
    						var href = baseUrl + 'wallet/recharge', target = '_blank';
                		} else {
                			var href = baseUrl+'wallet/directPay?orderId='+data.orderId+'&orderType='+obj.orderType + '&betRedpack=0', target = '_self';
                		}
    				}
    				new cx.Confirm({
                        title: '确认投注信息',
                        content: binfo,
                        input: 0,
                        tip: tip,
                        btns: [{type: 'confirm', txt: txt, href: 'javascript:;', data : {'href' : href, 'target' : target}}],
                        confirmCb: function() {
                        	if(data.redpackId !== undefined){
                        		var redpackId = $("input[name='redpackId']").val() || 0;
                        		datas.redpackId = redpackId;
                        		if($('#mustRecharge').closest('.form-pop-tip').is(":visible")) {
                        			if (target === '_blank') location.reload();
                        			return;
                        		}
                        	}
                			cx.ajax.post({
                        		url: 'order/pay', 
                        		data: datas, 
                        		success: function(response) {
                        			for (i in response) {
                        				data[i] = response[i];
                        			}
                                	data.code = response.code;
                                	data.msg = response.msg;
                        			cx.castCb(data, obj);
                        		}
                        	});
                        }
                    });
    			}else {
    				cx.castCb(data, obj);
    			}
    			break;
    		default:
    			switch (data.code) {
    				case 0:
    				case 200:
    					if ('random' in obj) obj.random();
    	                if(aptype){
    	                	$('.pop-confirm').remove();
    	                	cx.Mask.hide();
    	                }
    	                switch (obj.orderType) {
    	                	case 1:
    	                		var href = baseUrl + 'chases/detail/' + data.orderId;
    	                		break;
    	                	case 4:
    	                		var href = baseUrl + 'hemai/detail/hm' + data.orderId;
    	                		break;
    	                	case 5:
    	                		var href = baseUrl + 'hemai/gdetail/gd' + data.orderId;
    	                		break;                                        
    	                	default:
    	                		var href = baseUrl + 'orders/detail/' + data.orderId;
    	                		break;
    	                }
                        var cancelTxt="再来一单";
                        var tishi='<div class="pop-side"><div class="qrcode"><table><tbody><tr><td><img src="/caipiaoimg/v1.1/img/qrcode-pay.png" width="94" height="94" alt=""></td><td><p><b>扫码下载手机客户端</b>中奖结果早知道</p></td></tr></tbody></table></div></div>';
                        if(obj.orderType == 5) cancelTxt="关闭";
                        if(obj.orderType == 4 && obj.fromcreate === undefined)
                        	tishi = '<div class="pop-side"><a href="/gendan?hmtc" target="_blank" class="link-gg"><img src="/caipiaoimg/v1.1/images/img-popdzgd.jpg" width="100%" alt="定制跟单"></a></div>';                            
    	                new cx.Confirm({
    	                	content: data.msg,
    	                    btns: obj.btns || [{type: 'cancel', txt: cancelTxt}, {type: 'confirm', target: '_blank', txt: '查看详情', href: href}],
    	                    cancelCb: function() {var str = location.href.split("?"); location.href = str[0];},
    	                    confirmCb: function() {var str = location.href.split("?"); location.href = str[0];},
    	    	            append:tishi
    	                });
    					break;
    				case 402:
                        var alertTxt="<i class='icon-font'>&#xe611;</i>"+data.msg+"<br><a class='sub-color fz16' href='/help/index/b2-s2-f3' target='_blank'>什么是限号?</a>";
                        if(obj.orderType == 5) alertTxt="<i class='icon-font'>&#xe611;</i>"+data.msg;
    					new cx.Alert({content:alertTxt});
    					break;
    				case 12:
                        if ($.inArray(parseInt(obj.lotteryId, 10), [cx.Lid.SYXW, cx.Lid.JXSYXW, cx.Lid.HBSYXW, cx.Lid.KS, cx.Lid.JLKS, cx.Lid.JXKS, cx.Lid.KLPK, cx.Lid.CQSSC, cx.Lid.GDSYXW]) > -1 || (obj.orderType == 4 && data.type) || ( data.singleFlag || obj.singleFlag) ) {
                            var href = baseUrl + 'wallet/recharge', target = "_blank";
                        }else {
                            var href = baseUrl+'wallet/directPay?orderId='+data.orderId+'&orderType='+obj.orderType + (data.redpackId !== undefined ? '&betRedpack=' + data.redpackId : ''), target = "_self";
                        }
                        new cx.Confirm({title: '确认投注信息', content: obj.binfo, btns: [{type: 'confirm', txt: '去充值', href: href, target : target, data : {'href' : href, 'target' : target}}]});
                        break;                            
                    case 998:
                    	if(data.singleFlag||obj.singleFlag) {
                    		new cx.Alert({content: data.msg,confirmCb:function(){if(cx._basket_){cx._basket_.removeAll();}}});  
                    	}else{
                    		new cx.Alert({content: data.msg}); 
                        }
                        break;
    				case 3000:
                        new cx.Confirm({
                            content: '<div class="mod-result result-success"><div class="mod-result-bd"><div class="result-txt"><h2 class="result-txt-title">您的登录已超时，请重新登录！</h2></div></div></div>',
                            btns: [{type: 'confirm', txt: '重新登录', href: baseUrl + 'main/login'}]
                        });
    					break;
    				default:
    					if (obj.msgconfirmCb) {
                    		new cx.Confirm({single: data.msg, btns: [{type: 'confirm', txt: '确定', href: href}], confirmCb:obj.msgconfirmCb});
                    	}else if(aptype){
                    		$(aptype).html(data.msg);
                    	}else{
                            if(data.singleFlag||obj.singleFlag) {
                             new cx.Alert({content: data.msg,confirmCb:function(){if(cx._basket_){cx._basket_.removeAll();}}});  
                            }else{
                              new cx.Alert({content: data.msg});  
                            }	
                    	}
    					break;
    			}
    			break;
    	}
    };

    $('body').on('click', '.not-bind-bank', function (e) {
        cx.Alert({
            content: '您尚未绑定银行卡',
            confirm: '去绑银行卡',
            confirmCb: function () {
                location.href = '/safe/bankcard';
            }
        });
        e.stopImmediatePropagation();
    });

    //如何算奖
    $('.howCalculate').click(function (e) {
        $.ajax({
            type: 'post',
            url: '/pop/howCalculate',
            data: {version: version, type: $(this).data('type')},
            success: function (response) {
                $('body').append(response);
                cx.PopCom.show('.pop-bet');
                cx.PopCom.close('.pop-bet');
            }
        });
        e.stopImmediatePropagation();
        e.preventDefault();
    });


    $('.howCalcJCLQ').click(function (e) {
        $.ajax({
            type: 'post',
            url: '/pop/howCalcJCLQ',
            data: {version: version},
            success: function (response) {
                $('body').append(response);
                cx.PopCom.show('.pop-bet');
                cx.PopCom.close('.pop-bet');
            }
        });
        e.stopImmediatePropagation();
        e.preventDefault();
    });

    //如何领奖
    $('.howReceive').click(function (e) {
        $.ajax({
            type: 'post',
            url: '/pop/howReceive',
            data: {version: version},
            success: function (response) {
                $('body').append(response);
                cx.PopCom.show('.pop-bet');
                cx.PopCom.close('.pop-bet');
            }
        });
        e.stopImmediatePropagation();
        e.preventDefault();
    });

    //玩法
    $('.howBet').click(function (e) {
        $.ajax({
            type: 'post',
            url: '/pop/howBet',
            data: {version: version},
            success: function (response) {
                $('body').append(response);
                cx.PopCom.show('.pop-bet');
                cx.PopCom.close('.pop-bet');
            }
        });
        e.stopImmediatePropagation();
        e.preventDefault();
    });

    $('.howBetJCLQ').click(function (e) {
        $.ajax({
            type: 'post',
            url: '/pop/howBetJCLQ',
            data: {version: version},
            success: function (response) {
                $('body').append(response);
                cx.PopCom.show('.pop-bet');
                cx.PopCom.close('.pop-bet');
            }
        });
        e.stopImmediatePropagation();
        e.preventDefault();
    });
    // 查看投注信息
    $('#seeBetShop').on('click', function (e) {
        var shopId = $(this).attr('shop-data');
        $.ajax({
            type: 'post',
            url: '/pop/getBetShop',
            data: {version: version, shopId: shopId},
            success: function (response) {
                $('body').append(response);
                cx.PopCom.show('.betShopInfo');
                cx.PopCom.close('.betShopInfo');
            }
        });
        e.stopImmediatePropagation();
        e.preventDefault();
    });
})