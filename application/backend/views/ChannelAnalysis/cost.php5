<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">渠道分析 </a>&nbsp;&gt;&nbsp;<a href="">成本统计</a></div>
<div class="data-table-filter mt10">
<form action="/backend/ChannelAnalysis/cost" method="get"  id="search_form">
  <table>
    <colgroup>
        <col width="150">
        <col width="150">
        <col width="160">
    </colgroup>
    <tbody>
    <tr>
      <td>
        <label for="">平台：
            <select class="selectList w98" id="platform" name="platform">
              <?php foreach ($platform as $key=>$val):?>
              <option value="<?php echo $key;?>" <?php if($search['platform'] === "{$key}"): echo "selected"; endif;?>><?php echo $val;?></option>
              <?php endforeach;?>
            </select>
        </label>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <div class="filter">时间：
          <a href="###" class="filter-options" id="time1">过去7天</a>
          <a href="###" class="filter-options" id="time2">过去30天</a>
          <a href="###" class="filter-options" id="time3">过去60天</a>
        </div>
      </td>
      <td>
        <a id="search" href="javascript:;" class="btn-blue ml25">查询</a>
        <a href="javascript:;" class="btn-blue ml25" id="export">导出</a>
      </td>
    </tr>
    </tbody>
  </table>
  <input type="hidden" name="timeType" value="<?php echo $search['timeType'];?>" />
</form>
</div>

<div class="data-table-list mt10">
  <table class="tablesorter" id="tablesorter">
    <colgroup>
      <col width="100" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
    </colgroup>
    <thead>
      <tr>
        <th>渠道</th>
        <th class="filter-item">渠道投放成本</th>
        <th class="filter-item">有效用户单价</th>
        <th class="filter-item">有效用户价值</th>
        <th class="filter-item">充值用户价值</th>
        <th class="filter-item">投注用户客单价</th>
        <th class="filter-item">渠道销量总计</th>
        <th class="filter-item">渠道收益比</th>
      </tr>
    </thead>
    <tbody class="avoid-sort">
      <tr>
        <td>汇总</td>
        <td><?php echo number_format(ParseUnit($total['total_cost'],1), 2);?></td>
        <td><?php echo $total['total_valid_user_price'];?></td>
        <td><?php echo $total['total_valid_user_value'];?></td>
        <td><?php echo $total['total_recharge_user_value'];?></td>
        <td><?php echo $total['total_betting_user_price'];?></td>
        <td><?php echo number_format(ParseUnit($total['total_total'],1), 2);?></td>
        <td><?php echo $total['total_rate'];?></td>
      </tr>
      </tbody>
      <tbody>
      <?php foreach ($list as $val):?>
      <tr>
        <td><?php echo $val['channel'];?></td>
        <td><?php echo number_format(ParseUnit($val['cost'],1), 2);?></td>
        <td><?php echo $val['valid_user_price'];?></td>
        <td><?php echo $val['valid_user_value'];?></td>
        <td><?php echo $val['recharge_user_value'];?></td>
        <td><?php echo $val['betting_user_price'];?></td>
        <td><?php echo number_format(ParseUnit($val['total'],1), 2);?></td>
        <td><?php echo $val['rate'];?></td>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
</div>
</div>
<!-- 字段排序js -->
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tablesorter.css?v=7">
<script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery.tablesorter.js"></script>
<!-- 导出功能js -->
<script type='text/javascript' src="/source/tableExport/Blob.js"></script>
<script type='text/javascript' src="/source/tableExport/FileSaver.js"></script>
<script type='text/javascript' src="/source/tableExport/tableExport.js"></script>
<script>
// 侧栏导航
$(function(){
	$('.tablesorter').tablesorter({});
	$("#" + $('input[name="timeType"]').val()).addClass('selected');
	$("#search").click(function(){
		$('#search_form').submit();
	});

  $('.filter').find('.filter-options').on('click', function(){
	  $(this).addClass('selected').siblings().removeClass('selected');
	  $('input[name="timeType"]').val($(this).attr('id'));
  })
});

var $exportLink = document.getElementById('export');
$exportLink.addEventListener('click', function(e){
	e.preventDefault();
	tableExport('tablesorter', '投注统计', 'csv');
	
}, false);
</script>
</body>
</html>