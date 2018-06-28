<?php $this->load->view("templates/head") ?>
<div class="frame-container" style="margin-left:0;">
    <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/manageKind/">彩种管理</a></div>
    <div class="mod-tab-hd mt20">
        <ul>
            <li><a href="/backend/Management/manageKind/">彩种管理</a></li>
            <li class="current"><a href="/backend/Management/manageHemai/">合买彩种管理</a></li>
        </ul>
    </div>
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
                </colgroup>
                <thead>
                <tr>
                    <th>彩种</th>
                    <th>销售状态</th>
                    <th>合买提前截止时间（分钟）</th>
                    <th>操作</th>
                </tr>
                </thead>
                <?php if ($configItems): ?>
                    <tbody>
                    <?php foreach ($configItems as $row): ?>
                        <tr data-issue="<?php echo $row['lotteryId']; ?>"
                            data-id="<?php echo in_array($row['lotteryId'], array(BetCnName::RJ, BetCnName::SFC))
                                ? '胜负彩/任九' : BetCnName::$BetCnName[$row['lotteryId']]; ?>"
                            data-status="<?php echo $row['united_status']; ?>"
                            data-ahead="<?php echo $row['united_ahead']; ?>">
                            <?php if(in_array($row['lotteryId'], array(BetCnName::RJ, BetCnName::SFC))){ ?>
                            <td>胜负彩/任九</td>
                            <?php }elseif(in_array($row['lotteryId'], array(BetCnName::PLS, BetCnName::PLW))){ ?>
                            <td>排列3/排列5</td>
                            <?php }else{ ?>
                            <td><?php echo BetCnName::$BetCnName[$row['lotteryId']]; ?></td>
                            <?php } ?>
                            <td><?php if ($row['united_status']) {
                                    echo "开售";
                                }
                                else {
                                    echo "停售";
                                } ?></td>
                            <td><?php echo $row['united_ahead']; ?></td>
                            <td><a href="javascript:void(0)" class="btn-blue modify">修改</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<form id="modifyForm" method="post" action="/backend/Management/configUnitedLottery">
    <!-- 修改 start -->
    <div class="pop-dialog" id="modifyPop">
        <div class="pop-in">
            <div class="pop-head">
                <h2>合买彩种管理修改</h2>
            </div>
            <div class="pop-body">
                <div class="data-table-filter del-percent">
                    <input type="hidden" id="lotteryId" name="id" value="">
                    <table>
                        <colgroup>
                            <col width="68"/>
                            <col width="350"/>
                        </colgroup>
                        <tbody>
                        <tr>
                            <th>彩种:</th>
                            <td><input type="text" id="lotteryName" value=""></td>
                        </tr>
                        <tr>
                            <th>销售状态：</th>
                            <td>
                                <select id="united_status" name="united_status">
                                    <option value="1">开售</option>
                                    <option value="0">停售</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>合买提前截止时间：</th>
                            <td><input type="text" value="" class="ipt w222" id="united_ahead" name="united_ahead"></td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:void(0)" class="btn-blue-h32" id="modifySubmit">确认</a>
                <a href="javascript:closePop();" class="btn-blue-h32">取消</a>
            </div>
        </div>
    </div>
    <!-- 修改 end -->
</form>

<script>
    $(function () {
        var issue;

        $("tr").click(function () {
            $("tr").removeClass("select");
            $(this).addClass("select");
            if ($(this).attr("data-issue") != null && $(this).attr("data-id") != null) {
                $("#lotteryId").val($(this).attr("data-issue"));
                $("#lotteryName").val($(this).attr("data-id"));
                $("#united_status").val($(this).data('status'));
                var $window = $("#windowId");
                if ($(this).attr("data-window") != '0') {
                    $window.val($(this).attr("data-window"));
                    $window.attr("disabled", false);
                }
                else {
                    $window.val($(this).attr("data-window"));
                    $window.attr("disabled", "disabled");
                }
                $("#united_ahead").val($(this).attr("data-ahead"));
            }
        });
        $(".modify").click(function () {
            popdialog("modifyPop");
        });
        $("#modifySubmit").click(function () {
            closePop();
            var id = $("#lotteryId").val();
            var ahead = $("#united_ahead").val();
            var status = $("#united_status  option:selected").val();
            $.ajax({
                type: "post",
                url: "/backend/Management/configUnitedLottery",
                data: {
                    'id': id,
                    'ahead': ahead,
                    'status': status
                },
                dataType: "json",
                success: function (resp) {
                    if (resp.ok) {
                        location.reload();
                    }
                    else {
                        alert(resp.message);
                    }
                }
            })
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
    })
</script>