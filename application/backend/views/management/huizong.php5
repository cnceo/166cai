<?php $this->load->view("templates/head");
$this->load->driver('cache', array('adapter' => 'redis'));
$REDIS = $this->config->item('REDIS');
$lidArr = array(21406 => 'SYXW_ISSUE_TZ', 21407 => 'JXSYXW_ISSUE_TZ', 21408 => 'HBSYXW_ISSUE_TZ', 21421 => 'GDSYXW_ISSUE_TZ', 53 => 'KS_ISSUE_TZ',56 => 'JLKS_ISSUE_TZ',57 => 'JXKS_ISSUE_TZ',54 => 'KLPK_ISSUE_TZ', 55 => 'CQSSC_ISSUE_TZ', 42 => '', 43 => '', 
    		51 => 'SSQ_ISSUE', 23529 => 'DLT_ISSUE', 52 => 'FC3D_ISSUE', 33 => 'PLS_ISSUE', 35 => 'PLW_ISSUE', 10022 => 'QLC_ISSUE', 23528 => 'QXC_ISSUE', 11 => '', 19 => '', 44 => '', 45 => '');?>
<div class="frame-container" style="margin-left:0; font-size: 16px;">
		<div class="mod-tab-hd mt20">
			<ul>
				<li class="current"><a href="/backend/Management/huizong">出票汇总</a></li>
				<li><a href="/backend/Management/monitorTicket">单彩种监控</a></li>
				<li><a href="/backend/Management/chaseCancel">追号监控</a></li>
				<li><a href="/backend/Management/ticketLimit">出票限制</a></li>
			</ul>
		</div>
		<div class="mt10">
	        <div class="data-table-list mt10">
	            <table style="width: auto">
	                <colgroup><col width="140px"><col width="140px"><col width="140px"><col width="160px"><col width="140px"><col width="140px"><col width="140px"><col width="140px"><col width="140px"></colgroup>
	                <thead><tr><th>彩种</th><th>最近截止前未出</th><th>未出金额</th><th>最近截止时间</th><th>等待出票</th><th>正常出票中</th><th>异常订单</th><th>合买≥90%待出票</th><th>追号等待出票</th></tr></thead>
	                <tbody>
	                <?php foreach ($lidArr as $lid => $cacheName) {
	                	if (!empty($cacheName)) $cache = json_decode($this->cache->get($REDIS[$cacheName]), true);
	                	$url = "/backend/Management/monitorTicket?lid=".$lid;
	                	if ($data[$lid]) {?>
	                	<tr>
	                		<td><a class="cBlue" target="blank" href="<?php echo $url?>"><?php echo $this->caipiao_cfg[$lid]['name']?></a></td>
	                		<?php if ($data[$lid]['havenot'] > 0) {?>
	                			<td><a class="cBlue" target="blank" href="<?php echo $url?>&havenot=1"><?php echo $data[$lid]['havenot']?></a></td>
	                		<?php }else {?>
	                			<td></td>
	                		<?php }?>
	              	        <td><?php echo ($data[$lid]['summoney'] > 0) ? $data[$lid]['summoney']/100 : ''?></td>
	                		<td><?php echo empty($data[$lid]['havenot']) ? '--' : date('Y-m-d H:i', strtotime($data[$lid]['endTime']))?></td>
	                		<?php if ($data[$lid]['wait'] > 0) {?>
	                			<td><a class="cBlue" target="blank" href="<?php echo $url?>&status=0"><?php echo $data[$lid]['wait']?></a></td>
							<?php }else {?>
								<td></td>
							<?php }?>
							<?php if ($data[$lid]['draw'] > 0) {?>
	                			<td><a class="cBlue" target="blank" href="<?php echo $url?>&status=240"><?php echo $data[$lid]['draw']?></a></td>
							<?php }else {?>
								<td></td>
							<?php }?>
	                		<?php if ($data[$lid]['problem'] > 0) {?>
	                			<td><a class="cBlue" target="blank" href="<?php echo $url?>"><?php echo $data[$lid]['problem']?></a></td>
							<?php }else {?>
								<td></td>
							<?php }?>
	                		<?php if ($data[$lid]['uwait'] > 0) {?>
	                			<td><a class="cBlue" target="blank" href="/backend/Management/manageUnited?united_lid=<?php echo $data[$lid]['lid']?>&united_buyPlatform=-1&united_issue=<?php echo $issue[$lid]?>&united_status=40&united_start_time=<?php echo date("Y-m-d 00:00:00", strtotime('-13 days'))?>&united_end_time=<?php echo date('Y-m-d 23:59:59')?>"><?php echo $data[$lid]['uwait']?></a></td>
							<?php }else {?>
								<td></td>
							<?php }?>
	                		<?php if ($data[$lid]['cwait'] > 0) {?>
	                			<td><a class="cBlue" target="blank" href="/backend/Management/chaseCancel?&lid=<?php echo $lid?>&selectLid=<?php echo $lid?>&issue=<?php echo $cache['cIssue']['seExpect']?>"><?php echo $data[$lid]['cwait']?></a></td>
							<?php }else {?>
								<td></td>
							<?php }?>
						</tr>
					<?php }else {?>
					<tr>
						<td><a class="cBlue" target="blank" href="<?php echo $url?>"><?php echo $this->caipiao_cfg[$lid]['name']?></a>
						<td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
					</tr>
					<?php }
					}?>
	                </tbody>
	            </table>
	        </div>
	    </div>
    </div>