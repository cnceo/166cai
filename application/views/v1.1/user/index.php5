<?php
$weekarray = array("日", "一", "二", "三", "四", "五", "六");
?>
<?php if (!$this->is_ajax) { ?>
    <link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/hemai.min.css'); ?>" rel="stylesheet" type="text/css" />
    <div class="wrap p-hemai p-hemai-uc">

        <!-- 用户信息 -->
        <div class="userIntro clearfix">

            <div class="userPhoto">
                <div class="avatar" id="J_uc-avatar">
                    <img width="80px" height="80px" src="<?php echo $user['headimgurl']?$user['headimgurl']:getStaticFile('/caipiaoimg/v1.1/img/avatar/default-avatar.png'); ?>" onerror="this.src='<?php echo $user['headimgurl']?$user['headimgurl']:getStaticFile('/caipiaoimg/v1.1/img/avatar/default-avatar.png'); ?>'" alt="">
                    <i class="iMask"></i>
                </div>
            </div>

            <div class="userMessage">
                <div class="pTit">
                    <span class="sName"><?php echo $user['uname']; ?></span>
                    <span class="level"><?php echo $user['record']; ?></span>
                </div>
                <p class="pTxt">
                        <?php if ($this->uid == $user['uid']) { ?>
                            <?php
                            if ($user['introduction_status'] == 0 || $user['introduction_status'] == 1) {
                                echo $user['introduction']?$user['introduction']:'想中大奖的，抓紧跟单啦！';
                            } else {
                                echo '想中大奖的，抓紧跟单啦！';
                            }
                            ?>
                            <a href="javascript:;" target="_self" class="editBtn"><i class="icon-font">&#xe64d;</i>编辑</a>
                        <?php }else{
                            if ($user['introduction_status'] == 1) {
                                echo $user['introduction'];
                            }else{
                                echo '想中大奖的，抓紧跟单啦！';
                            }
                        } ?>
                </p>
            </div>

            <div class="userPrize">
                <ul>
                    <li>
                        <span class="sTit">合买中奖</span>
                        <span class="sDes"><em><?php echo $sum['winningTimes']; ?></em>次</span>
                    </li>
                    <li>
                        <span class="sTit">累积奖金</span>
                        <span class="sDes"><?php if (floor($sum['bonus'] / 10000000000) > 0) { ?><em><?php echo floor($sum['bonus'] / 10000000000); ?></em>亿<?php } ?><?php if (floor($sum['bonus'] / 1000000) > 0) { ?><em><?php echo floor(($sum['bonus'] - (floor($sum['bonus'] / 10000000000) * 10000000000)) / 1000000); ?></em>万<?php } ?><em><?php if (floor($sum['bonus'] / 10000000000) <= 0) { ?><?php echo floor(($sum['bonus'] - (floor($sum['bonus'] / 1000000) * 1000000)) / 100); ?></em>元<?php } ?></span>
                    </li>
                </ul>
                <i class="iBg"></i>
            </div>

        </div>
        <!-- 用户信息 -->

        <!-- 左侧主内容 -->
        <div class="homepage-main">

            <div class="mod-tab-line"></div>
            <ul class="mod-tab-hemai">
                <li class="current"><a><i class="icon-font">&#xe64e;</i>首页</a></li>
                <li><a><i class="icon-font">&#xe64c;</i>历史战绩</a></li>
                <li><a><i class="icon-font">&#xe6bd;</i>跟单定制</a></li>
            </ul>
            <div class="mod-tab-hemai-con">

                <!-- 首页 -->
                <div class="mod-tab-item" style="display:block">
                    <div class="th">
                        <span class="sMark">Ta当前发起的合买</span>
                    </div>
                    <div class="tb user_form hemai-table" id="user_form_content">
                    <?php } ?>
                    <?php $orderArr = str_split ( $order )?>   
                    <button type="button" class="btn-ss btn-search submit hidden">搜索</button>
                    <input type="hidden" name="order" class="vcontent" value="<?php echo $order ? $order : '00'?>">
                    <table class="mod-tableA" id="mod-tableA">
                        <thead>
                            <tr>
                                <th width="6%"  class="tal">&nbsp;</th>
                                <th width="12%" class="tal">彩种</th>
                                <th width="16%" class="filter-arrow <?php if ($orderArr[0] == 0) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="0">进度+保底<i></i></th>
                                <th width="15%" class="filter-arrow <?php if ($orderArr[0] == 3) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="3">方案金额<i></i></th>
                                <th width="15%" class="filter-arrow <?php if ($orderArr[0] == 4) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="4">剩余金额<i></i></th>
                                <th width="27%" class="tal">认购金额</th>
                                <th  class="tal">操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unitedOrders as $k => $unitOrder) { 
                            $lastmoney = ParseUnit($unitOrder['money'] - $unitOrder['buyTotalMoney'], 1);?>
                                <tr <?php if ($unitOrder['ujoin']) {?>class="follow"<?php }?>>
                                    <td>
                                    <?php if ($unitOrder['isTop'] >0 ) { ?>
                                        <img src="/caipiaoimg/v1.1/img/icon-recommend.png" width="18" height="18" alt="荐">
                                    <?php } else { ?>
                                        <em class="fcw"><?php echo $k + 1; ?></em>
                                    <?php } ?>
                                    <?php if ($unitOrder['ujoin']) {?><span class="icon-follow">已跟</span><?php }?>
                                    </td>
                                    <td class="tal"><?php echo BetCnName::getCnName($unitOrder['lid']) ?></td>
                                    <td><?php echo (round($unitOrder['buyTotalMoney'] / $unitOrder['money'], 4) * 100) . '%' ?><?php if ($unitOrder['guaranteeAmount'] > 0) { ?><?php echo '+' . floor($unitOrder['guaranteeAmount'] * 100 / $unitOrder['money']) . '%'; ?><span class="icon-guaranteed">保</span><?php } ?></td>
                                    <td><?php echo round($unitOrder['money'] / 100); ?>元</td>
                                    <td><em class="main-color-s"><?php echo $lastmoney; ?></em>元</td>
                                    <td class="sPay tal"><?php if ($lastmoney > 0) { ?><input type="text" class="numInput" value="<?php echo $lastmoney > 5 ? 5 : $lastmoney?>" data-max="<?php echo $lastmoney?>">&nbsp;元&nbsp;<a href="javascript:;" class="btn btn-main btn-buy <?php echo $showBind ? ' not-bind' : ''; ?>" money="<?php echo $unitOrder['money'] ?>" orderId="<?php echo $unitOrder['orderId'] ?>" cnName="<?php echo BetCnName::getCnName($unitOrder['lid']) ?>" issue="<?php echo $unitOrder['issue'] ?>">购买</a><?php } ?></td>
                                    <td class="tal"><a href="/hemai/detail/hm<?php echo $unitOrder['orderId']; ?>" target="_blank">详情</a></td>
                                </tr>
                            <?php } ?>    
                            <?php if (empty($unitedOrders)) { ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="noData"><p>暂无记录！赶紧<a href="/hall" target="_blank">发起合买</a></p></div>
                                    </td>
                                </tr>  
                            <?php } ?>
                        </tbody>
                    </table>
                    <script>
                        var buyMoneyModifier = new cx.AdderSubtractor('.sPay');
                        var orderArr = '<?php echo isset($order) ? $order : '00'?>'.split('');
                    </script>
                    <?php if (!empty($unitedOrders)) { ?>
                        <?php echo $pages; ?> 
                    <?php } ?>
                    <!-- pagination -->
                    <!--#include file="include/pagination.htm"-->
                    <!-- pagination end -->
                    <?php if (!$this->is_ajax) { ?>
                    </div>

                    <div class="th">
                        <span class="sMark">Ta最近发起合买中奖</span>
                    </div>

                    <div class="tb">

                        <table class="mod-tableA">
                            <thead>
                                <tr>
                                    <th width="17%">时间</th>
                                    <th width="20%">彩种期次</th>
                                    <th width="12%">方案金额</th>
                                    <th width="15%">税前奖金</th>
                                    <th width="12%">回报率</th>
                                    <th width="12%">战绩奖励</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($awards as $k => $award) { ?>
                                    <tr>
                                        <td><?php echo date("m-d H:i", strtotime($award['created'])); ?>(<?php echo "星期" . $weekarray[date("w", strtotime($award['created']))]; ?>)</td>
                                        <td><?php echo BetCnName::getCnName($award['lid']) . '-' . $award['issue']; ?></td>
                                        <td><?php echo round($award['money'] / 100, 2); ?>元</td>
                                        <td><em class="main-color-s"><?php echo number_format($award['orderBonus'] / 100, 2); ?></em>元</td>
                                        <td><?php echo round($award['orderBonus'] / $award['money'], 2); ?></td>
                                        <td><span class="level"><?php echo $award['award']; ?></span></td>
                                        <td><a href="/hemai/detail/hm<?php echo $award['orderId']; ?>" class="cBlue" target="_blank">详情</a></td>
                                    </tr>
                                <?php } ?>
                                <?php if (empty($awards)) { ?>
                                    <tr>
                                        <td colspan="6">
                                            <div class="noData"><p>近1个月暂无记录！马上<a href="/hall" target="_blank">发起合买</a></p></div>
                                        </td>
                                    </tr>  
                                <?php } ?>
                            </tbody>
                        </table>

                    </div>

                </div>
                <!-- 首页 -->

                <!-- 历史战绩 -->
                <div class="mod-tab-item history">
                    <button type="button" class="btn-ss btn-search submit hidden">搜索</button>
                    <p class="pNotice"><i class="icon-font">&#xe626;</i><span id="lidName">全部彩种</span>发起合买累积中奖<em id="winCount"><?php echo $sum['winningTimes']; ?></em>次，累积奖金<strong id="winNum"><?php echo number_format($sum['bonus'] / 100, 2); ?></strong>元<span id="unitePoints">，累计战绩奖励<s class='level'><?php echo calGrade($sum['united_points'], 0, ''); ?></s></span></p>
                    <div class="tab-radio">
                        <div class="th">
                            <ul class="tab-radio-hd">
                                <li class="selected">
                                    <label><input type="radio" value="1" name="unitedStatus" checked>&nbsp;合买成功</label>
                                </li>
                                <li>
                                    <label><input type="radio" value="2" name="unitedStatus">&nbsp;合买中奖</label>
                                </li>
                            </ul>
                            <select name="lid" id="lid" class="select-list">
                                <option value="0">全部彩种</option>
                                <?php foreach (array('51' => '双色球', '23529' => '大乐透', '42' => '竞彩足球', '43' => '竞彩篮球', '19' => '胜负/任九', '52' => '福彩3D', '23528' => '七乐彩', '10022' => '七星彩', '33' => '排列三/五') as $k => $lottery) { ?>
                                    <option value="<?php echo $k; ?>"><?php echo $lottery; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="tab-radio-bd" id="history_content">

                        </div>

                    </div>
                </div>
                <!-- 历史战绩 -->
                <!--跟单定制-->
                  <div class="mod-tab-item gendan">
                        <div id="gendan_content">

                        </div>
                  </div>
              <!--跟单定制-->      
            </div>

        </div>
        <!-- 左侧主内容 -->

        <!-- 右侧栏 -->
        <div class="homepage-side">

            <!-- 合买红人 -->
            <div class="buyHotRank">
                <div class="th">合买中奖排行榜</div>
                <div class="tb">
                    <ul class="clearfix">
                        <li class="tit"><span class="sName">用户名</span><span class="sNum">累计中奖</span></li>
                        <?php if (count($planners) > 0) { ?>
                            <?php foreach ($planners as $planner) { ?>
                            <li><a target="_blank" class="lnk-dz" href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $planner['uid'])), 'ENCODE')); ?>?gendan=1">定制</a><span class="sName"><a href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $planner['uid'])), 'ENCODE')); ?>" target="_blank"><?php echo uname_cut($planner['uname'], 1, 5); ?></a></span><span class="sNum"><?php if (floor($planner['bonus'] / 10000000000) > 0) { ?><em><?php echo round($planner['bonus'] / 10000000000); ?></em>亿<?php } ?><?php echo floor($planner['bonus'] / 1000000) > 0 ? round(($planner['bonus'] - (floor($planner['bonus'] / 10000000000) * 10000000000)) / 1000000) . '万' : ''; ?></span></li>
                            <?php } ?>
                        <?php } else { ?>
                            <li class="noData"><p>暂无红人，<a href="/hall" target="_blank">发起合买</a>做红人~</p></li>
                        <?php } ?>
                    </ul>


                </div>
            </div>
            <!-- 合买红人 -->

            <!-- 合买帮助 -->
            <dl class="buyHelp">
                <dt>合买帮助</dt>
                <dd class="clearfix">
                    <span><a href="/help/index/b2-s3-f1" target="_blank">1. 什么是合买？</a></span>
                    <span><a href="/help/index/b2-s3-f3" target="_blank">2. 什么是方案保底？</a></span>
                    <span><a href="/help/index/b2-s3-f4" target="_blank">3. 如何发起合买？</a></span>
                    <span><a href="/help/index/b2-s3-f5" target="_blank">4. 如何参与合买？</a></span>
                    <span><a href="/help/index/b2-s3-f6" target="_blank">5. 合买中奖后奖金如何分配？</a></span>
                    <span><a href="/help/index/b2-s3-f7" target="_blank">6. 合买中的佣金怎么计算？</a></span>
                    <span><a href="/help/index/b2-s3-f8" target="_blank">7. 什么是方案置顶？</a></span>
                    <span><a href="/help/index/b2-s3-f11" target="_blank">8. 什么是合买战绩？</a></span>
                </dd>
            </dl>
            <!-- 合买帮助 -->
        </div>
        <!-- 右侧栏 -->

        <div class="clear"></div>

    </div>
    <div class="listgendan">
        <button type="button" class="btn-ss btn-search submit hidden">搜索</button>
        <div id="listgendan_content">

        </div>
    </div>
    <script>
        var target = '/user/<?php echo $oid; ?>';
        var gendanuid=0;
        var gendanlid=0;
        $(function () {
            getHistoricalData();
            getGendanData();
            $("input:radio[name='unitedStatus']").change(function () {
                getHistoricalData();
            });

            $("#lid").change(function () {
                getHistoricalData();
                getCount();
            });

            new cx.vform('.user_form', {
                submit: function (data) {
                    var self = this;
                    $.ajax({
                        type: 'post',
                        url: target,
                        data: data,
                        success: function (response) {
                            $('#user_form_content').html(response);
                        }
                    });
                }
            });
            
            new cx.vform('.listgendan', {
                submit: function (data) {
                    var self = this;
                    data.uid=gendanuid;
                    data.lid=gendanlid;
                    $.ajax({
                        type: 'post',
                        url: target,
                        data: data,
                        success: function (response) {
                            $('#listgendan_content').html(response);
                            cx.PopCom.show('.pop-id');
                            cx.PopCom.close('.pop-id');
                            cx.PopCom.cancel('.pop-id');
                        }
                    });
                }
            });  
            
            new cx.vform('.history', {
                submit: function (data) {
                    var self = this;
                    var lid = $("#lid").val();
                    var unitedStatus = $("input[name='unitedStatus']:checked").val();
                    var uid = '<?php echo $user['uid']; ?>';
                    $.ajax({
                        type: 'post',
                        url: target,
                        data: {unitedStatus: unitedStatus, lid: lid, uid: uid},
                        success: function (response) {
                            $('#history_content').html(response);
                        }
                    });
                }
            });
            
            new cx.vform('.gendan', {
                submit: function (data) {
                    var self = this;
                    var uid = '<?php echo $user['uid']; ?>';
                    data.uid=uid;
                    $.ajax({
                        type: 'post',
                        url: '/user/getGendanData',
                        data: data,
                        success: function (response) {
                            $('#gendan_content').html(response);
                        }
                    });
                }
            });    

            $(".mod-tab-hemai").tabPlug({
                cntSelect: '.mod-tab-hemai-con',
                menuChildSel: 'li',
                onStyle: 'current',
                cntChildSel: '.mod-tab-item',
                eventName: 'click',
                callbackFun: function () {
                    
                }
            });
            var gendan='<?php echo $_GET['gendan']; ?>';
            if(gendan)
            {
               $(".mod-tab-hemai li:eq(2)").trigger("click");                
            }
        });

        $('.user_form').on('click', '.btn-buy', function () {
            var buymoney = $(this).parents('td').find('.numInput').val(), money = $(this).attr('money'), orderId = $(this).attr('orderId'), cnName = $(this).attr('cnName'), issue = $(this).attr('issue');
            if (!$.cookie('name_ie')) {//登录过期
            	$(this).addClass('needTigger');
                cx.PopAjax.login(1);
                return;
            }
            if ($(this).hasClass('not-bind'))
                return;

            cx.castCb({orderId:orderId, buyMoney:buymoney}, {ctype:'paysearch', orderType:4, buyMoney:buymoney,msgconfirmCb:function(){$('.user_form').find('.submit').trigger('click');}});
        }).on('click blur', '.numInput', function(){
    		var $this = $(this);
            if ((/^(.*)\D+(.*)$/.test($(this).val()))) $this.val($(this).val().replace(/\D+/, ''));
            if (!$this.val()) $this.val(1);
            if ($this.data('max') && $this.val() >= parseInt($this.data('max'))) $this.val($this.data('max'));
    	});

        var getHistoricalData = function () {
            var lid = $("#lid").val();
            var unitedStatus = $("input[name='unitedStatus']:checked").val();
            var uid = '<?php echo $user['uid']; ?>';
            $.ajax({
                type: 'post',
                url: '/user/getHistoricalData',
                data: {unitedStatus: unitedStatus, lid: lid, uid: uid},
                success: function (response) {
                    $('#history_content').html(response);
                }
            });
        };

        var getCount = function () {
            var lid = $("#lid").val();
            var uid = '<?php echo $user['uid']; ?>';
            $.ajax({
                type: 'post',
                url: '/user/getCount',
                data: {lid: lid, uid: uid},
                dataType: "json",
                success: function (response) {
                    $('#winNum').html(response.bonus);
                    $('#winCount').html(response.winningTimes);
                    $('#lidName').html(response.lidName);
                    if (response.united_points) {
                    	$('#unitePoints').html("，累计战绩奖励<s class='level'>"+response.united_points+"</s>")
                    }else {
                    	$('#unitePoints').empty();
                    }    
                }
            });
        };
        
        var getGendanData = function () {
            var uid = '<?php echo $user['uid']; ?>';
            $.ajax({
                type: 'post',
                url: '/user/getGendanData',
                data: {uid: uid},
                success: function (response) {
                    $('#gendan_content').html(response);
                }
            });            
        };
        
        $(".editBtn").on('click',function(){
            if (!($(this).hasClass('disabled'))) {
                $('.editBtn').addClass('disabled');
                $.ajax({
                    type: 'post',
                    url:  '/pop/editSelf',
                    data: {'version':version},
                    success: function(response) {
                        $('body').append(response);
                        cx.PopCom.show('.pop-id');
                        cx.PopCom.close('.pop-id');
                        cx.PopCom.cancel('.pop-id');
                        $('.editBtn').removeClass('disabled');
                        $('.J-wordsCd-in').focus();
                    }
                });
        }
        });
        
        $('.user_form').on("click","table thead .filter-arrow",function(){
           if ($(this).hasClass('filter-arrow-t')) {
          	 $('.user_form').find("input[name=order]").val(orderArr[0]+'1');
           }else if ($(this).hasClass('filter-arrow-b')) {
          	 $('.user_form').find("input[name=order]").val(orderArr[0]+'0');
           }else {
          	 $('.user_form').find("input[name=order]").val($(this).data('value')+'0');
           }
           $('.user_form').find('.submit').trigger('click');
        });
        $('.gendan').on("click","table thead .filter-arrow",function(){
           if ($(this).hasClass('filter-arrow-t')) {
                 $('.gendan').find("input[name=gendan]").val(gendanArr[0]+'1');
           }else if ($(this).hasClass('filter-arrow-b')) {
                 $('.gendan').find("input[name=gendan]").val(gendanArr[0]+'0');
           }else {
                 $('.gendan').find("input[name=gendan]").val($(this).data('value')+'0');
           }
           $('.gendan').find('.submit').trigger('click');
        });
        $('.gendan').on('click', '.btn-gendan', function () {
            var  uid = $(this).data('uid');
            var  lid = $(this).data('lid');
            if (!$.cookie('name_ie')) {//登录过期
            	$(this).addClass('needTigger');
                cx.PopAjax.login(1);
                return;
            }
            if ($(this).hasClass('not-bind'))
                return;
            if(lid == 11){
                new cx.Confirm({title: '选择定制彩种',content: '<div class="tac"><a href="javascript:;" data-lid="11" data-uid="'+uid+'" class="btn-s btn-gendans btn-main mr20">胜负彩</a><a href="javascript:;" data-lid="19" data-uid="'+uid+'" class="btn-s btn-main btn-gendans">任选九</a></div>',
                    btns: []}); 
                return false;
            }
            if(lid == 33){
                new cx.Confirm({title: '选择定制彩种',content: '<div class="tac"><a href="javascript:;" data-lid="33" data-uid="'+uid+'" class="btn-s btn-gendans btn-main mr20">排列三</a><a href="javascript:;" data-lid="35" data-uid="'+uid+'" class="btn-s btn-main btn-gendans">排列五</a></div>',
                    btns: []}); 
                return false;
            }
            gendan(uid,lid);
        });
     
        $("body").on('click','.btn-gendans', function () {
            $(".pop-close").click();
            var  uid = $(this).data('uid');
            var  lid = $(this).data('lid');
            if (!$.cookie('name_ie')) {//登录过期
            	$(this).addClass('needTigger');
                cx.PopAjax.login(1);
                return;
            }
            if ($(this).hasClass('not-bind'))
                return;
            gendan(uid,lid);
        });
     
    $(".gendan").on('click', '.gendanlist',function () {
            gendanuid = $(this).data('uid');
            gendanlid = $(this).data('lid');
            $.ajax({
                type: "post",
                url: "/user/gendanlist",
                data: {
                    'uid': gendanuid,
                    'lid': gendanlid,
                },
                success: function (res) {
                    $('#listgendan_content').html(res);
                    cx.PopCom.show('.pop-id');
                    cx.PopCom.close('.pop-id');
                    cx.PopCom.cancel('.pop-id');
                }
            });
        });
        
        var gendan = function(uid,lid){
            $.ajax({
                type: "post",
                url: "/pop/gendan",
                data: {
                    'uid': uid,
                    'lid': lid,
                    'version':version
                },
                success: function (res) {
                    if (res==1) {
                        cx.Alert({content: '<i class="icon-font">&#xe600;</i>您已定制发起人的方案，换个彩种试试吧',
                            confirmCb: function () {
                                $('.gendan').find('.submit').trigger('click');
                        }});
                        return false;
                    }
                    if (res==2) {
                        cx.Alert({content: '<i class="icon-font">&#xe600;</i>定制人数已达上限，换个彩种试试吧',
                            confirmCb: function () {
                                $('.gendan').find('.submit').trigger('click');
                        }});
                        return false;
                    }
                    $('body').append(res);
                    cx.PopCom.show('.pop-id');
                    cx.PopCom.close('.pop-id');
                    cx.PopCom.cancel('.pop-id');
                }
            });
        }        
    </script>
<?php } ?>