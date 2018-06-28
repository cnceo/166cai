<?php
/**
 * @see SFC::index()
 * @var $targetIssue
 * @var $currIssue
 * @var $matches
 * @var $lotteryConfig
 * @var $showBind
 * @var $lotteryId
 * @var $nextIssueIds
 * @var $cnName
 */
$time = $targetIssue['seFsendtime'] / 1000 - time();
$hmselling = 0;
$hmendTime = $targetIssue['seFsendtime'] / 1000 - $lotteryConfig[SFC]['united_ahead'] * 60;
if(!$lotteryConfig[SFC]['status'] || ($currIssue['sale_time'] > time() * 1000) || ($currIssue['seExpect'] != $minCurrentId)) {
    $selling = 0;
}else {
    $selling = 1;
    if ($lotteryConfig[SFC]['united_status']) {
        $hmselling = 1;
    }
}?>
<div class="wrap cp-box bet-jc sfc dssc">
    <?php echo $this->load->view('v1.1/elements/lottery/info_panel'); ?>
    <style>
        .bet-tab-hd {
            margin-bottom: 12px;
        }
        .pick-area-explain {
            margin: 0 0 14px 10px;
        }
        .pick-area-box {
            padding: 0;
        }
        .sfc .bet-type-duox {
            padding-top: 10px;
        }
        .upload {
            width: 618px; margin: 0 auto;
        }
        .bet-area-txt {
            width: 618px; margin: 0 auto 10px; padding: 0 !important;
        }
        .sfc .buy-type {
            margin: 0 auto 20px;
        }
    </style>
    <div class="cp-box-bd bet">
        <div class="bet-main">
            <div class="bet-link-bd">
                <div class="pick-area-box bet-tab">
                    <!-- 数字彩投注区 start -->
                    <div class="bet-tab-hd _bet_tab_hd">
                        <ul>
                            <li class="dssc <?php if($cz=='sfc'){echo ' current';}?>"><a href='javascript:;'>胜负彩</a></li>
                            <li class="dssc <?php if($cz=='rj'){echo ' current';}?>"><a href='rj/dssc'>任选九</a></li>
                        </ul>
                    </div>
                    <div class="bet-tab-bd">
                        <div class="bet-tab-bd-inner">
                            <div class="pick-area">
                                <p class="pick-area-explain"><i class="icon-font"></i>玩法说明：上传方案中的单式号码与开奖号码相同，即中一等奖，单注最高奖金500万元！</p>
                            </div>
                            <div class="upload">
                                <div class="rule">
                                    <h3>上传规则：</h3>
                                    <ol>
                                        <li>1、单式上传提前<?php echo $dsjzsj;?>分钟截止，上传文件必须是（.txt）文本，文件大小不能超过256KB；</li>
                                        <li>2、每个号码为1位数字，一行一个投注号，只支持单式号码上传；</li>
                                        <li class="example">
                                            <span>3、投注示例：</span>
                                            <div class="example-cnt">
                                                <p>13031013003010</p>
                                            </div>
                                        </li>
                                    </ol>
                                </div>
                                <div id="uploader" class="up-prog">
                                    <div id="picker0">选择文件</div>
                                    <div id="thelist0" class="uploader-list"><span class="uploader-list-tips">未选择任何文件</span></div>
                                    <a href="javascript:;" id="ctlBtn0" class="btn btn-default">开始上传</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bet-type-area bet-type-duox">
                <div class="bet-area-txt">
                    已选<strong class="num-multiple betNum _betNum">0</strong>注，投
                    <div class="multi-modifier">
                        <a href="javascript:;" class="_minus">-</a>
                        <label><input class="_multi" type="text" value="1" autocomplete="off"></label>
                        <a href="javascript:;" class="_plus"  data-max="99">+</a>
                    </div>
                    倍（最大99倍），共计<strong class="num-money bet-money  betMoney _betMoney">0</strong>元
                </div>
            </div>
            <div class="buy-type tab-radio">
                <div class="buy-type-hd tab-radio-hd">
                    <div class="chase-number-notes">由购买人自行全额购买彩票，独享奖金<i class="icon-font"></i></div>
                    <em>购买方式：</em>
                        <ul class='_select_buy_way'>
                            <li><label for="ordertype0"><input type="radio" id="ordertype0" name="chaseNumberTab" checked>自购</label></li>
                            <li><label for="ordertype2" class="main-color-s"><input type="radio" id="ordertype2" name="chaseNumberTab">发起合买</label></li>
                        </ul>
                </div>
                <div class="buy-type-bd tab-radio-bd">
                    <div class="tab-radio-inner hide" style="display: none;"></div>
                    <div class="tab-radio-inner"><?php $this->load->view('v1.1/elements/lottery/hemai_dssc');?></div>
                </div>
            </div>
            <div class="btn-group mb20">
                <a class="btn btn-main _submit <?php echo $showBind ? ' not-bind': '';?>">确认预约</a>
                <a id="pd_sfc_buy" class="btn btn-main _gc_buy btn-betting
                            <?php echo $lotteryConfig[RJ]['status'] ? 'submit' : 'btn-disabled' ?>
                            <?php echo $showBind ? 'not-bind' : '' ?>" style="display: none;">确认预约</a>                    
                <p class="btn-group-txt _JRJbox">
                    <input class="ipt_checkbox" type="checkbox" checked="checked" id="agreenment"><label for="agreenment">我已阅读并同意</label>
                    <a href="javascript:;" class="lottery_pro">《用户委托投注协议》</a>
                </p>
                </div>
            </div>
        </div>
</div>

<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/math.min.js'); ?>"></script>
<script type="text/javascript">
var lotteryId = <?php echo $lotteryId?>, currIssue = '<?php echo $currIssue['seExpect'] ?>', nextIssue = '<?php echo $nextIssueIds[0] ?>', typeCnName = '<?php echo $cnName . ", 第" . $currIssue["seExpect"] . "期" ?>',
realTypeCnName = '胜负彩', typeEnName = 'sfc', time = <?php echo $time?>, alertLastTime = 5, selling = <?php echo $selling?>, hmendTime = '<?php echo $hmendTime?>', hmDate = new Date(hmendTime * 1000), 
hmselling = '<?php echo $hmselling?>', realendTime = '<?php echo $currIssue['end_sale_time']?>';
var ENDTIME = "<?php echo date('Y-m-d H:i:s', $currIssue['seFsendtime'] / 1000) ?>";
var CUR_ISSUE = currIssue;
var LOTTERY_ID = lotteryId;
var _CURR_LI = 1;
var baseUrl = "<?php echo $baseUrl; ?>";
var _STATIC_FILE = baseUrl+"/caipiaoimg/v1.1/";
var playType = 1; //玩法
</script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/lzc.js'); ?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/webuploader.min.js');?>"></script>
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.1/js/dssc.js');?>"></script>
<script>
$(function(){
    $('.bet-type-link li').removeClass('selected');
    $('.bet-type-link li').eq(2).addClass('selected');
    $('.bet-type-link li a').eq(0).attr('href',baseUrl+'sfc');
    $('.bet-type-link li a').eq(1).attr('href',baseUrl+'rj');
    $('.bet-tab-hd li').unbind();
    setTimeout(function(){
     if($('._submit').hasClass('btn-disabled')){
        $('._JRJbox').html('<span class="num-red">（该期次处于预售期，暂不支持销售）</span>');
    }  
   },50);
})
</script>