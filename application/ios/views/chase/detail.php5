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
    <div class="wrapper bet-detail bet-detail-zh bet-detail-<?php echo $enName; ?>">
        <div class="bet-detail-hd bet-<?php if($bonus > 0): ?>winning<?php endif;?>">
            <div class="lottery-info">
                <h1 class="lottery-info-name">
                	<?php echo $cnName; ?>
                	<?php if ($chaseType) {?><span class="icon-bzbp"><a href="/ios/event/zhbzbp"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/icon-bzbp.png');?>" alt="不中包赔"></a></span><?php }?>
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
                                        <a <?php if(!empty($items['orderUrl'])):?>onClick="window.location.href='<?php echo $items['orderUrl']; ?>';" <?php else: ?>href="javascript:;"<?php endif; ?> class="zh-item">
                                            <div class="zh-item-l">
                                                <p class="hd"><u><?php echo $items['issue']; ?>期</u><?php echo ParseUnit($items['money'], 1); ?>元</p>
                                                <div class="bd">
                                                  <?php echo $items['str']; ?>  
                                                </div>
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
    </div>
    <!-- 撤单确认弹框 -->
    <div class="ui-confirm" style="display:none;">
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

            // 待付款 - 源生支付按钮
            var orderId = '<?php echo $chaseId; ?>', orderStatus = '<?php echo $chaseBtnStatus; ?>', orderType = '<?php echo $orderType; ?>';

            var codes = '<?php echo $orderPlan['codes']; ?>', lid = '<?php echo $orderPlan['lid']; ?>', betLid = '<?php echo $lid; ?>', isChase = '<?php echo $orderPlan['isChase']; ?>', nolexuan = true;

            if(orderStatus == '10'){
                try{
                    window.webkit.messageHandlers.continuePay.postMessage({orderId:orderId, orderType:orderType});
                }catch(e){
                    // ..
                }
            }else{
                
                try{
                    window.webkit.messageHandlers.addDoBetButton.postMessage({lid:betLid});
                }catch(e){
                    // ..
                }

                try{
                    if ($.inArray(lid, ['21406']) > -1) {
                        $.each(codes.split(';'), function(k, code){
                            if($.inArray(code.split(':')[1], ['13', '14', '15']) > -1) nolexuan = false;
                        })
                    }

                    if (lid != '' && codes != '' && ($.inArray(lid, ['33', '52']) === -1 || buyPlatform != '0' || appVersionCode >= '11') && nolexuan)
                        // 原生继续购买按钮
                         window.webkit.messageHandlers.continueBuy.postMessage({codes:codes,lid:lid,isChase:isChase});
                }catch(e){
                    // ..
                }
            }

            // appstore评分
            var appStars = '<?php echo $appStars; ?>';
            if(appStars > 0){
                try{
                    window.webkit.messageHandlers.didNeedRateApp.postMessage({});
                }catch(e){}
            }

            // 打开撤单确认弹窗
            $('#cancelConfirm').on('tap', function(){
                $('.ui-confirm').show();
            })

            // 关闭撤单确认弹窗
            $('#closeConfirm').on('tap', function(){
                $('.ui-confirm').hide();
            })
        });

        // 撤单
        var closeTag = true;
        $('#cancelChase').on('tap', function(){  
            
            $('.ui-confirm').hide();

            var chaseId = $('input[name="chaseId"]').val();

            if(closeTag)
            {
                closeTag = false;
                // 等待浮层
                var showLoading = $.loading().loading("mask");
                $.ajax({
                    type: "post",
                    url: '/ios/chase/cancelChase',
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
    });
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>