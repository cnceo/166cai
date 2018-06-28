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
    <style>
        canvas, canvas ~ img {
            position: absolute;
            left: 0;
            top: 0;
            z-index: 10;
            width: 100%;
        }
    </style>
</head>

<body ontouchstart>
    <div class="wrapper wx" v-cloak>
        <div class="notice">长按图片分享给微信好友</div>
        <div class="main share" :class="{'share2': popInfo.matachs.length <= 2}">
            <div class="share-inner">
                <div class="real-share">
                    <h2>我的预测</h2>
                    <div :class="['share-lv', 'type1']">
                        <p>和我免费竞猜世界杯瓜分奖金</p>
                        <p><strong>本期奖池{{ popInfo.money }}元</strong></p>
                    </div>
                    <div :class="['mod-match-group', 'mod-match-group' + popInfo.matachs.length]">
                        <div class="mod-match" v-for="(i, it) in popInfo.matachs">
                            <div class="mod-match-hd">
                                <span class="name"><span class="img"><img :src="it.homeurl" alt=""></span>{{ it.home }}</span>
                                <span class="vs">VS</span>
                                <span class="name"><span class="img"><img :src="it.awayurl" alt=""></span>{{ it.away }}</span>
                            </div>
                            <div class="mod-match-bd" v-if="it.code">
                                <span :class="it.code === '3'? 'choose': ''">主胜</span>
                                <span :class="it.code === '1'? 'choose': ''">平</span>
                                <span :class="it.code === '0'? 'choose': ''">客胜</span>
                            </div>
                        </div>
                    </div> 
                    <div class="share-ft">
                        <div class="img"></div>
                        <div class="text">
                            <p>长按二维码参与竞猜活动</p>
                            <p><b>壕送百万助您玩赚世界杯</b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>"></script>
    <script src="https://cdn.bootcss.com/html2canvas/0.5.0-beta4/html2canvas.js"></script>
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
                    popInfo: {}
                }
            },
            computed: {
                isWx: function () {
                    return navigator.userAgent.toLowerCase().indexOf('micromessenger') >= 0
                },
                getIndexApi: function () {
                    return '/jchd/index'
                }
            },
            ready: function () {
                this.getIndex()
            },
            methods: {
                html2img: function () {
                    if (this.isWx) {
                        this.$nextTick(function () {
                            var shareContent = document.querySelector('.main')
                            var width = shareContent.offsetWidth
                            var height = shareContent.offsetHeight
                            var canvas = document.createElement("canvas")
                            var scale = 2

                            canvas.width = width * scale
                            canvas.height = height * scale
                            canvas.getContext("2d").scale(scale, scale)
                            html2canvas(shareContent, {
                                scale: scale,
                                canvas: canvas,
                                width: width,
                                height: height
                            }).then(function(canvas) {
                                document.body.appendChild(canvas)
                                var img = document.createElement('img')
                                img.src = document.querySelector('canvas').toDataURL()
                                document.body.appendChild(img)
                            });
                        })
                    }
                },
                lotGet: function (fn) {
                    clearTimeout(window.timer)
                    window.timer = setTimeout(function () {
                        fn()
                    }, 500)
                },
                getIndex: function () {
                    this.$http.get(this.getIndexApi).then(function (res) {
                        var res = JSON.parse(res.data)
                        if (res.status === '200') {
                            this.popInfo = res.data.currentMatch
                            if (this.popInfo.matachs && this.popInfo.matachs.length) {
                                this.popInfo.matachs = this.popInfo.matachs.map(function (it) {
                                    it.homeurl = '/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.home] + '.png'
                                    it.awayurl = '/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.away] + '.png'
                                    return it
                                })
                            }
                            this.html2img()
                        }
                    }).catch(function (err) {
                        this.lotGet(this.getIndex)
                    })
                }
            }
        })
    </script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js');?>"></script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>

</html>