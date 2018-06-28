<?php $this->load->view("templates/head"); ?>
<div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/manageMatch/">对阵管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li class="current"><a href="/backend/Management/manageMatch/?type=bjdc">北京单场</a></li>
            <li><a href="/backend/Management/manageMatch/?type=bdsfgg">北单胜负过关</a></li>
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
                <input type="hidden" name="curSaleStatus" id="curSaleStatus" value=""/>
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
                    <col width="10%"/>
                    <col width="10%"/>
                    <col width="5%"/>
                    <col width="5%"/>
                    <col width="5%"/>
                    <col width="5%"/>
                    <col width="5%"/>
                    <col width="5%"/>
                    <col width="5%"/>
                    <col width="5%"/>
                    <col width="5%"/>
                    <col width="5%"/>
                    <col width="5%"/>
                    <col width="8%"/>
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
                    <th>销售状态</th>
                    <th>赛事状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php if ($result): ?>
                    <?php foreach ($result as $row): ?>
                        <tr id="<?php echo $row['id']; ?>" saleStatus="<?php echo $row['statusInfo'];?>"
                        data-sid="<?php echo $row['sale_status']; ?>">
                            <td><?php echo $row['mname']; ?></td>
                            <td><?php echo $row['showEndTime']; ?></td>
                            <td><?php echo $row['home']; ?></td>
                            <td><?php echo $row['away']; ?></td>
                            <td><?php echo $row['rq']; ?></td>
                            <td><?php if ($row['m_status'] == 0): echo $row['half_score'];
                                else: echo '----'; endif; ?></td>
                            <td><?php if ($row['m_status'] == 0): echo $row['full_score'];
                                else: echo '----'; endif; ?></td>
                            <td><?php echo $row['spf_odds']; ?></td>
                            <td><?php echo $row['dcbf_odds']; ?></td>
                            <td><?php echo $row['bqc_odds']; ?></td>
                            <td><?php echo $row['dss_odds']; ?></td>
                            <td><?php echo $row['jqs_odds']; ?></td>
                            <td><?php if ($row['m_status'] == 0): echo "否";
                                else: echo "是"; endif; ?></td>
                            <td><?php echo $saleStatusStrMap[$row['sale_status']]; ?></td>
                            <td><?php echo $row['statusInfo']; ?></td>
                            <td></td>
                            <!-- <td><a onclick="" href="javascript:void(0);" class="btn-blue">重算奖金</a></td> -->
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="pop-mask" style="display:none;width:200%"></div>
<form id='modifyForm' method='post' action=''>
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
                            <td style="text-align:center;font-weight: bold;">请确认北单官网对该场次玩法进行停售</td>
                        </tr>
                        </tfoot>
                        <thead id="thead2">
                        </thead>
                        <tbody id="tbody2">
                        <div style="text-align:center;">
                            <?php if ($saleStatusName && $saleStatus): ?>
                                <?php $fields = array('spf', 'bqc', 'jqs', 'sxds', 'bf'); ?>
                            <div>
                            <?php for ($i = 0, $count = count($fields), $half = floor($count / 2); $i < $count; $i ++): ?>

    					<span>
                            <input name="selectVal" type="checkbox"
                                    id="checkbox<?php echo $i; ?>"
                                    checked=""
                                   value="<?php echo $saleStatus[$fields[$i]]; ?>"
                                   style="vertical-align:text-top;"/>
                                    <?php echo $saleStatusName[$fields[$i]]; ?>
    					</span>
                                <?php if ($i == $half - 1): ?>
    					</div>
    					</br>
    					<div>
                            <?php endif; ?>
                                <?php endfor; ?>
    					</div>
                            <?php endif; ?>
                        </div>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pop-foot tac">
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="modifySubmit">确认</a>
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
            window.location.href = "/backend/Management/manageMatch?mid=" + mid;
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

        $("#score").click(function () {
            var id = $("#selectId").val();
            var status = $("#statusId").val();
            if (!id) {
                alertPop('请选中要修改的比赛场次');
                return false;
            }
            if(status != '在售'){
            	alertPop('只有对在售中的赛事进行修改');
                return false;
            }
            var title = '修改赛事销售状态';
            $("#pop_name2").html(title);
            $("#scoreId").val(id);
            popdialog("scorePop");
            
            var obj = document.getElementsByName('selectVal');
            var curSaleStatus = $("#curSaleStatus").val();
            for (var i = 0, len = obj.length; i < len; i++) {
                if (curSaleStatus & parseInt(obj[i].value)) {
                    $('#checkbox' + i).prop('checked', true);
                } else {
                    $('#checkbox' + i).prop('checked', false);
                }
            }
        });

        $("#modifySubmit").click(function () {
            var obj = document.getElementsByName('selectVal');
            var sum = 0;
            for (var i = 0; i < obj.length; i++) {
                if (obj[i].checked) {
                    sum += parseInt(obj[i].value);
                }
            }
            closePop();
            var mid = $('#mid').children('option:selected').val();
            var type = 'bjdc';
            var id = $("#selectId").val();
            var td = $("#" + id).children("td").siblings("td");
            var ssid = td.eq(0).html();
            $.ajax({
                type: "post",
                //预留接口
                url: '/backend/Management/updateMatchSaleStatus',
                data: {
                    'mid': mid,
                    'status': sum,
                    'type': type,
                    "id": id,
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
        function recalculateAlert() {
//     	$('#confirmRecalSubmit').attr('href','javascript:recalculate(\''+issue+'\',\''+type+'\');'); 
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