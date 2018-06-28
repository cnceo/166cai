<script type="text/javascript">
//倒计时
var _ss;//计算剩余的秒数
var time;
var disabled;
function netimer() {
    _ss = 60;
    name = $('._timer').data('freeze');
    $(".vyzm").focus();
    $('._timer').addClass('disabled').hide();
    $('#_timer').parents('.lnk-getvcode-disabled').removeClass('hide');
    $(".ui-poptip-yuyin").show().parents('.form-item-con').addClass('zindex10');
    YzmClick();
    closNeTimer();
    time = setInterval("_netimer()", 1000);
}

function closNeTimer(show) {
    clearInterval(time);
    if (show) {
        $('#_timer').parents('.lnk-getvcode-disabled').addClass('hide');
        $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
        $('._timer').removeClass('disabled').show();
        YzmClick();
    }
}

function _netimer() {
    _ss -= 1;
    if (_ss >= 0) {
        $('#_timer').html(_ss);
        return false;
    }
    initNeFun();
    $('#_timer').parents('.lnk-getvcode-disabled').addClass('hide');
    $(".ui-poptip-yuyin").hide().parents('.form-item-con').removeClass('zindex10');
    $('._timer').removeClass('disabled').show();
    closNeTimer();
    YzmClick();
    return true;
}

</script>
