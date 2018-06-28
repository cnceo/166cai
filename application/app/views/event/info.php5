<html>
<head>
<meta charset="utf-8">
<meta name="author" content="weblol">
<meta name="format-detection" content="telephone=no" />
<meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
<meta name="apple-mobile-web-app-title" content="166彩票">
<meta content="telephone=no" name="format-detection" />
<meta content="email=no" name="format-detection" />
<title>活动中心</title>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/ac-list.min.css');?>">
<script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js'); ?>"></script>
<?php $this->load->view('comm/baidu'); ?>
<?php $this->load->view('mobileview/common/tongji'); ?>
</head>
<body>
    <div class="wrapper ac-list" id="acList" v-cloak>
        <!--<span>暂无活动</span>-->
        <ul>
            <li v-for="item in list">
                <a :href="item.lid > 0 ? 'javascript:;' : item.url" :class="{'expire': item.expire}" @click="doBet(item)">
                    <div class="ac-hd">
                        <h2 class="title">{{ item.title }}</h2>
                        <time class="time">{{ item.time }}</time>
                    </div>
                    <div class="ac-bd">
                        <img src="{{ item.imgUrl }}" alt="">
                    </div>
                </a>
            </li>
        </ul>
        <cp-spin :show.sync="loading"></cp-spin>
        <div v-if="tips" style="text-align: center; font-size: 1rem;">{{ tipsTxt }}</div>
        <toast :show.sync="toast.show" :text="toast.text"></toast>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js');?>"></script>
    <script>
    var acList = new Vue({
        el: '#acList',
        data: function () {
            return {
                list: [],
                loading: false,
                flag: true,
                scroll: false,
                tips: false,
                showNoDate: false,
                tipsTxt: '加载中...',
                toast: {
                    text: '',
                    show: false
                },
                api: {
                    getHistory: '/app/event/getEventInfo'
                },
                page: {
                    size: 10,
                    current: 1
                }
            }
        },
        ready: function() {
            this.getData();
        },
        computed: {
            getUrl: function () {
                return this.api.getHistory + '?number=' + this.page.size + '&page=' + this.page.current;
            },
        },
        methods: {
            getData: function() {
                this.$http.get(this.getUrl, {
                    timeout: 4000,
                    before: function (request) {
                        this.loading = true;
                    }
                }).then(function(res) {
                    res = JSON.parse(res.data);
                    this.loading = false;
                    if (res.status === '200') {
                        if (res.data.length === 0) {
                            this.showNoDate = true;
                            this.flag = false;
                            return;
                        }
                        this.list = this.list.concat(res.data);
                        this.scroll = true;
                        if (res.data.length < this.page.size) return;
                        this.page.current++;
                        this.scrollFn();
                    } else {
                        this.toast.text = err.msg
                        this.toast.show = true;
                    }
                }).catch(function(error) {
                    this.loading = false;
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
                            var res = JSON.parse(res.data);
                            if (!res.data.length) {
                                this.tipsTxt = '没有更多了';
                                setTimeout(function () {
                                    this.tips = false;
                                }.bind(this), 1000)
                                return;
                            }
                            this.list = this.list.concat(res.data);
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
            },
            doBet: function (item) {
                if(item.lid > 0){
                    bet.btnclick4NotFinish(item.lid);
                }                
            },
        }
    })
    </script>
</body>

</html>
