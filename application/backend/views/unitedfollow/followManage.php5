<?php
if ($fromType != 'ajax'): $this->load->view("templates/head");
endif;
?>
<div class="frame-container" style="margin-left:0;padding-left: 0px;">
    <?php if ($fromType != 'ajax'):?>
    <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Unitedfollow/index">跟单管理</a></div>
        <div class="mod-tab-hd mt20">
            <ul>
                <li><a href="/backend/Unitedfollow/index">跟单管理</a></li>
                <li class="current"><a href="/backend/Unitedfollow/followManage">定制管理</a></li>
            </ul>
        </div>
    <?php endif; ?>
    <div class="data-table-filter mt10" style="width:960px">
        <form action="" method="get"  id="search_form_followOrder">
            <table>
                <colgroup>
                    <col width="62" />
                    <col width="232" />
                    <col width="62" />
                    <col width="400" />
                    <col width="62" />
                    <col width="100" />
                </colgroup>
                <tbody>
                    <tr>
                        <th>关键字：</th>
                        <td>
                            <input type="text" class="ipt w120"  name="name" value="<?php echo $search['name']; ?>"  placeholder="发起人用户名" />
                        </td>
                        <th>彩  种：</th>
                        <td>
                            <select class="selectList w120 mr20"  name="lid" id="follow_lid">
                                <option value="">全部</option>
                                <?php foreach ($lottery as $key => $val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ($search['lid'] === "{$key}"): echo "selected";
                                    endif; ?>><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <th class = "tar">定制状态：</th>
                        <td>
                            <select class="selectList w98" id="follow_status" name="status">
                                <option value="-1">不限</option>
                                <?php foreach ($status as $key => $val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ("{$key}" === $search['status']): echo "selected";
                                    endif; ?>><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <th></th>
                        <td>
                            <a id="follow_searchOrder" href="javascript:void(0);" class="btn-blue mr20" onclick="">查询</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="fromType" value="<?php echo $fromType ?>"  id="fromType" />
        </form>
    </div>
    <div class="data-table-list mt20">
        <table id="tablesorter" class="tablesorter">
            <colgroup>
                <col width="60" />
                <col width="100" /> 
                <col width="100" />
                <col width="160" />
                <col width="120" />
                <col width="140" />
                <col width="180" />
                <col width="140" />
                <col width="100" />
            </colgroup>
            <thead>
                <tr>
                    <th>序号</th>
                    <th>发起人</th>
                    <th>彩种</th>
                    <th>彩种战绩</th>
                    <th>中奖次数</th>
                    <th>税前累计奖金（元）</th>
                    <th>定制人数</th>
                    <th>历史定制</th>
                    <th>操作</th>
                </tr>
            </thead>
            <?php foreach ($orders as $key => $order): ?>
                <tr id="orders">
                    <td><?php echo $key + 1 ?></td>
                    <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $order['uid'] ?>" class="cBlue"><?php echo $order['uname'] ?></a></td>
                    <td><?php echo $lottery[$order['lid']] ?></td>
                    <td><?php echo calUnitedPoints($order['united_points']) ?></td>
                    <td><?php echo $order['winningTimes'] ?></td>
                    <td><?php echo number_format(ParseUnit($order['bonus'], 1), 2) ?></td>
                    <td><?php echo $order['isFollowNum'] . '人（还剩' . (2000 - $order['isFollowNum']) . '人）'; ?></td>
                    <td><?php echo $order['followTimes'] . '人次' ?></td>
                    <td><a target="_blank" class="cBlue" href="/backend/Unitedfollow/plannerDetail/?puid=<?php echo $order['uid'] ?>&lid=<?php echo $order['lid'] ?>">查看</a></td>
                </tr>
            <?php endforeach; ?>
            <tfoot>
                <tr>
                    <td colspan="11">
                        <div class="stat">
                            <span>本页&nbsp;<?php echo $pages[2] ?>&nbsp;条</span>
                            <span class="ml20">共&nbsp;<?php echo $pages[1] ?>&nbsp;页</span>
                            <span class="ml20">总计&nbsp;<?php echo $pages[3] ?>&nbsp;</span>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="page mt10 united_order">
        <?php echo $pages[0];?>
    </div>
    <!-- 信息弹层 start -->
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
    <!-- 信息弹层 end -->
</div>
<!-- 字段排序js -->
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tablesorter.css?v=7">
<script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery.tablesorter.js"></script>
<script>
    $(function () {
        $.tablesorter.addParser({
           id: "points", 
           is: function(s){
              return false;
           },
           format: function(s){
                var points = 0;
                if(s){
                    var huangguanTpl = s.match(/(\d+)皇冠/);
                    var huangguan = huangguanTpl ? parseInt(huangguanTpl[1], 10) : 0;
                    var taiyangTpl = s.match(/(\d+)太阳/);
                    var taiyang = taiyangTpl ? parseInt(taiyangTpl[1], 10) : 0;
                    var yueliangTpl = s.match(/(\d+)月亮/);
                    var yueliang = yueliangTpl ? parseInt(yueliangTpl[1], 10) : 0;
                    var xingxingTpl = s.match(/(\d+)星星/);
                    var xingxing = xingxingTpl ? parseInt(xingxingTpl[1], 10) : 0;
                    points = huangguan * 1000 + taiyang * 100 + yueliang * 10 + xingxing;
                }
                return points;
           },
           type: "numeric" 
        });

        $('#tablesorter').tablesorter({headers: {0: {sorter: false}, 1: {sorter: false}, 2: {sorter: false}, 3: {sorter: 'points'}, 8: {sorter: false}}});

        // 查询
        $("#follow_searchOrder").click(function () {
            // if ($("#fromType").val() == "ajax")
            // {
            //     $("#united_order").load("/backend/Management/manageUnited?" + $("#search_form_followOrder").serialize() + "&fromType=ajax");
            //     return false;
            // }
            $('#search_form_followOrder').submit();
        });
    });

    // 弹层
    function alertPop(content) {
        $("#alertBody").html(content);
        popdialog("alertPop");
    }

</script>
<?php if ($fromType != 'ajax'): ?>
</body>
</html>
<?php endif; ?>