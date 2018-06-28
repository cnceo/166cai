<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/dialog.css');?>" rel="stylesheet" type="text/css" />
<style>
a.guarantee-all {
    color: #1467ad;
    margin-right: 10px;
}
.crowd-tip-gua {
    background: url(/caipiaoimg/v1.0/images/common/helper_tip.png) no-repeat;
    width: 20px;
    height: 20px;
    display: inline-block;
}
.dialogInfo div.crowd-tip-gua-content {
    width: 450px;
    position: absolute;
    background: #fff;
    padding: 10px;
    border: 1px solid #ccc;
    display: none;
    left: 18px;
}
.crowd-buy {
    position: fixed;
    z-index: 60;
    top: 10%;
    left: 50%;
    margin-left: -425px;
    display: none;
}
.crowd-buy input[type="text"],
.crowd-buy select {
    width: 100px;
    border: 1px solid #aaa;
    height: 1.5em;
    line-height: 1.5em;
    padding: 0 5px;
}
.crowd-buy select {
    width: 112px;
}
.crowd-buy .crowd-helper {
    width: 100px;
    display: inline-block;
}
</style>
<div class="dialogWrap crowd-buy">
    <h1>发起合买 <a class="close"><img src="<?php echo getStaticFile('/caipiaoimg/v1.0/images/bg/close.gif');?>" alt="关闭" width="22" height="22" /></a></h1>
    <div class="dialogInfo">
        <h3>
            <label>我要提成：</label>
            <select class="commision-per">
                <?php for ($i = 0; $i <= 10; ++$i): ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            <label class="crowd-helper">% </label>
            <label class="crowd-tip">税后奖金的0%-10%</label>
        </h3>
        <h3>
            <label>我要认购：</label>
            <input type="text" class="my-per" />
            <label class="crowd-helper">% <span class="my-money">0</span>元</label>
            <label class="crowd-tip">最低认购<span class="least-per">5</span>%，至少1元，且不能低于提成比例</label>
            </h3>
        <h3>
            <label>我要保底：</label>
            <input type="text" class="guarantee-per" value="0" />
            <label class="crowd-helper">% <span class="guarantee-money">0</span>元</label>
            <a class="crowd-tip crowd-tip-gua"></a>
            <div class="crowd-tip-gua-content">
                <div class="triangle-up" style="left: 300px;"></div>
                <h3 style="font-weight: bold;">保底：</h3>
                <p>发起人承诺合买截止后，如果方案还没有满员，发起人再投入先前承诺的金额以最大限度让方案成交。保底时，系统将暂时冻结保底资金，在合买截止时如果方案还未满员的话，系统将会用冻结的保底资金去认购方案。如果在合买截止前方案已经满员，系统会解冻保底资金。</p>
            </div>
            <a class="crowd-tip guarantee-all">全保</a>
            <a class="crowd-tip guarantee-none">清除</a>
        </h3>
        <h2>
            <label>方案设置：</label>
            <span class="has-private selected" data-val="1">完全公开</span>
            <span class="has-private" data-val="2">跟单公开</span>
            <span class="has-private" data-val="3">开奖后公开</span>
        </h2>
        <h5>
            <a class="btn btn-cancel">取消</a>
            <a class="btn btn-confirm submit-crowd">立即投注</a>
        </h5>
    </div>
</div>
