var bjsIndex = (function(){
	var bjsIndex = function(){
		this.localPath = typeof localPath === 'undefined' ? 'https://service.bjs.bi/award/' : localPath;
		this.resourcesPath = typeof resourcesPath === 'undefined' ? '' : resourcesPath;
		this.indexUrl = {
			day3: 'market/day3',
			starAdd: 'user/star/add',
			starRemove: 'user/star/remove',
			starGet: 'user/star/find'
		};
		this.fontSize = 10;
		this.userInfo = this.getUserInfo();
		this.init();
	}
	bjsIndex.prototype.init = function(){
		this.initSkin();
		this.initLanguage();
		//全局通用
		//$(window).resize(this.initFontSize);
		//this.initFontSize();
		//this.initProgress(callback);//实例化进度条		
		Array.prototype.insertSort = function(fn){
		    var array = this;
		    var fn = fn || function(a,b){
		        return b > a;
		    }
		    for(var i = 1; i < array.length; i++){
		        var key = array[i];
		        var j = i - 1;
		        while(j >= 0 && fn(array[j],key)){
		            array[j + 1] = array[j];
		            j--;
		        }
		        array[j + 1] = key;
		    }
		    return array;
		}
		Object.defineProperty(Array.prototype, 'insertSort', {'enumerable': false});
	}
	bjsIndex.prototype.initIndex = function(){
		//初始化首页
		var _this = this;	
		this.styleSet = {
			white : {
				lineColor: ['rgb(0, 183, 183)' , 'rgb(255, 105, 105)'],//蓝 : 红
            	topColor: ['rgba(0, 183, 183, 0.4)' , 'rgba(255, 105, 105, 0.4)'],
            	bottomColor: ['rgba(255,255,255,0)' , 'rgba(255,255,255,0)'],
			},
			black : {
		        lineColor: ['rgb(0, 255, 255)' , 'rgb(255, 105, 105)'],//蓝 : 红
	            topColor: ['rgba(0, 255, 255, 0.4)' , 'rgba(255, 105, 105, 0.4)'],
	            bottomColor: ['rgba(0,0,0,0)' , 'rgba(0,0,0,0)'],
		    }
		};	
		this.initSearch();
		this.initBanner();
		this.getBiInfo();
		this.initStar();
		this.initIndexSort();
		this.initPaomadeng();
		this.initAdvertising();

		$(document).on('click','.style-white,.style-black',function(){
			if(!$(this).hasClass('change')){
				return false;
			}
			_this.initCanvas(_this.getBiInfoData);
		});
	}
	bjsIndex.prototype.initAdvertising = function(){
		var $box = $('.bjs-news-slide > ul');
		var length = $box.children().size();
		if(length < 4){
			return false;
		}
		$box.append($box.html());
		var state = true;
		var index = 0;
		$(document).on('click','.bjs-news-btn-right',function(){
			if(state){
				state = false;
				index --;
				if(index + length < 0){
					$box.css('marginLeft','0%');
					index = -1;
				}
				$box.stop().animate({
					marginLeft: index * 25 + '%'
				},300,function(){
					state = true;
				});
			}			
		});
		$(document).on('click','.bjs-news-btn-left',function(){
			if(state){
				state = false;
				index ++;

				if(index > 0){
					index = 1 - length;
					$box.css('marginLeft',- length * 25 + '%');
				}
				$box.stop().animate({
					marginLeft: index * 25 + '%'
				},300,function(){
					state = true;
				});
			}
		});
	}
	bjsIndex.prototype.initPaomadeng = function(){
		var width = 0;
		$('.bjs-notice-list-slide').children().each(function(i,item){
			width += $(item).outerWidth(true);
		});
		$('.bjs-notice-list-slide').append($('.bjs-notice-list-slide').html());
		var timer = null;
		var num = 0;

		function run(){
			num ++;
			if(num >= width){
				num = 0;
			}
			$('.bjs-notice-list').scrollLeft(num);
		}
		timer = setInterval(run,50);	
		$('.bjs-notice-list').hover(function(){
			if(timer){
				clearInterval(timer);
				timer = null;
			}
		},function(){
			timer = setInterval(run,50);
		});
	}
	bjsIndex.prototype.initIndexSort = function(){
		var _this = this;		
		$('.bjs-tab-thead').on('click','.curr-base',function(){
			var $this = $(this);
			var index = $this.parent().index();
			var state = $this.data('desc') || false;
			$this.closest('.bjs-tab-thead').find('.active').removeClass('active');
			$this.closest('.bjs-tab-table').find('.bjs-tab-tbody').each(function(){
				arr = [];
				var _html = '';
				$(this).find('.bjs-tab-tr').each(function(i,item){
					var obj = {};
					if(index == 0){						
						obj.item = item;
						obj.key = $(this).data('id');
						arr.push(obj);
					}else{				
						obj.item = item;
						var text = $(this).children().eq(index).text();
						text = text.replace(/(^[^\d\-]*|[^?=\d\.])/g,'');
						obj.key = parseFloat(text);
						arr.push(obj);
					}
				});
				if(state){
					arr.insertSort(function(a,b){
						return a.key > b.key;
					});
					$this.find('.bjs-sort-down').addClass('active');
					$this.find('.bjs-sort-up').removeClass('active');
				}else{					
					arr.insertSort(function(a,b){
						return a.key < b.key;
					});
					$this.find('.bjs-sort-up').addClass('active');
					$this.find('.bjs-sort-down').removeClass('active');
				}
				var $box = $(this).clone();
				$box.empty();
				arr.map(function(item,key,ary){
					$box.append(item.item);
				});
				$(this).html($box.html());
				_this.initCanvas(_this.getBiInfoData);
				_this.refreshIScroll('page-left-iscroll-wrap');
			});
			$this.data('desc',!state);
		});
	}
	bjsIndex.prototype.initRegister = function(){
		this.initRegisterSelect();
		this.initRegisterSubmit();
		//submit 事件
		$('.register-form').on('keyup','input',function(e){
			if(e.keyCode == 13){
				$(this).closest('.register-list').find('.register-form-submit').click();
			}
		});
	}
	bjsIndex.prototype.initRegisterSubmit = function(){
		//注册方式切换
		$('.register-head').on('click','a',function(event){
			event.preventDefault();
			if($(this).hasClass('active')){
				return false;
			}else{
				$(this).addClass('active').siblings().removeClass('active');
				$('#'+this.id+'-form').addClass('active').siblings().removeClass('active');
			}
		});
		//是否勾选了用户协议
		$('.register-agreement').on('change','input[type="checkbox"]',function(){
			var $submit = $(this).closest('.register-list').find('.register-form-submit');
			if(this.checked){
				$submit.removeClass('disabled-submit-btn');
			}else{
				$submit.addClass('disabled-submit-btn');
			}
		});
		//注册按钮
		$('.register-form-submit').on('click',function(){
			if($(this).hasClass('disabled-submit-btn')){
				return false;
			}
		});
	}
	bjsIndex.prototype.initC2c = function(){
		var _this = this;
		//展开订单详情
		$('.c2c-order-table').on('click','.open-order-btn',function(){
			var $this = $(this);
			var $detail = $this.closest('tr').next().next();
			if(!$detail.hasClass('order-detail-list')){
				return false;
			}
			if($this.hasClass('close-order-btn')){
				$this.removeClass('close-order-btn');
				$detail.addClass('hide');
			}else{
				$this.addClass('close-order-btn');
				$detail.removeClass('hide');
			}
		});
		//关闭订单过期提示
		$('.c2c-order-table').on('click','.order-detail-warning-close',function(){
			$(this).closest('.order-detail-warning').addClass('hide');
			return false;
		});
		//查看订单二维码
		$('.c2c-order-table').on('click','.order-detail-qrcode',function(){
			var src = $(this).data('img');
			var img = new Image();
			img.src = src;
			img.onload = function(){
				layer.open({
					title: '收款二维码'
					,btnAlign: 'c'
					,btn: ''
					,content: '<img src="'+src+'" style="max-width: 300px; width: 100%;" />'
				})
			}
			img.onerror = function(){
				layer.msg('图片已损坏',{icon:0})
			}
			return false;
		});
		//订单状态切换
		$('.c2c-order-title').on('click','a',function(){
			return false;
		});
		//submit 事件
		$('.trading-buy').on('keyup','input',function(e){
			if(e.keyCode == 13){
				$('.trading-buy-btn').click();
			}
		});
		$('trading-sell').on('keyup','input',function(e){
			if(e.keyCode == 13){
				$('.trading-sell-btn').click();
			}
		});
		this.initClipboard();
	}
	bjsIndex.prototype.initClipboard = function(){
		//复制到剪贴板
		var clipboard = new ClipboardJS('.copy-btn');	
		clipboard.on('success', function(e){
        	layer.msg('复制成功',{time:800,icon:1});
        });
        clipboard.on('error', function(e){
        	layer.msg('复制失败',{time:800,icon:2});
        });
	}
	bjsIndex.prototype.initPromotion = function(){
		this.initClipboard();
	}
	bjsIndex.prototype.initRecharge = function(){
		this.initClipboard();
		$('.select4').select2({
			minimumResultsForSearch: -1,
		    dropdownAutoWidth: true
		});
	}
	bjsIndex.prototype.initGoogleAuthenticator = function(){
		this.initClipboard();
		//submit 事件
		$('.form-input').on('keyup',function(e){
			if(e.keyCode == 13){
				$(this).closest('.default-form').find('.default-form-submit').trigger('click');
			}
		});
	}
	bjsIndex.prototype.initAuthentication = function(){
		//submit 事件
		$('.form-input').on('keyup',function(e){
			if(e.keyCode == 13){
				$(this).closest('.default-form').find('.default-form-submit').trigger('click');
			}
		});

		$('.select3').select2({
			minimumResultsForSearch: -1,
			dropdownAutoWidth: true
		});

		var $bank_card_pay = $('#bank-card-pay'), $alipay_pay = $('#alipay-pay'), $wechat_pay = $('#wechat-pay');
		$('#choose-nationality').change(function(){
			var val = this.value;
			switch(val){
				case '01' : break;
				case '02' : break;
				case '03' : break;
			}
		});

	}
	bjsIndex.prototype.initSetPay = function(){
		//submit 事件
		$('.form-input').on('keyup',function(e){
			if(e.keyCode == 13){
				$(this).closest('.default-form').find('.default-form-submit').trigger('click');
			}
		});

		$('.select3').select2({
			minimumResultsForSearch: -1,
			dropdownAutoWidth: true
		});

		var $bank_card_pay = $('#bank-card-pay'), $alipay_pay = $('#alipay-pay'), $wechat_pay = $('#wechat-pay');
		$('#choose-pay').change(function(){
			var val = this.value;
			switch(val){
				case '01' : $bank_card_pay.removeClass('hide'); $alipay_pay.addClass('hide'); $wechat_pay.addClass('hide'); break;
				case '02' : $bank_card_pay.addClass('hide'); $alipay_pay.addClass('hide'); $wechat_pay.removeClass('hide'); break;
				case '03' : $bank_card_pay.addClass('hide'); $alipay_pay.removeClass('hide'); $wechat_pay.addClass('hide'); break;
			}
		});
		//查看订单二维码
		$(document).on('click','.order-detail-qrcode',function(){
			var src = $(this).data('img');
			var img = new Image();
			img.src = src;
			img.onload = function(){
				layer.open({
					title: '收款二维码'
					,btnAlign: 'c'
					,btn: ''
					,content: '<img src="'+src+'" style="max-width: 300px; width: 100%;" />'
				})
			}
			img.onerror = function(){
				layer.msg('请上传图片',{icon:0})
			}
			return false;
		});
	}
	bjsIndex.prototype.initArticle = function(){
		$('.bjs-aboutUs').addClass('hide');
	}
	bjsIndex.prototype.initVote = function(timer){
		$('.vote-list').on('click','.vote-bi-check',function(){
			var $this = $(this);
			if($this.text().trim() == '查看介绍'){
				$this.text('收起介绍');
			}else{
				$this.text('查看介绍');
			}
			$this.closest('li').find('.vote-bi-detail').slideToggle(300);
		});
		var $timer = $('.vote-title-timer');
		var time = 0;
		if(timer && !isNaN(timer)){
			runTime();
		}
		function runTime(){
			var date = new Date().getTime();
			if(date > timer){
				return false;
			}
			time = timer - date;
			setInterval(function(){
				run();
			},1000);
		}
		function run(){
			time -= 1000;
			var day = (time / (1000*60*60*24)) >> 0;
			var hours = ((time - day * (1000*60*60*24)) / (1000*60*60)) >> 0;
			var minutes = ((time - day * (1000*60*60*24) - hours * (1000*60*60)) / (1000*60)) >> 0;
			var seconds = ((time - day * (1000*60*60*24) - hours * (1000*60*60) - minutes * (1000*60)) / 1000) >> 0;
			var _html = '还剩：<em>'+day+'</em>天<em>'+hours+'</em>时<em>'+minutes+'</em>分<em>'+seconds+'</em>秒';
			$timer.html(_html);
		}
		this.initVoteList();
	}
	bjsIndex.prototype.initVoteList = function(){
		this.initClipboard();
	}
	bjsIndex.prototype.initCoins = function(){
		var _this = this;
		_this.keywords = 'default'; //default logogram mvalue
		_this.$list = $('.coins-item')[0] ? $('.coins-list').clone() : null;
		$(document).on('click','.coins-search-list a',function(event){
			event.preventDefault();
			if(!_this.$list){
				return false;
			}
			$(this).addClass('active').siblings().removeClass('active');
			_this.keywords = $(this).data('keywords');
			_this._toSortCoins();
		});
		var $input = _this.$input = $('.coins-search-box input[name="keywords"]');
		if(!$input[0]){
			return false;
		}
		var txt = $input.val().trim();
		$input.on('keyup keydown change input',function(event){
			var _txt = $(this).val().trim();
			if(txt !== _txt){
				txt = _txt;
				_this._toSearchCoins(txt);
			}
		});
	}
	bjsIndex.prototype.initCoinsDetail = function(){
		this.initIScroll();
		var _this = this;
		_this.keywords = 'default'; //default logogram mvalue
		_this.$list = [];
		$(document).on('click','.coins-list-detail-search a',function(event){
			event.preventDefault();
			if(!_this.$list.length){
				$('.bjs-tab-tbody').each(function(){
					_this.$list.push($(this).clone())
				});
			}
			$('.coins-list-detail-search .active').removeClass('active');
			$(this).addClass('active');
			_this.keywords = $(this).data('keywords');
			_this._toSortCoinsDetail();
		});

		//搜索
		_this.initSearch();
	}
	bjsIndex.prototype._toSortCoinsDetail = function(){
		var _this = this;
		if(_this.keywords == 'default'){
			$('.bjs-tab-tbody').each(function(index,item){
				$(this).html(_this.$list[index].html())
			});
			_this.toSearch($('.coins-search-box input[name="keywords"]').val().trim());
			return false;
		}
		var $list = _this.$list;
		$('.bjs-tab-tbody').each(function(){
			var arr = [];
			$(this).children().each(function(){
				arr.push({
					el: this,
					keywords: $(this).data(_this.keywords)
				});
			});
			arr.insertSort(function(a,b){
				return a.keywords < b.keywords;
			});
			var $_html = $('<div></div>');
			for(var i = 0; i < arr.length; i++){
				$_html.append(arr[i].el);
			}
			$(this).html($_html.html());
		});
		
	}
	bjsIndex.prototype._toSearchCoins = function(txt){
		console.log(txt)
		var _this = this;
		var $list = $('.coins-list .coins-item');
		if($list[0]){
			$list.each(function(){
				var _txt = $(this).find('.coins-show').text();
				txt = txt.replace(/\s/g,'');
				_txt = _txt.replace(/\s/g,'');
				var patt1 = new RegExp(txt,'i');
				if(patt1.test(_txt)){
					$(this).removeClass('hide');
				}else{
					$(this).addClass('hide');
				}
			});
		}
	}
	bjsIndex.prototype._toSortCoins = function(){
		var _this = this;
		if(_this.keywords == 'default'){
			$('.coins-list').html(_this.$list.html());
			_this._toSearchCoins(_this.$input.val().trim());
			return false;
		}
		var $list = _this.$list;
		var arr = [];
		$('.coins-list').children().each(function(){
			arr.push({
				el: this,
				keywords: $(this).data(_this.keywords)
			});
		});
		arr.insertSort(function(a,b){
			return a.keywords < b.keywords;
		});
		var $_html = $('<div></div>');
		for(var i = 0; i < arr.length; i++){
			$_html.append(arr[i].el);
		}
		$('.coins-list').html($_html.html());
	}
	bjsIndex.prototype.initTrade = function(){
		var _this = this;
		$('body').addClass('w1280');
		$('.bjs-aboutUs').addClass('hide');
		$('.header .window-wrap').removeClass('w1500');
		$('.my-tab-head').on('click','a',function(event){
			event.preventDefault();
			var $this = $(this);
			if($this.hasClass('active')){
				return false;
			}
			var index = $this.index();
			var $unit = $this.closest('.my-tab-box').find('.my-tab-unit').eq(index);
			$this.addClass('active').siblings('.active').removeClass('active');
			$unit.addClass('active').siblings('.active').removeClass('active');
			var id = $unit.find('.my-tab-scroll-cont')[0].id;
			_this.refreshIScroll(id);
		});
		this.initIScroll();

		this.initSearch();
		this.initStar();
		this.initIndexSort();

		//submit 事件
		$('.trading-buy').on('keyup','input',function(e){
			if(e.keyCode == 13){
				$('.trading-buy-btn').click();
			}
		});
		$('trading-sell').on('keyup','input',function(e){
			if(e.keyCode == 13){
				$('.trading-sell-btn').click();
			}
		});
	}
	bjsIndex.prototype.initTradingView = function(){
		/*TradingView.onready(function(){
			var widget = window.tvWidget = new TradingView.widget({
				// debug: true, // uncomment this line to see Library errors and warnings in the console
				//fullscreen: true,
				symbol: 'AAPL',
				interval: 'D',
				height:'100%',
				width: '100%',
				container_id: "tv_chart_container",
				//	BEWARE: no trailing slash is expected in feed URL
				datafeed: new Datafeeds.UDFCompatibleDatafeed("https://demo_feed.tradingview.com"),
				library_path: "tradingview/charting_library/",
				locale: "zh",
				//	Regression Trend-related functionality is not implemented yet, so it's hidden for a while
				drawings_access: { type: 'black', tools: [ { name: "Regression Trend" } ] },
				disabled_features: [
					"use_localstorage_for_settings",
					"left_toolbar",
					"header_widget_dom_node",
					"header_symbol_search",
					"header_resolutions",
					"header_chart_type",
					"header_undo_redo",
					"header_indicators",
					"header_settings",
					"header_fullscreen_button",
					"header_compare",
					"header_saveload",
					"header_screenshot"
				],
				enabled_features: ["study_templates"],
				charts_storage_url: 'https://saveload.tradingview.com',
                charts_storage_api_version: "1.1",
				client_id: 'tradingview.com',
				user_id: 'public_user_id'
			});
		});*/
	}
	bjsIndex.prototype.initTradeProfession = function(){
		this._initResizeWindow();
		this.initTrade();
	}
	bjsIndex.prototype._initResizeWindow = function(){
		var _this = this;
		$(window).resize(resizeWindow);
		var $profession_page_left = $('.profession-page-left');//左栏

		var $profession_page_right = $('.profession-page-right');//右栏

		function resizeWindow(){
			var height = $(window).height();
			$profession_page_left.height(height);
			$profession_page_right.height(height);
			for(var i in _this.iscroll){
				_this.refreshIScroll(i);
			}
		}
		resizeWindow();
	}
	bjsIndex.prototype.refreshIScroll = function(id){
		try{
			this.iscroll[id].refresh();
		}catch(e){
			console.log(id+' is not iscroller !');
		}
		
	}
	bjsIndex.prototype.initIScroll = function(){
		this.iscroll = {};
		var options = { mouseWheel: true, click: true, scrollX: false, mouseWheelSpeed: 10, scrollbars: 'custom',bounce: false ,disableTouch: true/*, disablePointer: true, disableMouse: true*/, preventDefault: false};
		var _iscroll = [
			'article-wrap-iscroll',
			'page-left-iscroll-wrap',
			'my-tab-scroll-box-0',
			'my-tab-scroll-box-1',
			'my-tab-scroll-box-2',
			'my-tab-scroll-box-3',
			'my-tab-scroll-box-4',
			'my-tab-scroll-box-6',
			'my-tab-scroll-box-a',
			'my-tab-scroll-box-b',
			'my-tab-scroll-box-c',
			'my-tab-scroll-box-d',
			'my-tab-scroll-box-e',
			'my-tab-scroll-box-f'
		];
		for(var i = 0; i < _iscroll.length; i++){
			var $iscroll = $('#'+_iscroll[i]);
			if($iscroll[0]){
				this.iscroll[_iscroll[i]] = new IScroll('#'+_iscroll[i], options);
			}
		}		
	}
	bjsIndex.prototype.initRegisterSelect = function(){
		function formatState(state){
			var baseUrl = "";
			var $state = $(
				'<span><img src="images/language.png" class="img-flag" /> ' + state.text + '</span>'
			);
			return $state;
	    }
	    $(".select2").select2({
	    	minimumResultsForSearch: -1,
	    	dropdownAutoWidth: true,
	        templateResult: formatState
	    });
	    $(".select3").select2({
	    	minimumResultsForSearch: -1,
	    	dropdownAutoWidth: true,
	    });
	}
	bjsIndex.prototype.resetStar = function(){
		var _this = this;
		$('.bjs-tab-tbody').find('.bjs-star').removeClass('star-cur');
		for(var i in _this.fav){
			if(_this.fav[i]){
				$('.bjs-tab-tbody').find('[data-id="'+i+'"]').find('.bjs-star').addClass('star-cur');
			}
		}
	}
	bjsIndex.prototype.initTradeStar = function(){
		var _this = this;

		$('.bjs-tab-tbody').find('.bjs-star').removeClass('star-cur');

		if(_this.userInfo.userId != ''){
			_this.getStar(function(e){
				if(e.code == '10000' && e.data.length){
					_this.fav = {};
					for(var i = 0; i < e.data.length; i++){
						_this.fav[e.data[i].coin] = 1;
						$('.bjs-tab-tbody').find('[data-id="'+e.data[i].coin+'"]').find('.bjs-star').addClass('star-cur');
					}
				}
			});
		}
	}
	bjsIndex.prototype.initStar = function(){
		var _this = this;
		_this.fav = {};

		$('.bjs-tab-tbody').find('.bjs-star').removeClass('star-cur');

		if(_this.userInfo.userId != ''){
			_this.getStar(function(e){
				if(e.code == '10000' && e.data.length){
					for(var i = 0; i < e.data.length; i++){
						_this.fav[e.data[i].coin] = 1;
						$('.bjs-tab-tbody').find('[data-id="'+e.data[i].coin+'"]').find('.bjs-star').addClass('star-cur');
					}
				}
			});
		}
		//收藏、移除
		$('.bjs-tab-tbody').on('click','.bjs-star',function(){
			if(_this.userInfo.userId == ''){
				layer.msg('请登录',{icon:0});
				return false;
			}
			var $this = $(this);
			var cid = $this.closest('.bjs-tab-tr').data('id');
			if($(this).hasClass('star-cur')){
				//执行删除操作
				_this.removeStar(cid,function(){
					_this.fav[cid] = 0;
					$this.removeClass('star-cur');
					if($('.localStorage-star').hasClass('active')){
						$this.closest('.bjs-tab-tr').addClass('hide');
					}
				});
			}else{
				//执行添加操作
				_this.addStar(cid,function(){
					_this.fav[cid] = 1;
					$this.addClass('star-cur');
				});
			}
		});
		//自选区
		$('.bjs-tab-head').on('click','.localStorage-star',function(){
			if(_this.userInfo.userId == ''){
				layer.msg('请登录',{icon:0});
				return false;
			}
			$('.bjs-tab-tbody .bjs-tab-tr').addClass('hide');
			$(this).closest('ul').find('.active').removeClass('active').end().end().addClass('active');
			for(var i in _this.fav){
				if(_this.fav[i]){
					$('.bjs-tab-tbody').find('[data-id="'+i+'"]').removeClass('hide');					
				}
			}
		});
		//对CNYT交易区
		$('.bjs-tab-head').on('click','a',function(){
			if(!$(this).hasClass('active') && $(this).parent().index() == 0){
				$('.bjs-tab-tbody .bjs-tab-tr').removeClass('hide');
				$(this).closest('ul').find('.active').removeClass('active').end().end().addClass('active');
			}
		});
	}
	bjsIndex.prototype.getStar = function(callback){
		var _this = this;
		_this.fetch(_this.localPath+_this.indexUrl.starGet,{userId:_this.userInfo.userId},'post','json',callback);
	}
	bjsIndex.prototype.addStar = function(cid,callback){
		var _this = this;		
		//_this.fav[cid] = 1;
		//localStorage.fav = JSON.stringify(_this.fav);
		//callback();
		_this.fetch(_this.localPath+_this.indexUrl.starAdd,{userId:_this.userInfo.userId,coinType:cid},'post','json',callback);
	}
	bjsIndex.prototype.removeStar = function(cid,callback){
		var _this = this;
		//_this.fav[cid] = 0;
		//localStorage.fav = JSON.stringify(_this.fav);
		//callback();
		_this.fetch(_this.localPath+_this.indexUrl.starRemove,{userId:_this.userInfo.userId,coinType:cid},'post','json',callback);
	}
	bjsIndex.prototype.getBiInfo = function(){
		var _this = this;
		_this.fetch(_this.localPath+_this.indexUrl.day3,{coinType:123},'post','json',function(e){
			if(e.data && e.data.length){
				_this.getBiInfoData = e.data;
				_this.initCanvas(_this.getBiInfoData);
			}else{
				console.log(e.msg);
			}
		});
	}
	bjsIndex.prototype.initFontSize = function(){
		var windowWidth = $('body').width();
		this.fontSize = windowWidth > 1500 ? 10 : (windowWidth < 1200 ? 12000/1500 : windowWidth*10/1500);
		$('html').css({
			'fontSize': this.fontSize*10 + 'px'
		});
	}
	bjsIndex.prototype.initSearch = function(){
		var $input = $('.bjs-search-box input');
		if(!$input[0]){
			return false;
		}
		var txt = $input.val().trim();
		var _this = this;
		$input.on('keyup keydown change input',function(){
			var _txt = $(this).val().trim();
			if(txt !== _txt){
				txt = _txt;
				_this.toSearch(txt);
			}
		});
	}
	bjsIndex.prototype.toSearch = function(txt){
		var _this = this;
		var $list = $('.bjs-tab-tbody .bjs-tab-tr');
		if($list[0]){
			$list.each(function(){
				var _txt = $(this).find('.bjs-bi-icon').text();
				txt = txt.replace(/\s/g,'');
				_txt = _txt.replace(/\s/g,'');
				var patt1 = new RegExp(txt,'i');
				if(patt1.test(_txt)){
					$(this).removeClass('hide');
				}else{
					$(this).addClass('hide');
				}
				_this.refreshIScroll('page-left-iscroll-wrap');
			});
		}
	}
	bjsIndex.prototype.initBanner = function(){
		if(!$('.slider6')[0]){
			return false;
		}
        $('.slider6').bxSlider({
			mode: 'fade',
            slideMargin: 0,
	        captions: true,//自动控制
	        auto: true,
	        controls: false//隐藏左右按钮
        });

	}
	bjsIndex.prototype.initCanvas = function(data){
		var _this = this;		
		$('.bjs-tab-tbody canvas').each(function(index,obj){
			var id = $(obj).closest('.bjs-tab-tr').data('id');
			for(var i = 0; i < data.length; i++){
				if(data[i].coin === id){
					_this.drawCnvas(obj,data[i].marketData);
				}
			}			
		});
	}
	bjsIndex.prototype.drawCnvas = function(obj,strData){
		var _this = this;
		var skin = $.cookies.get('skin') || 'black';
		//strData = "[1,2,3]";//字符串转化为数组
		var data = strData.replace(/[\[\]\s]/g,'');
		var arr = data.split(',');
		var status = arr[0] - arr[arr.length - 1];
		var canvas2 = Charts({
		    el: obj,
		    data:arr,
		    styleSet:{
		        lineColor: status < 0 ? _this.styleSet[skin].lineColor[0] : _this.styleSet[skin].lineColor[1],//蓝 : 红
                topColor: status < 0 ? _this.styleSet[skin].topColor[0] : _this.styleSet[skin].topColor[1],
                bottomColor: status < 0 ? _this.styleSet[skin].bottomColor[0] : _this.styleSet[skin].bottomColor[1],
		    }
		});
	}
	bjsIndex.prototype.getUserInfo = function(){
		var info = {
			userId: ''
		};
		$.ajax({
			url: "/login/getuid/t/"+Math.ceil(Math.random()*1000000000000),
			data: {},
			dataType: 'json',
			type: 'get',
			async: false,
			success: function(result){
				if(result.userid){
					info.userId = result.userid;
				}
			}
		});
	    console.log(info)
		return info;
	}
	bjsIndex.prototype.initSkin = function(){
		var _this = this;
		var skin = $.cookies.get('skin') || 'black';
		if(skin == 'white'){
			$('.style-box').find('.style-white').addClass('active');
		}else{
			$('.style-box').find('.style-black').addClass('active');
			$('#style-white').remove();
			if(typeof layer != 'undefined'){
				layer.config({
				    extend: 'myskin/style.css', //加载您的扩展样式
				    skin: 'myskin'
				});
			}
		}
		$(document).on('click','.style-white,.style-black',function(){
			if($(this).hasClass('active')){
				$(this).removeClass('change');
				return false;
			}
			$(this).addClass('active').siblings('.active').removeClass('active');
			$(this).addClass('change').siblings('.change').removeClass('change');
			if($(this).hasClass('style-white')){
				$.cookies.set('skin','white',{expires: 30,path: "/"});
				var link = document.createElement('link');
				link.rel = 'stylesheet';
				link.type = 'text/css';
				link.href = _this.resourcesPath + 'css/white.css';
				link.id = 'style-white';
				$('head').append(link);
				layer.config({
				    extend: '', //加载您的扩展样式
				    skin: ''
				});
			}else{
				$.cookies.set('skin','black',{expires: 30,path: "/"});
				$('#style-white').remove();
				layer.config({
				    extend: 'myskin/style.css', //加载您的扩展样式
				    skin: 'myskin'
				});
			}
		});
	}
	bjsIndex.prototype.initLanguage = function(){
		$(document).on('click','.lang-option a',function(){
			var language = $(this).data('language');
			if($.cookies.get('think_language') == language){
				return false;
			}
			$.cookies.set('think_language',language,30);
			window.location.reload();
		});
	}
	bjsIndex.prototype.initProgress = function(callback){
		if(!$('.bjs-progress')[0]){
			return false;
		}
		var state = false, click = false;
		var left = 0, width = 0;
		var $this;
		$('.bjs-progress').on('mousedown','.progress-box',function(event){
			event.preventDefault();
			$this = $(this);
			state = false;
			click = false;
			width = $(this).width();
			left = $(this).offset().left;
			if(event.target.className == 'progress-btn'){
				state = true;
			}else{
				click = true;
			}
		});
		$('.bjs-progress').on('mouseup','.progress-box',function(event){
			event.preventDefault();
			if(click){ //点击选择
				var _left = event.pageX;
				var x = _left - left; // move s
				var _width = x / width * 100;
				_width = _width < 0 ? 0 : (_width > 100 ? 100 : _width>>0);
				$this.find('.progress-left').css('width',_width+'%');
				$this.find('.progress-btn').css('left',_width+'%');
				$this.siblings('.progress-num').html(_width+'%');
				$this.siblings('.progress-num-input').val(_width);
				callback($this);
			}
		});
		$(document).on('mousemove',function(event){			
			if(state){
				event.preventDefault();
				//触发拖拽
				var _left = event.pageX;
				var x = _left - left;
				var _width = x / width * 100;
				_width = _width < 0 ? 0 : (_width > 100 ? 100 : _width>>0);
				$this.find('.progress-left').css('width',_width+'%');
				$this.find('.progress-btn').css('left',_width+'%');
				$this.siblings('.progress-num').html(_width+'%');
				$this.siblings('.progress-num-input').val(_width);
				callback($this);
			}
		});
		$(document).on('mouseup',function(event){
			state = false;
			click = false;
		});
	}
	bjsIndex.prototype.fetch = function(url,data,type,dataType,success,error){
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