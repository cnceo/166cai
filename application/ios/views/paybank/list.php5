<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/withdraw-money.min.css');?>">
</head>
<body>
    <?php $this->load->view('mobileview/paybank/list');?>
	    <a href="javascript:void(0);" onClick="window.location.href='<?php echo $this->config->item('pages_url').$platform."/paybank/add/".$token."?sign=".$sign;?>'" class="btn-plain">添加新的银行卡</a>
	</div>
	<form id="doPayForm" action="/ios/paybank/cardlist/<?php echo $token?>?sign=<?php echo $sign?>" method="post">
		<input type='hidden' class='' name='bank_id' value=''/>
	</form>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>"></script>
    <script>
        require(['//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/config.js'], function() {
            require(['Zepto', 'basic', 'ui/loading/src/loadingfix', 'ui/tips/src/tipsfix'], function($, basic, loading) {
                var delbankid = '0';

                !function () {
                    var popEl = $('<div class="ui-popup ui-confirm" id="unBind"><div class="ui-popup-inner"><div class="ui-popup-bd"><span>确认解除本卡的快捷支付功能？</span></div><div class="ui-popup-ft"><a href="javascript:;" class="btn-pop-cancel">取消</a><a href="javascript:;" class="btn-pop-confirm">确定</a></div></div></div><div class="mask"></div></div>')
                    $('.bankcard-edit').on('click', '.unbind', function (e) {
                    	delbankid = $(this).attr('bankid').toString();
                        $('body').append(popEl);
                        e.stopPropagation();
                    }).on('click', 'input, label', function(e){
                        if(e.target.nodeName !== 'LABEL') {
                        	$.post('/ios/paybank/setDefault/<?php echo $token?>', {bid:$(this).attr('id')}, function(response){
                                if (response == 'fail') {
                                	$.tips({
                                        content:'设置错误，请返回重新操作',
                                        stayTime:2000
                                    })
                                }
                            });
                        }
                        e.stopPropagation();
                    })
                    
                    $('.bankcard-box').click(function(){
                        $('#doPayForm input[name="bank_id"]').val($(this).attr('bank_id'));
                    	$('#doPayForm').submit();
                    })

                    $('body').on('click', '#unBind .btn-pop-confirm', function (e) {
                        popEl.remove();
                        $.post(
                                '/ios/paybank/delBank/<?php echo $token?>',
                                {bankid:delbankid},
                                function(response){
                                    if (response.status == true) {
                                    	$.tips({
                                            content:'解绑成功',
                                            stayTime:2000
                                        }).on("tips:hide",function(){
                                        	if ($('.bankcard-box').length == 1) {
                                        		window.location.href = '<?php echo $params['refer']?>';
                                            }else {
                                            	location.reload();
                                            }
                                        });
                                    }
                                },'json'
                       	)
 
                    })
                    $('body').on('click', '#unBind .btn-pop-cancel', function () {
                        popEl.remove();
                    })

                    
                }()
            })
        })
    </script>
</body>
</html>