<?php $this->load->view("templates/head") ?>
<div>
    <div class="path">您的位置：<a href="/backend/Statistics/index?searchType=day">财务对账</a>&nbsp;&gt;&nbsp;<a href="/backend/Statistics/index?searchType=day">账户余额对账</a></div>
    <div class="mod-tab mt20">
        <div class="mod-tab-hd">
            <ul>
                <li <?php if($search['searchType'] == 'day'): ?>class="current"<?php endif;?>><a href="/backend/Statistics/index?searchType=day">按日查询</a></li>
                <li <?php if($search['searchType'] == 'month'): ?>class="current"<?php endif;?>><a href="/backend/Statistics/index?searchType=month">按月查询</a></li>
            </ul>
        </div>
        <div class="mod-tab-bd">
            <ul>
                <li class="current" style="display: block;">
                    <div class="data-table-filter" style="width:100%">
                        <form action="/backend/Statistics/index" method="get"  id="search_form_order">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                        日期：
                                        <input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="ipt ipt-date w150 Wdate1" /><i></i>
                                        至
                                        <input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="ipt ipt-date w150 Wdate1" /><i></i>
                                        <a href="javascript:;" id="search" class="btn-blue">查询</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <input type="hidden" name="searchType" value="<?php echo $search['searchType'] ?>"/>
                        </form>
                        <p class="pResult">
                            <b>充值 <span><?php echo m_format($tj['recharge']); ?></span></b>
                            <b>派奖 <span><?php echo m_format($tj['bonus']); ?></span></b>
                            <b>购彩 <span><?php echo m_format($tj['cost']); ?></span></b>
                            <b>购彩退款 <span><?php echo m_format($tj['refund']); ?></span></b>
                            <b>其他(+) <span><?php echo m_format($tj['oplus']); ?></span></b>
                            <b>其他(-) <span><?php echo m_format($tj['ominus']); ?></span></b>
                            <b>提现 <span><?php echo m_format($tj['withdraw']); ?></span></b>
                            <b>提现失败 <span><?php echo m_format($tj['withdraw_fail']); ?></span></b>
                            <b>送彩金 <span><?php echo m_format($tj['activity']); ?></span></b>
                            <b>返点 <span><?php echo m_format($tj['rebate']); ?></span></b>
                        </p>
                    </div>
                    <div class="data-table-list mt10">
                        <table>
                            <colgroup>
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="9%">
                                <col width="10%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>日期</th>
                                    <th>充值（+）</th>
                                    <th>派奖（+）</th>
                                    <th>购彩（-）</th>
                                    <th>购彩退款（+）</th>
                                    <th>提现（-）</th>
                                    <th>提现失败（+）</th>
                                    <th>送彩金（+）</th>
                                    <th>返点（+）</th>
                                    <th>其他（+）</th>
                                    <th>其他（-）</th>
                                    <th>用户账户余额</th>
                                    <th>明细</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($list)): ?>
                                <?php if($search['searchType'] == 'day'):?>
                                    <?php foreach ($list as $detail): ?>
                                    <tr>  
                                        <td><?php echo $detail['date']; ?></td>
                                        <td><?php echo m_format($detail['recharge']); ?></td>
                                        <td><?php echo m_format($detail['bonus']); ?></td>
                                        <td><?php echo m_format($detail['cost']); ?></td>
                                        <td><?php echo m_format($detail['refund']); ?></td>
                                        <td><?php echo m_format($detail['withdraw']); ?></td>
                                        <td><?php echo m_format($detail['withdraw_fail']); ?></td>
                                        <td><?php echo m_format($detail['activity']); ?></td>
                                        <td><?php echo m_format($detail['rebate']); ?></td>
                                        <td><?php echo m_format($detail['oplus']); ?></td>
                                        <td><?php echo m_format($detail['ominus']); ?></td>
                                        <td><?php echo m_format($detail['money']); ?></td>
                                        <td><a href="/backend/Statistics/userDetail?date=<?php echo $detail['date'];?>&searchType=day" class="cBlue">查看</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?php foreach ($list as $detail): ?>
                                    <tr>  
                                        <td><?php echo $detail['months']; ?></td>
                                        <td><?php echo m_format($detail['recharge']); ?></td>
                                        <td><?php echo m_format($detail['bonus']); ?></td>
                                        <td><?php echo m_format($detail['cost']); ?></td>
                                        <td><?php echo m_format($detail['refund']); ?></td>
                                        <td><?php echo m_format($detail['withdraw']); ?></td>
                                        <td><?php echo m_format($detail['withdraw_fail']); ?></td>
                                        <td><?php echo m_format($detail['activity']); ?></td>
                                        <td><?php echo m_format($detail['rebate']); ?></td>
                                        <td><?php echo m_format($detail['oplus']); ?></td>
                                        <td><?php echo m_format($detail['ominus']); ?></td>
                                        <td><?php echo m_format($detail['money']); ?></td>
                                        <td><a href="/backend/Statistics/userDetail?date=<?php echo $detail['months'];?>&searchType=month" class="cBlue">查看</a></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="stat mt10">
                        <span class="ml20">本页共&nbsp;<?php echo $pages[2]; ?>&nbsp;条</span>
                        <span class="ml20">共&nbsp;<?php echo $pages[1]; ?>&nbsp;页</span>
                        <span class="ml20">总计&nbsp;<?php echo $pages[3]; ?>&nbsp;</span>
                    </div>
                </li>
            </ul>
        </div>
    </div>
    <div class="page mt10">
         <?php echo $pages[0] ?>
    </div>
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
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
        // 查询
        $("#search").click(function(){
            var start = $("input[name='start_time']").val();
            var end = $("input[name='end_time']").val();
            if(start > end)
            {
                alertPop('您选择的时间段错误，请核对后操作');
                return false;
            }
            $('#search_form_order').submit();
        });

        $(".Wdate1").focus(function(){
            dataPicker();
        });
    });

    function alertPop(content){
        $("#alertBody").html(content);
        popdialog("alertPop");
    }
</script>
</body>
</html>
