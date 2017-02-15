if(typeof(BX.OrderEntityAccessManager) == "undefined")
{
	BX.OrderEntityAccessManager = function()
	{
		this._id = "";
		this._settings = {};
		this._serviceUrl = "";
		this._processDialogs = {};
	};

	BX.OrderEntityAccessManager.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : "order_entity_acc_mgr_" + Math.random().toString().substring(2);
			this._settings = settings ? settings : {};

			this._serviceUrl = this.getSetting("serviceUrl", "");
			if(!BX.type.isNotEmptyString(this._serviceUrl))
			{
				throw "BX.OrderEntityAccessManager. Could not find service url.";
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
		getMessage: function(name)
		{
			return BX.OrderEntityAccessManager.messages && BX.OrderEntityAccessManager.messages.hasOwnProperty(name) ? BX.OrderEntityAccessManager.messages[name] : "";
		},
		rebuildCompanyAttrs: function()
		{
			this._rebuildEntityAttrs("COMPANY");
		},
		rebuildContactAttrs: function()
		{
			this._rebuildEntityAttrs("CONTACT");
		},
		rebuildDealAttrs: function()
		{
			this._rebuildEntityAttrs("DEAL");
		},
		rebuildLeadAttrs: function()
		{
			this._rebuildEntityAttrs("LEAD");
		},
		rebuildQuoteAttrs: function()
		{
			this._rebuildEntityAttrs("QUOTE");
		},
		rebuildInvoiceAttrs: function()
		{
			this._rebuildEntityAttrs("INVOICE");
		},
		_rebuildEntityAttrs: function(entityTypeName)
		{
			var entityTypeNameU = entityTypeName.toUpperCase();
			var entityTypeNameC = entityTypeName.toLowerCase().replace(/(?:^)\S/, function(c){ return c.toUpperCase(); });
			var key = "rebuild" + entityTypeNameC + "AccessAttrs";

			var processDlg = null;
			if(typeof(this._processDialogs[key]) !== "undefined")
			{
				processDlg = this._processDialogs[key];
			}
			else
			{
				processDlg = BX.OrderLongRunningProcessDialog.create(
					key,
					{
						serviceUrl: this._serviceUrl,
						action:"REBUILD_ENTITY_ATTRS",
						params:{ "ENTITY_TYPE_NAME": entityTypeNameU },
						title: this.getMessage(key + "DlgTitle"),
						summary: this.getMessage(key + "DlgSummary")
					}
				);

				this._processDialogs[key] = processDlg;
				BX.addCustomEvent(processDlg, 'ON_STATE_CHANGE', BX.delegate(this._onProcessStateChange, this));
			}
			processDlg.show();
		},
		_onProcessStateChange: function(sender)
		{
			var key = sender.getId();
			if(typeof(this._processDialogs[key]) !== "undefined")
			{
				var processDlg = this._processDialogs[key];
				if(processDlg.getState() === BX.OrderLongRunningProcessState.completed)
				{
					var p = processDlg.getParams();
					var typeName = BX.type.isNotEmptyString(p["ENTITY_TYPE_NAME"]) ? p["ENTITY_TYPE_NAME"] : "";
					if(typeName === "COMPANY")
					{
						BX.onCustomEvent(this, 'ON_COMPANY_ATTRS_REBUILD_COMPLETE', [this]);
					}
					else if(typeName === "CONTACT")
					{
						BX.onCustomEvent(this, 'ON_CONTACT_ATTRS_REBUILD_COMPLETE', [this]);
					}
					else if(typeName === "DEAL")
					{
						BX.onCustomEvent(this, 'ON_DEAL_ATTRS_REBUILD_COMPLETE', [this]);
					}
					else if(typeName === "LEAD")
					{
						BX.onCustomEvent(this, 'ON_LEAD_ATTRS_REBUILD_COMPLETE', [this]);
					}
					else if(typeName === "QUOTE")
					{
						BX.onCustomEvent(this, 'ON_QUOTE_ATTRS_REBUILD_COMPLETE', [this]);
					}
					else if(typeName === "INVOICE")
					{
						BX.onCustomEvent(this, 'ON_INVOICE_ATTRS_REBUILD_COMPLETE', [this]);
					}
				}
			}
		}
	};

	if(typeof(BX.OrderEntityAccessManager.messages) == "undefined")
	{
		BX.OrderEntityAccessManager.messages = {};
	}

	BX.OrderEntityAccessManager.items = {};
	BX.OrderEntityAccessManager.create = function(id, settings)
	{
		var self = new BX.OrderEntityAccessManager();
		self.initialize(id, settings);
		this.items[self.getId()] = self;
		return self;
	};
}
