// 基础配置
require.config({
	baseUrl: '/caipiaoimg/static/js',
    paths: {
        "zepto" : "/caipiaoimg/static/js/lib/zepto.min",
        "frozen": "/caipiaoimg/static/js/lib/frozen.min",
        'basic':'/caipiaoimg/static/js/lib/basic'
    }
})


// UI相关
require(["zepto",'basic',"ui/tips/src/tips","ui/scroller/src/scroll"], function(z, basic, tips, Scroll){
    var $ = z;
    var t = tips;

    var rechargeIpt = $('.recharge-num-ipt');
    var rechargeNum = $('.recharge-num').find('li');
    var rechargeNumTxt;
    $('.recharge-num').find('li').on('tap', function(){
    	rechargeNumTxt = parseInt($(this).text());
    	rechargeIpt.val(rechargeNumTxt);
    });

    rechargeIpt.on('blur', function(){
        var val = $(this).val();
        if( /^\d+$/.test(val) ) {
            if( parseInt(val) < 1 ) { 
                $.tips({
                    content:'请输入大于1的整数金额',
                    stayTime:2000,
                    type:"warn"
                })
            }
        }else{
            $.tips({
                content:'请输入整数金额',
                stayTime:2000,
                type:"warn"
            })
        }
	});
    $('.recharge-way').find('a').on('tap', function(){
    	if(false){
    		return false;
    	}
    });


    $('.lottery-result').find('dt').on('tap', function(){
        $(this).parent().toggleClass('lottery-result-hide');
    });

    

    // $(window).scroll(function() {
    //     if($(window).scrollTop() == $(document).height() - $(window).height()) {
    //         alert(1)
    //         //替换alert为Ajax
    //     }
    // });


    var tab = new Scroll('.ui-tab', {
        role: 'tab',
    });

	//订单列表调用
	var strCode = $("input[name='strCode']").val();
	$('.ui-tab-nav').find('li').on('tap', function(){
    	var id = $(this).attr('data');
		var content = $("#type"+id).find('li').html();
		if(content == null){
			$.ajax({
				type: 'post',
				url: '/app/mylottery/ajax_betlist',
				data: {strCode:strCode,cpage:1,type:id},
				success: function (response) {
					$('#type'+id).append(response);
				}
			});
		}
		$("input[name='type']").val(id);
    });
	//初始化分页
	var cpage = {
		1 : 1,
		2 : 1,
		3 : 1,
	};
	var stop = {
		1 : true,
		2 : true,
		3 : true
	}
	$(window).scroll(function() {
         if($(this).scrollTop() + $(window).height() + 10 >= $(document).height() && $(this).scrollTop() > 10) {
			 var id = $("input[name='type']").val();
			 if(stop[id]==true){
				stop[id]=false;  
				cpage[id]=cpage[id]+1;//当前要加载的页码
				$.ajax({
					type: 'post',
					url: '/app/mylottery/ajax_betlist',
					data: {strCode:strCode,cpage:cpage[id],type:id},
					beforeSend: loading(true),
					success: function (response) {
						loading(false);
						if(response){
							$('#type'+id).append(response);
							stop[id]=true;
						}
						
					}
				});
			 }
         }
    });
	//加载中
	function loading(display){
		if(display){
			$(".ui-loading-wrap").show();
		}else{
			$(".ui-loading-wrap").hide();
		}
	}

    // 公用active背景
    // $('.cp-list').find('li:not(input)').on('tap', function(){
    //     alert(1)
    // });
    
})