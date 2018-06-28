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
      		<li class="current"><a href="/backend/Activity/newActivityJc">活动概览</a></li>
      		<li><a href="/backend/Activity/newManageJc">活动管理</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
    	<ul>
      		<li style="display: block;">
        		<div class="data-table-filter mb10">
        			<form action="/backend/Activity/newActivityJc" method="get" id="search_form" onsubmit="return check()" >
	          			<table>
	            			<colgroup>
	              				<col width="220">
	              				<col width="220">
                                                <col width="220">
	            			</colgroup>
		            		<tbody>
			              		<tr>
			                		<td>
			                  			关 键 字：
			                  			<input type="text" class="ipt w108" name="name" placeholder="用户名/订单号" value="<?php echo $search['name'] ?>">
			                		</td>
			                		<td>
			                  			参与平台：
			                  			<select class="selectList w108" id="" name="platform">
                                                                <option value="" <?php if(!$search['platform']){echo 'selected';} ?>>所有</option>
			                    			<option value="0"  <?php if($search['platform'] === '0'){echo 'selected';} ?>>网页</option>
                                                                <option value="1" <?php if($search['platform'] === '1'){echo 'selected';} ?>>Android</option>
                                                                <option value="2" <?php if($search['platform'] === '2'){echo 'selected';} ?>>iOS</option>
                                                                <option value="3" <?php if($search['platform'] === '3'){echo 'selected';} ?>>M版</option>
			                  			</select>
			                		</td>
                                                        <td>
			                  			活动期次：
			                  			<input type="text" class="ipt w108" name="activity_issue" value="<?php echo $search['activity_issue'] ?>">
			                		</td>
                                                                                                        </tr>
			              		<tr>
                                                        <td>
                                                            购买时间：
                                                            <input type="text" class="ipt w150 ipt-date Wdate1" id="start_r_c" name="start_r_c" value="<?php echo $search['start_r_c'] ?>">
                                                            <span>至</span>
                                                            <input type="text" class="ipt w150 ipt-date Wdate1" id='end_r_c' name="end_r_c" value="<?php echo $search['end_r_c'] ?>">
                                                        </td>
			                		<td>
			                  			参与金额：
			                  			<input type="text" class="ipt w108" id='start_r_m' name="start_r_m" value="<?php echo $search['start_r_m'] ?>">
			                  			<span>至</span>
			                  			<input type="text" class="ipt w108" id='end_r_m' name="end_r_m" value="<?php echo $search['end_r_m'] ?>">
			                		</td>
			                		<td>
			                  			红包使用状态：
			                  			<select class="selectList w108" id="" name="hongbao">
                                                                <option value="" <?php if(!$search['hongbao']){echo 'selected';} ?>>所有</option>
			                    			<option value="1" <?php if($search['hongbao'] === '1'){echo 'selected';} ?>>已使用</option>
                                                                <option value="2" <?php if($search['hongbao'] === '2'){echo 'selected';} ?>>未使用</option>
                                                                <option value="3" <?php if($search['hongbao'] === '3'){echo 'selected';} ?>>已过期</option>
                                                                <option value="4" <?php if($search['hongbao'] === '4'){echo 'selected';} ?>>无</option>
			                  			</select>
			                		</td>
			                		<td>
			                  			<a href="javascript:;" class="btn-blue" onclick="$('#search_form').submit();">查询</a>
			                		</td>
			              		</tr>
		            		</tbody>
	          			</table>
          			</form>
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
                                        <col width="120">
                                        <col width="120">
                                        <col width="120">
            			</colgroup>
            			<thead>
              				<tr>
                				<th>用户名</th>
                                                <th>订单编号</th>
                				<th>活动期次</th>
                				<th>购买时间</th>
                				<th>购买金额</th>
                				<th>购彩平台</th>
                                                <th>中奖状态</th>
                				<th>赔付状态</th>
                                                <th>红包使用状态</th>
                				<th>详情</th>
              				</tr>
            			</thead>
	            		<tbody>
	            			<?php if(!empty($result)):?>
                      		<?php foreach ($result as $items):?>
	              			<tr>
                                                <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $items['uid']; ?>" class="cBlue"><?php echo $items['userName'];?></a></td>
	                			<td><?php echo $items['orderId'];?></td>
	                			<td><?php echo $items['jcbp_id'];?></td>
	                			<td><?php echo $items['pay_time'];?></td>
	                			<td><?php echo ParseUnit($items['money'], 1);?></td>
                                                <td><?php if($items['buyPlatform']==0){echo "网站";}elseif($items['buyPlatform']==1){echo "Android";}elseif($items['buyPlatform']==2){echo "Ios";}else{echo "M版";} ?></td>
	                			<td><?php echo $this->caipiao_status_cfg[$items['status']][0]?></td>
                                                <td><?php if($items['pay_status'] == '0'){echo "无"; }else{ echo "已赔付"; } ?></td>
                                                <td><?php if($items['rid']){
                                                    if($items['hongbaostatus'] == '2'){echo '已使用';}
                                                    elseif($items['hongbaostatus'] < '2' && $items['valid_end']>date("Y-m-d H:i:s")){echo "未使用";}
                                                    else{echo "已过期";}
                                                    }else{echo "无";} ?></td>
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
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
        // 时间控件
        $(".Wdate1").focus(function(){
            dataPicker();
        });
    });
    
    function check(){
        var startTime=$("#start_r_c").val();
        var endTime=$("#end_r_c").val();
        var start_r_m=$("#start_r_m").val();
        var end_r_m=$("#end_r_m").val();
        if(startTime && endTime){
            if(startTime>endTime){
                alert("起始时间必须小于结束时间");
                return false;
            }
        }
        if(start_r_m && end_r_m){
            if(parseInt(start_r_m) > parseInt(end_r_m)){
                alert("起始金额必须小于最大金额");
                return false;
            }
        }
        return true;
    }
</script>