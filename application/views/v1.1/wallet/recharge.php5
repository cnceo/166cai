<?php $this->load->view('v1.1/elements/user/menu');?>
<div class="l-frame-cnt">
<div class="uc-main">
    <div class="tit-b">
        <h2>充值</h2>
    </div>
    <div class="tab-content recharge-content">
        <div class="tab-item" style="display: block;">
        <form target="_blank" action="/wallet/recharge/requestPay" method='post' class="form recharge-form">
        	<input type='hidden' class='vcontent' name='mode' value='wftWx'>
        	<input type='hidden' class='vcontent ipt_fee' name='p3_Amt' value='20'> 
    		<input type='hidden' class='vcontent' name='pd_FrpId' value=''>
    		<input type='hidden' class='vcontent' name='pay_type' value=''>
    		<input type='hidden' class='vcontent' name='cardNo' value=''>
    		<input type='hidden' class='vcontent' name='no_agree' value=''>
    		<input type='hidden' class='vcontent' name='redpack' value=''> 
            <div class="form-item">
            	<div class="form-item-con">
                	<div class="tab-nav-small">
                    	<ul class="clearfix">
                            <li class="active" data-val="wftWx"><a href="javascript:;"><span>微信支付</span></a></li>
                            <li data-val="payZfb"><a href="javascript:;"><span>支付宝支付</span></a></li>
                    		<li class="" data-val="yeepayKuaij"><a href="javascript:;"><span>快捷支付</span></a></li>
                    		<!-- <li class="" data-val="llpayKuaij"><a href="javascript:;"><span>银行卡快捷支付</span><i>无需开通网银</i></a></li> -->
                            <li class="" data-val="yeepayWangy"><a href="javascript:;"><span>网上银行</span></a></li>
                            <li class="" data-val="yeepayCredit"><a href="javascript:;"><span>信用卡</span></a></li>
                          </ul>
                	</div>
            	</div>
           	</div>
           	<div class="tab-small-cont">
           	<!-- <?php $this->load->view('v1.1/wallet/platform_quick');?> -->
           	<?php $this->load->view('v1.1/wallet/platform_pay');?>
           	<?php $this->load->view('v1.1/wallet/platform_zfb');?>
            <?php $this->load->view('v1.1/wallet/platform_yeepay');?>
           	<?php $this->load->view('v1.1/wallet/platform_bank');?>
           	<?php $this->load->view('v1.1/wallet/platform_credit');?>
           	</div>
            <div class="form-item">
		        <label class="form-item-label">充值金额</label>
		        <div class="form-item-con">
		            <div class="type_list">
		                <ul class="clearfix">
		                    <li data-val='10' >10元<i class="s_yes"></i></li>
		                    <li class="selected" data-val='20' >20元<i class="s_yes"></i></li>
		                    <li data-val='50' >50元<i class="s_yes"></i></li>
		                    <li data-val='100' >100元<i class="s_yes"></i></li>
		                    <li data-val='200' >200元<i class="s_yes"></i></li>
		                    <li data-val='500' >500元<i class="s_yes"></i></li>
		                    <li data-val='1000' >1000元<i class="s_yes"></i></li>
		                    <li data-val='2000' >2000元<i class="s_yes"></i></li>
		                    <li data-val='3000' >3000元<i class="s_yes"></i></li>
		                    <!-- <li data-val='4000' >4000元<i class="s_yes"></i></li> -->
		                    <li data-val='5000' >5000元<i class="s_yes"></i></li>
		                    <!-- <li data-val='10000' >10000元<i class="s_yes"></i></li> -->
		                    <!-- <li data-val='15000' >15000元<i class="s_yes"></i></li> -->
		                </ul>
		            </div>   
		        </div>
		    </div>
		    <div class="form-item">
		        <label class="form-item-label">其他金额</label>
		        <div class="form-item-con">
		            <input type="text" style="display:none" value="此处的input删掉然后回车按钮就会触发提交" />
		            <input type="text" class="form-item-ipt ipt-money other_money" placeholder="请输入10元以上的整数" c-placeholder="请输入10元以上的整数" value="" name="">元
		        </div>
		    </div>
		    <?php if(!empty($redpackData)):?>
		    <!-- 充值红包 start -->
		    <div class="form-item">
		        <label class="form-item-label">选择红包</label>
		        <div class="form-item-con">
		            <div class="hongbao-s" id="redpackInfo">
		                <ul>
		                    <?php foreach( $redpackData as $key => $items ): ?>
		                    <li redpack-data="<?php $params = json_decode($items['use_params'], true); echo $items['id'] . '#' . ParseUnit($params['money_bar'], 1);?>" class="redpack<?php echo ParseUnit($params['money_bar'], 1);?>" id="redpackId-<?php echo $items['id']; ?>">
		                        <?php echo ParseDesc($items['use_desc']);?><span><?php echo ParseEnd($items['valid_end']);?></span>
		                    </li>
		                    <?php endforeach; ?>
		                </ul>
		            </div> 
		        </div>
		    </div>
		    <!-- 充值红包 end -->
		    <?php endif; ?>
		    <div class="form-item btn-group">
		        <div class="form-item-con">
		            <a href="javascript:;" class="btn btn-main submit<?php echo $showBind ? ' not-bind': '';?>">下一步</a>
		        </div>
		    </div>
        	</form>
        </div>
        <div class="tab-item" style="display: none;">
        </div>
        <div class="warm-tip">
            <h3>温馨提示：</h3>
            <p>1、如果您已完成支付，银行账户钱扣了，166彩票账户还没有加上，请及时与我们联系，我们将第一时间为您处理；</p>
            <p>2、为防止恶意提现、洗钱等不法行为，信用卡每笔充值资金100%须用于购彩，储蓄卡每笔充值资金的<?php echo $this->config->item('txed')?>%须用于购彩；</p>
            <p>3、充值并选择使用红包后，此笔充值金额与红包金额均不可提现 。</p>
        </div>
    </div>
</div>
</div>
<script>
	$(function(){
		//默认勾选红包
		var defaultMoney = $('input[name="p3_Amt"]').val();
		selectRedpack(defaultMoney);
		//连连快捷
       	//$.ajax({
        //	type: 'post',
        //    url: '/wallet/getquickView',
        //    success: function (response) {
        //    	$("#platform_quick").html(response);
        //    }
        //});
        //有默认卡操作
		if($('#selectCard').find('.bank-selected').length > 0){
			var bankData = $('#selectCard').find('.bank-selected').attr('data-val').split("|");
			$('input[name="pd_FrpId"]').val('');
			$('input[name="no_agree"]').val(bankData[0]);
			$('input[name="pay_type"]').val(bankData[1]);
		}
		//tab切换
        $('.tab-nav-small li').click(function(e){
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
                var quickHtml = $("#platform_quick").html();
                if(quickHtml == null || quickHtml == ''){
                	$.ajax({
    	                type: 'post',
    	                url: '/wallet/getquickView',
    	                success: function (response) {
    	                	$("#platform_quick").html(response);
    	                }
    	            });
                }
                //有默认卡操作
        		if($('#selectCard').find('.bank-selected').length > 0){
        			var bankData = $('#selectCard').find('.bank-selected').attr('data-val').split("|");
        			$('input[name="pd_FrpId"]').val('');
        			$('input[name="no_agree"]').val(bankData[0]);
        			$('input[name="pay_type"]').val(bankData[1]);
        		}
            }
        });
        // 选择银行
        $('.bank_list').on('click', 'li', function(){
            var $this = $(this);
            $this.closest('.bank_list').find('li').removeClass('selected');
            $this.addClass('selected');
            $('input[name="pd_FrpId"]').val( $this.data('val') );
        });
        // 快捷支付切换
        $('.platform_yeepay').on('click', 'li', function(){
            var $this = $(this);
            $this.closest('.bank_list').find('li').removeClass('selected');
            $this.addClass('selected');
            $('input[name="pd_FrpId"]').val('');
            $('input[name="mode"]').val( $this.data('val') );
        });
		// 充值金额选择
        $('.recharge-form .type_list').on('click', 'li', function(e){           
            var $this = $(this);
            selectRedpack($this.data('val'));
            $('.recharge-form .ipt_fee').val( $this.data('val') );
            $this.addClass('selected').siblings().removeClass('selected');
            $('.recharge-form .other_money').val('');
            $('.recharge-form .other_money').closest('div').find('.form_tips').removeClass('form_tips_error').removeClass('form_tips_ok');
            $('.recharge-form .other_money').closest('div').find('.tip').show();
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

        // 平台支付 - 选择指定红包
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
                if( data.p3_Amt < 10 ) {
                    cx.Alert({content:'请输入10元以上整数'});
                    return false;
                }
                jumpBank( self, data, 'recharge-form');
	        }
	    });

		$(".tab-nav-small ul").tabPlug({
	        cntSelect: '.tab-small-cont',
	        menuChildSel: 'li',
	        onStyle: 'active',
	        cntChildSel: '.small-item',
	        eventName: 'click'
	    });
        
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

<?php $this->load->view('v1.1/elements/user/menu_tail');?>