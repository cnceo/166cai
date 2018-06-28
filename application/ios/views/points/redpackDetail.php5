<!doctype html> 
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
    <title>礼包详情</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/jifen.min.css');?>">
</head>
<body>
    <div class="wrapper p-jifen-pg" v-cloak>
        <div class="package-detail-hd">
            <div class="red-packets-box">
                <div class="red-packets">
                    <b>¥{{ money }}</b><small>通用购彩红包</small>
                </div>
            </div>
            <div class="red-packets-text">
                <h1>{{ money }}元彩金红包</h1>
                <div v-if="parseInt(price, 10) < parseInt(desc[money].price,10)">
                    <del>{{ desc[money].price }}积分</del><span><em>{{ price }}</em>积分</span>
                </div>
                <div v-else>
                    <span><em>{{ price }}</em>积分</span>
                </div>
            </div>
            
        </div>
        
        <div class="package-detail-bd jf-desc-box">
            <div class="jf-desc-item">
                <h2 class="title">商品介绍</h2>
                <ul>
                    <li v-for="item in desc[money].text">{{ item }}</li>
                </ul>
            </div>

            <div class="jf-desc-item">
                <h2 class="title">温馨提示</h2>
                <ol>
                    <li>1、积分兑换的是彩金红包，兑换成功后，红包会派发至红包页，客户端点击“我-红包”即可兑换等额彩金；</li>
                    <li>2、兑换彩金仅支持购彩消费，不支持提现；</li>
                    <li>3、礼包每天00：00更新，每人每天有3次兑换机会。</li>
                </ol>
            </div>
        </div>
        <button class="btn" v-if="out > 0" @click="exchange">立即兑换</button>
        <button class="btn" v-else disabled>今日已兑完</button>
        <toast :show.sync="toast.show" type="text" :text="toast.text" @on-hide="toast.cb"></toast>
        <confirm :show.sync="confirm.show" @on-confirm="confirm.cb" :confirm-text="confirm.cBtn">{{confirm.text}}</confirm>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js');?>" type="text/javascript" ></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js');?>" type="text/javascript" ></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>" type="text/javascript" ></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js');?>" type="text/javascript" ></script>
    <script>
        new Vue({
            el: '.wrapper',
            data: function () {
                return {
                    desc: {
                        '1': {
                            'price': '<?php echo $redpoints[0]['price'];?>',
                            'text': ['青铜、白银彩民<?php echo $redpoints[0]['lv2'];?>分可兑换', '黄金、铂金彩民<?php echo $redpoints[0]['lv4'];?>分可兑换', '钻石彩民<?php echo $redpoints[0]['lv6'];?>分可兑换']
                        },
                        '2': {
                            'price': '<?php echo $redpoints[1]['price'];?>',
                            'text': ['青铜、白银彩民<?php echo $redpoints[1]['lv2'];?>分可兑换', '黄金、铂金彩民<?php echo $redpoints[1]['lv4'];?>分可兑换', '钻石彩民<?php echo $redpoints[1]['lv6'];?>分可兑换'],
                        },
                        '5': {
                            'price': '<?php echo $redpoints[2]['price'];?>',
                            'text': ['青铜、白银彩民<?php echo $redpoints[2]['lv2'];?>分可兑换', '黄金、铂金彩民<?php echo $redpoints[2]['lv4'];?>分可兑换', '钻石彩民<?php echo $redpoints[2]['lv6'];?>分可兑换']
                        },
                        '10': {
                            'price': '<?php echo $redpoints[3]['price'];?>',
                            'text': ['青铜、白银彩民<?php echo $redpoints[3]['lv2'];?>分可兑换', '黄金、铂金彩民<?php echo $redpoints[3]['lv4'];?>分可兑换', '钻石彩民<?php echo $redpoints[3]['lv6'];?>分可兑换']
                        },
                        '100': {
                            'price': '<?php echo $redpoints[4]['price'];?>',
                            'text': ['青铜、白银彩民<?php echo $redpoints[4]['lv2'];?>分可兑换', '黄金、铂金彩民<?php echo $redpoints[4]['lv4'];?>分可兑换', '钻石彩民<?php echo $redpoints[4]['lv6'];?>分可兑换']
                        },
                        '500': {
                            'price': '<?php echo $redpoints[5]['price'];?>',
                            'text': ['青铜、白银彩民<?php echo $redpoints[5]['lv2'];?>分可兑换', '黄金、铂金彩民<?php echo $redpoints[5]['lv4'];?>分可兑换', '钻石彩民<?php echo $redpoints[5]['lv6'];?>分可兑换']
                        },
                        '1000': {
                            'price': '<?php echo $redpoints[6]['price'];?>',
                            'text': ['黄金、铂金彩民<?php echo $redpoints[6]['lv4'];?>分可兑换', '钻石彩民<?php echo $redpoints[6]['lv6'];?>分可兑换']
                        },
                        '5000': {
                            'price': '<?php echo $redpoints[7]['price'];?>',
                            'text': ['黄金、铂金彩民<?php echo $redpoints[7]['lv4'];?>分可兑换', '钻石彩民<?php echo $redpoints[7]['lv6'];?>分可兑换']
                        }
                    },
                    toast: {
                        show: false,
                        text: '',
                        cb: function () {}
                    },
                    confirm: {
                        show: false,
                        text: '',
                        cBtn: '',
                        cb: function () {}
                    }
                }
            },
            computed: {
                pramas: function () {
                    return {
                        rid: <?php echo $rid; ?>
                    }
                }
            },
            created: function () {
                var querySelecter = window.location.search.substr(1).split('&')
                querySelecter.forEach(function (it) {
                    var arr = it.split('=')
                    Vue.set(this, arr[0], arr[1])
                }.bind(this))
                this.$http.post('/ios/points/getRedPackInfo', this.pramas, {
                    emulateJSON: true
                }).then(function (res) {
                    var res = JSON.parse(res.data)
                    if (res.status === '200') {
                        for(var k in res.data) {
                            if (this[k]) {
                                this[k] = res.data[k]
                            } else {
                                Vue.set(this, k, res.data[k])
                            }
                        }
                    }else if (res.status === '300') {
                            android.relogin(location.href);
                            return;
                    } else {
                        this.toast.show = true;           
                        this.toast.text = res.msg;
                    }
                }.bind(this)).catch(function (err) {
                    console.log(err)
                })
            },
            methods: {
                exchange: function () {
                    this.confirm.show = true
                    this.confirm.text = '兑换' + this.money + '元彩金红包？'
                    this.confirm.cBtn = '兑换'
                    this.confirm.cb = this.reqExchange;
                },
                reqExchange: function () {
                    this.$http.post('/ios/points/exchangeRedPack', this.pramas,{
                    emulateJSON: true
                }).then(function (res) {
                        var res = JSON.parse(res.data)
                        if (res.status === '200') {
                            this.confirm.show = true;
                            this.confirm.cBtn = '查看红包';
                            this.confirm.text = '恭喜您，兑换' + this.money + '元红包成功';
                            this.confirm.cb = function () {
                                window.location.href="/ios/redpack/index/<?php echo $token; ?>";
                            }
                        }else if (res.status === '300') {
                            android.relogin(location.href);
                            return;
                        } else if (res.status === '0') {
                            this.toast.show = true;           
                            this.toast.text = res.msg;
                            this.toast.cb = function () {
                                window.location.reload()
                            }
                        }
                    }.bind(this)).catch(function (err) {
                        console.log(err)
                    })
                }
            }
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>