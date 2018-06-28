<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-custom.min.css');?>" rel="stylesheet" />
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
<?php $arr = array("日", "一", "二", "三", "四", "五", "六");
$time = $info['next']['seFsendtime'] / 1000 - time();
$day = floor($time / 86400);
$hour = floor(($time % 86400) / 3600);
$min = floor(($time % 3600) / 60);
$sec = ($time % 3600) % 60; ?>
<div class="lotteryTit issue cp-box-hd">
    <?php
    /* 帮助中心 - 配置文件 --- @Author liusijia --- start --- */
    $this->config->load('help'); //@Author liusijia
    $help_center_rule = $this->config->item('help_center_rule');
    $lottery_type = $this->config->item('lottery_type');
    /* 帮助中心 - 配置文件 --- @Author liusijia --- end --- */
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
                    <h1 class="lottery-info-name"><?php if ($lotteryId == JXSYXW) {?>新11选5<?php }elseif ($lotteryId == HBSYXW) {?>惊喜11选5<?php }elseif ($lotteryId == GDSYXW) {?>乐11选5<?php }else {?>老11选5<?php }?></h1>
                    <p class="lottery-info-num"><span><b>第<i class="curr-issue"><?php echo $info['next']['issue'] ?></i>期</b></span></p>
                </div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a class="no-bd-l" href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type[$enName]; ?>" target="_blank"><i class="icon-font">&#xe60f;</i>玩法介绍</a><a href="/mylottery/betlog" target="_blank"><i class="icon-font">&#xe60c;</i>投注记录</a><a class="rule" href="javascript:;"><i class="icon-font">&#xe60d;</i>中奖规则</a>

                        <div class="det_pop" style="width: 660px;">
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
                    </div>
                    <ul class="bet-type-link"><li class="selected"><a href="/<?php echo $enName?>">普通投注</a></li></ul>
                    <p class="count-down">10分钟开奖更好玩</p>
                </div>
            </div>
    </div>
    <?php elseif (in_array($lotteryId, array(KS, JXKS))): ?>
    <div class="lottery-info">
        <div class="lottery-info-img lottery-ks">
          <div class="lottery-img">
	           <svg width="320" height="320">
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
	           </svg>
	       </div>
        </div>
        <div class="lottery-info-txt">
                <div class="lottery-info-top"><h1 class="lottery-info-name"><?php echo $cnName?></h1><p class="lottery-info-num"><span><b>第<i class="curr-issue"><?php echo substr($info['next']['issue'], 2) ?></i>期</b></span></p></div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a class="no-bd-l" href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type[$enName]; ?>" target="_blank"><i class="icon-font">&#xe60f;</i>玩法介绍</a><a href="/mylottery/betlog" target="_blank"><i class="icon-font">&#xe60c;</i>投注记录</a>
                    </div>
                    <ul class="bet-type-link"><li class="selected"><a href="/<?php echo $enName?>">普通投注</a></li></ul><p class="count-down">10分钟开奖更好玩</p>
                </div>
            </div>
    </div>
    <?php elseif (in_array($lotteryId, array(JLKS))): ?>
    <div class="lottery-info">
        <div class="lottery-info-img lottery-<?php echo $enName?>">
          <div class="lottery-img">
	           <svg width="320" height="320">
					<img src="<?php echo getStaticFile('/caipiaoimg/v1.1/images/'.$enName.'.png'); ?>" srcset="<?php echo getStaticFile('/caipiaoimg/v1.1/images/'.$enName.'.svg'); ?> 2x" width="80" height="80" alt="">
				</svg>
	       </div>
        </div>
        <div class="lottery-info-txt">
                <div class="lottery-info-top"><h1 class="lottery-info-name"><?php echo $cnName?></h1><p class="lottery-info-num"><span><b>第<i class="curr-issue"><?php echo substr($info['next']['issue'], 2) ?></i>期</b></span></p></div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a class="no-bd-l" href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type[$enName]; ?>" target="_blank"><i class="icon-font">&#xe60f;</i>玩法介绍</a><a href="/mylottery/betlog" target="_blank"><i class="icon-font">&#xe60c;</i>投注记录</a>
                    </div>
                    <ul class="bet-type-link"><li class="selected"><a href="/<?php echo $enName?>">普通投注</a></li></ul><p class="count-down">10分钟开奖更好玩</p>
                </div>
            </div>
    </div>
    <?php elseif (in_array($lotteryId, array(KLPK))): ?>
    <div class="lottery-info">
        <div class="lottery-info-img lottery-klpk">
          <div class="lottery-img">
	           <svg width="320" height="320">
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
	           </svg>
	       </div>
        </div>
        <div class="lottery-info-txt">
                <div class="lottery-info-top"><h1 class="lottery-info-name">快乐扑克</h1><p class="lottery-info-num"><span><b>第<i class="curr-issue"><?php echo substr($info['next']['issue'], 2) ?></i>期</b></span></p></div>
                <div class="lottery-info-bt">
                    <div class="lnk-list">
                        <a class="no-bd-l" href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type[$enName]; ?>" target="_blank"><i class="icon-font">&#xe60f;</i>玩法介绍</a><a href="/mylottery/betlog" target="_blank"><i class="icon-font">&#xe60c;</i>投注记录</a>
                    </div>
                    <ul class="bet-type-link"><li class="selected"><a href="/<?php echo $enName?>">普通投注</a></li></ul><p class="count-down">10分钟开奖更好玩</p>
                </div>
            </div>
    </div>
    <?php elseif ($lotteryId == JCZQ): ?>
    <div class="lottery-info">
        <div class="lottery-info-img lottery-jczq">
            <div class="lottery-img">
	           <svg width="320" height="320">
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
	           </svg>
	       </div>
        </div>
        <div class="lottery-info-txt">
            <div class="lottery-info-top">
                <p class="lottery-info-time tar">竞猜为全场90分钟（含伤停补时）的比分结果，不含加时及点球大战<br>自购截止：赛前<em><?php echo $lotteryConfig[JCZQ]['ahead']; ?></em>分钟，合买截止：赛前<em><?php echo $lotteryConfig[JCZQ]['ahead']+$lotteryConfig[JCZQ]['united_ahead']; ?></em>分钟</p>
                <h1 class="lottery-info-name">竞彩足球</h1>
            </div>
            <div class="lottery-info-bt">
                <?php if ($this->con != 'awards'): ?>
                    <div class="lnk-list">
                    	<a rel="nofollow" href="<?php echo $this->config->item('api_bf');?>jingcai" class="no-bd-l" target="_blank">比分直播</a>
                        <a rel="nofollow" href="<?php echo $this->config->item('api_info');?>" class="no-bd-l" target="_blank">资料库</a>
                        <a href="/kaijiang/jczq" class="no-bd-l" target="_blank">赛果信息</a>
                    </div>
                <?php endif; ?>
                <?php $this->load->view('v1.1/elements/lottery/tabs'); ?>
            </div>
        </div>
    </div>
    <?php elseif ($lotteryId == JCLQ): ?>
    <div class="lottery-info">
        <div class="lottery-info-img lottery-jclq">
            <div class="lottery-img">
	           <svg width="320" height="320">
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
	           </svg>
	       </div>
        </div>
        <div class="lottery-info-txt">
            <div class="lottery-info-top">
                <p class="lottery-info-time tar">69%超高返奖率，竞彩全场结果（包括加时赛）<br>自购截止：赛前<em><?php echo $lotteryConfig[JCLQ]['ahead']; ?></em>分钟，合买截止：赛前<em><?php echo $lotteryConfig[JCLQ]['ahead']+$lotteryConfig[JCLQ]['united_ahead']; ?></em>分钟</p>
                <h1 class="lottery-info-name">竞彩篮球</h1>
            </div>
            <div class="lottery-info-bt">
                <?php if ($this->con != 'awards'): ?>
                    <div class="lnk-list">
                    	<a rel="nofollow" href="<?php echo $this->config->item('api_bf');?>jingcai" class="no-bd-l" target="_blank">比分直播</a>
                        <a rel="nofollow" href="<?php echo $this->config->item('api_info');?>" class="no-bd-l" target="_blank">资料库</a>
                        <a href="/kaijiang/jclq" class="no-bd-l" target="_blank">赛果信息</a>
                    </div>
                <?php endif; ?>
                <?php $this->load->view('v1.1/elements/lottery/tabs'); ?>
            </div>
        </div>
    </div>
    <?php elseif ($lotteryId == SFC): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-sfc">
                <div class="lottery-img">
	           <svg width="320" height="320">
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
	           </svg>
	       </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <div class="lottery-info-time">
                        <a href="/kaijiang/sfc" class="fr">详情</a>第<?php echo $info['current']['issue']; ?>期 开奖结果
                        <div class="num-group">
                            <?php $awardNums = explode(',', $info['current']['awardNum']);if ( ! empty($awardNums)): ?><span><?php echo implode('</span><span>', $awardNums) ?></span><?php endif; ?>
                        </div>
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
                    <?php $this->load->view('v1.1/elements/lottery/tabs'); ?>
                    <p class="count-down1 count-down"></p>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == RJ): ?>
        <div class="lottery-info">
            <div class="lottery-info-img lottery-rj">
               <div class="lottery-img">
	           <svg width="320" height="320">
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
	           </svg>
	       </div>
            </div>
            <div class="lottery-info-txt">
                <div class="lottery-info-top">
                    <div class="lottery-info-time">
                        <a href="/kaijiang/rj" class="fr">详情</a>第<?php echo $info['current']['issue']; ?>期 开奖结果
                        <div class="num-group">
                            <?php $awardNums = explode(',', $info['current']['awardNum']); if ( ! empty($awardNums)): ?><span><?php echo implode('</span><span>', $awardNums) ?></span><?php endif; ?>
                        </div>
                    </div>

                    <h1 class="lottery-info-name">任选九</h1>

                    <p class="lottery-info-num"><span><b>第<i
                                    class="curr-issue"><?php echo $info['next']['issue'] ?></i>期</b></span><span>本期投注截止时间：<i
                                class="end-time"><?php echo date('m-d H:i', $info['next']['seFsendtime'] / 1000) ?></i>（<i
                                class="week-day">星期<?php 
                                echo $arr[date("w",
                                    $info['next']['seFsendtime'] / 1000)] ?></i>）</span><?php if (date('z') === date('z',
                            $info['next']['seFsendtime'] / 1000) && time() < $info['next']['seFsendtime'] / 1000
                        ) ?></p>
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
                    <?php $this->load->view('v1.1/elements/lottery/tabs'); ?>
                    <p class="count-down1 count-down"></p>
                </div>
            </div>
        </div>
    <?php elseif ($lotteryId == SSQ): ?>
    <div class="lottery-info">
        <div class="lottery-info-img lottery-ssq">
            <div class="lottery-img">
	           <svg width="320" height="320">
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
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
                <p class="count-down1 count-down">本期投注剩余：<?php if ($day) { ?><em><?php echo str_pad($day, 2, "0",
                        STR_PAD_LEFT) ?></em>天<?php } ?>
                    <em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
                        2, "0", STR_PAD_LEFT) ?></em>分<?php if (empty($day)) { ?><em><?php echo str_pad($sec, 2,
                        "0", STR_PAD_LEFT) ?></em>秒<?php } ?></p>
            </div>
        </div>
    </div>
    <?php elseif ($lotteryId == DLT): ?>
    <div class="lottery-info">
        <div class="lottery-info-img lottery-dlt">
            <div class="lottery-img">
	           <svg width="320" height="320">
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
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
    <!-- <div class="fr last-award">
                <p>上一期第<em class="last-issue"></em>期     开奖结果<a href="<?php echo $baseUrl; ?>kaijiang/dlt" target="_blank" class="kj-more">详情</a></p>
                <div class="award-nums"></div>
            </div> -->
    <?php elseif ($lotteryId == FCSD): ?>
    <div class="lottery-info">
        <div class="lottery-info-img lottery-fcsd">
            <div class="lottery-img">
	           <svg width="320" height="320">
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
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
                        STR_PAD_LEFT) ?></em>天<?php } ?>
                    <em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
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
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
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
                        STR_PAD_LEFT) ?></em>天<?php } ?>
                    <em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
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
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
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
                        STR_PAD_LEFT) ?></em>天<?php } ?>
                    <em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
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
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
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
                        STR_PAD_LEFT) ?></em>天<?php } ?>
                    <em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
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
					<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
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
                        STR_PAD_LEFT) ?></em>天<?php } ?>
                    <em><?php echo str_pad($hour, 2, "0", STR_PAD_LEFT) ?></em>小时<em><?php echo str_pad($min,
                        2, "0", STR_PAD_LEFT) ?></em>分<?php if (empty($day)) { ?><em><?php echo str_pad($sec, 2,
                        "0", STR_PAD_LEFT) ?></em>秒<?php } ?></p>
            </div>
        </div>
    </div>
    <?php elseif ($lotteryId == GJ): ?>
    <div class="lottery-info">
        <div class="lottery-info-img"><div class="lottery-img"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/gjc.png'); ?>" width="80" height="80"></div></div>
        <div class="lottery-info-txt">
            <div class="lottery-info-top"><h1 class="lottery-info-name">冠军彩</h1></div>
            <div class="lottery-info-bt">
                <div class="lnk-list">
                   	<a rel="nofollow" href="<?php echo $this->config->item('api_bf');?>jingcai" class="no-bd-l" target="_blank">比分直播</a>
                    <a rel="nofollow" href="<?php echo $this->config->item('api_info');?>" class="no-bd-l" target="_blank">资料库</a>
                </div>
                <?php $this->load->view('v1.1/elements/lottery/tabs'); ?>
            </div>
        </div>
    </div>
    <?php elseif ($lotteryId == GYJ): ?>
    <div class="lottery-info">
        <div class="lottery-info-img"><div class="lottery-img"><img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/gyj.png'); ?>" width="80" height="80"></div></div>
        <div class="lottery-info-txt">
            <div class="lottery-info-top"><h1 class="lottery-info-name">冠亚军彩</h1></div>
            <div class="lottery-info-bt">
                <div class="lnk-list">
                   	<a rel="nofollow" href="<?php echo $this->config->item('api_bf');?>jingcai" class="no-bd-l" target="_blank">比分直播</a>
                    <a rel="nofollow" href="<?php echo $this->config->item('api_info');?>" class="no-bd-l" target="_blank">资料库</a>
                </div>
                <?php $this->load->view('v1.1/elements/lottery/tabs'); ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<script>
<?php if ($enName !== 'syxw' && $enName !== 'jxsyxw') {?>
	var time = <?php echo $time;?>;
	var cycle = 1000;
	var interval;
	$(function () {
	    interval = setInterval("fun()", cycle);
	})
	function padd(num) {
	    return ('0' + num).slice(-2);
	}
	function isnull(exp) {
	    if (!exp && typeof(exp) != "undefined" && exp != 0) {
	        return true;
	    }
	    return false;
	}
	function fun() {
	    if (time <= 0) {
	        if ($(".count-down1 em").length > 0) {
	            $.ajax({
	                url: "/ajax/getIssue/<?php echo $enName?>",
	                dataType: 'json',
	                beforeSend: function () {
	                    cycle *= 5;
	                    clearInterval(interval);
	                    interval = setInterval("fun()", cycle);
	                },
	                success: function (data) {
	                    if (isnull(data) || isnull(data.issue) || isnull(data.seFsendtime)) {
	                        clearInterval(interval);
	                        cycle *= 2;
	                        interval = setInterval("fun()", cycle);
	                    } else {
	                        $(".curr-issue").html(data.issue);
	                        date = new Date(data.seFsendtime);
	                        $(".end-time").html((padd(date.getMonth() + 1)) + "-" + padd(date.getDate()) + " " + padd(date.getHours()) + ":" + padd(date.getMinutes()));
	                        $(".week-day").html('星期' + ['日', '一', '二', '三', '四', '五', '六'][date.getDay()]);
	                        var now = new Date();
	                        var weekday = now.getDay();
	                        if ("<?php echo $enName?>" !== 'pls' && "<?php echo $enName?>" !== 'fcsd' && "<?php echo $enName?>" !== 'plw' && "<?php echo $enName?>" !== 'syxw') {
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
	                        time = (data.seFsendtime - now.getTime()) / 1000;
	                        if (time >= 86400) {
	                            str = "本期投注剩余：<em>" + padd(Math.floor(time / 86400)) + "</em>天<em>" + padd(Math.floor((time % 86400) / 3600)) + "</em>小时<em>" + padd(Math.floor((time % 3600) / 60)) + "</em>分";
	                        } else {
	                        	<?php if($lotteryId == SYXW):?>
	                        	str = "本期投注剩余：<em>" + padd(Math.floor((time % 3600) / 60)) + "</em>分<em>" + padd(Math.floor((time % 3600) % 60)) + "</em>秒";
	                        	<?php else:?>
	                        	str = "本期投注剩余：<em>" + padd(Math.floor(time / 3600)) + "</em>小时<em>" + padd(Math.floor((time % 3600) / 60)) + "</em>分<em>" + padd(Math.floor((time % 3600) % 60)) + "</em>秒";
	                        	<?php endif;?>
	                        }
	                        $(".count-down1").html(str);
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
	        time--;
	        var d = Math.floor(time / 86400);
	        var h = Math.floor((time % 86400) / 3600);
	        var m = Math.floor((time % 3600) / 60);
	        var s = Math.floor((time % 3600) % 60);
	        if (d > 0) {
	            str = "本期投注剩余：<em>" + padd(d) + "</em>天<em>" + padd(h) + "</em>小时<em>" + padd(m) + "</em>分";
	        } else {
	            <?php if($lotteryId == SYXW):?>
	            str = "本期投注剩余：<em>" + padd(m) + "</em>分<em>" + padd(s) + "</em>秒";
	            <?php else:?>
	            str = "本期投注剩余：<em>" + padd(h) + "</em>小时<em>" + padd(m) + "</em>分<em>" + padd(s) + "</em>秒";
	            <?php endif;?>
	        }
	        $(".count-down1").html(str);
	    }
	}
<?php } ?>
<?php if (in_array($enName, array('ssq', 'dlt'))) {?>
$(function(){
	$('.jiangjinCalculate').click(function(e){
		var self = $(this);
		
		self.removeClass('jiangjinCalculate');
	    $.ajax({
	        type: 'post',
	        <?php if ($enName == 'ssq') {?>
	        url:  '/pop/jiangjinCalculate',
	        <?php }else {?>
	        url:  '/pop/dltjiangjinCalculate',
			<?php }?>
	        data: {version:version},
	        success: function(response) {
	            $('body').append(response);
	            cx.PopCom.show('.pop-jsq');
	            cx.PopCom.close('.pop-jsq');
	            
	            // 奖金计算器弹窗
	            var popJsq= $('.pop-jsq');
	            var windowHeight = $(window).height();
	            var docHeight = $('.pop-mask').height();	
	            var ie6=!-[1,]&&!window.XMLHttpRequest;
	            
	            popJsq.css({
	        		'position': 'absolute',
	    			'top': $(window).scrollTop() + $(window).height()/2 - $('.pop-jsq').outerHeight()/2 + 'px',
	    			'margin-top': 0
	    		})
	    		
	    		
	            if (!self.hasClass('jiangjinCalculate')) {
	        		self.addClass('jiangjinCalculate');
	        	}
	        },
	        error: function() {
	        	if (!self.hasClass('jiangjinCalculate')) {
	        		self.addClass('jiangjinCalculate');
	        	}
	        }
	    });
	    e.stopImmediatePropagation();
	    e.preventDefault();
	});
})

<?php }?>
</script>
