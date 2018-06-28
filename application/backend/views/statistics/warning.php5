<?php $this->load->view("templates/head") ?>
<div>
    <div class="path">您的位置：<a href="/backend/Statistics/index?searchType=day">财务对账</a>&nbsp;&gt;&nbsp;<a href="/backend/Statistics/warning">余额预警设置</a></div>
    <form action="/backend/Statistics/warning" method="get"  id="submit_form">
    <div class="data-table-list mt20">
        <table>
            <colgroup>
                <col width="33%">
                <col width="33%">
                <col width="34%">
            </colgroup>
            <thead>
                <tr>
                    <th>合作商1</th>
                    <th>当前余额</th>
                    <th>预警额度</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($list['detail'])): ?>
                    <?php foreach ($list['detail'] as $detail): ?>
                    <tr>
                        <td><?php echo $detail['name']; ?></td>
                        <td><?php echo m_format($detail['money']); ?></td>
                        <td><input type="text" value="<?php echo m_format($detail['warning_money']); ?>" name="<?php echo $detail['name'];?>" class="ipt w222"></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="warning-notice mt20">
        <div>
            <label for="">预警通知人1：</label>
            <input name="warningUser1" value="<?php echo $list['phone'][0]; ?>" type="text" class="ipt w222">
        </div>
        <div class="mt10">
            <label for="">预警通知人2：</label>
            <input name="warningUser2" value="<?php echo $list['phone'][1]; ?>" type="text" class="ipt w222">
        </div>
    </div>
    <input type="hidden" name="fromType" value="save"/>
    <a href="javascript:void(0)" id="saveForm" class="btn-blue mt40" margin-left="50%">保存</a>
    </form>
</div>
<script  src="/source/date/WdatePicker.js"></script>
<script>
    $(function(){
        // 查询
        $("#saveForm").click(function(){
            $('#submit_form').submit();
        });
    });
</script>
</body>
</html>
