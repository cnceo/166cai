<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/psw.min.css');?>">
</head>
<body ontouchstart="">
    <?php $this->load->view('mobileview/paybank/add'); ?>
     <!-- 表单提交 -->
	<form id="doPayForm" action="/app/wallet/doPayForm" method="post">
		<input type='hidden' class='' name='uid' value=''/>
		<input type='hidden' class='' name='trade_no' value=''/>
		<input type='hidden' class='' name='money' value=''/>
		<input type='hidden' class='' name='ip' value=''/>
		<input type='hidden' class='' name='real_name' value=''/>
		<input type='hidden' class='' name='id_card' value=''/>
		<input type='hidden' class='' name='merId' value=''/>
		<input type='hidden' class='' name='configId' value=''/>
		<input type='hidden' class='' name='pay_type' value=''/>
		<input type='hidden' class='' name='token' value=''/>
		<input type='hidden' class='' name='bank_id' value=''/>
	</form>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>"></script>
    <script>
        require(['//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/config.js'], function() {

            require(['Zepto', 'basic', 'ui/loading/src/loadingfix', 'ui/tips/src/tipsfix'], function($, basic, loading, tips) {

                !function () {
                	function checkCardId (context) {
                        if (/^\d{15,19}$/.test(context.val())) {
                            context.closest('.cp-form').find('.btn-confirm').prop('disabled', false);
                        } else {
                            context.closest('.cp-form').find('.btn-confirm').prop('disabled', true);
                        }
                    }

                	checkCardId($('#cardId'))
                    $('#cardId').focus();
                    $('#cardId').on('focus input', function(e) {
                        var val = $.trim($(this).val());
                        $(this).val(val);
                        if (!val) {
                            $(this).closest('.form-item').find('.clear-ipt').remove();
                            return;
                        }
                        if (val && !$(this).closest('.form-item').find('.clear-ipt').size()) {
                            $(this).closest('.form-item').append('<i class="clear-ipt"></i>');
                            $('.form-item').on('click', '.clear-ipt', function () {
                                $(this).closest('.form-item').find('input').val('').focus();
                                $(this).remove();
                            })
                        }
                        checkCardId($(this))
                    })

                    $('.cp-form').on('click', '.btn-confirm', function () {
                    	try{// 点击事件
                            android.umengStatistic('webview_umpay_addcardnext');
                        }catch(e){}
                        $('#doPayForm input[name="uid"]').val('<?php echo $this->uid?>');
                        $('#doPayForm input[name="trade_no"]').val('<?php echo $params['trade_no']?>');
                        $('#doPayForm input[name="money"]').val('<?php echo $params['money']?>');
                        $('#doPayForm input[name="ip"]').val('<?php echo UCIP?>');
                        $('#doPayForm input[name="real_name"]').val('<?php echo $params['real_name']?>');
                        $('#doPayForm input[name="id_card"]').val('<?php echo $params['id_card']?>');
                        $('#doPayForm input[name="merId"]').val('<?php echo $params['merId']?>');
                        $('#doPayForm input[name="configId"]').val('<?php echo $params['configId']?>');
                        $('#doPayForm input[name="pay_type"]').val('umPay');
                        $('#doPayForm input[name="token"]').val('<?php echo $params['token']?>');
                        $('#doPayForm input[name="bank_id"]').val($("#cardId").val());
                        $('#doPayForm').submit();
                    })
                }()
                $('.btn-block-confirm').on('tap', function() {

                    $.loading().loading("mask");

                    $.tips({
                        content: '网络异常，请稍后再试',
                        stayTime: 2000
                    })
                    return false;
                })

            })

        })
    </script>
</body>
</html>