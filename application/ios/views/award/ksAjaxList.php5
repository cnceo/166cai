<tbody>
    <?php if($awards):?>
    <?php foreach ($awards as $key => $items):?>
    <tr>
        <td><?php echo substr($items['seExpect'], 9, 2); ?>期</td>
        <td>
            <span class="dice">
                <?php $awardArry = explode(',', $items['awardNumber']); ?>
                <?php foreach ($awardArry as $k => $number):?>
                <i class="d<?php echo $number; ?>"></i>
                <?php endforeach;?>
            </span><?php echo str_replace(',', '', $items['awardNumber']); ?></td>
        <td><?php echo array_sum(explode(',', $items['awardNumber'])); ?></td>
        <td><?php echo $items['mark']; ?></td>
    </tr>
    <?php endforeach;?>
    <?php endif;?>  
</tbody>              