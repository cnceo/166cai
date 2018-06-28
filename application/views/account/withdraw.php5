<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/money.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/dialog.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>"></script>
<script type="text/javascript">
$(function(){
    //组件表单交互
    $(".addCardForm p input").each(function(){
        var _this = $(this);
        $(this).focus(function(){
            _this.removeClass().addClass("hover");
            _this.next("span").hide();
        });
        $(this).blur(function(){
            if(_this.val() == ""){
                _this.removeClass();
                _this.next().show();
            }
        });
    });
    $(".addCardForm p span").each(function(){
        var _this = $(this);
        $(this).click(function(){
            _this.prev("input").focus();
        });
    });
    //LOGO选择
    $(".moneySelect").click(function() {
        var $this = $(this);
        $('.moneySelect').removeClass('selected');
        $this.addClass('selected');
        bind = binds[$this.data('key')];
    });
    $('.moneySelect').hover(function() {
        $(this).find('.input-clear').removeClass('hidden');
    }, function() {
        $(this).find('.input-clear').addClass('hidden');
    });
    $('.input-clear').click(function() {
        var $this = $(this);
        var $card = $this.closest('.moneySelect');
        var clearBind = binds[$card.data('key')];
        var data = {
            isToken: 1,
            isJson: 1,
            name: clearBind.name
        };
        var url = cx.url.getPayUrl('payBinding/bankcard/unbind');
        var tip = '确定解绑该银行卡?';
        if (clearBind.type == 2) {
            tip = '确定解绑该支付宝账号?';
            url = cx.url.getPayUrl('payBinding/alipay/unbind');
            data.id = clearBind.identification;
        } else {
            data.name = clearBind.name;
            data.cardNo = clearBind.identification;
            data.bankId = clearBind.payProvider;
        }
        new cx.Confirm({
            single: tip,
            confirmCb: function() {
                cx.ajax.post({
                    url: url,
                    data: data,
                    success: function(response) {
                        if (response.code == 0) {
                            $card.remove();
                            bind = null;
                        } else {
                            new cx.Alert({
                                content: response.msg
                            });
                        }
                    }
                });
            }
        });
    });
    var binds = $.parseJSON('<?php echo json_encode($binds); ?>');
    var bind = null;
    var withdrawForm = new cx.vform('.withdraw-form', {
        submit: function(data) {
            if (bind == null) {
                new cx.Alert({
                    title: 'test',
                    content: '请选择银行卡或支付宝。'
                });
                return;
            }
            $.ajax({
                type: 'post',
                url: baseUrl + 'ajax/withdraw',
                data: {
                    money: data.money,
                    name: bind.name,
                    identification: bind.identification,
                    payProvider: bind.payProvider
                },
                success: function(response) {
                    if (response.code == 0) {
                        cx.Alert({
                            title: 'test',
                            content: '提款申请成功，我们会在一个工作日内为您处理。',
                            confirmCb: function() {
                                location.href = baseUrl + 'bills';
                            }
                        });
                    } else {
                        cx.Alert({
                            title: 'test',
                            content: response.msg
                        });
                    }
                }
            });
        }
    });
});
</script>
<!--容器-->
<div class="addCardWrap clearfix">
        <h1>提款</h1>
        <div class="moneyTit">
            账户余额：<span><?php echo number_format($wallet['amount'], 2); ?></span>可提款：<strong><?php echo number_format($wallet['hdAmount'], 2); ?></strong>
        </div>
        <div class="addCardForm withdraw-form">
            <p>
                <input type="text" name="money" class="vcontent" data-rule="withdraw_money" />
                <span>请输入充值金额，至少提款10元。</span>
                <a class="getMoney submit">
                    <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/btn/btnUserTX.gif');?>" alt="提款" width="80" height="40" />
                </a>
                <em class="addCardError tip" style="display: none;">错误提示区</em>
            </p>
            <p style="color: red; height: 20px;">【重要】根据国家相关规定，请完善 "手机" 与 "身份证" 信息！ <a href="<?php echo $baseUrl; ?>account">现在就去&gt;&gt;</a></p>
        </div>
        <div class="moneyBank">
            <h2>选择提款方式</h2>
            <ul class="clearfix">
                <?php foreach ($binds as $key => $bind): ?>
                <?php if ($bind['type'] == 1): ?>
                <li class="moneySelect" data-key="<?php echo $key; ?>">
                    <div class="moneySelectBG">
                        <div class="fl">
                            <div class="bank-icon" style="background-position: 0 -<?php echo ($bind['payProvider'] - 1) * 43; ?>px"></div>
                        </div>
                        <div class="fr" style="position: relative;">
                            <strong><?php echo $banks[$bind['payProvider']]; ?></strong>
                            <span><?php echo $bind['identification']; ?></span>
                            <img class="input-clear hidden" src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/common/clear.png');?>" alt="" />
                        </div>
                    </div>
                    <em></em>
                </li>
                <?php endif; ?>
                <?php endforeach; ?>
                <li><a href="<?php echo $baseUrl; ?>account/bindBank">添加银行卡</a></li>
            </ul>
            <ul class="clearfix">
                <?php foreach ($binds as $bind): ?>
                <?php if ($bind['type'] == 2): ?>
                <li class="moneySelect" data-key="<?php echo $key; ?>">
                    <div class="moneySelectBG">
                        <div class="fl"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/logo/alipaySmall.gif');?>" alt="" width="41" height="41" /></div>
                        <div class="fr" style="position: relative;">
                            <strong>支付宝</strong>
                            <span><?php echo $bind['identification']; ?></span>
                        </div>
                        <img class="input-clear hidden" src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/common/clear.png');?>" alt="" />
                    </div>
                    <em></em>
                </li>
                <?php endif; ?>
                <?php endforeach; ?>
                <li><a href="<?php echo $baseUrl; ?>account/bindAlipay">添加支付宝</a></li>
            </ul>
        </div>
</div>
<!--容器end-->
