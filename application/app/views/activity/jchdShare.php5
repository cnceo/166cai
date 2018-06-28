<!DOCTYPE html>
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
    <title>世界杯竞猜</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/jcjc.min.css');?>">
</head>

<body ontouchstart>
    <div class="wrapper" :class="whois === 'isM' ? 'hasHeader' : ''" v-cloak>
        <header class="m-header" v-if="whois === 'isM'">
            <h1>世界杯竞猜</h1>
            <a href="/" class="hd-lnk-l">首页</a>
        </header>
        <div :class="['main', 'share', {'share2': popInfo.matachs.length <= 2}]" v-if="popInfo.uid">
            <div class="share-inner">
                <div class="real-share">
                    <h2><span class="img"><img :src="popInfo.headimgurl" alt=""></span>当期战绩</h2>
                    <div :class="['share-lv', 'type' + popInfo.show_status]">
                        <p v-if="popInfo.show_status === '4'">当期共有{{ popInfo.bouns_num }}名球迷预测成功</p>
                        <p v-else>我击败了<em>{{ popInfo.defeat_num }}</em>位 ({{ popInfo.defeat_ratio }}) 球迷</p>
                        <p v-if="popInfo.show_status === '1'"><strong>赢得了{{ popInfo.bouns }}元奖金</strong></p>
                        <p v-if="popInfo.show_status === '2'"><strong>还差{{ popInfo.lack_num }}场即可赢得{{ popInfo.bouns }}元奖金</strong></p>
                        <p v-if="popInfo.show_status === '3'"><strong>专业毒奶</strong></p>
                        <p v-if="popInfo.show_status === '4'"><strong>每人赢得{{ popInfo.bouns }}元奖金</strong></p>
                        <p><mark>当期奖池{{ popInfo.money }}元</mark></p>
                    </div>
                    <div :class="['mod-match-group', 'mod-match-group' + popInfo.matachs.length]">
                        <div class="mod-match" v-for="(i, it) in popInfo.matachs">
                            <div class="mod-match-hd">
                                <span class="name"><span class="img"><img :src="it.homeurl" alt=""></span>{{ it.home }}</span>
                                <span class="score">{{ it.score }}</span>
                                <span class="name"><span class="img"><img :src="it.awayurl" alt=""></span>{{ it.away }}</span>
                            </div>
                            <div class="mod-match-bd">
                                <span :class="[realCode[i].index === '3' ? 'result' : '', (it.code === '3' && popInfo.show_status !== '4') ? 'choose': '', realCode[i].class]">主胜</span>
                                <span :class="[realCode[i].index === '1' ? 'result' : '', (it.code === '1' && popInfo.show_status !== '4') ? 'choose': '', realCode[i].class]">平</span>
                                <span :class="[realCode[i].index === '0' ? 'result' : '', (it.code === '0' && popInfo.show_status !== '4') ? 'choose': '', realCode[i].class]">客胜</span>
                            </div>
                        </div>
                    </div>
                    <template v-if="!beShare">
                        <div class="share-ft fixed">
                            <div class="inner">
                                <a class="btn" href="javascript:" @click="continue">继续竞猜</a>
                                <button class="btn" v-if="whois !== 'isM'" @click="share">晒一下</button>
                            </div>
                        </div>
                        <div class="fixed-placehold"></div>
                    </template>   
                    <div class="share-ft" v-else>
                        <div class="img"></div>
                        <div class="text">
                            <p>来166彩票跟我玩赚世界杯</p>
                            <p><b>长按识别二维码，立即下载APP</b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <toast :show.sync="toast.show" type="text" :text="toast.text" @on-hide="toast.cb"></toast>   
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>"></script>
    <script>
        var imgMap = {
            '巴西': 'baxi',
            '德国': 'deguo',
            '西班牙': 'xibanya',
            '阿根廷': 'agenting',
            '法国': 'faguo',
            '比利时': 'bilishi',
            '葡萄牙': 'putaoya',
            '英格兰': 'yinggelan',
            '乌拉圭': 'wulagui',
            '哥伦比亚': 'gelunbiya',
            '克罗地亚': 'keluodiya',
            '俄罗斯': 'eluosi',
            '墨西哥': 'moxige',
            '波兰': 'bolan',
            '瑞士': 'ruishi',
            '丹麦': 'danmai',
            '塞尔维亚': 'saierweiya',
            '瑞典': 'ruidian',
            '秘鲁': 'milu',
            '日本': 'riben',
            '尼日利亚': 'niriliya',
            '塞内加尔': 'saineijiaer',
            '埃及': 'aiji',
            '冰岛': 'bingdao',
            '突尼斯': 'tunisi',
            '澳大利亚': 'aodaliya',
            '摩洛哥': 'moluoge',
            '韩国': 'hanguo',
            '伊朗': 'yilang',
            '哥斯达': 'gesidalijia',
            '巴拿马': 'banama',
            '沙特': 'shate',
        },
        timer = null
        // 创建Vue实例 所有操作在这
        var active = new Vue({
            el: '.wrapper',
            data: function() {
                return {
                    popInfo: {},
                    toast: {
                        show: false,
                        text: '',
                        cb: function () {}
                    },
                    beShare: false
                }
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
                orderBonusApi: function () {
                    if (this.whois === 'isAndroid') {
                        return '/app/api/v2/jchd/orderBouns'
                    } else if (this.whois === 'isIOS') {
                        return '/app/api/v2/jchd/orderBouns'
                    } else {
                        return '/jchd/orderBouns'
                    }
                },
                activeUrl: function () {
                    if (this.whois === 'isAndroid') {
                        return '//www.ka5188.com/app/activity/jchd'
                    } else if (this.whois === 'isIOS') {
                        return '//www.ka5188.com/ios/activity/jchd'
                    } else {
                        return '//8.166cai.cn/activity/jchd'
                    }
                },
                realCode: function () {
                    return this.popInfo.matachs.map(function (it) {
                        var code = this.score2code(it.score)
                        if (code === it.code) {
                            return {
                                index: code,
                                class: 'true'
                            }
                        } else {
                            return {
                                index: code,
                                class: 'false'
                            }
                        }
                    }.bind(this))
                }
            },
            watch: {
                // popInfo: function (val) {
                //     if (val && val.matachs && val.matachs.length) {
                //         val.matachs = val.matachs.map(function (it) {
                //             it.homeurl = '//888.166cai.cn/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.home] + '.png'
                //             it.awayurl = '//888.166cai.cn/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.away] + '.png'
                //             return it
                //         })
                //     }
                // }
            },
            created: function () {
                this.showPop(this.getQueryString('orderId'))
            },
            methods: {
                getQueryString: function (name) {
                    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)")
                    var r = window.location.search.substr(1).match(reg)
                    if (r != null) return decodeURI(r[2])
                    return null
                },
                lotGet: function (fn) {
                    clearTimeout(window.timer)
                    window.timer = setTimeout(function () {
                        fn()
                    }, 500)
                },
                showPop: function (orderId) {
                    this.$http.post(this.orderBonusApi, {
                        'orderId': orderId,
                    }, {
                        emulateJSON: true
                    }).then(function (res) {
                        var res = JSON.parse(res.data)
                        var data = res.data.detail
                        this.popInfo = data
                        this.popInfo.matachs = data.matachs.map(function (it) {
                            var code = this.score2code(it.score)
                            if (it.code === '3') {
                                it.codeCn = it.home + '胜'
                            } else if (it.code === '0') {
                                it.codeCn = it.away + '胜'
                            } else {
                                it.codeCn = '平'
                            }
                            it.homeurl = '//888.166cai.cn/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.home] + '.png'
                            it.awayurl = '//888.166cai.cn/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.away] + '.png'
                            return it
                        }.bind(this))
                    }).catch(function () {
                        this.toast.show = true
                        this.toast.text = '获取失败，请刷新重试'
                    })
                },
                score2code: function (score) {
                    var scoreArr = score.split(':'),
                        code = null
                    if (scoreArr[0] - scoreArr[1] > 0) {
                        code = '3'
                    } else if (scoreArr[0] - scoreArr[1] === 0) {
                        code = '1'
                    } else {
                        code = '0'
                    }
                    return code
                },
                continue: function () {
                    // 返回竞猜页面
                    window.location.href = this.activeUrl
                },
                share: function () {
                    this.beShare = true
                    setTimeout(function () {
                        try {
                            if (this.whois === 'isAndroid') {
                                window.android.shareWebViewCapture()
                            } else if (this.whois === 'isIOS') {
                                window.webkit.messageHandlers.shareWebViewCapture.postMessage({})
                            } else {
                                this.toast.show = true
                                this.toast.text = '在166彩票APP内打开才能分享'
                            }
                        } catch (err) {
                            this.toast.show = true
                            this.toast.text = '赞不支持分享'
                        }
                    }.bind(this), 10)
                    
                    setTimeout(function () {
                        this.beShare = false
                    }.bind(this), 3000)
                }
            }
        })
    </script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js');?>"></script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>

</html>