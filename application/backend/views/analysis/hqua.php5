<?php $this->load->view("templates/head")?>
 <!--<div class="frame-container" style="margin-left:0;">-->
	<div class="path">
		您的位置：<a href="">数据分析 </a>&nbsp;&gt;&nbsp;<a href="/backend/Analysis/hqua/">优质用户分析</a>
	</div>
	<div class="data-table-filter mt10">
		<table>
			<colgroup>
				<col width="180">
				<col width="180">
				<col width="180">
				<col width="180">
			</colgroup>
			<tbody>
			<form action="/backend/Analysis/hqua" method="post"  id="search_form">
				<tr>
					<td colspan="2"><label for="">登录次数： <input type="text" class="ipt w98" id = 'timesBeginId' name = 'loginTimesBegin' value ='<?php echo $searchData['loginTimesBegin']?>' > -- <input type="text" class="ipt w98" id = 'timesEndId' name = 'loginTimesEnd' value ='<?php echo $searchData['loginTimesEnd']?>'>
					</label></td>
					<td colspan="2"><label for="">投注总额： <input type="text" class="ipt w98" id = 'moneyBeginId' name = 'totalMoneyBegin' value ='<?php echo $searchData['totalMoneyBegin']?>'> -- <input type="text" class="ipt w98" id = 'moneyEndId' name = 'totalMoneyEnd' value ='<?php echo $searchData['totalMoneyEnd']?>'>
					</label></td>
				</tr>
				<tr>
					<td><label for="">平台： <select class="selectList w98" id="platformId" name="platform">
                      <?php foreach ($platform as $key=>$val):?>
                      <option value="<?php echo $key;?>"
        			  <?php if($searchData['platform'] == "{$key}"): echo "selected"; endif;?>><?php echo $val;?></option>
                      <?php endforeach;?>
                      </select>
					</label></td>
					<td><label for="">渠道： <select class="selectList w98" id="channelId" name="channel">
					<option value="all">全部</option>
                    <?php foreach ($channels as $val):?>
                    <option value="<?php echo $val['id'];?>"
        			<?php if($searchData['channel'] == "{$val['id']}"): echo "selected"; endif;?>><?php echo $val['name'];?></option>
                    <?php endforeach;?>
                    </select>
					</label></td>
					<td id = 'versionContainer'><label for="">版本： <select class="selectList w98" id="versionId" name="version">
					<option value="all">全部</option>
                    <?php foreach ($version as $val):?>
                    <option value="<?php echo $val['version'];?>"
        			<?php if($searchData['version'] == "{$val['version']}"): echo "selected"; endif;?>><?php echo $val['version'];?></option>
                    <?php endforeach;?>
                    </select>
					</label></td>
					<td><a href="javascript:;" class="btn-blue ml25" id="choose">查询</a><a class="btn-blue ml25" download="filename.csv" href="javascript:;" id="tableExport">导出</a></td>
					
				</tr>
				</form>
			</tbody>
		</table>
	</div>
	<div class="data-table-list mt10">
		<table id="tablesorter" class="tablesorter" >
			<colgroup>
				<col width="100" />
				<col width="70" />
				<col width="70" />
				<col width="90" />
				<col width="80" />
				<col width="90" />
				<col width="40" />
				<col width="40" />
				<col width="40" />
				<col width="80" />
				<col width="70" />
				<col width="100" />
			</colgroup>
			<thead>
				<tr>
					<th class="filter-item">用户名</th>
					<th class="filter-item">30日内登录次数</th>
					<th class="filter-item">投注总额</th>
					<th class="filter-item">中奖总额</th>
					<th class="filter-item">中奖回报率</th>
					<th class="filter-item">单笔订单均额</th>
					<th class="filter-item">高</th>
					<th class="filter-item">慢</th>
					<th class="filter-item">竞</th>
					<th class="filter-item">综合实力排名</th>
					<th class="filter-item">帐户余额</th>
					<th class="filter-item">最后登录时间</th>
				</tr>
			</thead>
			<tbody>
					<?php if ($result): ?>
                    <?php foreach ($result as $row): ?>
				<tr>
					<td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $row['uid'] ?>"
                           class="cBlue"><?php echo $row['uname'];?></a></td>
					<td><?php echo $row['login_times_30day'];?></td>
					<td><?php echo $row['total_betmoney'];?></td>
					<td><?php echo $row['total_winmoney'];?></td>
					<td><?php echo $row['rate_of_repay'];?></td>
					<td><?php echo $row['money_per_order'];?></td>
					<td><?php echo $row['gao'];?></td>
					<td><?php echo $row['man'];?></td>
					<td><?php echo $row['jing'];?></td>
					<td><?php echo '排名';?></td>
					<td><?php echo $row['account'];?></td>
					<td><?php echo $row['last_login_time'];?></td>
				</tr>
					<?php endforeach;?>
					<?php endif;?>
					</tbody>
		</table>
	</div>
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
	$(document).ready(function(){
		if($("#platformId").val() == '1' || $("#platformId").val() == '4')
		  {
			  $("#versionId").val('all');
			  $("#versionId").attr("disabled",true);
			  $("#versionContainer").hide();
		  }
		  else
		  {
			  $("#versionId").removeAttr("disabled");
			  $("#versionContainer").show(); 
		  }
	});

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
		
	$("#platformId").change(function(){
		  if($("#platformId").val() == '1' || $("#platformId").val() == '4')
		  {
			  $("#versionId").val('all');
			  $("#versionId").attr("disabled",true);
			  $("#versionContainer").hide();
		  }
		  else
		  {
			  $("#versionId").removeAttr("disabled"); 
			  $("#versionContainer").show(); 
		  }
		  refreshChannels($("#platformId").val());
	  });
	  
	$("#choose").click(function() {
		if(isNaN($('#timesBeginId').val()) || ($('#timesBeginId').val().match(/^-/)))
		{
			alert('请输入正确次数查询条件');
			return;
		}
		if($('#timesEndId').val() != 'max')
		{
			if(isNaN($('#timesEndId').val()) || ($('#timesEndId').val().match(/^-/)))
    		{
    			alert('请输入正确次数查询条件');
    			return;
    		}
		}
		if(isNaN($('#moneyBeginId').val()) || ($('#moneyBeginId').val().match(/^-/)))
		{
			alert('请输入正确次数查询条件');
			return;
		}
		if($('#moneyEndId').val() != 'max')
		{
			if(isNaN($('#moneyEndId').val()) || ($('#moneyEndId').val().match(/^-/)))
    		{
    			alert('请输入正确次数查询条件');
    			return;
    		}
		}
		
		$('#search_form').submit();
// 		var platform = $('#platformId').children('option:selected').val();
// 		var channel = $('#channelId').children('option:selected').val();
// 		var version = $('#versionId').children('option:selected').val();
// 		var loginTimesBegin = $("#timesBeginId").val();
//         var loginTimesEnd = $("#timesEndId").val();
//         var totalMoneyBegin = $("#moneyBeginId").val();
//         var totalMoneyEnd = $("#moneyEndId").val();
// 		$.ajax({
//             type: 'POST',
//             url: '/backend/Analysis/deb',
//             data: {
//                 'platform':platform,
//                 'channel':channel,
//                 'version':version,
//                 'loginTimesBegin':loginTimesBegin,
//                 'loginTimesEnd':loginTimesEnd,
//                 'totalMoneyBegin':totalMoneyBegin,
//                 'totalMoneyEnd':totalMoneyEnd
//             },
//             dataType: "json",
//             success: function (resp) {
//                 if (resp.ok) {
//                     location.reload();
//                 }
//                 else {
//                     alert(resp.msg);
//                 }
//             }
//         });
        
// 		$("#data-list tr").find("td:eq(1)").each(function() {
// 			if($(this).html()=='1'){
// 				$(this).parent("tr").show();
// 			}
// 			else{
// 				$(this).parent("tr").hide();
// 			}
// 		});
	});
});

function refreshChannels(pform)
{
	$.ajax({
         type: 'POST',
         url: '/backend/Analysis/getChannels',
         data: {'pform' : pform},
         dataType: "text",
         success: function (resp) { 
             $('#channelId').html(resp);
         }
     });
}

var $exportLink = document.getElementById('tableExport');
$exportLink.addEventListener('click', function(e){
	e.preventDefault();
	tableExport('tablesorter', '优质用户统计', 'csv');
	
}, false);
</script>
</body>
</html>