<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="javascript:;">渠道分析 </a>&nbsp;&gt;&nbsp;<a href="/backend/ChannelAnalysis/manage">渠道管理 </a>&nbsp;&gt;&nbsp;<a href="javascript:;">渠道日志</a></div>
<div class="data-table-list mt10">
  <table>
    <colgroup>
      <col width="130" />
      <col width="130" />
      <col width="100" />
      <col width="100">
      <col width="120" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
    </colgroup>
    <thead>
      <tr>
      	<th>修改时间</th>
        <th>渠道名称</th>
        <th>结算方式</th>
        <th>单价(CPA,元)</th>
        <th>分成比例（CPS）</th>
        <th>注册时限（CPS，天）</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($list as $val):?>
      <tr>
      	<td style="display:none">
      		<div id="channelid">
				<?php echo $val['channel_id'];?>
			</div>
		</td>
		 <td>
	        <div class="table-modify">
	            <p class="table-modify-txt"><?php echo $val['change_time']; ?></p>
	         </div>
        </td>
        <td>
	        <div class="table-modify">
	            <p class="table-modify-txt"><?php echo $val['name']; ?></p>
	         </div>
        </td>
        <td>
        	 <div class="table-modify">
	            <p class="table-modify-txt"><?php echo $settleModeArr[$val['settle_mode']]; ?></p>
	         </div>
        </td>		
		<td>
			<?php if($val['settle_mode'] == 1){ ?>
        	<div class="table-modify">
	            <p class="table-modify-txt"><?php echo number_format($val['unit_price']/100,2); ?></p>
	         </div>
			<?php } else{ ?>
			<div>N/A</div>
			<?php } ?>
        </td>
        <td>
			<?php if($val['settle_mode'] == 2){ ?>
        	<div class="table-modify">
	            <p class="table-modify-txt"><?php echo number_format($val['share_ratio'],2); ?>%</p>
	         </div>
			<?php } else{ ?>
			<div>N/A</div>
			<?php } ?>
	    </td>
        <td>
      <?php if($val['settle_mode'] == 2){ ?>
          <div class="table-modify">
              <p class="table-modify-txt"><?php echo '≤'.$val['reg_time']; ?></p>
           </div>
      <?php } else{ ?>
      <div>N/A</div>
      <?php } ?>
      </td>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
</div>


</body>
</html>