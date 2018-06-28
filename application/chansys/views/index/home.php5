<?php $this->load->view('template/header');?>
<?php 
$tableHead = array(
    'unit_price' => '分成比例/单价',
    'balance_active' => '新增激活',
    'balance_reg' => '注册',
    'balance_real' => '实名',
    'balance_yj' => '分成金额（元）',
    'balance_amount' => '渠道购彩（元）',
    'partner_lottery_num' => '渠道购彩总人数',
    'partner_active_lottery_num' => '新用户购彩人数',
    'partner_curr_lottery_total_amount' => '新用户购彩总额',
);
?>
<style type="text/css">
	.mod-tab-bd ul li{display: block;}
</style>	
<div class="frame-container">
	<div id="app">
	    <div class="mod-tab mt20">
	        <div class="mod-tab-bd">
	            <ul>
	                <li>
	                  <div class="data-table-filter">
	                        <table width="100%">
	                            <tbody>
	                                <tr>
	                                <form method="get" name='listSearch'>
	                                    <td>
	                                      涉及渠道:<select class="selectList w222 mr20"  name="channel_id" id="channel_id">
                            <option value="">全部</option>
                            <?php foreach ($channels as $key => $channel): ?>
                            <option value="<?php echo $key ?>" <?php if($search['channel_id'] === "{$key}"): echo "selected"; endif;    ?>><?php echo $channel['name']; ?></option>   
                            <?php endforeach; ?>
                        </select>
	                                    </td>
	                                    <td>
	                                        查询时间：
	                                        <span class="ipt-date w184"><input type="text" class="ipt Wdate1" name='start_time' value="<?php echo $search['start_time'];?>" ><i></i></span>
	                                        <span class="ml8 mr8">至</span>
	                                        <span class="ipt-date w184"><input type="text" class="ipt Wdate1" name='end_time'  value="<?php echo $search['end_time'];?>"><i></i></span>
	                                    </td>
	                                    <td>
	                                        <a href="javascript:;" class="btn-blue" id="doSearch">查询</a>
	                                        <a href="<?php echo $this->config->item('base_url').'/index/export?start_time='.$search['start_time'].'&end_time='.$search['end_time'] . '&channel_id='.$search['channel_id'];?>" class="btn-blue ml20" id='doExport'>数据导出</a>
	                                        <a href="<?php echo $this->config->item('base_url').'/index/writeExcel?start_time='.$search['start_time'].'&end_time='.$search['end_time'] . '&channel_id='.$search['channel_id'];?>" class="btn-blue ml20" id='doBalance'>导出对账单</a>
	                                    </td>
	                                </form>
	                                </tr>
	                            </tbody>
	                        </table>
	                    </div>
	                    <div class="data-table-list mt10" >
	                        <div class="overflow-x" >
	                            <table>
	                                <caption class="capwhite">
	                                	<?php if(in_array('balance_active', $userFields)):?><b>新增激活：<span><?php echo $total['balance_active'] ;?></span></b><?php endif;?>
	                                	<?php if(in_array('balance_reg', $userFields)):?><b>注册：<span><?php echo $total['balance_reg'] ;?></span></b><?php endif;?>
	                                	<?php if(in_array('balance_real', $userFields)):?><b>实名：<span><?php echo $total['balance_real'] ;?></span></b><?php endif;?>
	                                	<?php if(in_array('balance_yj', $userFields)):?><b>分成金额（元）：<span><?php echo m_format($total['balance_yj']) ;?></span></b><?php endif;?>
	                                	<?php if(in_array('balance_amount', $userFields)):?><b>渠道购彩（元）：<span><?php echo m_format($total['balance_amount']) ;?></span></b><?php endif;?>
	                                	<?php if(in_array('partner_lottery_num', $userFields)):?><b>渠道购彩总人数：<span><?php echo $total['partner_lottery_num'] ;?></span></b><?php endif;?>
	                                	<?php if(in_array('partner_active_lottery_num', $userFields)):?><b>新用户购彩人数：<span><?php echo $total['partner_active_lottery_num'] ;?></span></b><?php endif;?>
	                                	<?php if(in_array('partner_curr_lottery_total_amount', $userFields)):?><b>新用户购彩总额：<span><?php echo m_format($total['partner_curr_lottery_total_amount']) ;?></span></b><?php endif;?>                      
	                                </caption>
	                                <thead>
	                                    <tr>
	                                        <th>日期</th>
	                                        <th>渠道名称</th>
	                                        <?php 
	                                           foreach ($tableHead as $key => $val) {
	                                               if(in_array($key, $userFields)) {
	                                                   echo "<th>{$val}</th>";
	                                               }
	                                           }
	                                        ?>
	                                    </tr>
	                                </thead>
	                                <tbody>
	                                <?php foreach ($result as $k => $v): ?>
	                                    <tr>
	                                        <td><?php echo $v['date'] ;?></td>
	                                        <td><?php echo $channels[$v['channel_id']]['name'];?></td>
	                                        <?php if(in_array('unit_price', $userFields)):?><td><?php if ($v['settle_mode'] == 1) { echo m_format($v['unit_price']);} else { echo $v['unit_price'] . '%';}?></td><?php endif;?>
	                                        <?php if(in_array('balance_active', $userFields)):?><td><?php if($v['cpstate'] == '2') { echo $v['balance_active'];} else { echo '--';}?></td><?php endif;?>
	                                        <?php if(in_array('balance_reg', $userFields)):?><td><?php echo $v['balance_reg'];?></td><?php endif;?>
	                                        <?php if(in_array('balance_real', $userFields)):?><td><?php echo $v['balance_real'];?></td><?php endif;?>
	                                        <?php if(in_array('balance_yj', $userFields)):?><td><?php if($v['cpstate'] == '2' || $v['settle_mode'] == '2') { echo m_format($v['balance_yj']);} else { echo '--';}?></td><?php endif;?>
	                                        <?php if(in_array('balance_amount', $userFields)):?><td><?php echo m_format($v['balance_amount']);?></td><?php endif;?>
	                                        <?php if(in_array('partner_lottery_num', $userFields)):?><td><?php echo $v['partner_lottery_num'];?></td><?php endif;?>
	                                        <?php if(in_array('partner_active_lottery_num', $userFields)):?><td><?php echo $v['partner_active_lottery_num'];?></td><?php endif;?>
	                                        <?php if(in_array('partner_curr_lottery_total_amount', $userFields)):?><td><?php echo m_format($v['partner_curr_lottery_total_amount']);?></td><?php endif;?>
	                                    </tr>
	                                <?php endforeach; ?>
	                                </tbody>
								    <tfoot>
								      <tr >
								        <td colspan="<?php echo count($userFields) + 2;?>">
								          <div class="stat">
								            <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
								            <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
								            <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
								          </div>
								        </td>
								      </tr>
								    </tfoot>
	                            </table>
	                        </div>
	                        <div class="page mt10 order_info" >
								<?php echo $pages[0] ?>
							</div>
	                    </div>
	                </li>
	            </ul>
	        </div>
	    </div>
	</div>
</div>
<?php $this->load->view('template/side');?>	
<script type="text/javascript">
$(function(){
	//日历
	$(".Wdate1").focus(function(){WdatePicker({dateFmt: "yyyy-MM-dd HH:mm:ss"});});	
	$('#doSearch').click(function(){
		$('form[name=listSearch]').submit();
	});
});	
</script>
<?php $this->load->view('template/footer');?>