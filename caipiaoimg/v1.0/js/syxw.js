$(function() {

    var min = {
        rx2: 2,
        rx3: 3,
        rx4: 4,
        rx5: 5,
        rx6: 6,
        rx7: 7,
        rx8: 8,
        q1: 1,
        qzhi2: 1,
        qzu2: 2,
        qzhi3: 1,
        qzu3: 3
    }[type];

    var boxes = new cx.BoxCollection('.box-collection');
    for (var i = 1; i <= boxCount; ++i) {
        boxes.add(new cx.BallBox('.ball-box-' + i, {
            amount: 11,
            min: min,
            minBall: 1,
            mutex: 1
        }));
    }

    var multiModifier = new cx.AdderSubtractor('.multi-modifier');
    var issue = new cx.Issue('.issue', {
        lotteryId: cx.Lottery.SYXW,
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
        lotteryId: cx.Lottery.SYXW,
        boxes: boxes,
        multiModifier: multiModifier,
        playType: type,
        issue: issue,
        getCastOptions:'getCastOptions1'
    });
    cx._basket_ = basket;

    new cx.LastAward('.last-award', {
        lotteryId: cx.Lottery.SYXW
    });
});
