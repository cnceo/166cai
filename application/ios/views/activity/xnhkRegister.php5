<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="author" content="weblol">
  <meta name="viewport" content="width=device-width,user-scalable=no,minimal-ui">
  <meta name="format-detection" content="telephone=no, email=no">
  <meta name="apple-mobile-web-app-capable" content="yes"/>
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
  <meta name="apple-mobile-web-app-title" content="166彩票">
  <meta name="x5-orientation" content="portrait">
  <meta name="screen-orientation" content="portrait">
  <title>新春送好礼</title>
  <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/active/heka.min.css');?>">
  <?php $this->load->view('comm/baidu'); ?>
</head>
<body ontouchstart="">
  <div id="app" v-cloak>
    <div class="heka" :class="hekabg" v-if="showHK">
      <a href="javascript:" class="open" @click="open"><span><i></i></span></a>
    </div>
    <div class="wrap" v-else>
      <div class="wrap-content">
        <h1 class="banner">新春送好礼-免费领彩票</h1>
        <div class="web-box">
          <template v-if="resData.status !== '200'">
            <validator name="validator">
            <div class="join-form">
              <div :class="'join-form-item' + (join ? ' tac': '')">
                <input id="tel" @focus="push" @blur="pull" v-model="ajaxData.tel" detect-change="off" v-validate:tel="{required: {rule: true, message: '手机号码不能为空'}, tel: {rule: true, message: '手机号码格式不正确'}}" type="tel" placeholder="请输入您的手机号" maxlength="11" initial="off">
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
          
          <template v-if="resData.status === '200'">
            <div class="join-success">
              <div class="join-success-bd">
                <em>{{ resData.msg }}</em>
              </div>
            </div>
            <a href="javascript:;" @click="dlApp(), baidutj(['_trackEvent','wxhkios', 'down', 'new'])" class="btn-click-w">马上下载客户端使用</a>
          </template>
          
          <template v-if="resData.status === '300'">
            <div class="popup">
                <div class="inner" @touchmove.prevent="">
                  <div class="title">仅限新用户领取</div>
                  <p>您可以到APP分享贺卡<br>获取抽8888元大奖机会</p>
                  <span class="close" @click.prevent="resData.status = '400'">&times;</span>
                  <a href="javascript:;" @click="dlApp(), baidutj(['_trackEvent','wxhkios', 'down', 'old'])" class="btn go-share">前往分享贺卡</a>
                </div>
                <div class="mask" @touchmove.prevent=""></div>
            </div>
          </template>

          <template v-if="resData.status === '500'">
            <div class="popup">
                <div class="inner" @touchmove.prevent="">
                  <div class="title">您已领取过红包</div>
                  <p>快来开启千万大奖</p>
                  <span class="close" @click.prevent="resData.status = '400'">&times;</span>
                  <a href="javascript:;" @click="dlApp(), baidutj(['_trackEvent','wxhkios', 'down', 'old'])" class="btn go-share">下载客户端使用</a>
                </div>
                <div class="mask" @touchmove.prevent=""></div>
            </div>
          </template>
        </div>
        <!--<p class="join-from">接受小伙伴<em v-if="fromName">{{ fromName }}</em>的邀请，加入166彩票。</p>-->
      </div>
      <toast :show.sync="toast.show" type="text" :text="toast.text"></toast>
    </div>
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
          dlUrl: 'http://a.app.qq.com/o/simple.jsp?pkgname=com.caipiao166&ckey=CK1387973181886',
          dlUrl1: 'https://888.166cai.cn/app/download/?c=10267',
          api: {
            register: '/ios/activity/xnhkDoRegister'
          },
          showHK: true,
          hk: {
            cur: 0,
            list: ['1', '2', '3', '4']
          },
          join: <?php echo (int)($recode[0] < 11)?>, // 是否展示图形验证码
          random: '',
          rotateClass: '',
          resData: {
            status: '100',
            msg: ''
          },
          ajaxData: {
            tel: '',
            imgCode: '',
            btn: true
          },
          ajaxFlag: true,
          toast: {
            show: false,
            text: '',
          },
          uid: <?php echo $uid;?>,
          hkid: 0,
          openAppFlag: true
        }
      },
      created: function () {
        this.refresh();
        this.hkid = parseInt(this.getQueryString('hkid'), 10);
      },
      computed: {
        hekabg: function () {
          return 'heka-bg' + (this.hkid + 1);
        },
        hkImg: function () {
          return '/caipiaoimg/static/images/active/heka/banner' + this.hk.cur + '.png'
        },
        validatorRes: function () {
          return this.join ? (this.$validator.tel.required || this.$validator.tel.tel) : (this.$validator.tel.required || this.$validator.tel.tel|| this.$validator.imgcode.required || this.$validator.imgcode.minlength)
        }
      },
      methods: {
    	open:function (){
        	  this.showHK = !this.showHK;
        	  _hmt.push(['_trackEvent','wxhkios', 'open']);
        },
        getQueryString: function (name) {
          var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
          var r = window.location.search.substr(1).match(reg);
          if (r!=null) return decodeURI(r[2]);
          return null;
        },
        choose: function (index) {
          this.hk.cur = index;
        },
        noscroll: function (event) {
            event.preventDefault()
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
        	_hmt.push(['_trackEvent','wxhkios', 'click']);
          var _this = this;
          var validatorRes =  this.validatorRes;
          var data = {
            uid: this.uid
          };
          if (this.ajaxData.tel) {
            data.phone = this.ajaxData.tel;
          }
          if (this.ajaxData.imgCode) {
            data.imgCode = this.ajaxData.imgCode;
          }
          var validatorModified = this.join ? this.$validator.tel.modified : (this.$validator.tel.modified && this.$validator.imgcode.modified);
          if (validatorModified) {
            if (this.ajaxFlag && validatorRes === false) {
              this.$http.post(this.api.register, data, {
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
                if (_this.resData.status === '100') {
                  this.toast.show = true;           
                  this.toast.text = _this.resData.msg;
                }else if(_this.resData.status === '200'){
                	_hmt.push(['_trackEvent','wxhkios', 'click', 'get']);
                }
                _this.ajaxFlag = _this.ajaxData.btn = true;
                this.refresh();
              }).catch(function (err) {
                this.toast.show = true;
                this.toast.text = err.msg
                _this.ajaxFlag = _this.ajaxData.btn = true;
                this.refresh();
              })
            } else {
              this.toast.show = true;           
              this.toast.text = validatorRes;
            }
          } else {
            if (!this.$validator.tel.modified) {
              this.toast.text = '请先填写手机号码'
            } else {
              this.toast.text = '请先填写验证码'
            }
            this.toast.show = true;           
            
          }
        },
        baidutj: function (arr){
          _hmt.push(arr);
        },
        dlApp: function () {
        	// _hmt.push(['_trackEvent','wxhkios', 'download']);
            if (this.openAppFlag) {
                this.openAppFlag = false;
                if (/android|Android/i.test(navigator.userAgent)) {
                    if (navigator.userAgent.indexOf("MQQBrowser") > -1 || navigator.userAgent.indexOf("wv") > -1) {
                        window.location = this.dlUrl1; //跳转的APP下载链接
                        this.openAppFlag = true;
                    } else {
                        var loadDateTime = new Date();
                        var _this = this;
                        setTimeout(function() {
                            var timeOutDateTime = new Date();
                            if (timeOutDateTime - loadDateTime < 5000) {
                            	window.location = _this.dlUrl1; //跳转的APP下载链接
                            } else {
                                window.close();
                            }
                            _this.openAppFlag = true;
                        }, 2000); // 2秒后说明没拉起成功，改为去下载
                        window.location.href = 'lottery://166.app/android';
                    }
                } else {
                  window.location = this.dlUrl; //跳转的APP下载链接
                  this.openAppFlag = true;
                }
  			}
        },
        push: function () {
        //   if(/(Android|Adr)/i.test(navigator.userAgent)) {
        //     var web = document.querySelector('.web');
        //     web.style.paddingBottom = '120px'
        //   }
        },
        pull: function () {
        //   if(/(Android|Adr)/i.test(navigator.userAgent)) {
        //     var web = document.querySelector('.web');
        //     web.style.paddingBottom = '0'
        //   }
        }
      }
    })
  </script>
  <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js');?>"></script>
<?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>
