<?php if(!empty($editballs)):?>
<script>
	var types = {
        'fcsd':{
            '1':'zx',
            '2':'z3',
            '3':'z6'
        },
        'pls':{
            '1':'zx',
            '2':'z3',
            '3':'z6'
        },
        'plw':{
            '1':'default'
        },
        'qlc':{
            '1':'default'
        },
        'qxc':{
            '1':'default'
        }
    };
	$(function() {
	<?php foreach ($editballs as $allball):?>
		cx._basket_.setType(types['<?php echo $enName ?>'][<?php echo $allball[1] ?>]);
		cx._basket_.add(cx._basket_.boxes[types['<?php echo $enName ?>'][<?php echo $allball[1] ?>]].addBall([<?php echo implode(', ', $allball[0]);?>], <?php echo $allball[2]?>));
		cx._basket_.boxes[types['<?php echo $enName ?>'][<?php echo $allball[1] ?>]].removeAll();
	<?php endforeach;?>
	cx._basket_.setType(types['<?php echo $enName ?>'][1]);
	})
</script>
<?php endif;?>