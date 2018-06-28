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
    boxes['default'] = new cx.BoxCollection('.box-collection', {lotteryId: cx.Lottery.PLW});
    for(var i = 1; i <= boxCount; ++i){
        boxes['default'].add(new cx.BallBox('.ball-box-' + i, {
            amount: 9,
            min: 1
        }));
    }
 
    var multiModifier = new cx.AdderSubtractor('.multi-modifier');
    
    var basket = new cx.CastBasket('.cast-basket', {
        lotteryId: cx.Lottery.PLW,
        boxes: boxes,
        chases: chase,
        chaseLength: chaselength,
        multiModifier: multiModifier,
        issue: PLW_ISSUE,
        getCastOptions:'getCastOptions1'
    });
    cx._basket_ = basket;
});
