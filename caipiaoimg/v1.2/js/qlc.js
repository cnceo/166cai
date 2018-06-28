(function() {
	
	window.cx || (window.cx = {});
	
	cx.Lottery = (function(){
		
		var lid = cx.Lid.QLC, me = {};
		
		me.lid = lid;
		me.playTypes = {'default':'1'};
		
		me.getPlayTypeByMidCode = function(midCode) {
			return 'default';
	    }
		
		me.getNumberSeparator = function(playType) {
			return ',';
		}
		
		me.getPlaceSeparator = function(playType) {
			return '';
	    }
		
		me.hasPaddingZero = function(playType) {
	        return true;
	    }
		
		me.getCastPost = function( playType) {
		    return '1';
	    };
	    
	    me.getPlayTypeName = function(playType) {
		    return '普通';
	    };
	    
	    me.getMinLength = function(playType) {
		    return [7]
	    }
	    
	    me.getAmount = function(playType) {
		    return [30]
	    }
	    
	    me.getStartIndex = function(playType) {
		    return [1]
	    }
	    
	    me.getRule = function(playType, state) {
	    	if (state[0] == 1)  return {'status':false, 'content':'<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">７</span>个号码', 'size':'18'}
		    return {'status':true, 'content':'', 'size':'18'};
	    }
	    
	    me.getPlayTypeByCode = function(lotteryId, code) {
	    	return 'default';
        }
	    
	    return me;
		
	})();
	
})();

$(function() {
	
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
        this.$el.find('.rand-select').click(function() {
            var count = self.$el.find('.rand-count').val() || 1;
            self.removeBalls();
            self.rand(count, function(i) {
            	self.$balls.eq(i - 1).addClass('selected');
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
            });
            self.collection.renderBet();
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
        	var startindex = cx.Lottery.getStartIndex(self.playType), string = {};
        	self.$castList.find('li').removeClass('hover');
        	self.boxes[self.playType].edit = $(this).parent('li').addClass('hover').data('index');
        	string = self.strings[self.boxes[self.playType].edit];
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
    	var tpl = '<li ';
    	if(hover) tpl += ' class="hover"'
    	tpl += ' data-index="'+index+'"><span class="bet-type">';
        var ballTpl = [], self = this;
        $.each(allBalls.balls, function(pi, balls){
            var tmpTpl = '<span class="num-red">';
            $.each(balls['tuo'].sort(sort), function(ti, ball){
            	tmpTpl += ball + ' ';
            })
            tmpTpl += '</span>';
            ballTpl.push(tmpTpl);
        })
        ballTpl = ballTpl.join('<em>|</em>');
    	if (allBalls.betNum > 1) {
    		tpl += '复式';
    	} else {
    		tpl += '单式';
    	}
    	tpl += '</span><div class="num-group">'+ballTpl+'</div><a href="javascript:;" class="remove-str">删除</a><a href="javascript:;" class="modify-str">修改</a><span class="bet-money">'+ allBalls.betMoney +'元</span></li>';
        return tpl;
    }

	var boxes = {'default':new cx.BoxCollection('.box-collection')};
    boxes['default'].add(new cx.BallBox('.pre-box', {playType: 'default'}));

    var multiModifier = new cx.AdderSubtractor('.multi-modifier'),
    basket = new cx.CastBasket('.cast-basket', {boxes: boxes, setMoney: 5000, chases: chase, chaseLength: chaselength, multi: MULTI, multiModifier: multiModifier, tabClass:'current', tab: 'bet-tab-hd li'});
    cx._basket_ = basket;
});
