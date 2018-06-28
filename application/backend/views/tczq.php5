<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：数据中心&nbsp;&gt;&nbsp;<a href="/backend/Match/">对阵管理</a></div>
<div class="mod-tab mt20">
  <div class="mod-tab-hd">
    <ul>
      <li><a href="/backend/Match/">北京单场</a></li>
      <li><a href="/backend/Match/bdsfgg">北单胜负过关</a></li>
      <li class="current"><a href="/backend/Match/tczq">老足彩</a></li>
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
              <form action="/backend/Match/tczq" method="get"  id="search_form">
              对阵类型：
                <select class="selectList w184" id="ctype" name="ctype" onchange="$('#search_form').submit();">
                  <?php foreach ($ctypes as $ctype => $val): ?>
                  <option value="<?php echo $ctype;?>" <?php if($search['ctype'] === "{$ctype}"): echo "selected"; endif;   ?>><?php echo $val;?></option>
                  <?php endforeach; ?>
                </select>
                期次编号：
                
                <select class="selectList w130" id="mid" name="mid" onchange="$('#search_form').submit();">
                  <?php foreach ($mids as $mid): ?>
                  <option value="<?php echo $mid;?>" <?php if($search['mid'] === "{$mid}"): echo "selected"; endif;   ?>><?php echo $mid;?></option>
                  <?php endforeach; ?>
                </select>
                <span style="margin-left: 120px;">
                <a href="javascript:;" class="btn-blue" id="delay">延期</a>
                <a href="javascript:;" class="btn-blue" id="score">修改比分</a>
                <a href="javascript:;" class="btn-blue" id="time">修改比赛时间</a>
                <a href="javascript:;" class="btn-blue btn-table-modify ml25">保存</a>
                <input type="hidden" name="selectId" id="selectId" value="" />
                </span>
                </form>
              </td> 
            </tr>
            </tbody>
          </table>
        </div>
        <?php if($search['ctype'] == 1):?>
        <div class="data-table-list mt10">
          <table id="tczqTable">
            <colgroup>
              <col width="8%" />
              <col width="12%" />
              <col width="10%" />
              <col width="10%" />
              <col width="12%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
            </colgroup>
            <thead>
              <tr>
                <th>场次</th>
                <th>赛事</th>
                <th>主队</th>
                <th>客队</th>
                <th>比赛时间</th>
                <th>半场比分</th>
                <th>全场比分</th>
                <th>赛果</th>
                <th>赛事状态</th>
              </tr>
            </thead>
            <tbody>
            <?php
            ?>
              <?php foreach ($result as $row):?>
              <tr id="<?php echo $row['id'];?>">
               <td><?php echo $row['mname'];?></td>
               <td><?php echo $row['league'];?></td>
               <td><?php echo $row['home'];?></td>
               <td><?php echo $row['away'];?></td>
                 <td>
                      <div class="table-modify">
                          <p class="table-modify-txt"><?php echo $row['begin_date'];?></p>
                          <p class="table-modify-ipt"><input type="text" id = "time" class="ipt" name = "time" value="<?php echo $row['begin_date'];?>"></p>
                      </div>
                  </td>
               <td><?php if($row['status'] != 51): echo $row['half_score']; else: echo '----'; endif;?></td>
               <td><?php if($row['status'] != 51): echo $row['full_score']; else: echo '----'; endif;?></td>
               <td><?php if($row['status'] != 51): echo $row['result1']; else: echo '----'; endif;?></td>
               <td><?php echo getTczqStatus($row['begin_date'], $row['status']);?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
        <div class="pop-mask" style="display:none;width:200%"></div>
<form id='delayForm' method='post' action=''>
<div class="pop-dialog" id="delayPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2 id="pop_name"></h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-filter del-percent" id="tbody">
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
	$("tr").click(function(){
		$("tr").removeClass("select");
		$(this).addClass("select");
        $("#selectId").val($(this).attr("id"));
	});

	$("#score").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(11).html() == "在售")
		{
			alertPop('请不要对“在售”场次做修改比分操作');
			return false;
		}
		var title = td.eq(2).html() + " vs " + td.eq(3).html();
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
        td.eq(11).html() == "截止" ? matchStatus = 0 : matchStatus = 1;
        $("#scoreSubmit").click(function(){
            $.ajax({
                type: "post",
                url: '/backend/Match/tczq_update_score',
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



	$("#delay").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(td.length-1).html() != "截止")
		{
			alertPop('请不要对“在售或结期”场次做取消操作');
			return false;
		}
		var title = td.eq(2).html() + " vs " + td.eq(3).html();
		var html = '请确认 '+title+' 该场比赛官方已经做延期处理';
		$("#tbody").html(html);
		$("#pop_name").html(title);
		$("#delayId").val(id);
		popdialog("delayPop");
	});

	$("#delaySubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/tczq_delay',
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

	$("#time").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(td.length-1).html() == "结期")
		{
			alertPop('请不要对“结期”场次做修改比赛时间操作');
			return false;
		}
		var title = td.eq(2).html() + " vs " + td.eq(3).html();
		var html = '<tr><th>截止时间：</th><td><input type="text" value="'+td.eq(4).find('input').val()+'" class="ipt w222" name="time"></td></tr>';
		$("#tbody1").html(html);
		$("#pop_name1").html(title);
		$("#timeId").val(id);
		popdialog("timePop");
	});

	$("#timeSubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/tczq_update_time',
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
        //重写提示框
    function alertPop(content){
        $("#alertBody").html(content);
        popdialog("alertPop");
    }
    //提取字符串中的数字
    function getNum(str)
    {
        var value = str.replace(/[^0-9]/ig,"");
        return value;
    }

    function validateAlphaNum(str)
    {
        var returnArr = [true,""];
        if(/^[a-zA-Z0-9]+$/.test(str) == false)
        {
            message = "用户名只能为字母数字";
            returnArr[0] = false;
            returnArr[1] = message;
        }
        return returnArr;
    }

    function validateNum(str,type)
    {
        var returnArr = [false,"请输入大于等于0的数字"];
        if(str != "" &&str != null)
        {
            if(/^([0-9]*\.|\.?)[0-9]*$/.test(str) == true)
            {
                returnArr[0] = true;
                returnArr[1] = "";
            }
            return returnArr;
        }

    }

    function alertPop(content){
        $("#alertBody").html(content);
        popdialog("alertPop");
        console.log(popdialog("alertPop"));
    }

    $(function(){
        $("tr").click(function(){
            $("tr").removeClass("select");
            $(this).addClass("select");
            $("#selectId").val($(this).attr("id"));
        });

        function alertPop(content){
            $("#alertBody").html(content);
            popdialog("alertPop");
        }

        $("#search").click(function(){
            $('#search_form').submit();
        });
        $('.table-modify-txt').on('click', function(){
            $(this).hide();
            $(this).parents('.table-modify').find('.table-modify-ipt').show();
            var ipt = $(this).parents('.table-modify').find('.table-modify-ipt');
            var flage = ipt.find('input').attr('flage' ,'1') ;
        });
        $('.btn-table-modify').on('click', function(){
            var tableModify= $(this).parents('.wrapper').find('.table-modify');
            tableModify.find('.table-modify-ipt').hide();
            tableModify.find('.table-modify-txt').show();
            var table = document.getElementById("tczqTable");
            var tbody = table.tBodies[0];
            var jsonArray = [];
            for(var k =0, rowb; rowb = tbody.rows[k]; k++){

                if(getText(rowb.cells[4])||getText(rowb.cells[7])||getText(rowb.cells[8])||getText(rowb.cells[9]))
                {
                    var arr = {
                        id : rowb.id,
                        begin_date : getText(rowb.cells[4]),
                        eur_odd_win : getText(rowb.cells[7]),
                        eur_odd_deuce : getText(rowb.cells[8]),
                        eur_odd_loss : getText(rowb.cells[9])
                    }
                    jsonArray.push(arr);
                }

            }

            var data = JSON.stringify(jsonArray);
            $.ajax({
                type: "post",
                url: '/backend/Match/alter',
                data: {data:data,env:'<?php echo ENVIRONMENT?>'},
                dataType: "json",
                success: function (returnData) {
                    if(returnData.status =='y')
                    {
                        alert("修改成功");
                        location.reload();
                    }else
                    {
                        alertPop(returnData.message);
                    }
                }
            });

        })
        //获取dom文本
        var getText = function( el ){
            if($(el).find("input").attr('flage') == 1)
            {
                return $(el).find("input").val();
            }

        };

    });
})
</script>
        <?php elseif ($search['ctype'] == 2):?>
        <div class="data-table-list mt10">
            <table id="tczqTable">
            <colgroup>
              <col width="8%" />
              <col width="12%" />
              <col width="10%" />
              <col width="10%" />
              <col width="12%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
              <col width="6%" />
            </colgroup>
            <thead>
              <tr>
                <th>场次</th>
                <th>赛事</th>
                <th>主队</th>
                <th>客队</th>
                <th>比赛时间</th>
                <th>半场比分</th>
                <th>全场比分</th>
                <th>赛果</th>
                <th>状态</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($result as $row):?>
              <tr id="<?php echo $row['id'];?>">
               <td><?php echo $row['mname'];?></td>
               <td><?php echo $row['league'];?></td>
               <td><?php echo $row['home'];?></td>
               <td><?php echo $row['away'];?></td>
                  <td>
                      <div class="table-modify">
                          <p class="table-modify-txt"><?php echo $row['begin_date'];?></p>
                          <p class="table-modify-ipt"><input type="text" id = "time" class="ipt" name = "time" value="<?php echo $row['begin_date'];?>"></p>
                      </div>
                  </td>
               <td><?php if($row['status'] != 51): echo $row['half_score']; else: echo '----'; endif;?></td>
               <td><?php if($row['status'] != 51): echo $row['full_score']; else: echo '----'; endif;?></td>

               <td><div style="border-bottom: 1px dashed #ccc;"><?php if($row['status'] != 51): echo $row['result1']; else: echo '----'; endif;?></div>
                <div><?php if($row['status'] != 51): echo $row['result2']; else: echo '----'; endif;?></div></td>
               <td><?php echo getTczqStatus($row['begin_date'], $row['status']);?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
        <div class="pop-mask" style="display:none;width:200%"></div>
<form id='delayForm' method='post' action=''>
<div class="pop-dialog" id="delayPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2 id="pop_name"></h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-filter del-percent" id="tbody">
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
	$("tr").click(function(){
		$("tr").removeClass("select");
		$(this).addClass("select");
		$("#selectId").val($(this).attr("id"));
	});

	$("#score").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(td.length-1).html() == "在售")
		{
			alertPop('请不要对“在售”场次做修改比分操作');
			return false;
		}
		var title = td.eq(2).html() + " vs " + td.eq(3).html();
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
        td.eq(td.length-1).html() == "截止" ? matchStatus = 0 : matchStatus = 1;
        $("#scoreSubmit").click(function(){
            $.ajax({
                type: "post",
                url: '/backend/Match/tczq_update_score',
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

	$("#delay").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(td.length-1).html() != "截止")
		{
			alertPop('请不要对“在售或结期”场次做取消操作');
			return false;
		}
		var title = td.eq(2).html() + " vs " + td.eq(3).html();
		var html = '请确认 '+title+' 该场比赛官方已经做延期处理';
		$("#tbody").html(html);
		$("#pop_name").html(title);
		$("#delayId").val(id);
		popdialog("delayPop");
	});

	$("#delaySubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/tczq_delay',
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

	$("#time").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(td.length-1).html() == "结期")
		{
			alertPop('请不要对“结期”场次做修改比赛时间操作');
			return false;
		}
		var title = td.eq(2).html() + " vs " + td.eq(3).html();
		var html = '<tr><th>截止时间：</th><td><input type="text" value="'+td.eq(4).find('input').val()+'" class="ipt w222" name="time"></td></tr>';
		$("#tbody1").html(html);
		$("#pop_name1").html(title);
		$("#timeId").val(id);
		popdialog("timePop");
	});

	$("#timeSubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/tczq_update_time',
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
    //重写提示框
    function alertPop(content){
        $("#alertBody").html(content);
        popdialog("alertPop");
    }
    //提取字符串中的数字
    function getNum(str)
    {
        var value = str.replace(/[^0-9]/ig,"");
        return value;
    }

    function validateAlphaNum(str)
    {
        var returnArr = [true,""];
        if(/^[a-zA-Z0-9]+$/.test(str) == false)
        {
            message = "用户名只能为字母数字";
            returnArr[0] = false;
            returnArr[1] = message;
        }
        return returnArr;
    }

    function validateNum(str,type)
    {
        var returnArr = [false,"请输入大于等于0的数字"];
        if(str != "" &&str != null)
        {
            if(/^([0-9]*\.|\.?)[0-9]*$/.test(str) == true)
            {
                returnArr[0] = true;
                returnArr[1] = "";
            }
        }
        return returnArr;
    }

    function alertPop(content){
        $("#alertBody").html(content);
        popdialog("alertPop");
        console.log(popdialog("alertPop"));
    }

    $(function(){
        $("tr").click(function(){
            $("tr").removeClass("select");
            $(this).addClass("select");
            $("#selectId").val($(this).attr("id"));
        });

        function alertPop(content){
            $("#alertBody").html(content);
            popdialog("alertPop");
        }

        $("#search").click(function(){
            $('#search_form').submit();
        });

        $('.table-modify-txt').on('click', function(){
            $(this).hide();
            $(this).parents('.table-modify').find('.table-modify-ipt').show();
            var ipt = $(this).parents('.table-modify').find('.table-modify-ipt');
            var flage = ipt.find('input').attr('flage' ,'1') ;
        });

        $('.btn-table-modify').on('click', function(){
            var tableModify= $(this).parents('.wrapper').find('.table-modify');
            tableModify.find('.table-modify-ipt').hide();
            tableModify.find('.table-modify-txt').show();
            var table = document.getElementById("tczqTable");
            var tbody = table.tBodies[0];
            var jsonArray = [];
            for(var k =0, rowb; rowb= tbody.rows[k]; k++)
            {
                if(getText(rowb.cells[4])||getText(rowb.cells[7])||getText(rowb.cells[8])||getText(rowb.cells[9])) {
                    var arr = {
                        id: rowb.id,
                        begin_date: getText(rowb.cells[4]),
                        eur_odd_win: getText(rowb.cells[7]),
                        eur_odd_deuce: getText(rowb.cells[8]),
                        eur_odd_loss: getText(rowb.cells[9])
                    }
                    jsonArray.push(arr);
                }
            }

            var data = JSON.stringify(jsonArray);
            $.ajax({
                type: "post",
                url: '/backend/Match/alter',
                data: {data:data,env:'<?php echo ENVIRONMENT?>'},
                dataType: "json",
                success: function (returnData) {
                    if(returnData.status =='y')
                    {
                        alert("修改成功");
                        location.reload();
                    }else
                    {
                        alertPop(returnData.message);
                    }
                }
            });

        })
        //获取dom文本
        var getText = function( el ){
            if($(el).find("input").attr('flage') == 1)
            {
                return $(el).find("input").val();
            }

        };
    });
})
</script>
        <?php else:?>
        <div class="data-table-list mt10">
            <table id="tczqTable">
            <colgroup>
              <col width="8%" />
              <col width="12%" />
              <col width="10%" />
              <col width="10%" />
              <col width="12%" />
              <col width="12%" />
              <col width="6%" />
            </colgroup>
            <thead>
              <tr>
                <th>场次</th>
                <th>赛事</th>
                <th>对阵</th>
                <th>比赛时间</th>
                <th>全场比分</th>
                <th>赛果</th>
                <th>状态</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($result as $row):?>
              <tr id="<?php echo $row['id'];?>">
               <td><?php echo $row['mname'];?></td>
               <td><?php echo $row['league'];?></td>
               <td>
               	  <div style="border-bottom: 1px dashed #ccc;"><?php echo $row['home'];?></div>
                  <div><?php echo $row['away'];?></div>
               </td>
                  <td>
                      <div class="table-modify">
                          <p class="table-modify-txt"><?php echo $row['begin_date'];?></p>
                          <p class="table-modify-ipt"><input type="text" id = "time" class="ipt" name = "time" value="<?php echo $row['begin_date'];?>"></p>
                      </div>
                  </td>
               <td><?php if($row['status'] != 51): echo $row['full_score']; else: echo '----'; endif;?></td>
               <td>
               	  <div style="border-bottom: 1px dashed #ccc;"><?php if($row['status'] != 51): echo $row['result1']; else: echo '----'; endif;?></div>
                  <div><?php if($row['status'] != 51): echo $row['result2']; else: echo '----'; endif;?></div>
               </td>
               <td><?php echo getTczqStatus($row['begin_date'], $row['status']);?></td>
              </tr>
              <?php endforeach;?>
            </tbody>
          </table>
        </div>
<div class="pop-mask" style="display:none;width:200%"></div>
<form id='delayForm' method='post' action=''>
<div class="pop-dialog" id="delayPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2 id="pop_name"></h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-filter del-percent" id="tbody">
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
	$("tr").click(function(){
		$("tr").removeClass("select");
		$(this).addClass("select");
		$("#selectId").val($(this).attr("id"));
	});

	$("#score").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(9).html() == "在售")
		{
			alertPop('请不要对“在售”场次做修改比分操作');
			return false;
		}
		var div = td.eq(2).children("div").siblings("div");
		var title = div.eq(0).html() + " vs " + div.eq(1).html();
		var thead = '<tr><td>比分</td><td>'+ div.eq(0).html() + '</td><td>' + div.eq(1).html()+'</td></tr>';
		var full = td.eq(4).html().split(":");
		if(typeof full[1] === 'undefined') full[1] = '';
		var html = '<tr><td>全场</td><td><input type="text" class="ipt" name="full_h" value="'+full[0]+'" /></td><td><input type="text" class="ipt" name="full_a" value="'+full[1]+'" /></td></tr>';
		$("#thead2").html(thead);
		$("#tbody2").html(html);
		$("#pop_name2").html(title);
		$("#scoreId").val(id);
		popdialog("scorePop");
        var matchStatus;
        td.eq(9).html() == "截止" ? matchStatus = 0 : matchStatus = 1;
        $("#scoreSubmit").click(function(){
            $.ajax({
                type: "post",
                url: '/backend/Match/tczq_update_score',
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

	$("#delay").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(9).html() != "截止")
		{
			alertPop('请不要对“在售或结期”场次做取消操作');
			return false;
		}
		var div = td.eq(2).children("div").siblings("div");
		var title = div.eq(0).html() + " vs " + div.eq(1).html();
		var html = '请确认 '+title+' 该场比赛官方已经做延期处理';
		$("#tbody").html(html);
		$("#pop_name").html(title);
		$("#delayId").val(id);
		popdialog("delayPop");
	});

	$("#delaySubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/tczq_delay',
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

	$("#time").click(function(){
		var id = $("#selectId").val();
		if(!id)
        {
			alertPop('请选中要修改的比赛场次');
            return false;
        }
		var td = $("#"+id).children("td").siblings("td");
		if(td.eq(9).html() == "结期")
		{
			alertPop('请不要对“结期”场次做修改比赛时间操作');
			return false;
		}
		var div = td.eq(2).children("div").siblings("div");
		var title = div.eq(0).html() + " vs " + div.eq(1).html();
		var html = '<tr><th>比赛时间：</th><td><input type="text" value="'+td.eq(3).find('input').val()+'" class="ipt w222" name="time"></td></tr>';
		$("#tbody1").html(html);
		$("#pop_name1").html(title);
		$("#timeId").val(id);
		popdialog("timePop");
	});

	$("#timeSubmit").click(function(){
		$.ajax({
            type: "post",
            url: '/backend/Match/tczq_update_time',
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
    //重写提示框
    function alertPop(content){
        $("#alertBody").html(content);
        popdialog("alertPop");
    }
    //提取字符串中的数字
    function getNum(str)
    {
        var value = str.replace(/[^0-9]/ig,"");
        return value;
    }

    function validateAlphaNum(str)
    {
        var returnArr = [true,""];
        if(/^[a-zA-Z0-9]+$/.test(str) == false)
        {
            message = "用户名只能为字母数字";
            returnArr[0] = false;
            returnArr[1] = message;
        }
        return returnArr;
    }

    function validateNum(str,type)
    {
        var returnArr = [false,"请输入大于等于0的数字"];
        if(str != "" &&str != null)
        {
            if(/^([0-9]*\.|\.?)[0-9]*$/.test(str) == true)
            {
                returnArr[0] = true;
                returnArr[1] = "";
            }
        }
        return returnArr;
    }

    function alertPop(content){
        $("#alertBody").html(content);
        popdialog("alertPop");
        console.log(popdialog("alertPop"));
    }

    $(function(){
        $("tr").click(function(){
            $("tr").removeClass("select");
            $(this).addClass("select");
            $("#selectId").val($(this).attr("id"));
        });

        function alertPop(content){
            $("#alertBody").html(content);
            popdialog("alertPop");
        }

        $("#search").click(function(){
            $('#search_form').submit();
        });

        $('.table-modify-txt').on('click', function(){
            $(this).hide();
            $(this).parents('.table-modify').find('.table-modify-ipt').show();
            var ipt = $(this).parents('.table-modify').find('.table-modify-ipt');
            var flage = ipt.find('input').attr('flage' ,'1') ;
        });
        $('.btn-table-modify').on('click', function(){
            var tableModify= $(this).parents('.wrapper').find('.table-modify');
            tableModify.find('.table-modify-ipt').hide();
            tableModify.find('.table-modify-txt').show();
            var table = document.getElementById("tczqTable");
            var tbody = table.tBodies[0];
            var jsonArray = [];
            for(var k =0, rowb; rowb= tbody.rows[k]; k++)
            {
                if(getText(rowb.cells[3])||getText(rowb.cells[5])||getText(rowb.cells[6])||getText(rowb.cells[7])) {
                    var arr = {
                        id: rowb.id,
                        begin_date: getText(rowb.cells[3]),
                        eur_odd_win: getText(rowb.cells[5]),
                        eur_odd_deuce: getText(rowb.cells[6]),
                        eur_odd_loss: getText(rowb.cells[7])
                    }
                    jsonArray.push(arr);
                }
            }

            var data = JSON.stringify(jsonArray);
            $.ajax({
                type: "post",
                url: '/backend/Match/alter',
                data: {data:data,env:'<?php echo ENVIRONMENT?>'},
                dataType: "json",
                success: function (returnData) {
                    if(returnData.status =='y')
                    {
                        alert("修改成功");
                        location.reload();
                    }else
                    {
                        alertPop(returnData.message);
                    }
                }
            });

        })
        //获取dom文本
        var getText = function( el ){
            if($(el).find("input").attr('flage') == 1)
            {
                return $(el).find("input").val();
            }

        };


    });
})
</script>
        <?php endif;?>
  </div>
</div>
</body>
</html>