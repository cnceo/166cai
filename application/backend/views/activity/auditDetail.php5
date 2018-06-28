<?php
    $this->load->view("templates/head");
    $check_status = array(
      '0' => '未审核',
      '1' => '审核成功',
      '2' => '审核失败'
    );
    $status = array(
      '0' => '未派发',
      '1' => '已派发',
    );
?>
<div class="path">您的位置：审核管理&nbsp;>&nbsp;<a href="/backend/Activity/auditList">红包派发审核</a>&nbsp;>&nbsp;详情</div>
<div class="data-table-list mt20">
  <table>
    <thead>
      <tr><td colspan="11" style="text-align: left;text-indent: 10px;">用户统计：<?php echo $count['user_count'] ;?> 人,&nbsp;购彩红包总额：<?php echo $count['buy_money'] ;?> 元,&nbsp;充值红包总额：<?php echo $count['recharge_money'] ;?> 元
</td></tr>
      <tr>
        <th>用户名</th>
        <th>真实姓名</th>
        <th>红包类型</th>
        <th>红包金额（元）</th>
        <th>使用条件</th>
        <th>客户端专享</th>
        <th>有效期</th>
        <th>生效日</th>
        <th>个数</th>
        <th>申请时间</th>
        <th>审核状态</th>
        <th>状态</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($result as $k => $v): ?>
      <tr>
        <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $v['uid'] ?>" class="cBlue"><?php echo $v['uname'] ;?></a></td>
        <td><?php echo $v['real_name'] ;?></td>
        <td><?php echo $v['p_name'] ;?></td>
        <td><?php echo $v['money']/100; ?></td>
        <td><?php echo $v['use_desc'] ; ?></td>
        <td><?php echo $v['ismobile_used'] ? '是' : '否'; ?></td>
        <td><?php 
        $day = json_decode($v['use_params'],true);
        $day = $day['end_day'] - $day['start_day'];
        echo $day.'天' ; ?></td>
          <td><?php echo $v['validity'] ; ?></td>
        <td><?php echo $v['num'] ; ?></td>
        <td><?php echo $v['created'] ; ?></td>
        <td><?php echo $check_status[$v['check_status']]; ?></td>
        <td><?php echo $status[$v['status']]; ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    	<tr><td colspan="11" style="text-align: left">待发短信内容：<?php echo $count['message'] ;?></td></tr>
    </tfoot>
  </table>
</div>

<div class="pop-dialog" id="dialog-delete" style="display:none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2>确认删除红包？</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body" id="pop-body">
		</div>
		<div class="pop-foot tac">
			<input type="hidden" name="selectId" value="" />
			<a href="javascript:redpackDelete();" class="btn-blue-h32 mlr15">确认</a>
			<a href="javascript:closePop();" class="btn-b-white mlr15">取消</a><br/><br/>
		</div>
	</div>
</div>
</body>
</html>