BX.OrderConfigClass = (function ()
{
	var OrderConfigClass = function (parameters)
	{
		this.randomString = parameters.randomString;
		this.tabs = parameters.tabs;
	};

	OrderConfigClass.prototype.selectTab = function(tabId)
	{
		var div = BX('tab_content_'+tabId);
		if(!div) return;
		if(div.className == 'view-report-wrapper-inner active')
			return;

		for (var i = 0, cnt = this.tabs.length; i < cnt; i++)
		{
			var content = BX('tab_content_'+this.tabs[i]);
			if(content && content.className == 'view-report-wrapper-inner active')
			{
				this.showTab(this.tabs[i], false);
				content.className = 'view-report-wrapper-inner';
				break;
			}
		}

		this.showTab(tabId, true);
		div.className = 'view-report-wrapper-inner active';
	};

	OrderConfigClass.prototype.showTab = function(tabId, on)
	{
		var sel = (on? 'sidebar-tab-active':'');
		BX('tab_'+tabId).className = 'sidebar-tab '+sel;
	};

	return OrderConfigClass;
})();