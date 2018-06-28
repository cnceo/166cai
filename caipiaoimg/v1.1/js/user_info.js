$(function(){
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

	var me = {};
	 
    var QuickCast = function(selector, options) {
        this.$el = $(selector);
        this.$randNum = this.$el.find('.rand-nums');
        this.lotteryId = options.lotteryId;
        this.randStrs = null;
        this.issue = options.issue;
		this.playType = options.playType || null;
		// 格式检查
		// 球容器

        this.init();
    };

    QuickCast.render = function(ballBoxes) {
        var tpl = '';
        ballBoxes = ballBoxes.balls;
        if (ballBoxes.length > 0) {
            tpl += QuickCast.renderBalls(ballBoxes[0], 'ball-red');
        }
        if (ballBoxes.length > 1) {
            tpl += QuickCast.renderBalls(ballBoxes[1], 'ball-blue');
        }
        return tpl;
    };
    
    QuickCast.pad = function(i){
		if( isNaN( parseInt(i, 10) ) ){
			return i;
		}
    	i = '' + i;
		if (i.length < 2) {
			i = '0' + i;
		}
		return i;
    };
    
    QuickCast.renderBalls = function(balls, color) {
        var tpl = '';
        for (var i = 0; i < balls.length; ++i) {
            tpl += '<span class="ball ' + color + '">' + this.pad( balls[i] ) + '</span>';
        }
        return tpl;
    };

    QuickCast.prototype = {
        init: function() {
            var self = this;
            this.$el.on('click', '.switch-cast', function(e) {
                self.random();
				e.preventDefault();
            });
            this.$el.on('click', '.do-cast', function(e) {

				if( self.getError() ){
					new cx.Alert({content: "投注不符合玩法规则"});
					return ;
				}
				
                var $this = $(this);

				var castStr = cx.Lottery.toCastString(self.lotteryId, [self.randStrs], self.playType);
				var playType = parseInt(cx.Lottery.playTypes[self.lotteryId][self.playType], 10) || 0;

				var lidUrl = {
					23529 : '/dlt'
				};
				location.href = lidUrl[self.lotteryId] +  '?codes=' + encodeURIComponent( castStr ) + '&playType=' +  encodeURIComponent( playType );
				e.preventDefault();
            });
        },
        setCollection: function(collection) {
            this.collection = collection;
        },
        random: function() {
            var self = this;
            this.randStrs = this.collection.rand();
            this.$randNum.html(QuickCast.render(this.randStrs));
        },
		getError: function(){
			return $.inArray( true, this.collection.getError() ) !== -1 ; 
		},
        pad: function(i) {
        	i = '' + i;
    		if (i.length < 2) {
    			i = '0' + i;
    		}
    		return i;
        }
    };

	//# 公告
    var dltIssue = new cx.Issue('.lottery-dlt', {
        lotteryId: cx.Lottery.DLT
    });

	//# 快投
    var dltCast = new QuickCast('.lottery-dlt', {
        lotteryId: cx.Lottery.DLT,
        issue: dltIssue
    });

	//# 大乐透
    var dltPreBox = new cx.BallBox('.rand-dlt .redball', {
        amount: 35,
        min: 5
    });
    var dltPostBox = new cx.BallBox('.rand-dlt .blueball', {
        amount: 12,
        min: 2
    });
    var dltCollection = new cx.BoxCollection();
    dltCollection.add(dltPreBox);
    dltCollection.add(dltPostBox);



	//# 给快速投注设置容器
    dltCast.setCollection(dltCollection);

	//# 随机一个号
    dltCast.random();
});
