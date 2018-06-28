<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/manageMatch/">对阵管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/Management/manageMatch/?type=bjdc">北京单场</a></li>
            <li><a href="/backend/Management/manageMatch/?type=bdsfgg">北单胜负过关</a></li>
            <li><a href="/backend/Management/manageMatch/?type=tczq">老足彩</a></li>
            <li class="current"><a href="/backend/Management/manageMatch/?type=jczq">竞彩足球</a></li>
            <li><a href="/backend/Management/manageMatch/?type=jclq">竞彩篮球</a></li>
        </ul>
    </div>
    <div>
        <div class="data-table-filter">
            <form action="/backend/Management/manageMatch/?type=jczq" method="post" id="search_form">
                <table>
                    <tbody>
                    <tr>
                        <td colspan="6">
                            选择时间：
                            <span class="ipt ipt-date w184"><input type="text" name='start_time'
                                                                   value="<?php echo $search['start_time'] ?>"
                                                                   class="Wdate1"><i></i></span>
                            <span class="ml8 mr8">至</span>
                            <span class="ipt ipt-date w184"><input type="text" name='end_time'
                                                                   value="<?php echo $search['end_time'] ?>"
                                                                   class="Wdate1"><i></i></span>
                            <a id="search" href="javascript:void(0);" class="btn-blue">查询</a>
                <span style="margin-left: 20px;">
                <a href="javascript:void(0);" class="btn-blue" id="score">修改赛事销售状态</a>
                <input type="hidden" name="selectId" id="selectId" value=""/>
                <input type="hidden" name="statusId" id="statusId" value=""/>
                    <input type="hidden" name="curSaleStatus" id="curSaleStatus" value=""/>
                </span>
                            <a href="javascript:;" class="btn-blue btn-modify">保存热门赛事修改</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        </div>
        <div class="overflow-x">
            <div class="data-table-list mt10">
                <table>
                     <colgroup>
                        <col width="70" />
                        <col width="50" />
                        <col width="60" />
                        <col width="110" />
                        <col width="90" />
                        <col width="90" />
                        <col width="60" />
                        <col width="70" />
                        <col width="70" />
                        <col width="70" />
                        <col width="70" />
                        <col width="70" />
                        <col width="117">
                    </colgroup>
                    <thead>
                    <tr>
                        <th>热门赛事</th>
                        <th>优先级</th>
                        <th>赛事编号</th>
                        <th>截止时间</th>
                        <th>主队</th>
                        <th>客队</th>
                        <th>让球数</th>
                        <th>半场比分</th>
                        <th>全场比分</th>
                        <th>是否取消</th>
                        <th>销售状态</th>
                        <th>赛事状态</th>
                        <th>操作</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="data-table-list table-scroll">
                <table id="jczqTable">
                    <colgroup>
                        <col width="70" />
                        <col width="50" />
                        <col width="60" />
                        <col width="110" />
                        <col width="90" />
                        <col width="90" />
                        <col width="60" />
                        <col width="70" />
                        <col width="70" />
                        <col width="70" />
                        <col width="70" />
                        <col width="70" />
                        <col width="100">
                    </colgroup>
                    <tbody>
                    <?php foreach ($result as $row): ?>
                        <tr id="<?php echo $row['id']; ?>" saleStatus="<?php echo $row['statusInfo']; ?>"
                            data-sid="<?php echo $row['sale_status']; ?>" class = "tr-table">
                            <td>
                                <div class = "box">
                                    <input type="checkbox" name = "hot" class = "checkbox"  <?php if($row['hot'] != 0){echo 'checked = "checked" flages = "0"';}?>> 热门
                                </div>
                            </td>
                            <td>
                                <div class="table-modify">
                                <p class="table-modify-txt"><?php echo $row['hotid'];?><i></i></p>
                                <p class="table-modify-ipt"><input type="text" id="hotid" name="hotid" class="ipt"  value="<?php echo $row['hotid'];?>"  style="width: 3em;"><i></i></p>
                                </div>
                            </td>
                            <td><?php echo $row['mname']; ?></td>
                            <td><?php echo $row['showEndTime']; ?></td>
                            <td><?php echo $row['home']; ?></td>
                            <td><?php echo $row['away']; ?></td>
                            <td><?php echo $row['rq']; ?></td>
                            <td><?php if ($row['m_status'] == 0): echo $row['half_score'];
                                else: echo '----'; endif; ?></td>
                            <td><?php if ($row['m_status'] == 0): echo $row['full_score'];
                                else: echo '----'; endif; ?></td>
                            <td><?php if ($row['m_status'] == 0): echo "否";
                                else: echo "是"; endif; ?></td>
                            <td><?php echo $saleStatusStrMap[$row['sale_status']]; ?></td>
                            <td><?php echo $row['statusInfo']; ?></td>
                            <?php if ($row['statusInfo'] == '在售'): ?>
                                <td></td>
                            <?php else: ?>
                                <td><!-- <a onclick="javascript:recalculateAlert(<?php echo "'" . $row['mid'] . "'"; ?>);"
                                       href="javascript:void(0);" class="btn-blue">重算奖金</a> --></td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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
                            <td style="text-align:center;font-weight: bold;">请确认竞彩官方对该场次玩法进行停售</td>
                        </tr>
                        </tfoot>
                        <p id="thead2" style="font-size:14px" ;>
                        </p>
                        <tbody id="tbody2">
                        <div style="text-align:center;">
                            <?php if ($saleStatusName && $saleStatus): ?>
                                <?php $fields = array('spf', 'rqspf', 'bqc', 'jqs', 'bf'); ?>
                            <div>
                            <?php for ($i = 0, $count = count($fields), $half = floor($count / 2); $i < $count; $i ++): ?>
    					<span>
                            <input name="selectVal" type="checkbox"
                                    id="checkbox<?php echo $i; ?>"
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
            <a href="javascript:recalculate(mid);" class="btn-blue-h32 mlr15" id="confirmRecalSubmit">确认</a>
            <a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
            <input type="hidden" name="" id="closeRecalId" value=""/>
        </div>
    </div>
</div>

<script src="/source/date/WdatePicker.js"></script>
<script>
    $("#search").click(function () {
        var start = $("input[name='start_time']").val();
        var end = $("input[name='end_time']").val();
        if (start > end) {
            alertPop('您选择的时间段错误，请核对后操作');
            return false;
        }
        $('#search_form').submit();
    });

    $(".Wdate1").focus(function () {
        dataPicker();
    });

    $("tr").click(function () {
        $("tr").removeClass("select");
        $(this).addClass("select");
        if ($(this).attr("id")) {
            $("#selectId").val($(this).attr("id"));
        }
        if ($(this).attr("saleStatus")) {
            $("#statusId").val($(this).attr("saleStatus"));
        }
        var curSaleStatus = $(this).data('sid');
        $("#curSaleStatus").val(curSaleStatus);
    });

    $("#score").click(function () {
        var self = $(this);
        var id = $("#selectId").val();
        var status = $("#statusId").val();
        if (!id) {
            alertPop('请选中要修改的比赛场次');
            return false;
        }
        if (status != "在售") {
            alertPop('只有对在售中的赛事进行修改');
            return false;
        }
        var td = $("#" + id).children("td").siblings("td");
        var title1 = td.eq(2).html() + " " + td.eq(4).html() + " vs " + td.eq(5).html();
        $("#thead2").html(title1);
        var title2 = '修改赛事销售状态';
        $("#pop_name2").html(title2);
        $("#scoreId").val(id);
        popdialog("scorePop");
        $("input[name='selectVal']").each(function(){
			$(this).attr('checked', false);
        });
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
        var type = 'jczq';
        var id = $("#selectId").val();
        var td = $("#" + id).children("td").siblings("td");
        var mid = td.eq(2).html();
        $.ajax({
            type: "post",
            //预留接口
            url: '/backend/Management/updateMatchSaleStatus',
            data: {
                'status': sum,
                'type': type,
                "id": id,
                'mid':mid,
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

    function recalculateAlert(mid) {
        $('#confirmRecalSubmit').attr('href', "javascript:recalculate(\'" + mid + "\');");
        popdialog("confirmRecalPop");
    }

    function recalculate(mid) {
        closePop();
        $.ajax({
            type: 'POST',
            url: '/backend/Management/calculateMatchAward',
            data: {
                'type': 'jczq',
                'mid': mid,
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

    $('.table-modify-txt').on('click', function(){
        $(this).hide();
        $(this).parents('.table-modify').find('.table-modify-ipt').show();
        var ipt = $(this).parents('.table-modify').find('.table-modify-ipt');
		var flages = ipt.find('input').attr('flages','0');
    });
    $('.checkbox').on('click', function(){
        var check = $(this).is(':checked');
        if (check) {
            var id = $(this).parents('tr').find('input').attr('checked');
            var flages = $(this).parents('tr').find('input[name = hot]').attr('flages','1');
        }else{
            var id = $(this).parents('tr').find('input').removeAttr('checked');
            var flages = $(this).parents('tr').find('input[name = hot]').attr('flages','2');
        }
    })
    $('.btn-modify').on('click', function(){
        var tableModify= $(this).parents('.wrapper').find('.table-modify');
        tableModify.find('.table-modify-ipt').hide();
        tableModify.find('.table-modify-txt').show();
        var jsonArray = [];
        $(".tr-table").each(function(){
           var check = $(this).find(".checkbox").is(":checked");
           var hotflage = $(this).find('input[name = hot]').attr('flages');
		   var hotidflage = $(this).find('input[id = hotid]').attr('flages');
            if(check)
            {
                var arr = {
                    id : $(this).attr('id'),
                    hot: 1,
                    hotid : $(this).find("input[id = hotid]").attr('value')
                }
            }
            else
            {
                var arr = {
                    id : $(this).attr('id'),
                    hot: 0,
                    hotid : 0
                }
            }
            if(hotflage == 1 || hotflage == 2 || (hotflage == 0 && hotidflage == 0))
            {
                jsonArray.push(arr);
            }

        })
        var data = JSON.stringify(jsonArray);
        $.ajax({
            type: "post",
            url: '/backend/Management/updateJczqHotStatus',
            data: {data:data,'env': '<?php echo ENVIRONMENT?>'},
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

    //重写提示框
    function alertPop(content) {
        $("#alertBody").html(content);
        popdialog("alertPop");
    }
</script>
</body>
</html>