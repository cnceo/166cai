<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">数据分析 </a>&nbsp;&gt;&nbsp;<a href="">投注</a></div>
<div class="data-table-filter mt10">
<form action="/backend/DataAnalysis/betting" method="get"  id="search_form">
  <table>
    <colgroup>
        <col width="180">
        <col width="180">
        <col width="180">
        <col width="180">
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
      <td>
        <label for="">渠道：
          <select class="selectList w98" id="" name="channel">
            <option value="all">全部</option>
            <?php foreach ($channels as $val):?>
            <option value="<?php echo $val['id'];?>" <?php if($search['channel'] === "{$val['id']}"): echo "selected"; endif;?>><?php echo $val['name'];?></option>
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
      <td>
        <label for="">彩种：
          <select class="selectList w98" id="" name="lid">
          	<option value="">全部</option>
            <?php foreach ($lid as $key => $val):?>
            <option value="<?php echo $key;?>" <?php if($search['lid'] === "{$key}"): echo "selected"; endif;?>><?php echo $val;?></option>
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
  <table>
    <colgroup>
      <col width="100" />
      <col width="120" />
      <col width="145" />
      <col width="150" />
      <col width="140" />
      <col width="140" />
    </colgroup>
    <caption>汇总数据：</caption>
    <thead>
      <tr>
        <th>日均投注用户</th>
        <th>投注转化率</th>
        <th>日均投注金额</th>
        <th>日均订单数量</th>
        <th>平均单笔投注金额</th>
        <th>人均投注金额</th>
        <th>日均中奖用户</th>
        <th>日均中奖金额</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php echo $avgTotal['avg_users'];?></td>
        <td><?php echo $avgTotal['conversion_rate'];?></td>
        <td><?php echo $avgTotal['avg_total'];?></td>
        <td><?php echo $avgTotal['avg_nums'];?></td>
        <td><?php echo $avgTotal['avg_order_money'];?></td>
        <td><?php echo $avgTotal['avg_user_money'];?></td>
        <td><?php echo $avgTotal['awardUser'];?></td>
        <td><?php echo $avgTotal['awardTotal'];?></td>
      </tr>
    </tbody>
  </table>
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
        <th>日期</th>
        <th class="filter-item">投注用户</th>
        <th class="filter-item">投注转化率</th>
        <th class="filter-item">投注总额</th>
        <th class="filter-item">订单数量</th>
        <th class="filter-item">平均投注金额</th>
        <th class="filter-item">人均投注金额</th>
        <th class="filter-item">中奖用户</th>
        <th class="filter-item">中奖总额</th>
        <th class="filter-item">返奖率</th>
      </tr>
    </thead>
    <tbody class="avoid-sort">
      <tr>
        <td>汇总</td>
        <td><?php echo $total['users'];?></td>
        <td><?php echo $total['conversion_rate'];?></td>
        <td><?php echo number_format($total['total']/100, 2);?></td>
        <td><?php echo $total['order_nums'];?></td>
        <td><?php echo $total['avg_order_money'];?></td>
        <td><?php echo $total['avg_user_money'];?></td>
        <td><?php echo $total['awardUser'];?></td>
        <td><?php echo number_format($total['awardTotal']/100, 2);?></td>
        <td><?php echo $total['award_rate'];?>%</td>
      </tr>
      </tbody>
      <tbody>
      <?php foreach ($list as $val):?>
      <tr>
        <td><span class="<?php echo $val['dateClass'];?>"><?php echo $val['date'];?></span></td>
        <td><?php echo $val['betting_users'];?></td>
        <td><?php echo $val['conversion_rate'];?></td>
        <td><?php echo number_format($val['total']/100, 2);?></td>
        <td><?php echo $val['order_nums'];?></td>
        <td><?php echo $val['avg_order_money'];?></td>
        <td><?php echo $val['avg_user_money'];?></td>
        <td><?php echo $val['award_users'];?></td>
        <td><?php echo number_format($val['award_total']/100, 2);?></td>
        <td><?php echo $val['award_rate'];?></td>
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
$(function(){
	if($('#platform option:selected').val()==1)
	{
		$('#tversion').hide();
	}
	$('#platform').change(function(){
		var platform = $('#platform option:selected').val();
		if(platform == 1)
		{
			$('#tversion').hide();
		}else
		{
			$('#tversion').show();
		}
		$.ajax({
            type: "post",
            url: '/backend/DataAnalysis/getChannels',
            data: {platform:platform},
            success: function (returnData) {
                $('select[name="channel"]').html(returnData);
            }
        });
	});
});

$(function(){
	$('.tablesorter').tablesorter();
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