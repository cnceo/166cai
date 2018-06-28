$(function(){

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
            tpl += QuickCast.renderBalls(ballBoxes[0], 'redball');
        }
        if (ballBoxes.length > 1) {
            tpl += QuickCast.renderBalls(ballBoxes[1], 'blueball');
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
            //tpl += '<li class="ball ' + color + '">' + balls[i] + '</li>';
			tpl += '<input maxlength="2" class="ball ' + color + ' rotate" value="' + this.pad( balls[i] ) + '" style="ime-mode: disabled;" max="33">';
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
            this.$el.on('blur', 'input', function() {
				self.edit();
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
					51 : '/ssq',
					23529 : '/dlt',
					21406 : '/syxw',
					23528 : '/qlc'
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
			/*
            setTimeout(function() {
                self.$el.find('.ball').addClass('rotate');
            }, 0);
			*/
        },
        edit: function() {
            var self = this;
            this.randStrs = this.collection.edit();
            this.$randNum.html(QuickCast.render(this.randStrs));
			/*
            setTimeout(function() {
                self.$el.find('.ball').addClass('rotate');
            }, 0);
			*/
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
    var ssqIssue = new cx.Issue('.lottery-ssq', {
        lotteryId: cx.Lottery.SSQ
    });
    var dltIssue = new cx.Issue('.lottery-dlt', {
        lotteryId: cx.Lottery.DLT
    });
    var syxwIssue = new cx.Issue('.lottery-syxw', {
        lotteryId: cx.Lottery.SYXW
    });
	/*
    var qxcIssue = new cx.Issue('.lottery-qxc', {
        lotteryId: cx.Lottery.QXC
    });
	*/
    var qlcIssue = new cx.Issue('.lottery-qlc', {
        lotteryId: cx.Lottery.QLC
    });

	//# 快投
    var ssqCast = new QuickCast('.lottery-ssq', {
        lotteryId: cx.Lottery.SSQ,
        issue: ssqIssue
    });
    var dltCast = new QuickCast('.lottery-dlt', {
        lotteryId: cx.Lottery.DLT,
        issue: dltIssue
    });
    var syxwCast = new QuickCast('.lottery-syxw', {
        lotteryId: cx.Lottery['SYXW'],
        issue: syxwIssue,
		playType: 'rx5'
    });
	/*
    var qxcCast = new QuickCast('.lottery-qxc', {
        lotteryId: cx.Lottery.QXC,
        issue: qxcIssue
    });
	*/
	var qlcCast = new QuickCast('.lottery-qlc', {
        lotteryId: cx.Lottery.QLC,
        issue: qlcIssue
    });

	//# 双色球
    var ssqPreBox = new cx.BallBox('.rand-ssq .redball', {
        amount: 33,
        min: 6
    });
    var ssqPostBox = new cx.BallBox('.rand-ssq .blueball', {
        amount: 16,
        min: 1
    });
    var ssqCollection = new cx.BoxCollection();
    ssqCollection.add(ssqPreBox);
    ssqCollection.add(ssqPostBox);

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

	//# 十一运
    var syxwBox = new cx.BallBox('.rand-syxw .redball', {
        amount: 11,
        min: 5
    });
    var syxwCollection = new cx.BoxCollection();
    syxwCollection.add(syxwBox);

	//# 七星彩
	/*
    var qxcBox = new cx.BallBox('.rand-qxc .redball', {
        amount: 9,
        min: 7
    });
    var qxcCollection = new cx.BoxCollection();
    qxcCollection.add(qxcBox);
	*/

	//# 七乐彩
    var qlcBox = new cx.BallBox('.rand-qlc .redball', {
        amount: 30,
        min: 7
    });
    var qlcCollection = new cx.BoxCollection();
    qlcCollection.add(qlcBox);

	//# 给快速投注设置容器
    dltCast.setCollection(dltCollection);
    ssqCast.setCollection(ssqCollection);
    syxwCast.setCollection(syxwCollection);
	//qxcCast.setCollection(qxcCollection);
	qlcCast.setCollection(qlcCollection);

	//# 随机一个号
    ssqCast.random();
    dltCast.random();
    syxwCast.random();
	//qxcCast.random();
	qlcCast.random();



    $('.tabs li').click(function() {
        var $this = $(this);
        var type = $this.data('type');
        $this.addClass('selected').siblings().removeClass('selected');
        $('.lottery-' + type).siblings('.lotteryOneInfo').hide();
        $('.lottery-' + type).show();
        $('.lottery-helper').toggle();
    });
	
    /*var promise = cx.ajax.get({
        url: cx.url.getCmsUrl('content/tag/list')
    });
    var categories = {
        1: '公告',
        2: '博文',
        3: '新闻',
        4: '活动',
        5: '广告'
    };
    promise.done(function(tagList) {
        if (tagList['code'] != 1) {
            return ;
        }
        tagList = tagList['data'];
        var $topicList = $('.topic-list');
        var indexTag = [];
        for (var tagId in tagList) {
            var tagName = tagList[tagId];
            if (tagName == 'index-3') {
                indexTag.push(tagId);
            }
        }
        cx.ajax.get({
            url: cx.url.getCmsUrl('content/fragment/list'),
            data: {
                category: '2,3,4',
                tag: indexTag.join(',')
            },
            success: function(response) {
                var tpl = '';
                if (response.code == 1) {
                    var data = response.data.results;
                    $(data).each(function(key, topic) {
                        tpl += '<p>';
                        tpl += '<a href="' + baseUrl + 'news/index/' + topic.category + '">[' + categories[topic.category] + ']</a>';
                        tpl += '<a href="' + baseUrl + 'news/detail/' + topic.id + '">' + topic.title + '</a>';
                        tpl += '</p>';
                    });
                }
                $topicList.html(tpl);
            }
        });
    });

    promise.done(function(tagList) {
        if (tagList['code'] != 1) {
            return ;
        }
        tagList = tagList['data'];
        var $noticeList = $('.notice-list');
        var indexTag = [];
        for (var tagId in tagList) {
            var tagName = tagList[tagId];
            if (tagName == 'index-4') {
                indexTag.push(tagId);
            }
        }
        cx.ajax.get({
            url: cx.url.getCmsUrl('content/fragment/list'),
            data: {
                category: '1',
                tag: indexTag.join(',')
            },
            success: function(response) {
                var tpl = '';
                if (response.code == 1) {
                    var data = response.data.results;
                    $(data).each(function(key, topic) {
                        tpl += '<p>';
                        tpl += '<a href="' + baseUrl + 'news/index/' + topic.category + '">[' + categories[topic.category] + ']</a>';
                        tpl += '<a href="' + baseUrl + 'news/detail/' + topic.id + '">' + topic.title + '</a>';
                        tpl += '</p>';
                    });
                }
                $noticeList.html(tpl);
            }
        });
    });*/
    
	$('.not-login').click(function(e) {
		var $this = $(this);
		me.notLogin($this, e);
	});	

	me.notLogin = function ($this, e){
		if ($this.hasClass('not-login') || !$.cookie('name_ie')) {
			cx.PopLogin.show();
			e.stopImmediatePropagation();
            // alert(1)
		}
		e.preventDefault();
	}
});
