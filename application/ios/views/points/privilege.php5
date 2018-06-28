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
    <title>会员特权</title>
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/cpui.min.css');?>">
    <link rel="stylesheet" href="<?php echo getStaticFile('/caipiaoimg/static/css/layout/jifen.min.css');?>">
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/swiper-4.0.2.min.js');?>"></script>
</head>
<body>
    <div class="wrapper p-privileges" v-cloak>
        <div class="privileges-hd">
            <div class="privileges-hd-inner" v-ref:scroll>
                <div class="swiper-container">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide" v-for="(i, it) in list">
                            <div class="img"><img :src="'/caipiaoimg/static/images/icon-privilege-' + (i + 1) + '.png'" alt=""></div>
                            <span>{{ it }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="privileges-bd jf-desc-item-box">
            <div class="jf-desc-item" v-for="(k, v) in detail[curIdx]">
                <h2 class="title">{{ k }}</h2>
                <p v-if="typeof v === 'string'">{{ v }}</p>
                <p v-else v-for="it in v">{{ it }}</p>
            </div>
            <a v-if="curIdx === 2" href="/ios/points/mall?type=1" class="btn" v-if="out > 0">去兑换</a>
        </div>
    </div>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/fastclick.js');?>" type="text/javascript" ></script>
    <script src="<?php echo getStaticFile('/caipiaoimg/static/js/lib/vue.min.js');?>" type="text/javascript" ></script>
    <script>
        new Vue({
            el: '.wrapper',
            data: function () {
                return {
                    idx: <?php echo $id-1;?>,
                    curIdx: <?php echo $id-1;?>,
                    list: ['身份勋章', '提现特权', '积分兑换', '升级礼包', '生日礼包', '积分双倍'],
                    detail: [
                        {
                            '特权介绍': ['身份勋章是会员尊贵身份的象征，登录会员中心后可查看所属身份勋章。'],
                            '专享人群': '新手及以上会员'
                        },
                        {
                            '特权介绍': [
                                '会员提款免手续费，不同会员每天支持提现次数不同，具体权益如下：',
                                '新手、青铜、白银彩民每天支持3次；',
                                '黄金彩民每天支持4次；',
                                '铂金彩民每天支持5次；',
                                '钻石彩民每天支持8次。'
                            ],
                            '专享人群': '新手及以上会员'
                        },
                        {
                            '特权介绍': ['会员可通过购彩、完成任务获得积分，登录积分商城后可使用积分兑换丰厚的礼包。'],
                            '专享人群': '青铜及以上会员'
                        },
                        {
                            '特权介绍': [
                                '拥有该权益的会员，在升级时即可领取不等的升级礼包。权益如下：',
                                '青铜升至白银可得16元彩金红包；',
                                '白银升至黄金可得66元彩金红包；',
                                '黄金升至铂金可得266元彩金红包；',
                                '铂金升至钻石可得1666元彩金红包。'
                            ],
                            '专享人群': '白银及以上会员'
                        },
                        {
                            '特权介绍': [
                                '拥有该权益的会员，在生日（以实名身份信息为准）当天会获得超值满减红包，红包会于生日当天8点发放，发放后30天内有效，权益如下：',
                                '黄金：满100减20*1；',
                                '铂金：满100减20*1、满500减100*1；',
                                '钻石：满100减20*1、满500减100*1、满1000减200*1；'
                            ],
                            '专享人群': '黄金及以上会员'
                        },
                        {
                            '特权介绍': '拥有该权益的会员，购彩时可获得额外积分，每天上限5000，登录积分商城后可使用积分兑换丰厚的礼包。',
                            '专享人群': '钻石及以上会员'
                        }
                    ]
                }
            },
            ready: function () {
                var _this = this
                new Swiper('.swiper-container', {
                    slidesPerView: 'auto',
                    touchRatio: .5,
                    initialSlide: <?php echo $id-1;?>,
                    slideToClickedSlide: true,
                    spaceBetween: 6,
                    centeredSlides: true,
                    on: {
                        slideChange: function () {
                            _this.curIdx = this.activeIndex
                        }
                    },
                });
            },
            methods: {
            }
        })
    </script>
    <?php $this->load->view('mobileview/common/tongji'); ?>
</body>
</html>