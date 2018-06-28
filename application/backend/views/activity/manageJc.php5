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
      		<li><a href="/backend/Activity/activityJc">活动概览</a></li>
      		<li class="current"><a href="/backend/Activity/manageJc">活动管理</a></li>
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
                  					<a href="javascript:;" class="btn-blue" id="createJc">新建活动</a>
                				</td>
              				</tr>
            			</tbody>
          			</table>
        		</div>
        		<div>
          		活动期次/赔付期次：<span><?php echo $total['allActivity']; ?>/<?php echo $total['payActivity']; ?></span>次&nbsp;&nbsp;参与总人数：<span><?php echo $total['totalPeople']; ?></span>人&nbsp;&nbsp;已赔付金额：<span><?php echo ParseUnit($total['totalPayMoney'], 1); ?></span>元
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
            			</colgroup>
            			<thead>
              				<tr>
                				<th>活动期次</th>
                				<th>比赛场次/方案</th>
                        <th>玩法</th>
                				<th>总赔付额</th>
                				<th>已认购额</th>
                				<th>参与人数</th>
                				<th>中奖状态</th>
                				<th>赔付状态</th>
                        <th>上架时间</th>
              				</tr>
            			</thead>
            			<tbody>
                      <?php if(!empty($result)):?>
                      <?php foreach ($result as $items):?>
              				<tr>
                				<td><?php echo $items['activity_issue']; ?></td>
                				<td><?php echo $items['week'] . $items['matchId']; ?>/<?php echo $items['plan']; ?></td>
                        <td><?php echo $items['playType']; ?></td>
                				<td><?php echo ParseUnit($items['pay_money'], 1); ?></td>
                				<td><?php echo ParseUnit($items['pay_money'] - $items['left_money'], 1); ?></td>
                				<td><?php echo $items['join_num']; ?></td>
                				<td><?php echo $items['statusMsg']; ?></td>
                				<td><?php echo $items['payStatusMsg']; ?></td>
                        <td><?php echo $items['startTime']; ?></td>
              				</tr>
                      <?php endforeach;?>
                      <?php endif; ?>
            			</tbody>
                  <tfoot>
                    <tr>
                      <td colspan="14">
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
  	<!-- 创建活动 start -->
  	<div class="pop-dialog" id="dialog-createJc" style="display:none;">
		<div class="pop-in">
			<div class="pop-head">
				<h2>新建活动</h2>
				<span class="pop-close" title="关闭">关闭</span>
			</div>
			<div class="pop-body">
				<div class="data-table-filter del-percent">
					<table>
						<colgroup>
							<col width="175">
			                <col width="175">
						</colgroup>
						<tbody>
							<tr>
								<td>活动期次：<input type="text" class="ipt w84" name="issue"></td>
								<td>
									赔付总金额：<input type="text" class="ipt w84" name="payMoney">
								</td>
							</tr>
							<tr>
								<td>比赛场次：<input type="text" class="ipt w84" name="mid"></td>
								<td>
									&nbsp;比赛方案：<input type="text" class="ipt w84" name="plan">
								</td>
							</tr>
							<tr>
								<td>上架时间：<input type="text" class="ipt Wdate1 w84 ipt-date" name="startDate"></td>
								<td>
									&nbsp;玩法选择：
									<select name="playType" id="playType" class="selectList w84">
										<option value="spf" selected>胜负平</option>
										<option value="rqspf">让球胜平负</option>
									</select>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="pop-foot tac">
				<a href="javascript:closePop();" class="btn-b-white">取消</a>
				<a href="javascript:;" class="btn-blue-h32 mlr15" id="confirmJc">创建活动</a>
			</div>
		</div>
	</div>
	<!-- 创建活动 end -->
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
$(function(){

	// 时间控件
	$(".Wdate1").focus(function(){
        dataPicker();
    });

	// 新建活动弹框 
    $("#createJc").click(function(){
    	popdialog("dialog-createJc");
		return false;
	});

	// 创建活动
	var selectTag = true;
	$("#confirmJc").click(function(){
    	var activity_issue = $('input[name="issue"]').val();
    	var pay_money = $('input[name="payMoney"]').val();
    	var mid = $('input[name="mid"]').val();
    	var plan = $('input[name="plan"]').val();
    	var startDate = $('input[name="startDate"]').val();
    	var playType = $("#playType").val();

    	if(selectTag){

    		closeTag = false;

    		$.ajax({
                type: 'post',
                url: '/backend/Activity/createJc',
                data: {activity_issue:activity_issue,pay_money:pay_money,mid:mid,plan:plan,startDate:startDate,playType:playType},

                success: function (response) {
                    var response = $.parseJSON(response);
                    if(response.status == 'y')
                    {
                        selectTag = true;
                        closePop();
                        alert(response.message);
                        window.location.reload();
                    }else{
                        selectTag = true;
                        alert(response.message);
                    }
                },
                error: function () {
                    selectTag = true;
                    alert('网络异常，请稍后再试');
                }
            });
    	}

	});

});
</script>