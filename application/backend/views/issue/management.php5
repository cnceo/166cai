<?php $this->load->view("templates/head") ?>
<?php $aduitArr = array(''=>'全部','0'=>'待审核','1'=>'人工审核');?>
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
          <select class="selectList w100" id="" name="" onchange="window.location.href=this.options[selectedIndex].value">
            <?php foreach ($lrule as $l => $types): ?>
              <option <?php if($search['type'] == $l):?>selected<?php endif;?> value="/backend/Issue/management/?type=<?php echo $l; ?>"><?php echo $types['name'];?></option>
            <?php endforeach;?>      
          </select>
          审核状态：
          <select class="selectList w100" id="" name="aduitflag">
            <?php foreach ($aduitArr as $l => $types): ?>
              <option <?php if($search['aduitflag'] == $l &&$search['aduitflag']!==''):?>selected<?php endif;?> value="<?php echo $l; ?>"><?php echo $types;?></option>
            <?php endforeach;?>      
          </select>
          <a href="javascript:void(0);" class="btn-blue" id="searchBtn">查询</a>
          <a href="javascript:void(0);" class="btn-blue" id="sh_<?php echo $search['type'];?>">号码审核</a>
          <a href="javascript:void(0);" class="btn-blue" id="chaxun">号码录入</a>
          <a href="javascript:void(0);" class="btn-blue" id="chaxun">详情录入</a>
          <?php if($search['type'] == 'syxw'): ?>
          <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1"><i></i></span>
          <span class="ml8 mr8">至</span>
          <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1"><i></i></span>
          <?php endif;?>
          <input type='hidden' class='vcontent' name='type' value='<?php echo $search['type'];?>'/>
          <!-- <a onclick="$('#search_form').submit();" href="javascript:void(0);" class="btn-blue">查询</a> -->
          <a href="javascript:void(0);" class="btn-blue" id="modifyIssue">修改开奖信息</a>
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
      	<col width="10%">
        <col width="10%">
        <col width="10%">
        <col width="15%">
        <col width="15%">
        <col width="15%">
        <col width="15%">
        <col width="10%">
      </colgroup>
      <thead>
        <tr>
          <th><input type="checkbox" disabled="" /></th>
          <th>期号</th>
          <th>状态</th>
          <th>开始时间</th>
          <th>截止时间</th>
          <th>开奖号码</th>
          <th>号码审核</th>
          <th>开奖详情</th>
        </tr>
      </thead>
      <?php if($result): ?>
      <tbody>
        <?php foreach ($result as $row):?>
        <tr data-issue="<?php echo $row['issue'];?>">
        <?php if($row['status'] == '截止'): ?>
         <td><input type="checkbox" name="selectIssue" data-issue="<?php echo $row['issue'];?>"></td>
        <?php else: ?>
         <td></td>
        <?php endif;?> 
         <td><?php echo $row['issue'];?></td>
         <td class = "status"><?php echo $row['status'];?></td>
         <td><?php echo $row['sale_time'];?></td>
         <td><?php echo $row['end_time'];?></td>
         <?php if($row['compare_status'] == '2'): ?>
         <td>核对异常，<a href="javascript:void(0);" data-issue="<?php echo $row['issue'];?>" class="compare-detail">点击查询</a></td>
         <?php else: ?>
         <td><?php echo $row['awardNum'];?></td>
         <?php endif;?>
         <td><?php echo $aduitArr[$row['aduitflag']] ; ?></td>
         <?php if($row['rstatus'] == '2'): ?>
         <td>详情核对异常</td>
         <?php elseif($row['rstatus'] >= '50'): ?>
         <td><a href="/backend/Issue/detail?lid=<?php echo $search['type'];?>&issue=<?php echo $row['issue'];?>">查看详情</a></td>
         <?php else: ?>
         <td></td>
         <?php endif;?>
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

<!-- 弹出层 -->
<div class="pop-mask" style="display:none;width:200%"></div>
<form id="compareForm" method="post" action="">
</form>
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
<!--审核弹窗层-->
<div class="pop-dialog pop-kjhm" id="shp_<?php echo $search['type'];?>" style="display:none;">
    <div class="pop-in">
        <div class="pop-head">
            <h2>双色球2014151期开奖号码</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup>
                        <col width="86">
                        <col>
                    </colgroup>
                    <tbody>
                        <tr>
                            <td>开奖号码：</td>
                            <td>
                                <div class="num-box">
                                    <span class="cRed">红色</span>
                                    <input type="text" value="" maxlength="2">
                                    <input type="text" value="" maxlength="2">
                                    <input type="text" value="" maxlength="2">
                                    <input type="text" value="" maxlength="2">
                                    <span class="cBlue">红色</span>
                                    <input type="text" value="" maxlength="2">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>确认开奖号码：</td>
                            <td>
                                <div class="num-box">
                                    <input type="text" value="" maxlength="2">
                                    <input type="text" value="" maxlength="2">
                                    <input type="text" value="" maxlength="2">
                                    <input type="text" value="" maxlength="2">
                                    <input type="text" value="" maxlength="2">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p>输入开奖号码格式为01、02 不支持1、2</p>
                <p class="cRed">请谨慎修改开奖号码</p>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15">确定</a>
            <a href="javascript:;" class="btn-b-white">取消</a>
        </div>
    </div>
</div>

<script  src="/source/date/WdatePicker.js"></script>
<script>
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
              console.log(data);
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

$("#modifyIssue").click(function(){
    var id = $("#selectId").val();
    if(!id){
      alert('请先选择你要修改的期次');
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

$(".compare-detail").click(function(){
      var issue = $(this).attr("data-issue");
      var type = $("input[name='type']").val();
      $.ajax({
            type: "post",
            url: '/backend/Issue/compareDetail',
            data: {type:type,issue:issue},
            success: function (data) {
                if(data == '2')
                {
                  alert('获取不到任何开奖抓取信息');
                }else{
                  $('#compareForm').html(data);
                  popdialog("idetail");
                }
            }
        });
    });
//新加的逻辑
$(function(){
  //勾选
  $('input[name=selectIssue]').click(function(){
    $('input[name=selectIssue]').removeAttr('checked');
    $(this).attr('checked','checked');
  });
  //查询操作
  $('#searchBtn').click(function(){
    $('#search_form').submit();
  });
  //
  $("#sh_"+"<?php echo $search['type'];?>").click(function(){
    popdialog("shp_<?php echo $search['type'];?>");
  });  
});
</script>  
</body>
</html>
