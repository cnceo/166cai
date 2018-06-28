<?php $this->load->view("templates/head") ?>
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
                            <select class="selectList w222" id="selectType" name=""
                                    onchange="window.location.href=this.options[selectedIndex].value">
                                <?php foreach ($lrule as $l => $types): ?>
                                    <option <?php if ($search['type'] == $l): ?>selected<?php endif; ?>
                                            value="/backend/Management/manageIssue/?type=<?php echo $l; ?>"><?php echo $types['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type='hidden' class='vcontent' name='type' value='<?php echo $search['type']; ?>'/>
                            <a href="javascript:void(0);" class="btn-blue" id="endIssue">完结期次</a>
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
                    <col width="10%">
                    <col width="5%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="15%">
                    <col width="10%">
                    <col width="15%">
                </colgroup>
                <thead>
                <tr>
                    <th>期号</th>
                    <th>状态</th>
                    <th>开始时间</th>
                    <th>截止时间</th>
                    <th>开奖号码</th>
                    <th>开奖详情</th>
                    <th>是否派奖</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php if ($result): ?>
                    <tbody>
                    <?php foreach ($result as $row): ?>
                        <tr data-issue="<?php echo $row['issue']; ?>" data-status="<?php echo $row['status']; ?>">
                            <td><?php echo $row['issue']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td><?php echo $row['sale_time']; ?></td>
                            <td><?php echo $row['showEndTime']; ?></td>
                            <td><?php echo $row['awardNum']; ?></td>
                            <?php if ($row['rstatus'] == 2): ?>
                                <td>详情核对异常</td>
                            <?php elseif ($row['rstatus'] >= $this->order_status['paiqi_complete'] || ! empty($row['bonusDetail'])): ?>
                                <td>
                                    <a href="/backend/Management/detail?lid=<?php echo $search['type']; ?>&issue=<?php echo $row['issue']; ?>">查看详情</a>
                                </td>
                            <?php else: ?>
                                <td></td>
                            <?php endif; ?>
                            <td><?php echo $row['awardInfo']; ?></td>
                            <td><?php if ($row['status'] == '已过关' || $row['rstatus'] == $this->order_status['paiqi_jjsucc']): ?>
                                    <a
                                    onclick="javascript:recalculateAlert(<?php echo "'" . $row['issue'] . "','" . $search['type'] . "'"; ?>);"
                                    href="javascript:void(0);" class="btn-blue">重算奖金</a><?php endif; ?></td>
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
</div>
<div class="page mt10 login_info">
    <?php echo $pages[0] ?>
</div>

<!-- 弹出层 -->
<div class="pop-mask" style="display:none;width:200%"></div>
<form id="compareForm" method="post" action="">
</form>

<div class="pop-dialog" id="confirmPop">
    <div class="pop-in">
        <div class="pop-body">
            <div class="data-table-list">
                <table>
                    <div id="showAlert" style="text-align:center;font-size:20px;font-weight:bolder"></div>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15" id="closeSubmit">确认</a>
            <a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
            <input type="hidden" name="" id="closeId" value=""/>
        </div>
    </div>
</div>
<div class="pop-dialog" style="width:150px;" id="confirmRecalPop">
    <div class="pop-in">
        <div class="pop-body">
            <div class="data-table-list">
                <table>
                    <div id="showRecalAlert" style="text-align:center;font-size:20px;font-weight:bolder">请确认重算奖金</div>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:recalculate(issue,type);" class="btn-blue-h32 mlr15" id="confirmRecalSubmit">确认</a>
            <a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
            <input type="hidden" name="" id="closeRecalId" value=""/>
        </div>
    </div>
</div>
<script src="/source/date/WdatePicker.js"></script>
<script>
    $(function () {
        $(".Wdate1").focus(function () {
            dataPicker();
        });
    })

    var endFlag = false;
    $("tr").click(function () {
        $("tr").removeClass("select");
        $(this).addClass("select");
        $("#selectId").val($(this).attr("data-issue"));
        if ($(this).attr("data-status")) {
            if ($(this).attr("data-status") == '派奖中') {
                endFlag = true;
            }
            else {
                endFlag = false;
            }
        }

    });

    $("#modifyIssue").click(function () {
        var id = $("#selectId").val();
        if (!id) {
            alert('请先选择你要修改的比赛');
            return false;
        }
        var type = $("input[name='type']").val();
        window.location.href = '/backend/Management/modifyIssueDetail?lid=' + type + '&issue=' + id;
    });
    $("#endIssue").click(function () {
        var id = $("#selectId").val();
        if (!id) {
            alert('请先选择你要完结的期次');
            return false;
        }
        if (!endFlag) {
            alert('只能对派奖中的期次进行完结');
            return false;
        }
        $("#closeId").val(id);
        var type = $("#selectType").find("option:selected").text();
        $("#closeId").attr('name', type);

        $("#showAlert").html("请确认完结" + type + id + "期次");
        popdialog("confirmPop");
    });

    $("#closeSubmit").click(function () {
        closePop();
        var id = $("#closeId").val();
        if (!id) {
            location.reload();
            alert('请先选择你要完结的期次');
            return false;
        }
        var type = $("input[name='type']").val();
        $.ajax({
            type: 'POST',
            url: '/backend/Management/closeIssue',
            data: {
                'type': type,
                'issue': id
            },
            dataType: 'json',
            success: function (resp) {
                alert(resp.message);
                if (resp.ok) {
                    location.reload();
                }
            }
        });
    });

    function recalculateAlert(issue, type) {
        $('#confirmRecalSubmit').attr('href', 'javascript:recalculate(\'' + issue + '\',\'' + type + '\');');
        popdialog("confirmRecalPop");
    }

    function recalculate(issue, type) {
        closePop();
        $.ajax({
            type: 'POST',
            url: '/backend/Management/calculateAward',
            data: {
                'type': type,
                'issue': issue
            },
            dataType: 'json',
            success: function (resp) {
                alert(resp.message);
                if (resp.ok) {
                    location.reload();
                }
            }
        });
    }

    //     $(".compare-detail").click(function () {
    //         var issue = $(this).attr("data-issue");
    //         var type = $("input[name='type']").val();
    //         $.ajax({
    //             type: "post",
    //             url: '/backend/Issue/compareDetail',
    //             data: {type: type, issue: issue},
    //             success: function (data) {
    //                 if (data == '2') {
    //                     alert('获取不到任何开奖抓取信息');
    //                 } else {
    //                     $('#compareForm').html(data);
    //                     popdialog("idetail");
    //                 }
    //             }
    //         });
    //     });
</script>
</body>
</html>
