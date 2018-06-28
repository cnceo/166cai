<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <meta http-equiv="Pragma" content="no-cache">
    <title>充值</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/recharge.min.css');?>">
    <style>
    .test-transform {
        position: absolute;
        left: 50%;
        bottom: 50%;
        width: 280px;
        height: 40px;
        background: red;
        -webkit-transform: translate3d(-50%, -50%, 0);
    }
    </style>
</head>
<body>
    <div class="wrapper recharge ios">
        <section class="cp-box cp-box-b recharge-area">
            <!-- <div class="cp-box-hd">
                <h1 class="cp-box-title">账户余额：<?php echo $money;?>元</h1>
            </div> -->
            <ul class="cp-list">
                <li>
                    <div class="cp-form-item">
                        <label for="rechargeNumIpt">充值金额</label>
                        <input name="rechargeMoney" type="number" id="rechargeNumIpt" class="recharge-num-ipt" min="1" maxlength="8" placeholder="请至少充值10元" value="<?php echo $rechargeMoney?$rechargeMoney:'20'; ?>"><span>元</span>
                    </div> 
                </li>
            </ul>
            
            <ul class="recharge-num">
                <li>10元</li><li class="active">20元</li><li>50元</li><li>100元</li><li>200元</li>
            </ul>
        </section>
        <?php if(!empty($redpackData)):?>
        <!-- 红包详情 -->
        <section class="cp-box m-redPackets">
            <header class="cp-box-hd">
                <h1 class="cp-box-title">选择红包</h1>
            </header>
            <?php if(!empty($redpackData)):?>
            <div class="m-redPackets-bd show-r-mask">
                <div class="m-redPackets-bd-inner">
                <ul>
                    <?php foreach( $redpackData as $key => $items ): ?>
                    <li redpack-data="<?php $params = json_decode($items['use_params'], true); echo $items['id'] . '#' . ParseUnit($params['money_bar'], 1);?>" class="redpack<?php echo ParseUnit($params['money_bar'], 1);?>" id="redpackId-<?php echo $items['id']; ?>">
                        <p><?php echo ParseDesc($items['use_desc']);?></p>
                        <p><?php echo ParseEnd($items['valid_end']);?></p>
                    </li>
                    <?php endforeach; ?>
                </ul>
                </div>
            </div>
            <?php endif;?>
            <!-- <p class="m-redPackets-ft">注：使用红包后，充值金额与红包均不能直接提现。</p> -->
        </section>
        <input type='hidden' class='' name='redpackNum' value='<?php echo count($redpackData);?>'/>
        <?php endif;?>
        <div class="btn-group">
           <a href="javascript:void(0)" class="btn btn-block-confirm" id="recharge-select">充值</a> 
        </div> 
        <input type='hidden' class='' name='token' value='<?php echo $token;?>'/>
        <input type='hidden' class='' name='redirectPage' value='<?php echo $redirectPage;?>'/>
        <input type='hidden' class='' name='channel' value='<?php echo $channel;?>'/>
        <input type='hidden' class='' name='appVersion' value='<?php echo $appVersion;?>'/>
    </div>
    <!-- 红包提示 -->
    <div class="ui-alert" id="redpack-alert" style="display: none;">
        <div class="ui-alert-inner">
            <div class="ui-alert-hd">提示</div>
            <div class="ui-alert-bd">
                <p>充值金额不满足红包使用条件</p>
                <p>请修改充值方案</p>
            </div>
            <div class="ui-alert-ft">
                <a href="javascript:;" class="special-color" id="redpack-alert-confirm">确认</a>
            </div>
        </div>
    </div>
    <!-- 红包后台失效提示 -->
    <div class="ui-alert" id="redpack-used" style="display: none;">
        <div class="ui-alert-inner">
            <div class="ui-alert-hd">提示</div>
            <div class="ui-alert-bd">
                <p>红包已失效，请重新选择。</p>
            </div>
            <div class="ui-alert-ft">
                <a href="javascript:;" class="special-color" id="redpack-used-confirm">确认</a>
            </div>
        </div>
    </div>
    <!-- 充值金额限制提示 -->
    <div class="ui-alert" id="recharge-limit" style="display: none;">
        <div class="ui-alert-inner">
            <!-- <div class="ui-alert-hd">提示</div> -->
            <div class="ui-alert-bd">
                <p>单笔充值限额5000元。大额充值请登录网页888.166cai.cn使用网上银行支付。</p>
            </div>
            <div class="ui-alert-ft">
                <a href="javascript:;" class="special-color" id="recharge-limit-confirm">确认</a>
            </div>
        </div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>" type="text/javascript"></script>
    <script>
        // 基础配置
        require.config({
            baseUrl: '//<?php echo DOMAIN;?>/caipiaoimg/static/js',
            paths: {
                "zepto" : "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/zepto.min",
                "frozen": "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/frozen.min",
                'basic':'//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/basic'
            }
        })
        require(['basic', 'ui/loading/src/loading', 'ui/tips/src/tips'], function(basic, loading, tips){

            $(function(){
                var rechargeIpt = $('.recharge-num-ipt');
                var rechargeNum = $('.recharge-num').find('li');
                var rechargeNumTxt;
                var flag = null;

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

                rechargeNum.on('click', function(){
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

                // 获取所选择红包信息
                function getRedpack(){
                    redpackInfo = [];
                    $('.m-redPackets-bd li.selected').each(function(){
                        redpackInfo.push($(this).attr('redpack-data'));
                    });
                    return redpackInfo;
                }

                // 选择红包
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
                }

                // 选择指定红包
                $('.m-redPackets-bd').on('click', 'li', function(){
                    $('.recharge-num-ipt').blur();
                    var money = $('input[name="rechargeMoney"]').val();
                    $(this).parents('.m-redPackets-bd-inner').trigger('click');
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
                                    content: '充值金额不满足此红包使用条件',
                                    stayTime: 2000
                                })
                                return false;
                            }
                        }
                    }
                })

                // 关闭红包提示
                $('#redpack-alert').on('click', function(){
                    // $('input[name="redpackId"]').val('');
                    $('#redpack-alert').hide();
                })

                // 关闭失效红包提示
                $('#redpack-used').on('click', function(){
                    $('#redpack-used').hide();
                    window.location.reload();
                })

                // 关闭充值金额限制提示
                $('#recharge-limit-confirm').on('tap', function(){
                    $('#recharge-limit').hide();
                })

                var selectTag = true;
                var selectDate = 0;

                // APP内金额及红包选择确认
                $('#recharge-select').on('click', function(){
                    // 失焦
                    $('.recharge-num-ipt').blur();
                    // 检查金额
                    var val = $('.recharge-num-ipt').val();

                    var money = $('input[name="rechargeMoney"]').val();
                    var token = $('input[name="token"]').val();
                    var redirectPage = $('input[name="redirectPage"]').val();
                    var channel = $('input[name="channel"]').val();
                    var appVersion = $('input[name="appVersion"]').val();

                    // 获取红包方案
                    redpackInfo = getRedpack();

                    var redpackId = '';
                    if(redpackInfo.length > 0){
                        redpackId = redpackInfo.toString();
                    }

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

                    // 充值金额校验
                    if(money > 5000)
                    {
                        $('#recharge-limit').show();
                        $('input[name="rechargeMoney"]').val('5000');
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
                    if(selectTag)
                    {
                        // 获取当前时间戳
                        var tagDate = new Date();
                        var time = tagDate.getTime() - selectDate;
                        if(0 < time && time < 450)
                        {
                            return false;
                        }
                        selectDate = tagDate.getTime();

                        selectTag = false;

                        // 浮层
                        var loadPay = $.loading().loading("mask");

                        $.ajax({
                            type: 'post',
                            url: '/ios/wallet/checkRecharge',
                            data: {money:money,token:token,redirectPage:redirectPage,redpackId:redpackId,channel:channel,appVersion:appVersion},
                            // beforeSend: loading,
                            success: function (response) {
                                var response = $.parseJSON(response);
                                loadPay.loading("hide");
                                if(response.status == 1)
                                {
                                    selectTag = true;
                                    // window.location.href = response.data;
                                    // 触发IOS跳转
                                    window.webkit.messageHandlers.jumpWeb.postMessage({
                                        url: response.data
                                    });
                                }else if(response.status == 2){
                                    // 红包不可用
                                    selectTag = true;
                                    $('#redpack-used').show();
                                }else{
                                    selectTag = true;
                                    $.tips({
                                        content: response.msg,
                                        stayTime: 2000
                                    })
                                }
                            },
                            error: function () {
                                selectTag = true;
                                loadPay.loading("hide");
                                $.tips({
                                    content: '网络异常，请稍后再试',
                                    stayTime: 2000
                                })
                            }
                        });
                    }
                })
            })
        });
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>