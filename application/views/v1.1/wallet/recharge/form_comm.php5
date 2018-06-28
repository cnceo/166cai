<input type='hidden' class='vcontent' name='mode' value='<?php echo isset($pay_way[0]) ? $pay_way[0]['mode'] : '';?>'>
<input type='hidden' class='vcontent ipt_fee' name='p3_Amt' value='20'> 
<input type='hidden' class='vcontent' name='pd_FrpId' value=''>
<input type='hidden' class='vcontent' name='redpack' value=''> 
<div class="form-item">
    <div class="form-item-con">
        <div class="tab-nav">
            <ul>
            <?php foreach($pay_list as $k => $v ): ?>
                <li <?php echo $k == $mode_str ? 'class="active" ' : ''; ?> ><a href="<?php echo $v['url'] ?>"><span><?php echo $v['name'] ?></span></a><?php if($v['guide']){ ?><div class="icon-bank-intro"><b><s><?php echo $v['guide'] ?></s></b></div><?php } ?></li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>