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
    <title>2018世界杯冠军彩</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/gjc.min.css');?>">
</head>

<body ontouchstart>
<div class="wrapper" :class="whois === 'isM' ? 'hasHeader' : ''" v-cloak>
        <header class="m-header" v-if="whois === 'isM'">
            <h1>2018世界杯</h1>
            <a href="/" class="hd-lnk-l">首页</a>
        </header>
        <div class="ui-tab">
            <ul class="ui-tab-nav">
                <li :class="{'current': tab.curIdx === i}" v-for="(i, it) in tab.text" @click="changeTab(i)">{{ it }}</li>
            </ul>
            <div class="ui-tab-bd" v-if="tab.curIdx === 0">
                <div class="mod-item thead">
                    <span class="idx">编号</span>
                    <span class="name">球队</span>
                    <span class="sp">赔率</span>
                    <span class="status">销售状态</span>
                </div>
                <ul>
                    <li v-for="(i, it) in gj" @click="choose(it)" :class="{'out': it.status === '1', 'win': it.status === '2', 'stop': it.status === '3'}">
                        <input type="checkbox" name="gj" v-model="checked" :value="it">
                        <div class="mod-item">
                            <span class="idx">{{ (it.mid).toString().length < 2 ? '0' + it.mid : it.mid }}</span>
                            <span class="name"><i :class="it.logo"></i>{{ it.name }}</span>
                            <span class="sp">{{ it.odds }}</span>
                            <span class="status">{{ it.status | status }}</span>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="ui-tab-bd gyj" v-if="tab.curIdx === 1">
                <div class="mod-item thead">
                    <span class="idx">编号</span>
                    <span class="name">球队</span>
                    <span class="sp">赔率</span>
                </div>
                <ul>
                    <li v-for="(i, it) in gyj" @click="choose(it)" :class="{'out': it.status === '1', 'win': it.status === '2', 'stop': it.status === '3'}">
                        <input type="checkbox" name="gyj" v-model="checked" :value="it">
                        <div class="mod-item">
                            <span class="idx">{{ (it.mid).toString().length < 2 ? '0' + it.mid : it.mid }}</span>
                            <span v-if="it.name.split('—').length > 1" class="name">
                                <em class="left">{{ it.name.split('—')[0] }}<i :class="it.logo.split(',')[0]"></i></em>
                                <s>VS</s>
                                <em class="right"><i :class="it.logo.split(',')[1]"></i>{{ it.name.split('—')[1] }}</em>
                            </span>
                            <span v-else class="name">{{ it.name }}</span>
                            <span class="sp">{{ it.odds }}</span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <div class="m-betbar">
            <div class="betbar-sub bet-mul" v-if="checked.length">
                <div class="action-ipt">投<span @click="KBnumCtrl">{{ mul }}<em v-if="keyboard.show"></em></span>倍</div>
                <div class="agree">我已阅读并同意《投注协议》</div>
            </div>
            <div class="betbar-main">
                <div class="betbar-l">
                    <button class="mac-select" type="button" @click="clearSelect">清空</button>
                </div>
                <div class="betbar-text">
                    <p v-if="!checked.length" class="tips">{{ betTips }}</p>
                    <p v-if="checked.length">{{ checked.length }}注  总计{{ checked.length * 2 * mul}}元</p>
                    <p v-if="checked.length" class="forecast">预测奖金:{{ forecast }}</p>
                </div>
                <div class="action-btn">
                    <button v-if="checked.length" class="btn btn-confirm" type="button" @click="createOrder">预约</button>
                    <button v-else class="btn btn-confirm" type="button" style="pointer-events: none; opacity: .5;">预约</button>
                </div>
                <calc :show.sync="keyboard.show" :mul.sync="mul"></calc>
            </div>
        </div>
        <div class="placeholder-box" :class="{'plus':checked.length}"></div>
<!--        <div class="play-desc" @click.stop="showDesc" v-el:pd>
            <span class="close" @click.stop="this.$els.pd.remove()"></span>
        </div>
        <alert :show.sync="alert.show" title="2018世界杯冠军彩玩法说明" button-text="朕知道了">
            <dl>
                <dt>冠军竞猜游戏：</dt>
                <dd>即竞猜本届世界杯冠军队伍。根据世界杯决赛阶段32支队伍，玩法共设置32个结果选项，您可选择相应的球队进行投注；</dd>
                <dt>冠亚军竞猜游戏：</dt>
                <dd>即不分顺序竞猜本届世界杯冠亚军队伍，也就是猜哪两支球队最终进入决赛。为了方便您投注，冠亚军竞猜设置50个选项，如果购彩者预测的决赛队伍在默认球队选项之外，可以选择“其他”项；</dd>
                <dt>奖金计算：</dt>
                <dd>奖金=投注本金*出票赔率，球队对应赔率会根据赛事进行而变化，您的奖金将根据出票时的赔率进行计算；</dd>
                <dt>奖金派发：</dt>
                <dd>冠军竞猜、冠亚军竞猜均会在7月15日决赛后24小时内完成派奖；</dd>
            </dl>
        </alert>-->
        <div class="btn-share" @click="share"></div>
        <toast :show.sync="toast.show" type="text" :text="toast.text" @on-hide="toast.cb"></toast>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.min.js');?>"></script>
    <script>
    !function(e,t,n,g,i){e[i]=e[i]||function(){(e[i].q=e[i].q||[]).push(arguments)},n=t.createElement("script"),tag=t.getElementsByTagName("script")[0],n.async=1,n.src=('https:'==document.location.protocol?'https://':'http://')+g,tag.parentNode.insertBefore(n,tag)}(window,document,"script","assets.growingio.com/2.1/gio.js","gio");
    gio('init','8d4b2106242d6858', {});
        Vue.component('calc', {
            template: '\
                <template v-if="show">\
                    <div class="keyboard">\
                        <tt v-for="num in keyboard.num" :key="num.id" @click="KBnum(num)">{{ num }}</tt>\
                        <tt @click="KBnumDel">&times;</tt>\
                        <tt @click="KBnumCtrl">确认</tt>\
                    </div>\
                    <div class="mask" @click="KBnumCtrl"></div>\
                </template>\
            ',
            props: {
                show: {
                    type: Boolean,
                    default: false,
                    twoWay: true
                },
                mul: {
                    type: String,
                    default: '1',
                    twoWay: true
                }
            },
            data: function () {
                return {
                    keyboard: {
                        num: [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]
                    }
                }
            },
            methods: {
                // 数字键盘
                KBnum: function (val) {
                    this.mul += val;
                    this.mul = parseInt(this.mul, 10) + '';
                    if (this.mul === '0') {
                        this.mul = '1'
                    } else if (this.mul > 100000) {
                        this.mul = '100000';
                    } else {
                        this.mul = this.mul;
                    }
                },
                KBnumDel: function () {
                    var mulString = this.mul.toString().slice(0, -1);
                    if (mulString !== '') {
                        this.mul = mulString;
                    } else {
                        this.mul = '';
                    }
                },
                KBnumCtrl: function () {
                    this.show = !this.show;
                    if (this.mul === '' || this.mul === 0) {
                        this.mul = '1'
                    }
                }
            }
        });
        // 创建Vue实例 所有操作在这
        var active = new Vue({
            el: '.wrapper',
            data: function() {
                return {
                    tab: {
                        text: ['冠军竞猜', '冠亚军竞猜'],
                        curIdx: 0
                    },
                    gj: [],
                    gyj: [],
                    checked: [],
                    checkedSP: [],
                    mul: '10',
                    keyboard: {
                        show: false
                    },
                    alert: {
                        show: false
                    },
                    toast: {
                        show: false,
                        text: '',
                        cb: function () {}
                    },
                    map: {
                        '0': {
                            'name': 'GJ',
                            'lid': '44',
                        },
                        '1': {
                            'name': 'GYJ',
                            'lid': '45',
                        }
                    },
                    //isM: false
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
                betTips: function () {
                    if (this.tab.curIdx === 0) {
                        return '请选择冠军球队'
                    } else {
                        return '请选择冠亚军球队'
                    }
                },
                forecast: function () {
                    var arr = this.checkedSP;
                    if (arr.length === 0) {
                        return '0元'
                    } else if (arr.length >= 2) {
                        return (arr[0] * 1000 * this.mul * 2 / 1000) + '元~' + (arr[arr.length - 1] * 1000 * this.mul * 2/ 1000) + '元'
                    } else {
                        return (arr[0] * 1000 * this.mul * 2 / 1000) + '元'
                    }
                },
                codes: function () {
                    var arr = []
                    this.checked.forEach(function (it) {
                        arr.push(((it.mid).length < 2 ? '0' + it.mid : it.mid) + '(' + it.odds + ')')
                    })
                    return this.map[this.tab.curIdx].name + '|18001=' + arr.join('/') + '|' + this.checked.length
                },
                createApi: function () {
                    if (this.whois === 'isAndroid') {
                        return '/app/gjc/doBet'
                    } else if (this.whois === 'isIOS') {
                        return '/ios/gjc/doBet'
                    } else {
                        return '/order/createOrder'
                    }
                },
                params: function () {
                    if (this.whois === 'isAndroid' || this.whois === 'isIOS') {
                        return {
                            lid: this.map[this.tab.curIdx].lid,
                            codes: this.codes,
                            multi: this.mul,
                            betTnum: this.checked.length
                        }
                    } else {
                        return {
                            codes: this.codes,
                            lid: this.map[this.tab.curIdx].lid,
                            money: this.checked.length * 2 * this.mul,
                            multi: this.mul,
                            issue: '18001',
                            playType: 1,
                            isChase: 0,
                            betTnum: this.checked.length,
                            orderType: 0,
                            endTime: '2018-07-15 23:00:00'
                        }
                    }
                }
            },
            watch: {
                'tab.curIdx': function () {
                    this.clearSelect()
                    window.scrollTo(0, 0)
                },
                checked: function (val) {
                    if (val.length === 0) {
                        this.checkedSP = []
                    } else if (val.length === 1) {
                        this.checkedSP = [parseFloat(val[0].odds)]
                    } else {
                        var sp = val.map(function (it) {
                            return parseFloat(it.odds)
                        }).sort(function (a, b) {
                            return a - b
                        })
                        this.checkedSP = this.uniq(sp)
                    }
                }
            },
            created: function () {
                this.getCombines()
                this.getTeams()
                
                var search = window.location.search.split("?")[1]
                if (search) {
                    var kv = search.split('=')
                    if (kv[0] === 'tab') {
                        if (kv[1] === '1') {
                            this.tab.curIdx = 1
                        } else {
                            this.tab.curIdx = 0
                        }
                    }  else {
                        this.tab.curIdx = 0
                    }
                } else {
                    this.tab.curIdx = 0
                }
            },
            methods: {
                choose: function (it) {
                    var i = this.checked.indexOf(it)
                    if (i >= 0) {
                        this.checked.splice(i, 1)
                    } else {
                        this.checked.push(it)
                    }
                },
                uniq: function (arr) {
                    var obj = {}
                    return arr.filter(function (it) {
                        var key = typeof it + it
                        if (!obj[key]) {
                            obj[key] = true
                            return true
                        }
                    })
                },
                createOrder: function () {
                    this.$http.post(this.createApi, this.params,{
                        headers: {"X-Requested-With": "XMLHttpRequest"},
                        emulateJSON: true
                    }).then(function (res) {
                        var json = JSON.parse(res.data);
                        var ua = navigator.userAgent.toLowerCase();
                        if(json.status == '1'){
                            window.location.href = json.data;
                            closeTag = true;
                        }else if(json.status == '2'){
                            var backUrl = window.location.href;
							if (ua.indexOf('android') >= 0 && ua.indexOf('2345caipiao')) android.relogin(backUrl);
							else window.webkit.messageHandlers.relogin.postMessage({url:backUrl});
                            closeTag = true;
                        }else if(json.status == '3'){
                            var backUrl = window.location.href;
                            if (ua.indexOf('android') >= 0 && ua.indexOf('2345caipiao')) android.auth(backUrl);
                            else window.webkit.messageHandlers.goBind.postMessage({url:backUrl});
                            closeTag = true;
                        }else{
                            $.tips({
                                content:json.msg,
                                stayTime:2000
                            });
                            closeTag = true;
                        }
                    }).catch(function (err) {
                        this.toast.show = true
                        this.toast.text = '预约失败，请稍后再试'
                    })
                },
                getTeams: function () {
                    this.$http.get('/<?php echo $agent?>/gjc/getTeams').then(function (res) {
                        var res = JSON.parse(res.data)
                        this.gj = res
                    }).catch(function (err) {
                        this.toast.show = true
                        this.toast.text = '请求队伍信息失败，刷新页面重新加载'
                    })
                },
                getCombines: function () {
                    this.$http.get('/<?php echo $agent?>/gjc/getCombines').then(function (res) {
                        var res = JSON.parse(res.data)
                        this.gyj = res
                    }).catch(function (err) {
                        this.toast.show = true
                        this.toast.text = '请求对阵信息失败，刷新页面重新加载'
                    })
                },
                changeTab: function (i) {
                    this.tab.curIdx = i
                },
                clearSelect: function () {
                    this.checked = []
                },
                KBnumCtrl: function () {
                    this.keyboard.show = !this.keyboard.show;
                },
                showDesc: function () {
                    if (this.alert.show) return
                    this.alert.show = true
                    this.$nextTick(function () {
                        document.querySelector('.ui-alert').addEventListener('touchmove', function (e) {
                            e.preventDefault()
                        }, false)
                    })
                },
                share: function () {
                    try {
                        var url = 'https://8.166cai.cn/gjc?fx';
                        var imgurl = "<?php echo 'https:'.getStaticFile('/caipiaoimg/static/images/active/gjc/gjcfx.png')?>";
                        if (this.whois === 'isAndroid') {
                            android.shareSocialMedia("世界杯猜冠军", "竞猜2018年世界杯冠军及冠亚军", imgurl, url);
                        } else if (this.whois === 'isIOS') {
                            window.webkit.messageHandlers.snsShare.postMessage({url:url,title:"世界杯猜冠军",content:"竞猜2018年世界杯冠军及冠亚军",imageUrl:imgurl});
                        } else {
                            this.toast.show = true
                            this.toast.text = '在166彩票APP内打开才能分享'
                        }
                    } catch (err) {
                        this.toast.show = true
                        this.toast.text = '暂不支持分享'
                    }
                }        
            },
            filters: {
                status: function (val) {
                    if (val === '0') {
                        return '开售'
                    } else if (val === '1') {
                        return '淘汰'
                    } else if (val === '2') {
                        return '夺冠'
                    }
                     else if (val === '3') {
                        return '停售'
                    } else {
                        return val
                    }
                }
            }
        })
        gio('send');
    </script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js');?>"></script>
</body>

</html>