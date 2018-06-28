var td, cy = 1000, rest = tm, inteval, boxes = {};

(function() {
	
	window.cx || (window.cx = {});
	
	cx.Lottery = (function(){
		
		var lid = cx.Lid.GDSYXW, me = {}; 
		
		me.lid = lid;
		me.playTypes = {'default': '05', q1: '01', rx2: '02', rx2dt: '02', rx3: '03', rx3dt: '03', rx4: '04', rx4dt: '04', rx5: '05', rx5dt: '05', rx6: '06', rx6dt: '06', 
				rx7: '07', rx7dt: '07', rx8: '08', qzhi2: '09', qzhi3: '10', qzu2: '11', qzu2dt: '11', qzu3: '12', qzu3dt: '12'};
		
		me.getPlayTypeByMidCode = function(midCode, postCode) {
			var data = {'01':'q1', '02':'rx2', '03':'rx3', '04':'rx4', '05':'rx5', '06':'rx6', '07':'rx7', '08':'rx8', '09':'qzhi2', '10':'qzhi3', '11':'qzu2', '12':'qzu3', '13':'lexuan3', '14':'lexuan4', '15':'lexuan5'}, playType = (midCode in data) ? data[midCode] : 'rx8';
			return postCode == '05' ? playType+"dt" : playType;
	    }
		
		me.jiangjin = {q1:'13',rx2:'6',rx2dt:'6',rx3:'19',rx3dt:'19',rx4:'78',rx4dt:'78',rx5:'540',rx5dt:'540',rx6:'90',
				rx6dt:'90',rx7:'26',rx7dt:'26',rx8:'9',qzhi2:'130',qzhi3:'1170',qzu2:'65',qzu2dt:'65',qzu3:'195',qzu3dt:'195'};
		
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
	        var CAST_POST = {'default': '01', 'rx2': '01', 'rx2dt': '05', 'rx3': '01', 'rx3dt': '05', 'rx4': '01', 'rx4dt': '05', 'rx5': '01', 'rx5dt': '05', 'rx6': '01',
	                'rx6dt': '05', 'rx7': '01', 'rx7dt': '05', 'rx8': '01', 'q1':'01', 'qzhi2': '01', 'qzhi3': '01', 'qzu2': '01', 'qzu2dt': '05', 'qzu3': '01', 'qzu3dt': '05'};
	        playType || (playType = 'default');
	        if (playType in CAST_POST) post = CAST_POST[playType];
	        return post;
	    };
	    
	    me.getPlayTypeName = function(playType) {
	    	playCnNames = {'01': '前一', '02': '任二', '03': '任三', '04': '任四', '05': '任五', '06': '任六', '07': '任七', '08': '任八', '09': '前二直选', '10': '前三直选', '11': '前二组选', '12': '前三组选'};
            return playCnNames[playType];
	    };
	    
	    me.getMinLength = function(playType) {
	    	var data = {'default': [1], 'rx2': [2], 'rx2dt': [2], 'rx3': [3], 'rx3dt': [3], 'rx4': [4], 'rx4dt': [4], 'rx5': [5], 'rx5dt': [5], 'rx6': [6],
	    		'rx6dt': [6], 'rx7': [7], 'rx7dt': [7], 'rx8': [8], 'q1': [1], 'qzu2': [2], 'qzu2dt': [2], 'qzhi2': [1, 1], 'qzu3': [3], 'qzu3dt': [3], 'qzhi3': [1, 1, 1]}
	    	playType = playType || 'default';
	    	return data[playType];
	    }
	    
	    me.getAmount = function(playType) {
	    	var data = {'default': [11], 'rx2': [11], 'rx2dt': [11], 'rx3': [11], 'rx3dt': [11], 'rx4': [11], 'rx4dt': [11], 'rx5': [11], 'rx5dt': [11], 'rx6': [11], 'rx6dt': [11], 
	    			'rx7': [11], 'rx7dt': [11], 'rx8': [11], 'q1': [11], 'qzu2': [11], 'qzu2dt': [11], 'qzhi2': [11, 11], 'qzu3': [11], 'qzu3dt': [11], 'qzhi3': [11, 11, 11]}
	    	playType = playType || 'default';
	    	return data[playType];
	    }
	    
	    me.getStartIndex = function(playType) {
	    	var data = {'default': [1], 'qzhi2': [1, 1], 'qzhi3': [1, 1, 1]};
	    	return (playType in data) ? data[playType] : data['default'];
	    }
	    
	    me.getRule = function(playType, state) {
	    	var result = {'status':true, 'content':'', 'size':'16'}, index = parseInt(playType.replace(/(\D+)/ig, ''), 10);
	    	switch (playType) {
		    	case 'q1':
		        case 'rx2':
		        case 'rx3':
		        case 'rx4':
		        case 'rx5':
		        case 'rx6':
		        case 'rx7':
		        case 'rx8':
		        case 'qzu2':
		        case 'qzu3':
		        	if (state[0] == '1') {
		        		result['status'] = false;
    					result['content'] = '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">'+index+'</span>个号码';
		        	}
		        	break;
		        case 'rx2dt':
		        case 'qzu2dt':
		        	for (i in state) {
		        		switch (state[i]) {
		        			case 1:
		            		case 2:
		                	case 3:
		                	case '5':
		                		result['status'] = false;
		    					result['content'] = '<i class="icon-font">&#xe611;</i>请选择1个胆码，2~10个拖码，胆码＋拖码<span class="num-red">≥3</span>个';
		                		break;
		        		}
		        	}
		        	break;
		        case 'rx3dt':
		        case 'rx4dt':
		        case 'rx5dt':
		        case 'rx6dt':
		        case 'rx7dt':
		        case 'qzu3dt':
		        	for (i in state) {
		        		switch (state[i]) {
		        			case 1:
		            		case 2:
		                	case 3:
		                	case '5':
		                		result['status'] = false;
		    					result['content'] = '<i class="icon-font">&#xe611;</i>请选择1~'+(index-1)+'个胆码，2~10个拖码，胆码＋拖码≥<span class="num-red">'+(index+1)+'</span>个';
		                		break;
		        		}
		        	}
		        	break;
		        case 'qzhi2':
		        	if($.inArray('01', state) > -1 || $.inArray('10', state) > -1 || $.inArray('11', state) > -1) {
		        		result['status'] = false;
    					result['content'] = '<i class="icon-font">&#xe611;</i>每位至少选择1个号码，且相互不重复';
		        	}
		            break;
		        case 'qzhi3':
		        	for (i in state) {
		        		if (state[i].indexOf('1') > -1) {
		        			result['status'] = false;
	    					result['content'] = '<i class="icon-font">&#xe611;</i>每位至少选择1个号码，且相互不重复';
	                		break;//退出for循环
		        		}
		        	}
		            break;
		        default:
		        break;
		    }
		    return result;
	    }
	    
	    me.getPlayTypeByCode = function(lotteryId, code) {
        	code = parseInt(code, 10)
			var codeArr = ['', 'q1', 'rx2', 'rx3', 'rx4', 'rx5', 'rx6', 'rx7', 'rx8', 'qzhi2', 'qzhi3', 'qzu2', 'qzu3'];
			return codeArr[code];
        }
	    
	    return me;
		
	})();
	
})();

$(function() {
	
	cx.BoxCollection.prototype.renderBet = function() {
		this.$el.find('.num-red:eq(0)').html(this.getNum(0)[0]+this.getNum(0)[1]);
		this.$el.find('.num-red:eq(1)').html(this.getNum(0)[0]);
		this.$el.find('.num-red:eq(2)').html(this.getNum(0)[1]);
    	this.$el.find('.num-multiple').html(this.calcBetNum());
        this.$el.find('.num-money').html(this.calcMoney());
    	this.$el.find(".sub-txt1").hide();
    	var rule = cx.Lottery.getRule(this.getType(), this.isValid());
        if (rule['status'] === true) {
    		this.$el.find(".sub-txt").show();
    		this.caculateBonus(this.$el, this.getType(), this.calcMoney(), this.boxes);
            this.$el.find('.add-basket').removeClass('btn-disabled');
        } else {
        	this.$el.find(".sub-txt").hide();
        	this.$el.find('.add-basket').addClass('btn-disabled');
        }
    }
	
	cx.BoxCollection.prototype.rand1 = function(playType) {
		var randStrs = {balls: [], betNum: 0, betMoney: 0}, startindex = cx.Lottery.getStartIndex(playType), arr = cx.Lottery.getMinLength(playType), amount = cx.Lottery.getAmount(playType);
    	randStrs.betNum = 1;
    	randStrs.betMoney = this.betMoney;
    	for (i in arr) {
    		randStrs.balls[i] = {};
    		randStrs.balls[i]['tuo'] = [];
    		while (randStrs.balls[i]['tuo'].length < arr[i]) {
        		j = Math.floor(Math.random() * (amount[i] - startindex[i] + 1) + startindex[i]);
        		if ($.inArray(playType, ['qzhi2', 'qzhi3']) > -1) {
        			var eflag = true;
        			for (k in randStrs.balls) {
        				if ($.inArray(j, randStrs.balls[k]['tuo']) > -1) eflag = false;
        			}
        			if (eflag) randStrs.balls[i]['tuo'].push(j);
        		} else {
        			if ($.inArray(j, randStrs.balls[i]['tuo']) === -1) randStrs.balls[i]['tuo'].push(j);
        		}
        	}
    	}
    	playType = playType.replace(/dt/, '');
        randStrs.playType = playType;
        return randStrs;
	}
	
	cx.BoxCollection.prototype.caculateBonus = function($el, playType, betMoney, balls) {
		var index = parseInt(playType.replace(/(\D+)/ig, ''), 10), jiangjin = cx.Lottery.jiangjin;
		switch (playType) {
			case 'rx2':
			case 'rx3':
			case 'rx4':
			case 'rx5':
				var money = jiangjin[playType], num = balls[0].balls.length, smalljj = cx.Math.combine((num > 6 + index ? num - 6 : index), index) * money,
				bigjj = cx.Math.combine(num > 5 ? 5 : num, index) * money, smallyl = smalljj-betMoney, bigyl = bigjj-betMoney;
				break;
			case 'rx2dt':
			case 'rx3dt':
			case 'rx4dt':
			case 'rx5dt':
				var money = jiangjin[playType], num = balls[0].balls.length, dnum = balls[0].dans.length, smalljj = cx.Math.combine(num -6 > index - dnum ? num-6:index - dnum, index - dnum) * money, 
				bigjj = cx.Math.combine(5 - dnum > num ? num : 5-dnum, index-dnum) * money, smallyl = smalljj-betMoney, bigyl = bigjj-betMoney;
				break;
			case 'rx6':
			case 'rx7':
			case 'rx8':
				var money = jiangjin[playType], num = balls[0].balls.length, smalljj = bigjj = cx.Math.combine(num-5, index-5) * money, smallyl = bigyl = bigjj-betMoney;
				break;
			case 'rx6dt':
			case 'rx7dt':
				var money = jiangjin[playType], num = balls[0].balls.length, dnum = balls[0].dans.length, bigjj = dnum < 5 ? cx.Math.combine(num-5+dnum, index - 5) * money : cx.Math.combine(num, index - dnum) * money, 
				smalljj = dnum < index - 5 ? cx.Math.combine(num-5, index - dnum - 5) * money : money, smallyl = smalljj-betMoney, bigyl = bigjj-betMoney;
				break;
			case 'q1':
			case 'qzu2':
			case 'qzu3':
			case 'qzu2dt':
			case 'qzu3dt':
			case 'qzhi2':
			case 'qzhi3':
				var money = jiangjin[playType], smalljj = bigjj = money, smallyl = bigyl = money-betMoney;
				break;
		}
		str = '（如中奖，奖金 <span class="main-color-s">'
		if (smalljj == bigjj) {
			str += smalljj;
		} else {
			str += smalljj+'</span>~<span class="main-color-s">'+bigjj;
		}
		str += '</span> 元，';
		if (smallyl == bigyl) {
			if (smallyl >= 0) {
				str += '盈利 <span class="main-color-s">'+smallyl;
			} else {
				str += '盈利 <span class="green-color">'+smallyl;
			}
		} else {
			if (smallyl >= 0) {
				str += '盈利 <span class="main-color-s">'+smallyl;
			} else {
				str += '盈利 <span class="green-color">'+smallyl;
			}
			if (bigyl >= 0) {
				str += '</span>~<span class="main-color-s">'+bigyl;
			} else {
				str += '</span>~<span class="green-color">'+bigyl;
			}
		}
		str += '</span> 元）';
		$el.find(".sub-txt1").html(str).show();
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
            	if ($.inArray(i, self.dans) === -1) self.BallTriger(self.$balls.eq(i - minBall));
            }
        });
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
        	if ($.inArray(this.playType, ['qzhi2', 'qzhi3']) > -1) {
        		$.each(this.collection.boxes, function(i, e){
        			e.$balls.eq(ball-1).removeClass('dt-pick');
        		})
        	}
        	arr.eq(ball-1).removeClass('dt-pick');
        	this.removeBall(ball, type);
        } else if (!this.ballValid(type)) {
        	var rule = cx.Lottery.getRule(this.playType, [pad(this.collection.boxes.length, 5, this.index)]);
        	cx.Alert({content: rule['content'], size: rule['size']});
        } else {
        	if ($.inArray(this.playType, ['qzhi2', 'qzhi3']) > -1) {
        		var self = this;
        		$.each(this.collection.boxes, function(i, e){
        			self.collection.boxes[i].$balls.eq(ball-1).removeClass('selected').addClass('dt-pick');
        			self.collection.boxes[i].removeBall(ball);
        		})
        	}
        	arr.eq(ball-1).removeClass('selected').addClass('dt-pick');
        	this.removeBall(ball, t);
        	$el.removeClass('dt-pick').addClass('selected');
        	this.addBall(ball, type);
        }
        this.collection.renderBet();
    }
	
	cx.BallBox.prototype.removeBalls = function() {
    	var self = this;
    	if ($.inArray(this.playType, ['qzhi2', 'qzhi3']) > -1) {
    		$.each(this.collection.boxes, function(i, ele){
    			if (i != self.index) {
    				$.each(self.balls, function(j, e){
    					ele.$balls.eq(e-1).removeClass('dt-pick');
    				})
    			}
    		})
    	}
        this.balls = [];
        this.$balls.removeClass('selected');
        this.$dans.removeClass('dt-pick');
        this.collection.renderBet();
    }
	
	cx.CastBasket.prototype.init = function(){
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
        	if(string.playType !== self.playType) {
        		flag = string.playType.replace(/dt/, '');
        		$("."+self.tab).filter("[data-type^="+flag+"]").trigger('click');
        		$(".bet-type-link-item."+flag).find("#"+string.playType).removeAttr('checked').parents('li').removeClass('selected').trigger('click');
        	}
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
        		case 0:
        			cx._basket_.orderType = 0;
                    $('#ordertype1').parents("span").find('.ptips-bd').show();
                    var str = '由购买人自行全额购买彩票，独享奖金<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>自购：</em>选好投注号码后，由自己全额<br>支付购彩款。中奖后，自己独享全部<br>税后奖金。">&#xe613;</i>';
                    $(".chase-number-notes").html(str);
        			break;
        	}
      	  	chgbtn();
        })
	}
	
	cx.CastBasket.prototype.renderString = function(allBalls, index, hover) {
		var tpl = '<li ', ballTpl = [], dt = false;
    	if(hover) tpl += ' class="hover"'
    	tpl += ' data-index="'+index+'"><span class="bet-type">'+ cx.Lottery.getPlayTypeName(cx.Lottery.playTypes[allBalls.playType]);
        $.each(allBalls.balls, function(pi, balls){
            var tmpTpl = '<span class="num-red">';
        	danTpl = '';
        	if ('dan' in balls) {
        		dt = true;
        		$.each(balls['dan'].sort(sort), function(ti, ball){
                	danTpl += pad(2, ball, 1) + ' ';
                })
                tmpTpl += "("+danTpl.replace(/(\s*$)/g,'')+") ";
        	}
            $.each(balls['tuo'].sort(sort), function(ti, ball){
            	tmpTpl += pad(2, ball, 1) + ' ';
            })
            tmpTpl += '</span>';
            ballTpl.push(tmpTpl);
        })
        ballTpl = ballTpl.join('<em>|</em>');
        if (parseInt(cx.Lottery.playTypes[allBalls.playType], 10) >= 9 && parseInt(cx.Lottery.playTypes[allBalls.playType], 10) <= 12) {
        	if (dt) tpl = tpl.replace(/(组|直)选/g, '')+'胆拖';
        }else {
        	if (dt) {
            	tpl += '胆拖';
            } else if (allBalls.betNum > 1) {
        		tpl += '复式';
        	} else {
        		tpl += '单式';
        	}
        }
    	tpl += '</span><div class="num-group">'+ballTpl+'</div><a href="javascript:;" class="remove-str">删除</a><a href="javascript:;" class="modify-str">修改</a><span class="bet-money">'+allBalls.betMoney+'元</span></li>';
        return tpl;
    }
	
	cx.CastBasket.prototype.setType = function(type) {
		this.playType = type;
		var t = this.playType.replace(/dt/, '');
		$("."+this.tab+"[data-type='"+t+"'], ."+this.tab+"[data-type='"+t+"dt']").attr('data-type', this.playType);
		rfshhisty(this.playType);
		this.boxes[this.playType].renderBet();
		if(this.boxes[this.playType].edit > 0) this.$castList.find('li').removeClass('hover').filter('[data-index="'+this.boxes[this.playType].edit+'"]').addClass('hover');
        if (this.playType.indexOf('dt') > -1) {
        	$("."+this.playType.replace(/dt/, '')+" .dt .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
        }else {
        	$("."+this.playType+" .default .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
        }
    }
	
	inteval = setInterval("countdown()", cy);
	for (var j = 2; j <= 8; j++){
		boxes['rx'+j] =  new cx.BoxCollection('.rx'+j+' .default .box-collection');
	    for (var i = 1; i <= 1; ++i) {
	        boxes['rx'+j].add(new cx.BallBox('.rx'+j+' .default .ball-box-'+i, {playType: 'rx'+j}));
	    }
	    
	    if (j != 8) {
	    	boxes['rx'+j+'dt'] =  new cx.BoxCollection('.rx'+j+' .dt .box-collection');
		    for (i = 1; i <= 1; ++i) {
		        boxes['rx'+j+'dt'].add(new cx.BallBox('.rx'+j+' .dt .ball-box-'+i+':last', {hasdan: true, dmax: j-1, tmin: 2, dtmin: j+1, playType: 'rx'+j+'dt'},'.rx'+j+' .dt .ball-box-'+i+':first'));
		    }
	    }
	}
    
    boxes['q1'] =  new cx.BoxCollection('.q1 .default .box-collection');
    for (var i = 1; i <= 1; ++i) {
        boxes['q1'].add(new cx.BallBox('.q1 .default .ball-box-'+i, {playType: 'q1'}));
    }
    
    for (j = 2; j <=3; j++) {
    	boxes['qzu'+j] =  new cx.BoxCollection('.qzu'+j+' .default .box-collection');
        for (var i = 1; i <= 1; ++i) {
            boxes['qzu'+j].add(new cx.BallBox('.qzu'+j+' .default .ball-box-'+i, {playType: 'qzu'+j}));
        }
        
        boxes['qzu'+j+'dt'] =  new cx.BoxCollection('.qzu'+j+' .dt .box-collection');
        for (i = 1; i <= 1; ++i) {
            boxes['qzu'+j+'dt'].add(new cx.BallBox('.qzu'+j+' .dt .ball-box-'+i+':last', {hasdan: true, dmax: j-1, tmin: 2, dtmin: j+1, playType: 'qzu'+j+'dt'},'.qzu'+j+' .dt .ball-box-'+i+':first'));
        }
        
	    boxes['qzhi'+j] =  new cx.BoxCollection('.qzhi'+j+' .default .box-collection');
	    for (var i = 1; i <= j; ++i) {
	        boxes['qzhi'+j].add(new cx.BallBox('.qzhi'+j+' .default .ball-box-'+i, {playType: 'qzhi'+j}));
	    }
    }
    
    var multiModifier = new cx.AdderSubtractor('.multi-modifier'), 
    basket = new cx.CastBasket('.cast-basket', {boxes: boxes, multi: MULTI, multiModifier: multiModifier, chases: chase, chaseLength: chaselength, playType: 'rx8', tab: 'bet-type-link li', tabClass: 'selected'});
    cx._basket_ = basket;
    
$(".rx8 .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    
    $(".tab-list-hd").on("click", "li", function(){
    	$(this).find('input').attr("checked","checked" );
    })
	
    $(".tab-list-hd li input").click(function(e){
    	e.stopPropagation();
    })
	
	$('.my-order-hd').on('click', function(){
        var myOrder = $(this).parents('.my-order'), myOrderBd = myOrder.find('.my-order-bd');
        myOrder.toggleClass('my-order-open');
        myOrderBd.slideToggle();
        return false;
    })
    
    $('.ykj-info').find('tbody tr:last-child').show();
    $('.ykj-info-action').on('click', function(){
    	var ykjInfo = $(this).parents('.ykj-info');
        // 控制是否展开
        ykjInfo.toggleClass('ykj-info-open');
        ykjInfo.find('thead').toggle();
        ykjInfo.find('tr').toggle();
        $('.ykj-info').find('tr:last-child').show();
        $('.canvas-mask').toggle();
        if ($.inArray(cx._basket_.playType, ['q1', 'qzhi2', 'qzhi3']) > -1) cavas();
        if($('.ykj-info').hasClass('ykj-info-open')){
        	$(".ykj-info-action").html("收起走势<i class='arrow'></i>");
        } else {
        	$(".ykj-info-action").html("近期走势<i class='arrow'></i>");
        }
    });
    
 // 滚动接近tab模块时，吸顶
    var ceilingBox = $('.bet-syxw .cp-box-bd'), ceilingBoxTop, thisWindow = $(window), beforeScrollTop = thisWindow.scrollTop();
    thisWindow.on('scroll', function(){
        afterScrollTop = thisWindow.scrollTop();
        ceilingBoxTop = ceilingBox.offset().top;
        // 向下滚动
        if(afterScrollTop > beforeScrollTop && afterScrollTop >= ceilingBoxTop - 120 && afterScrollTop < ceilingBoxTop) $('html, body').scrollTop(ceilingBoxTop)
        beforeScrollTop = afterScrollTop;     
    })
    
    $(".my-order-hd a").click(function(){
    	if(!$(this).hasClass('not-login') && $(".my-order-bd table").length == 0) {
    		$.ajax({
    			url: "/ajax/getOrders/gdsyxw",
    			dataType: 'json',
    			success: function(data) {
    				var str = '<table><colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>\
    				<thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal"><a target="_blank" href="mylottery/betlog">更多</a></th></tr></thead><tbody>';
    				if (data.length > 0) {
    					$.each(data, function(i, dt){
    						str += '<tr><td>'+dt.created+'</td><td>'+dt.issue+'</td><td class="tal" title="';
    						var codes = dt.codes.split(';'),carr = [];
    						$.each(codes, function(j, cds){
    							carr = cds.split(':');
    							str += cx.Lottery.getPlayTypeName(carr[1]);
    							if ($.inArray(carr[1], ['09', '10', '11', '12']) === -1) str += (carr[2] == '05' ? '胆拖': (carr[0].split(',').length > parseInt(carr[1]) ? '复式' : '单式'));
    							str += carr[0].replace(/((.+)\$)/ig, '( $2 ) ').replace(/,/ig, ' ').replace(/\|/g, '<s> | </s>')+';';
    						})
    						carr = codes[0].split(':');
    						str += '"><div class="text-overflow">'+ cx.Lottery.getPlayTypeName(carr[1]);
    						if ($.inArray(carr[1], ['09', '10', '11', '12']) === -1) str += (carr[2] == '05' ? '胆拖': (carr[0].split(',').length > parseInt(carr[1]) ? '复式' : '单式'));
    						str += ' <span class="specil-color">'+carr[0].replace(/((.+)\$)/ig, '( $2 ) ').replace(/,/ig, ' ').replace(/\|/g, '<s> | </s>')+'</span>';
    						if (j > 0) str += '...';
    						str += '</div></td><td><span class="fcs">'+fmoney(parseInt(dt.money, 10) / 100, 3)+'.00</span></td><td><span class="fcs">'+cx.Order.getStatus(dt.status, 3)+'</span></td>';
    						if (dt.margin > 0) {
    							str += '<td><img src="/caipiaoimg/v1.1/img/gold.png" alt="">&nbsp;<strong class="spec arial">'+fmoney(dt.margin / 100, 3)+'.00</strong></td>';
    						}else if($.inArray(dt.status, ['1000', '2000']) === -1) {
    							str += '<td>--</td>'
    						}else {
    							str += '<td>'+fmoney(dt.margin / 100, 3)+'.00</td>';
    						}
    						str += '<td><a target="_blank" href="/orders/detail/'+dt.orderId+'">查看详情</a>';
    						if (dt.ljzf == 1) {
    							str += '<a href="javascript:cx.castCb({orderId:\''+dt.orderId+'\'}, {ctype:\'paysearch\', orderType:0});"><span class="num-red">立即支付</span></a>';
    						}else {
    							str += '<a target="_blank" href="/gdsyxw?orderId='+dt.orderId+'">继续预约</a></td><td></td></tr>';
    						}
    					})
    				} else {
    					str += '<tr><td colspan="8" style="height: 100px;">亲，您三个月内还没有订单哦！</td></tr></tbody></table>'
    				}
    				$(".my-order-bd").html(str);
    			}
    		})
    	}
    })
    
    kjNumAimation(vJson)
    setTimeout(function(){
        kjNumAimation(vJson)
    }, 2000)
});

function render(data){
	$(".lottery-info-time b").html(data.issue);//渲染页面中当前期
    $(".kj-periods b").html(data.prev);//渲染页面上一期
    
    //渲染页面倒计时剩余时间
    if (cx._basket_.playType.indexOf('dt') > -1) {
    	$("."+cx._basket_.playType.replace(/dt/, '')+" .dt .pick-area-time").html("<em><b>"+data.issue.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    }else {
    	$("."+cx._basket_.playType+" .default .pick-area-time").html("<em><b>"+data.issue.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    }
    $(".periods-num").html("已售"+data.count+"期，还剩<b class='specil-color'>"+data.rest+"</b>期");//渲染页面已售、剩余
    rfshhisty(cx._basket_.playType);//刷新遗漏数据、最近时期开奖走势列表
    
    //渲染页面开奖号码
    kjNumAimation(vJson);
    setTimeout(function(){
        kjNumAimation(vJson)
    }, 2000);
}

//开奖动画
function kjNumAimation(json){
    setTimeout(function(){
        var kjNumUl = $('.kj-num-item').find('ul');
        var kjNumLiHeight = kjNumUl.find('li').height();
        $(json).each(function(index){
            $(kjNumUl[index]).animate({top: -kjNumLiHeight * (parseInt(json[index], 10) ? parseInt(json[index], 10) : 0)}, 800)
        })
    }, 0)
    
}

function rfshhisty(type) {
	$('.canvas-mask').hide();
	$('.canvas-mask').empty();
	$(".ykj-info .column-num").remove();
	$(".ykj-info").removeClass('qezx');
	$(".ykj-info").removeClass('qezx')
	var hstr = '<th class="column-num"><div class="ball-group-s">';
	for (var n = 1; n <=11; n++) {
		hstr += '<span>'+padd(n)+'</span>';
	}
	hstr += '</div></th>';
	if(type === 'qzhi2') {
		$(".ykj-info").addClass('qezx');
		var str = "<colgroup><col width='85'><col width='304'><col width='304'><col width='124'><col width='59'><col width='59'><col width='59'></colgroup><thead";
		if ($(".ykj-info").hasClass('ykj-info-open')) {
			str += " style='display: table-header-group;'><tr style='display: table-row;'";
		}else {
			str += "><tr";
		}
		str += "><th>期次</th>"+hstr+hstr+"<th>开奖号码</th><th>和值</th><th>大小比</th><th>奇偶比</th></tr></thead>";
		$.each(hsty, function(h, ele){
			str += "<tr";
			if (h == 9 || $(".ykj-info").hasClass('ykj-info-open')) str += ' style="display: table-row;"';
			str += "><td>"+ele.issue+"</td><td class='column-num'";
			if (atm > 0 && !ele.award_time) {
				str += ' colspan="2"><em class="main-color-s atime">'+maketstr(atm)+'</em>后开奖...</td><td>--</td><td>--</td><td>--</td><td>--</td>';
	        }
			else if (ele.awardNum === undefined) {
				str += " colspan='2'>正在开奖中...</td><td>--</td><td>--</td><td>--</td><td>--</td>";
			} else {
				str += "><div class='ball-group-s column-1'>";
				var awardNum = ele.awardNum.split(',');
				var hi = 0;
				if(mall[ele.issue] !== undefined) {
					$.each(mall[ele.issue][1].split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), awardNum) > -1 && hi == awardNum[0]) {
							str += "<span class='selected'>"+hi+"</span>";
						} else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td>";
					str += "<td class='column-num'><div class='ball-group-s column-2'>";
					hi = 0;
					$.each(mall[ele.issue][2].split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), awardNum) > -1 && hi == awardNum[1]) {
							str += "<span class='selected'>"+hi+"</span>";
						} else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td>";
				} else {
					str += "---</div></td><td class='column-num'><div class='ball-group-s column-2'>---</div></td>";
				}
				str += "<td><div class='num-group'>";
				$.each(awardNum, function(aw, e){
					str += '<span>'+e+'</span>';
				})
				str += "</div></td><td>"+ele.he+"</td><td>"+ele.dx+"</td><td>"+ele.jo+"</td></tr>";
			}
		})
		$(".ykj-info table").html(str);
	} else if (type === 'qzhi3') {
		$(".ykj-info").addClass('qezx');
		var str = "<colgroup><col width='86'><col width='304'><col width='304'><col width='304'></colgroup><thead";
		if ($(".ykj-info").hasClass('ykj-info-open')) {
			str += " style='display: table-header-group;'><tr style='display: table-row;'";
		}else {
			str += "><tr";
		}
		str += "><th>期次</th>"+hstr+hstr+hstr+"</tr></thead>";
		$.each(hsty, function(h, ele){
			str += "<tr";
			if (h == 9 || $(".ykj-info").hasClass('ykj-info-open')) str += ' style="display: table-row;"';
			str += "><td>"+ele.issue+"</td><td class='column-num'";
			if (atm > 0 && !ele.award_time) {
				str += ' colspan="3"><em class="main-color-s atime">'+maketstr(atm)+'</em>后开奖...</td>';
	        }
			else if (ele.awardNum === undefined) {
				str += " colspan='3'>正在开奖中...</td>";
			} else {
				str += "><div class='ball-group-s column-1''>";
				var awardNum = ele.awardNum.split(',');
				var hi = 0;
				if(mall[ele.issue] !== undefined) {
					$.each(mall[ele.issue][1].split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), awardNum) > -1 && hi == awardNum[0]) {
							str += "<span class='selected'>"+hi+"</span>";
						} else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td><td class='column-num'><div class='ball-group-s column-2''>";
					hi = 0;
					$.each(mall[ele.issue][2].split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), awardNum) > -1 && hi == awardNum[1]) {
							str += "<span class='selected'>"+hi+"</span>";
						} else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td>";
					str += "<td class='column-num'><div class='ball-group-s column-3''>";
					hi = 0;
					$.each(mall[ele.issue][3].split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), awardNum) > -1 && hi == awardNum[2]) {
							str += "<span class='selected'>"+hi+"</span>";
						} else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td>";
				} else {
					str += "---</div></td><td class='column-num'><div class='ball-group-s column-2'>---</div></td><td class='column-num'><div class='ball-group-s column-3'>---</div></td>";
				}
			}
		})
		$(".ykj-info table").html(str);
	} else {
		var str = "<colgroup><col width='160'><col width='506'><col width='152'><col width='60'><col width='60'><col width='60'></colgroup>";
		str += "<thead";
		if ($(".ykj-info").hasClass('ykj-info-open')) {
			str += " style='display: table-header-group;'><tr style='display: table-row;'";
		}else {
			str += "><tr";
		}
		str += "><th>期次</th>"+hstr+"<th>开奖号码</th><th>和值</th><th>大小比</th><th>奇偶比</th></tr></thead>";
		$.each(hsty, function(h, ele){
			str += "<tr";
			if (h == 9 || $(".ykj-info").hasClass('ykj-info-open')) str += ' style="display: table-row;"';
			str += "><td>"+hsty[h].issue+"</td><td class='column-num'>";
			if (atm > 0 && !ele.award_time) {
				str += '<em class="main-color-s atime">'+maketstr(atm)+'</em>后开奖...</td><td><span class="specil-color">--</span></td><td>--</td><td>--</td><td>--</td>';
	        }
			else if (hsty[h].awardNum === undefined) {
				str += "正在开奖中...</td><td><span class='specil-color'>--</span></td><td>--</td><td>--</td><td>--</td>";
			} else {
				str += "<div class='ball-group-s column-1'>";
				var awardNum = hsty[h].awardNum.split(','), award = awardNum;
				if($.inArray(type, ['qzu2', 'qzu3', 'q1']) > -1) {
					var index = parseInt(type.replace(/(\D+)/ig, ''), 10);
					award = awardNum.slice(0, index);
				}
				var mss;
				if(mall[hsty[h].issue] !== undefined) {
					var hi = 0;
					if (type == 'q1') {
						mss = mall[hsty[h].issue][1];
					} else if($.inArray(type, ['qzu2', 'qzu3']) > -1) {
						mss = mall[hsty[h].issue][index+2];
					} else {
						mss = mall[hsty[h].issue][0];
					}
					$.each(mss.split(','), function(m, e){
						hi++;
						if ($.inArray(padd(hi), award) > -1) {
							str += "<span class='selected'>"+hi+"</span>";
						}else {
							str += "<span>"+e+"</span>";
						}
					})
					str += " </div></td>"
				} else {
					str += "---</div></td>";
				}
				str += "<td><div class='num-group'>";
				$.each(awardNum, function(aw, e){
					str += '<span>'+e+'</span>';
				})
				str += "</div></td><td>"+hsty[h].he+"</td><td>"+hsty[h].dx+"</td><td>"+hsty[h].jo+"</td></tr>";
			}
		})
		$(".ykj-info table").html(str);
	}
	if ($.inArray(type, ['q1', 'qzhi2', 'qzhi3']) > -1 && $(".ykj-info").hasClass('ykj-info-open')) {
		$('.canvas-mask').show();
		cavas();
	}
}

var cavas = function () {
    // 静态页面点击创建，到开发那边改为ajax加载开奖结果成功后开始创建
    if($('.ykj-info').hasClass('ykj-info-open')){
        var i = 0, itemAarry = [], item = $('.ykj-info').find('.ball-group-s').find('span.selected'), columnNum = $('.ykj-info').find('tbody tr').eq(0).find('.ball-group-s').length, 

        // 获取canvas父级的定位
        canvasMaskOffset = $('.canvas-mask').offset(), canvasMaskTop = canvasMaskOffset.top, canvasMaskLeft = canvasMaskOffset.left;
        $('.canvas-mask').attr({'data-left': canvasMaskLeft, 'data-top': canvasMaskTop})

        // 给选中的球添加自身的定位参数
        for(var i = 0, itemLength = item.length; i < itemLength; i++){
            $(item[i]).attr({'data-top': Math.round($(item[i]).offset().top - canvasMaskTop) + 10, 'data-left': Math.round($(item[i]).offset().left - canvasMaskLeft) + 10})
        }

        // 中奖数字分组
        for(var i = 0; i < columnNum; i++){
            itemAarry.push($('.ykj-info').find('.column-' + (i + 1)).find('span.selected'))
        }

        for(var k = 0, itemAarryLength = itemAarry.length; k < itemAarryLength; k++){
            for( var i = 0, itemAarryClength = itemAarry[k].length; i < itemAarryClength; i++){
                    //控制创建canvas的个数
                if(i < (itemAarryClength - 1)){

                    // 计算两个中奖球之间矩形的宽高
                    var left1 = Math.round($(itemAarry[k][i]).attr('data-left')), top1 = Math.round($(itemAarry[k][i]).attr('data-top')), left2 = Math.round($(itemAarry[k][i+1]).attr('data-left')), 
                    top2 = Math.round($(itemAarry[k][i+1]).attr('data-top')), width = left2 - left1, height = top2 - top1, canvasTag = document.createElement('canvas');

                    // 插入到html中
                    $('.canvas-mask').append(canvasTag);
                    if(!$.support.leadingWhitespace) var canvas = window.G_vmlCanvasManager.initElement($('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k + i]);
                    
                    var canvas = $('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k  + i].getContext('2d');
                    if(width > 3){
                        // 当连接线是斜线时
                        // width = width - 20;
                        $($('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k  + i]).css({'position': 'absolute', 'left': left1 + 'px', 'top': top1 + 'px'}).attr({'width': width, 'height': height})
                        canvas.beginPath();
                        canvas.moveTo(6,6*height/width);//第一个起点
                        canvas.lineTo(width-6,height-6*height/width);//第二个点
                    }else if(width < 0){
                        // 当连接线是反向斜线时
                        width = -width
                        $($('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k  + i]).css({'position': 'absolute', 'left': (left1 - width) + 'px', 'top': top1 + 'px'}).attr({'width': width, 'height': height})
                        canvas.beginPath();
                        canvas.moveTo(width - 6, 6*height/width);//第一个起点
                        canvas.lineTo(6,height-6*height/width);//第二个点
                    }else {
                        // 当连接线是垂直线时
                        // height = height - 18;
                        $($('.canvas-mask').find('canvas')[(itemAarryClength - 1) * k  + i]).css({'position': 'absolute', 'left': left1 + 'px', 'top': top1 + 'px'}).attr({'width': width + 2, 'height': height})
                        canvas.beginPath();
                        canvas.moveTo(0,6);//第一个起点
                        canvas.lineTo(0,height-6);//第二个点
                    }
                    
                    // 画线
                    canvas.lineWidth = 1;
                    canvas.strokeStyle = '#e82828';
                    canvas.stroke();
                }
            }
        }
    }
}

function renderTime() {
	if (atm > 0 && atm < 100000) {
		$('.kj-num-item:first li:first').html(padd(Math.floor(atm/60)));
		$('.kj-num-item:eq(1) li:first').html(padd(atm%60));
		$('.kj-num-item:eq(2) li:first').html('后');
		$('.kj-num-item:eq(3) li:first').html('开');
		$('.kj-num-item:eq(4) li:first').html('奖');
		$("body").find(".atime").html(maketstr(atm));
	}else if (isNaN(vJson[0])) {
		$('.kj-num-item:first li:first').html('正');
		$('.kj-num-item:eq(1) li:first').html('在');
		$('.kj-num-item:eq(2) li:first').html('开');
		$('.kj-num-item:eq(3) li:first').html('奖');
		$('.kj-num-item:eq(4) li:first').html('中');
	}
	if (cx._basket_.playType.indexOf('dt') > -1) {
    	$("."+cx._basket_.playType.replace(/dt/, '')+" .dt .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    }else {
    	$("."+cx._basket_.playType+" .default .pick-area-time").html("<em><b>"+ISSUE.substring(2, 8)+"</b></em>期剩余<span>"+maketstr(tm)+"</span><i class='arrow'></i>");
    }
}

function fmoney(s) {   
	s = s.toString().split("").reverse().join("").substring(0, s.toString().length);
	return s.replace(/(\d{3})/g, '$1,').split("").reverse().join("").replace(',', '');
} 