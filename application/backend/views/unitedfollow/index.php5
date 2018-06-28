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
                            <input type="text" class="ipt w222"  name="name" value="<?php echo $search['name']; ?>"  placeholder="用户名/跟单订单编号/发起人" />
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
                        <th class = "tar">投注平台：</th>
                        <td>
                            <select class="selectList w98" id="follow_buyPlatform" name="buyPlatform">
                                <option value="-1">不限</option>
                                <?php foreach ($platforms as $key => $val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ("{$key}" === $search['buyPlatform']): echo "selected";
                                    endif; ?>><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <th>每次认购：</th>
                        <td>
                            <select class="selectList w98" id="follow_followType" name="followType">
                                <option value="-1">不限</option>
                                <?php foreach ($followTypes as $key => $val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ("{$key}" === $search['followType']): echo "selected";
                                    endif; ?>><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>投注金额：</th>
                        <td>
                            <input type="text" class="ipt w98" name="start_money" value='<?php echo $search['start_money']; ?>'/>
                            <span class="ml8 mr8">至</span>
                            <input type="text" class="ipt w98" name="end_money" value='<?php echo $search['end_money']; ?>'>
                        </td>
                        <th class="tar">注册渠道：</th>
                        <td>
                            <select class="selectList w130" name="channel">
                                <option value="-1">不限</option>
                                <?php foreach ($channels as $key=>$val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ($key == ($search['channel'])): echo "selected";
                                    endif; ?>><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <th>扣款方式：</th>
                        <td>
                            <select class="selectList w130" name="payType">
                                <option value="-1">不限</option>
                                <?php foreach ($payTypes as $key=>$val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ("{$key}" === ($search['payType'])): echo "selected";
                                    endif; ?>><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>   
                        <th class="tar"></th>
                        <td>    
                        </td>
                    </tr>
                    <tr>
                        <th>跟单状态：</th>
                        <td>
                            <select class="selectList w120 mr20"  name="status" id="follow_status">
                                <option value="-1">全部</option>
                                <?php foreach ($status as $key => $val): ?>
                                    <option value="<?php echo $key; ?>" <?php
                                    if ("{$key}" === ($search['status'])): echo "selected";
                                    endif; ?>><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <th>定制时间：</th>
                        <td>
                            <span class="ipt ipt-date w184"><input type="text" name='start_time' value="<?php echo $search['start_time'] ? $search['start_time'] : $searchTime['start_time']; ?>" class="Wdate1" /><i></i></span>
                            <span class="ml8 mr8">至</span>
                            <span class="ipt ipt-date w184"><input type="text" name='end_time' value="<?php echo $search['end_time'] ? $search['end_time'] : $searchTime['end_time']; ?>" class="Wdate1" /><i></i></span>
                        </td>
                        <th></th>
                        <td>
                            <a id="follow_searchOrder" href="javascript:void(0);" class="btn-blue mr20" onclick="">查询</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <input type="hidden" name="fromType" value="<?php echo $fromType ?>"  id="fromType" />
            <?php if ($fromType == 'ajax'): ?>
            <input type="hidden" name="uid" value="<?php echo $search['uid'] ?>"/>
            <?php endif; ?>
        </form>
    </div>
    <div class="data-table-list mt20">
        <table>
            <colgroup>
                <col width="40" />
                <col width="40" />
                <col width="100" />
                <col width="80" />
                <col width="80" />
                <col width="70" />
                <col width="110" />
                <col width="100" />
                <col width="100" />
                <col width="100" />
                <col width="100" />
                <col width="60" />
                <col width="60" />
                <col width="60" />
            </colgroup>
            <tr>
                <td colspan="14">
                    <div class="tal">
                        <strong>已跟总额</strong>
                        <span><?php echo  number_format(ParseUnit($count['totalBuyMoney'], 1), 2); ?> 元</span>
                        <strong class="ml10">中奖总额（税后）</strong>
                        <span><?php echo number_format(ParseUnit($count['totalMargin'], 1), 2); ?> 元</span>
                        <strong class="ml10">用户统计</strong>
                        <span><?php echo (int)$count['totalUsers']; ?> 人</span>
                        <strong class="ml10">预付扣款方案</strong>
                        <span><?php echo (int)$count['payByAdvance']; ?> 个</span>
                        <strong class="ml10">实时扣款方案</strong>
                        <span><?php echo (int)$count['payByTime']; ?> 个</span>
                    </div>
                </td>
            </tr>
        </table>
        <table id="tablesorter" class="tablesorter">
            <colgroup>
                <col width="140" />
                <col width="100" /> 
                <col width="80" />
                <col width="100" />
                <col width="120" />
                <col width="80" />
                <col width="70" />
                <col width="110" />
                <col width="100" />
                <col width="100"/>
                <col width="100" />
                <col width="60" />
                <col width="60" />
                <col width="60" />
            </colgroup>
            <thead>
                <tr>
                    <th>跟单编号</th>
                    <th>用户名</th>
                    <th>彩种</th>
                    <th>发起人</th>
                    <th>创建时间</th>
                    <th>扣款方式</th>
                    <th>每次认购</th>
                    <th>已跟次数/总次数</th>
                    <th>投注总额（元）</th>
                    <th>中奖金额（税后）</th>
                    <th>订单状态</th>
                    <th>详情</th>
                    <th>投注平台</th>
                    <th>注册渠道</th>
                </tr>
            </thead>
            <?php foreach ($orders as $key => $order): ?>
                <tr id="orders">
                    <td><?php echo $order['followId'] ?></td>
                    <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $order['uid'] ?>" class="cBlue"><?php echo $order['uname'] ?></a></td>
                    <td><?php echo $this->caipiao_cfg[$order['lid']]['name'] ?></td>
                    <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $order['puid'] ?>" class="cBlue"><?php echo $order['puname'] ?></a></td>
                    <td><?php echo $order['created'] ?></td>
                    <td><?php echo $payTypes[$order['payType']] ?></td>
                    <td><?php echo $order['followType'] ? $order['buyMoneyRate'] . '%' : number_format(ParseUnit($order['buyMoney'], 1), 2) . '元'; ?></td>
                    <td><?php echo $order['followTimes'] . '/' . $order['followTotalTimes']; ?></td>
                    <td><?php echo number_format(ParseUnit($order['totalMoney'], 1), 2); ?></td>
                    <td><?php echo number_format(ParseUnit($order['totalMargin'], 1), 2); ?></td>
                    <td><?php echo ($order['my_status']) ? $status[$order['status']] : ($order['status'] ? '跟单中' : $status[$order['status']]); ?></td>
                    <td><a target="_blank" class="cBlue" href="/backend/Unitedfollow/followOrderDetail/?followId=<?php echo $order['followId'] ?>">查看</a></td>
                    <td><?php echo ($order['buyPlatform'] == 0) ? "网页" : ($order['buyPlatform'] == 1 ? "Android" : ($order['buyPlatform'] == 2 ? "IOS" : "M版")); ?></td>
                    <td><?php echo $channels[$order['channel']]; ?></td>
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
    <div class="page mt10 follow_order">
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
<script src="/source/date/WdatePicker.js"></script>
<script>
    $(function () {
        // 时间控件
        $(".Wdate1").focus(function(){
            dataPicker();
        });

        // 查询
        $("#follow_searchOrder").click(function () {
            var start = $("input[name='united_start_time']").val();
            var end = $("input[name='united_end_time']").val();
            if (start > end) {
                alertPop('您选择的时间段错误，请核对后操作');
                return false;
            }
            if ($("#fromType").val() == "ajax")
            {
                $("#follow_order").load("/backend/Unitedfollow/index?" + $("#search_form_followOrder").serialize() + "&fromType=ajax");
                return false;
            }
            $('#search_form_followOrder').submit();
        });

        $('.follow_order a').click(function () {
            if ($("#fromType").val() == "ajax")
            {
                var _this = $(this);
                $("#follow_order").load(_this.attr("href"));
                return false;
            }
            return true;
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