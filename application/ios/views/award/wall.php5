<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta name="author" content="weblol">
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<meta name="apple-mobile-web-app-title" content="166彩票">
<meta content="telephone=no" name="format-detection" />
<meta content="email=no" name="format-detection" />
<title>历史大奖墙</title>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/bonus-rank.min.css');?>">
<?php $this->load->view('comm/baidu'); ?>
</head>
<body>
    <div class="wrapper bonus-rank" id="bonus" v-cloak>
        <div class="banner">
            <strong><?php echo $lottery['award'];?></strong>
        </div>
        <div class="bonus-news-list">
            <ul>
                <li v-for="item in list">
                    <a href="{{ item.url }}" class="bonus-news"  :class="{'top': item.top === '1'}">
                        <div class="bonus-num">
                            <strong>{{ item.bonus }}</strong>
                            <span>{{ item.lot }}</span>
                        </div>
                        <p class="bonus-txt">{{ item.text }}</p>
                    </a>
                </li>
            </ul>
        </div>
        <div class="q-bet-placeholder"></div>
        <qbet order-api="/ios/order/createOrder" lid="<?php echo $lottery['lid']; ?>" issue="<?php echo $lottery['issue']; ?>" end-time="<?php echo $lottery['endTime']; ?>" title="<?php echo $lottery['title']; ?>" slogan="<?php echo $lottery['slogan']; ?>" @on-bet="betCb" @on-handle="doSuccess" @on-error="doError"></qbet>
        <toast :show.sync="toast.show" type="text" :text="toast.text"></toast>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js'); ?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js'); ?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>"></script>
    <script>
        Vue.component('qbet', {
            template: '<div class="q-bet">'
                + '<div class="q-bet-hd">'
                    + '<div class="title">{{ title }}<em>{{ slogan }}</em></div>'
                    + '<span class="change" @click="change" v-el:change>换一注</span>'
                + '</div>'
                + '<div class="q-bet-bd">'
                    + '<div class="ball-area" :class="qBet[lid].name">'
                        + '<span v-for="item in numArr" track-by="$index">{{ item }}</span>'
                    + '</div>'
                    + '<a href="javascript:" class="btn btn-confirm" v-html="btnText" @click="onBet"></a>'
                + '</div>'
            + '</div>',
            props: {
                title: 'String',
                slogan: 'String',
                btnText: {
                    type: 'String',
                    default: '立即投注'
                },
                orderApi: '',
                lid: 'String',
                issue: 'String',
                endTime: 'String'
            },
            data: function () {
                return {
                    numArr: [],
                    redNum: [],
                    blueNum: [],
                    qBet: {
                        '51': {
                            name: 'ssq',
                            format: [6, 1],
                            num: [33, 16]
                        },
                        '23529': {
                            name: 'dlt',
                            format: [5, 2],
                            num: [35, 12]
                        }
                    }
                }
            },
            created: function () {
                this.run()
            },
            methods: {
                randomBall: function (n, m) {
                    return parseInt(Math.random() * (m -n + 1) + n, 10)
                },
                run: function(){
                    var redNum,
                        redNumArr = [],
                        blueNum,
                        blueNumArr = [];
                    for(var i = 0; i < 100; i++) {
                        redNum = this.randomBall(1, this.qBet[this.lid].num[0]);
                        redNum = redNum < 10 ? '0' + redNum : redNum + '';
                        if (redNumArr.indexOf(redNum) >= 0) continue;
                        redNumArr.push(redNum);
                        if (redNumArr.length === this.qBet[this.lid].format[0]) break;
                    }
                    this.redNum = redNumArr.sort();
                    for(var i = 0; i < 100; i++) {
                        blueNum = this.randomBall(1, this.qBet[this.lid].num[1]);
                        blueNum = blueNum < 10 ? '0' + blueNum : blueNum + '';
                        if (blueNumArr.indexOf(blueNum) >= 0) continue;
                        blueNumArr.push(blueNum);
                        if (blueNumArr.length === this.qBet[this.lid].format[1]) break;
                    }
                    this.blueNum = blueNumArr.sort();
                    this.numArr = this.redNum.concat(this.blueNum);
                },
                change: function () {
                    this.run()
                    var el = this.$els.change;
                    el.className += ' active';
                    setTimeout(function () {
                        el.className = 'change'
                    }, 800)
                },
                onBet: function () {
                    var data = {
                        codes: this.redNum.join(',') + '|' + this.blueNum.join(',') + ':1:1',
                        lid: this.lid,
                        money: 2,
                        multi: 1,
                        issue: this.issue,
                        playType: 0,
                        betTnum: 1,
                        isChase: 0,
                        orderType: 0,
                        endTime: this.endTime
                    }
                    this.$http.post(this.orderApi, data, {headers: { "X-Requested-With": "XMLHttpRequest"}, emulateJSON: true})
                    .then(function (response) {
                        this.$emit('on-handle', response);
                    }).catch(function (e) {
                        this.$emit('on-error', '网络异常，请稍后再试');
                    })
                }
            }
        })
        var sMessge = new Vue({
            el: '#bonus',
            data: {
                list: <?php echo json_encode($info)?>,
                current: {
                    issue: "<?php echo $lottery['issue']; ?>",
                    endTime: "<?php echo $lottery['endTime']; ?>",
                    lid: 51,
                },
                numArr: [],
                toast: {
                    show: false,
                    text: ''
                }
            },
            methods: {
                doSuccess: function (response) {
                    var response = JSON.parse(response.data)
                    if(response.status === '200'){
                        window.location.href = response.data
                    }else if(response.status === '300'){
                        // 登录
                        var backUrl = window.location.href;
                        window.webkit.messageHandlers.relogin.postMessage({url:backUrl});
                    }else if(response.status === '500'){
                        // 实名
                        var backUrl = window.location.href;
                        window.webkit.messageHandlers.goBind.postMessage({url:backUrl});
                    }else{
                        this.doError(response.msg);
                    }
                },
                doError: function (msg) {
                    this.toast.show = true
                    this.toast.text = msg
                }
            }
        })  
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>