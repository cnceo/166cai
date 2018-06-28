<?php $this->load->view("templates/head") ?>
<?php 
$aduitArr = array(''=>'全部','0'=>'待审核','1'=>'人工审核','2'=>'系统审核');
$search['type'] = isset($search['type']) ? $search['type'] : 'ssq';
$lname = $lrule[$search['type']]['name'];
?>
<div class="frame-container" style="margin-left:0;">
<div class="path">您的位置：数据中心&nbsp;&gt;&nbsp;<a href="/backend/Issue/management">期次管理</a></div>
<div class="mt10">
    <div class="data-table-filter" style=" width: 100%;">
    <form action="/backend/Issue/management" method="get"  id="search_form">
    <input type="hidden" name="p" value="<?php echo isset($_GET['p'])? (intval($_GET['p'])==0 ? 1: intval($_GET['p'])) : 1; ?>">
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
              <option <?php if($search['aduitflag'] == $l &&$search['aduitflag']!=''):?>selected<?php endif;?> value="<?php echo $l; ?>"><?php echo $types;?></option>
            <?php endforeach;?>      
          </select>
          <a onclick="$('#search_form').submit();" href="javascript:void(0);" class="btn-blue">查询</a>
          <a href="javascript:void(0);" class="btn-blue" id="sh_<?php echo $search['type'] ; ?>" data-capacity = "7_2_8">号码审核</a>
          <a href="javascript:void(0);" class="btn-blue" id="hm_<?php echo $search['type'] ;?>" data-capacity = "7_2_3" >号码录入</a>
          <a href="javascript:void(0);" class="btn-blue" id="modifyDetail" data-capacity = "7_2_4">详情录入</a>
          <a class="btn-blue" id="recount" data-capacity = "7_2_5" >遗漏数据重算</a>
          <input type='hidden' class='vcontent' name='type' value='<?php echo $search['type'] ;?>'/>
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
        <tr data-issue="<?php echo $row['issue'];?>" >
        <?php if($row['status'] == '截止'): ?>
         <td class='firstTd'><input type="checkbox" name="selectIssue" data-issue="<?php echo $row['issue'];?>" data-awardNum="<?php echo $row['awardNum'];?>" data-aduit="<?php echo $row['aduitflag'];?>"></td>
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
<div class="pop-dialog pop-kjhm" id="pop_sh_<?php echo $search['type'] ;?>" style="display:none;">
    <div class="pop-in">
        <div class="pop-head">
            <h2></h2>
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
                                    <input type="text" data-val='0' class='inputTxt' name='awardNums[]' value="" maxlength="1">
                                    <input type="text" data-val='1' class='inputTxt' name='awardNums[]' value="" maxlength="1">
                                    <input type="text" data-val='2' class='inputTxt' name='awardNums[]' value="" maxlength="1">
                                    <input type="text" data-val='3' class='inputTxt' name='awardNums[]' value="" maxlength="1">
                                    <input type="text" data-val='4' class='inputTxt' name='awardNums[]' value="" maxlength="1">
                                    <input type="text" data-val='5' class='inputTxt' name='awardNums[]' value="" maxlength="1">
                                    <input type="text" data-val='6' class='inputTxt' name='awardNums[]' value="" maxlength="1">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p>输入开奖号码格式为1、2 不支持01、02</p>
                <p class="cRed">请谨慎修改开奖号码</p>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15 shBtn">确定</a>
            <a href="javascript:;" class="btn-b-white pop-cancel">取消</a>
        </div>
    </div>
</div>
<!--开奖号码录入-->
<div class="pop-dialog pop-kjhm" id="pop_hm_<?php echo $search['type'] ;?>" style="display:none;">
    <div class="pop-in">
        <div class="pop-head">
            <h2></h2>
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
                                    <input type="text" name='awardNums[]' data-val='0' class="inputTxt1" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='1' class="inputTxt1" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='2' class="inputTxt1" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='3' class="inputTxt1" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='4' class="inputTxt1" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='5' class="inputTxt1" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='6' class="inputTxt1" value="" maxlength="1">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>确认开奖号码：</td>
                            <td>
                                <div class="num-box">
                                    <input type="text" name='awardNums[]' data-val='0' class="inputTxt2" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='1' class="inputTxt2" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='2' class="inputTxt2" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='3' class="inputTxt2" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='4' class="inputTxt2" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='5' class="inputTxt2" value="" maxlength="1">
                                    <input type="text" name='awardNums[]' data-val='6' class="inputTxt2" value="" maxlength="1">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p>输入开奖号码格式为1、2 不支持01、02</p>
                <p class="cRed">请谨慎修改开奖号码</p>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15 hmBtn">确定</a>
            <a href="javascript:;" class="btn-b-white pop-cancel">取消</a>
        </div>
    </div>
</div>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script type="text/javascript">
  var awardNum = '';//开奖号码
  var lname = '<?php echo $lname; ?>';
  var curr_issue = '';
  var type = "<?php echo $search['type'] ;?>";  //获取类型
  var aduitflag = 0;
  var bigIndex = 6;
  var allCapacity = ("<?php echo $allCapacity;?>").split(",");
</script>
<script src="/caipiaoimg/v1.0/js/admin/management.js"></script>
<script>
$(function(){
  var balls = getCode(9,0);
  balls.push('0');
  //审核提交处理 shBtn
  $(document).on('click', '.shBtn', function(){
    var awardNumObj = $("#pop_sh_"+type).find('.inputTxt');
    var awardNumStr = '';
    var awardNumArr = new Array();
    awardNumObj.each(function(index){
        if($.inArray($(this).val(),balls)==-1)
        {
          layer.alert('输入的开奖号码格式不正确~', {icon: 2,btn:'',title:'温馨提示',time:0});
          return;
        }
        awardNumArr.push($(this).val());
    });
    awardNumStr = awardNumArr.join(',');
    //验证是否正确
    if(awardNum!==awardNumStr)
    {
      layer.alert('号码审核失败，请重新审核~', {icon: 2,btn:'',title:'温馨提示',time:0});
      return false;
    }
    //重复审核
    if(aduitflag==1)
    {
      layer.alert('请不要重复提交审核~', {icon: 2,btn:'',title:'温馨提示',time:0});
      return false;
    }
    //发起审核操作
    layer.load(0, {shade: [0.5, '#393D49']});
    $.ajax({
          type: "post",
          url: '/backend/Issue/aduitIssue',
          data: {type:'<?php echo $search['type']; ?>',issue:curr_issue,awardNum:awardNumStr,aduitflag:aduitflag,env:'<?php echo ENVIRONMENT?>'},
          success: function (data) {
            layer.closeAll();
            var data = jQuery.parseJSON(data);
            if(data.status == 'y'){
              closePop();
              //更新号码
              $('tr.select').find('td').eq(6).html('人工审核');
              layer.alert(data.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){} });
            }else{  
              layer.alert(data.message, {icon: 2,btn:'',title:'温馨提示',time:0});
            }
          }
      });
  });
  //开奖号码录入操作
  $(document).on('click', '.hmBtn', function(){
    //验证号码是否相同并且组合字符串
    var awardNumStr = '';
    var tempAward = new Array();//前
    var tag = true;
    $('.inputTxt1').each(function(index){
      if($(this).val()!=$('.inputTxt2').eq(index).val())
      {
        tag = false;
        layer.alert('输入两组号码不匹配~', {icon: 2,btn:'',title:'温馨提示',time:0});
        return ;
      }else{
          if($.inArray($(this).val(),balls)==-1)
          {
            tag = false;
            layer.alert('输入的号码格式不正确~', {icon: 2,btn:'',title:'温馨提示',time:0});
            return;
          }
          tempAward.push($(this).val());
      }
    });
    awardNumStr = tempAward.join(',');
    if(tag===false) return ;
    //录入号码操作
    layer.load(0, {shade: [0.5, '#393D49']});
    $.ajax({
          type: "post",
          url: '/backend/Issue/insertAwardNum',
          data: {type:'<?php echo $search['type']; ?>',issue:curr_issue,awardNum:awardNumStr,aduitflag:aduitflag,env:'<?php echo ENVIRONMENT?>'},
          success: function (data) {
            var data = jQuery.parseJSON(data);
            layer.closeAll();
            if(data.status == 'y'){
              closePop();
              //更新号码
              $('tr.select').find('td').eq(5).html(awardNumStr);
              awardNum = awardNumStr;
              layer.alert(data.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){} });
            }else{  
              layer.alert(data.message, {icon: 2,btn:'',title:'温馨提示',time:0});
            }
          }
      });
  });
  //输入号码下一步操作
  $(document).on('keyup', '.inputTxt,.inputTxt1,.inputTxt2', function(){
      var _JIndex = $(this).attr('data-val');
      var className = '.'+$.trim($(this).attr('class'));
      if(($(this).val()).length == 1 && (_JIndex < $(className).length)  )
      {
        //对前区后区判断
        if( 
          (_JIndex < bigIndex && balls.indexOf($(this).val()) == -1 )||
          (_JIndex == bigIndex && balls.indexOf($(this).val()) == -1 )
         ){
          $(this).val('').focus();
        }else{
          $(className).eq(parseInt(_JIndex)+1).focus();
        }
        if($.trim($(this).attr('class'))=='inputTxt1' && _JIndex == bigIndex)
        {
          $('.inputTxt2').eq(0).val('').focus();
        }

      } 
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
</script>  
</body>
</html>
