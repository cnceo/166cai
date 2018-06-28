<?php if (in_array($lotteryId, array(SSQ, DLT, SYXW, JXSYXW, HBSYXW, KS, KLPK, CQSSC, GDSYXW))) {?>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/lottery_gaoji.min.js');?>"></script>
<?php } elseif (in_array($lotteryId, array(PLS, PLW, FCSD, QLC, QXC))) {?>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/lottery.min.js');?>"></script>
<?php }
$arr = array("日", "一", "二", "三", "四", "五", "六");?>

<script type="text/javascript">
    $(function () {
        var timeId_hide,timeId_show,$det_pop = $(".lotteryTit .det_pop");

        $(".lotteryTit .rule").on("mouseenter", function (e) {
            clearTimeout(timeId_show);
            clearTimeout(timeId_hide);
            timeId_show = setTimeout(show_det_pop,500);
        });

        $(".lotteryTit .rule").on("mouseleave", function (e) {
            clearTimeout(timeId_show);
            timeId_hide = setTimeout(hide_det_pop,100);
        });

        $(".lotteryTit .det_pop").on("mouseenter", function (e) {
            clearTimeout(timeId_show);
            clearTimeout(timeId_hide);
            timeId_show = setTimeout(show_det_pop,500);
        });

        $(".lotteryTit .det_pop").on("mouseleave", function (e) {
            clearTimeout(timeId_show);
            timeId_hide = setTimeout(hide_det_pop,100);
        });

        function show_det_pop(){
            $det_pop.show();
        }

        function hide_det_pop(){
            $det_pop.hide();
        }
    });
</script>
<?php
if (in_array($lotteryId, array(SFC, RJ))) {
    $time = $targetIssue['seFsendtime'] / 1000 - time();
} else {
    $time = $info['next']['seFsendtime'] / 1000 - time();
}
$day = floor($time / 86400);
$hour = floor(($time % 86400) / 3600);
$min = floor(($time % 3600) / 60);
$sec = ($time % 3600) % 60;
?>
<div class="lotteryTit <?php if (!in_array($lotteryId, array(KS, KLPK))) {?>issue cp-box-hd<?php } ?>">
    <?php
    $this->config->load('help');
    $help_center_rule = $this->config->item('help_center_rule');
    $lottery_type = $this->config->item('lottery_type');
    ?>
    <?php if (in_array($lotteryId, array(SYXW, JXSYXW, HBSYXW, GDSYXW))): ?>
        <div class="lottery-info lottery-<?php echo $enName?>">
            <div class="lottery-info-img">
              <div class="lottery-img">
              		<?php if(in_array($lotteryId, array(GDSYXW))):?>
              		 <img class="noMap" src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/gdsyxw.png'); ?>" srcset="/caipiaoimg/v1.1/images/gdsyxw.svg 2x" width="80" height="80" alt="">
              		 <?php else :?>
                    <svg width="320" height="320">
                        <image alt="<?php echo $cnName?>" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                    <?php endif;?>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <h1 class="lottery-info-name"><?php echo $cnName;?></h1>
                    <span class="lottery-info-time"><?php if($lotteryId == JXSYXW) {echo '(新11选5)';}?>第<b><?php echo $info['cIssue']['seExpect'] ?></b>期</span>
                    <?php if ($lotteryId == SYXW) {?><span class="lottery-info-tips"><i class="iArrow"></i><em>还剩<?php echo $rest?>期</em></span><?php }?>
                    <?php $numArr = array(SYXW => '87', JXSYXW => '84', HBSYXW => '81', GDSYXW => '84')?>
                    <p class="lottery-info-num"><b>10</b>分钟一期，每天<b><?php echo $numArr[$lotteryId]?></b>期，返奖率<b class="main-color-s">59%</b></p>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type[$enName]; ?>" class="no-bd-l" target="_blank"><i class="icon-font">&#xe620;</i>玩法介绍</a>
                        <?php if ($lotteryId == SYXW) {?>
                        <a href="https://zoushi.166cai.cn/cjw11x5_qs/view/11x5_jiben-5-11ydj-11.html" rel="nofollow" target="_blank"><i class="icon-font">&#xe622;</i>基本走势</a>
                        <?php }elseif ($lotteryId == JXSYXW) {?>
                        <a href="https://zoushi.166cai.cn/cjw11x5_qs/view/11x5_jiben-5-11x5-11.html" rel="nofollow" target="_blank"><i class="icon-font">&#xe622;</i>基本走势</a>
                        <?php }elseif ($lotteryId == GDSYXW) {?>
                        <a href="https://zoushi.166cai.cn/cjw11x5_qs/view/11x5_jiben-5-gd11x5-11.html" rel="nofollow" target="_blank"><i class="icon-font">&#xe622;</i>基本走势</a>
                        <?php }else {?>
                        <a href="https://zoushi.166cai.cn/cjw11x5_qs/view/11x5_jiben-5-hub11x5-11.html" rel="nofollow" target="_blank"><i class="icon-font">&#xe622;</i>基本走势</a>
                        <?php }?>
                        <a href="javascript:;" target="_self" class="rule"><i class="icon-font">&#xe61a;</i>中奖规则</a>
                    </div>
                </div>
            </div>
            <div class="kj-area">
                <h2 class="kj-periods">第<b><?php echo empty($info['aIssue']) ? $info['lIssue']['seExpect'] : $info['aIssue']['seExpect']?></b>期开奖号码</h2>
                <div class="kj-num">
                <?php for ($j = 0; $j < 5; $j++) {?>
                    <div class="kj-num-item">
                        <div class="inner">
                            <ul>
                            <?php switch ($j) {case 0:?><li>正</li><?php break;case 1:?><li>在</li><?php break;case 2:?><li>开</li><?php break;case 3:?><li>奖</li><?php break;case 4:?><li>中</li><?php break;}?>
                                <?php for ($i = 1; $i <= 11; $i++) {?>
                                    <li><?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?></li>
                                <?php }?>
                            </ul>
                        </div>
                    </div>
                <?php }?>
                </div>
                <div class="kj-time">销售时间：<?php if ($lotteryId == SYXW) {?>8:25-22:55<?php }elseif ($lotteryId == HBSYXW) {?>8:25-21:55<?php }else {?>9:00-23:00<?php }?></div>
            </div>
        </div>
        <div class="det_pop">
            <div class="arr"></div>
            <?php if ($lotteryId == SYXW) {?>
            <table width="765">
                <colgroup><col width="70"><col width="90"><col width="105"><col width="160"><col width="265"><col width="75"></colgroup>
                <thead><tr><th>玩法</th><th>单注投注金额</th><th>开奖号码示例</th><th>投注号码示例</th><th>中奖规则</th><th>单注奖金</th></tr></thead>
                <tbody>
                <tr><td rowspan="3">乐选三</td><td rowspan="3">6元</td><td rowspan="19">01 02 03 04 05</td><td class="tal">01 02 03</td><td class="tal">仅选3个号码，猜中前三个号码且顺序一致</td><td class="tar"><em>1384</em>元</td></tr>
                <tr><td class="tal">03 02 01</td><td class="tal">仅选3个号码，猜中前3个号码（排序不限）</td><td class="tar"><em>214</em>元</td></tr>
                <tr><td class="tal">01 02 04</td><td class="tal">仅选3个号码，猜中开奖号码任意3个数字</td><td class="tar"><em>19</em>元</td></tr>
                <tr><td rowspan="2">乐选四</td><td rowspan="2">10元</td><td class="tal">01 02 03 04</td><td class="tal">仅选4个号码，猜中开奖号码任意4个数字</td><td class="tar"><em>154</em>元</td></tr>
                <tr><td class="tal">01 02 03 06</td><td class="tal">仅选4个号码，猜中开奖号码任意3个数字</td><td class="tar"><em>19</em>元</td></tr>
                <tr><td rowspan="2">乐选五</td><td rowspan="2">14元</td><td class="tal">01 02 03 04 05</td><td class="tal">仅选5个号码，猜中开奖号码全部5个数字</td><td class="tar"><em>1080</em>元</td></tr>
                <tr><td class="tal">01 02 03 04 06</td><td class="tal">仅选5个号码，猜中开奖号码任意4个数字</td><td class="tar"><em>90</em>元</td></tr>
                <tr><td>任选二</td><td>2元</td><td class="tal">01 05</td><td class="tal">选2个号码，猜中开奖号码任意2个数字</td><td class="tar"><em>6</em>元</td></tr>
                <tr><td>任选三</td><td>2元</td><td class="tal">01 02 04</td><td class="tal">选3个号码，猜中开奖号码任意3个数字</td><td class="tar"><em>19</em>元</td></tr>
                <tr><td>任选四</td><td>2元</td><td class="tal">01 02 04 05</td><td class="tal">选4个号码，猜中开奖号码任意4个数字</td><td class="tar"><em>78</em>元</td></tr>
                <tr><td>任选五</td><td>2元</td><td class="tal">01 02 03 04 05</td><td class="tal">选5个号码，猜中开奖号码的全部5个数字</td><td class="tar"><em>540</em>元</td></tr>
                <tr><td>任选六</td><td>2元</td><td class="tal">01 02 03 04 05 06</td><td class="tal">选6个号码，猜中开奖号码的全部5个数字</td><td class="tar"><em>90</em>元</td></tr>
                <tr><td>任选七</td><td>2元</td><td class="tal">01 02 03 04 05 06 07</td><td class="tal">选7个号码，猜中开奖号码的全部5个数字</td><td class="tar"><em>26</em>元</td></tr>
                <tr><td>任选八</td><td>2元</td><td class="tal">01 02 03 04 05 06 07 08</td><td class="tal">选8个号码，猜中开奖号码的全部5个数字</td><td class="tar"><em>9</em>元</td></tr>
                <tr><td>直选前一</td><td>2元</td><td class="tal">01</td><td class="tal">选1个号码，猜中开奖号码第1个数字</td><td class="tar"><em>13</em>元</td></tr>
                <tr><td>直选前二</td><td>2元</td><td class="tal">01 02</td><td class="tal">选2个号码与开奖的前2个号码相同且顺序一致</td><td class="tar"><em>130</em>元</td></tr>
                <tr><td>组选前二</td><td>2元</td><td class="tal">02 01</td><td class="tal">选2个号码与开奖的前2个号码相同</td><td class="tar"><em>65</em>元</td></tr>
                <tr><td>直选前三</td><td>2元</td><td class="tal">01 02 03</td><td class="tal">选3个号码与开奖的前3个号码相同且顺序一致</td><td class="tar"><em>1170</em>元</td></tr>
                <tr><td>组选前三</td><td>2元</td><td class="tal">03 02 01</td><td class="tal">选3个号码与开奖的前3个号码相同</td><td class="tar"><em>195</em>元</td></tr>
                </tbody>
            </table>
            <?php }else {?>
            <table width="695">
                <colgroup><col width="61"><col width="120"><col width="160"><col width="280"><col width="70"></colgroup>
                <thead><tr><th>玩法</th><th>开奖号码示例</th><th>投注号码示例</th><th>中奖规则</th><th>单注奖金</th></tr></thead>
                <tbody>
                <tr><td>任选二</td><td rowspan="12">01 02 03 04 05</td><td class="tal">01 05</td><td class="tal">选2个号码，猜中开奖号码任意2个数字</td><td class="tar"><em>6</em>元</td></tr>
                <tr><td>任选三</td><td class="tal">01 02 04</td><td class="tal">选3个号码，猜中开奖号码任意3个数字</td><td class="tar"><em>19</em>元</td></tr>
                <tr><td>任选四</td><td class="tal">01 02 04 05</td><td class="tal">选4个号码，猜中开奖号码任意4个数字</td><td class="tar"><em>78</em>元</td></tr>
                <tr><td>任选五</td><td class="tal">01 02 03 04 05</td><td class="tal">选5个号码，猜中开奖号码的全部5个数字</td><td class="tar"><em>540</em>元</td></tr>
                <tr><td>任选六</td><td class="tal">01 02 03 04 05 06</td><td class="tal">选6个号码，猜中开奖号码的全部5个数字</td><td class="tar"><em>90</em>元</td></tr>
                <tr><td>任选七</td><td class="tal">01 02 03 04 05 06 07</td><td class="tal">选7个号码，猜中开奖号码的全部5个数字</td><td class="tar"><em>26</em>元</td></tr>
                <tr><td>任选八</td><td class="tal">01 02 03 04 05 06 07 08</td><td class="tal">选8个号码，猜中开奖号码的全部5个数字</td><td class="tar"><em>9</em>元</td></tr>
                <tr><td>直选前一</td><td class="tal">01</td><td class="tal">选1个号码，猜中开奖号码第1个数字</td><td class="tar"><em>13</em>元</td></tr>
                <tr><td>直选前二</td><td class="tal">01 02</td><td class="tal">选2个号码与开奖的前2个号码相同且顺序一致</td><td class="tar"><em>130</em>元</td></tr>
                <tr><td>组选前二</td><td class="tal">02 01</td><td class="tal">选2个号码与开奖的前2个号码相同</td><td class="tar"><em>65</em>元</td></tr>
                <tr><td>直选前三</td><td class="tal">01 02 03</td><td class="tal">选3个号码与开奖的前3个号码相同且顺序一致</td><td class="tar"><em>1170</em>元</td></tr>
                <tr><td>组选前三</td><td class="tal">01 02 03</td><td class="tal">选3个号码与开奖的前3个号码相同</td><td class="tar"><em>195</em>元</td></tr>
                </tbody>
            </table>
            <?php }?>
            
        </div>
        
        <div class="my-order">
            <div class="my-order-hd"><a href="javascript:;" <?php if (!$isLogin) { echo 'class="not-login"';  }?>>我的投注<i class="arrow"></i></a><i></i></div>
            <div class="my-order-bd">
            <?php if ($isLogin) {?>
                    <table>
                        <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                        <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal"><a target="_blank" href="mylottery/betlog">更多</a></th></tr></thead>
                    <tbody>
            <?php if (empty($orders)) {?>
                            <tr><td colspan="8" style="height: 100px;">亲，您三个月内还没有订单哦！</td></tr></tbody></table>
            <?php } else {?>
                        <?php foreach ($orders as $order) {
                            $cstr = '';
                            $codeArr = explode(';', $order['codes']);
                            foreach ($codeArr as $c => $code) {
                                if ($c < 8) {
                                    $carr = explode(':', $code);
                                    $ctarr = array('01' => '前一', '02' => '任二', '03' => '任三', '04' => '任四', '05' => '任五', '06' => '任六', '07' => '任七', '08' => '任八', '09' => '前二直选', '10' => '前三直选', '11' => '前二组选', '12' => '前三组选', '13' => '乐三', '14' => '乐四', '15' => '乐五');
                                    $count = count(explode(',', $carr[0]));
                                    $cstr .= $ctarr[$carr[1]].(!in_array($carr[1], array('09', '10', '11', '12')) ? ($carr[2] === '05' ? '胆拖': ($count > intval($carr[1]) ? '复式' : '单式')) : '').str_replace(',', ' ', preg_replace('/((.+)\$)/', '( $2 ) ', $carr[0])).';';
                                }
                            }
                            if ($c >= 8) $cstr .= '...';?>
                            <tr>
                                <td><?php echo $order['created']?></td>
                                <td><?php echo $order['issue'];?></td>
                                <td class="tal" title="<?php echo $cstr?>">
                                    <div class="text-overflow">
                                    <?php $carr = explode(':', $codeArr[0]);
                                    $count = count(explode(',', $carr[0]));
                                    echo $ctarr[$carr[1]].(!in_array($carr[1], array('09', '10', '11', '12')) ? ($carr[2] === '05' ? '胆拖': ($count > intval($carr[1]) ? '复式' : '单式')) : '')?> <span class="specil-color"><?php echo str_replace(array('|', ','), array('<s> | </s>', ' '), preg_replace('/((.+)\$)/', '( $2 ) ', $carr[0]))?></span>
                                    <?php if ($c >= 1) echo '...'?>
                                    </div>
                                </td>
                                <td><span class="fcs"><?php echo number_format(ParseUnit($order['money'], 1), 2);?></span></td>
                                <td><span class="fcs"><?php echo parse_order_status($order['status'], $order['my_status']); ?></span></td>
                                <?php if($order['margin'] > 0):?>
                                    <td><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/gold.png');?>" alt="">&nbsp;<strong class="spec arial"><?php echo number_format(ParseUnit($order['margin'], 1), 2);?></strong></td>
                                <?php elseif(!in_array($order['status'], array('1000', '2000'))):?>
                                    <td>--</td>
                                <?php else:?>
                                    <td><?php echo number_format(ParseUnit($order['margin'], 1), 2);?></td>
                                <?php endif;?>
                                <td><a target="_blank" href="/orders/detail/<?php echo $order['orderId']; ?>">查看详情</a>
                                <?php if ($order['status'] == '10' && (strtotime("-{$lotteryConfig[$order['lid']]['ahead']} MINUTE", strtotime($order['endTime'])) > time())) {?>
                                    <a href="javascript:cx.castCb({orderId:'<?php echo $order['orderId']; ?>'}, {ctype:'paysearch', orderType:0})"><span class="num-red">立即支付</span></a></td>
                                <?php } else {?>
                                    <a target="_blank" href="/<?php echo $enName?>?orderId=<?php echo $order['orderId']; ?>">继续预约</a></td>
                                <?php }?>
                                <td></td>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            <?php }
            } ?>
            </div>
        </div>
    <?php elseif ($lotteryId == KS): ?>
    	<div class="lottery-info lottery-ks">
			<div class="lottery-img">
				<svg width="320" height="320">
					<image alt="经典快3" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
				</svg>
			</div>
			<div class="lottery-info-txt">
				<div class="lottery-info-col lottery-info-col1"><h1 class="lottery-info-name">经典快3</h1><p class="lottery-info-num">10分钟一期，每天82期~</p></div>
				<div class="lottery-info-col lottery-info-time"><span>第<strong></strong>期<b>投注剩余</b></span><span class="time"></span><span class="arrow-tag">剩<?php echo $rest?>期<i></i></span></div>
				<div class="lottery-info-col kj-area">
					<h2 class="kj-periods">第<em><?php echo substr($prev, -3)?></em>期<b>开奖号码</b></h2><div class="kj-num" style="display: none"></div><div class="kj-ing"></div><ul><li>和值：<strong></strong></li><li></li></ul>
				</div>
			</div>
		</div>
	<?php elseif ($lotteryId == KLPK): ?>
		<div class="lottery-info lottery-klpk">
			<div class="lottery-img">
				<svg width="320" height="320">
					<image alt="快乐扑克" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
				</svg>
			</div>
			<div class="lottery-info-txt">
				<div class="lottery-info-col lottery-info-col1"><h1 class="lottery-info-name">快乐扑克</h1><p class="lottery-info-num">10分钟一期，轻松中奖~<br>每天8:20-23:00销售</p></div>
				<div class="lottery-info-col lottery-info-time"><span>第<strong></strong>期<b>投注剩余</b></span><span class="time"></span><span class="arrow-tag">剩<?php echo $rest?>期<i></i></span></div>
				<div class="lottery-info-col kj-area">
					<h2 class="kj-periods">第<em><?php echo substr($prev, -2)?></em>期<b>开奖号码</b></h2><div class="kj-num" style="display: none"></div><div class="kj-ing"></div><ul><li>形态:<strong></strong></li></ul>
				</div>
			</div>
		</div>
    <?php elseif ($lotteryId == JCZQ): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-jczq">
                <div class="lottery-img">
                    <svg width="320" height="320">
                        <image alt="竞彩足球" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <p class="lottery-info-time tar">竞猜为全场90分钟（含伤停补时）的比分结果，不含加时及点球大战<br>自购截止：赛前<em><?php echo $lotteryConfig[JCZQ]['ahead']; ?></em>分钟，合买截止：赛前<em><?php echo $lotteryConfig[JCZQ]['ahead']+$lotteryConfig[JCZQ]['united_ahead']; ?></em>分钟</p>
                    <h1 class="lottery-info-name">竞彩足球</h1>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a rel="nofollow" href="<?php echo $this->config->item('api_bf');?>jingcai" class="no-bd-l" target="_blank">比分直播</a>
                        <a rel="nofollow" href="<?php echo $this->config->item('api_info');?>" class="no-bd-l" target="_blank">资料库</a>
                        <a href="/kaijiang/jczq" class="no-bd-l" target="_blank">赛果信息</a>
                    </div>
                    <?php $this->load->view('v1.1/elements/lottery/tabs'); ?>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == JCLQ): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-jclq">
                <div class="lottery-img">
                    <svg width="320" height="320">
                        <image alt="竞彩篮球" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <p class="lottery-info-time tar">69%超高返奖率，竞彩全场结果（包括加时赛）<br>自购截止：赛前<em><?php echo $lotteryConfig[JCLQ]['ahead']; ?></em>分钟，合买截止：赛前<em><?php echo $lotteryConfig[JCLQ]['ahead']+$lotteryConfig[JCLQ]['united_ahead']; ?></em>分钟</p>
                    <h1 class="lottery-info-name">竞彩篮球</h1>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a rel="nofollow" href="<?php echo $this->config->item('api_bf');?>basketball" class="no-bd-l" target="_blank">比分直播</a>
                        <a rel="nofollow" href="<?php echo $this->config->item('api_info');?>lanqiu" class="no-bd-l" target="_blank">资料库</a>
                        <a href="/kaijiang/jclq" class="no-bd-l" target="_blank">赛果信息</a>
                    </div>
                    <?php $this->load->view('v1.1/elements/lottery/tabs'); ?>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == SFC): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-sfc">
                <div class="lottery-img">
                    <svg width="320" height="320">
                        <image alt="胜负彩" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <div class="lottery-info-time">
                        <a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/sfc" class="fr">详情</a>第<?php echo $awardIssue['seExpect']; ?>期 开奖结果
                        <div class="num-group"><?php $awardNums = explode(',', $awardIssue['awardNumber']);if ( ! empty($awardNums)): ?><span><?php echo implode('</span><span>', $awardNums) ?></span><?php endif; ?></div>
                    </div>
                    <h1 class="lottery-info-name">胜负彩</h1>
                    <p class="lottery-info-num">
                        <span><b>第<i><?php echo $targetIssue['seExpect'] ?></i>期</b></span><span>本期投注截止时间：<i class="end-time" id="end-time" 
                        data-time="<?php echo date('Y-m-d H:i:s', $currIssue['seFsendtime'] / 1000) ?>"><?php echo date('m-d H:i', $targetIssue['seFsendtime'] / 1000) ?></i>（<i 
                        class="week-day">星期<?php echo $arr[date("w", $targetIssue['seFsendtime'] / 1000)] ?></i>）</span><span>合买截止：<i 
                        class="end-time"><?php echo date('m-d H:i', ($targetIssue['seFsendtime'] / 1000 - $lotteryConfig[$lotteryId]['united_ahead'] * 60))?></i></span>
                    </p>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a rel="nofollow" href="<?php echo $this->config->item('api_bf');?>zucai" class="no-bd-l" target="_blank">比分直播</a><a rel="nofollow" href="<?php echo $this->config->item('api_info');?>" 
                        target="_blank">资料库</a><a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['sfc']; ?>" target="_blank"><i class="icon-font">&#xe60f;</i>玩法介绍</a><a 
                        class="rule" href="javascript:;"><i class="icon-font">&#xe60d;</i>中奖规则</a>
                        <div class="det_pop">
                            <div class="arr"></div>
                            <table width="500">
                                <colgroup><col width="70"><col width="160"><col width="270"></colgroup><thead><tr><th>奖级</th><th>中奖条件</th><th>单注奖金</th></tr></thead>
                                <tbody>
                                <tr><td>一等奖</td><td>14场比赛胜平负结果全中</td><td>奖金总额的70%与奖池奖金之和除以中奖注数</td></tr>
                                <tr><td>二等奖</td><td>中任意13场比赛胜平负结果</td><td>奖金总额的30%除以中奖注数</td></tr>
                                <tr><td colspan="3" class="tal plr10"><p style="white-space: nowrap;"><em class="special-color">注：</em>1、单注奖金封顶500万元</p></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->load->view('v1.1/elements/lottery/tabs'); 
                    if ($targetIssue['seFsendtime'] > time() * 1000): ?>
                        <p class="count-down1 count-down">本期投注剩余：<?php if ($day) { ?><em><?php echo str_pad($day, 2, "0", STR_PAD_LEFT) ?></em>天<?php } ?><em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min, 2, "0", STR_PAD_LEFT) ?></em>分<?php if (empty($day)) { ?><em><?php echo str_pad($sec, 2, "0", STR_PAD_LEFT) ?></em>秒<?php } ?></p>
                    <?php else: ?>
                        <p class="count-down">投注时间已截止</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == RJ): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-rj">
                <div class="lottery-img">
                    <svg width="320" height="320">
                        <image alt="任选九" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <div class="lottery-info-time">
                        <a target="_blank" href="<?php echo $baseUrl; ?>kaijiang/rj" class="fr">详情</a>第<?php echo $awardIssue['seExpect']; ?>期 开奖结果
                        <div class="num-group"><?php $awardNums = explode(',', $awardIssue['awardNumber']); if ( ! empty($awardNums)): ?><span><?php echo implode('</span><span>', $awardNums) ?></span><?php endif; ?></div>
                    </div>
                    <h1 class="lottery-info-name">任选九</h1>
                    <p class="lottery-info-num">
                        <span><b>第<i><?php echo $targetIssue['seExpect'] ?></i>期</b></span><span>本期投注截止时间：<i class="end-time" id="end-time"
                        data-time="<?php echo date('Y-m-d H:i:s', $targetIssue['seFsendtime'] / 1000) ?>"><?php echo date('m-d H:i', $targetIssue['seFsendtime'] / 1000) ?></i>（<i 
                        class="week-day">星期<?php echo $arr[date("w", $targetIssue['seFsendtime'] / 1000)] ?></i>）</span><span>合买截止：<i 
                        class="end-time"><?php echo date('m-d H:i', ($targetIssue['seFsendtime'] / 1000 - $lotteryConfig[$lotteryId]['united_ahead'] * 60))?></i></span>
                    </p>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a rel="nofollow" href="<?php echo $this->config->item('api_bf');?>zucai" class="no-bd-l" target="_blank">比分直播</a><a rel="nofollow" href="<?php echo $this->config->item('api_info');?>"  
                        target="_blank">资料库</a><a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['rj']; ?>" target="_blank"><i class="icon-font">&#xe60f;</i>玩法介绍</a><a 
                        class="rule" href="javascript:;"><i class="icon-font">&#xe60d;</i>中奖规则</a>

                        <div class="det_pop">
                            <div class="arr"></div>
                            <table width="500">
                                <colgroup><col width="70"><col width="160"><col width="270"></colgroup><thead><tr><th>奖级</th><th>中奖条件</th><th>单注奖金</th></tr></thead>
                                <tbody>
                                <tr><td>一等奖</td><td>9场比赛胜平负结果全中</td><td>奖金总额的100%与奖池奖金之和除以中奖注数</td></tr>
                                <tr><td colspan="3" class="tal plr10"><p style="white-space: nowrap;"><em class="special-color">注：</em>1、单注奖金封顶500万元</p></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->load->view('v1.1/elements/lottery/tabs');
                    if ($targetIssue['seFsendtime'] > time() * 1000): ?>
                        <p class="count-down1 count-down">本期投注剩余：<?php if ($day) { ?><em><?php echo str_pad($day, 2, "0", STR_PAD_LEFT) ?></em>天<?php } ?><em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min, 2, "0", STR_PAD_LEFT) ?></em>分<?php if (empty($day)) { ?><em><?php echo str_pad($sec, 2, "0", STR_PAD_LEFT) ?></em>秒<?php } ?></p>
                    <?php else: ?>
                        <p class="count-down">投注时间已截止</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == SSQ): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-ssq">
                <div class="lottery-img">
                    <svg width="320" height="320">
                        <image alt="<?php echo $cnName; ?>" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <p class="lottery-info-time">每周二、四、日晚21:15开奖</p><h1 class="lottery-info-name"><?php echo $cnName; ?></h1><p class="lottery-info-num"><span><b>第<i 
                    class="curr-issue"><?php echo $info['next']['issue'] ?></i>期</b></span><span>本期投注截止时间：<i class="end-time"><?php echo date('m-d H:i', $info['next']['seFsendtime'] / 1000) ?></i>（<i 
                    class="week-day">星期<?php echo $arr[date("w", $info['next']['seFsendtime'] / 1000)] ?></i>）</span><span>合买截止：<i 
                    class="end-time"><?php echo date('m-d H:i', ($info['next']['seFsendtime'] / 1000 - $lotteryConfig[$lotteryId]['united_ahead'] * 60))?></i></span><?php if (date('z') === date('z', $info['next']['seFsendtime'] / 1000) 
                    && time() < $info['next']['seFsendtime'] / 1000) { ?><span class="tips-s"><s class="tips-s-arrow"></s>今日开奖</span><?php } ?></p>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['ssq']; ?>" class="no-bd-l" target="_blank"><i class="icon-font">&#xe612;</i>玩法介绍</a>
                        <a href="https://zoushi.166cai.cn/cjwssq/view/ssqzonghe.html#166" target="_blank" rel="nofollow"><i class="icon-font">&#xe615;</i>基本走势</a>
                        <a href="javascript:;" target="_self" class="rule" target="_blank"><i class="icon-font">&#xe613;</i>中奖规则</a>
                        <div class="det_pop">
                            <div class="arr"></div>
                            <table width="236">
                                <colgroup><col width="50"><col width="116"><col width="68"></colgroup><thead><tr><th>奖级</th><th>中奖条件</th><th>奖金分配</th></tr></thead>
                                <tbody>
                                <tr><td>一等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 6; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span></div></td><td>浮动</td></tr>
                                <tr><td>二等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 6; $i++) {?><span class="ball9 ball9-red"></span><?php }?></div></td><td>浮动</td></tr>
                                <tr><td>三等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 5; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span></div></td><td>3000元</td></tr>
                                <tr><td rowspan="2">四等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 5; $i++) {?><span class="ball9 ball9-red"></span><?php }?></div></td><td rowspan="2">200元</td></tr>
                                <tr><td><div class="award-balls9"><?php for ($i = 0; $i < 4; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span></div></td></tr>
                                <tr><td rowspan="2">五等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 4; $i++) {?><span class="ball9 ball9-red"></span><?php }?></div></td><td rowspan="2">10元</td></tr>
                                <tr><td><div class="award-balls9"><span class="ball9 ball9-red"></span><span class="ball9 ball9-red"></span><span class="ball9 ball9-red"></span><span class="ball9 ball9-blue"></span></div></td></tr>
                                <tr><td rowspan="3">六等奖</td><td><div class="award-balls9"><span class="ball9 ball9-red"></span><span class="ball9 ball9-red"></span><span class="ball9 ball9-blue"></span></div></td><td rowspan="3">5元</td></tr>
                                <tr><td><div class="award-balls9"><span class="ball9 ball9-red"></span><span class="ball9 ball9-blue"></span></div></td></tr>
                                <tr><td><div class="award-balls9"><span class="ball9 ball9-blue"></span></div></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->load->view('v1.1/elements/lottery/tabs', array('type' => 'cast')); ?>
                    <p class="count-down1 count-down">
                    本期投注剩余：<?php if ($day) { ?><em><?php echo str_pad($day, 2, "0", STR_PAD_LEFT) ?></em>天<?php } ?><em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
                                2, "0", STR_PAD_LEFT) ?></em>分<?php if (empty($day)) { ?><em><?php echo str_pad($sec, 2,
                            "0", STR_PAD_LEFT) ?></em>秒<?php } ?>
                    </p>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == DLT): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-dlt">
                <div class="lottery-img">
                    <svg width="320" height="320">
                        <image alt="<?php echo $cnName; ?>" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <p class="lottery-info-time">每周一、三、六晚20:30开奖</p><h1 class="lottery-info-name"><?php echo $cnName; ?></h1><p class="lottery-info-num"><span><b>第<i 
                    class="curr-issue"><?php echo $info['next']['issue'] ?></i>期</b></span><span>本期投注截止时间：<i class="end-time"><?php echo date('m-d H:i', $info['next']['seFsendtime'] / 1000) ?></i>（<i 
                    class="week-day">星期<?php echo $arr[date("w", $info['next']['seFsendtime'] / 1000)] ?></i>）</span><span>合买截止：<i 
                    class="end-time"><?php echo date('m-d H:i', ($info['next']['seFsendtime'] / 1000 - $lotteryConfig[$lotteryId]['united_ahead'] * 60))?></i></span><?php if (date('z') === date('z', $info['next']['seFsendtime'] / 1000) 
                    && time() < $info['next']['seFsendtime'] / 1000) { ?><span class="tips-s"><s class="tips-s-arrow"></s>今日开奖</span><?php } ?></p>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['dlt']; ?>" class="no-bd-l" target="_blank"><i class="icon-font">&#xe612;</i>玩法介绍</a>
                        <a href="https://zoushi.166cai.cn/cjwdlt/view/dltjiben.html#166" target="_blank" rel="nofollow"><i class="icon-font">&#xe615;</i>基本走势</a>
                        <a href="javascript:;" target="_self" class="rule" target="_blank"><i class="icon-font">&#xe60d;</i>中奖规则</a>
                        <div class="det_pop">
                            <div class="arr"></div>
                            <table width="236">
                                <colgroup><col width="50"><col width="116"><col width="68"></colgroup><thead><tr><th>奖级</th><th>中奖条件</th><th>奖金分配</th></tr></thead>
                                <tbody>
                                <tr>
                                    <td>一等奖</td>
                                    <td><div class="award-balls9"><?php for ($i = 0; $i < 5; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span><span class="ball9 ball9-blue"></span></div></td>
                                    <td>浮动</td>
                                </tr>
                                <tr><td>二等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 5; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span></div></td><td>浮动</td></tr>
                                <tr><td rowspan="2">三等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 5; $i++) {?><span class="ball9 ball9-red"></span><?php }?></div></td><td rowspan="2">浮动</td></tr>
                                <tr><td><div class="award-balls9"><?php for ($i = 0; $i < 4; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span><span class="ball9 ball9-blue"></span></div></td></tr>
                                <tr><td rowspan="2">四等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 4; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span></div></td><td rowspan="2">200元</td></tr>
                                <tr><td><div class="award-balls9"><?php for ($i = 0; $i < 3; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span><span class="ball9 ball9-blue"></span></div></td></tr>
                                <tr><td rowspan="3">五等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 4; $i++) {?><span class="ball9 ball9-red"></span><?php }?></div></td><td rowspan="3">10元</td></tr>
                                <tr><td><div class="award-balls9"><?php for ($i = 0; $i < 3; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span></div></td></tr>
                                <tr><td><div class="award-balls9"><span class="ball9 ball9-red"></span><span class="ball9 ball9-red"></span><span class="ball9 ball9-blue"></span><span class="ball9 ball9-blue"></span></div></td></tr>
                                <tr><td rowspan="4">六等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 3; $i++) {?><span class="ball9 ball9-red"></span><?php }?></div></td><td rowspan="4">5元</td></tr>
                                <tr><td><div class="award-balls9"><span class="ball9 ball9-red"></span><span class="ball9 ball9-red"></span><span class="ball9 ball9-blue"></span></div></td></tr>
                                <tr><td><div class="award-balls9"><span class="ball9 ball9-red"></span><span class="ball9 ball9-blue"></span><span class="ball9 ball9-blue"></span></div></td></tr>
                                <tr><td><div class="award-balls9"><span class="ball9 ball9-blue"></span><span class="ball9 ball9-blue"></span></div></td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->load->view('v1.1/elements/lottery/tabs', array('type' => 'cast')); ?>
                    <p class="count-down1 count-down">本期投注剩余：<?php if ($day) { ?><em><?php echo str_pad($day, 2, "0",
                            STR_PAD_LEFT) ?></em>天<?php } ?><em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
                                2, "0", STR_PAD_LEFT) ?></em>分<?php if (empty($day)) { ?><em><?php echo str_pad($sec, 2,
                            "0", STR_PAD_LEFT) ?></em>秒<?php } ?></p>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == FCSD): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-fcsd">
                <div class="lottery-img">
                    <svg width="320" height="320">
                        <image alt="<?php echo $cnName; ?>" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <p class="lottery-info-time">每晚21:15开奖</p><h1 class="lottery-info-name"><?php echo $cnName; ?></h1><p class="lottery-info-num"><span><b>第<i 
                    class="curr-issue"><?php echo $info['next']['issue'] ?></i>期</b></span><span>本期投注截止时间：<i class="end-time"><?php echo date('m-d H:i', $info['next']['seFsendtime'] / 1000) ?></i>（<i
                    class="week-day">星期<?php echo $arr[date("w", $info['next']['seFsendtime'] / 1000)] ?></i>）</span><span>合买截止：<i 
                    class="end-time"><?php echo date('m-d H:i', ($info['next']['seFsendtime'] / 1000 - $lotteryConfig[$lotteryId]['united_ahead'] * 60))?></i></span><span class="tips-s"><s class="tips-s-arrow"></s>每日开奖</span></p>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['fcsd']; ?>" class="no-bd-l" target="_blank"><i 
                        class="icon-font">&#xe612;</i>玩法介绍</a><a href="https://zoushi.166cai.cn/cjw3d/view/3d_danxuan.html" target="_blank" rel="nofollow"><i
                        class="icon-font">&#xe615;</i>基本走势</a><a href="javascript:;" target="_self" class="rule"><i class="icon-font">&#xe613;</i>中奖规则</a>
                        <div class="det_pop">
                            <div class="arr"></div>
                            <table width="254">
                                <colgroup><col width="50"><col width="134"><col width="68"></colgroup><thead><tr><th>等级</th><th>中奖条件</th><th>奖金分配</th></tr></thead>
                                <tbody>
                                    <tr><td>直选</td><td class="tal">与开奖号顺序一致</td><td><em>1040</em>元</td></tr>
                                    <tr><td>组选三</td><td class="tal">与开奖号一致但不定位</td><td><em>346</em>元</td></tr>
                                    <tr><td>组选六</td><td class="tal">与开奖号一致但不定位</td><td><em>173</em>元</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->load->view('v1.1/elements/lottery/tabs', array('type' => 'cast')); ?>
                    <p class="count-down1 count-down">本期投注剩余：<?php if ($day) { ?><em><?php echo str_pad($day, 2, "0",
                            STR_PAD_LEFT) ?></em>天<?php } ?><em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
                                2, "0", STR_PAD_LEFT) ?></em>分<?php if (empty($day)) { ?><em><?php echo str_pad($sec, 2,
                            "0", STR_PAD_LEFT) ?></em>秒<?php } ?></p>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == PLS): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-pls">
                <div class="lottery-img">
                    <svg width="320" height="320">
                        <image alt="<?php echo $cnName; ?>" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <p class="lottery-info-time">每晚20:30开奖</p><h1 class="lottery-info-name"><?php echo $cnName; ?></h1><p class="lottery-info-num"><span><b>第<i 
                    class="curr-issue"><?php echo $info['next']['issue'] ?></i>期</b></span><span>本期投注截止时间：<i class="end-time"><?php echo date('m-d H:i', $info['next']['seFsendtime'] / 1000) ?></i>（<i
                    class="week-day">星期<?php echo $arr[date("w", $info['next']['seFsendtime'] / 1000)] ?></i>）</span><span>合买截止：<i 
                    class="end-time"><?php echo date('m-d H:i', ($info['next']['seFsendtime'] / 1000 - $lotteryConfig[$lotteryId]['united_ahead'] * 60))?></i></span><span class="tips-s"><s class="tips-s-arrow"></s>每日开奖</span></p>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['pls']; ?>" class="no-bd-l" target="_blank"><i 
                        class="icon-font">&#xe612;</i>玩法介绍</a><a href="https://zoushi.166cai.cn/cjwpl3/view/pl3_danxuan.html" target="_blank" rel="nofollow"><i
                        class="icon-font">&#xe615;</i>基本走势</a><a href="javascript:;" target="_self" class="rule"><i class="icon-font">&#xe613;</i>中奖规则</a>
                        <div class="det_pop">
                            <div class="arr"></div>
                            <table width="254">
                                <colgroup><col width="50"><col width="134"><col width="68"></colgroup><thead><tr><th>等级</th><th>中奖条件</th><th>奖金分配</th></tr></thead>
                                <tbody>
                                    <tr><td>直选</td><td class="tal">与开奖号顺序一致</td><td><em>1040</em>元</td></tr>
                                    <tr><td>组选三</td><td class="tal">与开奖号一致但不定位</td><td><em>346</em>元</td></tr>
                                    <tr><td>组选六</td><td class="tal">与开奖号一致但不定位</td><td><em>173</em>元</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->load->view('v1.1/elements/lottery/tabs', array('type' => 'cast')); ?>
                    <p class="count-down1 count-down">本期投注剩余：<?php if ($day) { ?><em><?php echo str_pad($day, 2, "0",
                            STR_PAD_LEFT) ?></em>天<?php } ?><em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
                                2, "0", STR_PAD_LEFT) ?></em>分<?php if (empty($day)) { ?><em><?php echo str_pad($sec, 2,
                            "0", STR_PAD_LEFT) ?></em>秒<?php } ?></p>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == PLW): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-plw">
                <div class="lottery-img">
                    <svg width="320" height="320">
                        <image alt="<?php echo $cnName; ?>" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <p class="lottery-info-time">每晚20:30开奖</p><h1 class="lottery-info-name"><?php echo $cnName; ?></h1><p class="lottery-info-num"><span><b>第<i 
                    class="curr-issue"><?php echo $info['next']['issue'] ?></i>期</b></span><span>本期投注截止时间：<i class="end-time"><?php echo date('m-d H:i', $info['next']['seFsendtime'] / 1000) ?></i>（<i
                    class="week-day">星期<?php echo $arr[date("w", $info['next']['seFsendtime'] / 1000)] ?></i>）</span><span>合买截止：<i 
                    class="end-time"><?php echo date('m-d H:i', ($info['next']['seFsendtime'] / 1000 - $lotteryConfig[$lotteryId]['united_ahead'] * 60))?></i></span><span class="tips-s"><s class="tips-s-arrow"></s>每日开奖</span></p>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['plw']; ?>" class="no-bd-l" target="_blank"><i class="icon-font">&#xe612;</i>玩法介绍</a><a
                        href="https://zoushi.166cai.cn/cjwpl5/view/pl5_zst.html" target="_blank" rel="nofollow"><i class="icon-font">&#xe615;</i>基本走势</a><a href="javascript:;" target="_self" class="rule"><i 
                        class="icon-font">&#xe613;</i>中奖规则</a>
                        <div class="det_pop">
                            <div class="arr"></div>
                            <table width="254">
                                <colgroup><col width="50"><col width="134"><col width="68"></colgroup><thead><tr><th width="44">奖级</th><th width="116">中奖条件</th><th width="70">奖金分配</th></tr></thead>
                                <tbody><tr><td>直选</td><td class="tal">与开奖号顺序一致</td><td><span class="num-red">100000</span>元</td></tr></tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->load->view('v1.1/elements/lottery/tabs', array('type' => 'cast')); ?>
                    <p class="count-down1 count-down">本期投注剩余：<?php if ($day) { ?><em><?php echo str_pad($day, 2, "0",
                            STR_PAD_LEFT) ?></em>天<?php } ?><em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
                                2, "0", STR_PAD_LEFT) ?></em>分<?php if (empty($day)) { ?><em><?php echo str_pad($sec, 2,
                            "0", STR_PAD_LEFT) ?></em>秒<?php } ?></p>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == QXC): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-qxc">
                <div class="lottery-img">
                    <svg width="320" height="320">
                        <image alt="<?php echo $cnName; ?>" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <p class="lottery-info-time">每周二、五、日晚20:30开奖</p><h1 class="lottery-info-name"><?php echo $cnName; ?></h1><p class="lottery-info-num"><span><b>第<i 
                    class="curr-issue"><?php echo $info['next']['issue'] ?></i>期</b></span><span>本期投注截止时间：<i class="end-time"><?php echo date('m-d H:i', $info['next']['seFsendtime'] / 1000) ?></i>（<i
                    class="week-day">星期<?php echo $arr[date("w", $info['next']['seFsendtime'] / 1000)] ?></i>）</span><span>合买截止：<i 
                    class="end-time"><?php echo date('m-d H:i', ($info['next']['seFsendtime'] / 1000 - $lotteryConfig[$lotteryId]['united_ahead'] * 60))?></i></span><?php if (date('z') === date('z',
                    $info['next']['seFsendtime'] / 1000) && time() < $info['next']['seFsendtime'] / 1000) { ?><span class="tips-s"><s class="tips-s-arrow"></s>今日开奖</span><?php } ?></p>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a href="help/index/b5-s8" class="no-bd-l" target="_blank"><i class="icon-font">&#xe612;</i>玩法介绍</a><a href="https://zoushi.166cai.cn/cjw7xc/view/7xc_haoma.html#166" 
                        target="_blank" rel="nofollow"><i class="icon-font">&#xe615;</i>基本走势</a><a href="javascript:;" target="_self" class="rule"><i class="icon-font">&#xe613;</i>中奖规则</a>
                        <div class="det_pop">
                            <div class="arr"></div>
                            <table width="310">
                                <colgroup><col width="50"><col width="170"><col width="87"></colgroup><thead><tr><th>奖级</th><th>中奖条件</th><th>奖金分配</th></tr></thead>
                                <tbody>
                                    <tr><td>一等奖</td><td><p class="tal p5">投注号码与开奖号码全部相符且顺序一致</p></td><td>最高<span class="num-red">500万</span>元</td></tr>
                                    <tr><td>二等奖</td><td><p class="tal p5">连续6位号码与开奖号码相同位置的连续6位号码相同</p></td><td>浮动</td></tr>
                                    <tr><td>三等奖</td><td><p class="tal p5">连续5位号码与开奖号码相同位置的连续5位号码相同</p></td><td><span class="num-red">1800</span>元</td></tr>
                                    <tr><td>四等奖</td><td><p class="tal p5">连续4位号码与开奖号码相同位置的连续4位号码相同</p></td><td><span class="num-red">300</span>元</td></tr>
                                    <tr><td>五等奖</td><td><p class="tal p5">连续3位号码与开奖号码相同位置的连续3位号码相同</p></td><td><span class="num-red">20</span>元</td></tr>
                                    <tr><td>六等奖</td><td><p class="tal p5">连续2位号码与开奖号码相同位置的连续2位号码相同</p></td><td><span class="num-red">5</span>元</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->load->view('v1.1/elements/lottery/tabs', array('type' => 'cast')); ?>
                    <p class="count-down1 count-down">本期投注剩余：<?php if ($day) { ?><em><?php echo str_pad($day, 2, "0",
                            STR_PAD_LEFT) ?></em>天<?php } ?><em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
                                2, "0", STR_PAD_LEFT) ?></em>分<?php if (empty($day)) { ?><em><?php echo str_pad($sec, 2,
                            "0", STR_PAD_LEFT) ?></em>秒<?php } ?></p>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == QLC): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-qlc">
                <div class="lottery-img">
                    <svg width="320" height="320">
                        <image alt="<?php echo $cnName; ?>" xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                    </svg>
                </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <p class="lottery-info-time">每周一、三、五晚21:15开奖</p><h1 class="lottery-info-name"><?php echo $cnName; ?></h1><p class="lottery-info-num"><span><b>第<i 
                    class="curr-issue"><?php echo $info['next']['issue'] ?></i>期</b></span><span>本期投注截止时间：<i class="end-time"><?php echo date('m-d H:i', $info['next']['seFsendtime'] / 1000) ?></i>（<i
                    class="week-day">星期<?php echo $arr[date("w", $info['next']['seFsendtime'] / 1000)] ?></i>）</span><span>合买截止：<i 
                    class="end-time"><?php echo date('m-d H:i', ($info['next']['seFsendtime'] / 1000 - $lotteryConfig[$lotteryId]['united_ahead'] * 60))?></i></span><?php if (date('z') === date('z',
                    $info['next']['seFsendtime'] / 1000) && time() < $info['next']['seFsendtime'] / 1000) { ?><span class="tips-s"><s class="tips-s-arrow"></s>今日开奖</span><?php } ?></p>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a href="help/index/b5-s7" class="no-bd-l" target="_blank"><i class="icon-font">&#xe612;</i>玩法介绍</a><a href="https://zoushi.166cai.cn/cjwqlc/view/qlcjiben.html#166" 
                        target="_blank" rel="nofollow"><i class="icon-font">&#xe615;</i>基本走势</a><a href="javascript:;" target="_self" class="rule"><i class="icon-font">&#xe613;</i>中奖规则</a>
                        <div class="det_pop">
                            <div class="arr"></div>
                            <table width="240">
                                <colgroup><col width="50"><col width="118"><col width="68"></colgroup><thead><tr><th>奖级</th><th>中奖条件</th><th>奖金分配</th></tr></thead>
                                <tbody>
                                <tr><td>一等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 7; $i++) {?><span class="ball9 ball9-red"></span><?php }?></div></td><td>浮动</td></tr>
                                <tr><td>二等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 6; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span></div></td><td>浮动</td></tr>
                                <tr><td>三等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 6; $i++) {?><span class="ball9 ball9-red"></span><?php }?></div></td><td>浮动</td></tr>
                                <tr><td>四等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 5; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span></div></td><td>200元</td></tr>
                                <tr><td>五等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 5; $i++) {?><span class="ball9 ball9-red"></span><?php }?></div></td><td>50元</td></tr>
                                <tr><td>六等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 4; $i++) {?><span class="ball9 ball9-red"></span><?php }?><span class="ball9 ball9-blue"></span></div></td><td>10元</td></tr>
                                <tr><td>七等奖</td><td><div class="award-balls9"><?php for ($i = 0; $i < 4; $i++) {?><span class="ball9 ball9-red"></span><?php }?></div></td><td>5元</td></tr>
                                <tr>
                                    <td colspan="3" class="tal" style="text-align: left">
                                        <p style="width: 226px;"><span class="num-red">特别号码（蓝色球）说明：</span>特别号码仅做为二、四、六等奖的使用，即开出7个奖号后再从23个号码里面随机摇出一个就是特别号，只要跟你买的7个号码中的任意一个号码相符，就算中特别号。</p>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php $this->load->view('v1.1/elements/lottery/tabs', array('type' => 'cast')); ?>
                    <p class="count-down1 count-down">本期投注剩余：<?php if ($day) { ?><em><?php echo str_pad($day, 2, "0",
                            STR_PAD_LEFT) ?></em>天<?php } ?><em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
                                2, "0", STR_PAD_LEFT) ?></em>分<?php if (empty($day)) { ?><em><?php echo str_pad($sec, 2,
                            "0", STR_PAD_LEFT) ?></em>秒<?php } ?></p>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="lotteryTitImg"><img
                src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/cp-logo/cp-' . strtolower($enName) . '.png'); ?>"
                alt="<?php echo $cnName; ?>" width="75" height="75"/></div>
        <div class="lottery-info">
            <h1 class="lottery-info-name"><?php echo $cnName; ?></h1>

            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <p class="lottery-info-time">
                        <?php if ($lotteryId == SSQ): ?>
                            每周二、四、日晚21:15开奖
                        <?php elseif ($lotteryId == DLT): ?>
                            每周一、三、六晚20:30开奖
                        <?php endif; ?>
                    </p>

                    <p><span>第<b class="curr-issue"></b>期</span><span>投注截止时间：<b class="end-time"></b>（<b
                                class="week-day"></b>）</span></p>
                </div>
            </div>
        </div>
        <!-- <div class="fr last-award">
            <p>上期[第<em class="last-issue"></em>期]： <span class="award-nums"></span>  <a href="<?php echo $baseUrl; ?>awards/number/<?php echo $lotteryId; ?>">更多&gt;&gt;</a></p>
        </div> -->
    <?php endif; ?>
</div>
<script>
function formatDateTime(inputTime) 
{    
    var date = new Date(inputTime);  
    var y = date.getFullYear();    
    var m = date.getMonth() + 1;    
    m = m < 10 ? ('0' + m) : m;    
    var d = date.getDate();    
    d = d < 10 ? ('0' + d) : d;    
    var h = date.getHours();  
    h = h < 10 ? ('0' + h) : h;  
    var minute = date.getMinutes();  
    var second = date.getSeconds();  
    minute = minute < 10 ? ('0' + minute) : minute;    
    second = second < 10 ? ('0' + second) : second;   
    return y + '-' + m + '-' + d+' '+h+':'+minute+':'+second;    
}; 
<?php if (in_array($enName, array('ssq', 'dlt', 'fcsd', 'pls', 'plw', 'qlc', 'qxc'))) {?>
    var time = <?php echo $time;?>,rest = <?php echo $time;?>,cycle = 1000,interval,cont = 0;
    $(function () {
        interval = setInterval("fun()", cycle);
    })
    function padd(num) {
        return ('0' + num).slice(-2);
    }
    function isnull(exp, num) {
        if (!exp || typeof(exp) === "undefined" || exp === 0) {
            return true;
        }
        if (num && isNaN(parseInt(exp, 10))) {
            return true;
        }
        return false;
    }
    function fun() {
        if (time < rest - 5 && $(".jiezhi").length > 0) {
            $(".jiezhi").parents('.pop-alert').remove();
            cx.Mask.hide();
        }
        if (time <= 0) {
            cont++;
            if ($(".count-down1 em").length > 0 && cont <= 10) {
                $.ajax({
                    url: "/source/cache/issue_<?php echo $enName?>.html?"+Math.floor(Math.random()*10000),
                    dataType: 'json',
                    beforeSend: function () {
                        cycle *= 2;
                        clearInterval(interval);
                        interval = setInterval("fun()", cycle);
                    },
                    success: function (data) {
                        if (isnull(data, false) || isnull(data.issue, true) || isnull(data.seFsendtime, true)) {
                            clearInterval(interval);
                            cycle *= 2;
                            interval = setInterval("fun()", cycle);
                        } else {
                            if (time <= 0 && data.restTime > 0) {
                                var tbstr = '', num, j = 0, maxissue, issues = [], multi = parseInt(($(".chase-number-table-hd .follow-multi:first").val() || cx._basket_.multiModifier.value), 10);
                                $(".curr-issue").html(data.issue);
                                date = new Date(data.seFsendtime);
                                cx._basket_.setIssue(data.issue);
                                ENDTIME = date.getFullYear()+"-"+padd(date.getMonth() + 1)+"-"+padd(date.getDate())+" "+padd(date.getHours())+":"+padd(date.getMinutes())+":"+padd(date.getSeconds());
                                CUR_ISSUE = data.issue;
                                realendTime = data.realendTime;
                                hmendTime = data.hmendTime;
                                var __JJTIME = formatDateTime(hmendTime*1000)
                                $('.hmendTime span').html(__JJTIME);
                                 if($('._gc_buy').hasClass('btn-disabled'))
                                {
                                    $('._submit').addClass('btn-disabled').html($('._gc_buy').html());
                                }else{
                                   $('._submit').html('确认预约').removeClass('btn-disabled'); 
                                }
                                $('._JRJbox').html('<span class="num-red">（该期次处于预售期，暂不支持销售）</span>');
                                //换期次处理结束
                                $(".pub-pop").remove();
                                cx.Alert({content:"<p class='jiezhi tal'>您好，第<span class='num-red'>"+<?php echo strtoupper($enName)?>_ISSUE+"</span>期方案已超过本期投注截<br>止时间，请尝试购买下一期次。</p>"});
                                chases = data.chases;
                                if (cx._basket_.chases[<?php echo strtoupper($enName)?>_ISSUE]) {
                                    cx._basket_.chaseMulti -= cx._basket_.chases[<?php echo strtoupper($enName)?>_ISSUE].multi;
                                    delete cx._basket_.chases[<?php echo strtoupper($enName)?>_ISSUE];
                                }else {
                                    cx._basket_.chaseLength++;
                                }
                                
                                for (i in cx._basket_.chases) {
                                    if (chases[i] === undefined) {
                                        cx._basket_.chaseMulti -= cx._basket_.chases[i].multi;
                                        delete cx._basket_.chases[i];
                                    }else {
                                        maxissue = i;
                                    }
                                }
                                for (i in chases) {
                                    if (j < cx._basket_.chaseLength) {
                                        if (cx._basket_.chases[i]) {//用户选了这期
                                            cx._basket_.setChaseByI(i);//重置下award_time,end_time
                                            j++;
                                        }else if (i > maxissue) {//用户没选这期，但是需要补这期
                                            cx._basket_.setChaseByI(i);
                                            cx._basket_.chases[i].multi = multi;
                                            <?php if (in_array($enName, array('ssq', 'dlt'))) {?>
                                            cx._basket_.chases[i].money = multi * cx._basket_.betMoney;
                                            <?php }else {?>
                                            cx._basket_.chases[i].money = multi * cx._basket_._betMoney * cx._basket_.betNum;
                                            <?php }?>
                                            cx._basket_.chaseMulti += multi;
                                            j++;
                                        }
                                        issues.push(i);
                                    }else {
                                        break;
                                    }
                                }
                                <?php if (in_array($enName, array('ssq', 'dlt'))) {?>
                                cx._basket_.chaseMoney = cx._basket_.chaseMulti * cx._basket_.betMoney;
                                <?php }else {?>
                                cx._basket_.chaseMoney = cx._basket_.chaseMulti * cx._basket_._betMoney * cx._basket_.betNum;
                                <?php }?>
                                cx._basket_.renderChase(issues);
                                $('.chase-number-table-hd .follow-issue').val(cx._basket_.chaseLength);
                                $(".chase-number-table-ft .fbig em:first").html(cx._basket_.chaseLength);
                                $(".chase-number-table-ft .fbig em:last").html(cx._basket_.chaseMoney);
                                
                                $(".end-time").html((padd(date.getMonth() + 1)) + "-" + padd(date.getDate()) + " " + padd(date.getHours()) + ":" + padd(date.getMinutes()));
                                //修正合买时间====lkj
                                $('.end-time').eq(1).html(__JJTIME.substring(5,16));
                                $(".week-day").html('星期' + ['日', '一', '二', '三', '四', '五', '六'][date.getDay()]);
                                var now = new Date();
                                var weekday = now.getDay();
                                if ("<?php echo $enName?>" !== 'pls' && "<?php echo $enName?>" !== 'fcsd' && "<?php echo $enName?>" !== 'plw') {
                                    if (date.getMonth() === now.getMonth() && date.getDate() === now.getDate() && now.getTime() < data.seFsendtime) {
                                        if ($(".lottery-info-num").has('span.tips-s').length > 0) {
                                            $(".tips-s-arrow").html('今日开奖');
                                        } else {
                                            $(".lottery-info-num").append('<span class="tips-s"><s class="tips-s-arrow">今日开奖</s>今日开奖</span>');
                                        }
                                    } else {
                                        $(".tips-s").remove();
                                    }
                                }
                                time = data.restTime;
                                rest = data.restTime;
                                
                                if (time >= 86400) {
                                    str = "本期投注剩余：<em>" + padd(Math.floor(time / 86400)) + "</em>天<em>" + padd(Math.floor((time % 86400) / 3600)) + "</em>小时<em>" + padd(Math.floor((time % 3600) / 60)) + "</em>分";
                                } else {
                                    str = "本期投注剩余：<em>" + padd(Math.floor(time / 3600)) + "</em>小时<em>" + padd(Math.floor((time % 3600) / 60)) + "</em>分<em>" + padd(Math.floor((time % 3600) % 60)) + "</em>秒";
                                }
                                $(".count-down1").html(str);
                            }
                            
                            clearInterval(interval);
                            cycle = 1000;
                            interval = setInterval("fun()", cycle);
                        }
                    },
                    error: function () {
                        $(".count-down1").html('期次更新异常，请尝试刷新页面。');
                    }
                });
            }
        } else {
            cont = 0;
            time--;
            if (time >= 0) {
                var d = Math.floor(time / 86400);
                var h = Math.floor((time % 86400) / 3600);
                var m = Math.floor((time % 3600) / 60);
                var s = Math.floor((time % 3600) % 60);
                if (d > 0) {
                    str = "本期投注剩余：<em>" + padd(d) + "</em>天<em>" + padd(h) + "</em>小时<em>" + padd(m) + "</em>分";
                } else {
                    str = "本期投注剩余：<em>" + padd(h) + "</em>小时<em>" + padd(m) + "</em>分<em>" + padd(s) + "</em>秒";
                }
                $(".count-down1").html(str);
            }
        }
    }
<?php }?>
</script>
