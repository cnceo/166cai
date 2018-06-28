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
        <div class="main" v-else>
            <div class="banner">
                <div class="inner">
                    <p class="title">玩转世界杯<em>免费参与</em></p>
                    <p class="slogan">猜中当期所有场次即可瓜分奖池</p>
                    <p class="bonus">本期奖池<b v-for="it in money" track-by="$index">{{ it }}</b>元</p>
                    <p class="time">截止时间：<span>{{ endTime }}</span></p>
                </div>
            </div>
            <div class="mian-content">
                <ul class="ui-tab-nav">
                    <li :class="{'current': tab.curIdx === i}" v-for="(i, it) in tab.text" @click="changeTab(i)">{{ it }}</li>
                </ul>
                <div class="ui-tab-bd bqjc" v-if="tab.curIdx === 0">
                    <div class="loading" v-if="loading && !matchMap.length"><i></i><p>加载中...</p></div>
                    <div class="wating" v-if="match.status === '0'">
                        <p>竞猜活动<span v-if="isStart[0]"><em>{{ isStart[0] }}</em>天</span><span v-if="isStart[1]"><em>{{ isStart[1] }}</em>小时</span><span v-if="isStart[2]"><em>{{ isStart[2] }}</em>分钟</span>后开始
                        </p>
                        <p><small>猜比赛，分奖池</small></p>
                        <button class="btn" @click="goBuyJczq(null)">投注竞彩足球</button>
                    </div>

                    <div class="match-group" v-else>
                        <div class="mod-match" v-for="(i, it) in matchMap">
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
                            <div class="mod-match-bd" v-else>
                                <input type="checkbox" :id="'match' + i + '0'" :value="3" v-model="matchCode[it.mid]">
                                <label :for="'match' + i + '0'" @click="choose(it.mid, 3)">主胜</label>
                                <input type="checkbox" :id="'match' + i + '1'" :value="1" v-model="matchCode[it.mid]">
                                <label :for="'match' + i + '1'" @click="choose(it.mid, 1)">平</label>
                                <input type="checkbox" :id="'match' + i + '2'" :value="0" v-model="matchCode[it.mid]">
                                <label :for="'match' + i + '2'" @click="choose(it.mid, 0)">客胜</label>
                            </div>
                            <div class="mod-match-ft">
                                <span :style="{'-webkit-flex-basis': parseInt(it.ratio[0]) + '%', 'flex-basis': parseInt(it.ratio[0]) + '%'}">{{ parseInt(it.ratio[0]) }}%选主胜</span>
                                <span :style="{'-webkit-flex-basis': parseInt(it.ratio[1]) + '%', 'flex-basis': parseInt(it.ratio[1]) + '%'}">{{ parseInt(it.ratio[1]) }}%选平</span>
                                <span :style="{'-webkit-flex-basis': parseInt(it.ratio[2]) + '%', 'flex-basis': parseInt(it.ratio[2]) + '%'}">{{ parseInt(it.ratio[2]) }}%选客胜</span>
                            </div>
                        </div>
                        <button v-if="match.status === '1' && matchMap.length === chooseNum" class="btn" @click="createOrder">确认提交</button>
                        <button v-if="match.status === '1' && matchMap.length !== chooseNum" class="btn btn-disabled">确认提交</button>
                        <button v-if="match.status === '2'" class="btn" @click="goBuyJczq(matchMap)">本期已预测  同方案买竞足</button>
                        <a href="javascript:" @click="betShare" v-if="match.status === '2'" class="btn">邀请好友一起猜</a>
                        <button v-if="match.status === '3'" class="btn btn-disabled">已截止</button>
                    </div>
                </div>
                <div class="ui-tab-bd jcjl" v-if="tab.curIdx === 1">
                    <div class="loading" v-if="loading && !orders.length"><i></i><p>加载中...</p></div>
                    <div class="no-data" v-if="!loading && !orders.length">
                        <p>暂无竞猜记录</p>
                        <a href="javascript:" @click="goBuyJczq(null)">买一注竞彩足球</a>
                    </div>
                    <div :class="['mod-order', 'type' + it.status]" v-for="it in orders" track-by="$index">
                        <div class="mod-order-hd">
                            <span class="l">第{{ it.issue }}期世界杯竞猜</span>
                            <span class="r" v-if="it.status === '1' || it.status === '2'">预计{{ it.award_time }}开奖</span>
                            <span class="r" v-if="it.status === '3' && it.oStatus !== '2'"><span class="noawards">未中奖</span></span>
                            <span class="r" v-if="it.status === '3' && it.oStatus === '2'"><span class="awards"><i></i>中奖{{ it.bouns }}元</span></span>
                        </div>
                        <div class="mod-order-bd">
                            <div class="table">
                                <div class="table-hd">
                                    <span class="l">比赛对阵</span>
                                    <span class="r">我的竞猜</span>
                                </div>
                                <div class="table-bd">
                                    <ul>
                                        <li v-for="i in it.matachs" track-by="$index">
                                            <div class="l">
                                                <span class="name">{{ i.home }}</span>
                                                <span class="vs" v-if="i.score">{{ i.score }}</span>
                                                <span class="vs" v-else>VS</span>
                                                <span class="name">{{ i.away }}</span>
                                            </div>
                                            <div class="r">
                                                <em v-if="i.mark">{{ i.codeCn }}</em>
                                                <span v-else>{{ i.codeCn }}</span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="mod-order-ft">
                            <button class="btn" v-if="it.status === '1' && it.matachs[0].code" @click="goBuyJczq(it.matachs)">同方案买一注  2元可中{{ it.forecast_bouns }}元</button>
                             <a href="javascript:" @click="betShare" v-if="it.status === '1' && it.matachs[0].code && $index === 0" class="btn">邀请好友一起猜</a>
                            <button class="btn" v-if="it.status === '1' && !it.matachs[0].code" @click="goBuyJczq(null)">买一注足球</button>
                            <button class="btn" v-if="it.status === '2'" disabled>比赛进行中</button>
                            <a href="javascript:" class="btn" v-if="it.status === '3'" @click="showPop(it.orderId)">查看当期中奖情况 ></a>
                        </div>
                    </div>
                </div>
                <div class="ui-tab-bd jcdsb" v-if="tab.curIdx === 2">
                    <div class="loading" v-if="loading && !rankList.length"><i></i><p>加载中...</p></div>
                    <template v-if="!loading">
                        <div class="wating" v-if="!rankList.length">
                            <p>大神榜将在首期活动开奖后更新</p>
                            <p><small>抄单大神，等你中奖</small></p>
                        </div>
                        <div class="mod-rank" v-else>
                            <div class="mod-rank-hd">
                                <span class="col1">名次</span>
                                <span class="col2">用户名</span>
                                <span class="col3">猜中场次</span>
                                <span class="col4">累计奖金</span>
                            </div>
                            <div class="mod-rank-bd">
                                <ol>
                                    <li :class="['rank-item', {'open': it.rankIsOpen && !it.colorFlag, 'mine': it.colorFlag}]" v-for="(index, it) in rankList" track-by="$index">
                                        <div class="rank-item-hd" @click="showMore(index)">
                                            <span class="col1">{{ it.rank }}</span>
                                            <span class="col2"><u>{{ it.uname }}</u></span>
                                            <span class="col3">{{ it.match_num }}场</span>
                                            <span class="col4">{{ it.bouns }}元</span>
                                        </div>
                                        <div class="rank-item-bd" v-if="it.rankIsOpen">
                                            <div class="mod-order">
                                                <div class="mod-order-bd">
                                                    <div class="table">
                                                        <div class="table-hd">
                                                            <span class="l">比赛对阵</span>
                                                            <span class="r" v-if="it.colorFlag">我的竞猜</span>
                                                            <span class="r" v-else>TA的竞猜</span>
                                                        </div>
                                                        <div class="table-bd">
                                                            <ul>
                                                                <li v-for="i in it.matachs" track-by="$index">
                                                                    <div class="l">
                                                                        <span class="name">{{ i.home }}</span>
                                                                        <span class="vs">VS</span>
                                                                        <span class="name">{{ i.away }}</span>
                                                                    </div>
                                                                    <div class="r">
                                                                        {{ i.codeCn }}
                                                                    </div>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mod-order-ft">
                                                    <button class="btn" v-if="it.status === '1' && it.matachs[0].code" @click="goBuyJczq(it.matachs)">跟大神买一注  2元可中{{ it.forecast_bouns }}元</button>
                                                    <button class="btn" v-if="it.status === '1' && !it.matachs[0].code" @click="goBuyJczq(null)">买一注足球</button>
                                                    <button class="btn" v-if="it.status === '2'" disabled>比赛进行中</button>
                                                    <a href="javascript:" class="btn" v-if="it.status === '3' && it.orderId" @click="showPop(it.orderId)">查看当期中奖情况 ></a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="mod-text">
                <h2 class="mod-text-title">奖品明细</h2>
                <ol>
                    <li>1、<em>免费竞猜世界杯比赛，猜对当期所有场次即可平分当期奖池；</em></li>
                    <li>2、竞猜结果将在当期最后一场比赛结束后的6小时内公布，奖金将派发至您的彩票账户，您可至账户明细中查看；</li>
                </ol>
            </div>
            <div class="mod-text">
                <h2 class="mod-text-title">活动规则</h2>
                <ol>
                    <li>1、<em>本次竞猜完全免费，每个用户每期只能参与一次活动，</em>提交方案成功即计为参与成功；</li>
                    <li>2、竞猜彩果以比赛90分钟内比分(含伤停补时，不含加时赛、点球大战)结果为准。其中竞猜赛事取消、中断或改期，官方比赛彩果公布或确认取消将延后36小时，对应场次将同步延后处理，取消比赛的任何结果都算对；</li>
                    <li>3、严厉禁止和打击恶意刷参与活动的行为，对于恶意刷参与者，166彩票有权取消其奖励资格，并保留追究其法律责任的权利；</li>
                    <li>4、本活动最终解释权归166彩票网所有，如有疑问请咨询在线客服或致电400-690-6760；</li>
                </ol>
            </div>
        </div>
        <toast :show.sync="toast.show" type="text" :text="toast.text" @on-hide="toast.cb"></toast>
        <confirm :show.sync="confirm.show" :title="confirm.title" :cancel-Text="confirm.cancelText" confirm-Text="分享好友" @on-cancel="confirm.cancelCb" @on-confirm="betShare">
            <p style="text-align: left;">{{ confirm.text }}</p>
        </confirm>     
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>"></script>
    <script>
        var actionInfo = {
            'action': 'com.caipiao166.jczqbet',
            'data': {'20180509001': '1'},
            'lid': 42
        },
        imgMap = {
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
        nMap = {
            '3': '0',
            '1': '1',
            '0': '2'
        },
        jczqUrl = '//8.166cai.cn/jczq',
        timer = null
        // 创建Vue实例 所有操作在这
        var active = new Vue({
            el: '.wrapper',
            data: function() {
                return {
                    tab: {
                        text: ['本期竞猜', '竞猜记录', '竞猜大神榜'],
                        curIdx: 0
                    },
                    match: {},
                    matchCode: {},
                    orders: [],
                    rankList: [],
                    chooseNum: 0,
                    popInfo: {},
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
                    beShare: false,
                    confirm: {
                        show: false,
                        title: '',
                        cancelText: '',
                        cancelCb: function () {}
                    },
                    loading: true
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
                isWx: function () {
                    return navigator.userAgent.toLowerCase().indexOf('micromessenger') >= 0
                },
                matchMap: function () {
                    return this.match.matachs.map(function (it, i) {
                        Vue.set(this.matchCode, it.mid, [])
                        it.ratio = it.ratio.split(',')
                        it.homeurl = '//888.166cai.cn/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.home] + '.png'
                        it.awayurl = '//888.166cai.cn/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.away] + '.png'
                        return it
                    }.bind(this))
                },
                money: function () {
                    return this.match.money.toString().split('.')[0].split('')
                },
                endTime: function () {
                    return this.timeFormat(this.match.end_time)
                },
                isStart: function () {
                    if (!this.match.start_time || !this.match.timestamp) return [0, 0, 0]
                    var t = new Date(this.match.start_time.split('-').join('/')) - this.match.timestamp * 1000
                    if (t <= 0) {
                        return []
                    } else {
                        var d = parseInt(t / (24 * 60 * 60 * 1000)),
                            h = parseInt(t % (24 * 60 * 60 * 1000) / (60 * 60 * 1000)),
                            m = Math.ceil(t % (24 * 60 * 60 * 1000) % (60 *  60 * 1000) / (60 * 1000))
                        return [d, h, m]
                    }
                },
                codes: function () {
                    var arr = []
                    for(var k in this.matchCode) {
                        arr.push(k + '=' + this.matchCode[k])
                    }
                    return arr.join(',')
                },
                params: function () {
                    if (this.whois === 'isM') {
                        return {
                            theme_id: this.match.theme_id,
                            issue: this.match.issue,
                            code: this.codes
                        }
                    } else {
                        return {
                            theme_id: this.match.theme_id,
                            issue: this.match.issue,
                            code: this.codes
                        }
                    }
                },
                getIndexApi: function () {
                    if (this.whois === 'isAndroid') {
                        return '/app/api/v2/jchd/index'
                    } else if (this.whois === 'isIOS') {
                        return '/app/api/v2/jchd/index'
                    } else {
                        return '/jchd/index'
                    }
                },
                getOrderListApi: function () {
                    if (this.whois === 'isAndroid') {
                        return '/app/api/v2/jchd/orderList'
                    } else if (this.whois === 'isIOS') {
                        return '/app/api/v2/jchd/orderList'
                    } else {
                        return '/jchd/orderList'
                    }
                },
                createApi: function () {
                    if (this.whois === 'isAndroid') {
                        return '/app/api/v2/jchd/post'
                    } else if (this.whois === 'isIOS') {
                        return '/app/api/v2/jchd/post'
                    } else {
                        return '/jchd/post'
                    }
                },
                rankApi: function () {
                    if (this.whois === 'isAndroid') {
                        return '/app/api/v2/jchd/rankList'
                    } else if (this.whois === 'isIOS') {
                        return '/app/api/v2/jchd/rankList'
                    } else {
                        return '/jchd/rankList'
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
                },
                shareUrl: function () {
                    if (this.whois === 'isAndroid') {
                        return '//www.ka5188.com/app/activity/jchdShare'
                    } else if (this.whois === 'isIOS') {
                        return '//www.ka5188.com/ios/activity/jchdShare'
                    } else {
                        return '//8.166cai.cn/activity/jchdShare'
                    }
                }
            },
            watch: {
                // popInfo: function (val) {
                //     if (val && val.matachs && val.matachs.length) {
                //         val.matachs.forEach(function (it) {
                //             it.homeurl = '//888.166cai.cn/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.home] + '.png'
                //             it.awayurl = '//888.166cai.cn/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.away] + '.png'
                //         })
                //     }
                // },
                'tab.curIdx': function (val) {
                    var _this = this
                    clearTimeout(window.timer)
                    this.loading = true
                    if (val === 1) {
                        window.timer = setTimeout(function () {
                            _this.getOrders()
                        }, 500)
                    } else if (val === 2) {
                        window.timer = setTimeout(function () {
                            _this.getRank()
                        }, 500)
                    }
                },
                matchCode: {
                    handler: function (val) {
                        this.chooseNum = 0
                        for(var k in val) {
                            if (val[k].length > 0) {
                                this.chooseNum++
                            }
                        }
                    },
                    deep: true
                }
            },
            created: function () {
                this.getIndex()
            },
            methods: {
                choose: function (k) {
                    setTimeout(function () {
                        if (this.matchCode[k].length === 2) {
                            this.matchCode[k].splice(0, 1)
                        }
                    }.bind(this))
                },
                getSession: function () {
                    var jchdSS = JSON.parse(window.sessionStorage.getItem('jchdSS'))
                    if (jchdSS) {
                        jchdSS.split(',').forEach(function (it) {
                            var arr = it.split('=')
                            Vue.set(this.matchCode, arr[0], [Number(arr[1])])
                        }.bind(this))
                    }
                },
                removeSession: function () {
                    window.sessionStorage.removeItem('jchdSS')
                },
                getIndex: function () {
                    this.$http.get(this.getIndexApi).then(function (res) {
                        var res = JSON.parse(res.data)
                        if (res.status === '200') {
                            this.match = res.data.currentMatch
                            this.popInfo = res.data.popInfo
                            if (this.popInfo.matachs && this.popInfo.matachs.length) {
                                this.popInfo.matachs = this.popInfo.matachs.map(function (it) {
                                    it.homeurl = '//888.166cai.cn/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.home] + '.png'
                                    it.awayurl = '//888.166cai.cn/caipiaoimg/v1.1/images/active/gjc2018/' + window.imgMap[it.away] + '.png'
                                    return it
                                })
                            }
                            this.loading = false
                            setTimeout(function () {
                                this.getSession()
                            }.bind(this))
                        }
                    }).catch(function (err) {
                        this.lotGet(this.getIndex)
                    })
                },
                getOrders: function () {
                    this.$http.post(this.getOrderListApi, {
                        'theme_id': this.match.theme_id,
                    }, {
                        emulateJSON: true
                    }).then(function (res) {
                        var res = JSON.parse(res.data)
                        if (res.status === '100') {
                            this.goLogin()
                        } else if (res.status === '300') {
                            this.toast.show = true
                            this.toast.text = res.msg
                        } else {
                           if (res.data.orders) {
                                this.orders = res.data.orders.map(function (item) {
                                    item.award_time = this.timeFormat(item.award_time)
                                    item.matachs.forEach(function (it) {
                                        if (it.score) {
                                            var code = this.score2code(it.score)
                                            if (it.code === code) {
                                                it.mark = true
                                            }
                                        }
                                        if (it.code) {
                                            if (it.code === '3') {
                                                it.codeCn = it.home + '胜'
                                            } else if (it.code === '0') {
                                                it.codeCn = it.away + '胜'
                                            } else {
                                                it.codeCn = '平'
                                            }
                                        }
                                    }.bind(this))                   
                                    return item
                                }.bind(this))
                                this.loading = false
                            } 
                        }
                    }).catch(function (err) {
                        // this.toast.show = true
                        // this.toast.text = '获取排行榜失败，请刷新重试'
                        this.lotGet(this.getOrders)
                    })
                },
                getRank: function () {
                    this.$http.post(this.rankApi, {
                        'theme_id': this.match.theme_id,
                        'issue': this.match.issue
                    }, {
                        emulateJSON: true
                    }).then(function (res) {
                        var res = JSON.parse(res.data)
                        if (res.status === '100') {
                            this.goLogin()
                        }  else {
                            this.rankList = res.data.ranks.map(function (item) {
                                item.matachs.forEach(function (it) {
                                    var code = this.score2code(it.score)
                                    if (it.code === '3') {
                                        it.codeCn = it.home + '胜'
                                    } else if (it.code === '0') {
                                        it.codeCn = it.away + '胜'
                                    } else if (it.code === '1')  {
                                        it.codeCn = '平'
                                    } else {
                                        it.codeCn = '--'
                                    }
                                }.bind(this))
                                if (item.rank === '1') {
                                    item.rankIsOpen = true
                                } else {
                                    item.rankIsOpen = false     
                                }
                                return item
                            }.bind(this))
                            this.loading = false
                        }
                    }).catch(function () {
                        // this.toast.show = true
                        // this.toast.text = '获取排行榜失败，请刷新重试'
                        this.lotGet(this.getRank)
                    })
                },
                lotGet: function (fn) {
                    clearTimeout(window.timer)
                    window.timer = setTimeout(function () {
                        fn()
                    }, 800)
                },
                showPop: function (orderId) {
                    // 打开新分享的页面
                    window.location.href = this.shareUrl + '?orderId=' + orderId
                },
                setSessionStorage: function (str) {
                    window.sessionStorage.setItem('jchdSS', str)
                },
                createOrder: function () {
                    this.$http.post(this.createApi, this.params, {
                        emulateJSON: true
                    }).then(function (res) {
                        var res = JSON.parse(res.data)
                        if (res.status === '200') {
                            this.confirm.title = "预测成功"
                            this.confirm.text = res.msg
                            this.confirm.cancelText = '查看竞猜'
                            this.confirm.cancelCb = function () {
                                this.tab.curIdx = 1
                            }.bind(this)
                            this.confirm.show = true

                            // 改变状态为已预测
                            this.match.status = '2'
                            this.match.matachs.forEach(function (it) {
                                it.code = this.matchCode[it.mid] + ''
                            }.bind(this))
                            this.removeSession()
                        } else if (res.status === '300') {
                            this.confirm.title = "预测失败"
                            this.confirm.text = res.msg
                            this.confirm.cancelText = '朕知道了'
                            this.confirm.confirmCb = function () {
                                this.goBuyJczq(null)
                            }.bind(this)
                            this.confirm.show = true
                        } else if (res.status === '100') {
                            this.setSessionStorage(JSON.stringify(this.codes))
                            this.goLogin()
                        } else {
                            this.toast.show = true
                            this.toast.text = res.msg
                        }
                    }).catch(function (err) {
                        this.toast.show = true
                        this.toast.text = '预约失败，请稍后再试'
                    })
                },
                goLogin: function () {
                    this.tab.curIdx = 0
                    if (this.whois === 'isM') {
                        window.location.href = '/main/login?directUrl=' + window.location.href;
                    } else {
                        try {
                            if (this.whois === 'isAndroid') {
                                window.android.relogin(window.location.href)
                            } else {
                                window.webkit.messageHandlers.relogin.postMessage({url: window.location.href})
                            }
                        } catch (err) {
                            this.toast.show = true
                            this.toast.text = '请先去登录'
                        }
                    }
                },
                timeFormat: function (time) {
                    var timeArr = time.split(' ')
                    return timeArr[0].split('-').slice(1).join('.') + ' ' + timeArr[1].split(':').slice(0, 2).join(':')
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
                changeTab: function (i) {
                    if (this.match.issue) {
                        this.tab.curIdx = i
                    }
                },
                showMore: function (index) {
                    this.rankList[index].rankIsOpen = !this.rankList[index].rankIsOpen
                },
                continue: function () {
                    this.popInfo = {}
                },
                betShare: function () {
                    if (this.whois === 'isM') {
                        if (this.isWx) {
                            window.location.href = '//8.166cai.cn/activity/sharepage'
                            return
                        }
                        this.toast.show = true
                        this.toast.text = '在166彩票APP内打开才能分享'
                    } else {
                        var params = {
                            title: '竞猜世界杯',
                            content: '每日免费竞猜世界杯赛果分万元奖池',
                            imageUrl: 'https://8.166cai.cn/caipiaoimg/v1/images/active/jcjc/icon4wx.png',
                            url: 'https://8.166cai.cn/activity/jchd'
                        }
                        try {
                            if (this.whois === 'isAndroid') {
                                window.android.shareSocialMedia(params.title, params.content, params.imageUrl, params.url)
                            } else {
                            	window.webkit.messageHandlers.snsShare.postMessage({url:params.url,title:params.title,content:params.content,imageUrl:params.imageUrl})
                            }
                        } catch (err) {
                            this.toast.show = true
                            this.toast.text = '在166彩票APP内打开才能分享'
                        }
                    }
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
                },
                goBuyJczq: function (match) {
                    if (this.whois === 'isM') {
                        window.location.href = jczqUrl
                    } else {
                        if (match) {
                            window.actionInfo.data = {}
                            match.forEach(function (it) {
                                window.actionInfo.data[it.mid] = window.nMap[it.code]
                            })
                        }
                        try {
                            if (this.whois === 'isAndroid') {
                                if (window.android.toActivity && match) {
                                    window.android.toActivity(JSON.stringify(window.actionInfo))
                                } else {
                                    window.bet.btnclick('42', 'jczq')
                                }
                            } else {
                                if (window.webkit.messageHandlers.goToSoccerBet && match) {
                                    window.webkit.messageHandlers.goToSoccerBet.postMessage(window.actionInfo)
                                } else {
                                    window.webkit.messageHandlers.doBet.postMessage({'lid': '42'})
                                }
                            }
                        } catch (err) {
                            this.toast.show = true
                            this.toast.text = '跳转失败'
                        }
                    }
                }
            }
        })
    </script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js');?>"></script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>

</html>