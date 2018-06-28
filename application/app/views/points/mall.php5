<!doctype html> 
<html> 
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="format-detection" content="telephone=no"/>
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no,minimal-ui"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-title" content="2345彩票">
    <meta content="telephone=no" name="format-detection" /> 
    <meta content="email=no" name="format-detection" />
    <title>积分商城</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/jifen.min.css');?>">
</head>
<body>
    <div class="wrapper p-jifen" v-cloak>
        <div class="cp-tips" v-if="inTime && user.last_year_points !== '0'"><span>您有{{ user.last_year_points }}积分将于3月1日过期</span></div>
        <div class="user-pannel">
            <div class="user-info">
                <div class="img">
                    <img :src="user.headimgurl || '/caipiaoimg/static/images/comment-face.png'" alt="">
                </div>
                <div class="text">
                    <div class="name">{{ user.uname }}</div>
                     <a v-if='user.grade' href="javascript:;" @click="goMember()" :class="'lv' + user.grade"></a>
                </div>
            </div>
            <h2 class="user-jifen">
                <a href="/app/points/pointList"><em>{{ user.grade_value }}</em>积分</a>
            </h2>
        </div>
        <div class="jifen-tab">
            <div class="jifen-tab-hd">
                <ul>
                    <li v-for="(i, it) in tab.list" @click="tab.curIdx = i" :class="tab.curIdx === i ? 'cur' : ''">{{ it }}</li>
                </ul>
            </div>
            <div class="jifen-tab-bd">
                <ul class="tjrw" v-if="tab.curIdx === 0 && joblist.length">
                    <li :class="{'recommend': item.hot === '1'}" v-for="(index, item) in joblist">
                        <div class="img">
                            <img :src="item.imgurl" alt="">
                        </div>
                        <div class="text">
                            <h3 class="name">{{ item.title }}</h3>
                            <span class="note">{{ item.desc }}</span>
                            <div class="tips">
                                <em>奖励积分 +{{ item.awards }}  限{{ item.awardsNum }}次</em>
                            </div>
                        </div>
                        <div class="action">
                            <a v-if="item.doStatus === '0'" href="javascript:;" @click="todo(item.id)">去完成</a>
                            <button v-if="item.doStatus === '1'" @click="getPoint(item.id, index)">可领取</button>
                            <span v-if="item.doStatus === '2'">已完成</span>
                        </div>
                    </li>
                </ul>

                <ul class="jfdh" v-if="tab.curIdx === 1 && redpacklist.length">
                    <li v-for="item in redpacklist">
                        <a :href="'/app/points/redpackDetail/'+item.rid" class="jfdh-item" :class="{'disabled': item.out === 0}">
                            <div class="red-packets">
                                <b>¥{{ item.money }}</b><small>{{ item.p_name }}</small>
                            </div>
                            <h3 class="name">{{ item.money }}元彩金红包</h3>
                            <div class="desc">
                                <em>{{ item.price }}积分</em>
                                <span>剩{{ item.out }}个</span>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <toast :show.sync="toast.show" type="text" :text="toast.text" @on-hide="toast.cb" :time="toast.time"></toast>
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
                    curTime: '',
                    tab: {
                        list: ['推荐任务', '积分兑换'],
                        curIdx: <?php echo $type;?>
                    },
                    user: {},
                    joblist: [],
                    redpacklist: [],
                    toast: {
                        show: false,
                        text: '',
                        time: 2000,
                        cb: function () {}
                    }
                }
            },
            computed: {
                inTime: function () {
                    if (!this.curTime) return ''
                    var T = this.curTime
                        Y = T.getFullYear();
                    return T > new Date(Y + '/01/01') && T < new Date(Y + '/03/01')
                }
            },
            created: function () {
                this.$http.get('/app/points/getMemberInfo').then(function (res) {
                    this.curTime = new Date(<?php echo time()*1000?>);
                    var res = JSON.parse(res.data);
                    if (res.status === '200') {
                        this.user = res.data
                    } else {
                        this.toast.show = true
                        this.toast.text = res.msg
                    }
                }.bind(this)).catch(function (err) {
                    this.toast.show = true
                    this.toast.text = '加载失败，请刷新当前页面'
                })
            },
            ready: function () {
                this.$http.get('/app/points/getTaskList').then(function (res) {
                    var res = JSON.parse(res.data)
                    if (res.status === '200') {
                        this.joblist = res.data.joblist
                        this.redpacklist = res.data.redpacklist
                    } else {
                        this.toast.show = true
                        this.toast.text = res.msg
                    }
                }.bind(this)).catch(function (err) {
                    this.toast.show = true
                    this.toast.text = '加载失败，请刷新当前页面'
                })
            },
            methods: {
                getPoint: function (id, index) {
                    this.$http.post('/app/points/getPoint', {
                        'id': id
                    }, {
                        emulateJSON: true
                    }).then(function (res) {
                        var res = JSON.parse(res.data)
                        if (res.status === '200' || res.status === '0') {
                            this.toast.show = true
                            this.toast.text = res.msg
                            this.toast.time = 2000
                            this.toast.cb = function () {
                                window.location.reload()
                            }
                            this.joblist[index].doStatus = '2'
                        }else if (res.status === '300') {
                            android.relogin(location.href);
                            return;
                        }else {
                            this.toast.show = true
                            this.toast.text = res.msg
                        }
                    }.bind(this)).catch(function (err) {
                        this.toast.show = true
                        this.toast.text = '请求失败，请重试'
                    })
                },
                todo: function (id) {
                    switch(id){
                        case '1':
                            android.goMainActivity('tabHome'); 
                            break;
                        case '2':
                            bet.btnclick4NotFinish('42');
                            break;
                        case '3':
                            android.goMainActivity('tabHome'); 
                            break;
                        case '4':
                            android.goMainActivity('tabHome'); 
                            break;
                        case '5':
                            android.goMainActivity('tabUnite'); 
                            break;
                    }   
                },
                goMember: function(){
                    android.toActivity('{"action":"com.caipiao166.vipship","key1":"","key2":""}'); 
                }
            }
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>