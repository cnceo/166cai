var cy = 1000, rest = tm, multiModifier, chaseModifier, hstyopen = 0, inteval;


(function() {
	
	window.cx || (window.cx = {});
	
	cx.Lottery = (function(){
		
		var lid = cx.Lid.KLPK, me = {};
		
		me.lid = lid;
		me.playTypes = {rx1: '1', rx2: '2', rx2dt: '22', rx3: '3', rx3dt: '32', rx4: '4', rx4dt: '42', rx5: '5', rx5dt: '52', rx6: '6', rx6dt: '62', th: '7', ths:'8', sz: '9', bz: '10', dz: '11'},
		
		me.getPlayTypeByMidCode = function(midCode) {
			var data = {'1':'rx1','2':'rx2','22':'rx2dt','3':'rx3','32':'rx3dt','4':'rx4','42':'rx4dt','5':'rx5','52':'rx5dt','6':'rx6','62':'rx6dt','7':'th','8':'ths','9':'sz','10':'bz','11':'dz'};
			return (midCode in data) ? data[midCode] : 'dz';
	    }
		
		me.jiangjin = {'rx1':5,'rx2':33,'rx2dt':33,'rx3':116,'rx3dt':116,'rx4':46,'rx4dt':46,'rx5':22,'rx5dt':22,'rx6':12,'rx6dt':12,
            	'th':90,'thbx':22,'ths':2150,'thsbx':535,'sz':400,'szbx':33,'bz':6400,'bzbx':500,'dz':88,'dzbx':7};
		
		me.typeArr = ['', '任选一', '任选二', '任选三', '任选四', '任选五', '任选六', '同花', '同花顺', '顺子', '豹子', '对子'];
		
		me.numArr = {
			0 : ['', 'A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'],
			'th' : ['同花包选', '黑桃', '红桃', '梅花', '方块'],
			'ths' : ['同花顺包选', '黑桃', '红桃', '梅花', '方块'],
			'sz' : ['顺子包选', 'A23', '234', '345', '456', '567', '678', '789', '8910', '910J', '10JQ', 'JQK', 'QKA'],
			'bz' : ['豹子包选', 'AAA', '222', '333', '444', '555', '666', '777', '888', '999', '101010', 'JJJ', 'QQQ', 'KKK'],
			'dz' : ['对子包选', 'AA', '22', '33', '44', '55', '66', '77', '88', '99', '1010', 'JJ', 'QQ', 'KK']
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
	        return 1;
	    };
	    
	    me.getPlayTypeName = function(playType) {
	        playCnNames = {1: '任一', 2: '任二单式', 21:'任二复式', 22:'任二胆拖', 3: '任三单式', 31:'任三复式', 32:'任三胆拖', 4: '任四单式', 41:'任四复式',
                	42:'任四胆拖', 5: '任五单式', 51:'任五复式', 52:'任五胆拖', 6: '任六单式', 61:'任六复式', 62:'任六胆拖', 7: '同花', 8: '同花顺', 9: '顺子', 10:'豹子', 11:'对子'};
	        cnName = playCnNames[playType] ? playCnNames[playType] : '普通';
	        return cnName;
	    };
	    
	    me.getMinLength = function(playType) {
	    	var data = {'default': [1], 'rx2': [2], 'rx2dt': [2], 'rx3': [3], 'rx3dt': [3], 'rx4': [4], 'rx4dt': [4], 'rx5': [5], 'rx5dt': [5], 'rx6': [6], 'rx6dt': [6]}
	    	playType = playType || 'default';
	    	return (playType in data) ? data[playType] : data['default'];
	    }
	    
	    me.getAmount = function(playType) {
	    	var data = {'default': [13], 'dz': [14], 'th': [5], 'sz': [13], 'ths': [5]}
	    	playType = playType || 'default';
	    	return (playType in data) ? data[playType] : data['default'];
	    }
	    
	    me.getStartIndex = function(playType) {
	    	return [1];
	    }
	    
	    me.getRule = function(playType, state) {
	    	var result = {'status':true, 'content':'', 'size':'16'}, index = parseInt(playType.replace(/(\D+)/ig, ''), 10);
	    	switch (playType) {
		    	case 'dz':
	        	case 'sz':
	        	case 'ths':
	        	case 'th':
	        	case 'bz':
	        		if (state[0] == '1') {
	            		result['status'] = false;
    					result['content'] = '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">1</span>个号码';
	            	}
	            	break;
	        	case 'rx1':
	            case 'rx2':
	            case 'rx3':
	            case 'rx4':
	            case 'rx5':
	            case 'rx6':
	            	if (state[0] == '1') {
	            		result['status'] = false;
    					result['content'] = '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">'+index+'</span>个号码';
	            	}
	            	break;
	            case 'rx2dt':
	            	for (i in state) {
	            		switch (state[i]) {
	            			case 1:
	                		case 2:
	                    	case 3:
	                    	case '5':
	                    		result['status'] = false;
	        					result['content'] = '<i class="icon-font">&#xe611;</i>请选择1个胆码，2~12个拖码，胆码＋拖码<span class="num-red">≥3</span>个';
	                    		break;
	            		}
	            	}
	            	break;
	            case 'rx3dt':
	            case 'rx4dt':
	            case 'rx5dt':
	            case 'rx6dt':
	            case 'rx7dt':
	            	for (i in state) {
	            		switch (state[i]) {
	            			case 1:
	                		case 2:
	                    	case 3:
	                    	case '5':
	                    		result['status'] = false;
	        					result['content'] = '<i class="icon-font">&#xe611;</i>请选择1~'+(index-1)+'个胆码，2~12个拖码，胆码＋拖码≥<span class="num-red">'+(index+1)+'</span>个';
	                    		break;
	            		}
	            	}
	            	break;
	            default:
	            	break;
		    }
		    return result;
	    }
	    
	    me.getPlayTypeByCode = function(code) {
        	code = parseInt(code, 10)
			var codeArr = ['', 'rx1', 'rx2', 'rx3', 'rx4', 'rx5', 'rx6', 'th', 'ths', 'sz', 'bz', 'dz'];
			return codeArr[code];
        }
	    
	    return me;
		
	})();
	
})();

$(function(){
	inteval = setInterval("countdown()", cy);
	multiModifier = new cx.AdderSubtractor('.multi-modifier-s.multi');
	chaseModifier = new cx.AdderSubtractor('.multi-modifier-s.chase');
	
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
    			if(self.edit === 0) {
            		self.basket.add(balls);
            	}else {
            		self.basket.edit(balls, self.edit);
            	}
            	self.removeAll();
            	$('.count-matches').html(self.basket.betNum);
    		}
        });
	}
	
	cx.BoxCollection.prototype.renderBet = function() {
		this.$el.find('.num-multiple').html(this.calcBetNum());
        this.$el.find('.num-money').html(this.calcMoney());
    	var rule = cx.Lottery.getRule(this.getType(), this.isValid());
        if (rule['status'] === true) {
        	this.caculateBonus(this.$el, this.getType(), this.calcMoney(), this.boxes)
            this.$el.find('.add-basket').removeClass('btn-disabled');
        } else {
        	this.$el.find('.pick-area-note').empty();
        	this.$el.find('.add-basket').addClass('btn-disabled');
        }
    }
	
	cx.BoxCollection.prototype.caculateBonus = function($el, playType, betMoney, balls) {
		var playType = this.getType(), num = this.boxes[0].balls.length, money = cx.Lottery.jiangjin[playType];
		if ($.inArray(playType, ['dz', 'sz', 'ths', 'th', 'bz']) > -1) {
			var moneybx = cx.Lottery.jiangjin[playType+'bx'];
			if ($.inArray(0, this.boxes[0].balls) === -1) {
				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+money+"元，盈利<em>"+(money-num * 2)+"元</em></span>");
			}else if (num == 1) {
				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+moneybx+"元，盈利<em>"+(moneybx-num * 2)+"元</em></span>");
			}else if (num == this.boxes[0].options.amount) {
				money = parseInt(moneybx, 10)+parseInt(money, 10);
				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+money+"元，盈利<em>"+(money-num * 2)+"元</em></span>");
			}else {
				money = parseInt(moneybx, 10)+parseInt(money, 10);
				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+moneybx+"~"+money+"元，盈利<em>"+(moneybx - num * 2)+"~"+(money-num * 2)+"元</em></span>");
			}
		}else if ($.inArray(playType, ['rx1', 'rx2', 'rx2dt']) > -1) {
			var index = parseInt(playType.replace(/(\D+)/ig, ''), 10);
			if(this.boxes[0].dans.length == 0) {
				var mn = cx.Math.combine(num, index);
				big = money * (mn >= 3 ? 3 : mn);
				small = money;
				if (big === small) {
					this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"元，盈利<em>"+(small - (mn * 2))+"元</em></span>");
				}else {
					this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"~"+big+"元，盈利<em>"+(small - (mn * 2))+"~"+(big - (mn * 2))+"元</em></span>");
				}
			}else{
				var dnum = this.boxes[0].dans.length, small = 33, big = 66, mn = cx.Math.combine(num, index - dnum) * 2;
				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"~"+big+"元，盈利<em>"+(small - mn)+"~"+(big - mn)+"元</em></span>");
			}
		}else {
			var index = parseInt(playType.replace(/(\D+)/ig, ''), 10), mn = cx.Math.combine(num, index);
			if(this.boxes[0].dans.length == 0) {
				var small = cx.Math.combine(num - 3, index - 3) * money, big = cx.Math.combine(num - 1, index - 1) * money, mn = cx.Math.combine(num, index) * 2;
        		if (big === small) {
        			this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"元，盈利<em>"+(small - mn)+"元</em></span>")
        		}else {
        			this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"~"+big+"元，盈利<em>"+(small - mn)+"~"+(big - mn)+"元</em></span>")
        		}
        		
			}else{
				var dnum = this.boxes[0].dans.length, big = cx.Math.combine(num, index - dnum) * money, mn = cx.Math.combine(num, index - dnum) * 2;
				if (index >= 5) {
					var small = cx.Math.combine(num - 3, index - dnum - 3) * money;
				}else {
					var small = money;
				}
				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"~"+big+"元，盈利<em>"+(small - mn)+"~"+(big - mn)+"元</em></span>");
			}
		}
	}
	
	cx.BallBox.prototype.init = function() {
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
        this.$el.find('.filter-all').click(function() {
            self.removeBalls();
            var ball, minBall = cx.Lottery.getStartIndex(self.playType)[self.index];
            for (var i = minBall; i <= cx.Lottery.getAmount(self.playType)[self.index]; ++i) {
            	if ($.inArray(i, self.dans) === -1) self.BallTriger(self.$balls.eq(i - minBall));
            }
        });
	}
	
	cx.BallBox.prototype.BallTriger = function ($el, type) {        	
    	var ball = $el.data('num');
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
        } else if (!this.ballValid(type)) {
        	var rule = cx.Lottery.getRule(this.playType, [pad(this.collection.boxes.length, 5, this.index)]);
        	cx.Alert({content: rule['content'], size: rule['size']});
        } else {
        	arr.eq(ball-1).removeClass('selected').addClass('dt-pick');
        	this.removeBall(ball, t);
        	$el.removeClass('dt-pick').addClass('selected');
        	this.addBall(ball, type);
        }
        if (this.balls.length == 1) $('html,body').animate({scrollTop: $('.bet-klpk-bd').offset().top + 30}, 200);
        this.collection.renderBet();
    }
	
	$.extend(cx.castData, {'toCastString':function(subStrings, zj){
		var danStr = '', tuoStr = '', betArr = [], danArr = [], tuoArr = [], preArr = [];
		$.each(subStrings, function(k, subString) {
			var playType = subString.playType || 'default', midStr = ':' + cx.Lottery.playTypes[playType], postStr = ':' + cx.Lottery.getCastPost(playType)
        	if ($.inArray(playType, ['rx2', 'rx3', 'rx4', 'rx5', 'rx6']) > -1 && subString.betNum > 1) midStr += '1';
            preArr = [];
            $.each(subString.balls, function(j, ball) {
            	danStr = '', tuoStr = '', danArr = [], tuoArr = [];
            	if (ball['dan'] !== undefined) {
            		$.each(ball['dan'].sort(sort), function(i, dan) {
            			danArr.push(pad(2, dan, 1));
            		})
            		danStr = danArr.join(cx.Lottery.getNumberSeparator(playType)) + "$";
            	}
                $.each(ball['tuo'].sort(sort), function(i, tuo) {
                	tuoArr.push(pad(2, tuo, 1));
                })
                tuoStr = tuoArr.join(cx.Lottery.getNumberSeparator(playType));
                preArr.push(danStr + tuoStr);
            })
            betArr.push(preArr.join(cx.Lottery.getPlaceSeparator(playType)) + midStr + postStr);
		})
        return betArr.join(';');
	}})

	var boxes = {
		'th':new cx.BoxCollection('.php_th .btn-group'),
		'sz':new cx.BoxCollection('.php_sz .btn-group'),
		'ths':new cx.BoxCollection('.php_ths .btn-group'),
		'dz':new cx.BoxCollection('.php_dz .btn-group'),
		'bz':new cx.BoxCollection('.php_bz .btn-group'),
		'rx1':new cx.BoxCollection('.php_rx1 .btn-group')
	};
	boxes['th'].add(new cx.BallBox('.php_th .pre-box', {playType:'th', smallel:'li'}));
	boxes['sz'].add(new cx.BallBox('.php_sz .pre-box', {playType:'sz', smallel:'li'}));
	boxes['ths'].add(new cx.BallBox('.php_ths .pre-box', {playType:'ths', smallel:'li'}));
	boxes['dz'].add(new cx.BallBox('.php_dz .pre-box', {playType:'dz', smallel:'li'}));
	boxes['bz'].add(new cx.BallBox('.php_bz .pre-box', {playType:'bz', smallel:'li'}));
	boxes['rx1'].add(new cx.BallBox('.php_rx1 .pre-box', {playType:'rx1', smallel:'li'}));
	
	for (var j = 2; j <= 6; j++){
		boxes['rx'+j] = new cx.BoxCollection('.php_rx'+j+' .default .btn-group');
		boxes['rx'+j].add(new cx.BallBox('.php_rx'+j+' .default .pre-box', {playType:'rx'+j, smallel:'li'}));
    	boxes['rx'+j+'dt'] =  new cx.BoxCollection('.php_rx'+j+' .bet-dt-klpk .btn-group');
	    for (i = 1; i <= 1; ++i) {
	        boxes['rx'+j+'dt'].add(new cx.BallBox('.php_rx'+j+' .bet-dt-klpk .pick-area:last', {
	            hasdan: true,
	            dmax: j-1,
	            tmin: 2,
	            dtmin: j+1,
	            playType: 'rx'+j+'dt', 
	            smallel:'li'
	        },'.php_rx'+j+' .bet-dt-klpk .pick-area:first'));
	    }
	}
	
	cx.CastBasket.prototype.renderString = function(allBalls, index, hover, noedit){
		$(".count-matches").html(this.betNum);
		var pid = cx.Lottery.playTypes[allBalls.playType];
		allBalls.balls[0]['tuo'].sort(sort);
		if (allBalls.balls[0]['dan']) {
			allBalls.balls[0]['dan'].sort(sort);
			var typeName = cx.Lottery.getPlayTypeName((pid.length == 1) ? pid+'2' : pid);
		}else {
			var typeName = cx.Lottery.typeArr[pid];
		}
		var str = "<tr data-index='"+index+"'><td class='tal'>"+typeName+"</td><td class='tal'>";
		if (allBalls.balls[0]['dan']) {
			str += "<span class='klpk-ball'>（</span>";
			$.each(allBalls.balls[0]['dan'], function(i, dan){
				str += "<span class='klpk-ball'>"+cx.Lottery.numArr[0][parseInt(dan, 10)]+"</span>";
			})
			str += "<span class='klpk-ball'>）</span>";
		}
		$.each(allBalls.balls[0]['tuo'], function(i, tuo){
			if ($.inArray(allBalls.playType, ['th', 'ths', 'sz', 'bz', 'dz']) > -1) {
				str += "<span class='klpk-ball'>"+cx.Lottery.numArr[allBalls.playType][parseInt(tuo, 10)]+"</span>";
			}else {
				str += "<span class='klpk-ball'>"+cx.Lottery.numArr[0][parseInt(tuo, 10)]+"</span>";
			}
		})
		str += "</td><td class='fcw'>"+(allBalls.betNum * 2)+"元</td><td><span><a class='del-match' href='javascript:;'>×</a></span></td></tr>";
		return str;
	};
	cx.CastBasket.prototype.setChaseByIssue = function(num, multi) {
    	var tbstr = '', j = 0, issue = [];
    	this.chase.chaseMulti = 0;
    	this.chase.chaseMoney = 0;
    	this.chase.chaseLength = num;
    	this.chase.chases = {};
    	
    	if (num > 0) {
    		for (i in chases) {
    			if (j < num) {
    				issue.push(i);
    				this.chase.setChaseByI(i);
    				this.chase.chases[i].multi = multi;
    				this.chase.chases[i].money = multi * this.betMoney;
    				this.chase.chaseMulti += multi;
            		j++;
    			}else {
    				break;
    			}
    		}
    		this.chase.chaseMoney = this.chase.chaseMulti * this.betMoney;
    	}
    },
    cx.CastBasket.prototype.renderBetMoney = function() {
        if (cx._basket_.orderType == 1) {
        	$(".numbox .bet-num").html(this.betMoney * this.multiModifier.getValue() * this.chase.chaseLength);
        }else {
        	$(".numbox .bet-num").html(this.betMoney * this.multiModifier.getValue());
        }
    }
	$('.seleFiveBoxScroll').on('click', '.del-match', function(){
		var $tr = $(this).closest('tr');
        var index = $tr.attr('data-index');
        for (i in cx._basket_.boxes) {
        	if(index === cx._basket_.boxes[i].edit) cx._basket_.boxes[i].clearButton(i);
        }
        $tr.remove();
        cx._basket_.remove($tr.data('index'));
        $(".count-matches").html(cx._basket_.betNum);
        if (cx._basket_.betNum == 0) {
			$('.seleFiveTit').removeClass('seleFiveTit2');
            $('.seleFiveBox').hide();
		}
	})
	$('body').on('click', '.clear-matches', function() {
		cx.Confirm({
    		single: '您确定要删除已选择的选项吗？',
            btns: [{type: 'confirm',txt: '确定',},{type: 'cancel',txt: '取消'}],
            confirmCb: function () {
            	cx._basket_.removeAll();
                for (i in self.boxes) {
                	cx._basket_.boxes[i].clearButton(i);
                }
                $(".count-matches").html('0');
                $('.seleFiveTit').removeClass('seleFiveTit2');
                $('.seleFiveBox').hide();
            }
        });
    });
	var basket = new cx.CastBasket('.cast-panel', {
        boxes: boxes,
        multi: MULTI,
        tab: 'bet-type-link li',
        tabClass: 'selected',
        playType: 'dz',
        chases: chase,
        setStatus: 1,
        chaseLength: chaselength,
        multiModifier: multiModifier,
        chaseModifier: chaseModifier,
        $castList:$(".selected-matches tbody")
    });
	
    cx._basket_ = basket;
    
    cx.Chase.prototype.init = function() {
		var _this = cx._basket_;
		var self = this;
		
    	this.chaseMulti = 10;
    	this.chaseMoney = 20;
    	
		$('.klpk-qr').find(".setStatus").click(function(){
        	self.setStatus = 0;
        	if ($(this).attr('checked') == 'checked') self.setStatus = 1;
        });
	}
    
    chaseModifier.setCb(function() {
    	cx._basket_.chase.setChaseByIssue(parseInt(this.getValue(), 10), cx._basket_.multiModifier.value);
    	cx._basket_.renderBetMoney();
    });
    
	$(".lottery-info-time span strong").html(ISSUE.substring(6, 8));
	renderTime();
	
	// 底部操作条悬停
    var eleFixedBox = $('.ele-fixed-box'), $castPanel = eleFixedBox.find('.cast-panel'), castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top,
    onScroll = function () {
        var scrollTop = $(document).scrollTop() + $(window).height();
        
        if (scrollTop >= castPanelTop) {
            $castPanel.removeClass('cast-panel-fixed');
            if(!-[1,]&&!window.XMLHttpRequest) $castPanel.css({'position': 'static'}); 
        }else {
            $castPanel.addClass('cast-panel-fixed');
            if(!-[1,]&&!window.XMLHttpRequest) $castPanel.css({'position': 'absolute', 'bottom': 'auto', 'top': scrollTop-$castPanel.height() + 'px'}); 
        }
    }
    $('.bet-type-link').on('click', 'li', function(){
    	cx._basket_.setType($(this).data('type'));
        castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
        onScroll();
    })
    
    $(".seleFiveTit").click(function () {
		if (cx._basket_.betNum > 0) $(this).toggleClass("seleFiveTit2").next("div.seleFiveBox").toggle();
	});
    
    $(".gg-type").click(function(){
		$("a[data-ordertype]").toggleClass('selected').each(function(){
			if ($(this).hasClass('selected')) cx._basket_.orderType = $(this).data('ordertype');
		})
		if (cx._basket_.orderType == 1) {
			$(".chase-div, .klpk-qr").show();
		}else {
			$(".chase-div, .klpk-qr").hide();
		}
		cx._basket_.renderBetMoney();
	});
    
    var Throttle;
    $(window).scroll(function () {
        onScroll();
    });
    $(window).resize(function(){
        clearTimeout(Throttle);
        Throttle = setTimeout(function(){
            onScroll();
        }, 100)
    });
    onScroll();
	
	$('.table-zs-hd').on('click', 'li', function(){
		if ($(this).hasClass('myorder') && ($(this).hasClass('not-login') || !$.cookie('name_ie'))) {
	        cx.PopAjax.login();
	        return ;
		}
		
        $(this).toggleClass('selected').siblings().removeClass('selected');
        $(this).parents('.table-zs').find('.table-zs-item').eq($(this).index()).toggle().siblings().hide();
        
        if ($(this).hasClass('selected') && $(this).hasClass('myorder')) {
        	$.ajax({
    			url: "/ajax/getOrders/klpk",
    			dataType: 'json',
    			success: function(data) {
    				var str = '';
    				if (data.length > 0) {
    					for (i in data) {
    						str += '<tr><td>'+data[i].created+'</td><td>'+data[i].issue+'</td><td class="tal" title="';
    						var codes = data[i].codes.split(';'),carr = [], carr0 = [], nArr;
    						for (j in codes) {
    							carr = codes[j].split(':');
    							nArr = ($.inArray(carr[1], ['7', '8', '9', '10', '11']) > -1) ? cx.Lottery.numArr[cx.Lottery.getPlayTypeByCode(carr[1])] : cx.Lottery.numArr[0];
    							str += cx.Lottery.getPlayTypeName(carr[1])+':';
    							carr0 = carr[0].split('$');
    							if (carr0.length == 1) {
    								for (k in carr0[0].split(',')) {
    									str += nArr[parseInt(carr0[0].split(',')[k], 10)]+" ";
    								}
    							}else {
    								str += '（';
    								for (k in carr0[0].split(',')) {
    									str += nArr[parseInt(carr0[0].split(',')[k], 10)]+" ";
    								}
    								str = str.slice(0, -1)+'）';
    								for (k in carr0[1].split(',')) {
    									str += nArr[parseInt(carr0[1].split(',')[k], 10)]+" ";
    								}
    							}
    							str = str.slice(0, -1)+'; ';
    						}
    						
    						carr = codes[0].split(':');
    						nArr = ($.inArray(carr[1], ['7', '8', '9', '10', '11']) > -1) ? cx.Lottery.numArr[cx.Lottery.getPlayTypeByCode(carr[1])] : cx.Lottery.numArr[0];
    						str += '"><div class="text-overflow">'+cx.Lottery.getPlayTypeName(carr[1])+' <span class="specil-color">';
    						carr0 = carr[0].split('$');
							if (carr0.length == 1) {
								for (k in carr0[0].split(',')) {
									str += nArr[parseInt(carr0[0].split(',')[k], 10)]+" ";
								}
							}else {
								str += '（';
								for (k in carr0[0].split(',')) {
									str += nArr[parseInt(carr0[0].split(',')[k], 10)]+" ";
								}
								str = str.slice(0, -1)+'）';
								for (k in carr0[1].split(',')) {
									str += nArr[parseInt(carr0[1].split(',')[k], 10)]+" ";
								}
							}
    						if (j > 0) {
    							str += '...';
    						}
    						str += '</div></td><td><span class="fcs">'+fmoney(parseInt(data[i].money, 10) / 100, 3)+'.00</span></td><td><span class="fcs">'+cx.Order.getStatus(data[i].status, 3)+'</span></td>';
    						if (data[i].margin > 0) {
    							str += '<td><img src="/caipiaoimg/v1.1/img/gold.png" alt="">&nbsp;<strong class="spec arial">'+fmoney(data[i].margin / 100, 3)+'.00</strong></td>';
    						}else if($.inArray(data[i].status, ['1000', '2000']) === -1) {
    							str += '<td>--</td>'
    						}else {
    							str += '<td>'+fmoney(data[i].margin / 100, 3)+'.00</td>';
    						}
    						str += '<td><a target="_blank" href="/orders/detail/'+data[i].orderId+'">查看详情</a>';
    						if (data[i].ljzf == 1) {
    							str += '<a href="javascript:cx.castCb({orderId:\''+data[i].orderId+'\'}, {ctype:\'paysearch\', orderType:0});"><span class="num-red">立即支付</span></a>';
    						}else {
    							str += '<a target="_blank" href="/klpk?orderId='+data[i].orderId+'">继续预约</a></td><td></td></tr>';
    						}
    					}
    				} else {
    					str += '<tr><td colspan="8" style="height: 100px;">亲，您三个月内还没有订单哦！</td></tr>'
    				}
    				$(".my-order tbody").html(str);
    				castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
    	            onScroll();
    			}
    		})
        }else {
        	if ($(this).hasClass('selected')) {
    			var playType = $(this).find('a:first').data('playtype');
    			hstyopen = 1;
    			$.ajax({
    				url: '/ajax/getHistory/klpk',
    				dataType: 'json',
    				beforeSend: function() {
    					$(".ykj-info tbody").html("<tr style='height:250px'><td colspan='22'><div class='pop-loading'><img src='/caipiaoimg/v1.1/img/pop-loading.gif' width='28' height='28' alt=''></div>拼命加载中，请稍等...</td></tr>");
    				},
    				success: function(data) {
    					hsty = eval(data.hsty);//重置历史数据全局变量
    				    mall = eval(data.miss);//重置遗漏数据全局变量
    				    rfshhisty(playType);
    				    castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
        	            onScroll();
    				}
    			})
    		}else {
    			hstyopen = 0;
    		}
        }
    })
	
	$(".bet-klpk-ft dt").click(function(){
		var iss, str = '';
		if (!$(this).hasClass('active')) {
			$.ajax({
				url: '/ajax/getKj/klpk',
				dataType: 'json',
				success: function(data) {
					str = '';
					for (i = 1; i <= 22; i++) {
						iss = padd(i);
						str += "<tr><th>"+iss+"</th><td>";
						if (data[iss] && data[iss].award) {
							for (j = 0; j < 3; j++) {
								str += "<span class='klpk-num-"+data[iss].award.split('|')[1].split(',')[j].toLowerCase()+"'>"+cx.Lottery.numArr[0][parseInt(data[iss].award.split('|')[0].split(',')[j], 10)]+"</span>";
								data[iss].award.split('|')
							}
							str += "</td><td>"+data[iss].type+"</td></tr>";
						}else {
							str += "</td><td></td><td></td></tr>"
						}
						$(".klpk-table-kj tbody:first").html(str);
					}
					str = '';
					for (i = 23; i <= 44; i++) {
						str += "<tr><th>"+i+"</th><td>";
						if (data[i] && data[i].award) {
							for (j = 0; j < 3; j++) {
								str += "<span class='klpk-num-"+data[i].award.split('|')[1].split(',')[j].toLowerCase()+"'>"+cx.Lottery.numArr[0][parseInt(data[i].award.split('|')[0].split(',')[j], 10)]+"</span>";
								data[i].award.split('|')
							}
							str += "</td><td>"+data[i].type+"</td></tr>";
						}else {
							str += "</td><td></td><td></td></tr>"
						}
						$(".klpk-table-kj tbody:eq(1)").html(str);
					}
					str = '';
					for (i = 45; i <= 66; i++) {
						str += "<tr><th>"+i+"</th><td>";
						if (data[i] && data[i].award) {
							for (j = 0; j < 3; j++) {
								str += "<span class='klpk-num-"+data[i].award.split('|')[1].split(',')[j].toLowerCase()+"'>"+cx.Lottery.numArr[0][parseInt(data[i].award.split('|')[0].split(',')[j], 10)]+"</span>";
								data[i].award.split('|')
							}
							str += "</td><td>"+data[i].type+"</td></tr>";
						}else {
							str += "</td><td></td><td></td></tr>"
						}
						$(".klpk-table-kj tbody:eq(2)").html(str);
					}
					str = '';
					for (i = 67; i <= 88; i++) {
						if (i <= 88) {
							str += "<tr><th>"+i+"</th><td>";
							if (data[i] && data[i].award) {
								for (j = 0; j < 3; j++) {
									str += "<span class='klpk-num-"+data[i].award.split('|')[1].split(',')[j].toLowerCase()+"'>"+cx.Lottery.numArr[0][parseInt(data[i].award.split('|')[0].split(',')[j], 10)]+"</span>";
									data[i].award.split('|')
								}
								str += "</td><td>"+data[i].type+"</td></tr>";
							}else {
								str += "</td><td></td><td></td></tr>"
							}
						}else {
							str += '<tr><th></th><td></td><td></td><td></td></tr>';
						}
						$(".klpk-table-kj tbody:last").html(str);
					}
				}
			})
		}
	})
})

function renderTime() {
	$(".time").html(maketstr(tm));
	if (atm > 0 && atm < 100000) {
		$("body").find(".atime").html(maketstr(atm));
	}else {
		$("body").find(".atime").parents('td').html('正在开奖中...');
	}
	if (atm > 0 && atm < 100000) {
		$(".kj-ing").show().html(maketstr(atm)+"后开奖");
		$(".kj-area ul li:last").html('形态：--');
		$(".kj-num").hide();
	}else if(vJson.length == 5) {
		$(".kj-num").show().html('<span>开</span><span>奖</span><span>中</span>');
		$(".kj-ing").hide();
		$(".kj-area ul li:last").html('形态：--');
	}else {
		var str = '', xt = '散牌', awdArr = [parseInt(vJson[0], 10), parseInt(vJson[1], 10), parseInt(vJson[2], 10)].sort();
		for (var i = 0; i < 3; i++) {
			str += "<span class='kj-num-"+vJson[i+3].toLowerCase()+"' style='position: relative'>"+cx.Lottery.numArr[0][parseInt(vJson[i], 10)]+"</span>";
			if (i == 0) {
				str += '<span>开</span>';
			}else if (i == 1) {
				str += '<span>奖</span>';
			}else {
				str += '<span>中</span>';
			}
		}
		if (vJson[3] == vJson[4] && vJson[4] == vJson[5]) xt = '同花';
		if (vJson[0] == vJson[1] && vJson[1] == vJson[2]) {
			xt = '豹子';
		}else if (vJson[0] == vJson[1] || vJson[1] == vJson[2] || vJson[2] == vJson[3]) {
			xt = '对子';
		}else if ((parseInt(vJson[0], 10) + 1 == parseInt(vJson[1], 10) && parseInt(vJson[1], 10) + 1 == parseInt(vJson[2], 10))
				|| (awdArr[0] == 1 && awdArr[1] == 12 && awdArr[2] == 13)) {
			if (xt === '同花') {
				xt = '同花顺';
			}else {
				xt = '顺子';
			}
		}
		$(".kj-area ul li:last").html("形态:<strong>"+xt+"</strong>");
		$(".kj-num").show().html(str);
		$(".kj-ing").hide();
	}
}

function render(data) {
	$(".lottery-info-time span strong").html(data.issue.substring(6, 8));
	$(".kj-periods em").html(data.prev.substring(6, 8));
	$(".arrow-tag").html("剩"+data.rest+"期<i></i>");
	if (hstyopen) rfshhisty(cx._basket_.playType);
}

var rfshhisty = function(type) {
	var str = '', ms, k, m;
	for (i in hsty) {
		str += '<tr';
		var awardArr = [];
		if (hsty[i].awardNum) {
			str += "><td>"+hsty[i].issue+"</td><td class='klpk-ball'>";
			awardArr = hsty[i].awardNum.split('|');
			for (j in awardArr[0].split(',')) {
				str += "<span class='klpk-num-"+awardArr[1].split(',')[j].toLowerCase()+"'>"+cx.Lottery.numArr[0][parseInt(awardArr[0].split(',')[j], 10)]+"</span>"
			}
			str += "</td><td><span>"+hsty[i].type+"</span></td>";
			if (mall[i]) {
				ms = mall[i][0].split('|')[0].split(',');
				for (j in ms) {
					k = padd(parseInt(j, 10)+1);
					if ($.inArray(k, awardArr[0].split(',')) > -1) {
						m = 0;
						for (l in awardArr[0].split(',')) {
							if (k == awardArr[0].split(',')[l]) {
								m++;
							}
						}
						str += "<td class='klpk-red'><span class='selected selected"+m+"'>"+cx.Lottery.numArr[0][parseInt(j, 10)+1]+"<i></i></span></td>";
					}else {
						str += "<td class='klpk-red'><span>"+ms[j]+"<i></i></span></td>";
					}
					
				}
				str += "</tr>";
			}else {
				str += "<td colspan='20'></td>"
			}
		}else if (hsty[i].prev && atm > 0 && atm < 100000) {
			str += "><td>"+hsty[i].issue+"</td><td colspan='21'><em class='main-color atime'>"+maketstr(atm)+"</em>后开奖...</td>";
		}else if (hsty[i].prev) {
			str += "><td>"+hsty[i].issue+"</td><td colspan='21'>正在开奖中...</td>";
		}else {
			str += "><td>"+hsty[i].issue+"</td><td colspan='21'></td>";
		}
	}
	$(".php_"+type+" .ykj-info table tbody").html(str);
}

function fmoney(s) {   
	s = s.toString().split("").reverse().join("").substring(0, s.toString().length);
	return s.replace(/(\d{3})/g, '$1,').split("").reverse().join("").replace(',', '');
} 
