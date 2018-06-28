<?php $this->load->view("templates/head") ?>
<div class="frame-container" style="margin-left:0;">
    <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/checkDistribution/">派奖核对</a></div>
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
                    <col width="8%">
                    <col width="8%">
                    <col width="12%">
                    <col width="8%">
                    <col width="12%">
                    <col width="12%">
                    <col width="12%">
                    <col width="12%">
                    <col width="16%">
                </colgroup>
                <thead>
                <tr>
                    <th>彩种</th>
                    <th>期次</th>
                    <th>网站销售总额</th>
                    <th>出票总额</th>
                    <th>网站税前奖金</th>
                    <th>出票税前奖金</th>
                    <th>网站税后奖金</th>
                    <th>出票税后奖金</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php if ($checkItems): ?>
                    <tbody>
                    <?php foreach ($checkItems as $row): ?>
                        <tr data-issue="<?php echo $row['issue']; ?>" data-lotteryId="<?php echo $row['lotteryId']; ?>">
                            <td><?php echo BetCnName::$BetCnName[$row['lotteryId']]; ?></td>
                            <td><?php echo $row['issue']; ?></td>
                            <td><?php echo m_format($row['totalSales']); ?></td>
                            <td><?php echo m_format($row['totalTicket']); ?></td>
                            <td><?php echo m_format($row['bonus']); ?></td>
                            <td><?php echo m_format($row['ticketBonus']); ?></td>
                            <td><?php echo m_format($row['margin']); ?></td>
                            <td><?php echo m_format($row['ticketMargin']); ?></td>
                            <!--双色球、大乐透、七乐彩、七星彩、排三、排五、福彩3D 去掉执行派奖-->
                            <td><?php if (in_array($row['lotteryId'], array('41', '53', '56', '57', '21406', '21407', '21408', '54', '55', '51', '23529', '10022','23528', '52', '33', '35', '21421'))): ?>
                                    <a href="/backend/Management/distributionDetail/?lid=<?php echo $row['lotteryId']; ?>&issue=<?php echo $row['issue']; ?>"
                                       class="btn-blue" id="">查看详情</a>
                                    <a href="javascript:fakeDistribution(<?php echo $row['issue'] . "," . $row['lotteryId'] ?>);"
                                       class="btn-blue fakeDistribution">确认</a>
                                <?php else: ?>
                                    <a href="/backend/Management/distributionDetail/?lid=<?php echo $row['lotteryId']; ?>&issue=<?php echo $row['issue']; ?>"
                                       class="btn-blue" id="">查看详情</a>   
                                    <a href="javascript:forceDistribution(<?php echo $row['issue'] . "," . $row['lotteryId'] ?>);"
                                       class="btn-blue forceDistribution">执行派奖</a>
                                <?php endif; ?>
                            </td>
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
<input type="hidden" name="" id="selectIssue" value=""/>
<input type="hidden" name="" id="selectLotteryId" value=""/>
<div class="pop-mask" style="display:none;width:200%"></div>

<div class="pop-dialog" id="forceDistributionPop" style="width:200">
    <div class="pop-in">
        <div class="pop-body">
            <div class="data-table-list">
                <table>
                    <div id="showAlert" style="text-align:center;font-size:20px;font-weight:bolder"></div>
                </table>
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-blue-h32 mlr15" id="forceDistSubmit">确认</a>
            <a href="javascript:;" class="btn-b-white mlr15 pop-cancel" id="forceDistCancel">取消</a>
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
        $("#selectIssue").val($(this).attr("data-issue"));
        $("#selectLotteryId").val($(this).attr("data-lotteryId"));
    });
    var issue;

    function forceDistribution(issue, lotteryId) {
        $("#selectIssue").val(issue);
        $("#selectLotteryId").val(lotteryId);
        if (!issue) {
            alert('请先选择执行派奖期次');
            return false;
        }
        $("#showAlert").html("请谨慎执行派奖工作");
        popdialog("forceDistributionPop");
    }

    function fakeDistribution(issue, lotteryId) {
        var id = $("#selectIssue").val();
        var lotteryId = $("#selectLotteryId").val();
        if (!issue) {
            alert('请先选择期次');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '/backend/Management/fakeDistribution',
            data: {
                'lid': lotteryId,
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
    }

    $("#forceDistSubmit").click(function () {
        var id = $("#selectIssue").val();
        var lotteryId = $("#selectLotteryId").val();
        if (!id) {
            location.reload();
            alert('请先选择执行派奖期次');
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '/backend/Management/forceDistribution',
            data: {
                'lid': lotteryId,
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
