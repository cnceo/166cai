(function() {
	
    window.cx || (window.cx = {});
    cx.closeCount = false;
    var Lottery = cx.Lottery = (function() {

        var me = {DLT: 23529, SYXW: 21406, JXSYXW: 21407, HBSYXW: 21408, SSQ: 51, KS:53, KLPK:54, FCSD:52, PLS:33, PLW:35, QLC:23528, QXC:10022, CQSSC:55};

        function _getNumberSeparator(lotteryId, playType) {
            var NUMBER_SEPARATOR = {};
            NUMBER_SEPARATOR[me.DLT] = {'default': ','};
            NUMBER_SEPARATOR[me.SSQ] = {'default': ','};
            NUMBER_SEPARATOR[me.SYXW] = {'default': ','};
            NUMBER_SEPARATOR[me.JXSYXW] = {'default': ','};
            NUMBER_SEPARATOR[me.HBSYXW] = {'default': ','};
            NUMBER_SEPARATOR[me.CQSSC] = {'default': ',', '1xzhi': '', '2xzhi': '', '3xzhi': '', '5xzhi': '', '5xt': ''};
            NUMBER_SEPARATOR[me.KS] = {'default': ',', 'hz': ',', 'sthtx': ',', 'sthdx': ',', 'sbth': ',', 'slhtx': ',', 'ethfx': ',', 'ebth': ','};

            var separator = ',';
            playType || (playType = 'default');
            if (lotteryId in NUMBER_SEPARATOR && playType in NUMBER_SEPARATOR[lotteryId]) separator = NUMBER_SEPARATOR[lotteryId][playType];

            return separator;
        }
        
        function _getPlaceSeparator(lotteryId, playType) {
            var PLACE_SEPARATOR = {};
            PLACE_SEPARATOR[me.DLT] = {'default': '|'};
            PLACE_SEPARATOR[me.SSQ] = {'default': '|'};
            PLACE_SEPARATOR[me.CQSSC] = {'1xzhi': ',', '2xzhi': ',', '3xzhi': ',', '5xzhi': ',', '5xt': ',', 'dxds':','};
            PLACE_SEPARATOR[me.KS] = {'default': ',', 'hz': ',', 'sthtx': ',', 'sthdx': ',', 'sbth': ',', 'slhtx': ',', 'ethfx': ',', 'ebth': ',' };

            var separator = '|';
            playType || (playType = 'default');
            if (lotteryId in PLACE_SEPARATOR && playType in PLACE_SEPARATOR[lotteryId]) separator = PLACE_SEPARATOR[lotteryId][playType];

            return separator;
        }

        function _hasPaddingZero(lotteryId, playType) {
            var PADDING_ZERO = {};
            PADDING_ZERO[me.DLT] = {'default': true};
            PADDING_ZERO[me.SSQ] = {'default': true};
            PADDING_ZERO[me.SYXW] = {'default': true};
            PADDING_ZERO[me.JXSYXW] = {'default': true};
            PADDING_ZERO[me.HBSYXW] = {'default': true};
            PADDING_ZERO[me.CQSSC] = {'default': false};
            PADDING_ZERO[me.KS] = {'default': false};
            PADDING_ZERO[me.KLPK] = {'default': true};

            var hasPadding = true;
            playType || (playType = 'default');
            if (lotteryId in PADDING_ZERO) hasPadding = PADDING_ZERO[lotteryId]['default'];
            return hasPadding;
        }

        me.playTypes = {
            23529: {'default': 1, 'zj' : 2, dt : 135},
            51: {'default': 1, dt : 135},
            21406: {'default': '05', q1: '01', rx2: '02', rx2dt: '02', rx3: '03', rx3dt: '03', rx4: '04', rx4dt: '04', rx5: '05', rx5dt: '05', rx6: '06', rx6dt: '06', rx7: '07', 
                rx7dt: '07', rx8: '08', qzhi2: '09', qzhi3: '10', qzu2: '11', qzu2dt: '11', qzu3: '12', qzu3dt: '12', lexuan3: '13', lexuan4: '14', lexuan5: '15'},
            21407: {'default': '05', q1: '01', rx2: '02', rx2dt: '02', rx3: '03', rx3dt: '03', rx4: '04', rx4dt: '04', rx5: '05', rx5dt: '05', 
                rx6: '06', rx6dt: '06', rx7: '07', rx7dt: '07', rx8: '08', qzhi2: '09', qzhi3: '10', qzu2: '11', qzu2dt: '11', qzu3: '12', qzu3dt: '12'},
            21408: {'default': '05', q1: '01', rx2: '02', rx2dt: '02', rx3: '03', rx3dt: '03', rx4: '04', rx4dt: '04', rx5: '05', rx5dt: '05', 
                rx6: '06', rx6dt: '06', rx7: '07', rx7dt: '07', rx8: '08', qzhi2: '09', qzhi3: '10', qzu2: '11', qzu2dt: '11', qzu3: '12', qzu3dt: '12'},
            53:{'hz' : '1', 'sthtx' : '2', 'sthdx' : '3', 'sbth' : '4', 'slhtx' : '5', 'ethfx' : '6', 'ethdx' : '7', 'ebth'  : '8'},
            54:{rx1: '1', rx2: '2', rx2dt: '22', rx3: '3', rx3dt: '32', rx4: '4', rx4dt: '42', rx5: '5', rx5dt: '52', rx6: '6', rx6dt: '62', th: '7', ths:'8', sz: '9', bz: '10', dz: '11'},
            55:{'1xzhi': '10', '2xzhi': ['20', '21'], '2xzu': ['23', '27'], '3xzhi': ['30', '31'], '3xzu3': ['33', '37'], '3xzu6': ['34', '38'], '5xzhi': ['40', '41'], '5xt':'43', 'dxds': '1'}
        };
        
        me.jiangjin = {
    		33:{zx:'1040', z3:'346', z6:'173'},
            35:{'default':'100000'},
            21407:{q1:'13',rx2:'6',rx2dt:'6',rx3:'19',rx3dt:'19',rx4:'78',rx4dt:'78',rx5:'540',rx5dt:'540',rx6:'90',
            	rx6dt:'90',rx7:'26',rx7dt:'26',rx8:'9',qzhi2:'130',qzhi3:'1170',qzu2:'65',qzu2dt:'65',qzu3:'195',qzu3dt:'195'},
            21408:{q1:'13',rx2:'6',rx2dt:'6',rx3:'19',rx3dt:'19',rx4:'78',rx4dt:'78',rx5:'540',rx5dt:'540',rx6:'90',
            	rx6dt:'90',rx7:'26',rx7dt:'26',rx8:'9',qzhi2:'130',qzhi3:'1170',qzu2:'65',qzu2dt:'65',qzu3:'195',qzu3dt:'195'},
            54:{'rx1':5,'rx2':33,'rx2dt':33,'rx3':116,'rx3dt':116,'rx4':46,'rx4dt':46,'rx5':22,'rx5dt':22,'rx6':12,'rx6dt':12,
            	'th':90,'thbx':22,'ths':2150,'thsbx':535,'sz':400,'szbx':33,'bz':6400,'bzbx':500,'dz':88,'dzbx':7}
        }

        function _getCastPost(lotteryId, playType) {
            var CAST_POST = {};
            CAST_POST[me.DLT] = {'default': '1', 'zj' : '1', dt:'5',};
            CAST_POST[me.SSQ] = {'default': '1', dt:'5'};
            CAST_POST[me.SYXW] = {'default': '01', 'rx2': '01', 'rx2dt': '05', 'rx3': '01', 'rx3dt': '05', 'rx4': '01', 'rx4dt': '05', 'rx5': '01', 'rx5dt': '05', 'rx6': '01',
                'rx6dt': '05', 'rx7': '01', 'rx7dt': '05', 'rx8': '01', 'q1':'01', 'qzhi2': '01', 'qzhi3': '01', 'qzu2': '01', 'qzu2dt': '05', 'qzu3': '01', 'qzu3dt': '05',
                'lexuan3': '01', 'lexuan4': '01', 'lexuan5': '01'};
            CAST_POST[me.JXSYXW] = {'default': '01', 'rx2': '01', 'rx2dt': '05', 'rx3': '01', 'rx3dt': '05', 'rx4': '01', 'rx4dt': '05', 'rx5': '01', 'rx5dt': '05', 'rx6': '01',
                'rx6dt': '05', 'rx7': '01', 'rx7dt': '05', 'rx8': '01', 'q1':'01', 'qzhi2': '01', 'qzhi3': '01', 'qzu2': '01', 'qzu2dt': '05', 'qzu3': '01', 'qzu3dt': '05'};
            CAST_POST[me.HBSYXW] = {'default': '01', 'rx2': '01', 'rx2dt': '05', 'rx3': '01', 'rx3dt': '05', 'rx4': '01', 'rx4dt': '05', 'rx5': '01', 'rx5dt': '05', 'rx6': '01',
                'rx6dt': '05', 'rx7': '01', 'rx7dt': '05', 'rx8': '01', 'q1':'01', 'qzhi2': '01', 'qzhi3': '01', 'qzu2': '01', 'qzu2dt': '05', 'qzu3': '01', 'qzu3dt': '05'};
            CAST_POST[me.KLPK] = {'default': '1'};
            CAST_POST[me.CQSSC] = {'default': '1'};

            var post = '1';
            playType || (playType = 'default');
            if (lotteryId in CAST_POST && playType in CAST_POST[lotteryId]) post = CAST_POST[lotteryId][playType];
            return post;
        };

        me.getPlayTypeName = function(lotteryId, playType) {
            var cnName = '';
            var playCnNames = {};
            if ($.inArray(lotteryId, [me.SYXW]) > -1) {
            	playCnNames = {'01': '前一', '02': '任二', '03': '任三', '04': '任四', '05': '任五', '06': '任六', '07': '任七', '08': '任八', '09': '前二直选', '10': '前三直选', '11': '前二组选', '12': '前三组选', '13': '乐三', '14': '乐四', '15': '乐五'};
                cnName = playCnNames[playType];
            }else if ($.inArray(lotteryId, [me.SYXW, me.JXSYXW, me.HBSYXW]) > -1) {
                playCnNames = {'01': '前一', '02': '任二', '03': '任三', '04': '任四', '05': '任五', '06': '任六', '07': '任七', '08': '任八', '09': '前二直选', '10': '前三直选', '11': '前二组选', '12': '前三组选'};
                cnName = playCnNames[playType];
            }else if(lotteryId === me.DLT){
            	playCnNames = {0: '普通', 1: '普通', 2: '普通 ', 135: '胆拖'};
            	cnName = playCnNames[playType] ? playCnNames[playType] : '普通';
            } else if(lotteryId === me.SSQ){
            	playCnNames = {0: '普通', 1: '普通', 135: '胆拖'};
            	cnName = playCnNames[playType] ? playCnNames[playType] : '普通';
            }else if(lotteryId === me.KS){
            	playCnNames = {1: '和值', 2: '三同号通选', 3: '三同号单选', 4: '三不同号', 5: '三连号通选', 6: '二同号复选', 7: '二同号单选', 8: '二不同号'};
                cnName = playCnNames[playType] ? playCnNames[playType] : '普通';
            }else if(lotteryId === me.KLPK){
            	playCnNames = {1: '任一', 2: '任二单式', 21:'任二复式', 22:'任二胆拖', 3: '任三单式', 31:'任三复式', 32:'任三胆拖', 4: '任四单式', 41:'任四复式',
                	42:'任四胆拖', 5: '任五单式', 51:'任五复式', 52:'任五胆拖', 6: '任六单式', 61:'任六复式', 62:'任六胆拖', 7: '同花', 8: '同花顺', 9: '顺子', 10:'豹子', 11:'对子'};
                cnName = playCnNames[playType] ? playCnNames[playType] : '普通';
            }else if(lotteryId === me.CQSSC){
            	playCnNames = {1: '大小单双', 10: '一星直选', 20:'二星直选', 21:'二星直选', 23: '二星组选', 27: '二星组选', 30:'三星直选', 31:'三星直选', 33: '三星组三单', 34:'三星组六',
                    	37: '三星组三复', 38: '三星组六', 40:'五星直选', 41:'五星直选', 43: '五星通选'};
                cnName = playCnNames[playType] ? playCnNames[playType] : '普通';
            } else if ($.inArray(lotteryId, [me.PLS, me.FCSD]) > -1) {
            	playCnNames = {1: '直选', 2: '组三', 3: '组六'};
            	cnName = playCnNames[playType] ? playCnNames[playType] : '普通';
            } else {
                cnName = '普通';
            }

            return cnName;
        };
        
        me.getMinLength = function(lotteryId, playType) {
        	var MIN_LENGTH = {};
        	MIN_LENGTH[me.DLT] = {'default': [5, 2], 'dt' : [5, 2], 'ddsh' : [5, 2]};
        	MIN_LENGTH[me.SSQ] = {'default': [6, 1], 'dt' : [6, 1], 'ddsh' : [6, 1]};
        	MIN_LENGTH[me.SYXW] = {'default': [1], 'rx2': [2], 'rx2dt': [2], 'rx3': [3], 'rx3dt': [3], 'rx4': [4], 'rx4dt': [4], 'rx5': [5], 'rx5dt': [5], 'rx6': [6],
                'rx6dt': [6], 'rx7': [7], 'rx7dt': [7], 'rx8': [8], 'q1': [1], 'qzu2': [2], 'qzu2dt': [2], 'qzhi2': [1, 1], 'qzu3': [3], 'qzu3dt': [3], 'qzhi3': [1, 1, 1],
                'lexuan3': [1, 1, 1], 'lexuan4': [4], 'lexuan5': [5]};
        	MIN_LENGTH[me.JXSYXW] = {'default': [1], 'rx2': [2], 'rx2dt': [2], 'rx3': [3], 'rx3dt': [3], 'rx4': [4], 'rx4dt': [4], 'rx5': [5], 'rx5dt': [5], 'rx6': [6],
                'rx6dt': [6], 'rx7': [7], 'rx7dt': [7], 'rx8': [8], 'q1': [1], 'qzu2': [2], 'qzu2dt': [2], 'qzhi2': [1, 1], 'qzu3': [3], 'qzu3dt': [3], 'qzhi3': [1, 1, 1]};
        	MIN_LENGTH[me.HBSYXW] = {'default': [1], 'rx2': [2], 'rx2dt': [2], 'rx3': [3], 'rx3dt': [3], 'rx4': [4], 'rx4dt': [4], 'rx5': [5], 'rx5dt': [5], 'rx6': [6],
                'rx6dt': [6], 'rx7': [7], 'rx7dt': [7], 'rx8': [8], 'q1': [1], 'qzu2': [2], 'qzu2dt': [2], 'qzhi2': [1, 1], 'qzu3': [3], 'qzu3dt': [3], 'qzhi3': [1, 1, 1]};
        	MIN_LENGTH[me.KS] = {'default': [1], 'hz': [1]};
        	MIN_LENGTH[me.PLS] = {'default': [1, 1, 1], 'zx': [1, 1, 1], 'z3': [3], 'z6': [3]};
        	MIN_LENGTH[me.PLW] = {'default': [1, 1, 1, 1, 1]};
        	MIN_LENGTH[me.QXC] = {'default': [1, 1, 1, 1, 1, 1, 1]};
        	MIN_LENGTH[me.QLC] = {'default': [7]};
        	MIN_LENGTH[me.FCSD] = {'default': [1, 1, 1], 'zx': [1, 1, 1], 'z3': [3], 'z6': [3]};
        	MIN_LENGTH[me.CQSSC] = {'1xzhi': [1], '2xzhi': [1, 1], '2xzu': [2], '3xzhi': [1, 1, 1], '3xzu3': [3], '3xzu6': [3], '5xzhi': [1, 1, 1, 1, 1], '5xt': [1, 1, 1, 1, 1], 'dxds' : [1, 1]};
        	
        	var minlength = [];
            playType || (playType = 'default');
            if (lotteryId in MIN_LENGTH && playType in MIN_LENGTH[lotteryId]) minlength = MIN_LENGTH[lotteryId][playType];
            
            return minlength;
        }
        
        me.getAmount = function(lotteryId, playType) {
        	var AMOUNT = {};
        	AMOUNT[me.DLT] = {'default': [35, 12], 'dt': [35, 12], 'ddsh': [35, 12]};
        	AMOUNT[me.SSQ] = {'default': [33, 16], 'dt': [33, 16], 'ddsh': [33, 16]};
        	AMOUNT[me.SYXW] = {
        		'default': [11], 'rx2': [11], 'rx2dt': [11], 'rx3': [11], 'rx3dt': [11], 'rx4': [11], 'rx4dt': [11], 'rx5': [11], 'rx5dt': [11], 'rx6': [11], 'rx6dt': [11],
                'rx7': [11], 'rx7dt': [11], 'rx8': [11], 'q1': [11], 'qzu2': [11], 'qzu2dt': [11], 'qzhi2': [11, 11], 'qzu3': [11], 'qzu3dt': [11], 'qzhi3': [11, 11, 11],
                'lexuan3': [11, 11, 11], 'lexuan4': [11], 'lexuan5':[11]
            };
        	AMOUNT[me.JXSYXW] = {
                'default': [11], 'rx2': [11], 'rx2dt': [11], 'rx3': [11], 'rx3dt': [11], 'rx4': [11], 'rx4dt': [11], 'rx5': [11], 'rx5dt': [11], 'rx6': [11], 'rx6dt': [11],
                'rx7': [11], 'rx7dt': [11], 'rx8': [11], 'q1': [11], 'qzu2': [11], 'qzu2dt': [11], 'qzhi2': [11, 11], 'qzu3': [11], 'qzu3dt': [11], 'qzhi3': [11, 11, 11]
            };
        	AMOUNT[me.HBSYXW] = {
        		'default': [11], 'rx2': [11], 'rx2dt': [11], 'rx3': [11], 'rx3dt': [11], 'rx4': [11], 'rx4dt': [11], 'rx5': [11], 'rx5dt': [11], 'rx6': [11], 'rx6dt': [11],
                'rx7': [11], 'rx7dt': [11], 'rx8': [11], 'q1': [11], 'qzu2': [11], 'qzu2dt': [11], 'qzhi2': [11, 11], 'qzu3': [11], 'qzu3dt': [11], 'qzhi3': [11, 11, 11]
            };
        	AMOUNT[me.CQSSC] = {'1xzhi': [9], '2xzhi': [9, 9], '2xzu': [9], '3xzhi': [9, 9, 9], '3xzu3': [9], '3xzu6': [9], '5xzhi': [9, 9, 9, 9, 9], '5xt': [9, 9, 9, 9, 9], 'dxds' : [9, 9]};
        	var amount = [];
            playType || (playType = 'default');
            if (lotteryId in AMOUNT && playType in AMOUNT[lotteryId]) amount = AMOUNT[lotteryId][playType];
            return amount;
        }
        
        me.getStartIndex = function(lotteryId, playType) {
        	var STARTINDEX = {};
        	STARTINDEX[me.DLT] = {'default': [1, 1]};
        	STARTINDEX[me.SSQ] = {'default': [1, 1]};
        	STARTINDEX[me.SYXW] = {'default': [1], 'qzhi2': [1, 1], 'qzhi3': [1, 1, 1], 'lexuan3': [1, 1, 1]};
        	STARTINDEX[me.JXSYXW] = {'default': [1], 'qzhi2': [1, 1], 'qzhi3': [1, 1, 1]};
        	STARTINDEX[me.HBSYXW] = {'default': [1], 'qzhi2': [1, 1], 'qzhi3': [1, 1, 1]};
        	STARTINDEX[me.CQSSC] = {'1xzhi': [0], '2xzhi': [0, 0], '2xzu': [0], '3xzhi': [0, 0, 0], '3xzu3': [0], '3xzu6': [0], '5xzhi': [0, 0, 0, 0, 0], '5xt': [0, 0, 0, 0, 0], 'dxds' : [0, 0]};
        	
        	var startindex = [];
        	if (lotteryId in STARTINDEX && playType in STARTINDEX[lotteryId]) {
        		startindex = STARTINDEX[lotteryId][playType];
        	}else if (lotteryId in STARTINDEX) {
        		startindex = STARTINDEX[lotteryId]['default'];
        	}
            
            return startindex;
        }

        me.getCnName = function(lotteryId) {
            lotteryId = parseInt(lotteryId, 10);
            return {23529: '大乐透', 51: '双色球', 21406: '11运夺金', 21407: '新11选5', 21408: '惊喜11选5', 53: '经典快3', 54: '快乐扑克', 55:'老时时彩'}[lotteryId];
        };

        me.getRule = function(lotteryId, playType, state) {
            if (lotteryId == Lottery.DLT) {
            	switch (playType) {
	            	case 'dt':
	            		for (i in state){
	            			switch (state[i]) {
		        				case '11':
		        					return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">1</span>个前区胆码';
		        					break;
		        				case '40':
		        					if ($.inArray('10', state) > -1) {
		        						return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">1</span>个前区胆码';
		        					} else if ($.inArray('30', state) > -1) {
		        						return '<i class="icon-font">&#xe611;</i>前区胆码＋前区拖码≥<span class="num-red">6</span>个';
		        					}
		        					break;
	            				case '21':
	            				case '22':
	            				case '23':
	            					return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">2</span>个前区拖码';
		        					break;
	            				case '31':
	            				case '32':
	            				case '33':
	            					return '<i class="icon-font">&#xe611;</i>前区胆码＋前区拖码≥<span class="num-red">6</span>个';
		        					break;
		        				case '50':
		        					return '<i class="icon-font">&#xe611;</i>最多选择<span class="num-red">4</span>个前区胆码';
		        					break;
		        				case '02':
		        				case '12':
		        				case '42':
		        					return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">2</span>个后区拖码';
		        					break;
		        				case '05':
		        					return '<i class="icon-font">&#xe611;</i>最多选择<span class="num-red">1</span>个后区胆码';
		        					break;
		        			}
	            		}
	            		break;
	        		case 'ddsh':
	        			for (i in state){
	        				switch (state[i]) {
			        			case '10':
			        				return '<i class="icon-font">&#xe611;</i>前区最多可定<span class="num-red">4</span>个胆码<span class="pop-small">(胆码超出后自动做杀号处理)</span>';
		        					break;
		        				case '20':
		        					return '<i class="icon-font">&#xe611;</i>前区最多可杀<span class="num-red">30</span>个号码';
		        					break;
		        				case '02':
		        					return '<i class="icon-font">&#xe611;</i>后区最多可杀<span class="num-red">10</span>个号码';
		        					break;
		        			}
	        			}
	        			break;
	        		case 'default':
	        		default:
	        			if ($.inArray('00', state) === -1) return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">５</span>个前区和<span class="num-blue">２</span>个后区';
	        			break;
            	}
                return true;
            } else if (lotteryId == Lottery.SSQ) {
            	switch (playType) {
            		case 'dt':
            			for (i in state) {
            				switch (state[i]) {
	            				case '10':
	            				case '12':
	            					return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">1</span>个红球胆码';
	            					break;
	            				case '20':
	            				case '22':
	            					return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">2</span>个红球拖码';
	            					break;
	            				case '30':
	            				case '32':
	            					return '<i class="icon-font">&#xe611;</i>红球胆码＋红球拖码≥<span class="num-red">7</span>个';
	            					break;
	            				case '50':
	            					return '<i class="icon-font">&#xe611;</i>最多选择<span class="num-red">5</span>个红球胆码';
	            					break;
	            				case '02':
	            				case '42':
	            					return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">1</span>个蓝球';
	            					break;
	            			}
            			}
            			break;
            		case 'ddsh':
            			for (i in state) {
            				switch (state[i]) {
	            				case '10':
	            					return '<i class="icon-font">&#xe611;</i>红球最多可定<span class="num-red">5</span>个胆码<span class="pop-small">(胆码超出后自动做杀号处理)</span>';
	            					break;
	            				case '20':
	            					return '<i class="icon-font">&#xe611;</i>红球最多可杀<span class="num-red">27</span>个号码';
	            					break;
	            				case '02':
	            					return '<i class="icon-font">&#xe611;</i>蓝球最多可杀<span class="num-red">15</span>个号码';
	            					break;
	            			}
            			}
            			break;
            		case 'default':
            		default:
            			if ($.inArray('00', state) === -1) return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">６</span>个红球和<span class="num-blue">１</span>个蓝球';
            			break;
            	}
                return true;
            } else if ($.inArray(lotteryId, [Lottery.JXSYXW, Lottery.HBSYXW]) > -1 ) {
            	var index = parseInt(playType.replace(/(\D+)/ig, ''), 10);
                switch (playType) {
                	case 'q1':
                    case 'rx2':
                    case 'rx3':
                    case 'rx4':
                    case 'rx5':
                    case 'rx6':
                    case 'rx7':
                    case 'rx8':
                    case 'qzu2':
                    case 'qzu3':
                    	if (state[0] == '1') return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">'+index+'</span>个号码';
                    case 'rx2dt':
                    case 'qzu2dt':
                    	for (i in state) {
                    		switch (state[i]) {
                    			case 1:
	                    		case 2:
	                        	case 3:
	                        	case '5':
	                        		return '<i class="icon-font">&#xe611;</i>请选择1个胆码，2~10个拖码，胆码＋拖码<span class="num-red">≥3</span>个';
	                        		break;
                    		}
                    	}
                    	break;
                    case 'rx3dt':
                    case 'rx4dt':
                    case 'rx5dt':
                    case 'rx6dt':
                    case 'rx7dt':
                    case 'qzu3dt':
                    	for (i in state) {
                    		switch (state[i]) {
                    			case 1:
	                    		case 2:
	                        	case 3:
	                        	case '5':
	                        		return '<i class="icon-font">&#xe611;</i>请选择1~'+(index-1)+'个胆码，2~10个拖码，胆码＋拖码≥<span class="num-red">'+(index+1)+'</span>个';
	                        		break;
                    		}
                    	}
                    	break;
                    case 'qzhi2':
                    	if($.inArray('01', state) > -1 || $.inArray('10', state) > -1 || $.inArray('11', state) > -1) return '<i class="icon-font">&#xe611;</i>每位至少选择1个号码，且相互不重复';
                        break;
                    case 'qzhi3':
                    	for (i in state) {
                    		switch (state[i]) {
	                    		case '001':
	                        	case '010':
	                        	case '011':
	                        	case '100':
	                        	case '101':
	                        	case '110':
	                        	case '111':
	                        		return '<i class="icon-font">&#xe611;</i>每位至少选择1个号码，且相互不重复';
	                        		break;
                    		}
                    	}
                        break;
                    default:
                    break;
                }
                return true;
            } else if(lotteryId == Lottery.KLPK){
            	var index = parseInt(playType.replace(/(\D+)/ig, ''), 10) || 1;
                switch (playType) {
                	case 'dz':
                	case 'sz':
                	case 'ths':
                	case 'th':
                	case 'bz':
                	case 'rx1':
                    case 'rx2':
                    case 'rx3':
                    case 'rx4':
                    case 'rx5':
                    case 'rx6':
                    	if (state[0] == '1') return '<i class="icon-font">&#xe611;</i>请至少选择<span class="num-red">'+index+'</span>个号码';
                    case 'rx2dt':
                    	for (i in state) {
                    		switch (state[i]) {
                    			case 1:
	                    		case 2:
	                        	case 3:
	                        	case '5':
	                        		return '<i class="icon-font">&#xe611;</i>请选择1个胆码，2~12个拖码，胆码＋拖码<span class="num-red">≥3</span>个';
	                        		break;
                    		}
                    	}
                    	break;
                    case 'rx3dt':
                    case 'rx4dt':
                    case 'rx5dt':
                    case 'rx6dt':
                    case 'rx7dt':
                    	for (i in state) {
                    		switch (state[i]) {
                    			case 1:
	                    		case 2:
	                        	case 3:
	                        	case '5':
	                        		return '<i class="icon-font">&#xe611;</i>请选择1~'+(index-1)+'个胆码，2~12个拖码，胆码＋拖码≥<span class="num-red">'+(index+1)+'</span>个';
	                        		break;
                    		}
                    	}
                    	break;
                    default:
                    	break;
                }
                return true;
            }
        };
        
        me.getPlayTypeByCode = function(lotteryId, code) {
        	code = parseInt(code, 10)
        	switch (lotteryId) {
        		case 23529:
        		case 51:
        		case 35:
        		case 10022:
        		case 23528:
        			return 'default';
        			break;
        		case 21406:
        			var codeArr = ['', 'q1', 'rx2', 'rx3', 'rx4', 'rx5', 'rx6', 'rx7', 'rx8', 'qzhi2', 'qzhi3', 'qzu2', 'qzu3', 'lexuan3', 'lexuan4', 'lexuan5'];
        			return codeArr[code];
        		case 21407:
        		case 21408:
        			var codeArr = ['', 'q1', 'rx2', 'rx3', 'rx4', 'rx5', 'rx6', 'rx7', 'rx8', 'qzhi2', 'qzhi3', 'qzu2', 'qzu3'];
        			return codeArr[code];
        			break;
        		case 53:
        			var codeArr = ['', 'hz', 'sthtx', 'sthdx', 'sbth', 'slhtx', 'ethfx', 'ethdx', 'ebth'];
        			return codeArr[code];
        		case 54:
        			var codeArr = ['', 'rx1', 'rx2', 'rx3', 'rx4', 'rx5', 'rx6', 'th', 'ths', 'sz', 'bz', 'dz'];
        			return codeArr[code];
        		case 52:
        		case 33:
        			var codeArr = ['', 'zx', 'z3', 'z6'];
        			return codeArr[code];
        		case 55:
        			var codeArr = {1:'dxds', 10:'1xzhi', 20:'2xzhi', 21:'2xzhi', 23:'2xzu', 27:'2xzu', 30:'3xzhi', 
        				31:'3xzhi', 33:'3xzu3', 37:'3xzu3', 34:'3xzu6', 38:'3xzu6', 40:'5xzhi', 41:'5xzhi', 43:'5xt'};
        			return codeArr[code];
        			break;
        	}
        }

        me.renderCast = function(lotteryId, code, award, from) {
            lotteryId = parseInt(lotteryId, 10);
            if (lotteryId === cx.Lottery.KLPK) {
            	var numArr = [];
        		numArr[0] = ['', 'A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K'];
        		numArr['th'] = ['同花包选', '黑桃', '红桃', '梅花', '方块'];
        		numArr['ths'] = ['同花顺包选', '黑桃', '红桃', '梅花', '方块'];
        		numArr['sz'] = ['顺子包选', 'A23', '234', '345', '456', '567', '678', '789', '8910', '910J', '10JQ', 'JQK', 'QKA'];
        		numArr['bz'] = ['豹子包选', 'AAA', '222', '333', '444', '555', '666', '777', '888', '999', '101010', 'JJJ', 'QQQ', 'KKK'];
        		numArr['dz'] = ['对子包选', 'AA', '22', '33', '44', '55', '66', '77', '88', '99', '1010', 'JJ', 'QQ', 'KK'];
            }else if(lotteryId === cx.Lottery.CQSSC) {
            	dxdsArr = {'1':'大', '2':'小', '4':'单', '5':'双'};
            }
            
            var codesArr = code.split(':'),playType = me.getPlayTypeByCode(lotteryId, codesArr[1]),fushi = false;
            var PlayTypeName = me.getPlayTypeName(lotteryId, codesArr[1]),item = '',cArr = [];
            if (lotteryId == Lottery.CQSSC) {
            	$.each(codesArr[0].split(','), function(i, e){
            		cArr[i] = [];
            		cArr[i]['t'] = e.split('');
            	})
            }else if ($.inArray(lotteryId, [Lottery.PLW, Lottery.QXC]) === -1 && playType !== 'zx') {
            	$.each(codesArr[0].split('|'), function(i, e){
            		cArr[i] = [];
            		if (e.indexOf('$') > -1) {
            			cArr[i]['d'] = e.split('$')[0].split(',');
            			cArr[i]['t'] = e.split('$')[1].split(',')
            		}else {
            			cArr[i]['t'] = e.split(',');
            		}
            	})
            }else {
            	$.each(codesArr[0].split(','), function(i, e){
            		cArr[i] = [];
            		if (e.indexOf('$') > -1) {
            			cArr[i]['d'] = e.split('$')[0].split('');
            			cArr[i]['t'] = e.split('$')[1].split('')
            		}else {
            			cArr[i]['t'] = e.split('');
            		}
            	})
            }
            if ($.inArray(lotteryId, [cx.Lottery.KS, cx.Lottery.KLPK, cx.Lottery.CQSSC]) === -1) {
            	if (parseInt(codesArr[2], 10) != 5 && parseInt(codesArr[1], 10) != 135) {
                	if (playType == 'z3' && parseInt(codesArr[2], 10) == 3) {
                		fushi = true;
                	}else {
                		var minLength = me.getMinLength(lotteryId, playType);
                		$.each(minLength, function(i, e){
                    		if (cArr[i]['t'].length > minLength[i]) fushi = true;
                		})
                	}
                	if (fushi) {
                		item = '复式';
                	}else {
                		item = '单式';
                	}
                }else if ($.inArray(lotteryId, [Lottery.SSQ, Lottery.DLT]) === -1) {
                	item = '胆拖';
                }
            }
            
            if ($.inArray(playType, ['slhtx', 'sthtx']) > -1) {
            	tmpTpl = "<span>"+PlayTypeName+"</span>";
            }else {
            	
            	var tmpTpl = '';
            	$.each(cArr, function(i, ele){
            		var dStr = '';
                	var tStr = '';
                	if (ele['d']) {
                		if (i == 1) { //大乐透的双区胆拖
                			dStr += me.renderBlueDetail('(', from);
                    		$.each(ele['d'], function(j, e){
                    			dStr += me.renderBlueDetail(e, from);
                    		})
                    		dStr += me.renderBlueDetail(')', from);
                		}else {
                			dStr += me.renderRedDetail('(', from);
                    		$.each(ele['d'], function(j, e){
                    			if (lotteryId === cx.Lottery.KLPK && $.inArray(playType, ['th', 'ths', 'sz', 'bz', 'dz']) > -1) {
                					num = numArr[playType][parseInt(e, 10)];
                				}else if (lotteryId === cx.Lottery.KLPK) {
                					num = numArr[0][parseInt(e, 10)];
                				}else {
                					num = e;
                				}
                    			dStr += me.renderRedDetail(num, from);
                    		})
                    		dStr += me.renderRedDetail(')', from);
                		}
                	}
                	$.each(ele['t'], function(j, e){
                		if (i == 1 && $.inArray(lotteryId, [Lottery.SSQ, Lottery.DLT]) > -1) {
            				tStr += me.renderBlueDetail(e, from);
            			}else {
            				if (lotteryId === cx.Lottery.KLPK && $.inArray(playType, ['th', 'ths', 'sz', 'bz', 'dz']) > -1) {
            					num = numArr[playType][parseInt(e, 10)];
            				}else if (lotteryId === cx.Lottery.KLPK) {
            					num = numArr[0][parseInt(e, 10)];
            				}else if (playType === 'dxds') {
            					num = dxdsArr[parseInt(e, 10)];
            				}else {
            					num = e
            				}
            				tStr += me.renderRedDetail(num, from);
            			}
                	})
                	
            		tmpTpl += dStr+tStr;
            		if (i < cArr.length-1 && ($.inArray(lotteryId, [Lottery.PLW, Lottery.QXC]) > -1 || $.inArray(playType, ['zx', 'qzhi2', 'qzhi3', 'lexuan3', '2xzhi', '3xzhi', '5xzhi', '5xt', 'dxds']) > -1 )) 
            			tmpTpl += me.renderGrayDetail('|' ,from);
            	})
            }
            var typeStr = PlayTypeName.replace(/普通/, '');
            if ($.inArray(lotteryId, [21406, 21407, 21408]) === -1 || $.inArray(codesArr[1], ['09', '10', '11', '12']) === -1) {
            	typeStr += item;
            }else if (parseInt(codesArr[2], 10) == 5) {
            	typeStr = typeStr.replace(/直选|组选/, '')+'胆拖';
            } 
            if (lotteryId == cx.Lottery.DLT && (parseInt(codesArr[1], 10) == 2 || (parseInt(codesArr[1], 10) == 135 && parseInt(codesArr[2], 10) == 1))) typeStr += '追加';
            	
            tpl = "<span class='type'>"+typeStr+"</span><div class='num-group'>"+tmpTpl+"</div>";
            return tpl;
        }

        me.renderGray = function(num) {
            return '<span class="ball ball-gray">' + num + '</span>';
        };

        me.renderBlue = function(num, from) {
            return '<span class="ball ball-blue">' + num + '</span>';
        };

        me.renderRed = function(num, from) {
            return '<span class="ball ball-red">' + num + '</span>';
        };

        me.renderGrayDetail = function(num, from) {
        	if (from === 'chase') {
        		return '<s>' + num + '</s>';
        	}else {
        		return '<em>' + num + '</em>';
        	}
        };

        me.renderBlueDetail = function(num, from) {
        	if (from === 'chase') {
        		return '<span class="num-blue">' + num + '</span>';
        	} else {
        		return '<span class="spec">' + num + '</span>';
        	}
        };

        me.renderRedDetail = function(num, from) {
        	if (from === 'chase') {
        		return '<span>' + num + '</span>';
        	} else {
        		return '<em class="spec">' + num + '</em>';
        	}
        };

        me.toCastString = function(lotteryId, subStrings, zj) {
            var betStr = [];
            var singleBet;
            var ballStr = [];
            var danStr = [];
            var tuoStr = [];
            var preStr = [];
            for (var k in subStrings) {
            	var playType = subStrings[k].playType || 'default';
            	var type = playType;
            	if (zj) type = (playType == 'default') ? 'zj' : playType;
            	if (typeof me.playTypes[lotteryId][type] === 'object') {
            		var midStr = ':' + ((subStrings[k].betNum > 1) ? me.playTypes[lotteryId][type][1] : me.playTypes[lotteryId][type][0]);
            	}else {
            		var midStr = ':' + me.playTypes[lotteryId][type];
            	}
            	
            	if (zj) type = (playType == 'dt') ? 'zj' : playType;
            	if (lotteryId === cx.Lottery.KLPK && $.inArray(playType, ['rx2', 'rx3', 'rx4', 'rx5', 'rx6']) > -1 && subStrings[k].betNum > 1) midStr += '1';
                var postStr = ':' + _getCastPost(lotteryId, type);
                singleBet = subStrings[k].balls;
                preStr = [];
                for (var j = 0; j < singleBet.length; ++j) {
                	ballStr = [];
                	danStr = [];
                	if (singleBet[j]['dan'] !== undefined)
                	{
                		singleBet[j]['dan'].sort(function(a, b){
        					a = parseInt(a, 10);
        					b = parseInt(b, 10);
        					return a > b ? 1 : ( a < b ? -1 : 0 );
        				});
                		for (var i = 0; i < singleBet[j]['dan'].length; ++i) {
                            singleBet[j]['dan'][i] += '';
                            if (singleBet[j]['dan'][i].length < 2 && _hasPaddingZero(lotteryId)) singleBet[j]['dan'][i] = '0' + singleBet[j]['dan'][i];
                            danStr.push($.trim(singleBet[j]['dan'][i]));
                        }
                		danStr = danStr.join(_getNumberSeparator(lotteryId, playType))+"$";
                	}
                    tuoStr = [];
                    singleBet[j]['tuo'].sort(function(a, b){
    					a = parseInt(a, 10);
    					b = parseInt(b, 10);
    					return a > b ? 1 : ( a < b ? -1 : 0 );
    				});
                    for (var i = 0; i < singleBet[j]['tuo'].length; ++i) {
                    	if (playType != 'hz') singleBet[j]['tuo'][i] += '';
                        if (singleBet[j]['tuo'][i].length < 2 && _hasPaddingZero(lotteryId)) singleBet[j]['tuo'][i] = '0' + singleBet[j]['tuo'][i];
                        tuoStr.push($.trim(singleBet[j]['tuo'][i]));
                    }
                    tuoStr = tuoStr.join(_getNumberSeparator(lotteryId, playType));
                    ballStr = danStr+tuoStr;
                    preStr.push(ballStr);
                }
                preStr = preStr.join(_getPlaceSeparator(lotteryId, playType));
                betStr.push(preStr + midStr + postStr);
            }
            return betStr.join(';');
        };
        
        me.toChaseString = function(chases) {
        	var chaseStr = '';
        	for(i in chases) {
        		chaseStr += i+"|"+chases[i].multi+"|"+chases[i].money+"|"+chases[i].award_time+"|"+chases[i].show_end_time+";";
        	}
        	return chaseStr;
        };

        return me;
    })();

    var BoxCollection = cx.BoxCollection = function(selector, options) {
        this.$el = $(selector);
        this.boxes = [];
        this.betMoney = options.betMoney || 2;
        this.edit = 0;
        this.basket;
        this.lotteryId = options.lotteryId || false;
        this.init();
    };

    BoxCollection.prototype = {
    	init: function() {
    		var self = this;
    		this.$el.find('.add-basket').click(function() {
    			
    			if (self.calcMoney() > 20000) {
            		cx.Alert({content: "<i class='icon-font'>&#xe611;</i>单笔订单金额最高<span class='num-red'>２万</span>元"});
            		return;
            	}
            	if (self.getType() === 'ddsh') {
            		var allball = self.getAllBalls(true), narr = [parseInt($(".tac .rand-count:first").val(), 10), parseInt($(".tac .rand-count").eq(1).val(), 10)], zn = $(".tac .rand-count:last").val(), balls, betNum = self.calcBetNum();
            		for (var i = 0; i <zn; i++) {
            			var balls = self.randddsh(self.lotteryId, narr, betNum, allball);
            			self.basket.add(balls);
            		}
            		$('html, body').animate({scrollTop: $('.cast-basket .btn-main').offset().top + $('.cast-basket .btn-main')[0].scrollHeight - $(window).height()});
            	}else {
            		if ($.inArray(self.lotteryId, [cx.Lottery.SYXW, cx.Lottery.CQSSC]) > -1) {
            			var rule = getRule(self.lotteryId, self.getType(), self.isValid());
            		}else {
            			var rule = cx.Lottery.getRule(self.lotteryId, self.getType(), self.isValid());
            		}
            		if (rule !== true) {
            			if ($.inArray(self.lotteryId, [cx.Lottery.SYXW, cx.Lottery.JXSYXW, cx.Lottery.HBSYXW]) > -1 ){
                			cx.Alert({content: rule, size : '16'});
                		}else {
                			cx.Alert({content: rule});
                		}
            		}else {
            			var balls = self.getAllBalls();
            			if (self.getType().indexOf('lexuan') > -1){
            				if (self.edit > 0) $('.cast-list').find("li[data-index='"+self.edit+"'] .remove-str").trigger('click');
            				self.edit = 0;
                			var balls = cx.splitlexuanBalls(balls);
                			self.basket.addAll(balls);
                		}else if (self.getType() == '3xzu3') {
                			balls = cx.z3ballsplit(balls);
                			self.basket.addAll(balls);
                		}else if(self.edit === 0) {
                    		self.basket.add(balls);
                    	}else {
                    		self.basket.edit(balls, self.edit);
                    	}
                    	self.removeAll();
                    	if ($.inArray(self.lotteryId, [cx.Lottery.SYXW, cx.Lottery.JXSYXW, cx.Lottery.HBSYXW]) > -1 ) {
                    		$('html, body').scrollTop($('.cast-basket .btn-main').offset().top + $('.cast-basket .btn-main')[0].scrollHeight - $(window).height());
                    	} else if(self.lotteryId !== cx.Lottery.KLPK) {
                    		$('html, body').animate({scrollTop: $('.cast-basket .btn-main').offset().top + $('.cast-basket .btn-main')[0].scrollHeight - $(window).height()});
                    	} else {
                    		$('.count-matches').html(self.basket.betNum);
                    	}
            		}
            	}
            });
    		$(".tac .rand-count:last").change(function(){
    			self.renderBet();
    		});
    		this.$el.find(".clear-pickball").click(function(){
    			for (i in self.boxes) {
    				self.boxes[i].removeAll();
    			}
    		})
    	},
    	setBasket: function(basket) {
    		this.basket = basket;
    	},
        add: function(box, index) {
            this.boxes.push(box);
            box.setCollection(this, index);
        },
        addBall: function(boxs) {
        	var editStrs = {
                balls: [],
                betNum: 0,
                betMoney: 0,
            };
        	$(this.boxes).each(function(k, box) {
        		editStrs.balls[k] = [];
        		box.removeAll();
        		for (var j in boxs[k]) {
        			editStrs.balls[k][j] = [];
        			for (var i = 0; i < boxs[k][j].length; ++i)
                 	{
            			box.addBall(boxs[k][j][i], j);
            			editStrs.balls[k][j].push(boxs[k][j][i]);
                 	}
        		}
            });
        	editStrs.betNum = this.calcBetNum();
        	editStrs.betMoney = editStrs.betNum * this.betMoney;
        	editStrs.playType = this.getType();
        	return editStrs;
        },
        isValid: function() {
        	var err = [];
            for (var i = 0; i < this.boxes.length; ++i) {
            	var error = [];
            	$.each(this.boxes[i].isValid(), function(j, isValid){
            		if (err.length == 0) {
            			error.push(parseInt(isValid));
            		}else {
            			$.each(err, function(k, er){
            				error.push(er+isValid);
            			})
            		}
            	})
            	err = error;
            }
            return err.sort(function(a, b) {
            	a = parseInt(a, 10) > 10 ? parseInt(a, 10) : parseInt(a, 10) + 50;
				b = parseInt(b, 10) > 10  ? parseInt(a, 10) : parseInt(a, 10) + 50;
				return a > b ? 1 : ( a < b ? -1 : 0 );
            });
        },
        removeAll: function() {
            $(this.boxes).each(function(k, box) {
                box.removeAll();
            });
        },
        renderBet: function() {
        	if(this.$el.find('.num-red').length > 0) {
        		this.$el.find('.num-red:eq(0)').html(this.getNum(0)[0]+this.getNum(0)[1]);
        		this.$el.find('.num-red:eq(1)').html(this.getNum(0)[0]);
        		this.$el.find('.num-red:eq(2)').html(this.getNum(0)[1]);
        	}
        	if(this.$el.find('.num-blue').length > 0) {
        		this.$el.find('.num-blue:eq(0)').html(this.getNum(1)[0]+this.getNum(1)[1]);
        		this.$el.find('.num-blue:eq(1)').html(this.getNum(1)[0]);
        		this.$el.find('.num-blue:eq(2)').html(this.getNum(1)[1]);
        	}
        	if (this.getType() === 'ddsh') {
        		var multiple = parseInt($(".tac .rand-count:last").val());
        		this.$el.find('.num-multiple').html(this.calcBetNum() * multiple);
                this.$el.find('.num-money').html(this.calcMoney() * multiple);
        	} else {
        		this.$el.find('.num-multiple').html(this.calcBetNum());
                this.$el.find('.num-money').html(this.calcMoney());
        	}
        	if($.inArray(this.lotteryId, [cx.Lottery.SYXW, cx.Lottery.JXSYXW, cx.Lottery.HBSYXW, cx.Lottery.CQSSC]) > -1 ) this.$el.find(".sub-txt1").hide();
        	if ($.inArray(this.lotteryId, [cx.Lottery.SYXW, cx.Lottery.CQSSC]) > -1) {
        		var rule = getRule(this.lotteryId, this.getType(), this.isValid());
        	}else {
        		var rule = cx.Lottery.getRule(this.lotteryId, this.getType(), this.isValid());
        	}
            if (rule === true) {
            	if(this.$el.find('.sub-txt').length > 0) {
            		var playType = this.getType();
            		var money = cx.Lottery.jiangjin[this.lotteryId][playType];
            		if(money > 10000) money = (money/10000)+'万';
            		this.$el.find(".sub-txt").html('（如中奖，奖金 <em>'+money+'</em> 元，盈利 <em>'+(cx.Lottery.jiangjin[this.lotteryId][playType]-this.calcMoney())+'</em> 元）');
            		this.$el.find(".sub-txt").show();
            	}
            	if ($.inArray(this.lotteryId, [cx.Lottery.SYXW, cx.Lottery.CQSSC]) > -1) {
            		cx.caculateBonus(this.$el, this.getType(), this.calcMoney(), this.boxes);
            	}else if($.inArray(this.lotteryId, [cx.Lottery.JXSYXW, cx.Lottery.HBSYXW]) > -1 ) {
            		var playType = this.getType();
            		var money = cx.Lottery.jiangjin[this.lotteryId][playType];
            		var index = parseInt(playType.replace(/(\D+)/ig, ''), 10);
            		var num = this.boxes[0].balls.length;
            		var dnum = this.boxes[0].dans.length;
            		switch (this.getType()) {
            			case 'rx2':
            			case 'rx3':
            			case 'rx4':
            			case 'rx5':
            				var small = cx.Math.combine((num > 6 + index ? num - 6 : index), index-dnum) * money;
                    		var big = cx.Math.combine(num > 5 ? 5 : num, index-dnum) * money;
            				str = '（如中奖，奖金 <span class="main-color-s">'
            				if (small == big) {
            					str += small;
            				} else {
            					str += small+'</span>~<span class="main-color-s">'+big;
            				}
            				str += '</span> 元，';
            				small = small-this.calcMoney();
            				big = big-this.calcMoney();
            				if (small == big) {
            					if (small >= 0) {
            						str += '盈利 <span class="main-color-s">'+small;
            					} else {
            						str += '盈利 <span class="green-color">'+small;
            					}
            				} else {
            					if (small >= 0) {
            						str += '盈利 <span class="main-color-s">'+small;
            					} else {
            						str += '盈利 <span class="green-color">'+small;
            					}
            					if (big >= 0) {
            						str += '</span>~<span class="main-color-s">'+big;
            					} else {
            						str += '</span>~<span class="green-color">'+big;
            					}
            				}
            				str += '</span> 元）';
            				this.$el.find(".sub-txt1").html(str);
            				break;
            			case 'rx2dt':
            			case 'rx3dt':
            			case 'rx4dt':
            			case 'rx5dt':
            				var small = cx.Math.combine(num -6 > index - dnum ? num-6:index - dnum, index - dnum) * money;
                    		var big = cx.Math.combine(5 - dnum > num ? num : 5-dnum, index-dnum) * money;
            				str = '（如中奖，奖金<span class="main-color-s">'
            				if (small == big) {
            					str += small;
            				} else {
            					str += small+'</span>~<span class="main-color-s">'+big;
            				}
            				str += '</span> 元，';
            				small = small-this.calcMoney();
            				big = big-this.calcMoney();
            				if (small == big) {
            					if (small >= 0) {
            						str += '盈利 <span class="main-color-s">'+small;
            					} else {
            						str += '盈利 <span class="green-color">'+small;
            					}
            				} else {
            					if (small >= 0) {
            						str += '盈利 <span class="main-color-s">'+small;
            					} else {
            						str += '盈利 <span class="green-color">'+small;
            					}
            					if (big >= 0) {
            						str += '</span>~<span class="main-color-s">'+big;
            					} else {
            						str += '</span>~<span class="green-color">'+big;
            					}
            				}
            				str += '</span> 元）';
            				this.$el.find(".sub-txt1").html(str);
            				break;
            			case 'rx6':
            			case 'rx7':
            			case 'rx8':
            				var num = cx.Math.combine(num-5, index-5) * money;
            				str = '（如中奖，奖金  <span class="main-color-s">'+num+'</span> 元，盈利 ';
            				num = num-this.calcMoney();
            				if (num >= 0) {
            					str += ' <span class="main-color-s">'+num+'</span> 元）';
            				} else {
            					str += ' <span class="green-color">'+num+'</span> 元）';
            				}
            				this.$el.find(".sub-txt1").html(str);
            				break;
            			case 'rx6dt':
            			case 'rx7dt':
            				var big = dnum < 5 ? cx.Math.combine(num-5+dnum, index - 5) * money : cx.Math.combine(num, index - dnum) * money;
            				var small = dnum < index - 5 ? cx.Math.combine(num-5, index - dnum - 5) * money : money;
            				str = '（如中奖，奖金 <span class="main-color-s">';
                				if (small == big) {
                					str += small;
                				} else {
                					str += small+'</span>~<span class="main-color-s">'+big;
                				}
                				str += '</span> 元，盈利 ';
                				small = small-this.calcMoney();
                				big = big-this.calcMoney();
                				if (small == big) {
                					if (small >= 0) {
                						str += '<span class="main-color-s">'+small;
                					} else {
                						str += '<span class="green-color">'+small;
                					}
                				} else {
                					if (small >= 0) {
                						str += '<span class="main-color-s">'+small;
                					} else {
                						str += '<span class="green-color">'+small;
                					}
                					if (big >= 0) {
                						str += '</span>~<span class="main-color-s">'+big;
                					} else {
                						str += '</span>~<span class="green-color">'+big;
                					}
                				}
                				str += '</span> 元）';
                				this.$el.find(".sub-txt1").html(str);
            				break;
            			case 'q1':
            			case 'qzu2':
            			case 'qzu3':
            			case 'qzu2dt':
            			case 'qzu3dt':
            			case 'qzhi2':
            			case 'qzhi3':
            				str = '（如中奖，奖金<span class="main-color-s">'+money+'</span> 元，盈利';
            				money = money-this.calcMoney();
            				if (money >= 0) {
            					str += ' <span class="main-color-s">'+money+'</span> 元）';
            				} else {
            					str += ' <span class="green-color">'+money+'</span> 元）';
            				}
            				this.$el.find(".sub-txt1").html(str);
            				break;
            		}
            		this.$el.find(".sub-txt1").show();
            	}else if (this.lotteryId === cx.Lottery.KLPK) {
            		var playType = this.getType();
            		var num = this.boxes[0].balls.length;
            		var money = cx.Lottery.jiangjin[this.lotteryId][playType];
            		if ($.inArray(playType, ['dz', 'sz', 'ths', 'th', 'bz']) > -1) {
            			var moneybx = cx.Lottery.jiangjin[this.lotteryId][playType+'bx'];
            			if ($.inArray(0, this.boxes[0].balls) === -1) {
            				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+money+"元，盈利<em>"+(money-num * 2)+"元</em></span>");
            			}else if (num == 1) {
            				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+moneybx+"元，盈利<em>"+(moneybx-num * 2)+"元</em></span>");
            			}else if (num == this.boxes[0].options.amount) {
            				money = parseInt(moneybx, 10)+parseInt(money, 10);
            				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+money+"元，盈利<em>"+(money-num * 2)+"元</em></span>");
            			}else {
            				money = parseInt(moneybx, 10)+parseInt(money, 10);
            				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+moneybx+"~"+money+"元，盈利<em>"+(moneybx - num * 2)+"~"+(money-num * 2)+"元</em></span>");
            			}
            		}else if ($.inArray(playType, ['rx1', 'rx2', 'rx2dt']) > -1) {
            			var index = parseInt(playType.replace(/(\D+)/ig, ''), 10);
            			if(this.boxes[0].dans.length == 0) {
            				var mn = cx.Math.combine(num, index);
            				big = money * (mn >= 3 ? 3 : mn);
            				small = money;
            				if (big === small) {
            					this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"元，盈利<em>"+(small - (mn * 2))+"元</em></span>");
            				}else {
            					this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"~"+big+"元，盈利<em>"+(small - (mn * 2))+"~"+(big - (mn * 2))+"元</em></span>");
            				}
            			}else{
            				var dnum = this.boxes[0].dans.length;
            				var small = 33;
            				var big = 66;
            				var mn = cx.Math.combine(num, index - dnum) * 2;
            				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"~"+big+"元，盈利<em>"+(small - mn)+"~"+(big - mn)+"元</em></span>");
            			}
            		}else {
            			var index = parseInt(playType.replace(/(\D+)/ig, ''), 10);
            			var mn = cx.Math.combine(num, index);
            			if(this.boxes[0].dans.length == 0) {
            				var small = cx.Math.combine(num - 3, index - 3) * money;
                    		var big = cx.Math.combine(num - 1, index - 1) * money;
                    		var mn = cx.Math.combine(num, index) * 2;
                    		if (big === small) {
                    			this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"元，盈利<em>"+(small - mn)+"元</em></span>")
                    		}else {
                    			this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"~"+big+"元，盈利<em>"+(small - mn)+"~"+(big - mn)+"元</em></span>")
                    		}
                    		
            			}else{
            				var dnum = this.boxes[0].dans.length;
            				if (index >= 5) {
            					var small = cx.Math.combine(num - 3, index - dnum - 3) * money;
            				}else {
            					var small = money;
            				}
            				var big = cx.Math.combine(num, index - dnum) * money;
            				var mn = cx.Math.combine(num, index - dnum) * 2;
            				this.$el.find('.pick-area-note').html("<span>如中奖，奖金"+small+"~"+big+"元，盈利<em>"+(small - mn)+"~"+(big - mn)+"元</em></span>");
            			}
            		}
            	}
                this.$el.find('.add-basket').removeClass('btn-disabled');
            } else {
            	this.$el.find(".sub-txt").hide();
            	if (this.lotteryId === cx.Lottery.KLPK) this.$el.find('.pick-area-note').empty();
            	this.$el.find('.add-basket').addClass('btn-disabled');
            }
        },
        rand1: function(lotteryId, playType) {
        	var randStrs = {
                balls: [],
                betNum: 0,
                betMoney: 0
            };
        	var startindex = cx.Lottery.getStartIndex(lotteryId, playType);
        	var arr = cx.Lottery.getMinLength(lotteryId, playType);
        	randStrs.betNum = 1;
        	randStrs.betMoney = this.betMoney;
        	var amount = cx.Lottery.getAmount(lotteryId, playType);
        	for (i in arr) {
        		randStrs.balls[i] = {};
        		randStrs.balls[i]['tuo'] = [];
        		while (randStrs.balls[i]['tuo'].length < arr[i]) {
            		j = Math.floor(Math.random() * (amount[i] - startindex[i] + 1) + startindex[i]);
            		if ($.inArray(lotteryId, [Lottery.SYXW, Lottery.JXSYXW, Lottery.HBSYXW]) > -1  && $.inArray(playType, ['qzhi2', 'qzhi3', 'lexuan3']) > -1) {
            			var eflag = true;
            			for (k in randStrs.balls) {
            				if ($.inArray(j, randStrs.balls[k]['tuo']) > -1) eflag = false;
            			}
            			if (eflag) randStrs.balls[i]['tuo'].push(j);
            		} else {
            			if ($.inArray(j, randStrs.balls[i]['tuo']) === -1) randStrs.balls[i]['tuo'].push(j);
            		}
            	}
        	}
        	if ($.inArray(lotteryId, [Lottery.SSQ, Lottery.DLT]) > -1){
        		playType = 'default';
        	}else if ($.inArray(lotteryId, [Lottery.SYXW, Lottery.JXSYXW, Lottery.HBSYXW]) > -1 ) {
        		playType = playType.replace(/dt/, '');
        	}
            randStrs.playType = playType;
            return randStrs;
        },
        randddsh: function(lotteryId, arr, betnum, allballs) {
        	var amount = cx.Lottery.getAmount(lotteryId, 'ddsh');
        	var min = cx.Lottery.getMinLength(lotteryId, 'ddsh')
    		var randStr = {
                balls: [],
                betNum: betnum,
                betMoney: betnum * this.betMoney,
                playType: 'default'
            };
        	$.each(arr, function(i, e){
        		randStr.balls[i] = {};
        		danlen = allballs.balls[i]['dan'] ? allballs.balls[i]['dan'].length : 0;
        		if (danlen) {
        			randStr.balls[i]['dan'] = [];
        			$.each(allballs.balls[i]['dan'], function(j, ele) {
        				randStr.balls[i]['dan'].push(ele);
        			})
        		}
        		randStr.balls[i]['tuo'] = [];
        		while (randStr.balls[i]['tuo'].length < e) {
            		j = Math.ceil(Math.random() * amount[i]);
            		if ($.inArray(j, randStr.balls[i]['tuo']) === -1 && $.inArray(j, allballs.balls[i]['tuo']) === -1 && $.inArray(j, allballs.balls[i]['sha']) === -1 && $.inArray(j, randStr.balls[i]['dan']) === -1) 
            			randStr.balls[i]['tuo'].push(j);
            	}
        		if (danlen && e + danlen === min[i]) {
        			randStr.balls[i]['tuo'] = randStr.balls[i]['tuo'].concat(randStr.balls[i]['dan']);
        			delete randStr.balls[i]['dan'];
        		}
        		if (allballs.balls[i]['tuo'].length > 0) randStr.balls[i]['tuo'] = randStr.balls[i]['tuo'].concat(allballs.balls[i]['tuo']);
        	})
    		for (i in randStr.balls) {
    			if (randStr.balls[i].dan) randStr.playType = 'dt';
    		}
        	return randStr;
        },
		edit: function() {
            var editStrs = {
                balls: [],
                betNum: 0,
                betMoney: 0,
            };
            $(this.boxes).each(function(k, box) {
                editStrs.balls.push(box.edit());
            });
            editStrs.betNum = this.calcBetNum();
            editStrs.betMoney = this.betNum * this.betMoney;
            $(this.boxes).each(function(k, box) {
                box.removeAll();
            });
            return editStrs;		
		},
		clearButton: function(playType){
        	this.edit = 0;
        	if ($.inArray(this.lotteryId, [cx.Lottery.SYXW, cx.Lottery.JXSYXW, cx.Lottery.HBSYXW]) > -1 ) {
        		if (playType.indexOf('dt') > -1) {
            		$("."+playType.replace(/dt/, '')+" .dt .add-basket").html('添加到投注区<i class="icon-font">&#xe614;</i>');
            	}else if(playType.indexOf('lexuan') > -1){
            		$("."+playType+" .add-basket").html('添加到投注区<i class="icon-font">&#xe614;</i>');
            	}else {
            		$("."+playType+" .default .add-basket").html('添加到投注区<i class="icon-font">&#xe614;</i>');
            	}
        	}else {
        		$("."+playType+" .add-basket, .php-"+playType+" .add-basket").html('添加到投注区<i class="icon-font">&#xe614;</i>');
        	}
    		
        },
		getError: function(){
			var error = [] ;
            $(this.boxes).each(function(k, box) {
                error.push(box.getError());
            });
			return error;
		},
        getAllBalls: function(remove) {
            var allBalls = [];
            var tmpBall = {};
            $(this.boxes).each(function(k, box) {
            	tmpBall = {};
            	tmpBall['tuo'] = box.getBalls();
            	if(box.getBalls('dan').length > 0) tmpBall['dan'] = box.getBalls('dan');
            	if(box.getBalls('sha').length > 0) tmpBall['sha'] = box.getBalls('sha');
                allBalls.push(tmpBall);
            });
            var betNum = this.calcBetNum();
            if (remove === false) {
            	$(this.boxes).each(function(k, box) {
                    box.removeAll();
                });
            }
            return {
                balls: allBalls,
                betNum: betNum,
                betMoney: betNum * this.betMoney,
                playType: this.getType()
            };
        },
        getStrings: function() {
            var strings = [];
            $(this.boxes).each(function(k, box) {
                strings.push(box.joinBalls());
            });
            return strings;
        },
        calcBetNum: function() {
            var product = 1;
            if ($.inArray(this.lotteryId, [cx.Lottery.SYXW, cx.Lottery.CQSSC]) > -1) {//先十一选五移出来，以后都移出来
            	var rule = getRule(this.lotteryId, this.getType(), this.isValid());
            }else {
            	var rule = cx.Lottery.getRule(this.lotteryId, this.getType(), this.isValid());
            }
            if (rule !== true) return 0;
            $(this.boxes).each(function(k, box) {
                product *= box.calcComb();
            });
            return product;
        },
        calcMoney: function() {
            return this.calcBetNum() * this.betMoney;
        },
        setBetMoney: function(moneyNum) {
            this.betMoney = moneyNum;
        },
        getBoxes: function() {
            return this.boxes;
        },
        getNum: function(i) {
            return this.boxes[i].getNum();
        },
        getType: function() {
        	return this.boxes[0].getType();
        }
    };

    var BallBox = cx.BallBox = function(selector, options, danselctor) {
        /*
         * amount
         * min
         * mutex
         */
		this.selector = selector;
        this.$el = $(selector);
        this.$danel = $(danselctor);
        this.options = options || {};
        this.options.mutex = this.options.mutex || false;
        this.options.playType = this.options.playType || 'default';
        this.minBall = this.options.minBall || 0;
        this.lotteryId = this.options.lotteryId || 0;
        this.balls = [];
        this.dans = [];
        this.shas = [];
        this.odds = [];
        this.evens = [];
        this.bigs = [];
        this.smalls = [];
        this.all = [];
        this.randSel = this.options.randSel || false;
        this.seldef = true;
        var mid = Math.floor(this.options.amount / 2);
        for (var i = this.minBall; i <= this.options.amount; ++i) {
            this.all.push(i);
            if (i % 2 == 0) {
                this.evens.push(i);
            } else {
                this.odds.push(i);
            }
            if (i > mid) {
                this.bigs.push(i);
            } else {
                this.smalls.push(i);
            }
        }
		this.error = false;
        this.init();
    };

    cx.BallBox.prototype = {
        init: function() {
            var self = this;
            if ($.inArray(this.lotteryId, [cx.Lottery.KS, cx.Lottery.KLPK]) > -1) {
        		this.$dans = self.$danel.find('.pick-area-ball li[data-num]');
        	}else {
        		this.$dans = self.$danel.find('.pick-area-ball a');
        	}
            if (this.getType() === 'ddsh') {
            	this.$shas = self.$el.find('.pick-area-ball a');
            	this.$shas.click(function() {
                	var $this = $(this);
                	self.ShaTriger($this);
                });
            }else {
            	if ($.inArray(this.lotteryId, [cx.Lottery.KS, cx.Lottery.KLPK]) > -1) {
            		this.$balls = self.$el.find('.pick-area-ball li[data-num]');
            	}else {
            		this.$balls = self.$el.find('.pick-area-ball a');
            	}
            	this.$balls.click(function() {
                	var $this = $(this);
                	self.BallTriger($this);
                });
            }
            this.$dans.click(function() {
            	var $this = $(this);
            	self.BallTriger($this, 'dan');
            });
            this.$el.find('.clear-balls').click(function() {
                self.removeBalls();
            });
            this.$el.find('.filter-bigs').click(function() {
                self.removeBalls();
                var ball;
                for (var i = 0; i < self.bigs.length; ++i) {
                    ball = self.bigs[i];
                    self.BallTriger(self.$balls.eq(ball - self.minBall));
                }
            });
            this.$el.find('.filter-smalls').click(function() {
                self.removeBalls();
                var ball;
                for (var i = 0; i < self.smalls.length; ++i) {
                    ball = self.smalls[i];
                    self.BallTriger(self.$balls.eq(ball - self.minBall));
                }
            });
            this.$el.find('.rand-select').click(function() {
                var count = self.$el.find('.rand-count').val() || 1;
                self.removeBalls();
                self.rand(count, function(i) {
                	self.$balls.eq(i - 1).addClass('selected');
                    self.$dans.eq(i - 1).addClass('dt-pick');
                    if (self.lotteryId === cx.Lottery.KLPK && self.minBall == 0 && i == self.options.amount) i = 0;
                    self.addBall(i + '') ;
                    if (self.lotteryId === cx.Lottery.KS) {
                    	self.balls = [];
                    	self.balls.push(self.$balls.eq(i - 1).data('num'));
                    }
                });
                self.collection.renderBet();
            });
            this.$el.find('.rand-count').change(function() {
                var count = self.$el.find('.rand-count').val();
                self.removeBalls();
                self.rand(count, function(i) {
                    self.addBall(i + '') ;
                    self.$balls.eq(i - 1).addClass('selected');
                    self.$dans.eq(i - 1).addClass('dt-pick');
                });
                self.seldef = false;
                self.collection.renderBet();
            });
            this.$el.find('.filter-odds').click(function() {
                self.removeBalls();
                var ball;
                for (var i = 0; i < self.odds.length; ++i) {
                    ball = self.odds[i];
                    self.BallTriger(self.$balls.eq(ball - self.minBall));
                }
            });
            this.$el.find('.filter-evens').click(function() {
                self.removeBalls();
                var ball;
                for (var i = 0; i < self.evens.length; ++i) {
                    ball = self.evens[i];
                    self.BallTriger(self.$balls.eq(ball - self.minBall));
                }
            });
            this.$el.find('.filter-all').click(function() {
                self.removeBalls();
                var ball;
                for (var i = 0; i < self.all.length; ++i) {
                	if ($.inArray(i+1, self.dans) == -1) {
                		ball = self.all[i];
                        self.BallTriger(self.$balls.eq(ball - self.minBall));
                	}
                }
            });
            if (this.getType() === 'ddsh') {
            	$(this.randSel).click(function(){
            		self.seldef = false;
            		self.collection.renderBet();
            	})
            }
        },
        setCollection: function(collection, index) {
            this.collection = collection;
            this.index = index;
        },
        isValid: function() {
        	var error = [];
        	switch (this.getType()) {
        		case 'dt':
        		case 'rx2dt':
        		case 'rx3dt':
        		case 'rx4dt':
        		case 'rx5dt':
        		case 'rx6dt':
        		case 'rx7dt':
        		case 'qzu2dt':
        		case 'qzu3dt':
        			if (this.options.hasdan === true && this.dans.length < 1) error.push('1');
        			if ((this.options.hasdan === true && this.balls.length < this.options.tmin) || (!this.options.hasdan && this.balls.length < this.options.min)) error.push('2');
        			if (this.options.hasdan === true && this.balls.length + this.dans.length < this.options.dtmin) error.push('3');
        			if (this.options.hasdan === true && this.balls.length < this.options.min) error.push('4');
        			break;
        		case 'ddsh':
        			break;
        		case 'default':
        		default:
        			if (this.balls.length < this.options.min) error.push('1');
        			break;
        	}
        	if (error.length == 0) error = ['0'];
        	return error;
        },
        ballValid: function(type) {
        	var res = true;
        	if (type === 'dan' || type === 'sha') res = res && this.dans.length < this.options.dmax;
        	if (type === 'sha') res = res && this.shas.length < this.options.smax;
        	return res;
        },
        BallTriger: function ($el, type) {        	
        	if ($.inArray(this.lotteryId, [cx.Lottery.KS, cx.Lottery.KLPK]) > -1) {
        		var ball = $el.data('num');
        	}else {
        		var ball = $el.html();
        	}
            switch (type) {
            	case 'dan':
            		var arr = this.$balls;
            		var t = 'tuo';
            		break;
            	case 'tuo':
            	default:
            		var arr = this.$dans;
            		var t = 'dan';
            		break;
            }
            if ($el.hasClass('selected')) {
            	$el.removeClass('selected');
            	if ($.inArray(this.getType(), ['qzhi2', 'qzhi3', 'lexuan3']) > -1) {
            		$.each(this.collection.boxes, function(i, e){
            			e.$balls.eq(ball-1).removeClass('dt-pick');
            		})
            	}
            	arr.eq(ball-1).removeClass('dt-pick');
            	this.removeBall(ball, type);
            	if (type === 'dan' && this.randSel !== false)
            		this.selReset(this.options.selmin[this.dans.length], this.options.selmax[this.dans.length],  this.options.seldefault[this.dans.length]);
            } else if (!this.ballValid(type)) {
            	if (this.lotteryId === cx.Lottery.SYXW) {
            		cx.Alert({content: getRule(this.lotteryId, this.getType(), [pad(this.collection.boxes.length, 5, this.index)])});
            	}else {
            		cx.Alert({content: cx.Lottery.getRule(this.lotteryId, this.getType(), [pad(this.collection.boxes.length, 5, this.index)])});
            	}
            } else {
            	if ($.inArray(this.getType(), ['qzhi2', 'qzhi3', 'lexuan3']) > -1) {
            		var self = this;
            		$.each(this.collection.boxes, function(i, e){
            			self.collection.boxes[i].$balls.eq(ball-1).removeClass('selected').addClass('dt-pick');
            			self.collection.boxes[i].removeBall(ball);
            		})
            	}
            	arr.eq(ball-1).removeClass('selected').addClass('dt-pick');
            	this.removeBall(ball, t);
            	$el.removeClass('dt-pick').addClass('selected');
            	this.addBall(ball, type);
            	if (type === 'dan' && this.randSel !== false) this.selReset(this.options.selmin[this.dans.length], this.options.selmax[this.dans.length],  this.options.seldefault[this.dans.length]);
            }
            if (this.lotteryId === cx.Lottery.KLPK && this.balls.length == 1) $('html,body').animate({scrollTop: $('.bet-klpk-bd').offset().top + 30}, 200);
            function pad(t, n, i) {
            	str = '';
            	for (j = 0; j < t; j++) {
            		if (j == i) {
            			str += n;
            		}else {
            			str += '0';
            		}
            	}
            	return str;
            }
            this.collection.renderBet();
        },
        ShaTriger: function($el) {
        	var dan = $el.html();
        	if (this.options.hasdan) {
        		var t = 'dan';
        	}else {
        		var t = 'tuo';
        	}
            if ($el.hasClass('selected')) {
            	if(this.shas.length < this.options.smax) {
            		$el.removeClass('selected');
                	$el.addClass('kill-ball');
                	if (this.lotteryId == cx.Lottery.DLT && this.index == 1) {
                		if ($.inArray(parseInt(dan, 10), this.dans) > -1) {
                			this.removeBall(dan, 'dan');
                		} else {
                			this.removeBall(dan);
                		}
                		if (this.balls.length == 1) {
	        				for (i in this.balls) {
	            				this.dans.push(this.balls[i]);
	            			}
	        				this.balls = [];
                		}
                	}else {
                		this.removeBall(dan, t);
                	}
                	this.addBall(dan, 'sha');
            	} else {
            		cx.Alert({content: cx.Lottery.getRule(this.lotteryId, this.getType(), [pad(this.collection.boxes.length, 2, this.index)])});
            	}
            } else if ($el.hasClass('kill-ball')) {
            	$el.removeClass('kill-ball');
            	this.removeBall(dan, 'sha');
            } else if (this.dans.length + this.balls.length < this.options.dmax) {
            	$el.addClass('selected');
            	this.addBall(dan, t);
            } else if(this.shas.length < this.options.smax) {
            	switch (this.lotteryId+":"+this.index) {
	            	case cx.Lottery.DLT+":1":
	        			if (this.dans.length > 0) {
	        				for (i in this.dans) {
	            				this.balls.push(this.dans[i]);
	            			}
	        			}
	        			this.dans = [];
	        			$el.addClass('selected');
	        			this.addBall(dan);
	        			break;
            		default:
            			if (this.shas.length === 0) cx.Alert({content: cx.Lottery.getRule(this.lotteryId, this.getType(), [pad(this.collection.boxes.length, 1, this.index)])});
                    	$el.addClass('kill-ball');
                    	this.addBall(dan, 'sha');
            			break;
            	}
            } else {
            	cx.Alert({content: cx.Lottery.getRule(this.lotteryId, this.getType(), [pad(this.collection.boxes.length, 2, this.index)])});
            }
            function pad(t, n, i) {
            	str = '';
            	for (j = 0; j < t; j++) {
            		if (j == i) {
            			str += n;
            		}else {
            			str += '0';
            		}
            	}
            	return str;
            }
            if (this.options.hasdan && (this.lotteryId !== cx.Lottery.DLT || this.index !== 1 || this.dans.length !== 0)) {
            	this.selReset(this.options.selmin[this.dans.length], Math.min(this.options.amount-this.dans.length-this.shas.length, this.options.selmax[this.dans.length]), this.options.seldefault[this.dans.length]);
        	}else {
        		this.selReset(this.options.selmin[this.balls.length], this.options.selmax[this.balls.length]-this.shas.length, this.options.seldefault[this.balls.length]);
        	}
            this.collection.renderBet();
        },
        selReset: function(start, end, dfault) {
        	if ($(this.randSel).val() >= start && $(this.randSel).val() <= end && !this.seldef) {
        		dfault = $(this.randSel).val();
        	} else {
        		this.seldef = true;
        	}
        	str = '';
        	$(this.randSel).empty();
        	for (var i = start; i <= end; i++) {
        		str += "<option value='"+i+"'";
        		if (dfault && dfault == i) str += " selected";
        		str += ">"+i+"</option>";
        	}
        	$(this.randSel).append(str);
        },
        rand: function(number, cb) {
            number || (number = this.options.min);
            var self = this;
            cb || (cb = function(i) {
                self.balls.push(i);
            });
            var j;
            this.balls = [];
            var mutexBoxes = this.getMutexBoxes();
            var flag = true;
            while (this.balls.length < number) {
                flag = true;
                j = Math.ceil(Math.random() * this.options.amount);
                if (j >= this.minBall && $.inArray(j, this.balls) === -1 && $.inArray(j, this.dans) === -1) {
                    if (this.options.mutex) {
                        for (var k = 0; k < mutexBoxes.length; ++k) {
                            if ($.inArray(j, mutexBoxes[k].balls) !== -1) {
                                flag = false;
                                break;
                            }
                        }
                        if (flag) cb(j);
                    } else {
                        cb(j);
                    }
                }
            }
			this.error = false;
            return this.balls.sort(function(a, b) {
            	a = parseInt(a, 10);
				b = parseInt(b, 10);
				return a > b ? 1 : ( a < b ? -1 : 0 );
            });
        },
		edit: function(number, cb){
            number || (number = this.options.min);
            var self = this;
            cb || (cb = function(i) {
                self.balls.push(i);
            });
			var errStr;
            var j;
            this.balls = [];
            var mutexBoxes = this.getMutexBoxes();
            for (var i = 0; i < $(self.selector).length; i++) {
                j = parseInt( $(self.selector).eq(i).val(), 10);// 取球的值
				if( isNaN(j) ){
					errStr = '请填写数字';
					j = $(self.selector).eq(i).val();
				} else if( j > this.options.amount || j < 1 ){
					errStr = '数字超过范围';
				} else if (j >= this.minBall && $.inArray(j, this.balls) == -1) {
                    if (this.options.mutex) {
                        for (var k = 0; k < mutexBoxes.length; ++k) {
                            if ($.inArray(j, mutexBoxes[k].balls) !== -1) errStr = '互斥错';
                        }
                    } 
                } else {
					errStr = '同种颜色的球数字重复';
				}
				cb(j); // 原样放入界面
            }
			if( errStr ){
				this.error = true;
				cx.Alert({content:errStr});
			}else{
				this.error = false;
			}
            return this.balls;
		},
		getError: function(){
			return this.error;
		},
        calcComb: function() {
        	if (this.getType() === 'ddsh') {
            	var combCount = cx.Math.combine(parseInt($(this.randSel).val())+this.balls.length, this.options.min-this.dans.length);
            } else {
                var combCount = cx.Math.combine(this.balls.length, this.options.min-this.dans.length);
            }
        	if (this.options.playType == '3xzu3') combCount *= 2;
            return combCount;
        },
        joinBalls: function() {
            return this.balls.join(',');
        },
        getNum: function() {
        	return [this.dans.length, this.balls.length];
        },
        getBalls: function(type) {
        	switch (type) {
        		case 'dan':
        			return this.dans;
        			break;
        		case 'sha':
        			return this.shas;
        			break;
        		case 'tuo':
        		default:
        			return this.balls;
        			break;
        	}
        },
        getABalls: function(type) {
        	switch (type) {
        		case 'dan':
        			return this.$dans;
        			break;
        		case 'sha':
        			return this.$shas;
        			break;
        		case 'tuo':
        		default:
        			return this.$balls;
        			break;
        	}
        },
        getType: function() {
        	return this.options.playType;
        },
        addBall: function(i, type) {
        	var arr = this.getBalls(type);
        	if ((this.lotteryId != cx.Lottery.KS) || this.options.playType == 'hz') i = parseInt(i, 10);
        	if ($.inArray(i, arr) > -1) return ;
        	arr.push(i);
        },
        removeBall: function(i, type) {
        	var arr = this.getBalls(type);
        	
        	if (this.lotteryId === cx.Lottery.KS && this.options.playType !== 'hz') {
        		var index;
        		for (j in arr) {
        			if (arr[j].replace(/,/g, '') === i.toString().replace(/,/g, '')) index = j;
            	}
        	}else {
        		i = parseInt(i, 10);
        		var index = $.inArray(i, arr);
        	}
            if (index == -1) return ;
            arr.splice(index, 1);
        },
        removeAll: function() {
            this.balls = [];
            this.dans = [];
            this.shas = [];
            this.$dans.removeClass('selected');
            this.$dans.removeClass('dt-pick');
            if (this.getType() === 'ddsh') {
            	this.$shas.removeClass('selected');
                this.$shas.removeClass('kill-ball');
                this.selReset(this.options.selmin[0], this.options.selmax[0],  this.options.seldefault[0]);
            } else {
            	this.$balls.removeClass('selected');
            	this.$balls.removeClass('dt-pick');
            }
            this.collection.renderBet();
        },
        removeBalls: function() {
        	var self = this;
        	if ($.inArray(this.lotteryId, [cx.Lottery.SYXW, cx.Lottery.JXSYXW, cx.Lottery.HBSYXW]) > -1 && $.inArray(this.getType(), ['qzhi2', 'qzhi3', 'lexuan3']) > -1) {
        		$.each(this.collection.boxes, function(i, ele){
        			if (i != self.index) {
        				$.each(self.balls, function(j, e){
        					ele.$balls.eq(e-1).removeClass('dt-pick');
        				})
        			}
        		})
        	}
            this.balls = [];
            this.$balls.removeClass('selected');
            this.$dans.removeClass('dt-pick');
            this.collection.renderBet();
        },
        addAll: function() {
            for (var i = 1; i <= this.options.amount; ++i) {
                this.addBall(i);
            }
        },
        addOdds: function() {
            for (var i = 0; i < this.odds.length; ++i) {
                this.addBall(this.odds[i]);
            }
        },
        addEvens: function() {
            for (var i = 0; i < this.evens.length; ++i) {
                this.addBall(this.odds[i]);
            }
        },
        addBigs: function() {
            for (var i = 0; i < this.bigs.length; ++i) {
                this.addBall(this.odds[i]);
            }
        },
        addSmalls: function() {
            for (var i = 0; i < this.smalls.length; ++i) {
                this.addBall(this.odds[i]);
            }
        },
        getMutexBoxes: function() {
            if (this.options.mutex == null) {
                return [];
            }
            if (!this.mutexBoxes) {
                var boxes = this.collection.getBoxes();
                var index = $.inArray(this, boxes);
                var box;
                var i = 0
                this.mutexBoxes = [];
                for (; i < boxes.length; ++i) {
                    box = boxes[i];
                    if ((index !== i) && (box.options.mutex === this.options.mutex)) this.mutexBoxes.push(boxes[i]);
                }
            }
            return this.mutexBoxes;
        }
    };

    var CastBasket = cx.CastBasket = function(selector, options) {
        this.$el = $(selector);
        this.lotteryId = options.lotteryId;
        this.tab = options.tab;
        this.tabClass = options.tabClass;
        this.boxes = options.boxes;
        this.issue = options.issue;
        this.multiModifier = options.multiModifier;
        this.multi = 1;
        this.strings = {};
        this.autoId = 0;
        this.$castList = options.$castList || this.$el.find('.cast-list');
        this.orderType = 0;
        this.$betNum = this.$el.find('.betNum');
        this.$betMoney = this.$el.find('.betMoney');
        this.$buyMoney = this.$el.find('.buyMoney input');
        this.betNum = 0;
        this.betMoney = 0;
        this.setStatus = options.setStatus || 0;
        this.setMoney = options.setMoney || 0;
        this.chases = options.chases || {};
        this.chaseLength = options.chaseLength || 0;
        this.chaseMulti = 0;
        this.chaseMoney = 0;
        this.playType = options.playType || 'default';
        this.zj = false;
        this.getCastOptions = this[options.getCastOptions] || this.getCastOptions;
        this.openStatus = 0;
        this.commission = 0;
		this.buyMoney = 0;
		this.guarantee = 0;
		this.rgpctmin = 5;
        this.init();
    };

    CastBasket.prototype = {
        init: function() {
            var self = this;
            
            $.each(this.boxes, function(i, boxes){
            	boxes.setBasket(self);
            })
            
            if ($("#ordertype1").attr('checked')) {
            	self.orderType = 1;
            	$("#ordertype1").parents('.chase-number-tab').find('.ptips-bd').hide();
            	$("#ordertype1").parents('.chase-number').find('.chase-number-bd').show();
            	self.$el.find('.chase-number-table-hd .follow-issue').val('10');
            	self.$el.find('.chase-number-table .follow-multi').val('1');
            	self.$el.find('.chase-number-table :checkbox').attr('checked', 'checked');
            	self.$el.find(".chase-number-table-ft :checkbox:first").removeAttr('checked');
            }
            
            if (this.lotteryId !== cx.Lottery.KLPK) {
            	this.chaseMulti = this.chaseLength * this.$el.find(".chase-number-table-hd .follow-multi:first").val();
                this.chaseMoney = this.chaseMulti * this.betMoney;
                this.$el.find(".chase-number-table-ft .fbig em:first").html(this.chaseLength);
                this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
            } else {
            	this.chaseMulti = 10;
            	this.chaseMoney = 20;
            }
            
            this.multiModifier.setCb(function() {
            	self.multi = parseInt(this.getValue(), 10);
            	$('.Multi').html(self.multi);
                self.renderBetMoney();
                self.setChaseMulti(self.multi);
            });
            this.$el.on('click', '.add-bets', function(e) {
            	if (e.target.id === 'addBets') {
            		var betMoney = 2;
            		self.zj = false;
            		if ($(this).find(':checkbox').attr('checked')) {
            			betMoney = 3;
            			self.zj = true;
            		}
            		self.setBetMoney(betMoney);
                	self.renderBetMoney();
            	}
            });
            this.$el.find('.rand-cast').click(function() {
                var $this = $(this);
                var amount = parseInt($this.data('amount'), 10);
                self.randSelect(amount);
            });
            this.$el.on('click', '.remove-str', function() {
                var $li = $(this).closest('li');
                var index = $li.attr('data-index');
                for (i in self.boxes) {
                	if(index === self.boxes[i].edit) {
                		self.boxes[i].clearButton(i);
                    	self.boxes[i].edit = 0;
                    }
                }
                $li.remove();
                self.remove($li.data('index'));
            });
            this.$el.on('click', '.modify-str', function() {
            	var index = $(this).parent().data('index');
            	if(self.strings[index].playType !== self.playType) self.setType(self.strings[index].playType);
            	var startindex = cx.Lottery.getStartIndex(self.lotteryId, self.playType);
            	$.each(self.strings[index].balls, function(n, e){
            		self.boxes[self.playType].boxes[n].removeAll();
  	              	for (j in self.strings[index].balls[n]) {
	  	              	$(self.strings[index].balls[n][j]).each(function(k, ball) {
	  	              		ball = parseInt(ball, 10);
	  	              		if(!isNaN(ball)) {
	  	              			if(j === 'dan') {
		  	              			self.boxes[self.playType].boxes[n].addBall(ball + '', 'dan');
		  	              			self.boxes[self.playType].boxes[n].$dans.eq(ball - parseInt(startindex[n])).addClass('selected'); 
		  	              			self.boxes[self.playType].boxes[n].$balls.eq(ball - parseInt(startindex[n])).addClass('dt-pick'); 
		  	              		}else if (self.playType === 'dxds') {
			  	              		self.boxes[self.playType].boxes[n].addBall(ball + '');
		  	              			self.boxes[self.playType].boxes[n].$balls.filter('[data-num='+ball+']').addClass('selected'); 
		  	              		}else {
			  	              		self.boxes[self.playType].boxes[n].addBall(ball + '');
		  	              			self.boxes[self.playType].boxes[n].$balls.eq(ball - parseInt(startindex[n])).addClass('selected'); 
		  	              			self.boxes[self.playType].boxes[n].$dans.eq(ball - parseInt(startindex[n])).addClass('dt-pick'); 
		  	              		}
	  	              		}
	  	              	})
  	              	}
            	})
	            self.boxes[self.playType].renderBet();
	            self.boxes[self.playType].edit = $(this).parent().attr('data-index');
	            if ($.inArray(self.lotteryId, [cx.Lottery.SYXW, cx.Lottery.JXSYXW, cx.Lottery.HBSYXW]) > -1) {
	            	if (self.strings[index].playType.indexOf('dt') > -1) {
	            		$("."+self.strings[index].playType.replace(/dt/, '')+" .dt .add-basket").html('确认修改<i class="icon-font">&#xe614;</i>');
	            	}else if(self.playType.indexOf('lexuan') > -1){
	            		$("."+self.playType+" .add-basket").html('确认修改<i class="icon-font">&#xe614;</i>');
	            	}else {
	            		$("."+self.strings[index].playType+" .default .add-basket").html('确认修改<i class="icon-font">&#xe614;</i>');
	            	}
	            } else {
	            	$("."+self.strings[index].playType+" .add-basket, .php-"+self.strings[index].playType+" .add-basket").html('确认修改<i class="icon-font">&#xe614;</i>');;
	            }
	            if ($.inArray(self.lotteryId, [cx.Lottery.SYXW, cx.Lottery.JXSYXW, cx.Lottery.HBSYXW]) === -1) $('html, body').animate({scrollTop: $('.bet-main').offset().top});
            });
            this.$el.find('.clear-list').click(function() {
                self.removeAll();
                for (i in self.boxes) {
                	self.boxes[i].clearButton(i);
                }
            });
            this.$el.on('click', '.submit', function(e) {
                var $this = $(this);
                self.submit($this, self);
            });
            this.$el.find('.setMoney').blur(function(){
            	var money = parseInt($(this).val(), 10);
            	if ($(this).val().match(/\D/g) !== null || !money) {
            		self.setMoney = 1;
            		$(this).val(1);
            	}else {
                	self.setMoney = money;
                }
            });
            this.$el.find('.setMoney').keyup(function(){
            	var money = parseInt($(this).val(), 10);
            	if(money >= 100000){
                	$(this).val(100000);
                	self.setMoney = 100000;
                }else {
                	self.setMoney = money;
                }
            });
            this.$el.find('.chase-number-table-hd .follow-issue').keyup(function(){
            	var num = parseInt($(this).val(), 10),multi = parseInt(self.$el.find(".chase-number-table-hd .follow-multi").val() || self.multiModifier.value, 10),max = $(this).attr('data-max');
            	if ($(this).val().match(/\D/g) !== null) {//非法字符
            		num = 10;
            		$(this).val(10);
            	}else if(num >= max){
                	$(this).val(max);
                    num = max;
                }
            	if (!isNaN(num) && num >= 2) self.setChaseByIssue(num, multi);
            });
            this.$el.find('.chase-number-table-hd .follow-issue').blur(function(){
            	var num = parseInt($(this).val(), 10),multi = parseInt((self.$el.find(".chase-number-table-hd .follow-multi").val() || self.multiModifier.value), 10);
            	if ($(this).val() === '' || parseInt($(this).val(), 10) < 2) {
            		num = 2;
            		$(this).val(2);
            		self.setChaseByIssue(num, multi);
            	}
            });
            this.$el.find(".chase-number-table-hd .follow-multi").keyup(function(){
            	var max = $(this).data('max');
            	if ($(this).val().match(/\D/g) !== null) {//非法字符
            		$(this).val(1);
            	}else if(parseInt($(this).val()) >= max){
                	$(this).val(max);
                }else if (!$(this).val() || $(this).val() == 0){
                	$(this).val('');
                }
            	var multi = parseInt($(this).val(), 10);
            	if (!isNaN(multi) && multi >= 1) {
            		self.chaseMulti = 0;
            		self.chaseMoney = 0;
                	var issue = [];
                	for (i in self.chases) {
                		self.chases[i].multi = multi;
                		self.chases[i].money = multi * self.betMoney;
                		self.chaseMulti += multi;
                	}
                	
                	self.chaseMoney = self.chaseMulti * self.betMoney;
                	self.$el.find(".chase-number-table-bd tbody tr").each(function(){
                		issue.push($(this).attr('data-issue'));
                	})
                	self.renderChase(issue);
                	self.$el.find(".chase-number-table-ft .fbig em:last").html(self.chaseMoney);
            	}
            });
            this.$el.find(".chase-number-table-hd :checkbox").click(function(){
            	self.chases = {};
            	self.chaseMoney = 0;
            	self.chaseMulti = 0;
            	var issue = [];
            	if ($(this).attr('checked') == 'checked') {
            		var multi = parseInt(self.multiModifier.value, 10);
            		$(".chase-number-table-bd tbody tr").each(function(){
            			i = $(this).attr('data-issue');
                		self.setChaseByI(i);
                		self.chases[i].multi = multi;
                		self.chases[i].money = multi * self.betMoney;
                		self.chaseMulti += multi;
                		issue.push(i);
                	})
                	self.chaseLength = $(".chase-number-table-bd tbody tr").length;
            		self.chaseMoney = self.chaseMulti * self.betMoney;
            		self.$el.find('.chase-number-table-hd .follow-issue').val(self.chaseLength);
            		self.$el.find('.chase-number-table-hd .follow-multi').val(multi);
            		self.$el.find(".chase-number-table-ft .fbig em:first").html(self.chaseLength);
            		self.$el.find(".chase-number-table-ft .fbig em:last").html(self.chaseMoney);
            		self.renderChase(issue);
            	}else {
            		self.chaseLength = 0;
            		self.$el.find(".chase-number-table-bd tbody :checkbox").removeAttr('checked');
            		self.$el.find('.chase-number-table-hd .follow-issue').val('0');
            		self.$el.find('.follow-multi').val('');
            		self.$el.find('.follow-money').html('0');
            		self.$el.find(".chase-number-table-ft .fbig em:first").html('0');
            		self.$el.find(".chase-number-table-ft .fbig em:last").html('0');
            	}
            });
            this.$el.on('click', ".chase-number-table-bd :checkbox", function(){
            	var i = $(this).parents('tr').attr('data-issue');
            	if ($(this).attr('checked') == 'checked') {
            		if ($(".chase-number-table-bd :checkbox[checked!='checked']").length == 0) $(".chase-number-table-hd :checkbox").attr('checked', 'checked');
            		multi = parseInt((self.$el.find(".chase-number-table-hd .follow-multi").val() || self.multiModifier.value), 10);
            		self.setChaseByI(i);
        			self.chases[i].multi = multi;
            		self.chases[i].money = multi * self.betMoney;
        			self.chaseMulti += multi;
            		self.chaseLength++;
            		$(this).parents('tr').find(".follow-multi").val(multi);
            		$(this).parents('tr').find(".follow-money").html(self.chases[i].money);
            	}else {
        			self.chaseMulti -= self.chases[i].multi;
            		delete self.chases[i];
            		self.chaseLength--;
            		$(this).parents('tr').find(".follow-multi").val('');
            		$(this).parents('tr').find(".follow-money").html('0');
            		self.$el.find(".chase-number-table-hd :checkbox").removeAttr('checked');
            	}
            	self.chaseMoney = self.chaseMulti * self.betMoney;
            	self.$el.find('.chase-number-table-hd .follow-issue').val(self.chaseLength);
            	self.$el.find(".chase-number-table-ft .fbig em:first").html(self.chaseLength);
            	self.$el.find(".chase-number-table-ft .fbig em:last").html(self.chaseMoney);
            });
            this.$el.on('keyup', ".chase-number-table-bd .follow-multi", function(){
            	var max = self.$el.find(".chase-number-table-hd .follow-multi").data('max');
            	
            	if ($(this).val().match(/\D/g) !== null) {//非法字符
            		$(this).val(1);
            	}else if(parseInt($(this).val()) >= max){
                	$(this).val(max);
                }else if (!$(this).val() || $(this).val() == 0){
                	$(this).val('');
                }
            	multi = parseInt($(this).val() || 0, 10);
            	if (!isNaN(multi)){
            		var i = $(this).parents('tr').attr('data-issue');
                	self.setChaseByBodyMulti($(this), multi, i);
            	}
            	
            });
            this.$el.find(".chase-number-table-ft :checkbox:first, .setStatus").click(function(){
            	self.setStatus = 0;
            	if ($(this).attr('checked') == 'checked') self.setStatus = 1;
            });
            this.$el.find('.commission').on('click', 'li', function(){
				$('.commission li').removeClass('cur');
				$(this).addClass('cur');
				self.commission = $(this).data('val');
				self.rgpctmin = (self.commission <= 5) ? 5 : self.commission;
				var buyMoney = Math.ceil(self.betMoney * self.multiModifier.getValue() * self.rgpctmin / 100);
				self.buyMoney = buyMoney < self.buyMoney ? self.buyMoney : buyMoney;
				if ($('.guaranteeAll').attr('checked') || self.guarantee > self.betMoney * self.multiModifier.getValue() - self.buyMoney) {
					self.guarantee = self.betMoney * self.multiModifier.getValue() - self.buyMoney;
					$('.guaranteeAll').attr('checked', 'checked');
	            	self.renderGuarantee();
	            }
				self.renderBuyMoney();
			});
			this.$buyMoney.on('blur', function(){
				buyMoney = isNaN(parseInt($(this).val(), 10)) ? 0 : parseInt($(this).val(), 10);
				buyMoneymin = Math.ceil(self.betMoney * self.multiModifier.getValue() * self.rgpctmin / 100);
				self.buyMoney = (buyMoney < buyMoneymin) ? buyMoneymin : (buyMoney > self.betMoney * self.multiModifier.getValue() ? self.betMoney * self.multiModifier.getValue() : buyMoney);
				if ($('.guaranteeAll').attr('checked') || self.guarantee > self.betMoney * self.multiModifier.getValue() - self.buyMoney) {
					self.guarantee = self.betMoney * self.multiModifier.getValue() - self.buyMoney;
					$('.guaranteeAll').attr('checked', 'checked');
	            	self.renderGuarantee();
	            }
				self.renderBuyMoney();
			});
			this.$el.find('.guarantee').on('blur', 'input.form-item-ipt', function(){
				guarantee = isNaN(parseInt($(this).val(), 10)) ? 0 : parseInt($(this).val(), 10);
				$('.guaranteeAll').removeAttr('checked');
				if (guarantee >= (self.betMoney * self.multiModifier.getValue() - self.buyMoney)) {
					guarantee = self.betMoney * self.multiModifier.getValue() - self.buyMoney;
					$('.guaranteeAll').attr('checked', 'checked');
				}
				self.guarantee = guarantee < 0 ? 0 : guarantee;
				self.renderGuarantee();
			});
			this.$el.find('.guaranteeAll').on('click', function(){
				self.guarantee = self.betMoney * self.multiModifier.getValue() - self.buyMoney;
	            self.renderGuarantee();
			});
			this.$el.find('input[name=bmsz]').click(function(){
				self.openStatus = $(this).val();
			})
        },
        submit: function($el, self) {
        	if ($el.hasClass('not-login') || !$.cookie('name_ie')) {
            	cx.PopAjax.login(1);
                return ;
            }
        	
        	if ($el.hasClass('not-bind')) return ;
			
            var data = this.getCastOptions();
            data.isToken = 1;
            if (data.betTnum == 0) {
                if(!$('._radio_selected ul li.cur').hasClass('dssc') && !$('._bet_tab_hd ul li.current').hasClass('dssc'))
                {
                 new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>至少选择<span class='num-red'>１</span>注号码才能投注，请先选择方案"});
                 return ;
                }
            }    
            if (data.orderType == 1 && data.totalIssue <= 1) {
            	cx.Alert({content: "<i class='icon-font'>&#xe611;</i>您好，追号玩法须至少选择<span class='num-red'> 2 </span>期"});
            	return ;
            }
            
            // 最大金额前端限制
        	if (data.orderType == 1) {
        		var checkflag = false;
        		$.each(data.chases.split(';'), function(i, e){
        			if (parseInt(e.split('|')[2], 10) > 20000) {
        				checkflag = true;
        				return false;
        			}
        		})
        		if (checkflag) {
        			new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>２万</span>元，请修改订单后重新投注"});
        			return ;
        		}
        	}else {
        		if ( data.money >20000 ) {
                    new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>订单总额需小于<span class='num-red'>２万</span>元，请修改订单后重新投注"});
                    return ;
                }
        	}
			
            if (this.$el.find(".ipt_checkbox#agreenment").get(0) && !this.$el.find(".ipt_checkbox#agreenment").attr("checked")) {
            	if (this.$el.find(".risk_pro").length > 0) {
            		return void new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请先阅读并同意《用户委托投注协议》<br>《限号投注风险须知》后才能继续"});
            	} else {
            		return void new cx.Alert({content: "<i class='icon-font'>&#xe611;</i>请先阅读并同意《用户委托投注协议》后才能继续"});
            	}
            }
            
            cx.castCb(data, {ctype:'create', lotteryId:self.lotteryId, orderType:self.orderType, betMoney:self.betMoney * self.multiModifier.getValue(), chaseLength:self.chaseLength, buyMoney:self.buyMoney, guarantee:self.guarantee, issue:self.issue});
        },
        getCastOptions1: function() {
            var self = this;
            switch (self.orderType) {
            	case 1:
            		var endTime = '';
                	for (i in self.chases) {
                    	if (endTime === ''){
                    		endTime = self.chases[i].show_end_time;
                    	}else {
                    		break;
                    	}
                    }
                	var data = {
                        money: self.chaseMoney,
                        multi: self.chaseMulti,
                        setStatus: self.setStatus,
                        setMoney: self.setStatus == 1 ? self.setMoney : '',
                        totalIssue: self.chaseLength,
                        chases: Lottery.toChaseString(self.chases), endTime: endTime
                    };
            		break;
            	case 4:
            		var data = {
                        money: self.betMoney * self.multiModifier.getValue(),
                        multi: self.multiModifier.getValue(),
                        issue: self.issue,
                        endTime: hmDate.getFullYear()+"-"+padd(hmDate.getMonth() + 1)+"-"+padd(hmDate.getDate())+" "+padd(hmDate.getHours())+":"+padd(hmDate.getMinutes())+":"+padd(hmDate.getSeconds()),
                        buyMoney: self.buyMoney,
                        commissionRate: self.commission,
                        guaranteeAmount: self.guarantee,
                        openStatus: self.openStatus,
                        openEndtime: realendTime
                    };
            		break;
            	case 0:
            	default:
            		var data = {money: self.betMoney * self.multiModifier.getValue(), multi: self.multiModifier.getValue(), issue: self.issue, endTime: ENDTIME};
            		break;
            }
            data.ctype = 'create';
            data.buyPlatform = 0;
            data.codes = Lottery.toCastString(self.lotteryId, self.strings, self.zj);
            data.lid = self.lotteryId;
            data.playType = 0;
            data.betTnum = self.betNum;
            data.isChase = self.zj ? 1 : 0;
            data.orderType = self.orderType;
            return data;
        },
        setChaseMoney: function() {
        	this.chaseMoney = this.betMoney * this.chaseMulti;
        	this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
        	for (i in this.chases) {
        		this.chases[i].money = this.chases[i].multi *　this.betMoney;
            	$(".chase-number-table-bd tbody tr[data-issue="+i+"] .follow-money").html(this.chases[i].money);
            }
        },
        setChaseMulti: function(multi) {
        	this.chaseMulti = 0;
        	$(".chase-number-table-hd .follow-multi").val(multi);
        	for (i in this.chases) {
        		this.chaseMulti += multi;
        		this.chases[i].multi = multi;
        		this.chases[i].money = multi *　this.betMoney;
        		$(".chase-number-table-bd tbody tr[data-issue="+i+"] .follow-multi").val(multi);
            	$(".chase-number-table-bd tbody tr[data-issue="+i+"] .follow-money").html(this.chases[i].money);
            }
        	this.chaseMoney = this.betMoney * this.chaseMulti;
        	this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
        },
        setChaseByIssue: function(num, multi) {
        	var tbstr = '', j = 0, issue = [], self=this;
        	this.chaseMulti = 0;
        	this.chaseMoney = 0;
        	this.chaseLength = 0;
        	this.chases = {};
        	
        	if (num > 0) {
        		$.each(chases, function(i, e){
        			if (j < num) {
        				issue.push(i);
        				self.setChaseByI(i);
        				self.chases[i].multi = multi;
        				self.chases[i].money = multi * self.betMoney;
        				self.chaseMulti += multi;
                		j++;
                		self.chaseLength++;
        			}
        		})
        		this.chaseMoney = this.chaseMulti * this.betMoney;
        		this.$el.find(".chase-number-table-bd tbody").html(tbstr);
        	}
        	this.renderChase(issue);
        	this.$el.find(".chase-number-table-hd :checkbox").attr('checked', 'checked');
        	this.$el.find(".chase-number-table-hd .follow-multi").val(multi);
        	this.$el.find(".chase-number-table-ft .fbig em:first").html(num);
        	this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
        },
        setChaseByI: function(i) {
        	if (!this.chases[i]) this.chases[i] = {};
        	this.chases[i].award_time = chases[i].award_time;
    		this.chases[i].show_end_time = chases[i].show_end_time;
        },
        setChaseByBodyMulti: function(el, multi, i) {
        	if (this.chases[i]) {
        		this.chaseMulti -= this.chases[i].multi;
        		this.chaseLength --;
        		delete this.chases[i];
        	}
        	if (multi > 0) {
        		this.setChaseByI(i);
        		this.chases[i].multi = multi;
        		this.chases[i].money = multi * this.betMoney;
        		this.chaseMulti += multi;
        		this.chaseLength ++;
        		el.parents('tr').find(':checkbox').attr('checked', 'checked');
        	}else {
        		el.parents('tr').find(':checkbox').removeAttr('checked', 'checked');
        	}
        	this.chaseMoney = this.chaseMulti * this.betMoney;
			el.parents('tr').find('.follow-money').html(multi * this.betMoney);
			this.$el.find('.chase-number-table-hd .follow-issue').val(this.chaseLength);
			this.$el.find(".chase-number-table-ft .fbig em:first").html(this.chaseLength);
			this.$el.find(".chase-number-table-ft .fbig em:last").html(this.chaseMoney);
        },
        renderChase: function(issue) {
        	var tbstr = '', j = 1, self = this;
        	$.each(issue, function(i, e){
        		multi = self.chases[e] ? self.chases[e].multi : ($(".follow-multi:first").val() || self.multiModifier.value);
				tbstr += '<tr data-issue="'+e+'"><td>'+j+'</td><td class="tal"><input type="checkbox"';
        		if (self.chases[e]) tbstr += ' checked="checked"';
        		tbstr += '>'+e+'期';
        		if (e == self.issue) tbstr += ' <span class="main-color-s">（当前期）</span>';
        		tbstr += '</td><td><input type="text"';
        		if (self.chases[e]) tbstr += ' value="'+multi+'"';
        		tbstr += ' class="ipt-txt follow-multi">倍</td><td><span class="main-color-s follow-money">';
        		if (self.chases[e]) {
        			tbstr += multi * self.betMoney;
        		}else {
        			tbstr += '0';
        		}
        		tbstr += '</span>元</td><td>'+chases[e].award_time.substring(0, 16)+'</td></tr>';
        		j++;
        	})
    		this.$el.find(".chase-number-table-bd tbody").html(tbstr);
    		if (this.$el.find(".chase-number-table-bd :checkbox[checked!='checked']").length == 0) $(".chase-number-table-hd :checkbox").attr('checked', 'checked');
        },
        setType: function(type) {
        	this.playType = type;
        	$("."+this.tab).removeClass(this.tabClass);
        	if ($.inArray(this.lotteryId, [cx.Lottery.SYXW, cx.Lottery.JXSYXW, cx.Lottery.HBSYXW]) > -1 ) {
        		var nt = type.indexOf('dt');
        		if (type.indexOf('lexuan') > -1) {
        			var ty = type;
        			var t = type.replace(/\d/, '');
        			$("."+this.tab+".last").addClass(this.tabClass);
        			$("."+this.tab+".last").attr('data-type', type);
        		}else {
        			if (nt == -1) {
        				var ty = 'default';
            		}else {
            			var ty = 'dt';
            		}
        			var t = type.replace(/dt/, '');
        			$("."+this.tab+"[data-type='"+t+"'], ."+this.tab+"[data-type='"+t+"dt']").addClass(this.tabClass);
            		$("."+this.tab+"[data-type='"+t+"'], ."+this.tab+"[data-type='"+t+"dt']").attr('data-type', type);
        		}
        		rfshhisty(type);
        		$("#"+type).attr("checked", "checked");        		
        		$(".bet-type-link-item, .bet-pick-area").hide();
        		$(".bet-type-link-item."+t).show();
        		$(".bet-type-link-item."+t+" ."+ty).show();
        	}else {
        		$("."+this.tab+"[data-type="+type+"]").addClass(this.tabClass);
        		$(".bet-tab-bd > .bet-pick-area, .bet-tab-bd > .bet-tab-bd-inner").hide();
            	$(".bet-pick-area."+type+", .bet-tab-bd-inner."+type).show();
        	}
        	if ($.inArray(this.lotteryId, [cx.Lottery.SSQ, cx.Lottery.DLT]) > -1) {
        		$(".bet-sup-bar a:eq(3)").remove();
        		if (this.playType === 'dt') {
        			$(".bet-sup-bar").append('<a href="activity/dantuo" target="_blank" class="what-dt">什么是<br>胆拖投注？</a>');
        			$(".bet-sup-bar a:eq(0)").attr('href', 'activity/dantuo');
        		} else if (this.playType === 'ddsh') {
        			$(".bet-sup-bar").append('<a href="activity/dingdanshahao" target="_blank" class="what-dt">什么是<br>定胆杀号？</a>');
        			$(".bet-sup-bar a:eq(0)").attr('href', 'activity/dingdanshahao');
        		} else {
        			$(".bet-sup-bar a:eq(0)").attr('href', tzjqurl);
        		}
        	}
        },
        setIssue: function(issue){
        	this.issue = issue
        },
        add: function(balls) {
            this.autoId += 1;
            this.strings[this.autoId] = balls;
            this.$castList.prepend(this.renderString(balls, this.autoId));
            this.betNum += balls.betNum;
            this.betMoney += balls.betMoney;
            this.setChaseMoney();
            this.renderAllBet();
        },
        addAll: function(balls) {
        	var self = this;
        	$.each(balls, function(i, e){
        		self.autoId += 1;
        		self.strings[self.autoId] = e;
        		self.betNum += e.betNum;
        		self.betMoney += e.betMoney;
        		self.$castList.prepend(self.renderString(e, self.autoId, false, false));
        	})
        	this.setChaseMoney();
            this.renderAllBet();
        },
        edit: function(balls, id) {
        	var betNum = this.strings[id].betNum;
        	var betMoney = this.strings[id].betMoney;
        	this.strings[id] = balls;
            this.$castList.find("li[data-index="+id+"]").replaceWith(this.renderString(balls, id, true));
            this.betNum += balls.betNum-betNum;
            this.betMoney += balls.betMoney-betMoney;
            this.setChaseMoney();
            this.renderAllBet();
            this.boxes[balls.playType].clearButton(balls.playType);
        },
        rand: function(amount) {
            var randStr = '';
            for (var i = 0; i < amount; ++i) {
                randStr = boxes[self.playType].rand().join(' ');
                this.add(randStr);
            }
        },
        randSelect: function(amount) {
            var rand = [];
            for (var i = 0; i < amount; ++i) {
                rand.push(this.boxes[this.playType].rand1(this.lotteryId, this.playType));
            }
            this.addAll(rand);
        },
        renderAllBet: function() {
            this.$betNum.html(this.betNum);
            this.renderBetMoney();
        },
        renderBetMoney: function() {
            var buyMoney = Math.ceil(this.betMoney * this.multi * this.rgpctmin / 100);
            this.buyMoney = (buyMoney <= this.buyMoney && this.buyMoney <= this.betMoney * this.multi) ? this.buyMoney : (buyMoney > this.buyMoney ? buyMoney : this.betMoney * this.multi);
            if ($('.guaranteeAll').attr('checked') || this.guarantee > this.betMoney * this.multi - this.buyMoney) this.guarantee = this.betMoney * this.multi - this.buyMoney;
            this.$betMoney.html(this.betMoney * this.multi);
            this.renderGuarantee();
            this.renderBuyMoney();
        },
        renderBuyMoney: function() {
        	this.$buyMoney.val(this.buyMoney).parents('.buyMoney').find('span em:first').html(this.rgpctmin);
        	this.buyMoney > 0 ? this.$buyMoney.parents('.buyMoney').find('u').show().find('em').html(Math.floor(this.buyMoney * 100/(this.betMoney * this.multi))) : this.$buyMoney.parents('.buyMoney').find('u').hide();
			$('.guarantee').find('span em:first').html(this.betMoney * this.multi - this.buyMoney);
			$('.buy_txt').html("<em class='main-color-s'>"+(this.buyMoney+this.guarantee)+"</em> 元 <span>（认购"+this.buyMoney+"元+保底"+this.guarantee+"元）</span></span>");
        },
        renderGuarantee: function() {
        	$('.guarantee input.form-item-ipt').val(this.guarantee).parents('.guarantee').find('span em:last').html(this.betMoney == 0 ? 0 : Math.floor(this.guarantee * 100 / (this.betMoney * this.multi)));
        	$('.buy_txt').html("<em class='main-color-s'>"+(this.buyMoney+this.guarantee)+"</em> 元 <span>（认购"+this.buyMoney+"元+保底"+this.guarantee+"元）</span></span>");
        },
        removeAll: function() {
            this.strings = {};
            this.betNum = 0;
            this.betMoney = 0;
            this.$castList.empty();
            this.setChaseMoney();
            this.renderAllBet();
        },
        remove: function(index) {
            var selected = this.strings[index];
            this.betNum -= selected.betNum;
            this.betMoney -= selected.betMoney;
            delete this.strings[index];
            this.setChaseMoney();
            this.renderAllBet();
        },
        setBetMoney: function(betMoney){
        	var self = this;
        	$.each(self.boxes, function(i, box){
        		box.setBetMoney(betMoney);
        	})
        	$.each(self.strings, function(s, string){
        		self.betMoney += betMoney * string.betNum-string.betMoney;
        		string.betMoney = betMoney * string.betNum;
        		$('.cast-list').find('li[data-index='+s+']').find('.bet-money').html(string.betMoney + '元');
        	})
        	self.setChaseMoney();
        },
        renderString: function(allBalls, index, hover, noedit) {
        	var tpl = '<li ';
        	if(hover) tpl += ' class="hover"'
        	tpl += ' data-index="'+index+'"><span class="bet-type">';
        	if ($.inArray(this.lotteryId, [Lottery.PLS, Lottery.FCSD, Lottery.SYXW, Lottery.JXSYXW, Lottery.HBSYXW]) > -1) 
        		tpl += cx.Lottery.getPlayTypeName(this.lotteryId, cx.Lottery.playTypes[this.lotteryId][allBalls.playType]);
            var ballTpl = [], dt = false, tuoTpl = '', self=this;
            $.each(allBalls.balls, function(pi, ele){
            	if (pi > 0 && (self.lotteryId == Lottery.DLT || self.lotteryId == Lottery.SSQ)) {
                    var tmpTpl = '<span class="num-blue">';
                } else {
                    var tmpTpl = '<span class="num-red">';
                }
                $.each(ele, function(ti, e){
                	var tempTpl = '';
                	e.sort(function(a, b){
    					a = parseInt(a, 10);
    					b = parseInt(b, 10);
    					return a > b ? 1 : ( a < b ? -1 : 0 );
    				});
                	$.each(e, function(bi, el) {
                		if($.inArray(self.lotteryId, [Lottery.DLT, Lottery.SSQ, Lottery.SYXW, Lottery.JXSYXW, Lottery.HBSYXW]) > -1) {
                    		tempTpl += pad(el) + ' ';
                    	}else {
                    		tempTpl += el + ' ';
                    	}
                		function pad(i) {
                            i = '' + i;
                            if (i.length < 2) i = '0' + i;
                            return i;
                        }
                	})
                	if(ti === 'dan') {
                		dt = true;
                		tmpTpl += "("+tempTpl.replace(/(\s*$)/g,'')+") ";
                	}else {
                		tuoTpl = tempTpl;
                	}
                })
                tmpTpl += tuoTpl+'</span>';
                ballTpl.push(tmpTpl);
            })
            ballTpl = ballTpl.join('<em>|</em>');
            if ($.inArray(this.lotteryId, [Lottery.SYXW, Lottery.JXSYXW, Lottery.HBSYXW]) > -1 && parseInt(cx.Lottery.playTypes[this.lotteryId][allBalls.playType], 10) >= 9 && parseInt(cx.Lottery.playTypes[this.lotteryId][allBalls.playType], 10) <= 12) {
            	if (dt) tpl = tpl.replace(/(组|直)选/g, '')+'胆拖';
            }else {
            	if (dt) {
                	tpl += '胆拖';
                } else if (allBalls.betNum > 1) {
            		tpl += '复式';
            	} else {
            		tpl += '单式';
            	}
            }
            
        	tpl += '</span><div class="num-group">'+ballTpl+'</div>';
        	if (!noedit) tpl += '<a href="javascript:;" class="remove-str">删除</a><a href="javascript:;" class="modify-str">修改</a>';
        	tpl += '<span class="bet-money">'+ allBalls.betMoney +'元</span></li>';
            return tpl;
        }
    };
})();
function chgbtn () {
	if (selling == 2 && ($.inArray(cx._basket_.orderType, [0, 1]) > -1 || (hmselling == 1 && hmendTime * 1000 >= (new Date()).valueOf()))) {
		$("[id^=pd][id$=_buy]").removeClass('btn-disabled').addClass('needTigger submit').html('确认预约');
		$('body').find('#buy_tip').remove();
	}else if(selling == 1) {
		$("[id^=pd][id$=_buy]").removeClass('needTigger submit').addClass('btn-disabled').html('期次更新中');
		if ($("[id^=pd][id$=_buy]").next('#buy_tip').length == 0)$("[id^=pd][id$=_buy]").after("<p id='buy_tip' class='main-color' style='margin: 4px 0 6px'>（下一期开售时间为"+realendTime.match(/\d{2}:\d{2}/)+"）</p>")
	}else {
		$("[id^=pd][id$=_buy]").removeClass('needTigger submit').addClass('btn-disabled').html('暂停预约');
		$('body').find('#buy_tip').remove();
	}
}
$(function(){
	if (typeof hmDate === 'object') 
		$('.hmendTime .form-item-txt').html(hmDate.getFullYear() + "-" + padd(hmDate.getMonth() + 1) + "-" + padd(hmDate.getDate()) + " " + padd(hmDate.getHours()) + ":" + padd(hmDate.getMinutes()) + ":" + padd(hmDate.getSeconds()));
	if (typeof selling !== 'undefined') chgbtn();
//投注栏点击修改添加hover状态
  $('.cast-list').on('click', '.modify-str', function(){
  	$(this).parents('.cast-list').find('li').removeClass('hover');
    $(this).parent('li').addClass('hover');
  })
  
//追号切换
  $('.buy-type-hd').on('click', 'li', function(){
	  if ($(this).index() == 1) {
    	  cx._basket_.orderType = 1;
          $(this).find('.ptips-bd').hide();
          var str = '连续多期购买同一个（组）号码<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>追号：</em>选好投注号码后，对期数、期<br>号、倍数进行设置后，系统按照设置<br>进行购买。">&#xe613;</i>';
          $(".chase-number-notes").html(str);
      }else if ($(this).index() == 2){
    	  cx._basket_.orderType = 4;
    	  $(this).find('.ptips-bd').hide();
    	  var str = '多人出资购买彩票，奖金按购买比例分享<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>合买：</em>选好投注号码后，由多人出资<br>购买彩票。中奖后，奖金按购买比例<br>分享。">&#xe613;</i>';
          $(".chase-number-notes").html(str);
      }else{
    	  cx._basket_.orderType = 0;
          $('#ordertype1').parents("span").find('.ptips-bd').show();
          var str = '由购买人自行全额购买彩票，独享奖金<span class="mod-tips"><i class="icon-font bubble-tip" tiptext="<em>自购：</em>选好投注号码后，由自己全额<br>支付购彩款。中奖后，自己独享全部<br>税后奖金。">&#xe613;</i>';
          $(".chase-number-notes").html(str);
      }
	  chgbtn();
  })
  $('.chase-number-notes, .guarantee').on('mouseenter', '.bubble-tip', function(){
      $.bubble({
          target:this,
          position: 'b',
          align: 'l',
          content: $(this).attr('tiptext'),
          width:'auto'
      })
  }).on('mouseleave', '.bubble-tip', function(){
      $('.bubble').hide();
  });
  
  $(".bet-tab-hd ul").tabPlug({
      cntSelect: '.bet-tab-bd',
      menuChildSel: 'li',
      onStyle: 'current',
      cntChildSel: '.bet-tab-bd-inner',
      eventName: 'click'
  });
  
  $(".bet-type-tab-hd").tabPlug({
      cntSelect: '.bet-type-tab-bd',
      menuChildSel: 'li',
      onStyle: 'selected',
      cntChildSel: '.bet-pick-area',
      eventName: 'click'
  });

  $(".bet-syxw .bet-type-link, .bet-k3 .bet-type-link, .bet-klpk .bet-type-link").tabPlug({
      cntSelect: '.bet-type-link-bd',
      menuChildSel: 'li',
      onStyle: 'selected',
      cntChildSel: '.bet-type-link-item',
      eventName: 'click'
  });
  
});
