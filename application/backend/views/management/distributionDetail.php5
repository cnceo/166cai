<?php $this->load->view("templates/head") ?>
<div class="frame-container" style="margin-left:0;">
    <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/checkDistribution/">派奖核对</a>&nbsp;&gt;&nbsp;<a
            href="/backend/Management/distributionDetail/">核对详情页</a></div>
    <div class="mt10">
        <div class="data-table-filter" style=" width: 100%;">
            <form action="/backend/Management" method="get" id="search_form">
                <table>
                    <colgroup>
                        <col width="62">
                        <col width="262">
                        <col width="62">
                        <col width="286">
                        <col width="62">
                        <col width="248">
                    </colgroup>
                </table>
            </form>
        </div>
        <div class="data-table-list mt10">
            <table>
                <colgroup>
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
                    <col width="20%">
                </colgroup>
                <thead>
                <tr>
                    <th>开奖信息不一致订单号</th>
                    <th>2345税前奖金（元）</th>
                    <th>票商税前奖金（元）</th>
                    <th>2345税后奖金（元）</th>
                    <th>票商税后奖金（元）</th>
                </tr>
                </thead>
                <?php if ($orders): ?>
                    <tbody>
                    <?php foreach ($orders as $row): ?>
                        <tr data-issue="<?php echo $row['orderId']; ?>">
                            <td><?php echo $row['orderId']; ?></td>
                            <td><?php echo m_format($row['bonus']); ?></td>
                            <td><?php echo m_format($row['ticketBonus']); ?></td>
                            <td><?php echo m_format($row['margin']); ?></td>
                            <td><?php echo m_format($row['ticketMargin']); ?></td>
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
            <a href="/backend/Management/distributionDetail/?lid=<?php echo $lotteryId; ?>&issue=<?php echo $issue; ?>&isCsv=1"
               class="btn-blue">全部导出</a>
        </div>
    </div>
</div>
<div class="page mt10 login_info">
    <?php echo $pages[0] ?>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<div class="pop-dialog" id="dialog-startissue" style='display:none;'>
    <div class="pop-in">
        <div class="pop-body">
            <p>请确认开启</p>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:bjdcStart();" class="btn-blue-h32 mlr15">确认</a>
            <a href="javascript:closePop();" class="btn-b-white mlr15">取消</a>
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

    $("tr").click(function () {
        $("tr").removeClass("select");
        $(this).addClass("select");
        $("#selectId").val($(this).attr("data-issue"));
    });
    var issue;
    $("#startIssue").click(function () {
        issue = $("#selectId").val();
        if (!issue) {
            alert('请先选择一次比赛');
            return false;
        }
        else {
            popdialog("dialog-startissue");
        }
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
