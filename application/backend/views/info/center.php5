<?php $this->load->view("templates/head") ?>
<?php
$categoryUrls = array(
    1 => 'csxw',
    2 => 'ssq',
    3 => 'qtfc',
    4 => 'dlt',
    5 => 'qttc',
    6 => 'jczq',
    7 => 'sfc',
    8 => 'jclq',
    9 => 'zjtjzq',
    10 => 'zjtjlq',
);
?>
<div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="/backend/Info/center">资讯中心管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/Info/crawl">资讯抓取配置</a></li>
            <li class="current"><a href="/backend/Info/center">资讯管理</a></li>
            <li><a href="/backend/Info/nba">NBA伤病管理</a></li>
            <li><a href="/backend/Info/banner">banner图管理</a></li>
        </ul>
    </div>
    <div class="mod-tab-bd">
        <ul>
            <li style="display: block">
                <div class="data-table-filter mt10">
                    <form action="/backend/Info/center" method="get" id="search_form">
                        <table>
                            <colgroup>
                                <col width="35%"/>
                                <col width="25%"/>
                                <col width="40%"/>
                            </colgroup>
                            <tbody>
                            <tr>
                                <td>
                                    标题：
                                    &nbsp;&nbsp;
                                    <input type="text" class="ipt w222" name="title"
                                           value="<?php echo $search['title'] ?>" placeholder=""/>
                                </td>
                                <td>
                                    是否显示：
                                    <select name="isshow">
                                    	<option value=''>所有</option>
                                    	<option value='1' <?php if ($search['isshow'] === '1'){?>selected<?php }?>>是</option>
                                    	<option value='0' <?php if ($search['isshow'] === '0'){?>selected<?php }?>>否</option>
                                    </select>
                                </td>
                                <td>
                                    创建时间：
                                    <span class="ipt ipt-date w184"><input type="text" name='start_time'
                                                                           value="<?php echo $search['start_time'] ?>"
                                                                           class="Wdate1"/><i></i></span>
                                    <span class="ml8 mr8">至</span>
                                    <span class="ipt ipt-date w184"><input type="text" name='end_time'
                                                                           value="<?php echo $search['end_time'] ?>"
                                                                           class="Wdate1"/><i></i></span>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    资讯分类：
                                    <select class="selectList w150" name="category">
                                        <option value="">全部</option>
                                        <?php foreach ($categoryList as $key => $value): ?>
                                            <option
                                                value="<?php echo $key; ?>" <?php if ($search['category'] == $key): echo "selected"; endif; ?>><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    资讯来源：
                                    <select class="selectList w150" name="source">
                                        <option value="">全部</option>
                                        <?php foreach ($sourceList as $key => $value): ?>
                                            <option
                                                value="<?php echo $key; ?>" <?php if ($search['source'] == $key): echo "selected"; endif; ?>><?php echo $value; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    提交人：
                                    <input type="text" class="ipt w130" name="submitter"
                                           value="<?php echo $search['submitter'] ?>">
                                    <a href="javascript:void(0);" class="btn-blue "
                                       onclick="$('#search_form').submit();">查询</a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </form>
                </div>
                <a href="/backend/Info/add_update/" class="btn-blue">新建资讯</a>
                <div class="data-table-list mt10">
                    <table id="tablesorter" class="tablesorter">
                        <colgroup>
                            <col width="20%">
                            <col width="12%">
                            <col width="10%">
                            <col width="7%">
                            <col width="7%">
                            <col width="6%">
                            <col width="8%">
                            <col width="10%">
                            <col width="6%">
                            <col width="10%">
                            <col width="15%">
                            <col width="5%">
                            <col width="5%">
                            <col width="5%">
                        </colgroup>
                        <thead>
                        <tr>
                            <th>资讯标题</th>
                            <th>创建时间</th>
                            <th>URL</th>
                            <th>分类</th>
                            <th>来源</th>
                            <th>是否显示</th>
                            <th>显示平台</th>
                            <th>实际/展示阅读数</th>
                            <th>评论数</th>
                            <th>实际/展示点赞数</th>
                            <th>操作</th>
                            <th>置顶</th>
                            <th>权重</th>
                            <th>提交人</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($notices as $key => $notice): ?>
                            <tr>
                                <td><?php echo $notice['title'] ?></td>
                                <td><?php echo substr($notice['created'], 0, 16) ?></td>
                                <td><a href="http://888.166cai.cn/info/<?php echo $categoryUrls[$notice['category_id']]?>/<?php echo $notice['id'] ?>" target="_blank" class="cBlue">/info/<?php echo $categoryUrls[$notice['category_id']]?>/<?php echo $notice['id'] ?></a></td>
                                <td><?php echo $categoryList[$notice['category_id']] ?></td>
                                <td><?php echo $sourceList[$notice['source_id']] ?></td>
                                <td><?php echo $notice['is_show'] ? "是" : "否" ?></td>
                                <td><?php if ($notice['platform'] == 0) {echo '无';}else {if ($notice['platform'] & 1) echo "网页 "; if ($notice['platform'] & 2) echo "Android "; if ($notice['platform'] & 4) echo "IOS "; if ($notice['platform'] & 8) echo "M版 ";}?></td>
                                <td><?php echo $notice['trueNum'].'/'.$notice['num'] ?></td>
                                <td><?php echo $notice['comNum'] ?></td>
                                <td><?php echo $notice['truelikeNum'].'/'.$notice['likeNum'] ?></td>
                                <td><a href="/backend/Info/add_update/?id=<?php echo $notice['id'] ?>&<?php echo http_build_query($search)?>"
                                       class="cBlue mr10">编辑</a>
                                    <a href="/backend/Info/notice_view/?id=<?php echo $notice['id'] ?>" class="cBlue"
                                       target="_blank">预览</a>
                                    <a href="/backend/Info/setTop/?id=<?php echo $notice['id'] ?>"
                                       class="cBlue mr10 setTop">置顶</a>
                                    <a href="/backend/Info/delete/?id=<?php echo $notice['id'] ?>"
                                       class="cBlue mr10 delete">删除</a>
                                </td>
                                <td><?php echo $notice['is_top'] == 0 ? "否" : "是"; ?></td>
                                <td><?php echo $notice['weight'] ?></td>
                                <td><?php echo $notice['submitter'] ?></td>
                            </tr>
                        <?php endforeach; ?>
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
                    <div class="page mt10">
                        <?php echo $pages[0] ?>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>

<script src="/source/date/WdatePicker.js"></script>
<!-- 字段排序js -->
<link rel="stylesheet" href="/caipiaoimg/v1.0/styles/admin/tablesorter.css?v=7">
<script type='text/javascript' src="/caipiaoimg/v1.0/js/jquery.tablesorter.js"></script>
<script>
    $(function () {
        $(".Wdate1").focus(function () {
            dataPicker();
        });

        $('#tablesorter').tablesorter({headers: {0: {sorter: false}, 1: {sorter: false}, 2: {sorter: false}, 3: {sorter: false}, 4: {sorter: false}, 5: {sorter: false}, 6: {sorter: false}, 10: {sorter: false}, 11: {sorter: false}, 12: {sorter: false}, 13: {sorter: false}}});

        $(".setTop").bind('click', function () {
            var _this = $(this);
            $.ajax({
                type: "get",
                url: _this.attr("href"),
                success: function (data) {
                    data = JSON.parse(data);
                    alert(data.message);
                    if (data.status == 'y') {
                        location.reload();
                    }
                }
            });
            return false;
        });

        $(".delete").bind('click', function () {
            var _this = $(this);
            $.ajax({
                type: "get",
                url: _this.attr("href"),
                success: function (data) {
                    data = JSON.parse(data);
                    alert(data.message);
                    if (data.status == 'y') {
                        location.reload();
                    }
                }
            });
            return false;
        });

    });

</script>
