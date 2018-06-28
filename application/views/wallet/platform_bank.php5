<?php
    $pay_type = 'shengpaybank';
?>
<form target="recharge_bank" <?php if($pay_type == 'shengpaybank'):?> action='http://pay.2345.com/shengPay.php' <?php else:?> action='http://pay.2345.com/doPay.php' <?php endif;?> method='post' class='form rchg_form' accept-charset="GB2312" >
	<input type='hidden' class='vcontent' name='mid' value='CP'>
	<input type='hidden' name='token' value=''>		
	<?php if($pay_type == 'shengpaybank'):?>
		<!-- 盛付通网银 -->
		<input type='hidden' class='vcontent' name='mode' value='PayType'>
        <input type='hidden' class='vcontent' name='OrderNo' value='<?php echo $trade_no;?>'>
        <input type='hidden' class='vcontent' name='PayChannel' value='19'>
        <input type='hidden' class="vcontent" name='BuyerIp' value='<?php echo UCIP;?>'>
        <input type='hidden' class='vcontent' name='OrderTime' value='<?php echo date('YmdHis', $dateline ); ?>'> 
        <input type='hidden' class="vcontent" name='ProductName' value='2345CP'>
        <input type='hidden' class='vcontent ipt_pay_type' name='PayType' value='shengpaybank'>
        <input type='hidden' class='vcontent ipt_fee' name='OrderAmount' value='10'>
        <input type='hidden' class='vcontent ipt_bank' name='InstCode' value='ICBC'>
	<?php else:?>
		<input type='hidden' class='vcontent' name='mode' value='pay_type'>
		<input type='hidden' class='vcontent ipt_pay_type' name='pay_type' value='chinabank'>
		<input type='hidden' class='vcontent' name='trade_no' value='<?php echo $trade_no;?>'>
		<input type='hidden' class='vcontent' name='dateline' value='<?php echo $dateline;?>'>
		<input type='hidden' name='subject' value='购买彩票'>
        <input type='hidden' name='body' value='购买彩票'>
        <input type='hidden' class='vcontent ipt_bank' name='bank' value='1025'>
        <input type='hidden' class='vcontent ipt_fee' name='total_fee' value='10'>
    <?php endif;?>
    <div class="form-item">
        <label class="form-item-label">充值方式</label>
        <div class="form-item-con">
            <div class="bank_list">
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
            <div class="line"><a href="javascript:;" class="other_bank">更多银行<i></i></a></div>
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