$(function() {

    var preBox = new cx.BallBox('.pre-box', {
        amount: 30,
        min: 7
    });
    
    var boxes = new cx.BoxCollection('.box-collection');
    boxes.add(preBox);

    var multiModifier = new cx.AdderSubtractor('.multi-modifier');
    var zhModifier = new cx.AdderSubtractor('.zh-modifier');
    var issue = new cx.Issue('.issue', {
        lotteryId: cx.Lottery.QLC
    });
    var basket = new cx.CastBasket('.cast-basket', {
        lotteryId: cx.Lottery.QLC,
        issue: issue,
        boxes: boxes,
        multiModifier: multiModifier,
        zhModifier: zhModifier,
        getCastOptions:'getCastOptions1'
    });
    cx._basket_ = basket;
});
