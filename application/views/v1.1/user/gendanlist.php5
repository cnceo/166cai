<div class="mod-tab-item hemai-table" style="display: block">
    <?php $gendanArr = str_split ( $gendanAr ); ?>
    <button type="button" class="btn-ss btn-search gendan submit hidden">搜索</button>
    <input type="hidden" name="gendan" class="vcontent" value="<?php echo $gendanAr ? $gendanAr : '00'?>">        
    <table class="mod-tableA mt20">
        <thead>
            <tr>
                <th width="30"></th>
                <th width="80" class="tal">彩种</th>
                <th width="122" class="tal"><span class="filter-arrow <?php if ($gendanArr[0] == 0) {if ($gendanArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="0">合买战绩<i></i></span></th>
                <th width="72" class="tar"><span class="filter-arrow <?php if ($gendanArr[0] == 1) {if ($gendanArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>"  data-value="1">中奖次数<i></i></span></th>
                <th width="130" class="tar"><span class="filter-arrow <?php if ($gendanArr[0] == 2) {if ($gendanArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="2">累计奖金<i></i></span></th>
                <th width="120" class="tar"><span class="filter-arrow <?php if ($gendanArr[0] == 3) {if ($gendanArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="3">定制人数<i></i></span></th>
                <th width="72" class=""><span class="filter-arrow <?php if ($gendanArr[0] == 4) {if ($gendanArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="4">历史定制<i></i></span></th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php $lottery=array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '11' => '胜负/任九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三/五') ;?>
            <?php foreach ($gendanlists as $gendanlist) { ?>            
            <tr class="<?php if(in_array($uid.','.$gendanlist['lid'], $hasgendan)){ echo "hasfollow";}?>">
                <td><?php if(in_array($uid.','.$gendanlist['lid'], $hasgendan)){ ?><span class="icon-follow">已定</span><?php } ?></td>
                <td class="tal"><?php echo $lottery[$gendanlist['lid']]; ?></td>
                <td class="tal">
                    <span class="level">
                        <span class="level">
                        <?php if(in_array($uid.','.$gendanlist['lid'], $hasgendan)){
                           echo calGrade($gendanlist['united_points'], 5, 3);
                        }else{
                            echo calGrade($gendanlist['united_points'], 5, '');
                        } ?>
                        </span>
                    </span>
                </td>
                <td class="tar"><?php echo $gendanlist['winningTimes']; ?>次</td>
                <td class="tar"><em class="main-color-s"><?php echo number_format($gendanlist['bonus']/100,2); ?></em>元</td>
                <td class="tar" colspan="2"><?php echo $gendanlist['isFollowNum']; ?>人(<?php echo $gendanlist['followTimes']; ?>人定过) <?php if($gendanlist['isFollowNum']>0){ ?><a href="javascript:;" data-lid="<?php echo $gendanlist['lid']; ?>" data-uid="<?php echo $uid; ?>" class="gendanlist">[查看]</a><?php } ?></td>
                <td>
                    <?php if(!in_array($uid.','.$gendanlist['lid'], $hasgendan)){ ?>
                    <a href="javascript:;" data-lid="<?php echo $gendanlist['lid']; ?>" data-uid="<?php echo $uid; ?>" class="btn-ss btn-main btn-gendan <?php echo $showBind ? ' not-bind': '';?>">立即定制</a>
                    <?php }else{ ?>
                    <a href="javascript:;" class="btn-ss btn-main btn-dzgd btn-disabled">已定制</a>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
        </tbody> 
    </table>
</div>
<script>
var gendanArr = '<?php echo isset($gendanAr) ? $gendanAr : '00'?>'.split('');
</script>
