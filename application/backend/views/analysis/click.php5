<?php
$this->load->view("templates/head");
$platforms = array(
    '1' => '网页',
    '2' => 'App',
);
$periods = array(7, 30, 60);
?>
<div class="path">您的位置：
    <a href="http://caipiao.2345.com/backend/Analysis/">数据分析 </a>&nbsp;&gt;&nbsp
    <a href="http://caipiao.2345.com/backend/Analysis/click">点击量</a></div>
<div class="data-table-filter mt10">
    <form action="/backend/Analysis/click/" method="get" id="clickForm">
        <table>
            <colgroup>
                <col width="150">
                <col width="150">
                <col width="160">
            </colgroup>
            <tbody>
            <tr>
                <td>
                    <label for="">平台：
                        <select class="selectList w98" id="platform" name="platform">
                            <?php foreach ($platforms as $pfk => $pfv): ?>
                                <option value="<?php echo $pfk; ?>"
                                    <?php echo ($pfk == $platform) ? 'selected' : ''; ?>>
                                    <?php echo $pfv; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </td>
                <td>
                    <label for="">渠道：
                        <select class="selectList w98" name="channelId">
                            <?php foreach ($channels as $chId => $chName): ?>
                                <option value="<?php echo $chId; ?>"
                                    <?php echo ($chId == $channelId) ? 'selected' : ''; ?>>
                                    <?php echo $chName; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </td>
                <td id="tdVersion">
                    <label for="">版本：
                        <select class="selectList w98" name="version">
                            <option value="">全部</option>
                            <?php foreach ($appVersions as $vs): ?>
                                <option value="<?php echo $vs; ?>"
                                    <?php echo ($vs == $version) ? 'selected' : ''; ?>>
                                    <?php echo $vs; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="filter">时间：
                        <?php foreach ($periods as $pr): ?>
                            <a data-sid="<?php echo $pr; ?>" class="filter-options periodList
                        <?php echo ($period == $pr) ? 'selected' : ''; ?>" href='###'>
                                过去<?php echo $pr; ?>天</a>
                        <?php endforeach; ?>
                    </div>
                </td>
                <input type="hidden" name="isCsv" id="isCsv" value="0"/>
                <input type="hidden" name="period" id="period" value="<?php echo $period; ?>"/>
                <td>
                    <a class="btn-blue ml25" id="searchSubmit" style="cursor: pointer; cursor: hand">查询</a>
                    <a class="btn-blue ml25" id="csvDownload" style="cursor: pointer; cursor: hand">导出</a>
                </td>
            </tr>
            </tbody>
        </table>
    </form>
</div>

<?php
$fields = array_keys($fieldsMap);
$dailyFieldsMap = array_diff_key($fieldsMap, array('dateStr' => 'dateStr'));
?>
<div class="data-table-list mt10">
    <table>
        <colgroup>
            <col width="100"/>
            <col width="120"/>
            <col width="145"/>
            <col width="150"/>
            <col width="140"/>
            <col width="140"/>
        </colgroup>
        <caption>汇总数据：</caption>
        <thead>
        <tr>
            <?php foreach ($dailyFieldsMap as $fieldName): ?>
                <th>日均<?php echo $fieldName; ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <?php foreach (array_keys($dailyFieldsMap) as $field): ?>
                <td><?php echo $daily[$field]; ?></td>
            <?php endforeach; ?>
        </tr>
        </tbody>
    </table>
</div>

<div class="data-table-list mt10">
    <table id="statTable">
        <colgroup>
            <col width="160"/>
            <col width="100"/>
            <col width="100"/>
            <col width="150"/>
            <col width="100"/>
            <col width="100"/>
            <col width="100"/>
        </colgroup>
        <thead>
        <tr>
            <?php foreach ($fieldsMap as $fieldName): ?>
                <th><?php echo $fieldName; ?></th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody class="avoid-sort">
        <tr>
            <?php foreach ($fields as $field): ?>
                <td><?php echo $summary[$field]; ?></td>
            <?php endforeach; ?>
        </tr>
        </tbody>
        <tbody>
        <?php if ($records): ?>
            <?php foreach ($records as $record): ?>
                <tr>
                    <?php foreach ($fields as $field): ?>
                        <td><?php echo $record[$field]; ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tablesorter.css?v=7">
<script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery.tablesorter.js"></script>
<script type='text/javascript' src="/source/tableExport/Blob.js"></script>
<script type='text/javascript' src="/source/tableExport/FileSaver.js"></script>
<script type='text/javascript' src="/source/tableExport/tableExport.js"></script>
<script>
    $(function () {
        $("#searchSubmit").click(function () {
            $('#clickForm').submit();
        });
        $("#csvDownload").click(function () {
            tableExport('statTable', '点击量统计', 'csv');
        });

        $('.periodList').click(function () {
            var period = $(this).data('sid');
            $("#period").val(period);
            $('.periodList').each(function (index, value) {
                if ($(this).data('sid') == period) {
                    $(this).addClass('selected');
                } else {
                    $(this).removeClass('selected');
                }
            })
        });

        if ($('#platform option:selected').val() == 1) {
            $('#tdVersion').hide();
        }

        $('#platform').change(function () {
            var platform = $('#platform option:selected').val();
            if (platform == 1) {
                $('#tdVersion').hide();
            } else {
                $('#tdVersion').show();
            }
            $.ajax({
                type: "post",
                url: '/backend/DataAnalysis/getChannels',
                data: {platform: platform},
                success: function (returnData) {
                    $('select[name="channelId"]').html(returnData);
                }
            });
        });

        $('#statTable').tablesorter();
    });
</script>
</body>
</html>