<?php 
if(!$lotteryConfig[KLPK]['status']) {
	$selling = 0;
}elseif (time() > floor($info['next']['seFsendtime']/1000) && time() < floor($info['next']['seFsendtime']/1000)) {
	$selling = 1;
}else {
	$selling = 2;
	if ($lotteryConfig['united_status']) {
		$hmselling = 1;
	}
}?>
<!DOCTYPE HTML>
<!--[if lt IE 8 ]><html class="ie7"><![endif]-->
<!--[if IE 8 ]><html class="ie8"><![endif]-->
<!--[if IE 9 ]><html class="ie9"><![endif]-->
<!--[if IE 10 ]><html class="ie10"><![endif]-->
<!--[if (gt IE 10)|!(IE)]><!-->
<html>
<!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>快乐扑克开奖结果-快乐扑克走势图-快乐扑克玩法-166彩票网</title>
    <meta content="166彩票网提供快乐扑克彩票购买，快乐扑克走势图，快乐扑克玩法等彩票购买服务。166彩票100%安全预约购彩平台！" name="Description" />
    <meta content="快乐扑克开奖结果，快乐扑克开奖结果，快乐扑克走势图，快乐扑克玩法，166彩票官网" name="Keywords" />
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-public.min.css');?>">
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
    <script>
    var ISSUE = "<?php echo $info['cIssue']['seExpect']?>", tm = <?php echo $info['cIssue']['seFsendtime']/1000-time();?>,
    atm = <?php echo $info['nlIssue']['awardTime']/1000-time();?>, hsty = eval(<?php echo json_encode($history)?>), mall = eval(<?php echo json_encode($mall)?>),
    ENDTIME = "<?php echo date('Y-m-d H:i:s', $info['cIssue']['seFsendtime']/1000)?>", vJson = [<?php echo $awardNum?>], version = 'v1.1', enName = '<?php echo $enName?>',
    baseUrl = '<?php echo $this->config->item('base_url'); ?>', chases = $.parseJSON('<?php echo json_encode($chases)?>'), chaselength = <?php echo $chaselength?>,
    selling = <?php echo $selling?>, MULTI = <?php echo (int)$multi?> || 1;
    </script>
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js');?>" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/order.min.js');?>"></script>
    <script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.2/js/klpk.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.2/js/gaopin.min.js');?>" type="text/javascript"></script>
</head>

<body>
    <!--top begin-->
	<?php if (empty($this->uid)): ?>
	    <div class="top_bar"><?php $this->load->view('v1.1/elements/common/header_topbar_notlogin'); ?></div>
	<?php else: ?>
	    <div class="top_bar"><?php $this->load->view('v1.1/elements/common/header_topbar'); ?></div>
	<?php endif; ?>    <!--top end-->
	</div>
    <div class="bet-klpk">
        <div class="bet-klpk-hd"><div class="wrap"><?php $this->load->view('v1.2/elements/lottery/info_panel', array('noIssue' => true)); ?></div></div>
        <div class="bet-klpk-bd">
            <div class="wrap">
                <div class="klpk-tab-hd">
                    <ul class="bet-type-link">
                    	<?php foreach ($playTypeArr as $playType) {?>
                    		<li <?php if ($playType['ename'] === 'dz'){?>class="selected"<?php }?> data-type="<?php echo $playType['ename']?>">
                    			<a href="javascript:;"><?php echo $playType['cname']?><span>奖金<b><?php echo $playType['bonus']?></b>元</span></a>
                    		</li>
                    	<?php }?>
                    </ul>
                </div>
                <div class="klpk-tab-bd">
                    <div class="inner">
                        <div class="bet-type-link-bd">
                            <div data-tid="11" class="bet-type-link-item bet-type-link-item4 php_dz" style="display: block;">
                                <div class="bet-pick-area">
                                    <div class="pick-area-explain">
                                        <i class="icon-font">&#xe61b;</i>单选：开出的号码中包含的对子为所选的对子即中奖，奖金单注88元！包选：开出的号码中包含对子即中奖，奖金单注7元！
                                        <div class="mod-tips">
                                            <i class="icon-font bubble-tip" tiptext="<ul><li>选号：<span class='main-color-s'>AA</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-s'>A</span><span class='klpk-num-d'>2</span></li><li>中奖：88元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>对子包选</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-s'>A</span><span class='klpk-num-d'>2</span></li><li>中奖：7元</li></ul>">&#xe613;</i>
                                        </div>
                                    </div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playtype="dz">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal">
                                                    <a target="_blank" href="/mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="110"><col width="145"><col width="82"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45">
                                                    <col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th rowspan="2">形态</th><th colspan="13">开奖号码分布</th></tr>
                                                    <tr><th><span>A</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                    <th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                    <th><span>J</span></th><th><span>Q</span></th><th><span>k</span></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball">
                                                <ol>
                                                <?php 
                                                $ms = explode(',', $miss[1]);
                                                for ($i = 1; $i <= 7; $i++) {?>
                                                	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1]?></i><span>彩</span></li>
                                                <?php }?>
                                                </ol>
                                                <ol>
                                                <?php for ($i = 8; $i <= 13; $i++) {?>
                                                	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1]?></i><span>彩</span></li>
                                                <?php }?>
                                                    <li data-num='00'><a href="javascript:;" class="card-all">对子包选<s>奖金7元</s></a><i <?php if ($ms[13] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[13]?></i><span>彩</span></li>
                                                </ol>
                                            </div>
                                            <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                            <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail rand-select btn-mc">机选</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
		                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
		                        </div>
                            </div>
                            <div data-tid="7" class="bet-type-link-item bet-type-link-item1 php_th">
                                <div class="bet-pick-area">
                                    <div class="pick-area-explain">
                                        <i class="icon-font">&#xe61b;</i>单选：开出的3张扑克牌花色都为所选花色即中奖，奖金单注90元！包选：开出的3张扑克牌为同花色即中奖，奖金单注22元！
                                        <div class="mod-tips">
                                            <i class="icon-font bubble-tip" tiptext="<ul><li class='klpk-num-only'>选号：<span class='klpk-num-d'></span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-d'>2</span><span class='klpk-num-d'>3</span></li><li>中奖：90元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>同花包选</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-d'>2</span><span class='klpk-num-d'>3</span></li><li>中奖：22元</li></ul>">&#xe613;</i>
                                        </div>
                                    </div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playtype="th">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal">
                                                    <a target="_blank" href="/mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="110"><col width="145"><col width="82"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45">
                                                    <col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th rowspan="2">形态</th><th colspan="13">开奖号码分布</th></tr>
                                                    <tr><th><span>A</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                    <th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                    <th><span>J</span></th><th><span>Q</span></th><th><span>k</span></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips">
                                                <ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                            </div>
                                            <div class="pick-area-ball">
                                            <?php $ms = explode(',', $miss[2])?>
                                                <ul>
                                                    <li data-num='01'><a href="javascript:;" class="card-s">黑桃</a><i <?php if ($ms[0] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[0]?></i><span>彩</span></li>
                                                    <li data-num='02'><a href="javascript:;" class="card-h">红桃</a><i <?php if ($ms[1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[1]?></i><span>彩</span></li>
                                                    <li data-num='03'><a href="javascript:;" class="card-c">梅花</a><i <?php if ($ms[2] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[2]?></i><span>彩</span></li>
                                                    <li data-num='04'><a href="javascript:;" class="card-d">方块</a><i <?php if ($ms[3] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[3]?></i><span>彩</span></li>
                                                    <li data-num='00'><a href="javascript:;" class="card-all">同花包选<s>奖金22元</s></a><i <?php if ($ms[4] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[4]?></i><span>彩</span></li>
                                                </ul>
                                            </div>
                                            <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                            <div class="pick-area-select has-btn">
                                                <a href="javascript:;" class="btn-ss btn-specail rand-select btn-mc">机选</a><a href="javascript:;" class="clear-balls">清空</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
		                          <div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a><p class="agree">付款代表您已同意<a href="javascript:;">《用户委托投注协议》</a><a href="javascript:;">《限号投注风险须知》</a></p>
		                        </div>
                            </div>
                            <div data-tid="9" class="bet-type-link-item bet-type-link-item2 php_sz">
                                <div class="bet-pick-area">
                                    <div class="pick-area-explain">
                                        <i class="icon-font">&#xe61b;</i>顺子：开出的3张扑克牌为所选的连续号码即中奖，奖金单注400元！包选：开出的3张扑克牌为连续号码即中奖，奖金单注33元！
                                        <div class="mod-tips">
                                            <i class="icon-font bubble-tip" tiptext="<ul><li>选号：<span class='main-color-s'>A23</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-s'>2</span><span class='klpk-num-c'>3</span></li><li>中奖：400元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>顺子包选</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-s'>2</span><span class='klpk-num-c'>3</span></li><li>中奖：33元</li></ul>">&#xe613;</i>
                                        </div>
                                    </div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playtype="sz">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal">
                                                    <a target="_blank" href="/mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="110"><col width="145"><col width="82"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45">
                                                    <col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th rowspan="2">形态</th><th colspan="13">开奖号码分布</th></tr>
                                                    <tr><th><span>A</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                    <th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                    <th><span>J</span></th><th><span>Q</span></th><th><span>k</span></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball">
                                                <ol>
                                                <?php $ms = explode(',', $miss[3]);
                                                for ($i = 1; $i <= 7; $i++) {?>
                                                	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em><em><?php echo $numArr[$i+1]?></em><em><?php echo $numArr[$i+2]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1]?></i><span>彩</span></li>
                                                <?php }?>
                                                </ol>
                                                <ol>
                                                <?php for ($i = 8; $i <= 11; $i++) {?>
                                                	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em><em><?php echo $numArr[$i+1]?></em><em><?php echo $numArr[$i+2]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1]?></i><span>彩</span></li>
                                                <?php }?>
                                                    <li data-num='12'><a href="javascript:;"><em>Q</em><em>K</em><em>A</em></a><i <?php if ($ms[11] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[11]?></i><span>彩</span></li>
                                                    <li data-num='00'><a href="javascript:;" class="card-all">顺子包选<s>奖金33元</s></a><i <?php if ($ms[12] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[12]?></i><span>彩</span></li>
                                                </ol>
                                            </div>
                                            <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                            <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail rand-select btn-mc">机选</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
		                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
		                        </div>
                            </div>
                            <div data-tid="8" class="bet-type-link-item bet-type-link-item3 php_ths">
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain">
                                        <i class="icon-font">&#xe61b;</i>单选：开出的3张扑克牌花色相同且连续即中奖，奖金单注2150元！包选：开出的3张扑克牌为同花顺即中奖，奖金单注535元！
                                        <div class="mod-tips">
                                            <i class="icon-font bubble-tip" tiptext="<ul><li class='klpk-num-only'>选号：<span class='klpk-num-h'></span></li><li>开奖：<span class='klpk-num-h'>A</span><span class='klpk-num-h'>2</span><span class='klpk-num-h'>3</span></li><li>中奖：2150元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>同花顺包选</span></li><li>开奖：<span class='klpk-num-s'>A</span><span class='klpk-num-s'>2</span><span class='klpk-num-s'>3</span></li><li>中奖：535元</li></ul>">&#xe613;</i>
                                        </div>
                                    </div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playtype="ths">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal">
                                                    <a target="_blank" href="/mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="110"><col width="145"><col width="82"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45">
                                                    <col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th rowspan="2">形态</th><th colspan="13">开奖号码分布</th></tr>
                                                    <tr><th><span>A</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                    <th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                    <th><span>J</span></th><th><span>Q</span></th><th><span>k</span></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball">
                                            <?php $ms = explode(',', $miss[4])?>
                                                <ul>
                                                    <li data-num='01'><a href="javascript:;" class="card-s">顺子</a><i <?php if ($ms[0] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[0]?></i><span>彩</span></li>
                                                    <li data-num='02'><a href="javascript:;" class="card-h">顺子</a><i <?php if ($ms[1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[1]?></i><span>彩</span></li>
                                                    <li data-num='03'><a href="javascript:;" class="card-c">顺子</a><i <?php if ($ms[2] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[2]?></i><span>彩</span></li>
                                                    <li data-num='04'><a href="javascript:;" class="card-d">顺子</a><i <?php if ($ms[3] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[3]?></i><span>彩</span></li>
                                                    <li data-num='00'><a href="javascript:;" class="card-all">同花顺包选<s>奖金535元</s></a><i <?php if ($ms[4] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[4]?></i><span>彩</span></li>
                                                </ul>
                                            </div>
                                            <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                            <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail rand-select btn-mc">机选</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
		                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
		                        </div>
                            </div>
                            <div data-tid="10" class="bet-type-link-item bet-type-link-item5 php_bz">
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain">
                                        <i class="icon-font">&#xe61b;</i>单选：开出的号码一样，且与所选的数字相同即中奖，奖金单注6400元！包选：开出的号码一样，即为中奖，奖金单注为500元！
                                        <div class="mod-tips">
                                            <i class="icon-font bubble-tip" tiptext="<ul><li>选号：<span class='main-color-s'>AAA</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-h'>A</span><span class='klpk-num-s'>A</span></li><li>中奖：6400元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>豹子包选</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-h'>A</span><span class='klpk-num-s'>A</span></li><li>中奖：500元</li></ul>">&#xe613;</i>
                                        </div>
                                    </div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playtype="bz">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal">
                                                    <a target="_blank" href="/mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="110"><col width="145"><col width="82"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45">
                                                    <col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th rowspan="2">形态</th><th colspan="13">开奖号码分布</th></tr>
                                                    <tr><th><span>A</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                    <th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                    <th><span>J</span></th><th><span>Q</span></th><th><span>k</span></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips">
                                                <ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                            </div>
                                            <div class="pick-area-ball">
                                                <ol>
                                                <?php $ms = explode(',', $miss[5]);
                                                for ($i = 1; $i <= 7; $i++) {?>
                                                	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em><em><?php echo $numArr[$i]?></em><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1]?></i><span>彩</span></li>
                                                <?php }?>
                                                </ol>
                                                <ol>
                                                <?php for ($i = 8; $i <= 13; $i++) {?>
                                                	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em><em><?php echo $numArr[$i]?></em><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1]?></i><span>彩</span></li>
                                                <?php }?>
                                                    <li data-num='00'><a href="javascript:;" class="card-all">豹子包选<s>奖金500元</s></a><i <?php if ($ms[13] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[13]?></i><span>彩</span></li>
                                                </ol>
                                            </div>
                                            <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                            <div class="pick-area-select has-btn">
                                                <a href="javascript:;" class="btn-ss btn-specail rand-select btn-mc">机选</a><a href="javascript:;" class="clear-balls">清空</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
		                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
		                        </div>
                            </div>
                            <div data-tid="1" class="bet-type-link-item bet-type-link-item6 php_rx1">
                                <div class="bet-pick-area">
                                    <div class="pick-area-explain">
                                        <i class="icon-font">&#xe61b;</i>从A-K(不区分花色)选择1张或多张扑克投注，所选牌与开奖中任意1张牌号码相同即中奖5元！
                                        <div class="mod-tips">
                                            <i class="icon-font bubble-tip" tiptext="<ul><li>选号：<span class='main-color-s'>A</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-d'>2</span><span class='klpk-num-d'>3</span></li><li>中奖：5元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>A</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-c'>A</span><span class='klpk-num-d'>3</span></li><li>中奖：5元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>A</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-c'>A</span><span class='klpk-num-s'>A</span></li><li>中奖：5元</li></ul>">&#xe613;</i>
                                        </div>
                                    </div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playtype="rx1">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal">
                                                    <a target="_blank" href="/mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="110"><col width="145"><col width="82"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45">
                                                    <col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th rowspan="2">形态</th><th colspan="13">开奖号码分布</th></tr>
                                                    <tr><th><span>A</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                    <th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                    <th><span>J</span></th><th><span>Q</span></th><th><span>k</span></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball">
                                                <ol>
                                                <?php $ms = explode(',', $miss[0]);
                                                for ($i = 1; $i <= 7; $i++) {?>
                                                	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                <?php }?>
                                                </ol>
                                                <ol>
                                                <?php for ($i = 8; $i <= 13; $i++) {?>
                                                	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                <?php }?>
                                                </ol>
                                            </div>
                                            <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                            <div class="pick-area-select has-btn">
                                                <a href="javascript:;" class="btn-ss btn-specail rand-select btn-mc">机选</a>
                                                <a href="javascript:;" class="btn-ss btn-specail btn-cAll filter-all">全包</a>
                                                <a href="javascript:;" class="clear-balls">清空</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
		                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
		                        </div>
                            </div>
                            <div data-tid="2" class="bet-type-link-item bet-type-link-item7 php_rx2">
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain">
                                        <i class="icon-font">&#xe61b;</i>从A-K(不区分花色)选择2张或多张扑克投注，所选牌与开奖中任意2张牌号码相同即中奖33元！
                                        <div class="mod-tips">
                                            <i class="icon-font bubble-tip" tiptext="<ul><li>选号：<span class='main-color-s'>A 2</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-d'>2</span><span class='klpk-num-d'>3</span></li><li>中奖：33元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>A 2</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-c'>A</span><span class='klpk-num-d'>2</span></li><li>中奖：33元</li></ul>">&#xe613;</i>
                                        </div>
                                    </div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playtype="rx2">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal">
                                                    <a target="_blank" href="/mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="110"><col width="145"><col width="82"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45">
                                                    <col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th rowspan="2">形态</th><th colspan="13">开奖号码分布</th></tr>
                                                    <tr><th><span>A</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                    <th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                    <th><span>J</span></th><th><span>Q</span></th><th><span>k</span></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="tab-radio">
                                  <div class="tab-radio-hd">
                                    <ul>
                                        <li class="selected"><label for="betType_rx20"><input type="radio" id="betType_rx20" checked name="betType_rx2">普通</label></li>
                                        <li class="dssc"><label for="betType_rx21"><input type="radio" id="betType_rx21" name="betType_rx2">胆拖</label></li>
                                    </ul>
                                  </div>
                                  <div class="tab-radio-bd">
                                    <div class="tab-radio-inner">
                                      <div class="pick-area-box default">
                                          <div class="pre-box">
                                              <div class="pick-area">
                                                  <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                  <div class="pick-area-ball">
                                                      <ol>
                                                      <?php for ($i = 1; $i <= 7; $i++) {?>
                                                      	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                      <?php }?>
                                                      </ol>
                                                      <ol>
                                                      <?php for ($i = 8; $i <= 13; $i++) {?>
                                                      	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                      <?php }?>
                                                      </ol>
                                                  </div>
                                                  <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                  <div class="pick-area-select has-btn">
                                                  	  <input type="hidden" class="rand-count" value="2">
                                                      <a href="javascript:;" class="btn-ss btn-specail rand-select btn-mc">机选</a>
                                                      <a href="javascript:;" class="btn-ss btn-specail btn-cAll filter-all">全包</a>
                                                      <a href="javascript:;" class="clear-balls">清空</a>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="btn-group">
			                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
					                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
					                        </div>
                                      </div>
                                    </div>
                                    <div class="tab-radio-inner" style="display: none;">
                                      <div class="pick-area-box bet-dt-klpk">
                                          <div class="pre-box">
                                              <div class="pick-area">
                                                <div class="pick-area-title"><span><em>胆牌区</em>我认为<i>必出</i>的牌码  请选择1张胆牌</span></div>
                                                <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">当前遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                <div class="pick-area-ball">
                                                    <ol>
                                                    <?php for ($i = 1; $i <= 13; $i++) {?>
                                                    	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                    <?php }?>
                                                    </ol>
                                                </div>
                                                <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                <div class="pick-area-select has-btn"></div>
                                              </div>
                                              
                                              <div class="pick-area bet-dt-klpk-item2">
                                                <div class="pick-area-title"><span><em>拖牌区</em>我认为<i>可能出</i>牌码，最少选择2张，最多选择12张</span></div>
                                                <div class="pick-area-tips">
                                                    <ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">当前遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                                </div>
                                                <div class="pick-area-ball">
                                                    <ol>
                                                    <?php for ($i = 1; $i <= 13; $i++) {?>
                                                    	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                    <?php }?>
                                                    </ol>
                                                </div>
                                                <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail btn-cAll filter-all">全包</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                              </div>
                                          </div>
                                          <div class="btn-group">
			                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
					                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
					                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div data-tid="3" class="bet-type-link-item bet-type-link-item8 php_rx3">
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain">
                                        <i class="icon-font">&#xe61b;</i>从A-K(不区分花色)选择3张或多张扑克投注，所选牌包含开奖中3张牌即中奖116元！
                                        <div class="mod-tips">
                                            <i class="icon-font bubble-tip" tiptext="<ul><li>选号：<span class='main-color-s'>A 2 3</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-d'>2</span><span class='klpk-num-d'>3</span></li><li>中奖：33元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>A 2 3</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-c'>A</span><span class='klpk-num-d'>2</span></li><li>中奖：33元</li></ul>">&#xe613;</i>
                                        </div>
                                    </div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playtype="rx3">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal">
                                                    <a target="_blank" href="/mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="110"><col width="145"><col width="82"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45">
                                                    <col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th rowspan="2">形态</th><th colspan="13">开奖号码分布</th></tr>
                                                    <tr><th><span>A</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                    <th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                    <th><span>J</span></th><th><span>Q</span></th><th><span>k</span></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="tab-radio">
                                  <div class="tab-radio-hd">
                                    <ul>
                                        <li class="selected"><label for="betType_rx30"><input type="radio" id="betType_rx30" checked name="betType_rx3">普通</label></li>
                                        <li class="dssc"><label for="betType_rx31"><input type="radio" id="betType_rx31" name="betType_rx3">胆拖</label></li>
                                    </ul>
                                  </div>
                                  <div class="tab-radio-bd">
                                    <div class="tab-radio-inner">
                                      <div class="pick-area-box default">
                                          <div class="pre-box">
                                              <div class="pick-area">
                                                  <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                  <div class="pick-area-ball">
                                                      <ol>
                                                      <?php for ($i = 1; $i <= 7; $i++) {?>
                                                      	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                      <?php }?>
                                                      </ol>
                                                      <ol>
                                                      <?php for ($i = 8; $i <= 13; $i++) {?>
                                                      	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                      <?php }?>
                                                      </ol>
                                                  </div>
                                                  <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                  <div class="pick-area-select has-btn">
                                                  	  <input type="hidden" class="rand-count" value="3">
                                                      <a href="javascript:;" class="btn-ss btn-specail rand-select btn-mc">机选</a>
                                                      <a href="javascript:;" class="btn-ss btn-specail btn-cAll filter-all">全包</a>
                                                      <a href="javascript:;" class="clear-balls">清空</a>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="btn-group">
			                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
					                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
					                        </div>
                                      </div>
                                    </div>
                                    <div class="tab-radio-inner" style="display: none;">
                                      <div class="pick-area-box bet-dt-klpk">
                                          <div class="pre-box">
                                              <div class="pick-area">
                                                <div class="pick-area-title"><span><em>胆牌区</em>我认为<i>必出</i>牌码，最少选择1张，最多选择2张</span></div>
                                                <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">当前遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                <div class="pick-area-ball">
                                                    <ol>
                                                    <?php for ($i = 1; $i <= 13; $i++) {?>
                                                    	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                    <?php }?>
                                                    </ol>
                                                </div>
                                                <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                <div class="pick-area-select has-btn"></div>
                                              </div>
                                              
                                              <div class="pick-area bet-dt-klpk-item2">
                                                <div class="pick-area-title"><span><em>拖牌区</em>我认为<i>可能出</i>牌码，最少选择2张，最多选择12张</span></div>
                                                <div class="pick-area-tips">
                                                    <ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">当前遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div>
                                                </div>
                                                <div class="pick-area-ball">
                                                    <ol>
                                                    <?php for ($i = 1; $i <= 13; $i++) {?>
                                                    	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                    <?php }?>
                                                    </ol>
                                                </div>
                                                <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail btn-cAll filter-all">全包</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                              </div>
                                          </div>
                                          <div class="btn-group">
			                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
					                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
					                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div data-tid="4" class="bet-type-link-item bet-type-link-item7 php_rx4">
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain">
                                        <i class="icon-font">&#xe61b;</i>从A-K(不区分花色)选择4张或多张扑克投注，所选牌包含开奖中3张牌即中奖46元！
                                        <div class="mod-tips">
                                            <i class="icon-font bubble-tip" tiptext="<ul><li>选号：<span class='main-color-s'>A 2 3 4</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-d'>2</span><span class='klpk-num-d'>3</span></li><li>中奖：46元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>A 2 3 4</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-c'>A</span><span class='klpk-num-d'>2</span></li><li>中奖：46元</li></ul>">&#xe613;</i>
                                        </div>
                                    </div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playtype="rx4">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal">
                                                    <a target="_blank" href="/mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="110"><col width="145"><col width="82"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45">
                                                    <col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th rowspan="2">形态</th><th colspan="13">开奖号码分布</th></tr>
                                                    <tr><th><span>A</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                    <th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                    <th><span>J</span></th><th><span>Q</span></th><th><span>k</span></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="tab-radio">
                                  <div class="tab-radio-hd">
                                    <ul>
                                        <li class="selected"><label for="betType_rx40"><input type="radio" id="betType_rx40" checked name="betType_rx4">普通</label></li>
                                        <li class="dssc"><label for="betType_rx41"><input type="radio" id="betType_rx41" name="betType_rx4">胆拖</label></li>
                                    </ul>
                                  </div>
                                  <div class="tab-radio-bd">
                                    <div class="tab-radio-inner">
                                      <div class="pick-area-box default">
                                          <div class="pre-box">
                                              <div class="pick-area">
                                                  <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                  <div class="pick-area-ball">
                                                      <ol>
                                                      <?php for ($i = 1; $i <= 7; $i++) {?>
                                                      	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                      <?php }?>
                                                      </ol>
                                                      <ol>
                                                      <?php for ($i = 8; $i <= 13; $i++) {?>
                                                      	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                      <?php }?>
                                                      </ol>
                                                  </div>
                                                  <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                  <div class="pick-area-select has-btn">
                                                  	<input type="hidden" class="rand-count" value="4"><a href="javascript:;" class="btn-ss btn-specail rand-select btn-mc">机选</a><a href="javascript:;" class="btn-ss btn-specail btn-cAll filter-all">全包</a><a href="javascript:;" class="clear-balls">清空</a>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="btn-group">
			                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
					                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
					                        </div>
                                      </div>
                                    </div>
                                    <div class="tab-radio-inner" style="display: none;">
                                      <div class="pick-area-box bet-dt-klpk">
                                          <div class="pre-box">
                                              <div class="pick-area">
                                              	<div class="pick-area-title"><span><em>胆牌区</em>我认为<i>必出</i>的牌码  最少选择1张 最多选择3张</span></div>
                                                
                                                <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">当前遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                <div class="pick-area-ball">
                                                    <ol>
                                                    <?php for ($i = 1; $i <= 13; $i++) {?>
                                                    	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                    <?php }?>
                                                    </ol>
                                                </div>
                                                <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                <div class="pick-area-select has-btn"></div>
                                              </div>
                                              
                                              <div class="pick-area bet-dt-klpk-item2">
                                                <div class="pick-area-title"><span><em>拖牌区</em>我认为<i>可能出</i>牌码，最少选择2张，最多选择12张</span></div>
                                                <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">当前遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                <div class="pick-area-ball">
                                                    <ol>
                                                    <?php for ($i = 1; $i <= 13; $i++) {?>
                                                    	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                    <?php }?>
                                                    </ol>
                                                </div>
                                                <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail btn-cAll filter-all">全包</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                              </div>
                                          </div>
                                          <div class="btn-group">
			                                  <div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
					                          <p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
					                      </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div data-tid="5" class="bet-type-link-item bet-type-link-item8 php_rx5">
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain">
                                        <i class="icon-font">&#xe61b;</i>从A-K(不区分花色)选择5张或多张扑克投注，所选牌包含开奖中3张牌即中奖22元！
                                        <div class="mod-tips">
                                            <i class="icon-font bubble-tip" tiptext="<ul><li>选号：<span class='main-color-s'>A 2 3 4 5</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-d'>2</span><span class='klpk-num-d'>3</span></li><li>中奖：22元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>A 2 3 4 5</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-c'>A</span><span class='klpk-num-d'>2</span></li><li>中奖：22元</li></ul>">&#xe613;</i>
                                        </div>
                                    </div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playtype="rx5">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal">
                                                    <a target="_blank" href="/mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="110"><col width="145"><col width="82"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45">
                                                    <col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th rowspan="2">形态</th><th colspan="13">开奖号码分布</th></tr>
                                                    <tr><th><span>A</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                    <th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                    <th><span>J</span></th><th><span>Q</span></th><th><span>k</span></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="tab-radio">
                                  <div class="tab-radio-hd">
                                    <ul><li class="selected"><label for="betType_rx50"><input type="radio" id="betType_rx50" checked name="betType_rx5">普通</label></li><li class="dssc"><label for="betType_rx51"><input type="radio" id="betType_rx51" name="betType_rx5">胆拖</label></li></ul>
                                  </div>
                                  <div class="tab-radio-bd">
                                    <div class="tab-radio-inner">
                                      <div class="pick-area-box default">
                                          <div class="pre-box">
                                              <div class="pick-area">
                                                  <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                  <div class="pick-area-ball">
                                                      <ol>
                                                      <?php for ($i = 1; $i <= 7; $i++) {?>
                                                      	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                      <?php }?>
                                                      </ol>
                                                      <ol>
                                                      <?php for ($i = 8; $i <= 13; $i++) {?>
                                                      	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                      <?php }?>
                                                      </ol>
                                                  </div>
                                                  <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                  <div class="pick-area-select has-btn">
                                                  	  <input type="hidden" class="rand-count" value="5"><a href="javascript:;" class="btn-ss btn-specail rand-select btn-mc">机选</a><a href="javascript:;" class="btn-ss btn-specail btn-cAll filter-all">全包</a><a href="javascript:;" class="clear-balls">清空</a>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="btn-group">
			                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
					                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
					                        </div>
                                      </div>
                                    </div>
                                    <div class="tab-radio-inner" style="display: none;">
                                      <div class="pick-area-box bet-dt-klpk">
                                          <div class="pre-box">
                                              <div class="pick-area">
                                                <div class="pick-area-title"><span><em>胆牌区</em>我认为<i>必出</i>牌码，最少选择1张，最多选择4张</span></div>
                                                <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">当前遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                <div class="pick-area-ball">
                                                    <ol>
                                                    <?php for ($i = 1; $i <= 13; $i++) {?>
                                                    	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                    <?php }?>
                                                    </ol>
                                                </div>
                                                <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                <div class="pick-area-select has-btn"></div>
                                              </div>
                                              
                                              <div class="pick-area bet-dt-klpk-item2">
                                                <div class="pick-area-title"><span><em>拖牌区</em>我认为<i>可能出</i>牌码，最少选择2张，最多选择12张</span></div>
                                                <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">当前遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                <div class="pick-area-ball">
                                                    <ol>
                                                    <?php for ($i = 1; $i <= 13; $i++) {?>
                                                    	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                    <?php }?>
                                                    </ol>
                                                </div>
                                                <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail btn-cAll filter-all">全包</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                              </div>
                                          </div>
                                          <div class="btn-group">
			                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
					                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
					                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                            <div data-tid="6" class="bet-type-link-item bet-type-link-item7 php_rx6">
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain">
                                        <i class="icon-font">&#xe61b;</i>从A-K(不区分花色)选择6张或多张扑克投注，所选牌包含开奖中3张牌即中奖12元！
                                        <div class="mod-tips">
                                            <i class="icon-font bubble-tip" tiptext="<ul><li>选号：<span class='main-color-s'>A 2 3 4 5 6</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-d'>2</span><span class='klpk-num-d'>3</span></li><li>中奖：12元</li></ul>
                                            <div class='hr'></div><ul><li>选号：<span class='main-color-s'>A 2 3 4 5 6</span></li><li>开奖：<span class='klpk-num-d'>A</span><span class='klpk-num-c'>A</span><span class='klpk-num-d'>2</span></li><li>中奖：12元</li></ul>">&#xe613;</i>
                                        </div>
                                    </div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playtype="rx6">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal">
                                                    <a target="_blank" href="/mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="110"><col width="145"><col width="82"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45">
                                                    <col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"><col width="45"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th rowspan="2">形态</th><th colspan="13">开奖号码分布</th></tr>
                                                    <tr><th><span>A</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                    <th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                    <th><span>J</span></th><th><span>Q</span></th><th><span>k</span></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="tab-radio">
                                  <div class="tab-radio-hd">
                                    <ul><li class="selected"><label for="betType_rx60"><input type="radio" id="betType_rx60" checked name="betType_rx6">普通</label></li><li class="dssc"><label for="betType_rx61"><input type="radio" id="betType_rx61" name="betType_rx6">胆拖</label></li></ul>
                                  </div>
                                  <div class="tab-radio-bd">
                                    <div class="tab-radio-inner">
                                      <div class="pick-area-box default">
                                          <div class="pre-box">
                                              <div class="pick-area">
                                                  <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                  <div class="pick-area-ball">
                                                      <ol>
                                                      <?php for ($i = 1; $i <= 7; $i++) {?>
                                                      	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                      <?php }?>
                                                      </ol>
                                                      <ol>
                                                      <?php for ($i = 8; $i <= 13; $i++) {?>
                                                      	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                      <?php }?>
                                                      </ol>
                                                  </div>
                                                  <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                  <div class="pick-area-select has-btn">
                                                  	<input type="hidden" class="rand-count" value="6"><a href="javascript:;" class="btn-ss btn-specail rand-select btn-mc">机选</a><a href="javascript:;" class="btn-ss btn-specail btn-cAll filter-all">全包</a><a href="javascript:;" class="clear-balls">清空</a>
                                                  </div>
                                              </div>
                                          </div>
                                          <div class="btn-group">
			                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
					                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
					                        </div>
                                      </div>
                                    </div>
                                    <div class="tab-radio-inner" style="display: none;">
                                      <div class="pick-area-box bet-dt-klpk">
                                          <div class="pre-box">
                                              <div class="pick-area">
                                                <div class="pick-area-title"><span><em>胆牌区</em>我认为<i>必出</i>牌码，最少选择1张，最多选择5张</span></div>
                                                <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">当前遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                <div class="pick-area-ball">
                                                    <ol>
                                                    <?php for ($i = 1; $i <= 13; $i++) {?>
                                                    	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                    <?php }?>
                                                    </ol>
                                                </div>
                                                <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                <div class="pick-area-select has-btn"></div>
                                              </div>
                                              
                                              <div class="pick-area bet-dt-klpk-item2">
                                                <div class="pick-area-title"><span><em>拖牌区</em>我认为<i>可能出</i>牌码，最少选择2张，最多选择12张</span></div>
                                                <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">当前遗漏</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                                <div class="pick-area-ball">
                                                    <ol>
                                                    <?php for ($i = 1; $i <= 13; $i++) {?>
                                                    	<li data-num='<?php echo str_pad($i, 2, "0", STR_PAD_LEFT)?>'><a href="javascript:;"><em><?php echo $numArr[$i]?></em></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1] < 0 ? 0 : $ms[$i-1]?></i><span>彩</span></li>
                                                    <?php }?>
                                                    </ol>
                                                </div>
                                                <!-- 新增【机选】按钮,涉及玩法：散牌号、二同号单选、二不同号 父级添加class has-btn -->
                                                <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail btn-cAll filter-all">全包</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                              </div>
                                          </div>
                                          <div class="btn-group">
			                                	<div class="pick-area-note"></div><a class="btn btn-specail add-basket btn-disabled">添加到投注区<i class="icon-font">&#xe614;</i></a>
					                          	<p class="agree">付款代表您已同意<a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
					                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="klpk-tab-bd-ft"></div>
                </div>
            </div>
        </div>
        <div class="ele-fixed-box">
            <div class="lotteryZQbg cast-panel">
                <div class="lotteryZQCenter">
                    <!--已选5场-->
                    <div class="seleFiveWrap">
                        <div class="seleFiveTit"><a href="javascript:;" class="bet-blue">已选<em class="count-matches">0</em>注<i></i></a></div>
                        <div class="seleFiveBox">
                            <div class="seleFiveBoxTit">
                                <table><colgroup><col width="100"><col width="260"><col width="75"><col width="63"></colgroup>
                                <thead><tr><th class="tal">玩法</th><th class="tal">投注内容</th><th>金额</th><th class="tal"><a href="javascript:;" class="clear-matches">清空</a></th></tr></thead></table>
                            </div>
                            <div class="seleFiveBoxScroll"><table class="selected-matches"><colgroup><col width="100"><col width="260"><col width="75"><col width="46"></colgroup><tbody></tbody></table></div>
                        </div>
                    </div>
                    <div class="seleFiveInfo">
                        <ul>
                            <li class="first">
                                <div class="passway">
                                    <p><a href="javascript:;" class="gg-type selected" data-orderType="0">自选</a><a href="javascript:;" class="gg-type" data-orderType="1">追号</a></p>
                                    <div class="multi-modifier-s multi"><a href="javascript:;" class="minus">-</a><label><input class="multi number" type="text" value="1" autocomplete="off"></label><a href="javascript:;" class="plus" data-max="10000">+</a></div> 倍，
                                    <span class="chase-div" style="display:none">追
	                                    <div class="multi-modifier-s chase"><a href="javascript:;" class="minus">-</a><label><input class="chase number" type="text" value="10" autocomplete="off"></label><a href="javascript:;" class="plus" data-max="<?php echo count($chases) > 88 ? 88 : count($chases)?>">+</a></div>
	                                                                                                              期，</span>
                                </div>
                                <div class="numbox">共 <span class="bet-num main-color-s">0</span> 元</div>
                                <div class="klpk-qr" style="display: none;margin-top:0">
		                            <label for="setStatus"><input type="checkbox" checked class="setStatus" id="setStatus">中奖后停止追号</label>
		                            <div class="mod-tips"><i class="icon-font bubble-tip" tiptext="<strong>中奖追停：</strong>勾选后，您的追号方案中的某一期中奖后，后续追号订单将被撤销，资金返还你的账户中。如不勾选，系统一直帮您购买所有的追号投注任务。">&#xe613;</i></div>
		                        </div>
                            </li>
                        </ul>
                        <?php if($lotteryConfig[KLPK]['status']):?>
			            	<a id="pd_klpk_buy" class="btn btn-main btn-betting submit <?php echo $showBind ? ' not-bind': '';?>">确认预约</a>
			            <?php else :?>
			            	<a id="pd_klpk_buy" class="btn btn-main btn-betting btn-disabled <?php echo $showBind ? ' not-bind': '';?>">暂停预约</a>
			            <?php endif;?>  
                    </div>
                </div>
            </div>
        </div>
        <div class="wrap bet-klpk-ft">
            <dl>
                <dt><i class="icon-font icon-arrow">&#xe62a;</i>今日开奖</dt>
                <dd>
                    <div class="klpk-table-kj">
                    <?php for ($i = 0; $i < 4; $i++) {?>
                    	<table><colgroup><col width="16%"><col width="54%"><col width="30%"></colgroup><thead><tr><th>期次</th><th>开奖号码</th><th>形态</th></tr></thead><tbody></tbody></table>
                    <?php }?>
                    </div>
                </dd>
            </dl>
            <dl>
                <dt><i class="icon-font icon-arrow">&#xe62a;</i>投注风险提示</dt>
                <dd>
                    <p>若用户设置的为中奖后停止追号的方案，则在执行追号过程中，发生某期追号方案直到当期网站销售截止前二分钟仍不明确上期追号的中奖状态，则网站会对当期追号方案做继续追号的处理。敬请知悉由此产生的追号风险。</p>
                    <p>查看<a href="/help/index/b5-s16" target="_blank">《玩法介绍》</a></p>
                </dd>
            </dl>
        </div>
    </div>
    <script src="//cdn.bootcss.com/underscore.js/1.8.3/underscore-min.js"></script>
    <?php $this->load->view('v1.2/elements/common/editballs');?>
    <?php $this->load->view('v1.1/elements/pop-spring');?>
    <?php $this->load->view('v1.1/elements/common/footer_mid');?>
    <script>
    $(function() {
        $('.bet-klpk-ft').on("click", 'dt', function() {
            $(this).toggleClass('active');
            $(this).parents('dl').find('dd').slideToggle();
        })
        
        $('.side-menu-klpk').on('click', '.past-award', function(){
            var tEle = $('.bet-klpk-ft').find('dl').eq(0);
            if (!$(".bet-klpk-ft dd")[0].style.display) {
            	$(".bet-klpk-ft dt").trigger('click')
            }
            
            tEle.find('dd').slideDown();

            var calcHeight = $('.bet-klpk-ft').offset().top;
            $('body, html').animate({
                scrollTop: calcHeight
            }, 400);  
        })

        $('.klpk-qr .bubble-tip').mouseenter(function(){
            $.bubble({
                target:this,
                position: 't',
                align: 'l',
                content: $(this).attr('tiptext'),
                width:'240px'
            })
        }).mouseleave(function(){
            $('.bubble').hide();
        });

        $('.pick-area-explain .bubble-tip, .pick-area-tips .bubble-tip').mouseenter(function(){
            $.bubble({
                target:this,
                position: 'b',
                align: 'l',
                content: $(this).attr('tiptext'),
                width:'auto'
            })
        }).mouseleave(function(){
            $('.bubble').hide();
        });


        $('.side-menu-klpk').on('click', '.past-award', function(){
            var tEle = $('.bet-klpk-ft').find('dl').eq(0);
            tEle.find('dt').addClass('active');
            tEle.find('dd').slideDown();

            var calcHeight = $('.bet-klpk-ft').offset().top;
            $('body, html').animate({
                scrollTop: calcHeight
            }, 400);  
        })
    })
    </script>
</body>

</html>
