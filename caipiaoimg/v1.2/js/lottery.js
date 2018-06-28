var chase = {}, j = 0;
(function() {
	
	window.cx || (window.cx = {});
	
	for (i in chases) {
		if (j < chaselength) {
			chase[i] = {};
			chase[i].award_time = chases[i].award_time;
			chase[i].show_end_time = chases[i].show_end_time;
			chase[i].multi = chases[i].multi;
			chase[i].money = chases[i].money;
			j++;
		}else {
			break;
		}
	}
    
	 var BoxCollection = cx.BoxCollection = function(selector, options) {
        this.$el = $(selector);
        this.boxes = [];
        this.options = options || {}
        this.betMoney = this.options.betMoney || 2;
        this.edit = 0;
        this.basket;
        this.init();
    };
    BoxCollection.prototype = {
    	init: function() {
    		var self = this;
    		this.$el.find('.add-basket').click(function() {
    			if (self.calcMoney() > 20000) {
            		cx.Alert({content: "<i class='icon-font'>&#xe611;</i>单笔订单金额最高<span class='num-red'>２万</span>元"});
            		return;
            	}
        		var rule = cx.Lottery.getRule(self.getType(), self.isValid());
        		if (rule['status'] !== true) {
        			cx.Alert({content: rule['content'], size: rule['size']});
        		}else {
        			var balls = self.getAllBalls();
        			if(self.edit === 0) {
                		self.basket.add(balls);
                	}else {
                		self.basket.edit(balls, self.edit);
                	}
                	self.removeAll();
                	$('html, body').animate({scrollTop: $('.cast-basket .btn-main').offset().top + $('.cast-basket .btn-main')[0].scrollHeight - $(window).height()});
        		}
            });
    	},
    	setBasket: function(basket) {
    		this.basket = basket;
    	},
        add: function(box) {
            this.boxes.push(box);
            box.setCollection(this);
        },
        addBall: function(boxs) {
        	var editStrs = {
                balls: [],
                betNum: 0,
                betMoney: 0,
            };
        	$(this.boxes).each(function(k, box) {
        		editStrs.balls[k] = [];
        		box.removeAll();
        		for (var j in boxs[k]) {
        			editStrs.balls[k][j] = [];
        			for (var i = 0; i < boxs[k][j].length; ++i) {
            			box.addBall(boxs[k][j][i], j);
            			editStrs.balls[k][j].push(boxs[k][j][i]);
                 	}
        		}
            });
        	editStrs.betNum = this.calcBetNum();
        	editStrs.betMoney = editStrs.betNum * this.betMoney;
        	editStrs.playType = this.getType();
        	return editStrs;
        },
        isValid: function() {
        	var err = [];
            for (var i = 0; i < this.boxes.length; ++i) {
            	var error = [];
            	$.each(this.boxes[i].isValid(), function(j, isValid){
            		if (err.length == 0) {
            			error.push(parseInt(isValid));
            		}else {
            			$.each(err, function(k, er){
            				error.push(er+isValid);
            			})
            		}
            	})
            	err = error;
            }
            return err.sort(function(a, b) {
            	a = parseInt(a, 10) > 10 ? parseInt(a, 10) : parseInt(a, 10) + 50;
				b = parseInt(b, 10) > 10  ? parseInt(a, 10) : parseInt(a, 10) + 50;
				return a > b ? 1 : ( a < b ? -1 : 0 );
            });
        },
        removeAll: function() {
            $(this.boxes).each(function(k, box) {
                box.removeAll();
            });
        },
        renderBet: function() {
        	if(this.$el.find('.num-red').length > 0) {
        		this.$el.find('.num-red:eq(0)').html(this.getNum(0)[0]+this.getNum(0)[1]);
        		this.$el.find('.num-red:eq(1)').html(this.getNum(0)[0]);
        		this.$el.find('.num-red:eq(2)').html(this.getNum(0)[1]);
        	}
        	this.$el.find('.num-multiple').html(this.calcBetNum());
            this.$el.find('.num-money').html(this.calcMoney());
        	var rule = cx.Lottery.getRule(this.getType(), this.isValid());
            if (rule['status'] === true) {
                this.$el.find('.add-basket').removeClass('btn-disabled');
            } else {
            	this.$el.find('.add-basket').addClass('btn-disabled');
            }
        },
        rand1: function(playType) {
        	var randStrs = {
                balls: [],
                betNum: 0,
                betMoney: 0
            },
            startindex = cx.Lottery.getStartIndex(playType), arr = cx.Lottery.getMinLength(playType), amount = cx.Lottery.getAmount(playType);
        	randStrs.betNum = 1;
        	randStrs.betMoney = this.betMoney;
        	
        	for (i in arr) {
        		randStrs.balls[i] = {};
        		randStrs.balls[i]['tuo'] = [];
        		while (randStrs.balls[i]['tuo'].length < arr[i]) {
            		j = Math.floor(Math.random() * (amount[i] - startindex[i] + 1) + startindex[i]);
            		if ($.inArray(j, randStrs.balls[i]['tuo']) === -1) randStrs.balls[i]['tuo'].push(j);
            	}
        	}
            randStrs.playType = playType;
            return randStrs;
        },
		edit: function() {
            var editStrs = {
                balls: [],
                betNum: 0,
                betMoney: 0,
            };
            $(this.boxes).each(function(k, box) {
                editStrs.balls.push(box.edit());
            });
            editStrs.betNum = this.calcBetNum();
            editStrs.betMoney = this.betNum * this.betMoney;
            $(this.boxes).each(function(k, box) {
                box.removeAll();
            });
            return editStrs;		
		},
		clearButton: function(playType){
        	this.edit = 0;
        	this.$el.find('.add-basket').html('添加到投注区<i class="icon-font">&#xe614;</i>');
        },
		getError: function(){
			var error = [] ;
            $(this.boxes).each(function(k, box) {
                error.push(box.getError());
            });
			return error;
		},
        getAllBalls: function(remove) {
            var allBalls = [], tmpBall = {};
            $(this.boxes).each(function(k, box) {
            	tmpBall = {};
            	tmpBall['tuo'] = box.getBalls();
            	if(box.getBalls('dan').length > 0) tmpBall['dan'] = box.getBalls('dan');
                allBalls.push(tmpBall);
            });
            var betNum = this.calcBetNum();
            if (remove === false) {
            	$(this.boxes).each(function(k, box) {
                    box.removeAll();
                });
            }
            return {
                balls: allBalls,
                betNum: betNum,
                betMoney: betNum * this.betMoney,
                playType: this.getType()
            };
        },
        getStrings: function() {
            var strings = [];
            $(this.boxes).each(function(k, box) {
                strings.push(box.joinBalls());
            });
            return strings;
        },
        calcBetNum: function() {
            var product = 1, rule = cx.Lottery.getRule(this.getType(), this.isValid());
            if (rule['status'] !== true) return 0;
            $(this.boxes).each(function(k, box) {
                product *= box.calcComb();
            });
            return product;
        },
        calcMoney: function() {
            return this.calcBetNum() * this.betMoney;
        },
        setBetMoney: function(moneyNum) {
            this.betMoney = moneyNum;
        },
        getBoxes: function() {
            return this.boxes;
        },
        getNum: function(i) {
            return this.boxes[i].getNum();
        },
        getType: function() {
        	return this.boxes[0].playType;
        }
    };

    var BallBox = cx.BallBox = function(selector, options, danselctor) {
		this.selector = selector;
        this.$el = $(selector);
        this.$danel = $(danselctor);
        this.options = options || {};
        this.playType = this.options.playType || 'default';
        this.smallel = this.options.smallel || 'a';
        this.balls = [];
        this.dans = [];
		this.error = false;
        this.init();
    };
    cx.BallBox.prototype = {
        init: function() {
            var self = this;
            this.$dans = self.$danel.find('.pick-area-ball '+self.smallel);
            this.$balls = self.$el.find('.pick-area-ball '+self.smallel);
        	this.$balls.click(function() {
            	var $this = $(this);
            	self.BallTriger($this);
            });
            this.$dans.click(function() {
            	var $this = $(this);
            	self.BallTriger($this, 'dan');
            });
            this.$el.find('.clear-balls').click(function() {
                self.removeBalls();
            });
        },
        setCollection: function(collection, index) {
            this.collection = collection;
            this.index = $.inArray(this, this.collection.boxes);
        },
        isValid: function() {
        	var error = [];
        	if (this.playType.indexOf('dt') > -1) {
        		var min = cx.Lottery.getMinLength(this.playType)[this.index];
        		if (this.options.hasdan === true && this.dans.length < 1) error.push('1');
    			if ((this.options.hasdan === true && this.balls.length < this.options.tmin) || (!this.options.hasdan && this.balls.length < min)) error.push('2');
    			if (this.options.hasdan === true && this.balls.length + this.dans.length < this.options.dtmin) error.push('3');
    			if (this.options.hasdan === true && this.balls.length < min) error.push('4');
        	}else {
        		if (this.balls.length < cx.Lottery.getMinLength(this.playType)[this.index]) error.push('1');
        	}
        	if (error.length == 0) error = ['0'];
        	return error;
        },
        ballValid: function(type) {
        	var res = true;
        	if (type === 'dan') res = res && this.dans.length < this.options.dmax;
        	return res;
        },
        BallTriger: function ($el, type) {        	
        	var ball = $el.html();
            switch (type) {
            	case 'dan':
            		var arr = this.$balls, t = 'tuo';
            		break;
            	case 'tuo':
            	default:
            		var arr = this.$dans, t = 'dan';
            		break;
            }
            if ($el.hasClass('selected')) {
            	$el.removeClass('selected');
            	arr.eq(ball-1).removeClass('dt-pick');
            	this.removeBall(ball, type);
            	if (type === 'dan' && this.randSel !== false) 
            		this.selReset(this.options.selmin[this.dans.length], this.options.selmax[this.dans.length], this.options.seldefault[this.dans.length]);
            } else if (!this.ballValid(type)) {
            	cx.Alert({content: cx.Lottery.getRule(this.playType, [pad(this.collection.boxes.length, 5, this.index)])['content']});
            } else {
            	arr.eq(ball-1).removeClass('selected').addClass('dt-pick');
            	this.removeBall(ball, t);
            	$el.removeClass('dt-pick').addClass('selected');
            	this.addBall(ball, type);
            	if (type === 'dan' && this.randSel !== false) 
            		this.selReset(this.options.selmin[this.dans.length], this.options.selmax[this.dans.length],  this.options.seldefault[this.dans.length]);
            }
            this.collection.renderBet();
        },
        rand: function(number, cb) {
            number || (number = cx.Lottery.getMinLength(this.playType)[this.index]);
            var self = this, amount = cx.Lottery.getAmount(this.playType)[this.index], startindex = cx.Lottery.getStartIndex(this.playType)[this.index];
            cb || (cb = function(i) {
                self.balls.push(i);
            });
            var j;
            this.balls = [];
            var flag = true;
            while (this.balls.length < number) {
                flag = true;
                j = Math.ceil(Math.random() * amount);
                if (j >= startindex && $.inArray(j, this.balls) === -1 && $.inArray(j, this.dans) === -1) cb(j);
            }
			this.error = false;
            return this.balls.sort(sort);
        },
		getError: function(){
			return this.error;
		},
        calcComb: function() {
            return cx.Math.combine(this.balls.length, cx.Lottery.getMinLength(this.playType)[this.index]-this.dans.length);
        },
        joinBalls: function() {
            return this.balls.join(',');
        },
        getNum: function() {
        	return [this.dans.length, this.balls.length];
        },
        getBalls: function(type) {
        	switch (type) {
        		case 'dan':
        			return this.dans;
        			break;
        		case 'tuo':
        		default:
        			return this.balls;
        			break;
        	}
        },
        getABalls: function(type) {
        	switch (type) {
        		case 'dan':
        			return this.$dans;
        			break;
        		case 'tuo':
        		default:
        			return this.$balls;
        			break;
        	}
        },
        addBall: function(i, type, duplicate) {
        	var arr = this.getBalls(type);
        	i = parseInt(i, 10);
        	if ($.inArray(i, arr) > -1 && duplicate) return ;
        	arr.push(i);
        },
        removeBall: function(i, type) {
        	var arr = this.getBalls(type);
        	i = parseInt(i, 10);
        	var index = $.inArray(i, arr);
            if (index == -1) return ;
            arr.splice(index, 1);
        },
        removeAll: function() {
            this.balls = [];
            this.dans = [];
            this.$dans.removeClass('selected dt-pick');
        	this.$balls.removeClass('selected dt-pick');
            this.collection.renderBet();
        },
        removeBalls: function() {
        	var self = this;
            this.balls = [];
            this.$balls.removeClass('selected');
            this.$dans.removeClass('dt-pick');
            this.collection.renderBet();
        }
    };

    var CastBasket = cx.CastBasket = function(selector, options) {
    	this.$el = $(selector);
        this.multi = options.multi || 1;
        this.strings = {};
        this.autoId = 0;
        this.orderType = 0;
        this.betNum = 0;
        this.betMoney = 0;
        this.upload_no = 0;
        this.playType = options.playType || 'default';
        this.boxes = options.boxes;
        this.multiModifier = options.multiModifier;
        this.$castList = options.$castList || this.$el.find('.cast-list');
        this.tab = options.tab;
        this.tabClass = options.tabClass;
        this.zj = false;
        this.init();
        var _this = this;
        
        var Chase = cx.Chase = function() {
        	this.chases = options.chases || {};
            this.chaseLength = options.chaseLength || 0;
            this.chaseMulti = 0;
            this.chaseMoney = 0;
            this.setStatus = options.setStatus || 0;
            this.setMoney = options.setMoney || 0;
            this.init();
        };
        
        var Hemai = cx.Hemai = function() {
        	this.$betNum = _this.$el.find('.betNum');
            this.$betMoney = _this.$el.find('.betMoney');
        	this.$buyMoney = _this.$el.find('.buyMoney input');
        	this.openStatus = 0;
            this.commission = 0;
    		this.buyMoney = 0;
    		this.guarantee = 0;
    		this.rgpctmin = 5;
    		this.init();
        }
        
        cx.Chase.prototype = {
			init: function() {
				var self = this;
				if ($("#ordertype1").attr('checked')) {
					_this.orderType = 1;
	            	$("#ordertype1").parents('.chase-number-tab').find('.ptips-bd').hide();
	            	$("#ordertype1").parents('.chase-number').find('.chase-number-bd').show();
	            	_this.$el.find('.chase-number-table-hd .follow-issue').val('10');
	            	_this.$el.find('.chase-number-table .follow-multi').val('1');
	            	_this.$el.find('.chase-number-table :checkbox').attr('checked', 'checked');
	            	_this.$el.find(".chase-number-table-ft :checkbox:first").removeAttr('checked');
	            }
					    		
            	this.chaseMulti = this.chaseLength * _this.multi;
                this.chaseMoney = this.chaseMulti * _this.betMoney;
                _this.$el.find(".chase-number-table-ft .fbig em:first").html(this.chaseLength);
                _this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
	    		
	    		_this.$el.find('.setMoney').blur(function(){
	            	var money = parseInt($(this).val(), 10);
	            	if ($(this).val().match(/\D/g) !== null || !money) {
	            		self.setMoney = 1;
	            		$(this).val(1);
	            	}else {
	            		self.setMoney = money;
	                }
	            });
	    		_this.$el.find('.setMoney').keyup(function(){
	            	var money = parseInt($(this).val(), 10);
	            	if(money >= 100000){
	                	$(this).val(100000);
	                	self.setMoney = 100000;
	                }else {
	                	self.setMoney = money;
	                }
	            });
	    		_this.$el.find('.chase-number-table-hd .follow-issue').keyup(function(){
	            	var num = parseInt($(this).val(), 10),multi = parseInt(_this.$el.find(".chase-number-table-hd .follow-multi").val() || _this.multiModifier.value, 10),max = $(this).attr('data-max');
	            	if ($(this).val().match(/\D/g) !== null) {//非法字符
	            		num = 10;
	            		$(this).val(10);
	            	}else if(num >= max){
	                	$(this).val(max);
	                    num = max;
	                }
	            	if (!isNaN(num) && num >= 2) self.setChaseByIssue(num, multi);
	            });
	    		_this.$el.find('.chase-number-table-hd .follow-issue').blur(function(){
	            	var num = parseInt($(this).val(), 10),multi = parseInt((_this.$el.find(".chase-number-table-hd .follow-multi").val() || _this.multiModifier.value), 10);
	            	if ($(this).val() === '' || parseInt($(this).val(), 10) < 2) {
	            		num = 2;
	            		$(this).val(2);
	            		self.setChaseByIssue(num, multi);
	            	}
	            });
	    		_this.$el.find(".chase-number-table-hd .follow-multi").keyup(function(){
	            	var max = $(this).data('max');
	            	if ($(this).val().match(/\D/g) !== null) {//非法字符
	            		$(this).val(1);
	            	}else if(parseInt($(this).val()) >= max){
	                	$(this).val(max);
	                }else if (!$(this).val() || $(this).val() == 0){
	                	$(this).val('');
	                }
	            	var multi = parseInt($(this).val(), 10);
	            	if (!isNaN(multi) && multi >= 1) {
	            		self.chaseMulti = 0;
	            		self.chaseMoney = 0;
	                	var issue = [];
	                	for (i in self.chases) {
	                		self.chases[i].multi = multi;
	                		self.chases[i].money = multi * _this.betMoney;
	                		self.chaseMulti += multi;
	                	}
	                	self.chaseMoney = self.chaseMulti * _this.betMoney;
	                	_this.$el.find(".chase-number-table-bd tbody tr").each(function(){
	                		issue.push($(this).attr('data-issue'));
	                	})
	                	self.renderChase(issue);
	                	_this.$el.find(".chase-number-table-ft .fbig em:last").html(self.chaseMoney);
	            	}
	            });
	    		_this.$el.find(".chase-number-table-hd :checkbox").click(function(){
	            	self.chases = {};
	            	self.chaseMoney = 0;
	            	self.chaseMulti = 0;
	            	var issue = [];
	            	if ($(this).attr('checked') == 'checked') {
	            		var multi = parseInt(_this.multiModifier.value, 10);
	            		$(".chase-number-table-bd tbody tr").each(function(){
	            			i = $(this).attr('data-issue');
	            			self.setChaseByI(i);
	            			self.chases[i].multi = multi;
	            			self.chases[i].money = multi * _this.betMoney;
	            			self.chaseMulti += multi;
	                		issue.push(i);
	                	})
	                	self.chaseLength = $(".chase-number-table-bd tbody tr").length;
	            		self.chaseMoney = self.chaseMulti * _this.betMoney;
	            		_this.$el.find('.chase-number-table-hd .follow-issue').val(self.chaseLength);
	            		_this.$el.find('.chase-number-table-hd .follow-multi').val(multi);
	            		_this.$el.find(".chase-number-table-ft .fbig em:first").html(self.chaseLength);
	            		_this.$el.find(".chase-number-table-ft .fbig em:last").html(self.chaseMoney);
	            		self.renderChase(issue);
	            	}else {
	            		self.chaseLength = 0;
	            		_this.$el.find(".chase-number-table-bd tbody :checkbox").removeAttr('checked');
	            		_this.$el.find('.chase-number-table-hd .follow-issue').val('0');
	            		_this.$el.find('.follow-multi').val('');
	            		_this.$el.find('.follow-money').html('0');
	            		_this.$el.find(".chase-number-table-ft .fbig em:first").html('0');
	            		_this.$el.find(".chase-number-table-ft .fbig em:last").html('0');
	            	}
	            });
	    		_this.$el.on('click', ".chase-number-table-bd :checkbox", function(){
	            	var i = $(this).parents('tr').attr('data-issue');
	            	if ($(this).attr('checked') == 'checked') {
	            		if ($(".chase-number-table-bd :checkbox[checked!='checked']").length == 0) $(".chase-number-table-hd :checkbox").attr('checked', 'checked');
	            		multi = parseInt((_this.$el.find(".chase-number-table-hd .follow-multi").val() || _this.multiModifier.value), 10);
	            		self.setChaseByI(i);
	        			self.chases[i].multi = multi;
	            		self.chases[i].money = multi * _this.betMoney;
	        			self.chaseMulti += multi;
	            		self.chaseLength++;
	            		$(this).parents('tr').find(".follow-multi").val(multi);
	            		$(this).parents('tr').find(".follow-money").html(self.chases[i].money);
	            	}else {
	        			self.chaseMulti -= self.chases[i].multi;
	            		delete self.chases[i];
	            		self.chaseLength--;
	            		$(this).parents('tr').find(".follow-multi").val('');
	            		$(this).parents('tr').find(".follow-money").html('0');
	            		_this.$el.find(".chase-number-table-hd :checkbox").removeAttr('checked');
	            	}
	            	self.chaseMoney = self.chaseMulti * _this.betMoney;
	            	_this.$el.find('.chase-number-table-hd .follow-issue').val(self.chaseLength);
	            	_this.$el.find(".chase-number-table-ft .fbig em:first").html(self.chaseLength);
	            	_this.$el.find(".chase-number-table-ft .fbig em:last").html(self.chaseMoney);
	            });
	    		_this.$el.on('keyup', ".chase-number-table-bd .follow-multi", function(){
	            	var max = _this.$el.find(".chase-number-table-hd .follow-multi").data('max');
	            	if ($(this).val().match(/\D/g) !== null) {//非法字符
	            		$(this).val(1);
	            	}else if(parseInt($(this).val()) >= max){
	                	$(this).val(max);
	                }else if (!$(this).val() || $(this).val() == 0){
	                	$(this).val('');
	                }
	            	multi = parseInt($(this).val() || 0, 10);
	            	if (!isNaN(multi)){
	            		var i = $(this).parents('tr').attr('data-issue');
	            		self.setChaseByBodyMulti($(this), multi, i);
	            	}
	            });
	    		_this.$el.find(".chase-number-table-ft :checkbox:first, .setStatus").click(function(){
	            	self.setStatus = 0;
	            	if ($(this).attr('checked') == 'checked') self.setStatus = 1;
	            });
			},
			setChaseMoney: function() {
	        	this.chaseMoney = _this.betMoney * this.chaseMulti;
	        	_this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
	        	for (i in this.chases) {
	        		this.chases[i].money = this.chases[i].multi *　_this.betMoney;
	            	$(".chase-number-table-bd tbody tr[data-issue="+i+"] .follow-money").html(this.chases[i].money);
	            }
	        },
	        setChaseMulti: function(multi) {
	        	this.chaseMulti = 0;
	        	$(".chase-number-table-hd .follow-multi").val(multi);
	        	for (i in this.chases) {
	        		this.chaseMulti += multi;
	        		this.chases[i].multi = multi;
	        		this.chases[i].money = multi *　_this.betMoney;
	        		$(".chase-number-table-bd tbody tr[data-issue="+i+"] .follow-multi").val(multi);
	            	$(".chase-number-table-bd tbody tr[data-issue="+i+"] .follow-money").html(this.chases[i].money);
	            }
	        	this.chaseMoney = _this.betMoney * this.chaseMulti;
	        	_this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
	        },
	        setChaseByIssue: function(num, multi) {
	        	var tbstr = '', j = 0, issue = [], self=this;
	        	this.chaseMulti = 0;
	        	this.chaseMoney = 0;
	        	this.chaseLength = 0;
	        	this.chases = {};
	        	
	        	if (num > 0) {
	        		$.each(chases, function(i, e){
	        			if (j < num) {
	        				issue.push(i);
	        				self.setChaseByI(i);
	        				self.chases[i].multi = multi;
	        				self.chases[i].money = multi * _this.betMoney;
	        				self.chaseMulti += multi;
	                		j++;
	                		self.chaseLength++;
	        			}
	        		})
	        		this.chaseMoney = this.chaseMulti * _this.betMoney;
	        		_this.$el.find(".chase-number-table-bd tbody").html(tbstr);
	        	}
	        	this.renderChase(issue);
	        	_this.$el.find(".chase-number-table-hd :checkbox").attr('checked', 'checked');
	        	_this.$el.find(".chase-number-table-hd .follow-multi").val(multi);
	        	_this.$el.find(".chase-number-table-ft .fbig em:first").html(num);
	        	_this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
	        },
	        setChaseByI: function(i) {
	        	if (!this.chases[i]) this.chases[i] = {};
	        	this.chases[i].award_time = chases[i].award_time;
	    		this.chases[i].show_end_time = chases[i].show_end_time;
	        },
	        setChaseByBodyMulti: function(el, multi, i) {
	        	if (this.chases[i]) {
	        		this.chaseMulti -= this.chases[i].multi;
	        		this.chaseLength --;
	        		delete this.chases[i];
	        	}
	        	if (multi > 0) {
	        		this.setChaseByI(i);
	        		this.chases[i].multi = multi;
	        		this.chases[i].money = multi * _this.betMoney;
	        		this.chaseMulti += multi;
	        		this.chaseLength ++;
	        		el.parents('tr').find(':checkbox').attr('checked', 'checked');
	        	}else {
	        		el.parents('tr').find(':checkbox').removeAttr('checked', 'checked');
	        	}
	        	this.chaseMoney = this.chaseMulti * _this.betMoney;
				el.parents('tr').find('.follow-money').html(multi * _this.betMoney);
				_this.$el.find('.chase-number-table-hd .follow-issue').val(this.chaseLength);
				_this.$el.find(".chase-number-table-ft .fbig em:first").html(this.chaseLength);
				_this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
	        },
	        renderChase: function(issue) {
	        	var tbstr = '', j = 1, self = this;
	        	$.each(issue, function(i, e){
	        		multi = self.chases[e] ? self.chases[e].multi : ($(".follow-multi:first").val() || _this.multiModifier.value);
					tbstr += '<tr data-issue="'+e+'"><td>'+j+'</td><td class="tal"><input type="checkbox"';
	        		if (self.chases[e]) tbstr += ' checked="checked"';
	        		tbstr += '>'+e+'期';
	        		if (e == ISSUE) tbstr += ' <span class="main-color-s">（当前期）</span>';
	        		tbstr += '</td><td><input type="text"';
	        		if (self.chases[e]) tbstr += ' value="'+multi+'"';
	        		tbstr += ' class="ipt-txt follow-multi">倍</td><td><span class="main-color-s follow-money">';
	        		if (self.chases[e]) {
	        			tbstr += multi * _this.betMoney;
	        		}else {
	        			tbstr += '0';
	        		}
	        		tbstr += '</span>元</td><td>'+chases[e].award_time.substring(0, 16)+'</td></tr>';
	        		j++;
	        	})
	    		_this.$el.find(".chase-number-table-bd tbody").html(tbstr);
	    		if (_this.$el.find(".chase-number-table-bd :checkbox[checked!='checked']").length == 0) $(".chase-number-table-hd :checkbox").attr('checked', 'checked');
	        }
        }  
        
        Hemai.prototype = {
        	init: function() {
        		var self = this;
        		_this.$el.find('.commission').on('click', 'li', function(){
    				$('.commission li').removeClass('cur');
    				$(this).addClass('cur');
    				self.commission = $(this).data('val');
    				self.rgpctmin = (self.commission <= 5) ? 5 : self.commission;
    				var buyMoney = Math.ceil(_this.betMoney * _this.multiModifier.getValue() * self.rgpctmin / 100);
    				self.buyMoney = buyMoney < self.buyMoney ? self.buyMoney : buyMoney;
    				if ($('.guaranteeAll').attr('checked') || self.guarantee > _this.betMoney * _this.multiModifier.getValue() - self.buyMoney) {
    					self.guarantee = _this.betMoney * _this.multiModifier.getValue() - self.buyMoney;
    					$('.guaranteeAll').attr('checked', 'checked');
    	            	self.renderGuarantee();
    	            }
    				self.renderBuyMoney();
    			});
    			this.$buyMoney.on('blur', function(){
    				buyMoney = isNaN(parseInt($(this).val(), 10)) ? 0 : parseInt($(this).val(), 10);
    				buyMoneymin = Math.ceil(_this.betMoney * _this.multiModifier.getValue() * self.rgpctmin / 100);
    				self.buyMoney = (buyMoney < buyMoneymin) ? buyMoneymin : (buyMoney > _this.betMoney * _this.multiModifier.getValue() ? _this.betMoney * _this.multiModifier.getValue() : buyMoney);
    				if ($('.guaranteeAll').attr('checked') || self.guarantee > _this.betMoney * _this.multiModifier.getValue() - self.buyMoney) {
    					self.guarantee = _this.betMoney * _this.multiModifier.getValue() - self.buyMoney;
    					$('.guaranteeAll').attr('checked', 'checked');
    	            	self.renderGuarantee();
    	            }
    				self.renderBuyMoney();
    			});
    			_this.$el.find('.guarantee').on('blur', 'input.form-item-ipt', function(){
    				guarantee = isNaN(parseInt($(this).val(), 10)) ? 0 : parseInt($(this).val(), 10);
    				$('.guaranteeAll').removeAttr('checked');
    				if (guarantee >= (_this.betMoney * _this.multiModifier.getValue() - self.buyMoney)) {
    					guarantee = _this.betMoney * _this.multiModifier.getValue() - self.buyMoney;
    					$('.guaranteeAll').attr('checked', 'checked');
    				}
    				self.guarantee = guarantee < 0 ? 0 : guarantee;
    				self.renderGuarantee();
    			});
    			_this.$el.find('.guaranteeAll').on('click', function(){
    				self.guarantee = _this.betMoney * _this.multiModifier.getValue() - self.buyMoney;
    	            self.renderGuarantee();
    			});
    			_this.$el.find('input[name=bmsz]').click(function(){
    				self.openStatus = $(this).val();
    			})
        	},
        	caculateBuyMoney: function() {
        		var buyMoney = Math.ceil(_this.betMoney * _this.multi * this.rgpctmin / 100);
                this.buyMoney = (buyMoney <= this.buyMoney && this.buyMoney <= _this.betMoney * _this.multi) ? this.buyMoney : (buyMoney > this.buyMoney ? buyMoney : _this.betMoney * _this.multi);
        	    this.guarantee = _this.$el.find('.guaranteeAll').is(":checked") ? _this.betMoney * _this.multi - this.buyMoney :this.guarantee;
            },
        	renderBuyMoney: function() {
            	this.$buyMoney.val(this.buyMoney).parents('.buyMoney').find('span em:first').html(this.rgpctmin);
            	this.buyMoney > 0 ? this.$buyMoney.parents('.buyMoney').find('u').show().find('em').html(Math.floor(this.buyMoney * 100/(_this.betMoney * _this.multi))) : this.$buyMoney.parents('.buyMoney').find('u').hide();
    			$('.guarantee').find('span em:first').html(_this.betMoney * _this.multi - this.buyMoney);
    			$('.buy_txt').html("<em class='main-color-s'>"+(this.buyMoney+this.guarantee)+"</em> 元 <span>（认购"+this.buyMoney+"元+保底"+this.guarantee+"元）</span></span>");
            },
            renderGuarantee: function() {
            	$('.guarantee input.form-item-ipt').val(this.guarantee).parents('.guarantee').find('span em:last').html(_this.betMoney == 0 ? 0 : Math.floor(this.guarantee * 100 / (_this.betMoney * _this.multi)));
            	$('.buy_txt').html("<em class='main-color-s'>"+(this.buyMoney+this.guarantee)+"</em> 元 <span>（认购"+this.buyMoney+"元+保底"+this.guarantee+"元）</span></span>");
            },
            renderHeMai:function(){
                this.caculateBuyMoney();
                this.renderBuyMoney();
                this.renderGuarantee();
            }
        }
        
        this.chase = new Chase();
        this.hemai = new Hemai();
        
    };

    CastBasket.prototype = {
        init: function() {
            var self = this;
            $.each(this.boxes, function(i, boxes){
            	boxes.setBasket(self);
            })
            
            this.multiModifier.setCb(function() {
            	self.multi = parseInt(this.getValue(), 10);
            	$('.Multi').html(self.multi);
                self.renderBetMoney();
                self.hemai.renderHeMai();
                self.chase.setChaseMulti(self.multi);
            });
            this.$el.find('.rand-cast').click(function() {
                var $this = $(this);
                var amount = parseInt($this.data('amount'), 10);
                self.randSelect(amount);
            });
            this.$el.on('click', '.remove-str', function() {
                var $li = $(this).closest('li');
                var index = $li.attr('data-index');
                for (i in self.boxes) {
                	if(index == self.boxes[i].edit) self.boxes[i].clearButton(i);
                }
                $li.remove();
                self.remove($li.data('index'));
            });
            this.$el.find('.clear-list').click(function() {
                self.removeAll();
                for (i in self.boxes) {
                	self.boxes[i].clearButton(i);
                }
            });
            this.$el.on('click', '.submit', function(e) {
                var $this = $(this);
                self.submit($this, self);
            });
            this.$el.on('click', '.buy-type-hd li', function() {
            	switch($(this).index()) {
            		case 1:
            			cx._basket_.orderType = 1;
                        $(this).find('.ptips-bd').hide();
                        var str = '连续多期购买同一个（组）号码<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>追号：</em>选好投注号码后，对期数、期<br>号、倍数进行设置后，系统按照设置<br>进行购买。">&#xe613;</i>';
                        $(".chase-number-notes").html(str);
            			break;
            		case 2:
            			cx._basket_.orderType = 4;
                    	$(this).find('.ptips-bd').hide();
                    	var str = '多人出资购买彩票，奖金按购买比例分享<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>合买：</em>选好投注号码后，由多人出资<br>购买彩票。中奖后，奖金按购买比例<br>分享。">&#xe613;</i>';
                        $(".chase-number-notes").html(str);
            			break;
            		case 0:
            		default:
            			cx._basket_.orderType = 0;
    	                $('#ordertype1').parents("span").find('.ptips-bd').show();
    	                var str = '由购买人自行全额购买彩票，独享奖金<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>自购：</em>选好投注号码后，由自己全额<br>支付购彩款。中奖后，自己独享全部<br>税后奖金。">&#xe613;</i>';
    	                $(".chase-number-notes").html(str);
            			break;
            	}
          	  	chgbtn();
            })
        },
        submit: function($el, self) {
        	if ($el.hasClass('not-login') || !$.cookie('name_ie')) {
            	cx.PopAjax.login(1);
                return ;
            }
        	
        	if ($el.hasClass('not-bind')) return ;
			
            var data = cx.castData.getCastOptions();
            data.isToken = 1;
            if (data.betTnum == 0) {
	             new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>至少选择<span class='num-red'>１</span>注号码才能投注，请先选择方案"});
	             return ;
            }    
            if (data.orderType == 1 && data.totalIssue <= 1) {
            	cx.Alert({content: "<i class='icon-font'>&#xe611;</i>您好，追号玩法须至少选择<span class='num-red'> 2 </span>期"});
            	return ;
            }
            
            // 最大金额前端限制
        	if (data.orderType == 1) {
        		var checkflag = false;
        		$.each(data.chases.split(';'), function(i, e){
        			if (parseInt(e.split('|')[2], 10) > 20000) {
        				checkflag = true;
        				return false;
        			}
        		})
        		if (checkflag) {
        			new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>２万</span>元，请修改订单后重新投注"});
        			return ;
        		}
        	}else {
        		if ( data.money >20000 ) {
                    new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>２万</span>元，请修改订单后重新投注"});
                    return ;
                }
        	}
			
            if (this.$el.find(".ipt_checkbox#agreenment").get(0) && !this.$el.find(".ipt_checkbox#agreenment").attr("checked")) {
            	if (this.$el.find(".risk_pro").length > 0) {
            		return void new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请先阅读并同意《用户委托投注协议》<br>《限号投注风险须知》后才能继续"});
            	} else {
            		return void new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请先阅读并同意《用户委托投注协议》后才能继续"});
            	}
            }
            cx.castCb(data, {ctype:'create', lotteryId:cx.Lottery.lid, orderType:self.orderType, betMoney:self.betMoney * self.multiModifier.getValue(), chaseLength:self.chase.chaseLength, buyMoney:self.hemai.buyMoney, guarantee:self.hemai.guarantee, issue:ISSUE});
        },
        
        setType: function(type) {
        	this.playType = type;
        	this.boxes[this.playType].renderBet();
        	if(this.boxes[this.playType].edit > 0) this.$castList.find('li').removeClass('hover').filter('[data-index="'+this.boxes[this.playType].edit+'"]').addClass('hover');
        },
        add: function(balls) {
            this.autoId += 1;
            this.strings[this.autoId] = balls;
            this.$castList.prepend(this.renderString(balls, this.autoId));
            this.betNum += balls.betNum;
            this.betMoney += balls.betMoney;
            this.chase.setChaseMoney();
            this.hemai.renderHeMai();
            this.renderAllBet();
        },
        addAll: function(balls) {
        	var self = this;
        	$.each(balls, function(i, e){
        		self.autoId += 1;
        		self.strings[self.autoId] = e;
        		self.betNum += e.betNum;
        		self.betMoney += e.betMoney;
        		self.$castList.prepend(self.renderString(e, self.autoId, false, false));
        	})
        	this.chase.setChaseMoney();
            this.hemai.renderHeMai();
            this.renderAllBet();
        },
        edit: function(balls, id) {
        	var betNum = this.strings[id].betNum, betMoney = this.strings[id].betMoney;
        	this.strings[id] = balls;
            this.$castList.find("li[data-index="+id+"]").replaceWith(this.renderString(balls, id, true));
            this.betNum += balls.betNum-betNum;
            this.betMoney += balls.betMoney-betMoney;
            this.chase.setChaseMoney();
            this.hemai.renderHeMai();
            this.renderAllBet();
            this.boxes[balls.playType].clearButton(balls.playType);
        },
        rand: function(amount) {
            var randStr = '';
            for (var i = 0; i < amount; ++i) {
                randStr = boxes[self.playType].rand().join(' ');
                this.add(randStr);
            }
        },
        randSelect: function(amount) {
            var rand = [];
            for (var i = 0; i < amount; ++i) {
                rand.push(this.boxes[this.playType].rand1(this.playType));
            }
            this.addAll(rand);
        },
        renderAllBet: function() {
            this.hemai.$betNum.html(this.betNum);
            this.renderBetMoney();
        },
        renderBetMoney: function() {
            this.hemai.$betMoney.html(this.betMoney * this.multi);
        },
        removeAll: function() {
            this.strings = {};
            this.betNum = 0;
            this.betMoney = 0;
            this.$castList.empty();
            this.chase.setChaseMoney();
            this.hemai.renderHeMai();
            this.renderAllBet();
        },
        remove: function(index) {
            var selected = this.strings[index];
            this.betNum -= selected.betNum;
            this.betMoney -= selected.betMoney;
            delete this.strings[index];
            this.chase.setChaseMoney();
            this.hemai.renderHeMai();
            this.renderAllBet();
        },
        setBetMoney: function(betMoney){
        	var self = this;
        	$.each(self.boxes, function(i, box){
        		box.setBetMoney(betMoney);
        	})
        	$.each(self.strings, function(s, string){
        		self.betMoney += betMoney * string.betNum-string.betMoney;
        		string.betMoney = betMoney * string.betNum;
        		$('.cast-list').find('li[data-index='+s+']').find('.bet-money').html(string.betMoney + '元');
        	})
            self.betMoney = self.betNum*betMoney;
            self.hemai.renderHeMai();
        	self.chase.setChaseMoney();
        },
        renderString: function(allBalls, index, hover) {
        	var tpl = '<li ';
        	if(hover) tpl += ' class="hover"'
        	tpl += ' data-index="'+index+'"><span class="bet-type">'+ cx.Lottery.getPlayTypeName(cx.Lottery.playTypes[allBalls.playType]);
            var ballTpl = [], dt = false;
            $.each(allBalls.balls, function(pi, balls){
                var tmpTpl = '<span class="num-red">';
            	danTpl = '';
            	if ('dan' in balls) {
            		dt = true;
            		$.each(balls['dan'].sort(sort), function(ti, ball){
            			if (cx.Lottery.hasPaddingZero(this.playType)) ball = pad(2, ball, 1);
                    	danTpl += ball + ' ';
                    })
                    tmpTpl += "("+danTpl.replace(/(\s*$)/g,'')+") ";
            	}
                $.each(balls['tuo'].sort(sort), function(ti, ball){
                	if (cx.Lottery.hasPaddingZero(this.playType)) ball = pad(2, ball, 1);
                	tmpTpl += ball + ' ';
                })
                tmpTpl += '</span>';
                ballTpl.push(tmpTpl);
            })
            ballTpl = ballTpl.join('<em>|</em>');
        	if (dt) {
            	tpl += '胆拖';
            } else if (allBalls.betNum > 1) {
        		tpl += '复式';
        	} else {
        		tpl += '单式';
        	}
            
            tpl += '</span><div class="num-group">'+ballTpl+'</div><a href="javascript:;" class="remove-str">删除</a><a href="javascript:;" class="modify-str">修改</a><span class="bet-money">'+allBalls.betMoney+'元</span></li>';
            return tpl;
        }
    };
    
    var castData = cx.castData = (function() {
    	
    	var me = {};
    	    	
    	me.getCastOptions = function() {
            var self = this;
            switch (cx._basket_.orderType) {
            	case 1:
            		var endTime = '';
                	for (i in cx._basket_.chase.chases) {
                    	if (endTime === ''){
                    		endTime = cx._basket_.chase.chases[i].show_end_time;
                    	}else {
                    		break;
                    	}
                    }
                	var data = {
                        money: cx._basket_.chase.chaseMoney,
                        multi: cx._basket_.chase.chaseMulti,
                        setStatus: cx._basket_.chase.setStatus,
                        setMoney: cx._basket_.chase.setStatus == 1 ? cx._basket_.chase.setMoney : '',
                        totalIssue: cx._basket_.chase.chaseLength,
                        chases: me.toChaseString(cx._basket_.chase.chases), endTime: endTime
                    };
            		break;
            	case 4:
            		var data = {
                        money: cx._basket_.betMoney * cx._basket_.multiModifier.getValue(),
                        multi: cx._basket_.multiModifier.getValue(),
                        issue: ISSUE,
                        endTime: hmDate.getFullYear()+"-"+padd(hmDate.getMonth() + 1)+"-"+padd(hmDate.getDate())+" "+padd(hmDate.getHours())+":"+padd(hmDate.getMinutes())+":"+padd(hmDate.getSeconds()),
                        buyMoney: cx._basket_.hemai.buyMoney,
                        commissionRate: cx._basket_.hemai.commission,
                        guaranteeAmount: cx._basket_.hemai.guarantee,
                        openStatus: cx._basket_.hemai.openStatus,
                        openEndtime: realendTime
                    };
            		break;
            	case 0:
            	default:
            		var data = {money: cx._basket_.betMoney * cx._basket_.multiModifier.getValue(), multi: cx._basket_.multiModifier.getValue(), issue: ISSUE, endTime: ENDTIME};
            		break;
            }
            data.ctype = 'create';
            data.buyPlatform = 0;
            data.lid = cx.Lottery.lid;
            data.playType = (cx._basket_.playType === 'dssc') ? playType : 0;
            data.betTnum = cx._basket_.betNum;
            data.isChase = cx._basket_.zj ? 1 : 0;
            data.orderType = cx._basket_.orderType;
            if (cx._basket_.playType === 'dssc') {
            	data.upload_no = cx._basket_.upload_no;
                data.singleFlag = 1; 
            }else {
            	data.codes = me.toCastString(cx._basket_.strings, cx._basket_.zj);
            }
            return data;
        }
    	
    	me.toChaseString = function(chases) {
        	var chaseStr = '';
        	for(i in chases) {
        		chaseStr += i+"|"+chases[i].multi+"|"+chases[i].money+"|"+chases[i].award_time+"|"+chases[i].show_end_time+";";
        	}
        	return chaseStr;
        };
        
        me.toCastString = function(subStrings){
    		var danStr = '', tuoStr = '', betArr = [], danArr = [], tuoArr = [], preArr = [];
    		$.each(subStrings, function(k, subString) {
    			var playType = subString.playType || 'default', midStr = ':' + cx.Lottery.playTypes[playType], postStr = ':' + cx.Lottery.getCastPost(playType);
                preArr = [];
                $.each(subString.balls, function(j, ball) {
                	danStr = '', tuoStr = '', danArr = [], tuoArr = [];
                	if (ball['dan'] !== undefined) {
                		$.each(ball['dan'].sort(sort), function(i, dan) {
                			if (cx.Lottery.hasPaddingZero()) dan = pad(2, dan, 1);
                			danArr.push(dan);
                		})
                		danStr = danArr.join(cx.Lottery.getNumberSeparator(playType)) + "$";
                	}
                	$.each(ball['tuo'].sort(sort), function(i, tuo) {
                		if (cx.Lottery.hasPaddingZero()) tuo = pad(2, tuo, 1);
                		tuoArr.push(tuo);
                	})
                    tuoStr = tuoArr.join(cx.Lottery.getNumberSeparator(playType));
                    preArr.push(danStr + tuoStr);
                })
                betArr.push(preArr.join(cx.Lottery.getPlaceSeparator(playType)) + midStr + postStr);
    		})
            return betArr.join(';');
    	}
        
        return me;
    })();
})();

function chgbtn () {
	if (selling == 2 && ($.inArray(cx._basket_.orderType, [0, 1]) > -1 || (hmselling == 1 && hmendTime * 1000 >= (new Date()).valueOf()))) {
		$("[id^=pd][id$=_buy]").removeClass('btn-disabled').addClass('needTigger submit').html('确认预约');
		$('body').find('#buy_tip').remove();
	}else if(selling == 1) {
		$("[id^=pd][id$=_buy]").removeClass('needTigger submit').addClass('btn-disabled').html('期次更新中');
		if ($("[id^=pd][id$=_buy]").next('#buy_tip').length == 0)$("[id^=pd][id$=_buy]").after("<p id='buy_tip' class='main-color' style='margin: 4px 0 6px'>（下一期开售时间为"+realendTime.match(/\d{2}:\d{2}/)+"）</p>")
	}else {
		$("[id^=pd][id$=_buy]").removeClass('needTigger submit').addClass('btn-disabled').html('暂停预约');
		$('body').find('#buy_tip').remove();
	}
}
$(function(){
	if (typeof hmDate === 'object') 
		$('.hmendTime .form-item-txt').html(hmDate.getFullYear() + "-" + padd(hmDate.getMonth() + 1) + "-" + padd(hmDate.getDate()) + " " + padd(hmDate.getHours()) + ":" + padd(hmDate.getMinutes()) + ":" + padd(hmDate.getSeconds()));
	if (typeof selling !== 'undefined') chgbtn();
  
  $('.chase-number-notes, .guarantee').on('mouseenter', '.bubble-tip', function(){
      $.bubble({
          target:this,
          position: 'b',
          align: 'l',
          content: $(this).attr('tiptext'),
          width:'auto'
      })
  }).on('mouseleave', '.bubble-tip', function(){
      $('.bubble').hide();
  });
  
  $(".bet-tab-hd ul").tabPlug({
      cntSelect: '.bet-tab-bd',
      menuChildSel: 'li',
      onStyle: 'current',
      cntChildSel: '.bet-tab-bd-inner',
      eventName: 'click',
      callbackFun: function (k, cne) {
    	  var type = $("."+cx._basket_.tab).eq(k).data('type');
    	  cx._basket_.setType(type);
      }
  });    
});

function pad(t, n, i) {
	str = '';
	for (j = n.toString().length-1; j < t; j++) {
		if (j == i) {
			str += n;
		}else {
			str += '0';
		}
	}
	return str;
}

var sort = function(a, b){
	a = parseInt(a, 10);
	b = parseInt(b, 10);
	return a > b ? 1 : ( a < b ? -1 : 0 );
}

