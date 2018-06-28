(function() {
	
	window.cx || (window.cx = {});
	
	cx.Lottery = (function(){
		
		var lid = cx.Lid.SSQ, me = {}; 
		
		me.lid = lid;
		
		me.playTypes = {'default': 1, dt : 135};
		
		me.getPlayTypeByMidCode = function(midCode) {
			var data = {'1':'default', '135':'dt'};
			return (midCode in data) ? data[midCode] : 'default';
	    }
		
		me.getNumberSeparator = function(playType) {
		    return ',';
		}
		
		me.getPlaceSeparator = function(playType) {
	        return '|';
	    }
		
		me.hasPaddingZero = function(playType) {
	        return true;
	    }
		
		me.getCastPost = function( playType) {
	        var CAST_POST = {'default': '1', dt:'5'};
	        playType || (playType = 'default');
	        if (playType in CAST_POST) post = CAST_POST[playType];
	        return post;
	    };
	    
	    me.getPlayTypeName = function(playType) {
	        playCnNames = {0: '普通', 1: '普通', 135: '胆拖'};
	        cnName = playCnNames[playType] ? playCnNames[playType] : '普通';
	        return cnName;
	    };
	    
	    me.getMinLength = function(playType) {
	        return [6, 1];
	    }
	    
	    me.getAmount = function(playType) {
	        return [33, 16];
	    }
	    
	    me.getStartIndex = function(playType) {
	        return [1, 1];
	    }
	    
	    me.getRule = function(playType, state) {
	    	var result = {'status':true, 'content':'', 'size':'18'};
	    	switch (playType) {
				case 'dt':
					for (i in state) {
						switch (state[i]) {
		    				case '10':
		    				case '12':
		    					result['status'] = false;
		    					result['content'] = '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">1</span>个红球胆码';
		    					break;
		    				case '20':
		    				case '22':
		    					result['status'] = false;
		    					result['content'] = '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">2</span>个红球拖码';
		    					break;
		    				case '30':
		    				case '32':
		    					result['status'] = false;
		    					result['content'] = '<i class="icon-font">&#xe611;</i>红球胆码＋红球拖码≥<span class="num-red">7</span>个';
		    					break;
		    				case '50':
		    					result['status'] = false;
		    					result['content'] = '<i class="icon-font">&#xe611;</i>最多选择<span class="num-red">5</span>个红球胆码';
		    					break;
		    				case '02':
		    				case '42':
		    					result['status'] = false;
		    					result['content'] = '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">1</span>个蓝球';
		    					break;
		    			}
					}
					break;
				case 'ddsh':
					for (i in state) {
						switch (state[i]) {
		    				case '10':
		    					result['status'] = false;
		    					result['content'] = '<i class="icon-font">&#xe611;</i>红球最多可定<span class="num-red">5</span>个胆码<span class="pop-small">(胆码超出后自动做杀号处理)</span>';
		    					break;
		    				case '20':
		    					result['status'] = false;
		    					result['content'] = '<i class="icon-font">&#xe611;</i>红球最多可杀<span class="num-red">27</span>个号码';
		    					break;
		    				case '02':
		    					result['status'] = false;
		    					result['content'] = '<i class="icon-font">&#xe611;</i>蓝球最多可杀<span class="num-red">15</span>个号码';
		    					break;
		    			}
					}
					break;
				case 'default':
				default:
					if ($.inArray('00', state) === -1) {
						result['status'] = false;
						result['content'] = '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">６</span>个红球和<span class="num-blue">１</span>个蓝球';
					}
					break;
			}
		    return result;
	    }
	    
	    me.getPlayTypeByCode = function(lotteryId, code) {
	    	return 'default';
        }
	    
	    return me;
		
	})();
	
})();

$(function() {
	
	cx.BoxCollection.prototype.init = function() {
		var self = this;
		this.$el.find('.add-basket').click(function() {
			if (self.calcMoney() > 20000) {
        		cx.Alert({content: "<i class='icon-font'>&#xe611;</i>单笔订单金额最高<span class='num-red'>２万</span>元"});
        		return;
        	}
        	if (self.getType() === 'ddsh') {
        		var allball = self.getAllBalls(true), narr = [parseInt($(".tac .rand-count:first").val(), 10), parseInt($(".tac .rand-count").eq(1).val(), 10)], zn = $(".tac .rand-count:last").val(), balls, betNum = self.calcBetNum();
        		for (var i = 0; i <zn; i++) {
        			var balls = self.randddsh(narr, betNum, allball);
        			self.basket.add(balls);
        		}
        		$('html, body').animate({scrollTop: $('.cast-basket .btn-main').offset().top + $('.cast-basket .btn-main')[0].scrollHeight - $(window).height()});
        	}else {
        		var rule = cx.Lottery.getRule(self.getType(), self.isValid());
        		if (rule['status'] !== true) {
        			cx.Alert({content: rule['content'], size : rule['size']});
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
        	}
        });
		$(".tac .rand-count:last").change(function(){
			self.renderBet();
		});
		this.$el.find(".clear-pickball").click(function(){
			for (i in self.boxes) {
				self.boxes[i].removeAll();
			}
		})
	} 
	
	cx.BoxCollection.prototype.renderBet = function() {
		this.$el.find('.num-red:eq(0)').html(this.getNum(0)[0]+this.getNum(0)[1]);
		this.$el.find('.num-red:eq(1)').html(this.getNum(0)[0]);
		this.$el.find('.num-red:eq(2)').html(this.getNum(0)[1]);
		this.$el.find('.num-blue:eq(0)').html(this.getNum(1)[0]+this.getNum(1)[1]);
		this.$el.find('.num-blue:eq(1)').html(this.getNum(1)[0]);
		this.$el.find('.num-blue:eq(2)').html(this.getNum(1)[1]);
    	if (this.getType() === 'ddsh') {
    		var multiple = parseInt($(".tac .rand-count:last").val());
    		this.$el.find('.num-multiple').html(this.calcBetNum() * multiple);
            this.$el.find('.num-money').html(this.calcMoney() * multiple);
    	} else {
    		this.$el.find('.num-multiple').html(this.calcBetNum());
            this.$el.find('.num-money').html(this.calcMoney());
    	}
    	var rule = cx.Lottery.getRule(this.getType(), this.isValid());
    	this.$el.find('.add-basket').removeClass('btn-disabled');
        if (!rule['status']) this.$el.find('.add-basket').addClass('btn-disabled');
    }
				
	cx.BoxCollection.prototype.rand1 = function(playType) {
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
    	playType = 'default';

        randStrs.playType = playType;
        return randStrs;
	}
	
	cx.BoxCollection.prototype.randddsh = function(arr, betnum, allballs) {
    	var amount = cx.Lottery.getAmount('ddsh'), min = cx.Lottery.getMinLength('ddsh'),
    	randStr = {
            balls: [],
            betNum: betnum,
            betMoney: betnum * this.betMoney,
            playType: 'default'
        };
    	$.each(arr, function(i, e){
    		randStr.balls[i] = {};
    		danlen = allballs.balls[i]['dan'] ? allballs.balls[i]['dan'].length : 0;
    		if (danlen) {
    			randStr.balls[i]['dan'] = [];
    			$.each(allballs.balls[i]['dan'], function(j, ele) {
    				randStr.balls[i]['dan'].push(ele);
    			})
    		}
    		randStr.balls[i]['tuo'] = [];
    		while (randStr.balls[i]['tuo'].length < e) {
        		j = Math.ceil(Math.random() * amount[i]);
        		if ($.inArray(j, randStr.balls[i]['tuo']) === -1 && $.inArray(j, allballs.balls[i]['tuo']) === -1 && $.inArray(j, allballs.balls[i]['sha']) === -1 && $.inArray(j, randStr.balls[i]['dan']) === -1) 
        			randStr.balls[i]['tuo'].push(j);
        	}
    		if (danlen && e + danlen === min[i]) {
    			randStr.balls[i]['tuo'] = randStr.balls[i]['tuo'].concat(randStr.balls[i]['dan']);
    			delete randStr.balls[i]['dan'];
    		}
    		if (allballs.balls[i]['tuo'].length > 0) randStr.balls[i]['tuo'] = randStr.balls[i]['tuo'].concat(allballs.balls[i]['tuo']);
    	})
		for (i in randStr.balls) {
			if (randStr.balls[i].dan) randStr.playType = 'dt';
		}
    	return randStr;
    };
    
    cx.BoxCollection.prototype.getAllBalls = function(remove) {
        var allBalls = [];
        var tmpBall = {};
        $(this.boxes).each(function(k, box) {
        	tmpBall = {};
        	tmpBall['tuo'] = box.getBalls();
        	if(box.getBalls('dan').length > 0) tmpBall['dan'] = box.getBalls('dan');
        	if(box.getBalls('sha').length > 0) tmpBall['sha'] = box.getBalls('sha');
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
    }
	
	cx.BallBox.prototype.init = function() {
		var self = this;
		this.shas = [];
		this.seldef = true;
		this.randSel = this.options.randSel || false;
    	this.$dans = self.$danel.find('.pick-area-ball '+self.smallel);
        if (this.playType === 'ddsh') {
        	this.$shas = self.$el.find('.pick-area-ball '+self.smallel);
        	this.$shas.click(function() {
            	var $this = $(this);
            	self.ShaTriger($this);
            });
        }else {
        	this.$balls = self.$el.find('.pick-area-ball '+self.smallel);
        	this.$balls.click(function() {
            	var $this = $(this);
            	self.BallTriger($this);
            });
        }
        this.$dans.click(function() {
        	var $this = $(this);
        	self.BallTriger($this, 'dan');
        });
        this.$el.find('.clear-balls').click(function() {
            self.removeBalls();
        });
        this.$el.find('.rand-select').click(function() {
            var count = self.$el.find('.rand-count').val() || 1;
            self.removeBalls();
            self.rand(count, function(i) {
            	self.$balls.eq(i - 1).addClass('selected');
                self.$dans.eq(i - 1).addClass('dt-pick');
                self.addBall(i + '') ;
            });
            self.collection.renderBet();
        });
        this.$el.find('.rand-count').change(function() {
            var count = self.$el.find('.rand-count').val();
            self.removeBalls();
            self.rand(count, function(i) {
                self.addBall(i + '') ;
                self.$balls.eq(i - 1).addClass('selected');
                self.$dans.eq(i - 1).addClass('dt-pick');
            });
            self.seldef = false;
            self.collection.renderBet();
        });
    	$(this.randSel).click(function(){
    		self.seldef = false;
    		self.collection.renderBet();
    	})
	}
	
	cx.BallBox.prototype.isValid = function() {
    	var error = [];
    	switch (this.playType) {
    		case 'dt':
    			var min = cx.Lottery.getMinLength('dt')[this.index];
    			if (this.options.hasdan === true && this.dans.length < 1) error.push('1');
    			if ((this.options.hasdan === true && this.balls.length < this.options.tmin) || (!this.options.hasdan && this.balls.length < min)) error.push('2');
    			if (this.options.hasdan === true && this.balls.length + this.dans.length < this.options.dtmin) error.push('3');
    			if (this.options.hasdan === true && this.balls.length < min) error.push('4');
    			break;
    		case 'ddsh':
    			break;
    		case 'default':
    		default:
    			if (this.balls.length < cx.Lottery.getMinLength('default')[this.index]) error.push('1');
    			break;
    	}
    	if (error.length == 0) error = ['0'];
    	return error;
    }
    	
	cx.BallBox.prototype.ballValid = function(type) {
    	var res = true;
    	if (type === 'dan' || type === 'sha') res = res && this.dans.length < this.options.dmax;
    	if (type === 'sha') res = res && this.shas.length < this.options.smax;
    	return res;
    }
    	
	cx.BallBox.prototype.calcComb = function() {
    	if (this.playType === 'ddsh') {
        	var combCount = cx.Math.combine(parseInt($(this.randSel).val())+this.balls.length, cx.Lottery.getMinLength('ddsh')[this.index]-this.dans.length);
        } else {
            var combCount = cx.Math.combine(this.balls.length, cx.Lottery.getMinLength(this.playType)[this.index]-this.dans.length);
        }
        return combCount;
    }
	
	cx.BallBox.prototype.removeAll = function() {
        this.balls = [];
        this.dans = [];
        this.shas = [];
        this.$dans.removeClass('selected dt-pick');
        if (this.playType === 'ddsh') {
        	this.$shas.removeClass('selected kill-ball');
            this.selReset(this.options.selmin[0], this.options.selmax[0],  this.options.seldefault[0]);
        } else {
        	this.$balls.removeClass('selected dt-pick');
        }
        this.collection.renderBet();
    }
    
    cx.BallBox.prototype.getBalls = function(type) {
    	switch (type) {
    		case 'dan':
    			return this.dans;
    			break;
    		case 'sha':
    			return this.shas;
    			break;
    		case 'tuo':
    		default:
    			return this.balls;
    			break;
    	}
    }
    
    cx.BallBox.prototype.getABalls = function(type) {
    	switch (type) {
    		case 'dan':
    			return this.$dans;
    			break;
    		case 'sha':
    			return this.$shas;
    			break;
    		case 'tuo':
    		default:
    			return this.$balls;
    			break;
    	}
    }
    
    cx.BallBox.prototype.BallTriger = function ($el, type) {        	
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
        	if (type === 'dan') this.selReset(this.options.selmin[this.dans.length], this.options.selmax[this.dans.length],  this.options.seldefault[this.dans.length]);
        } else if (!this.ballValid(type)) {
        	cx.Alert({content: cx.Lottery.getRule(this.playType, [pad(this.collection.boxes.length, 5, this.index)])['content']});
        } else {
        	arr.eq(ball-1).removeClass('selected').addClass('dt-pick');
        	this.removeBall(ball, t);
        	$el.removeClass('dt-pick').addClass('selected');
        	this.addBall(ball, type);
        	if (type === 'dan') this.selReset(this.options.selmin[this.dans.length], this.options.selmax[this.dans.length],  this.options.seldefault[this.dans.length]);
        }
        this.collection.renderBet();
    }
	
	cx.BallBox.prototype.ShaTriger = function($el) {
		var dan = $el.html();
    	if (this.options.hasdan) {
    		var t = 'dan';
    	}else {
    		var t = 'tuo';
    	}
        if ($el.hasClass('selected')) {
        	if(this.shas.length < this.options.smax) {
        		$el.removeClass('selected').addClass('kill-ball');
            	this.removeBall(dan, t);
            	this.addBall(dan, 'sha');
        	} else {
        		cx.Alert({content: cx.Lottery.getRule(this.playType, [pad(this.collection.boxes.length, 2, this.index)])['content']});
        	}
        } else if ($el.hasClass('kill-ball')) {
        	$el.removeClass('kill-ball');
        	this.removeBall(dan, 'sha');
        } else if (this.dans.length + this.balls.length < this.options.dmax) {
        	$el.addClass('selected');
        	this.addBall(dan, t);
        } else if(this.shas.length < this.options.smax) {
        	if (this.shas.length === 0) cx.Alert({content: cx.Lottery.getRule(this.playType, [pad(this.collection.boxes.length, 1, this.index)])['content']});
            $el.addClass('kill-ball');
            this.addBall(dan, 'sha');
        } else {
        	cx.Alert({content: cx.Lottery.getRule(this.playType, [pad(this.collection.boxes.length, 2, this.index)])['content']});
        }
        if (this.index == 1) {
        	this.selReset(this.options.selmin[this.balls.length], this.options.selmax[this.balls.length]-this.shas.length, this.options.seldefault[this.balls.length]);
        }else {
        	this.selReset(this.options.selmin[this.dans.length], Math.min(cx.Lottery.getAmount()[this.index]-this.dans.length-this.shas.length, this.options.selmax[this.dans.length]), this.options.seldefault[this.dans.length]);
        }
        this.collection.renderBet();
	}
		
	cx.BallBox.prototype.selReset = function(start, end, dfault) {
		if ($(this.randSel).val() >= start && $(this.randSel).val() <= end && !this.seldef) {
    		dfault = $(this.randSel).val();
    	} else {
    		this.seldef = true;
    	}
    	str = '';
    	$(this.randSel).empty();
    	for (var i = start; i <= end; i++) {
    		str += "<option value='"+i+"'";
    		if (dfault && dfault == i) str += " selected";
    		str += ">"+i+"</option>";
    	}
    	$(this.randSel).append(str);
	}
	
	cx.CastBasket.prototype.init = function() {
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
        this.$el.on('click', '.modify-str', function() {
        	var startindex, string = {};
        	self.$castList.find('li').removeClass('hover');
        	string = self.strings[$(this).parent('li').data('index')];
        	self.boxes[string.playType].edit = $(this).parent('li').addClass('hover').data('index');
        	if(string.playType !== self.playType) $("."+self.tab).filter("[data-type^="+string.playType+"]").trigger('click');
        	startindex = cx.Lottery.getStartIndex(self.playType)
        	self.boxes[self.playType].removeAll();
        	$.each(string.balls, function(n, balls){
        		if ('dan' in balls) {
        			$.each(balls['dan'], function(i, ball){
            			if(!isNaN(ball)) self.boxes[self.playType].boxes[n].$dans.eq(ball - parseInt(startindex[n])).trigger('click');
            		})
        		}
        		$.each(balls['tuo'], function(i, ball){
        			if(!isNaN(ball)) self.boxes[self.playType].boxes[n].$balls.eq(ball - parseInt(startindex[n])).trigger('click');
        		})
        	})
            self.boxes[self.playType].renderBet();
        	self.boxes[self.playType].$el.find('.add-basket').html('确认修改<i class="icon-font">&#xe614;</i>');
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
    }
	
	cx.CastBasket.prototype.setType = function(type) {
		this.playType = type;
		if (type === 'ddsh') {
			$(".ptips-bd-b").hide();
		}else {
			$(".p1tips-bd-b").show();
		}
		$(".bet-sup-bar a:eq(3)").remove();
		if (this.playType === 'dt') {
			$(".bet-sup-bar").append('<a href="activity/dantuo" target="_blank" class="what-dt">什么是<br>胆拖投注？</a>');
			$(".bet-sup-bar a:eq(0)").attr('href', 'activity/dantuo');
		} else if (this.playType === 'ddsh') {
			$(".bet-sup-bar").append('<a href="activity/dingdanshahao" target="_blank" class="what-dt">什么是<br>定胆杀号？</a>');
			$(".bet-sup-bar a:eq(0)").attr('href', 'activity/dingdanshahao');
		} else {
			$(".bet-sup-bar a:eq(0)").attr('href', tzjqurl);
		}
	    //或略单式上传过滤报错
	    if (this.playType !== 'dssc') this.boxes[this.playType].renderBet();
    }
	
	cx.CastBasket.prototype.renderString = function(allBalls, index, hover, noedit) {
    	var tpl = '<li ';
    	if(hover) tpl += ' class="hover"'
    	tpl += ' data-index="'+index+'"><span class="bet-type">';
        var ballTpl = [], dt = false, tuoTpl = '', danTpl = '', self=this;
        $.each(allBalls.balls, function(pi, balls){
        	if (pi > 0) {
                var tmpTpl = '<span class="num-blue">';
            } else {
                var tmpTpl = '<span class="num-red">';
            }
        	tuoTpl = ''; 
        	danTpl = '';
        	if ('dan' in balls) {
        		dt = true;
        		$.each(balls['dan'].sort(sort), function(ti, ball){
                	danTpl += pad(2, ball, 1) + ' ';
                })
                tmpTpl += "("+danTpl.replace(/(\s*$)/g,'')+") ";
        	}
            $.each(balls['tuo'].sort(sort), function(ti, ball){
            	tuoTpl += pad(2, ball, 1) + ' ';
            })
            tmpTpl += tuoTpl+'</span>';
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
    	tpl += '</span><div class="num-group">'+ballTpl+'</div>';
    	if (!noedit) tpl += '<a href="javascript:;" class="remove-str">删除</a><a href="javascript:;" class="modify-str">修改</a>';
    	tpl += '<span class="bet-money">'+ allBalls.betMoney +'元</span></li>';
        return tpl;
    }
	
    var boxes = {'default':new cx.BoxCollection('.default .box-collection'), 'dt':new cx.BoxCollection('.dt .box-collection'), 'ddsh':new cx.BoxCollection('.ddsh .box-collection')};

    boxes['default'].add(new cx.BallBox('.default .pre-box', {playType:'default'}));
    boxes['default'].add(new cx.BallBox('.default .post-box', {playType:'default'}));
    boxes['dt'].add(new cx.BallBox('.dt .pre-box:last', {
        dmax: 5,
        tmin: 2,
        dtmin: 7,
        hasdan: true,
        playType: 'dt',
        randSel: '.dt .pre-box .rand-count:eq(0)',
        selmin: [2, 6, 5, 4, 3, 2],
        seldefault: [5, 6, 5, 4, 3, 2],
        selmax: [30, 18, 23, 30, 29, 28]
    }, '.dt .pre-box:first'));
    boxes['dt'].add(new cx.BallBox('.dt .post-box', {dmax: 0, hasdan: false, playType: 'dt'}));
    boxes['ddsh'].add(new cx.BallBox('.ddsh .pre-box', {
        playType: 'ddsh',
        dmax: 5,
        smax: 27,
        hasdan: true,
        randSel: '.tac .rand-count:eq(0)',
        selmin: [6, 5, 4, 3, 2, 1],
        seldefault: [6, 5, 4, 3, 2, 1],
        selmax: [16, 18, 23, 30, 29, 28]
    }));
    boxes['ddsh'].add(new cx.BallBox('.ddsh .post-box', {
        playType: 'ddsh',
        dmax: 16,
        smax: 15,
        hasdan: false,
        randSel: '.tac .rand-count:eq(1)',
        selmin: [1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        seldefault: [1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        selmax: [16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0]
    }));

    var multiModifier = new cx.AdderSubtractor('.multi-modifier'),
    basket = new cx.CastBasket('.cast-basket', {boxes: boxes, tab: 'bet-tab-hd li', tabClass: 'current', playType: 'default', setMoney: 5000, chases: chase, chaseLength: chaselength, multi: MULTI, multiModifier: multiModifier});

    cx._basket_ = basket;
    
    $('.jiangjinCalculate').click(function(e){
    	var self = $(this);
    	self.removeClass('jiangjinCalculate');
        $.ajax({
            type: 'post',
            url:  '/pop/jiangjinCalculate',
            data: {version:version},
            success: function(response) {
                $('body').append(response);
                cx.PopCom.show('.pop-jsq');
                cx.PopCom.close('.pop-jsq');
                
                // 奖金计算器弹窗
                var popJsq= $('.pop-jsq'), windowHeight = $(window).height(), docHeight = $('.pop-mask').height(), ie6=!-[1,]&&!window.XMLHttpRequest;
                popJsq.css({'position': 'absolute', 'top': $(window).scrollTop() + $(window).height()/2 - $('.pop-jsq').outerHeight()/2 + 'px', 'margin-top': 0})
                if (!self.hasClass('jiangjinCalculate')) self.addClass('jiangjinCalculate');
            },
            error: function() {
            	if (!self.hasClass('jiangjinCalculate')) self.addClass('jiangjinCalculate');
            }
        });
        e.stopImmediatePropagation();
        e.preventDefault();
    });
});

