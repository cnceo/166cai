<form target="recharge_phone" action='https://pay.2345.com/cardPay.php' method='post' class="form rchg_form" accept-charset="GB2312" >
	<input type='hidden' class='vcontent' name='mode' value='Cardtype'>
	<input type='hidden' class='vcontent' name='mid' value='CP'>
    <input type='hidden' class='vcontent' name='trade_no' value='<?php echo $trade_no;?>'>
    <input type='hidden' class='vcontent' name='dateline' value='<?php echo $dateline;?>'>		

    <input type='hidden' class='vcontent ipt_pay_type' name='Cardtype' value='1'>
    <input type='hidden' class='vcontent ipt_fee' name='Amount' value='50'>
    <input type='hidden' name='token' value=''>
    
    <div class="form-item">
        <label class="form-item-label">充值方式</label>
        <div class="bank_list rechargeCard_list">
            <ul class="clearfix">
                <li data-val='1' class="selected"><img title="移动充值卡" alt="移动充值卡" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/yidong.png');?>"><i class="s_yes"></i></li>
                <li data-val='2'><img title="联通充值卡" alt="联通充值卡" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/liantong.png');?>"><i class="s_yes"></i></li>
                <li data-val='3'><img title="电信充值卡" alt="电信充值卡" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/dianxin.png');?>"><i class="s_yes"></i></li>
            </ul>
        </div>
    </div>
    <div class="form-item">
        <label class="form-item-label">充值金额</label>
        <div class="form-item-con">
            <div class="type_list">
                <ul class="clearfix">
                    <li data-val='10'>10元<i class="s_yes"></i></li>
                    <li data-val='20'>20元<i class="s_yes"></i></li>
                    <li data-val='30'>30元<i class="s_yes"></i></li>
                    <li data-val='50' class="selected">50元<i class="s_yes"></i></li>
                    <li data-val='100'>100元<i class="s_yes"></i></li>
                </ul>
            </div>  
        </div>
    </div>
    <div class="form-item">
        <label class="form-item-label">实际到账金额</label>
        <div class="form-item-con">
            <div class="dz-con">
                <b class="money">48.00</b>元（服务费：<b class="spec fee">2.00</b>元，由运营商收取）
            </div> 
        </div>
    </div>
    <div class="form-item">
        <label class="form-item-label">充值卡序列号</label>
        <div class="form-item-con">
            <input type="text" class="form-item-ipt vcontent" value="" name="CardNo">
        </div>
    </div>
    <div class="form-item">
        <label class="form-item-label">充值卡密码</label>
        <div class="form-item-con">
            <input type="text" class="form-item-ipt vcontent" value="" name="CardPwd">
        </div>
    </div>
    <div class="form-item btn-group">
        <div class="form-item-con">
            <a href="javascript:;" class="btn btn-confirm submit<?php echo $showBind ? ' not-bind': '';?>">立即充值</a>
            <!-- <a href="javascript:;" class="btn btn-confirm btn-disabled<?php echo $showBind ? ' not-bind': '';?>">立即充值</a> -->
        </div>
    </div>
</form>