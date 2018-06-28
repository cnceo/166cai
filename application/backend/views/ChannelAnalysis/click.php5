<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">渠道分析 </a>&nbsp;&gt;&nbsp;<a href="">点击量</a></div>
<div class="data-table-filter mt10">
<form action="/backend/ChannelAnalysis/click" method="get"  id="search_form">
  <table>
    <colgroup>
        <col width="150">
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
  <table  class="tablesorter" id="tablesorter">
	<?php if($search['platform'] == 1){?>
    <colgroup>
      <col width="160" />
      <col width="100" />
      <col width="100" />
      <col width="150" />
      <col width="100" />
      <col width="100" />
      <col width="100" />
    </colgroup>
	<?php }else{ ?>
		<colgroup>
		 <col width="100" />
		 <col width="100" />
		 <col width="100" />
		 <col width="100" />
		 <col width="100" />
		 <col width="100" />
		 <col width="100" />
		 <col width="150" />
	   </colgroup>
	<?php } ?>
    <thead>
		<?php if($search['platform'] == 1){?>
			<tr>
			  <th>渠道</th>
			  <th>UV</th>
			  <th>PV</th>
			  <th>点击用户数</th>
			  <th>点击转化率</th>
			  <th>点击量</th>
			  <th>人均点击量</th>
			</tr>
		<?php }else{ ?>
			<tr>
			  <th>渠道</th>
			  <th>新增用户</th>
			  <th>活跃用户</th>
			  <th>启动次数</th>
			  <th>点击用户数</th>
			  <th>点击转化率</th>
			  <th>点击量</th>
			  <th>人均点击量</th>
			</tr>
		<?php } ?>
    </thead>
    <tbody class="avoid-sort">
	<tr>
        <td>汇总</td>
        <td><?php echo ($search['platform'] == 1) ? $total['uv'] : $total['pv']; ?></td>
        <td><?php echo ($search['platform'] == 1) ? $total['pv'] : $total['uv']; ?></td>
		<?php if($search['platform'] == 2){?>
			<td><?php echo $total['lv'];?></td>
		<?php }?>
        <td><?php echo $total['click_uv']; ?></td>
        <td><?php echo number_format($total['click_ratio']*100,2); ?>%</td>
        <td><?php echo $total['click_pv']; ?></td>
        <td><?php echo number_format($total['rj_click'],2); ?></td>
     </tr>
     </tbody>
      <tbody>
      <?php foreach ($list as $val):?>
      <tr>
        <td><?php echo $val['name'] ;?></td><!--$val['name']-->
        <td><?php echo ($search['platform'] == 1) ? $val['uv'] : $val['pv'];?></td>
        <td><?php echo ($search['platform'] == 1) ? $val['pv'] : $val['uv'];?></td>
		<?php if($search['platform'] == 2){?>
			<td><?php echo $val['lv'];?></td>
		<?php }?>
        <td><?php echo $val['click_uv']?></td>
        <td><?php echo $val['click_rate'];?></td>
        <td><?php echo $val['click_pv'];?></td>
        <td><?php echo $val['rj_click'];?></td>
      </tr>
      <?php endforeach;?>
    </tbody>
  </table>
</div>


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
  });
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
var $exportLink = document.getElementById('export');
$exportLink.addEventListener('click', function(e){
	e.preventDefault();
	tableExport('tablesorter', '点击量渠道统计', 'csv');
	
}, false);
</script>
</body>
</html>