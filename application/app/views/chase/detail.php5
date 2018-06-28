<!doctype html> 
<html>
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>追号详情</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/bet-history.min.css');?>">
</head>
<body>
    <div class="wrapper bet-detail bet-detail-zh bet-detail-<?php echo $enName; ?> bet-detail-completed">
        <div class="bet-detail-hd bet-<?php if($bonus > 0): ?>winning<?php endif;?>">
            <div class="lottery-info">
                <h1 class="lottery-info-name">
                	<?php echo $cnName; ?>
                	<?php if ($chaseType) {?><span class="icon-bzbp"><a href="/app/event/zhbzbp"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/icon-bzbp.png');?>" alt="不中包赔"></a></span><?php }?>
                </h1>
                <p <?php if($bonus > 0):?>class="bingo"<?php endif; ?>><?php echo $mangeStatus; ?></p>
                <?php if($status > $chaseStatus['create']): ?>
                <div class="zh-info"><span><?php echo $mangeProgress; ?></span>已追<em><?php echo $chaseIssue; ?></em>期/共<?php echo $totalIssue; ?>期</div>
                <?php endif; ?>
            </div>
        </div>
        <input type='hidden' class='' name='chaseId' value='<?php echo $chaseId; ?>'/>
        <div class="bet-detail-bd cp-box zh-box">
            <div class="cp-box-hd">
                <table class="table-info">
                    <tbody>
                        <tr>
                            <th>投注方案</th>
                            <td>总投注<?php echo ParseUnit($money, 1); ?>元 <?php if($setStatus): ?>•中奖<?php echo $setMoney ? ParseUnit($setMoney, 1) . '元' : '' ; ?>后停止追号<?php endif; ?></td>
                        </tr>
                        <tr>
                            <th></th>
                            <td>
                                <dl>
                                <?php echo $award['tpl'];?>
                                </dl>
                            </td>
                        </tr>
                    </tbody>
                </table>   
            </div>
            <div class="cp-box-bd zh-box-bd">
                <table class="table-bet">
                    <colgroup>
                        <col width="10%">
                        <col width="90%">
                    </colgroup>
                    <tbody>
                        <?php if(!empty($orderList)): ?>
                            <?php foreach ($orderList as $items): ?>
                                <tr>
                                    <td><?php echo $items['index']; ?></td>
                                    <td>
                                        <a href="<?php echo $items['orderUrl']; ?>" class="zh-item">
                                            <div class="zh-item-l">
                                                <p class="hd"><u><?php echo $items['issue']; ?>期</u><?php echo ParseUnit($items['money'], 1); ?>元</p>
                                                <div class="bd">
                                                  <?php echo $items['str']; ?>  
                                                </div>
                                            </div>
                                            <div class="zh-item-r">
                                                <em class="<?php if($items['status'] == $orderStatus['win']):?>bingo<?php endif;?>"><?php echo $items['statusMsg']; ?></em>
                                            </div>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table> 
                <?php if($status > $chaseStatus['create'] && $status != $chaseStatus['chase_over'] && $status != $chaseStatus['stop_by_award'] && $hasstop): ?>
                <a href="javascript:;" class="btn btn-kill" id="cancelConfirm">全部撤单</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="pd30">
			<table class="table-info">
				<tbody>
					<tr>
						<th>订单编号</th>
						<td><?php echo $chaseId;?></td>
					</tr>
					<tr>
						<th>创建时间</th>
						<td><?php echo $created;?></td>
					</tr>
					<?php if($status > $chaseStatus['create']): ?>
					<tr>
						<th>付款时间</th>
						<td><?php echo $pay_time;?></td>
					</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>  
        <?php if($status > $chaseStatus['is_chase'] && ($is_hide & 1) == 0 && $versionInfo['appVersionCode'] >= '40100'): ?>
        <!-- 删除完结订单 -->
        <div class="del-order">
            <i class="icon"><svg viewBox="0 0 28 28"><path class="cls-1" d="M25,5.44H3a1,1,0,0,0,0,1.9H25a1,1,0,0,0,0-1.9ZM22.76,23.2c0,1.4.14,1.9-1.3,1.9H7.19c-1.43,0-1.95-.5-1.95-1.9v-14H3.3V24.46A2.57,2.57,0,0,0,5.89,27H22.11a2.57,2.57,0,0,0,2.59-2.54V9.24H22.76Zm-11-3.51V12.76a1,1,0,1,0-1.94,0v6.92a1,1,0,0,0,1.94,0Zm6.49,0V12.76a1,1,0,1,0-1.94,0v6.92a1,1,0,0,0,1.94,0ZM11.08,4.8v-1a1,1,0,0,1,1-1h3.89a1,1,0,0,1,1,1v1h1.85a2.46,2.46,0,0,0,.09-.63V3.54A2.57,2.57,0,0,0,16.27,1H11.73A2.57,2.57,0,0,0,9.14,3.54v.63a2.46,2.46,0,0,0,.09.63Z"></path></svg></i><span class="del-order-list">删除订单记录</span>
        </div>
        <input type='hidden' class='' name='codeStr' value='<?php echo $codeStr; ?>'/>
        <?php endif; ?>
    </div>
    <!-- 撤单确认弹框 -->
    <div class="ui-confirm" id="cancel-chase-popup" style="display:none;">
        <div class="ui-confirm-inner">
            <div class="ui-confirm-hd">全部撤单</div>
            <div class="ui-confirm-bd">
                <div class="confirm-txt tac">
                    <p>停止剩下所有追号？</p>
                </div>
            </div>
            <div class="ui-confirm-ft">
                <a href="javascript:;" id="closeConfirm">取消</a>
                <a href="javascript:;" class="special-color" id="cancelChase">确认</a>
            </div>
        </div>
    </div>
    <!-- 删除订单 -->
    <div class="ui-popup ui-confirm" id="del-order-popup" style="display: none;">
        <div class="ui-popup-inner">
            <div class="ui-popup-bd">删除订单后记录将无法还原，是否确认删除？</div>
            <div class="ui-popup-ft">
                <a href="javascript:;" id="del-order-cancel">取消</a>
                <a href="javascript:;" id="del-order-confirm">删除</a>
            </div>
        </div>
        <div class="mask"></div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/require.js');?>"></script>
    <script>
    // 基础配置
    require.config({
        baseUrl: '//<?php echo DOMAIN;?>/caipiaoimg/static/js',
        paths: {
            "zepto" : "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/zepto.min",
            "frozen": "//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/frozen.min",
            'basic':'//<?php echo DOMAIN;?>/caipiaoimg/static/js/lib/basic'
        }
    })
    require(['basic', 'ui/loading/src/loading', 'ui/tips/src/tips'], function(basic, loading, tips){

        $(function(){

            try{
                var orderId = '<?php echo $chaseId; ?>', orderStatus = '<?php echo $chaseBtnStatus; ?>', orderType = '<?php echo $orderType; ?>';
                // 源生支付按钮
                android.showNativeButtonByOrderState(orderId, orderStatus, orderType);

                var codes = '<?php echo $orderPlan['codes']; ?>', lid = '<?php echo $orderPlan['lid']; ?>', isChase = '<?php echo $orderPlan['isChase']; ?>', nolexuan = true;
                if ($.inArray(lid, ['21406']) > -1) {
                	$.each(codes.split(';'), function(k, code){
                		if($.inArray(code.split(':')[1], ['13', '14', '15']) > -1) nolexuan = false;
                	})
               	}

                if(lid != '' && nolexuan) android.continuebuy(codes, lid, isChase);
              	
            }catch(e){
                // ..
            }

            // 打开撤单确认弹窗
            $('#cancelConfirm').on('tap', function(){
                $('#cancel-chase-popup').show();
            })

            // 关闭撤单确认弹窗
            $('#closeConfirm').on('tap', function(){
                $('#cancel-chase-popup').hide();
            })
        });

        // 撤单
        var closeTag = true;
        $('#cancelChase').on('tap', function(){  
            
            $('#cancel-chase-popup').hide();

            var chaseId = $('input[name="chaseId"]').val();

            if(closeTag)
            {
                closeTag = false;
                // 等待浮层
                var showLoading = $.loading().loading("mask");
                $.ajax({
                    type: "post",
                    url: '/app/chase/cancelChase',
                    data: {chaseId:chaseId},                   
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

        // 删除订单弹层
        $('.del-order-list').on('click', function(){
            $('#del-order-popup').show();
        })

        // 关闭订单弹层
        $('#del-order-cancel').on('click', function(){
            $('#del-order-popup').hide();
        })

        // 确认删除订单
        var choseTag = true;
        $('#del-order-confirm').on('click', function(){   
            var codeStr = $('input[name="codeStr"]').val();
            if(choseTag)
            {
                choseTag = false;
                var showLoading = $.loading().loading("mask");
                $.ajax({
                    type: "post",
                    url: '/app/chase/chaseOrderDel',
                    data: {codeStr:codeStr},                   
                    success: function (data) {
                        showLoading.loading("hide");
                        var data = $.parseJSON(data);
                        if(data.status == '1')
                        {
                            $.tips({
                                content:data.msg,
                                stayTime:200
                            }).on("tips:hide",function(){
                                try{
                                    android.goBetListActivity('tag');
                                }catch(e){}
                            });
                            choseTag = true;
                        }
                        else
                        {
                            $.tips({
                                content:data.msg,
                                stayTime:2000
                            });
                            choseTag = true;
                        }
                        $('#del-order-popup').hide();
                    },
                    error: function () {
                        choseTag = true;
                        showLoading.loading("hide");
                        $.tips({
                            content: '网络不给力，请稍候再试',
                            stayTime: 2000
                        })
                    }
                });  
            }
        })
    });
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>