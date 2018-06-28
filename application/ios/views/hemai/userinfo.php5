<?php $this->load->view('comm/header'); ?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/hemai.min.css');?>">
</head>

<body>
    <div class="wrapper hemai-detail" id="zhanji" v-cloak>
        <div class="hemai-box">
            <div class="hemai-box-hd">
                <h2 class="hemai-box-title">发起人信息</h2>
            </div>
            <div class="hemai-box-bd pd30">
                <table class="table-info">
                    <tbody>
                        <tr>
                            <th width="20%">发起人</th>
                            <td width="80%">{{ userInfo.uname }}</td>
                        </tr>
                        <tr>
                            <th>战绩记录</th>
                            <td>
                                <span v-if="userInfo.points == 0">--</span>
                                <div class="level" v-else>
                                    <span v-if="zhanji[0]" v-for="item in zhanji[0]" :class="'level-hg level-' + item">皇冠</span>
                                    <span v-if="zhanji[1]" :class="'level-ty level-' + zhanji[1]">太阳</span>
                                    <span v-if="zhanji[2]" :class="'level-yl level-' + zhanji[2]">月亮</span>
                                    <span v-if="zhanji[3]" :class="'level-xx level-' + zhanji[3]">星星</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>累计中奖</th>
                            <td>共{{ userInfo.winningTimes }}次，共<em>{{ userInfo.bonus }}</em></td>
                        </tr>
                        <tr>
                            <th>近1月中奖</th>
                            <td>共{{ userInfo.monthWinTimes }}次，共<em>{{ userInfo.monthBonus }}</em></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="hemai-box">
            <div class="hemai-box-hd">
                <h2 class="hemai-box-title">合买中奖记录</h2>
            </div>
            <div class="hemai-box-bd">
                <table class="table-hemai">
                    <colgroup>
                        <col width="21%">
                        <col width="19%">
                        <col width="16%">
                        <col width="23%">
                        <col width="21%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th>发起时间</th>
                            <th>彩种</th>
                            <th>方案金额</th>
                            <th>税前奖金</th>
                            <th>回报率</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="item in table.data">
                            <td>{{ item.created.slice(5, -3) }}</td>
                            <td>{{ item.cname }}</td>
                            <td>{{ item.money }}</td>
                            <td><span class="main-color">{{ item.orderBonus }}</span></td>
                            <td><span class="main-color">{{ item.scale }}</span></td>
                        </tr>
                        <tr v-if="loading">
                            <td colspan="5">
                                <br>
                                <cp-spin :show.sync="loading"></cp-spin>
                                <br>
                            </td>
                        </tr>
                        <tr v-if="showNoDate">
                            <td colspan="5">
                                <div class="no-data">
                                    <img src="data:ipg/image;base64,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"
                                        alt="">
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="tips" style="text-align: center; font-size: 1rem;">{{ tipsTxt }}</div>
            </div>
        </div>

        <toast :show.sync="toast.show" :text="toast.text"></toast>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js');?>" type="text/javascript"></script>
    <script>
        // 创建Vue实例 所有操作在这
        var zhanji = new Vue({
            el: '#zhanji',
            data: function() {
                return {
                    userInfo : {
                        uid: "<?php echo $uid?>",
                        winningTimes: "<?php echo $winningTimes?>",
                        bonus: "<?php $b = floor($bonus/10000000000); $e = floor(($bonus - $b * 10000000000)/1000000); $y = $b > 0 ? 0 : floor(($bonus - $e * 1000000)/100); echo ($b > 0 ? $b."亿" : '').($e > 0 ? $e."万" : '').(($y > 0 || $bonus < 100) ? $y."元" : '')?>",
                        monthBonus: "<?php $b = floor($monthBonus/10000000000); $e = floor(($monthBonus - $b * 10000000000)/1000000); $y = $b > 0 ? 0 : floor(($monthBonus - $e * 1000000)/100); echo ($b > 0 ? $b."亿" : '').($e > 0 ? $e."万" : '').(($y > 0 || $monthBonus < 100) ? $y."元" : '')?>",
                        monthWinTimes: "<?php echo $monthWinTimes?>",
                        uname: "<?php echo $uinfo['uname']?>",
                        points: "<?php echo $united_points?>"
                    },
                    api: {
                        getHistory: '/ios/api/v1/hemai/userRecordList/'
                    },
                    categray: '123',
                    page: {
                        size: 10,
                        current: 0
                    },
                    table: {
                        data: []
                    },
                    loading: true,
                    flag: true,
                    scroll: false,
                    tips: false,
                    showNoDate: false,
                    tipsTxt: '加载中...',
                    toast: {
                        text: '',
                        show: false
                    }
                }
            },
            computed: {
                getUrl: function () {
                    return this.api.getHistory + this.userInfo.uid + '?number=' + this.page.size + '&page=' + this.page.current + '&lid=<?php echo $lid?>' ;
                },
                zhanji: function () {
                    var hg, ty, yl, xx, hgArr = [];
                    var points = this.userInfo.points;
                    (points > 99999) && (points = 99999);
                    if (points >= 1000) {
                        hg = Math.floor(points / 1000);
                        ty = Math.floor((points - hg * 1000) / 100);
                        yl = Math.floor((points - hg * 1000 - ty * 100) / 10);
                        xx = points % 10;
                        if (hg > 10) {
                            for(var i = 0; i < Math.floor(hg / 10); i++) {
                                hgArr.push(10)
                            }
                            hgArr.push(hg % 10)
                            
                        } else {
                            hgArr.push(hg)
                        }
                        hg = hgArr
                    } else if (points >= 100) {
                        ty = Math.floor(points / 100)
                        yl = Math.floor((points - ty * 100) / 10)
                        xx = points % 10
                    } else if (points >= 10) {
                        yl = Math.floor(points / 10)
                        xx = points % 10
                    } else {
                        xx = points
                    }
                    return [hg, ty, yl, xx]
                }
            },
            ready: function() {
                setTimeout(function () {
                    this.getData();
                }.bind(this), 1000)
            },
            methods: {
            	getData: function() {
                    var _this = this;
                    this.$http.get(this.getUrl, {
                        timeout: 4000,
                        before: function () {
                            this.loading = true;
                        }
                    }).then(function(res) {
                        res = JSON.parse(res.data);
                        this.loading = false;
                        if (res.status === '1') {
                            if (res.data.length === 0) {
                                this.showNoDate = true;
                                this.flag = false;
                                return;
                            }
                            _this.table.data = _this.table.data.concat(res.data);
                            _this.scroll = true;
                            if (res.data.length < _this.page.size) return;
                            _this.page.current++;
                            _this.scrollFn();
                        } else {
                            this.toast.text = err.msg
                            this.toast.show = true;
                        }
                    }).catch(function(error) {
                        this.toast.text = '加载失败';
                        this.toast.show = true;
                    })
                },
                scrollFn: function () {
                    window.addEventListener('scroll', function() {
                        if (!this.flag) return;
                        if (window.pageYOffset + window.innerHeight >= document.documentElement.scrollHeight - 20) {
                            this.flag = false;
                            this.$http.get(this.getUrl, {
                                before: function () {
                                    this.tips = true;
                                    this.tipsTxt = '加载中...'
                                }
                            })
                            .then(function (res) {
                                res = JSON.parse(res.data);
                                if (!res.data.length) {
                                    this.tipsTxt = '没有更多了';
                                    setTimeout(function () {
                                        this.tips = false;
                                    }.bind(this), 1000)
                                    return;
                                }
                                this.table.data = this.table.data.concat(res.data);
                                this.flag = true;
                                this.tips = false;
                                this.page.current++;
                            }).catch(function (err) {
                                this.tips = false
                                this.flag = true;
                                this.toast.text = "加载出错";
                                this.toast.show = true;
                            })
                        }
                    }.bind(this), false);
                }
            }
        })

        // var timer = null,
        //     scrollHeight, scrollTop, windowHeight
        // window.addEventListener('scroll', function() {
        //     windowHeight = document.body.clientHeight;
        //     scrollHeight = document.body.scrollHeight;
        //     scrollTop = document.body.scrollTop;
        //     clearTimeout(timer)
        //     timer = setTimeout(zhanji.scrollN(windowHeight, scrollHeight, scrollTop), 200)
        // });
    </script>
</body>

</html>