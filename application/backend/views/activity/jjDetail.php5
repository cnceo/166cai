<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">竞彩活动</a></div>
<div class="mod-tab mt20 mb20">
  <div class="mod-tab-hd">
      <ul>
          <li><a href="/backend/Activity/newActivityJc">不中包赔</a></li>
          <li class="current"><a href="/backend/Activity/activityJj">竞彩加奖</a></li>
      </ul>
    </div>
</div>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li><a href="/backend/Activity/activityJj">活动概览</a></li>
      		<li class="current"><a href="/backend/Activity/manageJj">活动管理</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mt20">
              <table>
                <tbody>
                  <tr>
                    <td width="220">活动期次：<span><?php echo $id; ?></span>次</td>
                    <td width="280">活动开始时间：<span><?php echo $startTime; ?></span></td>
                    <td width="280">活动结束时间：<span><?php echo $endTime; ?></span></td>
                    <td width="220">彩种：<span><?php echo $lname; ?></span></td>
                    <td>加奖玩法：<span><?php echo $playTypeName; ?></span></td>
                  </tr>
                  <tr>
                    <td>用户统计：<span><?php echo $num; ?></span></td>
                    <td>中奖总额（税后）：<span><?php echo ParseUnit($margin, 1); ?></span></td>
                    <td>加奖总额（元）：<span><?php echo ParseUnit($add_money, 1); ?></span></td>
                    <td>活动状态（元）：<span><?php echo $status; ?></span></td>
                    <td>加奖平台：<span><?php echo $buyPlatform; ?></span></td>
                  </tr>
                </tbody>  
              </table>
            </div>
        		<div class="mt10">
              <table class="data-table-list">
                  <colgroup>
                      <col width="250">
                      <col width="300">
                  </colgroup>
                  <thead>
                      <tr>
                          <th>订单中奖金额（元）</th>
                          <th>加奖金额（元）</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php if(!empty($params)):?>
                      <?php foreach ($params as $detail):?>
                      <tr>
                          <?php if($detail['max'] != '*'): ?>
                          <td><?php echo ParseUnit($detail['min'], 1);?>&lt;奖金≤<?php echo ParseUnit($detail['max'], 1);?></td>
                          <td><?php echo ParseUnit($detail['val'], 1);?></td>
                          <?php else: ?>
                          <td>奖金&gt;<?php echo ParseUnit($detail['min'], 1);?></td>
                          <td><?php echo ParseUnit($detail['val'], 1);?></td>
                          <?php endif; ?>
                      </tr>
                      <?php endforeach;?>
                      <?php endif; ?>
                  </tbody>
              </table>
            </div>
      		</li>
    	</ul>
  	</div>
</div>