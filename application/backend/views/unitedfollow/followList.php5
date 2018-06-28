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
                        <th>跟单状态：</th>
                        <td>
                            <select class="selectList w98" id="follow_status" name="status">
                                <option value="-1">不限</option>
                                <?php foreach ($status as $key => $val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ((string) $key === ($search['status'])): echo "selected";
                                    endif; ?>><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <th>用户名：</th>
                        <td>
                            <input type="text" class="ipt w120"  name="name" value="<?php echo $search['name']; ?>"  placeholder="跟单发起人用户名" />
                        </td>
                        <th></th>
                        <td>
                            <a id="follow_searchOrder" href="javascript:void(0);" class="btn-blue mr20" onclick="">查询</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="fromType" value="<?php echo $fromType ?>"  id="fromType" />
            <input type="hidden" name="puid" value="<?php echo $search['puid'] ?>"  id="fromType" />
            <input type="hidden" name="lid" value="<?php echo $search['lid'] ?>"  id="fromType" />
        </form>
    </div>
    <div class="data-table-list mt20">
        <table id="tablesorter" class="tablesorter">
            <colgroup>
                <col width="60" />
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
                    <th>用户名</th>
                    <th>定制时间</th>
                    <th>每次认购金额</th>
                    <th>每次认购比例</th>
                    <th>已跟次数/总次数</th>
                    <th>跟单状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <?php foreach ($orders as $key => $order): ?>
                <tr id="orders">
                    <td><?php echo $key + 1 ?></td>
                    <td><?php echo $order['uname'] ?></td>
                    <td><?php echo $order['effectTime'] ?></td>
                    <td><?php echo $order['followType'] ? '/' : number_format(ParseUnit($order['buyMoney'], 1), 2) . '元'; ?></td>
                    <td><?php echo $order['followType'] ? $order['buyMoneyRate'] . '%' : '/'; ?></td>
                    <td><?php echo $order['followTimes'] . '/' . $order['followTotalTimes'] ?></td>
                    <td><?php echo ($order['my_status']) ? $status[$order['status']] : ($order['status'] ? '跟单中' : $status[$order['status']]); ?></td>
                    <td><a target="_blank" class="cBlue" href="/backend/Unitedfollow/followOrderDetail/?followId=<?php echo $order['followId'] ?>">查看</a></td>
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
<script>
    $(function () {
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