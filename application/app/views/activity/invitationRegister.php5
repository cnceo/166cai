<!doctype html>
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
  <title>新用户专享166元红包</title>
  <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/sjbyqhy.min.css')?>">
  <?php $this->load->view('comm/baidu'); ?>
</head>
<body ontouchstart="">
<div class="wrap web" id="app" v-cloak>
      <div class="web-box">
        <template v-if="resData.status == 0 || resData.status == 100">
          <validator name="validator">
          <div class="join-form">
            <div :class="'join-form-item' + (join ? ' tac': '')">
              <input id="phone" @focus="push" @blur="pull" v-model="ajaxData.phone" detect-change="off" v-validate:phone="{required: {rule: true, message: '手机号码不能为空'}, phone: {rule: true, message: '手机号码格式不正确'}}" type="tel" placeholder="请输入您的手机号" maxlength="11" initial="off">
            </div>
            <div class="join-form-item yzm" v-if="!join">
              <input id="imgcode" @focus="push" @blur="pull" v-model="ajaxData.imgCode" detect-change="off" v-validate:imgcode="{required: {rule: true, message: '验证码不能为空'}, minlength: {rule: 4, message: '请输入4位验证码'}}" type="tel" placeholder="输入4位验证码" maxlength="4" class="input-yzm" initial="off">
              <img :src="'/app/activity/captcha?v=' + random" @click="refresh" alt="">
              <a href="javascript:;" class="change-img" :class="rotateClass" @click="refresh(), rotate()">换一张</a>
            </div>
          </div>
          <validator name="validator">
          <a v-if="ajaxData.btn" href="javascript:;" class="btn-click-w" @click="postEvent">立即领取</a>
          <a v-else href="javascript:;" class="btn-click-w">提交中...</a>
          <div class="note">
            <p>每个手机号只能参加一次，详细规则见客户端</p>
            <p>活动最终解释权归166彩票网所有。</p>
          </div>
        </template>
        
        <template v-if="resData.status == 200">
          <div class="join-success">
            <div class="join-success-bd">
              <em>{{ resData.msg }}</em>
            </div>
          </div>
          <a href="javascript:;" @click="dlApp(['_trackEvent','appinvite', 'download', 'receive'])" class="btn-click-w">马上下载客户端使用</a>
        </template>
        
        <template v-if="resData.status == 300">
          <div class="join-success">
            <div class="join-success-bd">
              <em>{{ resData.msg }}</em>
            </div>
          </div>
          <a href="javascript:;" @click="dlApp(['_trackEvent', 'appinvite', 'download', 'fail'])" class="btn-click-w">马上下载客户端使用</a>
        </template>
      <p class="join-from">接受小伙伴<em v-if="fromName">{{ fromName }}</em>的邀请，加入166彩票。</p>
    </div>
    <toast :show.sync="toast.show" type="text" :text="toast.text"></toast>
  </div>
  <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js');?>"></script>
  <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-resource.js');?>"></script>
  <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue-validator.min.js');?>"></script>
  <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/component.min.js');?>"></script>
  <script>
    var newUser = new Vue({
      el: '#app',
      data: function () {
        return {
          dlUrl: '<?php echo $downHref;?>',
          api: {
            register: '/app/activity/invitationDoRegister'
          },
          join: <?php echo (int)($recode[0] < 15)?>, 
          random: '',
          rotateClass: '',
          resData: {
        	status: 0,
            msg: ''
          },
          ajaxData: {
          	phone: '',
            imgCaptcha: '',
            id:<?php echo $id; ?>,
            btn: true,
            channel:'0'
          },
          ajaxFlag: true,
          toast: {
            show: false,
            text: '',
          },
          formValid: [],
          isWx: false
        }
      },
      created: function () {
        this.refresh();
        try{
			this.ajaxData.channel = android.getAppChannel();
		}catch(e){
			this.ajaxData.channel = '0';
		}
      },
      computed: {
        money: function () {
          return this.peopleNum * this.baseMoney
        },
        fromName: function () {
          var name = '<?php echo $from['uname'];?>';
          return name.length > 4 ? name.slice(0, 3) + '...' : name
        },
        validatorRes: function () {
          return this.join ? (this.$validator.phone.required || this.$validator.phone.phone) : (this.$validator.phone.required || this.$validator.phone.phone|| this.$validator.imgcode.required || this.$validator.imgcode.minlength)
        }
      },
      methods: {
        getQueryString: function (name) {
          var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
          var r = window.location.search.substr(1).match(reg);
          if (r!=null) return decodeURI(r[2]);
          return null;
        },
        refresh: function () {
          this.random = Math.random();
        },
        rotate: function () {
          this.rotateClass = "change-rotate";
          setTimeout(function () {
            this.rotateClass = '';
          }.bind(this), 400)
        },
        
        postEvent: function () {
       	  _hmt.push(['_trackEvent', 'appclick', 'appcent']);
          var _this = this;
          var validatorRes =  this.validatorRes;
          var validatorModified = this.join ? this.$validator.phone.modified : (this.$validator.phone.modified && this.$validator.imgcode.modified);
          if (validatorModified) {
            if (this.ajaxFlag && validatorRes === false) {
              this.$http.post(this.api.register, this.ajaxData, {
                emulateJSON: true,
                before: function (request) {
                  _this.ajaxFlag = _this.ajaxData.btn = false
                }
              }).then(function (res) {
                if (typeof res.data === 'string') {
                  _this.resData = JSON.parse(res.data)
                } else {
                  _this.resData = res.data
                }
                _this.ajaxFlag = _this.ajaxData.btn = true;
                if(_this.resData.status==100){
                    this.refresh();
                    this.rotate();
                    this.toast.show = true;
                    this.toast.text = _this.resData.msg
                }
              }).catch(function (err) {
                this.toast.show = true;
                this.toast.text = err.msg
                _this.ajaxFlag = _this.ajaxData.btn = true;
              })
            } else {
              this.toast.show = true;           
              this.toast.text = validatorRes;
            }
          } else {
        	  if (!this.$validator.phone.modified) {
                  this.toast.text = '请先填写手机号码';
            } else {
                  this.toast.text = '请先填写验证码';
            }
            this.toast.show = true;           
          }
        },
        dlApp: function (arr) {
          _hmt.push(arr);
          window.location.href = this.dlUrl
        },
        push: function (e) {
          if(/(Android|Adr)/i.test(navigator.userAgent)) {
            var web = document.querySelector('.web');
            web.style.paddingBottom = '120px'
          }
        },
        pull: function () {
          if(/(Android|Adr)/i.test(navigator.userAgent)) {
            var web = document.querySelector('.web');
            web.style.paddingBottom = '0'
          }
        }
      }
    })
  </script>
  <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js');?>"></script>
  <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>
