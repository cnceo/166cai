$(function() {
    var min = {
        bz: 1
    }[type]
    
    var boxes = new cx.BoxCollection('.box-collection');
    for(var i = 1; i <= boxCount; ++i){
        boxes.add(new cx.BallBox('.ball-box-' + i, {
            amount: 9,
            min: 1
        }));
    }
 
    var multiModifier = new cx.AdderSubtractor('.multi-modifier');
    var issue = new cx.Issue('.issue', {
        lotteryId: cx.Lottery.PLW,
        render: function(tick) {
            var time = cx.Datetime.formatTime(tick);
            var tpl = '';
            if ('hour' in time && time.hour > 0) {
                if (time.hour < 10) {
                    time.hour = '0' + time.hour;
                }
                tpl += time.hour + ':';
            }
            if ('min' in time) {
                if (time.min < 10) {
                    time.min = '0' + time.min;
                }
                tpl += time.min + ':';
            }
            if ('second' in time) {
                if (time.second < 10) {
                    time.second = '0' + time.second;
                }
                tpl += time.second;
            }
            this.$countDown.html(tpl);
        }
    });
    
    var basket = new cx.CastBasket('.cast-basket', {
        lotteryId: cx.Lottery.PLW,
        boxes: boxes,
        multiModifier: multiModifier,
        issue: issue,
        getCastOptions:'getCastOptions1'
    });
    cx._basket_ = basket;

    new cx.LastAward('.last-award', {
        lotteryId: cx.Lottery.PLW
    });
});
