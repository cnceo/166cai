<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/withdraw-money.min.css');?>">
</head>
<body>
    <?php $this->load->view('mobileview/paybank/list');?>
	    <a href="<?php echo $this->config->item('pages_url').$platform."/paybank/add/".$token;?>" id="addcard" class="btn-plain">添加新的银行卡</a>
	</div>
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
		<input type='hidden' class='' name='refer' value=''/>
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
                        	try{// 点击事件
                                android.umengStatistic('webview_umpay_setdefaultcard');
                            }catch(e){}
                        	$.post('/app/paybank/setDefault', {bid:$(this).attr('id')}, function(response){
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
                    
                    $('#addcard').click(function(){
                    	try{// 点击事件
                            android.umengStatistic('webview_umpay_addcard');
                        }catch(e){}
                    })
                    
                    $('.bankcard-box').click(function(){
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
                        $('#doPayForm input[name="refer"]').val('<?php echo $params['refer']?>');
                        $('#doPayForm input[name="bank_id"]').val($(this).attr('bank_id'));
                        
                        $('#doPayForm').submit();
                    })

                    $('body').on('click', '#unBind .btn-pop-confirm', function (e) {
                    	
                        popEl.remove();
                        try{// 点击事件
                            android.umengStatistic('webview_umpay_unbindcard');
                        }catch(e){}
                        $.post(
                                '/app/paybank/delBank',
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