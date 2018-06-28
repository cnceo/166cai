<?php $this->load->view("templates/head") ?>
<?php 
    $awardtype = array(
        'S' => '黑桃',
        'H' => '红桃',
        'C' => '梅花',
        'D' => '方块'
    );
?>
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
	          	<span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1"><i></i></span>
	          	<span class="ml8 mr8">至</span>
	          	<span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1"><i></i></span>
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
         		<td class = "awardNum">
                    <?php
                        $awardNum = '';
                        if(!empty($row['awardNum']))
                        {
                            $awardArr = explode('|', $row['awardNum']);
                            $numArr = explode(',', $awardArr[0]);
                            $typeArr = explode(',', $awardArr[1]);
                            $awardNum .= $awardtype[$typeArr[0]] . $numArr[0] . ',' . $awardtype[$typeArr[1]] . $numArr[1] . ',' . $awardtype[$typeArr[2]] . $numArr[2];
                        }
                        echo $awardNum;
                    ?>
                </td>
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
				  	<span class="pop-close _Jcancle" title="关闭">关闭</span>
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
							  	<td colspan="2"><p>输入开奖号码格式为花色+牌号（01-13）</p></td>
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
                                  		<select class="selectList w60 fl ml10" id="" name="awardtype[0]" >    
                          								<option selected value="S">黑桃</option> 
                          								<option value="H">红桃</option> 
                          								<option value="C">梅花</option> 
                          								<option value="D">方块</option>    
          							             	</select>
                                      <input name="awardnum[0]" type="text" value="" maxlength="2" class='inputTxt' data-val ='0'>
                                      <select class="selectList w60 fl ml10" id="" name="awardtype[1]" >    
                        								<option selected value="S">黑桃</option> 
                        								<option value="H">红桃</option> 
                        								<option value="C">梅花</option> 
                        								<option value="D">方块</option>    
          								            </select>
                                      <input name="awardnum[1]" type="text" value="" maxlength="2" class='inputTxt' data-val ='1'>
                                      <select class="selectList w60 fl ml10" id="" name="awardtype[2]" >    
                        								<option selected value="S">黑桃</option> 
                        								<option value="H">红桃</option> 
                        								<option value="C">梅花</option> 
                        								<option value="D">方块</option>    
          								            </select>
                                      <input name="awardnum[2]" type="text" value="" maxlength="2" class='inputTxt' data-val ='2'>
                                  	</div>
                              	</td>
                          	</tr>
                          	<tr>
                              	<td>确认开奖号码：</td>
                              	<td>
                                  	<div class="num-box">
                                  		<select class="selectList w60 fl ml10" id="" name="awardtypeAgain[0]" >    
                        								<option selected value="S">黑桃</option> 
                        								<option value="H">红桃</option> 
                        								<option value="C">梅花</option> 
                        								<option value="D">方块</option>    
          								            </select>
                                      <input name="awardnumAgain[0]" type="text" value="" maxlength="2" class='inputTxt' data-val ='3'>
                                      <select class="selectList w60 fl ml10" id="" name="awardtypeAgain[1]" >    
                        								<option selected value="S">黑桃</option> 
                        								<option value="H">红桃</option> 
                        								<option value="C">梅花</option> 
                        								<option value="D">方块</option>
          								            </select>
                                      <input name="awardnumAgain[1]" type="text" value="" maxlength="2" class='inputTxt' data-val ='4'>
                                      <select class="selectList w60 fl ml10" id="" name="awardtypeAgain[2]" >    
                        								<option selected value="S">黑桃</option> 
                        								<option value="H">红桃</option> 
                        								<option value="C">梅花</option> 
                        								<option value="D">方块</option>    
          								            </select>
                                      <input name="awardnumAgain[2]" type="text" value="" maxlength="2" class='inputTxt' data-val ='5'>
                                  	</div>
                              	</td>
                          	</tr>
						  	</tbody>
					  	</table>
				  	</div>
			  	</div>
			  	<div class="pop-foot tac">
				  	<a href="javascript:;" class="btn-blue-h32 mlr15" id="issueSubmit">确认</a>
				  	<a href="javascript:;" class="btn-b-white mlr15 pop-cancel _Jcancle">取消</a>
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
});


$("tr").click(function(){

	$("tr").removeClass("select");
	$(this).addClass("select");
	$("#selectId").val($(this).attr("data-issue"));
});
$('._Jcancle').click(function(){
  $('input[type=checkbox]').attr('checked',false);
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
	var title = "快乐扑克 第" + id + "期开奖号码";
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

function issueDelete()
{
	$.ajax({
	    type: "post",
	    url: "/backend/Issue/issueDelete",
	    data: {'issues':issues, 'type':type,env:'<?php echo ENVIRONMENT?>'},
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
$(function(){
  //进入下一步
  $(document).on('keyup', '.inputTxt', function(){
      var _JIndex = $(this).attr('data-val');
      var _JArr = ['01','02','03','04','05','06','07','08','09','10','11','12','13'];
      if(($(this).val()).length == 2 && (_JIndex < $('.inputTxt').length)  )
      {
        if(_JArr.indexOf($(this).val()) == -1)
        {
          $(this).val('');
          $(this).focus();
        }else{
          $('.inputTxt').eq(parseInt(_JIndex)+1).focus();
        }
      } 
  });


});
</script>  
</body>
</html>
  