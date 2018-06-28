$(function () {
    $('.lotteryTableTH-fixed-box').css({'height': $('.lotteryTableTH').outerHeight()});
    $(".lotteryPlayWrap").hover(function () {
        $(this).find('h3').addClass("hover").next("div.lotteryPlayBox").show();
    }, function () {
        $(".lotteryPlayWrap h3").removeClass().next("div.lotteryPlayBox").hide();
    });

    $(".seleFiveTit").hover(function () {
        $(this).addClass("seleFiveTit2").next("div.seleFiveBox").show();
    });
    $(".seleFiveWrap").hover(function () {}, function () {
        $(".seleFiveTit").removeClass("seleFiveTit2").next("div.seleFiveBox").hide();
    });
    var $castPanel = $('.cast-panel');
    var $header = $('.lotteryTableTH');
    var castPanelTop = $castPanel.height() + $castPanel.offset().top;
    var headerTop = $('.userLotteryTab').height() + $('.userLotteryTab').offset().top;
    function onScroll() {
        var top = $('#container').height() + $('#container').offset().top;
        var scrollTop = $(document).scrollTop() + $(window).height();
        
        if (scrollTop >= castPanelTop) {
            $castPanel.removeClass('fixed');
            if(!-[1,]&&!window.XMLHttpRequest){
               $castPanel.css({'position': 'static'}); 
            }
         } else {
            $castPanel.addClass('fixed');
            if(!-[1,]&&!window.XMLHttpRequest){
               $castPanel.css({'position': 'absolute'}); 
            }
        }

        if ($(document).scrollTop() >= headerTop) {
            $header.addClass('fixed');
            if(!-[1,]&&!window.XMLHttpRequest){
               $header.css({'top': $(document).scrollTop() - headerTop - 1}); 
            }
        } else {
            $header.removeClass('fixed');
            if(!-[1,]&&!window.XMLHttpRequest){
               $header.css({'top': 0}); 
            }
        }
    }

    var Throttle;
    $(window).scroll(function () {
        onScroll();
    });
    $(window).resize(function(){
        clearTimeout(Throttle);
        Throttle = setTimeout(function(){
            onScroll();
        }, 100)
    });
    onScroll();
    $('.league').click(function () {
        var $this = $(this);
        var val = $this.val();
        var checked = $this.attr('checked');
        $('.match').each(function (key, match) {
            var $match = $(match);
            var league = $match.data('league');
            if (league == val) {
                $match.toggle();
                $match.next().toggle();
                if ($match.find('.open-sfc').hasClass('opened')) {
                    $match.find('.open-sfc').trigger('click');
                }
            }
        });
        onScroll();
    });

    $('.select-anti').click(function () {
        $('.league').trigger('click');
    });
    $('.select-all').click(function () {
        $('.league').each(function (key, league) {
            if ($(league).attr('checked') != 'checked') {
                $(league).trigger('click');
            }
        });
    });
    $('.select-none').click(function () {
        $('.league').each(function (key, league) {
            if ($(league).attr('checked') == 'checked') {
                $(league).trigger('click');
            }
        });
    });
});