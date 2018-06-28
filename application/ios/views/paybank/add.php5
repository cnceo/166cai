<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/psw.min.css');?>">
</head>
<body ontouchstart="">
    <?php $this->load->view('mobileview/paybank/add'); ?>
    <form id="doPayForm" action="/ios/paybank/add/<?php echo $token?>?sign=<?php echo $sign?>" method="post">
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