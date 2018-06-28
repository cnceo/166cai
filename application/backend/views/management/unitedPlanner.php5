<?php $this->load->view('templates/head'); ?>

<div class="frame-container" style="margin-left:0;padding-left: 0px;">
    <div class="path">您的位置：运营管理&nbsp;&gt;&nbsp;<a href="/backend/Management/manageUnited/">合买管理</a></div>
    <div class="mod-tab-hd mt20">
        <ul>
            <li><a href="/backend/Management/manageUnited/">合买管理</a></li>
            <li class="current"><a href="/backend/Management/managePlanner/">合买红人</a></li>
        </ul>
    </div>
    <div class="mt10" style="width:100%">
        <div style="margin-top: 30px;">
            <label><input name="lottery" type="radio" value="0" <?php
                            if ($lid == 0 || !$lid): echo "checked";
                            endif;
                            ?>>全部彩种</label>
            <?php foreach ($lottery as $k => $caipiao) {
    ?>
                <label style="margin-left: 10px;"><input name="lottery" type="radio" <?php
                            if ($lid == $k): echo "checked";
                            endif;
                            ?> value="<?php echo $k; ?>"><?php echo $caipiao; ?></label>
            <?php 
} ?>
            <form method="get" style="float:right;">
                <input name="lid" value="<?php echo $lid;?>" style="display: none;">
                <input value="<?php echo $username;?>" id="username" name="username" placeholder="用户名"  class="ipt w120"><button class="btn-blue ml10 mr10" type="submit">搜索</button>
            </form>
        </div>
        <div class="data-table-list mt20">
            <table id="tablesorter" class="tablesorter">
                <colgroup>
                    <col width="40" />
                    <col width="80" />
                    <col width="80" />
                    <col width="80" />
                    <col width="80" />
                    <col width="80" />
                    <col width="80" />
                    <col width="80" />
                    <col width="80" />
                    <col width="80" />
                    <col width="80" />
                    <col width="80" />
                    <col width="80" />
                </colgroup>
                <thead>
                    <tr>
                        <th>红人</th>
                        <th>用户名</th>
                        <th class="filter-item">发起合买总额</th>
                        <th class="filter-item">税前中奖总额</th>
                        <th class="filter-item">中奖回报率</th>
                        <th class="filter-item">单笔订单均额</th>
                        <?php if($lid==0){ ?>
                        <th class="filter-item">高</th>
                        <th class="filter-item">慢</th>
                        <th class="filter-item">竞</th>
                        <?php } ?>
                        <th class="filter-item">发单次数</th>
                        <th class="filter-item">成功次数</th>
                        <th class="filter-item">发单成功率</th>
                        <th>操作</th>
                    </tr>
                </thead>
                    <tbody>
                    <?php foreach ($planners as $key => $planner) {
    ?>
                        <tr>
                            <td><?php echo ($planner['isHot']==0)?'':'<font color="red">红</font>'; ?></td>
                            <td><a target="_blank" href="/backend/User/user_manage/?uid=<?php echo $planner['uid'] ?>"
                                   class="cBlue"><?php echo $planner['uname'] ?></a></td>
                            <td><?php echo m_format($planner['money']); ?></td>
                            <td><?php echo m_format($planner['bonus']); ?></td>
                            <td><?php echo ($planner['money']==0)?'0%':round($planner['bonus'] / $planner['money'] * 100, 2).'%'; ?></td>
                            <td><?php echo m_format($planner['money'] / $planner['allTimes']); ?></td>
                            <?php if($lid==0){ ?>
                            <td>0%</td>
                            <td><?php echo ($planner['money']==0)?'0%':round($planner['m'] / $planner['money'] * 100, 2).'%'; ?></td>
                            <td><?php echo ($planner['money']==0)?'0%':round($planner['j'] / $planner['money'] * 100, 2).'%'; ?></td>
                            <?php } ?>
                            <td><?php echo $planner['allTimes']; ?></td>
                            <td><?php echo $planner['succTimes']; ?></td>
                            <td><?php echo round($planner['succTimes'] / $planner['allTimes'] * 100, 2).'%'; ?></td>
                            <td><a data-id="<?php echo $planner['id']; ?>" data-uid="<?php echo $planner['uid']; ?>" data-hot="<?php echo $planner['isHot']; ?>" data-lid="<?php echo $planner['lid']; ?>" 
                                class="updateTop" href="javascript:void(0);"><?php echo $planner['isHot'] == 0 ? '<font style="color:blue;">设为红人</font>' : '取消'; ?><a></td>
                        </tr>
                                    <?php 
} ?>
                    </tbody>
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
        </div>
	<div class="page mt10 united_order">
        <?php echo $pages[0];?>
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
        <div class="pop-dialog" id="forceDistributionPop" style="width:200">
            <div class="pop-in">
                <div class="pop-body">
                    <div class="data-table-list">
                        <table>
                            <div id="showAlert" style="text-align:center;font-size:20px;font-weight:bolder"></div>
                        </table>
                    </div>
                </div>
                <div class="pop-foot tac">
                    <a href="javascript:;" class="btn-blue-h32 mlr15" id="forceDistSubmit" data-id="0" data-lid="0" data-hot="0" data-uid="0">确认</a>
                    <a href="javascript:;" class="btn-b-white mlr15 pop-cancel" id="forceDistCancel">取消</a>
                </div>
            </div>
        </div>
</div>
<!-- 字段排序js -->
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tablesorter.css?v=7">
<script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery.tablesorter.js"></script>
<script>
    $(function () {
        $('#tablesorter').tablesorter({headers:{0:{sorter:false},1:{sorter:false},<?php echo $lid==0?12:9;?>:{sorter:false}},sortForce: [[0,0]]});
        $(".updateTop").on('click', function () {
            var id = $(this).attr("data-id");
            var lid = $(this).attr("data-lid");
            var hot = $(this).attr("data-hot");
            var uid=$(this).attr("data-uid");
            if(hot==0){
                hot=1;
                var message='是否要设为合买红人？';
            }else{
                hot=0;
                var message='是否要取消合买红人？';
            }
            $.ajax({
                type: "post",
                url: '/backend/Management/updatePlannerTop?judge=1',
                data: {id:id, lid:lid, hot:hot, uid:uid},
                dataType: "json",
                success: function (data) {
                    if (data.status == 'success') {
                        $("#showAlert").html(message);
                        $("#forceDistSubmit").attr("data-id", id);
                        $("#forceDistSubmit").attr("data-lid", lid);
                        $("#forceDistSubmit").attr("data-hot", hot);
                        $("#forceDistSubmit").attr("data-uid", uid);
                        popdialog("forceDistributionPop");
                    } else {
                        alertPop(data.message);
                        return false;
                    }
                }
            });
        });
        
        $('#forceDistSubmit').click(function () {
            $('#forceDistributionPop').hide();
            var id = $(this).attr("data-id");
            var lid = $(this).attr("data-lid");
            var hot = $(this).attr("data-hot");
            var uid=$(this).attr("data-uid");
            $.ajax({
                type: "post",
                url: '/backend/Management/updatePlannerTop',
                data: {id:id, lid:lid, hot:hot, uid:uid},
                dataType: "json",
                success: function (data) {
                    if (data.status == 'success') {
                        alertPop('恭喜你,操作成功!');
                        location.reload();
                    } else {
                        alertPop(data.message);
                        return false;
                    }
                }
            });
        });
        
        $("input:radio[name='lottery']").change(function () {
            var lid = $("input[name='lottery']:checked").val();
            location.href="/backend/Management/managePlanner/?lid="+lid;    
        });
    });

    function alertPop(content) {
        $("#alertBody").html(content);
        popdialog("alertPop");
    }
    
</script>    
</body>
</html>