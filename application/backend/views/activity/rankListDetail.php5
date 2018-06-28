<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：<a href="">运营活动</a>&nbsp;&gt;&nbsp;<a href="">中奖排行榜活动</a></div>
<div class="data-table-filter mt10" style="width:1100px">
    <form action="/backend/Activity/rankListDetail" method="get" id="search_form">
        <table>
            <colgroup>
                <col width="62">
                <col width="140">
                <col width="62">
                <col width="400">
                <col width="62">
                <col width="232">
            </colgroup>
            <tbody>
                <tr>
                    <th>关键字：</th>
                    <td>
                        <input type="text" class="ipt w108" name="uname" value="" placeholder="用户名">
                    </td>
                    <td><a href="javascript:void(0);" class="btn-blue mr20 " onclick="$('#search_form').submit();">查询</a></td>
                    <th></th>
                </tr>
            </tbody>
        </table>
        <input type="hidden" class="ipt w108" name="plid" value="<?php echo $search['plid']; ?>">
        <input type="hidden" class="ipt w108" name="pissue" value="<?php echo $search['pissue']; ?>">
    </form>
</div>
<div class="data-table-list mt20">
  	<table>
        <colgroup>
            <col width="5">
            <col width="15">
            <col width="10">
            <col width="10">
            <col width="10">
        </colgroup>
        <thead>
            <tr>
                <td colspan="12">
                    <div class="tal">
                        <strong>活动期次</strong>
                        <span><?php echo $total['pissue']; ?> 次</span>
                        <strong class="ml20">彩种系列</strong>
                        <span><?php echo $total['lname']; ?></span>
                        <strong class="ml20">活动时间</strong>
                        <span><?php echo $total['start_time'] . '至' . $total['end_time']; ?></span>
                        <strong class="ml20">参与彩种</strong>
                        <span><?php echo $total['lids']; ?></span>
                    </div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th>排名</th>
                <th>用户名</th>
                <th>累计购彩金额</th>
                <th>累计中奖金额</th>
                <th><?php echo ($total['cstate'])? '最终奖励' : '预计奖励'; ?></th>
            </tr>
            <?php if(!empty($result)):?>
            <?php foreach ($result as $items):?>
            <tr>
                <td><?php echo $items['rankId']; ?></td>
                <td><a href="/backend/User/user_manage/?uid=<?php echo $items['uid']; ?>" class="cBlue" target="_blank"><?php echo $items['uname']; ?></a></td>
                <td><?php echo ParseUnit($items['money'], 1); ?></td>
                <td><?php echo ParseUnit($items['margin'], 1); ?></td>
                <td><?php echo ParseUnit($items['addMoney'], 1); ?></td>
            </tr>
            <?php endforeach;?>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="9">
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
<div class="page mt10 order_info">
    <?php echo $pages[0] ?>
</div>
</body>
</html>