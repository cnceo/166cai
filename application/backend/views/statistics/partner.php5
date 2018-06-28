<?php $this->load->view("templates/head") ?>
<div>
    <div class="path">您的位置：<a href="/backend/Statistics/index?searchType=day">财务对账</a>&nbsp;&gt;&nbsp;<a href="/backend/Statistics/partner?searchType=day">合作商对账</a></div>
    <div class="mod-tab mt20">
        <div class="mod-tab-hd" style="position:relative">
            <ul>
                <li <?php if($search['searchType'] == 'day'): ?>class="current"<?php endif;?>><a href="/backend/Statistics/partner?searchType=day">按日查询</a></li>
                <li <?php if($search['searchType'] == 'month'): ?>class="current"<?php endif;?>><a href="/backend/Statistics/partner?searchType=month">按月查询</a></li>
            </ul>
        </div>
        <div class="mod-tab-bd">
            <ul>
                <li class="current" style="display: block;">
                    <div class="data-table-filter">
                        <form action="/backend/Statistics/partner" method="get"  id="search_form_order">
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                        日期：
                                        <input type="text" name='start_time' value="<?php echo $search['start_time'] ?>" class="ipt ipt-date w150 Wdate1" /><i></i>
                                        至
                                        <input type="text" name='end_time' value="<?php echo $search['end_time'] ?>" class="ipt ipt-date w150 Wdate1" /><i></i>
                                        合作商：
                                        <select class="partnerType w122" id="" name="partnerType">
                                            <?php foreach ($partners as $partner): ?>
                                            <option <?php if($search['partnerType'] == $partner['seller']):?>selected<?php endif;?> value="<?php echo $partner['seller'];?>"><?php echo $partner['seller'];?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <a href="javascript:;" id="search" class="btn-blue">查询</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <input type="hidden" name="searchType" value="<?php echo $search['searchType'] ?>"/>
                        </form>
                    </div>
                    <div class="data-table-list mt10">
                        <?php if($search['searchType'] == 'day'):?>
                        <table>
                            <colgroup>
                                <col width="14%">
                                <col width="14%">
                                <col width="14%">
                                <col width="14%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>合作商</th>
                                    <th>日期</th>
                                    <th>派奖（+）</th>
                                    <th>购彩（-）</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($list)): ?>
                                <?php foreach ($list as $detail): ?>
                                <tr>  
                                    <td><?php echo $detail['seller']; ?></td>
                                    <td><?php echo $detail['date']; ?></td>
                                    <td><?php echo m_format($detail['bonus']); ?></td>
                                    <td><?php echo m_format($detail['cost']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <table>
                            <colgroup>
                                <col width="14%">
                                <col width="14%">
                                <col width="14%">
                                <col width="14%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>合作商</th>
                                    <th>日期</th>
                                    <th>派奖（+）</th>
                                    <th>购彩（-）</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if(!empty($list)): ?>
                                <?php foreach ($list as $detail): ?>
                                <tr>  
                                    <td><?php echo $detail['seller']; ?></td>
                                    <td><?php echo $detail['months']; ?></td>
                                    <td><?php echo m_format($detail['bonus']); ?></td>
                                    <td><?php echo m_format($detail['cost']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
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
<!-- 弹出层 -->
<div class="pop-mask" style="display:none;width:200%"></div>
<!-- 彩种预排时间修改 end -->
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
