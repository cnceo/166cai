<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="送贺卡赢8888元">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>送贺卡赢8888元</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/heka.min.css');?>">
    <?php $this->load->view('comm/baidu'); ?>
</head>
<body ontouchstart="">
    <div class="wrapper" id="app" v-cloak>
        <div class="join-wrap" v-if="!showHk">
            <div class="join-bd ui-tab">
                <ul class="ui-tab-nav">
                    <li v-for="(idx, item) in tab.list" :class="tab.curIdx === idx ? 'current': ''" @click="choose(idx)" v-html="item"></li>
                </ul>
                <div class="ui-tab-content">
                    <div class="join-tab-item fix-pd" v-if="tab.curIdx === 0">
                        <div class="join-hd" :class="{'timeout': timeOut, 'beOut': beOut}">
                            <h1>送贺卡，赢8888元</h1>
                            <a href="javascript:" v-if="canChoose" @click="goChooseHk">免费挑选贺卡</a>   
                        </div>
                        <div class="join-rule">
                            <h2>活动规则</h2>
                            <ol>
                                <li v-for="item in rule">{{ item }}</li>
                            </ol>
                        </div>
                    </div>
                    <div class="join-tab-item" v-if="tab.curIdx === 1">
                        <div class="join-lucky">
                            <div class="join-lucky-hd">
                                <p v-if="cSlogan" v-html="cSlogan"></p>
                                <template v-else>
                                    <p v-if="numTime > 0">您有 <em>{{ numTime }}</em> 次抽奖机会，赶紧抽奖吧~</p>
                                    <p v-else>您还没有抽奖机会，快去邀请好友吧~</p>
                                </template>
                            </div>
                            <div class="join-m">
                                <ul>
                                    <li v-for="(idx, item) in gifs.list" :class="'join-m' + (idx + 1) + ' ' + (idx === gifs.curIdx ? 'current' : '')">
                                        <em><b>{{ item.split(':')[1] }}</b>元</em><span>{{ item.split(':')[0] }}</span>
                                    </li>
                                </ul>
                                <a href="javascript:;" class="btn-start join-m-btn" :class="{'disabled': startBtn}" @click="start">立即抽奖</a>
                            </div>
                        </div>
                        <div class="join-roll">
                            <ul>
                                <li v-for="item in prizeListSort" track-by="$index">{{item}}</li>
                            </ul>
                        </div>
                        <div class="join-roster">
                            <h2>我的抽奖记录</h2>
                            <div class="join-roster-bd join-table">
                            	<?php if($awards):?>
                                <div class="join-table-th">
                                    获得奖品<span>抽奖时间</span>
                                </div>
                                <div class="join-table-td">
                                <ul>
                                	<?php foreach ($awards as $value):?>
                                    <li><?php echo $value['mark'];?><span><?php echo date('Y-m-d H:i', strtotime($value['created']));?></span></li>
                                    <?php endforeach;?>
                                </ul>
                            	</div>
                             	<?php else:?>
                                <div class="join-table-td">
                                    <ul></ul>
                                    <div class="no-data">暂无抽奖记录</div>
                                </div>
                                <?php endif;?>
                            </div>
                        </div>
                        <?php if($awards):?>
                    		<a href="/ios/redpack/index/<?php echo $token;?>" class="go-rp">查看奖品</a>
                    	<?php endif;?>
                    </div>
                </div>
            </div>
            <div class="popup bingo" v-if="result">
                <div class="inner" @touchmove.prevent="">
                    <div class="title">
                        <div class="gifs"><b>{{ result.split(':')[1] }}</b>元</div>
                        恭喜您获得{{ result.split(':')[0] }}
                    </div>
                    <p>亿万大奖等你赢</p>
                    <span class="close" @click.prevent="goOnRun">&times;</span>
                    <a href="javascript:" @click="goOnRun" class="btn">继续抽奖</a>
                    <p class="sub-link"><a href="/ios/redpack/index/<?php echo $token;?>">立即查看</a></p>
                </div>
                <div class="mask" @touchmove.prevent=""></div>
            </div>
            <div class="popup none" v-if="makeAchance">
                <div class="inner" @touchmove.prevent="">
                    <div class="title">暂无抽奖机会</div>
                    <p>送贺卡获更多抽奖次数</p>
                    <span class="close" @click.prevent="makeAchance = false">&times;</span>
                    <a href="javascript:" @click="showHk = true" class="btn">制作贺卡</a>
                </div>
                <div class="mask" @touchmove.prevent=""></div>
            </div>
        </div>
        <div class="heka" v-else>
            <div class="heka-img">
                <div class="inner">
                    <img :src="hkImg" alt="">
                </div>
            </div>
            <div class="heka-action">
                <div class="choose">
                <h2>选择贺卡</h2>
                <ul>
                    <li v-for="item in hk.list" :class="{'cur': $index === hk.curIdx }" @click="chooseHk($index)">
                        <img :src="'/caipiaoimg/static/images/active/heka/' + item" alt="">
                    </li>
                </ul>
                </div>
                <div class="share">
                <a href="javascript:" @click="share">发送给我的微信好友</a>
                </div>
            </div>
            <div class="popup success" v-if="success">
                <div class="inner" @touchmove.prevent="">
                    <div class="title">分享成功！</div>
                    <p>好友购彩您可抽奖</p>
                    <span class="close" @click.prevent="success = false">&times;</span>
                    <a href="javascript:" @click="success = false" class="btn">继续分享</a>
                    <p class="sub-link"><a href="#yqhy" @click="goIndex">回到活动首页</a></p>
                </div>
                <div class="mask" @touchmove.prevent=""></div>
            </div>
        </div>

        <toast :show.sync="toast.show" type="text" :text="toast.text"></toast>
    </div>

    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js');?>"></script>
    <script>
        new Vue({
            el: '#app',
            data: function () {
                return {
                    showHk: false,
                    curTime: null,
                    tipsTime: new Date('<?php echo date('Y/m/d', strtotime($endTime));?>'),
                    endTime: new Date('2018/02/28 23:59'),
                    timeOut: false,
                    tab: {
                        curIdx: 0,
                        list: ['邀请好友', '抽奖'],
                    },
                    gifs: {
                        curIdx: 0,
                        list: ['彩金红包:8888', '购彩红包:5', '彩金红包:166', '购彩红包:3', '彩金红包:888', '购彩红包:20', '购彩红包:50', '购彩红包:10'],
                        map: [8,2,6,1,7,4,5,3],
                    },
                    cSlogan: '',
                    startBtn: false,
                    time: 400,
                    changeSpeed: false,
                    havePrizeShow: -1,
                    cycle: 0,
                    result: '',
                    rule: [
                    	'1.活动时间：即日起至<?php echo date('Y年m月d日H:i', strtotime($endTime));?>。',
                        '2.活动期间邀请好友完成注册并购彩即可获得抽奖机会。每日邀请次数不设上限，每日最多获得5次抽奖机会。',
                        '3.抽奖机会将于2018年2月28日23:59统一清零，请及时使用。',
                        '4.抽奖奖品红包与彩金有效期为14天，逾期未使用将被系统收回。',
                        '5.166元红包仅限新用户领取，同一用户最多领取一次，同一手机号、同一身份证号均视为同一用户。',
                        '6.活动过程中如有用户通过不正当手段领取奖品，166彩票网有权不予赠送、限制提现、冻结账户以及要求用户返还不正当得利。在法律允许范围内，166彩票网保留最终解释权。',
                        '7.关于活动的任何问题，请联系在线客服或拨打电话400-690-6790。'
                    ],
                    numTime: 0, // 抽奖次数
                    prizeList: [
                        <?php foreach ($prizeList as $key => $val):?>
                        '<?php echo '羡慕！' . (uname_cut($val['uname'], 2, 3)). '抽中' . mb_substr($val['mark'], (strpos($val['mark'], '减') > 0 ? strpos($val['mark'], '减') + 1 : 0));?>',
                        <?php endforeach;?>
                    ], //50条数据
                    toast: {
                        show: false,
                        text: ''
                    },
                    hk: {
                        list: ['choice1.png', 'choice2.png', 'choice3.png', 'choice4.png'],
                        curIdx: 0
                    },
                    makeAchance: false,
                    uid:<?php echo $uid; ?> + '' || 0,
                    baseUrl: '<?php echo $url; ?>',
                    controller: <?php echo $remark;?>,
                    success: false
                }
            },
            computed: {
            	beOut: function () {
					return this.controller === 1 ? true : false
                },
                prizeListSort: function () {
                    var len = this.prizeList.length;
                    for (var i = 0; i < len - 1; i++) {
                        var index = parseInt(Math.random() * (len - i));
                        var temp = this.prizeList[index];
                        this.prizeList[index] = this.prizeList[len - i - 1];
                        this.prizeList[len - i - 1] = temp;
                    }
                    return this.prizeList.concat(this.prizeList[0]);
                },
                hkImg: function () {
                    return '/caipiaoimg/static/images/active/heka/cards' + (this.hk.curIdx + 1) + '.png';
                },
                url: function () {
                    return this.baseUrl + '?hkid=' + this.hk.curIdx
                },
                canChoose: function () {
					return this.controller === 2 & !this.timeOut;
                }
            },
            watch: {
                curTime: function (val) {
                    if (val < this.tipsTime) {
                    	if(this.numTime > 0 && this.controller === 1){
                        	this.cSlogan = '<small>活动已结束，抽奖机会将于2月28日23:59清零</small>';
                        }else if(this.numTime <= 0 && this.controller === 1){
                        	this.cSlogan = '活动已结束';
                        }else{
                        	this.cSlogan = '';
                        }
                    } else if ((val < this.endTime && this.numTime > 0) || (val < this.endTime && this.numTime > 0 && this.controller === 1)) {
                    	this.timeOut = true;
                        this.cSlogan = '<small>活动已结束，抽奖机会将于2月28日23:59清零</small>';
                    } else {
                        this.timeOut = true;
                        this.cSlogan = '活动已结束';
                    }
                },
                numTime: function (val) {
                    if (val > 0) {
                    	this.tab.list.$set(1, '抽奖(<em>' + val + '</em>次)');
                    } else {
                        this.tab.list.$set(1, '抽奖');
                    }
                },
                havePrizeShow: function (val) {
                    if (this.gifs.curIdx > val) {
                        if (this.gifs.curIdx - val > 4) {
                            this.cycle = 4;
                            this.changeSpeed = true; 
                        } else {
                            this.cycle = 5;
                            this.changeSpeed = false;
                        }
                    } else {
                        if (val - this.gifs.curIdx > 4) {
                            this.cycle = 5;
                            this.changeSpeed = false;
                        } else {
                            this.cycle = 4;
                            this.changeSpeed = true; 
                        }
                    }
                },
                'gifs.curIdx': function (val) {
                    setTimeout(this.run, this.time)
                },
                'tab.curIdx': function (val) {
                    if (val === 0) {
                    	window.location.hash = 'yqhy'; 
                    } else {
                        window.location.hash = 'cj'; 
                    }
                }
            },
            created: function () {
                this.curTime = new Date('<?php echo date('Y/m/d');?>');
                this.numTime = <?php echo $chj['left_num'];?>;   
                window.onhashchange = this.changeUrl;
                this.changeUrl();       
            },
            methods: {
            	changeUrl: function(){
                	var hash = document.location.hash.split('#')[1];
                    if (hash === 'cj') {
                        this.tab.curIdx = 1;
                        this.showHk = false;
                    } else if (hash === 'showHk') {
                        this.showHk = true;
                    } else {
                        this.tab.curIdx = 0
                        this.showHk = false;
                    }
                },
            	goOnRun: function () {
                    this.result = '';
                    window.location.reload();
                },
            	goIndex: function () {
                    this.showHk = false;
                    this.success = false
                },
                getFilterUrl: function(hash) {
                    return document.location.protocol + '//' + document.location.host + document.location.pathname + document.location.search + '#' + hash;
                },
                goChooseHk: function () {
                	if (!this.isLogin()) return;
                	_hmt.push(['_trackEvent', 'iosxnhk', 'pick']);
                    this.showHk = !this.showHk;
                    var hash = "showHk";
                    window.location.hash = 'showHk'; 
                },
                run: function () {
                    var idx = this.gifs.list.length - 1;
                    // 大于最少圈数且
                    if (this.cycle > 4 && this.havePrizeShow === this.gifs.curIdx) {
                        this.cycle = 0;
                        this.time = 400;
                        this.havePrizeShow = -1;
                        this.startBtn = false;
                        this.result = this.gifs.list[this.gifs.curIdx];
                        return;
                    }
                    this.gifs.curIdx++;
                    if (this.gifs.curIdx > idx) {
                        this.cycle++
                        this.gifs.curIdx = 0;
                    }
                    if (this.havePrizeShow < 0) {
                        this.time -= 60;
                    } else {
                        if (this.changeSpeed) {
                            this.time += 20;
                        } else {
                            this.time += 100;
                        }
                    }
                    if (this.time < 60) {
                        this.time = 60;
                    } else if (this.time > 800) {
                        this.time = 800;
                    }
                },
                start: function () {
                    var _this = this;
                    this.result = '';
                    if (!this.isLogin()) return;
                    if (this.timeOut && this.numTime <= 0) {
                        this.toast.text = '亲，来晚啦，活动已经结束';
                        this.toast.show = true;
                        return false;
                    }

                    if(this.controller === 1 && this.numTime <= 0){
                    	this.toast.text = '亲，来晚啦，活动已经结束';
                        this.toast.show = true;
                        return false;
                    }

                    if (this.numTime <= 0) {
                        this.makeAchance = true;
                        return false;
                    }
                    this.startBtn = true;
                    this.$http.get('/app/activity/xnchj').then(function (res) {
                        res = JSON.parse(res.data);
                        if (!res.status) {
                            if (res.data === '002') {
                                _this.toast.text = res.msg;
                                _this.toast.show = true;
                            } else if (res.data === '001') {
                                _this.makeAchance = true;
                            }
                            _this.startBtn = false;
                        } else {
                            _this.run();
                            _this.numTime = res.data.left_num;

                            setTimeout(function () {
                                _this.havePrizeShow = _this.gifs.map.indexOf(res.data.rid);
                            }, 3000);
                        }
                    }).catch(function (err) {
                        _this.startBtn = false;
                        console.log(err)
                    })
                },
                choose: function (idx) {
                    this.tab.curIdx = idx;
                },
                chooseHk: function (idx) {
                    this.hk.curIdx = idx;
                },
                isLogin: function () {
                    if (this.uid) return true;
                    try {
                    	var backUrl = window.location.href;
                        window.webkit.messageHandlers.relogin.postMessage({url:backUrl});
                    } catch (err) {
                        console.log(err);
                    }
                },
                share: function () {
                    if (!this.isLogin()) return;
                    _hmt.push(['_trackEvent','iosxnhk', 'share']);
                    try {
                    	window.webkit.messageHandlers.snsShare.postMessage({url:this.url,title:"我给你做了张贺卡",content:"快打开看看~还有166元新年豪礼相送！>>",imageUrl:"<?php echo $imgurl?>"});
                    } catch (err) {
                        console.log(err);
                    }
                }
            }
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>