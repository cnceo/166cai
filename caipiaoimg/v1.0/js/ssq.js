$(function() {

	var preBox = new cx.BallBox('.pre-box', {
        amount: 33,
        min: 6,
        minBall: 1
    });
    var postBox = new cx.BallBox('.post-box', {
        amount: 16,
        min: 1,
        minBall: 1
    });
    var boxes = new cx.BoxCollection('.box-collection');
    boxes.add(preBox);
    boxes.add(postBox);

    var multiModifier = new cx.AdderSubtractor('.multi-modifier');
    var zhModifier = new cx.AdderSubtractor('.zh-modifier');
    var issue = new cx.Issue('.issue', {
        lotteryId: cx.Lottery.SSQ
    });
    var basket = new cx.CastBasket('.cast-basket', {
        lotteryId: cx.Lottery.SSQ,
        issue: issue,
        boxes: boxes,
        multiModifier: multiModifier,
        zhModifier: zhModifier,
        getCastOptions:'getCastOptions1'
    });
    cx._basket_ = basket;
});
