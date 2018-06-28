<div class="ele-fixed-box">
    <div class="lotteryZQbg cast-panel">
        <div class="lotteryZQCenter">
            <div class="seleFiveWrap">
                <div class="seleFiveTit">
                    <a href="javascript:;">已选<em class="count-matches">0</em>场<i></i></a>
                </div>
                <div class="seleFiveBox">
                    <div class="seleFiveBoxTit">
                        <table>
                            <thead>
                            <tr>
                                <th width="60">序号</th>
                                <th width="83" class="tar"><span>客队</span></th>
                                <th width="83" class="tal"><span>主队</span></th>
                                <th width="264"><span>投注内容</span></th>
                                <th width="60"><span><a href="javascript:;" class="clear-matches">清空</a></span></th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="seleFiveBoxScroll">
                        <table class="selected-matches">
                        </table>
                    </div>
                </div>
            </div>

            <div class="seleFiveInfo">
                <div id="no-matches" class="no-match">请至少选择一场比赛
                    <?php if(!empty($hoverInfo) && $hoverInfo['startTime'] <= date('Y-m-d H:i:s') && $hoverInfo['endTime'] >= date('Y-m-d H:i:s')): ?>
                    <div class="jiajiang-tips">
                    <span class="bubble-tip" tiptext="<div class='jiajiang-table'><table class='jc-inTable'><thead><tr><th width='156'>竞彩单关、2串1奖金分布</th><th width='88'>单关加奖金额</th><th width='88'>2串1加奖金额</th></tr></thead><tbody><?php echo $hoverInfo['tpl']; ?></tbody></table></div>"><?php echo $hoverInfo['slogan']; ?></span>
                    <img src="<?php echo getStaticFile('/caipiaoimg/v1.1/img/new.gif'); ?>" width="28" height="18" alt="new">
                    </div>
                    <?php endif; ?>
                </div>
                <ul class="has-matches clearfix panel-hide">
                    <li class="first">
                        <?php $this->load->view('v1.1/elements/jclq/gg_types'); ?>
                        <div class="numbox">共 <span class="bet-num main-color-s">0</span> 注</div>
                    </li>
                    <li class="second">
                        <div class="tzbs">
                            <span class="fl">投注倍数：</span>

                            <div class="multi-modifier">
                                <a href="javascript:;" class="minus">-</a>
                                <label><input class="multi number" type="text" value="1" autocomplete="off"></label>
                                <a href="javascript:;" class="plus" data-max="100000">+</a>
                            </div>
                        </div>
                        <div class="tzje">投注金额：<em class="bet-money main-color-s"></em> 元</div>
                        <div class="ycjj">
                            <span class="fl">预测奖金：</span>

                            <p><span><em class="min-money main-color-s">0.00</em> - <em
                                        class="max-money main-color-s">0.00</em></span>元</p>
                        </div>
                        <div class="fycjj hidden">
                            <span class="fl">奖金：</span>

                            <p><span><em
                                        class="old-max-money main-color-s">0.00</em></span>元</p>
                        </div>
                    </li>
                </ul>
                <div class="has-matches jjmx panel-hide"><p class="jjyh" id="optimize"><a class="seleView start-detail main-color-s">奖金优化</a></p></div>
                <a href="javascript:;" class="btn btn-specail btn-hemai <?php echo $lotteryConfig[JCLQ]['united_status'] ? '' : 'btn-disabled' ?> <?php echo $showBind ? 'not-bind' : '' ?>">发起合买</a>
                <a class="btn btn-main btn btn-betting 
                <?php echo $lotteryConfig[JCLQ]['status'] ? 'submit' : 'btn-disabled' ?>
                <?php echo $showBind ? 'not-bind' : '' ?>
                "><?php if($lotteryConfig[JCLQ]['status']):?>确认预约<?php else:?>暂停预约<?php endif;?></a>
                <span class="jjyc-img hidden"></span>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
$('.no-match .bubble-tip').mouseenter(function(){
        $.bubble({
            target:this,
            position: 't',
            align: 'c',
            width: 316,
            content: $(this).attr('tiptext'),
            autoClose: false
        })
    }).mouseleave(function(){
        $('.bubble').hide();
    });
    
$('.lotteryZQCenter').on('mouseenter','.bubble-tip', function(){
    $.bubble({
        target: this,
        position: 't',
        align: 'l',
        content: $(this).attr('tiptext'),
        width: 'auto',
        skin: 3,
        autoClose: false
    })
}).on('mouseleave','.bubble-tip', function(){
    $('.bubble').hide();
});
$('.more-type-bd .gg-type').mouseenter(function(){
    $.bubble({
        target: this,
        position: 'b',
        align: 'l',
        content: '至少猜对' + $(this).data('min') + '场可中奖',
        width: 'auto',
        skin: 3,
        autoClose: false
    })
}).mouseleave(function(){
    $('.bubble').hide();
});

function CLOSE_TYPE_MORE () {
    var moreType = $('.more-type');
    var btnMoreType = moreType.find('.gg-type-more');
    var moreTypePop = moreType.find('.more-type-pop');
    moreTypePop.hide();
    btnMoreType.removeClass('active')
    if (moreTypePop.find('.selected').length) btnMoreType.addClass('selected')
}
$('.passway').on('click', '.gg-type-more', function () {
    $(this).toggleClass('active');
    if ($(this).hasClass('active')) {
        $(this).next('.more-type-pop').show()
    } else {
        CLOSE_TYPE_MORE()
    }
})

$('.more-type').on('click', '.more-type-hide', function () {
    CLOSE_TYPE_MORE()
})
</script>