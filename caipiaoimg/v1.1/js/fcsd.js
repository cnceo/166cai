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

    var boxes = [];
    boxes['zx'] = new cx.BoxCollection('.box-collection', 
    	{lotteryId: cx.Lottery.PLS}
    );
    for(var i = 1; i <= 3; ++i){
    	boxes['zx'].add(new cx.BallBox('.zx .ball-box-' + i, {
            amount: 9,
            min: 1,
            playType: 'zx'
        }));
    }
    
    boxes['z3'] = new cx.BoxCollection('.box-collection',
    	{lotteryId: cx.Lottery.PLS}
    );
    for(var i = 1; i <= 1; ++i){
    	boxes['z3'].add(new cx.BallBox('.z3 .ball-box-' + i, {
            amount: 9,
            min: 2,
            playType: 'z3'
        }));
    }
    
    boxes['z6'] = new cx.BoxCollection('.box-collection', {lotteryId: cx.Lottery.PLS});
    for(var i = 1; i <= 1; ++i){
    	boxes['z6'].add(new cx.BallBox('.z6 .ball-box-' + i, {
            amount: 9,
            min: 3,
            playType: 'z6'
        }));
    }
    
    var multiModifier = new cx.AdderSubtractor('.multi-modifier');
    
    var basket = new cx.CastBasket('.cast-basket', {
        lotteryId: cx.Lottery.FCSD,
        boxes: boxes,
        chases: chase,
        chaseLength: chaselength,
        multiModifier: multiModifier,
        playType: type,
        issue: FCSD_ISSUE,
        getCastOptions:'getCastOptions1'
    });
    cx._basket_ = basket;
    
    $('.bet-tab-hd').on('click', 'li', function () {
    	var type = $(this).data('type');
    	cx._basket_.setType(type);
    	cx._basket_.boxes[type].renderBet();
    	if(boxes[type].isValid())
    	{
    		$('.add-basket').removeClass('btn-disabled');
    	}else 
    	{
    		if(!$('.add-basket').hasClass('btn-disabled')) $('.add-basket').addClass('btn-disabled');
    	}
    	if(boxes[type].edit > 0)
		{
    		$('.cast-list').find('li').removeClass('hover');
    	    $('.cast-list').find('li[data-index="'+boxes[type].edit+'"]').addClass('hover');
			$('.add-basket').html('确认修改<i class="icon-font">&#xe614;</i>');
		}else 
		{
			$('.add-basket').html('添加到投注区<i class="icon-font">&#xe614;</i>');
		}
    })
    
    cx.z3ballsplit = function(aball){
    	var ball = aball.balls[0], balls = {}, k = 0;
    	$.each(ball, function(i, a){
    		$.each(ball, function(j, b){
    			if (i != j) {
    				balls[k] = {
        				balls:[[a, a, b]],
        				betNum: 1,
        				playType: "z3"
        			};
    				k++;
    			}
    		})
    	})
    	return balls;
    };

});
