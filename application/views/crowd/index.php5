<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/buy.css');?>" rel="stylesheet" type="text/css" />
<link href="<?php echo getStaticFile('/caipiaoimg/v1.0/styles/dialog.css');?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?php echo getStaticFile('/caipiaoimg/v1.0/js/lottery.js');?>"></script>
<script type="text/javascript">
$(function(){
    //合买彩票表单交互
    $(".buyItemsForm input").each(function(){
        var _this = $(this);
        var _val = _this.val();
        $(this).focus(function(){
            _this.removeClass("gray").val("");
        });
        $(this).blur(function(){
            if(_this.val() == ""){
                _this.addClass("gray").val(_val);
            }
        });
    });
    var currPage = <?php echo $currPage; ?>;
    var totalPage = <?php echo $totalPage; ?>;
    var oflag = <?php echo $oflag; ?>;
    var selected = <?php echo $selected; ?>;
    $('.prev-page').click(function() {
        if (currPage > 1) {
            currPage -= 1
            location.href = baseUrl + 'crowd?' + concatParams();
        }
    });
    $('.next-page').click(function() {
        if (currPage < totalPage) {
            currPage += 1;
            location.href = baseUrl + 'crowd?' + concatParams();
        }
    });
    $('.select-page').change(function() {
        var page = $(this).val();
        if (page != currPage) {
            currPage = page;
            location.href = baseUrl + 'crowd?' + concatParams();
        }
    });
    $('.cx-radio').click(function() {
        var $this = $(this);
        if ($this.hasClass('selected')) {
            return ;
        }
        var key = $this.data('key');
        if (key == 1) {
            location.href = baseUrl + 'crowd';
            return ;
        }
        if (key == 3) {
            oflag = 1;
        } else if (key == 4) {
            oflag = 2;
        } else if (key == 5) {
            oflag = 3;
        }
        selected = key;
        currPage = 1;

        location.href = baseUrl + 'crowd?' + concatParams();
    });

    function concatParams() {
        var params = 'pn=' + currPage;
        if (oflag > 0) {
            params += '&oflag=' + oflag;
        }
        params += '&selected=' + selected;

        return params;
    }
});
</script>
<!--容器-->
<div class="buyWrap">
    <?php
        $this->load->view('elements/helper/cx_radio', array(
            'items' => array(
                1 => '全部合买',
                //2 => '我发起的合买',
                3 => '完全公开',
                4 => '跟单公开',
                5 => '截止后公开',
            ),
            'selected' => $selected,
        ));
    ?>
    <div class="buyItems clearfix">
        <ul>
        <?php foreach ($items as $item): ?>
          <li>
            <?php //$this->load->view('elements/crowd/item', array('item' => $item)); ?>
          </li>
        <?php endforeach; ?>
        </ul>
    </div>
    <div class="buyPages">
        <p>
            <span class="prev-page">上一页</span>
            <select class="select-page">
                <?php for ($i = 1; $i <= $totalPage; ++$i): ?>
                <option <?php if ($i == $currPage) echo 'selected="selected"';?> value="<?php echo $i; ?>"><?php echo $i; ?>/<?php echo $totalPage; ?></option>
                <?php endfor; ?>
            </select>
            <span class="next-page">下一页</span>
        </p>
    </div>
</div>
<!--容器end-->
