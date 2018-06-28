<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/pay.min.css');?>">
<div class="wrap_in pay-container recharge-form">
    <form target="_blank" action='/wallet/requestPay' method='post' id='rchg_form' name='rchg_form'>

        <input type='hidden' class='vcontent ipt_fee' name='p3_Amt' value='<?php echo ParseUnit($data['money'], 1);?>'> 
        <input type='hidden' class='vcontent' name='pd_FrpId' value=''> 
        <input type='hidden' class='vcontent' name='orderId' value='<?php echo $orderId;?>'>
        <input type='hidden' class='vcontent' name='mode' value='wftWx'>
        <input type='hidden' class='vcontent' name='orderType' value='<?php echo $orderType;?>'>
    	<input type='hidden' class='vcontent' name='pay_type' value=''>
    	<input type='hidden' class='vcontent' name='cardNo' value=''>
    	<input type='hidden' class='vcontent' name='no_agree' value=''>
        <input type='hidden' class='vcontent' name='redpack' value=''>

        <div class="product-info">
            <?php $datetime = strtotime($data['created']);?>
            <h2 class="tit">商品信息：<?php echo BetCnName::getCnName($data['lid']);?><?php if($orderType!=5){ ?>第<?php echo $data['issue'];?>期<?php } ?></h2>
            <p class="buy-time">购买时间：<?php echo date('Y', $datetime)."年".date('m', $datetime)."月".date('d', $datetime)."日 ".date('H:i:s', $datetime);?></p>
            <p class="order-num">订单编号：<?php echo $data['orderId'];?></p>
            <span class="total-money" id="total_money" data-totalMoney='<?php echo ParseUnit($data['money'], 1);?>' >总金额：<b><?php echo ParseUnit($data['money'], 1);?></b>元</span>
        </div>

        <div class="pay-method-cont">
            <div class="balance">
                <div class="balance-min"><label for="checkbox-balance"><input type="checkbox" class="ipt_checkbox vcontent" name="checkbox-balance" id="checkbox-balance" checked>余额支付</label>
                账户<strong class="account"><?php echo $this->uname;?></strong>余额<b class="money" id="remain_money" data-balance='<?php echo ParseUnit($money, 1);?>'><?php echo ParseUnit($money, 1);?></b>元
                </div>
                <span class="pay-nm">支付<b class="money" id="show_remain_money">0</b>元</span>
            </div>
            <div class="pay-form ">
                <span class="pay-nm">支付<b class="money recharge_money" id="need_recharge"><?php echo ParseUnit($data['money'], 1);?></b>元</span>
                <div class="item">
                    <label class="label_like">其他支付方式</label>
                    <div class="tab-nav">
                        <ul class="clearfix">
                        	<!-- <li class="" data-val="llpayKuaij"><a href="javascript:;"><span>银行卡快捷支付</span><i>无需开通网银</i></a></li> -->
                            <li class="active" data-val='wftWx'><a href="javascript:;"><span>微信支付</span></a></li>
                            <li class="" data-val="payZfb"><a href="javascript:;"><span>支付宝支付</span></a></li>
                            <li class="" data-val='yeepayKuaij'><a href="javascript:;"><span>快捷支付</span></a></li>
                            <li class="" data-val='yeepayWangy'><a href="javascript:;"><span>网上银行</span></a></li>
                            <li class="" data-val='yeepayCredit'><a href="javascript:;"><span>信用卡</span></a></li>
                        </ul>
                    </div>
                    <div class="tab-content">
                    	<!-- <div class="tab-item platform_quick" id="platform_quick" style="display: block;">
                            <p class="form-tips-bar">提示：借记卡和信用卡均可充值，无须开通网银！</p>
                                <div class="form-item" id="inputCard" <?php if($cardList):?>style="display:none;"<?php endif;?>>
								    <label class="form-item-label">银行卡号</label>
								    <div class="form-item-con">
									<input type="text" class="form-item-ipt j-bank-id" value="" name="quickCard">
									<div class="form-tip">
									    <i class="icon-tip"></i>
									    <span class="form-tip-con quickCard">请输入银行卡号</span>
									    <s></s>
									</div>
									<div class="mod-tips">
									    <span class="bubble-tip" tiptext="<p><strong>借记卡：</strong>支持中国银行、招行、农行、光大银行、华夏、平安、建行、邮政、兴业、中信、浦发、广发等38家银行</p>
										<p><strong>信用卡：</strong>支持中国银行、工行、农行等57家银行</p>">支持银行列表<i class="icon-font">&#xe613;</i></span>
									</div>
									<?php if($cardList):?>
									<div class="bank-selected-lnk">
									    <a href="javascript:;" id="backCardList">返回历史银行卡</a>
									</div>
									<?php endif;?>
								    </div>
								</div>
                                <div class="form-item" id="selectCard" <?php if(empty($cardList)):?>style="display:none;"<?php endif;?>>
									<?php if($cardList[0]):?>
								    <label class="form-item-label">银行卡号</label>
								    <div class="form-item-con">
								    <?php 
								    	$dataVal = $cardList[0]['no_agree'] . '|' . $cardList[0]['card_type'];
								    	$types = array('2' => '储蓄卡', '3' => '信用卡');
								    ?>
									<div class="bank-selected" data-val="<?php echo $dataVal;?>" id="selectedBank"><?php echo $cardList[0]['bank_name'];?> <?php echo $types[$cardList[0]['card_type']];?> 尾号<?php echo $cardList[0]['card_no'];?></div>
									<div class="bank-selected-lnk">
									    <a href="javascript:;" id="backInputCard">使用其它银行卡</a>
									    <s class="split-line">|</s>
									    <a href="javascript:;" id="bankManage">管理快捷银行卡</a>
									</div>
								    </div>
								    <?php endif;?>
								</div>
                        </div> -->
                    	<div class="tab-item" style="display: block;">
                    		<div class="bank_list">
                                <ul class="clearfix">
                                    <li class="selected ybzf"><img alt="微信支付" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/ybzf-weixin.png');?>"><i class="s_yes"></i></li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-item">
                    		<div class="bank_list">
                                <ul class="clearfix">
                                    <li class="selected ybzf"><img alt="支付宝支付" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/ybzf-zfb.png');?>"><i class="s_yes"></i></li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-item">
                            <div class="bank_list" id="platform_kuaijie">
                                <ul class="clearfix">
                                    <li class="selected ybzf" data-val="yeepayKuaij"><img alt="易宝支付支付" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/ybzf-kuaijie.jpg');?>"><i class="s_yes"></i></li>
                                    <li class="ybzf" data-val="sumpayWeb"><img alt="统统付支付" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/ttf-kuaijie.jpg');?>"><i class="s_yes"></i></li>
                                </ul>
                                <!-- <a href="javascript:;" class="bank_list-tip" id="bank-list-tip"><span>银行卡支付使用指南</span><i class="icon-font">&#xe60d;</i></a> -->
                            </div>
                        </div>
                        <div class="tab-item platform_bank">
	                        <div class="bank_list">
	                            <ul class="clearfix">
	                                <li data-val='ICBC-NET-B2C' class="selected"><img title="中国工商银行" alt="中国工商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gsyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='POST-NET-B2C'><img title="中国邮政储蓄银行" alt="中国邮政储蓄银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/yzcxyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='ABC-NET-B2C'><img title="中国农业银行" alt="中国农业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/nyyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='CMBCHINA-NET-B2C'><img title="招商银行" alt="招商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zsyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='CCB-NET-B2C'><img title="中国建设银行" alt="中国建设银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/jsyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='BOC-NET-B2C'><img title="中国银行" alt="中国银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zgyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='BOCO-NET-B2C'><img title="交通银行" alt="交通银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/jtyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='CMBC-NET-B2C'><img title="中国民生银行" alt="中国民生银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/msyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='CIB-NET-B2C'><img title="兴业银行" alt="兴业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/xyyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='CEB-NET-B2C'><img title="光大银行" alt="光大银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gdyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='HXB-NET-B2C'><img title="华夏银行" alt="华夏银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/hxyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='ECITIC-NET-B2C'><img title="中信银行" alt="中信银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zxyh.png');?>"><i class="s_yes"></i></li>
	                            </ul>
	                            <div class="line"><a href="javascript:void(0);" class="other_bank">更多银行<i></i></a></div>
	                            <ul style="display: none;" class="clearfix other_bank_detail">
	                                <li data-val='GDB-NET-B2C'><img title="广东发展银行" alt="广东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gfyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='SPDB-NET-B2C'><img title="上海浦东发展银行" alt="上海浦东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/shpdfzyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='BCCB-NET-B2C'><img title="北京银行" alt="北京银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/bjyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='SHB-NET-B2C'><img title="上海银行" alt="上海银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/shyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='PINGANBANK-NET-B2C'><img title="平安银行" alt="平安银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/payh.png');?>"><i class="s_yes"></i></li>
	                            </ul>
	                        </div>
                        </div>
                        <div class="tab-item platform_credit">
	                        <div class="bank_list">
	                            <ul class="clearfix">
	                                <li data-val='ICBC-NET-B2C' class="selected"><img title="工商银行" alt="工商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gsyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='CMBCHINA-NET-B2C'><img title="招商银行" alt="招商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zsyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='CCB-NET-B2C'><img title="中国建设银行" alt="中国建设银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/jsyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='BOC-NET-B2C'><img title="中国银行" alt="中国银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zgyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='CMBC-NET-B2C'><img title="中国民生银行" alt="中国民生银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/msyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='CEB-NET-B2C'><img title="光大银行" alt="光大银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gdyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='SHB-NET-B2C'><img title="上海银行" alt="上海银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/shyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='PINGANBANK-NET-B2C'><img title="平安银行" alt="平安银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/payh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='BOCO-NET-B2C'><img title="交通银行" alt="交通银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/jtyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='CIB-NET-B2C'><img title="兴业银行" alt="兴业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/xyyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='GDB-NET-B2C'><img title="广东发展银行" alt="广东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/gfyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='SPDB-NET-B2C'><img title="上海浦东发展银行" alt="上海浦东发展银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/shpdfzyh.png');?>"><i class="s_yes"></i></li>
	                                <li data-val='ECITIC-NET-B2C'><img title="中信银行" alt="中信银行" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/bank/zxyh.png');?>"><i class="s_yes"></i></li>
	                            </ul>
	                        </div>
                        </div>
                    </div>
                    <?php if(!empty($redpackData)):?>
                    <!-- 充值红包 start -->
                    <div class="hongbao-s" id="redpackInfo">
                    	<h3 class="hongbao-s-title">选择红包</h3>
                        <ul>
                            <?php foreach( $redpackData as $key => $items ): ?>
                            <li redpack-data="<?php $params = json_decode($items['use_params'], true); echo $items['id'] . '#' . ParseUnit($params['money_bar'], 1);?>" class="redpack<?php echo ParseUnit($params['money_bar'], 1);?>" id="redpackId-<?php echo $items['id']; ?>">
                                <?php echo ParseDesc($items['use_desc']);?><span><?php echo ParseEnd($items['valid_end']);?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div> 
                    <!-- 充值红包 end -->
                    <?php endif; ?>
                </div>
                <div class="item btn_area">
                    <a class="btn btn-main submit<?php echo $showBind ? ' not-bind': '';?>" href="javascript:void(0);">确认预约</a>
                    <p class="tips">如选择其他支付方式，确认预约会跳转到对应支付方式页面完成支付</p>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
$(function(){

    var orderId = '<?php echo $orderId;?>';
    var orderType = '<?php echo $orderType;?>';
    console.log(orderId)
    //有默认卡操作
	if($('#selectCard').find('.bank-selected').length > 0){
		var bankData = $('#selectCard').find('.bank-selected').attr('data-val').split("|");
		$('input[name="pd_FrpId"]').val('');
		$('input[name="no_agree"]').val(bankData[0]);
		$('input[name="pay_type"]').val(bankData[1]);
	}
    //Tab切换
    $('.tab-nav').on('click', 'li', function(){
    	$('input[name="mode"]').val($(this).data('val'));
    	$('input[name="pd_FrpId"]').val('');
    	$('input[name="pay_type"]').val('');
  	    $('input[name="cardNo"]').val('');
    	$('input[name="no_agree"]').val('');
    	$('input[name="quickCard"]').val('')
    	if($(this).data('val') == 'yeepayWangy'){
    		$('input[name="pd_FrpId"]').val($('.platform_bank ul').find('.selected').data('val'));
        }else if($(this).data('val') == 'yeepayCredit'){
        	$('input[name="pd_FrpId"]').val($('.platform_credit ul').find('.selected').data('val'));
        }else if($(this).data('val') == 'llpayKuaij'){
        	//有默认卡操作
    		if($('#selectCard').find('.bank-selected').length > 0){
    			var bankData = $('#selectCard').find('.bank-selected').attr('data-val').split("|");
    			$('input[name="pd_FrpId"]').val('');
    			$('input[name="no_agree"]').val(bankData[0]);
    			$('input[name="pay_type"]').val(bankData[1]);
    		}
        }
    });
    
    // 选择红包
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

    // 获取所选择红包信息
    function getRedpack(){
        
        var redpackInfo = [];
        if($('#redpackInfo li.selected').length > 0)
        {
            $('#redpackInfo li.selected').each(function(){
                redpackInfo.push($(this).attr('redpack-data'));
            });
        }    
        return redpackInfo;
    }
    
    $('.bank_list li').click(function(){
        var val = $(this).data('val');     
        $(this).closest('.bank_list').find('li').removeClass('selected');
        $(this).addClass('selected');
        $('input[name="pd_FrpId"]').val(val);

    });
    // 快捷支付切换
    $('#platform_kuaijie').on('click', 'li', function(){
        var $this = $(this);
        $this.closest('.bank_list').find('li').removeClass('selected');
        $this.addClass('selected');
        $('input[name="pd_FrpId"]').val('');
        $('input[name="mode"]').val( $this.data('val') );
    });
    
    // 直接付款
    $("#checkbox-balance").click(function(){
        var total_money, balance, need_recharge;
        if($(this).prop("checked")){
            $(this).parents(".balance").addClass('balance-selected');
            balance = $('#remain_money').data('balance');
            total_money = $('#total_money').data('totalmoney');
            $('#show_remain_money').html( balance );
            
            need_recharge = (total_money - balance).toFixed(2);
            selectRedpack(need_recharge);
            $('#need_recharge').html(need_recharge);
            $('.recharge-form .ipt_fee').val( need_recharge ); 
        }else{
            $(this).parents(".balance").removeClass('balance-selected');
            total_money = $('#total_money').data('totalmoney');
            $('.recharge-form .ipt_fee').val( total_money ); 
            selectRedpack(total_money);
            $('#show_remain_money').html( 0 );
            $('#need_recharge').html(total_money);
        }
    });
    if($("#checkbox-balance").prop("checked")){
    	var total_money, balance, need_recharge;
    	$(this).parents(".balance").addClass('balance-selected');
        balance = $('#remain_money').data('balance');
        total_money = $('#total_money').data('totalmoney');
        $('#show_remain_money').html( balance );
        
        need_recharge = (total_money - balance).toFixed(2);
        selectRedpack(need_recharge);
        $('#need_recharge').html(need_recharge);
        $('.recharge-form .ipt_fee').val( need_recharge ); 
    }

    new cx.vform('.recharge-form', {
        renderTip: 'renderTips',
        submit: function(data) {
            var self = this;
            <?php if($this->uinfo['userStatus'] == 2):?>
        		cx.Alert({content:'您的账户已被冻结，如需解冻请联系客服。'});
        		return false;
        	<?php endif;?>
            //连连卡前支付
            if(data.mode == 'llpayKuaij'){
	            if((data.pay_type == '' && data.cardNo == '') || (data.pay_type == '' && data.no_agree == '')) {
	            	if($('input[name="quickCard"]').val() == ''){
	            		$('.quickCard').closest('.form-tip').addClass('form-tip-error')
		            }
	            	if($('.quickCard').closest('.form-tip').hasClass('form-tip-error')){
		            }else{
		            	cx.Alert({content:'等待银行卡信息校验中···请稍后重试'});
			        }
		            return false;
		        }
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

            $('#rchg_form').submit();

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

    //选择红包
    function selectRedpack(money){
    	var selectPack = 0;
		var rule, redMoney;
        var selectedId = '';
        var selectedMoney = 0;
		money = parseFloat(money);
		$('#redpackInfo li').each(function(){
			rule = $(this).attr('redpack-data').split("#");
			redMoney = parseFloat(rule[1]);
			if((money>= redMoney) && (selectPack < redMoney) && (selectedMoney < redMoney)){
				selectedMoney = redMoney;
                selectedId = rule[0];
			}
        });
		// 清除红包信息
        $('input[name="redpack"]').val('');
        $('#redpackInfo li').removeClass('selected');
        $('#redpackId-' + selectedId).addClass('selected');
    }

    //返回银行卡历史
	$('#backCardList').click(function(){
		$('input[name="quickCard"]').val('');
		var val = $('#selectCard').find('.bank-selected').data('val').split("|");
		$('input[name="pd_FrpId"]').val('');
		$('input[name="cardNo"]').val('');
		$('input[name="no_agree"]').val(val[0]);
		$('input[name="pay_type"]').val(val[1]);
    	$('.quickCard').closest('.form-tip').removeClass('form-tip-error form-tip-true');
    	$('#selectCard').show();
    	$('#inputCard').hide();
    });
	//使用其它银行卡
    $('#backInputCard').click(function(){
    	$('input[name="quickCard"]').val('');
    	$('input[name="pd_FrpId"]').val('');
		$('input[name="no_agree"]').val('');
		$('input[name="pay_type"]').val('');
		$('.quickCard').html('请输入银行卡号');
    	$('.quickCard').closest('.form-tip').removeClass('form-tip-error form-tip-true');
    	$('#selectCard').hide();
    	$('#inputCard').show();
    });
    $('input[name="quickCard"]').focus(function(){
    	$('input[name="cardNo"]').val('');
		$('input[name="no_agree"]').val('');
		$('input[name="pay_type"]').val('');
    	$('.quickCard').html('请输入银行卡号');
    	$('.quickCard').closest('.form-tip').removeClass('form-tip-error form-tip-true');
    });
    //查询卡bin信息
    $('input[name="quickCard"]').blur(function(){
    	$('.quickCard').closest('.form-tip').removeClass('form-tip-error form-tip-true');
		var val = $(this).val().replace(/\s+/g, "");
		if( /^\d{15,19}$/.test(val) ) {
			$.ajax({
                type: 'post',
                url: '/wallet/getCardBin',
                data: {cardNo:val},
                dataType: 'json',
                success: function (response) {
                   if(response.ret_code == '0000'){
                	   $('input[name="pd_FrpId"]').val(response.bank_code);
                	   $('input[name="pay_type"]').val(response.card_type);
                	   $('input[name="cardNo"]').val(val);
                	   $('.quickCard').html(response.bank_name);
                	   $('.quickCard').closest('.form-tip').addClass('form_tips_ok');
	               }else{
	            	   $('.quickCard').html(response.ret_msg);
		               $('.quickCard').closest('.form-tip').addClass('form-tip-error');
		           }
                }
            });
        } else {
        	$('.quickCard').html('请输入正确的银行卡号');
        	$('.quickCard').closest('.form-tip').addClass('form-tip-error');
        }
	});

    $('#bankManage').click(function(){
    	var no_agree = $('input[name="no_agree"]').val();
    	$.ajax({
            type: 'post',
            url: '/wallet/getLlBankPop',
            data: {no_agree:no_agree},
            success: function (response) {
                $('body').append(response);
                cx.PopCom.show('.bankList');
                cx.PopCom.close('.bankList');
                cx.PopCom.cancel('.bankList');
            }
        });
    });
    // 银行卡输入卡号，逢4个数字中间插入2个空格
    $('.j-bank-id').on('keyup mouseout input',function(){
        var value=$(this).val().replace(/\s/g,'').replace(/(\d{4})(?=\d)/g,"$1  ");    
        $(this).val(value);
    });

    $('#platform_quick').on('mouseenter', '.bubble-tip', function(){
        $.bubble({
            target:this,
            position: 'b',
            align: 'l',
            content: $(this).attr('tiptext'),
            width:'320'
        })
    }).on('mouseleave', '.bubble-tip', function(){
        $('.bubble').hide();
    });

    // 查看tips
    $('#bank-list-tip').on('click', function (e) {
        var shopId = $(this).attr('shop-data');
        $.ajax({
            type: 'post',
            url: '/pop/getRechargeTip',
            data: {version:version},
            success: function (response) {
                $('body').append(response);
                cx.PopCom.show('.rechargeTip');
                cx.PopCom.close('.rechargeTip');
            }
        });
        e.stopImmediatePropagation();
        e.preventDefault();
    });
});
</script>