<?php
$this->load->view("templates/head");
$setStatus = array(
	'0' => '中奖后继续追号',
	'1' => '中奖后停止追号'
);
function bonusComputed($status)
{
	$isComputed = ($status >= 1000);

	return $isComputed;
}
?>
<div class="path" style="width:100%;">您的位置：运营管理&nbsp;>&nbsp;<a href="/backend/Management/chaseManage">追号管理</a>&nbsp;>&nbsp;详情
</div>
<div class="data-table-log mt20" style="width:100%;">
    <table>
        <colgroup>
            <col width="100"/>
            <col width="200"/>
            <col width="100"/>
            <col width="200"/>
            <col width="100"/>
            <col width="117"/>
            <col/>
        </colgroup>
        <tbody>
        <tr class="title">
            <th colspan="10">订单信息</th>
        </tr>
        <?php if ($manageOrder): ?>
            <tr>
                <th>追号订单编号：</th>
                <td><?php echo $manageOrder['chaseId']; ?></td>
                <th>购买方式：</th>
                <td>追号</td>
                <th>投注方式：</th>
                <td><?php if ($manageOrder['betTnum'] > 1): echo "复式投注";
                    else: echo "单式投注"; endif; ?></td>
                <th>用户名：</th>
                <td><?php echo $manageOrder['userName']; ?></td>
                <th>期次范围：</th>
                <td><?php echo $manageOrder['minIssue']; ?>-<?php echo $manageOrder['maxIssue']; ?></td>
            </tr>
            <tr>
                <th>彩种：</th>
                <td><?php echo $this->caipiao_cfg[$manageOrder['lid']]['name'] ?></td>
                <th>订单状态：</th>
                <td><?php echo $this->chase_manage_cfg[$manageOrder['status']];?></td>
                <th>创建时间：</th>
                <td><?php echo $manageOrder['created']; ?></td>
            </tr>
            <tr>
                <th>支付时间：</th>
                <td><?php echo $manageOrder['pay_time']; ?></td>
                <th>投注金额：</th>
                <td><?php echo m_format($manageOrder['money']); ?></td>
                <th>总奖金：</th>
                <td><?php echo m_format($manageOrder['margin']); ?></td>
            </tr>
            <tr>
                <th>追号设置：</th>
                <td><?php echo $setStatus[$manageOrder['setStatus']]; ?><?php if($manageOrder['setStatus'] && $manageOrder['setMoney'] > 0):?>(大于<?php echo m_format($manageOrder['setMoney']);?>元)<?php endif;?></td>
                <th>进度：</th>
                <td>已追<?php echo $manageOrder['chaseIssue']; ?>期/共<?php echo $manageOrder['totalIssue'];?>期 </td>
                <th>是否软删除：</th>
                <td><?php echo ($manageOrder['is_hide'] & 1) ? '是' : '否';?></td>
            </tr>
        <?php endif; ?>
        <tr class="hr">
            <td colspan="10">
                <div class="hr-dashed"></div>
            </td>
        </tr>
        <tr>
        	<th align="top">投注内容：</th>
            <td colspan="10">
            	<textarea class="textarea w830" rows="10" cols="30" id="" name=""><?php echo $manageOrder['codes'] ?></textarea>
            </td>
       	</tr>
       	<tr class="title">
            <th colspan="10">追号进度</th>
        </tr>
        </tbody>
    </table>
    <p class="data-table-filter" style="width: 1300px"><?php if ($manageOrder['hasstop']) {?><a class="btn-blue stopCheck" style="float: right;margin-bottom:5px">所选订单人工撤单</a><?php }?></p>
    <div class="data-table-list mt20">
            <table>
                <colgroup>
                	<col width="5%"/>
                    <col width="5%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="20%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col/>
                </colgroup>
                <tbody>
                <tr>
                	<th><?php if ($manageOrder['hasstop'] == 1) {?><input type="checkbox" class="stopIssue"><?php }?></th>
                    <th>序号</th>
                    <th>期次</th>
                    <th>方案金额</th>
                    <th>倍数</th>
                    <th>税前奖金</th>
                    <th>税后奖金</th>
                    <th>开奖号码</th>
                    <th>订单状态</th>
                    <th>详情</th>
                </tr>
                <?php if ($subOrders): ?>
                    <?php foreach ($subOrders as $subOrder): ?>
                        <tr>
                        	<td><?php if ($manageOrder['hasstop'] == 1 && $subOrder['bet_flag'] == 0 && $subOrder['status'] == 0) {?><input type="checkbox" class="stopIssue" value="<?php echo $subOrder['id']; ?>"><?php }?></td>
                            <td><?php echo $subOrder['sequence']; ?></td>
                            <td><?php echo $subOrder['issue']; ?></td>
                            <td><?php echo m_format($subOrder['money']); ?> 元</td>
                            <td><?php echo $subOrder['multi'];?></td>
                            <td><?php echo bonusComputed($subOrder['status']) ? (m_format($subOrder['bonus']) . '元') : '--'; ?></td>
                            <td><?php echo bonusComputed($subOrder['status']) ? (m_format($subOrder['margin']) . '元') : '--'; ?></td>
                            <td><?php if($awards[$subOrder['issue']]){ echo awardsFormat($manageOrder['lid'], $awards[$subOrder['issue']]); }else{ echo '预计开奖：' . $subOrder['award_time']; }?></td>
                            <td>
                            <?php 
                            	if ($subOrder['status'] == '2000')
                            	{ 
                            		echo $this->caipiao_ms_cfg['2000'][$subOrder['my_status']][0];
                            	}elseif ($manageOrder['status'] == '240' && $subOrder['status'] == '0'){
                            		echo '等待出票';
                            	}elseif ($manageOrder['status'] == '0' && $subOrder['status'] == '0'){
                            		echo '待付款';
                            	}else{
            						echo $this->caipiao_status_cfg[$subOrder['status']][0];
            					}
            				?>
            				</td>
                            <td><?php if($subOrder['orderId']):?><a target="_blank" class="cBlue" href="/backend/Management/orderDetail/?id=<?php echo $subOrder['orderId'];?>">查看</a><?php else:?>--<?php endif;?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
</div>
</body>
</html>
<script>
$(".stopIssue").click(function(){
	var index = $(".data-table-list .stopIssue").index(this);
	if (index == 0) {
	 	if ($(this).attr('checked') == 'checked') {
			$(".data-table-list .stopIssue").attr('checked', 'checked');
		}else {
			$(".data-table-list .stopIssue").removeAttr('checked');
		}
	}else {
		if ($(this).attr('checked') != 'checked') {
			$(".data-table-list .stopIssue:first").removeAttr('checked');
		}else if ($(".data-table-list .stopIssue[checked!='checked']").length == 1) {
			$(".data-table-list .stopIssue:first").attr('checked', 'checked');
		}
	}
})
$(".stopCheck").click(function(){
	var ids = [];
	$("tbody .stopIssue:checked").each(function(){
		ids.push($(this).val());
	})
	if (ids.length > 0) {
		if(confirm('是否要人工撤单？')) {
			$.ajax({
				type : 'post',
				data　: {chaseId:'<?php echo $manageOrder['chaseId']?>', ids:ids},
				url  : '/backend/Management/chaseCancelByUser',
				dataType : 'json',
				success: function(data){
					console.log(data);
					if (data.status == 'y') {
						location.reload();
					}else {
						alert(data.message);location.reload();
					}
					
				}
			});
		}
	}else {
	}
})
</script>