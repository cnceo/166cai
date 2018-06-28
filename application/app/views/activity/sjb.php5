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
    <title>玩赚世界杯</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/jhy.min.css') ?>">
<?php $this->load->view('mobileview/common/tongji'); ?>
</head>

<body ontouchstart>
    <div class="wrapper" :class="isM ? 'hasHeader' : ''" v-cloak>
        <header class="m-header" v-if="isM">
            <h1>玩赚世界杯</h1>
            <a href="8.166cai.cn" class="hd-lnk-l">首页</a>
        </header>
        <div class="hd"></div>
        <div class="main">
            <ul>
                <li :class="['a' + item.name, item.class, {'blink': aStart && i === 0, 'join': item.join}]" v-for="(i, item) in list">
                    <a :href="path + item.path">
                        <i class="img"></i>
                        <div class="text">
                            <h2>{{ item.title }}</h2>
                            <p>{{ item.slogan[item.sloganIdx] }}</p>
                        </div>
                    </a>
                    <span class="status">{{ item.status }}</span>
                </li>
            </ul>
        </div>
        <div class="ft">
            <span>为生活添彩</span>
        </div>
        <ul class="a-rp" v-if="aStart">
            <li v-for="item in 8">1</li>
        </ul>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js')?>"></script>
    <script>
        new Vue({
            el: '.wrapper',
            data: function () {
                return {
                    list: [
                        {
                            title: '世界杯猜冠军',
                            slogan: ['选出你心目中的冠军！'],
                            time: ['2018/5/15'],
                            seTime: ['2018/5/15', '2018/7/15 20:00:00'],
                            path: '/app/gjc?jhy',
                            name: 0,
                            index: 0,
                            join: false,
                            isOnlyNative: false
                        },
                        {
                            title: '全民红包大派送',
                            slogan: ['天上下了红包雨，全民疯抢中，手慢无', '累计派送红包<?php echo $hongbao?>元，领红包中大奖', '累计派送红包<?php echo $hongbao?>元'],
                            time: ['2018/5/14', '2018/6/5', '2018/6/11'],
                            seTime: ['2018/5/14', '2018/6/10 23:59:59'],
                            path: '/app/activity/worldcup2018/hb',
                            name: 1,
                            index: 1,
                            join: <?php echo $hbstatus?>
                        },
                        {
                            title: '答题赢彩金',
                            slogan: [ '答题时间，足球知识知多少？', '答题时间，足球知识知多少？', '彩金在手，大奖不愁！'],
                            time: ['2018/5/25', '2018/6/10', '2018/6/11'],
                            seTime: ['2018/5/25', '2018/6/10 23:59:59'],
                            path: '/app/activity/dthd',
                            name: 2,
                            index: 2,
                            join: <?php echo $dtstatus?>
                        },
                        {
                            title: '每日竞猜分奖池',
                            slogan: ['寻找章鱼哥，免费参与竞猜瓜分奖池！', '<?php echo $money>0?'今日奖池'.$money.'元，免费参与瓜分奖池':'寻找章鱼哥，免费参与竞猜瓜分奖池！';?>'],
                            time: [ '2018/6/10', '2018/6/11'],
                            seTime: ['2018/6/11', '2018/7/15 23:59:59'],
                            path: '/app/activity/jchd',
                            name: 3,
                            index: 3,
                            join: <?php echo $jcstatus?>
                        },
                        {
                            title: '疯狂加奖10%',
                            slogan: ['2串1、单关加奖高至5000元！', '2串1、单关加奖高至5000元！'],
                            time: ['2018/6/11', '2018/7/15'],
                            seTime: ['2018/6/11', '2018/7/15 23:59:59'],
                            path: '/app/activity/sjbjj',
                            name: 4,
                            index: 4,
                            join: false
                        },
                        {
                            title: '中奖大神榜',
                            slogan: ['上榜周周送豪礼，最高8888元彩金！', '抢占排行榜赢8888元彩金！', '<?php echo $userName?"排行榜第一名{$userName}，速来围观！":"抢占排行榜赢8888元彩金！"?>','上榜周周送豪礼，最高8888元彩金！'],
                            time: ['2018/5/31', '2018/6/1','2018/6/11', '2018/7/16'],
                            seTime: ['2018/6/11', '2018/7/15 23:59:59'],
                            path: '/app/activityphb/jc/<?php echo $max_pissue['max_pissue'] ?>',
                            name: 5,
                            index: 5,
                            join: false
                        },
                        {
                            title: '邀请好友享好礼',
                            slogan: ['球迷大狂欢，邀请好友奖励全面升级', '和小伙伴一起看比赛、猜赛果、赢奖金'],
                            time: ['2018/5/14', '2018/6/11'],
                            seTime: ['2018/5/14', '2018/7/15 23:59:59'],
                            path: '/app/activity/invitation',
                            name: 6,
                            index: 6,
                            join: false
                        },
                    ],
                    curTime: '<?php echo $_GET['time']?$_GET['time']:date("Y/m/d H:i:s"); ?>', // PHP给个当前时间
                    diffTime: '',
                    isAndroid: false,
                    aStart: false,
                    isM: false
                }
            },
            computed: {
                path: function () {
                    if (this.isM) {
                        return '//8.166cai.cn'
                    } else if (this.isAndroid) {
                        return '//<?php echo DOMAIN;?>'
                    } else {
                        return '//<?php echo DOMAIN;?>'
                    }
                }
            },
            created: function () {
                this.curTime = +new Date(this.curTime)
            },
            ready: function () {
                this.aStart = !this.getCookie()
                document.cookie = 'aStart=0'

                var ua = navigator.userAgent.toLowerCase()
                if (ua.indexOf('android') >= 0 && ua.indexOf('2345caipiao')) {
                    this.isAndroid = true
                }

                this.updateStatus()
            },
            methods: {
                getCookie: function () {
                    return document.cookie.split('; ').some(function (item) {
                        var it = item.split('=');
                        if (it[0] === 'aStart') {
                            return true;
                        }
                    })
                },
                updateStatus: function () {
                    var _this = this
                    this.list.map(function (it, idx) {
                        var sloganTimeArr = it.time.map(function (date) {
                                return +new Date(date)
                            }),
                            inTimeArr = it.seTime.map(function (date) {
                                return +new Date(date)
                            }),
                            i = 0
                        
                        sloganTimeArr.push(_this.curTime)
                        sloganTimeArr.sort(function (a, b) {
                            return b - a
                        })

                        var sloganTimeArrLength = sloganTimeArr.length,
                        i = sloganTimeArrLength - sloganTimeArr.indexOf(_this.curTime) - 1

                        if (_this.curTime < inTimeArr[0]) {
                            _this.list[idx].class = 'comming'
                            _this.list[idx].status = _this.showDate(it.seTime)
                        } else if (_this.curTime > inTimeArr[1]) {
                               _this.list[idx].class = 'over'
                               _this.list[idx].index = 7
                               _this.list[idx].status = '已结束'
                        } else {
                            _this.list[idx].class = ''
                            _this.list[idx].index = 0
                            _this.list[idx].status = _this.calcDate(inTimeArr[1] - _this.curTime)
                        }
                        if (i !== 0) {
                            i--
                        }
                        if (it.slogan[i].indexOf('答题开始还有') >= 0) {
                            _this.diffDate()
                            _this.list[idx].slogan[i] += _this.diffTime
                        }
                        Vue.set(_this.list[idx], 'sloganIdx',  i)
                    })

                    setTimeout(function () {
                        _this.list.sort(function (a, b) {
                            return a.index - b.index
                        })
                    }, 0)
                },
                diffDate: function () {
                    var mm = 0
                    if (this.curTime < new Date('2018/5/25')) {
                        mm = (new Date('2018/5/25') - this.curTime) / 1000
                    } else if (this.curTime < new Date('2018/6/1')) {
                        mm = (new Date('2018/6/1') - this.curTime) / 1000
                    } else if (this.curTime < new Date('2018/6/8')) {
                        mm = (new Date('2018/6/8') - this.curTime) / 1000
                    }
                    this.diffTime = Math.floor(mm / (24 * 60 * 60)) + '天' + Math.floor((mm % (24 * 60 * 60)) / 3600) + ':' + Math.floor((mm % (24 * 60 * 60) % 3600) / 60)
                },
                showDate: function (time) {
                    return time[0].split(' ')[0].split('/').slice(1).join('.') + '-' + time[1].split(' ')[0].split('/').slice(1).join('.')
                },
                calcDate: function (date) {
                    var mm = 3600
                    date = Math.floor(date / 1000)
                    if (date >= 24 * mm) {
                        return '进行中'
                    } else {
                        return '距结束' + (Math.floor(date / mm) < 10 ? '0' + Math.floor(date / mm) : Math.floor(date / mm)) + ':' + (Math.floor(date % mm / 60) ? (Math.floor(date % mm / 60) < 10 ? '0' + Math.floor(date % mm / 60) : Math.floor(date % mm / 60)) : '00')
                    }
                }
            }
        })
    </script>
</body>

</html>