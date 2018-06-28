<?php
if ($fromType != 'ajax'): $this->load->view("templates/head");
endif;
?>
<div class="frame-container" style="margin-left:0;padding-left: 0px;">
    <?php if ($fromType != 'ajax'):?>
    <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Unitedfollow/index">跟单管理</a></div>
        <div class="mod-tab-hd mt20">
            <ul>
                <li class="current"><a href="/backend/Unitedfollow/index">跟单管理</a></li>
                <li><a href="/backend/Unitedfollow/followManage">定制管理</a></li>
            </ul>
        </div>
    <?php endif; ?>
    <div class="data-table-log mt20">
        <table>
            <colgroup>
                <col width="100">
                <col width="200">
                <col width="100">
                <col width="200">
                <col width="100">
                <col width="117">
                <col>
            </colgroup>
            <tbody>
                <tr class="title">
                    <th colspan="6">订单信息</th>
                </tr>
                <tr>
                    <th>跟单编号：</th>
                    <td><?php echo $info['followId'] ?></td>
                    <th>购买方式：</th>
                    <td>定制跟单</td>
                    <th>用户名：</th>
                    <td><?php echo $info['uname'] ?></td>
                </tr>
                <tr>
                    <th>彩种：</th>
                    <td><?php echo $this->caipiao_cfg[$info['lid']]['name'] ?></td>
                    <th>跟单状态：</th>
                    <td><?php echo ($info['my_status']) ? $status[$info['status']] : ($info['status'] ? '跟单中' : $status[$info['status']]); ?></td>
                    <th>创建时间：</th>
                    <td><?php echo $info['created'] ?></td>
                </tr>
                <tr>
                    <th>定制时间：</th>
                    <td><?php echo $info['effectTime'] ?></td>
                    <th>扣款方式：</th>
                    <td><?php echo $payTypes[$info['payType']] ?></td>
                    <th>每次认购：</th>
                    <td><?php echo $info['followType'] ? ($info['buyMoneyRate'] . '%，但不超过' . number_format(ParseUnit($info['buyMaxMoney'], 1), 2) . ' 元') : (number_format(ParseUnit($info['buyMoney'], 1), 2) . ' 元'); ?></td>
                </tr>
                <tr>
                    <th>进度：</th>
                    <td><?php echo '已跟' . $info['followTimes'] . '次/共' . $info['followTotalTimes'] . '次'; ?><?php echo ($info['followTotalTimes'] > $info['followTimes'] && in_array($info['status'], array(3, 4))) ? '，已取消' . ($info['followTotalTimes'] - $info['followTimes']) . '次' : ''; ?></td>
                    <th>预付金额：</th>
                    <td><?php echo $info['payType'] ? '/' : number_format(ParseUnit($info['totalMoney'], 1), 2) . ' 元'; ?></td>
                    <th></th>
                    <td></td>
                </tr>
                <tr class="hr">
                    <td colspan="6">
                        <div class="hr-dashed"></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="tab-nav mt20">
        <ul class="clearfix">
            <li class="active load_info"><span>跟单进度</span></li>
            <li>
                <div style="margin-left:200px;">
                    <button id="cancelOrder" class="btn-blue">停止跟单</button>
                </div>
            </li>
        </ul>
    </div>
    <div class="data-table-list mt20">
        <table id="tablesorter" class="tablesorter">
            <colgroup>
                <col width="60" />
                <col width="100" /> 
                <col width="160" />
                <col width="120" />
                <col width="140" />
                <col width="140" />
                <col width="140" />
                <col width="140" />
                <col width="80" />
            </colgroup>
            <thead>
                <tr>
                    <th>序号</th>
                    <th>发起人</th>
                    <th>认购时间</th>
                    <th>期次</th>
                    <th>方案金额（元）</th>
                    <th>跟单金额（元）</th>
                    <th>订单状态</th>
                    <th>税后奖金（元）</th>
                    <th>操作</th>
                </tr>
            </thead>
            <?php foreach ($orders as $key => $order): ?>
                <tr id="orders">
                    <td><?php echo $key + 1 ?></td>
                    <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $order['uid'] ?>" class="cBlue"><?php echo $order['uname'] ?></a></td>
                    <td><?php echo $order['created'] ?></td>
                    <td><?php echo $order['issue'] ?></td>
                    <td><?php echo number_format(ParseUnit($order['money'], 1), 2) ?></td>
                    <td><?php echo number_format(ParseUnit($order['buyMoney'], 1), 2) ?></td>
                    <td><?php echo $ustatus[$order['status']] ?></td>
                    <td><?php echo number_format(ParseUnit($order['margin'], 1), 2) ?></td>
                    <td><a target="_blank" class="cBlue" href="/backend/Management/unitedOrderDetail/?id=<?php echo $order['hmOrderId'] ?>">查看</a></td>
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
                <a href="javascript:;" class="btn-blue-h32 mlr15" id="confirmCancel" data-type="0">确认</a>
                <a href="javascript:;" class="btn-b-white mlr15 pop-cancel">取消</a>
            </div>
        </div>
    </div>
    <!-- 信息弹层 end -->
</div>
<script>
    $(function () {
        // 停止跟单
        $("#cancelOrder").click(function () {
            alertPop('确认要停止跟单吗？')
        });
    });

    // 弹层
    function alertPop(content) {
        $("#alertBody").html(content);
        popdialog("alertPop");
    }

    // 停止跟单
    $("#confirmCancel").click(function () {
        var followId = '<?php echo $info['followId']; ?>';
        var uid = '<?php echo $info['uid']; ?>';
        $.ajax({
            type: "post",
            url: '/backend/Unitedfollow/cancelFollowOrder',
            data: {followId:followId, uid:uid},
            success: function(data){
                var json = jQuery.parseJSON(data);
                alert(json.message);
                closePop();
                if(json.status =='y')
                {
                    location.reload();
                }                
            }
        });
    });
    

</script>
<?php if ($fromType != 'ajax'): ?>
</body>
</html>
<?php endif; ?>