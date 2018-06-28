<?php $this->load->view("templates/head") ?>
<div class="frame-container-xxx" style="padding: 10px 30px; *zoom: 1;">
    <div class="path">您的位置：数据中心&nbsp;&gt;&nbsp;<a href="/backend/Issue/pre_issue">期次预排</a></div>
  <div class="mod-tab mt20">
    <div class="mod-tab-hd">
      <ul>
        <li>
          <a href="/backend/Issue/pre_issue">彩种预排时间配置</a>
        </li>
        <li class="current">
          <a href="/backend/Issue/delay_issue">停售时间配置</a>
        </li>
      </ul> 
    </div>
    <div class="mod-tab-bd">
      <ul>
        <li style="display: block;">
          <div class="data-table-filter">
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
                <td colspan="6">
                  彩种：
                  <select class="selectList w222" id="" name="" onchange="window.location.href=this.options[selectedIndex].value">
                    <?php foreach ($lrule as $l => $types): ?>
                      <?php if(!in_array($l, array('syxw', 'jxsyxw', 'ks', 'jlks', 'jxks', 'hbsyxw', 'klpk', 'cqssc', 'gdsyxw')) && !empty($types['rule'])): ?>
                      <option <?php if($type == $l):?>selected<?php endif;?> value="/backend/Issue/delay_issue/<?php echo $l; ?>"><?php echo $types['name'];?></option>
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
                <col width="20%">
                <col width="30%">
                <col width="30%">
                <col width="20%">
              </colgroup>
              <tbody>
                <tr>
                  <th>彩种</th>
                  <th>停售开始时间</th>
                  <th>停售截止时间</th>
                  <th>操作</th>
                </tr>
                <tr>
                 <td><?php echo $name;?></td>
                 <td><?php echo $delay_start_time;?></td>
                 <td><?php echo $delay_end_time;?></td>
                 <td><a href="javascript:void(0);" class="btn-blue" id="delay-issue" data-value="<?php echo $lid; ?>">修改</a></td>
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
<!-- 停售配置 start -->
<div class="pop-dialog" id="delayPop">
  <div class="pop-in">
    <div class="pop-head">
      <h2>停售配置</h2>
      <span class="pop-close" title="关闭">关闭</span>
    </div>
    <div class="pop-body">
      <div class="data-table-filter del-percent">
        <table>
          <colgroup>
            <col width="68">
                    <col width="350">
          </colgroup>
          <tfoot>
            <tr>
              <td colspan="2"><p>请确认所输入期次开奖时间是否在停售期内第一期</p></td>
            </tr>
          </tfoot>
          <tbody>
            <tr>
              <th>彩种:</th>
              <td><?php echo $name;?></td>
            </tr>
            <tr>
              <th>停售开始时间：</th>
              <td><input type="text" value="<?php echo $delay_start_time;?>" class="ipt w222" name="delay_start_time"></td>
            </tr>
            <tr>
              <th>停售截止时间：</th>
              <td><input type="text" value="<?php echo $delay_end_time;?>" class="ipt w222" name="delay_end_time"></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div class="pop-foot tac">
      <a href="javascript:;" class="btn-blue-h32 mlr15" id="delaySubmit">确认</a>
    </div>
  </div>
</div>
<!-- 彩种预排时间修改 end -->
<input type="hidden" value="" name="" id="">
</form>

<script>
  $(function(){

    $("#delay-issue").click(function(){
      popdialog("delayPop");
    }); 

    $("#delaySubmit").click(function(){
      var type = $('#delay-issue').attr('data-value');
      var delay_start_time = $('input[name="delay_start_time"]').val();
      var delay_end_time = $('input[name="delay_end_time"]').val();
      $.ajax({
            type: "post",
            url: '/backend/Issue/delayPreIssue',
            data: {type:type,delay_start_time:delay_start_time,delay_end_time:delay_end_time},
            success: function (data) {
              var data = jQuery.parseJSON(data);
              if(data.status == '00'){
                window.location.href = '/backend/Issue/delay_issue/'+type;
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
