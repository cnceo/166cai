(function() {
    window.cx || (window.cx = {});

    var Order = cx.Order = (function() {
        var me = {};

        me.getStatus = function(status, returnFlag) {
            status = parseInt(status, 10);
            var cnStatus = '未知';
            if (status <= 100) {
                cnStatus = '未付款';
            } else if (status == 200) {
                cnStatus = '待出票';
            } else if (status <= 300 || status == 501) {
                cnStatus = '出票中';
            } else if (status == 500) {
                cnStatus = '等待开奖';
            } else if (status == 600) {
                if (returnFlag == 2) {
                    cnStatus = '系统撤单';
                } else {
                    cnStatus = '用户撤单';
                }
            } else if (status == 1000) {
                cnStatus = '未中奖';
            } else if (status == 1500) {
                cnStatus = '大奖待审核';
            } else if (status == 2000) {
                cnStatus = '中奖';
            } else if (status == 5000) {
                cnStatus = '已派奖'
            }
            return cnStatus;
        };

        return me;
    })();

})();
