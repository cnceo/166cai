<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/pay.css');?>">
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
<?php $pay_type = 'shengpaybank';?>
<div class="wrap_in pay-container recharge-form">
    <!-- <form target="recharge_direct" action='http://pay.2345.com/doPay.php' method='post' id='rchg_form' name='rchg_form' accept-charset="GB2312" > -->
    <form target="recharge_direct" action='https://pay.2345.com/tenpay.php' method='post' id='rchg_form' name='rchg_form' accept-charset="GB2312" >
        <?php if($pay_type == 'shengpaybank'):?>
			<!-- 盛付通网银 -->
			<input type='hidden' class='vcontent ipt_paychannel' name='PayChannel' value='19'>
			<input type='hidden' class='vcontent ipt_bank' name='InstCode' value=''>
		<?php endif;?>
		<input type='hidden' class='vcontent mode' name='mode' value='pay_type'>
        <input type='hidden' class='vcontent' name='mid' value='CP'>
        <input type='hidden' class='vcontent' name='trade_no' value='<?php echo $trade_no;?>'><!--  -->
        <input type='hidden' class='vcontent' name='OrderNo' value='<?php echo $trade_no;?>'><!-- 盛付通PC -->
		
        <input type='hidden' name='spbill_create_ip' value='<?php echo UCIP;?>'><!-- 财付通PC  -->
        <input type='hidden' class="vcontent" name='BuyerIp' value='<?php echo UCIP;?>'><!-- 盛付通PC -->
		
        <input type='hidden' class='vcontent' name='dateline' value='<?php echo $dateline;?>'>		
        <input type='hidden' class='vcontent' name='OrderTime' value='<?php echo date('YmdHis', $dateline ); ?>'> <!-- 盛付通PC -->

		<input type='hidden' name='subject' value='购买彩票'><!-- 支付宝 , 财付通PC -->
		<input type='hidden' class="vcontent" name='ProductName' value='2345CP'><!-- 盛付通PC -->
        <input type='hidden' name='body' value='购买彩票'><!-- 财付通PC -->

        <input type='hidden' class='vcontent ipt_pay_type' name='pay_type' value='tenpay'><!-- 支付宝 , 财付通PC -->
        <input type='hidden' class='vcontent ipt_pay_type' name='PayType' value='tenpay'> <!-- 盛付通PC -->

        <input type='hidden' class='vcontent ipt_bank' name='bank' value=''> <!-- 支付宝 -->
        
        <input type='hidden' class='vcontent ipt_fee' name='total_fee' value='<?php echo ParseUnit($data['money'], 1);?>'> <!-- 支付宝, 财付通PC -->
        <input type='hidden' class='vcontent ipt_fee' name='OrderAmount' value='<?php echo ParseUnit($data['money'], 1);?>'> <!-- 盛付通PC -->

        <input type='hidden' class='vcontent' name='orderId' value='<?php echo $orderId;?>'>
        <input type='hidden' name='token' value=''>
        <input type='hidden' class='vcontent' name='orderType' value='<?php echo $orderType;?>'>
        
        <div class="product-info">
            <?php $datetime = strtotime($data['created']);?>
            <h2 class="tit">商品信息：<?php echo BetCnName::getCnName($data['lid']);?>第<?php echo $data['issue'];?>期</h2>
            <p class="buy-time">购买时间：<?php echo date('Y', $datetime)."年".date('m', $datetime)."月".date('d', $datetime)."日 ".date('H:i:s', $datetime);?></p>
            <p class="order-num">订单编号：<?php echo $data['orderId'];?></p>
            <span class="total-money" id="total_money" data-totalMoney='<?php echo ParseUnit($data['money'], 1);?>' >总金额：<b><?php echo ParseUnit($data['money'], 1);?></b>元</span>
        </div>

        <div class="pay-method-cont">
            <div class="balance">
                <div class="balance-min"><label for="checkbox-balance"><input type="checkbox" class="ipt_checkbox vcontent" name="checkbox-balance" id="checkbox-balance">余额支付</label>
                账户<strong class="account"><?php echo $this->uname;?></strong>余额<b class="money" id="remain_money" data-balance='<?php echo ParseUnit($money, 1);?>'><?php echo ParseUnit($money, 1);?></b>元
                </div>
                <div class="balance-item" id="payPwd">
                    <label class="label_like">支付密码</label>
                    <input type="password" name="pay_pwd" value="" style="width:160px;" class="ipt_text vcontent"><a href="/safe/paypwd" class="ml10">忘记密码?</a>
                </div>
                <span class="pay-nm">支付<b class="money" id="show_remain_money">0</b>元</span>
            </div>
            <div class="pay-form ">
                <span class="pay-nm">支付<b class="money recharge_money" id="need_recharge"><?php echo ParseUnit($data['money'], 1);?></b>元</span>
                <div class="item">
                    <label class="label_like">其他支付方式</label>
                    <div class="tab-nav">
                        <ul class="clearfix">
                            <li class="active" data-val='0'><a href="javascript:;"><span>支付平台</span></a></li>
                            <li class="" data-val='19'><a href="javascript:;"><span>网上银行</span></a></li>
                            <li class="" data-val='20'><a href="javascript:;"><span>信用卡</span></a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <div style="display: block;" class="tab-item bank_list">
                            <ul class="clearfix">
                                <!-- <li class="selected" data-val='directPay'><img alt="支付宝帐号支付" src="/caipiaoimg/v1.0/img/bank/alipay-account.png"><i class="s_yes"></i></li> -->
                                <li class="selected" data-val='tenpay'><img alt="财付通帐号支付" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/tenpay-account.png');?>"><i class="s_yes"></i></li>
                                <li class="" data-val='shengpay'><img alt="盛付通帐号支付" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/sft-account.png');?>"><i class="s_yes"></i></li>
                            </ul>
                        </div>
                        <div class="tab-item bank_list" style="display: none;">
                            <ul class="clearfix">
                                <?php if($pay_type == 'shengpaybank'):?>
                                <li data-val='ICBC' class="selected"><img title="中国工商银行" alt="中国工商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='PSBC'><img title="中国邮政储蓄银行" alt="中国邮政储蓄银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/yzcxyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='ABC'><img title="中国农业银行" alt="中国农业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/nyyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='CMB'><img title="招商银行" alt="招商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='CCB'><img title="中国建设银行" alt="中国建设银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/jsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='BOC'><img title="中国银行" alt="中国银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zgyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='COMM'><img title="交通银行" alt="交通银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/jtyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='CMBC'><img title="中国民生银行" alt="中国民生银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/msyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='CIB'><img title="兴业银行" alt="兴业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/xyyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='CEB'><img title="光大银行" alt="光大银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gdyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='HXB'><img title="华夏银行" alt="华夏银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/hxyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='CITIC'><img title="中信银行" alt="中信银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zxyh.png');?>"><i class="s_yes"></i></li>
                                <?php else:?>
                                <li data-val='1025' class="selected"><img title="中国工商银行" alt="中国工商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3230'><img title="中国邮政储蓄银行" alt="中国邮政储蓄银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/yzcxyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='103'><img title="中国农业银行" alt="中国农业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/nyyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3080'><img title="招商银行" alt="招商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='105'><img title="中国建设银行" alt="中国建设银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/jsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='104'><img title="中国银行" alt="中国银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zgyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='301'><img title="交通银行" alt="交通银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/jtyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='305'><img title="中国民生银行" alt="中国民生银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/msyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='309'><img title="兴业银行" alt="兴业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/xyyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='312'><img title="光大银行" alt="光大银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gdyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='311'><img title="华夏银行" alt="华夏银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/hxyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='313'><img title="中信银行" alt="中信银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zxyh.png');?>"><i class="s_yes"></i></li>
                                <?php endif;?>
                            </ul>
                            <div class="line"><a href="javascript:void(0);" class="other_bank">更多银行<i></i></a></div>
                            <ul style="display: none;" class="clearfix other_bank_detail">
                                <?php if($pay_type == 'shengpaybank'):?>
                                <li data-val='GDB'><img title="广东发展银行" alt="广东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gfyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='SPDB'><img title="上海浦东发展银行" alt="上海浦东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/shpdfzyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='NJCB'><img title="南京银行" alt="南京银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/njyh.png');?>"><i class="s_yes"></i></li>
                                <!-- <li data-val='324'><img title="杭州银行" alt="杭州银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/hzyh.png');?>"><i class="s_yes"></i></li> -->
                                <li data-val='NBCB'><img title="宁波银行" alt="宁波银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/nbyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='BCCB'><img title="北京银行" alt="北京银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bjyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='BOS'><img title="上海银行" alt="上海银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/shyh.png');?>"><i class="s_yes"></i></li>
                                <!-- <li data-val='335'><img title="北京农商行" alt="北京农商行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bjnsyh.png');?>"><i class="s_yes"></i></li> -->
                                <li data-val='BOCD'><img title="成都银行" alt="成都银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/cdyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='SZPAB'><img title="平安银行" alt="平安银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/payh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='SHRCB'><img title="上海农商银行" alt="上海农商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/shncsyyh.png');?>"><i class="s_yes"></i></li>
                                <?php else:?>
                                <li data-val='306'><img title="广东发展银行" alt="广东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gfyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='314'><img title="上海浦东发展银行" alt="上海浦东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/shpdfzyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='316'><img title="南京银行" alt="南京银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/njyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='324'><img title="杭州银行" alt="杭州银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/hzyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='302'><img title="宁波银行" alt="宁波银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/nbyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='310'><img title="北京银行" alt="北京银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bjyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='326'><img title="上海银行" alt="上海银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/shyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='335'><img title="北京农商行" alt="北京农商行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/bjnsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='336'><img title="成都银行" alt="成都银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/cdyh.png');?>"><i class="s_yes"></i></li>
                                <?php endif;?>
                            </ul>
                        </div>
                        <div class="tab-item bank_list" style="display: none;">
                            <ul class="clearfix">
                                <?php if($pay_type == 'shengpaybank'):?>
                                <li data-val='ICBC' class="selected"><img title="工商银行" alt="工商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='CMB'><img title="招商银行" alt="招商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='CCB'><img title="中国建设银行" alt="中国建设银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/jsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='BOC'><img title="中国银行" alt="中国银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zgyh.png');?>"><i class="s_yes"></i></li>
                                <!-- <li data-val='HXB'><img title="华夏银行" alt="华夏银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/hxyh.png');?>"><i class="s_yes"></i></li> -->
                                <li data-val='CMBC'><img title="中国民生银行" alt="中国民生银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/msyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='CEB'><img title="光大银行" alt="光大银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gdyh.png');?>"><i class="s_yes"></i></li>
                                <!-- <li data-val='PSBC'><img title="中国邮政储蓄银行" alt="中国邮政储蓄银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/yzcxyh.png');?>"><i class="s_yes"></i></li> -->
                                <!-- <li data-val='3241'><img title="杭州银行" alt="杭州银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/hzyh.png');?>"><i class="s_yes"></i></li> -->
                                <!-- <li data-val='NBCB'><img title="宁波银行" alt="宁波银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/nbyh.png');?>"><i class="s_yes"></i></li> -->
                                <li data-val='BOS'><img title="上海银行" alt="上海银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/shyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='SZPAB'><img title="平安银行" alt="平安银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/payh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='COMM'><img title="交通银行" alt="交通银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/jtyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='CIB'><img title="兴业银行" alt="兴业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/xyyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='GDB'><img title="广东发展银行" alt="广东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gfyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='SPDB'><img title="上海浦东发展银行" alt="上海浦东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/shpdfzyh.png');?>"><i class="s_yes"></i></li>
                                <!-- <li data-val='334'><img title="青岛银行" alt="青岛银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/qdyh.png');?>"><i class="s_yes"></i></li> -->
                                <?php else:?>
                                <li data-val='1027' class="selected"><img title="工商银行" alt="工商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='308'><img title="招商银行" alt="招商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='1054'><img title="中国建设银行" alt="中国建设银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/jsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='106'><img title="中国银行" alt="中国银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zgyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3112'><img title="华夏银行" alt="华夏银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/hxyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3051'><img title="中国民生银行" alt="中国民生银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/msyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3121'><img title="光大银行" alt="光大银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gdyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3231'><img title="中国邮政储蓄银行" alt="中国邮政储蓄银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/yzcxyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3241'><img title="杭州银行" alt="杭州银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/hzyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='303'><img title="宁波银行" alt="宁波银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/nbyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3261'><img title="上海银行" alt="上海银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/shyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='334'><img title="青岛银行" alt="青岛银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/qdyh.png');?>"><i class="s_yes"></i></li>
                                <?php endif;?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="item btn_area">
                    <a class="btn btn-blue-med submit<?php echo $showBind ? ' not-bind': '';?>" href="javascript:void(0);">确认预约</a>
                    <p class="tips">如选择其他支付方式，确认预约会跳转到对应支付方式页面完成支付</p>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
$(function(){

    var orderId = '<?php echo $orderId;?>';
	var $pay_type = '<?php echo $pay_type;?>';
	var paychannel = 0;
    var orderType = '<?php echo $orderType;?>';
    //Tab切换
    $('.tab-nav li').click(function(){
        var idx = $(this).index();
        paychannel = $(this).data('val');
        $(this).addClass('active').siblings('li').removeClass('active');
        $(".tab-content .tab-item").hide();

        var $selectTab = $('.tab-content .tab-item').eq(idx);
        $selectTab.show();
        
        // 处理pay_type bank
        var selectVal = $selectTab.find('.selected').data('val')
        var pay_type, bank, action;
        if( selectVal == 'directPay' ){
            pay_type = 'alipay';
            bank = 'directPay';
            action = 'http://pay.2345.com/doPay.php';
            $(".mode").val('pay_type');
        } else if( selectVal == 'tenpay' ) {
            pay_type = 'tenpay';
            bank = '';
            action = 'https://pay.2345.com/tenpay.php';
            $(".mode").val('pay_type');
        } else if( selectVal == 'shengpay' ) {
            pay_type = 'shengpay';
            bank = '';
            action = 'http://pay.2345.com/shengPay.php';
            $(".mode").val('PayType');
        } else {
        	if($pay_type == 'shengpaybank'){
            	action = 'http://pay.2345.com/shengPay.php';
            	bank = selectVal;
            	pay_type = $pay_type;
            	$('.ipt_paychannel').val(paychannel);
            	$(".mode").val('PayType');
            }else{
            	pay_type = 'chinabank';
                bank = selectVal;
                action = 'http://pay.2345.com/doPay.php';
                $(".mode").val('pay_type');
            }
        }

        $('.recharge-form .ipt_pay_type').val( pay_type );
        $('.recharge-form .ipt_bank').val( bank );
        $('#rchg_form').attr('action', action);
    
    });
    
    $('.bank_list li').click(function(){
        var val = $(this).data('val');
        var pay_type, bank, action;
        if( val == 'directPay' ){
            pay_type = 'alipay';
            bank = 'directPay';
            action = 'http://pay.2345.com/doPay.php';
            $(".mode").val('pay_type');
        } else if( val == 'tenpay' ){
            pay_type = 'tenpay';
            bank = '';
            action = 'https://pay.2345.com/tenpay.php';
            $(".mode").val('pay_type');
        } else if( val == 'shengpay' ){
            pay_type = 'shengpay';
            bank = '';
            action = 'http://pay.2345.com/shengPay.php';
            $(".mode").val('PayType');
        } else {
            if($pay_type == 'shengpaybank'){
            	action = 'http://pay.2345.com/shengPay.php';
            	bank = val;
            	pay_type = $pay_type;
            	$('.ipt_paychannel').val(paychannel);
            	$(".mode").val('PayType');
            }else{
            	pay_type = 'chinabank';
                bank = val;
                action = 'http://pay.2345.com/doPay.php';
                $(".mode").val('pay_type');
            }
        }

        $('.recharge-form .ipt_pay_type').val( pay_type );
        $('.recharge-form .ipt_bank').val( bank );
        $('#rchg_form').attr('action', action);
        
        $(this).closest('.bank_list').find('li').removeClass('selected');
        $(this).addClass('selected');

    });

    // 直接付款
    $("#checkbox-balance").click(function(){
        var total_money, balance, need_recharge;
        if($(this).prop("checked")){
            $(this).parents(".balance").addClass('balance-selected');
            $("#payPwd").show();
            $("#payPwd input[type='password']").addClass('vcontent');
            balance = $('#remain_money').data('balance');
            total_money = $('#total_money').data('totalmoney');
            $('#show_remain_money').html( balance );
            
            need_recharge = (total_money - balance).toFixed(2);
            $('#need_recharge').html(need_recharge);
            $('.recharge-form .ipt_fee').val( need_recharge ); 
        }else{
            $(this).parents(".balance").removeClass('balance-selected');
            $("#payPwd").hide();
            $("#payPwd input[type='password']").removeClass('vcontent').val('');
            total_money = $('#total_money').data('totalmoney');
            $('.recharge-form .ipt_fee').val( total_money ); 
            $('#show_remain_money').html( 0 );
            $('#need_recharge').html(total_money);
        }
    });

    new cx.vform('.recharge-form', {
        renderTip: 'renderTips',
        submit: function(data) {
            var self = this;
            if( data.total_fee <= 0 ) {
                cx.Alert({content:'充值数额不合理'});
                return false;
            }
            
            var blankFrame = window.open('about:blank','recharge_direct');

            $.ajax({
                type: 'post',
                url:  '/wallet/recharge',
                data: data,
                success: function(response) {
                    
                    if( response == 2 ){
                        cx.Alert({content:'支付密码错误'});
                        blankFrame.close();
                        return false;
                    } else if(response) {
                        self.$form.find('input[name="token"]').val(response);
                        $('#rchg_form').submit();

                        if(orderType){
                            cx.Confirm({
                                single:'请在新打开页面完成付款，付款完成前请不要关闭此页面<br /><a href="/wallet/directPay?orderId='+ orderId +'&orderType=1">选择其他支付方式</a>',
                                btns:[
                                    {
                                        type: 'confirm',
                                        href: '/chase/detail/' + orderId,
                                        txt: '已完成付款'
                                    },
                                    {
                                        type: 'cancel',
                                        href: '/help/index/b1-f4',
                                        txt: '支付遇到问题'
                                    }
                                ],
                                cancelCb: function(){
                                    location.href = location.href;
                                }
                            });
                        }
                        else{
                            cx.Confirm({
                                single:'请在新打开页面完成付款，付款完成前请不要关闭此页面<br /><a href="/wallet/directPay?orderId='+ orderId +'">选择其他支付方式</a>',
                                btns:[
                                    {
                                        type: 'confirm',
                                        href: '/orders/detail/' + orderId,
                                        txt: '已完成付款'
                                    },
                                    {
                                        type: 'cancel',
                                        href: '/help/index/b1-f4',
                                        txt: '支付遇到问题'
                                    }
                                ],
                                cancelCb: function(){
                                    location.href = location.href;
                                }
                            });
                        }
                    }
                }
            });   
        }
    });

});
</script>