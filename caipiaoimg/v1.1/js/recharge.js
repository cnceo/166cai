$(function(){
    //当为网银时候 进行初始化操作
    if(pay_type == '1_5' || pay_type == '1_6')
    {
       $('input[name=pd_FrpId]').val($('._mybankList ul li').eq(0).attr('data-val'));
    }
    //初始化选择
    $('._type_list ul li ').click(function(){
        $('._type_list li').removeClass('selected');
        $(this).addClass('selected');
        var _dataVal = $(this).attr('data-val');
        initRedPack(_dataVal);
        $('.other_money').val('');
        //更新选择金额和红包 
        $('input[name=p3_Amt]').val(_dataVal);
    });

     // 其他数额调整
    $('.recharge-form .other_money').focus(function(){
        // 清除红包信息
        $('input[name="redpack"]').val('');
        $('#redpackInfo li').removeClass('selected');
        // 清理固定金额
        $('.recharge-form .type_list li').removeClass('selected');
        $('.recharge-form .ipt_fee').val(0);
    });
    $('.recharge-form .other_money').blur(function(){
        var val = $(this).val();
        $('.recharge-form .ipt_fee').val( val );
        if( /^\d+$/.test(val) ) {
            if( parseInt(val) >= 10 ) {
                $('.recharge-form .ipt_fee').val( val );
            }else {
                //cx.Alert({content:'请输入10元以上整数'});
            }
        } else {
            $('.recharge-form .ipt_fee').val( 0 );
            //cx.Alert({content:'请输入10元以上整数'});
        }
    });
    //平台支付 - 选择指定红包
    $('#redpackInfo').on('click', 'li', function(){
        if($(this).hasClass('selected')){
            $(this).removeClass('selected');
        }else{
            $(this).addClass('selected');
            // 红包检查
            var redpackInfo = getRedpack();
            var money = $('input[name="p3_Amt"]').val();
            var redpackId = '';
            if(redpackInfo.length > 0){
                redpackId = redpackInfo.toString();
            }
            // 红包金额检查
            if(redpackId != '')
            {
                var redpacks = new Array();
                var checkMoney = 0;
                // 遍历规则条件
                redpacks = redpackId.split(",");
                for(i=0; i<redpacks.length; i++ ){
                    rule = redpacks[i].split("#");
                    checkMoney = checkMoney + parseFloat(rule[1]);
                } 
                if(isNaN(parseFloat(money)) || parseFloat(money) < parseFloat(checkMoney)){
                    $(this).removeClass('selected');
                    // 红包条件不满足
                    cx.Alert({content:'充值金额不满足红包使用条件'});
                    return false;
                }
            }
        }
    })
    //选择银行
    $('.bank_list').on('click', 'li', function(){
        var $this = $(this);
        $this.closest('.bank_list').find('li').removeClass('selected');
        $this.addClass('selected');
        $('input[name="pd_FrpId"]').val( $this.data('val') );
    });
    //选择支付方式
    $('._payWay').on('click', 'li', function(){
        var $this = $(this);
        $this.closest('._payWay').find('li').removeClass('selected');
        $this.addClass('selected');
        $('input[name="mode"]').val( $this.attr('data-value') );
    });   

    /**
     * [is_recharge  1：充值提交验证 0：收银台提交验证]
     * @type {Boolean}
     */
    if(is_recharge==1)
    {
        //初始化 20元红包
        initRedPack(20);
        new cx.vform('._recharge', {
            renderTip: 'renderTips',
            submit: function(data) {
                var self = this;

                if(_userStatus == 2)
                {
                    cx.Alert({content:'您的账户已被冻结，如需解冻请联系客服。'});
                    return false;
                }
                if (self.$form.find('a').hasClass('not-bind')) {
                    return ;
                }
                if( data.p3_Amt < 10 ) {
                   cx.Alert({content:'请输入10元以上整数'});
                    return false;
                }
                if(parseInt($('.other_money').val())/2000>1 && isZfb)
                {
                  cx.Alert({content:'支付宝充值限额2000元'});
                    return false;
                }
                jumpBank( self, data, 'recharge-form');
            }
        });

    }else{
        //收银台红包初始化
        initRedPack($('#need_recharge').attr('data-val'));
        //收银台提交
        new cx.vform('._cashier', {
            renderTip: 'renderTips',
            submit: function(data) {
                var self = this;
                if(_userStatus == 2)
                {
                    cx.Alert({content:'您的账户已被冻结，如需解冻请联系客服。'});
                    return false;
                }
                if (self.$form.find('a').hasClass('not-bind')) {
                    return ;
                }
                if( data.p3_Amt <= 0 ) {
                    cx.Alert({content:'充值数额不合理'});
                    return false;
                }
                // 获取红包方案
                var redpackInfo = getRedpack();

                var redpackId = '';
                if(redpackInfo.length > 0){
                    redpackId = redpackInfo.toString();
                }

                // 红包金额检查
                if(redpackId != '')
                {
                    var redpacks = new Array();
                    var checkMoney = 0;
                    // 遍历规则条件
                    redpacks = redpackId.split(",");
                    for(i=0; i<redpacks.length; i++ ){
                        rule = redpacks[i].split("#");
                        checkMoney = checkMoney + parseFloat(rule[1]);
                    } 
                    if(isNaN(parseFloat(data.p3_Amt)) || parseFloat(data.p3_Amt) < parseFloat(checkMoney)){
                        // 红包条件不满足
                        cx.Alert({content:'充值金额不满足红包使用条件'});
                        $('input[name="redpack"]').val('');
                        return false;
                    }
                    $('input[name="redpack"]').val(redpackId);
                }

                $('._cashier').submit();

                switch (parseInt(orderType, 10)) {
                    case 4:
                        cx.Confirm({
                            content: '<div class="pop-txt text-indent"><i class="icon-font">&#xe61f;</i>请在新开页面完成付款，付款完成前请不要关闭此弹框</div><p class="pop-help">支付遇到问题：<a href="/help/index/b1-f7">如何使用银行卡支付</a></p>',
                            btns:[{type: 'cancel', href: '/hall', txt: '继续购彩'}, {type: 'confirm', href: '/mylottery/betlog', txt: '查看详情'}],
                            cancelCb: function(){location.href = location.href;}
                        });
                        break;
                    case 5:
                        cx.Confirm({
                            content: '<div class="pop-txt text-indent"><i class="icon-font">&#xe61f;</i>请在新开页面完成付款，付款完成前请不要关闭此弹框</div><p class="pop-help">支付遇到问题：<a href="/help/index/b1-f7">如何使用银行卡支付</a></p>',
                            btns:[{type: 'cancel', href: '/gendan', txt: '继续购彩'}, {type: 'confirm', href: '/mylottery/recharge', txt: '查看详情'}],
                            cancelCb: function(){location.href = location.href;}
                        });
                        break;                        
                    case 1:
                        cx.Confirm({
                            content: '<div class="pop-txt text-indent"><i class="icon-font">&#xe61f;</i>请在新开页面完成付款，付款完成前请不要关闭此弹框</div><p class="pop-help">支付遇到问题：<a href="/help/index/b1-f7">如何使用银行卡支付</a></p>',
                            btns:[{type: 'cancel', href: '/hall', txt: '继续购彩'}, {type: 'confirm', href: '/chases/detail/' + orderId, txt: '查看详情'}],
                            cancelCb: function(){location.href = location.href;}
                        });
                        break;
                    default:
                        cx.Confirm({
                            content: '<div class="pop-txt text-indent"><i class="icon-font">&#xe61f;</i>请在新开页面完成付款，付款完成前请不要关闭此弹框</div><p class="pop-help">支付遇到问题：<a href="/help/index/b1-f7">如何使用银行卡支付</a></p>',
                            btns:[{type: 'cancel', href: '/hall', txt: '继续购彩'}, {type: 'confirm', href: '/orders/detail/' + orderId, txt: '查看详情'}],
                            cancelCb: function(){location.href = location.href;}
                        });
                        break;
                }
            }
        });

    }
    /**
     * [getRedpack 获取红包信息]
     * @author LiKangJian 2017-06-15
     * @return {[type]} [description]
     */
    function getRedpack()
    {
        var redpackInfo = [];
        if($('#redpackInfo li.selected').length > 0)
        {
            $('#redpackInfo li.selected').each(function(){
                redpackInfo.push($(this).attr('redpack-data'));
            });
        }
        return redpackInfo;
    }
    /**
     * [initRedPack 红包初始化选择]
     * @author LiKangJian 2017-06-26
     * @param  {[type]} val [description]
     * @return {[type]}     [description]
     */
    function initRedPack(val)
    {
        var _redpacks = $('.hongbao-s ul li');
        var _push_big = new Array();
        var _push_arr = new Array();
        if(_redpacks.length>0)
        {
            $('.hongbao-s ul li').removeClass('selected');
            $('.redpack'+val).addClass('selected');
            if($('.hongbao-s ul li.selected').length>0)
            {
                $('.hongbao-s ul li.selected').each(function(index){
                    if(index>0){$(this).removeClass('selected');}
                });
                if($('.hongbao-s .selected:hidden').length) 
                {
                 $('.hongbao-s .show-more a').trigger('click')
                }
            }else{
                _redpacks.each(function(index){
                    var _redData = $(this).attr('redpack-data');
                        _redData =  _redData.split('#');
                        if( parseFloat(val) > parseFloat(_redData[1]) )
                        {
                            _push_big[index] = _redData[1];
                            _push_arr.push(_redData[1]);
                        }
                });
                _max  = Math.max.apply( Math, _push_arr );
                for (var k in _push_big)
                {  
                  if(_push_big[k] == _max)
                  {
                    $('.hongbao-s ul li').eq(k).addClass('selected'); 
                    if($('.hongbao-s .selected:hidden').length) 
                    {
                     $('.hongbao-s .show-more a').trigger('click')
                    }
                    return;
                  }
                }     
            }


        }

    }
    /**
     * [jumpBank 提交后]
     * @author LiKangJian 2017-06-15
     * @param  {[type]} ctx   [description]
     * @param  {[type]} data  [description]
     * @param  {[type]} frame [description]
     * @return {[type]}       [description]
     */
    function jumpBank( ctx, data, frame ){
        // 获取红包方案
        var redpackInfo = getRedpack();
        var redpackId = '';
        if(redpackInfo.length > 0){
            redpackId = redpackInfo.toString();
        }
        // 红包金额检查
        if(redpackId != '')
        {
            var redpacks = new Array();
            var checkMoney = 0;
            // 遍历规则条件
            redpacks = redpackId.split(",");
            for(i=0; i<redpacks.length; i++ ){
                rule = redpacks[i].split("#");
                checkMoney = checkMoney + parseFloat(rule[1]);
            } 
            if(isNaN(parseFloat(data.p3_Amt)) || parseFloat(data.p3_Amt) < parseFloat(checkMoney)){
                // 红包条件不满足
                cx.Alert({content:'充值金额不满足红包使用条件'});
                $('input[name="redpack"]').val('');
                return false;
            }
            $('input[name="redpack"]').val(redpackId);
        }
        // 提交表单
        ctx.$form.submit();
        cx.Confirm({
            content: '<div class="pop-txt text-indent"><i class="icon-font">&#xe61f;</i>请在新开页面完成付款，付款完成前请不要关闭此弹框</div><p class="pop-help">支付遇到问题：<a href="/help/index/b1-f7" target="_blank">如何使用银行卡支付</a></p>',
            btns:[
                {
                    type: 'confirm',
                    href: '/mylottery/recharge',
                    txt: '已完成付款'
                }
            ],
            cancelCb: function(){
                location.href = location.href;
            }
        });        
    }

//checkbox-balance  total_money   data-balance  need_recharge  p3_Amt
$('body').on('click', '#checkbox-balance', function(){
    var _totalmoney = parseFloat($('#total_money').attr('data-totalmoney')) * 100;
    var _remain_money = parseFloat($('#remain_money').attr('data-balance')) * 100;
    if($(this).is(':checked'))
    {
        var _need_recharge = (_totalmoney-_remain_money)/100;
        $('#need_recharge').html(_need_recharge);
        $('input[name=p3_Amt]').val(_need_recharge);
        initRedPack(_need_recharge);
    }else{
        $('#need_recharge').html(_totalmoney/100);
        $('input[name=p3_Amt]').val(_totalmoney/100);
    }
});



});