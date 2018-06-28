<?php
    $this->load->view("templates/head");
    $status = array(
      '0' => '未审核',
    	'1' => '审核成功',
    	'2' => '审核失败'
    );
?>
<style type="text/css">
  .my_span input{position: relative;left: -2px;top:3px;}
  .my_span i{padding-left: 2px;}
  a{color:#0066FF;}
</style>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script  src="/source/date/WdatePicker.js"></script>
<script type="text/javascript">
  $(function(){
      $("#search").click(function(){
        $('#search_form').submit();
      });
    //日历
    $(".Wdate1").focus(function(){
        dataPicker();
    });
    //发送请求
    $('.toCheck').click(function(){
      var id = $(this).attr('data-id');
      var status = $(this).attr('data-status');
      var that = $(this).parent().find('.toCheck');
      var statusTd = $(this).parent().parent().find('.status_td');
      layer.load(0, {shade: [0.5, '#393D49']});
      $.ajax({
        type: "post",
        url: "/backend/Activity/ajaxAudit",
        data: {id:id,status:status},
        success: function(data)
        {
          var json = jQuery.parseJSON(data);
          layer.closeAll();
          that.remove();
          if(json.status == 'SUCCESSS')
          {
            layer.alert(json.message, {icon: 1,btn:'',title:'温馨提示',time:0});
            if(status == 2)
            {
              statusTd.html('审核失败'); 
            }
            else
            {
              statusTd.html('审核成功'); 
            }
            //location.reload();
          }else{
            layer.alert(json.message, {icon: 2,btn:'',title:'温馨提示',time:0});
          }
        }
      })
    });
  <?php if(isset($_GET['suc']) && !empty($_GET['suc'])): ?>
    var url = window.location.href;
    var arr = url.split('?'); 
    layer.alert('<?php echo $_GET['suc'];?>', {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){window.location.href=arr[0]}});
  <?php endif; ?> 

  });
</script>
<div class="path">您的位置：审核管理&nbsp;>&nbsp;红包派发审核</div>
<div class="data-table-filter mt10" style="width:960px">
  <form action="/backend/Activity/auditList" method="get"  id="search_form">
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
      <th class="tar">申请时间：</th>
      <td>
      	<span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo isset($_GET['start_time']) ? $search['start_time'] : date('Y-m-d ',strtotime("-1 month")).'00:00:00'; ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo isset($_GET['end_time']) ? $search['end_time'] : date('Y-m-d H:i:s',strtotime(date('Y-m-d'))+60*60*24-1); ?>" class="Wdate1" /><i></i></span>
      </td>
    </tr>
    <tr>
      <th>状态：</th>
      <td colspan="2">
      	<span class='my_span'>
          <input type="radio" name="status" value="" <?php echo $search['status'] === '' ? 'checked' : '';?> /><i>全部</i>
        </span>
        <span class='my_span'>
          <input type="radio" name="status" value="0" <?php echo $search['status'] === '0' ? 'checked' : '';?> >待审核
        </span>
        <span class='my_span'>
          <input type="radio" name="status" value="3" <?php echo $search['status'] === '3' ? 'checked' : '';?> />已操作
        </span>
      </td>
      <td >
          <a id="search" href="javascript:void(0);" class="btn-blue ml20" onclick="">查询</a>
      </td>
    </tr>
    </tbody>
  </table>
  </form>
</div>
<div class="data-table-list mt20">
  <table>
    <thead>
      <tr>
        <th>派发期次</th>
        <th>用户数</th>
        <th> 购彩红包总额</th>
        <th>充值红包总额</th>
        <th>申请时间</th>
        <th>状态</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($result as $k => $v): ?>
      <tr>
          <td><?php echo $v['id'] ;?></td>
          <td><?php echo $v['user_count'] ;?></td>
          <td><?php echo $v['buy_money'] ;?></td>
          <td><?php echo $v['recharge_money'] ;?></td>
          <td><?php echo $v['created'] ;?></td>
          <td class='status_td'><?php echo $status[$v['status']]; ?></td>
          <td>
          <?php if (empty( $v['status'] )): ?>
            <a href="javascript:;" class='toCheck' data-status='1' data-id="<?php echo $v['id'] ;?>">审核通过</a>
            <a href="javascript:;" class='toCheck' data-status='2' data-id="<?php echo $v['id'] ;?>">审核失败</a>
          <?php endif; ?>
            <a href="/backend/Activity/auditDetail/auditId/<?php echo $v['id'] ;?>" target="_blank">查看详情</a>
          </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr >
        <td colspan="7">
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
</body>
</html>