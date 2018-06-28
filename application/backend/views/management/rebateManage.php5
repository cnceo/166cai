<?php
    $this->load->view("templates/head");
    $level = array(
    	'' => '不限',
    	'1' => '一级',
    	'2' => '二级'
    );
    $status = array(
    	''  => '不限',
    	'0' => '正常',
    	'1' => '停止返点',
    );
?>
<div class="path">您的位置：<a href="">运营管理</a>&nbsp;&gt;&nbsp;<a href="">推广管理</a></div>
<div class="data-table-filter mt10">
<form action="/backend/Management/rebateManage" method="get"  id="search_form">
  <table>
    <tbody>
    <tr>
      <td>
        用户信息：
        <input type="text" placeholder="用户名/真实姓名/手机号/代理编号" name="info" value="<?php echo $search['info'];?>" class="ipt w222">
      </td>
      <td>
        代理级别：
        <select class="selectList w95" id="" name="level">
        	<?php foreach ($level as $key => $val): ?>
            <option value="<?php echo $key; ?>" <?php if ($search['level'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?></option>
            <?php endforeach; ?>
        </select></td>
      <td>
        申请时间：<input type="text" class="ipt ipt-date w184 Wdate1"  name='start_time' value="<?php echo $search['start_time'] ?>" />
        <span class="ml8 mr8">至</span>
        <input type="text" class="ipt ipt-date w184 Wdate1" name='end_time' value="<?php echo $search['end_time'] ?>" />
      </td>
    </tr>
    <tr>
      <td>
        销售金额：
        <input type="text" class="ipt w95" name="start_money" value="<?php echo $search['start_money'] ?>">
        <span class="ml8 mr8">至</span>
        <input type="text" class="ipt w95" name="end_money" value="<?php echo $search['end_money'] ?>">
      </td>
      <td>
        代理状态：
        <select class="selectList w95" id="" name="stop_flag">
          	<?php foreach ($status as $key => $val): ?>
            <option value="<?php echo $key; ?>" <?php if ($search['stop_flag'] === "{$key}"): echo "selected"; endif; ?>><?php echo $val; ?></option>
            <?php endforeach; ?>
        </select>
      </td>
      <td>
        <a href="javascript:;" class="btn-blue fr" id="addRebate">添加代理</a>
        <a id="search" href="javascript:void(0);" class="btn-blue ml20" onclick="">查询</a>
      </td>
    </tr>
    </tbody>
  </table>
  </form>
</div>
<div class="data-table-list mt10">
  <table>
    <colgroup>
      <col width="7%">
      <col width="7%">
      <col width="7%">
      <col width="7%">
      <col width="9%">
      <col width="13%">
      <col width="8%">
      <col width="12%">
      <col width="12%">
      <col width="13%">
      <col width="5%">
    </colgroup>
    <thead>
      <tr>
        <th>代理编号</th>
        <th>代理级别</th>
        <th>用户名</th>
        <th>真实姓名</th>
        <th>手机号码</th>
        <th>申请时间</th>
        <th>上级用户</th>
        <th>用户销量（元）</th>
        <th>累积收入（元）</th>
        <th>操作</th>
        <th>详情</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($lists as $value):?>
      <tr>
       <td><?php echo $value['id'];?></td>
       <td><?php if($value['puid']){ echo '二级';}else{ echo '一级';}?></td>
       <td><a class="cBlue" href="/backend/User/user_manage/?uid=<?php echo $value['uid'];?>" target="_blank"><?php echo $value['uname'];?></a></td>
       <td><?php echo $value['real_name'];?></td>
       <td><?php echo $value['phone'];?></td>
       <td><?php echo $value['created'];?></td>
       <td><?php if(empty($value['up_uname'])):?>网站<?php else :?><a class="cBlue" href="/backend/User/user_manage/?uid=<?php echo $value['puid'];?>" target="_blank"><?php echo $value['up_uname'];?></a><?php endif;?></td>
       <td><?php echo m_format($value['total_sale']);?></td>
       <td><?php echo m_format($value['total_income']);?></td>
       <td><?php if(empty($value['puid'])):?><a class="cBlue setRebate" data-id="<?php  echo $value['id'];?>" href="javascript:;">调整比例</a> <?php endif;?><!-- <?php if($value['stop_flag']):?><a class="cBlue open" data-id="<?php  echo $value['uid'];?>" data-val="<?php echo $value['uname'];?>" href="javascript:;">开启返点</a><?php else :?><a class="cBlue cancel" data-id="<?php  echo $value['uid'];?>" data-val="<?php echo $value['uname'];?>" href="javascript:;">停止返点</a><?php endif;?> --></td>
       <td><a class="cBlue" href="/backend/Management/rebateDetail?id=<?php  echo $value['id'];?>" target="_blank">查看</a></td>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>       
</div>
<div class="stat mt10">
  <span class="ml20">本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
  <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
  <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
</div>
<div class="page mt10 order_info">
   <?php echo $pages[0] ?>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<form id='setRebatesForm' method='post' action=''>
<div class="pop-dialog set-rebates" id="set-rebates">
	<div class="pop-in">
		<div class="pop-head">
			<h2>设置返点比例</h2>
			<span class="pop-close" title="关闭">×</span>
		</div>
		<div class="pop-body" id="pop-body">
		</div>
		<div class="pop-foot">
			<div class="pop-foot tac">
				<a href="javascript:;" class="btn-blue-h32 mlr15" id="submitForm">确定</a>
				<a href="javascript:;" class="btn-b-white pop-cancel">关闭</a>
			</div>
		</div>
	</div>
</div>
<input type="hidden" value="" name="rebateId"  id="rebateId"/>
</form>
<form id='cancelForm' method='post' action=''>
<div class="pop-dialog" id="cancelPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2 id="pop_name">确认操作</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body" id="pop-body1">
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-blue-h32 mlr15" id="cancelSubmit">确认</a>
			<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
		</div>
	</div>
</div>
<input type="hidden" value="" name="cancelId"  id="cancelId"/>
<input type="hidden" value="" name="setStatus"  id="setStatus"/>
</form>
<form id='addForm' method='post' action='' autocomplete="off">
<div class="pop-dialog" id="addPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2>添加代理</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="pop-txt">
				<div class="data-table-filter del-percent">
					<table>
						<tbody>
							<tr>
								<td class="tac">
									用户名：
								</td>
								<td class="tac">
									<input type="text" name="uname" class="ipt w222">
								</td>
							</tr>
							<tr>
								<td class="tac">
									手机号：
								</td>
								<td class="tac">
									<input type="text" name="phone" class="ipt w222">
								</td>
							</tr>
							<tr>
								<td></td>
								<td id="addMsg" style="display:none;">用户名或手机号错误</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-blue-h32 mlr15" id="addSubmit">确定</a>
			<a href="javascript:;" class="btn-b-white pop-cancel">关闭</a>
		</div>
	</div>
</div>
</form>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
        $("#search").click(function(){
    		var start = $("input[name='start_time']").val();
    		var end = $("input[name='end_time']").val();
    		if(start > end){
    			alertPop('您选择的时间段错误，请核对后操作');
    			return false;
    		}
    		$('#search_form').submit();
    	});
    	
       	$(".Wdate1").focus(function(){
            dataPicker();
        });
       	$(".setRebate").click(function(){
       		var id = $(this).attr("data-id");
       		$.ajax({
                type: "post",
                url: '/backend/Management/getRebatePopHtml',
                data: {'id': id},
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    if(json.status =='y'){
                    	$("#pop-body").html(json.message);
                    	$("#rebateId").val(id);
                    	popdialog("set-rebates");
                    }else{
                        alert(json.message);
                    }
                }
            });
        });

       	$("#submitForm").click(function(){
            $.ajax({
                type: "post",
                url: '/backend/Management/setRebate',
                data: $("#setRebatesForm").serialize(),
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    if(json.status =='y')
                    {
                        alert('操作成功');
                        location.reload();
                    }else{
                    	$("#pop-body").html(json.message);
                    }
                }
            });
            return false;
        });
       	$(".cancel").click(function(){
       		var id = $(this).attr("data-id");
       		var uname = $(this).attr("data-val");
       		var body = '<div class="data-table-filter del-percent">是否确认停止用户：<span style="color:red;">' + uname + '</span>获取返点收益？</div>';
    		$("#pop-body1").html(body);
    		$("#cancelId").val(id);
    		$("#setStatus").val(1);
    		popdialog("cancelPop");
    	});
       	$(".open").click(function(){
       		var id = $(this).attr("data-id");
       		var uname = $(this).attr("data-val");
       		var body = '<div class="data-table-filter del-percent">是否确认开启用户：<span style="color:red;">' + uname + '</span>获取返点收益？</div>';
    		$("#pop-body1").html(body);
    		$("#cancelId").val(id);
    		$("#setStatus").val(0);
    		popdialog("cancelPop");
    	});
       	$("#cancelSubmit").click(function(){
    		$.ajax({
                type: "post",
                url: '/backend/Management/rebateCancel',
                data: $("#cancelForm").serialize(),
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    alert(json.message)
                    if(json.status =='y')
                    {
                        location.reload();
                    }
                }
            });
            return false;
    	});
       	$("#addRebate").click(function(){
    		popdialog("addPop");
    	});
       	$("#addSubmit").click(function(){
    		$.ajax({
                type: "post",
                url: '/backend/Management/addRebate',
                data: $("#addForm").serialize(),
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    if(json.status =='y')
                    {
                    	alert(json.message);
                        location.reload();
                    }else{
                    	$("#addMsg").html(json.message);
                        $("#addMsg").show();
                    }
                }
            });
            return false;
    	});
    }); 
</script>
</body>
</html>