(function() {
    window.cx || (window.cx = {});

    var codes = {
        0: "登录信息有误，请重新输入！"
    };

    var rules = {
    	username_phonenum: {
	        pattern: /^[\d\w_]{3,24}$|^.*[\u4e00-\u9fa5].*$/,
	        anti_charpattern: /[\u4e00-\u9fa5]/,
	        tips: {
	            empty_tip: '请填写手机号或账号',
	            format_tip: '请填写正确的手机号或166彩账号'
	        }
	    },
	    username: {
			cbfun: validUsername,
	    	cbajax: ajaxCheck,
	        tips: {
	            empty_tip: '请填写用户名',
	            format_tip: '支持中文，字母、数字组合，2-24字符',
	            '1': '请输入汉字，字母，数字',
	            '2': '此帐号已被注册，请登录或重新输入',
	            '3': '此账号已被注销'
	        }
	    },
	    phonenum: {
	    	cbajax: ajaxCheck,
	        pattern: /^\d{11}$/,
	        anti_charpattern: /[\u4e00-\u9fa5,a-z]/,
	        tips: {
	            empty_tip: '请输入手机号',
	            format_tip: '请输入本人手机号码',
	            error_tip: '请输入正确的手机号码',
	            '1': "该手机号已注册，您可以换绑手机或<a href='/main/login'>立即登录</a>",
	            '2': "该手机号已被注销"
	        }
	    },
	    wechat: {
	    	cbajax: ajaxCheck,
	        pattern: /^\d{11}$/,
	        anti_charpattern: /[\u4e00-\u9fa5,a-z]/,
	        tips: {
	            empty_tip: '请输入手机号',
	            format_tip: '请输入本人手机号码',
	            error_tip: '请输入正确的手机号码',
	            '1': "该手机号已绑定了其他微信账号，请更换手机号码",
	            '2': "该手机号已被注销"
	        }
	    },
	    lxcheck: {
	    	cbajax: ajaxCheck,
	        pattern: /^\d{11}$/,
	        anti_charpattern: /[\u4e00-\u9fa5,a-z]/,
	        tips: {
	            empty_tip: '请输入手机号',
	            format_tip: '请输入正确的手机号码',
	            '0': "手机号不存在",
	            '1': "您的手机号已注册，去<a href='/main/login' target='_blank'>登录</a>",
	            '2': "您的手机号已参与过此活动，快去<a href='/main/register' target='_blank'>注册</a>领取红包吧"
	        }
	    },
	    findpwd: {
	    	cbajax: ajaxCheck,
	        pattern: /^\d{11}$/,
	        anti_charpattern: /[\u4e00-\u9fa5,a-z]/,
	        tips: {
	            empty_tip: '请输入手机号',
	            format_tip: '请输入正确的手机号码',
	            '1': '手机号不存在'
	        }
	    },
	    modifyPhone: {
	    	cbajax: ajaxCheck,
	        pattern: /^\d{11}$/,
	        anti_charpattern: /[\u4e00-\u9fa5,a-z]/,
	        tips: {
	            empty_tip: '请输入手机号',
	            format_tip: '请输入正确的手机号码',
	            '1': '手机号已被注册'
	        }
	    },
	    nickname: {
	        pattern: /^[\d\w!@#\$%\^&\*\(\)\[\]\{\}\/\:\;\"\'<>,\.\?\-\|\u4e00-\u9fa5]{1,36}$/,
	        tips: {
	            empty_tip: '请输入昵称',
	            format_tip: '请输入1-36位长度的昵称'
	        }
	    },
	    email: {
	    	cbajax: ajaxCheck,
	        pattern: /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/,
	        tips: {
	            empty_tip: '请输入邮箱地址',
	            format_tip: '请输入正确的邮箱地址',
	            '1': '该邮箱已被绑定，请绑定其他邮箱'
	        }
	    },
	    same_email: {
	    	cbajax: ajaxCheck,
	        pattern: /^(\w)+(\.\w+)*@(\w)+((\.\w+)+)$/,
	        tips: {
	            empty_tip: '请输入邮箱地址',
	            format_tip: '请输入正确的邮箱地址',
	            '1': '原邮箱地址不正确'
	        }
	    },
	    identification: {
	    	cbajax: ajaxCheck,
	        pattern: /^[\d]{17}[\dxX]{1}$/,
	        tips: {
	            empty_tip: '请输入身份证号',
	            format_tip: '请输入真实的身份证号',
	            '1': '该身份证已被绑定5个账号'
	        }
	    },
	    sameidcard: {
	    	cbajax: ajaxCheck,
	        pattern: /^[\d]{17}[\dxX]{1}$/,
	        tips: {
	            empty_tip: '请输入身份证号',
	            format_tip: '请输入真实的身份证号',
	            '1': '证件号不一致'
	        }
	    },
	    checkcode: {
			cbajax: ajaxCheck,
	        pattern: /^[0-9a-zA-Z]{4}$/,
	        tips: {
	            empty_tip: '请输入验证码',
	            format_tip: '请输入4位验证码',
				'1': '验证码有误，请重新输入'
	        }
	    },
	    checkrsgcode: {
			cbajax: rsgajaxCheck,
	        pattern: /^[0-9a-zA-Z]{4}$/,
	        tips: {
	            empty_tip: '请输入验证码',
	            format_tip: '请输入4位验证码',
				'1': '请输入图片上的字母'
	        }
	    },
	    checkmsgcode: {
	    	cbajax: ajaxCheck,
	        pattern: /^[0-9a-zA-Z]{6}$/,
	        tips: {
	            empty_tip: '请输入验证码',
	            format_tip: '请输入6位验证码',
	            '1': '验证码有误，请重新输入'
	        }
	    },
	    checkecode: {
	        tips: {
	            empty_tip: '请输入验证码'
	        }
	    },
	    password: {
			cbfun: validPassword,
			cbajax: ajaxCheck,
			keep: true,
			min_len: 6,
			max_len: 16,
	        //pattern: /^[\d\w!@#\$%\^&\*\(\)\[\]\{\}\/\:\;\"\'<>,\.\?\-\|]{6,16}$/,
	        tips: {
	            empty_tip: '请输入密码',
	            format_tip: '支持字母、数字、符号的组合，6-16个字符',
				min_len_tip: '最少6个字符',
				max_len_tip: '最多16个字符',
				'1': '该密码和登录密码相同'
	        }
	    },
	    recharge_money: {
	        min: 1,
	        pattern: /^\d+$/,
	        tips: {
	            min_tip: '至少充值1元以上整数',
	            empty_tip: '请选择充值金额',
	            format_tip: '请输入整数金额'
	        }
	    },
	    withdraw_money: {
            min: 10,
            pattern: /^\+?([0-9]+|[0-9]+\.[0-9]{0,2}|\.[0-9]{0,2})$/,
            tips: {
                min_tip: '单笔提现金额最少为10元',
                empty_tip: '请输入提款金额',
                format_tip: '请输入正确的提款金额'
            }
	    },
	    bankcard: {
	        pattern: /^\d{16,19}$/,
	        tips: {
	            empty_tip: '请输入正确的银行卡号',
	            format_tip: '请输入正确的银行卡号'
	        }
	    },
	    chinese: {
	    	cbajax: ajaxCheck,
	        pattern: /^[\u4e00-\u9fa5·]{2,10}$/,
	        tips: {
	            empty_tip: '请输入正确的中文名',
	            format_tip: '请输入正确的中文名',
	            '1': '与实名认证信息不一致'
	        }
	    },
	    qq: {
	        pattern: /^\d{5,10}$|^\s*$/,
	        tips: {
	            format_tip: '请输入正确QQ号'
	        }
	    },
	    number: {
	        pattern: /\d+/
	    },
	    required: {
	        pattern: /.+/
	    },
	    same: {
	        tips: {
				empty_tip: '请再次输入密码',
				format_tip: '再输一次上面的内容'
	        }
	    }
    };

    var vform = cx.vform = function(selector, options) {
        options || (options = {});
        this.$form = $(selector);
        this.$tip = this.$form.find('.tip');
        this.$eles = this.$form.find('.vcontent');
        this.callback = options.callback || null;
        this.errorCallback = options.errorCallback || null;
        this.submit = options.submit || null;
        this.renderTip = this[options.renderTip] || this.renderTip;
        this.sValidate = this[options.sValidate] || this.validateAll;
        this.checklogin = options.checklogin || false;
        this.data = {};
        this.tip = null;

        this.init();
    };

    vform.prototype = {
        init: function() {
            var self = this;
            this.$eles.filter("[data-noblur!=1]").blur(function() {
                if ($(this).attr('readonly') == 'readonly') {
                    return ;
                }
                self.validate(this);
            });
            this.$eles.filter("[data-noblur!=1]").focus(function() {
                if ($(this).attr('readonly') == 'readonly') {
                    return ;
                }

                self.showtips(this);
            });

            this.$form.on('click', '.submit', function() {
            	if (self.checklogin && !$.cookie('name_ie')) {
            		cx.PopAjax.login(1);
                	return ;
                }
                if (self.sValidate()) {
                    if ($.isFunction(self.callback)) {
                        self.callback();
                    }
                } else {
                    if ($.isFunction(self.errorCallback)) {
                        self.errorCallback();
                    }
                }
            });
            this.$eles.keypress(function(e) {
            	if (self.checklogin && !$.cookie('name_ie')) {
            		cx.PopAjax.login(1);
                	return ;
                }
                if (e.which == 13) {
                    if (self.sValidate()) {
                        if ($.isFunction(self.callback)) {
                            self.callback();
                        }
                    } else {
                        if ($.isFunction(self.errorCallback)) {
                            self.errorCallback();
                        }
                    }
                }
            });
        },

        renderTip: function(tip) {
            if (tip == '') {
                this.tip.hide().html('');
            } else {
                this.tip.removeClass('hidden').show().html(tip);
                this.tip.parent().removeClass('hidden').show();
            }
        },
		renderTips: function(tip, container, status){
			this.tip = container || this.tip;
			status = status || (tip == '' ? 'ok' : 'error')  ;
			this.tip.closest('.form-tip').removeClass('form-tip-error form-tip-true');
			this.tip.closest('.nform-tip').removeClass('nform-tip-error form-tip-true');
			if (status == 'ok') {
				this.tip.closest('.form-tip').addClass('form-tip-true').removeClass('hide');
				this.tip.closest('.nform-tip').addClass('nform-tip-true').removeClass('hide');
			} else {
				this.tip.closest('.form-tip').addClass('form-tip-error').removeClass('hide');
				this.tip.closest('.nform-tip').addClass('nform-tip-error').removeClass('hide');
			}

			if( tip == '' ){
				this.tip.hide().html('');
			} else {
				this.tip.show().html(tip);
			}
		},

        setcontainer: function(ele){
        	var tip = null;
        	if(this.$tip.length >= 1)
        	{
        		this.$tip.each(function(){
        			if($(this).hasClass($(ele).attr('name'))){
        				tip = this;
        			}
        		});
        	}
        	this.tip = $(tip);
        },

        validate: function(ele, close) {
            var val, ruleName, rule, ajaxCheck, encrypt;
            val = $.trim(this.$form.find(ele).val());
            ruleName = $(ele).data('rule');
            ajaxCheck = $(ele).attr('data-ajaxcheck');
            encrypt = $(ele).data('encrypt');
            this.setcontainer(ele);
            if($(ele).is(':hidden') && $(ele).attr('type') != 'hidden'){
            	return true;
            }
            if ($(ele).attr('type') == 'checkbox') {
                if ($(ele).attr('checked') != 'checked') {
					if( ruleName ){
						this.renderTip('请同意用户协议');
						return false;
					}
					val = '';
					$(ele).val('');
                } else {
                	val = 1;
					$(ele).val('1');
				}
            }

            var name = $(ele).attr('name');
            if (name != '') {
                this.data[name] = val;
                if(encrypt == '1'){
                	 var self = this;
	           		 this.data[name] = cx.rsa_encrypt( val );
	           		 if(!this.data['encrypt']){
	           			 this.data['encrypt'] = '';
	           		 }
	           		 if(this.data['encrypt'].indexOf(name) == -1)
	           			 this.data['encrypt'] += name + '|';
            	}
            }

            if (ruleName == 'same') {
                var withVal = this.data[$(ele).data('with')];
                if (this.data[name] != withVal) {
                    this.renderTip('两次输入不一致');
                    return false;
                }
            }
            if (ruleName in rules) {
                rule = rules[ruleName];
				if( rule.tips && rule.tips.empty_tip && val === '' ){
					this.renderTip(rule.tips.empty_tip);
					return false;
				}

                if ('min_len' in rule) {
                    if ( val.length < rule.min_len ) {
                        if (rule.tips) {
                            this.renderTip(rule.tips.min_len_tip);
                        } else {
                            this.renderTip($(ele).data('tip'));
                        }
                        return false;
                    }
                }

                if ('max_len' in rule) {
                    if ( val.length > rule.max_len ) {
                        if (rule.tips) {
                            this.renderTip(rule.tips.max_len_tip);
                        } else {
                            this.renderTip($(ele).data('tip'));
                        }
                        return false;
                    }
                }

                if (rule.pattern) {
                    if (!rule.pattern.test(val)) {
                        if (rule.tips) {
                            this.renderTip(rule.tips.error_tip || rule.tips.format_tip);
                        } else {
                            this.renderTip($(ele).data('tip'));
                        }
                        return false;
                    }
                }
                if ('min' in rule) {
                    val = parseFloat(val, 10);
                    if (val < rule.min) {
                        this.renderTip(rule.tips.min_tip);
                        return false;
                    }
                }
                if ('max' in rule) {
                    val = parseFloat(val, 10);
                    if (val > rule.max) {
                        this.renderTip(rule.tips.max_tip);
                        return false;
                    }
                }

				if( rule.cbfun && $.isFunction(rule.cbfun) ) {
					if( !rule.cbfun.call(this, val, this.renderTip) ){
						return false;
					}
				}
				if(ajaxCheck == '1' && close != '1'){
					var disable = $('.' + $(ele).data('freeze'));
					if( rule.cbajax && $.isFunction(rule.cbajax) ) {
						var datas = name + '=' + this.data[name];
						if(typeof(this.data['encrypt']) != "undefined")
						{
							datas += '&encrypt=' + this.data['encrypt'];
						} 
						var result = false;
						result = rule.cbajax.call(this, datas);
						if( result ){
							this.renderTip(rule.tips[result]);
							if(disable){
								disable.addClass('disabled');
								disable.addClass('lnk-getvcode-disb');
							}
							if(name == 'username'){
								$(".lnk-getvcode").addClass('lnk-getvcode-disb');
							}
							return false;
						}
						if(disable){
							disable.removeClass('disabled');
							disable.removeClass('lnk-getvcode-disb');
						}
						if($(".lnk-getvcode").hasClass('lnk-getvcode-disb')){
							$(".lnk-getvcode").removeClass('lnk-getvcode-disb');
						}
					}
				}
            }
			
			if( rule && !('keep' in rule) ){
	            this.renderTip('');
			}

            return true;
        },
        validateAll: function() {
            var $el, self = this;
            this.$form.find('.vcontent').each(function(i, e){
            	$el = $(this);
                if (!self.validate($el)) {
                    return false;
                }
            })

            if (this.submit) {
                this.submit.apply(this, [this.data]);
            }
            return true;
        },
        submitValidate: function() {
        	var $el;
            for (var i = 0; i < this.$eles.length; ++i) {
                if($(this.$tip[i]).closest('.form-tip').hasClass('form-tip-error') || $(this.$tip[i]).closest('.nform-tip').hasClass('nform-tip-error')){
                	//已验错误直接return
        			return false;
        		}else{
        			$el = $(this.$eles[i]);
        			if (!this.validate($el, 1)) {
                        return false;
                    }
        		}
            }
        	if (this.submit) {
                this.submit.apply(this, [this.data]);
            }
            return true;
        },
        showtips: function(ele){
            ruleName = $(ele).data('rule');
            if(ruleName){
	            name = $(ele).attr('name');
	            if (ruleName in rules) {
	            	var $tip = $('.' + name);
	            	var el = $tip.closest('.form-tip').length > 0 ? $tip.closest('.form-tip') : ($tip.closest('.nform-tip').length > 0 ? $tip.closest('.nform-tip') : null);
	            	if (el && el.data('formattip')) {
	            		$tip.html(el.data('formattip')).show();
	            	}else {
	            		rule = rules[ruleName];
		                $tip.html(rule.tips.format_tip).show();
	            	}
	                $tip.closest('.form-tip').removeClass('form-tip-error form-tip-true hide');
	                $tip.closest('.nform-tip').removeClass('nform-tip-error form-tip-true hide');
	            }
            }
        }
    };

	function validPassword( pass, renderTip ){
		var self = this;
		function passwordGrade(pass)
		{
			function calsgrade(pwd){
				var score = 0;
				var regexArr = ['[0-9]', '[a-z]', '[A-Z]', '[\\W_]'];
				var repeatCount = 0;
				var prevChar = '';
				var len = pwd.length;
				score += len > 18 ? 18 : len;
				for (var i = 0, num = regexArr.length; i < num; i++) { if (eval('/' + regexArr[i] + '/').test(pwd)) score += 4; }
				for (var i = 0, num = regexArr.length; i < num; i++) {
					if (pwd.match(eval('/' + regexArr[i] + '/g')) && pwd.match(eval('/' + regexArr[i] + '/g')).length >= 2) score += 2;
					if (pwd.match(eval('/' + regexArr[i] + '/g')) && pwd.match(eval('/' + regexArr[i] + '/g')).length >= 5) score += 2;
				}
				for (var i = 0, num = pwd.length; i < num; i++) {
					if (pwd.charAt(i) == prevChar) repeatCount++;
					else prevChar = pwd.charAt(i);
				}
				score -= repeatCount * 1;
				return score;
			}

			function checkPassSame(pass)
			{
				var first = pass.substring(0,1);
				var exp = new RegExp('^'+first+'+$');
				if(exp.test(pass))
				{
					return false;
				}
				if (first == 'a' || first == 'A')
				{
					f = pass.charCodeAt(0);
					for(i = 1; i < pass.length; i++)
					{
						tmp = pass.charCodeAt(i);
						if (tmp - f != i)
						{
							return true;
						}
					}
					return false;
				}
				return true;
			}

			var pgrade = 0;
			var score = 0;

			if (pass == '123456' || pass == '654321' || pass == '111222' || checkPassSame(pass) == false)
			{
				score = 0;
			}
			else
			{
				score = calsgrade(pass);
			}
			
			if (score <= 10)
			{
				pgrade = 1;
			}
			else if (score >= 11 && score <= 20)
			{
				pgrade = 2;
			}
			else if (score >= 21 && score <= 30)
			{
				pgrade = 3;
			}
			else
			{
				pgrade = 4;
			}
			return pgrade;
		}

		function passwordRender( pgrade ){
			var str = '';
			var i = 0;
			var gtips = {1 : '弱', 2 : '中', 3 : '强', 4 : '极佳'};
			str = '<div class="pwd_streng pwd_streng_' + pgrade + '" >';
			for( i in gtips ){
				if( i <= pgrade ){
					str += '<i class="on"></i>';
				} else {
					str += '<i></i>';
				}
			}
			str += '<em class="streng_field">' + gtips[pgrade] + '</em>';
			str += '</div>';
			return str;
		}

		var renderStr = '';
		if( pass == self.$form.find('input[name="username"]').val() ){
			renderStr = '<div>密码不能与帐号一致，请重新输入</div>';
			renderTip.call(self, renderStr, null, 'error' );
			return false;
		}

		var pgrade = passwordGrade(pass);
		var gradeStr = passwordRender( pgrade );
		
		if( pgrade < 2 ){
			renderStr = gradeStr;
			renderTip.call(self, renderStr, null, 'error' );
			return false; 
		}else{
			renderStr = '<span>密码状态</span>' + gradeStr;
			renderTip.call(self, renderStr, null, 'ok' );
			return true;
		}
	}
	
	function validUsername( username, renderTip ){
		var self = this;
		// 判断字符是否超过, 或少于规定长度
		username = $.trim(username);
		if (username.length < 2)
		{
			renderTip.call(self, '最少1个汉字或2个字符', null, 'error' );
			return false;
		}else if (username.replace(/[^\x00-\xff]/g, "**").length > 24)
		{
			renderTip.call(self, '请不要超过12个汉字或24个字符', null, 'error' );
			return false;
		}else if (/^[0-9]*$/.test(username)){
			renderTip.call(self, '用户名不能是纯数字，请重新输入', null, 'error' );
			return false;
		}else if (/[^\u4E00-\u9FA5\w_@\.\-]/.test(username))
		{
			renderTip.call(self, '请输入汉字，字母，数字', null, 'error' );
			return false;
		}
		return true;
	}

	function ajaxCheck(datas){
		var self = this;
		var result = 0;
		$.ajax({
	        type: "POST",
	        url: "/mainajax/uinfoCheck",
	        data: datas,
	        dataType: "json",
	        async: false,
	        success: function(response){
				result = response;
				if (result == 1) {
					recaptcha_reg();
				}
			}
        });
		return result;
	}
	
	function rsgajaxCheck(datas) {
		var self = this;
		var result = 0;
		$.ajax({
	        type: "POST",
	        url: "/mainajax/uinfoCheck",
	        data: datas,
	        dataType: "json",
	        async: false,
	        success: function(response){
				result = response;
				if (result == 0) {
					closeTimer(1);
				}else {
					recaptcha_reg();
				}
			}
        });
		return result;
	}
	
})();