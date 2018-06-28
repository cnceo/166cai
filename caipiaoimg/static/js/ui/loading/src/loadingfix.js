/**
 * User: weblol
 * Date: 15-05-25
 * Time: 上午10:20
 */

define([
    'Zepto',
    'basic'
], function(Zepto, basic) {

    ! function($) {
        // 默认模板
        // var _loadingTpl='<div class="loading">'+
        // 				'<div class="loading-inner"><canvas id="animation_canvas" width="57" height="26"></canvas></div>'+
        // 				'<p><%=content%></p>'+
        // 				'</div>';
        var _loadingTpl = '<div class="loading">' +
            '<i></i>' +
            '<p><%=content%></p>' +
            '</div>';
        // 默认参数
        var defaults = {
            content: '加载中...'
        }



        // 创建canvas帧动画
        //   window['canvasgif'] = {
        // canvas:null,//canvas元素
        // context:null,//canvas环境
        // fps:30,//帧频
        // loopCount:1,//循环次数
        // tempCount:0,//当前的循环次数，用来计数

        // img_obj : {//图片信息保存变量
        // 	'source': null,
        // 	'current': 0,
        // 	'total_frames': 0,
        // 	'width': 0,
        // 	'height': 0
        // },

        // init:function(canvas,imgsrc,frames,fps,loopCount,fn){//初始化canvas和图片信息
        // 	var me = this;
        // 	me.canvas = canvas;
        // 	me.context = canvas.getContext("2d");
        // 	me.fps = fps || 30;
        // 	me.loopCount = loopCount || 1;
        // 	var img = new Image();
        // 	img.src = imgsrc || 'anim.png';
        // 	img.onload = function () { 
        // 	    me.img_obj.source = img;
        // 	    me.img_obj.total_frames = frames;
        // 	    me.img_obj.width = this.width/28;
        // 	    me.img_obj.height = this.height;
        // 	    me.loopDraw(fn);
        // 	}
        // },
        // draw_anim:function(context,iobj){//绘制单张图片
        // 	if (iobj.source != null){
        // 		context.drawImage(iobj.source, iobj.current * iobj.width, 0, iobj.width, iobj.height,0, 0, iobj.width, iobj.height);
        // 		iobj.current = (iobj.current + 1) % iobj.total_frames;
        // 		//如果不是无限循环，则需要计算当前循环次数
        // 		if(this.loopCount != -1 && iobj.current == iobj.total_frames - 1){
        // 			this.tempCount++;
        // 		}
        // 	}
        // },

        // render:function(canvas,imgsrc,frames,fps,loopCount,fn){
        // 	this.init(canvas,imgsrc,frames,fps,loopCount,fn);
        // },

        // loopDraw:function(fn){//循环绘制图片
        // 	var me = this;
        // 	var ctx = me.context;
        // 	var pic = me.img_obj;
        // 	var width = me.canvas.width,height = me.canvas.height;
        // 	var intervalid = setInterval((function(){
        // 	    ctx.clearRect(0,0,width,height);
        // 	     me.draw_anim(ctx,pic);
        // 	    if(me.loopCount != -1 && me.tempCount == me.loopCount){
        // 	        me.tempCount = 0;
        // 	        clearInterval(intervalid);
        // 	        ctx.clearRect(0,0,width,height);
        // 	        typeof fn == "function" && fn();
        // 	    }
        // 	}), 1000/this.fps);
        // }
        //   }


        // 构造函数
        var Loading = function(el, option, isFromTpl) {
            var self = this;
            this.element = $(el);
            this._isFromTpl = isFromTpl;
            this.option = $.extend(defaults, option);
            this.show();
        }
        Loading.prototype = {
            show: function() {
                var e = $.Event('loading:show');
                this.element.trigger(e);
                this.element.show();
                // window.canvasgif.render(document.getElementById('animation_canvas'),'../images/sprite-loading-canvas.png',28,14,-1,function(){});
            },
            hide: function() {
                var e = $.Event('loading:hide');
                this.element.trigger(e);
                this.element.remove();
            },
            mask: function() {
                var e = $.Event('loading:mask');
                this.element.trigger(e);
                this.element.addClass('loading-mask');
            }
        }

        function Plugin(option) {

            return $.adaptObject(this, defaults, option, _loadingTpl, Loading, "loading");
        }
        $.fn.loading = $.loading = Plugin;
    }(window.Zepto)

});