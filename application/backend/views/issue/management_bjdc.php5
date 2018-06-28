<?php $this->load->view("templates/head") ?>
<div class="frame-container" style="margin-left:0;">
<div class="path">您的位置：数据中心&nbsp;&gt;&nbsp;<a href="/backend/Issue/management">期次管理</a></div>
<div class="mt10">
    <div class="data-table-filter" style=" width: 100%;">
    <form action="/backend/Issue/management" method="get"  id="search_form">
    <table>
      <colgroup>
        <col width="62">
        <col width="262">
        <col width="62">
        <col width="286">
        <col width="62">
        <col width="248">
      </colgroup>
      <tbody>
      <tr>
        <td colspan="9">
          彩种：
          <select class="selectList w222" id="" name="" onchange="window.location.href=this.options[selectedIndex].value">
            <?php foreach ($lrule as $l => $types): ?>
              <option <?php if($search['type'] == $l):?>selected<?php endif;?> value="/backend/Issue/management/?type=<?php echo $l; ?>"><?php echo $types['name'];?></option>
            <?php endforeach;?>      
          </select>
        </td>
      </tr>
      </tbody>
    </table>
  </form>
  </div>
  <div class="data-table-list mt10">
    <table>
      <colgroup>
      	<col width="15%">
        <col width="15%">
        <col width="30%">
        <col width="30%">
      </colgroup>
      <thead>
        <tr>
          <th>期号</th>
          <th>状态</th>
          <th>开始时间</th>
          <th>截止时间</th>
        </tr>
      </thead>
      <?php if($result): ?>
      <tbody>
        <?php foreach ($result as $row):?>
        <tr data-issue="<?php echo $row['issue'];?>">
         <td><?php echo $row['issue'];?></td>
         <td><?php echo $row['status'];?></td>
         <td><?php echo $row['sale_time'];?></td>
         <td><?php echo $row['end_time'];?></td>
        </tr>
        <?php endforeach;?>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="5">
              <div class="stat">
                  <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
                  <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
                  <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
              </div>
          </td>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>
</div>
</div>
<div class="page mt10 login_info">
   <?php echo $pages[0] ?>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<div class="pop-dialog" id="dialog-startissue" style='display:none;'>
	<div class="pop-in">
		<div class="pop-body">
			<p>请确认开启</p>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:bjdcStart();" class="btn-blue-h32 mlr15">确认</a>
			<a href="javascript:closePop();" class="btn-b-white mlr15">取消</a>
		</div>
	</div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
$(function(){
  $(".Wdate1").focus(function(){
        dataPicker();
    });
})

$("tr").click(function(){
    $("tr").removeClass("select");
    $(this).addClass("select");
    $("#selectId").val($(this).attr("data-issue"));
  });
var issue;
$("#startIssue").click(function(){
    issue = $("#selectId").val();
    if(!issue){
      alert('请先选择一次比赛');
      return false;
    }
    else
    {
    	popdialog("dialog-startissue");
    }
});

function bjdcStart()
{
	$.ajax({
	    type: "post",
	    url: "/backend/Issue/bjdcStart",
	    data: {'issues':issue, 'env':'<?php echo ENVIRONMENT?>'},
	    dataType: "text",
	    success: function(data){
	        if(data == true) 
		    {
			    location.href = location.href;
			}
	        else
	        {
	        	closePop();
	        	alert('操作失败');
			}
	    }
	})
}
</script>  
</body>
</html>
