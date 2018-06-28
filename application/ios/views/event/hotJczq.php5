<!DOCTYPE html>
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
    <title>热门单关</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/hot-single.min.css')?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js')?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/zepto.min.js')?>"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/basic.js');?>" type="text/javascript"></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/ui/tips/src/tips.js');?>"  type="text/javascript"></script>
</head>
<body ontouchstart="">
    <div class="wrap p-plus-awards" v-cloak id="plus">
        <template v-if="name">
            <div class="plus-awards-bd">
                <h2><a href="javascript:;" onclick="window.webkit.messageHandlers.doBet.postMessage({lid: '42'});" target="_blank" class="more-match">更多比赛</a>{{ items.lname }}<em>{{ items.time }}截止</em></h2>
                <div class="plus-awards-area">
                    <div class="card">
                        <ul>
                            <li v-for="item in items.odds" :class="{'selected': items.class[$index]}" @click="toggle($index)">
                                <strong v-if="$index == 0 && items.rq">{{ name[$index] }} <br> {{ items.rq }}</strong>
                                <strong v-else>{{ name[$index] }}</strong>
                                <span>{{items.type[$index]}}</span>
                                <em>赔率{{ items.odds[$index] }}</em>
                            </li>
                        </ul>
                    </div>
                    <div class="multiple">
                        <a href="javascript:;" v-for="multxt in mul.num" :class="{ 'selected': mul.class == $index }"@click="choose($index)"> {{multxt}}倍</a>
                    </div>
                    <template v-if="result.length > 0">
                        <p v-if="responseData.spf.length < 2" class="bonus-txt">预测奖金 <em>{{result[0] * mulNum * 2 | currency ''}}</em> = 2 * 赔率{{result[0]}} * {{mulNum}}倍</p>
                        <p v-else class="bonus-txt">预测奖金 <em>{{result[0] * mulNum * 2 | currency ''}} ~ {{result[result.length-1] * mulNum * 2 | currency ''}}</em></p>
                    </template>
                    <p v-else class="bonus-txt">预测奖金=2*赔率*倍数</p>
                    <p v-if="timestamp" class="tips-refresh">比赛已过期，请刷新页面</p>
                    <a href="javascript:;" v-if="result.length > 0" @click="submit" class="btn-bet">立即付款{{ 2*mulNum*classPlus }}元</a>
                    <a href="javascript:;" v-else class="btn-bet btn-disabled">请选择比赛</a>
                    <p class="tips">* 赔率以出票时为准</p>
                </div>
                <ol>
                    <li v-for="(index, item) in olList"><span>{{ index + 1 }}</span>{{{ item }}}</li>
                </ol>
            </div>
        </template>
        <template v-else>
            meiyou
        </template>
    </div>


    <script>
        var matchInfo = '<?php echo $matchInfo; ?>';
        var closeTag = true;
        var plusAwards = new Vue({
            el: '#plus',
            data: {
                items: null
            },
            created: function () {
                this.$data = JSON.parse(matchInfo)
            },
            computed: {
                mulNum: function () {
                    return this.mul.num[this.mul.class]
                },
                name: function () {
                    var arr = []
                    arr.push(this.items.hname)
                    arr.push('')
                    arr.push(this.items.aname)
                    return arr
                },
                classPlus: function () {
                    var classPlus = 0;
                    var classArr = this.items.class;
                    for(var i = 0, classL = classArr.length; i < classL; i++) {
                        classPlus += classArr[i]
                    }
                    return classPlus
                },
                result: function () {
                    var result = []
                    var type = this.items.class;
                    var odds = this.items.odds;
                    for(var i = 0, itemL = type.length; i < itemL; i++){
                        if (type[i] == 1) {
                            result.push(odds[i])
                        }
                    }
                    return result.sort(function (a, b) {
                        return a - b
                    })
                },
                responseData: function () {
                    var spf = [];
                    var mul = this.mul.num[this.mul.class]
                    var type = this.items.class;
                    for(var i = 0, itemL = type.length; i < itemL; i++){
                        if (type[i] == 1) {
                            if (i == 0) {
                                spf.push(3)
                            } else if (i == 1) {
                                spf.push(1)
                            } else {
                                spf.push(0)
                            }
                        }
                    }
                    spf = spf.join(',')
                    
                    return {
                        mid: this.items.mid,
                        spf: spf,
                        mul: mul,
                        money: 2*this.mulNum*this.classPlus
                    }
                }
            },
            methods: {
                toggle: function(i){
                    if(this.items.class[i]){
                        this.items.class.$set(i, 0)
                    }else {
                        this.items.class.$set(i, 1)
                    }
                },
                choose: function(i){
                    this.mul.class = i;
                },
                submit: function () {
                    if(closeTag){
                        closeTag = false;
                        $.ajax({
                            type: 'POST',
                            url: '/ios/event/doBetJczq',
                            data: {mid:this.responseData.mid,money:this.responseData.money,mul:this.responseData.mul,spf:this.responseData.spf},
                            dataType: "json",
                            success: function(json){
                                if(json.status == '1'){
                                    window.location.href = json.data;
                                    closeTag = true;
                                }else if(json.status == '2'){
                                    var backUrl = window.location.href;
                                    window.webkit.messageHandlers.relogin.postMessage({url:backUrl});
                                    closeTag = true;
                                }else{
                                    $.tips({
                                        content:json.msg,
                                        stayTime:2000
                                    });
                                    closeTag = true;
                                }
                            },
                            error: function(){
                                $.tips({
                                    content:'网络异常，请稍后再试',
                                    stayTime:2000
                                });
                                closeTag = true;
                            }
                        })
                    }
                }
            }
        })
    </script>
<?php $this->load->view('mobileview/common/tongji'); ?>
  </body>
</html>
