<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/help.min.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/kaijiang.min.css');?>" rel="stylesheet" type="text/css" />
<?php $numArr = array(1 => 'A', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10', 11 => 'J', 12 => 'Q', 13 => 'K');?>
<div class="wrap lottery-detail">
    <div class="l-frame">
        <?php $this->load->view('v1.1/kaijiang/aside');?>
        <div class="l-frame-cnt">
            <div class="lottery-detail-main lottery-klpk">
                <div class="lottery-detail-img">
                    <div class="lottery-img">
                        <svg width="320" height="320">
								<image xlink:href="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.svg'); ?>" src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/lottery-logo.png'); ?>" width="320" height="320" />
                        </svg>
                    </div>
                </div>
                <div class="lottery-detail-txt">
                    <div class="lottery-detail-title">
                        <h1 class="lottery-detail-name">快乐扑克开奖结果</h1>
                        <a href="<?php echo $baseUrl?>klpk" target="_blank" class="btn-ss btn-ss-bet">立即预约</a>
                    </div>
					<dl class="lottery-detail-dl">
                        <dt>第<?php echo $info['cIssue']['seExpect']?>期开奖时间：</dt>
                        <dd><?php $arr=array("日","一","二","三","四","五","六");
                        echo date('Y年m月d日 H:i ', $info['cIssue']['awardTime']/1000)."周".$arr[date("w", $info['cIssue']['awardTime']/1000)]?></dd>
                        <dt>第<?php echo $info['lIssue']['seExpect']?>期开奖号码：</dt>
                        <dd>
                        	<div class="klpk-num">
                        		<?php $awardNum = explode('|', $info['lIssue']['awardNumber']);
								$award[] = explode(',', $awardNum[0]);
								$award[] = explode(',', $awardNum[1]);
								for ($i = 0; $i < 3; $i++) {?>
									<span class="klpk-num-<?php echo strtolower($award[1][$i])?>"><?php echo $numArr[(int)$award[0][$i]]?></span>
								<?php }?>
                            </div>
                        </dd> 
                    </dl>
                    <p class="fast-lottery-title">第<b><?php echo $info['cIssue']['seExpect'];?></b>期正在销售&nbsp;&nbsp;&nbsp;&nbsp;距投注截止时间还有：<span id="time_rest"></span></p>
                    <div class="mod-box">
                        <div class="mod-box-hd">
                            <h2 class="mod-box-title">开奖详情</h2>
                            <div class="mod-box-subtxt">
                                <div class="date-select">
                                    <span>日期：</span>
                                    <dl class="simu-select">
                                        <dt><?php echo $date;?><i class="arrow"></i></dt>
                                        <dd class="select-opt">
                                            <div class="select-opt-in">
                                            <?php $date = strtotime('now');
                                            while ($date > strtotime('2016-11-22')) {?>
												<a href="<?php echo $baseUrl?>kaijiang/klpk/<?php echo date('Y-m-d', $date)?>"><?php echo date('Y-m-d', $date)?></a>
											<?php $date = $date - 86400;
											}?>
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="mod-box-bd">
                        	<div class="klpk-table-kj">
	                            <table>
	                                <colgroup><col width="20%"><col width="50%"><col width="30%"></colgroup>
	                                <thead><tr><th>期次</th><th>号码</th><th>形态</th></tr></thead>
	                                <tbody>
	                                <?php for ($issue = 1; $issue <= 22; $issue++) {
	                                $iss = str_pad($issue, 2, "0", STR_PAD_LEFT);
	                                $award = array();?>
	                                	<tr>
	                                        <th><?php echo $iss?></th>
	                                        <?php if (!empty($data[$iss]['awardNum'])) {?>
	                                        <td>
	                                        	<div class="klpk-num">
		                                        <?php $awardNum = explode('|', $data[$iss]['awardNum']);
												$award = array(explode(',', $awardNum[0]), explode(',', $awardNum[1]));
												for ($i = 0; $i < 3; $i++) {?>
													<span class="klpk-num-<?php echo strtolower($award[1][$i])?>"><?php echo $numArr[(int)$award[0][$i]]?></span>
												<?php }?>
												</div>
		                                    </td>
		                                    <td>
		                                    <?php sort($award[0]);
		                                    	$c0 = count(array_unique(array_values($award[0])));
		                                    	$c1 = count(array_unique(array_values($award[1])));
		                                    	if ($c0 == 1) {
		                                    		echo "<span class='bz'>豹子</span>";
		                                    	}elseif ($c0 == 2) {
		                                    		echo "<span class='dz'>对子</span>";
		                                    	}elseif ((($award[0][1] == $award[0][0] + 1) && ($award[0][2] == $award[0][1] + 1)) || implode(',', $award[0]) === '01,12,13') {
		                                    		if ($c1 == 1){
		                                    			echo "<span class='ths'>同花顺</span>";
		                                    		}else {
		                                    			echo "<span class='sz'>顺子</span>";
		                                    		}
		                                    	}elseif ($c1 == 1) {
													echo "<span class='th'>同花</span>";
												}else {
		                                    		echo '散牌';
		                                    	}?>
	                                        </td>
	                                        <?php }else{?>
	                                        	<td></td><td></td>
	                                        <?php }?>
	                                    </tr>
	                                <?php unset($awardArr);
	                                }?>
	                                </tbody>
	                            </table>
	                            <table>
	                                <colgroup><col width="20%"><col width="50%"><col width="30%"></colgroup>
	                                <thead><tr><th>期次</th><th>号码</th><th>形态</th></tr></thead>
	                                <tbody>
	                                <?php for ($issue = 23; $issue <= 44; $issue++) {
	                                	$award = array();?>
	                                	<tr>
	                                        <th><?php echo $issue?></th>
	                                        <?php if (!empty($data[$issue]['awardNum'])) {?>
	                                        <td>
	                                        	<div class="klpk-num">
		                                        <?php $awardNum = explode('|', $data[$issue]['awardNum']);
												$award = array(explode(',', $awardNum[0]), explode(',', $awardNum[1]));
												for ($i = 0; $i < 3; $i++) {?>
													<span class="klpk-num-<?php echo strtolower($award[1][$i])?>"><?php echo $numArr[(int)$award[0][$i]]?></span>
												<?php }?>
												</div>
		                                    </td>
		                                    <td>
		                                    <?php sort($award[0]);
		                                    	$c0 = count(array_unique(array_values($award[0])));
		                                    	$c1 = count(array_unique(array_values($award[1])));
		                                    	if ($c0 == 1) {
		                                    		echo "<span class='bz'>豹子</span>";
		                                    	}elseif ($c0 == 2) {
		                                    		echo "<span class='dz'>对子</span>";
		                                    	}elseif ((($award[0][1] == $award[0][0] + 1) && ($award[0][2] == $award[0][1] + 1)) || implode(',', $award[0]) === '01,12,13') {
		                                    		if ($c1 == 1){
		                                    			echo "<span class='ths'>同花顺</span>";
		                                    		}else {
		                                    			echo "<span class='sz'>顺子</span>";
		                                    		}
		                                    	}elseif ($c1 == 1) {
													echo "<span class='th'>同花</span>";
												}else {
		                                    		echo '散牌';
		                                    	}?>
	                                        </td>
	                                        <?php }else{?>
	                                        	<td></td><td></td>
	                                        <?php }?>
	                                    </tr>
	                                <?php unset($awardArr);
	                                }?>
	                                </tbody>
	                            </table>
	                            <table>
	                                <colgroup><col width="20%"><col width="50%"><col width="30%"></colgroup>
	                                <thead><tr><th>期次</th><th>号码</th><th>形态</th></tr></thead>
	                                <tbody>
	                                 <?php for ($issue = 45; $issue <= 66; $issue++) {
	                                	$award = array();?>
	                                	<tr>
	                                        <th><?php echo $issue?></th>
	                                        <?php if (!empty($data[$issue]['awardNum'])) {?>
	                                        <td>
	                                        	<div class="klpk-num">
		                                        <?php $awardNum = explode('|', $data[$issue]['awardNum']);
												$award = array(explode(',', $awardNum[0]), explode(',', $awardNum[1]));
												for ($i = 0; $i < 3; $i++) {?>
													<span class="klpk-num-<?php echo strtolower($award[1][$i])?>"><?php echo $numArr[(int)$award[0][$i]]?></span>
												<?php }?>
												</div>
		                                    </td>
		                                    <td>
		                                    <?php sort($award[0]);
		                                    	$c0 = count(array_unique(array_values($award[0])));
		                                    	$c1 = count(array_unique(array_values($award[1])));
		                                    	if ($c0 == 1) {
		                                    		echo "<span class='bz'>豹子</span>";
		                                    	}elseif ($c0 == 2) {
		                                    		echo "<span class='dz'>对子</span>";
		                                    	}elseif ((($award[0][1] == $award[0][0] + 1) && ($award[0][2] == $award[0][1] + 1)) || implode(',', $award[0]) === '01,12,13') {
		                                    		if ($c1 == 1){
		                                    			echo "<span class='ths'>同花顺</span>";
		                                    		}else {
		                                    			echo "<span class='sz'>顺子</span>";
		                                    		}
		                                    	}elseif ($c1 == 1) {
													echo "<span class='th'>同花</span>";
												}else {
		                                    		echo '散牌';
		                                    	}?>
	                                        </td>
	                                        <?php }else{?>
	                                        	<td></td><td></td>
	                                        <?php }?>
	                                    </tr>
	                                <?php unset($awardArr);
	                                 }?>
	                                </tbody>
	                            </table>
	                            <table>
	                               	<colgroup><col width="20%"><col width="50%"><col width="30%"></colgroup>
	                                <thead><tr><th>期次</th><th>号码</th><th>形态</th></tr></thead>
	                                <tbody>
	                                     <?php for ($issue = 67; $issue <= 88; $issue++) {
	                                     if ($issue <= 88) {
		                                	$award = array();?>
	                                	<tr>
	                                        <th><?php echo $issue?></th>
	                                        <?php if (!empty($data[$issue]['awardNum'])) {?>
	                                        <td>
	                                        	<div class="klpk-num">
		                                        <?php $awardNum = explode('|', $data[$issue]['awardNum']);
												$award = array(explode(',', $awardNum[0]), explode(',', $awardNum[1]));
												for ($i = 0; $i < 3; $i++) {?>
													<span class="klpk-num-<?php echo strtolower($award[1][$i])?>"><?php echo $numArr[(int)$award[0][$i]]?></span>
												<?php }?>
												</div>
		                                    </td>
		                                    <td>
		                                    <?php sort($award[0]);
		                                    	$c0 = count(array_unique(array_values($award[0])));
		                                    	$c1 = count(array_unique(array_values($award[1])));
		                                    	if ($c0 == 1) {
		                                    		echo "<span class='bz'>豹子</span>";
		                                    	}elseif ($c0 == 2) {
		                                    		echo "<span class='dz'>对子</span>";
		                                    	}elseif ((($award[0][1] == $award[0][0] + 1) && ($award[0][2] == $award[0][1] + 1)) || implode(',', $award[0]) === '01,12,13') {
		                                    		if ($c1 == 1){
		                                    			echo "<span class='ths'>同花顺</span>";
		                                    		}else {
		                                    			echo "<span class='sz'>顺子</span>";
		                                    		}
		                                    	}elseif ($c1 == 1) {
													echo "<span class='th'>同花</span>";
												}else {
		                                    		echo '散牌';
		                                    	}?>
	                                        </td>
	                                        <?php }else{?>
	                                        	<td></td><td></td>
	                                        <?php }?>
	                                    </tr>
		                                <?php unset($awardArr);
			                                }else {?>
			                                <tr><th></th><td></td><td></td></tr>
			                            <?php }
	                                     }?>
	                                </tbody>
	                            </table>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>
<script>
var intervalid, time = <?php echo $info['cIssue']['seFsendtime']/1000 - time()?>;
$(function(){
	fun();
	intervalid = setInterval("fun()", 1000);
})
function fun() { 
	time = parseInt(time, 10);
	if (time <= 0 || time%60 == 0) { 
	   location.reload()
	   clearInterval(intervalid); 
	} 
	time--;
	time = (time < 0) ? 0 : time;
	var h = Math.floor(time/3600), m = Math.floor((time - h * 3600)/60), s = time - h * 3600 - m * 60, str = '';
	if (h > 0) {
		str += "<b class='spec'>"+h+"</b>时";
	}
	str += "<b class='spec'>"+m+"</b>分<b class='spec'>"+s+"</b>秒</p>";
	$("#time_rest").html(str);
}
</script>