<?php $this->load->view("templates/head") ?>
<?php 
$stauts = array('0' => '待打款', '5' => '已提交银行');
?>
<div class="path">您的位置：审核管理&nbsp;>&nbsp;<a href="">提款审核</a></div>
<div class="data-table-filter mt10" style="width:1080px">
    <form action="/backend/Transactions/list_check" method="get" id="search_form">
  <table>
    <colgroup>
      <col width="70" />
      <col width="140" />
      <col width="80" />
      <col width="160" />
      <col width="80" />
      <col>
    </colgroup>
    <tbody>
    <tr>
      <th>提款用户：</th>
      <td>
          <input type="text" class="ipt w120" placeholder="用户名..."  name="name" value="<?php echo $search['name']; ?>"/>
      </td>
      <th>提款订单编号：</th>
      <td>
        <input type="text" class="ipt w150"  style=" " name="trade_no" value="<?php echo $search['trade_no'] ?>"  placeholder="提款订单编号..." />
      </td>
      <th>申请时间：</th>
      <td>
        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
    </tr>
    <tr>
      <th>状态：</th>
      <td colspan="3">
          <label for="ctypeall" class="mr10"><input type="radio" class="radio" name="ctype"  value="" <?php if($search['ctype']==''): echo "checked"; endif; ?>>全部</label>
          <label for="ctype1" class="mr10"><input type="radio" class="radio" name="ctype" value="1" <?php if($search['ctype']==='1'): echo "checked"; endif; ?>>人工审核</label>
          <label for="ctype0" class="mr10"><input type="radio" class="radio" name="ctype" value="0" <?php if($search['ctype']==='0'): echo "checked"; endif; ?>>待打款</label>
          <label for="ctype5" ><input type="radio" class="radio" name="ctype"  value="5" <?php if($search['ctype']==='5'): echo "checked"; endif; ?>>已提交银行</label>
      </td>
      <td colspan="2">
        <a href="javascript:void(0);" class="btn-blue " onclick="$('#search_form').submit();">查询</a>
      </td>
    </tr>
    </tbody>
  </table>
        <input type="hidden" name="jylx" value="1" />
    </form>
</div>
<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="40" />
      <col width="130" />
      <col width="120" />
      <col width="100" />
      <col width="110" />
      <col width="130" />
      <col width="100" />
      <col width="120" />
      <col width="70" />
      <col width="70" />
      <col width="100" />
      <col width="150" />
    </colgroup>
    <thead>
        <tr>
            <td colspan="<?php echo ($search['ctype'] === '5')?13:12; ?>">
                <div class="tal">
                    <strong>申请人数：</strong>
                    <span><?php echo intval($tj[0]); ?> 人</span>
                    <strong class="ml20">申请总额：</strong>
                    <span><?php echo m_format($tj[1]); ?> 元</span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr>
      <th><input type="checkbox" name="ckallbox"  class="ckbox" value="1"></th>
      <th>提款订单编号</th>
      <th>用户名</th>
      <th>真实姓名</th>
      <th>提款账户银行</th>
      <th>银行卡号</th>
      <th>提款金额（元）</th>
      <th>提款申请时间</th>
      <th>省</th>
      <th>市</th>
      <th>状态</th>
      <th>操作</th>
      <?php if($search['ctype'] === '5'): ?>
      <th>失败原因</th>
      <?php endif; ?>
    </tr>
    <?php  foreach($checks as $key => $check): ?>
    <tr>
     <td><input type="checkbox" name="ckbox[]"  class="ckbox" value="<?php  echo $check['trade_no']  ?>"></td>
      <td><?php echo $check['trade_no']  ?></td>
      <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $check['uid'] ?>" class="cBlue"><?php echo $check['uname']  ?></a></td>
      <td><?php echo $check['real_name']  ?></td>
      <td><?php $bank_tmp= explode("（", $this->pay_cfg['chinabank']['child'][$check['bank_name']][0]);echo $bank_tmp[0]; ?></td>
      <td><?php echo $check['bank_id']  ?></td>
      <td id="<?php echo $check['trade_no']  ?>" data-money="<?php echo $check['money'];?>"><?php echo m_format($check['money'])  ?></td>
      <td><?php echo $check['created']  ?></td>
      <td><?php echo $check['bank_province']  ?></td>
      <td><?php echo $check['bank_city']  ?></td>
      <td><?php if($check['review']==='0' && $check['status']==='0'){ echo "待审核"; }else{echo $stauts[$check['status']];} ?></td>
      <td><?php if($search['ctype'] === '5'): ?>
          <a href="" class="cBlue dc-success " data-id="<?php  echo $check['trade_no']  ?>" >提款成功</a><a href="" class="cBlue dc-fail mlr10" data-id="<?php  echo $check['trade_no']  ?>">提款失败</a><a href="" class="cBlue dc-query mlr10" data-channel="<?php  echo $check['withdraw_channel']  ?>" data-id="<?php  echo $check['trade_no']  ?>">状态查询</a>
        <?php elseif($search['ctype'] === '1') :?>
          <a href="" class="cBlue dc-operation mlr10" data-id="<?php  echo $check['trade_no']  ?>">人工审核通过</a><a href="" class="cBlue dc-fail mlr10" data-id="<?php  echo $check['trade_no']  ?>">提款失败</a>
        <?php elseif($search['ctype'] === '0') :?>
          <a href="" class="cBlue dc-submit mlr10" data-id="<?php  echo $check['trade_no']  ?>">提交至银行</a>
        <?php endif;  ?></td>
      <?php if($search['ctype'] === '5'): ?>
      <td><?php echo $check['remark']  ?></td>
      <?php endif; ?>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="10">
            <div class="tal ptb10 c999">
                <?php if($search['ctype'] === '1'): ?><a href="javascript:void(0);" class="btn-blue mr10" id="export">导出所有</a><a href="javascript:void(0);" class="btn-blue mr10" id="hasOperation">人工审核通过</a><?php endif;  ?>
                <?php if($search['ctype'] === '5'): ?><a href="javascript:void(0);" class="btn-blue mr10" id="batchCheck" style="width:90px;">批量提款成功</a><?php endif;  ?>
                <?php if($search['ctype'] === '5'): ?><a href="javascript:void(0);" class="btn-blue mr10" id="batchCheck2" style="width:90px;">批量提款失败</a><?php endif;  ?>
            </div>
        </td>
      </tr>

      <tr>
        <td colspan="10">
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
<div class="page mt10">
<?php echo $pages[0] ?>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<!-- 审核弹窗 -->
<form id='checkForm' method='post' action=''>
<div class="pop-dialog" id="J-dc-addAccount">
    <div class="pop-in">
        <div class="pop-head">
            <h2 id="pop_name"></h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup>
                        <col width="80" />
                        <col width="180" />
                        <col width="80" />
                        <col width="180" />
                    </colgroup>
                    <tbody id="tbody">

                    </tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15" id='submitForm'>提 交</a>
            <a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a>
        </div>
    </div>
</div>
    <input type="hidden" value="" name="hid_order_id"  id="hid_order_id"/>
    <input type="hidden" value="" name="hid_status"  id="hid_status"/>
</form>
<form id='batchCheckForm' method='post' action=''>
<div class="pop-dialog" id="J-dc-batchCheck">
    <div class="pop-in">
        <div class="pop-head">
            <h2 id="pop_name1"></h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <colgroup>
                        <col width="80" />
                        <col width="180" />
                    </colgroup>
                    <tbody id="tbody1">

                    </tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15" id='submitForm1'>确 认</a>
            <a href="javascript:;" class="btn-b-white mlr15 pop-cancel" >取 消</a>
        </div>
    </div>
</div>
	<input type="hidden" value="" name="hid_status"  id="b_hid_status"/>
    <input type="hidden" value="" name="hid_order_ids"  id="hid_order_ids"/>
</form>
<script  src="/source/date/WdatePicker.js"></script>
<script>
checkAll($("input[name='ckallbox']"), $("input[name='ckbox[]']"));
var pay_cfg =jQuery.parseJSON('<?php echo  json_encode($this->pay_cfg) ?>');
var rtype_1 = '<?php echo $search['rtype1'] ?>', isreq = true;
$(function(){
    $("#hasOperation").click(function(){
        var s = '';
        s = getCheckVal("ckbox[]");
        if(!s)
        {
            return false;
        }
        if (isreq) {
        	isreq = false;
        	$.ajax({
                type: "post",
                url: '/backend/Transactions/has_operation',
                data: {'ids': s},
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    if(json.status =='y')
                    {
                        alert(json.message);
                        location.reload();
                    }
                    else
                    {
                    	isreq = true;
                        alert((json.message+json.info.join(",")));
                    }
                },
                error: function(){
                	isreq = true;
                }
            });
        }
        return false;
    });

    $(".dc-operation").click(function(){
   		if (isreq) {
   			isreq = false;
   			var id = $(this).attr("data-id");
   			$.ajax({
   	            type: "post",
   	            url: '/backend/Transactions/has_operation',
   	            data: {'ids': id},
   	            success: function (data) {
   	                var json = jQuery.parseJSON(data);
   	                if(json.status =='y')
   	                {
   	                    alert(json.message);
   	                    location.reload();
   	                }
   	                else
   	                {
   	                	isreq = true;
   	                    alert((json.message+json.info.join(",")));
   	                }
   	            },
                error: function(){
                	isreq = true;
                }
   	        });
   		}
   		
        return false;
    });
    
    $(".Wdate1").focus(function(){
        dataPicker();
    });
    
    $(".dc-success,.dc-fail").click(function(){
        var _this = $(this);
        if(_this.hasClass("dc-success"))
        {
            info = "请确认您已审核通过并打款成功";
            title = '提款成功';
            mark = "";
            $("#hid_status").val(6);
        }
        else
        {
            title = '提款失败';
            info = "请填写提款失败具体原因";
            mark =  '<tr><th rowspan="5">失败原因：</th><td colspan="2"><input type="radio" name="failreason"><input value="户名或卡号信息错误，请核对" readonly style="width:200px"></td><td>·银行卡查询异常<br>·户名或卡号错误<br>·收款银行卡和姓名不一致</td></tr>\
            	<tr><td colspan="2"><input type="radio" name="failreason"><input value="不支持此卡，建议换卡重提" readonly style="width:200px"></td><td>·卡状态异常<br>·已销户<br>·交易不被银行受理<br>·II、III类账户限制</td></tr>\
            	<tr><td colspan="2"><input type="radio" name="failreason"><input value="不支持信用卡提现，请更换储蓄卡" readonly style="width:200px"></td><td>·商户未开通权限</td></tr>\
            	<tr><td colspan="2"><input type="radio" name="failreason"><input value="银行维护中，请稍后再试" readonly style="width:200px"></td><td>·银行维护中</td></tr>\
            	<tr><td colspan="3"><input type="radio" name="failreason" checked>其他<input class="ipt w360"></td></tr>';
            $("#hid_status").val(8);
        }
        var td = _this.parent("td").siblings("td");
        html = '<tr><td colspan=4>'+info+'</td></tr>';
        html += "<tr><th>用户名：</th><td>"+td.eq(3).html()+"</td><th>提款银行：</th><td>"+td.eq(4).html()+"</td></tr>";
        html += "<tr><th>提款卡号：</th><td>"+td.eq(5).html()+"</td><th>申请时间：</th><td>"+td.eq(7).html()+"</td></tr>";
        html += "<tr><th>提款金额：</th><td>"+td.eq(6).html()+"</td><th></th><td></td></tr>";
        $("#tbody").html(html+mark);
        $("#pop_name").html(title);
        $("#hid_order_id").val(_this.attr("data-id"));
        popdialog("J-dc-addAccount");
        return false;
    });
    
    $("#submitForm").click(function(){
        var data = $("#checkForm").serialize();
        data += '&content='+$('#J-dc-addAccount').find('input[name=failreason]:checked').parents('td').find('input:last').val();  
        console.log(data);
        $.ajax({
            type: "post",
            url: '/backend/Transactions/check',
            data: data,
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
    
    $("#export").click(function(){
        location.href="/backend/Transactions/export?<?php echo http_build_query($search);?>";
    });
    $("#rtype").bind('change', function(){
        if($("#rtype1").length > 0 || $(this).val()==0)
        {
            $("#rtype1").remove();
            if($(this).val()==0)
                return ;
        }
        rtype1 = pay_cfg[$(this).val()]['child'];
        if(rtype1 != undefined)
        {
            html = ' <select class="selectList w222"  name="rtype1" id="rtype1">';
            html += '<option value="">全部</option>';
            if(rtype1['default'] == undefined)
            {
                for (var key in rtype1) 
                {
                   html += '<option value="'+key+'">'+rtype1[key][0]+'</option>'; 
                }
            }
            html +='</select>';
            $("#rtype").after(html);
        }
    });
    $("#rtype").change();
    if($("#rtype1").length > 0 && rtype_1 != '')
    {
        $("#rtype1").val(rtype_1);
    }
	//批量提款成功操作
    $("#batchCheck").click(function(){
        var s = '';
        s = getCheckVal("ckbox[]");
        if(!s){
            return false;
        }

        var title = '批量提款成功';
        var ids_arr = s.split(",");
        var count = ids_arr.length;
        var total = 0;
        for(var i =0; i < count; i++){
            var tmp = $("#"+ids_arr[i]).attr("data-money");
            total = Number(total) + Number(tmp);
        }

        $.ajax({
            type: "post",
            url: '/backend/Transactions/get_m_format',
            data: {'money': total},
            success: function (data) {
                var json = jQuery.parseJSON(data);
                if(json.status =='y'){
                    html = '<tr><td colspan=2></td></tr>';
                    html += "<tr><th>提款成功订单数：</th><td>"+count+"</td></tr>";
                    html += "<tr><th>提款成功总金额：</th><td>"+json.info["money"]+" 元</td></tr>";
                    $("#tbody1").html(html);
                    $("#pop_name1").html(title);
                    $("#hid_order_ids").val(s);
                    $("#b_hid_status").val(6);
                    popdialog("J-dc-batchCheck");
                }else{
                    alert((json.message+json.info.join(",")));
                }
            }
        });
        
        return false;
    });

    $("#batchCheck2").click(function(){
        var s = '';
        s = getCheckVal("ckbox[]");
        if(!s){
            return false;
        }

        var title = '批量提款失败';
        var ids_arr = s.split(",");
        var count = ids_arr.length;
        var total = 0;
        for(var i =0; i < count; i++){
            var tmp = $("#"+ids_arr[i]).attr("data-money");
            total = Number(total) + Number(tmp);
        }

        $.ajax({
            type: "post",
            url: '/backend/Transactions/get_m_format',
            data: {'money': total},
            success: function (data) {
                var json = jQuery.parseJSON(data);
                if(json.status =='y'){
                    html = '<tr><td colspan=2></td></tr>';
                    html += "<tr><th>提款失败订单数：</th><td>"+count+"</td></tr>";
                    $("#tbody1").html(html);
                    $("#pop_name1").html(title);
                    $("#hid_order_ids").val(s);
                    $("#b_hid_status").val(8);
                    popdialog("J-dc-batchCheck");
                }else{
                    alert((json.message+json.info.join(",")));
                }
            }
        });
        
        return false;
    });
    
    $("#submitForm1").click(function(){
        $.ajax({
            type: "post",
            url: '/backend/Transactions/batch_check',
            data: $("#batchCheckForm").serialize(),
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
    
    $(".dc-query").click(function(){
        var id = $(this).attr("data-id");
        var channel = $(this).attr("data-channel");
        if(channel=='lianlian'){
            var url='//888.166cai.cn/api/withdraw/lianlianQuery/'+id;
        }if(channel=='xianfeng'){
            var url='//888.166cai.cn/api/withdraw/xianfengQuery/'+id;
        }else{
            var url='//888.166cai.cn/api/withdraw/withdrawStatus/'+id;
        }
        $.ajax({
            type: "post",
            url: url,
            success: function (data) {
                var json = jQuery.parseJSON(data);
                alert(json.data);
            }
        });
        return false;
    });
    
    $(".dc-submit").click(function(){
        var id = $(this).attr("data-id");
        $.ajax({
            type: "post",
            url: '/backend/Transactions/submitBank/'+id,
            success: function (data) {
                var json = jQuery.parseJSON(data);
                alert(json.message);
                location.reload();
            }
        });
        return false;
    });
});
</script>
</body>
</html>