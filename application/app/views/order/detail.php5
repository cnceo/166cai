<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/bet-history.min.css');?>">
</head>
<body>
    <div class="wrapper bet-detail bet-detail-<?php echo $enName;?> <?php if (in_array($enName, array('jlks', 'jxks'))) {?>bet-detail-ks<?php } ?> bet-detail-completed">
        <!-- Banner -->
        <div class="side-lnk" id="app-detail-pop" style="display:none;">
            <a href="/app/activity/joinus"><img src="<?php echo getStaticFile('/caipiaoimg/static/img/img-joinus.png');?>" alt=""></a>
            <a href="javascript:;" class="close" id="pop-side-close"></a>
        </div>
        <!-- 延期公告 -->
        <?php if(!empty($exception) && ($order['lid'] == BetCnName::JCZQ || $order['lid'] == BetCnName::JCLQ)): ?>
        <div class="ui-notice" id="showJjcRule"><span><?php echo $exception; ?></span></div>
        <?php endif; ?>
        <!-- 投注站信息 -->
        <?php if($order['status'] >= $orderStatus['drawing']):?>
        <section class="o2o-bet-detail">
            <h1>出票信息</h1>
            <p>已分配到<em class="sub-color" id="showShopDetail"><?php echo $shopDetail['cname'];?></em></p>
            <?php if ($order['status'] == $orderStatus['concel']) :?>
            <p>出票失败，已退款至账户。</p>
            <?php elseif($order['status'] == $orderStatus['draw_part'] || $order['failMoney'] > 0):?>
            <p>出票部分成功</p>
            <p><a href="<?php echo $this->config->item('pages_url'); ?>app/order/tickets/<?php echo $orderId; ?>/<?php echo $strCode; ?>"><em class="sub-color">查看出票明细</em></a><small>(未及时出票的投注金现已退至账户余额)</small></p>    
            <?php elseif($order['status'] >= $orderStatus['draw']): ?>
            <p>已成功出票。</p>
            <!-- 大乐透乐善码 start -->
            <?php if(!empty($lsDetail['detail'])): ?>
            <a href="<?php echo $this->config->item('pages_url'); ?>app/order/lsDetail/<?php echo $orderId; ?>/<?php echo $strCode; ?>" class="link-lsm">查看订单乐善码<?php if($lsDetail['totalMargin'] > 0):?>（中奖）<?php endif;?></a>
            <?php endif;?>
            <!-- 大乐透乐善码 end -->
            <?php endif;?>
        </section>
        <?php endif;?>   
        <!-- 订单详情 -->
        <div class="bet-detail-hd <?php if($order['margin'] > 0):?>bet-winning<?php elseif($order['margin'] == 0 && $order['status'] == $orderStatus['notwin']): ?>bet-failed<?php endif;?>">
            <div class="lottery-info">
                <h1 class="lottery-info-name"><?php echo $cnName;?><?php if($order['lid'] != BetCnName::JCZQ && $order['lid'] != BetCnName::JCLQ):?><span>第<?php echo $order['issue'];?>期</span><?php endif;?></h1>
                <p <?php if($order['margin'] > 0):?>class="bingo"<?php endif; ?>><?php if ($order['margin'] > 0):?>
                	<?php if($order['bonus'] != $order['margin']){
                		      echo "税前奖金<em>".number_format(ParseUnit($order['bonus'], 1), 2)."</em>元，税后奖金<em>".number_format(ParseUnit($order['margin'], 1), 2)."</em>元";
                		  }else{
                                if($order['add_money'] > 0 && in_array($order['lid'], array(BetCnName::JCZQ, BetCnName::JCLQ))){
                                    echo "中奖<em>".number_format(ParseUnit($order['margin'] + $order['add_money'], 1), 2)."</em>元";
                                }else{
                                    echo "中奖<em>".number_format(ParseUnit($order['margin'], 1), 2)."</em>元";
                                }
                	      }
                	?>
                <?php elseif ($order['status'] == $orderStatus['draw_part']):?>
                	<em>等待开奖</em>
                <?php else:?>
                	<em><?php 
                			echo parse_order_status($order['status'], $order['my_status']);
                			if($order['status'] == $orderStatus['drawing']){
                				echo "<a href='/app/order/ticketTime' class='lottery-info-timetips'></a>";
                			}
                			?>
                	</em>
                <?php endif;?>
                </p>
                <?php if($order['add_money'] > 0):?>
                <div class="zh-info">
                	<?php 
                	if($order['bonus'] != $order['margin']){
                		echo "加奖<em>".number_format(ParseUnit($order['add_money'], 1), 2)."</em>元";
                	}else{
                		if(in_array($order['lid'], array(BetCnName::JCZQ, BetCnName::JCLQ)))
                		{
                			echo "奖金<em>".number_format(ParseUnit($order['margin'], 1), 2)."</em>元+加奖<em>".number_format(ParseUnit($order['add_money'], 1), 2)."</em>元";
                		}else{
                			echo "奖金<em>".number_format(ParseUnit($order['margin']-$order['add_money'], 1), 2)."</em>元+加奖<em>".number_format(ParseUnit($order['add_money'], 1), 2)."</em>元";
                		}
                	}
                	?>
                </div>
                <?php endif;?>
            </div>
            <!--
            <a href="javascript:;" id="orderPay" class="btn btn-mini btn-confirm">付款</a>
            -->
            <input type='hidden' class='' name='codeStr' value='<?php echo $codeStr; ?>'/>
        </div>
        <div class="bet-detail-bd cp-box">
        <?php if($order['lid'] == BetCnName::JCZQ || $order['lid'] == BetCnName::JCLQ):?>
            <?php if($order['lid'] == BetCnName::JCZQ && $order['playType'] == 6):?>
            <!-- 竞足单关 -->
            <div class="cp-box-hd">
            	<table class="table-info">
					<tbody>
						<tr><th>订单金额</th><td><b>共<?php echo ParseUnit( $order['money'], 1 ); ?>元<?php if($order['redpackId'] && $order['status'] >='40'):?>(红包抵扣<?php echo ParseUnit( $order['redpackMoney'], 1 );?>元，实付<?php echo ParseUnit($order['money'] - $order['redpackMoney'], 1);?>元)<?php endif;?></b></td></tr>
						<tr><th>投注方案</th><td><b><?php echo $passWay;?></b><b><?php echo $order['betTnum']; ?>注</b></td></tr>
                        <!-- 竞足竞篮预测奖金 -->
                        <?php if(!in_array($order['status'], array(1000, 2000)) && !empty($order['forecastBonus'])): ?>
                        <tr><th>预测奖金</th><td><em><?php echo str_replace('|', '~', $order['forecastBonus']);?>元 (仅供参考)</em><i class="ui-icon-tips" id="showForecast">?</i></td></tr>
                        <?php endif; ?>
					</tbody>
				</table>
            </div>
            <div class="cp-box-bd">
                <table class="table-bet">
                    <colgroup>
                        <col width="18%">
                        <col width="22%">
                        <col width="20%">
                        <col width="22%">
                        <col width="18%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>场次</th>
                            <th>主队VS客队</th>
                            <th>玩法</th>
                            <th>投注方案<i>出票赔率</i></th>
                            <th>投注金额(元)</i></th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php echo $award['tpl'];?>
                    </tbody>
                </table>
             </div>
            <?php else: ?>
            <div class="cp-box-hd">
            	<table class="table-info">
					<tbody>
						<tr><th>订单金额</th><td><b>共<?php echo ParseUnit( $order['money'], 1 ); ?>元<?php if($order['redpackId'] && $order['status'] >='40'):?>(红包抵扣<?php echo ParseUnit( $order['redpackMoney'], 1 );?>元，实付<?php echo ParseUnit($order['money'] - $order['redpackMoney'], 1);?>元)<?php endif;?></b></td></tr>
						<tr><th>投注方案</th><td><b><?php echo $passWay;?></b><b><?php echo $order['betTnum']; ?>注</b><?php if ($order['playType'] < 7) echo "<b>".$order['multi']."倍</b>"; ?></td></tr>
                        <!-- 竞足竞篮预测奖金 -->
                        <?php if(!in_array($order['status'], array(1000, 2000)) && !empty($order['forecastBonus'])): ?>
                        <tr><th>预测奖金</th><td><em><?php echo str_replace('|', '~', $order['forecastBonus']);?>元 (仅供参考)</em><i class="ui-icon-tips" id="showForecast">?</i></td></tr>
                        <?php endif; ?>
					</tbody>
				</table>
            </div>
            <div class="cp-box-bd">
                <table class="table-bet">
                    <colgroup>
                        <col width="18%">
                        <col width="24%">
                        <col width="30%">
                        <col width="28%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>场次</th>
                            <th><?php if($order['lid'] == BetCnName::JCZQ){ echo '主队VS客队';}else { echo '客队VS主队';}?></th>
                            <th>玩法</th>
                            <th>投注方案<i>出票赔率</i></th>
                        </tr>
                    </thead>
                    <tbody>
                       <?php echo $award['tpl'];?>
                    </tbody>
                </table>
             </div>
            <?php endif;?>
        <?php elseif ($order['lid'] == BetCnName::SFC || $order['lid'] == BetCnName::RJ):?>
            <div class="cp-box-hd">
            	<table class="table-info">
					<tbody>
						<tr><th>订单金额</th><td><b>共<?php echo ParseUnit( $order['money'], 1 ); ?>元<?php if($order['redpackId'] && $order['status'] >='40'):?>(红包抵扣<?php echo ParseUnit( $order['redpackMoney'], 1 );?>元，实付<?php echo ParseUnit($order['money'] - $order['redpackMoney'], 1);?>元)<?php endif;?></b></td></tr>
						<tr><th>投注方案</th><td><b><?php echo $order['betTnum']; ?>注</b><b><?php echo $order['multi']; ?>倍</b></td></tr>
					</tbody>
				</table>
            </div>
            <div class="cp-box-bd">
                <table class="table-bet">
                    <colgroup>
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                        <col width="7%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>场次</th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th>主队</th>
                            <?php foreach ($matches as $matchinfo): ?>
                            <td><?php echo $matchinfo['teamName1']; ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <th>客队</th>
                            <?php foreach ($matches as $matchinfo): ?>
                            <td><?php echo $matchinfo['teamName2']; ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php foreach ($betStr as $betarrys): ?>
                        <tr>
                            <th>投注</th>
                            <?php foreach ($betarrys as $betarry): ?>
                            <td>
                            <?php if($betarry): ?>
                            <?php foreach ($betarry as $betstr): ?>
                            <?php if($betstr['is_win']=='1'): ?><span class="bingo"><?php echo $betstr['bet']; ?></span><?php else : ?>
                            <?php echo $betstr['bet']; ?>
                            <?php endif;?>
                            <?php endforeach; ?>
                            <?php else :?>
                            -
                            <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="cp-box-ft">
                <table class="table-info">
                    <tbody>
                        <tr>
                            <th>开奖号码</th>
                            <td>
                                <b class="main-color"><?php echo $award['atpl'];?></b>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="bet-detail-lnk">
                    <?php if($order['status'] >= $orderStatus['draw'] && $order['status'] != $orderStatus['concel']):?>
                    <a href="<?php echo $this->config->item('pages_url'); ?>app/order/tickets/<?php echo $orderId; ?>/<?php echo $strCode; ?>">查看出票明细</a> 
                    <?php endif; ?>
                </div>
            </div>
        <?php elseif ($order['lid'] == BetCnName::GJ || $order['lid'] == BetCnName::GYJ):?>
            <!-- 冠军彩 -->
            <div class="cp-box-hd">
            	<table class="table-info">
					<tbody>
						<tr><th>订单金额</th><td><b>共<?php echo ParseUnit( $order['money'], 1 ); ?>元<?php if($order['redpackId'] && $order['status'] >='40'):?>(红包抵扣<?php echo ParseUnit( $order['redpackMoney'], 1 );?>元，实付<?php echo ParseUnit($order['money'] - $order['redpackMoney'], 1);?>元)<?php endif;?></b></td></tr>
						<tr><th>投注方案</th><td><b><?php echo $order['betTnum']; ?>注</b><b><?php echo $order['multi']; ?>倍</b></td></tr>
					</tbody>
				</table>
            </div>
            <div class="cp-box-bd">
                <table class="table-bet">
                    <colgroup>
                        <col width="22%">
                        <col width="56%">
                        <col width="22%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>投注编号</th>                            
                            <th>投注方案<i>参考赔率</i></th>
                            <th>彩果</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($orderDetail['info'])): ?>
                        <?php foreach ($orderDetail['info'] as $detail): ?>
                        <tr>
                            <td><?php echo $detail['mid']?></td>
                            <td><?php echo $detail['name']?><br><?php echo $detail['sp']?></td>
                            <td <?php if($detail['bonus'] > 0){echo "class='bingo'";} ?>><?php echo $detail['statusMsg']?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else :?>
            <div class="cp-box-bd">
                <table class="table-info">
                    <tbody>
                    	<tr>
                            <th>订单金额</th>
                            <td>
                                共<?php echo ParseUnit( $order['money'], 1 ); ?>元<?php if($order['redpackId'] && $order['status'] >='40'):?>(红包抵扣<?php echo ParseUnit( $order['redpackMoney'], 1 );?>元，实付<?php echo ParseUnit($order['money'] - $order['redpackMoney'], 1);?>元)<?php endif;?>
                            </td>
                        </tr>
                        <tr>
                            <th>投注方案</th>
                            <td>
                                <dl>
                                   <dt><?php echo $order['betTnum']; ?>注<b><?php echo $order['multi']; ?>倍</b></dt> 
                                   <?php echo $award['tpl'];?>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <th>开奖号码</th>
                            <td>                              
                                <?php if( !empty($awardDetail['awardNumber']) ): ?>
                                <div class="<?php if($order['lid'] == '54'):?>poker<?php else :?>ball-group<?php endif;?>"><?php echo $award['atpl'];?></div>
                                <?php else: ?>
                                <span class="main-color"><?php echo $awardDetail['tip'];?></span>
                                <?php endif;?>                                
                            </td>
                        </tr>
                        <!-- 慢频彩派奖时间 -->
                        <?php if( !empty($awardDetail['awardNumber']) && in_array($order['status'], array($orderStatus['draw'], $orderStatus['draw_part'])) ): ?>
                            <?php if(in_array($order['lid'], $this->config->item('fc_lid'))): ?>
                            <tr>
                                <th>派奖时间</th>
                                <td><span class="main-color">今天21:50派奖</span></td>
                            </tr>
                            <?php elseif(in_array($order['lid'], $this->config->item('tc_lid'))): ?>
                            <tr>
                                <th>派奖时间</th>
                                <td><span class="main-color">今天21:05派奖</span></td>
                            </tr>
                            <?php endif; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
             </div>
        <?php endif;?>
            <!-- 出票明细 start -->
            <?php if(($order['status'] >= $orderStatus['draw'] && $order['status'] != $orderStatus['concel']) || (in_array($order['lid'], array(BetCnName::JCZQ, BetCnName::JCLQ)) && $order['playType'] == 7)):?>
            <?php if(!in_array($order['lid'], array(BetCnName::SFC, BetCnName::RJ))): ?>
            <div class="cp-box-ft">   
                <ul class="cp-list">
                    <!-- 竞彩足球奖金优化 start -->
                    <?php if(in_array($order['lid'], array(BetCnName::JCZQ, BetCnName::JCLQ)) && $order['playType'] == 7):?>
                    <li>
                        <a href="<?php echo $this->config->item('pages_url'); ?>app/order/bonusopt/<?php echo $orderId; ?>/<?php echo $strCode; ?>" target="_blank" class="ticket-detail">
                            <em>奖金优化</em>
                            <span>查看优化后方案注数及倍数</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <!-- 竞彩足球奖金优化 end -->
                    <?php if($order['status'] >= $orderStatus['draw'] && $order['status'] != $orderStatus['concel']): ?>
                    <li>
                        <a href="<?php echo $this->config->item('pages_url'); ?>app/order/tickets/<?php echo $orderId; ?>/<?php echo $strCode; ?>" target="_blank" class="ticket-detail">
                            <em>出票明细</em>
                            <span><?php echo (!in_array($order['lid'], array(BetCnName::JCZQ, BetCnName::JCLQ)))?'展示实际出票内容':'实际奖金以出票时刻盘口赔率为准'; ?></span>
                        </a>
                    </li>
                    <?php endif;?> 
                </ul>        
            </div>
            <?php endif;?> 
            <?php endif;?>
            <!-- 出票明细 end -->
        </div> 	
    	<div class="pd30">
    		<table class="table-info">
    			<tbody>
    				<tr>
    					<th>创建时间</th>
    					<td><?php echo date('Y-m-d H:i:s', strtotime($order['created']));?></td>
    				</tr>
                    <?php if($order['status'] >= $orderStatus['pay']): ?>
    				<tr>
    					<th>支付时间</th>
    					<td><?php echo date('Y-m-d H:i:s', strtotime($order['pay_time']));?></td>
    				</tr>
                    <?php endif; ?>
                    <?php if($order['status'] >= $orderStatus['draw'] && $order['status'] != $orderStatus['concel']): ?>
                    <tr>
                        <th>出票时间</th>
                        <td><?php echo $order['ticket_time'] ? $order['ticket_time'] : '0000-00-00 00:00:00'; ?></td>
                    </tr>
                    <?php endif; ?>
    				<tr>
    					<th>订单编号</th>
    					<td><?php echo $order['orderId']; ?></td>
    				</tr>
    			</tbody>
    		</table>
    	</div>
    	<div class="bet-detail-lnk">
    		<a href="<?php echo $this->config->item('pages_url'); ?>app/order/winTips">中奖后如何领奖？</a>
            <?php if(!in_array($order['lid'], array(BetCnName::JCZQ, BetCnName::JCLQ, BetCnName::SFC, BetCnName::RJ, BetCnName::GYJ, BetCnName::GJ))):?>
            <!-- 数字彩展示 -->
            <a href="<?php echo $this->config->item('pages_url'); ?>app/help/play/<?php echo $enName; ?>">中奖怎么算？</a>
            <?php endif; ?>
    	</div> 
    	<?php if(!empty($banner)): ?>
    	<aside class="img2active mini">
			<a href="javascript:;" class="img-link">
				<img src="<?php echo $banner['imgUrl']; ?>" alt="">
			</a>
		</aside>
		<?php endif;?>
        <?php ?>
        <?php if($order['status'] > $orderStatus['draw'] && ($order['is_hide'] & 1) == 0 && $versionInfo['appVersionCode'] >= '40100'): ?>
        <!-- 删除完结订单 -->
        <div class="del-order">
            <i class="icon"><svg viewBox="0 0 28 28"><path class="cls-1" d="M25,5.44H3a1,1,0,0,0,0,1.9H25a1,1,0,0,0,0-1.9ZM22.76,23.2c0,1.4.14,1.9-1.3,1.9H7.19c-1.43,0-1.95-.5-1.95-1.9v-14H3.3V24.46A2.57,2.57,0,0,0,5.89,27H22.11a2.57,2.57,0,0,0,2.59-2.54V9.24H22.76Zm-11-3.51V12.76a1,1,0,1,0-1.94,0v6.92a1,1,0,0,0,1.94,0Zm6.49,0V12.76a1,1,0,1,0-1.94,0v6.92a1,1,0,0,0,1.94,0ZM11.08,4.8v-1a1,1,0,0,1,1-1h3.89a1,1,0,0,1,1,1v1h1.85a2.46,2.46,0,0,0,.09-.63V3.54A2.57,2.57,0,0,0,16.27,1H11.73A2.57,2.57,0,0,0,9.14,3.54v.63a2.46,2.46,0,0,0,.09.63Z"></path></svg></i><span class="del-order-list">删除订单记录</span>
        </div>
        <?php endif; ?>
        <!-- 投注站详情 -->
        <div class="ui-alert" id="ui-alert-bet" style="display:none;">
            <div class="ui-alert-inner">
                <div class="ui-alert-hd">投注站详情</div>
                <div class="ui-alert-bd">
                    <div class="alert-txt tal">
                        <p>出票投注站: <?php echo $shopDetail['cname']; ?></p>
                        <p>投注站地址: <?php echo $shopDetail['address']; ?></p>
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
                <div class="ui-popup-bd">删除订单后记录将无法还原，是否确认删除？</div>
                <div class="ui-popup-ft">
                    <a href="javascript:;" id="del-order-cancel">取消</a>
                    <a href="javascript:;" id="del-order-confirm">删除</a>
                </div>
            </div>
            <div class="mask"></div>
        </div>
        <!-- 预测奖金 -->
        <div class="ui-alert" id="ui-alert-forecast" style="display:none;">
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
                    // Banner展示
                    // if(!localStorage.popshow || localStorage.popshow == 'undefined'){
                    //     $('#app-detail-pop').show();
                    // }

                    var orderId = '<?php echo $order['orderId']; ?>', orderStatus = '<?php echo $order['status']; ?>', orderType = '<?php echo $order['orderType']; ?>';
                    // 源生支付按钮
                    android.showNativeButtonByOrderState(orderId, orderStatus, orderType);
                    var buyPlatform = '<?php echo $order['buyPlatform']; ?>', codes = '<?php echo $orderPlan['codes']; ?>', lid = '<?php echo $orderPlan['lid']; ?>', 
                    isChase = '<?php echo $orderPlan['isChase']; ?>', appVersionCode = '<?php echo $versionInfo['appVersionCode']; ?>', nolexuan = true;
                    if ($.inArray(lid, ['21406']) > -1) {
                    	$.each(codes.split(';'), function(k, code){
                        	if($.inArray(code.split(':')[1], ['13', '14', '15']) > -1) nolexuan = false;
                        })
                    }
                    // 原生继续购买按钮
                    if (lid != '' && ($.inArray(lid, ['33', '52']) === -1 || ($.inArray(lid, ['33', '52']) > -1 && buyPlatform != '0' && appVersionCode <= '30701') || ($.inArray(lid, ['33', '52']) > -1 && appVersionCode > '30701')) && nolexuan){
                        android.continuebuy(codes, lid, isChase);
                    }
                    
                }catch(e){
                    // ..
                }
            });

            var appAction = "<?php echo $banner['appAction']; ?>", lid = "<?php echo $banner['tlid']; ?>", 
            enName = "<?php echo $banner['enName']; ?>", webUrl = "<?php echo $banner['webUrl']; ?>";

            $('.img-link').on('click', function(){
                try{
                    // 点击事件
                    android.umengStatistic('webview_paysuccess_ad');
                }catch(e){
                    // ...
                }
                if(appAction == 'bet'){
                    bet.btnclick(lid, enName);
                }else if(appAction == 'email'){
                    android.goBindEmail('');
                }else if(appAction == 'unsupport'){
                    $.tips({
                        content:'请前往设置页面升级至最新版本！',
                        stayTime:2000
                    });
                }else if(appAction == 'ignore'){
                    $.tips({
                        content:'您已绑定过邮箱',
                        stayTime:2000
                    });
                }else{
                    window.location.href = webUrl;
                }
            });

            // 查看投注站明细
            $('#showShopDetail').on('tap', function(){
                $('#ui-alert-bet').show();
            });

            // 关闭投注站明细
            $('#closeShopDetail').on('tap', function(){
                $('#ui-alert-bet').hide();
            });

            // 查看竞技彩取消延期公告
            $('#showJjcRule').on('tap', function(){
                $('#ui-alert-jjc').show();
            });

            // 关闭竞技彩取消延期公告
            $('#closeJjcRule').on('tap', function(){
                $('#ui-alert-jjc').hide();
            });

            // 查看预测奖金
            $('#showForecast').on('tap', function(){
                $('#ui-alert-forecast').show();
            });

            // 关闭预测奖金
            $('#closeForecast').on('tap', function(){
                $('#ui-alert-forecast').hide();
            });
            
            var closeTag = true;
            $('#orderPay').on('tap', function(){   
                var codeStr = $('input[name="codeStr"]').val();
                if(closeTag)
                {
                    closeTag = false;
                    var showLoading = $.loading().loading("mask");
                    $.ajax({
                        type: "post",
                        url: '/app/order/orderPay',
                        data: {codeStr:codeStr},                   
                        success: function (data) {
                            showLoading.loading("hide");
                            var data = $.parseJSON(data);
                            if(data.status == '1')
                            {
                                window.location.href = data.data;
                                closeTag = true;
                            }
                            else
                            {
                                $.tips({
                                    content:data.msg,
                                    stayTime:2000
                                });
                                closeTag = true;
                            }
                        },
                        error: function () {
                            closeTag = true;
                            showLoading.loading("hide");
                            $.tips({
                                content: '网络不给力，请稍候再试',
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
                        url: '/app/order/orderDel',
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

            // 关闭侧边栏
            $('#pop-side-close').on('click', function(){
                $(this).parents('#app-detail-pop').hide();
                localStorage.popshow = '1';
            })

            // 赛事分析页
            $('.jcMatchLive').on('click', function(){
                var jcMid = $(this).data('index').toString();
                var ctype = "<?php echo ($order['lid'] == '42') ? '0' : '1'; ?>";
                if(jcMid > 0){
                    try{
                        android.goLiveTab(jcMid, ctype);
                    }catch(e){
                        // ..
                    }
                }
            })

        });
    </script>
</body>
</html>
