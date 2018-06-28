<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="author" content="weblol">
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width,user-scalable=no">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="166彩">
        <meta content="telephone=no" name="format-detection">
        <meta content="email=no" name="format-detection">
        <meta name="screen-orientation" content="portrait">
        <meta name="x5-orientation" content="portrait">
        <meta name="full-screen" content="yes">
        <meta name="x5-fullscreen" content="true">
        <meta name="browsermode" content="application">
        <meta name="x5-page-mode" content="app">
        <title>邀请好友</title>
        <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/sjbyqhy.min.css') ?>">
<!--        <style>
            [v-cloak] {display: none;}
        </style>-->
        <?php $this->load->view('comm/baidu'); ?>
    </head>
    <body ontouchstart="">
        <div class="wrap" id="app" v-cloak>
            <div class="wrap-content">
                <div class="rp-box">
                    <h1>好友购彩您立得10元红包</h1>
                    <a href="javascript:" @click="shareFn" class="btn-click"><span>邀请好友</span></a>
                    <a href="javascript:" class="f2f" @click="openQrCode">面对面邀请</a>
                </div>

                <div class="share-step"></div>
                <div class="notice">
                    <p>已成功邀请<em>{{ peopleNum }}</em>人， 获得彩金<em>{{ money }}</em>元</p>
                </div>

                <div class="mod-bt" v-if="peopleNum > 0">
                    <div class="title">近期邀请记录</div>
                    <table>
                        <thead>
                            <tr>
                                <th width="50%">用户名</th>
                                <th width="50%">邀请时间</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in joinList">
                                <td>{{ item.uname }}</td>
                                <td>{{ item.created }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="qrCode" v-if="qrCode">
                    <div class="qrCode-inner">
                        <div class="img">
                            <img src="/app/wallet/qrCode/<?php echo urlencode(base64_encode($url)); ?>" alt="">
                        </div>
                        <p>好友扫二维码图案，即可接受邀请</p>
                        <span class="closeQrCode" @click="qrCode = false"></span>
                    </div>
                </div>
                <div class="rule" :class="{'active': rule}">
                     <div class="rule-inner">
                        <ol>
                            <li v-for="it in ruleList" :track-by="$index">{{ it }}</li>
                        </ol>
                        <span class="handle" @click="fnRule"><em>规则<i></i></em></span>
                    </div>
                    <div class="mask" @click="fnRule" v-if="rule"></div>
                </div>
            </div>
            <toast :show.sync="toast.show" type="text" :text="toast.text" @on-hide="toast.cb"></toast>
        </div>
        <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js'); ?>"></script>
        <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js'); ?>"></script>
        <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js'); ?>"></script>
        <script>

var newUser = new Vue({
    el: '#app',
    data: function () {
        return {
            baseMoney: 10,
            peopleNum: <?php echo $count; ?>,
            joinList: <?php echo json_encode($users); ?>,
            ruleList: [
                '1.活动时间：即日起至<?php echo date('Y年m月d日', strtotime($time['endTime'])) ?>。',
                '2.10元红包由2个满10减5红包组成，首个满减红包派发后即刻生效，第二个红包于次周生效，红包有效期为7天，逾期未使用的红包将被系统收回。',
                '3.活动过程中如用户通过不正当手段领取彩金，166彩票网有权不予赠送、限制提现、冻结账户以及要求用户返还不正当得利。在法律允许范围内，166彩票网保留最终解释权。',
                '4.关于活动的任何问题，请联系在线客服或拨打电话400-690-6760。'
            ],
            qrCode: false,
            toast: {
                show: false,
                text: '',
                cb: null
            },
            rule: false,
            uid: <?php echo $uid; ?> + '' || 0,
            showbind: <?php echo $showBind; ?>,
            url: '<?php echo $url; ?>'
        }
    },
    created: function () {
//        setTimeout(function () {
//            this.peopleNum = 3;
//        }.bind(this), 2000)
    },
    computed: {
        whois: function () {
            var ua = navigator.userAgent.toLowerCase()
            if (ua.indexOf('2345caipiao/android') >= 0) {
                return 'isAndroid'
            } else if (ua.indexOf('166cai/ios') >= 0) {
                return 'isIOS'
            } else {
                return 'isM'
            }
        },
        money: function () {
            return this.peopleNum * this.baseMoney;
        }
    },
    methods: {
        shareFn: function () {
            _hmt.push(['_trackEvent', 'apprequest', 'appinvite']);
            if (!this.uid) {
                android.relogin(location.href);
                return;
            }
            if (this.showbind) {
                android.auth(location.href);
                return;
            }
            android.shareSocialMedia("世界杯来了！推荐个中奖福地给你", "<?php echo $self['uname'] ?>邀你加入166彩票，一起玩赚世界杯！猛戳领取166元新人豪礼>>", "<?php echo $imgurl ?>?0614", this.url);
        },
        openQrCode: function () {
            if (!this.uid) {
                android.relogin(location.href);
                return;
            }
            if (this.showbind) {
                android.auth(location.href);
                return;
            }
            this.qrCode = true;
        },
        fnRule: function () {
            this.rule = !this.rule;
        }
    },
})


if ('addEventListener' in document) {
    document.addEventListener('DOMContentLoaded', function () {
        FastClick.attach(document.body);
    }, false);
}
        </script>
        <?php $this->load->view('mobileview/common/tongji'); ?>
    </body>
</html>
