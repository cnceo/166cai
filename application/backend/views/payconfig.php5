<?php $this->load->view("templates/head") ?>
<?php
$payName = array(
			13 => '京东H5',
            18 => '兴业银行',
            19 => '鸿粤浦发银行',
            21 => '厦门国际银行支付宝',
            22 => '鸿粤兴业银行H5',
            24 => '平安银行支付宝',
            23 => '浦发白名单微信H5',
            28 => '盈中平安银行支付宝',
            29 => '番茄支付支付宝h5',
            31 => '微信H5-上海银行',
            34 => '支付宝H5-上海银行',
            33 => '盈中平安银行微信h5',
            32 => '京东SDK',
            35 => '微信扫码-长沙中信银行渠道',
            36 => '支付宝扫码-长沙中信银行渠道',
            37 => '番茄支付微信h5',
          );
?>
<div class="path">您的位置：<a href="">运营管理</a>&nbsp;&gt;&nbsp;<a href="">支付渠道管理</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd">
	    <ul>
	    <?php foreach ($platFormArr as $pid => $pname) {?>
	    <li <?php if ($pid == $platform) {?> class="current" <?php }?>><a href="/backend/Management/payconfig/<?php echo $pid?>"><?php echo $pname?></a></li>
	    <?php }?>
	    <li><a href="/backend/Management/payAddList">商户号管理</a></li>
	    </ul>
  	</div>
	<div class="mod-tab-bd">
		<div class="data-table-filter mt10">
			<form action="/backend/Management/payconfig/<?php echo $platform?>" method="get" id="search_form">
				<table>
					<colgroup>
				      <col width="432" />
				      <col width="100" />
				      <col width="100" />
                                      <col width="100" />
				    </colgroup>
					<tbody>
						<tr>
							<td>
					          <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
					          <span class="ml8 mr8">至</span>
					          <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
					      	</td>
					      	<td ><a id="serchsubmit" href="javascript:void(0);" class="btn-blue mr20" onclick="">查询</a></td>
					      	<td ><a id="saveStatus" href="javascript:void(0);" class="btn-blue mr20" onclick="">保存</a></td>
                                                <td ><a id="freshpayconfig" href="javascript:void(0);" style="background-color: <?php echo $freshpayconfig['fresh_payconfig']==1?'red':''?>;" class="btn-blue mr20" onclick=""><?php echo $freshpayconfig['fresh_payconfig']==0?'支付配置缓存更新开启中':'支付配置缓存更新关闭中'?></a>
                                                    
                                                </td>
                                                <td>
                                                    <font style="color:red">提示：支付配置缓存更新开启时，会将194<br>的配置更新到线上；关闭时，只对194生效</font>
                                                </td>
                                                </tr>
					</tbody>
				</table>
			</form>
		</div>
		<strong>微信、支付宝支付</strong>
		<div class="data-table-list mt20">
			<table>
                                <colgroup><col width="10%" /><col width="15%" /><col width="10%" /><col width="10%" /><?php if ($platform > 1) {?><col width="10%" /><?php } ?><col width="15%" /><col width="15%" /></colgroup>
                                <thead><tr><th>支付方式</th><th>支付渠道（商户号）</th><th>备注</th><th>当前比例</th><th>充值金额</th><?php if ($platform > 1) {?><th>排序优先级</th><?php } ?><th>渠道状态</th><th>操作</th></tr></thead>
				<tbody>
				<?php foreach ($ctypeArr as $cid => $ctype) {
				if (($cid != 1 && $cid!=7 )&& !empty($data[$cid])) {
				foreach ($data[$cid] as $key => $val) {
					// 移动端京东支付调整
					if($platform == 1 && $cid == 8){
						continue;
					}
				?>
				<tr>
					<?php if ($key == 0) {?><td rowspan = <?php echo count($data[$cid])?>><?php echo $ctype?></td><?php }?>
					<td><?php echo in_array($val['pay_type'], array_keys($payName)) ? $payName[$val['pay_type']]."--".$val['mer_id'] : ( ($val['ctype'] == 4 ? ($val['pay_type']==17 ? '微众银行' : '').'支付宝' : (in_array($val['pay_type'], array(5, 8)) ? '中信' : '全付通').($val['ctype'] == 2 ? 'SDK' : '扫码'))."—".$val['mer_id'] );?></td>
					<input type="hidden"  class='status_mark_id' value="<?php echo $val['id']?>">
					<td class='status_mark'>
						<div class="table-modify">
							<p class="table-modify-txt"><?php echo $val['status_mark']?>&nbsp;<i></i></p>
							<p class="table-modify-ipt">
							 <input type="text"   value="<?php echo $val['status_mark']?>" class="ipt status_mark" style='width: 134px;'>
							 <i></i>
						    </p>
						</div>
					</td>
					<td><?php echo $val['rate']?>%</td>
					<td><?php echo m_format($money[$val['id']])?></td>
					<?php if ($key == 0) {?>
                                        <?php if ($platform > 1) {?>
                                        <td rowspan = <?php echo count($data[$cid])?> data-ctype="<?php echo $cid?>">
                                            <div class="table-modify" data-paytype='<?php echo $val['pay_type']?>' data-id='<?php echo $val['id']?>'>
							<p class="table-modify-txt"><?php echo $val['weight'];?><i></i></p>
							<p class="table-modify-ipt"><input type="text" class="ipt weight" value="<?php echo $val['weight'];?>" style="width: 3em;"><i></i></p>
				            </div>
                                        </td>
                                        <?php } ?>
					<td rowspan = <?php echo count($data[$cid])?> data-ctype="<?php echo $cid?>" class="statusswitch">
						<input type="radio" name="status<?php echo $cid?>" value="0" id="status<?php echo $cid?>open" <?php if ($data[$cid][0]['status'] == 0) {?>checked<?php }?>><label for="status<?php echo $cid?>open">正常开启</label>&nbsp;&nbsp;
						<input type="radio" name="status<?php echo $cid?>" value="1" id="status<?php echo $cid?>close" <?php if ($data[$cid][0]['status'] == 1) {?>checked<?php }?>><label for="status<?php echo $cid?>close">维护关闭</label>
					</td>
					<td rowspan = <?php echo count($data[$cid])?> data-ctype="<?php echo $cid?>" data-cname="<?php echo $ctype?>">
						<a href="javascript:;" class="cBlue tzbl">调整比例</a>
					</td>
					<?php }?>
				</tr>
				<?php }
					}
				}?>
				</tbody>
			</table>
		</div>
		<strong>快捷支付</strong>
		<div class="data-table-list mt20">
			<table>
				<colgroup><col width="20%" /><col width="20%" /><col width="20%" /><col width="20%" /></colgroup>
				<thead><tr><th>支付方式</th><th>充值金额</th><th>排序优先级</th><th>操作</th></tr></thead>
				<tbody>
				<?php if (!empty($data[1])) {
				foreach ($data[1] as $key => $val) {?>
				<tr>
					<td><?php echo $paytypeArr[$val['pay_type']]?></td>
					<td><?php echo m_format($money[$val['id']])?></td>
					<td>
						<div class="table-modify" data-paytype='<?php echo $val['pay_type']?>' data-id='<?php echo $val['id']?>'>
							<p class="table-modify-txt"><?php echo $val['weight'];?><i></i></p>
							<p class="table-modify-ipt"><input type="text" class="ipt weight" value="<?php echo $val['weight'];?>" style="width: 3em;"><i></i></p>
						</div>
                    </td>
					<td class="statusswitch1" data-paytype='<?php echo $val['pay_type']?>' data-id='<?php echo $val['id']?>'>
						<input type="radio" name="status1<?php echo $key?>" value="0" id="status1<?php echo $key?>open" <?php if ($val['status'] == 0) {?>checked<?php }?>><label for="status1<?php echo $key?>open">正常开启</label>&nbsp;&nbsp;
						<input type="radio" name="status1<?php echo $key?>" value="1" id="status1<?php echo $key?>close" <?php if ($val['status'] == 1) {?>checked<?php }?>><label for="status1<?php echo $key?>close">维护关闭</label>
					</td>
				</tr>
				<?php }
				}?>
				</tbody>
			</table>
		</div>
                <strong>银联云闪付</strong>
		<div class="data-table-list mt20">
			<table>
				<colgroup><col width="20%" /><col width="20%" /><col width="20%" /><col width="20%" /></colgroup>
				<thead><tr><th>支付方式</th><th>充值金额</th><th>排序优先级</th><th>操作</th></tr></thead>
				<tbody>
				<?php if (!empty($data[7])) {
				foreach ($data[7] as $key => $val) {?>
				<tr>
					<td><?php echo $paytypeArr[$val['pay_type']]?></td>
					<td><?php echo m_format($money[$val['id']])?></td>
					<td>
						<div class="table-modify" data-paytype='<?php echo $val['pay_type']?>' data-id='<?php echo $val['id']?>'>
							<p class="table-modify-txt"><?php echo $val['weight'];?><i></i></p>
							<p class="table-modify-ipt"><input type="text" class="ipt weight" value="<?php echo $val['weight'];?>" style="width: 3em;"><i></i></p>
						</div>
                    </td>
					<td class="statusswitch2" data-paytype='<?php echo $val['pay_type']?>' data-id='<?php echo $val['id']?>'>
						<input type="radio" name="status2<?php echo $key?>" value="0" id="status2<?php echo $key?>open" <?php if ($val['status'] == 0) {?>checked<?php }?>><label for="status2<?php echo $key?>open">正常开启</label>&nbsp;&nbsp;
						<input type="radio" name="status2<?php echo $key?>" value="1" id="status2<?php echo $key?>close" <?php if ($val['status'] == 1) {?>checked<?php }?>><label for="status2<?php echo $key?>close">维护关闭</label>
					</td>
				</tr>
				<?php }
				}?>
				</tbody>
			</table>
		</div>
		<?php if($platform == 1): ?>
		<strong>京东支付</strong>
		<div class="data-table-list mt20">
			<table>
				<colgroup><col width="20%" /><col width="20%" /><col width="20%" /><col width="20%" /></colgroup>
				<thead><tr><th>支付方式</th><th>充值金额</th><th>排序优先级</th><th>操作</th></tr></thead>
				<tbody>
				<?php if (!empty($data[8])) {
				foreach ($data[8] as $key => $val) {?>
				<tr>
					<td><?php echo $paytypeArr[$val['pay_type']]?></td>
					<td><?php echo m_format($money[$val['id']])?></td>
					<td>
						<div class="table-modify" data-paytype='<?php echo $val['pay_type']?>' data-id='<?php echo $val['id']?>'>
							<p class="table-modify-txt"><?php echo $val['weight'];?><i></i></p>
							<p class="table-modify-ipt"><input type="text" class="ipt weight" value="<?php echo $val['weight'];?>" style="width: 3em;"><i></i></p>
						</div>
                    </td>
					<td class="statusswitch3" data-paytype='<?php echo $val['pay_type']?>' data-id='<?php echo $val['id']?>'>
						<input type="radio" name="status3<?php echo $key?>" value="0" id="status3<?php echo $key?>open" <?php if ($val['status'] == 0) {?>checked<?php }?>><label for="status3<?php echo $key?>open">正常开启</label>&nbsp;&nbsp;
						<input type="radio" name="status3<?php echo $key?>" value="1" id="status3<?php echo $key?>close" <?php if ($val['status'] == 1) {?>checked<?php }?>><label for="status3<?php echo $key?>close">维护关闭</label>
					</td>
				</tr>
				<?php }
				}?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>
		<?php if($platform!=4): ?> 
		<strong>卡前置——快捷支付</strong>
		<div class="data-table-list mt20">
			<table>
				<colgroup><col width="10%" /><col width="15%" /><col width="10%" /><col width="10%" /><col width="15%" /><col width="15%" /></colgroup>
				<thead><tr><th>支付方式</th><th>支付渠道</th><th>备注</th><th>当前比例</th><th>充值金额</th><th>渠道状态</th><th>操作</th></tr></thead>
				<tbody>
				<?php if (!empty($data[6])) {
				foreach ($data[6] as $key => $val) {?>
				<tr>
					<?php if ($key == 0) {?><td rowspan = <?php echo count($data[6])?>><?php echo $paytypeArr[$val['pay_type']]?></td><?php }?>
					<td>联动优势</td>
					<td><?php echo $val['mark']?></td>
					<td><?php echo $val['rate']?>%</td>
					<td><?php echo m_format($money[$val['id']])?></td>
					<?php if ($key == 0) {?>
					<td rowspan = <?php echo count($data[6])?> data-ctype="6" class="statusswitch">
						<input type="radio" name="status6" value="0" id="status6open" <?php if ($data[6][0]['status'] == 0) {?>checked<?php }?>><label for="status6open">正常开启</label>&nbsp;&nbsp;
						<input type="radio" name="status6" value="1" id="status6close" <?php if ($data[6][0]['status'] == 1) {?>checked<?php }?>><label for="status6close">维护关闭</label>
					</td>
					<td rowspan = <?php echo count($data[6])?> data-ctype="6" data-cname="联动支付">
						<a href="javascript:;" class="cBlue tzbl">调整比例</a>
					</td>
					<?php }?>
				</tr>
				<?php }
				}?>
				</tbody>
			</table>
		</div>
		<?php endif; ?>
                <?php if($platform ==1){ ?>
                <?php  $alltypes = array(
                                        '1'=>'快捷支付',
                                        '3'=>'微信支付',
                                        '4'=>'支付宝支付',
                                        '5'=>'网上银行',
                                        '6'=>'信用卡',
                                        '7'=>'银联云闪付',
                                        '8'=>'京东支付',
                        );?>
                <div class="data-table-list mt20">
                    <table>
                        <colgroup><col width="10%" /><col width="15%" /><col width="15%" /></colgroup>
                        <thead><tr><th>支付方式</th><th>排序优先级</th><th>引导文案(最多8个字)</th></tr></thead>
                        <tbody>
                            <?php foreach ($pcweight as $p){ ?>
                            <tr>
                                <td><?php echo $alltypes[$p['ctype']]; ?></td>
                                <td>
                                    <div class="table-modify" data-paytype="<?php echo $platform.'_'.$p['ctype']; ?>">
                                            <p class="table-modify-txt"><?php echo $p['pcweight'] ; ?><i></i></p>
                                            <p class="table-modify-ipt"><input type="text" class="ipt weight" value="<?php echo $p['pcweight'] ; ?>" style="width: 3em;"><i></i></p>
                                    </div>
                               </td>
                               <td>
                                   <div class="table-modify-guide" data-paytype="<?php echo $platform.'_'.$p['ctype']; ?>">
                                       <input type='text' value='<?php echo $p['guide'] ; ?>' class='ipt w184'>
                                   </div>
                               </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
                <?php } ?>
		<div class="tac"><a class="btn-blue mt20 submit weightsubmit">保存并上线</a></div>
	</div>
</div>
<div class="pop-dialog" id="tzblPop">
	<div class="pop-in">
		<div class="pop-head"><h2>调整充值方式比例</h2><span class="pop-close" title="关闭">关闭</span></div>
        <div class="pop-body">
			<div class="data-table-filter del-percent">
				<input type="hidden" id="modify_cid" value="">
                    <table>
                        <colgroup><col width="100"/><col width="100"/></colgroup>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:void(0)" class="btn-blue-h32" id="modifySubmit">确认</a>
                <a href="javascript:closePop();" class="btn-blue-h32">取消</a>
            </div>
	</div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<!--引入第三方插件 layer.js-->
<script src="/caipiaoimg/src/layer/layer.js"></script>
<script>
var payName = new Array();
	payName['13'] ='京东H5';
	payName['17'] = '微众银行支付宝';
	payName['19'] = '鸿粤浦发银行';
	payName['21'] = '厦门国际银行支付宝';
    payName['22'] = '鸿粤兴业银行H5';
    payName['24'] = '平安银行支付宝';
    payName['23'] ='浦发白名单微信H5';
    payName['28'] ='盈中平安银行支付宝';
    payName['29'] ='番茄支付支付宝h5';
    payName['31'] = '微信H5-上海银行';
    payName['34'] = '支付宝H5-上海银行';
    payName['33'] = '盈中平安银行微信h5';
    payName['32'] ='京东SDK';
    payName['35'] = '微信扫码-长沙中信银行渠道';
    payName['36'] = '支付宝扫码-长沙中信银行渠道';
    payName['37'] ='番茄支付微信h5';
$(function(){
	var data = $.parseJSON('<?php echo json_encode($data)?>'), paytypeArr = $.parseJSON('<?php echo json_encode($paytypeArr)?>');
	$(".Wdate1").focus(function(){
        dataPicker();
    });  
    $('#serchsubmit').click(function(){
        $('#search_form').submit();
    })
    function getcname(ctype, pay_type)
    {
        switch(ctype) {
        	case '4':
            	return payName[pay_type] ? payName[pay_type] :'支付宝' ;
            	break;
        	case '6':
            	return '联动支付';
            	break;
        	case '2':
                    if(pay_type==18){
                        return '微信H5-兴业银行';
                    }else if(pay_type==22){
                        return '鸿粤兴业银行H5';
                    }else if(pay_type==23){
                        return '浦发白名单微信H5';
                    }
                    else if(pay_type==28){
                        return '盈中平安银行支付宝';
                    }
                    else if(pay_type==28){
                        return '番茄支付支付宝h5';
                    }
                    else if(pay_type==31){
                        return '微信H5-上海银行';
                    }
                    else if(pay_type==33){
                        return '盈中平安银行微信h5';
                    }
                    else if(pay_type==35){
                        return '微信扫码-长沙中信银行渠道';
                    }
                    else if(pay_type==37){
                        return '番茄支付微信h5';
                    }
                    else{
                        return ($.inArray(pay_type, [5, 8]) > -1 ? '中信' : '全付通')+'SDK'
                    }
                    
        		//return pay_type == 18 ? '微信H5-兴业银行' : ($.inArray(pay_type, [5, 8]) > -1 ? '中信' : '全付通')+'SDK';
            	break;
            case '8':
            	return payName[pay_type] ? payName[pay_type] :'京东支付' ;
            	break;
        	default:
                    if(pay_type==35){
                        return '微信扫码-长沙中信银行渠道';
                    }else if(pay_type==36){
                        return '支付宝扫码-长沙中信银行渠道';
                    }
        		return ($.inArray(pay_type, [5, 8]) > -1 ? '中信' : '全付通')+'扫码';
            	break;
        }
    }
    $('.tzbl').click(function(){
    	var ctype = $(this).parents('td').data('ctype'), str = "<tr><th>充值方式:</th><td>"+$(this).parents('td').data('cname')+"</td></tr>";
    	$('#modify_cid').val(ctype);
        $.each(data[parseInt(ctype, 10)], function(i, e){
            str += "<tr data-paytype='"+e.mer_id+"' data-id='"+e.id+"'><th>"+getcname(e.ctype, e.pay_type)+"-"+e.mer_id+":</th><td><input type='text' value='"+e.rate+"%' class='ipt w98'></td></tr>";
        })
        $('#tzblPop tbody').html(str);
        popdialog("tzblPop");
    })
    $('#modifySubmit').click(function(){
        var rate = {}, sum = 0;
    	$('#tzblPop tbody').find('tr').each(function(i, e){
        	if (i > 0) {
        		var tmpval = parseInt($('#tzblPop tbody').find("tr:eq("+i+") input").val().match(/\d+\.*\d*/), 10);
        		sum += tmpval;
        		rate[$('#tzblPop tbody').find("tr:eq("+i+")").data('id')] = tmpval; 
        	}
       	})
       	if (sum !== 100) {
       		alert('比例总值必须等于100！');
       		closePop();
           	return;
       	}
       	var json = {platform:<?php echo $platform?>,ctype : $('#modify_cid').val(), rate : rate, env : '<?php echo ENVIRONMENT?>'};
       	$.ajax({
            type: "post",
            url: "/backend/Management/payconfigmodify",
            data: json,
            dataType: "json",
            success: function (resp) {
            	if (resp.status == 'n')  alert(resp.message);
                location.reload();
            }
        })
    })
    $("#freshpayconfig").click(function(){
        $.ajax({
            type: "post",
            url: "/backend/Management/freshpayconfig",
            data: {},
            dataType: "json",
            success: function (resp) {
            	if (resp.status == 'n')  alert(resp.message);
                location.reload();
            }
        })
    });
    $('.table-modify-txt').on('click', function(){
        $(this).hide().parents('.table-modify').find('.table-modify-ipt').show();
        var ipt = $(this).parents('.table-modify').find('.table-modify-ipt');
		var flages = ipt.find('input').attr('flages','0');
    });
    $('.weightsubmit').click(function(){
        var kuaijie = {}, other = {}, weight = {}, yinlian = {}, jd = {}, guide = {};
        $('.statusswitch, .statusswitch1, .statusswitch2, .statusswitch3').each(function(e){
            if ($(this).hasClass('statusswitch')) {
            	other[$(this).data('ctype')] = $(this).find('input:radio:checked').val();
            }else if ($(this).hasClass('statusswitch1')) {
            	kuaijie[$(this).data('paytype')] = $(this).find('input:radio:checked').val();
            }else if ($(this).hasClass('statusswitch2')) {
                yinlian[$(this).data('paytype')] = $(this).find('input:radio:checked').val();
            }else if ($(this).hasClass('statusswitch3')) {
                jd[$(this).data('paytype')] = $(this).find('input:radio:checked').val();
            }
        })
         $('.table-modify').each(function(){
            if ($(this).find('.table-modify-ipt').css('display') == 'block'){
                if($(this).find('.table-modify-ipt').parent().parent('td').data('ctype')){
                   var ctype = $(this).find('.table-modify-ipt').parent().parent('td').data('ctype');
                   if(ctype == 2 ){
                       weight['weixin'] = $(this).find('input').val();
                   }
                   if(ctype == 3 ){
                       weight['weixinsaoma'] = $(this).find('input').val();   
                   }
                   if(ctype == 4 ){
                       weight['zhifubao'] = $(this).find('input').val();
                   }
                   if(ctype == 8 ){
                       weight['jingdong'] = $(this).find('input').val();
                   }
                }else{
                   weight[$(this).data('paytype')] = $(this).find('input').val();
                }
            }
        })
        $('.table-modify-guide').each(function(){
            var paytype = $(this).data('paytype');
            var val = $(this).find('input').val();
            if(val.length>8){
                alert('支付方式的引导文案不得超过8个字！');
                return;
            }
            guide[paytype] = $(this).find('input').val();
        });
        $.ajax({
            type: "post",
            url: "/backend/Management/payconfigweight",
            data: {platform:<?php echo $platform?>,ctype:1, weight:weight,guide:guide, kuaijie:kuaijie, yinlian:yinlian,jd:jd,other:other, env : '<?php echo ENVIRONMENT?>'},
            dataType: "json",
            success: function (resp) {
            	if (resp.status == 'n') alert(resp.message);
                location.reload();
            }
        })
    });
    $('#saveStatus').on('click', function(){
        var inputStatusMarkId = $('input.status_mark_id');
        var inputStatusMark = $('input.status_mark');
        var status_arr = new Array();
        $('input.status_mark_id').each(function(index, ele){
        	if ($(this).parents('tr').find('.status_mark .table-modify-ipt').css('display') == 'block') status_arr[index] = [$(this).val(),$('input.status_mark').eq(index).val()];
        });
        layer.load(0, {shade: [0.5, '#393D49']});
        $.ajax({
            type: "post",
            url: '/backend/Management/payconfigMark',
            data: {data:status_arr, env : '<?php echo ENVIRONMENT?>'},
            dataType: "json",
            success: function (returnData) {
                if(returnData.code =='200')
                {
                    layer.alert('修改成功', {icon: 1,btn:'',title:'温馨提示',time:0,end:function(){location.reload();} });
                    location.reload();
                }else
                {
                    layer.alert(returnData.message, {icon: 2,btn:'',title:'温馨提示',time:0});
                }
            }
        });

    })

})
</script>