(function() {
    window.cx || (window.cx = {});

    var codes = {
        0: "��¼��Ϣ�������������룡"
    };

    var rules = {
    	username_phonenum: {
	        pattern: /^[\d\w_]{3,24}$|^.*[\u4e00-\u9fa5].*$/,
	        anti_charpattern: /[\u4e00-\u9fa5]/,
	        tips: {
	            empty_tip: '����д�ֻ��Ż�2345�˺�',
	            format_tip: '����д��ȷ���ֻ��Ż�2345�˺�'
	        }
	    },
	    username: {
			cbfun: validUsername,
	    	cbajax: ajaxCheck,
	        tips: {
	            empty_tip: '����д2345�˺�',
	            format_tip: '2-24���ַ�(֧�ֺ��֡���ĸ�����ֺ������ַ)',
	            '1': '���ʺ��ѱ�ע�ᣬ���¼����������',
	            '2': '����˺Ű������дʣ��뻻һ����'
	        }
	    },
	    phonenum: {
	    	cbajax: ajaxCheck,
	        pattern: /^\d{11}$/,
	        anti_charpattern: /[\u4e00-\u9fa5,a-z]/,
	        tips: {
	            empty_tip: '�������ֻ���',
	            format_tip: '��������ȷ���ֻ�����',
	            '1': '���ֻ����ѱ��󶨹�'
	        }
	    },
	    nickname: {
	        pattern: /^[\d\w!@#\$%\^&\*\(\)\[\]\{\}\/\:\;\"\'<>,\.\?\-\|\u4e00-\u9fa5]{1,36}$/,
	        tips: {
	            empty_tip: '�������ǳ�',
	            format_tip: '������1-36λ���ȵ��ǳ�'
	        }
	    },
	    email: {
	    	cbajax: ajaxCheck,
	        pattern: /^\w+[\w]*@[\w]+\.[\w]+$/,
	        tips: {
	            empty_tip: '�����������ַ',
	            format_tip: '��������ȷ�������ַ',
	            '1': '�������ѱ��󶨹�'
	        }
	    },
	    same_email: {
	    	cbajax: ajaxCheck,
	        pattern: /^\w+[\w]*@[\w]+\.[\w]+$/,
	        tips: {
	            empty_tip: '�����������ַ',
	            format_tip: '��������ȷ�������ַ',
	            '1': 'ԭ�����ַ����ȷ'
	        }
	    },
	    identification: {
	    	cbajax: ajaxCheck,
	        pattern: /^[\d]{17}[\dxX]{1}$/,
	        tips: {
	            empty_tip: '���������֤��',
	            format_tip: '��������ʵ�����֤��',
	            '1': '�����֤�ѱ��󶨹�'
	        }
	    },
	    sameidcard: {
	    	cbajax: ajaxCheck,
	        pattern: /^[\d]{17}[\dxX]{1}$/,
	        tips: {
	            empty_tip: '���������֤��',
	            format_tip: '��������ʵ�����֤��',
	            '1': '֤���Ų�һ��'
	        }
	    },
	    checkcode: {
	    	cbajax: ajaxCheck,
	        pattern: /^[0-9a-zA-Z]{4}$/,
	        tips: {
	            empty_tip: '��������֤��',
	            format_tip: '������4λ��֤��',
	            '1': '��֤����������������'
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
	            empty_tip: '����������',
	            format_tip: '֧����ĸ�����֣�������ϣ�6-16λ�ַ�',
				min_len_tip: '����6���ַ�',
				max_len_tip: '���16���ַ�',
				'1': '������͵�¼������ͬ'
	        }
	    },
	    recharge_money: {
	        min: 1,
	        pattern: /^\d+$/,
	        tips: {
	            min_tip: '���ٳ�ֵ1Ԫ��������',
	            empty_tip: '��ѡ���ֵ���',
	            format_tip: '�������������'
	        }
	    },
	    withdraw_money: {
            min: 0.01,
            pattern: /^\+?([0-9]+|[0-9]+\.[0-9]{0,2}|\.[0-9]{0,2})$/,
            tips: {
                min_tip: '�������1��',
                empty_tip: '�����������',
                format_tip: '��������ȷ�������'
            }
	    },
	    bankcard: {
	        pattern: /^\d{16,19}$/,
	        tips: {
	            empty_tip: '��������ȷ�����п���',
	            format_tip: '��������ȷ�����п���'
	        }
	    },
	    chinese: {
	    	cbajax: ajaxCheck,
	        pattern: /^[\u4e00-\u9fa5]{2,10}$/,
	        tips: {
	            empty_tip: '��������ȷ��������',
	            format_tip: '��������ȷ��������',
	            '1': '��ʵ����֤��Ϣ��һ��'
	        }
	    },
	    qq: {
	        pattern: /^\d{5,10}$|^\s*$/,
	        tips: {
	            format_tip: '��������ȷQQ��'
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
				empty_tip: '���ٴ���������',
				format_tip: '����һ�����������'
	        }
	    }
    };

    var vform = cx.vform = function(selector, options) {
        options || (options = {});
        this.$form = $(selector);
        this.$tip = this.$form.find('.tip');
        this.$submit = this.$form.find('.submit');
        this.$eles = this.$form.find('.vcontent');
        this.callback = options.callback || null;
        this.errorCallback = options.errorCallback || null;
        this.submit = options.submit || null;
        this.renderTip = this[options.renderTip] || this.renderTip;
        this.data = {};
        this.tip = null;

        this.init();
    };

    vform.prototype = {
        init: function() {
            var self = this;
            this.$eles.blur(function() {
                if ($(this).attr('readonly') == 'readonly') {
                    return ;
                }
                self.validate(this);
            });
            this.$eles.focus(function() {
                if ($(this).attr('readonly') == 'readonly') {
                    return ;
                }
                self.showtips(this);
            });

            this.$submit.click(function() {

                if (self.validateAll()) {
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
                if (e.which == 13) {
                    if (self.validateAll()) {
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
			if (status == 'ok') {
				this.tip.closest('.form-tip').addClass('form-tip-true').removeClass('hide');
			} else {
				this.tip.closest('.form-tip').addClass('form-tip-error').removeClass('hide');
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

        validate: function(ele) {
            var val, ruleName, rule, ajaxCheck, encrypt;
            val = $.trim($(ele).val());
            ruleName = $(ele).data('rule');
            ajaxCheck = $(ele).data('ajaxcheck');
            encrypt = $(ele).data('encrypt');
            this.setcontainer(ele);
            if($(ele).is(':hidden') && $(ele).attr('type') != 'hidden'){
            	return true;
            }
            if ($(ele).attr('type') == 'checkbox') {
                if ($(ele).attr('checked') != 'checked') {
					if( ruleName ){
						this.renderTip('��ͬ���û�Э��');
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
                    this.renderTip('�������벻һ��');
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
                            this.renderTip(rule.tips.format_tip);
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
				if(ajaxCheck == '1'){
					var disable = $('.' + $(ele).data('freeze'));
					if( rule.cbajax && $.isFunction(rule.cbajax) ) {
						var datas = name + '=' + val;
						var result = false;
						result = rule.cbajax.call(this, datas)
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
            var $el;
            for (var i = 0; i < this.$eles.length; ++i) {
                $el = $(this.$eles[i]);

                if (!this.validate($el)) {
                    return false;
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
	                rule = rules[ruleName];
	                var $tip = $('.' + name);
	                $tip.html(rule.tips.format_tip).show();
	                $tip.closest('.form-tip').removeClass('form-tip-error form-tip-true hide');
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
			var gtips = {1 : '��', 2 : '��', 3 : 'ǿ', 4 : '����'};
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
			renderStr = '<div>���벻����2345�ʺ�һ�£�����������</div>';
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
			renderStr = '<div>����״̬</div>' + gradeStr;
			renderTip.call(self, renderStr, null, 'ok' );
			return true;
		}
	}
	
	function validUsername( username, renderTip ){
		var self = this;
		// �ж��ַ��Ƿ񳬹�, �����ڹ涨����
		username = $.trim(username);
		if (username.length < 2)
		{
			renderTip.call(self, '����1�����ֻ�2���ַ�', null, 'error' );
			return false;
		}
		if (username.replace(/[^\x00-\xff]/g, "**").length > 24)
		{
			renderTip.call(self, '�벻Ҫ����12�����ֻ�24���ַ�', null, 'error' );
			return false;
		}
		if (/[^\u4E00-\u9FA5\w_@\.\-]/.test(username))
		{
			renderTip.call(self, '�����뺺�֣���ĸ������', null, 'error' );
			return false;
		}
		return true;
	}

	function ajaxCheck(datas){
		var self = this;
		var result = 0;
		$.ajax({
	        type: "POST",
	        url: "/main/ReCallApi/uinfoCheck",
	        data: datas,
	        dataType: "json",
	        async: false,
	        success: function(response){
					result = response;
				}
        });
		return result;
	}
})();