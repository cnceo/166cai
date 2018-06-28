<?php
    $this->load->view("templates/head");
    $status = array(
    	'1' => '未使用',
    	'2' => '已使用',
    );
?>
<div class="path">您的位置：运营活动&nbsp;>&nbsp;<a href="/backend/Activity/worldcupredpack">世界杯红包</a></div>
<div class="data-table-filter mt10" style="width:960px">
  <form action="/backend/Activity/worldcupredpack" method="get"  id="search_form">
  <table>
    <colgroup>
      <col width="62" />
      <col width="200" />
      <col width="62" />
      <col width="10" />
      <col width="170" />
      <col width="10" />
      <col width="10" />
      <col width="62" />
      <col width="10" />
      <col width="62" />
      <col width="10" />
      <col width="62" />
    </colgroup>
    <tbody>
    <tr>
      <th>用户名：</th>
      <td colspan="2"><input type="text" class="ipt w184"  name="uname" value="<?php echo $search['uname']; ?>" /></td>
      <th>手机号：</th>
      <td colspan="2"><input type="text" class="ipt w130"  name="phone" value="<?php echo $search['phone']; ?>" /></td>
      <th>用户区间：</th>
      <td>
        <select class="selectList w60" name="section">
      		<option value="">不限</option>
      		<?php for ($sec = 1; $sec <= 8; $sec++) {?>
      		<option value="<?php echo $sec;?>" <?php if($search['section'] == $sec): echo "selected"; endif;?>><?php echo $sec;?></option>
      		<?php }?>
      	</select>
      </td>
      <th>红包金额：</th>
      <td><input type="text" class="ipt w60"  name="money" value="<?php echo $search['money']; ?>" /></td>
      <th>红包类型：</th>
      <td>
        <select class="selectList w195" name="p_type">
      		<option value="">不限</option>
      		<?php foreach ($ptype as $k => $v) {?>
      		<option value="<?php echo $k?>" <?php if($search['p_type'] == $k): echo "selected"; endif;?>><?php echo $v?></option>
      		<?php }?>
      	</select>
      </td>
    </tr>
    <tr>
      <th>使用状态：</th>
      <td>
      	<select class="selectList w94" name="status">
      		<option value="">不限</option>
      		<?php foreach ($status as $key => $val):?>
      		<option value="<?php echo $key;?>" <?php if ($search['status'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val;?></option>
      		<?php endforeach;?>
      	</select>
      </td>
      <th>领取平台：</th>
      <td>
      	<select class="selectList w94" name="platform">
      		<option value="">不限</option>
      		<?php foreach ($platformArr as $pid => $platform):?>
      		<option value="<?php echo $pid;?>" <?php if($search['platform'] == $pid): echo "selected"; endif;?>><?php echo $platform;?></option>
      		<?php endforeach;?>
      	</select>
      </td>
      <th class="tar">领取时间：</th>
      <td colspan="6">
      	<span class="ipt ipt-date w184"><input type="text" name='get_time0' value="<?php echo $search['get_time0'] ?>" class="Wdate0" /><i></i></span>
      	<span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='get_time1' value="<?php echo $search['get_time1'] ?>" class="Wdate1" /><i></i></span>
      </td>
    </tr>
    <tr>
      <th>使用时间：</th>
      <td colspan="4">
      	<span class="ipt ipt-date w184"><input type="text" name='use_time0' value="<?php echo $search['use_time0'] ?>" class="Wdate0" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='use_time1' value="<?php echo $search['use_time1'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <th>过期时间：</th>
      <td colspan="4">
      	<span class="ipt ipt-date w184"><input type="text" name='valid_end0' value="<?php echo $search['valid_end0'] ?>" class="Wdate0" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='valid_end1' value="<?php echo $search['valid_end1'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <td colspan="2">
          <a id="search" href="javascript:void(0);" class="btn-blue ml20" onclick="">查询</a>
      </td>
    </tr>
    </tbody>
  </table>
  </form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="150" />
      <col width="80" />
      <col width="110" />
      <col width="50" />
      <col width="55" />
      <col width="55" />
      <col width="50" />
      <col width="50" />
      <col width="56" />
      <col width="110" />
      <col width="110" />
      <col width="90" />
    </colgroup>
    <thead>
        <tr>
            <td colspan="12">
                <div class="tal">
                    <strong>&nbsp;总人数：</strong>
                    <span><?php echo $result['total']['unum'];?> 人</span>
                    <strong>&nbsp;红包总金额：</strong>
                    <span><?php echo m_format($result['total']['umoney']);?> 元</span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr>
      <th>用户名</th>
      <th>手机号</th>
      <th>领取时间</th>
      <th>用户区间</th>
      <th>红包金额</th>
      <th>红包类型</th>
      <th>使用状态</th>
      <th>领取平台</th>
      <th>客户端专享</th>
      <th>使用时间</th>
      <th>过期时间</th>
      <th>使用条件</th>
    </tr>
    <?php foreach ($result['data'] as $value): ?>
    <tr>
        <td><a href="/backend/User/user_manage/?uid=<?php echo $value['uid'] ?>" class="cBlue" target="_blank"><?php echo $result['users'][$value['uid']]['uname']; ?></a></td>
        <td><?php echo $result['users'][$value['uid']]['phone']; ?></td>
        <td><?php echo $value['get_time']; ?></td>
        <td><?php echo $value['section']; ?></td>
        <td><?php echo m_format($value['money']); ?> 元</td>
        <td><?php echo $ptype[$value['p_type']];?></td>
        <td><?php if($value['status'] == '2'){ echo '已使用';}else{ echo '未使用';}?></td>
        <td><?php echo $platformArr[$value['platform_id']+1];?></td>
        <td><?php echo empty($value['ismobile_used']) ? '否' : '是'; ?></td>
        <td><?php if($value['status'] == '2'){ echo $value['use_time'];}?></td>
        <td><?php if($value['valid_end'] == '0000-00-00 00:00:00'){ echo '无限期';}else{ echo $value['valid_end'];}?></td>
        <td><?php echo $value['use_desc'];?></td>
    </tr>
<?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="12">
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

<div class="page mt10 order_info">
   <?php echo $pages[0] ?>
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
<script  src="/source/date/WdatePicker.js"></script>
<script>
$("#search").click(function(){
	$('#search_form').submit();
});
$(".Wdate0").focus(function(){
    dataPicker();
});
$(".Wdate1").focus(function(){
    dataPicker({dateFmt:'yyyy-MM-dd HH:mm:ss',startDate:'%y-%M-%d 23:59:59'});
});
</script>
</body>
</html>