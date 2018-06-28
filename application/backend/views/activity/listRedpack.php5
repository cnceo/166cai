<?php
    $this->load->view("templates/head");
    $status = array(
    	'1' => '未使用',
    	'2' => '已使用',
    );
?>
<style type="text/css">
  .tab_box{width: 1000px;height: 40px;border-bottom:1px solid #ccc;margin-top:30px;}
  .tab_box a{display: block;width:100px;height: 40px;line-height: 40px;text-decoration: none;float: left;text-align: center;border:1px solid #ccc;background: #F9F9F9;margin-right: 5px;border-radius: 3px 3px 0px 0px;border-bottom: none;}
  .tab_box a.active{background: #5CA3E6;color: #fff;font-weight: bold;}
  .tab_box a:hover{background: #5CA3E6;color: #fff;font-weight: bold;}
</style>
<div class="path">您的位置：运营活动&nbsp;>&nbsp;<a href="/backend/Activity/listRedpack">红包管理</a></div>
<div class='tab_box'>
  <a href="<?php echo '/backend/Activity/listRedpack'; ?>"  class='active'>概览</a>
  <a href="<?php echo '/backend/Activity/createRedPack'; ?>">新增红包</a>
  <a href="<?php echo '/backend/Activity/pullRedPack'; ?>">红包派发</a>
</div>
<div class="data-table-filter mt10" style="width:960px">
  <form action="/backend/Activity/listRedpack" method="get"  id="search_form">
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
      <th>用户信息：</th>
      <td>
      <input type="text" class="ipt w184"  name="uinfo" value="<?php echo $search['uinfo']; ?>" placeholder="手机号/用户名"/>
      </td>
      <th class="tar">类型：</th>
      <td>
        <select class="selectList w130" name="p_type">
      		<option value="">不限</option>
      		<?php foreach ($p_type as $val):?>
      		<option value="<?php echo $val['p_type'];?>" <?php if($search['p_type'] == $val['p_type']): echo "selected"; endif;?>><?php echo $val['p_name'];?></option>
      		<?php endforeach;?>
      	</select>
      </td>
      <th class="tar">领取时间：</th>
      <td>
      	<span class="ipt ipt-date w184"><input type="text" name='g_start_time' value="<?php echo isset($_GET['g_start_time']) ? $search['g_start_time'] : date('Y-m-d H:i:s',strtotime(date('Y-m-d'))); ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='g_end_time' value="<?php echo isset($_GET['g_end_time']) ? $search['g_end_time'] : date('Y-m-d H:i:s',strtotime(date('Y-m-d'))+60*60*24-1); ?>" class="Wdate1" /><i></i></span>
      </td>
    </tr>
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
      <th class="tar">领取渠道：</th>
      <td>
      	<select class="selectList w130" name="channel_id">
      		<option value="">不限</option>
      		<?php foreach ($channels as $val):?>
      		<option value="<?php echo $val['id'];?>" <?php if($search['channel_id'] == $val['id']): echo "selected"; endif;?>><?php echo $val['name'];?></option>
      		<?php endforeach;?>
      	</select>
      </td>
      <th class="tar">生效日：</th>
      <td>
      	<span class="ipt ipt-date w184"><input type="text" name='v_start_time' value="<?php echo $search['v_start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='v_end_time' value="<?php echo $search['v_end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
    </tr>
    <tr>
      <th>红包金额：</th>
      <td>
      	<input type="text" class="ipt w184"  name="money" value="<?php echo $search['money']; ?>" />
      </td>
      <th class="tar">使用状态：</th>
      <td>
      	<select class="selectList w130" name="status">
      		<option value="">不限</option>
      		<?php foreach ($status as $key => $val):?>
      		<option value="<?php echo $key;?>" <?php if ($search['status'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val;?></option>
      		<?php endforeach;?>
      	</select>
      </td>
      <th class="tar">到期日：</th>
      <td>
      	<span class="ipt ipt-date w184"><input type="text" name='s_start_time' value="<?php echo $search['s_start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='s_end_time' value="<?php echo $search['s_end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
    </tr>
    <tr>
      <th>使用时间：</th>
      <td colspan="2">
      	<span class="ipt ipt-date w184"><input type="text" name='u_start_time' value="<?php echo $search['u_start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='u_end_time' value="<?php echo $search['u_end_time'] ?>" class="Wdate1" /><i></i></span>
        <span ><input type="checkbox" name="ismobile_used" value="1" <?php if($search['ismobile_used']){echo "checked='checked'";}?> /> 客户端专享</span>
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
      <col width="40" />
      <col width="153" />
      <col width="115" />
      <col width="110" />
      <col width="50" />
      <col width="55" />
      <col width="63" />
      <col width="150" />
      <col width="115" />
      <col width="115" />
      <col width="50" />
      <col width="115" />
      <col width="98" />
      <col width="130" />
      <col width="150" />
    </colgroup>
    <thead>
        <tr>
            <td colspan="15">
                <div class="tal">
                    <strong>&nbsp;总人数：</strong>
                    <span><?php echo $countUser;?> 人</span>
                    <strong>&nbsp;红包总金额：</strong>
                    <span><?php echo m_format($countMoney);?> 元</span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr>
      <th><input type="checkbox" class="_ck">全选</th>
      <th>用户名</th>
      <th>领取时间</th>
      <th>活动名称</th>
      <th>类型</th>
      <th>红包金额</th>
      <th>客户端专享</th>
      <th>领取渠道</th>
      <th>生效日</th>
      <th>到期日</th>
      <th>使用状态</th>
      <th>红包使用时间</th>
      <th>使用条件</th>
      <th>提现限制</th>
      <th>备注</th>
    </tr>
    <?php foreach ($result as $value): ?>
    <tr>
    	<td><?php if($value['status'] != '2'):?><input type="checkbox" class="ck_" value="<?php echo $value['id'];?>"><?php endif;?></td>
        <td><a href="/backend/User/user_manage/?uid=<?php echo $value['uid']?>" target="_blank"><?php echo $value['uname']; ?></a></td>
        <td><?php echo $value['get_time']; ?></td>
        <td><?php echo $aid[$value['aid']]['a_name']; ?></td>
        <td><?php echo $p_type[$value['p_type']]['p_name'];?></td>
        <td><?php echo m_format($value['money']); ?> 元</td>
        <td><?php echo empty($value['ismobile_used']) ? '否' : '是'; ?></td>
        <td><?php echo $channels[$value['channel_id']]['name'];?></td>
        <td><?php if($value['valid_start'] == '0000-00-00 00:00:00'){ echo '无限期';}else{ echo $value['valid_start'];}?></td>
        <td><?php if($value['valid_end'] == '0000-00-00 00:00:00'){ echo '无限期';}else{ echo $value['valid_end'];}?></td>
        <td><?php if($value['status'] == '2'){ echo '已使用';}else{ echo '未使用';}?></td>
        <td><?php if($value['status'] == '2'){ echo $value['use_time'];}?></td>
        <td><?php echo $value['use_desc'];?></td>
        <td><?php echo $value['refund_desc'];?></td>
        <td><?php echo $value['remark'];?></td>
    </tr>
<?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
      	<td colspan="14" style="text-align:left;"><a href="javascript:void(0);" class="btn-blue mr10" id="delete">删除</a></td>
      </tr>
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
    $(function(){
        $("#search").click(function(){
    		$('#search_form').submit();
    	});
    	
       	$(".Wdate1").focus(function(){
            dataPicker();
            //$(this).removeAttr("readonly");
        });

       	$("._ck").click(function(){
    		var self = this;
    		$(".ck_").each(function(){
    			if(self.checked)
    			{
    				$(this).attr("checked", true);
    			}
    			else
    			{
    				$(this).attr("checked", false);
    			}
    		});
    	});

       	$("#delete").click(function(){
    		ids = [];
    		$(".ck_").each(function(){
    			if(this.checked)
    			{
    				ids.push($(this).val());
    			}
    		})
    	    if(ids.length < 1)
    	    {
    	      alert('请先选择要删除的红包');
    	      return false;
    	    }
    		var html = '删除红包数量：' + ids.length + '个';
    		$("#pop-body").html(html);
    		$("input[name='selectId']").val(ids);
        	popdialog("dialog-delete");
    		return false;
    	});

       	$("#export").click(function(){
            location.href="/backend/Activity/redpackExport?<?php echo http_build_query($search);?>";
        }); 
    });
    
    function redpackDelete(){
    	var ids = $("input[name='selectId']").val();
		$.ajax({
		    type: "post",
		    url: "/backend/Activity/redpackDelete",
		    data: {'ids':ids},
		    success: function(data){
		    	var json = jQuery.parseJSON(data);
                alert(json.message);
                $(".ck_").each(function(){
        			$(this).attr("checked", false);
        		});
                location.reload();
		    }
		})
	}
</script>
</body>
</html>