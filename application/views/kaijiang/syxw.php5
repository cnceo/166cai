<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/help.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/kaijiang.css');?>" rel="stylesheet" type="text/css" />
<div class="wrap_in help-container lottery-detail">
    <div class="help-section clearfix">
        <?php $this->load->view('kaijiang/aside');?>
        <div class="article">
            <div class="help-content lottery-detail-syxw">
                <div class="lottery-detail-img">
                    <i class="icon-lottery"></i>
                </div>
                <div class="lottery-detail-txt">
                    <div class="lottery-detail-title">
                        <h1 class="lottery-detail-name">老11选5开奖结果</h1>
                        <a href="<?php echo $baseUrl?>syxw" target="_blank" class="btn btn-bet-small">立即投注</a>
                    </div>

                    <dl class="lottery-detail-dl">
                        <dt>第<?php echo $current['issue']?>期开奖时间：</dt>
                        <dd><?php $arr=array("日","一","二","三","四","五","六");
                        echo date('Y年m月d日 H:i ', strtotime($current['award_time']))."周".$arr[date("w",strtotime($current['award_time']))]?></dd>
                        <dt>第<?php echo $current['issue']?>期开奖号码：</dt>
                        <dd>
                            <div class="award-nums">
                            <?php foreach (explode(',', $current['awardNum']) as $red){?><span class="ball ball-red"><?php echo $red?></span><?php }?>
                            </div>
                        </dd> 
                    </dl>
                    <!-- <p class="lottery-detail-qs">今日已开<?php echo $count?>期，还剩<b><?php echo (78-$count)?></b>期</p> -->
                    <p class="fast-lottery-title">第<b><?php echo $issue;?></b>期正在销售&nbsp;&nbsp;&nbsp;&nbsp;
                    	距投注截止时间还有：
                    	<?php if(!empty($hour)):?>
                    	<b class="spec" id="hour"><?php echo $hour?></b>时
                    	<?php endif;?>
                    	<b class="spec" id="min"><?php echo $min?></b>分<b class="spec" id="sec"><?php echo $second?></b>秒</p>
                    <p class="trend-chart"><a target="_blank" href="http://caipiao2345.cjcp.com.cn/cjw11x5_qs/view/11x5_jiben-5-11ydj-11.html">走势图</a><i>&raquo;</i></p>
                    <div class="mod-box">
                        <div class="mod-box-hd">
                            <h2 class="mod-box-title">开奖详情</h2>
                            <div class="mod-box-subtxt">
                                <div class="date-select">
                                    <span>日期：</span>
                                    <dl class="simu-select">
                                        <dt><?php echo $date?><i class="arrow"></i></dt>
                                        <dd class="select-opt">
                                            <div class="select-opt-in">
                                                <?php foreach ($dateList as $value){?>
                                            		<a href="<?php echo $baseUrl?>kaijiang/syxw/<?php echo $value['date']?>"><?php echo $value['date']?></a>
                                            	<?php }?>
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="mod-box-bd">
                            <table>
                                <colgroup>
                                    <col width="25%">
                                    <col width="75%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="table-algin-c">期次</th>
                                        <th>开奖号码</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php for ($issue = 1; $issue <= 20; $issue++) {
                                $iss = str_pad($issue, 2, "0", STR_PAD_LEFT)?>
                                	<tr>
                                        <th><?php echo $iss?></th>
                                        <td>
                                        <?php if (!empty($data[$iss])) {
                                        foreach (explode(',', $data[$iss]['awardNum']) as $val) {?>
	                                        <span class="ball ball-red"><?php echo $val?></span>
	                                	<?php }
	                                	}?>
                                        </td>
                                    </tr>
                                <?php }?>
                                </tbody>
                            </table>
                            <table>
                                <colgroup>
                                    <col width="25%">
                                    <col width="75%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="table-algin-c">期次</th>
                                        <th>开奖号码</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php for ($issue = 21; $issue <= 40; $issue++) {
                                $iss = str_pad($issue, 2, "0", STR_PAD_LEFT)?>
                                	<tr>
                                        <th><?php echo $iss?></th>
                                        <td>
                                        <?php if (!empty($data[$iss])) {
                                        foreach (explode(',', $data[$iss]['awardNum']) as $val) {?>
	                                        <span class="ball ball-red"><?php echo $val?></span>
	                                	<?php }
	                                	}?>
                                        </td>
                                    </tr>
                                <?php }?>
                                </tbody>
                            </table>
                            <table>
                                <colgroup>
                                    <col width="25%">
                                    <col width="75%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="table-algin-c">期次</th>
                                        <th>开奖号码</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 <?php for ($issue = 41; $issue <= 60; $issue++) {
                                $iss = str_pad($issue, 2, "0", STR_PAD_LEFT)?>
                                	<tr>
                                        <th><?php echo $iss?></th>
                                        <td>
                                        <?php if (!empty($data[$iss])) {
                                        foreach (explode(',', $data[$iss]['awardNum']) as $val) {?>
	                                        <span class="ball ball-red"><?php echo $val?></span>
	                                	<?php }
	                                	}?>
                                        </td>
                                    </tr>
                                <?php }?>
                                </tbody>
                            </table>
                            <table>
                                <colgroup>
                                    <col width="25%">
                                    <col width="75%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="table-algin-c">期次</th>
                                        <th>开奖号码</th>
                                    </tr>
                                </thead>
                                <tbody>
                                     <?php for ($issue = 61; $issue <= 78; $issue++) {
	                                $iss = str_pad($issue, 2, "0", STR_PAD_LEFT)?>
	                                	<tr>
	                                        <th><?php echo $iss?></th>
	                                        <td>
	                                        <?php if (!empty($data[$iss])) {
	                                        foreach (explode(',', $data[$iss]['awardNum']) as $val) {?>
		                                        <span class="ball ball-red"><?php echo $val?></span>
		                                	<?php }
		                                	}?>
	                                        </td>
	                                    </tr>
	                                <?php }?>
                                    <tr>
                                        <th></th>
                                        <td><div style= "width: 100%; overflow: hidden;"></div></td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <td><div style= "width: 100%; overflow: hidden;"></div></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var intervalid;
$(function(){
	intervalid = setInterval("fun()", 1000);
})
function fun(time) { 
	var hour = parseInt($("#hour").html());
	if(isNaN(hour)) hour = 0;
	var min = parseInt($("#min").html());
	var sec = parseInt($("#sec").html());
	var time = hour*3600 + min*60 + sec;
	if (time == 0 || time%60 == 0) { 
	   location.reload()
	   clearInterval(intervalid); 
	} 
	time--;
	
	var m = Math.floor(time/60) < 0 ? 0 : Math.floor(time/60);
	if(m > 60){
		var h = Math.floor(m/60);
		m = m%60;
	}
	var s = time%60 < 0 ? 0 : time%60;
	$("#min").html(m); 
	$("#sec").html(s); 
}
</script>