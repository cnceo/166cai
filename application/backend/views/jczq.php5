<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：数据中心&nbsp;&gt;&nbsp;<a href="/backend/Match/">对阵管理</a></div>
<div class="mod-tab mt20">
	<div class="mod-tab-hd">
    	<ul>
      		<li><a href="/backend/Match/">北京单场</a></li>
      		<li><a href="/backend/Match/bdsfgg">北单胜负过关</a></li>
      		<li><a href="/backend/Match/tczq">老足彩</a></li>
      		<li class="current"><a href="/backend/Match/jczq">竞彩足球</a></li>
      		<li><a href="/backend/Match/jclq">竞彩篮球</a></li>
      		<li><a href="/backend/Match/gj">冠军彩</a></li>
      		<li><a href="/backend/Match/gyj">冠亚军</a></li>
    	</ul>
  	</div>
  	<div class="mod-tab-bd">
  		<div class="data-table-filter">
  			<form action="/backend/Match/jczq" method="get"  id="search_form">
          		<table>
            		<tbody>
            			<tr>
              				<td>
	               				选择时间：
	                			<span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="Wdate1"><i></i></span>
	                			<span class="ml8 mr8">至</span>
	                			<span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="Wdate1"><i></i></span>
				                <span style="margin-left: 20px;">
				                	<a href="javascript:void(0);" class="btn-blue" id="aduitCheck">比分审核</a>
					                <a href="javascript:void(0);" class="btn-blue" id="cancel">场次取消</a>
					                <a href="javascript:void(0);" class="btn-blue" id="delay">场次延期</a>
					                <a href="javascript:void(0);" class="btn-blue" id="cancelDelay">延期取消</a>
					                <a href="javascript:void(0);" class="btn-blue" id="time">修改比赛时间</a>
					                <a href="javascript:void(0);" class="btn-blue" id="score">修改对阵比分</a>
					                <input type="hidden" name="selectId" id="selectId" value="" />
	                			</span>
              				</td>
            			</tr>
            			<tr>
            				<td>
            					抓取状态：
	                			<select class="selectList"  name="is_capture">
				    				<?php foreach ($captureArr as $key => $val):?>
				            		<option value="<?php echo $key; ?>" <?php if ($search['is_capture'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val;?></option>
				            		<?php endforeach;?>
				        		</select>
	                			审核状态：
	                			<select class="selectList"  name="is_aduitflag">
				    				<?php foreach ($aduitflagArr as $key => $val):?>
				            		<option value="<?php echo $key; ?>" <?php if ($search['is_aduitflag'] == "{$key}"): echo "selected"; endif; ?>><?php echo $val;?></option>
				            		<?php endforeach;?>
				        		</select>
	                			<a id="search" href="javascript:void(0);" class="btn-blue">查询</a>
            				</td>
            			</tr>	
            		</tbody>
          		</table>
        	</form>
        </div>
        <div class="overflow-x mt10">
	        <div class="data-table-list">
	          	<table>
	            	<colgroup>
	            		<col width="30" />
	              		<col width="70" />
	              		<col width="110" />
	              		<col width="100" />
	              		<col width="100" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="100" />
	            	</colgroup>
	            	<thead>
	              		<tr>
	              			<th><input type="checkbox" class="_ck"></th>
			                <th>赛事编号</th>
			                <th>截止时间</th>
			                <th>主队</th>
			                <th>客队</th>
			                <th>让球数</th>
			                <th>半场比分</th>
			                <th>全场比分</th>
			                <th>是否延期</th>
			                <th>是否取消</th>
			                <th>赛事状态</th>
			                <th>比分审核</th>
			                <th>抓取状态</th>
	              		</tr>
	            	</thead>
	          	</table>
	        </div>
	        <div class="data-table-list table-scroll">
	          	<table>
	            	<colgroup>
	            		<col width="30" />
	              		<col width="70" />
	              		<col width="110" />
	              		<col width="100" />
	              		<col width="100" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="60" />
	              		<col width="87" />
	            	</colgroup>
	            	<tbody>
	              		<?php foreach ($result as $row):?>
	              		<tr id="<?php echo $row['id'];?>">
	              			<td><input type="checkbox" class="ck_" data-index="<?php echo $row['id'];?>"></td>
	               			<td><?php echo $row['mname'];?></td>
	               			<td><?php echo $row['end_sale_time'];?></td>
	               			<td><?php echo $row['home'];?></td>
	               			<td><?php echo $row['away'];?></td>
	               			<td><?php echo $row['rq'];?></td>
	               			<td><?php if($row['m_status'] == 0): echo $row['half_score']; else: echo '----'; endif;?></td>
	               			<td><?php if($row['m_status'] == 0): echo $row['full_score']; else: echo '----'; endif;?></td>
	               			<td><?php echo ($row['cstate'] & 1) ? "是" : "否"; ?></td>
	               			<td><?php if($row['m_status'] == 0): echo "否"; else: echo "是"; endif;?></td>
	               			<td><?php echo getJcStatus($row['end_sale_time'], $row['status'], $row['aduitflag']);?></td>
	               			<td data-status="<?php echo $row['aduitflag']; ?>"><?php echo ($row['aduitflag'] > 0) ? ($row['aduitflag'] > 1 ? '系统审核' : '人工审核') : '待审核' ;?></td>
	               			<td><?php if($row['isCapture']):?>已抓取<a href="javascript:;" class="cBlue ml10 captureDetail" data-index="<?php echo $row['mid'];?>">查看</a><?php else: ?>未抓取<?php endif;?></td>
	              		</tr>
	              		<?php endforeach;?>
	            	</tbody>
	          	</table>
	        </div>
        </div>
  	</div>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<form id='cancelForm' method='post' action=''>
	<div class="pop-dialog" id="cancelPop">
		<div class="pop-in">
			<div class="pop-head">
				<h2 id="pop_name"></h2>
				<span class="pop-close" title="关闭">关闭</span>
			</div>
			<div class="pop-body">
				<div class="data-table-filter del-percent">
					请确认该场次派奖按照取消处理
				</div>
			</div>
			<div class="pop-foot tac">
				<a href="javascript:;" class="btn-blue-h32 mlr15" id="cancelSubmit">确认</a>
				<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
			</div>
		</div>
	</div>
	<input type="hidden" value="" name="cancelId"  id="cancelId"/>
</form>
<!-- 场次延期 -->
<form id='delayForm' method='post' action=''>
	<div class="pop-dialog" id="delayPop">
		<div class="pop-in">
			<div class="pop-head">
				<h2 id="delay_name"></h2>
				<span class="pop-close" title="关闭">关闭</span>
			</div>
			<div class="pop-body">
				<div class="data-table-filter del-percent">
					请确认该场次设置为延期
				</div>
			</div>
			<div class="pop-foot tac">
				<a href="javascript:;" class="btn-blue-h32 mlr15" id="delaySubmit">确认</a>
				<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
			</div>
		</div>
	</div>
	<input type="hidden" value="" name="delayId"  id="delayId"/>
</form>
<!-- 取消延期 -->
<form id='cancelDelayForm' method='post' action=''>
	<div class="pop-dialog" id="cancelDelayPop">
		<div class="pop-in">
			<div class="pop-head">
				<h2 id="cancelDelay_name"></h2>
				<span class="pop-close" title="关闭">关闭</span>
			</div>
			<div class="pop-body">
				<div class="data-table-filter del-percent">
					请确认该场次设置为取消延期
				</div>
			</div>
			<div class="pop-foot tac">
				<a href="javascript:;" class="btn-blue-h32 mlr15" id="cancelDelaySubmit">确认</a>
				<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
			</div>
		</div>
	</div>
	<input type="hidden" value="" name="cancelDelayId"  id="cancelDelayId"/>
</form>
<form id='timeForm' method='post' action=''>
	<div class="pop-dialog" id="timePop">
		<div class="pop-in">
			<div class="pop-head">
				<h2 id="pop_name1"></h2>
				<span class="pop-close" title="关闭">关闭</span>
			</div>
			<div class="pop-body">
				<div class="data-table-filter del-percent">
					<table>
						<colgroup>
							<col width="68" />
			                <col width="350" />
						</colgroup>
						<tfoot>
							<tr>
								<td colspan="2"><p style="color: red;">请谨慎修改比赛时间,时间格式例：2015-01-01 01:01:00</p></td>
							</tr>
						</tfoot>
						<tbody id="tbody1">
						</tbody>
					</table>
				</div>
			</div>
			<div class="pop-foot tac">
				<a href="javascript:;" class="btn-blue-h32 mlr15" id="timeSubmit">确认</a>
				<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
			</div>
		</div>
	</div>
	<input type="hidden" value="" name="timeId"  id="timeId"/>
</form>
<form id='scoreForm' method='post' action=''>
	<div class="pop-dialog" id="scorePop">
		<div class="pop-in">
			<div class="pop-head">
				<h2 id="pop_name2"></h2>
				<span class="pop-close" title="关闭">关闭</span>
			</div>
			<div class="pop-body">
				<div class="data-table-list">
					<table>
						<colgroup>
							<col width="10%" />
			                <col width="45%" />
			                <col width="45%" />
						</colgroup>
						<tfoot>
							<tr>
								<td colspan="3" class="tal">请谨慎修改比赛比分</td>
							</tr>
						</tfoot>
						<thead id="thead2">
						</thead>
						<tbody id="tbody2">
						</tbody>
					</table>
				</div>
			</div>
			<div class="pop-foot tac">
				<a href="javascript:;" class="btn-blue-h32 mlr15" id="scoreSubmit">确认</a>
				<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
			</div>
		</div>
	</div>
	<input type="hidden" value="" name="scoreId"  id="scoreId"/>
</form>
<div class="pop-dialog" id="alertPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-filter del-percent" id="alertBody">
			</div>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">确认</a>
		</div>
	</div>
</div>
<!-- 抓取详情 -->
<div class="pop-dialog" id="detailPop">
    <div class="pop-in">
        <div class="pop-head">
            <h2>抓取状态</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent">
                <table>
                    <tbody id="captureList">
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15" id="detailSubmit">选取录入</a>
            <a href="javascript:;" class="btn-b-white pop-cancel">关闭</a>
        </div>
    </div>
</div>
<!-- 比分人工审核 -->
<form id='verifyScoreForm' method='post' action=''>
	<div class="pop-dialog" id="verifyScore">
	    <div class="pop-in">
	        <div class="pop-head">
	            <h2>比分人工输入审核</h2>
	            <span class="pop-close" title="关闭">关闭</span>
	        </div>
	        <div class="pop-body">
	            <div class="data-table-filter del-percent overflow-y">
	                <table>
	                    <thead>
	                        <tr>
	                            <th width="56">比赛场次</th>
	                            <th width="120">对阵</th>
	                            <th width="110" class="tac">全场比分</th>
	                            <th width="110" class="tac">半场比分</th>
	                        </tr>
	                    </thead>
	                    <tbody class="next-num">
	                    </tbody>
	                </table>
	            </div>
	        </div>
	        <div class="pop-foot tac">
	            <a href="javascript:;" class="btn-blue-h32 mlr15" id="verifySubmit">确认</a>
	            <a href="javascript:;" class="btn-b-white pop-cancel">关闭</a>
	        </div>
	    </div>
	</div>
</form>
<script  src="/source/date/WdatePicker.js"></script>
<script>
$(function(){
	$("#search").click(function(){
		var start = $("input[name='start_time']").val();
		var end = $("input[name='end_time']").val();
		if(start > end){
			alertPop('选择时间输入错误');
			return false;
		}
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

	// 获取选中的场次
	function getSelectId()
	{
		ids = [];
		$(".ck_").each(function(){
			if(this.checked)
			{
				if($.inArray($(this).data('index'), ids) == -1)
				{
					ids.push($(this).data('index'));
				}			
			}
		})
		return ids;
	}
	
	$(".Wdate1").focus(function(){
        dataPicker();
    });

	// $("tr").click(function(){
	// 	$("tr").removeClass("select");
	// 	$(this).addClass("select");
	// 	$("#selectId").val($(this).attr("id"));
	// });
	
	// 场次取消
	$("#cancel").click(function(){
		var ids = getSelectId();
		if(ids.length < 1)
		{
			alertPop('请选中要修改的比赛场次');
            return false;
		}
		if(ids.length > 1)
		{
			alertPop('请选中一场比赛场次');
            return false;
		}
		var id = ids[0];
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(10).html() != "截止")
		{
			alertPop('请不要对“在售或结期”场次做取消操作');
			return false;
		}
		var title = td.eq(1).html() + " " + td.eq(3).html() + " vs " + td.eq(4).html();
		$("#delay_name").html(title);
		$("#cancelId").val(id);
		popdialog("cancelPop");
	});

	$("#cancelSubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/jczq_cancel',
            data: $("#cancelForm").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").serialize(),
            success: function (data) {
                var json = jQuery.parseJSON(data);
                alert(json.message)
                if(json.status =='y')
                {
                    location.reload();
                }
            }
        });
        return false;
	});

	// 场次延期
	$("#delay").click(function(){
		var ids = getSelectId();
		if(ids.length < 1)
		{
			alertPop('请选中要修改的比赛场次');
            return false;
		}
		if(ids.length > 1)
		{
			alertPop('请选中一场比赛场次');
            return false;
		}
		var id = ids[0];
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(10).html() == "结期" || td.eq(10).html() == "在售")
		{
			alertPop('请不要对“在售或结期”场次做延期操作');
			return false;
		}
		var title = td.eq(1).html() + " " + td.eq(3).html() + " vs " + td.eq(4).html();
		$("#delay_name").html(title);
		$("#delayId").val(id);
		popdialog("delayPop");
	});

	$("#delaySubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/jczq_delay',
            data: $("#delayForm").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").serialize(),
            success: function (data) {
                var json = jQuery.parseJSON(data);
                alert(json.message)
                if(json.status =='y')
                {
                    location.reload();
                }
            }
        });
        return false;
	});

	// 取消延期
	$("#cancelDelay").click(function(){
		var ids = getSelectId();
		if(ids.length < 1)
		{
			alertPop('请选中要修改的比赛场次');
            return false;
		}
		if(ids.length > 1)
		{
			alertPop('请选中一场比赛场次');
            return false;
		}
		var id = ids[0];
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
        var td = $("#"+id).children("td").siblings("td");
		if(td.eq(8).html() == "否")
		{
			alertPop('请不要对“未延期”场次做取消延期操作');
			return false;
		}
		var td = $("#"+id).children("td").siblings("td");
		var title = td.eq(1).html() + " " + td.eq(3).html() + " vs " + td.eq(4).html();
		$("#cancelDelay_name").html(title);
		$("#cancelDelayId").val(id);
		popdialog("cancelDelayPop");
	});

	$("#cancelDelaySubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/jczq_cancel_delay',
            data: $("#cancelDelayForm").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").serialize(),
            success: function (data) {
                var json = jQuery.parseJSON(data);
                alert(json.message)
                if(json.status =='y')
                {
                    location.reload();
                }
            }
        });
        return false;
	});

	// 修改比赛时间
	$("#time").click(function(){
		var ids = getSelectId();
		if(ids.length < 1)
		{
			alertPop('请选中要修改的比赛场次');
            return false;
		}
		if(ids.length > 1)
		{
			alertPop('请选中一场比赛场次');
            return false;
		}
		var id = ids[0];
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(10).html() == "结期")
		{
			alertPop('请不要对“结期”场次做修改比赛时间操作');
			return false;
		}
		var title = td.eq(1).html() + " " + td.eq(3).html() + " vs " + td.eq(4).html();
		var html = '<tr><th>截止时间：</th><td><input type="text" value="'+td.eq(2).html()+'" class="ipt w222" name="time"></td></tr>';
		$("#tbody1").html(html);
		$("#pop_name1").html(title);
		$("#timeId").val(id);
		popdialog("timePop");
	});

	$("#timeSubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/jczq_update_time',
            data: $("#timeForm").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").serialize(),
            success: function (data) {
                var json = jQuery.parseJSON(data);
                alert(json.message)
                if(json.status =='y')
                {
                    location.reload();
                }
            }
        });
        return false;
	});

	// 修改对阵比分
	$("#score").click(function(){
		var ids = getSelectId();
		if(ids.length < 1)
		{
			alertPop('请选中要修改的比赛场次');
            return false;
		}
		if(ids.length > 1)
		{
			alertPop('请选中一场比赛场次');
            return false;
		}
		var id = ids[0];
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(10).html() == "在售")
		{
			alertPop('请不要对“在售”场次做修改比分操作');
			return false;
		}
		var title = td.eq(1).html() + " " + td.eq(3).html() + " vs " + td.eq(4).html();
		var thead = '<tr><td>比分</td><td>'+ td.eq(3).html() + '</td><td>' + td.eq(4).html()+'</td></tr>';
		var half = td.eq(6).html().split(":");
		var full = td.eq(7).html().split(":");
		if(typeof half[1] === 'undefined') half[1] = '';
		if(typeof full[1] === 'undefined') full[1] = '';
		var html  = '<tr><td>半场</td><td><input type="text" class="ipt" name="half_h" value="'+half[0]+'" /></td><td><input type="text" class="ipt" name="half_a" value="'+half[1]+'" /></td></tr>';
	    	html += '<tr><td>全场</td><td><input type="text" class="ipt" name="full_h" value="'+full[0]+'" /></td><td><input type="text" class="ipt" name="full_a" value="'+full[1]+'" /></td></tr>';
		$("#thead2").html(thead);
		$("#tbody2").html(html);
		$("#pop_name2").html(title);
		$("#scoreId").val(id);
		popdialog("scorePop");
        var matchStatus;
        td.eq(10).html() == "截止" ? matchStatus = 0 : matchStatus = 1;
        $("#scoreSubmit").click(function(){
            $.ajax({
                type: "post",
                url: '/backend/Match/jczq_update_score',
                data: $("#scoreForm").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").serialize() + '&matchStatus=' + matchStatus,
                success: function (data) {
                    var json = jQuery.parseJSON(data);
                    alert(json.message)
                    if(json.status =='y')
                    {
                        location.reload();
                    }
                }
            });
            return false;
        });
	});

	// 重写提示框
	function alertPop(content){
		$("#alertBody").html(content);
		popdialog("alertPop");
	}

	// 自动补位
	function PrefixInteger(num, length) {  
 		return (Array(length).join('0') + num).slice(-length);  
	}

	// 抓取状态
	$(".captureDetail").click(function(){
		var mid = $(this).data('index');
		$.ajax({
            type: "post",
		    url: "/backend/Match/getJczqCapture",
		    data: {'mid':mid},
		    success: function(data){
		    	var json = jQuery.parseJSON(data);
                if(json.status == '1'){
                	$('#captureList').html(json.data.html);
                	popdialog("detailPop");
                }else{
                	alertPop('抓取详情获取失败');
					return false;
                }
		    }
        });
	});

	// 选取录入
	$("#detailSubmit").click(function(){
		var scoreVal = $("input[name='captured']:checked").val();
		var mid = $("input[name='captureMid']").val();
		if(scoreVal == '')
		{
			alertPop('选取录入比分不能为空');
			return false;
		}
		$.ajax({
            type: "post",
		    url: "/backend/Match/saveJczqCapture",
		    data: {'scoreVal':scoreVal, 'mid':mid, 'env':'<?php echo ENVIRONMENT?>'},
		    success: function(data){
		    	var json = jQuery.parseJSON(data);
                if(json.status == 'y'){
                	alert('比分录入成功');
                	location.reload();
                }else{
                	alertPop(json.message);
					return false;
                }
		    }
        });
	});

	// 比分输入
	$('.next-num').on('keyup', 'input', function(){
	    if(!/\D/.test($(this).val())){
	        if(($(this).val().length == 2)){
	        	if($(this).data('index') < 4)
	        	{
	        		if($(this).index() == $(this).parent('td').find('input').length - 1){
		            	// 当前td的下一个
		                $(this).parent('td').next().find('input').eq(0).focus();
		            }else{
		                $(this).next().focus();
		            }
	        	}
	        	else
	        	{
	        		$(this).parent('td').parent('tr').next().find('input').eq(0).focus();
	        	}
	        }
	    }else{
	    	$(this).val('');
	    	alert('请输入0-9的两位数字');
			return false;
	    }
	});

	// 比分审核弹框
	$("#aduitCheck").click(function(){
		var ids = getSelectId();
		if(ids.length < 1)
		{
			alertPop('请选中要审核的比赛场次');
            return false;
		}
		// 组装弹框
		var tpl = '';
		for (var i = 0; i < ids.length; ++i) {
			var f_h = '';
			var f_a = '';
			var h_h = '';
			var h_a = '';
			var id = ids[i];
			var td = $("#"+id).children("td").siblings("td");
			// 检查审核状态
			if(td.eq(11).data('status') == '1')
			{
				// 已人工审核则弹框提示
				alertPop('选中的场次包含已人工审核的比赛');
				return false;
			}
			if(td.eq(6).html() == '' || td.eq(7).html() == '')
			{
				alertPop('所选比赛中包含暂无比分场次，请选择已抓取比分场次进行审核');
				return false;
			}
			if(td.eq(11).data('status') == '2')
			{
				// 已系统审核则默认输入
				var hs = td.eq(6).html().split(':');
				if(hs[0] !== '' && hs[1] !== '')
				{
					h_h = PrefixInteger(hs[0], 2);
					h_a = PrefixInteger(hs[1], 2);
				}
				var fs = td.eq(7).html().split(':');
				if(fs[0] !== '' && fs[1] !== '')
				{
					f_h = PrefixInteger(fs[0], 2);
					f_a = PrefixInteger(fs[1], 2);
				}
			}

			tpl += '<tr>';
			tpl += '<td>' + td.eq(1).html() + '</td>';
			tpl += '<td>' + td.eq(3).html() + ' VS ' + td.eq(4).html() + '</td>';
			tpl += '<td class="tac"><input type="text" class="ipt w40 tac" value="' + f_h + '" maxlength="2" data-index="1" name="score[' + id + '][f_h]"> : <input type="text" class="ipt w40 tac" value="' + f_a + '" maxlength="2" data-index="2" name="score[' + id + '][f_a]"></td>';
			tpl += '<td class="tac"><input type="text" class="ipt w40 tac" value="' + h_h + '" maxlength="2" data-index="3" name="score[' + id + '][h_h]"> : <input type="text" class="ipt w40 tac" value="' + h_a + '" maxlength="2" data-index="4" name="score[' + id + '][h_a]"></td>';
			tpl += '</tr>';
		}

		$(".next-num").html(tpl);
		popdialog("verifyScore");		
	});

	// 确认审核
	$("#verifySubmit").click(function(){
		var flag = true;
		// 输入框检查
		$(".next-num").find('input').each(function(){
			if($(this).val() == '')
			{		
				flag = false;
				return false;
			}
		});
		
		if(!flag)
		{
			alert("比分审核信息不能为空");
			return false;
		}

		$.ajax({
            type: "post",
		    url: "/backend/Match/verifyJczqScore",
		    data: $("#verifyScoreForm").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").serialize(),
		    success: function(data){
		    	var json = jQuery.parseJSON(data);
                if(json.status == 'y'){
                	alert(json.message);
                	location.reload();
                }else{
                	alert(json.message);
					return false;
                }
		    }
        });
	});
	

})
</script>
</body>
</html>