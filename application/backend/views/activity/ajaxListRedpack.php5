<?php
    $status = array(
    	'1' => '未使用',
    	'2' => '已使用',
    );
?>
<div class="data-table-filter mt10">
  <form action="/backend/Activity/ajaxListRedpack" method="get"  id="redpack_search_form">
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
      <th class="tar">领取时间：</th>
      <td>
      	<span class="ipt ipt-date w184"><input type="text" name='g_start_time' value="<?php echo $search['g_start_time'] ; ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='g_end_time' value="<?php echo $search['g_end_time'] ; ?>" class="Wdate1" /><i></i></span>
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
      <th>活动标记：</th>
      <td>
      	<select class="selectList w184" name="aid">
      		<option value="">不限</option>
      		<?php foreach ($aid as $val):?>
      		<option value="<?php echo $val['id'];?>" <?php if($search['aid'] == $val['id']): echo "selected"; endif;?>><?php echo $val['a_name'];?></option>
      		<?php endforeach;?>
      	</select>
      </td>
    </tr>
    <tr>
      <th>使用时间：</th>
      <td>
      	<span class="ipt ipt-date w184"><input type="text" name='u_start_time' value="<?php echo $search['u_start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='u_end_time' value="<?php echo $search['u_end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <th class="tar">使用状态：</th>
      <td>
      	<select class="selectList w130" name="status">
      		<option value="">不限</option>
      		<?php foreach ($status as $key => $val):?>
      		<option value="<?php echo $key;?>" <?php if($search['status'] == $key): echo "selected"; endif;?>><?php echo $val;?></option>
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
    </tr>
    <tr>
      <th class="tar">生效日：</th>
      <td>
      	<span class="ipt ipt-date w184"><input type="text" name='v_start_time' value="<?php echo $search['v_start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='v_end_time' value="<?php echo $search['v_end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <th class="tar">到期日：</th>
      <td colspan="3">
      	<span class="ipt ipt-date w184"><input type="text" name='s_start_time' value="<?php echo $search['s_start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='s_end_time' value="<?php echo $search['s_end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <td >
      	  <input type="hidden" name="fromType" value="<?php echo $fromType ?>" id="fromType"/>
      	  <?php if ($fromType == 'ajax'): ?>
      	  	<input type="hidden" name="uid" value="<?php echo $search['uid'] ?>"/>
          <?php endif; ?>
          <a id="searchRedPack" href="javascript:void(0);" class="btn-blue ml20" >查询</a>
      </td>
    </tr>
    </tbody>
  </table>
  <input type="hidden" name="uid" value="<?php echo $search['uid']?>"/>
  </form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="50" />
      <col width="80" />
      <col width="120" />
      <col width="120" />
      <col width="80" />
      <col width="120" />
      <col width="120" />
      <col width="80" />
      <col width="120" />
      <col width="80" />
      <col width="120" />
      <col width="100" />
      <col width="100" />
      <col width="80" />
    </colgroup>
    <tbody>
    <tr>
      <th>编号</th>
      <th>金额</th>
      <th>领取时间</th>
      <th>活动标记</th>
      <th>类型</th>
      <th>生效日</th>
      <th>到期日</th>
      <th>使用状态</th>
      <th>红包使用时间</th>
      <th>使用条件</th>
      <th>提现限制</th>
      <th>领取渠道</th>
      <th>备注</th>
      <th>操作</th>
    </tr>
    <?php foreach ($result as $key => $value): ?>
    <tr>
    	<td><?php echo ($page - 1) * $pageNum + $key + 1;?></td>
    	<td><?php echo m_format($value['money']); ?> 元</td>
    	<td><?php echo $value['get_time']; ?></td>
    	<td><?php echo $aid[$value['aid']]['a_name']; ?></td>
        <td><?php echo $p_type[$value['p_type']]['p_name'];?></td>
        <td><?php if($value['valid_start'] == '0000-00-00 00:00:00'){ echo '无限期';}else{ echo $value['valid_start'];}?></td>
        <td><?php if($value['valid_end'] == '0000-00-00 00:00:00'){ echo '无限期';}else{ echo $value['valid_end'];}?></td>
        <td><?php if($value['status'] == '2'){ echo '已使用';}else{ echo '未使用';}?></td>
        <td><?php if($value['status'] == '2'){ echo $value['use_time'];}?></td>
        <td><?php echo $value['use_desc'];?></td>
        <td><?php echo $value['refund_desc'];?></td>
        <td><?php echo $channels[$value['channel_id']]['name'];?></td>
        <td><?php echo $value['remark'];?></td>
        <td><?php if($value['status'] != '2'):?>
        <a href="javascript:void(0);" class="cBlue delete" id="<?php echo $value['id'];?>">删除</a>
        <?php if ($value['valid_start'] < date('Y-m-d H:i:s') && $value['valid_end'] > date('Y-m-d H:i:s') && $value['status'] == 1 && $value['p_type']!=3) {?>
        <a href="javascript:void(0);" class="cBlue use" id="<?php echo $value['id'];?>">使用</a>
        <?php }?>
        <?php endif;?></td>
    </tr>
<?php endforeach; ?>
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

<div class="page mt10 order_info">
   <?php echo $pages[0] ?>
</div>
<div class="pop-dialog" id="dialog-delete" style="display:none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2>确认删除红包？</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<table width="100%">
				<tbody id="pop-tbody">
				</tbody>
			</table>
		</div>
		<div class="pop-foot tac">
			<input type="hidden" name="selectId" value="" />
			<a href="javascript:redpackDelete();" class="btn-blue-h32 mlr15">确认</a>
			<a href="javascript:closePop();" class="btn-b-white mlr15">取消</a><br/><br/>
		</div>
	</div>
</div>
<div class="pop-dialog" id="dialog-use" style="display:none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2>确认使用红包？</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<table width="100%">
				<tbody id="pop-use-tbody">
				</tbody>
			</table>
		</div>
		<div class="pop-foot tac">
			<input type="hidden" name="useId" value="" />
			<a href="javascript:redpackUse();" class="btn-blue-h32 mlr15">确认</a>
			<a href="javascript:closePop();" class="btn-b-white mlr15">取消</a><br/><br/>
		</div>
	</div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
    	$("#searchRedPack").click(function(){
          if ($("#fromType").val() == "ajax") {
              $("#redpack_info").load("/backend/Activity/ajaxListRedpack?" + $("#redpack_search_form").serialize() + "&fromType=ajax");
              return false;
          }
          $('#redpack_search_form').submit();
    	});
    	
    	$('#redpack_search_form').submit(function(){
        	$("#redpack_info").load("/backend/Activity/ajaxListRedpack?"+$("#redpack_search_form").serialize()+"&fromType=ajax");
            return false;
    	});
    	
       	$(".Wdate1").focus(function(){
            dataPicker();
        });

       	$('.order_info a').click(function(){
        	var _this = $(this);
            $("#redpack_info").load(_this.attr("href"));
            return false;
        });

       	$(".delete").click(function(){
           	var td = $(this).closest("tr").children("td").siblings("td");
    		var html  = '<tr><td style="text-align:right;">金额：</td><td>'+td.eq(1).html()+'</td></tr>';
				html += '<tr><td style="text-align:right;">类型：</td><td>'+td.eq(4).html()+'</td></tr>';
				html += '<tr><td style="text-align:right;">有效期：</td><td>'+td.eq(5).html()+' - '+td.eq(6).html()+'</td></tr>';
    		$("#pop-tbody").html(html);
    		$("input[name='selectId']").val($(this).attr("id"));
        	popdialog("dialog-delete");
    		return false;
    	});
    	$(".use").click(function(){
    		var td = $(this).closest("tr").children("td").siblings("td");
    		var html  = '<tr><td style="text-align:right;">金额：</td><td>'+td.eq(1).html()+'</td></tr>';
				html += '<tr><td style="text-align:right;">类型：</td><td>'+td.eq(4).html()+'</td></tr>';
				html += '<tr><td style="text-align:right;">有效期：</td><td>'+td.eq(5).html()+' - '+td.eq(6).html()+'</td></tr>';
    		$("#pop-use-tbody").html(html);
    		$("input[name='useId']").val($(this).attr("id"));
        	popdialog("dialog-use");
    		return false;
        })
    });
    
    function redpackDelete(){
    	var id = $("input[name='selectId']").val();
		$.ajax({
		    type: "post",
		    url: "/backend/Activity/redpackDelete",
		    data: {'ids':id},
		    success: function(data){
		    	var json = jQuery.parseJSON(data);
		    	closePop();
                alert(json.message);
                if(json.status =='y')
                {
                	$('#redpack_search_form').submit();
                	//$("#"+id).remove();
                }
		    }
		})
	}

    function redpackUse(){
    	var id = $("input[name='useId']").val();
    	$("input[name='useId']").val('');
    	if (id) {
    		$.ajax({
    		    type: "post",
    		    url: "/backend/Activity/redpackUse",
    		    data: {'id':id},
    		    success: function(data){
    		    	var json = jQuery.parseJSON(data);
    		    	closePop();
    		    	alert(json.message);
                    if(json.status =='y')
                    {
                    	location.href += "&tab=li_5"
                    	//$("#"+id).remove();
                    }
    		    }
    		})
    	}
	}
    
</script>