/** 
 *  @desc  绘制折线图
 *  @param {Object}  defaultParam  配置参数
 *  @param {Object}  el            canvas el
 *  @param {Array }  data          折线数据点
 *  @param {Object}  styleSet      样式设置   可省略
 *  @eg:
 *     var canvas2 = Charts({
 *          el:obj,
 *          data:[20,60,60,80,100,120,140,60,100],
 *          styleSet:{
 *              lineColor:'#8a93fe',
 *          }
 *      })
 */
;(function(window,undefined){
    function Charts(defaultParam){
        //获取canvas
        var can1 = defaultParam.el;
            //手机端自适应  设置canvas绘制元素宽度（默认300）
            //获取canvas绘制元素高度
            can1.height = can1.height;
            height = can1.height - 5,
            ctx = can1.getContext("2d"),
            //初始数据
            nums = defaultParam.data,
            //默认颜色
            setDefault = {
                styleSet:{
                    lineColor:'#ff984e',
                }
            }

        return new Charts.prototype.init(defaultParam,ctx,nums,can1.width,height);
    }

    Charts.prototype = {
        init: function(defaultParam,ctx,nums,wd,ht){
            //去除边界宽度
            var wid = wd;
            //获取数据的最大值并设置峰值0.88比例
            var maxPoint = this.maxData(nums) / 0.9;
            //参数合并
            defaultParam = this.extend(setDefault,defaultParam);
            //执行绘制函数
            this.drawLinearGradient(defaultParam,ctx,nums,wd,ht,wid,maxPoint);
            this.drawLine(defaultParam,ctx,nums,wd,ht,wid,maxPoint);
            this.drawOpacity(defaultParam,ctx,nums,wd,ht,wid,maxPoint);
            return this;
        },
        drawLine: function(defaultParam,ctx,nums,wd,ht,wid,maxPoint){
            for (i = 0;i < nums.length-1;i ++){
                //起始坐标
                var axiosY = ht - ht*nums[i]/maxPoint,
                    averNum= (wid/nums.length),
                    axiosX = i * averNum + 2,
                //终止坐标  
                axiosNY = ht - ht*nums[i+1]/maxPoint,
                axiosNX = (i+1) * averNum + 2;
                //划线
                ctx.beginPath();
                ctx.moveTo(axiosX,axiosY);
                ctx.lineTo(axiosNX,axiosNY);
                ctx.lineWidth = 2;
                ctx.strokeStyle = defaultParam.styleSet.lineColor;
                ctx.setLineDash([0]);
                ctx.closePath();
                ctx.stroke();
            }
        },
        drawLinearGradient: function(defaultParam,ctx,nums,wd,ht,wid,maxPoint){
            ctx.beginPath();
            ctx.lineWidth = 1;
            var x0 = 0;
            for (i = 0;i < nums.length-1;i ++){
                //起始坐标
                var axiosY = ht - ht*nums[i]/maxPoint -1,
                    averNum= (wid/nums.length),
                    axiosX = i * averNum + 2,
                //终止坐标  
                axiosNY = ht - ht*nums[i+1]/maxPoint -1,
                axiosNX = (i+1) * averNum + 2;
                //划线
                if(!i){
                    x0 = axiosX;
                }
            }            
            var my_gradient = ctx.createLinearGradient(0,0,0,ht+5);
            my_gradient.addColorStop(0,defaultParam.styleSet.topColor);
            my_gradient.addColorStop(1,defaultParam.styleSet.bottomColor);
            ctx.fillStyle = my_gradient;
            ctx.fillRect(x0,0,axiosNX-x0,ht+5);
        },
        drawOpacity: function(defaultParam,ctx,nums,wd,ht,wid,maxPoint){
            ctx.beginPath();
            
            for (i = 0;i < nums.length-1;i ++){
                //起始坐标
                var axiosY = ht - ht*nums[i]/maxPoint -1,
                    averNum= (wid/nums.length),
                    axiosX = i * averNum + 2,
                //终止坐标  
                axiosNY = ht - ht*nums[i+1]/maxPoint -1,
                axiosNX = (i+1) * averNum + 2;
                //划线
                if(!i){
                    ctx.moveTo(axiosX,0);
                }
                ctx.lineTo(axiosX,axiosY);
            }
            ctx.lineTo(axiosNX+2,axiosNY);
            ctx.lineTo(axiosNX+2,0);
            ctx.fillStyle = '#000000';
            ctx.globalCompositeOperation = "destination-out";
            ctx.closePath();
            ctx.fill();
            ctx.globalCompositeOperation = 'source-over';
        },
        maxData:function(arr){
            return Math.max.apply(null,arr)
        },
        minData:function(arr){
            return Math.min.apply(null,arr)
        },
        extend: function(defaults,newObj){
    　　　　for (var i in newObj) {
    　　　　　　defaults[i] = newObj[i];
    　　　　}
    　　　　return defaults;
        }
    }
    Charts.prototype.init.prototype = Charts.prototype;

    if(!window.Charts){
        window.Charts =  Charts;
    }

})(window,undefined);