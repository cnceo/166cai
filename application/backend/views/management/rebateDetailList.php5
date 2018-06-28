<div class="rebates-led">
    <table>
      <tbody>
	<tr>
	  <td rowspan="2">今日收益<br><strong class="main-color-s"><?php echo m_format($todayIncome);?></strong>元</td>
	  <td rowspan="2">收入总计<br><strong><?php echo m_format($rebate['total_income']);?></strong>元</td>
	  <td rowspan="2">推广链接<br><input type="text" value="<?php echo $rebate['pro_link'];?>"></td>
	</tr>
	<tr>
	  <td><!-- <?php if($rebate['stop_flag'] > 0):?><a href="javascript:;" class="btn-blue open" data-id="0">开启返点</a><?php else:?><a href="javascript:;" class="btn-blue cancel" data-id="1">停止返点</a><?php endif;?> --></td>
	</tr>
      </tbody>
    </table>
  </div>

  <!--表单筛选 begin-->
  <div class="filter-oper">
  <form action="/backend/Management/rebateDetailList" method="get"  id="seach_form">
    <table class="data-table-filter">
      <colgroup>
	<col width="280">
	<col width="100">
	<col width="100">
	<col width="100">
      </colgroup>
      <tbody>
	<tr>
	  <td>
	    交易时间：
        <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1" /><i></i></span>
        <span class="ml8 mr8">至</span>
        <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1" /><i></i></span>
	  </td>
	  <td>
	    <select class="selectList w120 mr20"  name="lid">
            <option value="">全部</option>
            <?php foreach ($this->caipiao_cfg as $key => $cp): ?>
            <option value="<?php echo $key; ?>" <?php if ($search['lid'] === "{$key}"): echo "selected"; endif; ?>><?php echo $cp['name'] ?></option>
            <?php endforeach; ?>
         </select>
	  </td>
	  <td>
	    <input type="text" class="ipt w95" name="userName" value="<?php echo $search['userName'] ?>" placeholder="用户名">
	  </td>
	  <td>
	  	<input type="hidden" name="fromType" value="<?php echo $fromType  ?>"  id="fromType" />
	  	<input type="hidden" name="id" value="<?php echo $rebate['id']; ?>"/>
	    <a href="javascript:;" class="btn-blue" onclick="$('#seach_form').submit();">查询</a>
	  </td>
	</tr>
      </tbody>
    </table>
    </form>
      <!--表单筛选 end-->
      <!--表格 begin-->
      <table class="data-table-list">
	<colgroup>
	    <col width="110">
	    <col width="220">
	    <col width="140">
	    <col width="220">
	    <col width="140">
	    <col width="200">
	    <col width="170">
	</colgroup>
	<thead>
	  <tr>
	    <th>彩种</th>
	    <th>订单编号</th>
	    <th>期号</th>
	    <th>用户名</th>
	    <th>消费金额(元)</th>
	    <th>赚取收入(元)</th>
	    <th>交易时间</th>
	  </tr>
	</thead>
	<tbody>
	<?php foreach($lists as $key => $value): ?>
	  <tr>
	    <td><?php echo $this->caipiao_cfg[$value['lid']]['name'];?></td>
	    <td><?php echo $value['orderid'];?></td>
	    <td><?php echo $value['issue'];?></td>
	    <td><?php echo $value['userName'];?></td>
	    <td><?php echo m_format($value['money']);?></td>
	    <td class="main-color"><?php echo m_format($value['income']);?></td>
	    <td><?php echo $value['created'];?></td>
	  </tr>
	<?php endforeach;?>
	</tbody>
	<tfoot>
	  <tr>
	    <td class="tal table-foot-td" colspan="5">
	      <span class="mr20">收入笔数：<b class="font-color-s"><?php echo $pages[3]; ?></b> 笔</span>
	      <span>收入金额合计：<b class="main-color"><?php echo m_format($totalMoney);?></b> 元</span>
	    </td>
	    <td class="tar table-page">
	      <span class="mlr10">本页 <b><?php echo $pages[2]; ?></b> 条记录</span><span>共 <b><?php echo $pages[1] ?></b> 页</span>
	    </td>
	  </tr>
	</tfoot>
      </table>
      <!--表格 end-->
</div>
<div class="page mt10 abnmal">
   <?php echo $pages[0] ?>
</div>
<script>
var uid = <?php echo $rebate['uid'];?>;
$(function(){
	$('.abnmal a').click(function(){
        if($("#fromType").val() == "ajax")
        {
            var _this = $(this);
            $("#detail").load(_this.attr("href"));
            return false;
        }
        return true;
    });
	$('#seach_form').submit(function(){
        if($("#fromType").val() == "ajax")
        {
            $("#detail").load("/backend/Management/rebateDetailList?"+$("#seach_form").serialize());
            return false;
        }
        return true;
    });
    $(".Wdate1").focus(function(){
        dataPicker();
    });
	$(".cancel, .open").click(function(){
		var setStatus = $(this).attr("data-id");
		$.ajax({
            type: "post",
            url: '/backend/Management/rebateCancel',
            data: {'cancelId': uid, 'setStatus': setStatus},
            success: function (data) {
                var json = jQuery.parseJSON(data);
                alert(json.message)
                if(json.status =='y')
                {
                    location.reload();
                }
            }
        });
	});
});
</script>