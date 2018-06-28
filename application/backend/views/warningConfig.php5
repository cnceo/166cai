<?php 
$this->load->view("templates/head");
$sType = array(
    '1' => '短信',
	'2' => '邮件',
	'4' => '短信、邮件',
); ?>
        <div class="path">您的位置：系统管理&nbsp;&gt;&nbsp;<a href="/backend/Warning/">报警配置</a></div>
        <div class="data-table-list table-tb-border del-percent mt10">
            <table>
                <colgroup>
                    <col width="50" />
                    <col width="168" />
                    <col width="100" />
                    <col width="300" />
                    <col width="70" />
                    <col width="172" />
                    <col width="50" />
                    <col width="50" />
                </colgroup>
                <tbody>

                    <tr>
                        <th>类型ID</th>
                        <th>类型名称</th>
                        <th>手机号</th>
                        <th>邮箱</th>
                        <th>通知类型</th>
                        <th>彩种</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                    <?php if (!empty($configs)): ?>
                        <?php foreach ($configs as $config): 
                        	$otherCondition = json_decode($config['otherCondition'], true)?>
                            <tr id="<?php echo $config['ctype'];?>">
                                <td><?php echo $config['ctype']; ?></td>
                                <td><?php echo $config['name']; ?></td>
                                <td><?php echo $config['phone']; ?></td>
                                <td><?php echo $config['email']; ?></td>
                                <td><?php echo $sType[$config['sendType']]; ?></td>
                                <?php if ($otherCondition['lid']) {?>
                                <td data-lid="<?php echo implode(',', $otherCondition['lid'])?>">
                                <?php $tmpArr = array(); 
									foreach ($otherCondition['lid'] as $lid) {
										array_push($tmpArr, $caipiao[$lid]['name']);
									} 
									echo implode(',', $tmpArr);?>
								</td>
								<?php 
    								unset($otherCondition['lid']);  
                                }elseif ($otherCondition['payType']) {?>
								<td data-paytype="<?php echo implode(',', $otherCondition['payType'])?>"></td>
								<?php unset($otherCondition['payType']);
                                }else {?>
								<td <?php 
								if (!empty($otherCondition)) {
								    foreach ($otherCondition as $name => $condition) {
								        echo ' data-'.$name.'='.json_encode($condition);
								    }
								}
								?>></td>
								<?php }?>
                                <td><?php echo $config['stop'] ? '停止' : '开启'; ?></td>
                                <td><a href="javascript:void(0);" class="cBlue mr10" onclick="edit(<?php echo $config['ctype']; ?>)">编辑</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    <div class="pop-mask" style="display:none"></div>
	<form id='updateForm' method='post' action=''>
    	<div class="pop-dialog" id="updatePop">
    		<div class="pop-in">
    			<div class="pop-head"><h2>报警配置修改</h2><span class="pop-close" title="关闭">关闭</span></div>
    			<div class="pop-body"><div class="data-table-filter del-percent"><table><colgroup><col width="68" /><col width="100" /><col width="250" /></colgroup><tbody id="tbody"></tbody></table></div></div>
    			<div class="pop-foot tac"><a href="javascript:;" class="btn-blue-h32 mlr15" id="updateSubmit">确定</a><a href="javascript:;" class="btn-b-white mlr15 pop-cancel">关闭</a></div>
    		</div>
    	</div>
    	<input type="hidden" value="" name="updateId"  id="updateId"/>
	</form>
	<style>
	   .solidtr {
	       border:solid 0.5px;
	       text-align:center !important;
	   }
	</style>
	<script  src="/source/date/WdatePicker.js"></script>
	<script type="text/javascript">
	var gaopinlidArr = $.parseJSON('<?php echo json_encode($lidMap)?>'), payTypeName = eval(<?php echo json_encode($payTypeName)?>);
	$('#updateForm').on('focus', '.Wdate1', function(){
        dataPicker({dateFmt:'HH:mm',minDate:'00:00:00'});
    }); 
	$('#updateForm').on('click', '.ck_lid :checkbox', function(){
		var lid = parseInt($(this).val(), 10);
		if ($(this).attr('checked')) {
			if ($('#updateForm').find('#tbody .time_'+lid).length == 0) 
				$('#updateForm').find('#tbody').append('<tr class="time_'+lid+'"><th>'+gaopinlidArr[lid]+'不报警时间：</th>\
					<td colspan="2"><span class="ipt ipt-date w84"><input class="Wdate1" name="'+lid+'[stime]"><i></i></span>分&nbsp;至&nbsp;<span class="ipt ipt-date w84"><input class="Wdate1" name="'+lid+'[etime]"><i></i></span>分</td></tr>')
		}else {
			$('#updateForm').find('#tbody .time_'+lid).remove();
		}
    }); 
        //预览和编辑
        function edit(id)
        {
        	var td = $("#"+id).children("td").siblings("td");
        	var stype1 = stype2 = stype4 = '';
        	if(td.eq(4).html() == '短信'){
            	stype1 = ' selected';
            }else if(td.eq(4).html() == '邮件'){
            	stype2 = ' selected';
            }else if(td.eq(4).html() == '短信、邮件'){
                stype4 = ' selected';
            }
        	var selected0 = selected1 = '';
    		if(td.eq(6).html() == '开启')
    		{
    			selected0 = ' selected';
    		}
    		else
    		{
    			selected1 = ' selected';
    		}
    		var id = td.eq(0).html(), 
    		html = '<tr><th>类型ID：</th><td colspan="2">'+id+'</td></tr><tr><th>类型名称：</th><td colspan="2">'+td.eq(1).html()+'</td></tr>\
        				<tr><th>手机号：</th><td colspan="2"><input type="text" name="phone" class="ipt w222" value="'+td.eq(2).html()+'">(多个用,隔开)</td></tr>\
        				<tr><th>邮箱：</th><td colspan="2"><input type="text" name="email" class="ipt w222" value="'+td.eq(3).html()+'">(多个用,隔开)</td></tr>\
        				<tr><th>通知方式：</th><td colspan="2"><select class="selectList w222" name="sendType"><option value="4" '+stype4+'>短信、邮件</option><option value="1" '+stype1+'>短信</option><option value="2" '+stype2+'>邮件</option></select></td></tr>\
        				<tr><th>状态：</th><td colspan="2"><select class="selectList w222" name="stop"><option value="0" '+selected0+'>开启</option><option value="1" '+selected1+'>停止</option></select></td></tr>';
        	switch(id) {
        		case '9':
        			var stoptime = {};
        			if (td.eq(5).data('stoptime')) stoptime = td.eq(5).data('stoptime');
        			notickettime = td.eq(5).data('notickettime');
        			if (!notickettime) notickettime = [180, 180, 180, 120, 120, 120, 90, 90];
                    html += '<tr><th>报警配置:</th><th class="solidtr">订单创建时间</th><th class="solidtr">未出票报警时间</th></tr>\
                        <tr><td></td><td class="solidtr">预售</td><td class="solidtr"><input class="ipt w60" name="notickettime[0]" value="'+notickettime[0]+'"> 秒</td></tr>\
                    	<tr><td></td><td class="solidtr">0-1min</td><td class="solidtr"><input class="ipt w60" name="notickettime[1]" value="'+notickettime[1]+'"> 秒</td></tr>\
                    	<tr><td></td><td class="solidtr">1-2min</td><td class="solidtr"><input class="ipt w60" name="notickettime[2]" value="'+notickettime[2]+'"> 秒</td></tr>\
                    	<tr><td></td><td class="solidtr">2-3min</td><td class="solidtr"><input class="ipt w60" name="notickettime[3]" value="'+notickettime[3]+'"> 秒</td></tr>\
                    	<tr><td></td><td class="solidtr">3-4min</td><td class="solidtr"><input class="ipt w60" name="notickettime[4]" value="'+notickettime[4]+'"> 秒</td></tr>\
                    	<tr><td></td><td class="solidtr">4-5min</td><td class="solidtr"><input class="ipt w60" name="notickettime[5]" value="'+notickettime[5]+'"> 秒</td></tr>\
                    	<tr><td></td><td class="solidtr">5-6min</td><td class="solidtr"><input class="ipt w60" name="notickettime[6]" value="'+notickettime[6]+'"> 秒</td></tr>\
                    	<tr><td></td><td class="solidtr">6-show_endtime</td><td class="solidtr"><input class="ipt w60" name="notickettime[7]" value="'+notickettime[7]+'"> 秒</td></tr>';
                    html += '<tr><th>不报警彩种：</th><td  colspan="2" class="ck_lid">';
                	$.each(gaopinlidArr, function(lid, val){
                    	if (lid == 21406) html += '<br>';
                        html += '<input type="checkbox" id="ck_lid_'+lid+'"'+(lid in stoptime ? 'checked' : '')+' value="'+lid+'"><label for="ck_lid_'+lid+'">'+val+'</label>';
                    })
                    html += '</td></tr>';
                    $.each(stoptime, function(lid, val){
                        html += '<tr class="time_'+lid+'"><th>'+gaopinlidArr[lid]+'不报警时间：</th>\
							<td colspan="2"><span class="ipt ipt-date w84"><input class="Wdate1" name="'+lid+'[stime]" value="'+val[0]+'"><i></i></span>分&nbsp;至&nbsp;<span class="ipt ipt-date w84"><input class="Wdate1" name="'+lid+'[etime]" value="'+val[1]+'"><i></i></span>分</td></tr>';
                    })
        			break;
        		case '10':
        			var lidArr = [];
                	if (td.eq(5).data('lid')) lidArr = td.eq(5).data('lid').toString().split(',');
                	html += '<tr><th>彩种：</th><td colspan="2">';
                	$.each(gaopinlidArr, function(lid, val){
                    	if (lid == 21406) html += '<br>';
                        html += '<input type="checkbox" id="ck_lid_'+lid+'" name="lid[]" '+($.inArray(lid, lidArr) > -1 ? 'checked' : '')+' value="'+lid+'"><label for="ck_lid_'+lid+'">'+val+'</label>';
                    })
                    html += '</td></tr>';
        			break;
        		case '19':
        			payTypeArr = [];
        			if (td.eq(5).data('paytype')) payTypeArr = td.eq(5).data('paytype').toString().split(',');
        			html += '<tr><th>支付方式：</th><td colspan="2">';
        			var i = 0;
                	$.each(payTypeName, function(eName, cName){
                    	if (i % 4 == 0) html += '<br>';
                        html += '<input type="checkbox" id="ck_payType_'+eName+'" name="payType[]" '+($.inArray(eName, payTypeArr) > -1 ? 'checked' : '')+' value="'+eName+'"><label for="ck_payType_'+eName+'">'+cName+'</label>';
                        i++;
                    })
                    html += '</td></tr>';
            		break;
    			default:
        			break;
        	}
    		$("#tbody").html(html);
    		$("#updateId").val(id);
    		popdialog("updatePop");
        	return false ;
        }
        $(function(){
        	$("#updateSubmit").click(function(){
        		$.ajax({
                    type: "post",
                    url: '/backend/Warning/update',
                    data: $("#updateForm").serialize(),
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
        });
	</script>
    </body>
</html>