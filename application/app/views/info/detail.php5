<!doctype html>
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
<title><?php echo $category; ?></title>
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
<link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/msg.min.css');?>">
<?php $this->load->view('comm/baidu'); ?>
<?php $this->load->view('mobileview/common/tongji'); ?>
</head>
<body>
	<div class="wrapper msg-detail txt-bg">
        <article class="news-bet">
            <header class="msg-detail-hd">
                <h1><?php echo $title; ?></h1>
                <p><time><?php echo $date; ?></time><span class="msg-source">阅读 <?php echo $num; ?></span></p>
            </header>
            <section class="msg-detail-bd">
                <?php echo htmlspecialchars_decode($content); ?>
            </section>
            <?php if($lid): ?>
            <footer>
                <a href="javascript:" class="btn btn-plain-confirm" onclick="bet.btnclick('<?php echo $lid;?>', '<?php echo $enName;?>');">立即投注</a>
            </footer>
            <?php endif; ?>
            <?php if ($android && $additions == 1) :?>
            <footer>
                <a href="javascript:" class="btn btn-plain-confirm" onclick="share();">分享好友</a>
            </footer>
            <?php endif;?>
        </article>
        <?php if (!$android && $additions == 1) :?>
        <div class="fixed-bottombar">
        	<a href="javascript:;" onclick="javascript:location.href = '/app/download';">
            	<div class="title"><img src="<?php echo getStaticFile('/caipiaoimg/static/images/app-icon.png');?>" alt="">手机客户端</div>
                <button type="button" class="btn btn-confirm">立即下载</button>
            </a>
            <span class="close"></span>
        </div>
        <?php endif;?>
        <section class="comment-box" id="comment" v-cloak>
            <header class="comment-hd sticky">
                <h1 class="comment-title">全部评论</h1>
            </header>
            <div class="comment-bd">
                <ul v-if="scroll">
                    <li v-for="(idx, item) in list" class="comment-list-item">
                        <div class="comment-item">
                            <div class="user-info">
                                <div class="user-face" :class="item.isAdmin ? 'vip' : ''">
                                    <img v-if="item.isAdmin" src="<?php echo getStaticFile('/caipiaoimg/static/images/comment-face-admin.png'); ?>" alt="">
                                    <img v-else :src="item.headimgurl?item.headimgurl:'<?php echo getStaticFile('/caipiaoimg/static/images/comment-face.png'); ?>'" alt="">
                                </div>
                                <div class="user-name">
                                    <em class="user-name">{{ item.uname }}</em>
                                    <span :class="'lv' + item.lv"></span>
                                </div>
                                <div class="small"><small v-if="item.floor > 0">{{ item.floor }}楼</small><time>{{ item.date }}</time></div>
                                <span class="btn-reply" v-if="item.checked == 1" @click="replyForNative(item.uname, item.commentId, idx)">回复</span>
                            </div>
                            <div class="user-txt">
                                <p v-html="item.content"></p>
                            </div>
                        </div>
                        <div class="reply-item" v-if="item.reply.status == 1">
                            <div class="user-info">
                                <div class="user-name">
                                    <em class="user-name">{{ item.reply.uname }}</em>
                                    <span :class="'lv' + item.reply.lv"></span>
                                </div>
                                <span class="btn-reply" v-if="item.reply.checked == 1" @click="replyForNative(item.reply.uname, item.reply.commentId, idx)">回复</span>
                            </div>
                            <div class="user-txt">
                                <p v-html="item.reply.content"></p>
                            </div>
                        </div>
                        <div class="reply-item" v-if="item.reply.status == 2">
                            <div class="user-txt">
                                <p>该评论已被删除</p>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="comment-none" v-else>
                    <div class="comment-none-img">
                        <img src="<?php echo getStaticFile('/caipiaoimg/static/images/comment-none.png'); ?>" alt="">
                    </div>
                    <p>暂无评论，快抢沙发！</p>
                </div>
                <div v-if="list.length && tips" class="comment-tips">{{ tipsTxt }}</div>
            </div>
            <toast :show.sync="toast.show" type="text" :text="toast.text"></toast>
        </section>
        <div class="fixed-bottombar-hold"></div>
        <?php if(!empty($banner)): ?>
	    <aside class="img2active mini">
			<a href="javascript:;" class="img-link">
				<img src="<?php echo $banner['imgUrl']; ?>" alt="">
			</a>
		</aside>
		<?php endif;?>
    </div>

    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js'); ?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js');?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/stickyfill.min.js');?>"></script>

    <script>
        var comment = new Vue({
            el: '#comment',
            data: function () {
                return {
                    artId: '<?php echo $id; ?>',
                    likeNum: '<?php echo $likeNum; ?>',
                    comNum: '<?php echo $comNum; ?>',
                    isUserLike: '<?php echo $isUserLike; ?>',
                    getComment: {
                        api: '/app/info/getComment',
                        page: 1,
                        num: 10
                    },
                    postComment: {
                        api: '/app/info/postComment'
                    },
                    list: [],
                    flag: true,
                    tips: false,
                    scroll: false,
                    toast: {
                        show: false,
                        text: ''
                    },
                    tipsTxt: '加载更多',
                    imgpath: "<?php echo '//' . $this->config->item('domain') . '/caipiaoimg/static/images/emoji/'; ?>"
                }
            },
            created: function () {
                this.$http.get(this.getComment.api + '?id=' + this.artId + '&page=' + this.getComment.page + '&number=' + this.getComment.num)
                .then(function (res) {
                    var data = JSON.parse(res.data).data;
                    if (!data.length) return;
                    data = this.formatData(data)
                    this.list = this.list.concat(data);
                    this.scroll = true;
                    if (data.length < this.getComment.num) return;
                    this.getComment.page++;
                    this.scrollFn();
                }).catch(function (err) {
                    this.toast.text = err.msg
                    this.toast.show = true;
                })  
            },
            ready: function () {
                // 调用评论组件
                try{
                    android.showNewsComment(this.likeNum, this.comNum, this.isUserLike, this.artId);
                }catch(e){}
                try{
                    android.shareNews("<?php echo $title; ?>","<?php echo $cutContent; ?>");
                }catch(e){}
            },
            methods: {
                replyForNative: function (name, id, idx) {
                    try{
                        android.showUserComment(name, id, this.artId);
                    }catch(e){}

                    var el = document.querySelectorAll('.comment-list-item')[idx];
                    var top = document.documentElement.scrollTop || document.body.scrollTop;
                    window.scrollTo(0, top + el.getBoundingClientRect().top - document.querySelector('.comment-hd').offsetHeight);
                    
                },
                formatData: function (data) {
                    data.forEach(function (item) {
                        item.content = this.emoji4img(item.content)
                        if(item.reply){
                            item.reply.content = this.emoji4img(item.reply.content)
                        }
                    }.bind(this))
                    return data;
                },
                emoji4img: function  (str) {
                    var imgpath = this.imgpath;
                    var result = str,
                        pattern = /(\[.*?\])/g;
                    str.replace(pattern, function (match, p1) {
                        result = result.split(p1).join('<img class="emojione" src="' + imgpath + p1.slice(1, -1) +'.png">');
                    })
                    return result;
                },
                scrollFn: function () {
                    window.addEventListener('scroll', function() {
                        if (!this.flag) return;
                        if (window.pageYOffset + window.innerHeight >= document.documentElement.scrollHeight - 20) {
                            this.flag = false;
                            this.$http.get(this.getComment.api + '?id=' + this.artId + '&page=' + this.getComment.page + '&number=' + this.getComment.num, {
                                before: function () {
                                    this.tips = true;
                                }
                            })
                            .then(function (res) {
                                var data = JSON.parse(res.data).data;
                                if (!data.length) {
                                    this.tipsTxt = '\\(╯-╰)/  没有更多了';
                                    setTimeout(function () {
                                        this.tips = false;
                                    }.bind(this), 2000)
                                    return;
                                }
                                data = this.formatData(data)
                                this.list = this.list.concat(data);
                                this.flag = true;
                                this.tips = false;
                                this.getComment.page++;
                            }).catch(function (err) {
                                this.toast.text = '网络异常，请稍后再试'
                                this.toast.show = true;
                            })
                        }
                    }.bind(this), false);
                },
                removeLinks: function (){
                    // 去除链接 
                    document.querySelector('.msg-detail-bd').querySelectorAll('a').forEach(function (item) {
                            item.setAttribute('href','javascript:;');
                    })
                }
            }
        })

        var appAction = "<?php echo $banner['appAction']; ?>", lid = "<?php echo $banner['tlid']; ?>", 
        enName = "<?php echo $banner['enName']; ?>", webUrl = "<?php echo $banner['webUrl']; ?>";

        if (document.querySelector('.img-link')) {
        	document.querySelector('.img-link').addEventListener('click', function(){
                try{
                    // 点击事件
                    android.umengStatistic('webview_paysuccess_ad');
                }catch(e){
                    // ...
                }
                if(appAction == 'bet'){
                    bet.btnclick(lid, enName);
                }else if (appAction == 'notlogin') {
                	android.relogin(location.href);
                }else if(appAction == 'email'){
                    android.goBindEmail('');
                }else if(appAction == 'unsupport'){
                 comment.toast.text = '请前往设置页面升级至最新版本！'
                 comment.toast.show = true;
                }else if(appAction == 'ignore'){
                 comment.toast.text = '您已绑定过邮箱'
                 comment.toast.show = true;
                }else{
                    window.location.href = webUrl;
                }
            });
        }

        // sticky兼容方案
        !function () {
            var stickyElements = document.getElementsByClassName('sticky');
            for (var i = stickyElements.length - 1; i >= 0; i--) {
                Stickyfill.add(stickyElements[i]);
            }
        }()

        // requestAnimationFrame 兼容方案
        function RAF_Polyfill () {
            var lastTime = 0;
            if (!window.requestAnimationFrame) window.requestAnimationFrame = function(callback, element) {
                var currTime = new Date().getTime();
                var timeToCall = Math.max(0, 16 - (currTime - lastTime));
                var id = window.setTimeout(function() {
                    callback(currTime + timeToCall);
                }, timeToCall);
                lastTime = currTime + timeToCall;
                return id;
            };
            if (!window.cancelAnimationFrame) window.cancelAnimationFrame = function(id) {
                clearTimeout(id);
            };
        }
        
        // 滚动到评论区
        function scrollToComment () {
            var scrollComment = null;
            var elComment = document.querySelector('.comment-box');
            var windowHeight = window.innerHeight;
            RAF_Polyfill();
            
            var initScrollTop = window.pageYOffset;
            var target = elComment.offsetTop;
            var commentHeight = elComment.offsetHeight;
            if (commentHeight < windowHeight) {
                window.scrollTo(0, 10000);
                return;
            }
            function rander(target){
                target > initScrollTop ? (initScrollTop += Math.ceil((target - initScrollTop)/8)) : (initScrollTop -= Math.ceil((initScrollTop - target)/8));
                Math.abs(initScrollTop - target) < 10 && (initScrollTop = target);
                window.scrollTo(0, initScrollTop);
            }
            scrollComment = requestAnimationFrame(function(){
                cancelAnimationFrame(scrollComment)
                rander(target);
                window.scrollY !== target && requestAnimationFrame(arguments.callee);
            })
        }

        // 发送评论
        function sendComment (nativeComment) {
            var coms = JSON.parse(nativeComment)  
            comment.$http.post(comment.postComment.api, coms, {headers: { "X-Requested-With": "XMLHttpRequest"}, emulateJSON: true})
            .then(function (res) {
                var res = JSON.parse(res.data)
                if(res.status === '200'){
                    res.data.content = comment.emoji4img(res.data.content)
                    if(res.data.reply.content){
                        res.data.reply.content = comment.emoji4img(res.data.reply.content)
                    }
                    comment.list.unshift(res.data)
                    comment.scroll = true
                    setTimeout(function () {
                       scrollToComment(); 
                    }, 0)     
                    try{
                        android.sendNewsCommentSuccess();
                    }catch(e){

                    }      
                }else if(res.status === '300'){
                    try{
                        android.relogin();
                    }catch(e){
                        comment.toast.text = res.msg
                        comment.toast.show = true
                    }
                }else{
                    comment.toast.text = res.msg
                    comment.toast.show = true
                }                
            }).catch(function (err) {
                comment.toast.text = '网络异常，请稍后再试'
                comment.toast.show = true;
            })
        }

        function share () {
            android.shareSocialMedia("<?php echo $title; ?>","<?php echo $title?>…","<?php echo $imgurl?>",location.href);
        }

        function download () {
            location.href = '/app/download'
        }

        var box = document.querySelector('.fixed-bottombar'), boxHold = document.querySelector('.fixed-bottombar-hold'), parentNode = box.parentNode, close = box.querySelector('.close');
        close.addEventListener('click', function () {
        	parentNode.removeChild(box);
        	parentNode.removeChild(boxHold);
        })
    </script>
</body>
</html>
