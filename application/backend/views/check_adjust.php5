<?php
$this->load->view("templates/head") ;
?>
<div class="path">您的位置：报表管理&nbsp;>&nbsp;<a href="">调账审核</a></div>
<div class="data-table-filter mt10" style="width:1100px">
  <form action="/backend/Transactions/check_adjust" method="get"  id="search_form">
  <table>
    <colgroup><col width="90" /><col width="150" /><col width="90" /><col width="600" /></colgroup>
    <tbody>
    <tr>
      <th>用户名：</th><td><input type="text" class="ipt w120"  name="uname" value="<?php echo $search['uname'] ?>"  placeholder="用户名..." /></td>
      <th>申请时间：</th>
      <td>
        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
    </tr>
    <tr>
      <th>状态：</th>
      <td>
        <label for="ck_status_all" class="mr10"><input type="radio" value="all" name="status" id="ck_status_all" <?php if ($search['status'] === '01') {?>checked<?php }?>>全部</label>
        <label for="ck_status_0" class="mr10"><input type="radio" value="0" name="status" id="ck_status_0" <?php if ($search['status'] === '0') {?>checked<?php }?>>待审核</label>
        <label for="ck_status_1" class="mr10"><input type="radio" value="1" name="status" id="ck_status_1" <?php if ($search['status'] === '1') {?>checked<?php }?>>已操作</label>
      </td>
      <td colspan="2" style="text-align: center">
        <a href="javascript:void(0);" class="btn-blue " onclick="$('#search_form').submit();">查询</a>
      </td>
    </tr>
    </tbody>
  </table>
  </form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="30"/><col width="105"/><col width="108"/><col width="50"/><col width="45"/><col width="70"/><col width="45"/>
      <col width="66"/><col width="66"/><col width="115"/><col width="105"/><col width="40"/><col width="95"/>
    </colgroup>
    <thead>
        <tr>
            <td colspan="13">
                <div class="tal">
                    <strong>调账人数</strong><span><?php echo $count['ucount']?></span>
                    <strong class="ml20">调账笔数</strong><span><?php echo $count['count']?></span>
                    <strong class="ml20">待审核调账订单</strong><span><?php echo $count['dcount']?></span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr><th><input type="checkbox" name="ckallbox"  class="ckbox" value="1"></th><th>调账订单编号</th><th>用户名</th><th>真实姓名</th><th>操作类型</th><th>操作金额(元)</th><th>金额类型</th><th>是否走成本库</th><th>账户明细类型</th><th>备注原因</th><th>申请时间</th><th>状态</th><th>操作</th></tr>
    <?php foreach($data as $key => $val): ?>
      <tr>
      <td><?php if ($val['status'] == 0):?><input type="checkbox" name="ckbox[]"  class="ckbox" value="<?php echo $val['num']?>"><?php endif;?></td>
      <td><?php echo $val['num'] ?></td>
      <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $val['uid'] ?>" class="cBlue"><?php echo $val['uname'] ?></a></td>
      <td><?php echo $val['real_name'] ?></td>
      <td><?php echo $typeArr[$val['type']];?></td>
      <td><?php echo m_format($val['money']);?></td>
      <td><?php echo ($val['type'] == 1) ? '--' : $ismustcostArr[$val['ismustcost']];?></td>
      <td><?php echo $iscapitalArr[$val['iscapital']];?></td>
      <td><?php echo $ctypeArr[$val['ctype']];?></td>
      <td><?php echo $val['comment'] ?></td>
      <td><?php echo $val['created'] ?></td>
      <td><?php echo $statusArr[$val['status']]; ?></td>
      <td>
      <?php if ($val['status'] == 0) {?>
	      <a class="check_success cBlue" href="javascript:;" data-id="<?php echo $val['id']?>" data-num="<?php echo $val['num']?>">审核通过</a>
	      <a class="check_fail cBlue" href="javascript:;" data-id="<?php echo $val['id']?>" data-num="<?php echo $val['num']?>">调账失败</a>
      <?php }?>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="9">
            <div class="tal ptb10 c999">
                <a href="javascript:void(0);" class="btn-blue mr10" id="successCheck" data-type="1" style="width:90px;">审核通过</a>
                <a href="javascript:void(0);" class="btn-blue mr10" id="failCheck"  data-type="0" style="width:90px;">审核失败</a>
            </div>
        </td>
      </tr>
      <tr>
        <td colspan="9">
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
<div class="page mt10"><?php echo $pages[0] ?></div>
<div class="pop-dialog" id="J-dc-failed">
    <div class="pop-in">
        <div class="pop-head"><h2 id="lockHead">调账失败</h2><span class="pop-close" title="关闭">关闭</span></div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table><colgroup><col width="70" /><col width="240" /></colgroup><tbody><tr><td>失败原因：</td><td><textarea id="failreason"></textarea></td></tr></tbody></table>
            </div>
        </div>
        <div class="pop-foot tac"><a href="javascript:;" class="btn-blue-h32 mlr15" id='submitfailed'>确 认</a><a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a></div>
    </div>
</div>
<script src="/source/date/WdatePicker.js"></script>
<script>
checkAll($("input[name='ckallbox']"), $("input[name='ckbox[]']"));
    $(function(){
       $(".Wdate1").focus(function(){dataPicker();});
       $("#successCheck,#failCheck").click(function(){
    	   <?php if ($cancheck) {?>
           var s = '';
           s = getCheckVal("ckbox[]");
           if(!s){
               return false;
           }
           var type = $(this).data('type');
           var ss = '';
           if(type == '1'){
        	   ss = '请确认对已选的'+ s.split(",").length +'个调账订单批量审核通过！';
           }else{
        	   ss = '请确认对已选的'+ s.split(",").length +'个调账订单批量审核失败！';
           }
           if(confirm(ss)) {
        	   $.ajax({
                   type: "post",
                   url: '/backend/Transactions/adjustBatch',
                   data: {'ids': s, 'type': type},
                   success: function (data) {
                       var json = jQuery.parseJSON(data);
                       if(json.status =='y'){
                           alert(json.message);
                           location.reload();
                       }
                   }
               });
           }
           return false;
           <?php } else {?>
           		alert('您没有调账审核权限！');
           <?php }?>
       });
       
       $('.check_success').click(function(){
           <?php if ($cancheck) {?>
           if(confirm('确定调账成功？')) {
        	   var id = $(this).data('id');
        	   var num = $(this).data('num');
        	   $.ajax({
        		   type: 'post',
            	   url:'/backend/Transactions/adjust',
            	   data:{id:id, num:num, type:1},
            	   success:function(data){
                	   if (data == 1) {
                    	   alert('调账成功！');
                    	   location.reload();
                	   }else if(data == 2) {
                    	   alert('扣款金额大于用户余额！');
                       } else {
                    	   alert('调账失败！');
                	   }
                   }
               })
    	   }
           <?php } else {?>
           alert('您没有调账审核权限！');
           <?php }?>
    	   
       })
       $('.check_fail').click(function(){
    	   <?php if ($cancheck) {?>
    	   var id = $(this).data('id');
    	   var num = $(this).data('num');
           $("#failreason").attr('data-id', id);
           $("#failreason").attr('data-num', num);
    	   popdialog("J-dc-failed");
           <?php } else {?>
           alert('您没有调账审核权限！');
           <?php }?>
       })
       $("#submitfailed").click(function(){
    	   $.ajax({
    		   type: 'post',
        	   url:'/backend/Transactions/adjust',
        	   data:{id:$('#failreason').attr('data-id'), num:$('#failreason').attr('data-num'), type:2, failreason:$('#failreason').val()},
        	   success:function(data){
            	   if (data == 1) {
                	   alert('操作成功！');
                	   location.reload();
            	   } else {
                	   alert('操作失败！');
            	   }
               }
           })
       })
    });
</script>
</body>
</html>