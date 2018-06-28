<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：数据中心&nbsp;&gt;&nbsp;<a href="/backend/Match/">对阵管理</a></div>
<div class="mod-tab mt20">
  <div class="mod-tab-hd">
    <ul>
      <li class="current"><a href="/backend/Match/">北京单场</a></li>
      <li><a href="/backend/Match/bdsfgg">北单胜负过关</a></li>
      <li><a href="/backend/Match/tczq">老足彩</a></li>
      <li><a href="/backend/Match/jczq">竞彩足球</a></li>
      <li><a href="/backend/Match/jclq">竞彩篮球</a></li>
      <li><a href="/backend/Match/gj">冠军彩</a></li>
      <li><a href="/backend/Match/gyj">冠亚军</a></li>
    </ul>
  </div>
  <div>
        <div class="data-table-filter">
          <table>
            <tbody>
            <tr>
              <td>
                期次编号：
                <select class="selectList w222" id="mid" name="mid">
                  <?php foreach ($mids as $mid): ?>
                  <option value="<?php echo $mid;?>" <?php if($search['mid'] === "{$mid}"): echo "selected"; endif;   ?>><?php echo $mid;?></option>
                  <?php endforeach; ?>
                </select>
                <span style="margin-left: 120px;">
                <a href="javascript:;" class="btn-blue" id="cancel">取消</a>
                <a href="javascript:;" class="btn-blue" id="time">修改比赛时间</a>
                <a href="javascript:;" class="btn-blue" id="score">修改对阵比分</a>
                <input type="hidden" name="selectId" id="selectId" value="" />
                </span>
              </td> 
            </tr>
            </tbody>
          </table>
        </div>
        <div class="data-table-list mt10">
          <table>
            <colgroup>
              <col width="8%" />
              <col width="12%" />
              <col width="10%" />
              <col width="10%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
            </colgroup>
            <thead>
              <tr>
                <th>赛事编号</th>
                <th>截止时间</th>
                <th>主队</th>
                <th>客队</th>
                <th>让球数</th>
                <th>半场比分</th>
                <th>全场比分</th>
                <th>胜平负</th>
                <th>猜比分</th>
                <th>半全场</th>
                <th>上下单双</th>
                <th>进球数</th>
                <th>是否取消</th>
                <th>赛事状态</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($result as $row):?>
              <tr id="<?php echo $row['id'];?>">
               <td><?php echo $row['mname'];?></td>
               <td><?php echo $row['begin_time'];?></td>
               <td><?php echo $row['home'];?></td>
               <td><?php echo $row['away'];?></td>
               <td><?php echo $row['rq'];?></td>
               <td><?php if($row['m_status'] == 0): echo $row['half_score']; else: echo '----'; endif;?></td>
               <td><?php if($row['m_status'] == 0): echo $row['full_score']; else: echo '----'; endif;?></td>
               <td><?php echo $row['spf_odds'];?></td>
               <td><?php echo $row['dcbf_odds'];?></td>
               <td><?php echo $row['bqc_odds'];?></td>
               <td><?php echo $row['dss_odds'];?></td>
               <td><?php echo $row['jqs_odds'];?></td>
               <td><?php if($row['m_status'] == 0): echo "否"; else: echo "是"; endif;?></td>
               <td><?php echo getBjdcStatus($row['begin_time'], $row['status'], $row['state']);?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
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
<script>
$(function(){
	//排期编号事件
	$('#mid').change(function(){
		var mid=$(this).children('option:selected').val();
		window.location.href="/backend/Match/?mid="+ mid;
	});

	$("tr").click(function(){
		$("tr").removeClass("select");
		$(this).addClass("select");
		$("#selectId").val($(this).attr("id"));
	});
	
	$("#cancel").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(13).html() != "截止")
		{
			alertPop('请不要对“在售或结期”场次做取消操作');
			return false;
		}
		
		var title = td.eq(0).html() + " " + td.eq(2).html() + " vs " + td.eq(3).html();
		$("#pop_name").html(title);
		$("#cancelId").val(id);
		popdialog("cancelPop");
	});

	$("#cancelSubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/bjdc_cancel',
            data: $("#cancelForm").append("<input type='hidden' name='env' value='<?php echo ENVIRONMENT?>'>").serialize(),
            success: function (data) {
                var json = jQuery.parseJSON(data);
                if(json.status =='y')
                {
                    location.reload();
                }
            }
        });
        return false;
	});

	$("#time").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(13).html() == "结期")
		{
			alertPop('请不要对“结期”场次做修改比赛时间操作');
			return false;
		}
		var title = td.eq(0).html() + " " + td.eq(2).html() + " vs " + td.eq(3).html();
		var html = '<tr><th>截止时间：</th><td><input type="text" value="'+td.eq(1).html()+'" class="ipt w222" name="time"></td></tr>';
		$("#tbody1").html(html);
		$("#pop_name1").html(title);
		$("#timeId").val(id);
		popdialog("timePop");
	});

	$("#timeSubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/bjdc_update_time',
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

	$("#score").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(13).html() == "在售")
		{
			alertPop('请不要对“在售”场次做修改比分操作');
			return false;
		}
		
		var title = td.eq(0).html() + " " + td.eq(2).html() + " vs " + td.eq(3).html();
		var thead = '<tr><td>比分</td><td>'+ td.eq(2).html() + '</td><td>' + td.eq(3).html()+'</td></tr>';
		var half = td.eq(5).html().split(":");
		var full = td.eq(6).html().split(":");
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
        td.eq(13).html() == "截止" ? matchStatus = 0 : matchStatus = 1;
        $("#scoreSubmit").click(function(){
            $.ajax({
                type: "post",
                url: '/backend/Match/bjdc_update_score',
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


	//重写提示框
	function alertPop(content){
		$("#alertBody").html(content);
		popdialog("alertPop");
	}
})
</script>
</body>
</html>