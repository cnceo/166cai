<!-- <form target="recharge_pay" action='http://pay.2345.com/doPay.php' method='post' class="form rchg_form" accept-charset="GB2312" > -->
<form target="recharge_pay" action='https://pay.2345.com/tenpay.php' method='post' class="form rchg_form" accept-charset="GB2312" >
	<input type='hidden' class='vcontent' name='mode' value='pay_type'>
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

    <input type='hidden' class='vcontent ipt_bank' name='bank' value='directPay'> <!-- 支付宝 -->
    <input type='hidden' class='vcontent ipt_fee' name='total_fee' value='10'> <!-- 支付宝, 财付通PC -->
    <input type='hidden' class='vcontent ipt_fee' name='OrderAmount' value='10'> <!-- 盛付通PC -->

    <input type='hidden' name='token' value=''>
    <div class="form-item">
        <label class="form-item-label">充值方式</label>
        <div class="form-item-con">
            <div class="bank_list" style="display:block;">
                <ul class="clearfix">
                    <!-- <li class="" data-val='directPay'><img alt="支付宝帐号支付" src="/caipiaoimg/v1.0/img/bank/alipay-account.png"><i class="s_yes"></i></li> -->
                    <li class="selected" data-val='tenpay'><img alt="财付通帐号支付" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/tenpay-account.png');?>"><i class="s_yes"></i></li>
                    <li class="" data-val='shengpay'><img alt="盛付通帐号支付" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/sft-account.png');?>"><i class="s_yes"></i></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="form-item">
        <label class="form-item-label">充值金额</label>
        <div class="form-item-con">
            <div class="type_list">
                <ul class="clearfix">
                    <li class="selected" data-val='10' >10元<i class="s_yes"></i></li>
                    <li data-val='50' >50元<i class="s_yes"></i></li>
                    <li data-val='100' >100元<i class="s_yes"></i></li>
                    <li data-val='200' >200元<i class="s_yes"></i></li>
                    <li data-val='500' >500元<i class="s_yes"></i></li>
                    <li data-val='1000' >1000元<i class="s_yes"></i></li>
                    <li data-val='2000' >2000元<i class="s_yes"></i></li>
                    <li data-val='3000' >3000元<i class="s_yes"></i></li>
                    <li data-val='4000' >4000元<i class="s_yes"></i></li>
                    <li data-val='5000' >5000元<i class="s_yes"></i></li>
                    <li data-val='10000' >10000元<i class="s_yes"></i></li>
                    <li data-val='15000' >15000元<i class="s_yes"></i></li>
                </ul>
            </div>   
        </div>
    </div>
    <div class="form-item">
        <label class="form-item-label">其他金额</label>
        <div class="form-item-con">
            <input type="text" style="display:none" value="此处的input删掉然后回车按钮就会触发提交" />
            <input type="text" class="form-item-ipt ipt-money placeholder other_money" c-placeholder="请输入10元以上的整数" value="" name="">元
        </div>
    </div>
    <div class="form-item btn-group">
        <div class="form-item-con">
            <a href="javascript:;" class="btn btn-confirm submit<?php echo $showBind ? ' not-bind': '';?>">下一步</a>
            <!-- <a href="javascript:;" class="btn btn-confirm btn-disabled<?php echo $showBind ? ' not-bind': '';?>">下一步</a> -->
        </div>
    </div>
</form>