<?php $this->load->view("templates/head") ?>
<?php 
    $awardtype = array(
        'S' => '黑桃',
        'H' => '红桃',
        'C' => '梅花',
        'D' => '方块'
    );
?>
<div class="frame-container" style="margin-left:0;">
    <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/manageIssue">期次管理</a></div>
    <div class="mt10">
        <div class="data-table-filter" style=" width: 100%;">
            <form action="/backend/Management/manageIssue" method="get" id="search_form">
                <table>
                    <colgroup>
                        <col width="62">
                        <col width="262">
                        <col width="62">
                        <col width="286">
                        <col width="62">
                        <col width="248">
                    </colgroup>
                    <tbody>
                    <tr>
                        <td colspan="9">
                            彩种：
                            <select class="selectList w222" id="" name=""
                                    onchange="window.location.href=this.options[selectedIndex].value">
                                <?php foreach ($lrule as $l => $types): ?>
                                    <option <?php if ($search['type'] == $l): ?>selected<?php endif; ?>
                                            value="/backend/Management/manageIssue/?type=<?php echo $l; ?>"><?php echo $types['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                                <span class="ipt ipt-date w184"><input type="text" name='start_time'
                                                                       value="<?php echo $search['start_time'] ?>"
                                                                       class="Wdate1"><i></i></span>
                                <span class="ml8 mr8">至</span>
                                <span class="ipt ipt-date w184"><input type="text" name='end_time'
                                                                       value="<?php echo $search['end_time'] ?>"
                                                                       class="Wdate1"><i></i></span>
                            <input type='hidden' class='vcontent' name='type' value='<?php echo $search['type']; ?>'/>
                            <a onclick="" href="javascript:void(0);" class="btn-blue" id="search">查询</a>
                            <!-- <a href="javascript:void(0);" class="btn-blue" id="modifyIssue">开启期次</a> -->
                            <!-- <a href="javascript:void(0);" class="btn-blue" id="deleteIssue">删除期次</a> -->
                            <input type="hidden" name="selectId" id="selectId" value=""/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="data-table-list mt10">
            <table>
                <colgroup>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="20%"/>
                    <col width="20%"/>
                    <col width="20%"/>
                    <col width="10%"/>
                </colgroup>
                <thead>
                <tr>
                    <th><input type="checkbox" class="_ck">全选</th>
                    <th>期号</th>
                    <th>状态</th>
                    <th>开始时间</th>
                    <th>截止时间</th>
                    <th>开奖号码</th>
                    <th>是否派奖</th>
                </tr>
                </thead>
                <?php if ($result): ?>
                    <tbody>
                    <?php foreach ($result as $row): ?>
                        <tr data-issue="<?php echo $row['issue']; ?>">
                            <td>
                                <?php if ($row['compare_status'] < 50): ?>
                                    <input type="checkbox" class="ck_" value="<?php echo $row['issue']; ?>">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $row['issue']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><?php echo $row['sale_time']; ?></td>
                            <td><?php echo $row['showEndTime']; ?></td>
                            <td>
                                <?php
                                    $awardNum = '';
                                    if(!empty($row['awardNum']))
                                    {
                                        $awardArr = explode('|', $row['awardNum']);
                                        $numArr = explode(',', $awardArr[0]);
                                        $typeArr = explode(',', $awardArr[1]);
                                        $awardNum .= $awardtype[$typeArr[0]] . $numArr[0] . ',' . $awardtype[$typeArr[1]] . $numArr[1] . ',' . $awardtype[$typeArr[2]] . $numArr[2];
                                    }
                                    echo $awardNum;
                                ?>
                            </td>
                            <td><?php echo $row['awardInfo']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="5">
                            <div class="stat">
                                <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
                                <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
                                <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
    <div class="page mt10 login_info">
    <?php echo $pages[0] ?>
    </div>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>

<div class="pop-dialog" id="dialog-issuedelete" style='display:none;'>
    <div class="pop-in">
        <div class="pop-body">
            <p id="deleteAlert" style="text-align:center;font-size:20px;font-weight:bolder">请确认删除</p>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:void(0);" class="btn-blue-h32 mlr15" id="deleteConfirm">确认</a>
            <a href="javascript:closePop();" class="btn-b-white mlr15">取消</a><br/><br/>
            <p style = "color:red">请谨慎删除期次</p>
        </div>
    </div>
</div>
<div class="pop-dialog" id="alertPop">
	<div class="pop-in">
		<div class="pop-head">
			<h2>提示</h2>
			<span class="pop-close" title="关闭">关闭</span>
		</div>
		<div class="pop-body">
			<div class="data-table-filter del-percent" id="alertBody" style="text-align:center">
			</div>
		</div>
		<div class="pop-foot tac">
			<a href="javascript:;" class="btn-b-white mlr15 pop-cancel">确认</a>
		</div>
	</div>
</div>
<script src="/source/date/WdatePicker.js"></script>
<script>
	var type = '<?php echo $search['type'];?>';
    $(function () {
        $(".Wdate1").focus(function () {
            dataPicker();
        });
    })
    
    $("#search").click(function(){
		var start = $("input[name='start_time']").val();
		var end = $("input[name='end_time']").val();
		if(start > end){
			alertPop('您选择的时间段错误，请核对后操作');
			return false;
		}
		$('#search_form').submit();
	});
	
    var issues;
    $("#deleteIssue").click(function () {
        issues = [];
        $(".ck_").each(function () {
            if (this.checked) {
                issues.push($(this).val());
            }
        });
        if (issues.length < 1) {
            alert('请先选择你要删除的期次');
            return false;
        }
        else {
            $("#deleteAlert").html("请确认删除快乐扑克"+issues+"期次");
            popdialog("dialog-issuedelete");
        }
    });

    $("#deleteConfirm").click(function(){
        $.ajax({
            type: "POST",
            url: "/backend/Management/deleteIssue",
            data: {'issues': issues, 'type':type},
            dataType: "text",
            success: function (data) {
                if (data == true) {
                    location.href = location.href;
                }
                else {
                    alert(issues+"期操作失败 "+data);
                }
            }
        })
    });
    function alertPop(content){
		$("#alertBody").html(content);
		popdialog("alertPop");
	}
    
//     function issueDelete()
//     {
//     	$.ajax({
//     	    type: "post",
//     	    url: "/backend/Management/deleteIssue",
//     	    data: {'issues':issues},
//     	    dataType: "text",
//     	    success: function(data){
//     	        if(data == true) 
//     		    {
//     			    location.href = location.href;
//     			}
//     	        else
//     	        {
//     	        	closePop();
//     	        	alert('操作失败');
//     			}
//     	    }
//     	})
//     }

    $("._ck").click(function () {
        var self = this;
        $(".ck_").each(function () {
            if (self.checked) {
                $(this).attr("checked", true);
            }
            else {
                $(this).attr("checked", false);
            }
        });
    });
</script>
</body>
</html>
  