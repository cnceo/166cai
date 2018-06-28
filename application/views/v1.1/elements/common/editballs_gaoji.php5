<?php if(!empty($editballs)):?>
<script>
	var types = {
		'ssq':{
			'1':'default',
			'135':'dt'
		},
		'dlt':{
			'1':'default',
			'2':'default',
			'135':'dt'
		},
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
        'syxw':{
        	00:'rx8',
        	01:'q1',
        	02:'rx2',
        	03:'rx3',
        	04:'rx4',
        	05:'rx5',
        	06:'rx6',
        	07:'rx7',
        	08:'rx8',
        	09:'qzhi2',
        	10:'qzhi3',
        	11:'qzu2',
        	12:'qzu3',
        	13:'lexuan3',
        	14:'lexuan4',
        	15:'lexuan5'
        },
        'jxsyxw':{
        	00:'rx8',
        	01:'q1',
        	02:'rx2',
        	03:'rx3',
        	04:'rx4',
        	05:'rx5',
        	06:'rx6',
        	07:'rx7',
        	08:'rx8',
        	09:'qzhi2',
        	10:'qzhi3',
        	11:'qzu2',
        	12:'qzu3'
        },
        'hbsyxw':{
        	00:'rx8',
        	01:'q1',
        	02:'rx2',
        	03:'rx3',
        	04:'rx4',
        	05:'rx5',
        	06:'rx6',
        	07:'rx7',
        	08:'rx8',
        	09:'qzhi2',
        	10:'qzhi3',
        	11:'qzu2',
        	12:'qzu3'
        },
        'cqssc':{
        	1:'dxds', 
			10:'1xzhi', 
			20:'2xzhi', 
			21:'2xzhi', 
			23:'2xzu',
			27:'2xzu',
			30:'3xzhi', 
			31:'3xzhi', 
			33:'3xzu3', 
			34:'3xzu6',
			37:'3xzu3',
            38:'3xzu6', 
            40:'5xzhi', 
            41:'5xzhi', 
            43:'5xt'
        }
    };
    var getBetMoney = function(enName, playType, postStr) {
        switch (enName) {
        	case 'syxw':
        		switch (playType) {
        			case 13:
            			return 6;
            			break;
        			case 14:
            			return 10;
            			break;
        			case 15:
            			return 14;
            			break;
            		default:
                		return 2;
            			break;
        		}
            	break;
        	case 'dlt':
        		switch (playType) {
	        		case 2:
	        		case 135:
		        		if (postStr == 1) {
		        			cx._basket_.zj = true;
		        			cx._basket_.setBetMoney(3);
		            		return 3;
		        		}
	        			return 2;
	            		break;
	        		default:
	            		return 2;
            			break;
        		}
        		break;
            default:
                return 2;
            	break;
        }
    }
    var betMoney = {
    	'syxw':{
        	13 : 6,
        	14 : 10,
        	15 : 14
    	}
   	}
    
	$(function() {
		var balls = [];
	<?php foreach ($editballs as $key => $allball):
		if ($enName == 'dlt' && ($allball[1] == 2 || ($allball[1] == 135 && $allball[2] == 1))) {?>
			if (cx._basket_.zj == false) {
				$(this).find("#addBets").attr('checked', 'checked');
				cx._basket_.zj = true;
			}
		<?php }?>
		balls[<?php echo $key ?>] = {};
		balls[<?php echo $key ?>].balls = [];
		<?php foreach ($allball[0] as $k => $ball) {?>
		balls[<?php echo $key ?>].balls[<?php echo $k?>] = {};
		<?php $ball = str_replace(array('[', ']'), '', $ball);
			if (strpos($ball, "$") !== false) {
			$bl = explode("$", $ball)?>
			balls[<?php echo $key ?>].balls[<?php echo $k?>]['dan'] =[<?php echo $bl[0]?>];
		<?php $ball = $bl[1];}?>
		balls[<?php echo $key ?>].balls[<?php echo $k?>]['tuo'] = [<?php echo $ball?>];
		<?php }?>
		<?php if (in_array($enName, array('syxw', 'jxsyxw', 'hbsyxw'))) {
			if ($allball[2] == '05') {?>
				balls[<?php echo $key ?>].playType = types['<?php echo $enName ?>'][<?php echo $allball[1] ?>]+'dt';
			<?php }else {?>
				balls[<?php echo $key ?>].playType = types['<?php echo $enName ?>'][<?php echo $allball[1] ?>];
			<?php }?>
		<?php } else { ?>
		balls[<?php echo $key ?>].playType = types['<?php echo $enName ?>'][<?php echo $allball[1] ?>];
		<?php } ?>
		var p = 1, danl;
		$.each(balls[<?php echo $key ?>].balls, function(i, e){
			danl = 0;
			danl = balls[<?php echo $key ?>].balls[i]['dan'] ? balls[<?php echo $key ?>].balls[i]['dan'].length : danl;
			<?php if ($enName === 'cqssc') {?> 
			cx._basket_.setType(balls[<?php echo $key ?>].playType);
			<?php } ?>
			if ('<?php echo $allball[1] ?>' == '33') {
				p = 1;
			}else if ('<?php echo $allball[1] ?>' == '37') {
				p *= cx.Math.combine(balls[<?php echo $key ?>].balls[i]['tuo'].length, 2) * 2;
			}else {
				p *= cx.Math.combine(balls[<?php echo $key ?>].balls[i]['tuo'].length, cx._basket_.boxes[balls[<?php echo $key ?>].playType].boxes[i].options.min-danl);
			}
		})
		balls[<?php echo $key ?>].betNum = p;
		balls[<?php echo $key ?>].betMoney = p*getBetMoney('<?php echo $enName?>', <?php echo $allball[1] ?>, <?php echo $allball[2] ?>);
	<?php endforeach;?>
	cx._basket_.addAll(balls);
	<?php if (in_array($enName, array('syxw', 'jxsyxw', 'hbsyxw'))) {?>
	cx._basket_.setType(types['<?php echo $enName ?>'][0]);
	<?php }elseif (in_array($enName, array('cqssc'))) {?>
	cx._basket_.setType(types['<?php echo $enName ?>'][10]);
	<?php }else {?>
	cx._basket_.setType(types['<?php echo $enName ?>'][1]);
	<?php } ?>
	})
</script>
<?php endif;?>