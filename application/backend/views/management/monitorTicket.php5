<?php 
	$this->load->view("templates/head");
	$status = array(
		'240' => '出票中',
		'0' => '等待出票',
	);
	$playTypes = array(
		'SPF' => '胜平负',
		'RQSPF' => '让球胜平负',
		'BQC' => '半全场',
		'JQS' => '总进球',
		'CBF' => '比分',
		'SF' => '胜负',
		'RFSF' => '让分胜负',
		'SFC' => '胜分差',
		'DXF' => '大小分',
	);
	$errNumMap = array(
		'' => '',
		'0' => '收单成功',
		'1' => '已经收单',
		'2' => '格式错误',
		'3' => '金额错误',
		'4' => '期次错误',
		'5' => '代理错误',
		'6' => '余额不足',
		'9000' => '数据库错误',
		'100001' => '错误(重试)',
		'100002' => '解码错误',
		'100003' => '无效参数',
		'100004' => '系统异常',
		'100005' => '无效参数',
		'200001' => '余额不足',
		'200002' => '格式错误',
		'200005' => '无效玩法',
		'200006' => '玩法未开期',
		'200007' => '玩法已封期',
		'200008' => '投注额度满',
		'200009' => '暂时限号',
		'200010' => '无此序列号',
		'200011' => '无效赛事编号',
		'200012' => '不能投注的单场赛事',
		'200013' => '赛事销售已停止',
		'200014' => '非销售时间',
		'200015' => '身份信息错误',
		'200016' => '限号',
		'200017' => '未接票(重提)',
		'200020' => '出票失败',
		'200021' => '还未出票',
		'200022' => '无此序列号',
		'200023' => '出票成功',
		'200024' => '投注失败',
		'5_0'    => '出票成功',
		'5_1'    => '限号撤单',
		'5_2'    => '销售已经截止',
		'0_0'    => '未出票',
		'1_0'    => '正在出',
		'2_0'    => '已处理，出票成功',
		'2_1'    => '已处理，限号撤单',
		'2_2'    => '已处理，销售已截止',
		'3_0'    => '票不存在',
		'4_0'    => '查询异常'
	);
    // 不可撤单错误码配置
    $noCancelError = array(
        '0' => '收单成功',
        '1' => '已经收单',
        '200021' => '还未出票',
        '200022' => '无此序列号',
        '200024' => '投注失败',
        '5_0'    => '出票成功',
        '5_1'    => '限号撤单',
        '5_2'    => '销售已经截止',
        '0_0'    => '未出票',
        '1_0'    => '正在出',
        '2_0'    => '已处理，出票成功',
        '2_1'    => '已处理，限号撤单',
        '2_2'    => '已处理，销售已截止',
        '3_0'    => '票不存在',
        '4_0'    => '查询异常',
        '0000'   => '善彩出票中',
        '6_0'    => '善彩出票中',
        '2002'   => '恒钜出票中',
    );
	$noCheck = array('', '0','1', '200021');
?>
<div class="frame-container" style="margin-left:0;">
    <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/monitorTicket">出票监控</a></div>
    <div class="mod-tab-hd mt20">
        <ul>
            <li><a href="/backend/Management/huizong">出票汇总</a></li>
			<li class="current"><a href="/backend/Management/monitorTicket">单彩种监控</a></li>
			<li><a href="/backend/Management/chaseCancel">追号监控</a></li>
			<li><a href="/backend/Management/ticketLimit">出票限制</a></li>
        </ul> 
    </div>
    <div class="mt10">
        <div class="data-table-filter" style=" width: 100%;">
            <form action="/backend/Management/monitorTicket" method="get" id="search_form">
                <table>
                    <colgroup>
                        <col width="50">
                        <col width="92">
                        <col width="62">
                        <col width="92">
                        <col width="62">
                        <col width="92">
                        <col width="62">
                        <col width="80">
                        <col width="80">
                        <col width="62">
                        <col width="160">
                        <col width="160">
                        <col width="80">
                        <col width="80">
                        <col width="160">
                        <col width="92">
                    </colgroup>
                    <tbody>
                    <tr>
                        <td>彩种：</td>
                        <td>
                            <select class="selectList w84" id="selectType" name="lid" onchange="$('#search_form').submit();">
                                <?php foreach ($monitorKind as $l => $types): ?>
                                    <option <?php if ($search['lid'] == $types['lid']): ?>selected<?php endif; ?> value="<?php echo $types['lid']; ?>"><?php echo $types['name']; ?></option>
                                <?php endforeach; ?>
                            </select> 
                        </td>
                        <td>出票状态：</td>
                        <td>
                            <select class="selectList w84" name="status" onchange="$('#search_form').submit();">
                            	<option value="">全部</option>
                                <?php foreach ($status as $key => $val): ?>
                                    <option <?php if ($search['status'] === "{$key}"): ?>selected<?php endif; ?> value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>赛事玩法：</td>
                        <td>
                            <select class="selectList w84" name="playType">
                            	<option value="">全部</option>
                                <?php foreach ($playTypes as $key => $playType): ?>
                                    <option <?php if ($search['playType'] == "{$key}"): ?>selected<?php endif; ?> value="<?php echo $key; ?>"><?php echo $playType; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>赛事编号：</td>
                        <td colspan="2"><input type="text" class="ipt w130" name="mid" value='<?php echo $search['mid'] ?>' /></td>
                        <td>期次：</td>
                        <td><input type="text" class="ipt w130" name="issue" value='<?php echo $search['issue'] ?>' /></td>
                        <td><input type="checkbox" name="errNum" id="errNum" <?php if($search['errNum']){ echo 'checked';}?>><label for="errNum">包含错误返回码</label></td>
                        <td colspan="2"><input type="checkbox" name="endTimeOrder" id="endTimeOrder" <?php if($search['endTimeOrder']){ echo 'checked';}?>><label for="endTimeOrder">按照end time升序排列</label></td>
                        <td><a id="batchCancel" href="javascript:void(0);" class="btn-blue ml20">所选订单人工撤单</a></td>
                        <td><a id="batchTicket" href="javascript:void(0);" class="btn-blue ml20">手动提票</a></td>
                    </tr>
                    <tr>
                        <td>订单号：</td>
                        <td colspan="3"><input type="text" class="ipt w222" name="orderId" value='<?php echo $search['orderId'] ?>' placeholder="大订单号/子订单号" />
                        <td>票商：</td>
                        <td>
                            <select class="selectList w84" name="ticket_seller">
                                <option value="">全部</option>
                                <?php foreach ($ticket_seller as $key => $seller): ?>
                                    <option <?php if ($search['ticket_seller'] == "{$seller}"): ?>selected<?php endif; ?> value="<?php echo $seller; ?>"><?php echo $seller; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td colspan="2"><input type="checkbox" name="havenot" id="havenot" <?php if($search['havenot']){ echo 'checked';}?>><label for="havenot">显示最近截止场次</label></td>
                        <td colspan="2"><input type="checkbox" name="ticketed" id="ticketed" <?php if($search['ticketed']){ echo 'checked';}?>><label for="ticketed">更换过票商</label></td>
                        <td><a id="search" href="javascript:void(0);" class="btn-blue">查询</a></td>
                        <td></td>
                        <td>每页显示：</td>
                        <td>
                            <select class="selectList w84" id="perPage" name="perPage">
                                <option value="20" <?php if ($search['perPage'] == "20"): ?>selected<?php endif; ?>>20条</option>
                                <option value="50" <?php if ($search['perPage'] == "50"): ?>selected<?php endif; ?>>50条</option>
                                <option value="100" <?php if ($search['perPage'] == "100"): ?>selected<?php endif; ?>>100条</option>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="data-table-list mt10">
            <table>
                <colgroup>
                	<col width="4%">
                    <col width="4%">
                    <col width="8%">
                    <col width="18%">
                    <col width="15%">
                    <col width="15%">
                    <col width="8%">
                    <col width="7%">
                    <col width="8%">
                    <col width="5%">
                    <col width="6%">
                    <col width="13%">
                    <col width="8%">
                </colgroup>
                <thead>
                <tr>
                	<th><input type="checkbox" class="_ck" /></th>
                    <th>序号</th>
                    <th>彩种</th>
                    <th>订单号</th>
                    <th>订单创建时间</th>
                    <th>end time</th>
                    <th>金额（元）</th>
                    <th>出票状态</th>
                    <th>订单详情</th>
                    <th>出票商</th>
                    <th>历史提票</th>
                    <th>返回码</th>
                    <th>操作</th>
                </tr>
                </thead>
                    <tbody>
                    <?php 
                    	$num = ($page - 1) * $pageNum;
                    ?>
                    <?php foreach ($orders as $key => $row): ?>
                        <tr>
                        	<td><input type="checkbox" class="ck_" name="cancelId" value="<?php echo $row['sub_order_id'];?>"></td>
                            <td><?php echo ++$num; ?></td>
                            <td><?php echo $this->caipiao_cfg[$row['lid']]['name'];?></td>
                            <td <?php if($row['status'] == '240'):?>class="cGreen"<?php endif;?>><?php echo $row['sub_order_id']; ?></td>
                            <td><?php echo $row['created']; ?></td>
                            <td><?php echo $row['endTime']; ?></td>
                            <td><?php echo m_format($row['money']); ?></td>
                            <td><?php echo $status[$row['status']];?></td>
                            <td><?php if($row['orderType']!=4){ ?>
                            <a class="cBlue" target="_blank" href="/backend/Management/orderDetail?id=<?php echo $row['orderId']; ?>&isOrderId=1">查看详情</a>
                                <?php }else{ ?>
                            <a class="cBlue" target="_blank" href="/backend/Management/unitedOrderDetail?id=<?php echo $row['orderId']; ?>&isOrderId=1">查看详情</a>
                                <?php } ?></td>
                            <td><?php echo $row['ticket_seller']; ?></td>
                            <td><?php
                            		$sellerStr = '';
                            		foreach ($ticket_seller as $key => $seller)
                            		{
                            			$sellerStr .= ($row['ticket_flag'] & $key) ? $seller. ',' : '';
                            		}
                            		echo preg_replace('/,$/', '', $sellerStr); 
                            	 ?>
                            </td>
                            <td><?php echo $errNumMap[$row['error_num']];?><?php if($row['error_num']){ echo '('.$row['error_num'].')';}?></td>
                            <td><a class="cBlue" href="javascript:void(0);" onclick="ticket('<?php echo $row['sub_order_id']; ?>')">手动提票</a><?php if(($row['error_num'] > 0 && !in_array($row['error_num'], array_keys($noCancelError))) || ($row['saleTime'] > date('Y-m-d H:i:s') && !in_array($row['lid'], array('42', '43'))) || empty($row['ticket_seller'])): ?>&nbsp;&nbsp;<a class="cBlue" href="javascript:void(0);" onclick="cancel('<?php echo $row['sub_order_id']; ?>')">撤单</a><?php endif; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
            </table>
        </div>
    </div>
    <div class="page mt10 order_info">
   		<?php echo $pages[0]; ?>
	</div>
</div>
<!-- 弹出层 -->
<div class="pop-mask" style="display:none;width:200%"></div>
<div class="pop-dialog" id="dialog-orderCancel" style="display:none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2></h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body" style="text-align:center;">
		是否要人工撤单？
		</div>
		<div class="pop-foot tac">
			<input type="hidden" name="selectId" id="selectId" value="" />
			<a href="javascript:orderCancel();" class="btn-blue-h32 mlr15">确认</a>
			<a href="javascript:closePop();" class="btn-b-white mlr15">取消</a><br/><br/>
		</div>
	</div>
</div>
<div class="pop-dialog" id="dialog-orderTicket" style="display:none;">
	<div class="pop-in">
		<div class="pop-head">
			<h2>提票-选择票商</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body" style="text-align:center;">
			<br/>
			<?php foreach ($ticket_seller as $sid => $sl) {?>
			    <a href="javascript:orderTicket(<?php echo ($sid == 8) ? 5 : $sid?>);"  class="<?php echo $sid == 1 ? 'btn-blue-h32' : 'btn-b-white'?>"><?php echo $sl?></a>
			<?php }?>
			<br/>
		</div>
		<div class="pop-foot tac">
			<input type="hidden" name="selectTicketId" id="selectTicketId" value="" />
		</div>
	</div>
</div>
<script>
    $(function(){
        $("#search").click(function(){
    		$('#search_form').submit();
    	}); 
        $("._ck").click(function(){
            var self = this;
            $(".ck_").each(function(){
                if(self.checked)
                {
                    $(this).attr("checked", true);
                }
                else
                {
                    $(this).attr("checked", false);
                }
            });
        });
    	$("#batchCancel").click(function(){
    		$("#selectId").val(''); //清空以前选项
    		ids = [];
    		$(".ck_").each(function(){
    			if(this.checked)
    			{
    				ids.push($(this).val());
    			}
    		})
    	    if(ids.length < 1)
    	    {
    	      alert('请先选择要撤销的订单');
    	      return false;
    	    }
    	    $("#selectId").val(ids);
    		popdialog("dialog-orderCancel");
    		return false;
        });

    	$("#batchTicket").click(function(){
    		$("#selectTicketId").val(''); //清空以前选项
    		ids = [];
    		$(".ck_").each(function(){
    			if(this.checked)
    			{
    				ids.push($(this).val());
    			}
    		})
    	    if(ids.length < 1)
    	    {
    	      alert('请先选择要手动提票的订单');
    	      return false;
    	    }
    	    $("#selectTicketId").val(ids);
    		popdialog("dialog-orderTicket");
    		return false;
        });
    });

    function cancel(orderId)
    {
    	$("#selectId").val(''); //清空以前选项
    	$("#selectId").val(orderId);
		popdialog("dialog-orderCancel");
		return false;
    }

    //切换票商
    function ticket(orderId)
    {
    	$("#selectTicketId").val(''); //清空以前选项
    	$("#selectTicketId").val(orderId);
		popdialog("dialog-orderTicket");
		return false;
    }
    function orderCancel()
	{
    	var orderIds = $("#selectId").val();
		$.ajax({
		    type: "post",
		    url: "/backend/Management/orderCancel/",
		    data: {'orderIds':orderIds},
		    success: function(data){
		    	var json = jQuery.parseJSON(data);
                alert(json.message);
                closePop();
                if(json.status =='y')
                {
                    location.reload();
                }
		    }
		})
	}
	//切换票商
    function orderTicket(ticket)
	{
    	var orderIds = $("#selectTicketId").val();
		$.ajax({
		    type: "post",
		    url: "/backend/Management/orderTicket/",
		    data: {'orderIds':orderIds, 'ticket': ticket},
		    success: function(data){
		    	var json = jQuery.parseJSON(data);
                alert(json.message);
                closePop();
                if(json.status =='y')
                {
                    location.reload();
                }
		    }
		})
	}
	$("#perPage").change(function(){
		$('#search_form').submit();
	})
</script>
</body>
</html>
