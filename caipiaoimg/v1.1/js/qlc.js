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
	boxes['default'] = new cx.BoxCollection('.box-collection', {lotteryId: cx.Lottery.QLC});
	
    var preBox = new cx.BallBox('.pre-box', {
        amount: 30,
        min: 7
    });
    
    boxes['default'].add(preBox);

    var multiModifier = new cx.AdderSubtractor('.multi-modifier');

    var basket = new cx.CastBasket('.cast-basket', {
        lotteryId: cx.Lottery.QLC,
        issue: QLC_ISSUE,
        boxes: boxes,
        setMoney: 5000,
        chases: chase,
        chaseLength: chaselength,
        multiModifier: multiModifier,
        getCastOptions:'getCastOptions1'
    });
    cx._basket_ = basket;
});
