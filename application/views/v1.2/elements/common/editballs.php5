<?php if(!empty($codes)):?>
<script>
    var getBetMoney = function(enName, code1, code2) {
        switch (enName) {
        	case 'syxw':
        		switch (code1) {
        			case '13':
            			return 6;
            			break;
        			case '14':
            			return 10;
            			break;
        			case '15':
            			return 14;
            			break;
            		default:
                		return 2;
            			break;
        		}
            	break;
        	case 'dlt':
        		switch (code1) {
	        		case '2':
	        			$("#addBets").attr('checked', 'checked');
	        			cx._basket_.zj = true;
	        			cx._basket_.setBetMoney(3);
	            		return 3;
	            		break;
	        		case '135':
		        		if (code2 == 1) {
		        			$("#addBets").attr('checked', 'checked');
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
		<?php
		foreach (explode(';', $codes[0]) as $code) {?>
		var codesArr = '<?php echo $code?>'.split(':'), playType = cx.Lottery.getPlayTypeByMidCode(codesArr[1], codesArr[2]), minLength = cx.Lottery.getMinLength(playType),
		placeSeparator = cx.Lottery.getPlaceSeparator(playType), tmpBall = {'balls':[], betNum:1, betMoney:2, playType:playType};
		<?php if (in_array($enName, array('cqssc'))) {?>
		cx._basket_.setType(playType);
		<?php }?>
		if (placeSeparator !== '') {
			codes0 = codesArr[0].split(cx.Lottery.getPlaceSeparator(playType));
		}else {
			codes0 = [codesArr[0]];
		}
		$.each(codes0, function(k, codes){
			var tmpball = {}, codeArr = codes.split('$');
			if (codeArr.length == 2) {
				tmpball['dan'] = codeArr[0].split(cx.Lottery.getNumberSeparator(playType));
				tmpball['tuo'] = codeArr[1].split(cx.Lottery.getNumberSeparator(playType));
			}else {
				tmpball['tuo'] = codeArr[0].split(cx.Lottery.getNumberSeparator(playType));
			}
			tmpBall['betNum'] *= cx.Math.combine(tmpball['tuo'].length, minLength[k] - (codeArr.length == 2 ? tmpball['dan'].length : 0));
			tmpBall['balls'].push(tmpball);
		}) 
		if (playType.indexOf('z3') > -1 || playType.indexOf('3xzu3') > -1) {
			if (($.inArray('<?php echo $enName?>', ['pls', 'fcsd']) > -1 && codesArr[2] == 1) || ('<?php echo $enName?>' === 'cqssc' && codesArr[1] == 33)) {
				tmpBall['betNum'] = 1;
			} else {
				tmpBall['betNum'] *= 2;
			}
		}
		tmpBall['betMoney'] = tmpBall['betNum'] * getBetMoney('<?php echo $enName?>', codesArr[1], codesArr[2]);
		balls.push(tmpBall);
		<?php }?>
		cx._basket_.addAll(balls);
		cx._basket_.setType(cx.Lottery.getPlayTypeByMidCode('default'));
	})
</script>
<?php endif;?>