<?php $this->load->view('elements/user/menu');?>
<script type='text/javascript' src='<?php echo getStaticFile('/caipiaoimg/v1.0/js/vform.js');?>'></script>
<div class="article">
    <div class="tit-b">
        <h2>充值</h2>
        <p class="tip cOrange" style="font-size: 16px; color: #ff0000;">因网站彩票委托投注业务暂停，充值通道暂时关闭，给您带来不便敬请谅解。</p>
    </div>
    <div class="tab-content recharge-content">
        <div class="tab-item" style="display: block;">
            <div class="tab-nav-small">
                <ul class="clearfix">
                    <?php $this->load->view("wallet/platform_tab"); ?>
                </ul>
            </div>
            <div class="tab-small-cont">
                <div class="platform_pay recharge-form small-item" style="display: block;">
                    <?php $this->load->view('wallet/platform_pay'); ?>
                </div>
                <div class="platform_bank recharge-form small-item" style="display: none;">
                    <?php $this->load->view('wallet/platform_bank'); ?>
                </div>
                <div class="platform_credit recharge-form small-item" style="display: none;">
                    <?php $this->load->view('wallet/platform_credit'); ?>
                </div>
                <div class="platform_phone recharge-form small-item" style="display: none;">
                    <?php $this->load->view('wallet/platform_phone'); ?>
                </div>
            </div>
        </div>
        <div class="tab-item" style="display: none;">
        </div>
        <div class="warm-tip">
            <h3>温馨提示：</h3>
            <p>1、如果您添加了预付款，银行账户钱扣了，2345彩票账户还没有加上，请及时与我们联系，我们将第一时间为您处理！</p>
            <p>2、为防止恶意提款、洗钱等不法行为，每笔充值资金的15%须用于实际消费；</p>
            <p>3、客服电话：400-000-2345 转8 彩票业务，您也可以联系我们的<a target="_blank" href="http://wpa.qq.com/msgrd?v=3&uin=2584565084&site=qq&menu=yes">在线客服</a> 。</p>
        </div>
    </div>
</div>

<script>

	$(function(){

        /* ----- 支付平台 -----*/
		// 钱数
        $('.platform_pay .type_list li').click(function(e){
            var $this = $(this);
            $('.platform_pay .ipt_fee').val( $this.data('val') );
            $this.addClass('selected').siblings().removeClass('selected');
            $('.platform_pay .other_money').val('');
            $('.platform_pay .other_money').closest('div').find('.form_tips').removeClass('form_tips_error').removeClass('form_tips_ok');
            $('.platform_pay .other_money').closest('div').find('.tip').show();
        });

        // 其他数额调整
        $('.platform_pay .other_money').focus(function(){
            // 清理固定金额
            $('.platform_pay .type_list li').removeClass('selected');
            $('.platform_pay .ipt_fee').val(0);
        });

		$('.platform_pay .other_money').blur(function(){
			var val = $(this).val();
			if( /^\d+$/.test(val) ) {
                if( parseInt(val) >= 1 ) {
                    $('.platform_pay .ipt_fee').val( val );
                }
                else {
                    //cx.Alert({content:'请输入1元以上整数'});
                }
            } else {
                $('.platform_pay .ipt_fee').val( 0 );
                //cx.Alert({content:'请输入1元以上整数'});
            }
		});

        // 银行
        $('.platform_pay .bank_list li').click(function(){
            var pay_type = $(this).data('val');
            var action = '';
            if( pay_type == 'directPay' ){
                $('.platform_pay .ipt_pay_type').val( 'alipay' );
                $('.platform_pay .ipt_bank').val( pay_type );
                action = 'http://pay.2345.com/doPay.php';
            } else if( pay_type == 'tenpay' ){
                $('.platform_pay .ipt_pay_type').val( 'tenpay' );
                action = 'https://pay.2345.com/tenpay.php';
            } else if( pay_type == 'shengpay' ){
                $('.platform_pay .ipt_pay_type').val( 'shengpay' );
                action = 'http://pay.2345.com/shengPay.php';
            }
            $('.platform_pay form.rchg_form').attr('action', action);
            
            $(this).addClass('selected').siblings().removeClass('selected');
        });

        /* ----- 网上银行 -----*/
		// 钱数
        $('.platform_bank .type_list li').click(function(e){
            var $this = $(this);
            $('.platform_bank .ipt_fee').val( $this.data('val') );
            $this.addClass('selected').siblings().removeClass('selected');
            $('.platform_bank .other_money').val('');
            $('.platform_bank .other_money').closest('div').find('.form_tips').removeClass('form_tips_error').removeClass('form_tips_ok');
            $('.platform_bank .other_money').closest('div').find('.tip').show();
        });

        // 其他数额调整
        $('.platform_bank .other_money').focus(function(){
            // 清理固定金额
            $('.platform_bank .type_list li').removeClass('selected');
            $('.platform_bank .ipt_fee').val(0);
        });

        $('.platform_bank .other_money').blur(function(){
			var val = $(this).val();
			if( /^\d+$/.test(val) ) {
                if( parseInt(val) >= 1 ) {
                    
                    $('.platform_bank .ipt_fee').val( val );
                }
                else {
                    //cx.Alert({content:'请输入1元以上整数'});
                }
            } else {
                $('.platform_bank .ipt_fee').val( 0 );
                //cx.Alert({content:'请输入1元以上整数'});
            }
		});

        // 银行
        $('.platform_bank .bank_list li').click(function(){
            var $this = $(this);
            $this.closest('.bank_list').find('li').removeClass('selected');
            $this.addClass('selected');
            $('.platform_bank .ipt_bank').val( $this.data('val') );
        });


        /* ----- 信用卡 -----*/
		// 钱数
        $('.platform_credit .type_list li').click(function(e){
            var $this = $(this);
            $('.platform_credit .ipt_fee').val( $this.data('val') );
            $this.addClass('selected').siblings().removeClass('selected');
            $('.platform_credit .other_money').val('');
            $('.platform_credit .other_money').closest('div').find('.form_tips').removeClass('form_tips_error').removeClass('form_tips_ok');
            $('.platform_credit .other_money').closest('div').find('.tip').show();
        });

        // 其他数额调整
        $('.platform_credit .other_money').focus(function(){
            // 清理固定金额
            $('.platform_credit .type_list li').removeClass('selected');
            $('.platform_credit .ipt_fee').val(0);
        });

		$('.platform_credit .other_money').blur(function(){
			var val = $(this).val();
			if( /^\d+$/.test(val) ) {
                if( parseInt(val) >= 1 ) {
                    $('.platform_credit .ipt_fee').val( val );
                }
                else {
                    //cx.Alert({content:'请输入1元以上整数'});
                }
            } else {
                $('.platform_credit .ipt_fee').val( 0 );
                //cx.Alert({content:'请输入1元以上整数'});
            }
		});

        // 银行
        $('.platform_credit .bank_list li').click(function(){
            var $this = $(this);
            $this.closest('.bank_list').find('li').removeClass('selected');
            $this.addClass('selected');
            $('.platform_credit .ipt_bank').val( $this.data('val') );
        });

        /* ----- 手机充值卡 -----*/
		// 钱数
        $('.platform_phone .type_list li').click(function(e){
            var $this = $(this);
            var total_fee = $this.data('val');
            var real, fee;
            $('.platform_phone .ipt_fee').val( total_fee );
            // 服务费, 实到额
            switch ( parseInt(total_fee, 10) )
            {
                case 10: fee = '0.40'; real = '9.60'; break;
                case 20: fee = '0.80'; real = '19.20'; break;
                case 30: fee = '1.20'; real = '28.80'; break;
                case 50: fee = '2.00'; real = '48.00'; break;
                case 100: fee = '4.00'; real = '96.00'; break;
            }
            $('.platform_phone .money').html( real );
            $('.platform_phone .fee').html( fee );
            $this.addClass('selected').siblings().removeClass('selected');
        });

        // 卡种
        $('.platform_phone .bank_list li').click(function(){
            var $this = $(this);
            $this.closest('.bank_list').find('li').removeClass('selected');
            $this.addClass('selected');
            $('.platform_phone .ipt_pay_type').val( $this.data('val') );
        });

        $('.not-bind').on('click', showBind );

		new cx.vform('.platform_pay', {
            renderTip: 'renderTips',
	        submit: function(data) {
	            var self = this;
                if( data.total_fee < 10 ) {
                    cx.Alert({content:'请输入10元以上整数'});
                    return false;
                }
                jumpBank( self, data, 'recharge_pay');
	        }
	    });

		new cx.vform('.platform_bank', {
            renderTip: 'renderTips',
	        submit: function(data) {
	            var self = this;
                if( data.OrderAmount < 10 ) {
                    cx.Alert({content:'请输入10元以上整数'});
                    return false;
                }
                jumpBank( self, data, 'recharge_bank' );
	        }
	    });

		new cx.vform('.platform_credit', {
            renderTip: 'renderTips',
	        submit: function(data) {
	            var self = this;
                if( data.OrderAmount < 10 ) {
                    cx.Alert({content:'请输入10元以上整数'});
                    return false;
                }
                jumpBank( self, data, 'recharge_credit' );
	        }
	    });

		new cx.vform('.platform_phone', {
            renderTip: 'renderTips',
	        submit: function(data) {
	            var self = this;
                if( !data.CardNo ){
                    cx.Alert({content:'请输入充值卡序列号'});
                    return false;
                }
                if( !data.CardPwd ){
                    cx.Alert({content:'请输入充值卡密码'});
                    return false;
                }
                
                jumpBank( self, data, 'recharge_phone' );
	        }
	    });

        function jumpBank( ctx, data, frame ){
            console.log(data);
            var blankFrame = window.open('about:blank',frame);

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

                        ctx.$form.find('input[name="token"]').val(response);
                        ctx.$form.find('.rchg_form').submit();
                        cx.Confirm({
                            single:'请在新打开页面完成付款，付款完成前请不要关闭此页面<br /><a href="/wallet/recharge">选择其他支付方式</a>',
                            btns:[
                                {
                                    type: 'confirm',
                                    href: '/mylottery/recharge',
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
            });        
        }
	});
</script>

<?php $this->load->view('elements/user/menu_tail');?>