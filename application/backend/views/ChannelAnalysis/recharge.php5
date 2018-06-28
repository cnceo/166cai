<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">渠道分析 </a>&nbsp;&gt;&nbsp;<a href="">充值</a></div>
<div class="data-table-filter mt10">
<form action="/backend/ChannelAnalysis/recharge" method="get"  id="search_form">
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
      <td id="tversion">
        <label for="">版本：
          <select class="selectList w98" id="" name="version">
            <option value="all">全部</option>
            <?php foreach ($version as $val):?>
            <option value="<?php echo $val['version'];?>" <?php if($search['version'] === "{$val['version']}"): echo "selected"; endif;?>><?php echo $val['version'];?></option>
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
      <col width="160" />
      <col width="100" />
      <col width="100" />
      <col width="150" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
    </colgroup>
    <thead>
      <tr>
        <th>渠道</th>
        <th class="filter-item">充值用户</th>
        <th class="filter-item">充值转化率</th>
        <th class="filter-item">充值金额</th>
        <th class="filter-item">充值订单数量</th>
        <th class="filter-item">平均单笔充值金额</th>
        <th class="filter-item">人均充值金额</th>
      </tr>
    </thead>
    <tbody class="avoid-sort">
      <tr>
        <td>汇总</td>
        <td><?php echo $total['users'];?></td>
        <td><?php echo $total['conversion_rate']?></td>
        <td><?php echo number_format($total['total']/100, 2);?></td>
        <td><?php echo $total['recharge_nums'];?></td>
        <td><?php echo $total['avg_recharge_money'];?></td>
        <td><?php echo $total['avg_user_money'];?></td>
      </tr>
      </tbody>
      <tbody>
      <?php foreach ($list as $val):?>
      <tr>
        <td><?php echo $val['channel'];?></td>
        <td><?php echo $val['recharge_users'];?></td>
        <td><?php echo $val['conversion_rate']?></td>
        <td><?php echo number_format($val['total']/100, 2);?></td>
        <td><?php echo $val['recharge_nums'];?></td>
        <td><?php echo $val['avg_recharge_money'];?></td>
        <td><?php echo $val['avg_user_money'];?></td>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
</div>
</div>
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tablesorter.css?v=7">
<script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery.tablesorter.js"></script>
<!-- 导出功能js -->
<script type='text/javascript' src="/source/tableExport/Blob.js"></script>
<script type='text/javascript' src="/source/tableExport/FileSaver.js"></script>
<script type='text/javascript' src="/source/tableExport/tableExport.js"></script>
<script>
$('.tablesorter').tablesorter({});
$("#" + $('input[name="timeType"]').val()).addClass('selected');
$("#search").click(function(){
	$('#search_form').submit();
});

$('.filter').find('.filter-options').on('click', function(){
	$(this).addClass('selected').siblings().removeClass('selected');
 	$('input[name="timeType"]').val($(this).attr('id'));
  });

var $exportLink = document.getElementById('export');
$exportLink.addEventListener('click', function(e){
	e.preventDefault();
	tableExport('tablesorter', '充值统计', 'csv');
	
}, false);
$(function(){
	if($('#platform option:selected').val()==1)
	{
		$('#tversion').hide();
	}
	$('#platform').change(function(){
		if($('#platform option:selected').val()==1)
		{
			$('#tversion').hide();
		}else
		{
			$('#tversion').show();
		}
	});
});
</script>
</body>
</html>