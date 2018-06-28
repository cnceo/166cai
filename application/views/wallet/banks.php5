
                    <div class="tab-small-cont">
                        <div class="bank_list small-item" style="display:block;">
                            <ul class="clearfix">
                                <li class="selected" data-val='directPay'><img alt="支付宝帐号支付" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/alipay-account.png');?>"><i class="s_yes"></i></li>
                                <li data-val='tenpay'><img alt="财付通支付" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/alipay-code.png');?>"><i class="s_yes"></i></li>
                            </ul>
                        </div>
                        <div class="bank_list small-item">
                            <ul class="clearfix banks">
                                <li class="selected" data-val='1025'><img alt="中国工商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='105'><img alt="中国建设银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/jsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='301'><img alt="交通银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/jtyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='312'><img alt="中国光大银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gdyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3051'><img alt="中国民生银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/msyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3112'><img alt="华夏银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/hxyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='106'><img alt="中国银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zgyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='309'><img alt="兴业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/xyyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3080'><img alt="招商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zsyh.png');?>"><i class="s_yes"></i></li>
                            </ul>
                        </div>
                        <div class="bank_list small-item">
                            <ul class="clearfix banks">
                                <li class="selected" data-val='1027'><img alt="中国工商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='1054'><img alt="中国建设银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/jsyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='301'><img alt="交通银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/jtyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3121'><img alt="中国光大银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/gdyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3051'><img alt="中国民生银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/msyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='3112'><img alt="华夏银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/hxyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='106'><img alt="中国银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zgyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='309'><img alt="兴业银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/xyyh.png');?>"><i class="s_yes"></i></li>
                                <li data-val='308'><img alt="招商银行" src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/bank/zsyh.png');?>"><i class="s_yes"></i></li>
                            </ul>
                        </div>
                    </div>
    

<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
<script>
	$(function(){
		//Tab切换
		$('.tab-nav li').click(function(){
            var idx = $(this).index();
            $(this).addClass('active').siblings('li').removeClass('active');
            $(".tab-content .tab-item").hide();
            $('.tab-content .tab-item').eq(idx).show();
            $('#bank').val(($('.tab-content .tab-item').eq(idx).find('.selected').data('val')));
            if($('.tab-content .tab-item').eq(idx).hasClass('default_tab'))
            {
            	$('#pay_type').val('alipay');
            }
            else{
            	$('#pay_type').val('chinabank');
            }
        })
		// 选中交互
        $('.money_list li,.bank_list li,.type_list li').click(function(){
            if($(this).parent().parent().hasClass('bank_list')){
                if($(this).parent().hasClass('banks'))
                {
					$('#pay_type').val('chinabank');
                }
                else
                {
                    if($(this).data('val') == 'tenpay')
                    {
                    	$('#pay_type').val('tenpay');
                    }
                    else{
                    	$('#pay_type').val('alipay');
                    }
                }
            	$('#bank').val($(this).data('val'));
            }else{
            	$('#total_fee').val($(this).data('val'));
            }
            $(this).addClass('selected').siblings().removeClass('selected');
            $('#other_money').val('');
            $('#other_money').closest('div').find('.form_tips').removeClass('form_tips_error').removeClass('form_tips_ok');
            $('#other_money').closest('div').find('.tip').show();
        })

        

        $('#other_money').focus(function(){
            // 清理固定金额
            $('.type_list li').removeClass('selected');
            var $wrap = $(this).closest('div')
            $wrap.find('.form_tips').removeClass('form_tips_error').removeClass('form_tips_ok')
            $wrap.find('.tip').show();
            $('#total_fee').val(0);
        });
		$('#other_money').blur(function(){
			var val = $(this).val();
            var $wrap = $(this).closest('div')
			if( /^\d+$/.test(val) ) {
                if( parseInt(val) > 10 ) {
                    $('#total_fee').val($(this).val());
                    $wrap.find('.form_tips').removeClass('form_tips_error').addClass('form_tips_ok')
                    $wrap.find('.tip').hide();
                }
                else {
                    // 提示: 填大于10的整数
                    $wrap.find('.form_tips').addClass('form_tips_error');
    				$wrap.find('.tip').show().html('至少充值10元');
                }
            } else {
                // 提示: 填大于10的整数
                $wrap.find('.form_tips').addClass('form_tips_error');
                $wrap.find('.tip').show().html('请输入10元以上的整数');
            }
		});

        $('.not-bind').on('click', showBind );

     	// 收银台交互
        if(!-[1,]&&!window.XMLHttpRequest){
            $(".balance").hover(function(){
                $(this).addClass('balance-hover');
            },function(){
                $(this).removeClass('balance-hover');
            })
        }
        
        $("#checkbox-balance").click(function(){
            var total_money, balance, need_recharge;
            if($(this).prop("checked")){
                $(this).parents(".balance").addClass('balance-selected');
                $("#payPwd").show();
                $("#payPwd input[type='password']").addClass('vcontent');
                balance = $('#remain_money').data('balance');
                total_money = $('#total_money').data('totalmoney');
                $('#show_remain_money').html( balance );
                
                need_recharge = total_money - balance;
                $('#need_recharge').html(need_recharge);
                $('#total_fee').val( need_recharge ); 
            }else{
                $(this).parents(".balance").removeClass('balance-selected');
                $("#payPwd").hide();
                $("#payPwd input[type='password']").removeClass('vcontent').val('');
                total_money = $('#total_money').data('totalmoney');
                $('#total_fee').val( total_money ); 
                $('#show_remain_money').html( 0 );
                $('#need_recharge').html(total_money);
            }
        })
        $('.other_bank').click(function(){
            $(this).parent(".line").siblings('.other_bank_detail').toggle();
        })

		new cx.vform('.recharge-form', {
            renderTip: 'renderTips',
	        submit: function(data) {
	            var self = this;

	            $.ajax({
	                type: 'post',
	                url:  '/wallet/recharge',
	                data: data,
	                success: function(response) {
	                	//alert(response);return;
                        if( response == 2 ){
                            cx.Alert({content:'支付密码错误'});
                            return false;
                        } else if(response) {
	                		if($('#pay_type').val() == 'tenpay')
	                		{
	                			$('#rchg_form').attr('action', 'https://pay.2345.com/tenpay.php');
			                }
	                		else
	                		{
	                			$('#rchg_form').attr('action', 'http://pay.2345.com/doPay.php');
		                	}
		                	$('#token').val(response);
		                	$('#rchg_form').submit();

                            cx.Confirm({
                                single:'请完成充值',
                                btns:[
                                    {
                                        type: 'confirm',
                                        href: '/mylottery/recharge',
                                        txt: '已完成充值'
                                    },
                                    {
                                        type: 'cancel',
                                        href: 'javascript:;',
                                        txt: '取消'
                                    }
                                ]
                            });
	                	}
	                }
	            });
	        }
	    });
	});
</script>
