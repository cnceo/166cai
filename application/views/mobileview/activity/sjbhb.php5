<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="author" content="weblol">
    <meta name="viewport" content="width=device-width,user-scalable=no">
    <meta name="format-detection" content="telephone=no">
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
    <?php if ($agent === 'app') {?>
    <title>全民红包大派送</title>
    <?php } else {?>
    <title>世界杯红包大派送</title>
    <?php }?>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/rprain.min.css');?>">
</head>

<body>
    <div class="wrapper rp" :class="isM ? 'hasHeader' : ''" v-cloak>
        <header class="m-header" v-if="isM">
            <h1>世界杯红包大派送</h1>
            <a href="/" class="hd-lnk-l">首页</a>
        </header>
        <div class="rp-inner" v-if="start">
            <div v-if="isBefore" class="main isBefore"></div>
            <div v-if="isEnd" class="main isEnd"></div>
            <div v-if="!isBefore && !isEnd" class="main" @click="fnStart">
                <div class="cursor"></div>
            </div>
            <div class="rule" :class="{'active': rule}">
                <div class="rule-inner">
                    <ol>
                        <li>1.同一用户活动期间仅有一次获得红包的机会，同一手机号、同一身份证号均视为同一用户；</li>
                        <li>2.获得的红包仅限世界杯期间（6.12-7.15）投注竞彩足球使用；</li>
                        <li>3.购彩或充值时可直接使用满足条件的红包，红包有效期及使用条件可在“我的红包”内查看，逾期未使用的红包将被系统收回，请及时使用；</li>
                        <li>4.活动过程中如用户通过不正当手段领取红包和彩金，166彩票网有权收回赠送、限制提现、冻结账户以及要求用户返还不正当得利。在法律允许范围内，166彩票网保留最终解释权；</li>
                        <li>5.关于活动的任何问题，请联系在线客服或拨打电话400-690-6760。</li>
                    </ol>
                    <span class="handle" @click="fnRule"><em>规则<i></i></em></span>
                </div>
                <div class="mask" @click="fnRule" v-if="rule"></div>
            </div>
            <div v-if="loadImg">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/rprain/bg-rainer.jpg');?>" v-show="false" alt="">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/rprain/redpacket.png');?>" v-show="false" alt="">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/rprain/rp-res2.png');?>" v-show="false" alt="">
                <img src="<?php echo getStaticFile('/caipiaoimg/static/images/active/rprain/rp-res3.png');?>" v-show="false" alt="">
            </div>
        </div>
        <div class="rp-inner step2" v-else>
            <div :class="['countDown', {'iOS': iOS}]" v-if="countDown"><span><b>3</b><b>2</b><b>1</b></span>猛戳屏幕抢红包</div>
            <template v-if="!countDown">
                <div class="score">
                    得分<b>{{ score }}</b>
                </div>
                <div class="countDown2" :class="{'run': cdRun}">
                    <div class="process">
                        <div class="inner"></div>
                    </div>
                    <em>{{ cd }}S</em>
                </div>
                <div :class="['rp-package', {'iOS': iOS}]"></div>
            </template>
        </div>

        <div class="pop res1" v-if="res1 && !has">
            <div class="pop-inner" :class="{'into': res1 && !has}">
                <div class="pop-hd">哇！得到了{{ score }}分</div>
                <a href="javascript:" class="open" @click="open">拆</a>
            </div>
        </div>

        <div class="pop res2" v-if="res1 && has">
            <div class="pop-inner" :class="{'into': res1 && has}">
                <div class="pop-hd">
                    <u>恭喜您</u>
                    <p><span><strong>得到{{ score }}分</strong></span><span><s>您已领取过红包</s></span></p>
                </div>
                <a href="javascript:" class="btn-view" @click="goRp">查看红包</a>
                <a href="javascript:" class="link-agin" @click="agin">再玩一次</a>
            </div>
        </div>

        <div class="pop res2" v-if="res2">
            <div class="pop-inner" :class="{'into': res2}">
                <div class="pop-hd">
                    <u>恭喜您获得</u>
                    <p><span>¥<b>{{ rpmoney }}</b></span><span>{{ rpcontent }}</span></p>
                </div>
                <a href="javascript:" class="btn-view" @click="goRp">查看红包</a>
                <a href="javascript:" class="link-agin" @click="agin">再玩一次</a>
            </div>
        </div>

        <div class="pop res3" v-if="res3">
            <div class="pop-inner" :class="{'into': res3}">
                <div class="pop-hd">
                    <u>很遗憾</u>
                    <p>没有获得红包</p>
                </div>
                <a href="javascript:" class="btn-view" @click="agin">再玩一次</a>
            </div>
        </div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.min.js');?>"></script>
    <script>
    !function(e,t,n,g,i){e[i]=e[i]||function(){(e[i].q=e[i].q||[]).push(arguments)},n=t.createElement("script"),tag=t.getElementsByTagName("script")[0],n.async=1,n.src=('https:'==document.location.protocol?'https://':'http://')+g,tag.parentNode.insertBefore(n,tag)}(window,document,"script","assets.growingio.com/2.1/gio.js","gio");
    gio('init','8d4b2106242d6858', {});
        new Vue({
            el: '.wrapper',
            data: function () {
                return {
                    rule: false,
                    score: 0,
                    cd: 10,
                    start: true,
                    countDown: true,
                    cdRun: false,
                    randomRp: false,
                    timer: null,
                    has: <?php echo (int)$has?>,
                    res1: false,
                    res2: false,
                    res3: false,
                    loadImg: false,
                    isM: false,
                    isBefore: <?php echo (int)$isbefore?>,
                    isEnd: <?php echo (int)$isend?>,
                    channel: 0,
                    rpmoney: 0,
                    rpcontent: ''
                }
            },
            computed: {
                iOS: function () {
                    return /(iPhone|iPad|iPod|iOS)/i.test(navigator.userAgent)
                }
            },
            watch: {
            	randomRp: function (val) {
                    if (val) {
                        if (this.cd <= 0) return
                        this.createdEl()
                        this.timer = setInterval(function () {
                            this.createdEl()
                        }.bind(this), 280)
                    } else {
                        clearInterval(this.timer)
                        if (this.score) {
                            this.res1 = true
                        } else {
                            this.res3 = true
                        }
                    }
                },
                countDown: function (val) {
                    if (!val) {
                        this.bindClick()
                    }
                }
            },
            created: function () {
                document.addEventListener('touchmove', function (e) {
                    e.preventDefault()
                })
                try{
                    this.channel = android.getAppChannel();
                }catch(e){
                }
            },
            ready: function () {
                this.loadImg = true
            },
            methods: {
                open: function () {
                    var self = this;
                    this.res1 = false
                    if (this.score > 0) {
                    	this.$http.post('/<?php echo $agent?>/activity/getwcredpack', {channel:this.channel},{
                            headers: {"X-Requested-With": "XMLHttpRequest"},
                            emulateJSON: true
                        }).then(function (res) {
                        	var json = JSON.parse(res.data);
                        	if (json.status == '100') {
                        		var backUrl = window.location.href, ua = navigator.userAgent.toLowerCase();
    							if (ua.indexOf('android') >= 0 && ua.indexOf('2345caipiao')) android.relogin(backUrl);
    							else window.webkit.messageHandlers.relogin.postMessage({url:backUrl});
                        	} else if (json.status == '200') {
                        		self.res2 = true;
                        		self.rpmoney = json.data[0];
                        		self.rpcontent = json.data[1];
                        	} else if (json.status == '500') {
                            	self.res1 = true;
                            	self.has = true;
                        	} else {
                        		$.tips({
                                    content:json.msg,
                                    stayTime:2000
                                });
                        	}
                        }).catch(function (err) {
                        })
                    } else {
                    	self.res3 = true
                    }
                },
                agin: function () {
                    this.start = true
                    this.res1 = this.res2 = this.res3 = false
                    this.reset()
                    setTimeout(function () {
                        this.run()
                    }.bind(this), 10)
                },
                reset: function () {
                    this.start = false
                    this.cdRun = false
                    this.score = 0
                    this.cd = 10
                },
                fnStart: function () {
                	<?php if ($this->uid) {?>
                    this.reset()
                    this.countDown = true
                    setTimeout(function () {
                        this.countDown = false
                        this.run()
                    }.bind(this), 3000)
                    <?php } else {?>
                    var backUrl = window.location.href, ua = navigator.userAgent.toLowerCase();
					if (ua.indexOf('android') >= 0 && ua.indexOf('2345caipiao')) android.relogin(backUrl);
					else window.webkit.messageHandlers.relogin.postMessage({url:backUrl});
                    <?php }?>
                },
                run: function () {
                    this.fnCd()
                    this.randomRp = true
                    this.cdRun = true
                },
                createdEl: function () {
                    var elRp = document.createElement('span'),
                        random = Math.floor(Math.random() * 100)
                    elRp.innerHTML = '<i></i><i></i><em>+10</em>'
                    elRp.className = 'irp'
                    if (random > 50) {
                        elRp.style.right = 100 - random + '%'
                    } else {
                        elRp.style.left = random + '%'
                    }
                    elRp.style.animationDuration = 2 - random / 100 + 's'
                    document.querySelector('.rp-package').appendChild(elRp)
                },
                fnCd: function () {
                    setTimeout(function () {
                        this.cd--
                        if (this.cd) {
                            this.fnCd()
                        } else {
                            this.randomRp = false
                        }
                    }.bind(this), 1000)
                },
                goRp: function() {
                	var ua = navigator.userAgent.toLowerCase()
                	if (ua.indexOf('android') >= 0 && ua.indexOf('2345caipiao')) location.href = '/app/redpack/index/<?php echo $token?>';
                	else location.href = '/ios/redpack/index/<?php echo $token?>';
                },
                bindClick: function () {
                    document.querySelector('.rp-package').addEventListener('touchstart', function (e) {
                        var el = e.target
                        if (el.nodeName.toLowerCase() === 'span') {
                            el.className += " selected"
                            if (this.iOS) {
                                var curTransform = window.getComputedStyle(el).transform, curTop = window.getComputedStyle(el).top
                                el.style.webkitTransform = curTransform
                                el.style.transform = curTransform
                                el.style.top = curTop
                                el.classList.add('remove-animation')
                            }
                            this.score += 10
                            setTimeout(function () {
                                el.remove()
                            }, 1300)
                        }
                    }.bind(this))
                },
                fnRule: function () {
                    this.rule = !this.rule
                }
            }
        })
        gio('send');
    </script>
    <script>
        !function(e,t,n,g,i){e[i]=e[i]||function(){(e[i].q=e[i].q||[]).push(arguments)},n=t.createElement("script"),tag=t.getElementsByTagName("script")[0],n.async=1,n.src=('https:'==document.location.protocol?'https://':'http://')+g,tag.parentNode.insertBefore(n,tag)}(window,document,"script","assets.growingio.com/2.1/gio.js","gio");
        gio('init','8d4b2106242d6858', {});
        //custom page code begin here
        //custom page code end here
        gio('send');
    </script>
</body>

</html>