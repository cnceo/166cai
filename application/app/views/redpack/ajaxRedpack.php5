<?php if(!empty($redpack)): ?>
<?php foreach( $redpack as $items ): ?>
<li>
    <?php if($items['eventType'] == '1'): ?>
    <a href="<?php echo $items['a_data']['url']; ?>" <?php if($items['a_data']['class']):?> data-val="<?php echo $items['c_type'];?>"<?php endif;?> class="red-packets-item<?php echo ' ' . $items['a_data']['class'];?> <?php echo ($items['status'] == '1' && $items['valid_start'] > date('Y-m-d H:i:s'))?'isbefore':''; ?>" <?php echo $items['a_data']['onclick'];?>>
    <?php else :?>
    <a href="<?php echo $items['a_data']['url']; ?>" class="red-packets-item">
    <?php endif;?>
        <div class="red-packets-num">
            <span>¥<b><?php echo $items['money']; ?></b><small><?php echo $items['p_name'];?></small></span>
        </div>
        <div class="red-packets-note">
            <h2><?php echo $items['use_desc']; ?></h2>
            <p>有效期：<?php echo date('Y/m/d',strtotime($items['valid_start'])) . '-' . date('Y/m/d',strtotime($items['valid_end'])); ?></p>
                <?php if($items['eventType'] == '1'): ?>
                <div class="action   <?php echo $items['c_type'] == '1' && $items['p_type'] == '1' ? 'ljlq ' : '';  ?> <?php echo ($items['status'] == '1' && $items['valid_start'] <= date('Y-m-d H:i:s'))?'main-color':''; ?>" data-rid = "<?php echo $items['id']; ?>" data-money="<?php echo $items['money']; ?>"><span><?php echo $items['btn']; ?></span></div>
                <?php endif; ?>
        </div>
        <?php if(!empty($items['left'])): ?>
        <p class="term-tip"><span><?php echo $items['left']; ?></span></p>
        <?php elseif(!empty($items['tips'])): ?>
        <p class="term-tip"><span><?php echo $items['tips']; ?></span></p>
        <?php endif;?>
    </a>
</li>
<?php endforeach; ?>
<?php endif; ?>