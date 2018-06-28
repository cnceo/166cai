(function() {
    window.cx || (window.cx = {});

    var Order = cx.Order = (function() {
        var me = {};

        me.getStatus = function(status, returnFlag) {
            status = parseInt(status, 10);
            returnFlag = parseInt(returnFlag, 10);
            var cnStatus = '未知';
            
            switch (status) {
            	case 0:
            		cnStatus = '创建订单';
            		break;
            	case 10:
            		cnStatus = '待付款';
            		break;
            	case 20:
            	case 21:
            		cnStatus = '投注失败';
            		break;
            	case 30:
            		cnStatus = '付款中';
            		break;
            	case 40:
            		cnStatus = '已付款';
            		break;
            	case 200:
            		cnStatus = '等待出票';
            		break;
            	case 240:
            		cnStatus = '出票中';
            		break;
            	case 500:
            		cnStatus = '等待开奖';
            		break;
            	case 510:
            		cnStatus = '等待开奖';
            		break;
            	case 600:
            		cnStatus = '出票失败';
            		break;
            	case 1000:
            		cnStatus = '未中奖';
            		break;
            	case 2000:
            		switch (returnFlag) {
            			case 0:
            				cnStatus = '系统算奖中';
            				break;
            			case 1:
            				cnStatus = '已派奖';
            				break;
            			case 2:
            				cnStatus = '大奖待审核';
            				break;
            			case 3:
            				cnStatus = '已派奖';
            				break;
            			case 4:
            				cnStatus = '派奖失败';
            				break;
            			case 4:
            				cnStatus = '奖金已自提';
            				break;
            			default:
            				cnStatus = '未知';
            				break;
            		}
            		break;
            	default:
    				cnStatus = '未知';
    				break;
            }
            
//            if (status <= 100) {
//                cnStatus = '未付款';
//            } else if (status == 200) {
//                cnStatus = '待出票';
//            } else if (status <= 300 || status == 501) {
//                cnStatus = '出票中';
//            } else if (status == 500) {
//                cnStatus = '等待开奖';
//            } else if (status == 600) {
//                if (returnFlag == 2) {
//                    cnStatus = '系统撤单';
//                }else if(returnFlag == 3) {
//                	cnStatus = '出票失败';
//                } else {
//                    cnStatus = '用户撤单';
//                }
//            } else if (status == 1000) {
//                cnStatus = '未中奖';
//            } else if (status == 1500) {
//                cnStatus = '大奖待审核';
//            } else if (status == 2000) {
//                cnStatus = '中奖';
//            } else if (status == 5000) {
//                cnStatus = '已派奖'
//            }
            return cnStatus;
        };

        return me;
    })();
    
    var HmOrder = cx.HmOrder = (function() {
        var me = {};

        me.getStatus = function(status, returnFlag) {
            status = parseInt(status, 10);
            returnFlag = parseInt(returnFlag, 10);
            var cnStatus = '未知';
            
            switch (status) {
            	case 0:
            		cnStatus = '等待出票';
            		break;
            	case 20:
            		cnStatus = '过期未付款';
            		break;
            	case 40:
            		cnStatus = '等待出票';
            		break;
            	case 240:
            		cnStatus = '出票中';
            		break;
            	case 500:
            		cnStatus = '等待开奖';
            		break;
            	case 600:
            		cnStatus = '方案撤单';
            		break;
            	case 610:
            		cnStatus = '发起人撤单';
            		break;
            	case 620:
            		cnStatus = '未满员撤单';
            		break;
            	case 1000:
            		cnStatus = '未中奖';
            		break;
            	case 2000:
            		switch (returnFlag) {
            			case 0:
            				cnStatus = '系统算奖中';
            				break;
            			case 1:
            				cnStatus = '已派奖';
            				break;
            			case 2:
            				cnStatus = '大奖待审核';
            				break;
            			case 3:
            				cnStatus = '已派奖';
            				break;
            			case 4:
            				cnStatus = '派奖失败';
            				break;
            			case 4:
            				cnStatus = '奖金已自提';
            				break;
            			default:
            				cnStatus = '未知';
            				break;
            		}
            		break;
            	default:
    				cnStatus = '未知';
    				break;
            }
            return cnStatus;
        };

        return me;
    })();

})();
