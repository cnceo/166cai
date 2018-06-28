cx.single = function(data, obj, aptype) {
        switch (obj.ctype) {
            case 'create':
                cx.singleAjax.post({
                    url: "order/create",
                    data: data,
                    success: function(response) {
                        if ($.inArray(obj.lotteryId, [42, 43]) > -1 && obj.orderType == 4) cx.PopCom.hide($('.pop-pay'));
                        obj.ctype = 'pay';
                        obj.fromcreate = 1;
                        data = response.data;
                        data.code = response.code;
                        data.msg = response.msg;
                        cx.single(data, obj, aptype);
                    }
                });
                break;
            case 'paysearch':
                var datas = { orderId: data.orderId };
                switch (obj.orderType) {
                    case 4:
                        var url = '/hemai/getOrderInfo';
                        datas.buyMoney = data.buyMoney;
                        break;
                    case 1:
                        var url = '/chases/info';
                        break;
                    default:
                        var url = '/orders/info';
                        break;
                }
                $.ajax({
                    type: 'post',
                    url: url,
                    data: datas,
                    success: function(response) {
                        obj.ctype = 'pay';
                        obj.lotteryId = parseInt(response.lid, 10);
                        obj.issue = response.issue;
                        obj.typeCnName = response.typeCnName;
                        response.type = 1;
                        switch (obj.orderType) {
                            case 4:
                                response.money = obj.buyMoney;
                                break;
                            case 1:
                                obj.chaseLength = response.totalIssue;
                                obj.betMoney = response.betMoney;
                                break;
                            default:
                                break;
                        }
                        response.orderId = data.orderId;
                        cx.single(response, obj, aptype);
                    }
                })
                break;
            case 'pay':
                var tip = '付款后，您的订单将会自动分配到空闲的投注站出票', txt = '付款到彩店';
                if ($.inArray(data.code, [0, 12]) > -1) {
                    switch (obj.orderType) {
                        case 1:
                            var datas = {ctype: 'pay', orderType:obj.orderType, chaseId: data.orderId, money: data.money};
                            var binfo = betInfo.chase(getCnName(obj.lotteryId), obj.betMoney, obj.chaseLength, data.money, data.remain_money);
                            break;
                        case 4:
                            var datas = {ctype: 'pay', orderType:obj.orderType, type:(data.type ? data.type : 0), orderId: data.orderId, money: data.money};
                            if ($.inArray(obj.lotteryId, [42, 43]) > -1) {
                                var binfo = betInfo.jc(obj.typeCnName, data.money, data.remain_money, obj.buyMoney, obj.guarantee);
                            }else {
                                var binfo = betInfo.number(getCnName(obj.lotteryId), obj.issue, data.money, data.remain_money, obj.buyMoney, obj.guarantee);
                            }
                            tip = '付款后，合买订单满员将会自动分配到空闲的投注站出票';
                            txt = obj.fromcreate !== undefined ? '发起合买' : '参与合买';
                            break;
                        case 0:
                        default:
                            if(data.redpack !== undefined){
                                if ($.inArray(obj.lotteryId, [42, 43]) > -1) {
                                    var binfo = betInfo.redpackJc(obj.typeCnName, data.money, data.remain_money, data.redpack);
                                }else {
                                    var binfo = betInfo.redpackNumber(getCnName(obj.lotteryId), obj.issue, data.money, data.remain_money, data.redpack);
                                }
                                var datas = {ctype: 'pay', orderType:obj.orderType, orderId: data.orderId, money: data.money, redpackId:data.redpackId};
                            }
                            else
                            {
                                var datas = {ctype: 'pay', orderType:obj.orderType, orderId: data.orderId, money: data.money};
                                if ($.inArray(obj.lotteryId, [42, 43]) > -1) {
                                    var binfo = betInfo.jc(obj.typeCnName, data.money, data.remain_money);
                                }else {
                                    var binfo = betInfo.number(getCnName(obj.lotteryId), obj.issue, data.money, data.remain_money);
                                }
                            }
                            var cmoney = parseInt(data.money.replace(/,|\./g, ''));
                            if(data.redpackId !== undefined){
                                for (i = 0; i < data.redpack.length; i++) {
                                    if(data.redpack[i].id == data.redpackId){
                                        cmoney = cmoney - parseInt(data.redpack[i].money.replace(/,|\./g, ''));
                                        break;
                                    }
                                }
                            }
                            if(parseInt(data.remain_money.replace(/,|\./g, '')) < cmoney){
                                tip = '';
                                txt = '去充值';
                            }
                            else {
                                data.code = 0;
                            }
                            break;
                    }
                    obj.binfo = binfo;
                }
                delete obj.ctype;
                if (data.code == 0) {
                    new cx.Confirm({
                        title: '确认投注信息',
                        content: binfo,
                        input: 0,
                        tip: tip,
                        btns: [{type: 'confirm', txt: txt, href: 'javascript:;'}],
                        confirmCb: function() {
                            if(data.redpackId !== undefined){
                                var redpackId = $("input[name='redpackId']").val();
                                datas.redpackId = redpackId;
                                if($('#mustRecharge').closest('.form-pop-tip').is(":visible")){
                                    self.location=baseUrl+'wallet/directPay?orderId='+data.orderId+'&orderType='+obj.orderType + '&betRedpack=' + redpackId;
                                }else{
                                    cx.singleAjax.post({
                                        url: 'order/pay', 
                                        data: datas, 
                                        success: function(response) {
                                            for (i in response) {
                                                data[i] = response[i];
                                            }
                                            data.code = response.code;
                                            data.msg = response.msg;
                                            cx.single(data, obj);
                                        }
                                    });
                                }
                            }else{
                                cx.singleAjax.post({
                                    url: 'order/pay', 
                                    data: datas, 
                                    success: function(response) {
                                        for (i in response) {
                                            data[i] = response[i];
                                        }
                                        data.code = response.code;
                                        data.msg = response.msg;
                                        cx.single(data, obj);
                                    }
                                });
                            }
                        }
                    });
                }else {
                    cx.single(data, obj);
                }
                break;
            default:
                switch (data.code) {
                    case 0:
                    case 200:
                        if ('random' in obj) obj.random();
                        if(aptype){
                            $('.pop-confirm').remove();
                            cx.Mask.hide();
                        }
                        switch (obj.orderType) {
                            case 1:
                                var href = baseUrl + 'chases/detail/' + data.orderId;
                                break;
                            case 4:
                                var href = baseUrl + 'hemai/detail/hm' + data.orderId;
                                break;
                            default:
                                var href = baseUrl + 'orders/detail/' + data.orderId;
                                break;
                        }
                        new cx.Confirm({
                            content: data.msg,
                            btns: obj.btns || [{type: 'cancel', txt: '再来一单'}, {type: 'confirm', target: '_blank', txt: '查看详情', href: href}],
                            cancelCb: function() {var str = location.href.split("?"); location.href = str[0];},
                            confirmCb: function() {var str = location.href.split("?"); location.href = str[0];},
                            append:'<div class="pop-side"><div class="qrcode"><table><tbody><tr><td><img src="/caipiaoimg/v1.1/img/qrcode-pay.png" width="94" height="94" alt=""></td><td><p><b>扫码下载手机客户端</b>中奖结果早知道</p></td></tr></tbody></table></div></div>'
                        });
                        break;
                    case 402:
                        new cx.Alert({content:"<i class='icon-font'>&#xe611;</i>"+data.msg+"<br><a class='sub-color fz16' href='/help/index/b2-s2-f3' target='_blank'>什么是限号?</a>"});
                        break;
                    case 12:
                        var href = baseUrl + 'wallet/recharge';
                        var target = "_blank";
                        new cx.Confirm({title: '确认投注信息', content: obj.binfo, btns: [{type: 'confirm', txt: '去充值', href: href, target : target}]});
                        break;
                    case 3000:
                        new cx.Confirm({
                            content: '<div class="mod-result result-success"><div class="mod-result-bd"><div class="result-txt"><h2 class="result-txt-title">您的登录已超时，请重新登录！</h2></div></div></div>',
                            btns: [{type: 'confirm', txt: '重新登录', href: baseUrl + 'main/login'}]
                        });
                        break;
                    case 998:
                        new cx.Alert({content: data.msg});
                        break;
                    default:
                        if (obj.msgconfirmCb) {
                            new cx.Confirm({single: data.msg, btns: [{type: 'confirm', txt: '确定', href: href}], confirmCb:obj.msgconfirmCb});
                        }else if(aptype){
                            $(aptype).html(data.msg);
                        }else{
                            new cx.Alert({content: data.msg});
                        }
                        break;
                }
                break;
        }
    };

    var singleAjax  = cx.singleAjax = (function() {
        var me = {};
        var success;
        var locks = {};
        me.get = function(options) {
            var url = baseUrl + 'ajax/get';
            options.data || (options.data = {});
            var data = options.data;
            data['url'] = options.url;
            return $.ajax({
                url: url,
                data: data,
                success: function(response) {
                    if ('success' in options) {
                        options.success(response);
                    }
                }
            });
        }

        me.post = function(options) {
            var url = baseUrl + 'ajax/post';
            var data = options.data;
            data['url'] = options.url;
            var isJson = data.isJson || 0;
            data.isJson = isJson;
            if (!locks[options.url]) {
                locks[options.url] = true;
                return $.ajax({
                    type: 'post',
                    url: url,
                    data: data,
                    success: function(response) {
                        options.success(response);
                    },
                    complete: function() {
                        locks[options.url] = false;
                    }
                });
            }
            return false;
        };

        return me;
    })();
function singlePay( chaseId )
{    // 根据订单号, 获取订单状态, 信息(类型, 玩法, 期次), 用户剩余金额, 订单金额
    cx.single({orderId:chaseId}, {ctype:'paysearch', orderType:1});
}