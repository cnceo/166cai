<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">竞彩活动</a></div>
<div class="mod-tab mod-tab-s mt20 mb20">
  <div class="mod-tab-hd">
      <ul>
          <li class="current"><a href="/backend/Activity/newActivityJc">不中包赔</a></li>
          <li><a href="/backend/Activity/activityJj">竞彩加奖</a></li>
      </ul>
    </div>
</div>
<div class="mod-tab mt20 mb20">
	<div class="mod-tab-hd">
    	<ul>
      		<li><a href="/backend/Activity/newActivityJc">活动概览</a></li>
      		<li class="current"><a href="/backend/Activity/newManageJc">活动管理</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter">
          		    <table>
            			<tbody>
                                    <tr>
                                        <td>
                                            <a href="/backend/Activity/createJcbp" class="btn-blue" id="createJc">新建活动</a>
                                        </td>
                                    </tr>
            			</tbody>
          		    </table>
        		</div>
        		<div class="data-table-list mt10">
          			<table>
            			<colgroup>
                                    <col width="40">
                                    <col width="150">
                                    <col width="120">
                                    <col width="80">
                                    <col width="80">
                                    <col width="100">
                                    <col width="100">
                                    <col width="100">
                                    <col width="100">
            			</colgroup>
            			<thead>
                                    <tr>
                                        <th>活动期次</th>
                                        <th>比赛场次/方案</th>
                                        <th>玩法</th>
                                        <th>参与人数</th>
                                        <th>参与金额</th>
                                        <th>中奖状态</th>
                                        <th>活动成本</th>
                                        <th>活动开始时间</th>
                                        <th>活动结束时间</th>
                                    </tr>
            			</thead>
            			<tbody>
                      <?php if(!empty($result)):?>
                      <?php foreach ($result as $items):?>
              				<tr>
                				<td><?php echo $items['id']; ?></td>
                				<td><?php echo $items['plan']; ?></td>
                                                <td><?php echo $items['playType']; ?></td>
                                                <td><?php echo $items['joinNum']; ?></td>
                				<td><?php echo ParseUnit($items['buyMoney']*$items['joinNum'], 1); ?></td>
                				<td><?php echo $items['status']; ?></td>
                				<td><?php echo ParseUnit($items['cost'],1); ?></td>
                                                <td><?php echo $items['startTime']; ?></td>
                                                <td><?php echo $items['endTime']; ?></td>
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