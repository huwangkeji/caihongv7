<!-- 顶部数据统计卡片区域 (修复自适应，强制占满宽度均分) -->
<div class="w-full grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-4 gap-4 sm:gap-6 mb-6">
    <!-- 订单总数 -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-100 flex items-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer group" onclick="window.location.href='./list.php'">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg sm:text-xl mr-3 sm:mr-4 shrink-0 group-hover:scale-110 transition-transform duration-300">
            <i class="fa fa-list-ol"></i>
        </div>
        <div>
            <h3 class="text-base sm:text-2xl font-bold text-slate-800 m-0 leading-tight tracking-tight" id="count1"></h3>
            <p class="text-xs sm:text-sm text-slate-500 m-0 mt-0.5 font-medium">订单总数</p>
        </div>
    </div>
    
    <!-- 待处理订单 -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-100 flex items-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer group" onclick="window.location.href='./list.php?type=0'">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-lg sm:text-xl mr-3 sm:mr-4 shrink-0 group-hover:scale-110 transition-transform duration-300">
            <i class="fa fa-hourglass-half"></i>
        </div>
        <div>
            <h3 class="text-base sm:text-2xl font-bold text-slate-800 m-0 leading-tight tracking-tight" id="count3"></h3>
            <p class="text-xs sm:text-sm text-slate-500 m-0 mt-0.5 font-medium">待处理订单</p>
        </div>
    </div>
    
    <!-- 今日订单数 -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-100 flex items-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer group" onclick="window.location.href='./list.php'">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-lg sm:text-xl mr-3 sm:mr-4 shrink-0 group-hover:scale-110 transition-transform duration-300">
            <i class="fa fa-calendar-check-o"></i>
        </div>
        <div>
            <h3 class="text-base sm:text-2xl font-bold text-emerald-600 m-0 leading-tight tracking-tight">+ <span id="count4"></span></h3>
            <p class="text-xs sm:text-sm text-slate-500 m-0 mt-0.5 font-medium">今日订单数</p>
        </div>
    </div>
    
    <!-- 今日交易额 -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-100 flex items-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer group" onclick="window.location.href='./list.php'">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-sky-50 text-sky-500 flex items-center justify-center text-lg sm:text-xl mr-3 sm:mr-4 shrink-0 group-hover:scale-110 transition-transform duration-300">
            <i class="fa fa-line-chart"></i>
        </div>
        <div>
            <h3 class="text-base sm:text-2xl font-bold text-sky-600 m-0 leading-tight tracking-tight">￥<span id="count5"></span></h3>
            <p class="text-xs sm:text-sm text-slate-500 m-0 mt-0.5 font-medium">今日交易额</p>
        </div>
    </div>
    
    <!-- 今日收益 -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-100 flex items-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer group" onclick="window.location.href='./record.php'">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-orange-50 text-orange-500 flex items-center justify-center text-lg sm:text-xl mr-3 sm:mr-4 shrink-0 group-hover:scale-110 transition-transform duration-300">
            <i class="fa fa-money"></i>
        </div>
        <div>
            <h3 class="text-base sm:text-2xl font-bold text-orange-500 m-0 leading-tight tracking-tight">￥<span id="count15"></span></h3>
            <p class="text-xs sm:text-sm text-slate-500 m-0 mt-0.5 font-medium">今日收益</p>
        </div>
    </div>
    
    <!-- 昨日收益 -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-100 flex items-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 cursor-pointer group" onclick="window.location.href='./record.php'">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-lg sm:text-xl mr-3 sm:mr-4 shrink-0 group-hover:scale-110 transition-transform duration-300">
            <i class="fa fa-history"></i>
        </div>
        <div>
            <h3 class="text-base sm:text-2xl font-bold text-indigo-600 m-0 leading-tight tracking-tight">￥<span id="count16"></span></h3>
            <p class="text-xs sm:text-sm text-slate-500 m-0 mt-0.5 font-medium">昨日收益</p>
        </div>
    </div>

    <!-- 今日访问量 -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-100 flex items-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-teal-50 text-teal-500 flex items-center justify-center text-lg sm:text-xl mr-3 sm:mr-4 shrink-0 group-hover:scale-110 transition-transform duration-300">
            <i class="fa fa-eye"></i>
        </div>
        <div>
            <h3 class="text-base sm:text-2xl font-bold text-slate-800 m-0 leading-tight tracking-tight" id="visit_today">0</h3>
            <p class="text-xs sm:text-sm text-slate-500 m-0 mt-0.5 font-medium">今日访问量</p>
        </div>
    </div>
    
    <!-- 今日独立IP -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 shadow-sm border border-slate-100 flex items-center hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-lg sm:text-xl mr-3 sm:mr-4 shrink-0 group-hover:scale-110 transition-transform duration-300">
            <i class="fa fa-users"></i>
        </div>
        <div>
            <h3 class="text-base sm:text-2xl font-bold text-slate-800 m-0 leading-tight tracking-tight" id="ip_today">0</h3>
            <p class="text-xs sm:text-sm text-slate-500 m-0 mt-0.5 font-medium">今日独立IP</p>
        </div>
    </div>
</div>

<!-- 全网在线大盘数据横幅 -->



<!-- 分栏总容器 (修复自适应，强制占满宽度) -->
<div class="w-full grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
    
    <!-- 左侧主要内容区域 (占 8 份) -->
    <div class="lg:col-span-8 flex flex-col gap-4 sm:gap-6 min-w-0">

        
        <!-- 一周交易与订单统计图表 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-50 flex items-center">
                <i class="fa fa-bar-chart text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-base font-bold text-slate-800 m-0">一周交易数据分析</h3>
            </div>
            <div class="p-6">
                <!-- 走势图容器 -->
                <div id="chart-classic-dash" class="w-full h-[300px]"></div>
                
                <!-- 底部统计数据 -->
                <div class="grid grid-cols-3 gap-4 border-t border-slate-100 pt-6 mt-6">
                    <div class="text-center border-r border-slate-100 px-2">
                        <p class="text-sm text-slate-500 mb-2 flex items-center justify-center font-medium"><i class="fa fa-qq text-blue-500 mr-1.5"></i> QQ钱包</p>
                        <p class="text-lg lg:text-xl font-bold text-slate-800">￥<span id="count12"></span></p>
                    </div>
                    <div class="text-center border-r border-slate-100 px-2">
                        <p class="text-sm text-slate-500 mb-2 flex items-center justify-center font-medium"><i class="fa fa-weixin text-emerald-500 mr-1.5"></i> 微信</p>
                        <p class="text-lg lg:text-xl font-bold text-slate-800">￥<span id="count13"></span></p>
                    </div>
                    <div class="text-center px-2">
                        <p class="text-sm text-slate-500 mb-2 flex items-center justify-center font-medium"><i class="fa fa-credit-card text-sky-500 mr-1.5"></i> 支付宝</p>
                        <p class="text-lg lg:text-xl font-bold text-slate-800">￥<span id="count14"></span></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- 右侧信息区域 (占 4 份) -->
    <div class="lg:col-span-4 flex flex-col gap-5 sm:gap-6 min-w-0">
        
        <!-- 待处理工单 -->
        <div class="bg-gradient-to-br from-brand-500 to-indigo-600 rounded-2xl shadow-md border border-indigo-500/50 overflow-hidden text-white relative">
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 rounded-full bg-white opacity-10"></div>
            <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 rounded-full bg-white opacity-10"></div>
            
            <div class="px-6 py-5 flex items-center justify-between relative z-10">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-xl mr-4 backdrop-blur-sm">
                        <i class="fa fa-comments"></i>
                    </div>
                    <div>
                        <p class="text-indigo-100 text-sm font-medium mb-1">待处理工单数量</p>
                        <h3 class="text-2xl font-bold m-0"><a id="count17" href="workorder.php" class="text-white hover:text-indigo-50 transition-colors">0</a> <span class="text-base font-normal text-indigo-200">个</span></h3>
                    </div>
                </div>
                <i class="fa fa-bell-o text-4xl opacity-20 transform -rotate-12"></i>
            </div>
        </div>

        <!-- 分站统计 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-50 flex items-center">
                <i class="fa fa-sitemap text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-base font-bold text-slate-800 m-0">分站统计</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-slate-50/70 rounded-xl p-4 text-center border border-slate-100/50 hover:bg-slate-50 transition-colors">
                        <i class="fa fa-users text-brand-500 text-2xl mb-2"></i>
                        <h4 class="text-lg font-bold text-slate-800 m-0 mb-1" id="count6"></h4>
                        <p class="text-xs text-slate-500 m-0">分站/用户总数</p>
                    </div>
                    <div class="bg-slate-50/70 rounded-xl p-4 text-center border border-slate-100/50 hover:bg-slate-50 transition-colors">
                        <i class="fa fa-plus-circle text-emerald-500 text-2xl mb-2"></i>
                        <h4 class="text-lg font-bold text-slate-800 m-0 mb-1" id="count7"></h4>
                        <p class="text-xs text-slate-500 m-0">今日新开分站</p>
                    </div>
                    <div class="bg-slate-50/70 rounded-xl p-4 text-center border border-slate-100/50 hover:bg-slate-50 transition-colors">
                        <i class="fa fa-percent text-amber-500 text-2xl mb-2"></i>
                        <h4 class="text-lg font-bold text-slate-800 m-0 mb-1">￥<span id="count8"></span></h4>
                        <p class="text-xs text-slate-500 m-0">今日分站提成</p>
                    </div>
                    <div class="bg-slate-50/70 rounded-xl p-4 text-center border border-slate-100/50 hover:bg-slate-50 transition-colors">
                        <i class="fa fa-exchange text-rose-500 text-2xl mb-2"></i>
                        <h4 class="text-lg font-bold text-rose-600 m-0 mb-1"><a id="count11" href="tixian.php">￥<span id="count11_val"></span></a></h4>
                        <p class="text-xs text-slate-500 m-0">待处理提现</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- 安全中心 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-50 flex items-center">
                <i class="fa fa-shield text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-base font-bold text-slate-800 m-0">安全中心</h3>
            </div>
            <div class="p-6">
                <ul class="space-y-3">
                    <li class="flex items-center text-sm text-slate-600" id="updatemsg">
                        <i class="fa fa-spinner fa-spin text-brand-500 mr-2 shrink-0"></i> 正在获取中...
                    </li>
                    <li class="flex items-center text-sm text-slate-600"><span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded text-xs font-semibold mr-2">正常</span> <span class="text-emerald-600">暂未发现网站安全问题</span></li>                </ul>
            </div>
        </div>

        <!-- 系统信息 -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-50 flex items-center">
                <i class="fa fa-server text-brand-500 mr-2 text-lg"></i>
                <h3 class="text-base font-bold text-slate-800 m-0">系统信息</h3>
            </div>
            <div class="p-0">
                <ul class="divide-y divide-slate-50 text-sm">
                    <li class="px-6 py-3.5 flex items-center text-slate-600">
                        <i class="fa fa-code text-indigo-400 w-5 text-center mr-2"></i>
                        <span class="font-medium text-slate-700 mr-2">PHP 版本：</span> <span class="text-slate-500">8.5.8</span>
                    </li>
                    <li class="px-6 py-3.5 flex items-center text-slate-600">
                        <i class="fa fa-database text-emerald-400 w-5 text-center mr-2"></i>
                        <span class="font-medium text-slate-700 mr-2">MySQL 版本：</span> <span class="text-slate-500"></span>
                    </li>
                    <li class="px-6 py-3.5 flex items-center text-slate-600">
                        <i class="fa fa-globe text-sky-400 w-5 text-center mr-2"></i>
                        <span class="font-medium text-slate-700 mr-2">服务器软件：</span> <span class="text-slate-500 truncate" title=""></span>
                    </li>
                    <li class="px-6 py-3.5 flex items-center text-slate-600 bg-slate-50/50">
                        <i class="fa fa-clock-o text-brand-400 w-5 text-center mr-2"></i>
                        <span class="font-medium text-slate-700 mr-2">服务器时间：</span> <span class="text-slate-500">2026-10-15 01:30:52</span>
                    </li>
                </ul>
            </div>
        </div>

    </div>
</div>

<script src="/assets/vendor/cdn.staticfile.net/flot/0.8.3/jquery.flot.min.js"></script>
<script src="/assets/vendor/cdn.staticfile.net/flot/0.8.3/jquery.flot.resize.min.js"></script>

<script>
    // 提供给访问模态框原生的关闭函数
    function closeVisitModal() {
        const modal = document.getElementById('visitDetailModal');
        if(modal) {
            modal.classList.add('opacity-0', 'pointer-events-none');
            setTimeout(() => modal.classList.add('hidden'), 300);
            if (typeof currentPage !== 'undefined') currentPage = 1;
        }
    }

	$(document).ready(function() {
		
		$('#title').html('正在加载数据中...');
		
		$.ajax({
			type: "POST",
			url: "index.php",
			data:{'SF_Action':'check'},
			dataType: 'json',
			success: function(data) {
			    if(data.code == 0){
                    if(data.data.code == 1){
                        $("#updatemsg").html('<i class="fa fa-arrow-circle-up text-brand-500 mt-1 mr-2 shrink-0"></i> <span><b class="text-rose-500">通知:发现新版本 V'+data.data.data.edition+'</b> <a href="update.php" class="text-brand-600 hover:underline ml-1">点击更新</a></span>');
                    }else{
                        $("#updatemsg").html('<i class="fa fa-check-circle text-emerald-500 mt-1 mr-2 shrink-0"></i> <span class="text-slate-500">您当前已是最新版本</span>');
                    }
                }else{
                    $("#updatemsg").html('<i class="fa fa-exclamation-circle text-rose-500 mt-1 mr-2 shrink-0"></i> <span>'+data.msg+'</span>');
                }
			},
		    error :function(data){
			    $("#updatemsg").html('<i class="fa fa-check-circle text-emerald-500 mt-1 mr-2 shrink-0"></i> <span class="text-slate-500">链接服务器成功！</span>');
			}
		});
		$.ajax({
			type: "GET",
			url: "ajax.php?act=getcount",
			dataType: 'json',
			async: true,
			success: function(data) {
				$('#title').html('后台管理首页');
				$('#yxts').html(data.yxts);
				$('#count1').html(data.count1);
				$('#count2').html(data.count2);
				$('#count3').html(data.count3);
				$('#count4').html(data.count4);
				$('#count5').html(data.count5);
				$('#count6').html(data.count6);
				$('#count7').html(data.count7);
				$('#count8').html(data.count8);
				$('#count9').html(data.count9);
				$('#count10').html(data.count10);
				$('#count11').attr('href', 'tixian.php');
				$('#count11_val').html(data.count11);
				$('#count12').html(data.count12);
				$('#count13').html(data.count13);
				$('#count14').html(data.count14);
				$('#count15').html(data.count15);
				$('#count16').html(data.count16);
				$('#count17').html(data.count17);
				
				if(data.visit_today !== undefined) $('#visit_today').text(data.visit_today);
				if(data.ip_today !== undefined) $('#ip_today').text(data.ip_today);

				// 绘制图表 (恢复并美化原生 Flot 图表库)
				var t = $("#chart-classic-dash");
				var chartOptions = {
					colors: ['#3b82f6', '#10b981'],
					shadowSize: 0,
					legend: { show: !0, position: "nw", backgroundOpacity: 0, labelBoxBorderColor: "transparent" },
					grid: { borderWidth: 0, hoverable: !0, clickable: !0, color: "#94a3b8" },
					yaxis: { show: !1, ticks: 3 },
					xaxis: {
						ticks: data.chart.date,
						tickFormatter: function(val, axis) { return data.chart.date[val]; }
					}
				};
				
				var chartData = [{
					label: "订单量", data: data.chart.orders,
					lines: { show: !0, fill: !0, fillColor: { colors: [{ opacity: .2 }, { opacity: .05 }] } },
					points: { show: !0, radius: 4 }
				}, {
					label: "交易量", data: data.chart.money,
					lines: { show: !0, fill: !0, fillColor: { colors: [{ opacity: .2 }, { opacity: .05 }] } },
					points: { show: !0, radius: 4 }
				}];
				
				var plot = $.plot(t, chartData, chartOptions);
				$(window).resize(function() { t.empty(); plot = $.plot(t, chartData, chartOptions); });
		
                // 绘制访问统计图表
                if(data.visit_chart) {
                    var visitChartHtml = `
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden mt-6">
                        <div class="px-6 py-4 border-b border-slate-50 flex items-center justify-between">
                            <h3 class="text-base font-bold text-slate-800 m-0 flex items-center"><i class="fa fa-area-chart text-brand-500 mr-2 text-lg"></i> 一周访问统计</h3>
                            <button type="button" class="px-3 py-1.5 bg-brand-50 text-brand-600 hover:bg-brand-100 rounded-lg text-sm font-medium transition-colors border border-brand-100 shadow-sm" id="viewVisitDetails"><i class="fa fa-eye mr-1"></i> 详细查看</button>
                        </div>
                        <div class="p-6">
                            <div id="visit-chart" class="w-full h-[300px]"></div>
                        </div>
                    </div>`;
                    
                    var visitModalHtml = `
                    <div id="visitDetailModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden opacity-0 transition-opacity duration-300">
                        <div class="bg-white w-full max-w-5xl rounded-2xl shadow-2xl flex flex-col max-h-[85vh] m-4 relative transform transition-transform duration-300 scale-95 origin-center">
                            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50 rounded-t-2xl">
                                <h4 class="text-lg font-bold text-slate-800 flex items-center m-0"><i class="fa fa-list-alt text-brand-500 mr-2"></i> 访问详情</h4>
                                <button type="button" class="text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-full hover:bg-slate-100 outline-none" onclick="closeVisitModal()">
                                    <i class="fa fa-times text-lg w-6 h-6 flex items-center justify-center"></i>
                                </button>
                            </div>
                            <div class="p-6 overflow-y-auto flex-1">
                                <div class="overflow-x-auto rounded-xl border border-slate-100">
                                    <table class="w-full text-left text-sm text-slate-600 whitespace-nowrap">
                                        <thead class="bg-slate-50 text-slate-700 font-semibold border-b border-slate-100">
                                            <tr>
                                                <th class="px-4 py-3 border-r border-slate-100/50">IP地址</th>
                                                <th class="px-4 py-3 border-r border-slate-100/50">访问URL</th>
                                                <th class="px-4 py-3 border-r border-slate-100/50">地区</th>
                                                <th class="px-4 py-3 border-r border-slate-100/50">访问时间</th>
                                                <th class="px-4 py-3 border-r border-slate-100/50">访问次数</th>
                                                <th class="px-4 py-3">浏览器信息</th>
                                            </tr>
                                        </thead>
                                        <tbody id="visitDetailTable" class="divide-y divide-slate-100"></tbody>
                                    </table>
                                </div>
                                <div id="visitDetailEmpty" class="text-center py-16 hidden">
                                    <i class="fa fa-inbox text-5xl text-slate-200 mb-4 block"></i>
                                    <p class="text-slate-500 text-sm">暂无访问记录</p>
                                </div>
                                <div id="visitDetailLoading" class="text-center py-16">
                                    <i class="fa fa-spinner fa-spin text-3xl text-brand-500 block mb-3"></i>
                                    <p class="text-slate-500 text-sm">正在加载访问记录...</p>
                                </div>
                            </div>
                            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 rounded-b-2xl flex flex-wrap gap-4 justify-between items-center">
                                <ul class="flex space-x-1 m-0 p-0 list-none" id="visitDetailPagination"></ul>
                                <button type="button" class="px-5 py-2 bg-white border border-slate-200 hover:bg-slate-100 text-slate-700 rounded-xl text-sm font-medium transition-colors shadow-sm outline-none" onclick="closeVisitModal()">关闭</button>
                            </div>
                        </div>
                    </div>`;
                    
                    // 将图表和弹窗结构注入页面
                    if ($('#visit-chart').length === 0) {
                        $("#chart-classic-dash").closest(".bg-white").after(visitChartHtml);
                    }
                    if ($('#visitDetailModal').length === 0) {
                        $('body').append(visitModalHtml);
                    }
                    
                    var v = $("#visit-chart");
                    var visitChartOptions = {
                        colors: ['#6366f1', '#0ea5e9'],
                        shadowSize: 0,
                        legend: { show: !0, position: "nw", backgroundOpacity: 0, labelBoxBorderColor: "transparent" },
                        grid: { borderWidth: 0, hoverable: !0, clickable: !0, color: "#94a3b8" },
                        yaxis: { show: !1, ticks: 3 },
                        xaxis: {
                            ticks: data.visit_chart.date,
                            tickFormatter: function(val, axis) { return data.visit_chart.date[val]; }
                        }
                    };
                    
                    var visitChartData = [{
                        label: "访问量", data: data.visit_chart.visits,
                        lines: { show: !0, fill: !0, fillColor: { colors: [{ opacity: .2 }, { opacity: .05 }] } },
                        points: { show: !0, radius: 4 }
                    }, {
                        label: "独立IP", data: data.visit_chart.ips,
                        lines: { show: !0, fill: !0, fillColor: { colors: [{ opacity: .2 }, { opacity: .05 }] } },
                        points: { show: !0, radius: 4 }
                    }];
                    
                    var visitPlot = $.plot(v, visitChartData, visitChartOptions);
                    $(window).resize(function() { v.empty(); visitPlot = $.plot(v, visitChartData, visitChartOptions); });
                    
                    var visitPreviousPoint = null;
                    v.bind("plothover", function(event, pos, item) {
                        if (item) {
                            if (visitPreviousPoint != item.dataIndex) {
                                visitPreviousPoint = item.dataIndex;
                                $("#tooltip").remove();
                                showTooltip(item.pageX, item.pageY, item.series.label + "：" + item.datapoint[1].toFixed(2));
                            }
                        } else {
                            $("#tooltip").remove();
                            visitPreviousPoint = null;
                        }
                    });
                }

                // 主图表悬浮提示框逻辑
				var previousPoint = null;
				t.bind("plothover", function(event, pos, item) {
					if (item) {
						if (previousPoint != item.dataIndex) {
							previousPoint = item.dataIndex;
							$("#tooltip").remove();
							var y = item.datapoint[1].toFixed(2);
							showTooltip(item.pageX, item.pageY,
								item.series.label + "：" + (item.seriesIndex == 1 ? "￥" + y : y) + (item.seriesIndex == 0 ? " 订单" : " 张"));
						}
					} else {
						$("#tooltip").remove();
						previousPoint = null;
					}
				});

				function showTooltip(x, y, contents) {
                    $("<div id='tooltip' class='bg-slate-800 text-white text-[13px] font-medium px-3 py-2 rounded-lg shadow-lg z-[9999] pointer-events-none tracking-wide'>" + contents + "</div>").css({
                        position: "absolute", display: "none", top: y - 45, left: x + 15, opacity: 0.95
                    }).appendTo("body").fadeIn(200);
			    }

			window.currentPage = 1;
			const pageSize = 20;
			
			// 详细查看按钮点击事件
			$(document).off('click', '#viewVisitDetails').on('click', '#viewVisitDetails', function(e) {
				e.preventDefault();
				window.currentPage = 1;
				loadVisitDetails(window.currentPage);
				
				const modal = document.getElementById('visitDetailModal');
				if(modal) {
					modal.classList.remove('hidden');
					setTimeout(() => {
						modal.classList.remove('opacity-0');
						modal.querySelector('.transform').classList.remove('scale-95');
					}, 10);
				}
			});
			
			        // 初始化拉取全部数据
    function loadVisitDetails(page) {
				$('#visitDetailLoading').show();
				$('#visitDetailTable').empty();
				$('#visitDetailEmpty').hide();
				$('#visitDetailPagination').empty();
				
				$.ajax({
					type: 'GET',
					url: 'ajax.php?act=get_visit_details',
					data: { page: page, pageSize: pageSize },
					dataType: 'json',
					success: function(data) {
						$('#visitDetailLoading').hide();
						
						if (data.code === 0) {
							if (data.visits && data.visits.length > 0) {
								$.each(data.visits, function(index, visit) {
									const row = `
										<tr class="hover:bg-slate-50 transition-colors">
											<td class="px-4 py-3 font-medium text-slate-700">${visit.ip}</td>
											<td class="px-4 py-3 max-w-[200px] truncate" title="${visit.url}">${visit.url}</td>
											<td class="px-4 py-3"><span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-md text-xs">${visit.region}</span></td>
											<td class="px-4 py-3 text-slate-500">${visit.visit_time}</td>
											<td class="px-4 py-3"><span class="px-2 py-0.5 bg-brand-50 text-brand-600 font-semibold rounded">${visit.visits}</span></td>
											<td class="px-4 py-3 text-xs max-w-[300px] whitespace-normal text-slate-500">${visit.user_agent}</td>
										</tr>
									`;
									$('#visitDetailTable').append(row);
								});
								
								generatePagination(data.total, data.page, data.pageSize);
							} else {
								$('#visitDetailEmpty').show();
							}
						} else if (data.code === 1) {
							$('#visitDetailEmpty').show();
							$('#visitDetailEmpty').html(`
								<i class="fa fa-info-circle text-5xl text-slate-200 mb-4 block"></i>
								<p class="text-slate-500">${data.msg}</p>
							`);
						} else {
							layer.msg('加载失败：' + data.msg, {icon: 5});
						}
					},
					error: function() {
						$('#visitDetailLoading').hide();
						layer.msg('网络错误，无法加载访问记录', {icon: 2});
					}
				});
			}
			
			function generatePagination(total, current, pageSize) {
				const totalPages = Math.ceil(total / pageSize);
				const maxVisible = 5;
				let startPage = Math.max(1, current - Math.floor(maxVisible / 2));
				let endPage = Math.min(totalPages, startPage + maxVisible - 1);
				
				if (endPage - startPage + 1 < maxVisible) {
					startPage = Math.max(1, endPage - maxVisible + 1);
				}
				
				if (startPage > 1) {
					$('#visitDetailPagination').append(`<li><a href="#" data-page="1" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors text-sm shadow-sm inline-block">首页</a></li>`);
					$('#visitDetailPagination').append(`<li><span class="px-2 text-slate-400">...</span></li>`);
				}
				
				for (let i = startPage; i <= endPage; i++) {
					const activeClass = i === current ? 'border-brand-500 bg-brand-50 text-brand-600 font-bold' : 'border-slate-200 bg-white text-slate-600 hover:bg-slate-50';
					$('#visitDetailPagination').append(`<li><a href="#" data-page="${i}" class="px-3 py-1.5 rounded-lg border transition-colors text-sm shadow-sm inline-block ${activeClass}">${i}</a></li>`);
				}
				
				if (endPage < totalPages) {
					$('#visitDetailPagination').append(`<li><span class="px-2 text-slate-400">...</span></li>`);
					$('#visitDetailPagination').append(`<li><a href="#" data-page="${totalPages}" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 transition-colors text-sm shadow-sm inline-block">末页</a></li>`);
				}
				
				$('#visitDetailPagination a[data-page]').click(function(e) {
					e.preventDefault();
					const page = parseInt($(this).data('page'));
					if (page !== window.currentPage) {
						window.currentPage = page;
						loadVisitDetails(window.currentPage);
					}
				});
			}

		}
		});
	})
</script>
