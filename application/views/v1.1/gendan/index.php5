<?php if (!$this->is_ajax) { ?>
<link href="<?php echo getStaticFile('/caipiaoimg/v1.1/styles/hemai.min.css');?>" rel="stylesheet" type="text/css" />
    <div class="wrap p-hemai p-hemai-index">
        <div class="fn-sticky">
            <div class="fn-sticky-inner filter-tab">
                <div class="fixmacOS" style="margin-right: -10px;">
                    <span <?php if ($this->act === 'index') {?> class="current" <?php } ?>><a href="/gendan">全部彩种</a></span>
                    <span <?php if ($this->act === 'ssq') {?> class="current" <?php } ?>><a href="/gendan/ssq">双色球</a></span>
                    <span <?php if ($this->act === 'dlt') {?> class="current" <?php } ?>><a href="/gendan/dlt">大乐透</a></span>
                    <span <?php if ($this->act === 'jczq') {?> class="current" <?php } ?>><a href="/gendan/jczq">竞彩足球</a></span>
                    <span <?php if ($this->act === 'jclq') {?> class="current" <?php } ?>><a href="/gendan/jclq">竞彩篮球</a></span>
                    <span <?php if ($this->act === 'sfc') {?> class="current" <?php } ?>><a href="/gendan/sfc">胜负/任九</a></span>
                    <span <?php if ($this->act === 'fcsd') {?> class="current" <?php } ?>><a href="/gendan/fcsd">福彩3D</a></span>
                    <span <?php if ($this->act === 'qlc') {?> class="current" <?php } ?>><a href="/gendan/qlc">七乐彩</a></span>
                    <span <?php if ($this->act === 'qxc') {?> class="current" <?php } ?>><a href="/gendan/qxc">七星彩</a></span>
                    <span <?php if ($this->act === 'pls') {?> class="current" <?php } ?>><a href="/gendan/pls">排列三/五</a></span>
                </div>
            </div>
        </div>
        <div class="popular">
            <div class="popular-hd">
                <span class="popular-notes"><a href="/help/index/b2-s5">什么是定制跟单？</a></span>
                <h2 class="popular-title"><i class="icon-font">&#xe636;</i>跟单红人</h2>
            </div>
            <div class="popular-bd">
                <ul class="mod-tab clearfix" data-rule='{"type": "click"}'>
                    <li class="current"><a href="javascript:;">竞技彩高手</a></li>
                    <li><a href="javascript:;">数字彩达人</a></li>
                    <li><a href="javascript:;">中过大奖</a></li>
                    <li><a href="javascript:;">近期中奖多</a></li>
                </ul>
                <div class="mod-tab-con">
                    <div class="mod-tab-item" style="display: block;">
                        <div class="popular-group">
                            <?php foreach ($allInfo[0] as $user){ ?>
                            <div class="popular-card">
                                <div class="popular-card-hd">
                                    <a href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $user['uid'])), 'ENCODE')); ?>"><?php echo uname_cut($user['uname'], 1, 5); ?></a>
                                    <span class="level">
                                     <?php echo calGrade($user['united_points'], 5, ''); ?>
                                    </span>
                                </div>
                                <div class="popular-card-bd">
                                    <ul>
                                        <li>累积奖金：<em><?php echo number_format($user['bonus']/100,2); ?></em>元</li>
                                        <li>擅长彩种：<?php echo $lottery[$user['lid']] ?></li>
                                        <li>定制人数：<?php echo $user['isFollowNum']; ?>人</li>
                                    </ul>
                                </div>
                                <?php if(in_array($user['uid'].','.$user['lid'], $hasgendan)){ ?>
                                <a href="javascript:;" class="btn-ss btn-main btn-dzgd btn-disabled">已定制</a>
                                <?php }else{ ?>                                
                                <a href="javascript:;" data-lid="<?php echo $user['lid']; ?>" data-uid="<?php echo $user['uid']; ?>" class="btn-ss btn-main btn-dzgd btn-gendan <?php echo $showBind ? ' not-bind': '';?>">立即定制</a>
                                <?php } ?>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="mod-tab-item popular-group">
                            <?php foreach ($allInfo[1] as $user){ ?>
                            <div class="popular-card">
                                <div class="popular-card-hd">
                                    <a href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $user['uid'])), 'ENCODE')); ?>"><?php echo uname_cut($user['uname'], 1, 5); ?></a>
                                    <span class="level">
                                     <?php echo calGrade($user['united_points'], 5, ''); ?>
                                    </span>
                                </div>
                                <div class="popular-card-bd">
                                    <ul>
                                        <li>累积奖金：<em><?php echo number_format($user['bonus']/100,2); ?></em>元</li>
                                        <li>擅长彩种：<?php echo $lottery[$user['lid']] ?></li>
                                        <li>定制人数：<?php echo $user['isFollowNum']; ?>人</li>
                                    </ul>
                                </div>
                                <?php if(in_array($user['uid'].','.$user['lid'], $hasgendan)){ ?>
                                <a href="javascript:;" class="btn-ss btn-main btn-dzgd btn-disabled">已定制</a>
                                <?php }else{ ?>                                
                                <a href="javascript:;" data-lid="<?php echo $user['lid']; ?>" data-uid="<?php echo $user['uid']; ?>" class="btn-ss btn-main btn-dzgd btn-gendan <?php echo $showBind ? ' not-bind': '';?>">立即定制</a>
                                <?php } ?>
                            </div>
                            <?php } ?>
                    </div>
                    <div class="mod-tab-item popular-group">
                            <?php foreach ($allInfo[2] as $user){ ?>
                            <div class="popular-card">
                                <div class="popular-card-hd">
                                    <a href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $user['uid'])), 'ENCODE')); ?>"><?php echo uname_cut($user['uname'], 1, 5); ?></a>
                                    <span class="level">
                                     <?php echo calGrade($user['united_points'], 5, ''); ?>
                                    </span>
                                </div>
                                <div class="popular-card-bd">
                                    <ul>
                                        <li>累积奖金：<em><?php echo number_format($user['bonus']/100,2); ?></em>元</li>
                                        <li>中过最大：<em><?php echo round($user['orderBonus']/1000000,2); ?>万</em>元(<?php echo $lottery[$user['lid']]; ?>)</li>
                                        <li>定制人数：<?php echo $user['isFollowNum']; ?>人</li>
                                    </ul>
                                </div>
                                <?php if(in_array($user['uid'].','.$user['lid'], $hasgendan)){ ?>
                                <a href="javascript:;" class="btn-ss btn-main btn-dzgd btn-disabled">已定制</a>
                                <?php }else{ ?>                                
                                <a href="javascript:;" data-lid="<?php echo $user['lid']; ?>" data-uid="<?php echo $user['uid']; ?>" class="btn-ss btn-main btn-dzgd btn-gendan <?php echo $showBind ? ' not-bind': '';?>">立即定制</a>
                                <?php } ?>
                            </div>
                            <?php } ?>
                    </div>
                    <div class="mod-tab-item popular-group">
                            <?php foreach ($allInfo[3] as $user){ ?>
                            <div class="popular-card">
                                <div class="popular-card-hd">
                                    <a href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $user['uid'])), 'ENCODE')); ?>"><?php echo uname_cut($user['uname'], 1, 5); ?></a>
                                    <span class="level">
                                     <?php echo calGrade($user['united_points'], 5, ''); ?>
                                    </span>
                                </div>
                                <div class="popular-card-bd">
                                    <ul>
                                        <li>近一个月奖金：<em><?php echo number_format($user['monthBonus']/100,2); ?></em>元</li>
                                        <li>擅长彩种：<?php echo $lottery[$user['lid']]; ?></li>
                                        <li>定制人数：<?php echo $user['isFollowNum']; ?>人</li>
                                    </ul>
                                </div>
                                <?php if(in_array($user['uid'].','.$user['lid'], $hasgendan)){ ?>
                                <a href="javascript:;" class="btn-ss btn-main btn-dzgd btn-disabled">已定制</a>
                                <?php }else{ ?>                                
                                <a href="javascript:;" data-lid="<?php echo $user['lid']; ?>" data-uid="<?php echo $user['uid']; ?>" class="btn-ss btn-main btn-dzgd btn-gendan <?php echo $showBind ? ' not-bind': '';?>">立即定制</a>
                                <?php } ?>
                            </div>
                            <?php } ?>
                    </div>
                </div>
                <!-- 没有合买 -->
                <!-- <div class="popular-none">暂无合买红人! 赶紧<a href="###">发起合买</a>做红人~</div> -->
            </div>
        </div>

        <div class="hemai-table">
            <div class="hemai-table-hd">
                <div class="hemail-type">
                    <strong><?php echo $lidName; ?></strong>
                </div>
                <span class="hemai-table-note more"><a id="mygendan" target="_blank" href="javascript:;">我的跟单记录<i>&raquo;</i></a></span>
            </div>
            <div  class="user_form" id="user_form_content">
            <?php } ?>
            <?php $orderArr = str_split ( $order )?> 
            <div class="filter-bar">
                <div class="filter-bar-l">
                    <dl class="simu-select select-small" data-target="submit">
                        <dt>
                          <span class="_scontent"><?php echo $type==0?"未跟满":"已跟满"?></span><i class="arrow"></i>
                          <input type="hidden" name="type" class="vcontent" value='<?php echo $type?$type:0?>'>
                        </dt>
                        <dd class="select-opt">
                            <div class="select-opt-in" data-name="type">
                                <a href="javascript:;" data-value="0">未跟满</a>
                                <a href="javascript:;" data-value="1">已跟满</a>
                            </div>
                        </dd>
                    </dl>
                    <input type="text" name="nickname" value="<?php echo $nickname;?>" class="nickname vcontent" placeholder="发起人昵称..." c-placeholder="发起人昵称...">
                    <input type="hidden" name="order" class="vcontent" value="<?php echo $order ? $order : '00'?>">
                    <button type="button" class="btn-ss btn-search submit">搜索</button>
                </div>
            </div>
            <table class="mod-tableA">
                <thead>
                    <tr>
                        <th width="50"></th>
                        <th width="80" class="tal">彩种</th>
                        <th width="106" class="tal">发起人</th>
                        <th width="106" class="tal"><span class="filter-arrow <?php if ($orderArr[0] == 0) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="0">合买战绩<i></i></span></th>
                        <th width="72" class="tar"><span class="filter-arrow <?php if ($orderArr[0] == 1) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="1">中奖次数<i></i></span></th>
                        <th width="100" class="tar"><span class="filter-arrow <?php if ($orderArr[0] == 2) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="2">累计奖金<i></i></span></th>
                        <th width="100" class="tar"><span class="filter-arrow <?php if ($orderArr[0] == 3) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="3">近1月奖金<i></i></span></th>
                        <th width="120">最近发合买时间</th>
                        <th width="104" class="tar"><span class="filter-arrow <?php if ($orderArr[0] == 4) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="4">定制人数<i></i></span></th>
                        <th width="72" class=""><span class="filter-arrow <?php if ($orderArr[0] == 5) {if ($orderArr[1] == 0) {?>filter-arrow-t<?php }else {?>filter-arrow-b<?php }}?>" data-value="5">历史定制<i></i></span></th>
                        <th><span style="padding-left:20px;">操作</span></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allUser as $k=>$user){ ?>
                    <tr <?php if(in_array($user['uid'].','.$user['lid'], $hasgendan)){ ?>class="hasfollow" <?php }?> >
                        <td><span><?php echo str_pad(($cpage-1)*$perPage+$k+1, 2, '0', STR_PAD_LEFT)?></span><?php if(in_array($user['uid'].','.$user['lid'], $hasgendan)){ ?><span class="icon-follow">已定</span><?php } ?></td>
                        <td class="tal"><?php echo $lottery[$user['lid']]; ?></td>
                        <td class="tal"><a href="/user/<?php echo urlencode(strCode(json_encode(array('uid' => $user['uid'])), 'ENCODE')); ?>" target="_blank"><?php echo uname_cut($user['uname']);?></a></td>
                        <td class="tal">
                            <span class="level">
                            <?php if(in_array($user['uid'].','.$user['lid'], $hasgendan)){
                               echo calGrade($user['united_points'], 5, 3);
                            }else{
                                echo calGrade($user['united_points'], 5, '');
                            } ?>
                            </span>
                        </td>
                        <td class="tar"><?php echo $user['winningTimes'];?>次</td>
                        <td class="tar"><em class="main-color-s"><?php echo number_format($user['bonus']/100,2); ?></em>元</td>
                        <td class="tar"><em class="main-color-s"><?php echo number_format($user['monthBonus']/100,2); ?></em>元</td>
                        <td><?php  echo date("m-d H:i:s",  strtotime($user['lastPayTime']));?></td>
                        <td class="tar" colspan="2"><?php echo  $user['isFollowNum'];?>人(<?php echo $user['followTimes'];?>人定过) <?php if($user['isFollowNum']>0){ ?><a href="javascript:;" data-lid="<?php echo $user['lid']; ?>" data-uid="<?php echo $user['uid']; ?>" class="gendanlist">[查看]</a><?php } ?></td>
                        <td class="tar">
                            <?php if(in_array($user['uid'].','.$user['lid'], $hasgendan)){ ?>
                            <a href="javascript:;" class="btn-ss btn-main btn-dzgd btn-disabled">已定制</a>
                            <?php }else{ ?>
                            <a href="javascript:;" data-lid="<?php echo $user['lid']; ?>" data-uid="<?php echo $user['uid']; ?>" class="btn-ss btn-main btn-dzgd btn-gendan <?php echo $showBind ? ' not-bind': '';?>">立即定制</a>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if(empty($allUser)){ ?>
                    <tr>
                        <td colspan="10">
                            <div class="no-data">
                                <div class="no-data-img">
                                    <img src="/caipiaoimg/v1.1/img/img-noData.png" width="61" height="67" alt="">
                                </div>
                                <div class="no-data-txt">
                                    <p>暂无跟单红人，马上发合买做红人~</p>
                                    <a href="/hall">马上发合买~</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php  } ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="10" class="tar">本页<?php echo $allNum; ?>条记录；共 <?php echo $pagenum?> 页</td>
                    </tr>
                </tfoot>
            </table>
            <script>
                var orderArr = '<?php echo isset($order) ? $order : '00'?>'.split('');
            </script>
            <?php if(!empty($allUser)){ ?>
                <?php echo $pages?>
            <?php  } ?>
            <?php if (!$this->is_ajax) { ?>    
            </div>
        </div>
    </div>
    <div class="listgendan">
        <button type="button" class="btn-ss btn-search submit hidden">搜索</button>
        <div id="listgendan_content">

        </div>
    </div>
    <script>
        var target = window.location.href;
        var uid=0;
        var lid=0;
        $(function() {
            (function() {
                // 这边没有计算底部到什么位置停止
                var fnSticky = $('.fn-sticky');
                var fnStickyInner = $('.fn-sticky-inner');
                var fnStickyTop = fnSticky.offset().top;
                $(window).on('scroll', function() {
                    if ($(this).scrollTop() > fnStickyTop) {
                        fnStickyInner.addClass('fixed');
                    } else {
                        fnStickyInner.removeClass('fixed');
                    }
                })
            })();
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
                    data.uid=uid;
                    data.lid=lid;
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
            $("#mygendan").on("click",function(){
                if (!$.cookie('name_ie')) {//登录过期
                    $(this).addClass('needTigger');
                    cx.PopAjax.login(1);
                    return;
                }
                if ($(this).hasClass('not-bind'))
                    return;
                window.open("/mylottery/gendanlog");
                });
        })
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
        $(".p-hemai").on('click','.btn-gendan', function () {
            uid = $(this).data('uid');
            lid = $(this).data('lid');
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
            uid = $(this).data('uid');
            lid = $(this).data('lid');
            if (!$.cookie('name_ie')) {//登录过期
            	$(this).addClass('needTigger');
                cx.PopAjax.login(1);
                return;
            }
            if ($(this).hasClass('not-bind'))
                return;
            gendan(uid,lid);
        });
        $(".user_form").on('click','.gendanlist', function () {
             uid = $(this).data('uid');
             lid = $(this).data('lid');
            $.ajax({
                type: "post",
                url: "/pop/gendanlist",
                data: {
                    'uid': uid,
                    'lid': lid,
                    'version':version
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