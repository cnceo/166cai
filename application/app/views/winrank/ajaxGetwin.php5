<?php foreach($list as $k=>$v){?>
<tr>
    <td><?php echo $v['rankId']?></td>
    <td>
    <?php if(mb_strlen($v['userName'],'utf-8') > 3 ){echo mb_substr($v['userName'],0,3,'utf-8').'***';}?>
    <?php if(mb_strlen($v['userName'],'utf-8') == 3){echo mb_substr($v['userName'],0,2,'utf-8').'*';}?>
    <?php if(mb_strlen($v['userName'],'utf-8') < 3 ){echo mb_substr($v['userName'],0,1,'utf-8').'*';}?>
    </td>
    <td><?php echo ParseUnit($v['margin'], 1)?>元</td>
    <td><?php echo ParseUnit($v['addMoney'], 1)?>元</td>
</tr>
<?php } ?>