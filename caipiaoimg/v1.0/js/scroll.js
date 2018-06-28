window.index = window.index || {};

//大图轮播
index.setScrollEvent = function(){
    var donateRecommendTimer = {};
    //初始化
    var pre_fade = {};
    var slider_num = $(".slider_num");
    var slider_num_li = slider_num.children("li");
    var curr_index = 0;
    $(".slider_num li:first").addClass("on");
    $(".fade_ul > li:first").css({"opacity":"1", "z-index":"2"}).addClass("onshow");

    //鼠标移动到广告下方的数字上
    slider_num_li.bind("mouseover", function(){
        clearInterval(donateRecommendTimer);
        var item = $(this);
        if(item.hasClass("on")){
            return false;
        }
        pre_fade = getPreFade();
        curr_index = item.index();
        slider_num_li.removeClass("on");
        item.addClass("on");
        showFadeInImg(pre_fade, curr_index);
    });

    slider_num_li.bind("mouseout", function(){
        donateRecommendTimer = setInterval(autoSilder, 4000);
    });

    //广告自动播放
    $("li.onshow").hover(function(){
        clearInterval(donateRecommendTimer);
    }, function(){
        donateRecommendTimer = setInterval(autoSilder, 4000);
    }).trigger("mouseout");

    function autoSilder(){
        pre_fade = getPreFade();
        curr_index = pre_fade.index();
        slider_num_li.eq(curr_index).removeClass("on");
        if(curr_index == 1)
        {
            curr_index = 0;
        }
        else
        {
            curr_index++;
        }
        slider_num_li.eq(curr_index).addClass("on");
        showFadeInImg(pre_fade, curr_index);
    };

    //大图淡入淡出函数
    function showFadeInImg(pre_fade, index){
        var ua = navigator.userAgent.toLowerCase().match(/msie ([\d.]+)/);
        if(!ua){
            pre_fade.stop().animate({opacity:0, zIndex:1}, 500).removeClass("onshow");
            $("li.fade_img").eq(index).stop().animate({opacity:1, zIndex:2}, 500).addClass("onshow");
        }else{
            $(".fade_ul>li").css("opacity", 1);
            pre_fade.stop().fadeOut(1500, function(){
                pre_fade.hide();
            }).removeClass("onshow");
            $("li.fade_img").eq(index).stop().fadeIn(1000).addClass("onshow").show();
        }
    }
    //获取当前淡入的图片
    function getPreFade(){
        return $("ul.fade_ul > li.onshow");
    }
};
