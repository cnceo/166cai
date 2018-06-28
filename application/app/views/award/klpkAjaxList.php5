<tbody>
<?php if($awards):?>
<?php foreach ($awards as $key => $items):?>
<tr>
    <td><?php echo substr($items['seExpect'], 6, 2); ?>期</td>
    <td>
	<div class="poker">
	    <?php
		$award = explode(':', $items['awardNumber']);
		$awArr = array(explode(',', $award[0]), explode(',', $award[1]));
	    ?>
	    <?php foreach ($awArr[0] as $k => $number):?>
	    <span class="<?php echo strtolower($awArr[1][$k]); ?>"><?php echo str_replace(array('01', '11', '12', '13'), array('A', 'J', 'Q', 'K'), ($number == '01' ? $number : intval($number)));?></span>
	    <?php endforeach;?>
	</div></td>
    <td><?php echo $items['mark']; ?></td>
</tr>
<?php endforeach;?>
<?php endif;?>                
</tbody>             