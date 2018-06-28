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
          <?php if($search['type'] == 'syxw'): ?>
          <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1"><i></i></span>
          <span class="ml8 mr8">至</span>
          <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1"><i></i></span>
          <?php endif;?>
          <input type='hidden' class='vcontent' name='type' value='<?php echo $search['type'];?>'/>
          <a onclick="$('#search_form').submit();" href="javascript:void(0);" class="btn-blue">查询</a>
          <a href="javascript:void(0);" class="btn-blue" id="startIssue">开启期次</a>
          <input type="hidden" name="selectId" id="selectId" value="" />
          <a href="javascript:void(0);" class="btn-blue" id="modifyIssue">修改开奖信息</a>
        </td>
      </tr>
      </tbody>
    </table>
  </form>
  </div>
  <div class="data-table-list mt10">
    <table>
      <colgroup>
        <col width="10%">
        <col width="10%">
        <col width="15%">
        <col width="15%">
        <col width="30%">
        <col width="20%">
      </colgroup>
      <thead>
        <tr>
          <th>期号</th>
          <th>状态</th>
          <th>开始时间</th>
          <th>截止时间</th>
          <th>开奖号码</th>
          <th>开奖详情</th>
        </tr>
      </thead>
      <?php if($result): ?>
      <tbody>
        <?php foreach ($result as $row):?>
        <tr data-issue="<?php echo $row['issue'];?>">
         <td><?php echo $row['issue'];?></td>
         <td class = "status">
          <?php if(!$row['is_over']):?>
            结期
          <?php elseif(!$row['is_start']):?>
            开启
          <?php else:?>
            未开启
          <?php endif;?>
         </td>
         <td><?php echo $row['sale_time'];?></td>
         <td><?php echo $row['end_time'];?></td>
         <td><?php echo $row['awardNum'];?></td>
         <td><a href="/backend/Issue/detail?lid=<?php echo $search['type'];?>&issue=<?php echo $row['issue'];?>">查看详情</a></td>
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
			<a href="javascript:sfcStart();" class="btn-blue-h32 mlr15">确认</a>
			<a href="javascript:closePop();" class="btn-b-white mlr15">取消</a>
      <input type="hidden" name="issue" id="issue" value="" />
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
	issue = ''
    var issue = $("#selectId").val();
    if(!issue){
      alert('请先选择一次比赛');
      return false;
    }
    else
    {
      $('input[name="issue"]').val(issue);
    	popdialog("dialog-startissue");
    }
});

$("#modifyIssue").click(function(){
    var id = $("#selectId").val();
    if(!id){
      alert('请先选择你要修改的比赛');
      return false;
    }
    var status = $(".select").find('.status').html();
    if(status == "开启")
    {
        alert('不能对开启状态的期次进行开奖信息修改');
        return false;
    }
    var matchStatus;
    status == "截止" ? matchStatus = 0 : matchStatus = 1;
    var type = $("input[name='type']").val();
    window.location.href = '/backend/Issue/modifyIssueDetail?lid=' + type + '&issue=' + id + '&matchStatus=' + matchStatus;
});

function sfcStart(){
  var issue = $('input[name="issue"]').val();
  $.ajax({
      type: "post",
      url: "/backend/Issue/sfcStart",
      data: {'issues':issue,env:'<?php echo ENVIRONMENT?>'},
      dataType: "text",
      success: function(data){
          if(data) 
        {
          location.href = location.href;
      }
      }
  })
}
</script>  
</body>
</html>
