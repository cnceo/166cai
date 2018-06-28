if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function() {
        FastClick.attach(document.body);
    }, false);
}

//充值方式展示更多交互
!function () {
    var wg = $('.recharge-way-group');
    if (wg.find('li').length <= 4 ) return;

    wg.append('<a href="javascript:;" class="more"><span>查看更多支付方式</span></a>');
    wg.on('click', '.more', function () {
        var moreInner = $(this).find('span');
        wg.toggleClass('open');
    })
}()
var paytoken = '';
$(function(){
    var rechargeIpt = $('.recharge-num-ipt');
    var rechargeNum = $('.recharge-num').find('li');
    var rechargeNumTxt;
    var flag = null;
    //var paytoken = '';


    // 返回充值页面时，充值金额的选择状态
    !(function(){
        var iptNum = $('#rechargeNumIpt').val();
        // 默认勾选红包
        selectRedpack(iptNum);

        if(iptNum == '10'){
            rechargeNum.removeClass('active');
            rechargeNum.eq(0).addClass('active');
        }else if(iptNum == '20'){
            rechargeNum.removeClass('active');
            rechargeNum.eq(1).addClass('active');
        }else if(iptNum == '50'){
            rechargeNum.removeClass('active');
            rechargeNum.eq(2).addClass('active');
        }else if(iptNum == '100'){
            rechargeNum.removeClass('active');
            rechargeNum.eq(3).addClass('active');
        }else if(iptNum == '200'){
            rechargeNum.removeClass('active');
            rechargeNum.eq(4).addClass('active');
        }else{
            rechargeNum.removeClass('active');
        }
    })()

    // 红包滚动 - 计算ul的宽度
    ;(function(){
        // var mRedPackets = $('.m-redPackets-bd');
        var mRedPacketsItem = $('.m-redPackets-bd').find('li');
        var mWidth = (mRedPacketsItem.width() + 20) * mRedPacketsItem.length - 20
        // mRedPackets.find('ul').css({'width': mWidth})

        var mRb = $('.m-redPackets-bd');
        var mRbi = $('.m-redPackets-bd-inner');
        var mRbiWidth = mRbi.width();
        
        if(mWidth > mRbiWidth) {
            mRb.addClass('show-r-mask');
        }
        mRbi.on('scroll', function(){
            if(mRbiWidth + mRbi.scrollLeft() >= mWidth - 1) {
                mRb.removeClass('show-r-mask');
            }else {
                mRb.addClass('show-r-mask');
            }
            if(mRbi.scrollLeft() > 0){
                mRb.addClass('show-l-mask');
            }else if(mRbi.scrollLeft() <= 1){
                mRb.removeClass('show-l-mask');
            }
        })
        
    })()

    rechargeNum.on('tap', function(){
        $('.recharge-num-ipt').blur();
        // 清除红包选择
        $('.m-redPackets-bd li').removeClass('selected');

        rechargeNumTxt = parseInt($(this).text());
        rechargeIpt.val(rechargeNumTxt);

        $(this).addClass('active').siblings().removeClass('active');

        // 默认勾选红包
        selectRedpack(rechargeNumTxt);
    });


    rechargeIpt.on('input', function(){   
        var iptNum = $(this).val();
        // 清除红包选择
        $('.m-redPackets-bd li').removeClass('selected');

        // 默认勾选红包
        selectRedpack(iptNum);

        if(iptNum == '10'){
            rechargeNum.removeClass('active')
            rechargeNum.eq(0).addClass('active');
        }else if(iptNum == '20'){
            rechargeNum.removeClass('active')
            rechargeNum.eq(1).addClass('active');
        }else if(iptNum == '50'){
            rechargeNum.removeClass('active')
            rechargeNum.eq(2).addClass('active');
        }else if(iptNum == '100'){
            rechargeNum.removeClass('active')
            rechargeNum.eq(3).addClass('active');
        }else if(iptNum == '200'){
            rechargeNum.removeClass('active')
            rechargeNum.eq(4).addClass('active');
        }else{
            rechargeNum.removeClass('active')
        }
    })

    // 金额检查
    function moneyCheck(){
        var val = $('.recharge-num-ipt').val();
        // 匹配数字
        if( /^\d+$/.test(val) ) {
            if( parseInt(val) < 1 ) { 
                $.tips({
                    content: '请至少充值10元',
                    stayTime: 2000
                })
            }
            return false;
        }else{
            $.tips({
                content:'请输入整数金额',
                stayTime:2000
            })
            return false;
        }
    }

    //选择红包
    function selectRedpack(money){
        var selectPack = 0;
        var rule, redMoney;
        var selectedId = '';
        // 当前选中的红包金额
        var selectedMoney = 0;
        money = parseFloat(money);
        $('.m-redPackets-bd li').each(function(){
            rule = $(this).attr('redpack-data').split("#");
            redMoney = parseFloat(rule[1]);
            if((money >= redMoney) && (selectPack < redMoney) && (selectedMoney < redMoney)){
                selectedMoney = redMoney;
                selectedId = rule[0];
            }
        });
        // 清除红包信息
        $('.m-redPackets-bd li').removeClass('selected');
        $('#redpackId-' + selectedId).addClass('selected');

        // 统计红包
        countRedpack();
    }

    // 获取所选择红包信息
    function getRedpack(){
        redpackInfo = [];
        $('.m-redPackets-bd li.selected').each(function(){
            redpackInfo.push($(this).attr('redpack-data'));
        });
        return redpackInfo;
    }

    // 统计红包信息
    function countRedpack(){
        var totals = 0;
        var selectedMoney = 0;
        var redpackInfo = getRedpack();
        if(redpackInfo.length > 0){
            redpackId = redpackInfo.toString();
            var redpacks= new Array();
            // 遍历规则条件
            redpacks = redpackId.split(",");
            for(i=0; i<redpacks.length; i++ ){
                rule = redpacks[i].split("#");
                selectedMoney = selectedMoney + parseFloat(rule[2]);
            } 
            totals = redpackInfo.length;
        }
        $('#rNum').text(totals);
        $('#rMoney').text(selectedMoney);
    }

    //显示隐藏密码
    $('.ipt-psw').find('a').on('tap', function(){
        var aPsw = $(this).parents('.ipt-psw').find('input');
        if(aPsw.attr('type') == 'password'){
            aPsw.attr('type', 'text');
        }else if(aPsw.attr('type') == 'text'){
            aPsw.attr('type', 'password');
        }
    })

    // 打开充值详情页面
    $('.btn-recharge').on('click', function(){
        // 点击统计
        try{
            android.umengStatistic('webview_goRecharge');
        }catch(e){
            // ...
        }
        var that = $(this);
        $('.pay-for-recharge').show();

        setTimeout(function () {
            $('.pay-for-recharge').addClass('recharge-show');
        }, 50)
    });

    // 选择充值方式
    // $('.recharge-way-group').on('tap', 'a', function(){
    //     $('.recharge-num-ipt').blur();
    //     if(!$(this).hasClass('checked'))
    //     {
    //         $('.recharge-way-group a').removeClass('checked');
    //         $(this).addClass('checked');
    //     }
    // })

    // 取消充值
    $('.pay-for-recharge').on('click', '.btn-block-cancel', function(){
        var pFc = $(this).parents('.pay-for-recharge');
        pFc.hide().removeClass('recharge-show');
    })

    // 选择指定红包
    $('.m-redPackets-bd').on('tap', 'li', function(){
        $('.recharge-num-ipt').blur();
        var money = $('input[name="rechargeMoney"]').val();
        $(this).parents('.m-redPackets-bd-inner').trigger('tap');
        if($(this).hasClass('selected')){
            $(this).removeClass('selected');
        }else{
            if(money == '')
            {
                $.tips({
                    content: '请先输入充值金额',
                    stayTime: 2000
                })
                return false;
            }

            if(isNaN(money) || money <= 0)
            {
                $.tips({
                    content: '充值金额错误',
                    stayTime: 2000
                })
                return false;
            }

            $(this).addClass('selected');
            // 红包检查
            var redpackInfo = getRedpack();
            var redpackId = '';
            if(redpackInfo.length > 0){
                redpackId = redpackInfo.toString();
            }

            // 红包金额检查
            if(redpackId != '')
            {
                var redpacks= new Array();
                var checkMoney = 0;
                // 遍历规则条件
                redpacks = redpackId.split(",");
                for(i=0; i<redpacks.length; i++ ){
                    rule = redpacks[i].split("#");
                    checkMoney = checkMoney + parseFloat(rule[1]);
                } 
                if(parseFloat(money) < parseFloat(checkMoney)){
                    // 红包条件不满足
                    $(this).removeClass('selected');
                    $.tips({
                        content: '充值金额不满足红包使用条件',
                        stayTime: 2000
                    })
                    return false;
                }
            }
        }
        // 统计红包
        countRedpack();
    })

    // 关闭红包提示
    $('#redpack-alert').on('tap', function(){
        // $('input[name="redpackId"]').val('');
        $('#redpack-alert').hide();
    })

    // 关闭失效红包提示
    $('#redpack-used').on('tap', function(){
        $('#redpack-used').hide();
        window.location.reload();
    })

    // 关闭充值金额限制提示
    $('#recharge-limit-confirm').on('click', function(){
        $('#recharge-limit').hide();
    })

    var closeTag = true;
    var lastDate = 0;
    
    $('#change_bankid').on('tap', function(){
    	$('#doPayForm input[name="change_bankid"]').val('1');
    	$('#payYl').trigger('click');
    	try{// 点击事件
            android.umengStatistic('webview_umpay_changecard');
        }catch(e){}
    	$('#btn-block-confirm').trigger('tap');
    })

    // 充值
    $('#btn-block-confirm').on('tap', function(e){

        // 失焦
        $('.recharge-num-ipt').blur();
        // 检查金额
        var val = $('.recharge-num-ipt').val();

        var money = parseFloat($('input[name="rechargeMoney"]').val());
        var token = $('input[name="token"]').val();
        var redirectPage = $('input[name="redirectPage"]').val();
        var pay_type = $('input[name="rechargeWay"]:checked').attr('pay-data');
        var jsaction = $('input[name="rechargeWay"]:checked').attr('jsaction');
        var maxmoney = parseFloat($('input[name="rechargeWay"]:checked').attr('maxmoney'));
        var secredpack = $('input[name="rechargeWay"]:checked').attr('secredpack');
        var appVersionCode = $('input[name="appVersionCode"]').val();

        // 获取红包方案
        redpackInfo = getRedpack();

        var redpackId = '';
        if(redpackInfo.length > 0){
            redpackId = redpackInfo.toString();
        }

        // 获取版本渠道
        try{
            var app_version = android.getAppVersion();
            var channel = android.getAppChannel();
            // 点击事件
            android.umengStatistic('webview_recharge');
        }catch(e){
            var app_version = '';
            var channel = '';
        }

        // 充值渠道关闭
        // $.tips({
        //     content: '充值服务暂时关闭，重新开启后我们会通知您，带来不便，敬请谅解！',
        //     stayTime: 2000
        // })
        // return false;

        if(money == '')
        {
            $.tips({
                content: '请输入充值金额',
                stayTime: 2000
            })
            return false;
        }

        if(redirectPage == 'order'){
            if( /^(([1-9][0-9]*)|(([0]\.\d{1,2}|[1-9][0-9]*\.\d{1,2})))$/.test(money) ) {
                if( money <= 0 ) { 
                    $.tips({
                        content: '充值金额格式错误',
                        stayTime: 2000
                    })
                    return false;
                }
            }else{
                $.tips({
                    content:'充值金额格式错误',
                    stayTime:2000
                })
                return false;
            }
        }else{
            if( /^[1-9][0-9]*$/.test(money) ) {
                if( parseInt(money) < 10 ) { 
                    $.tips({
                        content: '请至少充值10元',
                        stayTime: 2000
                    })
                    return false;
                }
            }else{
                $.tips({
                    content:'请输入整数金额',
                    stayTime:2000
                })
                return false;
            }
        }
        
        if (maxmoney > 0 && money > maxmoney) {
        	$('#limit-money').html(maxmoney);
            $('#recharge-limit').show();
            $('input[name="rechargeMoney"]').val(maxmoney);
            selectRedpack(secredpack);
            return false;
        }

        // 红包金额检查
        if(redpackId != '')
        {
            var redpacks= new Array();
            var checkMoney = 0;
            // 遍历规则条件
            redpacks = redpackId.split(",");
            for(i=0; i<redpacks.length; i++ ){
                rule = redpacks[i].split("#");
                checkMoney = checkMoney + parseFloat(rule[1]);
            } 
            if(parseFloat(money) < parseFloat(checkMoney)){
                // 红包条件不满足
                $('#redpack-alert').show();
                return false;
            }
        }

        // 提交充值记录
        if(closeTag)
        {
            // 获取当前时间戳
            var tagDate = new Date();
            var time = tagDate.getTime() - lastDate;
            if(0 < time && time < 450)
            {
                return false;
            }
            lastDate = tagDate.getTime();

            closeTag = false;

            // 浮层
            var loadPay = $.loading().loading("mask");
            $.ajax({
                type: 'post',
                url: '/app/api/v1/wallet/doRecharge',
                data: {money:money,token:token,pay_type:pay_type,redirectPage:redirectPage,redpackId:redpackId,app_version:app_version,channel:channel},
                // beforeSend: loading,
                success: function (response) {
                    var response = $.parseJSON(response);
                    loadPay.loading("hide");
                    if(response.data.way && (response.data.way =='wftZfbWap' || response.data.way =='wftWxWap' || response.data.way == 'yzpay'))
                    {
                        closeTag = true;
                        window.location.href = response.data.code_url;
                    }else if(jsaction === 'llpay' && response.status == 1)
                    {
                        closeTag = true;
                        pay.llpay(response.data,response.token);
                    }else if(jsaction === 'wxpay' && response.status == 1){
                        closeTag = true;
                        if(response.data.pay_type =='hjWxWap')
                        {
                            // 填充提交
                            $('#doPayForm input[name="uid"]').val(response.data.uid);
                            $('#doPayForm input[name="trade_no"]').val(response.data.trade_no);
                            $('#doPayForm input[name="money"]').val(response.data.money);
                            $('#doPayForm input[name="ip"]').val(response.data.ip);
                            $('#doPayForm input[name="real_name"]').val(response.data.real_name);
                            $('#doPayForm input[name="id_card"]').val(response.data.id_card);
                            $('#doPayForm input[name="merId"]').val(response.data.merId);
                            $('#doPayForm input[name="configId"]').val(response.data.configId);
                            $('#doPayForm input[name="pay_type"]').val(response.data.pay_type);
                            $('#doPayForm input[name="refer"]').val(encodeURI(location.href));
                            $('#doPayForm input[name="token"]').val(response.data.token);
                            closeTag = true;
                            $('#doPayForm').submit();
                            $('#doPayForm input[name="change_bankid"]').val('0');
                        }else{
                            pay.wxpay(response.data,response.token);   
                        }
                        if(appVersionCode >= 11)
                        {
                            paytoken = response.data;
                            $('#recharge-completed').show();
                        }
                    }else if(jsaction === 'jdpay' && response.status == 1){
                        closeTag = true;
                        if(response.data.pay_type == 'jdPay'){
                            // 填充提交
                            $('#doPayForm input[name="uid"]').val(response.data.uid);
                            $('#doPayForm input[name="trade_no"]').val(response.data.trade_no);
                            $('#doPayForm input[name="money"]').val(response.data.money);
                            $('#doPayForm input[name="ip"]').val(response.data.ip);
                            $('#doPayForm input[name="real_name"]').val(response.data.real_name);
                            $('#doPayForm input[name="id_card"]').val(response.data.id_card);
                            $('#doPayForm input[name="merId"]').val(response.data.merId);
                            $('#doPayForm input[name="configId"]').val(response.data.configId);
                            $('#doPayForm input[name="pay_type"]').val(response.data.pay_type);
                            $('#doPayForm input[name="refer"]').val(encodeURI(location.href));
                            $('#doPayForm input[name="token"]').val(response.data.token);
                            
                            $('#doPayForm').submit();
                            $('#doPayForm input[name="change_bankid"]').val('0');
                        }else{
                            pay.jdpay(response.data,response.token);
                        }
                    }else if(jsaction === 'dopayform' && response.status == 1){
                        // 填充提交
                        $('#doPayForm input[name="uid"]').val(response.data.uid);
                        $('#doPayForm input[name="trade_no"]').val(response.data.trade_no);
                        $('#doPayForm input[name="money"]').val(response.data.money);
                        $('#doPayForm input[name="ip"]').val(response.data.ip);
                        $('#doPayForm input[name="real_name"]').val(response.data.real_name);
                        $('#doPayForm input[name="id_card"]').val(response.data.id_card);
                        $('#doPayForm input[name="merId"]').val(response.data.merId);
                        $('#doPayForm input[name="configId"]').val(response.data.configId);
                        $('#doPayForm input[name="pay_type"]').val(response.data.pay_type);
                        $('#doPayForm input[name="refer"]').val(encodeURI(location.href));
                        $('#doPayForm input[name="token"]').val(response.data.token);
                        closeTag = true;
                        $('#doPayForm').submit();
                        $('#doPayForm input[name="change_bankid"]').val('0');
                    }else if(jsaction === 'hrefrediect' && response.status == 1){
                        closeTag = true;
                        window.location.href = response.data;
                    }else if(response.status == 2){
                        // 红包不可用
                        closeTag = true;
                        $('#redpack-used').show();
                    }else{
                        closeTag = true;
                        $.tips({
                            content: response.msg,
                            stayTime: 2000
                        })
                    }
                },
                error: function () {
                    closeTag = true;
                    loadPay.loading("hide");
                    $.tips({
                        content: '网络异常，请稍后再试',
                        stayTime: 2000
                    })
                }
            });
        }
        
    })

    // 关闭充值是否完成提示
    $('#recharge-completed-cancel').on('click', function(){
        $('#recharge-completed').hide();
    })

    $('#recharge-completed-confirm').on('click', function(){
        var redirectPage = $('input[name="redirectPage"]').val();
        if(redirectPage == 'order'){
            // 跳转支付
            $.ajax({
                    type: 'get',
                    url: '/app/wallet/getWalletStatus/'+paytoken,
                    success: function (response) {
                        var response = $.parseJSON(response);
                        if(response.status){
                            window.location.href=response.url;
                        }else{
                            window.location.reload();
                        }
                    }
            });
        }else{
            // 跳转充值记录
            try{
                android.goActivity('com.caipiao166.accountmanager.UserRecordActivity');
            }catch(e){
                           
            }
            $('#recharge-completed').hide();
        }
    })
})
