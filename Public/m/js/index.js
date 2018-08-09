var bjsIndex = (function(){
	var bjsIndex = function(){
		this.init();
	}
	bjsIndex.prototype.init = function(){
		this.localPath = "http://bjs.bi:8080/award/";
		this.indexUrl = {
			starAdd: 'user/star/add',
			starRemove: 'user/star/remove',
			starGet: 'user/star/find'
		};
		//this._fastClick();
		this._initFontSize();
		this._resizeWindow();
		this._initSkin();
		this._initInputFocus();
		if($('.copy-btn')[0]){
			this._initClipboard();
		}
	}
	bjsIndex.prototype.initIndex = function(){
		this._initTabs();
		this._initSwiper();
		this._initFav();
		$(document).on('click','.assets-eyes',function(event){
			event.preventDefault();
			$(this).toggleClass('off');
		});
	}
	bjsIndex.prototype.initC2c = function(){
		this._initTabs();
		$('.input-group').on('focus','.form-control',function(){
			$(this).closest('.input-group').addClass('active');
		});
		$('.input-group').on('blur','.form-control',function(){
			$(this).closest('.input-group').removeClass('active');
		});
	}
	bjsIndex.prototype.initC2cMerchant = function(){
		var _this = this;
		this.initC2c();
	}
	bjsIndex.prototype.initMarket = function(){
		var txt = $('.market-search-input').val().trim();
		var _this = this;
		this._initFav();
		$(document).on('input propertychange keyup','.market-search-input',function(){
			var _txt = $(this).val().trim();
			if(txt !== _txt){
				txt = _txt;
				_this._toSearch(txt);
				if($('#bi-fav-btn').hasClass('active')){
					$('.bi-list-choose-fav').find('.bi-list-tbody-tr:not(".fav-cur")').addClass('hide');
				}
			}
		});
	}
	bjsIndex.prototype.initMy = function(){
		var _this = this;
		$(document).on('click','.my-heade-skin',function(){
			$(this).toggleClass('moon sun');
		});
	}
	bjsIndex.prototype.initMyAsset = function(){
		var _this = this;
		this._initTabs();
		this._initBack();
		var txt = $('.search-input').val().trim();
		var _this = this;
		$(document).on('input propertychange keyup','.search-input',function(){
			var _txt = $(this).val().trim();
			if(txt !== _txt){
				txt = _txt;
				_this._toSearch(txt,'.my-asset-list > ul','.my-asset-list-bi-icon');
			}
		});
	}
	bjsIndex.prototype.initUc = function(){
		var _this = this;
		this._initBack();
	}
	bjsIndex.prototype.initAddress = function(){
		var _this = this;
		this._initBack('#tibi-page');
	}
	bjsIndex.prototype.initPayment = function(){
		var _this = this;
		this._initBack('#payment-page');

		//添加支付地址
		$(document).on('click','#add-payment-btn',function(event){
			event.preventDefault();
			$('#payment-page').addClass('hide');
			$('#payment-address').removeClass('hide');
		});
		//点击返回按钮
		$(document).on('click','#payment-address .search-slide-back',function(event){
			event.preventDefault();
			$('#payment-page').removeClass('hide');
			$('#payment-address').addClass('hide');
		});
		//点击返回按钮
		$(document).on('click','.payment-address-page .search-slide-back',function(event){
			event.preventDefault();
			$('#payment-address').removeClass('hide');
			$(this).closest('.payment-address-page').addClass('hide');
		});
		//点击添加按钮
		$(document).on('click','.bjs-btn-pay-icon',function(){
			var id = $(this).data('id');
			$('#payment-address').addClass('hide');
			$('#'+id).removeClass('hide');
		});
		//预览图片
		$(document).on('click','.preview-btn',function(){
			if($(this).closest('.wrap-form-item').find('.upload-payment img')[0]){
				layer.load();
				var src = $(this).closest('.wrap-form-item').find('.upload-payment img')[0].src;
				var img = new Image();
				img.src = src;
				img.onload = function(){
					layer.closeAll('loading');
					layer.open({
						title: '图片预览'
						,btnAlign: 'c'
						,btn: ''
						,content: '<img src="'+src+'" style="max-width: 240px; width: 100%;" />'
					})
				}
				img.onerror = function(){
					layer.closeAll('loading');
					layer.msg('图片已损坏',{icon:0})
				}
			}else{
				layer.msg('请上传图片',{icon:0,time:1000});
			}
		});		
	}
	bjsIndex.prototype.initAssetDetail = function(){
		var _this = this;
		this._initBack();
		$(document).on('click','.asset-detail-filter-btn',function(){
			$('.asset-detail-filter-box').addClass('right-show');
		});
		$(document).on('click','.asset-detail-filter-box-bg',function(){
			$('.asset-detail-filter-box').removeClass('right-show');
		});
		$(document).on('click','.asset-detail-filter-box-cont a',function(event){
			event.preventDefault();
			var state = $(this).data('state');
			var text = $(this).text();
			if(state == 0){
				$('.asset-detail-filter-btn').text(text);
				$('.asset-detail-list').children().removeClass('hide');
			}else if(state > 0){
				$('.asset-detail-filter-btn').text(text);
				$('.asset-detail-list').children().addClass('hide');
				$('.asset-detail-list').children('[data-state="'+state+'"]').removeClass('hide');
			}
			$('.asset-detail-filter-box').removeClass('right-show');
		});
	}
	bjsIndex.prototype.initDelegation = function(){
		var _this = this;
		this._initBack();
		var state = 0;
		$(document).on('click','.delegation-tab-btn',function(){
			var $this = $(this);
			if($this.hasClass('active')){
				return false;
			}
			$this.addClass('active').siblings('.active').removeClass('active');
			state = $this.data('state');
			if(state == 0){
				$('.my-asset-list').children().removeClass('hide');
			}else{
				$('.my-asset-list').children().addClass('hide');
				$('.my-asset-list').children('[data-state="'+state+'"]').removeClass('hide');
			}
			if(txt){
				_this._toSearch(txt,'.my-asset-list > ul','.bjs-bi-name');
				if(state != 0){
					$('.my-asset-list').children(':not([data-state="'+state+'"])').addClass('hide');
				}
			}
		});
		var txt = $('.search-input').val().trim();
		var _this = this;
		$(document).on('input propertychange keyup','.search-input',function(){
			var _txt = $(this).val().trim();
			if(txt !== _txt){
				txt = _txt;
				_this._toSearch(txt,'.my-asset-list > ul','.bjs-bi-name');
				if(state != 0){
					$('.my-asset-list').children(':not([data-state="'+state+'"])').addClass('hide');
				}
			}
		});
	}
	bjsIndex.prototype.initPromotion = function(){
		var _this = this;
		this._initBack();		
	}
	bjsIndex.prototype.initComplaint = function(){
		var _this = this;
		this._initBack();
	}
	bjsIndex.prototype.initPay = function(){
		var _this = this;
		this._initFav();
		this._initTabs();
		//this._initProgress();
		this._initChooseBi();
		$('.form-item').on('focus','.form-input',function(){
			$(this).closest('.form-item').addClass('active');
		});
		$('.form-item').on('blur','.form-input',function(){
			$(this).closest('.form-item').removeClass('active');
		});
		$(document).on('click','.kline-btn',function(event){
			event.preventDefault();
			$('#trade-page').addClass('hide');
			$('#market-page').removeClass('hide');
		});
		$(document).on('click','#market-page .search-slide-back',function(event){
			event.preventDefault();
			$('#trade-page').removeClass('hide');
			$('#market-page').addClass('hide');
		});
	}

	bjsIndex.prototype.initLogin = function(){
		var _this = this;
		//password & text change
		$(document).on('click','.assets-eyes',function(event){
			event.preventDefault();
			$(this).toggleClass('off');
			var $input = $(this).closest('.wrap-form-item').find('.form-input-password');
			var val = $input.val();
			$input.val('');
			if($input.prop('type') == 'text'){
				$input.prop('type','password');
			}else{
				$input.prop('type','text');
			}
			$input.val(val);
		});
	}
	bjsIndex.prototype.initRegister = function(){
		var _this = this;
		this._initTabs();
		this.initLogin();		
	}
	bjsIndex.prototype.initResetPassword = function(){
		var _this = this;
		this._initTabs();
		this.initLogin();
		
	}
	bjsIndex.prototype.initOrder = function(){
		var _this = this;
		//关闭提示
		$(document).on('click','.order-detail-warning-close',function(event){
			event.preventDefault();
			$(this).closest('.order-detail-warning').addClass('hide');
		});
		//查看订单二维码
		$(document).on('click','.order-detail-qrcode',function(){
			layer.load();
			var src = $(this).data('img');
			var img = new Image();
			img.src = src;
			img.onload = function(){
				layer.closeAll('loading');
				layer.open({
					title: '收款二维码'
					,btnAlign: 'c'
					,btn: ''
					,content: '<img src="'+src+'" style="max-width: 240px; width: 100%;" />'
				})
			}
			img.onerror = function(){
				layer.closeAll('loading');
				layer.msg('图片已损坏',{icon:0})
			}
			return false;
		});
	}
	bjsIndex.prototype.initOrderPayment = function(){
		var _this = this;
		this.initOrder();
	}
	bjsIndex.prototype._initFav = function(){
		var _this = this;
		var userId = userid;
		var cid = market + 't';
		if(userId){
			_this._getFav(userId,function(result){
				result.data.forEach(function(item,index){
					if(item.coin == cid){
						$('.fav-btn').addClass('star-cur');
					}
					$('.bi-list-choose-fav').find('[data-name="'+item.coin+'"]').addClass('fav-cur');
				});				
			});
		}
		//切换自选区
		$(document).on('click','#bi-fav-btn',function(){
			if(!userId){
				layer.msg('请登录',{icon:0});
				return false;
			}
			$(this).parent().parent().find('.active').removeClass('active');
			$(this).addClass('active');
			if($('input[name="bi-keywords"]')[0]){
				_this._toSearch($('input[name="bi-keywords"]').val());
			}
			$('.bi-list-choose-fav').find('.bi-list-tbody-tr:not(".fav-cur")').addClass('hide');
		});
		//切换CNYT
		$(document).on('click','#bi-cnyt-btn',function(){
			if(!userId){
				return false;
			}
			$(this).parent().parent().find('.active').removeClass('active');
			$(this).addClass('active');
			$('.bi-list-choose-fav .bi-list-tbody-tr').removeClass('hide');
			if($('input[name="bi-keywords"]')[0]){
				_this._toSearch($('input[name="bi-keywords"]').val());
			}
		});
		//收藏、移除
		var timer = 0, num = 0;
		$(document).on('click','.fav-btn',function(event){
			event.preventDefault();
			if(!userId){
				layer.msg('请登录',{icon:0});
				return false;
			}
			var $this = $(this);
			/*num ++;
			var _timer = new Date().getTime();
			if(!timer){
				timer = _timer;
			}else if(_timer - timer < 3000 && num > 3){
				layer.msg('操作太快,休息一下吧~',{icon:5});
				setTimeout(function(){
					num = 0;
				},3000);
				return false;
			}else{
				timer = _timer;
			}*/
			if($(this).hasClass('star-cur')){
				//执行删除操作
				_this._removeFav(userId,cid,function(){
					$this.removeClass('star-cur');
					$('.bi-list-choose-fav').find('[data-name="'+cid+'"]').removeClass('fav-cur').addClass('hide');
					layer.msg('移除收藏成功',{icon:0,time:600});
				});				
			}else{
				//执行添加操作
				_this._addFav(userId,cid,function(){
					$this.addClass('star-cur');
					$('.bi-list-choose-fav').find('[data-name="'+cid+'"]').addClass('fav-cur').removeClass('hide');
					layer.msg('收藏成功',{icon:1,time:600});
				});
			}
		});
	}
	bjsIndex.prototype._getFav = function(userId,callback){
		var _this = this;		
		_this._fetch(_this.localPath+_this.indexUrl.starGet,{userId:userId},'post','json',callback);
	}
	bjsIndex.prototype._addFav = function(userId,cid,callback){
		var _this = this;		
		_this._fetch(_this.localPath+_this.indexUrl.starAdd,{userId:userId,coinType:cid},'post','json',callback);
	}
	bjsIndex.prototype._removeFav = function(userId,cid,callback){
		var _this = this;
		_this._fetch(_this.localPath+_this.indexUrl.starRemove,{userId:userId,coinType:cid},'post','json',callback);
	}
	bjsIndex.prototype._initBack = function(el){
		var back = ( el ? el + ' ' : '' ) + '.search-slide-back';
		$(document).on('click',back,function(event){
			event.preventDefault();
			window.history.back();
		});
	}
	bjsIndex.prototype._initClipboard = function(){
		//复制到剪贴板
		var clipboard = new ClipboardJS('.copy-btn');	
		clipboard.on('success', function(e){
        	layer.msg('复制成功',{time:800,icon:1});
        });
        clipboard.on('error', function(e){
        	layer.msg('复制失败,请手动复制',{time:800,icon:2});
        });
	}
	bjsIndex.prototype._initChooseBi = function(){
		var _this = this;
		$(document).on('click','.bjs-bi-choose-name',function(){
			$('.bjs-bi-choose-show').slideToggle(200);
			$('.search-btn-box,.search-slide-box').toggleClass('hide');
		});
		$(document).on('click','#trade-page .search-slide-back',function(event){
			event.preventDefault();
			$('.bjs-bi-choose-show').slideToggle(200);
			$('.search-btn-box,.search-slide-box').toggleClass('hide');
		});
		var txt = $('.search-input-box input').val().trim();
		var _this = this;
		$(document).on('input propertychange keyup','.search-input-box input',function(){
			var _txt = $(this).val().trim();
			if(txt !== _txt){
				txt = _txt;
				_this._toSearch(txt);
				if($('#bi-fav-btn').hasClass('active')){
					$('.bi-list-choose-fav').find('.bi-list-tbody-tr:not(".fav-cur")').addClass('hide');
				}
			}
		});
	}
	bjsIndex.prototype._toSearch = function(txt,list,name){
		var _this = this;
		var list = list || '.bi-list-tbody .bi-list-tbody-tr';
		var name = name || '.bi-list-tbody-tr-name';
		var $list = $(list);
		if($list[0]){
			$list.each(function(){
				var _txt = $(this).find(name).text();
				var _txt2 = $(this).data('name');
				txt = txt.replace(/\s/g,'');
				_txt = _txt.replace(/\s/g,'');
				_txt2 = _txt2.replace(/\s/g,'');
				var patt1 = new RegExp(txt,'i');
				if(patt1.test(_txt) || patt1.test(_txt2)){
					$(this).removeClass('hide');
				}else{
					$(this).addClass('hide');
				}
			});
		}
	}
	bjsIndex.prototype._initSwiper = function(){
		var swiper = new Swiper('.swiper-container', {
			slidesPerView: 1,
			spaceBetween: 30,
			loop: true,
			pagination: {
				el: '.swiper-pagination',
				clickable: true,
			}
		});
	}
	bjsIndex.prototype._fastClick = function(){
	    var u = navigator.userAgent, app = navigator.appVersion;
	    var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //g
	    var isIOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端
	    if (isAndroid) {
	    	//console.log('这个是安卓操作系统');
	       //这个是安卓操作系统
	    }
	    if (isIOS) {
	    	//console.log('这个是ios操作系统');
	    	FastClick.attach(document.body);
	　　　　//这个是ios操作系统
	    }		
	}
	bjsIndex.prototype._initFontSize = function(){
		var windowWidth = $('body').width();
		var fontSize = windowWidth > 1125 ? 10 : windowWidth*10/1125;
		$('html').css({
			'fontSize': fontSize*10 + 'px'
		});
	}
	bjsIndex.prototype._resizeWindow = function(){
		var _this = this;
		$(window).resize(function(){
			_this._initFontSize();
		});
	}
	bjsIndex.prototype._initTabs = function(){
		$(document).on('click','.bjs-tab-btn',function(event){
			event.preventDefault();
			var $this = $(this);
			if($this.hasClass('active')){
				return false;
			}
			var id = $this.data('id');
			//console.log(id)
			$this.closest('.bjs-tab-head').find('.active').removeClass('active').end().end().addClass('active');
			$('#'+id).addClass('active').siblings('.active').removeClass('active');
		});
	}
	bjsIndex.prototype._initSkin = function(){
		var _this = this;
		if(typeof layer != 'undefined'){
			layer.config({
			    extend: 'myskin/style.css', //加载您的扩展样式
			    skin: 'myskin'
			});
		}
	}
	bjsIndex.prototype._initInputFocus = function(){		
		$('input[type="password"],input[type="text"],input[type="number"],input[type="email"],input[type="phone"]').on('focus',function(){
			setTimeout(function(){
				//this.scrollIntoView(true)
    			this.scrollIntoViewIfNeeded();
    		}.bind(this),300);
		});
	}
	bjsIndex.prototype._initProgress = function(callback){
		if(!$('.bjs-progress')[0]){
			return false;
		}
		var state = false, click = false;
		var left = 0, width = 0;
		var $this;

		//$(document).on('touchstart','.bjs-progress-btn',start);
		$('.bjs-progress-up .bjs-progress-btn')[0].addEventListener('touchstart',start, false);
		$('.bjs-progress-down .bjs-progress-btn')[0].addEventListener('touchstart',start, false);
		document.addEventListener('touchmove',move, false);
		document.addEventListener('touchend',end, false);
		function start(event){
			$this = $(event.target);
			event.preventDefault();
			//console.log(event.touches[0]);
			width = $this.closest('.bjs-progress-top').width();
			left = $this.closest('.bjs-progress-top').offset().left;
			state = true;
		}
		function move(event){
			if(state){				
				var x = event.pageX || event.touches[0].pageX;
				var _left = x - left;
				_left = _left > width ? width : (_left < 0 ? 0 : _left);
				var pLeft = (_left / width * 100)>>0;
				$this.css('left',pLeft+'%');
				$this.siblings('.bjs-progress-line').css('width',pLeft+'%');
				$this.closest('.bjs-progress').find('.bjs-progress-num').text(pLeft+'%');
				$this.siblings('.bjs-progress-points').children().each(function(){
					if($(this).data('point') > pLeft){
						$(this).removeClass('active');
					}else{
						$(this).addClass('active');
					}
				});
				if(callback){
					callback(pLeft,$this.closest('.bjs-progress')[0]);
				}
			}			
		}
		function end(event){
			if(state){
				state = false;
			}
		}
		$(document).on('click','.bjs-progress-points',function(event){
			event.preventDefault();
			state = true;
			$this = $(this).siblings('.bjs-progress-btn');
			//console.log(event.touches[0]);
			width = $this.closest('.bjs-progress-top').width();
			left = $this.closest('.bjs-progress-top').offset().left;
			
			move(event);
		});
	}
	bjsIndex.prototype._fetch = function(url,data,type,dataType,success,error){
		$.ajax({
			url: url,
			data: data,
			dataType: dataType,
			type: type,
			success: success,
			error: error
		});
	}
	return bjsIndex;
})();


