<?php $this->load->view("templates/head") ?>
<div class="path">您的位置：信息管理&nbsp;&gt;&nbsp;<a href="/backend/Info/center">资讯中心管理</a></div>
<div class="mod-tab mt20">
    <div class="mod-tab-hd">
        <ul>
            <li><a href="/backend/Info/crawl">资讯抓取配置</a></li>
            <li><a href="/backend/Info/center">资讯管理</a></li>
            <li class="current"><a href="/backend/Info/nba">NBA伤病管理</a></li>
            <li><a href="/backend/Info/banner">banner图管理</a></li>
        </ul>
    </div>
    <div class="mod-tab-bd">
    <form action="" method="post">
        <ul>
            <li style="display: block">
            <?php foreach ($sqArr as $s => $sq) {?>
                <div class="data-table-list mt10">
                    <h2 class="team-part"><?php echo $sq?>赛区</h2>
                    <table>
                        <colgroup>
                            <col width="60%"/>
                            <col width="20%"/>
                            <col width="20%"/>
                        </colgroup>
                        <thead>
                        <tr>
                            <th>球队</th>
                            <th>排序</th>
                            <th>操作</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php for ($i = 1; $i <= 5; $i++) {?>
                        <tr>
                            <td><?php echo $data[$s][$i]['team']?></td>
                            <td>
                            	<input type="text" class="ipt w80 tac" name="info[<?php echo $s?>][<?php echo $i?>][priority]" value="<?php echo $i?>">
                            	<input type="hidden" name="info[<?php echo $s?>][<?php echo $i?>][id]" value="<?php echo $data[$s][$i]['id']?>">
                            </td>
                            <td><a href="/backend/Info/nbaedit/<?php echo $data[$s][$i]['id']?>" class="cBlue">编辑</a></td>
                        </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            <?php }?>
            </li>
        </ul>
        <p class="audit-detail-btns mt20 ml40" style="float:right"><input type="submit" class="btn-blue mr20" value="保存"></p>
        
    </form>
    </div>
</div>