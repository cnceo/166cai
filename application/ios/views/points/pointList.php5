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
    <title>积分明细</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/jifen.min.css');?>">
</head>
<body>
    <div class="wrapper account" v-cloak>
        <div class="sticky-box sticky-menu">
            <menu :menu-data="tabList" :cur.sync="tabCur"></menu>
        </div>
        <div v-for="(key, value) in renderData" class="cp-list-box">
            <h2 class="cp-list-title">{{ key.split('-').slice(1).join('-') }}</h2>
            <ul class="cp-list ui-list-link">
                <li v-for="item in value">
                    <a :href="item.url">
                        <div class="cp-list-txt">
                            <div>
                                <div class="cell-l">
                                    <p>{{ item.type }}</p>
                                </div>
                                <div class="cell-r">
                                    <p><strong :class="(item.numStatus > 0) ? 'color2' : 'color1'">{{ item.num }}</strong></p>
                                </div> 
                            </div>
                            <div>
                                <div class="cell-l">
                                    <p class="cp-list-s">{{ item.created.split(' ')[1].split(':').slice(0,2).join(':') }}</p>
                                </div>
                                <div class="cell-r">
                                    <p class="cp-list-s">余额{{ item.uvalue }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
        <div v-if="loading" class="loadingBefore">
            <cp-spin :show="true"></cp-spin>
        </div>
        
        <div v-if="jfList[cTypeName].length === 0 && !loading" class="no-data"><i class="logo-virtual"></i><p>暂无记录</p></div>
        <toast :show.sync="toast.show" type="text" :text="toast.text"></toast>
        <cp-spin :show="more && !noMore"></cp-spin>
        <div v-if="noMore" class="noMore">\\(╯-╰)/  没有更多了</div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js');?>" type="text/javascript" ></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js');?>" type="text/javascript" ></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>" type="text/javascript" ></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js');?>" type="text/javascript" ></script>
    <script>
        new Vue({
            el: '.wrapper',
            data: function() {
                return {
                    apiURL: '/ios/points/getPointLists',
                    tabList: [
                        {
                            name: '积分明细'
                        },
                        {
                            name: '购彩获得',
                            ctype: '0',
                        },
                        {
                            name: '任务获得',
                            ctype: '1',
                        },
                        {
                            name: '积分赠送',
                            ctype: '2',
                        },
                        {
                            name: '兑换红包',
                            ctype: '3',
                        },
                        {
                            name: '积分过期',
                            ctype: '4',
                        }
                    ],
                    tabCur: 0,
                    size: 10,
                    page: {},
                    jfList: {},
                    renderData: {},
                    toast: {
                        show: false,
                        text: ''
                    },
                    more: false,
                    noMore: false,
                    loading: true,
                    flag: true,
                    direction: true
                }
            },
            computed: {
                cTypeName: function () {
                    return this.tabList[this.tabCur].name
                },
                params: function () {
                    var data = {}
                    if (this.tabList[this.tabCur].ctype) {
                        data.ctype = this.tabList[this.tabCur].ctype
                    }
                    if (this.size && this.size !== 10) {
                        data.size = this.size
                    }
                    data.cpage = this.page[this.cTypeName]
                    return data
                }
            },
            watch: {
                tabCur: function () {
                    this.more = false
                    this.loading = true
                    this.flag = true
                    this.renderData = {}
                    if (this.jfList.hasOwnProperty(this.cTypeName)) {
                        this.renderTrans()
                        return
                    }
                    this.getData()
                }
            },
            created: function() {
                this.getData()
                this.scrollFn()
            },
            ready: function () {
                document.querySelector('.menu').addEventListener('touchstart', function (e) {
                    if (e.target && e.target.nodeName.toLowerCase() === 'input') {
                        window.scrollTo(0, 0)
                    }
                })
            },
            methods: {
                renderTrans: function () {
                    this.renderData = {}
                    this.jfList[this.cTypeName].forEach(function (it) {
                        var KT = it.created.split(' ')[0]
                        if (!this.renderData.hasOwnProperty(KT)) {
                            Vue.set(this.renderData, KT, [])
                        }
                        this.renderData[KT].push(it)
                    }.bind(this))
                    this.loading = false
                },
                getData: function() {
                    var name = ''
                    this.flag = false
                    this.$http.get(this.apiURL, {
                        before: function (request) {
                            if (this.previousRequest) {
                                this.previousRequest.abort();
                            }
                            this.previousRequest = request;
                            name = this.cTypeName
                        },
                        params: this.params
                    }).then(function(res) {
                        var res = JSON.parse(res.data)
                        if (res.status === '200') {
                            if (!this.jfList.hasOwnProperty(name)) {
                                Vue.set(this.jfList, name, [])
                            }
                            if (!this.page.hasOwnProperty(name)) {
                                Vue.set(this.page, name, 1)
                            }
                            if (res.data.length !== 0) {
                                // 获取之后把数据存起来
                                this.jfList[name] = this.jfList[name].concat(res.data)
                                // 把 page 改下
                                var nextPage = this.page[name] + 1
                                Vue.set(this.page, name, nextPage)
                            } else {
                                if (this.jfList[name].length !== 0) {
                                    this.noMore = true
                                    setTimeout(function () {
                                        this.noMore = false
                                        this.flag = false
                                    }.bind(this), 1000)
                                }
                            }
                            this.renderTrans()
                        } else {
                            this.toast.show = true
                            this.toast.text = res.msg
                        }
                        this.flag = true
                        this.more = false
                        this.loading = false
                    }).catch(function (err) {
                        this.flag = true
                        this.more = false
                        this.loading = false
                        this.toast.show = true
                        this.toast.text = '请求失败' + err
                    })
                },
                directionFn: function () {
                    var startX, startY, moveEndX, moveEndY, X, Y;
                    document.addEventListener('touchstart', function(e) {
                        startX = e.touches[0].pageX;
                        startY = e.touches[0].pageY; 
                    }.bind(this), false)
                    document.addEventListener('touchmove', function(e) {
                         moveEndX = e.changedTouches[0].pageX;
                         moveEndY = e.changedTouches[0].pageY;
                         X = moveEndX - startX; Y = moveEndY - startY;
                        if( Math.abs(Y) > Math.abs(X) && Y > 0) {
                            this.direction = false
                        } else if( Math.abs(Y) > Math.abs(X) && Y < 0 ) {
                            this.direction = true
                        }
                    }.bind(this))
                },
                scrollFn: function () {
                    this.directionFn()
                    window.addEventListener('scroll', function() {
                        if (!this.flag) return
                        if ((window.pageYOffset + window.innerHeight >= document.documentElement.scrollHeight - 20) && this.direction) {
                            this.more = true
                            this.getData()
                        }
                    }.bind(this), false);
                }
            }
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>