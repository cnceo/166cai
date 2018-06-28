<?php $this->load->view("templates/head") ?>
<div class="frame-container-xxx" style="padding: 10px 30px; *zoom: 1;">
    <div class="path">您的位置：数据中心&nbsp;&gt;&nbsp;<a href="/backend/Issue/pre_issue">期次预排</a></div>
  <div class="mod-tab mt20">
    <div class="mod-tab-hd">
      <ul>
        <li class="current">
          <a href="/backend/Issue/pre_issue">彩种预排时间配置</a>
        </li>
        <li>
          <a href="/backend/Issue/delay_issue">停售时间配置</a>
        </li>
      </ul> 
    </div>
    <div class="mod-tab-bd">
      <ul>
        <li style="display: block;">
          <div class="data-table-filter">
            <table>
              <tbody>
              <tr>
                <td colspan="6">
                  彩种：
                  <select class="selectList w222" id="" name="" onchange="window.location.href=this.options[selectedIndex].value">
                    <?php foreach ($lrule as $l => $types): ?>
                      <?php if(!empty($types['rule'])):?>
                      <option <?php if($type == $l):?>selected<?php endif;?> value="/backend/Issue/pre_issue/<?php echo $l; ?>"><?php echo $types['name'];?></option>
                      <?php endif;?>
                    <?php endforeach;?> 
                  </select>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <div class="data-table-list mt10">
            <table>
              <colgroup>
                <col width="15%">
                <col width="20%">
                <col width="15%">
                <col width="15%">
                <col width="20%">
                <col width="15%">
              </colgroup>
              <tbody>
                <tr>
                  <th>彩种</th>
                  <th>提前截止时间(分钟)</th>
                  <th>开奖时间</th>                 
                  <th><?php if($isGaopin):?>预排天数<?php else:?>预排期次<?php endif;?></th>
                  <th>预排时间</th>
                  <th>操作</th>
                </tr>
                <tr>
                 <td><?php echo $name;?></td>
                 <td><?php echo $early_time;?></td>
                 <td><?php echo $award_time;?></td>
                 <td><?php echo $issue_num;?></td>
                 <td><?php echo $start_date;?></td>
                 <td><a href="javascript:void(0);" class="btn-blue" id="modify-issue" data-value="<?php echo $lid; ?>">修改</a></td>
                </tr>
              </tbody>
            </table>
          </div>
        </li>
      </ul> 
    </div>
  </div>
</div>
<!-- 弹出层 -->
<div class="pop-mask" style="display:none;width:200%"></div>
<form id="timeForm" method="post" action="">
<!-- 彩种预排时间修改 start -->
<div class="pop-dialog" id="modifyPop">
  <div class="pop-in">
    <div class="pop-head">
      <h2>彩种预排时间</h2>
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
              <th>彩种:</th>
              <td><?php echo $name;?></td>
            </tr>
            <tr>
              <th>预排时间：</th>
              <td><input type="text" value="<?php echo $start_date;?>" class="ipt w222" name="start_date"></td>
            </tr>
            <?php if($type == 'syxw'):?>
            <input type="hidden" value="10" class="ipt w222" name="early_time">
            <?php else: ?>
            <tr>
              <th>提前截止时间：</th>
              <td><input type="text" value="<?php echo $early_time;?>" class="ipt w222" name="early_time"><span></span></td>
            </tr>
            <?php endif;?>
            <tr>
              <th><?php if($isGaopin):?>预排天数：<?php else: ?>预排期数：<?php endif;?></th>
              <td><input type="text" value="<?php echo $issue_num;?>" class="ipt w222" name="issue_num"><span>1的整数倍</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="pop-foot tac">
      <a href="javascript:;" class="btn-blue-h32" id="modifySubmit">确认</a>
    </div>
  </div>
</div>
<!-- 彩种预排时间修改 end -->
<input type="hidden" value="" name="" id="">
</form>
<script>
  $(function(){

    $("#modify-issue").click(function(){
      popdialog("modifyPop");
    }); 

    $("#modifySubmit").click(function(){
      var type = $('#modify-issue').attr('data-value');
      var start_date = $('input[name="start_date"]').val();
      var early_time = $('input[name="early_time"]').val();;
      var issue_num = $('input[name="issue_num"]').val();;
      $.ajax({
            type: "post",
            url: '/backend/Issue/modifyPreIssue',
            data: {type:type,start_date:start_date,early_time:early_time,issue_num:issue_num},
            success: function (data) {
              var data = jQuery.parseJSON(data);
              if(data.status == '00'){
                window.location.href = '/backend/Issue/pre_issue/'+type;
              }else{  
                alert(data.message);
              }
            }
        });
    }); 

  })
</script>
</body>
</html>
