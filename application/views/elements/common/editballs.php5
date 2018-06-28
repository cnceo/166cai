<?php if(!empty($editballs)):?>
<script>
	$(function() {
	<?php foreach ($editballs as $allball):?>
		cx._basket_.add(cx._basket_.boxes.addBall([<?php echo implode(', ', $allball[0]);?>]));
		cx._basket_.boxes.removeAll();
	<?php endforeach;?>
	})
</script>
<?php endif;?>