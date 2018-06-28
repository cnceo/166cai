<?php
$this->load->view("templates/head") ;
?>
<div class="path">您的位置：审核管理&nbsp;>&nbsp;<a href="">排行榜活动审核</a></div>
<div class="data-table-filter mt10" style="width:1100px">
    <form action="/backend/Activity/rankCheck" method="get" id="search_form">
        <table>
            <colgroup>
                <col width="90" />
                <col width="150" />
                <col width="90" />
                <col width="600" />
            </colgroup>
            <tbody>
                <tr>
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
                        <label for="ck_status_all" class="mr10"><input type="radio" value="0" name="status" id="ck_status_all" <?php echo ($search['status'] == 0) ? 'checked' : ''; ?> >全部</label>
                        <label for="ck_status_0" class="mr10"><input type="radio" value="1" name="status" id="ck_status_0" <?php echo ($search['status'] == 1) ? 'checked' : ''; ?> >待审核</label>
                        <label for="ck_status_1" class="mr10"><input type="radio" value="2" name="status" id="ck_status_1" <?php echo ($search['status'] == 2) ? 'checked' : ''; ?> >已审核</label>
                    </td>
                    <td colspan="2" style="text-align: center">
                        <a href="javascript:void(0);" class="btn-blue" onclick="$('#search_form').submit();">查询</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</div>

<div class="data-table-list mt20">
    <table>
        <colgroup>
            <col width="10%"/>
            <col width="10%"/>
            <col width="10%"/>
            <col width="10%"/>
            <col width="20%"/>
            <col width="10%"/>
            <col width="10%"/>
            <col width="20%"/>
        </colgroup>
        <tbody>
            <tr>
                <th>活动期次</th>
                <th>彩种系列</th>
                <th>活动开始时间</th>
                <th>活动结束时间</th>
                <th>涉及彩种</th>
                <th>申请时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            <?php if(!empty($result)): ?>
            <?php foreach($result as $key => $val): ?>
            <tr>
                <td><?php echo $val['issue'] ?></td>
                <td><?php echo $val['lname'] ?></td>
                <td><?php echo $val['start_time'] ?></td>
                <td><?php echo $val['end_time'] ?></td>
                <td><?php echo $val['lids'] ?></td>
                <td><?php echo $val['created'] ?></td>
                <td><?php echo $val['statusMsg'] ?></td>
                <td>
                    <?php if($val['cstate'] == 0): ?>
                    <a href="javascript:;" class="toCheck" data-status="1" data-id="<?php echo $val['id'] ?>">审核通过</a>
                    <a href="javascript:;" class="toCheck" data-status="0" data-id="<?php echo $val['id'] ?>">审核失败</a>
                    <?php endif; ?>
                    <a href="/backend/Activity/rankCheckDetail/<?php echo $val['id'] ?>" target="_blank">查看详情</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
        <tfoot>
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
<div class="page mt10 order_info">
    <?php echo $pages[0] ?>
</div>
<script src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
        // 时间控件
        $(".Wdate1").focus(function(){dataPicker();});
        var selectTag = true;
        $(".toCheck").click(function(){
            if(selectTag){
                selectTag = false;
                var id = $(this).attr('data-id');
                var status = $(this).attr('data-status');
                $.ajax({
                    type: "post",
                    url: '/backend/Activity/updateRankCheck',
                    data: {'id': id, 'status': status},
                    success: function (data) {
                        var json = jQuery.parseJSON(data);
                        if(json.status =='y'){
                            alert(json.message);
                            location.reload();
                        }else{
                            alert(json.message);
                        }
                        selectTag = true;
                    },
                    error: function () {
                        alert('网络异常，请稍后再试');
                        selectTag = true;
                    }
                });
            }
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
                   },
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