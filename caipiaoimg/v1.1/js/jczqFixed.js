$(function () {

    $('.lotteryTableTH-fixed-box').css({'height': $('.lotteryTableTH').outerHeight()});

    if ($('.seleFiveTit').find('a').hasClass('bet-blue')) {
        $(".seleFiveWrap").hover(function () {
                $('.seleFiveTit').addClass("seleFiveTit2").next("div.seleFiveBox").show();
            },
            function () {
                $(".seleFiveTit").removeClass("seleFiveTit2").next("div.seleFiveBox").hide();
            });
    }

// 表底
    function jcBtPanel() {
        var $castPanel = $('.cast-panel');
        var eleFixedBox = $('.ele-fixed-box') || {};
        if (!$.isEmptyObject(eleFixedBox)) {
            var castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
            if (castPanelTop > $(window).height()) {
                $castPanel.addClass('cast-panel-fixed');
                if (!-[1,] && !window.XMLHttpRequest) {
                    $castPanel.css({'position': 'absolute'});
                }
            }
        }
    }

    jcBtPanel();
// 滚动出内容区域，fixed
    var jcTableHdTop = $('.jc-table-hd-box').offset().top;
    $(window).on('scroll', function () {
        var dScrollTop = $(document).scrollTop();

        // 表头
        if (dScrollTop > jcTableHdTop && dScrollTop < ($('.bet-main').offset().top + $('.bet-main').outerHeight() - $('.jc-table-hd').outerHeight())) {
            $('.jc-table-hd').addClass('jc-table-hd-fixed');
        } else {
            $('.jc-table-hd').removeClass('jc-table-hd-fixed');
        }

        // 表底
        var $castPanel = $('.cast-panel');
        var eleFixedBox = $('.ele-fixed-box') || {};
        if (!$.isEmptyObject(eleFixedBox)) {
            var castPanelTop = eleFixedBox.height() + eleFixedBox.offset().top;
            if (castPanelTop > $(window).height() && dScrollTop + $(window).height() - $('.ele-fixed-box').height() < $('.ele-fixed-box').offset().top) {
                $castPanel.addClass('cast-panel-fixed');
                if (!-[1,] && !window.XMLHttpRequest) {
                    $castPanel.css({'position': 'absolute'});
                }
            } else {
                $castPanel.removeClass('cast-panel-fixed');
                if (!-[1,] && !window.XMLHttpRequest) {
                    $castPanel.css({'position': 'static'});
                }
            }
        }

        // 侧边栏
        var betMainHeight = $('.bet-main').outerHeight();
        var betBarHeight = $('.bet-bar').height();
        if (dScrollTop > 234 && !($('.bet-bar').height() >= $(window).height())) {
            $('.bet-bar').addClass('bet-bar-fixed');
            if (betMainHeight + $('.bet-main').offset().top - dScrollTop < betBarHeight) {
                $('.bet-bar').css({
                    top: betMainHeight + $('.bet-main').offset().top - betBarHeight - dScrollTop + 'px'
                })
            }
        } else {
            $('.bet-bar').removeClass('bet-bar-fixed');
        }
    });

    var ie6 = !-[1,] && !window.XMLHttpRequest;
    if (ie6) {
        $('.league-filter').on('mouseover', function () {
            $(this).addClass('hover');
        }).on('mouseout', function () {
            $(this).removeClass('hover');
        });
        $('.mod-tips').on('mouseover', function () {
            $(this).addClass('mod-tips-hover');
        }).on('mouseout', function () {
            $(this).removeClass('mod-tips-hover');
        });
    }
});
