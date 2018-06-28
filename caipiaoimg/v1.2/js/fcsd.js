(function() {
	
	window.cx || (window.cx = {});
	
	cx.Lottery = (function(){
		
		var lid = cx.Lid.FCSD, me = {};
		
		me.lid = lid;
		
		me.playTypes = {'default':'1', zx:'1', z3:'2', z6:'3'}; 
		
		me.getPlayTypeByMidCode = function(midCode) {
			var data = {'1':'zx', '2':'z3', '3':'z6'};
			return (midCode in data) ? data[midCode] : 'zx';
	    }
		
		me.jiangjin = {zx:'1040', z3:'346', z6:'173'};
		
		me.getNumberSeparator = function(playType) {
			var data = {'default': '', 'zx': '', 'z3': ',', 'z6': ','}
		    return (playType in data) ? data[playType] : data['default'];
		}
		
		me.getPlaceSeparator = function(playType) {
			var data = {'default': ',', 'zx': ',', 'z3': '', 'z6': ''}
		    return (playType in data) ? data[playType] : data['default'];
	    }
		
		me.hasPaddingZero = function(playType) {
	        return false;
	    }
		
		me.getCastPost = function(playType, multi) {
			if (multi && playType == 'z3') return '3';
			var data = {'default': '1', 'zx': '1', 'z3': '1', 'z6': '3'}
		    return (playType in data) ? data[playType] : data['default'];
	    };
	    
	    me.getPlayTypeName = function(playType) {
	    	var data = {1: '直选', 2: '组三', 3: '组六'}
		    return (playType in data) ? data[playType] : data['default'];
	    };
	    
	    me.getMinLength = function(playType) {
	    	var data = {'default': [1, 1, 1], 'zx': [1, 1, 1], 'z3': [2], 'z6': [3]}
		    return (playType in data) ? data[playType] : data['default'];
	    }
	    
	    me.getAmount = function(playType) {
	    	var data = {'default': [9, 9, 9], 'zx': [9, 9, 9], 'z3': [9], 'z6': [9]}
		    return (playType in data) ? data[playType] : data['default'];
	    }
	    
	    me.getStartIndex = function(playType) {
	    	var data = {'default': [0, 0, 0], 'zx': [0, 0, 0], 'z3': [0], 'z6': [0]}
		    return (playType in data) ? data[playType] : data['default'];
	    }
	    
	    me.getRule = function(playType, state) {
	    	var result = {'status':true, 'content':'', 'size':'18'};
	    	switch (playType) {
		    	case 'zx':
		    		if (state[0].indexOf('1') > -1) {
		    			result['status'] = false;
						result['content'] = '<i class="icon-font">&#xe611;</i>每位至少选择１个号码';
		    		}
	                break;
	            case 'z3':
	            	if (state[0] == 1) {
	            		result['status'] = false;
						result['content'] = '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">２</span>个号码';
		    		}
	                break;
	            case 'z6':
	            	if (state[0] == 1) {
	            		result['status'] = false;
						result['content'] = '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">３</span>个号码';
		    		}
	                break;
	            default:
	            	break;
			}
		    return result;
	    }
	    
	    me.getPlayTypeByCode = function(code) {
        	code = parseInt(code, 10)
			var codeArr = ['', 'zx', 'z3', 'z6'];
			return codeArr[code];
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
			var rule = cx.Lottery.getRule(self.getType(), self.isValid());
    		if (rule['status'] !== true) {
    			cx.Alert({content: rule['content'], size: rule['size']});
    		}else {
    			var balls = self.getAllBalls();
        		if (self.getType() === 'z3') {
        			balls = self.z3ballsplit(balls);
        			self.basket.addAll(balls);
        		}else {
        			if(self.edit === 0){
                		self.basket.add(balls);
                	}else{
                		self.basket.edit(balls, self.edit);
                	}
        		}
        		self.removeAll();
                $('html, body').animate({scrollTop: $('.cast-basket .btn-main').offset().top + $('.cast-basket .btn-main')[0].scrollHeight - $(window).height()});
        	}
        });
	}
	
	cx.BoxCollection.prototype.addBall = function(boxs, modcode) {
    	var editStrs = {
            balls: [],
            betNum: 0
        };
    	$(this.boxes).each(function(k, box) {
    		box.removeAll();
    		for (var i = 0; i < boxs[k].length; ++i){
    			if (this.getType() === 'z3') {
    				box.addBall(boxs[k][i], false);
    			}else {
    				box.addBall(boxs[k][i]);
    			}
         	}
    		editStrs.balls.push(box.balls);
        });
    	editStrs.betNum = (this.getType() === 'z3' && modcode == 1) ? 1 : this.calcBetNum();
    	editStrs.playType = this.getType();
    	return editStrs;
    }
	
	cx.BoxCollection.prototype.renderBet = function() {
    	this.$el.find('.num-red').html(this.getNum(0));
        this.$el.find('.num-multiple').html(this.calcBetNum());
        this.$el.find('.num-money').html(this.calcMoney());
        var rule = cx.Lottery.getRule(this.getType(), this.isValid());
        if (rule['status'] === true) {
        	if(this.$el.find('.sub-txt').length > 0) {
        		var playType = this.getType();
        		var money = cx.Lottery.jiangjin[playType];
        		if(money > 10000) money = (money/10000)+'万';
        		var yingli = (cx.Lottery.jiangjin[playType]-this.calcMoney());
        		str = '（如中奖，奖金 <em>'+money+'</em> 元，盈利 ';
        		if (yingli > 0) {
        			str += '<em>'+yingli+'</em> 元）';
        		}else {
        			str += '<em class="green-color">'+yingli+'</em> 元）';
        		}
            	$(".sub-txt").show().html(str);
        	}
            this.$el.find('.add-basket').removeClass('btn-disabled');
        } else {
        	$(".sub-txt").hide();
            this.$el.find('.add-basket').addClass('btn-disabled');
        }
    }
	
	cx.BoxCollection.prototype.rand1 = function(playType) {
		var randStrs = {
		     balls: [],
		     betNum: 0,
		     betMoney: 0
		 };
		var startindex = cx.Lottery.getStartIndex(playType);
		var arr = cx.Lottery.getMinLength(playType);
		randStrs.betNum = 1;
		randStrs.betMoney = this.betMoney;
		var amount = cx.Lottery.getAmount(playType);
		for (i in arr) {
			randStrs.balls[i] = {};
			randStrs.balls[i]['tuo'] = [];
			while (randStrs.balls[i]['tuo'].length < arr[i]) {
		 		j = Math.floor(Math.random() * (amount[i] - startindex[i] + 1) + startindex[i]);
		 		if ($.inArray(j, randStrs.balls[i]['tuo']) === -1) randStrs.balls[i]['tuo'].push(j);
		 	}
		}
		if (playType == 'z3') randStrs.balls[0]['tuo'].push(randStrs.balls[0]['tuo'][0]);
		randStrs.playType = playType;
		return randStrs;
     }
	
	cx.BoxCollection.prototype.z3ballsplit = function(aball){
    	var ball = aball.balls[0]['tuo'], balls = {}, k = 0;
    	$.each(ball, function(i, a){
    		$.each(ball, function(j, b){
    			if (i != j) {
    				balls[k] = {
        				balls:[{'tuo' : [a, a, b]}],
        				betNum: 1,
        				betMoney: 2,
        				playType: "z3"
        			};
    				k++;
    			}
    		})
    	})
    	return balls;
    };
    
    cx.BallBox.prototype.init = function() {
		var self = this;
    	this.$balls = self.$el.find('.pick-area-ball '+self.smallel);
    	this.$balls.click(function() {
        	var $this = $(this);
        	self.BallTriger($this);
        });
        this.$el.find('.clear-balls').click(function() {
            self.removeBalls();
        });
        this.$el.find('.filter-bigs').click(function() {
            self.removeBalls();
            var ball, amount = cx.Lottery.getAmount(self.playType)[self.index], minBall = cx.Lottery.getStartIndex(self.playType)[self.index];
            for (var i = Math.ceil(amount / 2); i <= amount; ++i) {
                self.BallTriger(self.$balls.eq(i - minBall));
            }
        });
        this.$el.find('.filter-smalls').click(function() {
            self.removeBalls();
            var ball, amount = cx.Lottery.getAmount(self.playType)[self.index], minBall = cx.Lottery.getStartIndex(self.playType)[self.index];
            for (var i = minBall; i < Math.ceil(amount / 2); ++i) {
            	self.BallTriger(self.$balls.eq(i - minBall));
            }
        });
        this.$el.find('.filter-odds').click(function() {
            self.removeBalls();
            var ball, minBall = cx.Lottery.getStartIndex(self.playType)[self.index];
            for (var i = minBall; i <= cx.Lottery.getAmount(self.playType)[self.index]; ++i) {
            	if (i % 2) self.BallTriger(self.$balls.eq(i - minBall));
            }
        });
        this.$el.find('.filter-evens').click(function() {
            self.removeBalls();
            var ball, minBall = cx.Lottery.getStartIndex(self.playType)[self.index];
            for (var i = minBall; i <= cx.Lottery.getAmount(self.playType)[self.index]; ++i) {
            	if (i % 2 == 0) self.BallTriger(self.$balls.eq(i - minBall));
            }
        });
        this.$el.find('.filter-all').click(function() {
            self.removeBalls();
            var ball, minBall = cx.Lottery.getStartIndex(self.playType)[self.index];
            for (var i = minBall; i <= cx.Lottery.getAmount(self.playType)[self.index]; ++i) {
            	self.BallTriger(self.$balls.eq(i - minBall));
            }
        });
	}
    
    cx.BallBox.prototype.BallTriger = function($el, type) {
    	var ball = $el.html();
        if ($el.hasClass('selected')) {
        	$el.removeClass('selected');
        	this.removeBall(ball, type);
        }else {
        	$el.addClass('selected');
        	this.addBall(ball, type);
        }
        this.collection.renderBet();
    }
    
    cx.BallBox.prototype.calcComb = function() {
        var combCount = cx.Math.combine(this.balls.length, cx.Lottery.getMinLength(this.playType)[this.index]);
        if (this.options.playType == 'z3') combCount *= 2;
        return combCount;
    }
    
    cx.BallBox.prototype.removeAll = function() {
        this.balls = [];
    	this.$balls.removeClass('selected');
        this.collection.renderBet();
    }
    
    cx.BallBox.prototype.removeBalls = function() {
    	var self = this;
        this.balls = [];
        this.$balls.removeClass('selected');
        this.collection.renderBet();
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
        	if(string.playType !== self.playType) $("."+self.tab).filter("[data-type="+string.playType+"]").trigger('click');
        	startindex = cx.Lottery.getStartIndex(self.playType)
        	self.boxes[self.playType].removeAll();
        	$.each(string.balls, function(n, balls){
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
    
    cx.CastBasket.prototype.renderString = function(allBalls, index, hover, noedit) {
    	console.log(allBalls);
    	var tpl = '<li ';
    	if(hover) tpl += ' class="hover"'
    	tpl += ' data-index="'+index+'"><span class="bet-type">'+ cx.Lottery.getPlayTypeName(cx.Lottery.playTypes[allBalls.playType]);
        var ballTpl = [];
        $.each(allBalls.balls, function(pi, balls){
            var tmpTpl = '<span class="num-red">';
            $.each(balls['tuo'].sort(sort), function(ti, ball){
            	tmpTpl += ball + ' ';
            })
            tmpTpl += '</span>';
            ballTpl.push(tmpTpl);
        })
        ballTpl = ballTpl.join('<em>|</em>');
    	tpl += ((allBalls.betNum > 1) ? '复式' : '单式') + '</span><div class="num-group">'+ballTpl+'</div><a href="javascript:;" class="remove-str">删除</a>';
        if (allBalls.playType !== 'z3') tpl += '<a href="javascript:;" class="modify-str">修改</a>';
    	tpl += '<span class="bet-money">'+ allBalls.betMoney +'元</span></li>';
        return tpl;
    }
    
    $.extend(cx.castData, {'toCastString':function(subStrings){
    	var betStr = [], ballStr = [], preStr = [];
        $.each(subStrings, function(k, subString){
        	var playType = subString.playType || 'default', midStr = ':' + cx.Lottery.playTypes[playType], postStr = ':' + cx.Lottery.getCastPost(playType, (subString.betNum > 1));
            preStr = [];
            $.each(subString.balls, function(j, ball){
            	ballStr = [];
                $.each(ball['tuo'].sort(sort), function(i, tuo){
                	ballStr.push(tuo);
                })
                ballStr = ballStr.join(cx.Lottery.getNumberSeparator(playType));
                preStr.push(ballStr);
            })
            preStr = preStr.join(cx.Lottery.getPlaceSeparator(playType));
            betStr.push(preStr + midStr + postStr);
        })
        return betStr.join(';');
	}})

    var boxes = {'zx' : new cx.BoxCollection('.zx .box-collection'), 'z3' : new cx.BoxCollection('.z3 .box-collection'), 'z6' : new cx.BoxCollection('.z6 .box-collection')};
    
    for(var i = 1; i <= 3; ++i){
    	boxes['zx'].add(new cx.BallBox('.zx .ball-box-' + i, {playType: 'zx'}));
    }
    boxes['z3'].add(new cx.BallBox('.z3 .ball-box-1' , {playType: 'z3'}));
    boxes['z6'].add(new cx.BallBox('.z6 .ball-box-1' , {playType: 'z6'}));
        
    var multiModifier = new cx.AdderSubtractor('.multi-modifier'),
    basket = new cx.CastBasket('.cast-basket', {
        boxes: boxes,
        chases: chase,
        chaseLength: chaselength,
        multi: MULTI,
        multiModifier: multiModifier,
        playType: type,
        tabClass:'current',
        tab: 'bet-tab-hd li'
    });
    cx._basket_ = basket;
});