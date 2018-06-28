<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="166彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>竞彩不中包赔</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/jcbzqp.min.css')?>">
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js'); ?>"></script>
</head>
<body ontouchstart="">
    <div class="wrapper jcbzqp" id="jcbzqp" v-cloak>
        <div class="jcbzqp-hd">
            <div class="timebox">
                <small>{{ info.title }}</small>
                <p class="countDown"><time>{{ time }}</time></p>
            </div>
        </div>
        <div class="jcbzqp-bd" :class="{'dg': info.match.length === 1}">
            <template v-if="info.lid === '43'">
                <div class="goods jclq" v-for="item in info.match">
                    <div class="items">
                        <div class="item" :class="item.res === '0' ? 'cur' : ''">
                            <div class="item-hd">
                                <img :src="item.homelogo" alt="">
                            </div>
                            <div class="item-bd">
                                <span>{{ item.awary }}  胜</span>
                                <s>{{ item.sp.split(',')[1] }}</s>
                            </div>
                        </div>
                        <div class="item">
                            <div class="item-hd">
                                <span>{{ item.name }}</span>
                                <s>{{ item.kstime }}</s>
                            </div>
                        </div>
                        <div class="item" :class="item.res === '3' ? 'cur': ''">
                            <div class="item-hd">
                                <img :src="item.awaylogo" alt="">
                            </div>
                            <div class="item-bd">
                                <span>{{ item.home }}<i v-if="item.playType === 'rfsf'">({{  item.let > 0 ? '+' + item.let : item.let }})</i>  胜</span>
                                <s>{{ item.sp.split(',')[0] }}</s>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="goods" v-for="item in info.match">
                    <div class="items">
                        <div class="item" :class="item.res === '3' ? 'cur' : ''">
                            <div class="item-hd">
                                <img :src="item.homelogo" alt="">
                            </div>
                            <div class="item-bd">
                                <span>{{ item.home }}<i v-if="item.playType === 'rqspf'">({{ item.let > 0 ? '+' + item.let : item.let }})</i>  胜</span>
                                <s>{{ item.sp.split(',')[0] }}</s>
                            </div>
                        </div>
                        <div class="item" :class="item.res === '1' ? 'cur': ''">
                            <div class="item-hd">
                                <span>{{ item.name }}</span>
                                <s>{{ item.kstime }}</s>
                            </div>
                            <div class="item-bd">
                                平
                                <s>{{ item.sp.split(',')[1] }}</s>
                            </div>
                        </div>
                        <div class="item" :class="item.res === '0' ? 'cur': ''">
                            <div class="item-hd">
                                <img :src="item.awaylogo" alt="">
                            </div>
                            <div class="item-bd">
                                <span>{{ item.awary }}  胜</span>
                                <s>{{ item.sp.split(',')[2] }}</s>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div class="ticket">
                <div class="ticket-inner">
                    <ul>
                        <li>玩法：<span>{{ info.playName }}</span></li>
                        <li>过关方式：<span>{{ info.ggName }}</span></li>
                        <li>投注金额：<span>{{ info.money }}元</span></li>
                        <li>预计奖金：<span><em>{{ fMoney }}</em>元</span></li>
                    </ul>
                    <button type="button" class="btn" :disabled="!seTime" @click="postOrder">{{ btnMsg }}</button>
                </div>
            </div>
        </div>
        <div class="plus-rule" :class="{'plus-rule-show': rule}">
            <ol class="rule-overflow-y">
                <li>1、活动时间：{{info.time}}。</li>
                <li>2、参与条件：仅限166彩票网已注册且实名认证的用户；每个身份证下面的所有账号仅可参与一次不中包赔活动。</li>
                <li>3、参与方式：仅限用户在166彩票移动端参与活动页面的指定方案，当您在活动页购买的方案支付成功，即代表您参与活动成功。</li>
                <li>4、若参与活动的方案未中奖，166彩票将在开奖后的48小时内返还您{{info.slogan}}。</li>
                <li>5、在法律允许范围内，166彩票网保留最终解释权，如有问题请联系在线客服或拨打电话400-690-6760。</li>
            </ol>
            <div class="rule-arrow" @click="showRule">规则<span>↓</span></div>
        </div>
        <div class="rule-bg" @click="showRule"></div>
        <toast :show.sync="toast.show" type="text" :text="toast.text"></toast>
    </div>  

    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js');?>"></script>
    <script>
        var plusAwards = new Vue({
            el: '#jcbzqp',
            data: {
                pay: '/ios/event/payJcbzbp',
                info: <?php echo $info; ?>,
                cd: 0,
                seTime: false,
                rule: false,
                toast: {
                    show: false,
                    text: ''
                }
            },
            created: function () {
                this.info.startTime = this.dateFormat(this.info.startTime)
                this.info.endTime = this.dateFormat(this.info.endTime)
                this.info.current = this.dateFormat(this.info.current)
            },
            computed: {
                fMoney: function () {
                    var result = 1;
                    var _this = this;
                    this.info.match.forEach(function (item, idx) {
                        var idx;
                        if (item.res == '3') {
                            idx = 0;
                        } else if (item.res == '0') {
                            idx = _this.info.match[idx].sp.split(',').length - 1;
                        } else {
                            idx = 1;
                        }
                        result *= item.sp.split(',')[idx]
                    })
                    return (result*this.info.money).toFixed(2)
                },
                time: function () {
                    if (this.cd <= 0) {
                        return '00:00:00';
                    }
                    var h = 0,
                        m = 0,
                        s = 0;
                    h = parseInt(this.cd / (1000 * 60 * 60), 10);
                    m = parseInt((this.cd - (h * 1000 * 60 * 60)) / (1000 * 60), 10);
                    s = (this.cd - (h * 1000 * 60 * 60) - (m * 1000 * 60)) / 1000;
                    if ( h > 0 ) {
                        return h + ':' + this.formart(m) + ':' + this.formart(s);
                    } else {
                        return this.formart(m) + ':' + this.formart(s);
                    }
                    
                },
                dateShow: function () {
                    var arr = this.info.startTime.split(' ');
                    return arr[0].split('/').slice(1).join('-') + ' ' + arr[1].split(':').slice(0, 2).join(':');
                },
                btnMsg: function () {
                    if (this.cd <= 0) {
                        this.seTime = false;
                        return '本期活动已结束';
                    } else if (+new Date(this.info.endTime) - this.cd > +new Date(this.info.startTime)) {
                        this.seTime = true;
                        return '下单' + this.info.money + '元，不中全赔';
                    } else {
                        this.seTime = false;
                        return '活动将于' + this.dateShow + '开始';
                    }
                }
            },
            ready: function () {
                this.countDown();
            },
            methods: {
                dateFormat: function (data) {
                    return data.split('-').join('/')
                },
                showRule: function () {
                    this.rule = !this.rule;
                },
                formart: function (n) {
                    if (n < 10) {
                        n = '0' + n;
                    }
                    return n;
                },
                countDown: function () {
                    var _this = this;
                    var interval = 1000,
                        startTime = new Date().getTime(),
                        count = 0;
                    this.cd = new Date(this.info.endTime).getTime() - new Date(this.info.current).getTime(); 
                    if( this.cd >= 0){
                        var timeCounter = setTimeout(countDownStart,interval);                  
                    }
                    
                    function countDownStart() {
                        count++;
                        var offset = new Date().getTime() - (startTime + count * interval);
                        var nextTime = interval - offset;
                        if (nextTime < 0) { nextTime = 0 };
                        _this.cd -= interval;
                        if(_this.cd < 0) {
                            clearTimeout(timeCounter);
                        } else {
                            timeCounter = setTimeout(countDownStart, nextTime);
                        }
                    }
                },
                toggle: function(i){
                    if (this.items[0].class[i]) {
                        this.total -= Number(this.items[0].odds[i])
                        this.items[0].class.$set(i, false)
                    } else {
                        this.total += Number(this.items[0].odds[i])
                        this.items[0].class.$set(i, true)
                    }
                    this.total = Math.round(this.total*100)/100
                },
                choose: function(i){
                    this.mul.class = i;
                    this.mulNum = this.mul.num[i]
                },
                replace: function(i){               
                    var eleData = this.items.splice(i+1, 1);
                    this.items.unshift(eleData[0])
                    this.total = 0;
                    this.mulNum = 1;
                    this.popShow = false;
                },
                postOrder: function () {
                    var data = {
                        activityId: this.info.id,
                    }
                    this.$http.post(this.pay, data, {headers: { "X-Requested-With": "XMLHttpRequest"}, emulateJSON: true}).then(function (response) {
                        var response = JSON.parse(response.data)
                        if(response.status === '200'){
                            window.location.href = response.data
                        }else if(response.status === '300'){
                            window.webkit.messageHandlers.relogin.postMessage({url:window.location.href});
                        }else if(response.status === '500'){
                            window.webkit.messageHandlers.goBind.postMessage({url:window.location.href});
                        }else{
                            this.toast.show = true
                            this.toast.text = response.msg
                        }
                    }), function (error) {
                        this.toast.show = true
                        this.toast.text = '网络异常，请稍后再试'
                    }
                },
            }
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>
