<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/manageMatch/">对阵管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/Management/manageMatch/?type=bjdc">北京单场</a></li>
            <li class="current"><a href="/backend/Management/manageMatch/?type=bdsfgg">北单胜负过关</a></li>
            <li><a href="/backend/Management/manageMatch/?type=tczq">老足彩</a></li>
            <li><a href="/backend/Management/manageMatch/?type=jczq">竞彩足球</a></li>
            <li><a href="/backend/Management/manageMatch/?type=jclq">竞彩篮球</a></li>
        </ul>
    </div>
    <div>
        <div class="data-table-filter">
            <table>
                <tbody>
                <tr>
                    <td colspan="14">
                        期次编号：
                        <select class="selectList w222" id="mid" name="mid">
                            <?php foreach ($mids as $mid): ?>
                                <option
                                    value="<?php echo $mid; ?>" <?php if ($search['mid'] === "{$mid}"): echo "selected"; endif; ?>><?php echo $mid; ?></option>
                            <?php endforeach; ?>
                        </select>
                <span style="margin-left: 120px;">
                <a href="javascript:void(0);" class="btn-blue" id="score">修改赛事销售状态</a>
                <input type="hidden" name="selectId" id="selectId" value=""/>
                <input type="hidden" name="statusId" id="statusId" value=""/>
                </span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="data-table-list mt10">
            <table>
                <colgroup>
                    <col width="5%"/>
                    <col width="12%"/>
                    <col width="5%"/>
                    <col width="12%"/>
                    <col width="12%"/>
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="8%"/>
                    <col width="6%"/>
                    <col width="5%"/>
                    <col width="5%"/>
                    <col width="10%"/>
                </colgroup>
                <thead>
                <tr>
                    <th>赛事编号</th>
                    <th>截止时间</th>
                    <th>赛事类型</th>
                    <th>主队</th>
                    <th>客队</th>
                    <th>让球数</th>
                    <th>全场比分</th>
                    <th>赔率</th>
                    <th>是否取消</th>
                    <th>销售状态</th>
                    <th>赛事状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($result as $row): ?>
                    <tr id="<?php echo $row['id']; ?>" saleStatus="<?php echo $row['statusInfo'];?>">
                        <td><?php echo $row['mname']; ?></td>
                        <td><?php echo $row['showEndTime']; ?></td>
                        <td><?php echo $row['game_type']; ?></td>
                        <td><?php echo $row['home']; ?></td>
                        <td><?php echo $row['away']; ?></td>
                        <td><?php echo $row['rq']; ?></td>
                        <td><?php if ($row['m_status'] == 0): echo $row['full_score'];
                            else: echo '----'; endif; ?></td>
                        <td><?php echo $row['sfgg_odds']; ?></td>
                        <td><?php if ($row['m_status'] == 0): echo "否";
                            else: echo "是"; endif; ?></td>
                        <td><?php echo $saleStatusStrMap[$row['sale_status']]; ?></td>
                        <td><?php echo $row['statusInfo']; ?></td>
                        <td></td>
                        <!--<td><a onclick="javascript:recalculateAlert();" href="javascript:void(0);" class="btn-blue">重算奖金</a></td>-->
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
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
                        <tfoot>
                        <tr>
                            <td id="alertContent" style="text-align:center;font-weight: bold;font-size:15px"></td>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="scoreSubmit">确认</a>
                <a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
            </div>
        </div>
    </div>
    <input type="hidden" value="" name="scoreId" id="scoreId"/>
</form>
<div class="pop-dialog" id="alertPop">
    <div class="pop-in">
        <div class="pop-head">
            <h2>提示</h2>
            <span class="pop-close" title="关闭">关闭</span>
        </div>
        <div class="pop-body">
            <div class="data-table-filter del-percent" id="alertBody" style="text-align: center">
            </div>
        </div>
        <div class="pop-foot tac">
            <a href="javascript:;" class="btn-b-white mlr15 pop-cancel">确认</a>
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
            <a href="javascript:closePop;" class="btn-b-white mlr15 pop-cancel">取消</a>
            <input type="hidden" name="" id="closeRecalId" value=""/>
        </div>
    </div>
</div>

<script>
    $(function () {
        //排期编号事件
        $('#mid').change(function () {
            var mid = $(this).children('option:selected').val();
            window.location.href = "/backend/Management/manageMatch?type=bdsfgg&mid=" + mid;
        });
        $("tr").click(function () {
            $("tr").removeClass("select");
            $(this).addClass("select");
            if($(this).attr("id")) $("#selectId").val($(this).attr("id"));
            if ($(this).attr("saleStatus")) $("#statusId").val($(this).attr("saleStatus"));
            var curSaleStatus = $(this).data('sid');
            if (curSaleStatus)
            {
                $("#curSaleStatus").val(curSaleStatus);
            }
        });

//        $("tr").click(function () {
//            $("tr").removeClass("select");
//            $(this).addClass("select");
//            $("#selectId").val($(this).attr("id"));
//            if ($(this).attr("saleStatus")) $("#statusId").val($(this).attr("saleStatus"));
//        });

        $("#score").click(function () {
            var id = $("#selectId").val();
            var status = $("#statusId").val();
            if (!id) {
                alertPop('请选中要修改的比赛场次');
                return false;
            }
            if(status != "在售")
            {
            	alertPop('只有对在售中的赛事进行修改');
                return false;
            }
            var title = '修改赛事销售状态';
            var td = $("#" + id).children("td").siblings("td");
            var num = td.eq(0).html();
            $("#pop_name2").html(title);
            $("#alertContent").html("请确认北单官网对该场次" + num + "胜负过关玩法进行停售");
            $("#scoreId").val(id);
            popdialog("scorePop");
        });

        $("#scoreSubmit").click(function () {
            closePop();
            var status = 0;
            var type = 'bdsfgg';
            var mid = $('#mid').children('option:selected').val();
            var id = $("#selectId").val();
            var td = $("#" + id).children("td").siblings("td");
            var ssid = td.eq(0).html();
            $.ajax({
                type: "post",
                //预留接口
                url: '/backend/Management/updateMatchSaleStatus',
                data: {
                    'status': status,
                    'type': type,
                    "id": id,
                    "mid":mid,
                    "ssid":ssid,
                    'env': '<?php echo ENVIRONMENT?>'
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
                    'issue': issue,
                    'env': '<?php echo ENVIRONMENT?>'
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

        //重写提示框
        function alertPop(content) {
            $("#alertBody").html(content);
            popdialog("alertPop");
        }
    })
</script>
</body>
</html>