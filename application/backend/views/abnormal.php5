<div class="data-table-filter mt10" style="width:1034px">
  <form action="/backend/Order/abnormal_list" method="get"  id="search_form">
  <table>
    <colgroup>
      <col width="62" />
      <col width="120" />
      <col width="62" />
      <col width="222" />
      <col width="62" />
      <col width="120" />
      <col width="70" />
      <col width="120" />
      <col width="50" />
    </colgroup>
    <tbody>
    <tr>
		<th>订单用户：</th>
		<td><input type="text" class="ipt w120"  name="name" value="<?php echo $search['name'] ?>"  placeholder="用户名" /></td>
		<th>订单编号：</th>
		<td><input type="text" class="ipt w222"  name="orderId" value="<?php echo $search['orderId'] ?>"  placeholder="订单编号" /></td>
		<th>创建时间：</th>
		<td colspan="4">
	        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
	        <span class="ml8 mr8">至</span>
	        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
		</td>
    </tr>
	<tr>
		<th>订单状态：</th>
		<td>
			<select class="selectList w120"  name="status">
	            <option value="">全部</option>
	            <option value="600" <?php if($search['status'] === "600"): echo "selected"; endif;    ?>>出票失败</option> 
	            <option value="510" <?php if($search['status'] === "510"): echo "selected"; endif;    ?>>部分出票成功</option> 
	            <option value="21" <?php if($search['status'] === "21"): echo "selected"; endif;    ?>>付款请求失败</option>     
			</select>
		</td>
		<th>异常原因：</th>
		<td>
			<select class="selectList w222"  name="mark">
				<option value="">全部</option>
				<?php foreach($this->caipiao_ab_cfg as $key => $value): ?>
				<option value="<?php echo $key;?>" <?php if($search['mark'] === "{$key}"): echo "selected"; endif;?>><?php echo $value; ?></option> 
				<?php endforeach; ?>
			</select>
		</td>
		<th>彩种：</th>
			<td>
				<select class="selectList w120"  name="lid">
	            	<option value="">全部</option>
	            	<?php foreach ($this->caipiao_cfg as $key => $cp): ?>
	            	<option value="<?php echo $key; ?>" <?php if ($search['lid'] === "{$key}"): echo "selected"; endif; ?>><?php echo $cp['name'] ?></option>
	            	<?php endforeach; ?>
				</select>
      		</td>
      		<th>期次场次：</th>
      		<td><input type="text" class="ipt w108" name="issue" value='<?php echo $search['issue'] ?>' placeholder="期次场次..." /></td>
    		<td><a href="javascript:void(0);" class="btn-blue mr20 " onclick="$('#search_form').submit();">查询</a></td>
		</tr>

    </tbody>
  </table>
      <input type="hidden" name="fromType" value="<?php echo $fromType  ?>"  id="fromType" />
  </form>
</div>

<div class="data-table-list mt20">
  <table>
    <colgroup>
        <col width="150" />

        <col width="98" />
        <col width="98" />
         <col width="98" />
        <col width="88" />
        <col width="70" />
        <col width="140" />
        <col width="98" />
        <col width="90" />
        <col width="90" />
        <col width="45" />
        <col width="45" />
    </colgroup>
    <tbody>
    <tr>
      <th>订单编号</th>

      <th>用户名</th>
       <th>真实姓名</th>
      <th>彩种</th>
      <th>玩法</th>
      <th>期次</th>
      <th>创建时间</th>
      <th>订单金额（元）</th>
      <th>订单状态</th>
      <th>异常原因</th>
      <th>详情</th>
      <th>操作</th>
    </tr>
    <?php foreach($abnormals as $key => $order): ?>
      <tr>
      <td><?php echo $order['orderId'] ?></td>

      <td><?php echo $order['uname'] ?></td>
      <td><?php echo $order['real_name'] ?></td>
      <td><?php echo $this->caipiao_cfg[$order['lid']]['name'] ?></td>
      <td><?php if(!empty($this->caipiao_cfg[$order['lid']]['play'])): echo print_playtype($order['lid'], $order['playType'], $this->caipiao_cfg[$order['lid']]['play']); else: echo $this->caipiao_cfg[$order['lid']]['name'];  endif;?></td>
      <td><?php echo $order['issue'] ?></td>
      <td><?php echo $order['created'] ?></td>
      <td><?php echo m_format($order['money']) ?></td>
      <td><?php echo $this->caipiao_status_cfg[$order['status']][0] ?></td>
      <td><?php echo $this->caipiao_ab_cfg[$order['mark']] ?></td>
      <?php if($order['orderType']!=4){ ?>
      <td><a target="_blank" href="/backend/Management/orderDetail/?id=<?php echo $order['orderId']; ?>" class="cBlue">查看</a></td>
      <?php }else{ ?>
      <td><a target="_blank" href="/backend/Management/unitedOrderDetail/?id=<?php echo $order['orderId']; ?>" class="cBlue">查看</a></td>
      <?php } ?>
      <td><a href="<?php echo $order['id'] ?>" class="cBlue hide_ab" >隐藏<a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="13">
          <div class="stat">
            <span>本页&nbsp;<i><?php echo $pages[2] ?></i>&nbsp;条</span>
            <span class="ml20">共&nbsp;<i><?php echo $pages[1] ?></i>&nbsp;页</span>
            <span class="ml20">总计&nbsp;<i><?php echo $pages[3] ?></i>&nbsp;</span>
          </div>
        </td>
      </tr>
    </tfoot>
  </table>
</div>
<div class="page mt10 abnomal">
   <?php echo $pages[0] ?>
</div>

<script>
    $(function(){
        $('.abnomal a').click(function(){
            if($("#fromType").val() == "ajax")
            {
                var _this = $(this);
                $("#abnomal").load(_this.attr("href"));
                return false;
            }
            return true;
        });
        $('#search_form').submit(function(){
            if($("#fromType").val() == "ajax")
            {
                $("#abnomal").load("/backend/Order/abnormal_list?"+$("#search_form").serialize());
                return false;
            }
            return true;
        });
        $(".Wdate1").focus(function(){
            dataPicker();
        });
        $(".hide_ab").click(function(){
            var _this = $(this);
            $.ajax({
                type: "post",
                url: '/backend/Order/hide_ab',
                data: "id="+_this.attr("href"),
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    alert(json.message);
                    if(json.status =='y')
                    {
                        _this.parent("td").parent("tr").remove();
                        $stat_span1 = $(".stat span:eq(0) i");
                        $stat_span2 = $(".stat span:eq(2) i");
                        $stat_span1.html(Number($stat_span1.html())-1);
                        $stat_span2.html(Number($stat_span2.html())-1);
                    }
                }
            });
            return false;
        });
    });

</script>
