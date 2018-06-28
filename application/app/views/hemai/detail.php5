<?php $this->load->view('comm/header'); ?>
	<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/bet-history.min.css');?>">
	<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/hemai.min.css');?>">
</head>
<body>
    <div class="wrapper bet-detail hemai-detail bet-detail-<?php echo BetCnName::getEgName($orderInfo['lid']); ?> bet-detail-completed">
    <!-- 延期公告 -->
    <?php if(!empty($exception) && ($orderInfo['lid'] == BetCnName::JCZQ || $orderInfo['lid'] == BetCnName::JCLQ)): ?>
    <div class="ui-notice" id="showJjcRule"><span><?php echo $exception; ?></span></div>
    <?php endif; ?>
    <?php if (time() < $orderInfo['endTime']) {?>
        <div class="hemai-deadline">截止投注时间<time><?php echo in_array($orderInfo['lid'], array(BetCnName::SFC, BetCnName::RJ)) ? date('Y-m-d H:i', $orderInfo['endTime']) : date('m-d H:i', $orderInfo['endTime'])?></time></div>
    <?php }?>
        <!-- 未中奖bet-failed, 中奖bet-winning -->
        <?php $fg = '';
        if ($joinInfo) {
        	if ($orderInfo['status'] == 2000) {
				$fg = 'bet-winning';
	        }elseif ($orderInfo['status'] == 1000) {
				$fg = 'bet-failed';
			} 
		}?>
        <div class="bet-detail-hd <?php echo $fg?>">
            <div class="lottery-info"><h1 class="lottery-info-name"><?php echo BetCnName::getCnName($orderInfo['lid'])?></h1></div>
            <div class="hemai-status">
            <?php switch ($fg) {
            	case 'bet-winning':
            		echo $joinInfo['margin'] > 0 ? "您中奖" . number_format(ParseUnit($joinInfo['margin'], 1), 2) . "元" : "<small>中奖不足0.01元，奖金给发起人</small>";
					break;
				case 'bet-failed':
					echo '未中奖';
					break;
				default:
					echo empty($joinInfo) ? '未参与' : ($joinInfo['bdzrg'] ? "<small>已购".ParseUnit($joinInfo['buyMoney'], 1)."元(含保转购".ParseUnit($joinInfo['bdzrg'], 1)."元)</small>" : "已购".ParseUnit($joinInfo['buyMoney'], 1)."元");
					break;
            }?>
            </div>
        </div>

        <div class="hemai-progress">
            <div class="hemai-pro-view">
                <div class="progress-cicle"><span><?php echo floor($orderInfo['buyTotalMoney'] * 100/$orderInfo['money'])?><small>%</small></span><div class="progress-cicle-pro"><s style="height: <?php echo 100-$orderInfo['buyTotalMoney'] * 100/$orderInfo['money']?>%;"></s></div></div>
                <?php if ($orderInfo['guaranteeAmount']) {?>保底认购<?php echo floor($orderInfo['guaranteeAmount']*100/$orderInfo['money'])?>%<?php }?>
            </div>
            <div class="hemai-pro-table">
                <table>
                    <thead><tr><th width="35%">方案总额</th><th width="30%">已参与</th><th width="35%">剩余</th> </tr></thead>
                    <tbody><tr><td><?php echo number_format(ParseUnit($orderInfo['money'], 1))?>元</td><td><?php echo $orderInfo['popularity']?>人</td><td><span class="main-color"><?php echo number_format(ParseUnit(($orderInfo['money'] - $orderInfo['buyTotalMoney']), 1))?>元</span></td></tr></tbody>
                </table>
            </div>
        </div>

        <div class="hemai-box">
            <div class="hemai-box-hd">
                <h2 class="hemai-box-title">方案信息</h2>
                <?php if(!empty($joinInfo) && $joinInfo['margin'] > 0 && $unfollow): ?>
                <!-- 合买中奖引导定制跟单 -->
                <span class="hemai-tips" id="gogendan">定制发起人，中奖不错过</span>
                <?php endif; ?>
            </div>
            <div class="hemai-box-bd pd30">
                <table class="table-info">
                    <tbody>
                    	<?php if (in_array($orderInfo['lid'], array(BetCnName::SSQ, BetCnName::DLT, BetCnName::FCSD, BetCnName::PLS, BetCnName::PLW, BetCnName::QXC, BetCnName::QLC, BetCnName::SFC, BetCnName::RJ))) {?><tr><th>方案期次</th><td><?php echo "第".$orderInfo['issue']."期";?></td></tr><?php }?>
                        <tr>
                            <th>发起人</th>
                            <td>
                                <?php if($versionInfo['appVersionCode'] >= 30800): ?>
                                <!-- 原生查看战绩 -->
                                <a href="javascript:;" onclick="android.goUniteWinningsRecordsActivity('<?php echo $orderInfo['uid']?>', '<?php echo $orderInfo['zjLid']; ?>');" class="name"><?php echo uname_cut($orderInfo['uname'])?></a>
                                <div class="level"><?php echo calGrade($orderInfo['points'], 4)?></div>
                                <?php if($versionInfo['appVersionCode'] >= 40000): ?>
                                <a href="javascript:;" id="gendan" class="ui-link">定制跟单</a>
                                <?php else: ?>
                                <a href="javascript:;" onclick="android.goUniteWinningsRecordsActivity('<?php echo $orderInfo['uid']?>', '<?php echo $orderInfo['zjLid']; ?>');" class="ui-link">查看战绩</a>
                                <?php endif; ?>  
                                <?php else: ?>
                                <a href="javascript:;" id="gozj" class="name"><?php echo uname_cut($orderInfo['uname'])?></a>
                                <div class="level"><?php echo calGrade($orderInfo['points'], 4)?></div>
                                <a href="javascript:;" id="zj" class="ui-link">查看战绩</a>
                                <?php endif; ?>                               
                            </td>
                        </tr>
                        <tr><th>认购人次</th><td><?php echo $orderInfo['popularity']?>人次，共<?php echo number_format(ParseUnit($orderInfo['buyTotalMoney'], 1))?>元<a href="javascript:;" id="joinlist" class="ui-link">认购列表</a></td></tr>
                        <tr><th>方案状态</th><td><?php echo parse_hemai_status($orderInfo['status'], $orderInfo['my_status'])?></td></tr>
                        <tr><th>盈利佣金</th><td><?php echo $orderInfo['commissionRate']?>%<?php if ($orderInfo['commission']) {echo "（".number_format(ParseUnit($orderInfo['commission'], 1), 2)."元）";}?><i class="ui-icon-tips tip0">?</i><a href="javascript:void(0);" onClick="window.location.href='<?php echo $this->config->item('pages_url'); ?>app/help/hemai#t-4';" class="ui-link">中奖怎么分</a></td></tr>
                        <?php if(in_array($orderInfo['lid'], array(BetCnName::JCLQ, BetCnName::JCZQ)) && !in_array($orderInfo['status'], array(1000, 2000))):?>
                            <?php if(!empty($orderInfo['ForecastBonusv'])):?><tr><th>预测奖金</th><td><em><?php echo str_replace('|', '~', $orderInfo['ForecastBonusv']);?>元 (仅供参考)</em><i class="ui-icon-tips" id="showForecast">?</i></td></tr><?php endif; ?>
                        <?php else: ?>
                            <?php if ($orderInfo['orderMargin']) {?><tr><th>总奖金</th><td>税后共<span class="main-color"><?php echo number_format(ParseUnit($orderInfo['orderMargin'], 1), 2)?></span>元</td></tr><?php }?>
                        <?php endif;?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="hemai-box <?php if (!$showdetail) {?>close<?php }?>">
            <div class="hemai-box-hd"><h2 class="hemai-box-title">方案内容</h2><?php $openStatusArr = array(0 => '完全公开', 1 => '跟单公开', 2 => '截止公开')?><span class="hemai-box-action"><?php echo $openStatusArr[$orderInfo['openStatus']]?></span></div>
            <?php if ($showdetail) {?>
            <div class="hemai-box-bd">
                <div class="cp-box">
                <?php $isszc = 1;
                if (in_array($orderInfo['lid'], array(BetCnName::JCLQ, BetCnName::JCZQ, BetCnName::SFC, BetCnName::RJ))) {
					$isszc = 0;?>
                    <div class="cp-box-hd">
                        <table class="table-info">
                            <tbody>
                                <tr>
                                    <th>投注方案</th>
                                    <td>
                                    <?php if ($orderInfo['lid'] == BetCnName::JCZQ && in_array($orderInfo['playType'], array(6, 8))) {?>
                                    	<b><?php echo $passWay;?></b><b><?php echo $orderInfo['betNum']; ?>注</b><b>共<?php echo ParseUnit( $orderInfo['money'], 1 ); ?>元</b>
                                    <?php }elseif (in_array($orderInfo['lid'], array(BetCnName::JCZQ, BetCnName::JCLQ))) {?>
                                    	<b><?php echo $passWay;?></b><b><?php echo $orderInfo['betNum']; ?>注</b><b><?php if ($orderInfo['playType'] < 7) echo $orderInfo['multi']."倍"; ?></b><b>共<?php echo ParseUnit( $orderInfo['money'], 1 ); ?>元</b>
                                    <?php }elseif (in_array($orderInfo['lid'], array(BetCnName::SFC, BetCnName::RJ))) {?>
                                    	<b><?php echo $orderInfo['betNum']; ?>注</b><b><?php echo $orderInfo['multi']; ?>倍</b><b>共<?php echo ParseUnit( $orderInfo['money'], 1 ); ?>元</b>
                                    <?php }?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php }?>
                    <?php if (in_array($orderInfo['lid'], array(BetCnName::JCZQ, BetCnName::JCLQ))) {?>
                    <div class="cp-box-bd">
                    	<table class="table-bet">
		                    <colgroup><col width="18%"><col width="24%"><col width="30%"><col width="28%"></colgroup>
		                    <thead><tr><th>场次</th><th><?php if($orderInfo['lid'] == BetCnName::JCZQ){ echo '主队VS客队';}else { echo '客队VS主队';}?></th><th>玩法</th><th>投注方案<i>出票赔率</i></th></tr></thead><tbody><?php echo $award['tpl'];?></tbody>
		                </table>
                    </div>
            		<?php }elseif (in_array($orderInfo['lid'], array(BetCnName::SFC, BetCnName::RJ))) {?>
            		<div class="cp-box-bd">
						<table class="table-bet">
	                    	<colgroup><?php for ($i = 0; $i < 15; $i++) {?><col width="7%"><?php }?></colgroup><thead><tr><th>场次</th><?php for ($i = 1; $i < 15; $i++) {?><th><?php echo $i?></th><?php }?></tr></thead>
		                    <tbody>
		                        <tr><th>主队</th><?php foreach ($matches as $matchinfo): ?><td><?php echo $matchinfo['teamName1']; ?></td><?php endforeach; ?></tr><tr><th>客队</th><?php foreach ($matches as $matchinfo): ?><td><?php echo $matchinfo['teamName2']; ?></td><?php endforeach; ?></tr>
		                        <?php foreach ($betStr as $betarrys): ?>
		                        <tr><th>投注</th><?php foreach ($betarrys as $betarry): ?><td><?php if($betarry) {foreach ($betarry as $betstr) {echo ($betstr['is_win']=='1') ? "<span class='bingo'>".$betstr['bet']."</span>" : $betstr['bet'];}} else {echo '-';} ?></td><?php endforeach; ?></tr>
		                        <?php endforeach; ?>
		                    </tbody>
		                </table>
		            </div>
		            <?php }else {?>
		            <div class="cp-box-bd">
		            	<table class="table-info">
                            <tbody>
                                <tr><th>投注方案</th><td><dl><dt><?php echo $orderInfo['betNum']; ?>注<b><?php echo $orderInfo['multi']; ?>倍</b><b>共<?php echo ParseUnit( $orderInfo['money'], 1 ); ?>元</b></dt><?php echo $award['tpl'];?></dl></td></tr>
                                <tr><th>开奖号码</th><td><?php echo empty($awardDetail['awardNumber']) ? "<span class='main-color'>".$awardDetail['tip']."</span>" : "<div class='ball-group'>".$award['atpl']."</div>"?></td></tr>
                                <!-- 慢频彩派奖时间 -->
                                <?php if( !empty($awardDetail['awardNumber']) && in_array($orderInfo['status'], array($orderStatus['draw'], $orderStatus['draw_part'])) ): ?>
                                    <?php if(in_array($orderInfo['lid'], $this->config->item('fc_lid'))): ?>
                                    <tr>
                                        <th>派奖时间</th>
                                        <td><span class="main-color">今天21:50派奖</span></td>
                                    </tr>
                                    <?php elseif(in_array($orderInfo['lid'], $this->config->item('tc_lid'))): ?>
                                    <tr>
                                        <th>派奖时间</th>
                                        <td><span class="main-color">今天21:05派奖</span></td>
                                    </tr>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
		            </div>
		            <?php }?>
                    <div class="cp-box-ft">
                    <?php if (in_array($orderInfo['lid'], array(BetCnName::SFC, BetCnName::RJ)) && $award['atpl']) {?><div class="pd30"><table class="table-info"><tbody><tr><th>开奖号码</th><td><b class="main-color"><?php echo $award['atpl'];?></b></td></tr></tbody></table></div><?php }?>
	                    <div class="bet-detail-lnk">
	                    <?php if(in_array($orderInfo['lid'], array(BetCnName::JCZQ, BetCnName::JCLQ)) && $orderInfo['playType'] == 7):?><a href="javascript:void(0);" onClick="window.location.href='<?php echo $this->config->item('pages_url'); ?>app/order/bonusopt/hm<?php echo $orderInfo['orderId']; ?>/<?php echo $strCode; ?>';">奖金优化明细</a><?php endif; ?>
	                    <?php if($orderInfo['status'] >= $orderStatus['draw'] && !in_array($orderInfo['status'], array(600, 610, 620))){?>
                        <a onClick="window.location.href='<?php echo $this->config->item('pages_url'); ?>app/order/tickets/hm<?php echo $orderInfo['orderId']; ?>/<?php echo $strCode; ?>'; " href="javascript:void(0);">查看出票明细</a>
                        <!-- 大乐透乐善码 start -->
                        <?php if(!empty($showdetail) && !empty($lsDetail['detail'])): ?>
                        <a href="<?php echo $this->config->item('pages_url'); ?>app/hemai/lsDetail/<?php echo $orderInfo['orderId']; ?>" class="link-lsm">查看订单乐善码<?php if($lsDetail['totalMargin'] > 0):?>（中奖）<?php endif;?></a>
                        <?php endif;?>
                        <!-- 大乐透乐善码 end -->
                        <?php }?>
	                    </div>
                    </div>
                </div>       
            </div>
            <?php }?>
        </div>

        <div class="pd30">
            <table class="table-info">
                <tbody>
                    <tr>
                        <th>合买宣言</th>
                        <td><?php echo $united_intro; ?></td>
                    </tr>
                    <tr><th>投注时间</th><td><?php echo $orderInfo['created']?></td></tr>
                    <tr><th>订单编号</th><td>hm<?php echo $orderInfo['orderId']?></td></tr>
                    <?php if (in_array($orderInfo['status'], array(500, 1000, 2000))) {?><tr><th>出票信息</th><td>已分配到<em class="sub-color" id="showShopDetail"><?php echo $orderInfo['cname'];?></em><p>已成功出票。</p></td></tr><?php }?>
                </tbody>
            </table>
        </div>
        <?php if($orderInfo['status'] > 500 && $is_hide == 0 && $versionInfo['appVersionCode'] >= '40100'): ?>
        <!-- 删除完结订单 -->
        <div class="del-order">
            <i class="icon"><svg viewBox="0 0 28 28"><path class="cls-1" d="M25,5.44H3a1,1,0,0,0,0,1.9H25a1,1,0,0,0,0-1.9ZM22.76,23.2c0,1.4.14,1.9-1.3,1.9H7.19c-1.43,0-1.95-.5-1.95-1.9v-14H3.3V24.46A2.57,2.57,0,0,0,5.89,27H22.11a2.57,2.57,0,0,0,2.59-2.54V9.24H22.76Zm-11-3.51V12.76a1,1,0,1,0-1.94,0v6.92a1,1,0,0,0,1.94,0Zm6.49,0V12.76a1,1,0,1,0-1.94,0v6.92a1,1,0,0,0,1.94,0ZM11.08,4.8v-1a1,1,0,0,1,1-1h3.89a1,1,0,0,1,1,1v1h1.85a2.46,2.46,0,0,0,.09-.63V3.54A2.57,2.57,0,0,0,16.27,1H11.73A2.57,2.57,0,0,0,9.14,3.54v.63a2.46,2.46,0,0,0,.09.63Z"></path></svg></i><span class="del-order-list">删除订单记录</span>
        </div>
        <?php endif; ?>
        <input type='hidden' class='' name='codeStr' value='<?php echo $codeStr; ?>'/>
    </div>
     <div class="ui-alert" id="tip0" style="display: none;">
        <div class="ui-alert-inner">
            <div class="ui-alert-hd">盈利佣金说明</div>
            <div class="ui-alert-bd">
                <p style="text-align: left">合买方案成功，当合买方案盈利（税后奖金大于方案金额）且税后奖金减去盈利佣金仍大于方案金额时，发起人才可以获得佣金。<br>发起人可获得的佣金 = 税后奖金× 佣金比例。</p>
            </div>
            <div class="ui-alert-ft">
                <a href="javascript:;" class="special-color" id="tip0-confirm">朕知道了</a>
            </div>
        </div>
    </div>
    <!-- 充值金额限制提示 -->
    <div class="ui-alert" id="tip1" style="display: none;">
        <div class="ui-alert-inner">
            <div class="ui-alert-hd">方案保底说明</div>
            <div class="ui-alert-bd">
                <p style="text-align: left">发起人保底指发起人承诺合买截止后，如果方案还没有满员，发起人再投入先前承诺的保底金额以最大限度让方案成交。<br>发起人可自愿选择是否保底，保底金额不限，最大可全额保底。</p>
            </div>
            <div class="ui-alert-ft">
                <a href="javascript:;" class="special-color" id="tip1-confirm">朕知道了</a>
            </div>
        </div>
    </div>
    <!-- 投注站详情 -->
    <div class="ui-alert" id="tip2" style="display:none;">
        <div class="ui-alert-inner">
            <div class="ui-alert-hd">投注站详情</div>
            <div class="ui-alert-bd">
                <div class="alert-txt tal">
                    <p>出票投注站: <?php echo $orderInfo['cname']; ?></p>
                    <p>投注站地址: <?php echo $orderInfo['address']; ?></p>
                </div>
            </div>
            <div class="ui-alert-ft">
                <a href="javascript:;" class="special-color" id="closeShopDetail">我知道了</a>
            </div>
        </div>
    </div>
    <!-- 取消、延期公告 -->
    <div class="ui-alert" id="ui-alert-jjc" style="display:none;">
        <div class="ui-alert-inner">
            <div class="ui-alert-hd">场次取消或延期说明</div>
            <div class="ui-alert-bd">
                <div class="alert-txt tal">
                    <p>根据竞彩官方规则，赛事中断或延期，官方等待时间为36小时。36小时内继续完成比赛则正常派奖；超过36小时未完成比赛或赛事取消，则当场比赛的任何结果都算对，所有选项赔率按照1计算。</p>
                </div>
            </div>
            <div class="ui-alert-ft">
                <a href="javascript:;" class="special-color" id="closeJjcRule">我知道了</a>
            </div>
        </div>
    </div>
    <!-- 删除订单 -->
    <div class="ui-popup ui-confirm" id="del-order-popup" style="display: none;">
        <div class="ui-popup-inner">
            <div class="ui-popup-bd" id="del-order-txt">删除订单后记录将无法还原，是否确认删除？</div>
            <div class="ui-popup-ft">
                <a href="javascript:;" id="del-order-cancel">取消</a>
                <a href="javascript:;" id="del-order-confirm">删除</a>
            </div>
        </div>
        <div class="mask"></div>
    </div>
    <!-- 预测奖金 -->
    <div class="ui-alert" id="tip3" style="display:none;">
        <div class="ui-alert-inner">
            <div class="ui-alert-hd">预测奖金</div>
            <div class="ui-alert-bd">
                <div class="alert-txt tal">
                    <p>预测奖金（仅供参考）= 参考赔率*投注本金，实际奖金以出票赔率进行计算。</p>
                </div>
            </div>
            <div class="ui-alert-ft">
                <a href="javascript:;" class="special-color" id="closeForecast">我知道了</a>
            </div>
        </div>
    </div>
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
    })
    require(['basic', 'ui/loading/src/loading', 'ui/tips/src/tips'], function(basic, loading, tips){
        var zj = true, joinlist = true;
    	$('.hemai-box-action').on('click', function() {
            $(this).parents('.hemai-box').toggleClass('close');
        })
        $(".tip0").click(function(){
            $("#tip0").show();
        })
        $("#tip0-confirm").click(function(){
          	$("#tip0").hide();
        })
        $(".tip1").click(function(){
            $("#tip1").show();
        })
        $("#tip1-confirm").click(function(){
          	$("#tip1").hide();
        })
        $('#showShopDetail').on('tap', function(){
            $('#tip2').show();
        })
        $("#closeShopDetail").click(function(){
          	$("#tip2").hide();
        })
        $('#showForecast').on('tap', function(){
            $('#tip3').show();
        })
        $("#closeForecast").click(function(){
            $("#tip3").hide();
        })
        $('#gozj').click(function(){
            if (zj) {
                zj = false;
                window.location.href = '<?php echo $this->config->item('base_url')?>hemai/userinfo/<?php echo $infostrCode?>'
                zj = true;
            }
        })
        $('#zj').click(function(){
            if (zj) {
                zj = false;
                window.location.href = '<?php echo $this->config->item('base_url')?>hemai/userinfo/<?php echo $infostrCode?>'
                zj = true;
            }
        })
        $('#joinlist').click(function(){
            if (joinlist) {
            	joinlist = false;
            	android.goUniteBuyRecordsActivity('<?php echo $orderInfo['orderId']?>');
            	joinlist = true;
            }
        })
        try {
            var orderState = <?php echo (int)($orderInfo['buyTotalMoney'] < $orderInfo['money'] && time()< $orderInfo['endTime'] && $orderInfo['status'] < 600)?>,
            cancelState = <?php echo (int)($orderInfo['uid'] == $uid && !in_array($orderInfo['status'], array(600, 610, 620)) && $orderInfo['buyTotalMoney'] + $orderInfo['guaranteeAmount'] < $orderInfo['money']/2 && $orderInfo['endTime'] > time())?>;
        	android.uniteDetailContent(orderState, '<?php echo empty($uid) ? '-1' : number_format(ParseUnit($uinfo['money'], 1), 2)?>', <?php echo ($orderInfo['money']-$orderInfo['buyTotalMoney'])/100?>, <?php 
        	echo (int)($orderInfo['uid'] == $uid)?>, '<?php echo $orderInfo['orderId']?>', '<?php echo $orderInfo['uname']?>', <?php echo $orderInfo['lid']?>, cancelState);
        }catch (e) {
        };

        
        try{
            // 原生继续购买按钮       
            var codes = '<?php echo $continuebuy['codes']; ?>';
            var lid = '<?php echo $orderInfo['lid']; ?>';
            var isChase = '<?php echo $continuebuy['isChase']; ?>';
            var buyPlatform = '<?php echo $orderInfo['buyPlatform']; ?>';
            var appVersionCode = '<?php echo $versionInfo['appVersionCode']; ?>';
            if (codes != '' && ($.inArray(lid, ['33', '52']) === -1 || ($.inArray(lid, ['33', '52']) > -1 && buyPlatform != '0' && appVersionCode <= '30701') || ($.inArray(lid, ['33', '52']) > -1 && appVersionCode > '30701')))
            {
                android.continuebuy(codes, lid, isChase);
            }
        }catch (e) {
        };


        // 赛事分析页
        $('.jcMatchLive').on('click', function(){
            var jcMid = $(this).data('index').toString();
            var ctype = "<?php echo ($orderInfo['lid'] == '42') ? '0' : '1'; ?>";
            if(jcMid > 0){
                try{
                    android.goLiveTab(jcMid, ctype);
                }catch(e){
                    // ..
                }
            }
        })
        
        $("#gendan").on('click', function(){
           $.post("/app/api/v1/gendan/hasGendan",
           {data:'<?php echo $postData;?>'},
            function(res){
                if(res.status==1){
                   android.goCustomizationOrderActivity("<?php echo $orderInfo['uid']?>|<?php echo $orderInfo['uname']?>|<?php echo $orderInfo['lid']?>|<?php echo BetCnName::getCnName($orderInfo['lid'])?>|<?php echo $orderInfo['points']?>");
                }
                if(res.status==300){
                    android.relogin(location.href);
                }
                if(res.status==400){
                    android.auth(location.href);
                }
                if(res.status==0){
                    $.tips({
                        content: res.msg,
                        stayTime: 2000
                    });
                }
            },
            'json'); 
        });

        // 删除订单弹层
        var choseTag = true;
        $('.del-order-list').on('click', function(){
            // 检查跟单情况
            var codeStr = $('input[name="codeStr"]').val();
            if(choseTag)
            {
                choseTag = false;
                var showLoading = $.loading().loading("mask");
                $.ajax({
                    type: "post",
                    url: '/app/hemai/countJoinOrders',
                    data: {codeStr:codeStr},                   
                    success: function (data) {
                        showLoading.loading("hide");
                        var data = $.parseJSON(data);
                        if(data.status == '1')
                        { 
                            if(parseInt(data.data) > 1){
                                $('#del-order-txt').text('当前合买方案您有多笔认购，是否全部删除？');
                            }
                            $('#del-order-popup').show();
                            choseTag = true;
                        }else{
                            $.tips({
                                content:data.msg,
                                stayTime:2000
                            });
                            choseTag = true;
                        }          
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

        // 关闭订单弹层
        $('#del-order-cancel').on('click', function(){
            $('#del-order-popup').hide();
        })

        // 确认删除订单
        $('#del-order-confirm').on('click', function(){   
            var codeStr = $('input[name="codeStr"]').val();
            if(choseTag)
            {
                choseTag = false;
                var showLoading = $.loading().loading("mask");
                $.ajax({
                    type: "post",
                    url: '/app/hemai/unitedOrderDel',
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

        // 查看竞技彩取消延期公告
        $('#showJjcRule').on('tap', function(){
            $('#ui-alert-jjc').show();
        });

        // 关闭竞技彩取消延期公告
        $('#closeJjcRule').on('tap', function(){
            $('#ui-alert-jjc').hide();
        });

        // 跳转发起人定制跟单
        $("#gogendan").on('click', function(){
            android.goCustomizationOrderActivity("<?php echo $orderInfo['uid']?>|<?php echo $orderInfo['uname']?>|<?php echo $orderInfo['lid']?>|<?php echo BetCnName::getCnName($orderInfo['lid'])?>|<?php echo $orderInfo['points']?>");
        });
    })
        
    </script>
</body>

</html>