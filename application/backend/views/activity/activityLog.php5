<?php
    $this->load->view("templates/head");
    $isRegister = array(
    	'1' => '是',
    	'2' => '否'
    );
    $platforms = array(
    	'0' => '网页',
    	'1' => 'Android',
      '2' => 'IOS',
      '3' => 'M版'
    );
?>
<div class="path">您的位置：运营活动&nbsp;>&nbsp;<a href="/backend/Activity/activityLog">活动数据</a></div>
<div class="data-table-filter mt10" style="width:960px">
  <form action="/backend/Activity/activityLog" method="get"  id="search_form">
  <table>
    <colgroup>
      <col width="62" />
      <col width="232" />
      <col width="62" />
      <col width="400" />
      <col width="62" />
      <col width="100" />
    </colgroup>
    <tbody>
    <tr>
      <th>活动名称：</th>
      <td>
      	<select class="selectList w184" name="aid">
      		<option value="">不限</option>
      		<?php foreach ($aid as $val):?>
      		<option value="<?php echo $val['id'];?>" <?php if($search['aid'] == $val['id']): echo "selected"; endif;?>><?php echo $val['a_name'];?></option>
      		<?php endforeach;?>
      	</select>
      </td>
      <th>参与渠道：</th>
      <td>
        <select class="selectList w130" name="channelId">
      		<option value="">不限</option>
      		<?php foreach ($channels as $val):?>
      		<option value="<?php echo $val['id'];?>" <?php if($search['channelId'] == $val['id']): echo "selected"; endif;?>><?php echo $val['name'];?></option>
      		<?php endforeach;?>
      	</select>
      </td>
      <th class="tar">日期：</th>
      <td>
      	<span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
    </tr>
    <tr>
      <th>手机号：</th>
      <td>
      <input type="text" class="ipt w184"  name="phone" value="<?php echo $search['phone']; ?>" />
      </td>
      <th>是否注册：</th>
      <td>
      	<select class="selectList w130" name="isRegister">
      		<option value="">不限</option>
      		<?php foreach ($isRegister as $key => $val):?>
      		<option value="<?php echo $key;?>" <?php if($search['isRegister'] == $key): echo "selected"; endif;?>><?php echo $val;?></option>
      		<?php endforeach;?>
      	</select>
      </td>
      <th class="tar">注册渠道：</th>
      <td>
        <select class="selectList w130" name="registerChannel">
      		<option value="">不限</option>
      		<?php foreach ($channels as $val):?>
      		<option value="<?php echo $val['id'];?>" <?php if($search['registerChannel'] == $val['id']): echo "selected"; endif;?>><?php echo $val['name'];?></option>
      		<?php endforeach;?>
      	</select>
      </td>
    </tr>
    <tr>
      <th>注册平台：</th>
      <td>
      	<select class="selectList w130" name="registerPlatform">
      		<option value="">不限</option>
      		<?php foreach ($platforms as $key => $val):?>
            <option value="<?php echo $key;?>" <?php if ($search['registerPlatform'] === "{$key}"): echo "selected"; endif; ?>><?php echo $val;?></option>
            <?php endforeach;?>
      	</select>
      </td>
      <th>注册版本：</th>
      <td>
      	<select class="selectList w130" name="registerVersion">
      		<option value="">不限</option>
      		<?php foreach ($version as $val):?>
            <option value="<?php echo $val['version'];?>" <?php if($search['registerVersion'] === "{$val['version']}"): echo "selected"; endif;?>><?php echo $val['version'];?></option>
            <?php endforeach;?>
      	</select>
      </td>
      <th>注册方式：</th>
      <td>
          <select class="selectList w130" name="reg_type">
              <option value="0" <?php if(empty($search['reg_type']) || $search['reg_type'] == '0'){ echo "selected"; }?>>不限</option>
              <option value="1" <?php if($search['reg_type'] == '1'){ echo "selected"; }?>>账号密码</option>
              <option value="3" <?php if($search['reg_type'] == '3'){ echo "selected"; }?>>微信</option>
              <option value="4" <?php if($search['reg_type'] == '4'){ echo "selected"; }?>>短信验证码</option>
          </select>
      </td>
      <td >
          <a id="search" href="javascript:void(0);" class="btn-blue ml20" onclick="">查询</a>
          <a href="javascript:void(0);" class="btn-blue mr10" id="export">导出</a>
      </td>
    </tr>
    </tbody>
  </table>
  </form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="135" />
      <col width="135" />
      <col width="150" />
      <col width="100" />
      <col width="130" />
      <col width="130" />
      <col width="130" />
      <col width="100" />
      <col width="50" />
      <col width="50" />
    </colgroup>
    <thead>
        <tr>
            <td colspan="9">
                <div class="tal">
                    <strong>&nbsp;总人数：</strong>
                    <span><?php echo $total;?> 人</span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr>
      <th>手机号</th>
      <th>参与活动时间</th>
      <th>活动名称</th>
      <th>参与渠道</th>
      <th>是否注册</th>
      <th>注册时间</th>
      <th>注册渠道</th>
      <th>注册平台</th>
      <th>注册版本</th>
      <th>注册方式</th>
    </tr>
    <?php foreach ($result as $value): ?>
    <tr>
        <td><?php echo $value['phone']; ?></td>
        <td><?php echo $value['created']; ?></td>
        <td><?php echo $aid[$value['aid']]['a_name']; ?></td>
        <td><?php echo $channels[$value['channel_id']]['name'];?></td>
        <td><?php if($value['uid']){ echo '是'; }else{ echo '否';} ?></td>
        <td><?php echo $value['rTime'];?></td>
        <td><?php echo $channels[$value['rChannel']]['name'];?></td>
        <td><?php if($value['uid']) { echo $platforms[$value['rPlatform']];}?></td>
        <td><?php if($platforms[$value['rPlatform']] == 'Android' || $platforms[$value['rPlatform']] == 'IOS') { echo $value['rVersion'];}?></td>
        <td><?php echo ($value['reg_type'] <= 2) ? '账号密码' : ($value['reg_type'] == 3 ? '微信' : '短信验证码'); ?></td>
    </tr>
<?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="9">
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
       	$("#export").click(function(){
            location.href="/backend/Activity/export?<?php echo http_build_query($search);?>";
        }); 
    }); 
</script>
</body>
</html>