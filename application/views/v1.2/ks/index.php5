<?php 
if(!$lotteryConfig[KS]['status']) {
	$selling = 0;
}elseif (time() > floor($info['next']['seFsendtime']/1000) && time() < floor($info['next']['seFsendtime']/1000)) {
	$selling = 1;
}else {
	$selling = 2;
	if ($lotteryConfig['united_status']) $hmselling = 1;
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
    <?php
	$this->config->load('seo');
	$seo = $this->config->item('seo');
	$set_data = $seo[$this->con][$this->act];
	?>
	<title><?php echo $set_data['title']; ?></title>
	<meta content="<?php echo $set_data['description']; //@Author liusijia   ?>" name="Description" />
	<meta content="<?php echo $set_data['keywords']; //@Author liusijia  ?>" name="Keywords" />
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/global.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/lottery-public.min.css');?>">
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/jquery-1.8.3.min.js');?>" type="text/javascript"></script>
    <script>
    var ISSUE = "<?php echo $info['cIssue']['seExpect']?>", tm = <?php echo $info['cIssue']['seFsendtime']/1000-time();?>,
    atm = <?php echo $info['nlIssue']['awardTime']/1000-time();?>, hsty = eval(<?php echo json_encode($history)?>), mall = eval(<?php echo json_encode($mall)?>),
    ENDTIME = "<?php echo date('Y-m-d H:i:s', $info['cIssue']['seFsendtime']/1000)?>", vJson = [<?php echo $awardNum?>], version = 'v1.2', enName = '<?php echo $enName?>',
    baseUrl = '<?php echo $this->config->item('base_url'); ?>', chases = $.parseJSON('<?php echo json_encode($chases)?>'), chaselength = <?php echo $chaselength?>,
    selling = <?php echo $selling?>, MULTI = <?php echo (int)$multi?> || 1;
    </script>
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/base.min.js');?>" type="text/javascript"></script>
    <script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/order.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.2/js/ks.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/v1.2/js/gaopin.min.js');?>" type="text/javascript"></script>
</head>

<body>
    <!--top begin-->
	<?php if (empty($this->uid)): ?>
	    <div class="top_bar">
	    	<?php $this->load->view('v1.1/elements/common/header_topbar_notlogin'); ?>
	    </div>
	<?php else: ?>
	    <div class="top_bar">
	        <?php $this->load->view('v1.1/elements/common/header_topbar'); ?>
	    </div>
	<?php endif; ?>    <!--top end-->
	</div>
    <div class="bet-k3">
        <div class="bet-k3-hd">
            <div class="wrap">
                <?php $this->load->view('v1.2/elements/lottery/info_panel', array('noIssue' => true)); ?>
            </div>
        </div>
        <div class="bet-k3-bd">
            <div class="wrap">
                <div class="k3-tab-hd">
                    <ul class="bet-type-link">
                        <li class="selected" data-playtype='hz'><a href="javascript:;">和值<span>奖金<b>9-240</b>元</span></a></li>
                        <li data-playtype='sthtx'><a href="javascript:;">三同号通选<span>奖金<b>40</b>元</span></a></li>
                        <li data-playtype='sthdx'><a href="javascript:;">三同号单选<span>奖金<b>240</b>元</span></a></li>
                        <li data-playtype='sbth'><a href="javascript:;">三不同号<span>奖金<b>40</b>元</span></a></li>
                        <li data-playtype='slhtx'><a href="javascript:;">三连号通选<span>奖金<b>10</b>元</span></a></li>
                        <li data-playtype='ethfx'><a href="javascript:;">二同号复选<span>奖金<b>15</b>元</span></a></li>
                        <li data-playtype='ethdx'><a href="javascript:;">二同号单选<span>奖金<b>80</b>元</span></a></li>
                        <li data-playtype='ebth'><a href="javascript:;">二不同号<span>奖金<b>8</b>元</span></a></li>
                    </ul>
                </div>
                <div class="k3-tab-bd">
                    <div class="inner">
                        <div class="bet-type-link-bd">
                            <div class="bet-type-link-item bet-type-link-item1 hz" data-tid="1" style="display: block;">
                                <div class="bet-pick-area">
                                	<div class="pick-area-note" style="display: none;"><span></span></div>
                                    <div class="pick-area-explain"><i class="icon-font">&#xe61b;</i>所选和值与开奖的3个号码的和值相同即中奖，最高可中240元！<div class="mod-tips"><i class="icon-font bubble-tip" tiptext="<ul><li>选号：5</li><li>开奖：113</li><li>中奖：40元</li></ul>">&#xe613;</i></div></div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playType="hz">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal"><a target="_blank" href="mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup><col width="80"><col width="106"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="40"><col width="40"><col width="40"><col width="40"></colgroup>
                                                    <thead><tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th colspan="16">和值走势</th><th colspan="4">和值形态</th></tr><tr><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th><th><span>6</span></th><th><span>7</span></th><th><span>8</span></th><th><span>9</span></th><th><span>10</span></th>
                                                   	<th><span>11</span></th><th><span>12</span></th><th><span>13</span></th><th><span>14</span></th><th><span>15</span></th><th><span>16</span></th><th><span>17</span></th><th><span>18</span></th><th><span>大</span></th><th><span>小</span></th><th><span>单</span></th><th><span>双</span></th></tr></thead><tbody></tbody>
                                                </table>
                                                <div class="canvas-mask"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li><li><a href="javascript:;" class="bubble-tip" tiptext="指该号码近82期出现的次数">冷热</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball"><ol>
                                                <?php $ms = explode(',', $miss[1]);
                                                $lr = explode(',', $lenr[1]);
                                                $jj = array(0, 0, 0, 240, 80, 40, 25, 16, 12, 10, 9, 9, 10, 12, 16, 25, 40, 80, 240);
                                                for ($i = 3; $i <= 10; $i++) {?>
                                                    <li data-num='<?php echo $i;?>'><a href="javascript:;"><b><?php echo $i?></b><br>奖金<s><?php echo $jj[$i]?></s>元</a><i <?php if ($ms[$i-3] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-3]?></i><em <?php if ($lr[$i-3] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[$i-3]?></em><span>彩 </span></li>
                                                <?php }?>
                                                    <li class="ball-spa filter-smalls"><a href="javascript:;"><b>小</b></a></li></ol><ol>
                                                <?php for ($i = 11; $i <= 18; $i++) {?>
                                                    <li data-num=<?php echo $i;?>><a href="javascript:;"><b><?php echo $i?></b><br>奖金<s><?php echo $jj[$i]?></s>元</a><i <?php if ($ms[$i-3] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-3]?></i><em <?php if ($lr[$i-3] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[$i-3]?></em><span>彩 </span></li>
                                                <?php }?>
                                                    <li class="ball-spa filter-bigs"><a href="javascript:;"><b>大</b></a></li></ol>
                                            </div>
                                            <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail btn-mc rand-select">机选</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bet-type-link-item bet-type-link-item2 sthtx" data-tid="2">
                            	<div class="pick-area-note" style="display: none;"><span></span></div>
                            	<div class="pick-area-explain"><i class="icon-font">&#xe61b;</i>当开奖号码为三同号中任意一个时即中奖，单注奖金40元！<div class="mod-tips"><i class="icon-font bubble-tip" tiptext="<ul><li>选号：三同号通选</li><li>开奖：111</li><li>中奖：40元</li></ul>">&#xe613;</i></div></div>
                            	<div class="bet-pick-area">
                            		<div class="table-zs">
                            			<div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playType="sthtx">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal"><a target="_blank" href="mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup>
                                                        <col width="80"><col width="106"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="50"><col width="50">
                                                        <col width="50"><col width="50"><col width="50"><col width="50"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36">
                                                    </colgroup>
                                                    <thead>
                                                        <tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th colspan="6">开奖号码分布</th><th colspan="6">开奖号码形态</th><th colspan="6">跨度走势</th></tr>
                                                        <tr>
                                                            <th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th><th><span>6</span></th><th><span>三同号</span></th><th><span>三不同</span></th><th><span>三连号</span></th>
                                                            <th><span>二同复</span></th><th><span>二同单</span></th><th><span>二不同</span></th><th><span>0</span></th><th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                <div class="canvas-mask"></div>
                                            </div>
                                        </div>
                            		</div>
                                </div>
                            	<div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li><li><a href="javascript:;" class="bubble-tip" tiptext="指该号码近82期出现的次数">冷热</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball"><ol><li data-num='0,0,0'><a href="javascript:;"><?php for ($i = 1; $i <= 6; $i++) { echo "<b>".$i.$i.$i."</b>";}?><br>任意开出即中40元</a><i><?php echo $miss[2]?></i><em><?php echo $lenr[2]?></em><span>彩</span></li></ol></div>
                                            <div class="pick-area-select"><a href="javascript:;" class="clear-balls">清空</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bet-type-link-item bet-type-link-item3 sthdx" data-tid="3">
                            	<div class="pick-area-note" style="display: none;"><span></span></div>
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain"><i class="icon-font">&#xe61b;</i>从111～666中所选号码与开奖号码的3个号码相同即中奖，单注奖金240元！<div class="mod-tips"><i class="icon-font bubble-tip" tiptext="<ul><li>选号：222</li><li>开奖：222</li><li>中奖：240元</li></ul>">&#xe613;</i></div></div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playType="sthdx">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal"><a target="_blank" href="mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup>
                                                        <col width="80"><col width="106"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="50"><col width="50">
                                                        <col width="50"><col width="50"><col width="50"><col width="50"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36">
                                                    </colgroup>
                                                    <thead>
                                                        <tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th colspan="6">开奖号码分布</th><th colspan="6">开奖号码形态</th><th colspan="6">跨度走势</th></tr>
                                                        <tr>
                                                            <th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th><th><span>6</span></th><th><span>三同号</span></th><th><span>三不同</span></th><th><span>三连号</span></th>
                                                            <th><span>二同复</span></th><th><span>二同单</span></th><th><span>二不同</span></th><th><span>0</span></th><th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                <div class="canvas-mask"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li><li><a href="javascript:;" class="bubble-tip" tiptext="指该号码近82期出现的次数">冷热</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball">
                                                <ol>
                                                <?php $ms = explode(',', $miss[3]);
                                                $lr = explode(',', $lenr[3]);
                                                for ($i = 1; $i <= 6; $i++) {?>
                                                	<li data-num='<?php echo $i.",".$i.",".$i?>'><a href="javascript:;"><b><?php echo $i.$i.$i?></b></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1]?></i><em <?php if ($lr[$i-1] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[$i-1]?></em><span>彩 </span></li>
                                                <?php }?>
                                                </ol>
                                            </div>
                                            <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail btn-mc rand-select">机选</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bet-type-link-item bet-type-link-item4 sbth" data-tid="4">
                            	<div class="pick-area-note" style="display: none;"><span></span></div>
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain"><i class="icon-font">&#xe61b;</i>所选号码与开奖号码的3个号码相同即中奖，单注奖金40元！<div class="mod-tips"><i class="icon-font bubble-tip" tiptext="<ul><li>选号：135</li><li>开奖：135</li><li>中奖：40元</li></ul>">&#xe613;</i></div></div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playType="sbth">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal"><a target="_blank" href="mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup>
                                                        <col width="80"><col width="106"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="50"><col width="50">
                                                        <col width="50"><col width="50"><col width="50"><col width="50"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36">
                                                    </colgroup>
                                                    <thead>
                                                        <tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th colspan="6">开奖号码分布</th><th colspan="6">开奖号码形态</th><th colspan="6">跨度走势</th></tr>
                                                        <tr>
                                                            <th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th><th><span>6</span></th><th><span>三同号</span></th><th><span>三不同</span></th><th><span>三连号</span></th>
                                                            <th><span>二同复</span></th><th><span>二同单</span></th><th><span>二不同</span></th><th><span>0</span></th><th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                <div class="canvas-mask"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li><li><a href="javascript:;" class="bubble-tip" tiptext="指该号码近82期出现的次数">冷热</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball">
                                            <?php $ms = explode(',', $miss[4]);
                                                $lr = explode(',', $lenr[4]);?>
                                                <ol>
                                                    <li data-num='1,2,3'><a href="javascript:;"><b>123</b></a><i <?php if ($ms[0] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[0]?></i><em <?php if ($lr[0] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[0]?></em><span>彩 </span></li>
                                                    <li data-num='1,2,4'><a href="javascript:;"><b>124</b></a><i <?php if ($ms[1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[1]?></i><em <?php if ($lr[1] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[1]?></em><span>彩</span></li>
                                                    <li data-num='1,2,5'><a href="javascript:;"><b>125</b></a><i <?php if ($ms[2] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[2]?></i><em <?php if ($lr[2] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[2]?></em><span>彩</span></li>
                                                    <li data-num='1,2,6'><a href="javascript:;"><b>126</b></a><i <?php if ($ms[3] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[3]?></i><em <?php if ($lr[3] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[3]?></em><span>彩</span></li>
                                                    <li data-num='1,3,4'><a href="javascript:;"><b>134</b></a><i <?php if ($ms[4] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[4]?></i><em <?php if ($lr[4] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[4]?></em><span>彩</span></li>
                                                </ol>
                                                <ol>
                                                    <li data-num='1,3,5'><a href="javascript:;"><b>135</b></a><i <?php if ($ms[5] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[5]?></i><em <?php if ($lr[5] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[5]?></em><span>彩 </span></li>
                                                    <li data-num='1,3,6'><a href="javascript:;"><b>136</b></a><i <?php if ($ms[6] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[6]?></i><em <?php if ($lr[6] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[6]?></em><span>彩</span></li>
                                                    <li data-num='1,4,5'><a href="javascript:;"><b>145</b></a><i <?php if ($ms[7] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[7]?></i><em <?php if ($lr[7] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[7]?></em><span>彩</span></li>
                                                    <li data-num='1,4,6'><a href="javascript:;"><b>146</b></a><i <?php if ($ms[8] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[8]?></i><em <?php if ($lr[8] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[8]?></em><span>彩</span></li>
                                                    <li data-num='1,5,6'><a href="javascript:;"><b>156</b></a><i <?php if ($ms[9] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[9]?></i><em <?php if ($lr[9] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[9]?></em><span>彩</span></li>
                                                </ol>
                                                <ol>
                                                    <li data-num='2,3,4'><a href="javascript:;"><b>234</b></a><i <?php if ($ms[10] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[10]?></i><em <?php if ($lr[10] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[10]?></em><span>彩 </span></li>
                                                    <li data-num='2,3,5'><a href="javascript:;"><b>235</b></a><i <?php if ($ms[11] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[11]?></i><em <?php if ($lr[11] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[11]?></em><span>彩</span></li>
                                                    <li data-num='2,3,6'><a href="javascript:;"><b>236</b></a><i <?php if ($ms[12] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[12]?></i><em <?php if ($lr[12] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[12]?></em><span>彩</span></li>
                                                    <li data-num='2,4,5'><a href="javascript:;"><b>245</b></a><i <?php if ($ms[13] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[13]?></i><em <?php if ($lr[13] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[13]?></em><span>彩</span></li>
                                                    <li data-num='2,4,6'><a href="javascript:;"><b>246</b></a><i <?php if ($ms[14] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[14]?></i><em <?php if ($lr[14] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[14]?></em><span>彩</span></li>
                                                </ol>
                                                <ol>
                                                    <li data-num='2,5,6'><a href="javascript:;"><b>256</b></a><i <?php if ($ms[15] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[15]?></i><em <?php if ($lr[15] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[15]?></em><span>彩 </span></li>
                                                    <li data-num='3,4,5'><a href="javascript:;"><b>345</b></a><i <?php if ($ms[16] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[16]?></i><em <?php if ($lr[16] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[16]?></em><span>彩</span></li>
                                                    <li data-num='3,4,6'><a href="javascript:;"><b>346</b></a><i <?php if ($ms[17] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[17]?></i><em <?php if ($lr[17] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[17]?></em><span>彩</span></li>
                                                    <li data-num='3,5,6'><a href="javascript:;"><b>356</b></a><i <?php if ($ms[18] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[18]?></i><em <?php if ($lr[18] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[18]?></em><span>彩</span></li>
                                                    <li data-num='4,5,6'><a href="javascript:;"><b>456</b></a><i <?php if ($ms[19] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[19]?></i><em <?php if ($lr[19] == max($lr)) {?>class="num-light"<?php }?>>2<?php echo $lr[19]?></em><span>彩</span></li>
                                                </ol>
                                            </div>
                                            <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail btn-mc rand-select">机选</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bet-type-link-item bet-type-link-item5 slhtx" data-tid="5">
                            	<div class="pick-area-note" style="display: none;"><span></span></div>
                                <div class="bet-pick-area">
                                    <div class="pick-area-explain"><i class="icon-font">&#xe61b;</i>当开奖号码为三连号中任意一个时即中奖，单注奖金10元！<div class="mod-tips"><i class="icon-font bubble-tip" tiptext="<ul><li>选号：三连号通选</li><li>开奖：123</li><li>中奖：10元</li></ul>">&#xe613;</i></div></div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playType="slhtx">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal"><a target="_blank" href="mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup>
                                                        <col width="80"><col width="106"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="50"><col width="50">
                                                        <col width="50"><col width="50"><col width="50"><col width="50"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36">
                                                    </colgroup>
                                                    <thead>
                                                        <tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th colspan="6">开奖号码分布</th><th colspan="6">开奖号码形态</th><th colspan="6">跨度走势</th></tr>
                                                        <tr>
                                                            <th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th><th><span>6</span></th><th><span>三同号</span></th><th><span>三不同</span></th><th><span>三连号</span></th>
                                                            <th><span>二同复</span></th><th><span>二同单</span></th><th><span>二不同</span></th><th><span>0</span></th><th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                <div class="canvas-mask"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li><li><a href="javascript:;" class="bubble-tip" tiptext="指该号码近82期出现的次数">冷热</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball"><ol><li data-num='0,0,0'><a href="javascript:;"><b>123</b><b>234</b><b>345</b><b>456</b><br>任意开出即中10元</a><i><?php echo $miss[5]?></i><em><?php echo $lenr[5]?></em><span>彩</span></li></ol></div>
                                            <div class="pick-area-select"><a href="javascript:;" class="clear-balls">清空</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bet-type-link-item bet-type-link-item6 ethfx" data-tid="6">
                            	<div class="pick-area-note" style="display: none;"><span></span></div>
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain"><i class="icon-font">&#xe61b;</i>开奖号码中包含所选择的对子即中奖，单注奖金15元！<div class="mod-tips"><i class="icon-font bubble-tip" tiptext="<ul><li>选号：11*</li><li>开奖：116</li><li>中奖：15元</li></ul>">&#xe613;</i></div></div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playType="ethfx">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal"><a target="_blank" href="mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup>
                                                        <col width="80"><col width="106"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="50"><col width="50">
                                                        <col width="50"><col width="50"><col width="50"><col width="50"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36">
                                                    </colgroup>
                                                    <thead>
                                                        <tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th colspan="6">开奖号码分布</th><th colspan="6">开奖号码形态</th><th colspan="6">跨度走势</th></tr>
                                                        <tr>
                                                            <th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th><th><span>6</span></th><th><span>三同号</span></th><th><span>三不同</span></th><th><span>三连号</span></th>
                                                            <th><span>二同复</span></th><th><span>二同单</span></th><th><span>二不同</span></th><th><span>0</span></th><th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                <div class="canvas-mask"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li><li><a href="javascript:;" class="bubble-tip" tiptext="指该号码近82期出现的次数">冷热</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball">
                                                <ol>
                                                    <?php $ms = explode(',', $miss[6]);
                                                	$lr = explode(',', $lenr[6]);
                                                    for ($i = 1; $i <= 6; $i++) {?>
	                                                	<li data-num='<?php echo $i.",".$i.",*"?>'><a href="javascript:;"><b><?php echo $i.$i."*"?></b></a><i <?php if ($ms[$i-1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$i-1]?></i><em <?php if ($lr[$i-1] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[$i-1]?></em><span>彩 </span></li>
	                                                <?php }?>
                                                </ol>
                                            </div>
                                            <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail btn-mc rand-select">机选</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bet-type-link-item bet-type-link-item7 ethdx" data-tid="7">
                            	<div class="pick-area-note" style="display: none;"><span></span></div>
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain"><i class="icon-font">&#xe61b;</i> 选择1对相同号码和1个不同号码投注，选号与奖号相同（顺序不限），即中奖80元！<div class="mod-tips"><i class="icon-font bubble-tip" tiptext="<ul><li>选号：155</li><li>开奖：155</li><li>中奖：80元</li></ul>">&#xe613;</i></div></div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playType="ethdx">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal"><a target="_blank" href="mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup>
                                                        <col width="80"><col width="106"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="50"><col width="50">
                                                        <col width="50"><col width="50"><col width="50"><col width="50"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36">
                                                    </colgroup>
                                                    <thead>
                                                        <tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th colspan="6">开奖号码分布</th><th colspan="6">开奖号码形态</th><th colspan="6">跨度走势</th></tr><tr><th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th><th><span>6</span></th>
                                                        <th><span>三同号</span></th><th><span>三不同</span></th><th><span>三连号</span></th><th><span>二同复</span></th><th><span>二同单</span></th><th><span>二不同</span></th><th><span>0</span></th><th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th></tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                <div class="canvas-mask"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li><li><a href="javascript:;" class="bubble-tip" tiptext="指该号码近82期出现的次数">冷热</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball">
                                            <?php $ms = explode(',', $miss[7]);$lr = explode(',', $lenr[7]); $k = 0;
                                            for ($i = 1; $i <= 6; $i++) {?>
                                            	<ol>
                                            	<?php for ($j = 1; $j <= 6; $j++) {
                                            	if ($i !== $j) {?>
                                            		<li data-num='<?php echo $i.",".$i.",".$j?>'><a href="javascript:;"><b><?php echo $i.$i.$j?></b></a><i <?php if ($ms[$k] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[$k]?></i><em <?php if ($lr[$k] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[$k]?></em><span>彩 </span></li>
                                            	<?php $k++;}
												}?>
                                                </ol>
                                            <?php }?>
                                            </div>
                                            <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail btn-mc rand-select">机选</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bet-type-link-item bet-type-link-item8 ebth" data-tid="8">
                            	<div class="pick-area-note" style="display: none;"><span></span></div>
                            	<div class="bet-pick-area">
                                    <div class="pick-area-explain"><i class="icon-font">&#xe61b;</i>所选号码与开奖号码任意2个号码相同即中奖，单注奖金8元！<div class="mod-tips"><i class="icon-font bubble-tip" tiptext="<ul><li>选号：12</li><li>开奖：123</li><li>中奖：8元</li></ul>">&#xe613;</i></div></div>
                                    <div class="table-zs">
                                        <div class="table-zs-hd"><ul><li class="myorder"><a href="javascript:;">我的投注</a></li><li><a href="javascript:;" class="jqzs" data-playType="ebth">近期走势</a></li></ul></div>
                                        <div class="table-zs-bd">
                                            <div class="table-zs-item my-order">
                                                <table>
                                                    <colgroup><col width="160"><col width="92"><col width="276"><col width="80"><col width="122"><col width="76"><col width="162"><col width="30"></colgroup>
                                                    <thead><tr><th>时间</th><th>期次</th><th>方案内容</th><th>订单金额</th><th>订单状态</th><th>我的奖金</th><th>操作</th><th class="tal"><a target="_blank" href="mylottery/betlog">更多</a></th></tr></thead><tbody></tbody>
                                                </table>
                                            </div>
                                            <div class="table-zs-item ykj-info">
                                                <table>
                                                    <colgroup>
                                                        <col width="80"><col width="106"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="50"><col width="50">
                                                        <col width="50"><col width="50"><col width="50"><col width="50"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36"><col width="36">
                                                    </colgroup>
                                                    <thead>
                                                        <tr><th rowspan="2">期次</th><th rowspan="2">开奖号码</th><th colspan="6">开奖号码分布</th><th colspan="6">开奖号码形态</th><th colspan="6">跨度走势</th></tr>
                                                        <tr>
                                                            <th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th><th><span>6</span></th><th><span>三同号</span></th><th><span>三不同</span></th><th><span>三连号</span></th>
                                                            <th><span>二同复</span></th><th><span>二同单</span></th><th><span>二不同</span></th><th><span>0</span></th><th><span>1</span></th><th><span>2</span></th><th><span>3</span></th><th><span>4</span></th><th><span>5</span></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                                <div class="canvas-mask"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="pick-area-box">
                                    <div class="pre-box">
                                        <div class="pick-area">
                                            <div class="pick-area-tips"><ul><li class="current"><a href="javascript:;" class="bubble-tip" tiptext="指该号码自上次开出后没有出现的次数">遗漏</a><i></i></li><li><a href="javascript:;" class="bubble-tip" tiptext="指该号码近82期出现的次数">冷热</a><i></i></li></ul><div class="ptips-bd-t">指该号码自上次开出后没有出现的次数<b></b><s></s></div></div>
                                            <div class="pick-area-ball">
                                            <?php $ms = explode(',', $miss[8]);$lr = explode(',', $lenr[8]);?>
                                                <ol>
                                                    <li data-num='1,2,*'><a href="javascript:;"><b>12</b></a><i <?php if ($ms[0] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[0]?></i><em <?php if ($lr[0] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[0]?></em><span>彩 </span></li>
                                                    <li data-num='1,3,*'><a href="javascript:;"><b>13</b></a><i <?php if ($ms[1] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[1]?></i><em <?php if ($lr[1] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[1]?></em><span>彩</span></li>
                                                    <li data-num='1,4,*'><a href="javascript:;"><b>14</b></a><i <?php if ($ms[2] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[2]?></i><em <?php if ($lr[2] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[2]?></em><span>彩</span></li>
                                                    <li data-num='1,5,*'><a href="javascript:;"><b>15</b></a><i <?php if ($ms[3] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[3]?></i><em <?php if ($lr[3] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[3]?></em><span>彩</span></li>
                                                    <li data-num='1,6,*'><a href="javascript:;"><b>16</b></a><i <?php if ($ms[4] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[4]?></i><em <?php if ($lr[4] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[4]?></em><span>彩</span></li>
                                                </ol>
                                                <ol>
                                                    <li data-num='2,3,*'><a href="javascript:;"><b>23</b></a><i <?php if ($ms[5] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[5]?></i><em <?php if ($lr[5] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[5]?></em><span>彩 </span></li>
                                                    <li data-num='2,4,*'><a href="javascript:;"><b>24</b></a><i <?php if ($ms[6] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[6]?></i><em <?php if ($lr[6] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[6]?></em><span>彩</span></li>
                                                    <li data-num='2,5,*'><a href="javascript:;"><b>25</b></a><i <?php if ($ms[7] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[7]?></i><em <?php if ($lr[7] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[7]?></em><span>彩</span></li>
                                                    <li data-num='2,6,*'><a href="javascript:;"><b>26</b></a><i <?php if ($ms[8] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[8]?></i><em <?php if ($lr[8] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[8]?></em><span>彩</span></li>
                                                    <li data-num='3,4,*'><a href="javascript:;"><b>34</b></a><i <?php if ($ms[9] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[9]?></i><em <?php if ($lr[9] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[9]?></em><span>彩</span></li>
                                                </ol>
                                                <ol>
                                                    <li data-num='3,5,*'><a href="javascript:;"><b>35</b></a><i <?php if ($ms[10] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[10]?></i><em <?php if ($lr[10] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[10]?></em><span>彩 </span></li>
                                                    <li data-num='3,6,*'><a href="javascript:;"><b>36</b></a><i <?php if ($ms[11] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[11]?></i><em <?php if ($lr[11] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[11]?></em><span>彩</span></li>
                                                    <li data-num='4,5,*'><a href="javascript:;"><b>45</b></a><i <?php if ($ms[12] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[12]?></i><em <?php if ($lr[12] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[12]?></em><span>彩</span></li>
                                                    <li data-num='4,6,*'><a href="javascript:;"><b>46</b></a><i <?php if ($ms[13] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[13]?></i><em <?php if ($lr[13] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[13]?></em><span>彩</span></li>
                                                    <li data-num='5,6,*'><a href="javascript:;"><b>56</b></a><i <?php if ($ms[14] == max($ms)) {?>class="num-light"<?php }?>><?php echo $ms[14]?></i><em <?php if ($lr[14] == max($lr)) {?>class="num-light"<?php }?>><?php echo $lr[14]?></em><span>彩</span></li>
                                                </ol>
                                            </div>
                                            <div class="pick-area-select has-btn"><a href="javascript:;" class="btn-ss btn-specail btn-mc rand-select">机选</a><a href="javascript:;" class="clear-balls">清空</a></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="agree">付款代表您已同意<a href="javascript:;"class="lottery_pro">《用户委托投注协议》</a><a href="javascript:;" class="risk_pro">《限号投注风险须知》</a></p>
                    </div>
                    <div class="k3-tab-bd-ft"></div>
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
                            <div class="seleFiveBoxTit"><table><colgroup><col width="100"><col width="260"><col width="56"><col width="63"></colgroup><thead><tr><th class="tal">玩法</th><th class="tal">投注内容</th><th>金额</th><th class="tal"><a href="javascript:;" class="clear-matches">清空</a></th></tr></thead></table></div>
                            <div class="seleFiveBoxScroll"><table class="selected-matches"><colgroup><col width="100"><col width="260"><col width="56"><col width="46"></colgroup><tbody></tbody></table></div>
                        </div>
                    </div>
                    <div class="seleFiveInfo">
                        <ul>
                            <li class="first">
                                <div class="passway">
                                    <p><a href="javascript:;" class="gg-type selected" data-orderType="0">自选</a><a href="javascript:;" class="gg-type" data-orderType="1">追号</a></p>
                                    <div class="multi-modifier-s multi">
                                        <a href="javascript:;" class="minus">-</a>
                                        <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                                        <a href="javascript:;" class="plus" data-max="10000">+</a>
                                    </div>
                                    倍，<span class="chase-div" style="display:none">追
                                    <div class="multi-modifier-s chase">
                                        <a href="javascript:;" class="minus">-</a>
                                        <label><input class="chase number" type="text" value="10" autocomplete="off"></label>
                                        <a href="javascript:;" class="plus" data-max="<?php echo count($chases) > 82 ? 82 : count($chases)?>">+</a>
                                    </div>
                                    期，</span>
                                </div>
                                <div class="numbox">共 <span class="bet-num main-color-s">0</span> 元</div>
                                <div class="k3-qr" style="display: none;margin-top:0;">
		                            <label for="setStatus"><input type="checkbox" checked class="setStatus" id="setStatus">中奖后停止追号</label>
		                            <div class="mod-tips"><i class="icon-font bubble-tip" tiptext="<strong>中奖追停：</strong>勾选后，您的追号方案中的某一期中奖后，后续追号订单将被撤销，资金返还你的账户中。如不勾选，系统一直帮您购买所有的追号投注任务。">&#xe613;</i></div>
		                        </div>
                            </li>
                        </ul>
                        <?php if($lotteryConfig[KS]['status']):?>
			            	<a id="pd_ks_buy" class="btn btn-main btn-betting submit <?php echo $showBind ? ' not-bind': '';?>">确认预约</a>
			            <?php else :?>
			            	<a id="pd_ks_buy" class="btn btn-main btn-betting btn-disabled <?php echo $showBind ? ' not-bind': '';?>">暂停预约</a>
			            <?php endif;?>   
                    </div>
                </div>
            </div>
        </div>
        <div class="wrap bet-k3-ft">
            <dl>
                <dt><i class="icon-font icon-arrow">&#xe62a;</i>今日开奖</dt>
                <dd>
                    <div class="k3-table-kj">
                    <?php for ($i = 0; $i < 4; $i++) {?>
                    	<table><colgroup><col width="15%"><col width="30%"><col width="25%"><col width="30%"></colgroup><thead><tr><th>期次</th><th>开奖号码</th><th>和值</th><th>形态</th></tr></thead><tbody></tbody></table>
                    <?php }?>
                    </div>
                </dd>
            </dl>
            <dl><dt><i class="icon-font icon-arrow">&#xe62a;</i>投注风险提示</dt><dd><p>若用户设置的为中奖后停止追号的方案，则在执行追号过程中，发生某期追号方案直到当期网站销售截止前二分钟仍不明确上期追号的中奖状态，则网站会对当期追号方案做继续追号的处理。敬请知悉由此产生的追号风险。</p><p>查看<a href="/help/index/b5-s14" target="_blank">《玩法介绍》</a></p></dd></dl>
        </div>
    <?php $this->load->view('v1.1/elements/pop-spring');?>
    <?php $this->load->view('v1.2/elements/common/footer_mid');?>
    <!--[if lt IE 9]><script type="text/javascript" src="../../caipiaoimg/v1.1/js/excanvas.js"></script><![endif]-->
    <script>
    $(function() {
		//继续投注
		<?php if ($codes) {
			$str = '';
			foreach ($codes as $key => $ballArr)  {?>
			$(".bet-type-link li:eq(<?php echo $key-1?>)").trigger('click');
			<?php foreach ($ballArr as $ball) {
				$str .= ",.bet-type-link-item:eq(".($key-1).") [data-num='".$ball."']";
			}
		}?>
		$("<?php echo substr($str, 1)?>").trigger('click');
		cx._basket_.renderBet();
		<?php }?>
    })
    </script>
</body>

</html>
