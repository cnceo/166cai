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
      		<li class="current"><a href="/backend/Activity/activityJc">活动概览</a></li>
      		<li><a href="/backend/Activity/manageJc">活动管理</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mb10">
        			<form action="/backend/Activity/activityJc" method="get" id="search_form">
	          			<table>
	            			<colgroup>
	              				<col width="220">
	              				<col width="400">
	              				<col width="220">
	            			</colgroup>
		            		<tbody>
			              		<tr>
			                		<td>
			                  			关 键 字：
			                  			<input type="text" class="ipt w108" name="name" placeholder="用户名/订单号" value="<?php echo $search['name'] ?>">
			                		</td>
			                		<td colspan="2">
			                  			参与平台：
			                  			<select class="selectList w108" id="" name="platform">
			                    			<option value="0">网页</option>
			                  			</select>
			                		</td> 
			              		</tr>
			              		<tr>
			                		<td>
			                  			活动期次：
			                  			<input type="text" class="ipt w108" name="activity_issue" value="<?php echo $search['activity_issue'] ?>">
			                		</td>
			                		<td>
			                  			参与金额：
			                  			<input type="text" class="ipt w108" name="start_r_m" value="<?php echo $search['start_r_m'] ?>">
			                  			<span>至</span>
			                  			<input type="text" class="ipt w108" name="end_r_m" value="<?php echo $search['end_r_m'] ?>">
			                		</td>
			                		<td>
			                  			<a href="javascript:;" class="btn-blue" onclick="$('#search_form').submit();">查询</a>
			                		</td>
			              		</tr>
		            		</tbody>
	          			</table>
          			</form>
        		</div>
        		<div class="mt20">
          		总人数：<span><?php echo $total['number']; ?></span>人&nbsp;&nbsp;订单总金额：<span><?php echo ParseUnit($total['money'], 1); ?></span>元&nbsp;&nbsp;赔付金额：<span><?php echo ParseUnit($total['totalPayMoney'], 1); ?></span>元&nbsp;&nbsp;已进行<span><?php echo $total['issue']; ?></span>期
        		</div>
        		<div class="data-table-list mt10">
          			<table>
            			<colgroup>
			              	<col width="120">
			              	<col width="120">
			              	<col width="120">
			              	<col width="120">
			              	<col width="120">
			              	<col width="120">
			              	<col width="120">
            			</colgroup>
            			<thead>
              				<tr>
                				<th>用户名</th>
                				<th>活动期次</th>
                				<th>购买时间</th>
                				<th>购买金额</th>
                				<th>中奖状态</th>
                				<th>赔付金额</th>
                				<th>详情</th>
              				</tr>
            			</thead>
	            		<tbody>
	            			<?php if(!empty($result)):?>
                      		<?php foreach ($result as $items):?>
	              			<tr>
	                			<td><?php echo $items['userName'];?></td>
	                			<td><?php echo $items['activity_issue'];?></td>
	                			<td><?php echo $items['created'];?></td>
	                			<td><?php echo ParseUnit($items['money'], 1);?></td>
	                			<td><?php if($items['status'] == '0'){echo "等待开奖"; }elseif($items['status'] == '600'){echo "出票失败";}elseif($items['status'] == '1000'){echo "未中奖";}elseif($items['status'] == '2000'){echo "已中奖";} ?></td>
	                			<td><?php if($items['status'] == '0'){echo "---"; }elseif($items['status'] == '600' || $items['status'] == '2000'){echo "无"; }elseif($items['status'] == '1000' && $items['pay_status'] == '0'){echo "等待赔付"; }elseif($items['status'] == '1000' && $items['pay_status'] == '1'){echo "已赔付"; } ?></td>
	                			<td><a href="/backend/Management/orderDetail/?id=<?php echo $items['orderId']; ?>" class="cBlue">查看</a></td>
	              			</tr>
	              			<?php endforeach;?>
                      		<?php endif; ?>
	            		</tbody>
	            		<tfoot>
				            <tr>
				                <td colspan="11">
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
      		</li>
    	</ul>
  	</div>
  	<div class="page mt10 order_info">
      <?php echo $pages[0] ?>
    </div>
</div>