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
          <?php if(in_array($search['type'], array('syxw', 'jxsyxw', 'hbsyxw', 'gdsyxw'))): ?>
          <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1"><i></i></span>
          <span class="ml8 mr8">至</span>
          <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1"><i></i></span>
          <?php endif;?>
          <input type='hidden' class='vcontent' name='type' value='<?php echo $search['type'];?>'/>
          <a onclick="$('#search_form').submit();" href="javascript:void(0);" class="btn-blue">查询</a>
          <!-- <a href="javascript:void(0);" class="btn-blue" id="modifyIssue">开启期次</a> -->
          <a href="javascript:void(0);" class="btn-blue" id="deleteIssue">删除期次</a>
		  <a href="javascript:void(0);" class="btn-blue" id="modifyIssue">添加开奖号码</a>
           <a class="btn-blue" id="recount">遗漏数据重算</a>
          <input type="hidden" name="selectId" id="selectId" value="" />
        </td>
      </tr>
      </tbody>
    </table>
  </form>
  </div>
  <div class="data-table-list mt10">
    <table>
      <colgroup>
      	<col width="10%" />
        <col width="10%" />
        <col width="10%" />
        <col width="20%" />
        <col width="20%" />
        <col width="30%" />
      </colgroup>
      <thead>
        <tr>
          <th><input type="checkbox" class="_ck">全选</th>
          <th>期号</th>
          <th>状态</th>
          <th>开始时间</th>
          <th>截止时间</th>
          <th>开奖号码</th>
        </tr>
      </thead>
      <?php if($result): ?>
      <tbody>
        <?php foreach ($result as $row):?>
        <tr data-issue="<?php echo $row['issue'];?>"  class = "tr-table">
         <td>
          <?php if($row['compare_status'] < 50):?>
          <input type="checkbox" class="ck_" value="<?php echo $row['issue'];?>">
          <?php endif;?>
        </td>
         <td><?php echo $row['issue'];?></td>
         <td class = "status"><?php echo $row['status'];?></td>
         <td><?php echo $row['sale_time'];?></td>
         <td><?php echo $row['end_time'];?></td>
         <td class = "awardNum"><?php echo $row['awardNum'];?></td>
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
  <form id='modifyIssueForm' method='post' action=''>
	  <div class="pop-dialog pop-kjhm" id="issuePop">
		  <div class="pop-in">
			  <div class="pop-head">
				  <h2 id="pop_name1"></h2>
				  <span class="pop-close" title="关闭">关闭</span>
			  </div>
			  <div class="pop-body">
				  <div class="data-table-filter del-percent">
					  <table>
						  <colgroup>
							  <col width="68" />
							  <col width="350" />
						  </colgroup>
						  <tfoot>
						  <tr>
							  <td colspan="2"><p>输入开奖号码格式为01、02 不支持1、2</p></td>
						  </tr>
						  <tr>
							  <td colspan="2"><p class="cRed">请谨慎修改开奖号码</p></td>
						  </tr>
						  </tfoot>
						  <tbody>
                          <tr>
                              <td>开奖号码：</td>
                              <td>
                                  <div class="num-box">
                                      <input name="awardnum[0]" type="text" value="" maxlength="2">
                                      <input name="awardnum[1]" type="text" value="" maxlength="2">
                                      <input name="awardnum[2]" type="text" value="" maxlength="2">
                                      <input name="awardnum[3]" type="text" value="" maxlength="2">
                                      <input name="awardnum[4]" type="text" value="" maxlength="2">
                                  </div>
                              </td>
                          </tr>
                          <tr>
                              <td>确认开奖号码：</td>
                              <td>
                                  <div class="num-box">
                                      <input name="awardnumAgain[0]" type="text" value="" maxlength="2">
                                      <input name="awardnumAgain[1]" type="text" value="" maxlength="2">
                                      <input name="awardnumAgain[2]" type="text" value="" maxlength="2">
                                      <input name="awardnumAgain[3]" type="text" value="" maxlength="2">
                                      <input name="awardnumAgain[4]" type="text" value="" maxlength="2">
                                  </div>
                              </td>
                          </tr>
						  </tbody>
					  </table>
				  </div>
			  </div>
			  <div class="pop-foot tac">
				  <a href="javascript:;" class="btn-blue-h32 mlr15" id="issueSubmit">确认</a>
				  <a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
			  </div>
		  </div>
	  </div>
	  <input type="hidden" value="" name="issueId"  id="issueId"/>
	  <input type='hidden' name='type' value='<?php echo $search['type'];?>'/>
  </form>
<div class="pop-dialog" id="dialog-issuedelete" style='display:none;'>
	<div class="pop-in">
		<div class="pop-body">
			<p>请确认删除</p>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:issueDelete();" class="btn-blue-h32 mlr15">确认</a>
			<a href="javascript:closePop();" class="btn-b-white mlr15">取消</a><br/><br/>
            <p style = "color:red">请谨慎删除期次</p>
		</div>
	</div>
</div>
  <div class="pop-dialog" id="alertPop">
	  <div class="pop-in">
		  <div class="pop-head">
			  <h2>提示</h2>
			  <span class="pop-close" title="关闭">关闭</span>
		  </div>
		  <div class="pop-body">
			  <div class="data-table-filter del-percent" id="alertBody">
			  </div>
		  </div>
		  <div class="pop-foot tac">
			  <a href="javascript:;" class="btn-b-white mlr15 pop-cancel">确认</a>
		  </div>
	  </div>
  </div>
<!-- 重算期次 start -->
<div class="pop-dialog" id="recountPop">
  <div class="pop-in">
    <div class="pop-head">
        <h2>
            <?php foreach ($lrule as $l => $types){ ?>
            <?php if($search['type'] == $l){ echo $types['name'].'遗漏重算'; } ?> 
            <?php } ?>
        </h2>
      <span class="pop-close" title="关闭">关闭</span>
    </div>
    <div class="pop-body">
      <div class="data-table-filter del-percent">
        <table>
          <colgroup>
            <col width="68" />
            <col width="350" />
          </colgroup>
          <tbody>
            <tr>
              <th>开始重算期次:</th>
              <td><input type="text" value="" class="ipt w222" name="period"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="pop-foot tac">
      <a href="javascript:;" class="btn-blue-h32" id="recountSubmit">重新计算</a>
    </div>
  </div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script>
var type = '<?php echo $search['type'];?>';
$(function(){
  $(".Wdate1").focus(function(){
        dataPicker();
    });
    
    $("#recount").click(function(){
      popdialog("recountPop");
    });
    
    $("#recountSubmit").click(function(){
      var type = '<?php echo $search['type']; ?>';
      var period = $('input[name="period"]').val();
      $.ajax({
            type: "post",
            url: '/backend/Issue/recountIssue',
            data: {type:type,period:period,env:'<?php echo ENVIRONMENT?>'},
            success: function (data) {
              var data = jQuery.parseJSON(data);
              if(data.status == 'y'){
                closePop();
              }else{  
                alert(data.message);
              }
            }
        });
    });

    //遗漏提交操作
    $(document).on('click', '#recountSubmit', function(){
      var period = $('input[name="period"]').val();
      if(!period)
      {
        layer.alert('请输入开始重算期次~', {icon: 2,btn:'',title:'温馨提示',time:0});
        return ;
      }
      layer.load(0, {shade: [0.5, '#393D49']});
      $.ajax({
            type: "post",
            url: '/backend/Issue/recountIssue',
            data: {type:type,period:period,env:'<?php echo ENVIRONMENT?>'},
            success: function (data) {
              layer.closeAll();
              var data = jQuery.parseJSON(data);
              if(data.status == 'y'){
                closePop();
                layer.alert('遗漏数据重算成功~', {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){} });
              }else{  
                layer.alert(data.message, {icon: 2,btn:'',title:'温馨提示',time:0});
              }
            }
        });
    });
});


$("tr").click(function(){

	$("tr").removeClass("select");
	$(this).addClass("select");
	$("#selectId").val($(this).attr("data-issue"));
});

$("#modifyIssue").click(function(){
	var id = $("#selectId").val();
	if(!id)
	{
		alertPop('请选中要修改的期次');
		return false;
	}
	var status = $(".select").find('.status').html();
	if(status != "截止")
	{
		alertPop('仅对截止的期次进行添加开奖号码操作');
		return false;
	}
	var data  = $(".select").find('.awardNum').html();
	var lType = {'jxsyxw': '江西', 'hbsyxw': '湖北', 'syxw': '', 'gdsyxw': '广东'};
	var title = (lType[type]) + "十一选五 第" + id + "期开奖号码";
	$("#pop_name1").html(title);
	$("#issueId").val(id);
	popdialog("issuePop");
});

$("#issueSubmit").click(function(){
	$.ajax({
		type: "post",
		url: '/backend/Issue/updateAwardNum',
		data: $("#modifyIssueForm").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").serialize(),
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
var issues;
$("#issuesSubmit").click(function(){
	issues = [];
	$(".ck_").each(function(){
		if(this.checked)
		{
			issues.push($(this).val());
		}
	})
    if(issues.length < 1)
    {
      alert('请先选择一次比赛');
      return false;
    }
    else
    {
    	popdialog("dialog-salestart");
    }
});

$("#deleteIssue").click(function(){
	issues = [];
	$(".ck_").each(function(){
		if(this.checked)
		{
			issues.push($(this).val());
		}
	})
    if(issues.length < 1)
    {
      alert('请先选择你要删除的期次');
      return false;
    }
    else
    {
    	popdialog("dialog-issuedelete");
    }
});

function saleStart()
{
	$.ajax({
	    type: "post",
	    url: "/backend/Issue/saleStart",
	    data: {'issues':issues, 'env':'<?php echo ENVIRONMENT?>'},
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
function issueDelete()
{
	$.ajax({
	    type: "post",
	    url: "/backend/Issue/issueDelete",
	    data: {'issues':issues, 'type':type, 'env':'<?php echo ENVIRONMENT?>'},
	    dataType: "text",
	    success: function(data){
	        if(data == true) 
		    {
			    location.href = location.href;
			}
	        else
	        {
	        	closePop();
	        	alert('操作失败 '+ data);
			}
	    }
	})
}
function alertPop(content){
	$("#alertBody").html(content);
	popdialog("alertPop");
}

$('.num-box').on('keyup', 'input', function(){
    if(!/\D/.test($(this).val())){
        if(($(this).val().length == 2)){
            if($(this).index() == $(this).parents('.num-box').find('input').length - 1){
                $(this).blur();
            }else{
                $(this).next().focus();
            }

        }
    }
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
</script>  
</body>
</html>
  