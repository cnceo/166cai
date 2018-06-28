
(function() {
    window.cx || (window.cx = {});

    var Counter = cx.Counter = function(options) {
        this.start = options.start;
        this.stop = options.stop || 0;
        this.step = options.step || 1000;

        this.timer = null;
    };

    Counter.prototype.countDown = function(cb, fcb) {
        var self = this;
        if (self.start <= self.stop) {
            if ($.isFunction(fcb)) {
                setTimeout(fcb, 1000); //11选5
            }
            return ;
        }else{
        	if(!cx.closeCount){
	        	var dialogs = $('.pop-alert');
	            if(dialogs){
	            	dialogs.each(function(){
	            		var dialog = $(this);
	            		dialog.find('.btn-confirm').trigger('click');
	            	})
	            }
        	}
        }
        this.timer = setInterval(function() {
            if ($.isFunction(cb)) {
                cb(self.start);
            }
            self.start -= self.step;
            if (self.start < self.stop) {
            	if(!cx.closeCount){
            		new cx.Alert({
                        content: '期号更新中，请稍等！',
                        confirmCb: function(){
                        	location.href = location.href;
                    	}
                	});
            	}
                clearInterval(self.timer);
                if ($.isFunction(fcb)) {
                    fcb();
                }
            }
        }, 1000);
    };

    var Money = cx.Money = (function() {
        var me = {};

        me.format = function(money) {
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

        me.round = function(money) {
            if (!money) {
                return '0';
            }
            money = Math.round(money * 10000 / 100) / 100;
            return money;
        };

        return me;
    })();

    var Datetime = cx.Datetime = (function() {
        var me = {};
        /*var Time = {
            SECOND: 1,
            MIN: 60,
            HOUR: 3600,
            DAY: 24 * 3600
        };*/
        var Time = {
            SECOND: 1000,
            MIN: 60000,
            HOUR: 3600000,
            DAY: 24 * 3600000
        };

        me.getWeekDay = function(timestamp) {
            return '星期' + ['日', '一', '二', '三', '四', '五', '六'][new Date(timestamp).getDay()];
        };

        me.formatTime = function(timestamp) {
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

        me.format = function(timestamp, options) {
            var datetime = new Date(timestamp);
            var tpl = '';
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

        me.padDate = function(str) {
            return ('0' + str).slice(-2);
        };

        me.getToday = function() {
            var datetime = new Date();
            var year = datetime.getFullYear();
            return year + padd((datetime.getMonth() + 1)) + padd(datetime.getDate());
        };

        return me;
    })();

    var Utils = cx.Utils = (function() {
        var me = {};

        me.padding = function(str, len, ph) {
            if (len <= str.length) {
                return str;
            }
            var padLen = len - str.length;
        };

        return me;
    })();

    $(function() {
        cx.Mask = (function() {
            var me = {};
            var $mask = $(".pop-mask:not('.pop-mask-guide')");
            var $iframeMask = $('.popIframe');
            me.show = function() {
                $mask.height($(document).height()).removeClass('hidden');
                $iframeMask.css({ height: $(document).height(), width: '100%'}).removeClass('hidden'); //设置iframe遮罩层宽高
            };
            me.hide = function() {
    			if( $('.pub-pop:visible').length < 1 ) {
    				$mask.addClass('hidden');			
                    $iframeMask.addClass('hidden');
    			}
            };

            return me;
        })();

    });


    // alert 弹层
    var Alert = cx.Alert = function(options) {
		var title = options.title || '提示';
		var confirm = options.confirm || '确定';
        this.tpl = '<div class="pub-pop pop-alert"><div class="pop-in"><div class="pop-head"><h2>' + title + '</h2><span class="pop-close" title="关闭">关闭</span></div><div class="pop-body"><div class="tac fz18 pt10 yahei c333">' + options.content + '</div></div><div class="btn-group"><a href="javascript:;" target="_self" class="btn btn-confirm">' + confirm + '</a></div></div></div>';
        cx.Mask.show();
        $('body').append(this.tpl);
        this.$el = $('.pop-alert');
        var self = this;
        if($.browser.msie && $.browser.version == '6.0'){
            //如果是IE6 就不添加MarginTop样式
            self.$el.css({marginLeft : (- $(self.$el).width()/2)});
        }else{
            if($.browser.msie && $.browser.version == '7.0'){
                //如果是IE7 给Box添加宽度
                self.$el.css({width: $(self.$el).width()}); 
            }
            self.$el.css({
                marginTop : (- $(self.$el).height()/2), 
                marginLeft : (- $(self.$el).width()/2)
            });
        }
        self.$el.show();
        var self = this;
        // 点击确认按钮-关闭弹窗
        this.$el.find('.btn-confirm').click(function() {
            self.$el.remove();
            cx.Mask.hide();
            if ($.isFunction(options.confirmCb)) {
                options.confirmCb();
            }
			return false;
        });

        // 点击关闭按钮-关闭弹窗
        this.$el.find('.pop-close').click(function() {
            // self.$el.addClass('hidden').one('transitionend webkitTransitionEnd oTransitionEnd MSTransitionEnd', function() {
            //     self.$el.remove();
            // });
            self.$el.remove();
            cx.Mask.hide();
            if(!options.addtion){
                if ($.isFunction(options.confirmCb)) {
                    options.confirmCb();
                }
            }          
        });
    };

    var Confirm = cx.Confirm = function(options) {
        var btns = [
            {
                type: 'confirm',
                href: 'javascript:;',
                txt: '确定'
            },
            {
                type: 'cancel',
                href: 'javascript:;',
                txt: '取消'
            }
        ];
        var inputs = [
            {
            	title : '支付密码:',
            	name  : 'pay_pwd',
            	type  : 'password',
            	idname: 'pay_pwd',
            	cname : 'form-item-ipt'
            }
        ];
        if (options.btns) {
            btns = options.btns;
        }
        var btn;
        var content = '';
		var single = ''
		if( options.single ){
			single = '<p class="tac fz18 pt10 yahei c333">' + options.single + '</p>';
		}
        if(options.input){
        	input = inputs[options.input - 1];
        	content = '<li class="form-item"><label class="form-item-label">' + input.title + '</label><div class="form-item-con"><input data-encrypt="1" type="' + input.type + '" class="' + input.cname + '" id="' + input.idname + '" value="" autocomplete="off" /><div class="pwderror form-tip-error"></div></li></ul>'; 
        }
        this.tpl = '<div class="pub-pop pop-confirm"><div class="pop-in"><div class="pop-head"><h2>提示</h2><span class="pop-close" title="关闭">关闭</span></div><div class="pop-body">' + single + (options.content || '') +  content + '</div>' 
         + '<div class="pop-action-area tac">';
        
        for (var i = 0; i < btns.length; ++i) {
            btn = btns[i];
            this.tpl += '<a target="' + (btn.target ||  '_self') + '" '
            if (btn.href) {
                this.tpl += 'href="' + btn.href + '"';
            }
            this.tpl += ' class="btn btn-' + btn.type + '">' + btn.txt + '</a>';
        }
        
        this.tpl += '</div></div></div>';
        cx.Mask.show();
        $('body').append(this.tpl);

        this.$el = $('.pop-confirm');
        var self = this;
        if($.browser.msie && $.browser.version == '6.0'){
            //如果是IE6 就不添加MarginTop样式
            self.$el.css({marginLeft : (- $(self.$el).width()/2)});
        }else{
            if($.browser.msie && $.browser.version == '7.0'){
                //如果是IE7 给Box添加宽度
                self.$el.css({width: $(self.$el).width()}); 
            }
            self.$el.css({
                marginTop : (- $(self.$el).height()/2), 
                marginLeft : (- $(self.$el).width()/2)
            });
        }
         self.$el.show();
        this.$el.find('.btn-confirm').click(function(e) {
            if (options.confirmCb) {
                options.confirmCb();
            }
            if(!options.input){
            	self.$el.remove();
                cx.Mask.hide();
            }
        });
        var self = this;
        this.$el.find('.btn-cancel').click(function(e) {
            if (options.cancelCb) {
                options.cancelCb();
            }
            self.$el.remove();
            cx.Mask.hide();
        });
        this.$el.find('.pop-close').click(function() {
            if (options.cancelCb) {
                options.cancelCb();
            }
            self.$el.remove();
            cx.Mask.hide();
        });
    };

    /*
     * trigger
     * selector
     * confirmCb
     */
    var Dialog = cx.Dialog = function(options) {
        this.$trigger = $(options.trigger);
        if ($(options.selector).length > 0) {
            this.$dialog = $(options.selector);
        } else {
            // append
            $('body').append(this.tpl);
            this.$dialog = $(options.selector);
        }
    };

    Dialog.show = function() {
        var self = this;
        this.$trigger.bind('click', function() {
            self.$dialog.show();
        });
    };

    Dialog.alert = function() {
    };

    Dialog.confirm = function() {
        var self = this;
        this.$dialog.find('.confirm').bind('click', function() {
            self.$dialog.remove();
            if ($.isFunction(self.confirmCb)) {
                self.confirmCb();
            }
        });
    };

    var AdderSubtractor = cx.AdderSubtractor = function(selector, options) {
        this.$el = $(selector);
        options = options || {};
        this.value = options['default '] || 1;
        this.step = options.step || 1;
        this.min = options.min || 1;
        this.cb = null;
        this.plusCb = null;
        this.minusCb = null;

        this.init();
    };

    AdderSubtractor.prototype = {
        init: function() {
            var self = this;
            var max = self.$el.find('.plus').attr('data-max') || 0;
			this.$el.find('.number').blur(function(e){
				var $this = $(this);
				if( !self.value ){
					self.value = 1;
					$this.val(1);
				}
				if(max){
                	if(parseInt(self.value) >= parseInt(max)){
                		$this.val(50);
                        self.value = 50;
                	}
                }
				self.cb && self.cb.apply(self);
			});
            this.$el.find('.number').val(this.value);
            this.$el.find('.plus').click(function() {
            	if(max){
            		if(parseInt(self.value) >= parseInt(max)){
            			self.value = max;
            		}else{
            			self.value += self.step;
            		}
            	}else{
            		self.value += self.step;
            	}
                self.render();
                self.plusCb && self.plusCb.apply(self);
                self.cb && self.cb.apply(self);
            });
            this.$el.find('.minus').click(function() {
                if (self.value == self.min) {
                    return ;
                }
                self.value -= self.step;
                self.render();
                self.minusCb && self.minusCb.apply(self);
                self.cb && self.cb.apply(self);
            });
            this.$el.find('.number').keyup(function(e) {
                var $this = $(this);
                var val = $this.val();
                if(max){
                	if(parseInt(val) >= parseInt(max)){
                		val = 50;
                		$this.val(50);
                        self.value = 50;
                	}
                }
                if ((e.which >= 48 && e.which <= 57) || e.which == 8 || (e.which >=96 && e.which <= 105)) {
                    if (val == '') {
                        //$this.val(1);
                        self.value = 0;
                    } else {
                        self.value = parseInt(val, 10);
                    }
                    self.cb && self.cb.apply(self);
                } else {
                    if (!(/^\d+$/.test(val))) {
                        $this.val(1);
                        self.value = 1;
                        //$this.val(val.slice(0, val.length - 1));
                    }
					self.cb && self.cb.apply(self);
                }
            });
        },
        setCb: function(cb) {
            this.cb = cb;
        },
        setPlusCb: function(cb) {
            this.plusCb = cb;
        },
        setMinusCb: function(cb) {
            this.minusCb = cb;
        },
        render: function() {
            this.$el.find('.number').val(this.value);
        },
        getValue: function() {
            return this.value;
        }
    };

    Dialog.close = function() {
        var self = this;
        this.$dialog.find('.pop-close').bind('click', function() {
            self.$dialog.remove();
        });
    };

    var ajax = cx.ajax = (function() {
        var me = {};
        var success;
        var locks = {};

        me.get = function(options) {
            var url = baseUrl + 'ajax/get';
            options.data || (options.data = {});
            var data = options.data;
            data['url'] = options.url;
            return $.ajax({
                url: url,
                data: data,
                success: function(response) {
                    if ('success' in options) {
                        options.success(response);
                    }
                }
            });
        }

        me.post = function(options) {
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
                    success: function(response) {
                        options.success(response);
                    },
                    complete: function() {
                        locks[options.url] = false;
                    }
                });
            }
            return false;
        };

        return me;
    })();

    cx.url = (function() {
        var me = {};
        var busiBase = G.busiUrl;
        var passBase = G.passUrl;
        var payBase = G.payUrl;
        var cmsBase = G.cmsUrl;

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

        me.getBusiUrl = function(action, params) {
            return _urlFactory(busiBase, action, params);
        };

        me.getPassUrl = function(action, params) {
            return _urlFactory(passBase, action, params);
        };

        me.getPayUrl = function(action, params) {
            return _urlFactory(payBase, action, params);
        };

        me.getCmsUrl = function(action, params) {
            return _urlFactory(cmsBase, action, params);
        };
        me.getBaseUrl = function(action, params) {
        	return _urlFactory(baseUrl, action, params);
        }

        return me;
    })();

})();

$(function() {
    $('.placeholder').each(function(key, el) {
        var $el = $(el);
        if ($el.data('placeholder') == '') {
            return ;
        }
        var val = $el.val();
        var placeholder = $el.data('placeholder');
        if (val == '') {
            $el.val(placeholder);
        }
    });
    var hasClass = $('.home-banner').children('a').hasClass('selected');
    $('.home-banner').hover(function() {
        $(this).children('a').addClass('selected');
        $(this).find('.index_app_menu').show();
    }, function() {
        if (!hasClass) {
            $(this).children('a').removeClass('selected');
        }
        $(this).find('.index_app_menu').hide();
    });
    var $userMoney = $('#user-money');
    if ($userMoney.length > 0) {
        cx.ajax.get({
            url: cx.url.getPayUrl('wallet/query'),
            data: {
                isToken: 1
            } ,
            success: function(response)  {
                if (response.code == 0) {
                    $userMoney.html(cx.Money.format(response.data.amount));
                }
            }
        });
    }
});
