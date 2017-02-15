//BX.OrderQuickPanelView
if(typeof(BX.OrderQuickPanelView) === "undefined")
{
	BX.OrderQuickPanelView = function()
	{
		this._id = "";
		this._settings = null;

		this._entityTypeName = "";
		this._entityId = 0;

		this._formId = "";
		this._prefix = "";

		this._formSettingsManager = null;
		this._placeholder = null;
		this._wrapper = null;
		this._innerWrapper = null;
		this._leftContainer = null;
		this._centerContainer = null;
		this._rightContainer = null;
		this._bottomContainer = null;

		this._instantEditor = null;

		this._lastChangedSection = null;
		this._isRequestRunning = false;
		this._requestCompleteCallback = null;
		this._scrollHandler = BX.delegate(this._onWindowScroll, this);
		this._resizeHandler = BX.delegate(this._onWindowResize, this);

		this._enableUserConfig = false;
		this._isExpanded = true;
		this._isFixed = false;
		this._isFixedLayout = false;
		this._isMenuShown = false;

		this._config = {};
		this._headerConfig = {};
		this._entityData = null;
		this._sections = {};
		this._headerItems = {};
		this._models = {};

		this._menuButton = null;
		this._pinButton = null;
		this._toggleButton = null;

		this._wait = null;
		this._waitAnchor = null;

		this._menu = null;
		this._menuId = "";
		this._progressLegend = null;

		this._enableInstantEdit = false;
		this._enableDragOverHandling = true;
		this._isIE = false;

		this._unloadHandlers = [];
		this._editorCreatedHandler = BX.delegate(this._onEditorCreated, this);
		this._itemDropHandler = BX.delegate(this._onItemDrop, this);

		BX.addCustomEvent(
			window,
			"OrderControlPanelLayoutChange",
			BX.delegate(this._onControlPanelLayoutChange, this)
		);
	};
	BX.OrderQuickPanelView.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : "";
			this._settings = settings ? settings : {};

			this._entityTypeName = this.getSetting("entityTypeName", "");
			this._entityId = parseInt(this.getSetting("entityId", 0));

			this._prefix = this.getSetting("prefix", "");

			this._placeholder = BX(this.resolveElementId("placeholder"));
			if(!this._placeholder)
			{
				throw "OrderQuickPanelView: Could no find placeholder.";
			}

			this._wrapper = BX(this.resolveElementId("wrap"));
			if(!this._wrapper)
			{
				throw "OrderQuickPanelView: Could no find wrapper.";
			}

			this._innerWrapper = BX(this.resolveElementId("inner_wrap"));
			if(!this._innerWrapper)
			{
				throw "OrderQuickPanelView: Could no find inner wrapper.";
			}

			this._entityData = this.getSetting("entityData");
			if(!this._entityData)
			{
				throw "OrderQuickPanelView: The entity data are not found.";
			}

			this._config = this.getSetting("config", {});
			if(!BX.type.isNotEmptyString(this._config["enabled"]))
			{
				this._config["enabled"] = "N";
			}

			this._enableInstantEdit = !!this.getSetting("enableInstantEdit", false);

			this._enableUserConfig = this._config["enabled"] === "Y";
			this._isExpanded = this._config["expanded"] === "Y";
			this._isFixed = this._config["fixed"] === "Y";
			if(this._isFixed)
			{
				this.adjust();
				BX.bind(window, "scroll", this._scrollHandler);
				BX.bind(window, "resize", this._resizeHandler);
			}

			this._menuButton = BX(this.resolveElementId("menu_btn"));
			if(this._menuButton)
			{
				BX.bind(this._menuButton, "click", BX.delegate(this._onMenuButtonClick, this));
			}
			this._menuId = this._id.toLowerCase() + "_main_menu";

			this._pinButton = BX(this.resolveElementId("pin_btn"));
			if(this._pinButton)
			{
				BX.bind(this._pinButton, "click", BX.delegate(this._onPinButtonClick, this));
			}

			this._toggleButton = BX(this.resolveElementId("toggle_btn"));
			if(this._toggleButton)
			{
				BX.bind(this._toggleButton, "click", BX.delegate(this._onToggleButtonClick, this));
			}

			var leftContainerId = this.getSetting("leftContainerId", "");
			if(!BX.type.isNotEmptyString(leftContainerId))
			{
				leftContainerId = this._prefix + "_left_container";
			}

			this._leftContainer = BX(leftContainerId);
			if(!this._leftContainer)
			{
				throw "OrderQuickPanelView: The left container is not found.";
			}

			this.prepareSection("left", this._leftContainer);

			var centerContainerId = this.getSetting("centerContainerId", "");
			if(!BX.type.isNotEmptyString(centerContainerId))
			{
				centerContainerId = this._prefix + "_center_container";
			}

			this._centerContainer = BX(centerContainerId);
			if(!this._centerContainer)
			{
				throw "OrderQuickPanelView: The center container is not found.";
			}

			this.prepareSection("center", this._centerContainer);

			var rightContainerId = this.getSetting("rightContainerId", "");
			if(!BX.type.isNotEmptyString(rightContainerId))
			{
				rightContainerId = this._prefix + "_right_container";
			}

			this._rightContainer = BX(rightContainerId);
			if(!this._rightContainer)
			{
				throw "OrderQuickPanelView: The right container is not found.";
			}

			this.prepareSection("right", this._rightContainer);

			var bottomContainerId = this.getSetting("bottomContainerId", "");
			if(!BX.type.isNotEmptyString(bottomContainerId))
			{
				bottomContainerId = this._prefix + "_bottom_container";
			}

			this._bottomContainer = BX(bottomContainerId);
			if(!this._bottomContainer)
			{
				throw "OrderQuickPanelView: The bottom container is not found.";
			}

			this.prepareSection("bottom", this._bottomContainer);

			this._formId = this.getSetting("formId", "");
			if(BX.type.isNotEmptyString(this._formId))
			{
				this._formSettingsManager = typeof(BX.OrderFormSettingManager) !== "undefined"
					? BX.OrderFormSettingManager.items[this._formId] : null;
				if(!this._formSettingsManager)
				{
					BX.addCustomEvent(
						window,
						"OrderFormSettingManagerCreate",
						BX.delegate(this._onFormSettingManagerCreate, this)
					);
				}
			}

			var progressLegendId = this.getSetting("progressLegendId", "");
			if(!BX.type.isNotEmptyString(progressLegendId))
			{
				progressLegendId = this._prefix + "_progress_legend";
			}

			this._progressLegend = BX(progressLegendId);
			if(this._progressLegend)
			{
				BX.addCustomEvent(
					window,
					"OrderProgressControlAfterSaveSucces",
					BX.delegate(this._onProgressControlChanged, this)
				);
			}

			this._headerConfig = this.getSetting("headerConfig", {});
			this.prepareHeader();

			this._isIE = BX.browser.IsIE();

			BX.OrderQuickPanelMultiField.setWrapper(this._innerWrapper);
			BX.OrderQuickPanelAddress.setWrapper(this._innerWrapper);

			var instantEditor = BX.OrderInstantEditor.getDefault();
			if(instantEditor)
			{
				this.setInstantEditor(instantEditor);
			}
			else
			{
				BX.addCustomEvent(window, "OrderInstantEditorCreated", this._editorCreatedHandler);
			}

			var bin = BX.OrderDragDropBin.getInstance();
			BX.addCustomEvent(bin, "OrderDragDropBinItemDrop", BX.delegate(this._onDragDropBinItemDrop, this));

			window.onbeforeunload = BX.delegate(this._onUnload, this);
		},
		isAllowedDragContext: function(contextId)
		{
			return (this._formSettingsManager
				&& this._formSettingsManager.getDraggableFieldContextId() === contextId);
		},
		prepareHeader: function()
		{
			for(var k in this._headerConfig)
			{
				if(!this._headerConfig.hasOwnProperty(k))
				{
					continue;
				}
				var config = this._headerConfig[k];
				var type = BX.type.isNotEmptyString(config["type"]) ? config["type"] : "";
				var fieldId = BX.type.isNotEmptyString(config["fieldId"]) ? config["fieldId"] : "";
				var container = BX(this._prefix + "_" + k.toLowerCase());

				this._headerItems[k] = BX.OrderQuickPanelHeaderItem.create(k, { model: this.getFieldModel(fieldId), container: container });
			}
		},
		prepareSection: function(id, container)
		{
			var section = BX.OrderQuickPanelSection.create(
				id,
				{
					view: this,
					prefix: this._id,
					container: container,
					config: BX.type.isNotEmptyString(this._config[id]) ? this._config[id].split(",") : []
				}
			);
			this._sections[id] = section;
			section.setDragDropContainerId(this._id + "_" + id);
			if(section.getItemCount() === 0)
			{
				section.createPlaceHolder(-1);
			}
			return section;
		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		getId: function()
		{
			return this._id;
		},
		getEntityTypeName: function()
		{
			return this._entityTypeName;
		},
		getEnityId: function()
		{
			return this._entityId;
		},
		getPrefix: function()
		{
			return this._prefix;
		},
		getInstantEditor: function()
		{
			return this._instantEditor;
		},
		setInstantEditor: function(instantEditor)
		{
			this._instantEditor = instantEditor;

			for(var k in this._sections)
			{
				if(!this._sections.hasOwnProperty(k))
				{
					continue;
				}

				var items =  this._sections[k].getItems();
				for(var i = 0; i < items.length; i++)
				{
					items[i].setInstantEditor(instantEditor);
				}
			}

			for(var n in this._models)
			{
				if(this._models.hasOwnProperty(n))
				{
					this._models[n].setInstantEditor(instantEditor);
				}
			}

			BX.addCustomEvent("OrderInstantEditorSetFieldReadOnly", BX.delegate(this._onSetReadOnlyField, this));
			var fieldNames = instantEditor.getReadOnlyFieldNames();
			for(var j = 0; j < fieldNames.length; j++)
			{
				this.setItemLocked(fieldNames[j], true);
			}

			BX.addCustomEvent(this._instantEditor, "OrderInstantEditorFieldValueSaved", BX.delegate(this._onEditorFieldValueSave, this));
		},
		getMessage: function(name)
		{
			var m = BX.OrderQuickPanelView.messages;
			return m.hasOwnProperty(name) ? m[name] : name;
		},
		resolveElementId: function(id)
		{
			return this._prefix !== "" ? (this._prefix + "_" + id) : id;
		},
		isExpanded: function()
		{
			return this._isExpanded;
		},
		setExpanded: function(expanded)
		{
			expanded = !!expanded;
			if(this._isExpanded === expanded)
			{
				return;
			}

			this._isExpanded = expanded;

			BX.onCustomEvent(
				window,
				"OrderQuickPanelViewExpanded",
				[this, this._isExpanded]
			);

			if(this._isExpanded)
			{
				BX.removeClass(this._toggleButton, "order-lead-header-contact-btn-close");
				BX.addClass(this._toggleButton, "order-lead-header-contact-btn-open");
			}
			else
			{
				BX.removeClass(this._toggleButton, "order-lead-header-contact-btn-open");
				BX.addClass(this._toggleButton, "order-lead-header-contact-btn-close");
			}
			this.saveConfig(false);
		},
		isFixed: function()
		{
			return this._isFixed;
		},
		setFixed: function(fixed)
		{
			fixed = !!fixed;
			if(this._isFixed === fixed)
			{
				return;
			}

			if(fixed)
			{
				BX.unbind(window, "scroll", this._scrollHandler);
				BX.bind(window, "scroll", this._scrollHandler);

				BX.unbind(window, "resize", this._resizeHandler);
				BX.bind(window, "resize", this._resizeHandler);

				BX.removeClass(this._pinButton, "order-lead-header-contact-btn-unpin");
				BX.addClass(this._pinButton, "order-lead-header-contact-btn-pin");
			}
			else
			{
				BX.unbind(window, "scroll", this._scrollHandler);
				BX.unbind(window, "resize", this._resizeHandler);

				BX.removeClass(this._wrapper, "order-lead-header-table-wrap-fixed");
				BX.removeClass(this._pinButton, "order-lead-header-contact-btn-pin");
				BX.addClass(this._pinButton, "order-lead-header-contact-btn-unpin");

				this._placeholder.style.height = this._placeholder.style.width = "";
				this._wrapper.style.height = this._wrapper.style.width = this._wrapper.style.left = this._wrapper.style.top = "";
			}

			this._isFixed = fixed;
			this._isFixedLayout = false;

			this.saveConfig(false);
		},
		isInstantEditEnabled: function()
		{
			return this._enableInstantEdit;
		},
		getConfig: function()
		{
			var config =
			{
				enabled: this._enableUserConfig ? "Y" : "N",
				expanded: this._isExpanded ? "Y" : "N",
				fixed: this._isFixed ? "Y" : "N"
			};
			for(var k in this._sections)
			{
				if(!this._sections.hasOwnProperty(k))
				{
					continue;
				}

				var items = this._sections[k].getItems();
				var ids = [];
				for(var i = 0; i < items.length; i++)
				{
					ids.push(items[i].getId());
				}
				
				config[k] = ids.length > 0 ? ids.join(",") : "";
			}
			return config;
		},
		saveConfig: function(forAllUsers, callback)
		{
			forAllUsers = !!forAllUsers && this.getSetting("canSaveSettingsForAll", false);

			var config = this.getConfig();
			for(var k in this._sections)
			{
				if(!(this._sections.hasOwnProperty(k) && config.hasOwnProperty(k)))
				{
					continue;
				}

				BX.userOptions.save('order.entity.quickpanelview', this._id, k, config[k], forAllUsers);
			}

			BX.userOptions.save('order.entity.quickpanelview', this._id, 'enabled', config.enabled, forAllUsers);
			BX.userOptions.save('order.entity.quickpanelview', this._id, 'expanded', config.expanded, forAllUsers);
			BX.userOptions.save('order.entity.quickpanelview', this._id, 'fixed', config.fixed, forAllUsers);

			this._waitAnchor = this._lastChangedSection;
			this._waiter = BX.showWait(this._lastChangedSection);

			if(BX.type.isFunction(callback))
			{
				this._requestCompleteCallback = callback;
			}

			BX.userOptions.send(BX.delegate(this._onConfigRequestComplete, this));
		},
		resetConfig: function(forAllUsers, callback)
		{
			forAllUsers = !!forAllUsers && this.getSetting("canSaveSettingsForAll", false);

			this._waitAnchor = this._lastChangedSection;
			this._waiter = BX.showWait(this._lastChangedSection);

			if(BX.type.isFunction(callback))
			{
				this._requestCompleteCallback = callback;
			}

			if(!this._isExpanded)
			{
				BX.onCustomEvent(
					window,
					"OrderQuickPanelViewExpanded",
					[this, true]
				);
				this._isExpanded = true;
			}

			BX.userOptions.del(
				'order.entity.quickpanelview',
				this._id,
				forAllUsers,
				BX.delegate(this._onConfigRequestComplete, this)
			);
		},
		reload: function()
		{
			window.location = window.location.href;
		},
		getFieldData: function(fieldId)
		{
			return this._entityData.hasOwnProperty(fieldId) ? this._entityData[fieldId] : null;
		},
		getFieldModel: function(fieldId)
		{
			var model = BX.OrderQuickPanelModel.getItem(fieldId);
			if(model)
			{
				return model;
			}

			var entityData = this._entityData.hasOwnProperty(fieldId) ? this._entityData[fieldId] : null;
			if(!entityData)
			{
				return null;
			}

			var type = BX.type.isNotEmptyString(entityData["type"]) ? entityData["type"] : "";
			if(type === "boolean")
			{
				model = BX.OrderQuickPanelBooleanModel.create(fieldId, { config: entityData });
			}
			else if(type === "enumeration")
			{
				model = BX.OrderQuickPanelEnumerationModel.create(fieldId, { config: entityData });
			}
			else if(type === "money")
			{
				model = BX.OrderQuickPanelMoneyModel.create(fieldId, { config: entityData });
			}
			else if(type === "html")
			{
				model = BX.OrderQuickPanelHtmlModel.create(fieldId, { config: entityData });
			}
			else if(type === "text" || type === "datetime")
			{
				model = BX.OrderQuickPanelTextModel.create(fieldId, { config: entityData });
			}
			else
			{
				model = BX.OrderQuickPanelModel.create(fieldId, { config: entityData });
			}

			if(this._instantEditor)
			{
				model.setInstantEditor(this._instantEditor);
			}
			return (this._models[fieldId] = model);
		},
		enableDragOverHandling: function(enable)
		{
			this._enableDragOverHandling = typeof(enable) !== "undefined" ? !!enable : true;
		},
		pauseDragOverHandling: function(timeout)
		{
			this._enableDragOverHandling = false;
			setTimeout(BX.delegate(this.enableDragOverHandling, this), timeout);
		},
		isDragOverHandlingEnabled: function()
		{
			this._enableDragOverHandling = true;
		},
		processSectionItemDeletion: function(section, item)
		{
			this._lastChangedSection = section;
			this._enableUserConfig = true;

			if(section.getItemCount() === 0)
			{
				section.createPlaceHolder(-1);
			}

			this.saveConfig(false);
		},
		processDraggedItemDrop: function(dragContainer, draggedItem)
		{
			var targetSection = dragContainer.getSection();
			var context = draggedItem.getContextData();
			var contextId = BX.type.isNotEmptyString(context["contextId"]) ? context["contextId"] : "";
			if(contextId === BX.OrderQuickPanelSectionDragItem.contextId)
			{
				var item = typeof(context["item"]) !== "undefined" ?  context["item"] : null;
				if(!item)
				{
					return;
				}

				var initialSection = item.getSection();
				if(!initialSection)
				{
					return;
				}

				if(targetSection === initialSection)
				{
					var placeholder = initialSection.getPlaceHolder();
					var index = placeholder ? placeholder.getIndex() : -1;
					if(initialSection.moveItem(item, index))
					{
						this._lastChangedSection = initialSection;
						this._enableUserConfig = true;
						this.saveConfig(false);
					}
				}
				else
				{
					if(targetSection.findItemById(item.getId()))
					{
						BX.NotificationPopup.show("field_already_exists", { messages: [this.getMessage("dragDropErrorFieldAlreadyExists")] });
					}
					else
					{
						initialSection.deleteItem(item);
						if(initialSection.getItemCount() === 0)
						{
							initialSection.createPlaceHolder(-1);
						}
						targetSection.createItem(item.getId(), item.getModel());

						this._lastChangedSection = targetSection;
						this._enableUserConfig = true;
						this.saveConfig(false);
					}
				}
			}
			else if(this._formSettingsManager && contextId === this._formSettingsManager.getDraggableFieldContextId())
			{
				var fieldId = this._formSettingsManager.resolveDraggableFieldId(context);
				if(targetSection.findItemById(fieldId))
				{
					BX.NotificationPopup.show("field_already_exists", { messages: [this.getMessage("dragDropErrorFieldAlreadyExists")] });
					return;
				}

				var model = this.getFieldModel(fieldId);
				if(!model)
				{
					BX.NotificationPopup.show("field_not_supported", { messages: [this.getMessage("dragDropErrorFieldNotSupported")] });
					return;
				}

				targetSection.createItem(fieldId, model);
				this._lastChangedSection = targetSection;
				this._enableUserConfig = true;
				this.saveConfig(false);
			}
		},
		processControlPanelLayoutChange: function(panel)
		{
			if(this._isFixed && this._isFixedLayout)
			{
				var heightOffset = panel.isFixed() ? panel.getRect().height : 0;
				this._wrapper.style.top = heightOffset > 0 ? (heightOffset.toString() + "px") : "0";
			}
		},
		getItemDropCallback: function()
		{
			return this._itemDropHandler;
		},
		adjust: function()
		{
			if(!this._isFixed)
			{
				return;
			}

			var heightOffset = 0;
			var panel = typeof(BX.OrderControlPanel) !== "undefined" ? BX.OrderControlPanel.getDefault() : null;
			if(panel && panel.isFixed())
			{
				heightOffset = panel.getRect().height;
			}

			if (BX.OrderQuickPanelView.getNodeRect(this._placeholder).top <= heightOffset)
			{
				if(this._isFixedLayout)
				{
					//synchronize wrapper width
					this._wrapper.style.width = BX.OrderQuickPanelView.getNodeRect(this._placeholder).width.toString() + "px";
				}
				else
				{
					var r = BX.OrderQuickPanelView.getNodeRect(this._wrapper);
					this._wrapper.style.height = this._placeholder.style.height = r.height.toString() + "px";
					this._wrapper.style.width = r.width.toString() + "px";
					this._wrapper.style.left = r.left > 0 ? (r.left.toString() + "px") : "0";
					this._wrapper.style.top = heightOffset > 0 ? (heightOffset.toString() + "px") : "0";

					BX.addClass(this._wrapper, "order-lead-header-table-wrap-fixed");
					this._isFixedLayout = true;
				}
			}
			else if(this._isFixedLayout)
			{
				this._isFixedLayout = false;
				BX.removeClass(this._wrapper, "order-lead-header-table-wrap-fixed");

				this._placeholder.style.height = this._placeholder.style.width = "";
				this._wrapper.style.height = this._wrapper.style.width = this._wrapper.style.left = this._wrapper.style.top = "";
			}
		},
		registerUnloadHandler: function(handler)
		{
			if(!BX.type.isFunction(handler))
			{
				return false;
			}

			for(var i = 0; i < this._unloadHandlers.length; i++)
			{
				if(this._unloadHandlers[i] === handler)
				{
					return false;
				}
			}
			this._unloadHandlers.push(handler);
			return true;
		},
		unregisterUnloadHandler: function(handler)
		{
			for(var i = 0; i < this._unloadHandlers.length; i++)
			{
				if(this._unloadHandlers[i] === handler)
				{
					this._unloadHandlers.splice(i, 1);
					return true;
				}
			}
			return false;
		},
		setItemLocked: function(id, locked)
		{
			for(var k in this._sections)
			{
				if(!this._sections.hasOwnProperty(k))
				{
					continue;
				}

				var item = this._sections[k].findItemById(id);
				if(item)
				{
					item.setLocked(locked);
					break;
				}
			}
		},
		_onItemDrop: function(dragContainer, draggedItem, x, y)
		{
			this.processDraggedItemDrop(dragContainer, draggedItem);
		},
		_onEditorFieldValueSave: function(name, value)
		{
			for(var k in this._models)
			{
				if(this._models.hasOwnProperty(k))
				{
					this._models[k].processEditorFieldValueSave(name, value);
				}
			}
		},
		_onFormSettingManagerCreate: function(mgr)
		{
			this._formSettingsManager = mgr;
		},
		_onProgressControlChanged: function(control, data)
		{
			if(control.getId() !== this.getId())
			{
				return;
			}

			var stepInfo = control.getCurrentStepInfo();
			this._progressLegend.innerHTML = BX.util.htmlspecialchars(
				BX.type.isNotEmptyString(stepInfo["name"]) ? stepInfo["name"] : stepInfo["id"]
			);
		},
		_onConfigRequestComplete: function()
		{
			if(this._waiter)
			{
				BX.closeWait(this._waitAnchor, this._waiter);
				this._waiter = null;
				this._waitAnchor = null;
			}

			if(this._requestCompleteCallback)
			{
				var callback = this._requestCompleteCallback;
				this._requestCompleteCallback = null;
				callback();
			}
		},
		_onMenuButtonClick: function(e)
		{
			if(!e)
			{
				e = window.event;
			}

			this._openMenu();
			return BX.PreventDefault(e);
		},
		_onPinButtonClick: function(e)
		{
			this.setFixed(!this.isFixed());
		},
		_onToggleButtonClick: function(e)
		{
			if(!e)
			{
				e = window.event;
			}

			this.setExpanded(!this.isExpanded());
			return BX.PreventDefault(e);
		},
		_openMenu: function()
		{
			if(this._isMenuShown)
			{
				return;
			}

			var menuItems =
			[
				{
					id: "reset",
					text: this.getMessage("resetMenuItem"),
					onclick: BX.delegate(this._onResetMenuItemClick, this)
				}
			];

			if(this.getSetting("canSaveSettingsForAll", false))
			{
				menuItems.push(
					{
						id: "saveForAll",
						text: this.getMessage("saveForAllMenuItem"),
						onclick: BX.delegate(this._onSaveForAllMenuItemClick, this)
					}
				);

				menuItems.push(
					{
						id: "resetForAll",
						text: this.getMessage("resetForAllMenuItem"),
						onclick: BX.delegate(this._onResetForAllMenuItemClick, this)
					}
				);
			}

			if(typeof(BX.PopupMenu.Data[this._menuId]) !== "undefined")
			{
				BX.PopupMenu.Data[this._menuId].popupWindow.destroy();
				delete BX.PopupMenu.Data[this._menuId];
			}

			this._menu = BX.PopupMenu.create(
				this._menuId,
				this._menuButton,
				menuItems,
				{
					autoHide: true,
					offsetTop: 0,
					offsetLeft: 0,
					angle:
					{
						position: "top",
						offset: 10
					},
					events:
					{
						onPopupClose : BX.delegate(this._onMenuClose, this)
					}
				}
			);

			this._menu.popupWindow.show();
			this._isMenuShown = true;
		},
		_closeMenu: function()
		{
			if(this._menu && this._menu.popupWindow)
			{
				this._menu.popupWindow.close();
			}
		},
		_onMenuClose: function()
		{
			this._menu = null;
			if(typeof(BX.PopupMenu.Data[this._menuId]) !== "undefined")
			{
				BX.PopupMenu.Data[this._menuId].popupWindow.destroy();
				delete BX.PopupMenu.Data[this._menuId];
			}
			this._isMenuShown = false;
		},
		_onResetMenuItemClick: function()
		{
			this._closeMenu();
			if(!this._formSettingsManager)
			{
				var self = this;
				this.resetConfig(false, function(){ self.reload(); });
			}
			else
			{
				var mgr = this._formSettingsManager;
				this.resetConfig(false, function(){ mgr.reset(); });
			}
		},
		_onResetForAllMenuItemClick: function()
		{
			this._closeMenu();
			if(!this._formSettingsManager)
			{
				var self = this;
				this.resetConfig(true, function(){ self.reload(); });
			}
			else
			{
				var mgr = this._formSettingsManager;
				this.resetConfig(true, function(){ mgr.reset(); });
			}
		},
		_onSaveForAllMenuItemClick: function()
		{
			this._closeMenu();
			if(!this._formSettingsManager)
			{
				this.saveConfig(true);
			}
			else
			{
				var mgr = this._formSettingsManager;
				this.saveConfig(
					true,
					function(){ mgr.save(true); }
				);
			}
		},
		_onWindowScroll: function()
		{
			this.adjust();
		},
		_onWindowResize: function(e)
		{
			this.adjust();
		},
		_onUnload: function(e)
		{
			var result = "";
			for(var i = 0; i < this._unloadHandlers.length; i++)
			{
				var text = this._unloadHandlers[i]();
				if(BX.type.isNotEmptyString(text))
				{
					if(result !== "")
					{
						result += "\r\n";
					}
					result += text;
				}
			}

			if(result !== "")
			{
				if (typeof(BX.PULL) != 'undefined' && typeof(BX.PULL.tryConnectDelay) == 'function') // TODO change to right code in near future (e.shelenkov)
				{
					BX.PULL.tryConnectDelay();
				}
				return result;
			}
		},
		_onEditorCreated: function(instantEditor)
		{
			BX.removeCustomEvent(window, "OrderInstantEditorCreated", this._editorCreatedHandler);
			this.setInstantEditor(instantEditor);
		},
		_onSetReadOnlyField: function(instantEditor, name, readonly)
		{
			if(this._instantEditor === instantEditor)
			{
				this.setItemLocked(name, readonly);
			}
		},
		_onControlPanelLayoutChange: function(panel)
		{
			this.processControlPanelLayoutChange(panel);
		},
		_onDragDropBinItemDrop: function(sender, draggedItem)
		{
			if(draggedItem instanceof BX.OrderQuickPanelSectionDragItem)
			{
				var item = draggedItem.getItem();
				if(item)
				{
					item.remove(true);
				}
			}
		}
	};
	if(typeof(BX.OrderQuickPanelView.messages) === "undefined")
	{
		BX.OrderQuickPanelView.messages = {};
	}
	BX.OrderQuickPanelView.getNodeRect = function(node)
	{
		var r = node.getBoundingClientRect();
		return (
			{
				top: r.top, bottom: r.bottom, left: r.left, right: r.right,
				width: typeof(r.width) !== "undefined" ? r.width : (r.right - r.left),
				height: typeof(r.height) !== "undefined" ? r.height : (r.bottom - r.top)
			}
		);
	};
	BX.OrderQuickPanelView._default  = null;
	BX.OrderQuickPanelView.getDefault = function()
	{
		return this._default;
	};
	BX.OrderQuickPanelView.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelView();

		if(!this._default)
		{
			this._default = self;
		}

		self.initialize(id, settings);
		return self;
	};
}
//BX.OrderQuickPanelItem
if(typeof(BX.OrderQuickPanelItem) === "undefined")
{
	BX.OrderQuickPanelItem = function()
	{
		this._id = "";
		this._settings = null;
		this._container = null;
		this._model = null;
		this._instantEditor = null;
		this._isLocked = false;
		this._hasLayout = false;

	};
	BX.OrderQuickPanelItem.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : BX.util.getRandomString(8);
			this._settings = settings ? settings : {};
			this._model = this.getSetting("model");

			var container = this.getSetting("container", null);
			var hasLayout = container && this.getSetting("hasLayout", true);
			this.setContainer(container, hasLayout);

			if(!this._model)
			{
				throw "OrderQuickPanelItem: The 'model' parameter is not defined in settings or empty.";
			}

			this.doInitialize();
		},
		doInitialize: function()
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
		getMessage: function(name)
		{
			var m = BX.OrderQuickPanelItem.messages;
			return m.hasOwnProperty(name) ? m[name] : name;
		},
		getModel: function()
		{
			return this._model;
		},
		getData: function()
		{
			return this._model.getData();
		},
		getType: function()
		{
			return this._model.getType();
		},
		getCaption: function()
		{
			return this._model.getCaption();
		},
		isCaptionEnabled: function()
		{
			return this._model.isCaptionEnabled();
		},
		getContainer: function()
		{
			return this._container;
		},
		setContainer: function(container, hasLayout)
		{
			this._container = container;
			this._hasLayout = !!hasLayout;

			this.doSetContainer();
		},
		doSetContainer: function()
		{
		},
		getControl: function()
		{
			return null;
		},
		getInstantEditor: function()
		{
			return this._instantEditor;
		},
		setInstantEditor: function(instantEditor)
		{
			this._instantEditor = instantEditor;
			var control = this.getControl();
			if(control)
			{
				control.setInstantEditor(instantEditor);
			}

			this.doSetInstantEditor();
		},
		doSetInstantEditor: function()
		{
		},
		isEditable: function()
		{
			return !this._isLocked && this._model.isEditable();
		},
		isLocked: function()
		{
			return this._isLocked;
		},
		setLocked: function(locked)
		{
			locked = !!locked;
			if(this._isLocked === locked)
			{
				return;
			}

			this._isLocked = locked;
			var control = this.getControl();
			if(control)
			{
				control.setLocked(locked);
			}

			this.doSetLocked();
		},
		doSetLocked: function()
		{
		}
	};
	if(typeof(BX.OrderQuickPanelItem.messages) === "undefined")
	{
		BX.OrderQuickPanelItem.messages = {};
	}
}
if(typeof(BX.OrderQuickPanelHeaderItem) === "undefined")
{
	BX.OrderQuickPanelHeaderItem = function()
	{
		BX.OrderQuickPanelHeaderItem.superclass.constructor.apply(this);
		this._control = null;
		this._editButton = null;
		this._editHandler = BX.delegate(this._onEditButtonClick, this);
		this._dblClickHandler = BX.delegate(this._onDoubleClick, this);
	};
	BX.extend(BX.OrderQuickPanelHeaderItem, BX.OrderQuickPanelItem);
	BX.OrderQuickPanelHeaderItem.prototype.doInitialize = function()
	{
		if(!this._container)
		{
			return;
		}

		this._control = this.createControl(this._container);
		this._editButton = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-title-edit" }, true, false);
		this.bindEvents();
	};
	BX.OrderQuickPanelHeaderItem.prototype.doSetLocked = function()
	{
		this._editButton.style.display = this._isLocked ? "none" : "";
	};
	BX.OrderQuickPanelHeaderItem.prototype.bindEvents = function()
	{
		if(this._editButton)
		{
			BX.bind(this._editButton, "click", this._editHandler);
		}

		BX.bind(this._container, "dblclick", this._dblClickHandler);
	};
	BX.OrderQuickPanelHeaderItem.prototype.createControl = function(container)
	{
		var control;
		var type = this.getType();
		if(type === "money")
		{
			control = BX.OrderQuickPanelHeaderMoney.create("", { item: this, container: container, hasLayout: true });
		}
		else
		{
			control = BX.OrderQuickPanelHeaderText.create("", { item: this, container: container, hasLayout: true });
		}

		if(this._instantEditor)
		{
			control.setInstantEditor(this._instantEditor);
		}

		return control;
	};
	BX.OrderQuickPanelHeaderItem.prototype._onEditButtonClick = function(e)
	{
		if(this._control)
		{
			this._control.toggleMode();
		}
	};
	BX.OrderQuickPanelHeaderItem.prototype._onDoubleClick = function(e)
	{
		if(!this.isEditable())
		{
			return;
		}

		if(this._control && this._control.getMode() === BX.OrderQuickPanelControl.mode.view)
		{
			this._control.switchMode(BX.OrderQuickPanelControl.mode.edit);
		}
	};
	BX.OrderQuickPanelHeaderItem.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelHeaderItem();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPaneSectionItem) === "undefined")
{
	BX.OrderQuickPaneSectionItem = function()
	{
		BX.OrderQuickPaneSectionItem.superclass.constructor.apply(this);
		this._section = null;
		this._prefix = "";
		//this._deleteButton = null;
		this._deleteHandler = BX.delegate(this._onDeleteButtonClick, this);
		this._editButton = null;
		this._editHandler = BX.delegate(this._onEditButtonClick, this);
		this._dragButton = null;
		this._dblClickHandler = BX.delegate(this._onDoubleClick, this);
		this._contextMenuHandler = BX.delegate(this._onContextMenu, this);
		this._control = null;
		this._dragItem = null;
		this._isInitialized = false;

		this._contextMenu = null;
		this._contextMenuId = "quick_panel_section_item";
		this._isContextMenuShown = false;
	};
	BX.extend(BX.OrderQuickPaneSectionItem, BX.OrderQuickPanelItem);
	BX.OrderQuickPaneSectionItem.prototype.doInitialize = function()
	{
		this._prefix = this.getSetting("prefix", "");
		this._section = this.getSetting("section", null);

		if(this._hasLayout)
		{
			this.initializeLayout();
		}
	};
	BX.OrderQuickPaneSectionItem.prototype.doSetContainer = function()
	{
		if(this._container && this._hasLayout)
		{
			this.initializeLayout();
		}
	};
	BX.OrderQuickPaneSectionItem.prototype.doSetLocked = function()
	{
		this._editButton.style.display = this._isLocked ? "none" : "";
	};
	BX.OrderQuickPaneSectionItem.prototype.initializeLayout = function()
	{
		if(this._isInitialized || !this._container)
		{
			return;
		}

		var enableCaption = this.isCaptionEnabled();
		this._control = this.createControl(this._container.cells[enableCaption ? 2 : 1]);
		//this._deleteButton = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-inner-del-btn" }, true, false);
		this._editButton = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-inner-edit-btn" }, true, false);
		this._dragButton = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-inner-move-btn" }, true, false);

		this.initializeDragDropAbilities();
		this.bindEvents();
		this._isInitialized = true;
	};
	BX.OrderQuickPaneSectionItem.prototype.createControl = function(container)
	{
		var control;
		var type = this.getType();
		if(type === "link")
		{
			control = BX.OrderQuickPanelLink.create("", { item: this, container: container, hasLayout: this._hasLayout });
		}
		else if(type === "date")
		{
			control = BX.OrderQuickPanelDateTime.create("", { item: this, container: container, hasLayout: this._hasLayout, enableTime: false });
		}
		else if(type === "datetime")
		{
			control = BX.OrderQuickPanelDateTime.create("", { item: this, container: container, hasLayout: this._hasLayout, enableTime: true });
		}
		else if(type === "boolean")
		{
			control = BX.OrderQuickPanelBoolean.create("", { item: this, container: container, hasLayout: this._hasLayout, enableTime: true });
		}
		else if(type === "enumeration")
		{
			control = BX.OrderQuickPanelEnumeration.create("", { item: this, container: container, hasLayout: this._hasLayout, enableTime: true });
		}
		else if(type === "multiField")
		{
			control = BX.OrderQuickPanelMultiField.create("", { item: this, container: container, hasLayout: this._hasLayout });
		}
		else if(type === "address")
		{
			control = BX.OrderQuickPanelAddress.create("", { item: this, container: container, hasLayout: this._hasLayout });
		}
		else if(type === "responsible")
		{
			control = BX.OrderQuickPanelResponsible.create("", { item: this, container: container, hasLayout: this._hasLayout });
		}
		else if(type === "client")
		{
			control = BX.OrderQuickPanelClientInfo.create("", { item: this, container: container, hasLayout: this._hasLayout });
		}
		else if(type === "money")
		{
			control = BX.OrderQuickPanelMoney.create("", { item: this, container: container, hasLayout: this._hasLayout });
		}
		else if(type === "custom")
		{
			control = BX.OrderQuickPanelHtml.create("", { item: this, container: container, hasLayout: this._hasLayout });
		}
		else if(type === "html")
		{
			control = BX.OrderQuickPanelVisualEditor.create("", { item: this, container: container, hasLayout: this._hasLayout });
		}
		else
		{
			control = BX.OrderQuickPanelText.create("", {item: this, container: container, hasLayout: this._hasLayout});
		}

		if(this._instantEditor)
		{
			control.setInstantEditor(this._instantEditor);
		}

		return control;
	};
	BX.OrderQuickPaneSectionItem.prototype.getView = function()
	{
		return this._section ? this._section.getView() : null;
	};
	BX.OrderQuickPaneSectionItem.prototype.getPrefix = function()
	{
		return this._prefix;
	};
	BX.OrderQuickPaneSectionItem.prototype.setPrefix = function(prefix)
	{
		this._prefix = prefix;
	};
	BX.OrderQuickPaneSectionItem.prototype.getSection = function()
	{
		return this._section;
	};
	BX.OrderQuickPaneSectionItem.prototype.setSection = function(section)
	{
		this._section = section;
	};
	BX.OrderQuickPaneSectionItem.prototype.layout = function()
	{
		if(!this._container)
		{
			throw "OrderQuickPaneSectionItem: The 'container' is not assigned.";
		}

		var enableCaption = this.isCaptionEnabled();
		var row = this._container;
		var cell = row.insertCell(-1);
		cell.className = "order-lead-header-inner-cell order-lead-header-inner-cell-move";
		this._dragButton = BX.create("DIV", { attrs: { className: "order-lead-header-inner-move-btn" } });
		cell.appendChild(this._dragButton);

		if(enableCaption)
		{
			cell = row.insertCell(-1);
			cell.className = "order-lead-header-inner-cell order-lead-header-inner-cell-title";
			cell.innerHTML = BX.util.htmlspecialchars(this.getCaption());

			cell = row.insertCell(-1);
			cell.className = "order-lead-header-inner-cell";
		}
		else
		{
			cell = row.insertCell(-1);
			cell.className = "order-lead-header-inner-cell order-lead-header-inf-block";
			cell.colSpan = 2;
		}

		this._control = this.createControl(cell);
		this._control.layout();

		cell = row.insertCell(-1);
		cell.className = "order-lead-header-inner-cell order-lead-header-inner-cell-del";

		//this._deleteButton = BX.create("DIV", { attrs: { className: "order-lead-header-inner-del-btn" } });
		//cell.appendChild(this._deleteButton);

		var enableEditButton = this.isEditable();
		if(enableEditButton)
		{
			enableEditButton = this._control.canChangeMode();
		}
		if(enableEditButton)
		{
			this._editButton = BX.create("DIV", { attrs: { className: "order-lead-header-inner-edit-btn" } });
			cell.appendChild(this._editButton);
		}

		this.initializeDragDropAbilities();
		this.bindEvents();
	};
	BX.OrderQuickPaneSectionItem.prototype.createGhostNode = function()
	{
		var node = BX.create("DIV", { attrs: { className: "order-lead-fly-item" } });
		var table = BX.create("TABLE", { attrs: { className: "order-lead-header-inner-table" } });
		node.appendChild(table);

		var row = table.insertRow();
		var cell = row.insertCell();
		cell.className = "order-lead-header-inner-cell order-lead-header-inner-cell-move";
		cell.appendChild(BX.create("DIV", { attrs: { className: "order-lead-header-inner-move-btn" } }));

		if(this.isCaptionEnabled())
		{
			cell = row.insertCell();
			cell.className = "order-lead-header-inner-cell order-lead-header-inner-cell order-lead-header-inner-cell-title";
			cell.innerHTML = BX.util.htmlspecialchars(this.getCaption());

			cell = row.insertCell();
			cell.className = "order-lead-header-inner-cell";
			cell.innerHTML = this.getContainer().cells[2].innerHTML;
		}
		else
		{
			cell = row.insertCell(-1);
			cell.className = "order-lead-header-inner-cell order-lead-header-inf-block";
			cell.colSpan = 2;
			cell.innerHTML = this.getContainer().cells[1].innerHTML;
		}

		cell = row.insertCell();
		cell.className = "order-lead-header-inner-cell order-lead-header-inner-cell-del";

		var rect = BX.pos(this._container);
		node.style.width = (rect.width - 10) + "px";
		return node;
	};
	BX.OrderQuickPaneSectionItem.prototype.clearLayout = function()
	{
		if(!this._container)
		{
			throw "OrderQuickPaneSectionItem: The 'container' is not assigned.";
		}

		if(this._control)
		{
			this._control.clearLayout();
			this._control = null;
		}

		this._closeContextMenu();
		this.releaseDragDropAbilities();
		this.unbindEvents();
		this._dragButton = null;
		this._editButton = null;
		//this._deleteButton = null;

		BX.cleanNode(this._container, false);

		this._isInitialized = false;
		this._hasLayout = false;
	};
	BX.OrderQuickPaneSectionItem.prototype.bindEvents = function()
	{
		BX.bind(this._container, "dblclick", this._dblClickHandler);

		//if(this._deleteButton)
		//{
		//	BX.bind(this._deleteButton, "click", this._deleteHandler);
		//}

		if(this._editButton)
		{
			BX.bind(this._editButton, "click", this._editHandler);
		}

		if(this._dragButton)
		{
			BX.bind(this._dragButton, "contextmenu", this._contextMenuHandler);
		}
	};
	BX.OrderQuickPaneSectionItem.prototype.unbindEvents = function()
	{
		BX.unbind(this._container, "dblclick", this._dblClickHandler);

		//if(this._deleteButton)
		//{
		//	BX.unbind(this._deleteButton, "click", this._deleteHandler);
		//}

		if(this._editButton)
		{
			BX.unbind(this._editButton, "click", this._editHandler);
		}

		if(this._dragButton)
		{
			BX.unbind(this._dragButton, "contextmenu", this._contextMenuHandler);
		}
	};
	BX.OrderQuickPaneSectionItem.prototype.remove = function(silent)
	{
		silent = !!silent;
		this._closeContextMenu();

		if(!this._section)
		{
			return;
		}

		if(silent || window.confirm(this.getMessage("deletionConfirmation")))
		{
			this._section.processItemDeletion(this);
		}
	};
	BX.OrderQuickPaneSectionItem.prototype._onContextMenu = function(e)
	{
		this._openContextMenu();
		return BX.eventReturnFalse(e);
	};
	BX.OrderQuickPaneSectionItem.prototype._openContextMenu = function()
	{
		if(this._isContextMenuShown)
		{
			return;
		}

		var currentMenu = BX.PopupMenu.getMenuById(this._contextMenuId);
		if(currentMenu)
		{
			currentMenu.popupWindow.close();
		}

		var menuItems = [];
		if(this.isEditable())
		{
			menuItems.push(
				{
					id: "edit",
					text: this.getMessage("editMenuItem"),
					onclick: BX.delegate(this._onEditMenuItemClick, this)
				}
			);
		}

		menuItems.push(
			{
				id: "delete",
				text: this.getMessage("deleteMenuItem"),
				onclick: BX.delegate(this._onDeleteMenuItemClick, this)
			}
		);

		this._contextMenu = BX.PopupMenu.create(
			this._contextMenuId,
			this._dragButton,
			menuItems,
			{
				autoHide: true,
				offsetTop: 0,
				offsetLeft: 0,
				angle: { position: "top", offset: 10 },
				events: { onPopupClose : BX.delegate(this._onContextMenuClose, this) }
			}
		);

		this._contextMenu.popupWindow.show();
		this._isContextMenuShown = true;
	};
	BX.OrderQuickPaneSectionItem.prototype._closeContextMenu = function()
	{
		if(this._contextMenu && this._contextMenu.popupWindow)
		{
			this._contextMenu.popupWindow.close();
		}
	};
	BX.OrderQuickPaneSectionItem.prototype._onContextMenuClose = function()
	{
		this._contextMenu = null;
		if(typeof(BX.PopupMenu.Data[this._contextMenuId]) !== "undefined")
		{
			BX.PopupMenu.Data[this._contextMenuId].popupWindow.destroy();
			delete BX.PopupMenu.Data[this._contextMenuId];
		}
		this._isContextMenuShown = false;
	};
	BX.OrderQuickPaneSectionItem.prototype._onDeleteButtonClick = function(e)
	{
		this.remove();
	};
	BX.OrderQuickPaneSectionItem.prototype._onDeleteMenuItemClick = function(e)
	{
		this._closeContextMenu();
		this.remove(false);
	};
	BX.OrderQuickPaneSectionItem.prototype._onEditButtonClick = function(e)
	{
		if(!this.isEditable())
		{
			return;
		}

		if(this._control)
		{
			this._control.toggleMode();
		}
	};
	BX.OrderQuickPaneSectionItem.prototype._onEditMenuItemClick = function()
	{
		this._closeContextMenu();

		if(!this.isEditable())
		{
			return;
		}

		if(this._control)
		{
			this._control.toggleMode();
		}
	};
	BX.OrderQuickPaneSectionItem.prototype._onDoubleClick = function(e)
	{
		if(!this.isEditable())
		{
			return;
		}

		if(this._control && this._control.getMode() === BX.OrderQuickPanelControl.mode.view)
		{
			this._control.switchMode(BX.OrderQuickPanelControl.mode.edit);
		}
	};
	//D&D abilities
	BX.OrderQuickPaneSectionItem.prototype.initializeDragDropAbilities = function()
	{
		if(this._dragItem)
		{
			return;
		}

		if(!this._dragButton)
		{
			throw "OrderQuickPaneSectionItem: Could not find drag button.";
		}

		this._dragItem = BX.OrderQuickPanelSectionDragItem.create(
			this.getId(),
			{
				item: this,
				node: this._dragButton,
				showItemInDragMode: false,
				ghostOffset: { x: -8, y: -8 }
			}
		);
	};
	BX.OrderQuickPaneSectionItem.prototype.releaseDragDropAbilities = function()
	{
		if(this._dragItem)
		{
			this._dragItem.release();
			this._dragItem = null;
		}
	};
	BX.OrderQuickPaneSectionItem.create = function(id, settings)
	{
		var self = new BX.OrderQuickPaneSectionItem();
		self.initialize(id, settings);
		return self;
	};
}
//BX.OrderQuickPanelControl
if(typeof(BX.OrderQuickPanelControl) === "undefined")
{
	BX.OrderQuickPanelControl = function()
	{
		this._id = "";
		this._settings = null;
		this._item = null;
		this._model = null;
		this._container = null;
		this._mode = 0;
		this._documentClickHandler = BX.delegate(this._onDocumentClick, this);
		this._fieldValueSaveHandler = BX.delegate(this._onFieldValueSave, this);
		this._beforeUnloadHandlerHandler = BX.delegate(this._onBeforeUnload, this);
		this._modelChangeHandler = BX.delegate(this._onModelChange, this);
		this._instantEditor = null;
		this._isEditable = false;
		this._hasLayout = false;
		this._isLocked = false;
		this._enableModelSubscription = false;
		this._enableDocumentUnloadSubscription = false;
	};
	BX.OrderQuickPanelControl.mode =
	{
		undifined: 0,
		view: 1,
		edit: 2
	};
	BX.OrderQuickPanelControl.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : BX.util.getRandomString(8);
			this._settings = settings ? settings : {};

			this._item = this.getSetting("item", null);
			if(!this._item)
			{
				throw  "Error: Could not find item.";
			}

			this._model = this.getSetting("model", null);
			if(!this._model)
			{
				this._model = this._item.getModel();
			}

			this._container = this.getSetting("container", null);
			if(!this._container)
			{
				throw  "Error: Could not find container.";
			}

			this._hasLayout = this.getSetting("hasLayout", false);
			this._mode = BX.OrderQuickPanelControl.mode.view;

			if(this._enableDocumentUnloadSubscription)
			{
				BX.OrderQuickPanelView.getDefault().registerUnloadHandler(this._beforeUnloadHandlerHandler);
			}

			this._isLocked = this._item.isLocked();

			if(this._enableModelSubscription)
			{
				this._model.registerCallback(this._modelChangeHandler);
			}

			this.doInitialize();
		},
		doInitialize: function()
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
		getInstantEditor: function()
		{
			return this._instantEditor;
		},
		setInstantEditor: function(instantEditor)
		{
			this._instantEditor = instantEditor;
			this.processInstantEditorChange();
		},
		processInstantEditorChange: function()
		{
		},
		getMode: function()
		{
			return this._mode;
		},
		isEditMode: function()
		{
			return this._mode === BX.OrderQuickPanelControl.mode.edit;
		},
		toggleMode: function()
		{
			if(this._mode === BX.OrderQuickPanelControl.mode.undifined
				|| (this._mode === BX.OrderQuickPanelControl.mode.view && !this.isEditable()))
			{
				return false;
			}

			if(this._mode === BX.OrderQuickPanelControl.mode.edit)
			{
				this.save();
			}

			var mode = this._mode === BX.OrderQuickPanelControl.mode.edit
				? BX.OrderQuickPanelControl.mode.view : BX.OrderQuickPanelControl.mode.edit;
			return this.switchMode(mode);
		},
		switchMode: function(mode)
		{
			if(this.isLocked())
			{
				return false;
			}

			if(this._mode === mode)
			{
				return false;
			}

			this.onBeforeModeChange();
			this._mode = mode;
			this.onAfterModeChange();
			this.layout();
			this.enableDocumentClick(this.isEditMode());
			return true;
		},
		onBeforeModeChange: function()
		{
		},
		onAfterModeChange: function()
		{
		},
		layout: function()
		{
		},
		clearLayout: function()
		{
			if(!this._hasLayout)
			{
				return;
			}

			this.enableDocumentClick(false);

			if(this._enableDocumentUnloadSubscription)
			{
				BX.OrderQuickPanelView.getDefault().unregisterUnloadHandler(this._beforeUnloadHandlerHandler);
			}

			if(this._enableModelSubscription)
			{
				this._model.unregisterCallback(this._modelChangeHandler);
			}

			this.doClearLayout();
			this._hasLayout = false;
		},
		doClearLayout: function()
		{
		},
		isEditable: function()
		{
			return this._isEditable;
		},
		canChangeMode: function()
		{
			return this.isEditable();
		},
		save: function()
		{
		},
		saveFieldValue: function(value)
		{
			var editor = this.getInstantEditor();
			if(editor)
			{
				BX.addCustomEvent(editor, "OrderInstantEditorFieldValueSaved", this._fieldValueSaveHandler);
				BX.showWait();
				editor.saveFieldValue(this._item.getId(), value);
			}
		},
		getMessage: function(name)
		{
			var m = BX.OrderQuickPanelControl.messages;
			return m.hasOwnProperty(name) ? m[name] : name;
		},
		isOwnElement: function(element)
		{
			return false;
		},
		enableDocumentClick: function(enable)
		{
			if(enable)
			{
				var self = this;
				window.setTimeout(function(){ BX.bind(document, "click", self._documentClickHandler) }, 0);
			}
			else
			{
				BX.unbind(document, "click", this._documentClickHandler);
			}
		},
		isLocked: function()
		{
			return this._isLocked;
		},
		setLocked: function(locked)
		{
			this._isLocked = !!locked;
		},
		isChanged: function()
		{
			return false;
		},
		_onDocumentClick: function(e)
		{
			if(!e)
			{
				e = window.event;
			}

			if(!this.isEditMode())
			{
				this.enableDocumentClick(false);
				return;
			}

			//Crutch for Chrome & IE
			var target = BX.getEventTarget(e);
			if(target === document.body)
			{
				return;
			}

			var isOwnElement = this.isOwnElement(target);
			if(isOwnElement === false)
			{
				this.toggleMode();
			}
		},
		_onFieldValueSave: function(name, value)
		{
			if(name !== this._item.getId())
			{
				return;
			}

			BX.removeCustomEvent(this.getInstantEditor(), "OrderInstantEditorFieldValueSaved", this._fieldValueSaveHandler);
			BX.closeWait();
		},
		_onBeforeUnload: function(e)
		{
			return (
				this.isEditable() && this.isEditMode() && this.isChanged()
				? this.getMessage("dataNotSaved").replace("#FIELD#", this._item.getCaption())
				: undefined
			);
		},
		_onModelChange: function(model, params)
		{
			if(params && params["source"] === this)
			{
				return;
			}

			this.layout();
		}
	};
	if(typeof(BX.OrderQuickPanelControl.messages) === "undefined")
	{
		BX.OrderQuickPanelControl.messages = {};
	}
}
if(typeof(BX.OrderQuickPanelText) === "undefined")
{
	BX.OrderQuickPanelText = function()
	{
		BX.OrderQuickPanelText.superclass.constructor.apply(this);
		this._wrapper = null;
		this._viewWrapper = null;
		this._editWrapper = null;
		this._input = null;
		this._baseTypeName = "";
		this._isMultiline = false;

		//autoresize
		this._hiddenInput = null;
		this._inputMaxHeight = 224;

		this._enableModelSubscription = true;
		this._enableDocumentUnloadSubscription = true;
		this._keyDownHandler = BX.delegate(this._onKeyDown, this);
		this._resizeHandler = BX.delegate(this._onResize, this);
	};
	BX.extend(BX.OrderQuickPanelText, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelText.prototype.doInitialize = function()
	{
		this._isEditable = this._item.isEditable();
		this._baseTypeName = this._model.getDataParam("baseType");
		this._isMultiline = this._model.getDataParam("multiline", false);

		if(this._hasLayout)
		{
			this._wrapper = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-text-wrapper" }, true, false);
			this._viewWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-text-view-wrapper" }, true, false);
			this._editWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-text-edit-wrapper" }, true, false);
			if(this._isEditable)
			{
				if(this._isMultiline)
				{
					this._input = BX.create("TEXTAREA", { attrs: { className: "order-lead-header-edit-field" } });
					this._hiddenInput = BX.create("TEXTAREA", { attrs: { className: "order-lead-header-edit-field" } });
					this._hiddenInput.style.visibility = "hidden";
					this._hiddenInput.style.position = "absolute";
					this._hiddenInput.style.left = "-300px";
					document.body.appendChild(this._hiddenInput);
				}
				else
				{
					this._input = BX.create("INPUT", { attrs: { type: "text", className: "order-lead-header-edit-inp" } });
				}
				this._editWrapper.appendChild(this._input);
			}
		}
	};
	BX.OrderQuickPanelText.prototype.layout = function()
	{
		if(!this._hasLayout)
		{
			this._wrapper = BX.create("DIV", { attrs: { className: "order-lead-header-text-wrapper" } });
			this._container.appendChild(this._wrapper);

			this._viewWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-text-view-wrapper" } });
			this._wrapper.appendChild(this._viewWrapper);

			this._editWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-text-edit-wrapper" } });
			this._wrapper.appendChild(this._editWrapper);

			if(this._isMultiline)
			{
				this._input = BX.create("TEXTAREA", { attrs: { className: "order-lead-header-edit-field" } });
				this._hiddenInput = BX.create("TEXTAREA", { attrs: { className: "order-lead-header-edit-field" } });
				this._hiddenInput.style.visibility = "hidden";
				this._hiddenInput.style.position = "absolute";
				this._hiddenInput.style.left = "-300px";
				document.body.appendChild(this._hiddenInput);
			}
			else
			{
				this._input = BX.create("INPUT", { attrs: { type: "text", className: "order-lead-header-edit-inp" } });
			}

			this._editWrapper.appendChild(this._input);

			this._hasLayout = true;
		}

		var text = this._model.getValue();
		if(!this.isEditMode())
		{
			BX.unbind(this._input, "keydown", this._keyDownHandler);

			if(this._isMultiline)
			{
				BX.unbind(this._input, "keyup", this._resizeHandler);
				BX.unbind(this._input, "change", this._resizeHandler);
			}

			this._editWrapper.style.display = "none";
			if(this._viewWrapper.style.display === "none")
			{
				this._viewWrapper.style.display = "";
			}
			if(this._isMultiline)
			{
				this._viewWrapper.innerHTML = BX.util.htmlspecialchars(text).replace(/(\n)/g, "<br/>");
			}
			else
			{
				this._viewWrapper.innerHTML = BX.util.htmlspecialchars(text);
			}
		}
		else
		{
			if(this._isMultiline)
			{
				var rect = BX.OrderQuickPanelView.getNodeRect(this._viewWrapper);
				var height = rect.height > 16
					? (rect.height < this._inputMaxHeight ? rect.height : this._inputMaxHeight)
					: 16;
				var width = rect.width;

				this._input.style.height = this._hiddenInput.style.height = height + "px";
				this._hiddenInput.style.width = width + "px";

				BX.bind(this._input, "keyup", this._resizeHandler);
				BX.bind(this._input, "change", this._resizeHandler);
			}

			this._viewWrapper.style.display = "none";
			if(this._editWrapper.style.display === "none")
			{
				this._editWrapper.style.display = "";
			}
			this._input.value = text;
			this._input.focus();

			BX.bind(this._input, "keydown", this._keyDownHandler);
		}
	};
	BX.OrderQuickPanelText.prototype.doClearLayout = function()
	{
		if(this.isEditMode())
		{
			BX.unbind(this._input, "keydown", this._keyDownHandler);
			if(this._isMultiline)
			{
				BX.unbind(this._input, "keyup", this._resizeHandler);
				BX.unbind(this._input, "change", this._resizeHandler);
			}
		}
		BX.cleanNode(this._wrapper);
	};
	BX.OrderQuickPanelText.prototype.save = function()
	{
		this.saveIfChanged();
	};
	BX.OrderQuickPanelText.prototype.saveIfChanged = function()
	{
		var previous = this._model.getValue();
		var current = this._input.value;
		if(this._baseTypeName === "int")
		{
			current = current.replace(/[^0-9]/g);
			if(current === "" || isNaN(parseInt(current)))
			{
				current = "0";
			}
		}

		if(previous === current)
		{
			return;
		}

		this._model.setValue(current, true, this);
	};
	BX.OrderQuickPanelText.prototype.isOwnElement = function(element)
	{
		return this._wrapper !== null && this._wrapper === BX.findParent(element, { className: "order-lead-header-text-wrapper" });
	};
	BX.OrderQuickPanelText.prototype.isChanged = function()
	{
		var previous = this._model.getValue();
		var current = this._input.value;
		return previous !== current;
	};
	BX.OrderQuickPanelText.prototype._onKeyDown = function(e)
	{
		if(!this.isEditMode())
		{
			return;
		}

		e = e || window.event;
		if(e.keyCode === 13 && !this._isMultiline)
		{
			this.saveIfChanged();
			this.switchMode(BX.OrderQuickPanelControl.mode.view);
		}
		else if(e.keyCode === 27)
		{
			this.switchMode(BX.OrderQuickPanelControl.mode.view);
		}
	};
	BX.OrderQuickPanelText.prototype._onResize = function(e)
	{
		var currentHeight = BX.OrderQuickPanelView.getNodeRect(this._input).height;
		this._hiddenInput.value = this._input.value;
		var scrollHeight = this._hiddenInput.scrollHeight;
		if (scrollHeight > this._inputMaxHeight)
		{
			scrollHeight = this._inputMaxHeight;
		}

		if(currentHeight != scrollHeight)
		{
			this._input.style.height = scrollHeight + "px";
		}
	};
	BX.OrderQuickPanelText.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelText();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelDateTime) === "undefined")
{
	BX.OrderQuickPanelDateTime = function()
	{
		BX.OrderQuickPanelDateTime.superclass.constructor.apply(this);
		this._wrapper = null;
		this._viewWrapper = null;
		this._editWrapper = null;
		this._input = null;
		this._selector = null;

		this._enableModelSubscription = true;
		this._enableDocumentUnloadSubscription = true;
	};
	BX.extend(BX.OrderQuickPanelDateTime, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelDateTime.prototype.doInitialize = function()
	{
		this._isEditable = this._item.isEditable();
		this._enableTime = this.getSetting("enableTime", true);
		if(this._hasLayout)
		{
			this._wrapper = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-date-wrapper" }, true, false);
			this._viewWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-date-view-wrapper" }, true, false);
			this._editWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-date-edit-wrapper" }, true, false);
			if(this._isEditable)
			{
				this._input = BX.create(
					"INPUT",
					{
						attrs: { className: "order-offer-item-inp order-item-table-date" },
						props: { type: "text" }
					}
				);
				this._editWrapper.appendChild(this._input);
			}
		}
	};
	BX.OrderQuickPanelDateTime.prototype.layout = function()
	{
		if(!this._hasLayout)
		{
			this._wrapper = BX.create("DIV", { attrs: { className: "order-lead-header-date-wrapper" } });
			this._container.appendChild(this._wrapper);

			this._viewWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-date-view-wrapper" } });
			this._wrapper.appendChild(this._viewWrapper);

			this._editWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-date-edit-wrapper" } });
			this._wrapper.appendChild(this._editWrapper);

			this._input = BX.create(
				"INPUT",
				{
					attrs: { className: "order-offer-item-inp order-item-table-date" },
					props: { type: "text" }
				}
			);
			this._editWrapper.appendChild(this._input);
			this._hasLayout = true;
		}

		if(!this.isEditMode())
		{
			this._editWrapper.style.display = "none";
			if(this._viewWrapper.style.display === "none")
			{
				this._viewWrapper.style.display = "";
			}
			this._viewWrapper.innerHTML = BX.util.htmlspecialchars(this._model.getDataParam("text"));
		}
		else
		{
			this._viewWrapper.style.display = "none";
			if(this._editWrapper.style.display === "none")
			{
				this._editWrapper.style.display = "";
			}
			this._input.value = this._model.getDataParam("text");
			if(!this._selector)
			{
				this._selector = BX.OrderDateLinkField.create(
					this._input,
					null,
					{
						showTime: this._enableTime,
						setFocusOnShow: false
					}
				);
			}
		}
	};
	BX.OrderQuickPanelDateTime.prototype.doClearLayout = function()
	{
		BX.cleanNode(this._wrapper);
	};
	BX.OrderQuickPanelDateTime.prototype.save = function()
	{
		this.saveIfChanged();
	};
	BX.OrderQuickPanelDateTime.prototype.saveIfChanged = function()
	{
		var previous = this._model.getValue();
		var current = this._input.value;
		if(previous === current)
		{
			return;
		}

		this._model.setValue(current, true, this);
	};
	BX.OrderQuickPanelDateTime.prototype.isOwnElement = function(element)
	{
		return this._wrapper !== null && this._wrapper === BX.findParent(element, { className: "order-lead-header-date-wrapper" });
	};
	BX.OrderQuickPanelDateTime.prototype.isChanged = function()
	{
		var previous = this._model.getValue();
		var current = this._input.value;
		return previous !== current;
	};
	BX.OrderQuickPanelDateTime.prototype.isTimeEnabled = function()
	{
		return this._enableTime;
	};
	BX.OrderQuickPanelDateTime.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelDateTime();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelBoolean) === "undefined")
{
	BX.OrderQuickPanelBoolean = function()
	{
		BX.OrderQuickPanelBoolean.superclass.constructor.apply(this);
		this._wrapper = null;
		this._viewWrapper = null;
		this._editWrapper = null;
		this._input = null;
		this._baseTypeName = "int";
		this._enableChar = false;
		this._value = false;

		this._enableModelSubscription = true;
		this._enableDocumentUnloadSubscription = true;
	};
	BX.extend(BX.OrderQuickPanelBoolean, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelBoolean.prototype.doInitialize = function()
	{
		this._isEditable = this._item.isEditable();
		this._enableChar = this._model.getBaseType() === "char";
		if(this._enableChar)
		{
			this._value = this._model.getDataParam("value") === "Y";
		}
		else
		{
			this._value = this._model.getDataParam("value") === 1;
		}

		if(this._hasLayout)
		{
			this._wrapper = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-boolean-wrapper" }, true, false);
			this._viewWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-boolean-view-wrapper" }, true, false);
			this._editWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-boolean-edit-wrapper" }, true, false);
			if(this._isEditable)
			{
				this._input = BX.create("INPUT", { props: { type: "checkbox", checked: this._value } });
				this._editWrapper.appendChild(this._input);
			}
		}
	};
	BX.OrderQuickPanelBoolean.prototype.layout = function()
	{
		if(!this._hasLayout)
		{
			this._wrapper = BX.create("DIV", { attrs: { className: "order-lead-header-boolean-wrapper" } });
			this._container.appendChild(this._wrapper);

			this._viewWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-boolean-view-wrapper" } });
			this._wrapper.appendChild(this._viewWrapper);

			this._editWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-boolean-edit-wrapper" } });
			this._wrapper.appendChild(this._editWrapper);

			this._input = BX.create("INPUT", { props: { type: "checkbox" } });
			this._editWrapper.appendChild(this._input);

			this._hasLayout = true;
		}

		if(!this.isEditMode())
		{
			this._editWrapper.style.display = "none";
			if(this._viewWrapper.style.display === "none")
			{
				this._viewWrapper.style.display = "";
			}
			this._viewWrapper.innerHTML =BX.util.htmlspecialchars(this.getMessage(this._value ? "yes" : "no"));
		}
		else
		{
			this._viewWrapper.style.display = "none";
			if(this._editWrapper.style.display === "none")
			{
				this._editWrapper.style.display = "";
			}
			this._input.checked = this._value;

		}
	};
	BX.OrderQuickPanelBoolean.prototype.doClearLayout = function()
	{
		BX.cleanNode(this._wrapper);
	};
	BX.OrderQuickPanelBoolean.prototype.save = function()
	{
		this.saveIfChanged();
	};
	BX.OrderQuickPanelBoolean.prototype.saveIfChanged = function()
	{
		var previous = this._value;
		var current = this._input.checked;

		if(previous === current)
		{
			return;
		}

		this._value = current;
		this._model.setValue(current, true, this);
	};
	BX.OrderQuickPanelBoolean.prototype.isOwnElement = function(element)
	{
		return this._wrapper !== null && this._wrapper === BX.findParent(element, { className: "order-lead-header-boolean-wrapper" });

	};
	BX.OrderQuickPanelBoolean.prototype.isChanged = function()
	{
		var previous = this._value;
		var current = this._input.checked;
		return previous !== current;
	};
	BX.OrderQuickPanelBoolean.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelBoolean();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelLink) === "undefined")
{
	BX.OrderQuickPanelLink = function()
	{
		BX.OrderQuickPanelLink.superclass.constructor.apply(this);
		this._wrapper = null;
		this._input = null;
		this._url = "";
		this._text = "";
	};
	BX.extend(BX.OrderQuickPanelLink, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelLink.prototype.doInitialize = function()
	{
		this._isEditable = false;

		this._url = this._model.getDataParam("url", "");
		if(this._url === "")
		{
			this._url = "#";
		}

		this._text = this._model.getDataParam("text", "");
		if(this._text === "")
		{
			this._text = this._url;
		}

		if(this._hasLayout)
		{
			this._wrapper = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-link-wrapper" }, true, false);
			this._input = BX.findChild(this._wrapper, { tagName: "A", className: "order-link" }, true, false);
		}
	};
	BX.OrderQuickPanelLink.prototype.layout = function()
	{
		if(!this._hasLayout)
		{
			this._wrapper = BX.create("DIV", { attrs: { className: "order-lead-header-link-wrapper" } });
			this._container.appendChild(this._wrapper);

			this._input = BX.create(
				"A",
				{
					attrs: { className: "order-link" },
					props: { href: this._url, target: "_blank" },
					text: this._text
				}
			);
			this._wrapper.appendChild(this._input);
			this._hasLayout = true;
		}
	};
	BX.OrderQuickPanelLink.prototype.doClearLayout = function()
	{
		BX.cleanNode(this._wrapper);
	};
	BX.OrderQuickPanelLink.prototype.isOwnElement = function(element)
	{
		return this._wrapper !== null && this._wrapper === BX.findParent(element, { className: "order-lead-header-link-wrapper" });

	};
	BX.OrderQuickPanelLink.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelLink();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelHtml) === "undefined")
{
	BX.OrderQuickPanelHtml = function()
	{
		BX.OrderQuickPanelHtml.superclass.constructor.apply(this);
		this._wrapper = null;
		this._html = "";
	};
	BX.extend(BX.OrderQuickPanelHtml, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelHtml.prototype.doInitialize = function()
	{
		this._isEditable = false;
		this._html = this._model.getDataParam("html");

		if(this._hasLayout)
		{
			this._wrapper = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-custom-wrapper" }, true, false);
		}
	};
	BX.OrderQuickPanelHtml.prototype.layout = function()
	{
		if(!this._hasLayout)
		{
			this._wrapper = BX.create("DIV", { attrs: { className: "order-lead-header-custom-wrapper" } });
			this._container.appendChild(this._wrapper);
			this._wrapper.innerHTML = this._html;
			this._hasLayout = true;
		}
	};
	BX.OrderQuickPanelHtml.prototype.doClearLayout = function()
	{
		BX.cleanNode(this._wrapper);
	};
	BX.OrderQuickPanelHtml.prototype.isOwnElement = function(element)
	{
		return this._wrapper !== null && this._wrapper === BX.findParent(element, { className: "order-lead-header-custom-wrapper" });

	};
	BX.OrderQuickPanelHtml.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelHtml();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelEnumeration) === "undefined")
{
	BX.OrderQuickPanelEnumeration = function()
	{
		BX.OrderQuickPanelEnumeration.superclass.constructor.apply(this);
		this._wrapper = null;
		this._viewWrapper = null;
		this._editWrapper = null;
		this._input = null;

		this._enableModelSubscription = true;
		this._enableDocumentUnloadSubscription = true;
	};
	BX.extend(BX.OrderQuickPanelEnumeration, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelEnumeration.prototype.doInitialize = function()
	{
		this._isEditable = this._item.isEditable();

		if(this._hasLayout)
		{
			this._wrapper = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-enumeration-wrapper" }, true, false);
			this._viewWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-enumeration-view-wrapper" }, true, false);
			this._editWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-enumeration-edit-wrapper" }, true, false);
			if(this._isEditable)
			{
				this._input = BX.create("SELECT", { attrs: { className: "order-item-table-select" } });
				this._editWrapper.appendChild(this._input);
				this.prepareItemOptions();
			}
		}
	};
	BX.OrderQuickPanelEnumeration.prototype.layout = function()
	{
		if(!this._hasLayout)
		{
			this._wrapper = BX.create("DIV", { attrs: { className: "order-lead-header-enumeration-wrapper" } });
			this._container.appendChild(this._wrapper);

			this._viewWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-enumeration-view-wrapper" } });
			this._wrapper.appendChild(this._viewWrapper);

			this._editWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-enumeration-edit-wrapper" } });
			this._wrapper.appendChild(this._editWrapper);

			this._input = BX.create("SELECT", { attrs: { className: "order-item-table-select" } });
			this._editWrapper.appendChild(this._input);
			this.prepareItemOptions();

			this._hasLayout = true;
		}

		if(!this.isEditMode())
		{
			this._editWrapper.style.display = "none";
			if(this._viewWrapper.style.display === "none")
			{
				this._viewWrapper.style.display = "";
			}
			this._viewWrapper.innerHTML = BX.util.htmlspecialchars(this._model.getDataParam("text"));
		}
		else
		{
			this._viewWrapper.style.display = "none";
			if(this._editWrapper.style.display === "none")
			{
				this._editWrapper.style.display = "";
			}
			this._input.selectedIndex = this.getItemIndex(this._model.getDataParam("value"));
		}
	};
	BX.OrderQuickPanelEnumeration.prototype.doClearLayout = function()
	{
		BX.cleanNode(this._wrapper);
	};
	BX.OrderQuickPanelEnumeration.prototype.save = function()
	{
		this.saveIfChanged();
	};
	BX.OrderQuickPanelEnumeration.prototype.saveIfChanged = function()
	{
		var previous = this._model.getValue();
		var current = this._input.value;
		if(previous === current)
		{
			return;
		}

		this._model.setValue(current, true, this);
	};
	BX.OrderQuickPanelEnumeration.prototype.isOwnElement = function(element)
	{
		return this._wrapper !== null && this._wrapper === BX.findParent(element, { className: "order-lead-header-enumeration-wrapper" });

	};
	BX.OrderQuickPanelEnumeration.prototype.isChanged = function()
	{
		var previous = this._model.getValue();
		var current = this._input.value;
		return previous !== current;
	};
	BX.OrderQuickPanelEnumeration.prototype.getItemText = function(val)
	{
		return this._model.getItemText(val);
	};
	BX.OrderQuickPanelEnumeration.prototype.getItemIndex = function(val)
	{
		return this._model.getItemIndex(val);
	};
	BX.OrderQuickPanelEnumeration.prototype.prepareItemOptions = function()
	{
		if(!this._input)
		{
			return;
		}

		this._input.options[0] = new Option(this.getMessage("notSelected"), "");
		var items = this._model.getItems();
		for(var i = 0; i < items.length; i++)
		{
			var item = items[i];
			var id = typeof(item["ID"]) !== "undefined" ? item["ID"] : "";
			var value = typeof(item["VALUE"]) !== "undefined" ? item["VALUE"] : "";
			this._input.options[this._input.options.length] = new Option(value, id);
		}
	};
	BX.OrderQuickPanelEnumeration.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelEnumeration();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelMoney) === "undefined")
{
	BX.OrderQuickPanelMoney = function()
	{
		BX.OrderQuickPanelMoney.superclass.constructor.apply(this);
		this._wrapper = null;
		this._viewWrapper = null;
		this._editWrapper = null;
		this._input = null;
		this._wait = null;
		this._editorCreatedHandler = BX.delegate(this._onEditorCreated, this);
		this._modelChangeHandler = BX.delegate(this._onModelChange, this);

		this._enableModelSubscription = true;
		this._enableDocumentUnloadSubscription = true;
	};
	BX.extend(BX.OrderQuickPanelMoney, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelMoney.prototype.doInitialize = function()
	{
		this._isEditable = this._item.isEditable();
		if(this._hasLayout)
		{
			this._wrapper = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-text-wrapper" }, true, false);
			this._viewWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-text-view-wrapper" }, true, false);
			this._editWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-text-edit-wrapper" }, true, false);
			if(this._isEditable)
			{
				this._input = BX.create("TEXTAREA", {attrs: {className: "order-lead-header-edit-field"}});
				this._editWrapper.appendChild(this._input);
			}
		}
	};
	BX.OrderQuickPanelMoney.prototype.layout = function()
	{
		if(!this._hasLayout)
		{
			this._wrapper = BX.create("DIV", { attrs: { className: "order-lead-header-text-wrapper" } });
			this._container.appendChild(this._wrapper);

			this._viewWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-text-view-wrapper" } });
			this._wrapper.appendChild(this._viewWrapper);

			this._editWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-text-edit-wrapper" } });
			this._wrapper.appendChild(this._editWrapper);

			this._input = BX.create("TEXTAREA", { attrs: { className: "order-lead-header-edit-field" } });
			this._editWrapper.appendChild(this._input);

			this._hasLayout = true;
		}

		if(!this.isEditMode())
		{
			this._editWrapper.style.display = "none";
			if(this._viewWrapper.style.display === "none")
			{
				this._viewWrapper.style.display = "";
			}
			this._viewWrapper.innerHTML = BX.util.htmlspecialchars(this._model.getFormattedValue(false));
		}
		else
		{
			var pos = BX.pos(this._viewWrapper);
			this._viewWrapper.style.display = "none";
			if(this._editWrapper.style.display === "none")
			{
				this._editWrapper.style.display = "";
			}
			this._input.style.height = pos.height + "px";
			this._input.value = this._model.getValue();

		}
	};
	BX.OrderQuickPanelMoney.prototype.doClearLayout = function()
	{
		this._model.unregisterCallback(this._modelChangeHandler);
		BX.cleanNode(this._wrapper);
	};
	BX.OrderQuickPanelMoney.prototype.save = function()
	{
		this.saveIfChanged();
	};
	BX.OrderQuickPanelMoney.prototype.saveIfChanged = function()
	{
		var previous = this._model.getValue();
		var current = this._input.value.replace(/[^0-9\.]+/g, "");
		if(previous === current)
		{
			return;
		}

		this._model.setValue(current, true, this);
	};
	BX.OrderQuickPanelMoney.prototype.isOwnElement = function(element)
	{
		return this._wrapper !== null && this._wrapper === BX.findParent(element, { className: "order-lead-header-text-wrapper" });

	};
	BX.OrderQuickPanelMoney.prototype.isChanged = function()
	{
		var previous = this._model.getValue();
		var current = this._input.value.replace(/[^0-9\.]+/g, "");
		return previous !== current;
	};
	BX.OrderQuickPanelMoney.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelMoney();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelResponsible) === "undefined")
{
	BX.OrderQuickPanelResponsible = function()
	{
		BX.OrderQuickPanelResponsible.superclass.constructor.apply(this);
		this._editButton = null;
		this._link = null;
	};
	BX.extend(BX.OrderQuickPanelResponsible, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelResponsible.prototype.doInitialize = function()
	{
		this._isEditable = this._item.isEditable();
		this._mode = BX.OrderQuickPanelControl.mode.undifined;
	};
	BX.OrderQuickPanelResponsible.prototype.getMessage = function(name)
	{
		var msgs = BX.OrderQuickPanelResponsible.messages;
		return msgs.hasOwnProperty(name) ? msgs[name] : "";
	};
	BX.OrderQuickPanelResponsible.prototype.layout = function()
	{
		var editable = this._isEditable;

		var wrapper = BX.create("DIV", { attrs: { className: "order-detail-info-resp-block" } });
		this._container.appendChild(wrapper);

		var header = BX.create("DIV", { attrs: { className: "order-detail-info-resp-header" } });
		wrapper.appendChild(header);

		header.appendChild(BX.create("SPAN", { attrs: { className: "order-detail-info-resp-text" }, text: this._item.getCaption() }));
		if(editable)
		{
			this._editButton = BX.create("SPAN", { attrs: { className: "order-detail-info-resp-edit" }, text: this.getMessage("change") });
			header.appendChild(this._editButton);
		}

		this._link = BX.create(
			"A",
			{
				attrs: { className: "order-detail-info-resp" },
				props:
				{
					target: "_blank",
					href: this._model.getDataParam("profileUrl")
				}
			}
		);
		wrapper.appendChild(this._link);

		var imgContainer = BX.create("DIV", { attrs: { className: "order-detail-info-resp-img" } });
		this._link.appendChild(imgContainer);

		var photoUrl = this._model.getDataParam("photoUrl", "");
		if(photoUrl !== "")
		{
			imgContainer.appendChild(BX.create("IMG", { props: { src: photoUrl } }));
		}

		this._link.appendChild(
			BX.create(
				"SPAN",
				{
					attrs: { className: "order-detail-info-resp-name" },
					text: this._model.getDataParam("name")
				}
			)
		);

		this._link.appendChild(
			BX.create(
				"SPAN",
				{
					attrs: { className: "order-detail-info-resp-descr" },
					text: this._model.getDataParam("position")
				}
			)
		);

		var serviceUrl =  this._model.getDataParam("serviceUrl", "");
		var userInfoProviderId = this._model.getDataParam("userInfoProviderID", "");
		if(userInfoProviderId !== "")
		{
			BX.OrderUserInfoProvider.createIfNotExists(
				userInfoProviderId,
				{
					serviceUrl: serviceUrl,
					userProfileUrlTemplate: this._model.getDataParam("profileUrlTemplate")
				}
			);
		}

		var editorId = this._model.getDataParam("editorID", "");
		var fieldId = this._model.getDataParam("fieldID");
		if(!editable)
		{
			BX.OrderUserLinkField.create(
				{ container: this._link, userInfoProviderId: userInfoProviderId, editorId: editorId, fieldId: fieldId }
			);
		}
		else
		{
			var userSelectorName = BX.util.getRandomString(16);
			BX.OrderSidebarUserSelector.create(
				userSelectorName,
				this._editButton,
				this._link,
				userSelectorName,
				{ userInfoProviderId: userInfoProviderId, editorId: editorId, fieldId: fieldId, enableLazyLoad: true, serviceUrl: serviceUrl }
			);
		}
	};
	BX.OrderQuickPanelResponsible.prototype.canChangeMode = function()
	{
		return false;
	};
	if(typeof(BX.OrderQuickPanelResponsible.messages) === "undefined")
	{
		BX.OrderQuickPanelResponsible.messages = {};
	}
	BX.OrderQuickPanelResponsible.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelResponsible();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelMultiField) === "undefined")
{
	BX.OrderQuickPanelMultiField = function()
	{
		BX.OrderQuickPanelMultiField.superclass.constructor.apply(this);
		this._id = "";
		this._settings = null;
		this._item = null;
		this._container = null;
		this._openListButton = null;
		this._openListHandler = BX.delegate(this._onOpenListButtonClick, this);

	};
	BX.extend(BX.OrderQuickPanelMultiField, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelMultiField.prototype.layout = function()
	{
		var items = this._model.getDataParam("items", []);
		var type =  this._model.getDataParam("type", "");

		if(items.length === 0)
		{
			return;
		}

		var wrapper = BX.create("SPAN", { attrs: { className: "order-detail-info-item-text" } });
		this._container.appendChild(wrapper);

		var firstItem = items[0];
		wrapper.innerHTML += firstItem["value"];

		if(type === "PHONE" && BX.type.isNotEmptyString(firstItem["sipCallHtml"]))
		{
			wrapper.innerHTML += firstItem["sipCallHtml"];
			BX.addClass(wrapper, "order-detail-info-item-handset");
		}

		if(items.length > 1)
		{
			BX.addClass(wrapper, "order-detail-info-item-list");
			this._openListButton = BX.create(
				"SPAN",
				{
					attrs: { className: "order-item-tel-list" }
				}
			);
			wrapper.appendChild(this._openListButton);
			BX.bind(this._openListButton, "click", this._openListHandler);
		}

		BX.OrderQuickPanelMultiField.adjustElement(wrapper);
	};
	BX.OrderQuickPanelMultiField.prototype._onOpenListButtonClick = function(e)
	{
		var items = this._model.getDataParam("items", []);
		var type =  this._model.getDataParam("type", "");

		if(items.length <= 1)
		{
			return;
		}

		var menuItems = [];
		for(var i = 1; i < items.length; i++)
		{
			var item = items[i];
			if(BX.type.isNotEmptyString(item["value"]))
			{
				menuItems.push(item);
			}
		}

		BX.OrderMultiFieldViewer.ensureCreated(
			this._id,
			{
				typeName: type,
				items: menuItems,
				anchor: this._openListButton,
				topmost: true
			}
		).show();
	};
	BX.OrderQuickPanelMultiField.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelMultiField();
		self.initialize(id, settings);
		return self;
	};
	BX.OrderQuickPanelMultiField._wrapper = null;
	BX.OrderQuickPanelMultiField.setWrapper = function(wrapper)
	{
		this._wrapper = wrapper;
		this.adjust();
		BX.bind(window, "resize", BX.delegate(BX.OrderQuickPanelMultiField.onWindowResize, this));
	};
	BX.OrderQuickPanelMultiField.onWindowResize = function(e)
	{
		this.adjust();
	};
	BX.OrderQuickPanelMultiField.adjust = function()
	{
		if(!this._wrapper || !BX.type.isFunction(cssQuery))
		{
			return;
		}

		var maxWidth = BX.OrderQuickPanelMultiField.calculateMaxElementWidth();
		if(maxWidth <= 0)
		{
			return;
		}

		var elements = cssQuery(".order-detail-info-item-text", this._wrapper);
		for(var i = 0; i < elements.length; i++)
		{
			elements[i].style.maxWidth =  maxWidth + 'px';
		}
	};
	BX.OrderQuickPanelMultiField.adjustElement = function(element)
	{
		if(!this._wrapper)
		{
			return;
		}

		var maxWidth = BX.OrderQuickPanelMultiField.calculateMaxElementWidth();
		if(maxWidth > 0)
		{
			element.style.maxWidth = maxWidth + 'px';
		}
	};
	BX.OrderQuickPanelMultiField.calculateMaxElementWidth = function()
	{
		return this._wrapper ? Math.ceil(65 * (this._wrapper.offsetWidth  / 3 - 30) / 100) : 0;
	};
}
if(typeof(BX.OrderQuickPanelClientInfo) === "undefined")
{
	BX.OrderQuickPanelClientInfo = function()
	{
		BX.OrderQuickPanelClientInfo.superclass.constructor.apply(this);
		this._fieldData = {};
		this._link = null;
		this._controls = {};
	};
	BX.extend(BX.OrderQuickPanelClientInfo, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelClientInfo.prototype.doInitialize = function()
	{
		var phone = this._model.getDataParam("PHONE", null);
		if(phone)
		{
			this._fieldData["phone"] = phone;
		}

		var email = this._model.getDataParam("EMAIL", null);
		if(email)
		{
			this._fieldData["email"] = email;
		}
	};
	BX.OrderQuickPanelClientInfo.prototype.getMessage = function(name)
	{
		var msgs = BX.OrderQuickPanelClientInfo.messages;
		return msgs.hasOwnProperty(name) ? msgs[name] : "";
	};
	BX.OrderQuickPanelClientInfo.prototype.layout = function()
	{
		var wrapper = BX.create("DIV", { attrs: { className: "order-detail-info-resp-block" } });
		this._container.appendChild(wrapper);

		var linkClassName = "order-detail-info-resp";
		var entityTypeName = this._model.getDataParam("ENTITY_TYPE_NAME");
		linkClassName += entityTypeName === "CONTACT"
			? " order-detail-info-head-cont" : " order-detail-info-head-firm";

		this._link = BX.create(
			"A",
			{
				attrs: { className: linkClassName },
				props: { target: "_blank", href: this._model.getDataParam("SHOW_URL") }
			}
		);

		wrapper.appendChild(this._link);

		var name = this._model.getDataParam("NAME");
		var isNotEmpty = BX.type.isNotEmptyString(name);

		var imageContainer = BX.create("DIV", { attrs: { className: entityTypeName === "COMPANY" && isNotEmpty ? "order-lead-header-company-img" : "order-detail-info-resp-img" } });
		this._link.appendChild(imageContainer);

		var imageUrl = this._model.getDataParam("IMAGE_URL", "");
		if(imageUrl !== "")
		{
			imageContainer.appendChild(BX.create("IMG", { props: { src: imageUrl } }));
		}

		if(isNotEmpty)
		{
			this._link.appendChild(
				BX.create(
					"SPAN",
					{ attrs: { className: "order-detail-info-resp-name" }, text: this._model.getDataParam("NAME") }
				)
			);

			this._link.appendChild(
				BX.create(
					"SPAN",
					{ attrs: { className: "order-detail-info-resp-descr" }, text: this._model.getDataParam("DESCRIPTION") }
				)
			);
		}
		else
		{
			this._link.appendChild(
				BX.create(
					"DIV",
					{
						attrs: { className: "order-detail-info-empty" },
						text: this.getMessage(
							entityTypeName === "CONTACT" ? "contactNotSelected" : "companyNotSelected"
						)
					}
				)
			);
		}

		var control = this.createMultifieldControl("phone", wrapper);
		if(control)
		{
			this._controls["phone"] = control;
			control.layout();
		}

		control = this.createMultifieldControl("email", wrapper);
		if(control)
		{
			this._controls["email"] = control;
			control.layout();
		}
	};
	BX.OrderQuickPanelClientInfo.prototype.createMultifieldControl = function(typeName, wrapper)
	{
		if(!this._fieldData.hasOwnProperty(typeName))
		{
			return null;
		}

		var fieldData = this._fieldData[typeName];
		var fieldWrapper = BX.create("DIV", { attrs: { className: "order-detail-info-item" } });
		wrapper.appendChild(fieldWrapper);

		fieldWrapper.appendChild(
			BX.create("SPAN",
				{
					attrs: { className: "order-detail-info-item-name" },
					text: (BX.type.isNotEmptyString(fieldData["caption"]) ? fieldData["caption"] : typeName) + ":"
				}
			)
		);

		return BX.OrderQuickPanelMultiField.create("",
			{
				item: this._item,
				model: BX.OrderQuickPanelModel.create(typeName, { config: { data: fieldData["data"] } }),
				container: fieldWrapper
			}
		);
	};
	if(typeof(BX.OrderQuickPanelClientInfo.messages) === "undefined")
	{
		BX.OrderQuickPanelClientInfo.messages = {};
	}
	BX.OrderQuickPanelClientInfo.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelClientInfo();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelVisualEditor) === "undefined")
{
	BX.OrderQuickPanelVisualEditor = function()
	{
		BX.OrderQuickPanelVisualEditor.superclass.constructor.apply(this);
		this._wrapper = null;
		this._viewWrapper = null;
		this._editWrapper = null;
		this._isLoaded = false;
		this._editorName = "";
		this._editor = null;
		this._serviceUrl = "";
		this._editorHtmlLoadHandler = BX.delegate(this._onEditorHtmlLoaded, this);
		this._editorScriptLoadHandler = BX.delegate(this._onEditorScriptLoaded, this);
		this._editorContentSaveHandler = BX.delegate(this._onEditorContentSave, this);
		this._timeoutHandler = BX.delegate(this._onTimeout, this);

		this._enableModelSubscription = true;
		this._enableDocumentUnloadSubscription = true;
	};
	BX.extend(BX.OrderQuickPanelVisualEditor, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelVisualEditor.prototype.doInitialize = function()
	{
		this._isEditable = this._item.isEditable();
		this._serviceUrl = this._model.getDataParam("serviceUrl", "");
		if(this._serviceUrl === "")
		{
			throw "OrderQuickPanelVisualEditor: Could no find serviceUrl.";
		}

		if(this._hasLayout)
		{
			this._wrapper = BX.findChild(this._container, { tagName: "DIV", className: "order-lead-header-lhe-wrapper" }, true, false);
			this._viewWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-lhe-view-wrapper" }, true, false);
			this._editWrapper = BX.findChild(this._wrapper, { tagName: "DIV", className: "order-lead-header-lhe-edit-wrapper" }, true, false);
		}
	};
	BX.OrderQuickPanelVisualEditor.prototype.save = function()
	{
		this.saveIfChanged();
	};
	BX.OrderQuickPanelVisualEditor.prototype.saveIfChanged = function()
	{
		if(!this._editor)
		{
			return;
		}

		this._editor.SaveContent();
		var previous = this._model.getDataParam("html");
		var current = this._editor.GetContent();
		if(previous === current)
		{
			return;
		}

		this._model.setValue(current, true, this);
	};
	BX.OrderQuickPanelVisualEditor.prototype.isChanged = function()
	{
		this._editor.SaveContent();
		var previousHtml = this._model.getDataParam("html", "");
		var currentHtml = this._editor.GetContent();
		return previousHtml !== currentHtml;
	};
	BX.OrderQuickPanelVisualEditor.prototype.layout = function()
	{
		if(!this._hasLayout)
		{
			this._wrapper = BX.create("DIV", { attrs: { className: "order-lead-header-lhe-wrapper" } });
			this._container.appendChild(this._wrapper);

			this._viewWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-lhe-view-wrapper" } });
			this._wrapper.appendChild(this._viewWrapper);

			this._editWrapper = BX.create("DIV", { attrs: { className: "order-lead-header-lhe-edit-wrapper" } });
			this._wrapper.appendChild(this._editWrapper);
			this._hasLayout = true;
		}

		if(!this.isEditMode())
		{
			this._editWrapper.style.display = "none";
			if(this._viewWrapper.style.display === "none")
			{
				this._viewWrapper.style.display = "";
			}
			this._viewWrapper.innerHTML = this._model.getDataParam("html", "");
		}
		else
		{
			this.initializeEditor();
		}
	};
	BX.OrderQuickPanelVisualEditor.prototype.doClearLayout = function()
	{
		BX.cleanNode(this._wrapper);
	};
	BX.OrderQuickPanelVisualEditor.prototype.initializeEditor = function()
	{
		if(this._isLoaded)
		{
			this._viewWrapper.style.display = "none";
			if(this._editWrapper.style.display === "none")
			{
				this._editWrapper.style.display = "";
			}

			this._editor.ReInit(this._model.getDataParam("html", ""));
			window.setTimeout(this._timeoutHandler, 10000);
			return;
		}

		this._editorName = (this._item.getPrefix() + "_" + this._item.getId() + "_" + BX.util.getRandomString(4)).toUpperCase();

		BX.addCustomEvent("onAjaxSuccessFinish", this._editorScriptLoadHandler);
		BX.showWait(this._wrapper);
		BX.ajax(
			{
				url: this._serviceUrl,
				method: "POST",
				dataType: "html",
				data:
				{
					"MODE": "GET_VISUAL_EDITOR",
					"EDITOR_ID": this._editorName,
					"EDITOR_NAME": this._editorName
				},
				onsuccess: this._editorHtmlLoadHandler
			}
		);
	};
	BX.OrderQuickPanelVisualEditor.prototype.isOwnElement = function(element)
	{
		if(!element)
		{
			return false;
		}

		if(this._wrapper !== null && this._wrapper === BX.findParent(element, { className: "order-lead-header-lhe-wrapper" }))
		{
			return true;
		}

		//Skip popup window and overlay click
		return (BX.hasClass(element, "bx-core-window")
			|| BX.hasClass(element, "bx-core-dialog-overlay")
			|| !!(BX.findParent(element, { className: /(bx-core-window)|(bx-core-dialog-overlay)/ }))
		);
	};
	BX.OrderQuickPanelVisualEditor.prototype._onEditorHtmlLoaded = function(data)
	{
		BX.closeWait(this._wrapper);

		this._viewWrapper.style.display = "none";
		if(this._editWrapper.style.display === "none")
		{
			this._editWrapper.style.display = "";
		}
		this._editWrapper.appendChild(BX.create("DIV", { html: data  }));
		this._isLoaded = true;
	};
	BX.OrderQuickPanelVisualEditor.prototype._onEditorScriptLoaded = function(config)
	{
		if(config["url"] !== this._serviceUrl)
		{
			return;
		}

		BX.removeCustomEvent("onAjaxSuccessFinish", this._editorScriptLoadHandler);
		this.setupEditor();
	};
	BX.OrderQuickPanelVisualEditor.prototype.setupEditor = function()
	{
		if(typeof(window.JCLightHTMLEditor) ===  "undefined"
			|| typeof(window.JCLightHTMLEditor.items[this._editorName]) === "undefined")
		{
			window.setTimeout(BX.delegate(this.setupEditor, this), 500);
			return;
		}

		this._editor = window.JCLightHTMLEditor.items[this._editorName];
		this._editor.ReInit(this._model.getDataParam("html", ""));
		//BX.addCustomEvent(this._editor, "OnSaveContent", this._editorContentSaveHandler);
		window.setTimeout(this._timeoutHandler, 10000);
	};
	BX.OrderQuickPanelVisualEditor.prototype._onEditorContentSave = function()
	{
		if(this.isEditMode())
		{
			this.toggleMode();
		}
		BX.removeCustomEvent(this._editor, "OnSaveContent", this._editorContentSaveHandler);
	};
	BX.OrderQuickPanelVisualEditor.prototype._onTimeout = function()
	{
		if(!this._hasLayout || !this.isEditMode())
		{
			return;
		}

		this.saveIfChanged();
		window.setTimeout(this._timeoutHandler, 10000);
	};
	BX.OrderQuickPanelVisualEditor.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelVisualEditor();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelHeaderText) === "undefined")
{
	BX.OrderQuickPanelHeaderText = function()
	{
		BX.OrderQuickPanelHeaderText.superclass.constructor.apply(this);
		this._viewWrapper = null;
		this._editWrapper = null;
		this._input = null;

		this._enableModelSubscription = true;
		this._enableDocumentUnloadSubscription = true;
		this._keyDownHandler = BX.delegate(this._onKeyDown, this);
	};
	BX.extend(BX.OrderQuickPanelHeaderText, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelHeaderText.prototype.doInitialize = function()
	{
		this._isEditable = this._item.isEditable();

		this._viewWrapper = BX.findChild(this._container, { tagName: "SPAN",  className: "order-lead-header-title-text" }, true, false);
		this._editWrapper = BX.findChild(this._container, { tagName: "SPAN",  className: "order-lead-header-title-edit-wrapper" }, true, false);
		this._input = BX.findChild(this._container, { tagName: "INPUT", className: "order-header-lead-inp" }, true, false);

		if(this._isEditable)
		{
			this._input = BX.create("INPUT", { props: { type: "text" }, attrs: { className: "order-header-lead-inp" } });
			this._editWrapper.appendChild(this._input);
		}
	};
	BX.OrderQuickPanelHeaderText.prototype.layout = function()
	{
		var text = this._model.getValue();
		if(!this.isEditMode())
		{
			this._editWrapper.style.display = "none";
			if(this._viewWrapper.style.display === "none")
			{
				this._viewWrapper.style.display = "";
			}
			this._viewWrapper.innerHTML = BX.util.htmlspecialchars(text);

			BX.removeClass(this._container, "order-lead-header-title-editable");
			BX.unbind(this._input, "keydown", this._keyDownHandler);
		}
		else
		{
			this._viewWrapper.style.display = "none";
			if(this._editWrapper.style.display === "none")
			{
				this._editWrapper.style.display = "";
			}
			this._input.value = text;
			this._input.focus();
			BX.addClass(this._container, "order-lead-header-title-editable");
			BX.bind(this._input, "keydown", this._keyDownHandler);
		}
	};
	BX.OrderQuickPanelHeaderText.prototype.save = function()
	{
		this.saveIfChanged();
	};
	BX.OrderQuickPanelHeaderText.prototype.saveIfChanged = function()
	{
		var previous = this._model.getValue();
		var current = this._input.value;

		if(previous === current)
		{
			return;
		}

		this._model.setValue(current, true, this);
	};
	BX.OrderQuickPanelHeaderText.prototype.isOwnElement = function(element)
	{
		return this._container === BX.findParent(element, { className: "order-lead-header-title" });

	};
	BX.OrderQuickPanelHeaderText.prototype.isChanged = function()
	{
		return this._input.value !== this._model.getValue();
	};
	BX.OrderQuickPanelHeaderText.prototype._onKeyDown = function(e)
	{
		if(!this.isEditMode())
		{
			return;
		}

		e = e || window.event;
		if(e.keyCode === 13)
		{
			this.save();
			this.switchMode(BX.OrderQuickPanelControl.mode.view);
		}
		else if(e.keyCode === 27)
		{
			this.switchMode(BX.OrderQuickPanelControl.mode.view);
		}
	};
	BX.OrderQuickPanelHeaderText.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelHeaderText();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelHeaderMoney) === "undefined")
{
	BX.OrderQuickPanelHeaderMoney = function()
	{
		BX.OrderQuickPanelHeaderMoney.superclass.constructor.apply(this);
		this._viewWrapper = null;
		this._enableModelSubscription = true;
	};
	BX.extend(BX.OrderQuickPanelHeaderMoney, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelHeaderMoney.prototype.doInitialize = function()
	{
		this._isEditable = false;
		this._viewWrapper = BX.findChild(this._container, { className: "order-lead-header-status-sum-num" }, true, false);
	};
	BX.OrderQuickPanelHeaderMoney.prototype.layout = function()
	{
		this._viewWrapper.innerHTML = BX.util.htmlspecialchars(this._model.getFormattedValue(true));
	};
	BX.OrderQuickPanelHeaderMoney.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelHeaderMoney();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderQuickPanelAddress) === "undefined")
{
	BX.OrderQuickPanelAddress = function()
	{
		BX.OrderQuickPanelAddress.superclass.constructor.apply(this);
		this._id = "";
		this._settings = null;
		this._item = null;
		this._container = null;
		this._openPopupButton = null;
		this._openPopupHandler = BX.delegate(this._onOpenPopupButtonClick, this);
		this._isPopupShown = false;
		this._popup = null;
	};
	BX.extend(BX.OrderQuickPanelAddress, BX.OrderQuickPanelControl);
	BX.OrderQuickPanelAddress.prototype.doInitialize = function()
	{
		var lines = this._model.getDataParam("lines", []);
		if(lines.length === 0)
		{
			return;
		}

		this._openPopupButton = BX.findChild(this._container, { tagName: "SPAN", className: "order-item-tel-list" }, true, false);
		if(this._openPopupButton)
		{
			BX.bind(this._openPopupButton, "click", this._openPopupHandler);
		}
	};
	BX.OrderQuickPanelAddress.prototype.layout = function()
	{
		var lines = this._model.getDataParam("lines", []);
		if(lines.length === 0)
		{
			return;
		}

		var wrapper = null;
		if(this._item.getSection().getId() === "bottom")
		{
			wrapper = BX.create("DIV", { attrs: { className: "order-lead-header-lhe-wrapper" } });
			this._container.appendChild(wrapper);

			wrapper.appendChild(
				BX.create(
					"DIV",
					{
						attrs: { className: "order-lead-header-lhe-view-wrapper" },
						html: lines.join(", ")
					}
				)
			);
		}
		else
		{
			wrapper = BX.create("SPAN", { attrs: { className: "order-detail-info-item-text" } });
			this._container.appendChild(wrapper);
			wrapper.innerHTML += lines[0];
			if(lines.length > 1)
			{
				BX.addClass(wrapper, "order-detail-info-item-list");
				this._openPopupButton = BX.create(
					"SPAN",
					{
						attrs: { className: "order-item-tel-list" }
					}
				);
				wrapper.appendChild(this._openPopupButton);
				BX.bind(this._openPopupButton, "click", this._openPopupHandler);
			}
			BX.OrderQuickPanelAddress.adjustElement(wrapper);
		}
	};
	BX.OrderQuickPanelAddress.prototype._onOpenPopupButtonClick = function(e)
	{
		var lines = this._model.getDataParam("lines", []);
		if(lines.length <= 1)
		{
			return;
		}

		if(this._isPopupShown)
		{
			return;
		}

		var tab = BX.create('TABLE');
		tab.className = "order-lead-address-popup-table";
		tab.cellSpacing = '0';
		tab.cellPadding = '0';
		tab.border = '0';
		tab.style.display = "block";

		for(var i = 0; i < lines.length; i++)
		{
			var r = tab.insertRow(-1);
			var c = r.insertCell(-1);
			c.className = "order-lead-address-popup-text";
			c.innerHTML = lines[i];
		}

		this._popup = new BX.PopupWindow(
			this._id,
			this._openPopupButton,
			{
				autoHide: true,
				draggable: false,
				offsetLeft: -80,
				offsetTop: 5,
				angle : { offset : 80 },
				bindOptions: { forceBindPosition: true },
				closeByEsc: true,
				zIndex: -10,
				events: { onPopupClose: BX.delegate(this._onPopupClose, this) },
				content: tab
			}
		);

		this._popup.show();
		this._isPopupShown = true;
	};
	BX.OrderQuickPanelAddress.prototype._onPopupClose = function(e)
	{
		if(this._popup)
		{
			this._popup.destroy();
			this._popup = null;
			this._isPopupShown = false;
		}
	};
	BX.OrderQuickPanelAddress.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelAddress();
		self.initialize(id, settings);
		return self;
	};
	BX.OrderQuickPanelAddress._wrapper = null;
	BX.OrderQuickPanelAddress.setWrapper = function(wrapper)
	{
		this._wrapper = wrapper;
		this.adjust();
		BX.bind(window, "resize", BX.delegate(BX.OrderQuickPanelAddress.onWindowResize, this));
	};
	BX.OrderQuickPanelAddress.onWindowResize = function(e)
	{
		this.adjust();
	};
	BX.OrderQuickPanelAddress.adjust = function()
	{
		if(!this._wrapper || !BX.type.isFunction(cssQuery))
		{
			return;
		}

		var maxWidth = BX.OrderQuickPanelAddress.calculateMaxElementWidth();
		if(maxWidth <= 0)
		{
			return;
		}

		var elements = cssQuery(".order-detail-info-item-text", this._wrapper);
		for(var i = 0; i < elements.length; i++)
		{
			elements[i].style.maxWidth =  maxWidth + 'px';
		}
	};
	BX.OrderQuickPanelAddress.adjustElement = function(element)
	{
		if(!this._wrapper)
		{
			return;
		}

		var maxWidth = BX.OrderQuickPanelAddress.calculateMaxElementWidth();
		if(maxWidth > 0)
		{
			element.style.maxWidth = maxWidth + 'px';
		}
	};
	BX.OrderQuickPanelAddress.calculateMaxElementWidth = function()
	{
		return this._wrapper ? Math.ceil(65 * (this._wrapper.offsetWidth  / 3 - 30) / 100) : 0;
	};
}
//BX.OrderQuickPanelModel
if(typeof(BX.OrderQuickPanelModel) === "undefined")
{
	BX.OrderQuickPanelModel = function()
	{
		this._id = "";
		this._settings = {};
		this._config = null;
		this._data = null;
		this._callbacks = [];
		this._instantEditor = null;
	};
	BX.OrderQuickPanelModel.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : BX.util.getRandomString(8);
			this._settings = settings ? settings : {};

			this._config = this.getSetting("config");
			if(!this._config)
			{
				this._config = {};
			}

			this._data = this.getConfigParam("data");
			if(!this._data)
			{
				this._data = {};
			}
			this.doInitialize();
		},
		doInitialize: function()
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
		getType: function()
		{
			return this.getConfigParam("type", "text");
		},
		getCaption: function()
		{
			return this.getConfigParam("caption", this._id);
		},
		getMessage: function(name)
		{
			var m = BX.OrderQuickPanelModel.messages;
			return m.hasOwnProperty(name) ? m[name] : name;
		},
		isCaptionEnabled: function()
		{
			return this.getConfigParam("enableCaption", true);
		},
		isEditable: function()
		{
			return this.getConfigParam("editable", false);
		},
		registerCallback: function(callback)
		{
			if(!BX.type.isFunction(callback))
			{
				return;
			}

			for(var i = 0; i < this._callbacks.length; i++)
			{
				if(this._callbacks[i] === callback)
				{
					return;
				}
			}
			this._callbacks.push(callback);
		},
		unregisterCallback: function(callback)
		{
			if(!BX.type.isFunction(callback))
			{
				return;
			}

			for(var i = 0; i < this._callbacks.length; i++)
			{
				if(this._callbacks[i] === callback)
				{
					this._callbacks.splice(i, 1);
					return;
				}
			}
		},
		notify: function(params)
		{
			for(var i = 0; i < this._callbacks.length; i++)
			{
				this._callbacks[i](this, params);
			}
		},
		getConfigParam: function(name, defaultval)
		{
			return this._config.hasOwnProperty(name) ? this._config[name] : defaultval;
		},
		setConfigParam: function(name, val)
		{
			this._config[name] = val;
		},
		getData: function()
		{
			return this._data;
		},
		getDataParam: function(name, defaultval)
		{
			return this._data.hasOwnProperty(name) ? this._data[name] : defaultval;
		},
		setDataParam: function(name, val)
		{
			this._data[name] = val;
		},
		getInstantEditor: function()
		{
			return this._instantEditor;
		},
		setInstantEditor: function(instantEditor)
		{
			this._instantEditor = instantEditor;
		},
		getValue: function()
		{
			throw "The 'getValue' must be implemented.";
		},
		setValue: function(value, save, source)
		{
			throw "The 'setValue' must be implemented.";
		},
		saveFieldValue: function()
		{
			var editor = this.getInstantEditor();
			if(editor)
			{
				editor.saveFieldValue(this._id, this.getValue());
			}
		},
		processEditorFieldValueSave: function(name, value)
		{
		}
	};
	BX.OrderQuickPanelModel.items = {};
	BX.OrderQuickPanelModel.getItem = function(id)
	{
		return this.items.hasOwnProperty(id) ? this.items[id] : null;
	};
	if(typeof(BX.OrderQuickPanelModel.messages) === "undefined")
	{
		BX.OrderQuickPanelModel.messages = {};
	}
	BX.OrderQuickPanelModel.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelModel();
		self.initialize(id, settings);
		BX.OrderQuickPanelModel.items[id] = self;
		return self;
	};
}
if(typeof(BX.OrderQuickPanelTextModel) === "undefined")
{
	BX.OrderQuickPanelTextModel = function()
	{
		BX.OrderQuickPanelTextModel.superclass.constructor.apply(this);
	};
	BX.extend(BX.OrderQuickPanelTextModel, BX.OrderQuickPanelModel);
	BX.OrderQuickPanelTextModel.prototype.getValue = function()
	{
		return this.getDataParam("text", "");
	};
	BX.OrderQuickPanelTextModel.prototype.setValue = function(value, save, source)
	{
		this.setDataParam("text", value);

		if(!!save)
		{
			this.saveFieldValue();
		}
		this.notify({ source: source });
	};
	BX.OrderQuickPanelTextModel.prototype.processEditorFieldValueSave = function(name, value)
	{
		if(name === this._id)
		{
			if(this.getDataParam("text", "") == value)
			{
				return;
			}

			this.setDataParam("text", value);
			this.notify({ source: null });
		}
	};
	BX.OrderQuickPanelTextModel.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelTextModel();
		self.initialize(id, settings);
		BX.OrderQuickPanelModel.items[id] = self;
		return self;
	};
}
if(typeof(BX.OrderQuickPanelBooleanModel) === "undefined")
{
	BX.OrderQuickPanelBooleanModel = function()
	{
		BX.OrderQuickPanelBooleanModel.superclass.constructor.apply(this);
		this._baseType = "";
	};
	BX.extend(BX.OrderQuickPanelBooleanModel, BX.OrderQuickPanelModel);
	BX.OrderQuickPanelBooleanModel.prototype.doInitialize = function()
	{
		this._baseType = this.getDataParam("baseType");
		if(this._baseType === "char")
		{
			this.setDataParam("value", this.getDataParam("value", "N") === "Y" ? "Y" : "N");
		}
		else
		{
			this.setDataParam("value", parseInt(this.getDataParam("value", 0)) > 0 ? 1 : 0);
		}
	};
	BX.OrderQuickPanelBooleanModel.prototype.getBaseType = function()
	{
		return this._baseType;
	};
	BX.OrderQuickPanelBooleanModel.prototype.getValue = function()
	{
		return this.getDataParam("value", "");
	};
	BX.OrderQuickPanelBooleanModel.prototype.setValue = function(value, save, source)
	{
		if(this._baseType === "char")
		{
			value =  value ? "Y" : "N";
		}
		else
		{
			value =  value ? 1 : 0;
		}

		this.setDataParam("value", value);

		if(!!save)
		{
			this.saveFieldValue();
		}
		this.notify({ source: source });
	};
	BX.OrderQuickPanelBooleanModel.prototype.processEditorFieldValueSave = function(name, value)
	{
		if(name === this._id)
		{
			if(this.getDataParam("value", "") == value)
			{
				return;
			}

			this.setDataParam("value", value);
			this.notify({ source: null });
		}
	};
	BX.OrderQuickPanelBooleanModel.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelBooleanModel();
		self.initialize(id, settings);
		BX.OrderQuickPanelModel.items[id] = self;
		return self;
	};
}
if(typeof(BX.OrderQuickPanelEnumerationModel) === "undefined")
{
	BX.OrderQuickPanelEnumerationModel = function()
	{
		BX.OrderQuickPanelEnumerationModel.superclass.constructor.apply(this);
	};
	BX.extend(BX.OrderQuickPanelEnumerationModel, BX.OrderQuickPanelModel);
	BX.OrderQuickPanelEnumerationModel.prototype.doInitialize = function()
	{
		this._data["items"] = BX.type.isArray(this._data["items"]) ? this._data["items"] : [];
		this._data["value"] = BX.type.isNotEmptyString(this._data["value"]) ? this._data["value"] : "";
		this._data["text"] = this.getItemText(this._data["value"]);
	};
	BX.OrderQuickPanelEnumerationModel.prototype.getValue = function()
	{
		return this.getDataParam("value", "");
	};
	BX.OrderQuickPanelEnumerationModel.prototype.setValue = function(value, save, source)
	{
		this.setDataParam("value", value);
		this.setDataParam("text", this.getItemText(value));

		if(!!save)
		{
			this.saveFieldValue();
		}
		this.notify({ source: source });
	};
	BX.OrderQuickPanelEnumerationModel.prototype.getItemIndex = function(val)
	{
		if(val === "")
		{
			return 0;
		}

		var items = this._data["items"];
		for(var i = 0; i < items.length; i++)
		{
			var item = items[i];
			var id = typeof(item["ID"]) !== "undefined" ? item["ID"] : "";
			if(id === val)
			{
				return i;
			}

		}
		return 0;
	};
	BX.OrderQuickPanelEnumerationModel.prototype.getItemText = function(val)
	{
		if(val === "")
		{
			return this.getMessage("notSelected");
		}

		var items = this._data["items"];
		for(var i = 0; i < items.length; i++)
		{
			var item = items[i];
			var id = typeof(item["ID"]) !== "undefined" ? item["ID"] : "";
			if(id === val)
			{
				return typeof(item["VALUE"]) !== "undefined" ? item["VALUE"] : "";
			}

		}

		return this.getMessage("notSelected");
	};
	BX.OrderQuickPanelEnumerationModel.prototype.getItems = function()
	{
		return this._data["items"];
	};
	BX.OrderQuickPanelEnumerationModel.prototype.processEditorFieldValueSave = function(name, value)
	{
		if(name === this._id)
		{
			if(this.getDataParam("value", "") == value)
			{
				return;
			}

			this.setDataParam("value", value);
			this.setDataParam("text", this.getItemText(value));
			this.notify({ source: null });
		}
	};
	BX.OrderQuickPanelEnumerationModel.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelEnumerationModel();
		self.initialize(id, settings);
		BX.OrderQuickPanelModel.items[id] = self;
		return self;
	};
}
if(typeof(BX.OrderQuickPanelMoneyModel) === "undefined")
{
	BX.OrderQuickPanelMoneyModel = function()
	{
		BX.OrderQuickPanelMoneyModel.superclass.constructor.apply(this);
	};
	BX.extend(BX.OrderQuickPanelMoneyModel, BX.OrderQuickPanelModel);
	BX.OrderQuickPanelMoneyModel.prototype.doInitialize = function()
	{
		this._data["value"] = BX.type.isNotEmptyString(this._data["value"]) ? this._data["value"] : "0.00";
		this._data["text"] = BX.type.isNotEmptyString(this._data["text"]) ? this._data["text"] : this._data["value"];
		this._data["currencyId"] = BX.type.isNotEmptyString(this._data["currencyId"]) ? this._data["currencyId"] : "";
		this._data["currencyFieldName"] = BX.type.isNotEmptyString(this._data["currencyFieldName"]) ? this._data["currencyFieldName"] : "CURRENCY_ID";

		if(!BX.type.isNotEmptyString(this._data["serviceUrl"]))
		{
			throw "OrderQuickPanelMoneyModel: Could no find serviceUrl.";
		}
	};
	BX.OrderQuickPanelMoneyModel.prototype.getFormattedValue = function(enableCurrency)
	{
		return this.getDataParam(
			!!enableCurrency ? "formatted_sum_with_currency" : "formatted_sum",
			""
		);
	};
	BX.OrderQuickPanelMoneyModel.prototype.getValue = function()
	{
		return this.getDataParam("value", "");
	};
	BX.OrderQuickPanelMoneyModel.prototype.setValue = function(value, save, source)
	{
		this.setDataParam("value", value);
		this.setDataParam("text", value);
		this.setDataParam("formatted_sum", value);
		this.setDataParam("formatted_sum_with_currency", value);

		if(!!save)
		{
			this.saveFieldValue();
		}
		//this.notify({ source: source });
		this.startMoneyFormatRequest();
	};
	BX.OrderQuickPanelMoneyModel.prototype.startMoneyFormatRequest = function()
	{
		BX.ajax(
			{
				url: this._data["serviceUrl"],
				method: "POST",
				dataType: "json",
				data:
				{
					"MODE": "GET_FORMATTED_SUM",
					"CURRENCY_ID": this._data["currencyId"],
					"SUM": this._data["value"]
				},
				onsuccess: BX.delegate(this.onMoneyFormatRequestSuccess, this)
			}
		);
	};
	BX.OrderQuickPanelMoneyModel.prototype.onMoneyFormatRequestSuccess = function(data)
	{
		this._data["formatted_sum"] = this._data["text"] = BX.type.isNotEmptyString(data["FORMATTED_SUM"])
			? data["FORMATTED_SUM"] : "";

		this._data["formatted_sum_with_currency"] = BX.type.isNotEmptyString(data["FORMATTED_SUM_WITH_CURRENCY"])
			? data["FORMATTED_SUM_WITH_CURRENCY"] : "";

		this.notify({ source: null });
	};
	BX.OrderQuickPanelMoneyModel.prototype.processEditorFieldValueSave = function(name, value)
	{
		if(name === this._id)
		{
			if(this.getDataParam("value", "") == value)
			{
				return;
			}

			this.setDataParam("value", value);
			this.setDataParam("text", value);
			this.setDataParam("formatted_sum", value);
			this.setDataParam("formatted_sum_with_currency", value);

			//this.notify({ source: null });
			this.startMoneyFormatRequest();
		}
		else if(name === this._data["currencyFieldName"])
		{
			if(this._data["currencyId"] == value)
			{
				return;
			}

			this._data["currencyId"] = value;
			//this.notify({ source: null });
			this.startMoneyFormatRequest();
		}
	};
	BX.OrderQuickPanelMoneyModel.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelMoneyModel();
		self.initialize(id, settings);
		BX.OrderQuickPanelModel.items[id] = self;
		return self;
	};
}
if(typeof(BX.OrderQuickPanelHtmlModel) === "undefined")
{
	BX.OrderQuickPanelHtmlModel = function()
	{
		BX.OrderQuickPanelHtmlModel.superclass.constructor.apply(this);
	};
	BX.extend(BX.OrderQuickPanelHtmlModel, BX.OrderQuickPanelModel);
	BX.OrderQuickPanelHtmlModel.prototype.doInitialize = function()
	{
		this._data["html"] = BX.type.isNotEmptyString(this._data["html"]) ? this._data["html"] : "";
	};
	BX.OrderQuickPanelHtmlModel.prototype.getValue = function()
	{
		return this.getDataParam("html", "");
	};
	BX.OrderQuickPanelHtmlModel.prototype.setValue = function(value, save, source)
	{
		this.setDataParam("html", value);

		if(!!save)
		{
			this.saveFieldValue();
		}
		this.notify({ source: source });
	};
	BX.OrderQuickPanelHtmlModel.prototype.processEditorFieldValueSave = function(name, value)
	{
		if(name === this._id)
		{
			if(this.getDataParam("html", "") == value)
			{
				return;
			}

			this.setDataParam("html", value);
			this.notify({ source: null });
		}
	};
	BX.OrderQuickPanelHtmlModel.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelHtmlModel();
		self.initialize(id, settings);
		BX.OrderQuickPanelModel.items[id] = self;
		return self;
	};
}
//BX.OrderQuickPanelItemPlaceholder
if(typeof(BX.OrderQuickPanelItemPlaceholder) === "undefined")
{
	BX.OrderQuickPanelItemPlaceholder = function()
	{
		this._settings = null;
		this._container = null;
		this._node = null;
		this._section = null;
		this._isDragOver = false;
		this._isActive = false;
		this._index = -1;
		this._timeoutId = null;
	};
	BX.OrderQuickPanelItemPlaceholder.prototype =
	{
		initialize: function(settings)
		{
			this._settings = settings ? settings : {};
			this._container = this.getSetting("container", null);
			this._section = this.getSetting("section", null);
			this._isActive = this.getSetting("isActive", false);
			this._index = parseInt(this.getSetting("index", -1));
		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		getContainer: function()
		{
			return this._container;
		},
		setContainer: function(container)
		{
			this._container = container;
		},
		isDragOver: function()
		{
			return this._isDragOver;
		},
		isActive: function()
		{
			return this._isActive;
		},
		setActive: function(active, interval)
		{
			if(this._timeoutId !== null)
			{
				window.clearTimeout(this._timeoutId);
				this._timeoutId = null;
			}

			interval = parseInt(interval);
			if(interval > 0)
			{
				var self = this;
				window.setTimeout(function(){ if(self._timeoutId === null) return; self._timeoutId = null; self.setActive(active, 0); }, interval);
				return;
			}

			active = !!active;
			if(this._isActive === active)
			{
				return;
			}

			this._isActive = active;
			if(this._node)
			{
				this._node.className = active ? "order-lead-header-drag-zone-bd" : "order-lead-header-drag-zone-bd-inactive";
			}
		},
		getIndex: function()
		{
			return this._index;
		},
		layout: function()
		{
			if(!this._container)
			{
				throw "OrderQuickPanelItemPlaceholder: The 'container' is not assigned.";
			}

			var row = this._container;
			var cell = row.insertCell(-1);
			cell.className = "order-lead-header-drag-zone";
			cell.colSpan = 4;
			this._node = BX.create("DIV", { attrs: { className: this._isActive ? "order-lead-header-drag-zone-bd" : "order-lead-header-drag-zone-bd-inactive" } });
			cell.appendChild(this._node);

			BX.bind(row, "dragover", BX.delegate(this._onDragOver, this));
			BX.bind(row, "dragleave", BX.delegate(this._onDragLeave, this));
		},
		_onDragOver: function(e)
		{
			e = e || window.event;
			this._isDragOver = true;
			return BX.eventReturnFalse(e);
		},
		_onDragLeave: function(e)
		{
			e = e || window.event;
			this._isDragOver = false;
			return BX.eventReturnFalse(e);
		}
	};
	BX.OrderQuickPanelItemPlaceholder.create = function(settings)
	{
		var self = new BX.OrderQuickPanelItemPlaceholder();
		self.initialize(settings);
		return self;
	};
}
//BX.OrderQuickPanelSection
if(typeof(BX.OrderQuickPanelSection) === "undefined")
{
	BX.OrderQuickPanelSection = function()
	{
		this._id = "";
		this._settings = null;
		this._view = null;
		this._items = [];
		this._container = null;
		this._placeHolder = null;
		this._dragDropContainerId = "";
		this._dragSection = null;
	};
	BX.OrderQuickPanelSection.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : "";
			this._settings = settings ? settings : {};

			this._view = this.getSetting("view");
			if(!this._view)
			{
				throw "OrderQuickPanelSection: The 'view' parameter is not defined in settings.";
			}

			this._container = this.getSetting("container");
			if(!this._container)
			{
				throw "OrderQuickPanelSection: The 'container' parameter is not defined in settings.";
			}

			this.initializeFromConfig(this.getSetting("config", null));

			this._dragSection = BX.OrderQuickPanelSectionDragContainer.create(
				this.getId(),
				{
					section: this,
					view: this._view,
					node: BX.findParent(this._container, { tagName: "TD", className: "order-lead-header-cell" })
				}
			);
			this._dragSection.addDragFinishListener(this._view.getItemDropCallback());
		},
		initializeFromConfig: function(config)
		{
			if(!BX.type.isArray(config))
			{
				return;
			}

			var prefix = this.getPrefix() + "_" + this.getId();
			for(var i = 0; i < config.length; i++)
			{
				var id = config[i];
				var model = this._view.getFieldModel(id);
				var container = BX(prefix + "_" + id.toLowerCase());
				if(model && container)
				{
					var item = BX.OrderQuickPaneSectionItem.create(id, { section: this, model: model, container: container, hasLayout: true, prefix: prefix });
					this._items.push(item);
				}
			}
		},
		getPrefix: function()
		{
			return this.getSetting("prefix", "");
		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		getId: function()
		{
			return this._id;
		},
		getContainer: function()
		{
			return this._container;
		},
		getView: function()
		{
			return this._view;
		},
		getItems: function()
		{
			return this._items;
		},
		getItemCount: function()
		{
			return this._items.length;
		},
		getItemByIndex: function(index)
		{
			return index >= 0 && index < this._items.length ? this._items[index] : null;
		},
		createItem: function(id, model)
		{
			var item = BX.OrderQuickPaneSectionItem.create(id, { model: model });
			this.addItem(item);
			item.layout();
			return item;
		},
		addItem: function(item)
		{
			var index = -1;
			if(!item.getContainer())
			{
				var row = null;
				if(!this._placeHolder)
				{
					row = this._container.insertRow(-1);
				}
				else
				{
					row = this._placeHolder.getContainer();
					index = row.rowIndex;
					BX.cleanNode(row, false);
					this._placeHolder = null;
				}
				item.setContainer(row);
			}
			item.setSection(this);
			item.setPrefix(this.getPrefix() + "_" + this.getId());


			if(index >= 0)
			{
				this._items.splice(index, 0, item);
			}
			else
			{
				this._items.push(item);
			}
		},
		moveItem: function(item, index)
		{
			var qty = this.getItemCount();
			if(index < 0  || index > qty)
			{
				index = qty;
			}

			var currentIndex = this.findItemIndex(item);
			if(currentIndex < 0 || currentIndex === index || (currentIndex === (qty - 1) && index === qty))
			{
				return false;
			}

			var rowIndex = index;
			var currentRowIndex = item.getContainer().rowIndex;
			if(currentRowIndex < rowIndex)
			{
				rowIndex--;
			}

			item.clearLayout();
			this._container.deleteRow(currentRowIndex);
			this._items.splice(currentIndex, 1);
			if(currentIndex < index)
			{
				index--;
			}

			item.setContainer(this._container.insertRow(rowIndex));
			item.layout();
			this._items.splice(index, 0, item);

			return true;
		},
		deleteItem: function(item)
		{
			var index = this.findItemIndex(item);
			if(index < 0)
			{
				return;
			}

			this._items.splice(index, 1);
			item.clearLayout();
			this._container.deleteRow(item.getContainer().rowIndex);
		},
		createPlaceHolder: function(index)
		{
			var qty = this.getItemCount();
			if(index < 0 || index > qty)
			{
				index = qty > 0 ? qty : 0;
			}

			if(this._placeHolder)
			{
				if(this._placeHolder.getIndex() === index)
				{
					return this._placeHolder;
				}

				this._container.deleteRow(this._placeHolder.getContainer().rowIndex);
				this._placeHolder = null;
			}

			this._placeHolder = BX.OrderQuickPanelItemPlaceholder.create(
				{
					section: this,
					container: this._container.insertRow(index === qty ? -1 : index),
					index: index
				}
			);
			this._placeHolder.layout();
			return this._placeHolder;
		},
		hasPlaceHolder: function()
		{
			return !!this._placeHolder;
		},
		getPlaceHolder: function()
		{
			return this._placeHolder;
		},
		getPlaceHolderRowIndex: function()
		{
			return this._placeHolder ? this._placeHolder.getContainer().rowIndex : -1;
		},
		removePlaceHolder: function()
		{
			if(this._placeHolder)
			{
				this._container.deleteRow(this._placeHolder.getContainer().rowIndex);
				this._placeHolder = null;
			}
		},
		hidePlaceHolder: function()
		{
			if(this._items.length === 0 && this._placeHolder)
			{
				this._placeHolder.setActive(false);
			}
			else if(this._placeHolder)
			{
				this._container.deleteRow(this._placeHolder.getContainer().rowIndex);
				this._placeHolder = null;
			}
		},
		getDragEnterCallback: function()
		{
			return BX.delegate(this._onDragEnter, this);
		},
		getDragDropContainerId: function()
		{
			return this._dragDropContainerId;
		},
		setDragDropContainerId: function(containerId)
		{
			this._dragDropContainerId = containerId;
		},
		findItemIndex: function(item)
		{
			for(var i = 0; i < this._items.length; i++)
			{
				if(item === this._items[i])
				{
					return i;
				}
			}

			return -1;
		},
		findItemById: function(id)
		{
			for(var i = 0; i < this._items.length; i++)
			{
				var item = this._items[i];
				if(item.getId() === id)
				{
					return item;
				}
			}

			return null;
		},
		processItemDeletion: function(item)
		{
			this.deleteItem(item);
			this._view.processSectionItemDeletion(this, item);
		}
	};
	BX.OrderQuickPanelSection.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelSection();
		self.initialize(id, settings);
		return self;
	};
}

//D&D Items
if(typeof(BX.OrderQuickPanelSectionDragItem) === "undefined")
{
	BX.OrderQuickPanelSectionDragItem = function()
	{
		BX.OrderQuickPanelSectionDragItem.superclass.constructor.apply(this);
		this._item = null;
		this._showItemInDragMode = true;
	};
	BX.extend(BX.OrderQuickPanelSectionDragItem, BX.OrderCustomDragItem);
	BX.OrderQuickPanelSectionDragItem.prototype.doInitialize = function()
	{
		this._item = this.getSetting("item");
		if(!this._item)
		{
			throw "OrderQuickPanelSectionDragItem: The 'item' parameter is not defined in settings or empty.";
		}

		this._showItemInDragMode = this.getSetting("showItemInDragMode", true);
	};
	BX.OrderQuickPanelSectionDragItem.prototype.getItem = function()
	{
		return this._item;
	};
	BX.OrderQuickPanelSectionDragItem.prototype.createGhostNode = function()
	{
		if(this._ghostNode)
		{
			return this._ghostNode;
		}

		this._ghostNode = this._item.createGhostNode();
		document.body.appendChild(this._ghostNode);
	};
	BX.OrderQuickPanelSectionDragItem.prototype.removeGhostNode = function()
	{
		if(this._ghostNode)
		{
			document.body.removeChild(this._ghostNode);
			this._ghostNode = null;
		}
	};
	BX.OrderQuickPanelSectionDragItem.prototype.getContextId = function()
	{
		return BX.OrderQuickPanelSectionDragItem.contextId;
	};
	BX.OrderQuickPanelSectionDragItem.prototype.getContextData = function()
	{
		return ({ contextId: BX.OrderQuickPanelSectionDragItem.contextId, item: this._item });
	};
	BX.OrderQuickPanelSectionDragItem.prototype.processDragStart = function()
	{
		if(!this._showItemInDragMode)
		{
			this._item.getContainer().style.display = "none";
		}
		BX.OrderQuickPanelSectionDragContainer.refresh();
	};
	BX.OrderQuickPanelSectionDragItem.prototype.processDragStop = function()
	{
		if(!this._showItemInDragMode)
		{
			this._item.getContainer().style.display = "";
		}
		BX.OrderQuickPanelSectionDragContainer.refreshAfter(300);
	};
	BX.OrderQuickPanelSectionDragItem.contextId = "quick_panel_section_item";
	BX.OrderQuickPanelSectionDragItem.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelSectionDragItem();
		self.initialize(id, settings);
		return self;
	};
}
//D&D Containers
if(typeof(BX.OrderQuickPanelSectionDragContainer) === "undefined")
{
	BX.OrderQuickPanelSectionDragContainer = function()
	{
		BX.OrderQuickPanelSectionDragContainer.superclass.constructor.apply(this);
		this._section = null;
	};
	BX.extend(BX.OrderQuickPanelSectionDragContainer, BX.OrderCustomDragContainer);
	BX.OrderQuickPanelSectionDragContainer.prototype.doInitialize = function()
	{
		this._section = this.getSetting("section");
		if(!this._section)
		{
			throw "OrderQuickPanelSectionDragContainer: The 'section' parameter is not defined in settings or empty.";
		}

		this._view = this.getSetting("view");
		if(!this._view)
		{
			throw "OrderQuickPanelSectionDragContainer: The 'view' parameter is not defined in settings or empty.";
		}
	};
	BX.OrderQuickPanelSectionDragContainer.prototype.getSection = function()
	{
		return this._section;
	};
	BX.OrderQuickPanelSectionDragContainer.prototype.createPlaceHolder = function(pos)
	{
		var rect;
		var placeholder = this._section.getPlaceHolder();
		if(placeholder)
		{
			rect = BX.pos(placeholder.getContainer());
			if(pos.y >= rect.top && pos.y <= rect.bottom)
			{
				if(!placeholder.isActive())
				{
					placeholder.setActive(true);
				}
				return;
			}
		}

		var items = this._section._items;
		for(var i = 0; i < items.length; i++)
		{
			rect = BX.pos(items[i].getContainer());
			if(pos.y >= rect.top && pos.y <= rect.bottom)
			{
				this._section.createPlaceHolder(
					(rect.top  + (rect.height / 2) - pos.y) >= 0 ? i : (i + 1)
				).setActive(true);
				return;
			}
		}

		this._section.createPlaceHolder(-1).setActive(true);
		this.refresh();
	};
	BX.OrderQuickPanelSectionDragContainer.prototype.removePlaceHolder = function()
	{
		if(!this._section.hasPlaceHolder())
		{
			return;
		}

		if(this._section.getItemCount() > 0)
		{
			this._section.removePlaceHolder();
		}
		else
		{
			this._section.getPlaceHolder().setActive(false);
		}
		this.refresh();
	};
	BX.OrderQuickPanelSectionDragContainer.prototype.isAllowedContext = function(contextId)
	{
		return (contextId === BX.OrderQuickPanelSectionDragItem.contextId
			|| this._view.isAllowedDragContext(contextId));
	};
	BX.OrderQuickPanelSectionDragContainer.refresh = function()
	{
		for(var k in this.items)
		{
			if(this.items.hasOwnProperty(k))
			{
				this.items[k].refresh();
			}
		}
	};
	BX.OrderQuickPanelSectionDragContainer.refreshAfter = function(interval)
	{
		interval = parseInt(interval);
		if(interval > 0)
		{
			window.setTimeout(function() { BX.OrderQuickPanelSectionDragContainer.refresh(); }, interval);
		}
		else
		{
			this.refresh();
		}
	};
	BX.OrderQuickPanelSectionDragContainer.items = {};
	BX.OrderQuickPanelSectionDragContainer.create = function(id, settings)
	{
		var self = new BX.OrderQuickPanelSectionDragContainer();
		self.initialize(id, settings);
		this.items[self.getId()] = self;
		return self;
	};
}