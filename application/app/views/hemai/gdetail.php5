<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/bet-history.min.css');?>">
</head>
<body>
    <div class="wrapper bet-detail bet-detail-gd bet-detail-<?php echo BetCnName::getEgName($orderInfo['lid']); ?>">
        <?php if($orderInfo['totalMargin']>0) { 
            $str = '<em>中奖'.number_format(ParseUnit($orderInfo['totalMargin'], 1), 2).'元</em>';  ?>
            <div class="bet-detail-hd bet-winning">
        <?php } ?>
        <?php if($orderInfo['totalMargin']==0) {
            if($orderInfo['my_status']==1){
                $str = '未中奖';
                echo '<div class="bet-detail-hd bet-failed">';
            }else{
                $str = '静待大奖';
                echo '<div class="bet-detail-hd">';
            }
        } ?>      
            <div class="lottery-info">
                <h1 class="lottery-info-name"><?php echo BetCnName::getCnName($orderInfo['lid'])?></h1>
                <p><?php echo $str; ?></p>
                <div class="zh-info"><span><?php echo $orderStatus; ?></span>已跟<em><?php echo $orderInfo['followTimes']; ?></em>次，<?php if($orderInfo['status']==3){ echo "取消".($orderInfo['followTotalTimes']-$orderInfo['followTimes']).'次／'; }?>共<?php echo $orderInfo['followTotalTimes']; ?>次</div>
                <?php if ($orderInfo['status'] == 1) {?>
                <a href="javascript:;" class="btn btn-mini btn-plain btn-stop-gd" id="cancelConfirm">停止跟单</a>
                <?php } ?>
            </div>
        </div>
        <div class="bet-detail-bd cp-box zh-box">
            <div class="cp-box-hd">
                <table class="table-info">
                    <tbody>
                        <tr>
                            <th class="tar">发起人</th>
                            <td><?php echo $uname; ?></td>
                        </tr>
                        <tr>
                            <th>扣款方式</th>
                            <td><?php echo $orderInfo['payType']==0?'预付扣款（预付总额：'.($orderInfo['totalMoney']/100).'元）':'实时扣款'; ?></td>
                        </tr>
                        <tr>
                            <th>每次认购</th>
                            <td><?php echo $orderInfo['followType']==1?$orderInfo['buyMoneyRate'].'%,但不超过'.($orderInfo['buyMaxMoney']/100):($orderInfo['buyMoney']/100); ?>元</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="cp-box-bd zh-box-bd">
                <?php if(!empty($orders)){ ?>
                <table class="table-bet">
                    <colgroup>
                        <col width="10%">
                        <col width="90%">
                    </colgroup>
                    <tbody>
                        <?php foreach ($orders as $k=>$order){ ?>
                        <tr>
                            <td><?php echo $k+1?></td>
                            <td>
                                <a href="<?php echo $this->config->item('base_url')?>hemai/detail/hm<?php echo $order['orderId'];?>/<?php echo $strCode?>" class="zh-item">
                                    <div class="zh-item-l">
                                        <p><em><?php echo $order['issue']; ?>期</em><?php echo number_format(ParseUnit($order['buyMoney'], 1), 2);?>元</p>
                                    </div>
                                    <div class="zh-item-r">
                                    <?php if ($order['status'] != 2000) {?>
                                        <em><?php echo parse_hemai_status($order['status'], $order['my_status']);?></em>
                                    <?php }else{ ?>
                                        <em class="bingo">中奖<?php echo $order['margin'] ? number_format(ParseUnit($order['margin'], 1), 2) : '不足0.01'; ?>元</em>
                                    <?php } ?>
                                    </div>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php }else{ ?>
                <div class="no-gd">
                    <img src="/caipiaoimg/static/images/img-nogd.png" alt="">
                    <p>暂无认购成功记录</p>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="pd30">
            <table class="table-info">
                <tbody>
                    <tr>
                        <th>订单编号</th>
                        <td>GD<?php echo $orderInfo['followId']?></td>
                    </tr>
                    <tr>
                        <th>创建时间</th>
                        <td><?php echo $orderInfo['created']?></td>
                    </tr>
                    <tr>
                        <th>定制时间</th>
                        <td><?php echo $orderInfo['effectTime']?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
        <!-- 撤单确认弹框 -->
    <div class="ui-confirm" style="display:none;">
        <div class="ui-confirm-inner">
            <div class="ui-confirm-hd">温馨提示</div>
            <div class="ui-confirm-bd">
                <div class="confirm-txt tac">
                    <p>确认要停止跟单吗？</p>
                </div>
            </div>
            <div class="ui-confirm-ft">
                <a href="javascript:;" id="closeConfirm">取消</a>
                <a href="javascript:;" class="special-color" id="cancelGendan">确认</a>
            </div>
        </div>
    </div>
    <input type='hidden' class='' name='orderId' value='<?php echo $orderId; ?>'/>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>"></script>
    <script>
    require.config({
        baseUrl: '//<?php echo DOMAIN;?>/caipiaoimg/static/js',
        paths: {
            "zepto" : "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/zepto.min",
            "frozen": "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/frozen.min",
            'basic':'//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/basic'
        }
    });
    require(['basic', 'ui/loading/src/loading', 'ui/tips/src/tips'], function(basic, loading, tips){
    try {
        var uinfo="<?php echo $orderInfo['puid'];?>|<?php echo $uname;?>|<?php echo $orderInfo['lid'];?>|<?php echo BetCnName::getCnName($orderInfo['lid'])?>|<?php echo $points;?>";
        var gendan = 0;
        <?php if ($orderInfo['status'] > 1) { ?>
            gendan = 1;
        <?php } ?>    
        android.continueGendan(uinfo,gendan);
    }catch (e) {
        
    };
            $('#cancelConfirm').on('tap', function(){
                $('.ui-confirm').show();
            });
            $('#closeConfirm').on('tap', function(){
                $('.ui-confirm').hide();
            });
                    // 撤单
        var closeTag = true;
        $('#cancelGendan').on('tap', function(){  
            
            $('.ui-confirm').hide();

            var orderId = $('input[name="orderId"]').val();

            if(closeTag)
            {
                closeTag = false;
                // 等待浮层
                var showLoading = $.loading().loading("mask");
                $.ajax({
                    type: "post",
                    url: '/app/hemai/cancelGendan',
                    data: {orderId:orderId},                   
                    success: function (data) {
                        showLoading.loading("hide");
                        var data = $.parseJSON(data);
                        if(data.status == '1')
                        {
                            closeTag = true;
                            showLoading.loading("hide");
                            $.tips({
                                content:data.msg,
                                stayTime:2000
                            }).on("tips:hide",function(){
                                window.location.reload();
                            });
                        }
                        else
                        {
                            closeTag = true;
                            showLoading.loading("hide");
                            $.tips({
                                content:data.msg,
                                stayTime:2000
                            }).on("tips:hide",function(){
                                window.location.reload();
                            });
                        }
                    },
                    error: function () {
                        closeTag = true;
                        showLoading.loading("hide");
                        $.tips({
                            content: '网络异常，请稍后再试',
                            stayTime: 2000
                        })
                    }
                });  
            }
        })
    });
    </script>
</body>
</html>