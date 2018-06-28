<?php $this->load->view("templates/head")?>
    <div class="path">您的位置：运营管理&nbsp;>&nbsp;<a href="/backend/Management/tictselrManage">出票商管理</a></div>
<div class="data-table-filter mt10" style="width:960px">
	<form action="/backend/Management/tictselrManage" id="form" method="get">
		<table>
			<colgroup>
		      <col width="62" />
		      <col width="400" />
		      <col width="62" />
		      <col width="62" />
		    </colgroup>
		    <tbody>
		    	<tr>
		    		<th>订单时间：</th>
				    <td>
			          <span class="ipt ipt-date w184"><input type="text" id="start_time" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
			          <span class="ml8 mr8">至</span>
			          <span class="ipt ipt-date w184"><input type="text" id="end_time" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
			      	</td>
			      	<td><a id="search" href="javascript:void(0);" class="btn-blue mr20" onclick="">查询</a></td>
			      	<td ><a id="saveMark" href="javascript:void(0);" class="btn-blue mr20" onclick="">保存备注</a></td>
		    	</tr>
		    </tbody>
		</table>
	</form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="80" />
      <col width="150" />
      <col width="130" />
      <?php foreach ($seller as $seler) {?>
      <col width="65" />
      <?php }?>
      <col width="60" />
    </colgroup>
    <tbody>
        <tr>
          <th></th>
          <th>备注</th>
          <th>当前比例(票商顺序)</th>
          <?php foreach ($seller as $seler) {?>
          <th><?php echo $seler;?></th>
          <?php }?>
          <th>操作</th>
        </tr>
        <?php foreach ($rate as $lid => $rt): ?>
        <tr data-lid="<?php echo $lid?>">
        	<td><?php echo $this->caipiao_cfg[$lid]['name']?></td>
        	<td>
                <div class="table-modify" data-lid='<?php echo $lid?>'>
    				<p class="table-modify-txt"><?php echo $rt['mark'];?>&nbsp;<i></i></p>
    				<p class="table-modify-ipt"><input type="text" class="ipt mark" style="width: 134px;" value="<?php echo $rt['mark'];?>" style="width: 3em;"><i></i></p>
    			</div>
            </td>
            <td>
            <?php 
            $i = 0;
            foreach ($seller as $sl => $seler) {
            	$i++;
            	echo ($rt[$sl]/100)."%"; 
            	if ($i < count($seller)) echo "&nbsp;&nbsp;";
            }?></td>
            <?php foreach ($seller as $sl => $seler) {?>
          		<td><?php echo m_format($datas[$lid][$sl]) ?></td>
          	<?php }?>
            <td><a class="cBlue modify" href="javascript:;">调整比例</a>&nbsp;<!-- <a class="cBlue down" href="javascript:;">下载详情</a> --></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
  </table>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<form id="modifyForm" method="post" action="/backend/Management/tictselrModify">
    <!-- 修改 start -->
    <div class="pop-dialog" id="modifyPop">
        <div class="pop-in">
            <div class="pop-head">
                <h2>调整出票商出票比例</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
                <div class="data-table-filter del-percent">
                    <input type="hidden" id="modify_lid" name="id" value="">
                    <table>
                        <colgroup>
                            <col width="68"/>
                            <col width="350"/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>彩种名称:</th>
                            <td id="modify_lottery"></td>
                        </tr>
                        <?php foreach ($seller as $sl => $seler) {?>
                        <tr>
                            <th><?php echo $seler?>出票比例</th>
                            <td data-sl="<?php echo $sl?>">
                                <input type="text" class="ipt w222">
                            </td>
                        </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:void(0)" class="btn-blue-h32" id="modifySubmit">确认</a>
                <a href="javascript:closePop();" class="btn-blue-h32">取消</a>
            </div>
        </div>
    </div>
    <!-- 修改 end -->
</form>
	<!-- 修改 start -->
	<div class="pop-dialog" id="downPop">
        <div class="pop-in">
            <div class="pop-head">
                <h2>子订单详情下载</h2>
                <span class="pop-close" title="关闭">关闭</span>
            </div>
            <div class="pop-body">
                <div class="data-table-filter del-percent">
                    <input type="hidden" id="lid" name="id" value="">
                    <table>
                        <colgroup>
                            <col width="68"/>
                            <col width="350"/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>彩种名称:</th>
                            <td id="lotteryName"></td>
                        </tr>
                        <tr>
                            <th>订单时间:</th>
                            <td id="downTime"><?php echo $search['start_time']."-".$search['end_time']?></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a class="btn-blue-h32" id="downUrl">确认</a>
                <a href="javascript:closePop();" class="btn-blue-h32">取消</a>
            </div>
        </div>
    </div>
    <!-- 修改 end -->
<script  src="/source/date/WdatePicker.js"></script>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script>
var rate = $.parseJSON('<?php echo json_encode($rate)?>');
var seller = $.parseJSON('<?php echo json_encode($seller)?>');
$("input[name='start_time']").focus(function(){
	 dataPicker({
    	minDate:'#F{$dp.$D(\'end_time\',{M:-1})&&\'2016-02-01 00:00:00\'}',
    	maxDate:'#F{$dp.$D(\'end_time\')&&\'%y-%M-%d %H:%m:%s\'}'
    });
})
$("input[name='end_time']").focus(function(){
	 dataPicker({
		 minDate:'#F{$dp.$D(\'start_time\')}',
		 maxDate:'#F{$dp.$D(\'start_time\',{M:+1})}',
    });
})
$("#search").click(function(){
	var start = $("input[name='start_time']").val();
	var end = $("input[name='end_time']").val();
	if(start > end){
		alert('您选择的时间段错误，请核对后操作');
		return false;
	}
	$("#form").submit();
})
$(".modify").click(function () {
	var lid = $(this).parents('tr').data('lid');
	$("#modify_lottery").html($(this).parents('tr').find('td:first').html());
	$("#modify_lid").val(lid);
	for (i in seller) {
		$("td[data-sl='"+i+"']").find("input").val(((i in rate[lid]) ? rate[lid][i] : 0)/100+"%");
	}
	popdialog("modifyPop");
});
$(".down").click(function () {
	var lid = $(this).parents('tr').data('lid');
	var lname = $(this).parents('tr').find('td:first').html();
	$("#lotteryName").html(lname);
	$("#lid").val(lid);
	for (i in seller) {
		$("td[data-sl='"+i+"']").find("input").val(rate[lid][i]/100+"%");
	}
	$("#downUrl").attr('href', "/backend/Management/downSplitdetail?lid="+lid+"&start=<?php echo $search['start_time']?>&end=<?php echo $search['end_time']?>");
	popdialog("downPop");
});
$("#modifySubmit").click(function () {
    var lid = $("#modify_lid").val(), json = {}, sum = '0';
    json.env = '<?php echo ENVIRONMENT?>';
    json.lid = lid;
    for (i in seller) {
    	match = $("td[data-sl='"+i+"']").find("input").val().match(/\d+\.*\d*/);
        sum = accAdd(sum, match[0]);
        json[i] = match[0];
    }
    if (sum != 100) {
        alert('比例总值必须等于100！');
        return false;
    }
    if ($.inArray(lid, ['55']) > -1 && (json.qihui != '100' || json.caidou != '0')) {
   	    alert('单一票商，比例调节失败');
   	    return false;
    }
	//caidou
// 	if ($.inArray(lid, ['53']) > -1 && (json.qihui != '0' || json.caidou != '100')) {
// 		alert('单一票商，比例调节失败');
// 		return false;
// 	}
    closePop();
    layer.load(0, {shade: [0.5, '#393D49']});
    $.ajax({
        type: "post",
        url: "/backend/Management/tictselrModify",
        data: json,
        dataType: "json",
        success: function (resp) { 
            layer.closeAll();
            if(resp.code == '0')
            {
                layer.alert(resp.message, {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();}});
            }else{
                layer.alert(resp.message, {icon: 2,btn:'',title:'温馨提示',time:0});
            } 
        }
    })
});
$('#saveMark').click(function() {
    var mark = {};
    $('input.mark').each(function(index, ele){
        var lid = $(this).parents('td').find('.table-modify').data('lid');
    	if ($(this).parents('td').find('.table-modify .table-modify-ipt').css('display') == 'block') mark[lid] = $(this).val();
    });
    layer.load(0, {shade: [0.5, '#393D49']});
    $.ajax({
        type: "post",
        url: '/backend/Management/tickselrMark',
        data: {mark, env : '<?php echo ENVIRONMENT?>'},
        dataType: "json",
        success: function (returnData) {
            if(returnData.code =='0')
            {
                layer.alert('修改成功', {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();} });
            }else
            {
                layer.alert(returnData.message, {icon: 2,btn:'',title:'温馨提示',time:0});
            }
        }
    });
})
function accAdd(arg1, arg2) {
	var arr1 = arg1.toString().trim().split("."), arr2 = arg2.toString().trim().split("."), z, x = 0;
	z = parseInt(arr1[0]) + parseInt(arr2[0]);
	if (arr1[1] ) {
		x += parseInt(arr1[1]);
	}
	if (arr2[1] ) {
		x += parseInt(arr2[1]);
	}
	return (z * 100 + x)/100;
}

$("#downUrl").click(function(){
	closePop();
})
$('.table-modify-txt').on('click', function(){
	console.log(1);
    $(this).hide().parents('.table-modify').find('.table-modify-ipt').show();
    var ipt = $(this).parents('.table-modify').find('.table-modify-ipt');
	var flages = ipt.find('input').attr('flages','0');
});
</script>