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
                            <a href="javascript:void(0);" class="btn-blue" id="closeIssue">完结期次</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="data-table-list mt10">
            <table>
                <colgroup>
                    <col width="15%">
                    <col width="15%">
                    <col width="25%">
                    <col width="25%">
                    <col width="20%">
                </colgroup>
                <thead>
                <tr>
                    <th>期号</th>
                    <th>状态</th>
                    <th>开始时间</th>
                    <th>截止时间</th>
                    <th>是否派奖</th>
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
                            <td><?php echo $row['awardInfo']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <input type="hidden" name="selectId" id="selectId" value=""/>
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
        if($(this).attr("data-status"))
        {
            if($(this).attr("data-status")=='派奖中') 
            {
            	endFlag = true;
            }
            else
            {
            	endFlag = false;
            }
        }

    });

    $("#closeIssue").click(function () {
        var id = $("#selectId").val();
        if (!id) {
            alert('请先选择你要完结的期次');
            return false;
        }
        if(!endFlag)
        {
            alert('只能对派奖中的期次进行完结');
            return false;
        }
        $("#closeId").val(id);
        var type = $("#selectType").find("option:selected").text();
        $("#closeId").attr('name', type);
        $("#showAlert").html("请确认完结" + type + id + "期次");
        popdialog("confirmPop");
    });

    $("#closeSubmit").click(function(){
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
                if (resp.ok) {
                    location.reload();
                }
                else {
                    alert(resp.message);
                }
            }
        });
    });
    
    function bjdcStart() {
        $.ajax({
            type: "post",
            url: "/backend/Issue/bjdcStart",
            data: {'issues': issue},
            dataType: "text",
            success: function (data) {
                if (data == true) {
                    location.href = location.href;
                }
                else {
                    closePop();
                    alert('操作失败');
                }
            }
        })
    }
</script>
</body>
</html>
