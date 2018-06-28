<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/pay.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/dialog.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js/vform.js?v=<?php echo VER_VFORM_JS; ?>"></script>
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
    $(".payLogo li").each(function(){
            var _this = $(this);
            _this.click(function(){
                    _this.addClass("selected").siblings().removeClass("selected");
                    $('.recharge-type').val(_this.data('type'));
            });
    });
    //Tab切换loginTabWrap
    var $divspan =$("div.payTabMenu span");
    $divspan.click(function(){
            $(this).addClass("selected").siblings().removeClass("selected");
            var index =  $divspan.index(this);
            $("div.payTabBox > div").eq(index).show().siblings().hide();
    });

    var rechargeForm = new cx.vform('.recharge-form', {
        submit: function(data) {
            this.$tip.hide();
            new cx.Confirm({
                single: '请在新开网页上完成付款后再选择',
                btns: [
                    {
                        type: 'cancel',
                        txt: '充值遇到问题'
                    },
                    {
                        type: 'confirm',
                        txt: '充值成功',
                        href: baseUrl + 'bills'
                    }
                ],
                cancelCb: function() {
                    new cx.Alert({
                        content: '请拨打客服电话<?php echo SERVICE_TEL; ?>'
                    });
                }
            });
            this.$submit.prop('form').submit();
        }
    });
});
</script>

<!--容器-->
<form method="post" class="addCardWrap clearfix recharge-form" target="_blank" action="<?php echo $baseUrl; ?>passport/thirdPay">
        <input type="hidden" name="type" class="recharge-type" value="1" />
        <h1>充值</h1>
        <div class="addCardForm">
            <p>
                <input type="text" class="vcontent" name="money" data-rule="recharge_money" />
                <span>请输入充值金额</span>
                <em class="addCardError tip" style="display: none; left: 330px;"></em>
            </p>
        </div>
        <div class="payAttention">充值金额30%不能用于提现！</div>
        <div class="payTabMenu">
            <span class="selected">第三方支付</span>
            <span style="display: none;">银行卡支付</span>
        </div>
        <div class="payTabBox">
                <!--第三方-->
                <div>
                    <ul class="payLogo">
                        <li class="selected" data-type="1">
                            <img src="images/logo/alipay.gif" alt="" width="170" height="40" />
                            <span></span>
                        </li>
                        <li data-type="2">
                            <img src="images/logo/bank_pingan.png" alt="" width="170" height="40" />
                            <span></span>
                        </li>
                    </ul>
                </div>
                <!--银行卡-->
                <div style="display:none">
                        <ul class="payLogo">
                            <li><img src="images/logo/bankABC.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankBJ.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankCBK.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankGD.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankGF.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankHX.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankICBC.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankJH.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankMS.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankSF.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankSHPD.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankSJ.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankXY.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankYC.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankZG.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankZH.gif" alt="" width="170" height="40" /><span></span></li>
                            <li><img src="images/logo/bankZX.gif" alt="" width="170" height="40" /><span></span></li>
                        </ul>
                </div>
        </div>
        <div class="paySubmit"><input type="button" class="submit" value="去支付" /></div>
</form>
<!--容器end-->
