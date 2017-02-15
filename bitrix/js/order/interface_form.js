function BxOrderInterfaceForm(name, aTabs)
{
	var _this = this;
	this.name = name; // is form ID
	this.aTabs = aTabs;
	this.bExpandTabs = false;
	this.vars = {};
	this.oTabsMeta = {};
	this.aTabsEdit = [];
	this.oFields = {};
	this.menu = new PopupMenu('bxFormMenu_'+this.name, 1010);
	this.settingsMenu = [];
	this.tabSettingsWnd = null;
	this.fieldSettingsWnd = null;
	this.activeTabClass = 'bx-order-view-tab-active';
	this._form = null;
	this._isSubmitted = false;
	this._enableSigleSubmit = true;

	this.isVisibleInViewMode = true;
	var container = BX("container_" + this.name.toLowerCase());
	if(container)
	{
		this.isVisibleInViewMode = container.style.display !== "none";
	}

	this.Initialize = function()
	{
		this._form = BX("form_" + this.name);
		if(this._enableSigleSubmit)
		{
			this._submitHandler = BX.delegate(this._OnSubmit, this);
			BX.bind(this._form, 'submit', this._submitHandler);
		}

		BX.onCustomEvent(window, 'OrderInterfaceFormCreated', [ this ]);
	};

	this.EnableSigleSubmit = function(enable)
	{
		enable = !!enable;
		if(this._enableSigleSubmit === enable)
		{
			return;
		}

		if(this._enableSigleSubmit && this._submitHandler)
		{
			BX.unbind(this._form, 'submit', this._submitHandler);
			this._submitHandler = null;
		}

		this._enableSigleSubmit = enable;

		if(this._enableSigleSubmit)
		{
			this._submitHandler = BX.delegate(this._OnSubmit, this);
			BX.bind(this._form, 'submit', this._submitHandler);
		}
	};

	this.GetForm = function()
	{
		return this._form;
	};

	this._OnSubmit = function(e)
	{
		if(!this._enableSigleSubmit)
		{
			return true;
		}

		if(this._isSubmitted)
		{
			return BX.PreventDefault(e);
		}

		this._isSubmitted = true;
		window.setTimeout(BX.delegate(this._LockSubmits, this), 10);
		return true;
	};

	this._LockSubmits = function()
	{
		var saveAndViewBtn = BX(this.name + "_saveAndView");
		if(saveAndViewBtn)
		{
			saveAndViewBtn.disabled = "disabled";
		}

		var saveAndAddBtn = BX(this.name + "_saveAndAdd");
		if(saveAndAddBtn)
		{
			saveAndAddBtn.disabled = "disabled";
		}

		var applyBtn = BX(this.name + "_apply");
		if(applyBtn)
		{
			applyBtn.disabled = "disabled";
		}
	};

	this.GetTabs = function()
	{
		var tabs = BX.findChildren(
			BX(this.name + '_tab_block'),
			{ "tagName": "a", "className": "bx-order-view-tab" },
			false
		);
		return tabs ? tabs : [];
	};

	this.GetActiveTabId = function()
	{
		var tabs = this.GetTabs();
		for(var i = 0; i < tabs.length; i++)
		{
			var tab = tabs[i];
			if(BX.hasClass(tab, this.activeTabClass))
			{
				return tab.id.substring((this.name + '_tab_').length);
			}
		}

		return '';
	};

	this.ShowOnDemand = function(caller)
	{
		var sectionContainer = BX.findParent(caller, { 'tagName':'DIV', 'className':'bx-order-view-fieldset' });
		var rows = BX.findChildren(sectionContainer, { 'tagName':'tr', 'className':'bx-order-view-on-demand' }, true);

		if(!BX.type.isArray(rows))
		{
			return;
		}

		for(var i = 0; i < rows.length; i++)
		{
			rows[i].style.display = '';
		}

		if(caller)
		{
			BX.findParent(caller, { 'tagName':'tr', 'className':'bx-order-view-show-more' }).style.display='none';
		}
	};

	this.SelectTab = function(tab_id)
	{
		var div = BX('inner_tab_' + tab_id);

		if(!div || div.style.display != 'none')
			return;

		for (var i = 0, cnt = this.aTabs.length; i < cnt; i++)
		{
			var tab = BX('inner_tab_'+this.aTabs[i]);
			if(!tab)
				continue;

			if(tab.style.display != 'none')
			{
				this.ShowTab(this.aTabs[i], false);
				tab.style.display = 'none';
				break;
			}
		}

		this.ShowTab(tab_id, true);
		div.style.display = 'block';

		var hidden = BX(this.name+'_active_tab');
		if(hidden)
			hidden.value = tab_id;

		BX.onCustomEvent(
			window,
			'BX_ORDER_INTERFACE_FORM_TAB_SELECTED',
			[this, this.name, tab_id, div]
		);
	};

	this.ShowTab = function(tab_id, on)
	{
		var id = this.name + '_tab_' + tab_id;
		var tabs = this.GetTabs();
		for(var i = 0; i < tabs.length; i++)
		{
			var tab = tabs[i];
			if(id !== tab.id)
			{
				continue;
			}

			if(on)
			{
				BX.addClass(tab, 'bx-order-view-tab-active');
				BX.onCustomEvent(this, 'OnTabShow', [ tab_id ]);
			}
			else
			{
				BX.removeClass(tab, 'bx-order-view-tab-active');
				BX.onCustomEvent(this, 'OnTabHide', [ tab_id ]);
			}

			break;
		}
	};

	this.HoverTab = function(tab_id, on)
	{
		var tab = document.getElementById('tab_'+tab_id);
		if(tab.className == 'bx-tab-selected')
			return;

		document.getElementById('tab_left_'+tab_id).className = (on? 'bx-tab-left-hover':'bx-tab-left');
		tab.className = (on? 'bx-tab-hover':'bx-tab');
		var tab_right = document.getElementById('tab_right_'+tab_id);
		tab_right.className = (on? 'bx-tab-right-hover':'bx-tab-right');
	};

	this.ShowDisabledTab = function(tab_id, disabled)
	{
		var tab = document.getElementById('tab_cont_'+tab_id);
		if(disabled)
		{
			tab.className = 'bx-tab-container-disabled';
			tab.onclick = null;
			tab.onmouseover = null;
			tab.onmouseout = null;
		}
		else
		{
			tab.className = 'bx-tab-container';
			tab.onclick = function(){_this.SelectTab(tab_id);};
			tab.onmouseover = function(){_this.HoverTab(tab_id, true);};
			tab.onmouseout = function(){_this.HoverTab(tab_id, false);};
		}
	};

	this.ToggleTabs = function(bSkipSave)
	{
		this.bExpandTabs = !this.bExpandTabs;

		var a = document.getElementById('bxForm_'+this.name+'_expand_link');
		a.title = (this.bExpandTabs? this.vars.mess.collapseTabs : this.vars.mess.expandTabs);
		a.className = (this.bExpandTabs? a.className.replace(/\s*bx-down/ig, ' bx-up') : a.className.replace(/\s*bx-up/ig, ' bx-down'));

		var div;
		for(var i in this.aTabs)
		{
			var tab_id = this.aTabs[i];
			this.ShowTab(tab_id, false);
			this.ShowDisabledTab(tab_id, this.bExpandTabs);
			div = document.getElementById('inner_tab_'+tab_id);
			div.style.display = (this.bExpandTabs? 'block':'none');
		}
		if(!this.bExpandTabs)
		{
			this.ShowTab(this.aTabs[0], true);
			div = document.getElementById('inner_tab_'+this.aTabs[0]);
			div.style.display = 'block';
		}
		if(bSkipSave !== true)
			BX.ajax.get('/bitrix/components'+this.vars.component_path+'/settings.php?FORM_ID='+this.name+'&action=expand&expand='+(this.bExpandTabs? 'Y':'N')+'&sessid='+this.vars.sessid);
	};

	this.SetTheme = function(menuItem, theme)
	{
		BX.loadCSS(this.vars.template_path+'/themes/'+theme+'/style.css');

		var themeMenu = this.menu.GetMenuByItemId(menuItem.id);
		themeMenu.SetAllItemsIcon('');
		themeMenu.SetItemIcon(menuItem, 'checked');

		BX.ajax.get('/bitrix/components'+_this.vars.component_path+'/settings.php?FORM_ID='+this.name+'&GRID_ID='+this.vars.GRID_ID+'&action=settheme&theme='+theme+'&sessid='+this.vars.sessid);
	};

	this.ShowSettings = function()
	{
		var bCreated = false;
		if(!window['formSettingsDialog'+this.name])
		{
			window['formSettingsDialog'+this.name] = new BX.CDialog({
				'content':'<form name="form_settings_'+this.name+'"></form>',
				'title': this.vars.mess.settingsTitle,
				'width': this.vars.settingWndSize.width,
				'height': this.vars.settingWndSize.height,
				'resize_id': 'InterfaceFormSettingWnd'
			});
			bCreated = true;
		}

		window['formSettingsDialog'+this.name].ClearButtons();
		window['formSettingsDialog'+this.name].SetButtons([
			{
				'title': this.vars.mess.settingsSave,
				'action': function()
				{
					_this.SaveSettings();
					this.parentWindow.Close();
				}
			},
			BX.CDialog.prototype.btnCancel
		]);

		window['formSettingsDialog'+this.name].Show();

		var form = document['form_settings_'+this.name];

		if(bCreated)
			form.appendChild(BX('form_settings_'+this.name));

		//editable data
		var i;
		this.aTabsEdit = [];
		for(i in this.oTabsMeta)
		{
			var fields = [];
			for(var j in this.oTabsMeta[i].fields)
				fields[fields.length] = BX.clone(this.oTabsMeta[i].fields[j]);
			this.aTabsEdit[this.aTabsEdit.length] = BX.clone(this.oTabsMeta[i]);
			this.aTabsEdit[this.aTabsEdit.length-1].fields = fields;
		}

		//tabs
		jsSelectUtils.deleteAllOptions(form.tabs);
		for(i in this.aTabsEdit)
			form.tabs.options[form.tabs.length] = new Option(this.aTabsEdit[i].name, this.aTabsEdit[i].id, false, false);

		//fields
		form.tabs.selectedIndex = 0;
		this.OnSettingsChangeTab();

		//available fields
		this.aAvailableFields = BX.clone(this.oFields);
		jsSelectUtils.deleteAllOptions(form.all_fields);
		for(i in this.aAvailableFields)
			form.all_fields.options[form.all_fields.length] = new Option(this.aAvailableFields[i].name, this.aAvailableFields[i].id, false, false);

		jsSelectUtils.sortSelect(form.all_fields);

		this.HighlightSections(form.all_fields);

		this.ProcessButtons();

		form.tabs.focus();
	};

	this.OnSettingsChangeTab = function()
	{
		var form = document['form_settings_'+this.name];
		var index = form.tabs.selectedIndex;

		jsSelectUtils.deleteAllOptions(form.fields);
		for(var i in this.aTabsEdit[index].fields)
		{
			var opt = new Option(this.aTabsEdit[index].fields[i].name, this.aTabsEdit[index].fields[i].id, false, false);
			if(this.aTabsEdit[index].fields[i].type == 'section')
				opt.className = 'bx-section';
			form.fields.options[form.fields.length] = opt;
		}

		this.ProcessButtons();
	};

	this.TabMoveUp = function()
	{
		var form = document['form_settings_'+this.name];
		var index = form.tabs.selectedIndex;

		if(index > 0)
		{
			var tab1 = BX.clone(this.aTabsEdit[index]);
			var tab2 = BX.clone(this.aTabsEdit[index-1]);
			this.aTabsEdit[index] = tab2;
			this.aTabsEdit[index-1] = tab1;
		}
		jsSelectUtils.moveOptionsUp(form.tabs);
	};

	this.TabMoveDown = function()
	{
		var form = document['form_settings_'+this.name];
		var index = form.tabs.selectedIndex;

		if(index < form.tabs.length-1)
		{
			var tab1 = BX.clone(this.aTabsEdit[index]);
			this.aTabsEdit[index] = BX.clone(this.aTabsEdit[index+1]);
			this.aTabsEdit[index+1] = tab1;
		}
		jsSelectUtils.moveOptionsDown(form.tabs);
	};

	this.TabEdit = function()
	{
		var form = document['form_settings_'+this.name];
		var tabIndex = form.tabs.selectedIndex;

		if(tabIndex < 0)
			return;

		this.ShowTabSettings(this.aTabsEdit[tabIndex],
			function()
			{
				var frm = document['tab_settings_'+_this.name];
				_this.aTabsEdit[tabIndex].name = frm.tab_name.value;
				_this.aTabsEdit[tabIndex].title = frm.tab_title.value;

				form.tabs[tabIndex].text = frm.tab_name.value;
			}
		);
	};

	this.TabAdd = function()
	{
		this.ShowTabSettings({'name':'', 'title':''},
			function()
			{
				var tab_id = 'tab_'+Math.round(Math.random()*1000000);

				var frm = document['tab_settings_'+_this.name];
				_this.aTabsEdit[_this.aTabsEdit.length] = {
					'id': tab_id,
					'name': frm.tab_name.value,
					'title': frm.tab_title.value,
					'fields': []
				};

				var form = document['form_settings_'+_this.name];
				form.tabs[form.tabs.length] = new Option(frm.tab_name.value, tab_id, true, true);
				_this.OnSettingsChangeTab();
			}
		);
	};

	this.TabDelete = function()
	{
		var form = document['form_settings_'+this.name];
		var tabIndex = form.tabs.selectedIndex;

		if(tabIndex < 0)
			return;

		//place to available fields before delete
		var i;
		for(i in this.aTabsEdit[tabIndex].fields)
		{
			this.aAvailableFields[this.aTabsEdit[tabIndex].fields[i].id] = this.aTabsEdit[tabIndex].fields[i];
			jsSelectUtils.addNewOption(form.all_fields, this.aTabsEdit[tabIndex].fields[i].id, this.aTabsEdit[tabIndex].fields[i].name, true, false);
		}

		this.HighlightSections(form.all_fields);

		this.aTabsEdit = BX.util.deleteFromArray(this.aTabsEdit, tabIndex);
		form.tabs.remove(tabIndex);

		if(form.tabs.length > 0)
		{
			i = (tabIndex < form.tabs.length? tabIndex : form.tabs.length-1);
			form.tabs[i].selected = true;
			this.OnSettingsChangeTab();
		}
		else
		{
			jsSelectUtils.deleteAllOptions(form.fields);
			this.ProcessButtons();
		}
	};

	this.ShowTabSettings = function(data, action)
	{
		var wnd = this.tabSettingsWnd;
		if(!wnd)
		{
			this.tabSettingsWnd = wnd = new BX.CDialog({
				'content':'<form name="tab_settings_'+this.name+'">'+
					'<table width="100%">'+
					'<tr>'+
					'<td width="50%" align="right">'+this.vars.mess.tabSettingsName+'</td>'+
					'<td><input type="text" name="tab_name" size="30" value="" style="width:90%"></td>'+
					'</tr>'+
					'<tr>'+
					'<td align="right">'+this.vars.mess.tabSettingsCaption+'</td>'+
					'<td><input type="text" name="tab_title" size="30" value="" style="width:90%"></td>'+
					'</tr>'+
					'</table>'+
					'</form>',
				'title': this.vars.mess.tabSettingsTitle,
				'width': this.vars.tabSettingWndSize.width,
				'height': this.vars.tabSettingWndSize.height,
				'resize_id': 'InterfaceFormTabSettingWnd'
			});
		}
		wnd.ClearButtons();
		wnd.SetButtons([
			{
				'title': this.vars.mess.tabSettingsSave,
				'action': function(){
					action();
					this.parentWindow.Close();
				}
			},
			BX.CDialog.prototype.btnCancel
		]);
		wnd.Show();

		var form = document['tab_settings_'+this.name];
		form.tab_name.value = data.name;
		form.tab_title.value = data.title;
		form.tab_name.focus();
	};

	this.ShowFieldSettings = function(data, action)
	{
		var wnd = this.fieldSettingsWnd;
		if(!wnd)
		{
			this.fieldSettingsWnd = wnd = new BX.CDialog({
				'content':'<form name="field_settings_'+this.name+'">'+
					'<table width="100%">'+
					'<tr>'+
					'<td width="50%" align="right" id="field_name_'+this.name+'"></td>'+
					'<td><input type="text" name="field_name" size="30" value="" style="width:90%"></td>'+
					'</tr>'+
					'</table>'+
					'</form>',
				'width': this.vars.fieldSettingWndSize.width,
				'height': this.vars.fieldSettingWndSize.height,
				'resize_id': 'InterfaceFormFieldSettingWnd'
			});
		}

		wnd.SetTitle(data.type && data.type == 'section'? this.vars.mess.sectSettingsTitle : this.vars.mess.fieldSettingsTitle);
		BX('field_name_'+this.name).innerHTML = (data.type && data.type == 'section'? this.vars.mess.sectSettingsName : this.vars.mess.fieldSettingsName);

		wnd.ClearButtons();
		wnd.SetButtons([
			{
				'title': this.vars.mess.tabSettingsSave,
				'action': function(){
					action();
					this.parentWindow.Close();
				}
			},
			BX.CDialog.prototype.btnCancel
		]);
		wnd.Show();

		var form = document['field_settings_'+this.name];
		form.field_name.value = data.name;
		form.field_name.focus();
	};

	this.FieldEdit = function()
	{
		var form = document['form_settings_'+this.name];
		var tabIndex = form.tabs.selectedIndex;
		var fieldIndex = form.fields.selectedIndex;

		if(tabIndex < 0 || fieldIndex < 0)
			return;

		this.ShowFieldSettings(this.aTabsEdit[tabIndex].fields[fieldIndex],
			function()
			{
				var frm = document['field_settings_'+_this.name];
				_this.aTabsEdit[tabIndex].fields[fieldIndex].name = frm.field_name.value;

				form.fields[fieldIndex].text = frm.field_name.value;
			}
		);
	};

	this.FieldAdd = function()
	{
		var form = document['form_settings_'+this.name];
		var tabIndex = form.tabs.selectedIndex;

		if(tabIndex < 0)
			return;

		this.ShowFieldSettings({'name':'', 'type':'section'},
			function()
			{
				var field_id = 'field_'+Math.round(Math.random()*1000000);
				var frm = document['field_settings_'+_this.name];
				_this.aTabsEdit[tabIndex].fields[_this.aTabsEdit[tabIndex].fields.length] = {
					'id': field_id,
					'name': frm.field_name.value,
					'type': 'section'
				};
				var opt = new Option(frm.field_name.value, field_id, true, true);
				opt.className = 'bx-section';
				form.fields[form.fields.length] = opt;
				_this.ProcessButtons();
			}
		);
	};

	this.FieldsMoveUp = function()
	{
		var form = document['form_settings_'+this.name];
		var tabIndex = form.tabs.selectedIndex;

		var n = form.fields.length;
		for(var i=0; i<n; i++)
		{
			if(form.fields[i].selected && i>0 && form.fields[i-1].selected == false)
			{
				var field1 = BX.clone(this.aTabsEdit[tabIndex].fields[i]);
				this.aTabsEdit[tabIndex].fields[i] = BX.clone(this.aTabsEdit[tabIndex].fields[i-1]);
				this.aTabsEdit[tabIndex].fields[i-1] = field1;

				var option1 = new Option(form.fields[i].text, form.fields[i].value);
				var option2 = new Option(form.fields[i-1].text, form.fields[i-1].value);
				option1.className = form.fields[i].className;
				option2.className = form.fields[i-1].className;
				form.fields[i] = option2;
				form.fields[i].selected = false;
				form.fields[i-1] = option1;
				form.fields[i-1].selected = true;
			}
		}
	};

	this.FieldsMoveDown = function()
	{
		var form = document['form_settings_'+this.name];
		var tabIndex = form.tabs.selectedIndex;

		var n = form.fields.length;
		for(var i=n-1; i>=0; i--)
		{
			if(form.fields[i].selected && i<n-1 && form.fields[i+1].selected == false)
			{
				var field1 = BX.clone(this.aTabsEdit[tabIndex].fields[i]);
				this.aTabsEdit[tabIndex].fields[i] = BX.clone(this.aTabsEdit[tabIndex].fields[i+1]);
				this.aTabsEdit[tabIndex].fields[i+1] = field1;

				var option1 = new Option(form.fields[i].text, form.fields[i].value);
				var option2 = new Option(form.fields[i+1].text, form.fields[i+1].value);
				option1.className = form.fields[i].className;
				option2.className = form.fields[i+1].className;
				form.fields[i] = option2;
				form.fields[i].selected = false;
				form.fields[i+1] = option1;
				form.fields[i+1].selected = true;
			}
		}
	};

	this.FieldsAdd = function()
	{
		var form = document['form_settings_'+this.name];
		var tabIndex = form.tabs.selectedIndex;

		if(tabIndex == -1)
			return;

		var fields = this.aTabsEdit[tabIndex].fields;

		var n = form.all_fields.length, i;
		for(i=0; i<n; i++)
			if(form.all_fields[i].selected)
				fields[fields.length] = {
					'id': form.all_fields[i].value,
					'name': form.all_fields[i].text,
					'type': this.aAvailableFields[form.all_fields[i].value].type
				};

		jsSelectUtils.addSelectedOptions(form.all_fields, form.fields, false, false);
		jsSelectUtils.deleteSelectedOptions(form.all_fields);

		for(i=0, n=form.fields.length; i<n; i++)
			if(fields[i].type == 'section')
				form.fields[i].className = 'bx-section';

		this.ProcessButtons();
	};

	this.FieldsDelete = function()
	{
		var form = document['form_settings_'+this.name];
		var tabIndex = form.tabs.selectedIndex;

		if(tabIndex == -1)
			return;

		var n = form.fields.length;
		var delta = 0;
		for(var i=0; i<n; i++)
		{
			if(form.fields[i].selected)
			{
				this.aAvailableFields[form.fields[i].value] = this.aTabsEdit[tabIndex].fields[i-delta];
				this.aTabsEdit[tabIndex].fields = BX.util.deleteFromArray(this.aTabsEdit[tabIndex].fields, i-delta);
				delta++;
			}
		}

		jsSelectUtils.addSelectedOptions(form.fields, form.all_fields, false, true);
		jsSelectUtils.deleteSelectedOptions(form.fields);

		this.HighlightSections(form.all_fields);

		this.ProcessButtons();
	};

	this.ProcessButtons = function()
	{
		var form = document['form_settings_'+this.name];

		form.add_btn.disabled = (form.all_fields.selectedIndex == -1 || form.tabs.selectedIndex == -1);
		form.del_btn.disabled = form.up_btn.disabled = form.down_btn.disabled = form.field_edit_btn.disabled = (form.fields.selectedIndex == -1);
		form.tab_up_btn.disabled = form.tab_down_btn.disabled = form.tab_edit_btn.disabled = form.tab_del_btn.disabled = form.field_add_btn.disabled = (form.tabs.selectedIndex == -1);
	};

	this.HighlightSections = function(el)
	{
		for(var i=0, n=el.length; i<n; i++)
			if(this.aAvailableFields[el[i].value].type == 'section')
				el[i].className = 'bx-section';
	};

	this.SaveSettings = function()
	{
		var data = {
			'FORM_ID': this.name,
			'action': 'savesettings',
			'sessid': this.vars.sessid,
			'tabs': this.aTabsEdit
		};
		var form = document['form_settings_'+this.name];
		if(form && form['set_default_settings'])
		{
			data.set_default_settings = (form.set_default_settings.checked? 'Y':'N');
			data.delete_users_settings = (form.delete_users_settings.checked? 'Y':'N');
		}
		BX.ajax.post('/bitrix/components'+_this.vars.component_path+'/settings.php', data, function(){_this.Reload()});
	};

	this.SaveSettings = function(options)
	{
		if(!BX.type.isPlainObject(options))
		{
			options = {};
		}

		var callback = BX.type.isFunction(options['callback']) ? options['callback'] : null;
		var data =
			{
				'FORM_ID': this.name,
				'action': 'savesettings',
				'sessid': this.vars.sessid,
				'tabs': this.aTabsEdit
			};

		var form = document['form_settings_'+this.name];
		if(form && form['set_default_settings'])
		{
			data['set_default_settings'] = (form.set_default_settings.checked? 'Y':'N');
			data['delete_users_settings'] = (form.delete_users_settings.checked? 'Y':'N');
		}
		else
		{
			if(BX.type.isBoolean(options['setDefaultSettings']))
			{
				data['set_default_settings'] = options['setDefaultSettings'] ? 'Y' : 'N';
			}

			if(BX.type.isBoolean(options['deleteUserSettings']))
			{
				data['delete_users_settings'] = options['deleteUserSettings'] ? 'Y' : 'N';
			}
		}

		var url = '/bitrix/components' + _this.vars.component_path + '/settings.php';
		if(callback)
		{
			BX.ajax.post(url, data, callback);
		}
		else
		{
			BX.ajax.post(url, data, function(){ _this.Reload(); });
		}
	};

	this.EnableSettings = function(enabled, callback)
	{
		var url = '/bitrix/components' + this.vars.component_path + '/settings.php?FORM_ID=' + this.name + '&action=enable&enabled=' + (enabled? 'Y':'N') + '&sessid=' + this.vars.sessid;

		if(BX.type.isFunction(callback))
		{
			BX.ajax.get(url, callback);
		}
		else
		{
			BX.ajax.get(url, function(){ _this.Reload(); });
		}
	};
	this.Reload = function()
	{
		var ajaxId = this.vars.ajax.AJAX_ID;
		if(ajaxId != '')
		{
			var url = BX.util.remove_url_param(this.vars.current_url, 'bxajaxid');
			if(url[url.length - 1] === '?')
			{
				//remove_url_param fix
				url = url.substr(0, url.length - 1);
			}
			BX.ajax.insertToNode(url + (url.indexOf('?') < 0 ? '?' : '&') + 'bxajaxid=' + ajaxId, 'comp_' + ajaxId);
		}
		else
		{
			window.location = window.location.href;
		}
	};
	this.ReloadActiveTab = function()
	{
		var tabParamName = this.name + '_active_tab';
		var url = BX.util.remove_url_param(this.vars.current_url, tabParamName);
		if(url[url.length - 1] === '?')
		{
			//remove_url_param fix
			url = url.substr(0, url.length - 1);
		}

		url += (url.indexOf('?') < 0 ? '?' : '&') +  tabParamName + '=' + this.GetActiveTabId();

		var ajaxId = this.vars.ajax.AJAX_ID;
		if(ajaxId != '')
		{
			BX.ajax.insertToNode(url + '&bxajaxid=' + ajaxId, 'comp_' + ajaxId);
		}
		else
		{
			window.location = url;
		}
	};
	this.SetViewModeVisibility = function(visible)
	{
		visible = !!visible;
		if(this.isVisibleInViewMode === visible)
		{
			return;
		}

		this.isVisibleInViewMode = visible;

		var container = BX("container_" + this.name.toLowerCase());
		if(container)
		{
			container.style.display = this.isVisibleInViewMode ? "" : "none";
		}

		BX.userOptions.save("main.interface.form", this.name, "show_in_view_mode", visible ? "Y" : "N", false);
	};
}

BX.OrderSidebarFieldSelector = function()
{
	this._id = '';
	this._fieldId = '';
	this._currentItem = null;
	this._elem = null;
	this._settings = {};
	this._items = {};
	this._popupMenu = null;
};

BX.OrderSidebarFieldSelector.prototype =
{
	initialize: function(id, fieldId, elem, settings)
	{
		this._id = id;
		this._fieldId = fieldId;
		this._elem = elem;
		this._settings = settings;

		this._items = {};
		var opts = this.getSettings('options', null);
		if(opts)
		{
			for(var i = 0; i < opts.length; i++)
			{
				var opt = opts[i];
				if(BX.type.isNotEmptyString(opt['id']))
				{
					var optId = opt['id'];
					this._items[optId] = BX.OrderSidebarFieldSelectorItem.create(optId, this, { "text": BX.type.isNotEmptyString(opt['caption']) ? opt['caption'] : optId });
				}
			}
		}

		BX.bind(this._elem, 'click', BX.proxy(this._onElementClick, this));

		var button = BX(this.getSettings('buttonId', ''));
		if(button)
		{
			BX.bind(button, 'click', BX.proxy(this._onElementClick, this));
		}
	},
	getSettings: function(name, defaultval)
	{
		var s = this._settings;
		return  s[name] ? s[name] : defaultval;
	},
	getFieldId: function()
	{
		return this._fieldId;
	},
	getCurrentItem: function()
	{
		return this._currentItem;
	},
	setCurrentItemId: function(itemId, save)
	{
		var item = null;
		for(var key in this._items)
		{
			if(!this._items.hasOwnProperty(key))
			{
				continue;
			}

			if(this._items[key].getId() === itemId)
			{
				item = this._items[key];
			}
		}

		if(!item)
		{
			return;
		}

		this._currentItem = item;
		if(this._elem)
		{
			this._elem.innerHTML = item.getTitle();
		}

		save = !!save;
		if(save)
		{
			var editor = BX.OrderInstantEditor.getDefault();
			if(editor)
			{
				editor.saveFieldValue(this._fieldId, item.getId());
			}

			BX.OrderSidebarFieldSelector._synchronize(this);
		}

	},
	_onElementClick: function(e)
	{
		var menuItems = [];
		for(var key in this._items)
		{
			if(!this._items.hasOwnProperty(key))
			{
				continue;
			}

			var item = this._items[key].createMenuItem();
			if(item)
			{
				menuItems.push(item);
			}
		}

		BX.PopupMenu.show(
			this._id,
			this._elem,
			menuItems,
			{ "offsetTop": 0, "offsetLeft": 0 }
		);

		this._popupMenu = BX.PopupMenu.currentItem;
	},
	handleItemChange: function(item)
	{
		if(this._popupMenu && this._popupMenu.popupWindow)
		{
			this._popupMenu.popupWindow.close();
		}

		this.setCurrentItemId(item.getId(), true);
	}
};
BX.OrderSidebarFieldSelector.items = {};
BX.OrderSidebarFieldSelector.create = function(id, fieldId, elem, settings)
{
	var self = new BX.OrderSidebarFieldSelector();
	self.initialize(id, fieldId, elem, settings);
	this.items[id] = self;
	return self;
};

BX.OrderSidebarFieldSelector._synchronize = function(item)
{
	//var type = item.getEntityType();
	//var id = item.getEntityId();

	var selectedItem = item.getCurrentItem();
	if(!selectedItem)
	{
		return;
	}

	var fieldId = item.getFieldId();
	for(var itemId in this.items)
	{
		if(!this.items.hasOwnProperty(itemId))
		{
			continue;
		}

		var curItem = this.items[itemId];
		if(curItem === item)
		{
			continue;
		}

		if(fieldId === curItem.getFieldId())
		{
			curItem.setCurrentItemId(selectedItem.getId(), false);
		}
	}
};

BX.OrderSidebarFieldSelectorItem = function()
{
	this._id = '';
	this._parent = null;
	this._settings = {};
};

BX.OrderSidebarFieldSelectorItem.prototype =
{
	initialize: function(id, parent, settings)
	{
		this._id = id;
		this._parent = parent;
		this._settings = settings;
	},
	getSettings: function(name, defaultval)
	{
		var s = this._settings;
		return  s[name] ? s[name] : defaultval;

	},
	getId: function()
	{
		return this._id;
	},
	getTitle: function()
	{
		return this.getSettings('text', this._id);
	},
	createMenuItem: function()
	{
		return {
			"text":  this.getTitle(),
			"onclick": BX.proxy(this._onMenuItemClick, this)
		};
	},
	_onMenuItemClick: function()
	{
		if(this._parent)
		{
			this._parent.handleItemChange(this);
		}
	}
};

BX.OrderSidebarFieldSelectorItem.create = function(id, parent, settings)
{
	var self = new BX.OrderSidebarFieldSelectorItem();
	self.initialize(id, parent, settings);
	return self;
};

BX.OrderSidebarUserSelector = function()
{
	this._id = '';
	this._settings = {};
	this._button = null;
	this._container = null;
	this.componentName = '';
	this._componentContainer = null;
	this._componentObj = null;
	this._fieldId = '';
	this._editor = null;
	this._dlg = null;
	this._dlgDisplayed = false;
	this._userInfo = null;
	this._userInfoProvider = null;
	this._enableLazyLoad = false;
	this._isLoaded = false;
	this._serviceUrl = '';
	this._options = {};
	this._userSelectorScriptLoaded = null;
};

BX.OrderSidebarUserSelector.prototype =
{
	initialize: function(id, button, container, componentName, options)
	{
		this._id = BX.type.isNotEmptyString(id) ? id : ('order_sidebar_user_sel_' + Math.random());
		if(!BX.type.isElementNode(button))
		{
			throw 'BX.OrderSidebarUserSelector: button is not defined';
		}

		this._button = button;

		if(!BX.type.isElementNode(container))
		{
			throw 'BX.OrderSidebarUserSelector: container is not defined';
		}

		this._container = container;

		if(!BX.type.isNotEmptyString(componentName))
		{
			throw 'BX.OrderSidebarUserSelector: componentName is not defined';
		}
		this.componentName = componentName;

		this._options = options ? options : {};
		this._enableLazyLoad = this.getOption('enableLazyLoad', false);
		this._serviceUrl = this.getOption('serviceUrl', '');

		if(!this._enableLazyLoad)
		{
			this._componentContainer = BX(componentName + '_selector_content');
			var objName = 'O_' + componentName;
			if(window[objName])
			{
				this._componentObj = window[objName];
				this._componentObj.onSelect = BX.delegate(this._handleUserSelect, this);
				this._isLoaded = true;
			}
		}

		BX.bind(this._button, 'click', BX.delegate(this._handleButtonClick, this));

		this._fieldId = this.getStringOption('fieldId');
		this._userInfoProvider = BX.OrderUserInfoProvider.getItemById(this.getStringOption('userInfoProviderId'));

		if(this._fieldId !== '')
		{
			var editorId = this.getOption('editorId', '');
			if(editorId !== '')
			{
				var editor = BX.OrderInstantEditor.items[editorId];
				if(editor)
				{
					this._setupEditor(editor);
				}
				else
				{
					BX.addCustomEvent(
						'OrderInstantEditorCreated',
						BX.delegate(this._handleEditorCreation, this)
					);
				}
			}
		}
	},
	openDialog: function()
	{
		this._dlg = new BX.PopupWindow(
			this._id,
			this._button,
			{
				autoHide: true,
				draggable: false,
				closeByEsc: true,
				offsetLeft: 0,
				offsetTop: 0,
				bindOptions: { forceBindPosition: true },
				content : this._componentContainer,
				events:
				{
					onPopupShow: BX.delegate(
						function()
						{
							this._dlgDisplayed = true;
						},
						this
					),
					onPopupClose: BX.delegate(
						function()
						{
							this._dlgDisplayed = false;
							this._dlg.destroy();
						},
						this
					),
					onPopupDestroy: BX.delegate(
						function()
						{
							this._dlg = null;
						},
						this
					)
				}
			}
		);

		this._dlg.show();
	},
	closeDialog: function()
	{
		if(this._dlg)
		{
			this._dlg.close();
		}
	},
	getSetting: function(name, defaultval)
	{
		return this._settings[name] ? this._settings[name] : defaultval;
	},
	getOption: function(name, defaultval)
	{
		return this._options.hasOwnProperty(name) ? this._options[name] : defaultval;
	},
	getStringOption: function(name)
	{
		return BX.type.isNotEmptyString(this._options[name]) ? this._options[name] : '';
	},
	layout: function()
	{
		this._container.href = this._userInfo ? this._userInfo.getProfileUrl() : '#';
		var nameElem = BX.findChild(this._container, { className: "order-detail-info-resp-name" }, true, false);
		if(nameElem)
		{
			nameElem.innerHTML = BX.util.htmlspecialchars(
				this._userInfo ? this._userInfo.getFullName() : ''
			);
		}

		var postElem = BX.findChild(this._container, { className: "order-detail-info-resp-descr" }, true, false);
		if(postElem)
		{
			postElem.innerHTML = BX.util.htmlspecialchars(
				this._userInfo ? this._userInfo.getWorkPosition() : ''
			);
		}

		var photoElem = BX.findChild(this._container, { className: "order-detail-info-resp-img" }, true, false);
		if(photoElem)
		{
			BX.cleanNode(photoElem, false);
			photoElem.appendChild(
				BX.create("IMG", { attrs: { src: this._userInfo ? this._userInfo.getPhotoUrl() : '' } })
			);
		}
	},
	toggleDialog: function()
	{
		if(this._dlg && this._dlgDisplayed)
		{
			this.closeDialog();
		}
		else
		{
			this.openDialog();
		}
	},
	_handleButtonClick: function()
	{
		if(this._isLoaded)
		{
			this.toggleDialog();
			return;
		}

		if(this._enableLazyLoad && this._serviceUrl !== "")
		{
			this._userSelectorScriptLoaded = BX.delegate(this._handleUserSelectorScriptLoaded, this);
			BX.addCustomEvent("onAjaxSuccessFinish", this._userSelectorScriptLoaded);
			BX.ajax(
				{
					url: this._serviceUrl,
					method: "POST",
					dataType: "html",
					data:
					{
						"MODE": "GET_USER_SELECTOR",
						"NAME": this.componentName
					},
					onsuccess: BX.delegate(this._handleUserSelectorHtmlLoaded, this)
				}
			);
		}
	},
	_handleUserSelectorHtmlLoaded: function(data)
	{
		this._container.parentNode.appendChild(BX.create("DIV", { html: data  }));
		this._isLoaded = true;
	},
	_handleUserSelectorScriptLoaded: function(config)
	{
		if(config["url"] !== this._serviceUrl)
		{
			return;
		}

		BX.removeCustomEvent("onAjaxSuccessFinish", this._userSelectorScriptLoaded);
		this._userSelectorScriptLoaded = null;

		this._componentContainer = BX(this.componentName + "_selector_content");
		var objName = "O_" + this.componentName;
		if(window[objName])
		{
			this._componentObj = window[objName];
			this._componentObj.onSelect = BX.delegate(this._handleUserSelect, this);
		}

		this.openDialog();
	},
	_handleUserSelect: function(user)
	{
		this.closeDialog();

		if(!this._userInfoProvider)
		{
			return;
		}

		var self = this;
		this._userInfoProvider.getInfo(
			user.id,
			function(userInfo)
			{
				self._userInfo = userInfo;
				self.layout();
				if(self._fieldId.length > 0)
				{
					var editor = self._editor;
					if(!editor)
					{
						editor = BX.OrderInstantEditor.getDefault();
					}

					if(editor)
					{
						editor.saveFieldValue(self._fieldId, userInfo.getId());
					}
				}
			}
		);
	},
	_handleEditorCreation: function(editor)
	{
		var editorId = this.getOption('editorId', '');
		if(editorId !== '' && editor.getId() === editorId)
		{
			this._setupEditor(editor);
		}
	},
	_handleEditorFieldValueSaved: function(name, val)
	{
		if(this._fieldId !== name || !this._userInfoProvider)
		{
			return;
		}

		if(this._userInfo && this._userInfo.getId() === val)
		{
			return;
		}

		var self = this;
		this._userInfoProvider.getInfo(
			val,
			function(userInfo)
			{
				self._userInfo = userInfo;
				self.layout();
			}
		);
	},
	_setupEditor: function(editor)
	{
		if(this._editor)
		{
			BX.removeCustomEvent(
				this._editor,
				'OrderInstantEditorFieldValueSaved',
				BX.delegate(this._handleEditorFieldValueSaved, this)
			);
		}

		this._editor = editor;

		if(this._editor)
		{
			BX.addCustomEvent(
				this._editor,
				'OrderInstantEditorFieldValueSaved',
				BX.delegate(this._handleEditorFieldValueSaved, this)
			);
		}
	}
};

BX.OrderSidebarUserSelector.create = function(id, button, container, componentName, options)
{
	var self = new BX.OrderSidebarUserSelector();
	self.initialize(id, button, container, componentName, options);
	return self;
};

BX.OrderUserSearchField = function()
{
	this._id = '';
	this._search_input = null;
	this._data_input = null;
	this._componentName = '';
	this._componentContainer = null;
	this._componentObj = null;
	this._dlg = null;
	this._dlgDisplayed = false;
	this._currentUser = {};
};

BX.OrderUserSearchField.prototype =
{
	initialize: function(id, search_input, data_input, componentName, user)
	{
		this._id = BX.type.isNotEmptyString(id) ? id : ('order_user_search_field_' + Math.random());

		if(!BX.type.isElementNode(search_input))
		{
			throw  "BX.OrderUserSearchField: 'search_input' is not defined!";
		}
		this._search_input = search_input;

		if(!BX.type.isElementNode(data_input))
		{
			throw  "BX.OrderUserSearchField: 'data_input' is not defined!";
		}
		this._data_input = data_input;

		if(!BX.type.isNotEmptyString(componentName))
		{
			throw  "BX.OrderUserSearchField: 'componentName' is not defined!";
		}
		this._componentName = componentName;

		this._componentContainer = BX(componentName + '_selector_content');
		var objName = 'O_' + componentName;
		if(window[objName])
		{
			this._componentObj = window[objName];
			this._componentObj.onSelect = BX.delegate(this._handleUserSelect, this);
			this._componentObj.searchInput = search_input;

			BX.bind(search_input, 'keyup', BX.proxy(this._handleSearchKey, this));
			BX.bind(search_input, 'focus', BX.proxy(this._handleSearchFocus, this));
			BX.bind(document, 'click', BX.delegate(this._handleExternalClick, this));
		}

		this._currentUser = user ? user : {};
		this._adjustUser();
	},
	openDialog: function()
	{
		this._dlg = new BX.PopupWindow(
			this._id,
			this._search_input,
			{
				autoHide: false,
				draggable: false,
				//closeByEsc: true,
				offsetLeft: 0,
				offsetTop: 0,
				bindOptions: { forceBindPosition: true },
				content : this._componentContainer,
				events:
				{
					onPopupShow: BX.delegate(
						function()
						{
							this._dlgDisplayed = true;
						},
						this
					),
					onPopupClose: BX.delegate(
						function()
						{
							this._dlgDisplayed = false;
							this._dlg.destroy();
						},
						this
					),
					onPopupDestroy: BX.delegate(
						function()
						{
							this._dlg = null;
						},
						this
					)
				}
			}
		);

		this._dlg.show();
	},
	_adjustUser: function()
	{
		this._search_input.value = this._currentUser['name'] ? this._currentUser.name : '';
		this._data_input.value = this._currentUser['id'] ? this._currentUser.id : 0;
	},
	closeDialog: function()
	{
		if(this._dlg)
		{
			this._dlg.close();
		}
	},
	_handleExternalClick: function(e)
	{
		if(!e)
		{
			e = window.event;
		}

		if(e.target !== this._search_input &&
			!BX.findParent(e.target, { attribute:{ id: this._componentName + '_selector_content' } }))
		{
			this._adjustUser();
			this.closeDialog();
		}
	},
	_handleSearchKey: function(e)
	{
		if(!this._dlg || !this._dlgDisplayed)
		{
			this.openDialog();
		}

		this._componentObj.search();
	},
	_handleSearchFocus: function(e)
	{
		if(!this._dlg || !this._dlgDisplayed)
		{
			this.openDialog();
		}

		this._componentObj._onFocus(e);
	},
	_handleUserSelect: function(user)
	{
		this._currentUser = user;
		this._adjustUser();
		this.closeDialog();
	}
};

BX.OrderUserSearchField.items = {};

BX.OrderUserSearchField.create = function(id, search_input, data_input, componentName, user)
{
	var self = new BX.OrderUserSearchField();
	self.initialize(id, search_input, data_input, componentName, user);
	this.items[id] = self;
	return self;
};

BX.OrderUserLinkField = function()
{
	this._settings = {};
	this._container = null;
	this._fieldId = '';
	this._editor = null;
	this._userInfoProvider = null;
	this._userInfo = null;

};

BX.OrderUserLinkField.prototype =
{
	initialize: function(settings)
	{
		this._settings = settings ? settings : {};
		this._container = this.getSetting('container', null);
		if(!this._container)
		{
			this._container = BX(this.getSetting('containerId', ''));
		}

		if(!this._container)
		{
			throw 'BX.OrderUserLinkField: container is not found';
		}

		this._userInfoProvider = BX.OrderUserInfoProvider.getItemById(this.getSetting('userInfoProviderId', ''));
		this._userInfo = this.getSetting('userInfo', null);

		this._fieldId = this.getSetting('fieldId', '');
		if(this._fieldId !== '')
		{
			var editorId = this.getSetting('editorId', '');
			if(editorId !== '')
			{
				var editor = BX.OrderInstantEditor.items[editorId];
				if(editor)
				{
					this._setupEditor(editor);
				}
				else
				{
					BX.addCustomEvent(
						'OrderInstantEditorCreated',
						BX.delegate(this._handleEditorCreation, this)
					);
				}
			}
		}
	},
	getSetting: function (name, defaultval)
	{
		return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
	},
	layout: function()
	{
		this._container.href = this._userInfo ? this._userInfo.getProfileUrl() : '#';

		var nameElem = BX.findChild(this._container, { className: 'order-detail-info-resp-name' }, true, false);
		if(nameElem)
		{
			nameElem.innerHTML = BX.util.htmlspecialchars(
				this._userInfo ? this._userInfo.getFullName() : ''
			);
		}

		var postElem = BX.findChild(this._container, { className: 'order-detail-info-resp-descr' }, true, false);
		if(postElem)
		{
			postElem.innerHTML = BX.util.htmlspecialchars(
				this._userInfo ? this._userInfo.getWorkPosition() : ''
			);
		}

		var photoElem = BX.findChild(this._container, { className: 'order-detail-info-resp-img' }, true, false);
		if(photoElem)
		{
			BX.cleanNode(photoElem, false);
			photoElem.appendChild(
				BX.create('IMG',
					{
						attrs: { src: this._userInfo ? this._userInfo.getPhotoUrl() : '' }
					}
				)
			);
		}
	},
	_handleEditorCreation: function(editor)
	{
		var editorId = this.getSetting('editorId', '');
		if(editorId !== '' && editor.getId() === editorId)
		{
			this._setupEditor(editor);
		}
	},
	_setupEditor: function(editor)
	{
		if(this._editor)
		{
			BX.removeCustomEvent(
				this._editor,
				'OrderInstantEditorFieldValueSaved',
				BX.delegate(this._handleEditorFieldValueSaved, this)
			);
		}

		this._editor = editor;

		if(this._editor)
		{
			BX.addCustomEvent(
				this._editor,
				'OrderInstantEditorFieldValueSaved',
				BX.delegate(this._handleEditorFieldValueSaved, this)
			);
		}
	},
	_handleEditorFieldValueSaved: function(name, val)
	{
		if(this._fieldId !== name || !this._userInfoProvider)
		{
			return;
		}

		var self = this;
		this._userInfoProvider.getInfo(
			val,
			function(userInfo)
			{
				self._userInfo = userInfo;
				self.layout();
			}
		);
	}
};

BX.OrderUserLinkField.create = function(settings)
{
	var self = new BX.OrderUserLinkField();
	self.initialize(settings);
	return self;
};

BX.OrderUserInfo = function()
{
	this._data = {};
};

BX.OrderUserInfo.prototype =
{
	initialize: function(data)
	{
		this._data = data ? data : {};
	},
	getId: function()
	{
		return BX.type.isNotEmptyString(this._data['ID']) ? this._data['ID'] : '';
	},
	getProfileUrl: function()
	{
		return BX.type.isNotEmptyString(this._data['USER_PROFILE']) ? this._data['USER_PROFILE'] : '';
	},
	getFullName: function()
	{
		return BX.type.isNotEmptyString(this._data['FULL_NAME']) ? this._data['FULL_NAME'] : '';
	},
	getWorkPosition: function()
	{
		return BX.type.isNotEmptyString(this._data['WORK_POSITION']) ? this._data['WORK_POSITION'] : '';
	},
	getPhotoUrl: function()
	{
		return BX.type.isNotEmptyString(this._data['PERSONAL_PHOTO']) ? this._data['PERSONAL_PHOTO'] : '';
	}
};

BX.OrderUserInfo.items = {};
BX.OrderUserInfo.create = function(data)
{
	var self = new BX.OrderUserInfo();
	self.initialize(data);
	this.items[self.getId()] = self;
	return self;
};

BX.OrderUserInfoProvider = function()
{
	this._id = '';
	this._settings = {};
	this._serviceUrl = '';
	this._items = {};
};

BX.OrderUserInfoProvider.prototype =
{
	initialize: function(id, settings)
	{
		if(!BX.type.isNotEmptyString(id))
		{
			throw 'BX.OrderUserInfoProvider: id is not defined';
		}

		this._id = id;

		this._settings = settings ? settings : {};
		var serviceUrl = this.getSetting('serviceUrl', '');
		if(serviceUrl === '')
		{
			throw 'BX.OrderUserInfoProvider: serviceUrl is not found';
		}

		this._serviceUrl = serviceUrl;
	},
	getId: function()
	{
		return this._id;
	},
	getSetting: function(name, defaultval)
	{
		return this._settings[name] ? this._settings[name] : defaultval;
	},
	getInfo: function(userId, callback)
	{
		if(!BX.type.isString(userId))
		{
			userId = userId.toString();
		}

		if(!BX.type.isNotEmptyString(userId))
		{
			if(BX.type.isFunction(callback))
			{
				callback(null);
			}
			return;
		}

		if(typeof(this._items[userId]) !== 'undefined')
		{
			if(BX.type.isFunction(callback))
			{
				callback(this._items[userId]);
			}
			return;
		}

		var self = this;
		BX.ajax(
			{
				'url': this._serviceUrl,
				'method': 'POST',
				'dataType': 'json',
				'data':
				{
					'MODE': 'GET_USER_INFO',
					'USER_ID': userId,
					'USER_PROFILE_URL_TEMPLATE': this.getSetting('userProfileUrlTemplate', '')
				},
				onsuccess: function(data)
					{
						var item = BX.OrderUserInfo.create(data['USER_INFO'] ? data['USER_INFO'] : {});
						self._items[userId] = item;
						if(BX.type.isFunction(callback))
						{
							callback(item);
						}
					},
				onfailure: function(data)
					{
						self._showError(self.getMessage('generalError'));
						if(BX.type.isFunction(callback))
						{
							callback(null);
						}
					}
			}
		);
	},
	getMessage: function(name)
	{
		var msg = BX.OrderUserInfoProvider.messages;
		return typeof(msg[name]) !== 'undefined' ? msg[name] : '';
	},
	_showError: function(msg)
	{
		alert(msg);
	}
};

BX.OrderUserInfoProvider.items = {};
BX.OrderUserInfoProvider.getItemById = function(id)
{
	return typeof(this.items[id]) ? this.items[id] : null;
};
BX.OrderUserInfoProvider.createIfNotExists = function(id, settings)
{
	if(typeof(this.items[id]) !== 'undefined')
	{
		return this.items[id];
	}

	var self = new BX.OrderUserInfoProvider();
	self.initialize(id, settings);
	this.items[self.getId()] = self;
	return self;
};

if(typeof(BX.OrderUserInfoProvider.messages) === 'undefined')
{
	BX.OrderUserInfoProvider.messages = {};
}

BX.OrderDateLinkField = function()
{
	this._dataElem = null;
	this._viewElem = null;
	this._settings = {};
};

BX.OrderDateLinkField.prototype =
{
	initialize: function(dataElem, viewElem, settings)
	{
		if(!BX.type.isElementNode(dataElem))
		{
			throw "BX.OrderDateLinkField: 'dataElem' is not defined!";
		}
		this._dataElem = dataElem;
		if(BX.type.isElementNode(viewElem))
		{
			this._viewElem = viewElem;
			BX.bind(viewElem, 'click', BX.delegate(this._onViewClick, this));
		}
		else
		{
			BX.bind(dataElem, 'click', BX.delegate(this._onViewClick, this));
		}
		this._settings = settings ? settings : {};
	},
	getSetting: function (name, defaultval)
	{
		return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
	},
	//layout: function(){},
	_onViewClick: function(e)
	{
		BX.calendar({ node: (this._viewElem ? this._viewElem : this._dataElem), field: this._dataElem, bTime: this.getSetting('showTime', true), bSetFocus: this.getSetting('setFocusOnShow', true), callback: BX.delegate(this._onCalendarSaveValue, this) });
	},
	_onCalendarSaveValue: function(value)
	{
		var s = BX.calendar.ValueToString(value, this.getSetting('showTime', true), false);
		this._dataElem.value = s;
		if(this._viewElem)
		{
			this._viewElem.innerHTML = s;
		}
	}
};

BX.OrderDateLinkField.create = function(dataElem, viewElem, settings)
{
	var self = new BX.OrderDateLinkField();
	self.initialize(dataElem, viewElem, settings);
	return self;
};

BX.OrderEntityEditor = function()
{
	this._id = '';
	this._settings = {};
	this._readonly = false;
	this._dlg = null;
	this._data = null;
	this._info = null;
	this._container = null;
	this._selector = null;
	this._advInfoContainer = null;
};

BX.OrderEntityEditor.prototype =
{
	initialize: function(id, settings, data, info)
	{
		this._id = BX.type.isNotEmptyString(id) ? id : 'ORDER_ENTITY_EDITOR' + Math.random();
		this._settings = settings ? settings : {};

		if(!data)
		{
			data = this._prepareData(settings['data']);
		}

		if(!data)
		{
			throw "BX.OrderEntityEditor: Could not find data!";
		}

		//console.log(data);
		this._data = data;

		this._info = info ? info : BX.OrderEntityInfo.create();

		var selectorId = this.getSetting('entitySelectorId', '');
		if(obOrder && obOrder[selectorId])
		{
			var selector = this._selector = obOrder[selectorId];
			selector.AddOnSaveListener(BX.delegate(this._onEntitySelect, this));
			//selector.AddOnBeforeSearchListener();
		}

		var c = this._container = BX(this.getSetting('containerId', ''));
		if(!c)
		{
			throw "BX.OrderEntityEditor: Could not find field container!";
		}

		this._advInfoContainer = BX(this.getSetting('containerId', '') + '_descr');

		//BX.bind(BX.findChild(c, { className: 'order-element-item-delete'}, true, false), 'click', BX.delegate(this._onDeleteButtonClick, this));

		var btnChangeIgnore = this.getSetting('buttonChangeIgnore', false);
		if (!btnChangeIgnore)
			BX.bind(BX.findChild(c, { className: 'bx-order-edit-order-entity-change'}, true, false), 'click', BX.delegate(this._onChangeButtonClick, this));
		var btnAdd = BX(this.getSetting('buttonAddId', ''));
		BX.bind((btnAdd) ? btnAdd : BX.findChild(c, { className: 'bx-order-edit-order-entity-add'}, true, false), 'click', BX.delegate(this._onAddButtonClick, this));

	},
	getId: function()
	{
		return this._id;
	},
	getTypeName: function()
	{
		return this.getSetting('typeName', '');
	},
	getSetting: function (name, defaultval)
	{
		return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
	},
	getData: function()
	{
		return this._data;
	},
	getMessage: function(name)
	{
		var msgs = BX.OrderEntityEditor.messages;
		return BX.type.isNotEmptyString(msgs[name]) ? msgs[name] : '';
	},
	openDialog: function(anchor, mode)
	{
		if(this._dlg)
		{
			this._dlg.setData(this._data);
			this._dlg.open(anchor, mode);
			return;
		}
		switch(this.getTypeName())
		{
			case 'PHYSICAL':
				this._dlg = BX.OrderPhysicalEditDialog.create(
					this._id,
					this.getSetting('dialog', {}),
					this._data,
					BX.delegate(this._onSaveDialogData, this));
				break;
			case 'CONTACT':
				this._dlg = BX.OrderContactEditDialog.create(
					this._id,
					this.getSetting('dialog', {}),
					this._data,
					BX.delegate(this._onSaveDialogData, this));
				break;
			case 'AGENT':
				this._dlg = BX.OrderAgentEditDialog.create(
					this._id,
					this.getSetting('dialog', {}),
					this._data,
					BX.delegate(this._onSaveDialogData, this));
				break;
		}

		if(this._dlg)
		{
			this._dlg.open(anchor, mode);
		}
	},
	closeDialog: function()
	{
		if(this._dlg)
		{
			this._dlg.close();
		}
	},
	isReadOnly: function()
	{
		return this._readonly;
	},
	setReadOnly: function(readonly)
	{
		readonly = !!readonly;
		if(this._readonly === readonly)
		{
			return;
		}

		this._readonly = readonly;

		/*var deleteButton = BX.findChild(this._container, { className: 'order-element-item-delete'}, true, false);
		if(deleteButton)
		{
			deleteButton.style.display = readonly ? 'none' : '';
		}*/

		var buttonsWrapper = BX.findChild(this._container, { className: 'bx-order-entity-buttons-wrapper'}, true, false);
		if(buttonsWrapper)
		{
			buttonsWrapper.style.display = readonly ? 'none' : '';
		}
	},
	_prepareData: function(settings)
	{
		var typeName = this.getTypeName();
		var enablePrefix = this.getSetting('enableValuePrefix', false);

		if(typeName === 'PHYSICAL')
		{
			return BX.OrderPhysicalData.create(settings);
		}

		if(typeName === 'CONTACT')
		{
			return BX.OrderContactData.create(settings);
		}

		if(typeName === 'AGENT')
		{
			return BX.OrderAgentData.create(settings);
		}
		if(typeName === 'USER')
		{
			return BX.OrderUserData.create(settings);
		}
		if(typeName === 'DIRECTION')
		{
			return BX.OrderDirectionData.create(settings);
		}
		if(typeName === 'NOMEN')
		{
			return BX.OrderNomenData.create(settings);
		}
		if(typeName === 'GROUP')
		{
			return BX.OrderGroupData.create(settings);
		}
		if(typeName === 'FORMED_GROUP')
		{
			return BX.OrderFormedGroupData.create(settings);
		}
		return null;
	},
	_onDeleteButtonClick: function(e)
	{
		if(this._readonly)
		{
			return;
		}

		var dataInput = BX(this.getSetting('dataInputId', ''));
		if(dataInput)
		{
			dataInput.value = 0;
		}

		BX.cleanNode(BX.findChild(this._container, { className: 'bx-order-entity-info-wrapper'}, true, false));
		if (this._advInfoContainer)
			BX.cleanNode(this._advInfoContainer);

		BX.onCustomEvent('OrderEntitySelectorChangeValue', [this.getId(), this.getTypeName(), 0, this]);
	},
	_onChangeButtonClick: function(e)
	{
		if(this._readonly)
		{
			return;
		}

		var selector = this._selector;
		if(selector)
		{
			selector.Open();
		}
	},
	_onAddButtonClick: function(e)
	{
		if(this._readonly)
		{
			return;
		}

		//this._data.reset();
		this.openDialog(
			BX.findChild(this._container, { className: 'bx-order-edit-order-entity-add'}, true, false),
			'CREATE'
		);
	},
	_onSaveDialogData: function(dialog)
	{
		this._data = this._dlg.getData();

		var url = this.getSetting('serviceUrl', '');
		var action = this.getSetting('actionName', '');

		if(!(BX.type.isNotEmptyString(url) && BX.type.isNotEmptyString(action)))
		{
			return;
		}

		var self = this;
		console.log(this._data.toJSON());
		//console.log(url);
		//console.log(action);
		BX.ajax(
			{
				'url': url,
				'method': 'POST',
				'dataType': 'json',
				'timeout':60,
				'data':
				{
					'ACTION' : action,
					'DATA': this._data.toJSON()
				},
				onsuccess: function(data)
				{
					console.log(data);
					if(data['ERROR'])
					{
						self._showDialogError(data['ERROR']);
					}
					else if(!data['DATA'])
					{
						self._showDialogError('BX.OrderEntityEditor: Could not find contact data!');
					}
					else
					{
						self._data = self._prepareData(data['DATA']);
						self._info = BX.OrderEntityInfo.create(data['INFO'] ? data['INFO'] : {});
						var newDataInput = BX(self.getSetting('newDataInputId', ''));
						if(newDataInput)
						{
							newDataInput.value = self._data.getId();
							BX.onCustomEvent('OrderEntitySelectorChangeValue', [self.getId(), self.getTypeName(), self._data.getId(), self]);
						}
						self.layout();
						self.closeDialog();
					}
				},
				onfailure: function(data)
				{
					self._showDialogError(data['ERROR'] ? data['ERROR'] : self.getMessage('unknownError'));
				}
			}
		);
	},
	layout: function()
	{
		var dataInput = BX(this.getSetting('dataInputId', ''));
		if(dataInput)
		{
			dataInput.value = this._data.getId();
		}
		var view = BX.findChild(this._container, { className: 'bx-order-entity-info-wrapper'}, true, false);
		if(!view)
		{
			return;
		}

		BX.cleanNode(view);
		view.appendChild(
			BX.create(
				'A',
				{
					attrs:
					{
						className: 'bx-order-entity-info-link',
						href: this._info.getSetting('url', ''),
						target: '_blank'
					},
					text: this._info.getSetting('title', this._data.getId())
				}
			)
		);
		/*var descBox=this._advInfoContainer;
		BX.cleanNode(descBox);
		console.log(this,'this');
		console.log(descBox,'descBox');
		if(typeof(this._info.getSetting('advancedInfo')) != "undefined" &&
			this._info.getSetting('advancedInfo') !== null)
			for(var j in this._info.getSetting('advancedInfo')['multiFields']) {
				var val=this._info.getSetting('advancedInfo')['multiFields'][j];
				console.log(val);
				var addDesc = document.createElement('span');
				if(val['TYPE_ID']=='PHONE') {
					addDesc.className = 'order-offer-info-descrip-tem order-offer-info-descrip-tel';
					addDesc.appendChild(document.createTextNode("Tel: " + val['VALUE']));
					var addDescIcon=document.createElement('a');
					addDescIcon.className="order-offer-info-descrip-icon";
					addDescIcon.href="callto:"+val['VALUE'];
					addDesc.appendChild(addDescIcon);
				} else if(val['TYPE_ID']=='EMAIL') {
					addDesc.className = 'order-offer-info-descrip-tem order-offer-info-descrip-email';
					addDesc.appendChild(document.createTextNode("Email: " + val['VALUE']));
					var addDescIcon=document.createElement('a');
					addDescIcon.className="order-offer-info-descrip-icon";
					addDescIcon.href="mailto:"+val['VALUE'];
					addDesc.appendChild(addDescIcon);
				}
				descBox.appendChild(addDesc);
				var br=document.createElement('br');
				descBox.appendChild(br);
			}*/
		/*view.appendChild(
			BX.create(
				'br'
			)
		);
		view.appendChild(
			BX.create(
				'span',
				{
					attrs:
					{
						className: 'bx-order-entity-info-link',
						href: this._info.getSetting('url', ''),
						target: '_blank'
					},
					text: this._info.getSetting('title', this._data.getId())
				}
			)
		);*/

		/*view.appendChild(
			BX.create(
				'SPAN',
				{
					attrs:
					{
						className: 'order-element-item-delete'
					},
					events:
					{
						click: BX.delegate(this._onDeleteButtonClick, this)
					}
				}
			)
		);*/

		if (this._advInfoContainer)
		{
			this._advInfoContainer.innerHTML = this._prepareAdvInfoHTML();
		}
	},
	_onEntitySelect: function(settings)
	{
		var typeName = this.getTypeName().toLowerCase();
		var item = settings[typeName] && settings[typeName][0] ? settings[typeName][0] : null;
		if(!item)
		{
			return;
		}

		this._data.setId(item['id']);
		this._info = BX.OrderEntityInfo.create(item);
		this.layout();

		BX.onCustomEvent('OrderEntitySelectorChangeValue', [this.getId(), this.getTypeName(), item['id'], this]);
	},
	_showDialogError: function(msg)
	{
		if(this._dlg)
		{
			this._dlg.showError(msg);
		}
	},
	_prepareAdvInfoHTML: function()
	{
		var result = "";
		var type, advInfo, i;
		var contactType = "";
		var phoneItems = [], emailItems = [];
		type = this._info.getSetting("type", null);
		if (type)
		{
			advInfo = this._info.getSetting("advancedInfo", null);
			console.log(advInfo);
			if (advInfo)
			{
				if (advInfo["legal"] && advInfo["legal"]=='Y' &&
					advInfo["contact"] && advInfo["contact"] instanceof Object )
				{
					var contactTitle=advInfo["contact"]['title'];
					var phoneContactItems = [], emailContactItems = [];
					if (advInfo["contact"]["multiFields"] && advInfo["contact"]["multiFields"] instanceof Array)
					{
						var mf = advInfo["contact"]["multiFields"];
						for (i = 0; i < mf.length; i++)
						{
							if (mf[i]["TYPE_ID"] && mf[i]["TYPE_ID"] === "PHONE" && mf[i]["VALUE"]!="")
							{
								phoneContactItems.push({"VALUE": BX.util.trim(mf[i]["VALUE"])});
							}
							if (mf[i]["TYPE_ID"] && mf[i]["TYPE_ID"] === "EMAIL" && mf[i]["VALUE"]!="")
							{
								emailContactItems.push({"VALUE": BX.util.trim(mf[i]["VALUE"])});
							}
						}
					}
				}

				if (advInfo["multiFields"] && advInfo["multiFields"] instanceof Array)
				{
					var mf = advInfo["multiFields"];
					for (i = 0; i < mf.length; i++)
					{
						if (mf[i]["TYPE_ID"] && mf[i]["TYPE_ID"] === "PHONE" && mf[i]["VALUE"]!="")
						{
							phoneItems.push({"VALUE": BX.util.trim(mf[i]["VALUE"])});
						}
						if (mf[i]["TYPE_ID"] && mf[i]["TYPE_ID"] === "EMAIL" && mf[i]["VALUE"]!="")
						{
							emailItems.push({"VALUE": BX.util.trim(mf[i]["VALUE"])});
						}
					}
				}
				
				switch (type)
				{
					case 'physical':
						if (phoneItems.length > 0)
						{
							result +=
								"<span class=\"order-offer-info-descrip-tem order-offer-info-descrip-tel\">" +
								this.getMessage("prefPhone") + ": " + BX.util.htmlspecialchars(phoneItems[0]['VALUE']) +
								"<a href=\"callto:" + BX.util.htmlspecialchars(phoneItems[0]['VALUE']) +
								"\" class=\"order-offer-info-descrip-icon\"></a></span><br/>";
						}
						if (emailItems.length > 0)
						{
							result +=
								"<span class=\"order-offer-info-descrip-tem order-offer-info-descrip-email\">" +
								this.getMessage("prefEmail") + ": " + BX.util.htmlspecialchars(emailItems[0]['VALUE']) +
								"<a href=\"mailto:" + BX.util.htmlspecialchars(emailItems[0]['VALUE']) +
								"\" class=\"order-offer-info-descrip-icon\"></a></span><br/>";
						}
						break;
					case 'contact':
						if (phoneItems.length > 0)
						{
							result +=
								"<span class=\"order-offer-info-descrip-tem order-offer-info-descrip-tel\">" +
								this.getMessage("prefPhone") + ": " + BX.util.htmlspecialchars(phoneItems[0]["VALUE"]) +
								"<a href=\"callto:" + BX.util.htmlspecialchars(phoneItems[0]["VALUE"]) +
								"\" class=\"order-offer-info-descrip-icon\"></a></span><br/>";
						}
						if (emailItems.length > 0)
						{
							result +=
								"<span class=\"order-offer-info-descrip-tem order-offer-info-descrip-email\">" +
								this.getMessage("prefEmail") + ": " + BX.util.htmlspecialchars(emailItems[0]["VALUE"]) +
								"<a href=\"mailto:" + BX.util.htmlspecialchars(emailItems[0]["VALUE"]) +
								"\" class=\"order-offer-info-descrip-icon\"></a></span><br/>";
						}
						if (contactType)
						{
							result +=
								"<span class=\"order-offer-info-descrip-tem order-offer-info-descrip-type\">" +
								this.getMessage("prefContactType") + ": " + BX.util.htmlspecialchars(contactType) +
								"</span><br/>";
						}
						break;
					case 'agent':
						if (phoneItems.length > 0)
						{
							result +=
								"<span class=\"order-offer-info-descrip-tem order-offer-info-descrip-tel\">" +
								this.getMessage("prefPhone") + ": " + BX.util.htmlspecialchars(phoneItems[0]['VALUE']) +
								"<a href=\"callto:" + BX.util.htmlspecialchars(phoneItems[0]['VALUE']) +
								"\" class=\"order-offer-info-descrip-icon\"></a></span><br/>";
						}
						if (emailItems.length > 0)
						{
							result +=
								"<span class=\"order-offer-info-descrip-tem order-offer-info-descrip-email\">" +
								this.getMessage("prefEmail") + ": " + BX.util.htmlspecialchars(emailItems[0]['VALUE']) +
								"<a href=\"mailto:" + BX.util.htmlspecialchars(emailItems[0]['VALUE']) +
								"\" class=\"order-offer-info-descrip-icon\"></a></span><br/>";
						}
						if(contactTitle && contactTitle!='') {
							result +=
								"<span class=\"order-offer-info-descrip-tem\">" +
								this.getMessage("contactTitle") + ": " + BX.util.htmlspecialchars(contactTitle) +"</span><br/>";
							if (phoneContactItems.length > 0)
							{
								result +=
									"<span class=\"order-offer-info-descrip-tem order-offer-info-descrip-tel\">" +
									this.getMessage("prefPhone") + ": " + BX.util.htmlspecialchars(phoneContactItems[0]['VALUE']) +
									"<a href=\"callto:" + BX.util.htmlspecialchars(phoneContactItems[0]['VALUE']) +
									"\" class=\"order-offer-info-descrip-icon\"></a></span><br/>";
							}
							if (emailContactItems.length > 0)
							{
								result +=
									"<span class=\"order-offer-info-descrip-tem order-offer-info-descrip-email\">" +
									this.getMessage("prefEmail") + ": " + BX.util.htmlspecialchars(emailContactItems[0]['VALUE']) +
									"<a href=\"mailto:" + BX.util.htmlspecialchars(emailContactItems[0]['VALUE']) +
									"\" class=\"order-offer-info-descrip-icon\"></a></span><br/>";
							}
						}
						break;
				}
			}
		}

		return result;
	}
};

if(typeof(BX.OrderEntityEditor.messages) === 'undefined')
{
	BX.OrderEntityEditor.messages = {};
}

BX.OrderEntityEditor.items = {};

BX.OrderEntityEditor.create = function(id, settings, data, info)
{
	var self = new BX.OrderEntityEditor();
	self.initialize(id, settings, data, info);
	this.items[id] = self;
	return self;
};

BX.OrderPhysicalEditDialog = function()
{
	this._id = '';
	this._settings = {};
	this._dlg = null;
	this._dlgCfg = {};
	this._data = null;
	this._mode = 'CREATE';
	this._onSaveCallback = null;
};

BX.OrderPhysicalEditDialog.prototype =
{
	initialize: function(id, settings, data, onSaveCallback)
	{
		this._id = BX.type.isNotEmptyString(id) ? id : 'ORDER_CONTACT_EDIT_DIALOG_' + Math.random();
		this._settings = settings ? settings : {};
		this._data = data ? data : BX.OrderPhysicalData.create();
		this._onSaveCallback = BX.type.isFunction(onSaveCallback) ? onSaveCallback : null;
	},
	getSetting: function (name, defaultval)
	{
		return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
	},
	getData: function()
	{
		return this._data;
	},
	setData: function(data)
	{
		this._data = data ? data : BX.OrderPhysicalData.create();
	},
	isOpened: function()
	{
		return this._dlg && this._dlg.isShown();
	},
	open: function(anchor, mode)
	{
		if(!BX.type.isNotEmptyString(mode) || (mode !== 'CREATE' && mode !== 'EDIT'))
		{
			mode = this._mode;
		}

		if(this._dlg && this._mode === mode)
		{
			if(!this._dlg.isShown())
			{
				this._dlg.show();
			}
			return;
		}

		if(this._mode !== mode)
		{
			this._mode = mode;
		}

		var cfg = this._dlgCfg = {};
		cfg['id'] = this._id;
		this._dlg = new BX.PopupWindow(
			cfg['id'],
			anchor,
			{
				autoHide: false,
				draggable: true,
				offsetLeft: 0,
				offsetTop: 0,
				bindOptions: { forceBindPosition: false },
				closeByEsc: true,
				closeIcon: { top: '10px', right: '15px'},
				titleBar:
				{
					content: BX.OrderPopupWindowHelper.prepareTitle(this.getSetting('title', 'New contact'))
				},
				events:
				{
					//onPopupShow: function(){},
					onPopupClose: BX.delegate(this._onPopupClose, this),
					onPopupDestroy: BX.delegate(this._onPopupDestroy, this)
				},
				content: this._prepareContent(),
				buttons: this._prepareButtons()
			}
		);

		this._dlg.show();
	},
	close: function()
	{
		if(this._dlg)
		{
			this._dlg.close();
		}
	},
	showError: function(msg)
	{
		var errorWrap = BX(this._getElementId('errors'));
		if(errorWrap)
		{
			errorWrap.innerHTML = msg;
			errorWrap.style.display = '';
		}
	},
	_onPopupClose: function()
	{
		this._dlg.destroy();
	},
	_onPopupDestroy: function()
	{
		this._dlg = null;
	},
	_prepareContent: function()
	{
		var wrapper = BX.create(
			'DIV',
			{
				attrs: { className: 'bx-order-dialog-quick-create-popup' }
			}
		);

		var data = this._data;
		wrapper.appendChild(
			BX.create(
				'DIV',
				{
					attrs:
					{
						className: 'bx-order-dialog-quick-create-error-wrap',
						style: 'display:none'
					},
					props: { id: this._getElementId('errors') }
				}
			)
		);
		wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('lastName'), title: this.getSetting('lastNameTitle', 'Last Name'), value: data.getLastName() }));
		wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('name'), title: this.getSetting('nameTitle', 'Name'), value: data.getName() }));
		wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('secondName'), title: this.getSetting('secondNameTitle', 'Second Name'), value: data.getSecondName() }));
		wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('email'), title: this.getSetting('emailTitle', 'E-mail'), value: data.getEmail() }));
		wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('phone'), title: this.getSetting('phoneTitle', 'Phone'), value: data.getPhone() }));
		/*wrapper.appendChild(BX.OrderPopupWindowHelper.prepareSelectField({ id: this._getElementId('gender'), title: this.getSetting('genderTitle', 'Gender'), value: data.getGender(), items: this.getSetting('genderList',null) }));
		wrapper.appendChild(BX.OrderPopupWindowHelper.prepareDateField({ id: this._getElementId('bDay'), title: this.getSetting('bDayTitle', 'Birthday'), value: data.getBDay() }));
		wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextAreaField({ id: this._getElementId('description'), title: this.getSetting('descriptionTitle', 'Description'), value: data.getDescription() }));*/

		return wrapper;
	},
	_prepareButtons: function()
	{
		return BX.OrderPopupWindowHelper.prepareButtons(
			[
				{
					type: 'button',
					settings:
					{
						text: this.getSetting('addButtonName', 'Add'),
						className: 'popup-window-button-accept',
						events:
						{
							click : BX.delegate(this._onSaveButtonClick, this)
						}
					}
				},
				{
					type: 'link',
					settings:
					{
						text: this.getSetting('cancelButtonName', 'Cancel'),
						className: 'popup-window-button-link-cancel',
						events:
						{
							click :
								function()
								{
									this.popupWindow.close();
								}
						}
					}
				}
			]
		);
	},
	_getElementId: function(code)
	{
		return this._dlgCfg['id'] + '_' + code;
	},
	_onSaveButtonClick: function()
	{
		this._data.setLastName(BX(this._getElementId('lastName')).value);
		this._data.setName(BX(this._getElementId('name')).value);
		this._data.setSecondName(BX(this._getElementId('secondName')).value);
		this._data.setEmail(BX(this._getElementId('email')).value);
		this._data.setPhone(BX(this._getElementId('phone')).value);
		/*this._data.setGender(BX(this._getElementId('gender')).value);
		this._data.setBDay(BX(this._getElementId('bDay')).value);
		this._data.setDescription(BX(this._getElementId('description')).value);*/

		if(this._onSaveCallback)
		{
			this._onSaveCallback(this);
		}
	}
};

BX.OrderPhysicalEditDialog.create = function(id, settings, data, onSaveCallback)
{
	var self = new BX.OrderPhysicalEditDialog();
	self.initialize(id, settings, data, onSaveCallback);
	return self;
};
BX.OrderContactEditDialog = function()
{
	this._id = '';
	this._settings = {};
	this._dlg = null;
	this._dlgCfg = {};
	this._data = null;
	this._mode = 'CREATE';
	this._onSaveCallback = null;
};

BX.OrderContactEditDialog.prototype =
{
	initialize: function(id, settings, data, onSaveCallback)
	{
		this._id = BX.type.isNotEmptyString(id) ? id : 'ORDER_CONTACT_EDIT_DIALOG_' + Math.random();
		this._settings = settings ? settings : {};
		this._data = data ? data : BX.OrderContactData.create();
		this._onSaveCallback = BX.type.isFunction(onSaveCallback) ? onSaveCallback : null;
	},
	getSetting: function (name, defaultval)
	{
		return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
	},
	getData: function()
	{
		return this._data;
	},
	setData: function(data)
	{
		this._data = data ? data : BX.OrderContactData.create();
	},
	isOpened: function()
	{
		return this._dlg && this._dlg.isShown();
	},
	open: function(anchor, mode)
	{
		if(!BX.type.isNotEmptyString(mode) || (mode !== 'CREATE' && mode !== 'EDIT'))
		{
			mode = this._mode;
		}

		if(this._dlg && this._mode === mode)
		{
			if(!this._dlg.isShown())
			{
				this._dlg.show();
			}
			return;
		}

		if(this._mode !== mode)
		{
			this._mode = mode;
		}

		var cfg = this._dlgCfg = {};
		cfg['id'] = this._id;
		this._dlg = new BX.PopupWindow(
			cfg['id'],
			anchor,
			{
				autoHide: false,
				draggable: true,
				offsetLeft: 0,
				offsetTop: 0,
				bindOptions: { forceBindPosition: false },
				closeByEsc: true,
				closeIcon: { top: '10px', right: '15px'},
				titleBar:
				{
					content: BX.OrderPopupWindowHelper.prepareTitle(this.getSetting('title', 'New contact'))
				},
				events:
				{
					//onPopupShow: function(){},
					onPopupClose: BX.delegate(this._onPopupClose, this),
					onPopupDestroy: BX.delegate(this._onPopupDestroy, this)
				},
				content: this._prepareContent(),
				buttons: this._prepareButtons()
			}
		);

		this._dlg.show();
	},
	close: function()
	{
		if(this._dlg)
		{
			this._dlg.close();
		}
	},
	showError: function(msg)
	{
		var errorWrap = BX(this._getElementId('errors'));
		if(errorWrap)
		{
			errorWrap.innerHTML = msg;
			errorWrap.style.display = '';
		}
	},
	_onPopupClose: function()
	{
		this._dlg.destroy();
	},
	_onPopupDestroy: function()
	{
		this._dlg = null;
	},
	_prepareContent: function()
	{
		var wrapper = BX.create(
			'DIV',
			{
				attrs: { className: 'bx-order-dialog-quick-create-popup' }
			}
		);

		var data = this._data;
		wrapper.appendChild(
			BX.create(
				'DIV',
				{
					attrs:
					{
						className: 'bx-order-dialog-quick-create-error-wrap',
						style: 'display:none'
					},
					props: { id: this._getElementId('errors') }
				}
			)
		);
		wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('lastName'), title: this.getSetting('lastNameTitle', 'Last Name'), value: data.getLastName() }));
		wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('name'), title: this.getSetting('nameTitle', 'Name'), value: data.getName() }));
		wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('secondName'), title: this.getSetting('secondNameTitle', 'Second Name'), value: data.getSecondName() }));
		if(this.getSetting('enableEmail', true))
		{
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('email'), title: this.getSetting('emailTitle', 'E-mail'), value: data.getEmail() }));
		}
		if(this.getSetting('enablePhone', true))
		{
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('phone'), title: this.getSetting('phoneTitle', 'Phone'), value: data.getPhone() }));
		}
		if(this.getSetting('enableExport', true))
		{
			if(this._mode === 'CREATE')
			{
				data.markAsExportable(true);
			}

			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareCheckBoxField({ id: this._getElementId('export'), title: this.getSetting('exportTitle', 'Enable Export'), value: data.isExportable() }));
		}
		return wrapper;
	},
	_prepareButtons: function()
	{
		return BX.OrderPopupWindowHelper.prepareButtons(
			[
				{
					type: 'button',
					settings:
					{
						text: this.getSetting('addButtonName', 'Add'),
						className: 'popup-window-button-accept',
						events:
						{
							click : BX.delegate(this._onSaveButtonClick, this)
						}
					}
				},
				{
					type: 'link',
					settings:
					{
						text: this.getSetting('cancelButtonName', 'Cancel'),
						className: 'popup-window-button-link-cancel',
						events:
						{
							click :
								function()
								{
									this.popupWindow.close();
								}
						}
					}
				}
			]
		);
	},
	_getElementId: function(code)
	{
		return this._dlgCfg['id'] + '_' + code;
	},
	_onSaveButtonClick: function()
	{
		this._data.setLastName(BX(this._getElementId('lastName')).value);
		this._data.setName(BX(this._getElementId('name')).value);
		this._data.setSecondName(BX(this._getElementId('secondName')).value);
		if(this.getSetting('enableEmail', true))
		{
			this._data.setEmail(BX(this._getElementId('email')).value);
		}
		if(this.getSetting('enablePhone', true))
		{
			this._data.setPhone(BX(this._getElementId('phone')).value);
		}
		if(this.getSetting('enableExport', true))
		{
			this._data.markAsExportable(BX(this._getElementId('export')).checked);
		}
		if(this._onSaveCallback)
		{
			this._onSaveCallback(this);
		}
	}
};

BX.OrderContactEditDialog.create = function(id, settings, data, onSaveCallback)
{
	var self = new BX.OrderContactEditDialog();
	self.initialize(id, settings, data, onSaveCallback);
	return self;
};

BX.OrderAgentEditDialog = function()
{
	this._id = '';
	this._settings = {};
	this._dlg = null;
	this._dlgCfg = {};
	this._data = null;
	this._mode = 'CREATE';
	this._onSaveCallback = null;
};

BX.OrderAgentEditDialog.prototype =
{
	initialize: function(id, settings, data, onSaveCallback)
	{
		this._id = BX.type.isNotEmptyString(id) ? id : 'ORDER_AGENT_EDIT_DIALOG_' + Math.random();
		this._settings = settings ? settings : {};
		this._data = data ? data : BX.OrderUserData.create();
		this._onSaveCallback = BX.type.isFunction(onSaveCallback) ? onSaveCallback : null;
	},
	getSetting: function (name, defaultval)
	{
		return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
	},
	getData: function()
	{
		return this._data;
	},
	setData: function(data)
	{
		this._data = data ? data : BX.OrderUserData.create();
	},
	isOpened: function()
	{
		return this._dlg && this._dlg.isShown();
	},
	open: function(anchor, mode)
	{
		if(!BX.type.isNotEmptyString(mode) || (mode !== 'CREATE' && mode !== 'EDIT'))
		{
			mode = this._mode;
		}

		if(this._dlg && this._mode === mode)
		{
			this._dlg.setContent(this._prepareContent());
			if(!this._dlg.isShown())
			{
				this._dlg.show();
			}
			return;
		}

		if(this._mode !== mode)
		{
			this._mode = mode;
		}

		var cfg = this._dlgCfg = {};
		cfg['id'] = this._id;
		this._dlg = new BX.PopupWindow(
			cfg['id'],
			anchor,
			{
				autoHide: false,
				draggable: true,
				offsetLeft: 0,
				offsetTop: 0,
				bindOptions: { forceBindPosition: false },
				closeByEsc: true,
				closeIcon: { top: '10px', right: '15px'},
				titleBar:
				{
					content: BX.OrderPopupWindowHelper.prepareTitle(this.getSetting('title', 'New contragent'))
				},
				events:
				{
					//onPopupShow: function(){},
					onPopupClose: BX.delegate(this._onPopupClose, this),
					onPopupDestroy: BX.delegate(this._onPopupDestroy, this)
				},
				content: this._prepareContent(),
				buttons: this._prepareButtons()
			}
		);

		this._dlg.show();
	},
	close: function()
	{
		if(this._dlg)
		{
			this._dlg.close();
		}
	},
	showError: function(msg)
	{
		var errorWrap = BX(this._getElementId('errors'));
		if(errorWrap)
		{
			errorWrap.innerHTML = msg;
			errorWrap.style.display = '';
		}
	},
	_onPopupClose: function()
	{
		this._dlg.destroy();
	},
	_onPopupDestroy: function()
	{
		this._dlg = null;
	},
	_prepareContent: function()
	{
		var wrapper = BX.create(
			'DIV',
			{
				attrs: { className: 'bx-order-dialog-quick-create-popup' }
			}
		);

		var data = this._data;
		wrapper.appendChild(
			BX.create(
				'DIV',
				{
					attrs:
					{
						className: 'bx-order-dialog-quick-create-error-wrap',
						style: 'display:none'
					},
					props: { id: this._getElementId('errors') }
				}
			)
		);

		wrapper.appendChild(BX.OrderPopupWindowHelper.prepareHiddenField({ id: this._getElementId('legal'), value: (data.getLegal()=='Y')?'Y':'N'}));
		if(data.getLegal()=='Y') {
			var self=this;
			BX.ajax(
				{
					'url': '/bitrix/components/newportal/order.physical.edit/ajax.php?sessid='+BX.bitrix_sessid(),
					'method': 'POST',
					'dataType': 'json',
					'data':
					{
						'ACTION' : 'GET_POPUP',
						'DATA':
						{
							'fullName':data.getCTitle(),
							'phone':data.getCPhone(),
							'email':data.getCEmail(),
							'formID':this._id
						},
					},
					onsuccess: function(data)
					{

						//console.log(data);
						wrapper.appendChild(
							BX.create(
								'DIV',
								{
									attrs: {className: 'bx-order-dialog-quick-create-field'},
									children: [
										BX.create(
											'SPAN',
											{
												attrs: {className: 'bx-order-dialog-quick-create-field-title'},
												text: self.getSetting('contactTitle', 'Contact') + ':'
											}
										),
										BX.create(
											'DIV',
											{
												attrs: {style: 'display: inline-block; vertical-align: top;'},
												html: data['html']
											}
										)
									]
								}
							),
							BX.findParent(BX(self._id+'_lastName'))
						);
						eval(data['script']);

					},
					onfailure: function(data)
					{
						//console.log(data);
						//self._showDialogError(data['ERROR'] ? data['ERROR'] : self.getMessage('unknownError'));
					}
				}
			);
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('type'), title: this.getSetting('typeTitle', 'Legal'), value: this.getSetting('legal', 'Legal'), disabled:true}));
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('title'), title: this.getSetting('titleTitle', 'Title'), value: data.getTitle() }));
			//wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('fullTitle'), title: this.getSetting('fullTitleTitle', 'FullTitle'), value: data.getFullTitle() }));
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('phone'), title: this.getSetting('phoneTitle', 'Phone'), value: data.getPhone() }));
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('email'), title: this.getSetting('emailTitle', 'Email'), value: data.getEmail() }));

			/*wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('contactLastName'), title: this.getSetting('contactLastNameTitle', 'contactLastName'), value: data.getCLastName() }));
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('contactName'), title: this.getSetting('contactNameTitle', 'contactName'), value: data.getCName() }));
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('contactSecondName'), title: this.getSetting('contactSecondNameTitle', 'contactSecondName'), value: data.getCSecondName() }));
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('contactPhone'), title: this.getSetting('contactPhoneTitle', 'contactPhone'), value: data.getCPhone() }));
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('contactEmail'), title: this.getSetting('contactEmailTitle', 'contactEmail'), value: data.getCEmail() }));*/
		}
		else {
			var self=this;
			BX.ajax(
				{
					'url': '/bitrix/components/newportal/order.physical.edit/ajax.php?sessid='+BX.bitrix_sessid(),
					'method': 'POST',
					'dataType': 'json',
					'data':
					{
						'ACTION' : 'GET_POPUP',
						'DATA':
						{
							'fullName':data.getTitle(),
							'phone':data.getPhone(),
							'email':data.getEmail(),
							'formID':this._id
						},
					},
					onsuccess: function(data)
					{

						//console.log(data);
						//alert(self.getSetting('physicalTitle', 'Physical'));
						wrapper.appendChild(
							BX.create(
								'DIV',
								{
									attrs: {className: 'bx-order-dialog-quick-create-field'},
									children: [
										BX.create(
											'SPAN',
											{
												attrs: {className: 'bx-order-dialog-quick-create-field-title'},
												text: self.getSetting('physicalTitle', 'Physical') + ':'
											}
										),
										BX.create(
											'DIV',
											{
												attrs: {style: 'display: inline-block; vertical-align: top;'},
												html: data['html']
											}
										)
									]
								}
							),
							BX.findParent(BX(self._id+'_lastName'))
						);
						eval(data['script']);

					},
					onfailure: function(data)
					{
						//console.log(data);
						//self._showDialogError(data['ERROR'] ? data['ERROR'] : self.getMessage('unknownError'));
					}
				}
			);

			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('type'), title: this.getSetting('typeTitle', 'Legal'), value: this.getSetting('physical', 'Physical'), disabled:true}));
			/*wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('lastName'), title: this.getSetting('lastNameTitle', 'LastName'), value: data.getLastName() }));
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('name'), title: this.getSetting('nameTitle', 'Name'), value: data.getName() }));
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('secondName'), title: this.getSetting('secondNameTitle', 'SecondName'), value: data.getSecondName() }));
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('phone'), title: this.getSetting('phoneTitle', 'Phone'), value: data.getPhone() }));
			wrapper.appendChild(BX.OrderPopupWindowHelper.prepareTextField({ id: this._getElementId('email'), title: this.getSetting('emailTitle', 'Email'), value: data.getEmail() }));*/
		}
		return wrapper;
	},
	_prepareButtons: function()
	{
		return BX.OrderPopupWindowHelper.prepareButtons(
			[
				{
					type: 'button',
					settings:
					{
						text: this.getSetting('addButtonName', 'Add'),
						className: 'popup-window-button-accept',
						events:
						{
							click : BX.delegate(this._onSaveButtonClick, this)
						}
					}
				},
				{
					type: 'link',
					settings:
					{
						text: this.getSetting('cancelButtonName', 'Cancel'),
						className: 'popup-window-button-link-cancel',
						events:
						{
							click :
								function()
								{
									this.popupWindow.close();
								}
						}
					}
				}
			]
		);
	},
	_getElementId: function(code)
	{
		return this._dlgCfg['id'] + '_' + code;
	},
	_onSaveButtonClick: function()
	{
		var physID=BX(this._id+'_DATA_INPUT_'+this._getElementId('CHANGE_BTN_PHYSICAL_ID')).value;
		if(BX(this._getElementId('legal')).value=='Y') {
			this._data.setTitle(BX(this._getElementId('title')).value);
			//this._data.setFullTitle(BX(this._getElementId('fullTitle')).value);
			this._data.setCId(physID);
			/*this._data.setCLastName(BX(this._getElementId('contactLastName')).value);
			this._data.setCName(BX(this._getElementId('contactName')).value);
			this._data.setCSecondName(BX(this._getElementId('contactSecondName')).value);
			this._data.setCPhone(BX(this._getElementId('contactPhone')).value);
			this._data.setCEmail(BX(this._getElementId('contactEmail')).value);*/
			this._data.setEmail(BX(this._getElementId('email')).value);
			this._data.setPhone(BX(this._getElementId('phone')).value);
			this._data.setLegal('Y');
		}
		else {
			this._data.setPhysId(physID);
			this._data.setLegal('N');
		}


		if(this._onSaveCallback)
		{
			this._onSaveCallback(this);
		}
	}
};

BX.OrderAgentEditDialog.create = function(id, settings, data, onSaveCallback)
{
	var self = new BX.OrderAgentEditDialog();
	self.initialize(id, settings, data, onSaveCallback);
	return self;
};

BX.OrderPhysicalData = function()
{
	this._id='';
	this._name = this._secondName = this._lastName = this._email = this._phone = '';
	//this._gender = this._bDay = this._description = '';
};

BX.OrderPhysicalData.prototype =
{
	initialize: function(settings)
	{
		//console.log(settings);
		if(!settings)
		{
			return;
		}

		if(settings['ID'])
		{
			this.setId(settings['ID']);
		}

		var title=settings['TITLE'].split(' ');

		if(title[1])
		{
			this.setName(title[1]);
		}

		if(title[2])
		{
			this.setSecondName(title[2]);
		}

		if(title[0])
		{
			this.setLastName(title[0]);
		}

		if(settings['EMAIL'])
		{
			this.setEmail(settings['EMAIL']);
		}

		if(settings['PHONE'])
		{
			this.setPhone(settings['PHONE']);
		}

		/*if(settings['GENDER'])
		{
			this.setGender(settings['GENDER']);
		}

		if(settings['BDAY'])
		{
			this.setPhone(settings['BDAY']);
		}

		if(settings['DESCRIPTION'])
		{
			this.setPhone(settings['DESCRIPTION']);
		}*/
	},
	reset: function()
	{
		this._id='';
		this._name = this._secondName = this._lastName = this._email = this._phone = '';
		//this._gender = this._bDay = this._description = '';
	},
	getId: function()
	{
		return this._id;
	},
	setId: function(val)
	{
		this._id = val;
	},
	getName: function()
	{
		return this._name;
	},
	setName: function(val)
	{
		this._name = BX.type.isNotEmptyString(val) ? val : '';
	},
	getSecondName: function()
	{
		return this._secondName;
	},
	setSecondName: function(val)
	{
		this._secondName = BX.type.isNotEmptyString(val) ? val : '';
	},
	getLastName: function()
	{
		return this._lastName;
	},
	setLastName: function(val)
	{
		this._lastName = BX.type.isNotEmptyString(val) ? val : '';
	},
	getEmail: function()
	{
		return this._email;
	},
	setEmail: function(val)
	{
		this._email = BX.type.isNotEmptyString(val) ? val : '';
	},
	getPhone: function()
	{
		return this._phone;
	},
	setPhone: function(val)
	{
		this._phone = BX.type.isNotEmptyString(val) ? val : '';
	},
	/*getGender: function()
	{
		return this._gender;
	},
	setGender: function(val)
	{
		this._gender = BX.type.isNotEmptyString(val) ? val : '';
	},
	getBDay: function()
	{
		return this._bDay;
	},
	setBDay: function(val)
	{
		this._bDay = BX.type.isNotEmptyString(val) ? val : '';
	},
	getDescription: function()
	{
		return this._description;
	},
	setDescription: function(val)
	{
		this._description = BX.type.isNotEmptyString(val) ? val : '';
	},*/
	toJSON: function()
	{
		var result =
			{
				id: this._id,
				name: this._name,
				secondName: this._secondName,
				lastName: this._lastName,
				email: this._email,
				phone: this._phone,
				/*gender: this._gender,
				bDay: this._bDay,
				description: this._description*/
			};
		return result;
	}
};

BX.OrderPhysicalData.create = function(settings)
{
	var self = new BX.OrderPhysicalData();
	self.initialize(settings);
	return self;
};

BX.OrderContactData = function()
{
	this._id = 0;
	this._name = this._secondName = this._lastName = this._email = this._phone = '';
};

BX.OrderContactData.prototype =
{
	initialize: function(settings)
	{
		if(!settings)
		{
			return;
		}

		if(settings['id'])
		{
			this.setId(settings['id']);
		}

		if(settings['name'])
		{
			this.setName(settings['name']);
		}

		if(settings['secondName'])
		{
			this.setSecondName(settings['secondName']);
		}

		if(settings['lastName'])
		{
			this.setLastName(settings['lastName']);
		}

		if(settings['email'])
		{
			this.setEmail(settings['email']);
		}

		if(settings['phone'])
		{
			this.setPhone(settings['phone']);
		}
	},
	reset: function()
	{
		this._id = 0;
		this._name = this._secondName = this._lastName = this._email = this._phone = '';
	},
	getId: function()
	{
		return this._id;
	},
	setId: function(val)
	{
		this._id = val;
	},
	getName: function()
	{
		return this._name;
	},
	setName: function(val)
	{
		this._name = BX.type.isNotEmptyString(val) ? val : '';
	},
	getSecondName: function()
	{
		return this._secondName;
	},
	setSecondName: function(val)
	{
		this._secondName = BX.type.isNotEmptyString(val) ? val : '';
	},
	getLastName: function()
	{
		return this._lastName;
	},
	setLastName: function(val)
	{
		this._lastName = BX.type.isNotEmptyString(val) ? val : '';
	},
	getEmail: function()
	{
		return this._email;
	},
	setEmail: function(val)
	{
		this._email = BX.type.isNotEmptyString(val) ? val : '';
	},
	getPhone: function()
	{
		return this._phone;
	},
	setPhone: function(val)
	{
		this._phone = BX.type.isNotEmptyString(val) ? val : '';
	},
	toJSON: function()
	{
		var result =
			{
				id: this._id,
				name: this._name,
				secondName: this._secondName,
				lastName: this._lastName,
				email: this._email,
				phone: this._phone,
			};
		return result;
	}
};

BX.OrderContactData.create = function(settings)
{
	var self = new BX.OrderContactData();
	self.initialize(settings);
	return self;
};

BX.OrderAgentData = function()
{
	this._id='';
	this._legal='N';
	this._cPhone = this._cEmail = this._email = this._phone = this._title = '';
	this._physId = this._cId = this._cTitle = '';
};

BX.OrderAgentData.prototype =
{
	initialize: function(settings)
	{
		if(!settings)
		{
			return;
		}

		if(settings['ID'])
		{
			this.setId(settings['ID']);
		}

		if(settings['LEGAL'])
		{
			this.setLegal(settings['LEGAL']);
		}

		if(settings['EMAIL'])
		{
			this.setEmail(settings['EMAIL']);
		}

		if(settings['PHONE'])
		{
			this.setPhone(settings['PHONE']);
		}
		if(settings['TITLE'])
		{
			this.setTitle(settings['TITLE']);
		}
		if(this.getLegal()=='Y') {

			if(settings['CONTACT_FULL_NAME'])
			{
				this.setCTitle(settings['CONTACT_FULL_NAME']);
			}

			if(settings['CONTACT_EMAIL'])
			{
				this.setCEmail(settings['CONTACT_EMAIL']);
			}

			if(settings['CONTACT_PHONE'])
			{
				this.setCPhone(settings['CONTACT_PHONE']);
			}
		}
		else {

			if(settings['ID'])
			{
				this.setPhysId(settings['ID']);
			}
		}
	},
	reset: function()
	{
		this._id='';
		this._legal='N';
		this._cPhone = this._cEmail = this._email = this._phone = this._title = '';
		this._physId = this._cId = this._cTitle = '';
	},
	getId: function()
	{
		return this._id;
	},
	setId: function(val)
	{
		this._id = val;
	},
	getPhysId: function()
	{
		return this._physId;
	},
	setPhysId: function(val)
	{
		this._physId = val;
	},
	getCId: function()
	{
		return this._cId;
	},
	setCId: function(val)
	{
		this._cId = val;
	},
	getLegal: function()
	{
		return this._legal;
	},
	setLegal: function(val)
	{
		this._legal = val=='Y' ? 'Y': 'N';
	},
	getTitle: function()
	{
		return this._title;
	},
	setTitle: function(val)
	{
		this._title = BX.type.isNotEmptyString(val) ? val : '';
	},
	getEmail: function()
	{
		return this._email;
	},
	setEmail: function(val)
	{
		this._email = BX.type.isNotEmptyString(val) ? val : '';
	},
	getPhone: function()
	{
		return this._phone;
	},
	setPhone: function(val)
	{
		this._phone = BX.type.isNotEmptyString(val) ? val : '';
	},
	getCTitle: function()
	{
		return this._cTitle;
	},
	setCTitle: function(val)
	{
		this._cTitle = BX.type.isNotEmptyString(val) ? val : '';
	},
	getCEmail: function()
	{
		return this._cEmail;
	},
	setCEmail: function(val)
	{
		this._cEmail = BX.type.isNotEmptyString(val) ? val : '';
	},
	getCPhone: function()
	{
		return this._cPhone;
	},
	setCPhone: function(val)
	{
		this._cPhone = BX.type.isNotEmptyString(val) ? val : '';
	},
	toJSON: function()
	{
		var result =
			{
				id: this._id,
				physId: this._physId,
				cId: this._cId,
				legal: this._legal,
				title: this._title,
				email: this._email,
				phone: this._phone,
				cTitle: this._cTitle,
				cEmail: this._cEmail,
				cPhone: this._cPhone
			};
		return result;
	}
};

BX.OrderAgentData.create = function(settings)
{
	var self = new BX.OrderAgentData();
	self.initialize(settings);
	return self;
};

BX.OrderUserData = function()
{
	this._id = 0;
	this._name = this._secondName = this._lastName = this._email = this._phone = '';
};

BX.OrderUserData.prototype =
{
	initialize: function(settings)
	{
		if(!settings)
		{
			return;
		}

		if(settings['id'])
		{
			this.setId(settings['id']);
		}

		if(settings['name'])
		{
			this.setName(settings['name']);
		}

		if(settings['secondName'])
		{
			this.setSecondName(settings['secondName']);
		}

		if(settings['lastName'])
		{
			this.setLastName(settings['lastName']);
		}

		if(settings['email'])
		{
			this.setEmail(settings['email']);
		}

		if(settings['phone'])
		{
			this.setPhone(settings['phone']);
		}
	},
	reset: function()
	{
		this._id = 0;
		this._name = this._secondName = this._lastName = this._email = this._phone = '';
	},
	getId: function()
	{
		return this._id;
	},
	setId: function(val)
	{
		this._id = val;
	},
	getName: function()
	{
		return this._name;
	},
	setName: function(val)
	{
		this._name = BX.type.isNotEmptyString(val) ? val : '';
	},
	getSecondName: function()
	{
		return this._secondName;
	},
	setSecondName: function(val)
	{
		this._secondName = BX.type.isNotEmptyString(val) ? val : '';
	},
	getLastName: function()
	{
		return this._lastName;
	},
	setLastName: function(val)
	{
		this._lastName = BX.type.isNotEmptyString(val) ? val : '';
	},
	getEmail: function()
	{
		return this._email;
	},
	setEmail: function(val)
	{
		this._email = BX.type.isNotEmptyString(val) ? val : '';
	},
	getPhone: function()
	{
		return this._phone;
	},
	setPhone: function(val)
	{
		this._phone = BX.type.isNotEmptyString(val) ? val : '';
	},
	toJSON: function()
	{
		var result =
		{
			id: this._id,
			name: this._name,
			secondName: this._secondName,
			lastName: this._lastName,
			email: this._email,
			phone: this._phone
		};
		return result;
	}
};

BX.OrderUserData.create = function(settings)
{
	var self = new BX.OrderUserData();
	self.initialize(settings);
	return self;
};

BX.OrderGroupData = function()
{
	this._id = this._nomen = 0;
	this._title = '';
};

BX.OrderGroupData.prototype =
{
	initialize: function(settings)
	{
		if(!settings)
		{
			return;
		}

		if(settings['id'])
		{
			this.setId(settings['id']);
		}

		if(settings['nomen'])
		{
			this.setNomen(settings['nomen']);
		}

		if(settings['title'])
		{
			this.setTitle(settings['title']);
		}
	},
	reset: function()
	{
		this._id = this._nomen = this._enrolled = this._free = this._max = 0;
		this._title = '';
	},
	getId: function()
	{
		return this._id;
	},
	setId: function(val)
	{
		this._id = val;
	},
	getNomen: function()
	{
		return this._id;
	},
	setNomen: function(val)
	{
		this._id = val;
	},
	setTitle: function(val)
	{
		this._title = BX.type.isNotEmptyString(val) ? val : '';
	},

	toJSON: function()
	{
		return {
			id: this._id,
			nomen: this._nomen,
			title: this._title
		};
	}
};

BX.OrderGroupData.create = function(settings)
{
	var self = new BX.OrderGroupData();
	self.initialize(settings);
	return self;
};

BX.OrderFormedGroupData = function()
{
	this._nomen = this._enrolled = this._free = this._max = this._dateStart = this._dateEnd = 0;
	this._id = thsi._groupId = this._title = '';
};

BX.OrderFormedGroupData.prototype =
{
	initialize: function(settings)
	{
		if(!settings)
		{
			return;
		}

		if(settings['id'])
		{
			this.setId(settings['id']);
		}

		if(settings['groupId'])
		{
			this.setId(settings['groupId']);
		}

		if(settings['nomen'])
		{
			this.setNomen(settings['nomen']);
		}

		if(settings['enrolled'])
		{
			this.setEnrolled(settings['enrolled']);
		}

		if(settings['free'])
		{
			this.setFree(settings['free']);
		}

		if(settings['max'])
		{
			this.setMax(settings['max']);
		}

		if(settings['date_start'])
		{
			this.setFree(settings['date_start']);
		}

		if(settings['date_end'])
		{
			this.setMax(settings['date_end']);
		}

		if(settings['title'])
		{
			this.setTitle(settings['title']);
		}
	},
	reset: function()
	{
		this._nomen = this._enrolled = this._free = this._max = this._dateStart = this._dateEnd = 0;
		this._id = thsi._groupId = this._title = '';
	},
	getId: function()
	{
		return this._id;
	},
	setId: function(val)
	{
		this._id = val;
	},
	getGroupId: function()
	{
		return this._groupId;
	},
	setGroupId: function(val)
	{
		this._groupId = val;
	},
	getEnrolled: function()
	{
		return this._enrolled;
	},
	setEnrolled: function(val)
	{
		this._enrolled = val;
	},
	getFree: function()
	{
		return this._free;
	},
	setFree: function(val)
	{
		this._free = val;
	},
	getMax: function()
	{
		return this._max;
	},
	setMax: function(val)
	{
		this._max = val;
	},
	getDateStart: function()
	{
		return this._dateStart;
	},
	setDateStart: function(val)
	{
		this._dateStart = val;
	},
	getDateEnd: function()
	{
		return this._dateEnd;
	},
	setDateEnd: function(val)
	{
		this._dateEnd = val;
	},
	setTitle: function(val)
	{
		this._title = BX.type.isNotEmptyString(val) ? val : '';
	},

	toJSON: function()
	{
		return {
			id: this._id,
			groupId: this._groupId,
			enrolled: this._enrolled,
			free: this._free,
			max: this._max,
			dateStart: this._dateStart,
			dateEnd: this._dateEnd,
			title: this._title
		};
	}
};

BX.OrderFormedGroupData.create = function(settings)
{
	var self = new BX.OrderFormedGroupData();
	self.initialize(settings);
	return self;
};

BX.OrderNomenData = function()
{
	this._id = this._nomen = 0;
	this._title = '';
};

BX.OrderNomenData.prototype =
{
	initialize: function(settings)
	{
		if(!settings)
		{
			return;
		}

		if(settings['id'])
		{
			this.setId(settings['id']);
		}

		if(settings['title'])
		{
			this.setTitle(settings['title']);
		}

		if(settings['nomen'])
		{
			this.setNomen(settings['nomen']);
		}
	},
	reset: function()
	{
		this._id = this._dealPrice = 0;
		this._title = this._dealType = '';
	},
	getId: function()
	{
		return this._id;
	},
	setId: function(val)
	{
		this._id = val;
	},
	getTitle: function()
	{
		return this._title;
	},
	setTitle: function(val)
	{
		this._title = BX.type.isNotEmptyString(val) ? val : '';
	},
	getNomen: function()
	{
		return this._nomen;
	},
	setNomen: function(val)
	{
		this._nomen = val;
	}
};

BX.OrderNomenData.create = function(settings)
{
	var self = new BX.OrderNomenData();
	self.initialize(settings);
	return self;
};

BX.OrderDirectionData = function()
{
	this._id = 0;
	this._title = '';
};

BX.OrderDirectionData.prototype =
{
	initialize: function(settings)
	{
		if(!settings)
		{
			return;
		}

		if(settings['id'])
		{
			this.setId(settings['id']);
		}

		if(settings['title'])
		{
			this.setTitle(settings['title']);
		}
	},
	reset: function()
	{
		this._id = 0;
		this._title = '';
	},
	getId: function()
	{
		return this._id;
	},
	setId: function(val)
	{
		this._id = val;
	},
	getTitle: function()
	{
		return this._title;
	},
	setTitle: function(val)
	{
		this._title = BX.type.isNotEmptyString(val) ? val : '';
	}
};

BX.OrderDirectionData.create = function(settings)
{
	var self = new BX.OrderDirectionData();
	self.initialize(settings);
	return self;
};

BX.OrderEntityInfo = function()
{
	this._settings = {};
};

BX.OrderEntityInfo.prototype =
{
	initialize: function(settings)
	{
		this._settings = settings ? settings : {};
	},
	getSetting: function(name, defaultval)
	{
		return this._settings[name] ? this._settings[name] : defaultval;
	}
};

BX.OrderEntityInfo.create = function(settings)
{
	var self = new BX.OrderEntityInfo();
	self.initialize(settings);
	return self;
};

BX.OrderPopupWindowHelper = {};
BX.OrderPopupWindowHelper.prepareButtons = function(data)
{
	var result = [];
	for(var i = 0; i < data.length; i++)
	{
		var datum = data[i];
		result.push(
			datum['type'] === 'link'
				? new BX.PopupWindowButtonLink(datum['settings'])
				: new BX.PopupWindowButton(datum['settings']));
	}

	return result;
};

BX.OrderPopupWindowHelper.prepareTextField = function(settings)
{
	return BX.create(
		'DIV',
		{
			attrs: { className: 'bx-order-dialog-quick-create-field' },
			children:
				[
					BX.create(
						'SPAN',
						{
							attrs: { className: 'bx-order-dialog-quick-create-field-title' },
							text: settings['title'] + ':'
						}
					),
					BX.create(
						'INPUT',
						{
							attrs: { className: 'bx-order-dialog-quick-create-field-text-input' },
							props: { id: settings['id'], value: settings['value'], disabled: (!!settings['disabled'])?'disabled':'' }
						}
					)
				]
		}
	);
};

BX.OrderPopupWindowHelper.prepareHiddenField = function(settings)
{
	return BX.create(
		'DIV',
		{
			attrs: { className: 'bx-order-dialog-quick-create-field' },
			children:
				[
					BX.create(
						'INPUT',
						{
							props: { id: settings['id'], value: settings['value'], type: 'hidden' }
						}
					)
				]
		}
	);
};

BX.OrderPopupWindowHelper.prepareSelectField = function(settings)
{
	var select = BX.create(
		'SELECT',
		{
			attrs: { className: 'bx-order-dialog-quick-create-field-select' },
			props: { id: settings['id'] }
		}
	);

	var value = settings['value'] ? settings['value'] : '';

	if(settings['items'])
	{
		for(var i = 0; i < settings['items'].length; i++)
		{
			var item = settings['items'][i];
			var v = item['value'] ? item['value'] : i.toString();

			var option = BX.create(
				'OPTION',
				{
					text: item['text'] ? item['text'] : v,
					props: { value : v }
				}
			);

			if(!BX.browser.isIE)
			{
				select.add(option, null);
			}
			else
			{
				try
				{
					// for IE earlier than version 8
					select.add(option, select.options[null]);
				}
				catch (e)
				{
					select.add(option, null);
				}
			}

			if(v === value)
			{
				option.selected = true;
			}
		}
	}

	return BX.create(
		'DIV',
		{
			attrs: { className: 'bx-order-dialog-quick-create-field' },
			children:
				[
					BX.create(
						'SPAN',
						{
							attrs: { className: 'bx-order-dialog-quick-create-field-title' },
							text: settings['title'] + ':'
						}
					),
					select
				]
		}
	);
};

BX.OrderPopupWindowHelper.prepareTextAreaField = function(settings)
{
	return BX.create(
		'DIV',
		{
			attrs: { className: 'bx-order-dialog-quick-create-field' },
			children:
				[
					BX.create(
						'SPAN',
						{
							attrs: { className: 'bx-order-dialog-quick-create-field-title' },
							text: settings['title'] + ':'
						}
					),
					BX.create(
						'TEXTAREA',
						{
							attrs: { className: 'bx-order-dialog-quick-create-field-text-input' },
							props: { id: settings['id'] },
							text: settings['value']
						}
					)
				]
		}
	);
};

BX.OrderPopupWindowHelper.prepareDateField = function(settings) {
	var input = BX.create(
		'INPUT',
		{
			attrs: {className: 'order-offer-item-inp order-item-table-date'},
			props: {id: settings['id'], type: 'text', value: settings['value'], name: settings['id']}
		}
	);
	BX.OrderDateLinkField.create(BX(input), null, {showTime: false, setFocusOnShow: false});
	return BX.create(
		'DIV',
		{
			attrs: {className: 'bx-order-dialog-quick-create-field'},
			children: [
				BX.create(
					'SPAN',
					{
						attrs: {className: 'bx-order-dialog-quick-create-field-title'},
						text: settings['title'] + ':'
					}
				),
				input
			]
		}
	);
};

BX.OrderPopupWindowHelper.prepareCheckBoxField = function(settings)
{
	var checkbox = BX.create(
		'INPUT',
		{
			attrs: { className: 'bx-order-dialog-quick-create-field-checkbox' },
			props: { id: settings['id'], type: 'checkbox', checked: (!!settings['value']) ? 'checked' : '',
				disabled: (!!settings['disabled']) ? 'disabled' : ''
			}
		}
	);

	if(!!settings['value'])
	{
		checkbox.checked = true;
	}

	if(!!settings['disabled'])
	{
		checkbox.disabled = true;
	}

	return BX.create(
		'DIV',
		{
			attrs: { className: 'bx-order-dialog-quick-create-field' },
			children:
				[
					BX.create(
						'LABEL',
						{
							attrs: { className: 'bx-order-dialog-quick-create-field-checkbox-label' },
							children:
							[
								checkbox,
								BX.create(
									'SPAN',
									{
										attrs: { className: 'bx-order-dialog-quick-create-field-checkbox-label-text' },
										text: settings['title']
									}
								)
							]
						}
					)
				]
		}
	);
};

BX.OrderPopupWindowHelper.prepareTitle = function(text)
{
	return BX.create(
		'DIV',
		{
			attrs: { className: 'bx-order-dialog-tittle-wrap' },
			children:
				[
					BX.create(
						'SPAN',
						{
							text: text,
							props: { className: 'bx-order-dialog-title-text' }
						}
					)
				]
		}
	);
};

BX.OrderEntityDetailViewDialog = function()
{
	this._id = '';
	this._dlg = null;
	this._settings = {};
};

BX.OrderEntityDetailViewDialog.prototype =
{
	initialize: function(id, settings)
	{
		this._id = BX.type.isNotEmptyString(id) ? id : 'ORDER_ENTITY_DETAIL_VIEW_DIALOG_' + Math.random();
		this._settings = settings ? settings : {};
	},
	getId: function()
	{
		return this._id;
	},
	getSetting: function (name, defaultval)
	{
		return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
	},
	isOpened: function()
	{
		return this._dlg && this._dlg.isShown();
	},
	open: function()
	{
		if(this._dlg)
		{
			if(!this._dlg.isShown())
			{
				this._dlg.show();
			}
			return;
		}

		var container = BX(this.getSetting('containerId'));
		if(!container)
		{
			container = BX.findChild(BX('sidebar'), { 'class': 'order-entity-info-details-container' }, true, false);
		}

		this._dlg = new BX.PopupWindow(
			this._id,
			null,
			{
				autoHide: false,
				draggable: true,
				offsetLeft: 0,
				offsetTop: 0,
				bindOptions: { forceBindPosition: false },
				closeByEsc: true,
				closeIcon: { top: '10px', right: '15px'},
				titleBar:
				{
					content: BX.OrderPopupWindowHelper.prepareTitle(this.getSetting('title', 'Details'))
				},
				events:
				{
					onAfterPopupShow:  BX.delegate(this._onAfterPopupShow, this),
					onPopupDestroy: BX.delegate(this._onPopupDestroy, this)
				},
				content: container
			}
		);

		this._dlg.show();
	},
	close: function()
	{
		if(this._dlg && this._dlg.isShown())
		{
			this._dlg.close();
		}
	},
	toggle: function()
	{
		this.isOpened() ? this.close() : this.open();
	},
	_onAfterPopupShow: function()
	{
		var sidebarContainer = BX.findChild(BX('sidebar'), { 'class': 'sidebar-block' }, true, false);
		if(!sidebarContainer)
		{
			return;
		}
		var sidebarPos = BX.pos(sidebarContainer);

		var dialogContainer = this._dlg.popupContainer;
		if(!dialogContainer)
		{
			return;
		}
		var dialogPos = BX.pos(dialogContainer);

		dialogContainer.style.top = sidebarPos.top.toString() + 'px';
		dialogContainer.style.left = (sidebarPos.left - dialogPos.width - 1).toString() + 'px';
	},
	_onPopupDestroy: function()
	{
		this._dlg = null;
	}
};

BX.OrderEntityDetailViewDialog.items = {};
BX.OrderEntityDetailViewDialog.create = function(id, settings)
{
	var self = new BX.OrderEntityDetailViewDialog();
	self.initialize(id, settings);
	this.items[self.getId()] = self;
	return self;
};

BX.OrderEntityDetailViewDialog.ensureCreated = function(id, settings)
{
	return typeof(this.items[id]) !== 'undefined' ? this.items[id] : this.create(id, settings);
};

BX.OrderContactEditor = function()
{
	this._id = '';
	this._settings = {};
	this._dlg = null;
	this._clientField = null;
	this._mode = 'CREATE';
};

BX.OrderContactEditor.prototype =
{
	initialize: function(id, settings)
	{
		this._id = id;
		this._settings = settings ? settings : {};

		var initData = this.getSetting('data', null);
		if(initData)
		{
			this._mode = 'EDIT';
		}
		else
		{
			initData = {};
			this._mode = 'CREATE';
		}
		this._data = BX.OrderContactData.create(initData);
	},
	getSetting: function (name, defaultval)
	{
		return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
	},
	openDialog: function(anchor)
	{
		if(this._dlg)
		{
			this._dlg.setData(this._data);
			this._dlg.open(anchor);
			return;
		}

		this._dlg = BX.OrderContactEditDialog.create(
			this._id,
			this.getSetting('dialog', {}),
			this._data,
			BX.delegate(this._onSaveDialogData, this));

		if(this._dlg)
		{
			this._dlg.open(anchor, this._mode);
		}
	},
	closeDialog: function()
	{
		if(this._dlg)
		{
			this._dlg.close();
		}
	},
	openExternalFieldEditor: function(field)
	{
		this._clientField = field;
		this.openDialog();
	},
	_onSaveDialogData: function(dialog)
	{
		this._data = this._dlg.getData();

		var url = this.getSetting('serviceUrl', '');
		var action = this.getSetting('actionName', '');

		if(!(BX.type.isNotEmptyString(url) && BX.type.isNotEmptyString(action)))
		{
			return;
		}

		var self = this;
		BX.ajax(
			{
				'url': url,
				'method': 'POST',
				'dataType': 'json',
				'data':
				{
					'ACTION' : action,
					'DATA': this._data.toJSON(),
					'NAME_TEMPLATE': this.getSetting('nameTemplate', '')
				},
				onsuccess: function(data)
				{

					if(data['ERROR'])
					{
						self._showDialogError(data['ERROR']);
					}
					else if(!data['DATA'])
					{
						self._showDialogError('BX.OrderContactEditor: Could not find contact data!');
					}
					else
					{
						self._data = BX.OrderContactData.create(data['DATA']);
						var info = data['INFO'] ? data['INFO'] : {};
						self._clientField.setFieldValue(
							BX.type.isNotEmptyString(info['title'])
								? BX.util.htmlspecialchars(info['title']) : ''
						);
						self.closeDialog();
					}
				},
				onfailure: function(data)
				{
					self._showDialogError(data['ERROR'] ? data['ERROR'] : self.getMessage('unknownError'));
				}
			}
		);
	},
	_showDialogError: function(msg)
	{
		if(this._dlg)
		{
			this._dlg.showError(msg);
		}
	}
};

BX.OrderContactEditor.create = function(id, settings)
{
	var self = new BX.OrderContactEditor();
	self.initialize(id, settings);
	return self;
};

BX.OrderSonetSubscription = function()
{
	this._id = '';
	this._settings = {};
};

BX.OrderSonetSubscription.prototype =
{
	initialize: function(id, settings)
	{
		this._id = id;
		this._settings = settings ? settings : {};
	},
	getSetting: function (name, defaultval)
	{
		return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
	},
	enableSubscription: function(entityId, enable, callback)
	{
		var url = this.getSetting("serviceUrl", "");
		var action = this.getSetting("actionName", "");

		if(!(BX.type.isNotEmptyString(url) && BX.type.isNotEmptyString(action)))
		{
			return;
		}

		var reload = this.getSetting("reload", false);
		//var self = this;
		BX.ajax(
			{
				"url": url,
				"method": "POST",
				"dataType": "json",
				"data":
				{
					"ACTION" : action,
					"ENTITY_TYPE": this.getSetting("entityType", ""),
					"ENTITY_ID": entityId,
					"ENABLE": enable ? "Y" : "N"
				},
				onsuccess: function(data)
				{
					if(BX.type.isFunction(callback))
					{
						callback();
					}
				},
				onfailure: function(data) {}
			}
		);
	},
	subscribe: function(entityId, callback)
	{
		this.enableSubscription(entityId, true, callback);
	},
	unsubscribe: function(entityId, callback)
	{
		this.enableSubscription(entityId, false, callback);
	}
};

BX.OrderSonetSubscription.items = {};
BX.OrderSonetSubscription.create = function(id, settings)
{
	var self = new BX.OrderSonetSubscription();
	self.initialize(id, settings);
	this.items[id] = self;
	return self;
};

if(typeof(BX.OrderFormTabLazyLoader) == "undefined")
{
	BX.OrderFormTabLazyLoader = function()
	{
		this._id = "";
		this._settings = {};
		this._container = null;
		this._wrapper = null;
		this._serviceUrl = "";
		this._formId = "";
		this._tabId = "";
		this._params = {};
		this._formManager = null;

		this._isRequestRunning = false;
		this._isLoaded = false;

		this._waiter = null;
		this._scrollHandler = BX.delegate(this._onWindowScroll, this);
		this._formManagerHandler = BX.delegate(this._onFormManagerCreate, this);
	};

	BX.OrderFormTabLazyLoader.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : "order_lf_disp_" + Math.random().toString().substring(2);
			this._settings = settings ? settings : {};

			this._container = BX(this.getSetting("containerID", ""));
			if(!this._container)
			{
				throw "Error: Could not find container.";
			}

			this._wrapper = BX.findParent(this._container, { "tagName": "DIV", "className": "bx-edit-tab-inner" });

			this._serviceUrl = this.getSetting("serviceUrl", "");
			if(!BX.type.isNotEmptyString(this._serviceUrl))
			{
				throw "Error. Could not find service url.";
			}

			this._formId = this.getSetting("formID", "");
			if(!BX.type.isNotEmptyString(this._formId))
			{
				throw "Error: Could not find form id.";
			}

			this._tabId = this.getSetting("tabID", "");
			if(!BX.type.isNotEmptyString(this._tabId))
			{
				throw "Error: Could not find tab id.";
			}

			this._params = this.getSetting("params", {});

			var formManager = window["bxForm_" + this._formId];
			if(formManager)
			{
				this.setFormManager(formManager);
			}
			else
			{
				BX.addCustomEvent(window, "OrderInterfaceFormCreated", this._formManagerHandler);
			}
		},
		getId: function()
		{
			return this._id;
		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		setSetting: function (name, val)
		{
			this._settings[name] = val;
		},
		load: function()
		{
			if(this._isLoaded)
			{
				return;
			}

			var params = this._params;
			params["FORM_ID"] = this._formId;
			params["TAB_ID"] = this._tabId;

			this._startRequest(params);
		},
		getContainerRect: function()
		{
			var r = this._container.getBoundingClientRect();
			return(
				{
					top: r.top, bottom: r.bottom, left: r.left, right: r.right,
					width: typeof(r.width) !== "undefined" ? r.width : (r.right - r.left),
					height: typeof(r.height) !== "undefined" ? r.height : (r.bottom - r.top)
				}
			);
		},
		isContanerInClientRect: function()
		{
			return this.getContainerRect().top <= document.documentElement.clientHeight;
		},
		setFormManager: function(formManager)
		{
			if(this._formManager === formManager)
			{
				return;
			}

			this._formManager = formManager;
			if(!this._formManager)
			{
				return;
			}

			if(this._formManager.GetActiveTabId() !== this._tabId)
			{
				BX.addCustomEvent(window, 'BX_ORDER_INTERFACE_FORM_TAB_SELECTED', BX.delegate(this._onFormTabSelect, this));
			}
			else
			{
				if(this.isContanerInClientRect())
				{
					this.load();
				}
				else
				{
					BX.bind(window, "scroll", this._scrollHandler);
				}
			}
		},
		_startRequest: function(params)
		{
			if(this._isRequestRunning)
			{
				return false;
			}

			this._isRequestRunning = true;
			this._waiter = BX.showWait(this._container);
			BX.ajax(
				{
					url: this._serviceUrl,
					method: "POST",
					dataType: "html",
					data:
					{
						"LOADER_ID": this._id,
						"PARAMS": params
					},
					onsuccess: BX.delegate(this._onRequestSuccess, this),
					onfailure: BX.delegate(this._onRequestFailure, this)
				}
			);

			return true;
		},
		_onRequestSuccess: function(data)
		{
			this._isRequestRunning = false;

			if(this._waiter)
			{
				BX.closeWait(this._container, this._waiter);
				this._waiter = null;
			}

			this._container.innerHTML = data;
			this._isLoaded = true;
		},
		_onRequestFailure: function(data)
		{
			this._isRequestRunning = false;

			if(this._waiter)
			{
				BX.closeWait(this._container, this._waiter);
				this._waiter = null;
			}
			this._isLoaded = true;
		},
		_onFormManagerCreate: function(formManager)
		{
			if(formManager["name"] === this._formId)
			{
				BX.removeCustomEvent(window, "OrderInterfaceFormCreated", this._formManagerHandler);
				this.setFormManager(formManager);
			}
		},
		_onFormTabSelect: function(sender, formId, tabId, tabContainer)
		{
			if(this._formId === formId && (tabId === this._tabId || this._wrapper === tabContainer))
			{
				this.load();
			}
		},
		_onWindowScroll: function(e)
		{
			if(!this._isLoaded && !this._isRequestRunning && this.isContanerInClientRect())
			{
				BX.unbind(window, "scroll", this._scrollHandler);
				this.load();
			}
		}
	};

	BX.OrderFormTabLazyLoader.items = {};
	BX.OrderFormTabLazyLoader.create = function(id, settings)
	{
		var self = new BX.OrderFormTabLazyLoader();
		self.initialize(id, settings);
		this.items[self.getId()] = self;
		return self;
	};
}

if(typeof(BX.OrderCustomDragItem) === "undefined")
{
	BX.OrderCustomDragItem = function()
	{
		this._id = "";
		this._settings = {};
		this._node = null;
		this._ghostNode = null;
		this._ghostOffset = { x: 0, y: 0 };

		this._enableDrag = true;
		this._isInDragMode = false;
		this._dragNotifier = null;
		this._bodyOverflow = "";
	};
	BX.OrderCustomDragItem.prototype =
	{
		initialize: function(id, settings)
		{
			if(typeof(jsDD) === "undefined")
			{
				throw "OrderCustomDragItem: Could not find jsDD API.";
			}

			this._id = BX.type.isNotEmptyString(id) ? id : BX.util.getRandomString(8);
			this._settings = settings ? settings : {};

			this._node = this.getSetting("node");
			if(!this._node)
			{
				throw "OrderCustomDragItem: The 'node' parameter is not defined in settings or empty.";
			}

			this._enableDrag = this.getSetting("enableDrag", true);
			this._ghostOffset = this.getSetting("ghostOffset", { x: 0, y: 0 });

			this._dragNotifier = BX.OrderNotifier.create(this);

			this.doInitialize();
			this.bindEvents();
		},
		doInitialize: function()
		{
		},
		release: function()
		{
			this.doRelease();
			this.unbindEvents();
		},
		doRelease: function()
		{
		},
		getId: function()
		{
			return this._id;
		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		bindEvents: function()
		{
			this._node.onbxdragstart = BX.delegate(this._onDragStart, this);
			this._node.onbxdrag = BX.delegate(this._onDrag, this);
			this._node.onbxdragstop = BX.delegate(this._onDragStop, this);
			this._node.onbxdragrelease = BX.delegate(this._onDragRelease, this);

			jsDD.registerObject(this._node);

			this.doBindEvents();
		},
		doBindEvents: function()
		{
		},
		unbindEvents: function()
		{
			delete this._node.onbxdragstart;
			delete this._node.onbxdrag;
			delete this._node.onbxdragstop;
			delete this._node.onbxdragrelease;

			if(BX.type.isFunction(jsDD.unregisterObject))
			{
				jsDD.unregisterObject(this._node);
			}

			this.doUnbindEvents();
		},
		doUnbindEvents: function()
		{
		},
		createGhostNode: function()
		{
			throw "OrderCustomDragItem: The 'createGhostNode' function is not implemented.";
		},
		getGhostNode: function()
		{
			return this._ghostNode;
		},
		removeGhostNode: function()
		{
			throw "OrderCustomDragItem: The 'removeGhostNode' function is not implemented.";
		},
		processDragStart: function()
		{
		},
		processDrag: function(x, y)
		{
		},
		processDragStop: function()
		{
		},
		addDragListener: function(listener)
		{
			this._dragNotifier.addListener(listener);
		},
		removeDragListener: function(listener)
		{
			this._dragNotifier.removeListener(listener);
		},
		getContextId: function()
		{
			return "";
		},
		getContextData: function()
		{
			return {};
		},
		getScrollTop: function()
		{
			var html = document.documentElement;
			var body = document.body;

			var scrollTop = html.scrollTop || body && body.scrollTop || 0;
			scrollTop -= html.clientTop;

			return scrollTop;
		},
		_onDragStart: function()
		{
			if(!this._enableDrag)
			{
				return;
			}

			this.createGhostNode();

			var pos = BX.pos(this._node);
			this._ghostNode.style.top = pos.top + "px";
			this._ghostNode.style.left = pos.left + "px";

			this._isInDragMode = true;
			BX.OrderCustomDragItem.currentDragged = this;

			BX.onCustomEvent('OrderDragItemDragStart', [this]);
			this.processDragStart();

			window.setTimeout(BX.delegate(this._prepareDocument, this), 0);
		},
		_onDrag: function(x, y)
		{
			if(!this._isInDragMode)
			{
				return;
			}

			if(this._ghostNode)
			{
				this._ghostNode.style.top = (y + this._ghostOffset.y) + "px";
				this._ghostNode.style.left = (x + this._ghostOffset.x) + "px";
			}

			var scrollTop = this.getScrollTop();
			if(scrollTop > 0 && y <= scrollTop)
			{
				window.scrollTo(0, 0);
				return;
			}

			this.processDrag(x, y);
			this._dragNotifier.notify([x, y]);
		},
		_onDragStop: function(x, y)
		{
			if(!this._isInDragMode)
			{
				return;
			}

			this.removeGhostNode();
			this._isInDragMode = false;
			if(BX.OrderCustomDragItem.currentDragged === this)
			{
				BX.OrderCustomDragItem.currentDragged = null;
			}

			BX.onCustomEvent('OrderDragItemDragStop', [this]);
			this.processDragStop();

			window.setTimeout(BX.delegate(this._resetDocument, this), 0);
		},
		_onDragRelease: function(x, y)
		{
			BX.onCustomEvent('OrderDragItemDragRelease', [this]);
		},
		_prepareDocument: function()
		{
			this._bodyOverflow = document.body.style.overflow;
			document.body.style.overflow = "hidden";
		},
		_resetDocument: function()
		{
			document.body.style.overflow = this._bodyOverflow;
		}
	};
	BX.OrderCustomDragItem.currentDragged = null;
	BX.OrderCustomDragItem.emulateDrag = function()
	{
		jsDD.refreshDestArea();
		if(jsDD.current_node)
		{
			//Emilating drag event on previous drag position
			jsDD.drag({ clientX: (jsDD.x - jsDD.wndSize.scrollLeft), clientY: (jsDD.y - jsDD.wndSize.scrollTop) });
		}
	};
}
if(typeof(BX.OrderCustomDragContainer) === "undefined")
{
	BX.OrderCustomDragContainer = function()
	{
		this._id = "";
		this._settings = {};
		this._node = null;
		this._itemDragHandler = BX.delegate(this._onItemDrag, this);
		this._draggedItem = null;
		this._dragFinishNotifier = null;
		this._enabled = true;
	};
	BX.OrderCustomDragContainer.prototype =
	{
		initialize: function(id, settings)
		{
			if(typeof(jsDD) === "undefined")
			{
				throw "OrderCustomDragContainer: Could not find jsDD API.";
			}

			this._id = BX.type.isNotEmptyString(id) ? id : BX.util.getRandomString(8);
			this._settings = settings ? settings : {};

			this._node = this.getSetting("node");
			if(!this._node)
			{
				throw "OrderCustomDragContainer: The 'node' parameter is not defined in settings or empty.";
			}

			this._dragFinishNotifier = BX.OrderNotifier.create(this);
			this.doInitialize();
			this.bindEvents();
		},
		doInitialize: function()
		{
		},
		release: function()
		{
			this.doRelease();
			this.unbindEvents();
		},
		doRelease: function()
		{
		},
		getId: function()
		{
			return this._id;
		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		bindEvents: function()
		{
			this._node.onbxdestdraghover = BX.delegate(this._onDragOver, this);
			this._node.onbxdestdraghout = BX.delegate(this._onDragOut, this);
			this._node.onbxdestdragfinish = BX.delegate(this._onDragFinish, this);
			this._node.onbxdragstop = BX.delegate(this._onDragStop, this);
			this._node.onbxdragrelease = BX.delegate(this._onDragRelease, this);

			jsDD.registerDest(this._node, this.getPriority());

			this.doBindEvents();
		},
		doBindEvents: function()
		{
		},
		unbindEvents: function()
		{
			delete this._node.onbxdestdraghover;
			delete this._node.onbxdestdraghout;
			delete this._node.onbxdestdragfinish;
			delete this._node.onbxdragstop;
			delete this._node.onbxdragrelease;

			if(BX.type.isFunction(jsDD.unregisterDest))
			{
				jsDD.unregisterDest(this._node);
			}

			this.doUnbindEvents();
		},
		doUnbindEvents: function()
		{
		},
		createPlaceHolder: function(pos)
		{
			throw "OrderCustomDragContainer: The 'createPlaceHolder' function is not implemented.";
		},
		removePlaceHolder: function()
		{
			throw "OrderCustomDragContainer: The 'removePlaceHolder' function is not implemented.";
		},
		initializePlaceHolder: function(pos)
		{
			this.createPlaceHolder(pos);
			this.refresh();
		},
		releasePlaceHolder: function()
		{
			this.removePlaceHolder();
			this.refresh();
		},
		getPriority: function()
		{
			return 100;
		},
		addDragFinishListener: function(listener)
		{
			this._dragFinishNotifier.addListener(listener);
		},
		removeDragFinishListener: function(listener)
		{
			this._dragFinishNotifier.removeListener(listener);
		},
		getDraggedItem: function()
		{
			return this._draggedItem;
		},
		setDraggedItem: function(draggedItem)
		{
			if(this._draggedItem === draggedItem)
			{
				return;
			}

			if(this._draggedItem)
			{
				this._draggedItem.removeDragListener(this._itemDragHandler);
			}

			this._draggedItem = draggedItem;

			if(this._draggedItem)
			{
				this._draggedItem.addDragListener(this._itemDragHandler);
			}
		},
		isAllowedContext: function(contextId)
		{
			return true;
		},
		isEnabled: function()
		{
			return this._enabled;
		},
		enable: function(enable)
		{
			enable = !!enable;
			if(this._enabled === enable)
			{
				return;
			}

			this._enabled = enable;
			if(enable)
			{
				jsDD.enableDest(this._node);
			}
			else
			{
				jsDD.disableDest(this._node);
			}
		},
		refresh: function()
		{
			jsDD.refreshDestArea(this._node.__bxddeid);
		},
		processDragOver: function(pos)
		{
			this.initializePlaceHolder(pos);
		},
		processDragOut: function()
		{
			this.releasePlaceHolder();
		},
		processDragStop: function()
		{
			this.releasePlaceHolder();
		},
		processDragRelease: function()
		{
			this.releasePlaceHolder();
		},
		processItemDrop: function()
		{
			this.releasePlaceHolder();
		},
		_onDragOver: function(node, x, y)
		{
			var draggedItem = BX.OrderCustomDragItem.currentDragged;
			if(!draggedItem)
			{
				return;
			}

			if(!this.isAllowedContext(draggedItem.getContextId()))
			{
				return;
			}

			this.setDraggedItem(draggedItem);
			this.processDragOver({ x: x, y: y });
		},
		_onDragOut: function(node, x, y)
		{
			if(!this._draggedItem)
			{
				return;
			}

			this.processDragOut();
			this.setDraggedItem(null);
		},
		_onDragFinish: function(node, x, y)
		{
			if(!this._draggedItem)
			{
				return;
			}

			this._dragFinishNotifier.notify([this._draggedItem, x, y]);

			this.processItemDrop();
			this.setDraggedItem(null);

			BX.OrderCustomDragContainer.refresh();
		},
		_onDragRelease: function(node, x, y)
		{
			if(!this._draggedItem)
			{
				return;
			}

			this.processDragRelease();
			this.setDraggedItem(null);
		},
		_onDragStop: function(node, x, y)
		{
			if(!this._draggedItem)
			{
				return;
			}

			this.processDragStop();
			this.setDraggedItem(null);
		},
		_onItemDrag: function(item, x, y)
		{
			if(!this._draggedItem)
			{
				return;
			}

			this.initializePlaceHolder({ x: x, y: y });
		}
	};
	BX.OrderCustomDragContainer.refresh = function()
	{
		jsDD.refreshDestArea();
	};
}

BX.OrderDragDropBinState = { suspend: 0, wait: 1, ready: 2, open: 3, close: 4 };

if(typeof(BX.OrderDragDropBin) === "undefined")
{
	BX.OrderDragDropBin = function()
	{
		this._state = BX.OrderDragDropBinState.suspend;
		this._chargeItem = null;

		this._enableChargeItem = false;
		this._chargeDragStartHandler = BX.delegate(this._onChargeDragStart, this);
		this._chargeDragStopHandler = BX.delegate(this._onChargeDragStop, this);
		this._chargeDragReleaseHandler = BX.delegate(this._onChargeDragRelease, this);
		this._chargeDragHandler = BX.delegate(this._onChargeDrag, this);

		this._workareaRect = null;

		this._promptingWrapper = null;
		this._closePromptingButtonId = "order_dd_bin_close_prompting_btn";
		this._closePromptingHandler = BX.delegate(this._onClosePromptingButtonClick, this);

		this._demoButtonId = "order_dd_bin_demo_btn";
		this._demoHandler = BX.delegate(this._onDemoButtonClick, this);

	};
	BX.extend(BX.OrderDragDropBin, BX.OrderCustomDragContainer);
	BX.OrderDragDropBin.prototype.doInitialize = function()
	{
		BX.addCustomEvent(window, "OrderDragItemDragStart", this._chargeDragStartHandler);
		BX.addCustomEvent(window, "OrderDragItemDragStop", this._chargeDragStopHandler);
		BX.addCustomEvent(window, "OrderDragItemDragRelease", this._chargeDragReleaseHandler);

		this.cacheWorkareaRect();
		BX.bind(window, "resize", BX.delegate(this._onWindowResize, this));
	};
	BX.OrderDragDropBin.prototype.getPriority = function()
	{
		return 10;
	};
	BX.OrderDragDropBin.prototype.createPlaceHolder = function(pos)
	{
	};
	BX.OrderDragDropBin.prototype.removePlaceHolder = function()
	{
	};
	BX.OrderDragDropBin.prototype.processDragOver = function(pos)
	{
		if(this._chargeItem)
		{
			this._enableChargeItem = false;
		}
		this.setState(BX.OrderDragDropBinState.open);
	};
	BX.OrderDragDropBin.prototype.processDragOut = function()
	{
		if(this._chargeItem)
		{
			this._enableChargeItem = true;
		}
		this.setState(BX.OrderDragDropBinState.ready);
	};
	BX.OrderDragDropBin.prototype.processDragStop = function()
	{
		this.setState(BX.OrderDragDropBinState.suspend);
	};
	BX.OrderDragDropBin.prototype.processDragRelease = function()
	{
		this.setState(BX.OrderDragDropBinState.suspend);
	};
	BX.OrderDragDropBin.prototype.processItemDrop = function()
	{
		if(this._chargeItem)
		{
			this._chargeItem.removeDragListener(this._chargeDragHandler);
			this._chargeItem = null;
		}
		this._enableChargeItem = false;

		this.setState(BX.OrderDragDropBinState.close);
		window.setTimeout(BX.delegate(this.reset, this), 1000);
		BX.onCustomEvent(this, "OrderDragDropBinItemDrop", [ this, this.getDraggedItem() ]);
	};
	BX.OrderDragDropBin.prototype.getState = function()
	{
		return this._state;
	};
	BX.OrderDragDropBin.prototype.reset = function()
	{
		this.setState(BX.OrderDragDropBinState.suspend);
	};
	BX.OrderDragDropBin.prototype.setState = function(state)
	{
		state = parseInt(state);
		if(state < BX.OrderDragDropBinState.suspend || state > BX.OrderDragDropBinState.close)
		{
			state = BX.OrderDragDropBinState.suspend;
		}

		if(this._state === state)
		{
			return;
		}

		this._state = state;

		var classNames = ["order-cart-block-wrap"];
		if(this._state >= BX.OrderDragDropBinState.wait)
		{
			classNames.push("order-cart-start");
		}
		if(this._state >= BX.OrderDragDropBinState.ready)
		{
			classNames.push("order-cart-active");
		}
		if(this._state >= BX.OrderDragDropBinState.open)
		{
			classNames.push("order-cart-hover");
		}
		if(this._state === BX.OrderDragDropBinState.close)
		{
			classNames.push("order-cart-finish");
		}

		this._node.className = classNames.join(" ");

		window.setTimeout(BX.delegate(BX.OrderCustomDragItem.emulateDrag, this), 400);
		window.setTimeout(BX.delegate(BX.OrderCustomDragItem.emulateDrag, this), 800);
	};
	BX.OrderDragDropBin.prototype._onChargeDragStart = function(item)
	{
		this._enableChargeItem = true;
		this._chargeItem = item;
		this._chargeItem.addDragListener(this._chargeDragHandler);

		this.setState(BX.OrderDragDropBinState.wait);
	};
	BX.OrderDragDropBin.prototype._onChargeDragStop = function(item)
	{
		if(!this._enableChargeItem || this._chargeItem !== item)
		{
			return;
		}

		this._chargeItem.removeDragListener(this._chargeDragHandler);
		this._chargeItem = null;
		this._enableChargeItem = false;

		this.setState(BX.OrderDragDropBinState.suspend);
	};
	BX.OrderDragDropBin.prototype._onChargeDragRelease = function(item)
	{
		if(!this._enableChargeItem || this._chargeItem !== item)
		{
			return;
		}

		this._chargeItem.removeDragListener(this._chargeDragHandler);
		this._chargeItem = null;
		this._enableChargeItem = false;

		this.setState(BX.OrderDragDropBinState.suspend);
	};
	BX.OrderDragDropBin.prototype._onChargeDrag = function(item, x, y)
	{
		if(this._enableChargeItem && this._chargeItem === item)
		{
			this.adjust();
		}
	};
	BX.OrderDragDropBin.prototype._onWindowResize = function(e)
	{
		this.cacheWorkareaRect();
	};
	BX.OrderDragDropBin.prototype.cacheWorkareaRect = function()
	{
		var workarea = BX("workarea");
		if(!workarea)
		{
			workarea = document.documentElement;
		}
		this._workareaRect = BX.pos(workarea);
		this._readyThreshold = this._workareaRect.width / 6;
	};
	BX.OrderDragDropBin.prototype.adjust = function()
	{
		if(!this._chargeItem)
		{
			return;
		}

		var ghostNode = this._chargeItem.getGhostNode();
		if(!ghostNode)
		{
			return;
		}

		var ghostRect = BX.pos(ghostNode);
		var isReady = this._state >= BX.OrderDragDropBinState.ready;
		if(isReady !== ((this._workareaRect.right - ghostRect.left) <= this._readyThreshold))
		{
			isReady = !isReady;
			this.setState(isReady ? BX.OrderDragDropBinState.ready : BX.OrderDragDropBinState.wait);
		}
	};
	BX.OrderDragDropBin.prototype.getMessage = function(name, defaultval)
	{
		var m = BX.OrderDragDropBin.messages;
		return m.hasOwnProperty(name) ? m[name] : defaultval;
	};
	BX.OrderDragDropBin.prototype.showPromptingIfRequired = function(container)
	{
		if(BX.localStorage.get("order_dd_bin_show_prompt") !== "N")
		{
			this.showPrompting(container);
		}
	};
	BX.OrderDragDropBin.prototype.showPrompting = function(container)
	{
		if(this._promptingWrapper)
		{
			return;
		}

		var msg = this.getMessage("prompting");
		msg = msg.replace("#CLOSE_BTN_ID#", this._closePromptingButtonId).replace("#DEMO_BTN_ID#", this._demoButtonId);
		this._promptingWrapper = BX.create("DIV", { attrs: { className: "order-view-message" }, html: msg });
		container.appendChild(this._promptingWrapper);

		BX.bind(BX(this._closePromptingButtonId), "click", this._closePromptingHandler);
		BX.bind(BX(this._demoButtonId), "click", this._demoHandler);
	};
	BX.OrderDragDropBin.prototype.hidePrompting = function()
	{
		if(!this._promptingWrapper)
		{
			return;
		}

		BX.localStorage.set("order_dd_bin_show_prompt", "N", 31104000);
		BX.unbind(BX(this._closePromptingButtonId), "click", this._closePromptingHandler);
		BX.unbind(BX(this._demoButtonId), "click", this._demoHandler);
		BX.remove(this._promptingWrapper);
	};
	BX.OrderDragDropBin.prototype.demo = function()
	{
		this.setState(BX.OrderDragDropBinState.wait);

		var self = this;
		window.setTimeout(function(){ self.setState(BX.OrderDragDropBinState.ready); }, 1000);
		window.setTimeout(function(){ self.setState(BX.OrderDragDropBinState.open); }, 1500);
		window.setTimeout(function(){ self.setState(BX.OrderDragDropBinState.close); }, 2000);
	};
	BX.OrderDragDropBin.prototype._onDemoButtonClick = function(e)
	{
		this.demo();
		return BX.PreventDefault(e);
	};
	BX.OrderDragDropBin.prototype._onClosePromptingButtonClick = function(e)
	{
		this.hidePrompting();
		return BX.PreventDefault(e);
	};
	BX.OrderDragDropBin.instance = null;
	BX.OrderDragDropBin.getInstance = function()
	{
		if(this.instance)
		{
			return this.instance;
		}

		var node = BX.create("DIV",
			{
				attrs: { className: "order-cart-block-wrap" },
				children:
				[
					BX.create("DIV",
						{
							attrs: { className: "order-cart-block" },
							children:
							[
								BX.create("DIV",
									{
										attrs: { className: "order-cart-icon" },
										children:
										[
											BX.create("DIV", { attrs: { className: "order-cart-icon-top" } }),
											BX.create("DIV", { attrs: { className: "order-cart-icon-body" } })
										]
									}
								)
							]
						}
					)
				]
			}
		);
		document.body.appendChild(node);
		var self = new BX.OrderDragDropBin();
		self.initialize("default", { node: node });
		return (this.instance = self);
	};

	if(typeof(BX.OrderDragDropBin.messages) === "undefined")
	{
		BX.OrderDragDropBin.messages = {};
	}
}

if(typeof(BX.OrderLocalitySearchField) === "undefined")
{
	BX.OrderLocalitySearchField = function()
	{
		this._id = "";
		this._settings = {};
		this._localityType = "";
		this._serviceUrl = "";
		this._searchInput = null;
		this._dataInput = null;
		this._timeoutId = 0;
		this._value = "";
		this._items = [];
		this._menuId = "order-locality-search";
		this._menu = null;
		this._isRequestStarted = false;

		this._checkHandler = BX.delegate(this.check, this);
		this._keyPressHandler = BX.delegate(this.onKeyPress, this);
		this._menuItemClickHandler = BX.delegate(this.onMenuItemClick, this);
		this._searchCompletionHandler =  BX.delegate(this.onSearchRequestComplete, this);
	};

	BX.OrderLocalitySearchField.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : ('order_loc_search_field_' + Math.random());
			this._settings = settings ? settings : {};

			this._localityType = this.getSetting("localityType");
			if(!BX.type.isNotEmptyString(this._localityType))
			{
				throw  "BX.OrderLocalitySearchField: localityType is not found!";
			}

			this._serviceUrl = this.getSetting("serviceUrl");
			if(!BX.type.isNotEmptyString(this._serviceUrl))
			{
				throw  "BX.OrderLocalitySearchField: serviceUrl is not found!";
			}

			this._searchInput = BX(this.getSetting("searchInputId"));
			if(!BX.type.isElementNode(this._searchInput))
			{
				throw  "BX.OrderLocalitySearchField: searchInput is not found!";
			}

			this._dataInput = BX(this.getSetting("dataInputId"));
			if(!BX.type.isElementNode(this._dataInput))
			{
				throw  "BX.OrderLocalitySearchField: dataInputId is not found!";
			}

			BX.bind(this._searchInput, "keyup", BX.proxy(this._keyPressHandler, this));
			BX.bind(document, "click", BX.delegate(this._handleExternalClick, this));
		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		check: function()
		{
			this._timeoutId = 0;
			if(this._value !== this._searchInput.value)
			{
				this._value = this._searchInput.value;
				this._timeoutId = window.setTimeout(this._checkHandler, 750);
			}
			else if(this._value.length >= 2)
			{
				this.startSearchRequest(this._value);
			}
		},
		startSearchRequest: function(needle)
		{
			if(this._isRequestStarted)
			{
				return false;
			}

			this._isRequestStarted = true;

			BX.ajax(
				{
					url: this._serviceUrl,
					method: "POST",
					dataType: "json",
					data:
					{
						"ACTION" : "FIND_LOCALITIES",
						"LOCALITY_TYPE": this._localityType,
						"NEEDLE": needle
					},
					onsuccess: this._searchCompletionHandler,
					onfailure: this._searchCompletionHandler
				}
			);
		},
		showMenu: function(items)
		{
			BX.PopupMenu.destroy(this._menuId);

			var menuItems = [];
			for(var i = 0; i < items.length; i++)
			{
				menuItems.push(this.prepareMenuItem(items[i]));
			}

			this._menu = BX.PopupMenu.create(this._menuId, this._searchInput, menuItems, { offsetTop:0, offsetLeft:0 });
			this._menu.popupWindow.show();
		},
		closeMenu: function()
		{
			BX.PopupMenu.destroy(this._menuId);
			this._menu = null;
		},
		prepareMenuItem: function(data)
		{
			var code = BX.type.isNotEmptyString(data["CODE"]) ? data["CODE"] : "";
			if(code === "")
			{
				throw  "BX.OrderLocalitySearchField: could not find item code!";
			}
			var caption = BX.type.isNotEmptyString(data["CAPTION"]) ? data["CAPTION"] : code;
			return { value: code,  text: caption, onclick: this._menuItemClickHandler };
		},
		onMenuItemClick: function(e, item)
		{
			this.selectItem(item);
			this.closeMenu();
		},
		selectItem: function(item)
		{
			this._dataInput.value = item["value"];
			this._searchInput.value = item["text"];
		},
		onKeyPress: function(e)
		{
			if(this._timeoutId !== 0)
			{
				window.clearTimeout(this._timeoutId);
				this._timeoutId = 0;
			}
			this._timeoutId = window.setTimeout(this._checkHandler, 375);
		},
		onSearchRequestComplete: function(result)
		{
			this._isRequestStarted = false;

			var items = typeof(result["DATA"]) !== "undefined" && typeof(result["DATA"]["ITEMS"]) !== "undefined"
				? result["DATA"]["ITEMS"] : [];

			if(items.length > 0)
			{
				this.showMenu(items);
			}
		}
	};

	BX.OrderLocalitySearchField.create = function(id, settings)
	{
		var self = new BX.OrderLocalitySearchField();
		self.initialize(id, settings);
		return self;
	};
}