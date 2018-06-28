var chase = {}, j = 0;

$(function() {
	
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
	
	var preBox = new cx.BallBox('.default .pre-box', {
		lotteryId: cx.Lottery.DLT,
        amount: 35,
        min: 5,
        minBall: 1,
        playType: 'default'
    });
    var postBox = new cx.BallBox('.default .post-box', {
    	lotteryId: cx.Lottery.DLT,
        amount: 12,
        min: 2,
        minBall: 1,
        playType: 'default'
    });
    
	var dtpreBox = new cx.BallBox('.dt .pre-box:last', {
		lotteryId: cx.Lottery.DLT,
        amount: 35,
        min: 5,
        minBall: 1,
        dmax: 4,
        tmin: 2,
        dtmin: 6,
        selmin: [2, 5, 4, 3, 2],
        seldefault: [5, 5, 4, 3, 2],
        selmax: [33, 23, 33, 32, 31],
        hasdan: true,
        playType: 'dt',
        randSel: '.dt .pre-box:last .rand-count:eq(0)'
    }, '.dt .pre-box:first');
	
    var dtpostBox = new cx.BallBox('.dt .post-box:last', {
    	lotteryId: cx.Lottery.DLT,
        amount: 12,
        dmax: 1,
        min: 2,
        minBall: 1,
        tmin: 2,
        dmax: 1,
        dtmin: 1,
        selmin: [2, 2],
        seldefault: [2, 2],
        selmax: [12, 11],
        hasdan: true,
        playType: 'dt',
        randSel: '.dt .post-box:last .rand-count:eq(0)'
    }, '.dt .post-box:first');
    
    var ddshpreBox = new cx.BallBox('.ddsh .pre-box', {
    	lotteryId: cx.Lottery.DLT,
        amount: 35,
        min: 5,
        minBall: 1,
        tmin: 5,
        playType: 'ddsh',
        selmin: [5, 4, 3, 2, 1],
        seldefault: [5, 4, 3, 2, 1],
        selmax: [18, 23, 33, 32, 31],
        dmax: 4,
        smax: 30,
        hasdan: true,
        randSel: '.tac .rand-count:eq(0)'
    });
    var ddshpostBox = new cx.BallBox('.ddsh .post-box', {
    	lotteryId: cx.Lottery.DLT,
        amount: 12,
        min: 2,
        minBall: 1,
        tmin: 2,
        playType: 'ddsh',
        selmin: [2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        seldefault: [2, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        selmax: [12, 11, 10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0],
        dmax: 1,
        smax: 10,
        hasdan: true,
        randSel: '.tac .rand-count:eq(1)'
    });
    var boxes = {};

    boxes['default'] = new cx.BoxCollection('.default .box-collection', {lotteryId: cx.Lottery.DLT});
    boxes['default'].add(preBox, 0);
    boxes['default'].add(postBox, 1);
    
    boxes['dt'] = new cx.BoxCollection('.dt .box-collection', {lotteryId: cx.Lottery.DLT});
    boxes['dt'].add(dtpreBox, 0);
    boxes['dt'].add(dtpostBox, 1);
    
    boxes['ddsh'] = new cx.BoxCollection('.ddsh .box-collection', {lotteryId: cx.Lottery.DLT});
    boxes['ddsh'].add(ddshpreBox, 0);
    boxes['ddsh'].add(ddshpostBox, 1);

    var multiModifier = new cx.AdderSubtractor('.multi-modifier');

    var basket = new cx.CastBasket('.cast-basket', {
        lotteryId: cx.Lottery.DLT,
        issue: DLT_ISSUE,
        boxes: boxes,
        tab: 'bet-tab-hd li',
        tabClass: 'current',
        playType: 'default',
        setMoney: 5000,
        chases: chase,
        chaseLength: chaselength,
        multiModifier: multiModifier,
        getCastOptions:'getCastOptions1'
    });
    cx._basket_ = basket;
    
    $('.bet-tab-hd li').click(function(){
    	var type = $(this).data('type');
    	if (type === 'ddsh') {
    		$(".ptips-bd-b").hide();
    	}else {
    		$(".ptips-bd-b").show();
    	}
    	cx._basket_.setType(type);
        //或略单式上传过滤报错
        if ($('.bet-tab-hd ul li.current').attr('data-type') != 'dssc')
        {
            cx._basket_.boxes[type].renderBet();
            if(cx.Lottery.getRule(boxes[type].lotteryId, type, boxes[type].isValid()) === true)
            {
                $('.'+type+' .add-basket').removeClass('btn-disabled');
            }else 
            {
                if(!$('.'+type+' .add-basket').hasClass('btn-disabled'))
                {
                    $('.'+type+' .add-basket').addClass('btn-disabled');
                }
            }
            if(boxes[type].edit > 0)
            {
                $('.cast-list').find('li').removeClass('hover');
                $('.cast-list').find('li[data-index="'+boxes[type].edit+'"]').addClass('hover');
            }           
        }

    })
    $('.jiangjinCalculate').click(function(e){
    	var self = $(this);
    	self.removeClass('jiangjinCalculate');
        $.ajax({
            type: 'post',
            url:  '/pop/dltjiangjinCalculate',
            data: {version:version},
            success: function(response) {
                $('body').append(response);
                cx.PopCom.show('.pop-jsq');
                cx.PopCom.close('.pop-jsq');
                // 奖金计算器弹窗
                var popJsq= $('.pop-jsq');
                var windowHeight = $(window).height();
                var docHeight = $('.pop-mask').height();	
                var ie6=!-[1,]&&!window.XMLHttpRequest;
                
                popJsq.css({
            		'position': 'absolute',
        			'top': $(window).scrollTop() + $(window).height()/2 - $('.pop-jsq').outerHeight()/2 + 'px',
        			'margin-top': 0
        		})
        		
                if (!self.hasClass('jiangjinCalculate')) {
            		self.addClass('jiangjinCalculate');
            	}
            },
            error: function() {
            	if (!self.hasClass('jiangjinCalculate')) {
            		self.addClass('jiangjinCalculate');
            	}
            }
        });
        e.stopImmediatePropagation();
        e.preventDefault();
    });
});
