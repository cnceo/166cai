<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>

<script type="text/javascript">
    $(function() {
        var lotteryId = <?php echo $lotteryId; ?>;
        if (lotteryId != cx.Lottery.JCZQ && lotteryId != cx.Lottery.JCLQ) {
            new cx.LastAward('.last-award', {
                lotteryId: lotteryId
            });
        }
        var render = null;
        if (lotteryId == cx.Lottery.SYXW) {
            render = function(tick) {
                var time = cx.Datetime.formatTime(tick);
                var tpl = '';
                if ('hour' in time) {
                }
                if ('min' in time) {
                    if (time.min < 10) {
                        time.min = '0' + time.min;
                    }
                    tpl += time.min + ':';
                }
                if ('second' in time) {
                    if (time.second < 10) {
                        time.second = '0' + time.second;
                    }
                    tpl += time.second;
                }
                this.$countDown.html(tpl);
                if (tick <= 0) {
                    this.requestIssue();
                }
            }
        }
<?php if (empty($noIssue)): ?>
            my_issue = new cx.Issue('.issue', {
                lotteryId: lotteryId,
                render: render
            });
<?php endif; ?>
    
        var isHover = false;
        $(".lotteryTit .rule").on("mouseenter mouseleave", function(e){
            if ( e.type == "mouseenter" ){
                isHover = true;
                $(".lotteryTit .det_pop").show();
            }
            if (e.type == "mouseleave"){
                isHover = false;
                setTimeout(function(){
                    if(!isHover){
                        $(".lotteryTit .det_pop").hide();
                    }
                },500);
            }
        });
        $(".lotteryTit .det_pop").on("mouseenter mouseleave", function(e){
            if ( e.type == "mouseenter" ){
                isHover = true;
            }
            if (e.type == "mouseleave"){
                isHover = false;
                $(this).hide();
            }
        });
    });
</script>

<div class="lotteryTit issue mod-box-hd">
    <?php
    /* 帮助中心 - 配置文件 --- @Author liusijia --- start --- */
    $this->config->load('help'); //@Author liusijia
    $help_center_rule = $this->config->item('help_center_rule');
    $lottery_type = $this->config->item('lottery_type');
    /* 帮助中心 - 配置文件 --- @Author liusijia --- end --- */
    ?>
    <?php if ($lotteryId == SYXW): ?>
    <div class="lotteryTitImg lucky-syxw">
        <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
    </div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd">
                <h1>老11选5</h1>
                	<span>第<em><b class="curr-issue"></b></em>期</span><span>本期投注剩余时间：<em class="count-down"></em></span>
                	<!-- <span>本彩种于2015年2月12日22:00起<em>暂停销售</em>，恢复时间另行通知，敬请谅解</span> -->
            </div>
            <p class="lottery-time">10分钟开奖更好玩</p>
        </div>
        <div class="fr last-award">
            <p>上一期第<em class="last-issue"></em>期     开奖结果<a href="<?php echo $baseUrl; ?>kaijiang/syxw" target="_blank" class="kj-more">更多</a></p>
            <div class="award-nums"></div>
            <div class="link-list">
                <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['syxw']; ?>" target="_blank">玩法介绍</a>
                <b class="pipe">|</b>
                <a href="/mylottery/betlog" target="_blank">投注记录</a>
                <b class="pipe">|</b>
                <a class="rule" href="javascript:;" target="_blank">中奖规则<i class="help"></i></a>
            </div>
            <div class="det_pop" style="width: 660px;">
                <div class="arr" style="margin-left: 275px;"></div>
                <table width="100%">
                    <thead>
                        <tr>
                            <th width="74">玩法</th>
                            <th width="100">开奖号码示例</th>
                            <th width="155">投注号码示例</th>
                            <th>中奖规则</th>
                            <th width="60">单注奖金</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="">任选二</td>
                            <td rowspan="12">01 02 03 04 05</td>
                            <td class="tal plr10">01 05</td>
                            <td class="tal plr10">选2个号码，猜中开奖号码任意2个数字</td>
                            <td class="pr10 cRed tar">6元</td>
                        </tr>
                        <tr>
                            <td class="">任选三</td>
                            <td class="tal plr10">01 02 04</td>
                            <td class="tal plr10">选3个号码，猜中开奖号码任意3个数字</td>
                            <td class="pr10 cRed tar">19元</td>
                        </tr>
                        <tr>
                            <td class="">任选四</td>
                            <td class="tal plr10">01 02 04 05</td>
                            <td class="tal plr10">选4个号码，猜中开奖号码任意4个数字</td>
                            <td class="pr10 cRed tar">78元</td>
                        </tr>
                        <tr>
                            <td class="">任选五</td>
                            <td class="tal plr10">01 02 03 04 05</td>
                            <td class="tal plr10">选5个号码，猜中开奖号码的全部5个数字</td>
                            <td class="pr10 cRed tar">540元</td>
                        </tr>
                        <tr>
                            <td class="">任选六</td>
                            <td class="tal plr10">01 02 03 04 05 06</td>
                            <td class="tal plr10">选6个号码，猜中开奖号码的全部5个数字</td>
                            <td class="pr10 cRed tar">90元</td>
                        </tr>
                        <tr>
                            <td class="">任选七</td>
                            <td class="tal plr10">01 02 03 04 05 06 07</td>
                            <td class="tal plr10">选7个号码，猜中开奖号码的全部5个数字</td>
                            <td class="pr10 cRed tar">26元</td>
                        </tr>
                        <tr>
                            <td class="">任选八</td>
                            <td class="tal plr10">01 02 03 04 05 06 07 08</td>
                            <td class="tal plr10">选8个号码，猜中开奖号码的全部5个数字</td>
                            <td class="pr10 cRed tar">9元</td>
                        </tr>
                        <tr>
                            <td class="">直选前一</td>
                            <td class="tal plr10">01</td>
                            <td class="tal plr10">选1个号码，猜中开奖号码第1个数字</td>
                            <td class="pr10 cRed tar">13元</td>
                        </tr>
                        <tr>
                            <td class="">直选前二</td>
                            <td class="tal plr10">01 02</td>
                            <td class="tal plr10">选2个号码与开奖的前2个号码相同且顺序一致</td>
                            <td class="pr10 cRed tar">130元</td>
                        </tr>
                        <tr>
                            <td class="">组选前二</td>
                            <td class="tal plr10">02 01</td>
                            <td class="tal plr10">选2个号码与开奖的前2个号码相同</td>
                            <td class="pr10 cRed tar">65元</td>
                        </tr>
                        <tr>
                            <td class="">直选前三</td>
                            <td class="tal plr10">01 02 03</td>
                            <td class="tal plr10">选3个号码与开奖的前3个号码相同且顺序一致</td>
                            <td class="pr10 cRed tar">1170元</td>
                        </tr>
                        <tr>
                            <td class="">组选前三</td>
                            <td class="tal plr10">01 02 03</td>
                            <td class="tal plr10">选3个号码与开奖的前3个号码相同</td>
                            <td class="pr10 cRed tar">195元</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif ($lotteryId == JCZQ): ?>
    <div class="lotteryTitImg lucky-jczq">
        <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
    </div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd"><h1>竞彩足球</h1><span>竞猜对象为全场90分钟（含伤停补时）的比分结果，不含加时赛及点球大战</span><span>销售截止：赛前<em><?php echo $lotteryConfig[JCZQ]['ahead'];?></em>分钟</span></div>
        </div>

        <div class="fr last-award">
            <div class="link-list">
                <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['jczq']; ?>" target="_blank">玩法介绍</a>
                <b class="pipe">|</b>
                <a href="/mylottery/betlog" target="_blank">投注记录</a>
                <!-- <b class="pipe">|</b>
                <a class="rule" href="javascript:void(0);" target="_blank">中奖规则<i class="help"></i> --></a>
            </div>
        </div>
    <?php elseif ($lotteryId == BJDC): ?>
    <div class="lotteryTitImg">
        <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/indexBJDC.gif');?>" alt="北单" width="72" height="72" />
    </div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd"><h1>北单</h1><span><?php echo date('Y年m月d日'); ?></span><span>英超 德甲 意甲 西甲 法甲</span></div>
            <p>含90分钟＋伤停补时，不含加时赛和点球大战</p>
        </div>
    <?php elseif ($lotteryId == SFC): ?>
    <div class="lotteryTitImg lucky-sfc">
        <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
    </div>
    <div class="lotteryTitName">
      <div class="lotteryTitName-hd"><h1>胜负彩</h1><span>第<em class="curr-issue"></em>期</span><span>本期投注截止时间：<em><b class="end-time">7</b>（<b class="week-day"></b>）</em></span></div>
    </div>
    <div class="fr last-award">
        <p>上一期第<em class="last-issue"></em>期     开奖结果<a href="<?php echo $baseUrl; ?>kaijiang/sfc" target="_blank" class="kj-more">详情</a></p>
        <div class="award-nums award-nums-nobg"></div>
        <div class="link-list">
            <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['sfc']; ?>" target="_blank">玩法介绍</a>
            <b class="pipe">|</b>
            <a href="/mylottery/betlog" target="_blank">投注记录</a>
            <b class="pipe">|</b>
            <a class="rule" href="javascript:void(0);" target="_blank">中奖规则<i class="help"></i></a>
        </div>
        <div class="det_pop" style="width: 500px;">
            <div class="arr" style="margin-left: 150px;"></div>
            <table width="100%">
                <thead>
                    <tr>
                        <th width="70">奖级</th>
                        <th width="160">中奖条件</th>
                        <th>单注奖金</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>一等奖</td>
                        <td>14场比赛胜平负结果全中</td>
                        <td>奖金总额的70%与奖池奖金之和除以中奖注数</td>
                    </tr>
                    <tr>
                        <td>二等奖</td>
                        <td>中任意13场比赛胜平负结果</td>
                        <td>奖金总额的30%除以中奖注数</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="tal plr10"><em class="cRed">注：</em>1、单注奖金封顶500万元</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php elseif ($lotteryId == RJ): ?>
    <div class="lotteryTitImg lucky-rj">
        <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
    </div>
    <div class="lotteryTitName">
      <div class="lotteryTitName-hd"><h1>任选9</h1><span>第<em class="curr-issue"></em>期</span><span>本期投注截止时间：<em><b class="end-time">7</b>（<b class="week-day"></b>）</em></span></div>
    </div>
    <div class="fr last-award">
        <p>上一期第<em class="last-issue"></em>期     开奖结果<a href="<?php echo $baseUrl; ?>kaijiang/rj" target="_blank" class="kj-more">详情</a></p>
        <div class="award-nums award-nums-nobg"></div>
        <div class="link-list">
            <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['rj']; ?>" target="_blank">玩法介绍</a>
            <b class="pipe">|</b>
            <a href="/mylottery/betlog" target="_blank">投注记录</a>
            <b class="pipe">|</b>
            <a class="rule" href="javascript:void(0);" target="_blank">中奖规则<i class="help"></i></a>
        </div>
        <div class="det_pop" style="width: 500px;">
            <div class="arr" style="margin-left: 150px;"></div>
            <table width="100%">
                <thead>
                    <tr>
                        <th width="70">奖级</th>
                        <th width="160">中奖条件</th>
                        <th>单注奖金</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>一等奖</td>
                        <td>9场比赛胜平负结果全中</td>
                        <td>奖金总额的100%与奖池奖金之和除以中奖注数</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="tal plr10"><em class="cRed">注：</em>1、单注奖金封顶500万元</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <?php elseif ($lotteryId == JCLQ): ?>
    <div class="lotteryTitImg lucky-jclq">
        <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
    </div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd"><h1>竞彩篮球</h1><span>69%超高返奖率，竞猜全场结果（包含加时赛）</span><span>销售截止：赛前<em><?php echo $lotteryConfig[JCLQ]['ahead'];?></em>分钟</span></div>
        </div>
        <div class="fr last-award">
            <div class="link-list">
                <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['jclq']; ?>" target="_blank">玩法介绍</a>
                <b class="pipe">|</b>
                <a href="/mylottery/betlog" target="_blank">投注记录</a>
                <!-- <b class="pipe">|</b>
                <a class="rule" href="javascript:void(0);" target="_blank">中奖规则<i class="help"></i> --></a>
            </div>
        </div>
    <?php elseif ($lotteryId == SSQ): ?>
    <div class="lotteryTitImg lucky-ssq">
        <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
    </div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd"><h1><?php echo $cnName; ?></h1><span>第<em class="curr-issue"></em>期</span><span>本期投注截止时间：<em><b class="end-time"></b>（<b class="week-day"></b>）</em></span></div>
            <p class="lottery-time">每周二、四、日晚21:30开奖</p>
        </div>
        <div class="fr last-award">
            <p>上一期第<em class="last-issue"></em>期     开奖结果<a href="<?php echo $baseUrl; ?>kaijiang/ssq" target="_blank" class="kj-more">详情</a></p>
            <div class="award-nums"></div>
            <div class="link-list">
                <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['ssq']; ?>" target="_blank">玩法介绍</a>
                <b class="pipe">|</b>
                <a href="/mylottery/betlog" target="_blank">投注记录</a>
                <b class="pipe">|</b>
                <a class="rule" href="javascript:void(0);" target="_blank">中奖规则<i class="help"></i></a>
            </div>
            <div class="det_pop">
                <div class="arr" style="margin-left:24px;"></div>
                <table width="100%">
                    <thead>
                        <tr>
                            <th width="44">奖级</th>
                            <th width="116">中奖条件</th>
                            <th width="70">奖金分配</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>一等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                            <td>浮动</td>
                        </tr>
                        <tr>
                            <td>二等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                </div>
                            </td>
                            <td>浮动</td>
                        </tr>
                        <tr>
                            <td>三等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                            <td>3000元</td>
                        </tr>
                        <tr>
                            <td rowspan="2">四等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                </div>
                            </td>
                            <td rowspan="2">200元</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="2">五等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                </div>
                            </td>
                            <td rowspan="2">10元</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="3">六等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                            <td rowspan="3">5元</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif ($lotteryId == DLT): ?>
        <div class="lotteryTitImg lucky-dlt">
            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
        </div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd"><h1><?php echo $cnName; ?></h1><span>第<em><b class="curr-issue"></b></em>期</span><span>本期投注截止时间：<em><b class="end-time"></b>（<b class="week-day"></b>）</em></span></div>
            <p class="lottery-time">每周一、三、六晚20:30开奖</p>
        </div>
        <div class="fr last-award">
            <p>上一期第<em class="last-issue"></em>期     开奖结果<a href="<?php echo $baseUrl; ?>kaijiang/dlt" target="_blank" class="kj-more">详情</a></p>
            <div class="award-nums"></div>
            <div class="link-list">
                <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['dlt']; ?>" target="_blank">玩法介绍</a>
                <b class="pipe">|</b>
                <a href="/mylottery/betlog" target="_blank">投注记录</a>
                <b class="pipe">|</b>
                <a class="rule" href="javascript:void(0);" target="_blank">中奖规则<i class="help"></i></a>
            </div>
            <div class="det_pop">
                <div class="arr" style="margin-left: 23px;"></div>
                <table width="100%">
                    <thead>
                        <tr>
                            <th width="44">奖级</th>
                            <th width="116">中奖条件</th>
                            <th width="70">奖金分配</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>一等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                            <td>浮动</td>
                        </tr>
                        <tr>
                            <td>二等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                            <td>浮动</td>
                        </tr>
                        <tr>
                            <td rowspan="2">三等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                </div>
                            </td>
                            <td rowspan="2">浮动</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="2">四等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                            <td rowspan="2"><em class="cRed">200</em>元</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="3">五等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                </div>
                            </td>
                            <td rowspan="3"><em class="cRed">10</em>元</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td rowspan="4">六等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                </div>
                            </td>
                            <td rowspan="4"><em class="cRed">5</em>元</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-blue"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif ($lotteryId == FCSD): ?>
        <div class="lotteryTitImg lucky-fcsd">
            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
        </div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd"><h1><?php echo $cnName; ?></h1><span>第<em class="curr-issue"></em>期</span><span>本期投注截止时间：<em><b class="end-time"></b>（<b class="week-day"></b>）</em></span></div>
            <p class="lottery-time">每晚20:30开奖</p>
        </div>
        <div class="fr last-award">
            <p>上一期第<em class="last-issue"></em>期     开奖结果<a href="<?php echo $baseUrl; ?>kaijiang/fc3d" target="_blank" class="kj-more">详情</a></p>
            <div class="award-nums"></div>
            <div class="link-list">
                <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['fcsd']; ?>" target="_blank">玩法介绍</a>
                <b class="pipe">|</b>
                <a href="/mylottery/betlog" target="_blank">投注记录</a>
                <b class="pipe">|</b>
                <a class="rule" href="javascript:void(0);" target="_blank">中奖规则<i class="help"></i></a>
            </div>
            <div class="det_pop">
                <div class="arr" style="margin-left: 60px;"></div>
                <table width="100%">
                    <thead>
                        <tr>
                            <th width="44">奖级</th>
                            <th width="130">中奖条件</th>
                            <th width="56">奖金分配</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>直选</td>
                            <td>与开奖号顺序一致</td>
                            <td><em class="cRed">1040</em>元</td>
                        </tr>
                        <tr>
                            <td>组选三</td>
                            <td>与开奖号一致但不定位</td>
                            <td><em class="cRed">346</em>元</td>
                        </tr>
                        <tr>
                            <td>组选六</td>
                            <td>与开奖号一致但不定位</td>
                            <td><em class="cRed">173</em>元</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif ($lotteryId == PLS): ?>
        <div class="lotteryTitImg lucky-pls">
            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
        </div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd"><h1><?php echo $cnName; ?></h1><span>第<em class="curr-issue"></em>期</span><span>本期投注截止时间：<em><b class="end-time"></b>（<b class="week-day"></b>）</em></span></div>
            <p class="lottery-time">每天20:30开奖</p>
        </div>

        <div class="fr last-award">
            <p>上一期第<em class="last-issue"></em>期     开奖结果<a href="<?php echo $baseUrl; ?>kaijiang/pl3" target="_blank" class="kj-more">详情</a></p>
            <div class="award-nums"></div>
            <div class="link-list">
                <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['pls']; ?>" target="_blank">玩法介绍</a>
                <b class="pipe">|</b>
                <a href="/mylottery/betlog" target="_blank">投注记录</a>
                <b class="pipe">|</b>
                <a class="rule" href="javascript:;" target="_blank">中奖规则<i class="help"></i></a>
            </div>
            <div class="det_pop">
                <div class="arr" style="margin-left:60px"></div>
                <table width="100%">
                    <thead>
                        <tr>
                            <th width="44">奖级</th>
                            <th width="130">中奖条件</th>
                            <th width="56">奖金分配</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>直选</td>
                            <td>与开奖号顺序一致</td>
                            <td><em class="cRed">1040</em>元</td>
                        </tr>
                        <tr>
                            <td>组选三</td>
                            <td>与开奖号一致但不定位</td>
                            <td><em class="cRed">346</em>元</td>
                        </tr>
                        <tr>
                            <td>组选六</td>
                            <td>与开奖号一致但不定位</td>
                            <td><em class="cRed">173</em>元</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif ($lotteryId == PLW): ?>
        <div class="lotteryTitImg lucky-plw">
            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
        </div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd"><h1><?php echo $cnName; ?></h1><span>第<em class="curr-issue"></em>期</span><span>本期投注截止时间：<em><b class="end-time"></b>（<b class="week-day"></b>）</em></span></div>
            <p class="lottery-time">每天20:30开奖</p>
        </div>

        <div class="fr last-award">
            <p>上一期第<em class="last-issue"></em>期     开奖结果<a href="<?php echo $baseUrl; ?>kaijiang/pl5" target="_blank" class="kj-more">详情</a></p>
            <div class="award-nums"></div>
            <div class="link-list">
                <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['plw']; ?>" target="_blank">玩法介绍</a>
                <b class="pipe">|</b>
                <a href="/mylottery/betlog" target="_blank">投注记录</a>
                <b class="pipe">|</b>
                <a class="rule" href="javascript:void(0);" target="_blank">中奖规则<i class="help"></i></a>
            </div>
            <div class="det_pop">
                <div class="arr" style="margin-left: 60px;"></div>
                <table width="100%">
                    <thead>
                        <tr>
                            <th width="44">奖级</th>
                            <th width="130">中奖条件</th>
                            <th width="56">奖金分配</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>一等奖</td>
                            <td>与开奖号顺序一致</td>
                            <td><em class="cRed">100000</em>元</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif ($lotteryId == QXC): ?>
        <div class="lotteryTitImg lucky-qxc">
            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
        </div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd"><h1><?php echo $cnName; ?></h1><span>第<em class="curr-issue"></em>期</span><span>本期投注截止时间：<em><b class="end-time"></b>（<b class="week-day"></b>）</em></span></div>
            <p class="lottery-time">每周二、五、日晚20:30开奖</p>
        </div>
        <div class="fr last-award">
            <p>上一期第<em class="last-issue"></em>期     开奖结果<a href="<?php echo $baseUrl; ?>kaijiang/qxc" target="_blank" class="kj-more">详情</a></p>
            <div class="award-nums"></div>
            <div class="link-list">
                <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['qxc']; ?>" target="_blank">玩法介绍</a>
                <b class="pipe">|</b>
                <a href="/mylottery/betlog" target="_blank">投注记录</a>
                <b class="pipe">|</b>
                <a class="rule" href="javascript:void(0);" target="_blank">中奖规则<i class="help"></i></a>
            </div>
            <div class="det_pop" style="width:300px">
                <div class="arr" style="margin-left: 62px;"></div>
                <table width="100%">
                    <thead>
                        <tr>
                            <th width="44">奖级</th>
                            <th width="116">中奖条件</th>
                            <th width="70">奖金分配</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>一等奖</td>
                            <td><p class="tal p5">投注号码与开奖号码全部相符且顺序一致</p></td>
                            <td>最高<em class="cRed">500万</em>元</td>
                        </tr>
                        <tr>
                            <td>二等奖</td>
                            <td><p class="tal p5">连续6位号码与开奖号码相同位置的连续6位号码相同</p></td>
                            <td>浮动</td>
                        </tr>
                        <tr>
                            <td>三等奖</td>
                            <td><p class="tal p5">连续5位号码与开奖号码相同位置的连续5位号码相同</p></td>
                            <td><em class="cRed">1800</em>元</td>
                        </tr>
                        <tr>
                            <td>四等奖</td>
                            <td><p class="tal p5">连续4位号码与开奖号码相同位置的连续4位号码相同</p></td>
                            <td><em class="cRed">300</em>元</td>
                        </tr>
                        <tr>
                            <td>五等奖</td>
                            <td><p class="tal p5">连续3位号码与开奖号码相同位置的连续3位号码相同</p></td>
                            <td><em class="cRed">20</em>元</td>
                        </tr>
                        <tr>
                            <td>六等奖</td>
                            <td><p class="tal p5">连续2位号码与开奖号码相同位置的连续2位号码相同</p></td>
                            <td><em class="cRed">5</em>元</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php elseif ($lotteryId == QLC): ?>
        <div class="lotteryTitImg lucky-qlc">
            <img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/sprite-xz-img.png');?>" width="320" height="240" alt="" />
        </div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd"><h1><?php echo $cnName; ?></h1><span>第<em class="curr-issue"></em>期</span><span>本期投注截止时间：<em><b class="end-time"></b>（<b class="week-day"></b>）</em></span></div>
            <p class="lottery-time">每周一、三、五晚21:15开奖</p>
            <!-- <div class="lotteryTitName-hd"><h1>七乐彩</h1><span>本彩种于2015年2月16日18:00起<em>暂停销售</em></span>，恢复时间另行通知，敬请谅解</div> -->
        </div>
        <div class="fr last-award">
            <p>上一期第<em class="last-issue"></em>期     开奖结果<a href="<?php echo $baseUrl; ?>kaijiang/qlc" target="_blank" class="kj-more">详情</a></p>
            <div class="award-nums"></div>
            <div class="link-list">
                <a href="<?php echo $baseUrl; ?>help/index/b<?php echo $help_center_rule[0]; ?>-s<?php echo $lottery_type['qlc']; ?>" target="_blank">玩法介绍</a>
                <b class="pipe">|</b>
                <a href="/mylottery/betlog" target="_blank">投注记录</a>
                <b class="pipe">|</b>
                <a class="rule" href="javascript:void(0);" target="_blank">中奖规则<i class="help"></i></a>
            </div>
            <div class="det_pop">
                <div class="arr" style="margin-left:-7px"></div>
                <table width="100%">
                    <thead>
                        <tr>
                            <th width="44">奖级</th>
                            <th width="116">中奖条件</th>
                            <th width="70">奖金分配</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>一等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                </div>
                            </td>
                            <td>浮动</td>
                        </tr>
                        <tr>
                            <td>二等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                            <td>浮动</td>
                        </tr>
                        <tr>
                            <td>三等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                </div>
                            </td>
                            <td>浮动</td>
                        </tr>
                        <tr>
                            <td>四等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                            <td>200元</td>
                        </tr>
                        <tr>
                            <td>五等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                </div>
                            </td>
                            <td>50元</td>
                        </tr>
                        <tr>
                            <td>六等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-blue"></span>
                                </div>
                            </td>
                            <td>10元</td>
                        </tr>
                        <tr>
                            <td>七等奖</td>
                            <td>
                                <div class="award-balls9">
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                    <span class="ball9 ball9-red"></span>
                                </div>
                            </td>
                            <td>5元</td>
                        </tr>
                        <tr>
                            <td colspan="3"><p class="tal p5"><span class="cRed">特别号码（蓝色球）说明：</span>特别号码仅做为二、四、六等奖的使用，即开出7个奖号后再从23个号里面随机摇出一个就是特别号。只要跟你买的7个号中的任意1个号相符，就算中特别号。</p></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="lotteryTitImg"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/img/cp-logo/cp-'.strtolower($enName).'.png');?>" alt="<?php echo $cnName; ?>" width="75" height="75" /></div>
        <div class="lotteryTitName">
            <div class="lotteryTitName-hd"><h1><?php echo $cnName; ?></h1><span>第<em class="curr-issue"></em>期</span><span>投注截止时间：<em><b class="end-time"></b>（<b class="week-day"></b>）</em></span></div>
            <p class="lottery-time">
                <?php if ($lotteryId == SSQ): ?>
                    每周二、四、日晚21:30开奖
                <?php elseif ($lotteryId == DLT): ?>
                    每周一、三、六晚20:30开奖
                <?php endif; ?>
            </p>
        </div>
        <div class="fr last-award">
            <p>上期[第<em class="last-issue"></em>期]： <span class="award-nums"></span>  <a href="<?php echo $baseUrl; ?>awards/number/<?php echo $lotteryId; ?>">更多&gt;&gt;</a></p>
        </div>
    <?php endif; ?>
</div>
