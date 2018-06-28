<?php
    if ($fromType != 'ajax'): $this->load->view("templates/head")
?>
    <div class="path">您的位置：运营管理&nbsp;>&nbsp;<a href="/backend/Management/manageOrder">订单管理</a></div>
<?php endif; ?>
<?php  $platforms = array(
    '0' => '网页',
    '1' => 'Android',
    '2' => 'IOS',
    '3' => 'M版'
);
$orderType = array(
  '全部' => ' ',
  '自购' => '0',
  '追号' => '1',
  '赔付' => '3',
  '合买' => '4'  
);
?>
<div class="data-table-filter mt10" style="width:960px">
  <form action="/backend/Management/manageOrder" method="get"  id="search_form_order">
  <table>
    <colgroup>
      <col width="62" />
      <col width="232" />
      <col width="62" />
      <col width="400" />
      <col width="62" />
      <col width="100" />
    </colgroup>
    <tbody>
    <tr>
      <th>关键字：</th>
      <td>
          <input type="text" class="ipt w222"  name="name" value="<?php echo $search['name'] ?>"  placeholder="用户名/订单" />
      </td>
      <th>彩种玩法：</th>
      <td>
         <select class="selectList w120 mr20"  name="lid" id="caipiao_play">
            <option value="">全部</option>
            <?php foreach ($this->caipiao_cfg as $key => $cp): ?>
            <option value="<?php echo $key; ?>" <?php if ($search['lid'] === "{$key}"): echo "selected"; endif; ?>><?php echo $cp['name'] ?></option>
            <?php endforeach; ?>
         </select>
      </td>
      <th class = "tar">投注平台：</th>
      <td>
        <select class="selectList w98" id="platformId" name="buyPlatform">
            <option value="">不限</option>
            <?php foreach ($platforms as $key => $val):?>
            <option value="<?php echo $key;?>"
            <?php if((string)$key === ($search['buyPlatform']) ): echo "selected"; endif;?>><?php echo $val;?></option>
            <?php endforeach;?>
        </select>
      </td>
    </tr>
    <tr>
        <th>期次场次：</th>
        <td>
            <input type="text" class="ipt w222" name="issue" value='<?php echo $search['issue'] ?>' placeholder="期次场次..." />
        </td>
     <th>下单金额：</th>
      <td>
        <input type="text" class="ipt w120" name="start_money" value='<?php echo $search['start_money'] ?>'/>
        <span class="ml8 mr8">至</span>
        <input type="text" class="ipt w120" name="end_money" value='<?php echo $search['end_money'] ?>'/>
      </td>
      <th class="tar">注册渠道：</th>
      <td>
        <select class="selectList w130" name="channel">
      		<option value="">不限</option>
      		<?php foreach ($channels as $val):?>
      		<option value="<?php echo $val['id'];?>" <?php if($search['channel'] == $val['id']): echo "selected"; endif;?>><?php echo $val['name'];?></option>
      		<?php endforeach;?>
      	</select>
      </td>
    </tr>
    <tr>
      <th>订单状态：</th>
      <td>
        <select class="selectList w222 mr20"  name="status" id="status">
            <option value="">全部</option>
            <?php foreach ($this->caipiao_status_cfg as $key => $status): ?>
            <?php 
            	if(in_array($key, array('601', '602', '603','610','620')))
            		continue;
            ?>
    <option
        value="<?php echo $key ?>" <?php if ($search['status'] === "{$key}"): echo "selected"; endif; ?>><?php echo $status[0]; ?></option>
<?php endforeach; ?>
            <option value="success"  <?php if ($search['status'] === "success"): echo "selected"; endif; ?>>已出票</option>
        </select>
      </td>
      <th>下单时间：</th>
      <td>
          <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
          <span class="ml8 mr8">至</span>
          <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
      </td>
      <th class = "tar">购买方式：</th>
      <td>
        <select class="selectList w98" id="orderType" name="orderType">
            <?php foreach ($orderType as $key => $val):?>
            <option value="<?php echo $val;?>"
            <?php if($val === $search['orderType'] ): echo "selected"; endif;?>><?php echo $key;?></option>
            <?php endforeach;?>
        </select>
      </td>
    </tr>
    <tr>
      <th>出票子订单：</th>
      <td><input type="text" class="ipt w222"  name="sub_order_id" value="<?php echo $search['sub_order_id'] ?>"  placeholder="出票子订单" /></td>
      <th>出票商：</th>
      <td>
      	<select name="seller">
      		<option value="0">全部</option>
      		<?php foreach ($seller as $sl => $sel) {?>
      			<option <?php if ($search['seller'] == $sl){echo 'selected';}?> value="<?php echo $sl?>"><?php echo $sel?></option>
      		<?php }?>
      	</select>
      </td>
      <th class="tar">注册方式：</th>
      <td>
          <select class="selectList w130" name="reg_type">
              <option value="0" <?php if(empty($search['reg_type']) || $search['reg_type'] == '0'){ echo "selected"; }?>>不限</option>
              <option value="1" <?php if($search['reg_type'] == '1'){ echo "selected"; }?>>账号密码</option>
              <option value="3" <?php if($search['reg_type'] == '3'){ echo "selected"; }?>>微信</option>
              <option value="4" <?php if($search['reg_type'] == '4'){ echo "selected"; }?>>短信验证码</option>
          </select>
      </td>
      <td >
          <a id="searchOrder" href="javascript:void(0);" class="btn-blue mr20" onclick="">查询</a>
      </td>
    </tr>
    </tbody>
  </table>
      <input type="hidden" name="fromType" value="<?php echo $fromType ?>"  id="fromType" />
      <?php if ($fromType == 'ajax'): ?><input type="hidden" name="uid"
                                               value="<?php echo $search['uid'] ?>"/><?php endif; ?>
  </form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
      <col width="135" />
      <col width="128" />
      <col width="100" />
      <col width="70" />
      <col width="80" />
      <col width="130" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
      <col width="60" />
      <col width="60" />
      <col width="60" />
      <col width="60" />
<!--       <col width="70" /> -->
    </colgroup>
    <thead>
        <tr>
            <td colspan="13">
                <div class="tal">
                    <strong>订单总额</strong>
                    <span><?php echo m_format($count['money']); ?> 元</span>
                    <strong class="ml20">出票总额</strong>
                    <span><?php echo ($search['status'] === 'success' || $search['status'] === false || $search['status'] === '' || !in_array($search['status'], array(0, 10, 20, 21, 30, 40, 200, 240)))?m_format($count['cpmoney']):m_format(0); ?> 元</span>
                    <strong class="ml20">中奖总额(税前)</strong>
                    <span><?php echo m_format($count['bonus']); ?> 元</span>
                    <strong class="ml20">中奖总额(税后)</strong>
                    <span><?php echo m_format($count['margin']); ?> 元</span>
                    <strong class="ml20">用户统计</strong>
                    <span><?php echo $count['ucount']; ?> 人</span>
                </div>
            </td>
        </tr>
    </thead>
    <tbody>
    <tr>
      <th>订单编号</th>
      <th>用户名</th>
      <th>彩种</th>
      <th>玩法</th>
      <th>期次场次</th>
      <th>创建时间</th>
      <th>订单金额（元）</th>
      <th>中奖金额（税后）</th>
      <th>订单状态</th>
      <th>详情</th>
      <th>投注平台</th>
      <th>注册渠道</th>
      <th>注册方式</th>
<!--       <th>出票商</th> -->
    </tr>
    <?php foreach ($orders as $key => $order): ?>
    <tr>
        <td><?php echo $order['orderId'] ?></td>
        <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $order['uid'] ?>"
               class="cBlue"><?php echo $order['userName'] ?></a></td>
        <td><?php echo $this->caipiao_cfg[$order['lid']]['name'] ?></td>
        <td><?php if ( ! empty($this->caipiao_cfg[$order['lid']]['play'])): echo print_playtype($order['lid'], $order['playType'], $this->caipiao_cfg[$order['lid']]['play']);
            else: echo "--";  endif; ?></td>
        <td><?php echo $order['issue'] ?></td>
        <td><?php echo $order['created'] ?></td>
        <td><?php echo m_format($order['money']) ?></td>
        <td><?php echo m_format($order['margin']) ?></td>
        <td><?php if ($order['status'] == '2000'): echo $this->caipiao_ms_cfg['2000'][$order['my_status']][0];
            else: echo $this->caipiao_status_cfg[$order['status']][0]; endif; ?></td>
        <td><?php if($order['orderType']!=4){ ?><a href="/backend/Management/orderDetail/?id=<?php echo $order['orderId']; ?>" class="cBlue" target="_blank">查看</a><?php }else{ ?><a href="/backend/Management/unitedOrderDetail/?id=<?php echo $order['orderId']; ?>" class="cBlue" target="_blank">查看</a><?php } ?></td>
        <td><?php echo ($order['buyPlatform'] == 0) ? "网页" : ($order['buyPlatform'] == 1 ? "Android" : ($order['buyPlatform'] == 2 ? "IOS" : "M版")); ?></td>
        <td><?php echo $channels[$order['channel']]['name'];?></td>
        <td><?php echo ($order['reg_type'] <= 2) ? '账号密码' : ($order['reg_type'] == 3 ? '微信' : '短信验证码'); ?></td>
    </tr>
<?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="11">
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
<div class="pop-dialog" id="alertPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-filter del-percent" id="alertBody" style="text-align: center">
			</div>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">确认</a>
		</div>
	</div>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    var caipiao_cfg =jQuery.parseJSON('<?php echo json_encode($this->caipiao_cfg) ?>');
    var mystatus_cfg = jQuery.parseJSON('<?php echo json_encode($this->caipiao_ms_cfg) ?>');
    var play_type = '<?php echo $search['playType'] ?>';
    var s_my_status = '<?php echo $search['my_status'] ?>';
    $(function(){
        $("#caipiao_play").bind('change', function(){
            if($("#play_type").length > 0  || $(this).val()==0)
            {
                $("#play_type").remove();
                 if($(this).val()==0)
                    return ;
            }
            play = caipiao_cfg[$(this).val()]['play'];
            if(play != undefined)
            {
                html = ' <select class="selectList w120"  name="playType" id="play_type">';
                html += '<option value="">全部</option>';
                for (var key in play) 
                {
                   html += '<option value="'+key+'">'+play[key]['name']+'</option>'; 
                }
                html +='</select>';
                $("#caipiao_play").after(html);
            }
        });
        
        $("#searchOrder").click(function(){
    		var start = $("input[name='start_time']").val();
    		var end = $("input[name='end_time']").val();
    		if(start > end){
    			alertPop('您选择的时间段错误，请核对后操作');
    			return false;
    		}
    		 if($("#fromType").val() == "ajax")
             {
                 $("#order_info").load("/backend/Management/manageOrder?"+$("#search_form_order").serialize()+"&fromType=ajax");
                 return false;
             }
    		$('#search_form_order').submit();
    	});
    	
       $(".Wdate1").focus(function(){
            dataPicker();
        });   
        
        $('#search_form_order').submit(function(){
            if($("#fromType").val() == "ajax")
            {
                $("#order_info").load("/backend/Management/manageOrder?"+$("#search_form_order").serialize()+"&fromType=ajax");
                return false;
            }
            return true;
        });
        $('.order_info a').click(function(){
            if($("#fromType").val() == "ajax")
            {
                var _this = $(this);
                $("#order_info").load(_this.attr("href"));
                return false;
            }
            return true;
        });
        
        $("#status").bind("change",function(){
            if($(this).val() == '2000')
            {
                mstatus = mystatus_cfg[$(this).val()];
                if(mstatus != undefined)
                {
                    html = ' <select class="selectList w120"  name="my_status" id="my_stauts">';
                    html += '<option value="">全部</option>';
                    for (var key in mstatus) 
                    {
                        html += '<option value="'+key+'">'+mstatus[key][0]+'</option>'; 
                    }
                    html +='</select>';
                    $("#status").after(html);
                }
            }
            else
            {
                if($("#my_stauts").length > 0)
                {
                        $("#my_stauts").remove();
                }
            }
        });
        
        $("#caipiao_play").change();
        if($("#play_type").length > 0 && play_type != '')
        {
            $("#play_type").val(play_type);
        }
        $("#status").change();
        if($("#my_stauts").length > 0)
        {
            $("#my_stauts").val(s_my_status);
        }
    });
	function alertPop(content){
		$("#alertBody").html(content);
		popdialog("alertPop");
	}
    
</script>
<?php if ($fromType != 'ajax'): ?>
</body>
</html>
<?php endif; ?>