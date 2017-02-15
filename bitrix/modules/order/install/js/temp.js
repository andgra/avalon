BX.namespace("BX.Crm");
BX.CrmEntityEditor.prototype.layout = function()
{
    var dataInput = BX(this.getSetting('dataInputId', ''));
    if(dataInput)
    {
        dataInput.value = this._data.getId();
    }

    var view = BX.findChild(this._container, { className: 'bx-crm-entity-info-wrapper'}, true, false);
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
                    className: 'bx-crm-entity-info-link',
                    href: this._info.getSetting('url', ''),
                    target: '_blank'
                },
                text: this._info.getSetting('title', this._data.getId())
            }
        )
    );

    if (this._advInfoContainer)
    {
        this._advInfoContainer.innerHTML = this._prepareAdvInfoHTML();
    }
};

if (typeof(BX.CrmEducationEditor) === "undefined") {
    BX.CrmEducationEditor = function () {
        this._settings={};
        this._id='';
    };
    BX.CrmEducationEditor.prototype =
    {
        initialize: function (id, settings ,data, info) {
            this._id = BX.type.isNotEmptyString(id) ? id : 'CRM_EDUCATION_EDITOR' + Math.random();
            this._settings = settings ? settings : {};



            if(!data)
            {
                data = this._prepareData(settings);
            }

            if(!data)
            {
                throw "BX.CrmEntityEditor: Could not find data!";
            }

            this._data = data;

            this._info = info ? info : BX.CrmEducationInfo.create();
            //this.createWindow();

            var selectorId = this.getSetting('changeButtonId', '');
            if(obCrm && obCrm[selectorId])
            {
                var selector = this._selector = obCrm[selectorId];

                selector.AddOnSaveListener(BX.delegate(this._onEntitySelect, this));
                //selector.AddOnBeforeSearchListener();
            }

            var c = this._container = BX(this.getSetting('containerId', ''));
            if(!c)
            {
                throw "BX.CrmEntityEditor: Could not find field container!";
            }

            this._advInfoContainer = BX(this.getSetting('containerId', '') + '_descr');


            var btnChangeIgnore = this.getSetting('buttonChangeIgnore', false);
            if (!btnChangeIgnore)
                BX.bind(BX.findChild(c, { className: 'bx-crm-edit-crm-entity-change'}, true, false), 'click', BX.delegate(this._onChangeButtonClick, this));

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

            BX.cleanNode(BX.findChild(this._container, { className: 'bx-crm-entity-info-wrapper'}, true, false));
            if (this._advInfoContainer)
                BX.cleanNode(this._advInfoContainer);

            BX.onCustomEvent('CrmEntitySelectorChangeValue', [this.getId(), this.getTypeName(), 0, this]);
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

            this._data.reset();
            this.openDialog(
                BX.findChild(this._container, { className: 'bx-crm-edit-crm-entity-add'}, true, false),
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
            BX.ajax(
                {
                    'url': url,
                    'method': 'POST',
                    'dataType': 'json',
                    'data':
                    {
                        'ACTION' : action,
                        'DATA': this._data.toJSON()
                    },
                    onsuccess: function(data)
                    {

                        if(data['ERROR'])
                        {
                            self._showDialogError(data['ERROR']);
                        }
                        else if(!data['DATA'])
                        {
                            self._showDialogError('BX.CrmEntityEditor: Could not find contact data!');
                        }
                        else
                        {
                            self._data = self._prepareData(data['DATA']);
                            self._info = BX.CrmEducationInfo.create(data['INFO'] ? data['INFO'] : {});

                            var newDataInput = BX(self.getSetting('newDataInputId', ''));
                            if(newDataInput)
                            {
                                newDataInput.value = self._data.getId();
                                BX.onCustomEvent('CrmEntitySelectorChangeValue', [self.getId(), self.getTypeName(), self._data.getId(), self]);
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
            var msgs = BX.CrmEntityEditor.messages;
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
                case 'CONTACT':
                    this._dlg = BX.CrmContactEditDialog.create(
                        this._id,
                        this.getSetting('dialog', {}),
                        this._data,
                        BX.delegate(this._onSaveDialogData, this));
                    break;
                case 'COMPANY':
                    this._dlg = BX.CrmCompanyEditDialog.create(
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
        layout: function()
        {
            var dataInput = BX(this.getSetting('dataInputId', ''));
            if(dataInput)
            {
                dataInput.value = this._data.getId();
            }

            var view = BX.findChild(this._container, { className: 'bx-crm-entity-info-wrapper'}, true, false);
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
                            className: 'bx-crm-entity-info-link',
                            href: this._info.getSetting('url', ''),
                            target: '_blank'
                        },
                        text: this._info.getSetting('title', this._data.getId())
                    }
                )
            );

            if (this._advInfoContainer)
            {
                this._advInfoContainer.innerHTML = this._prepareAdvInfoHTML();
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

            var deleteButton = BX.findChild(this._container, { className: 'crm-element-item-delete'}, true, false);
            if(deleteButton)
            {
                deleteButton.style.display = readonly ? 'none' : '';
            }

            var buttonsWrapper = BX.findChild(this._container, { className: 'bx-crm-entity-buttons-wrapper'}, true, false);
            if(buttonsWrapper)
            {
                buttonsWrapper.style.display = readonly ? 'none' : '';
            }
        },
        _prepareData: function(settings)
        {
            var typeName = this.getTypeName();
            if(typeName == 'EDUCATION') {
                return BX.CrmEducationData.create(settings);
            }
            return null;
        },
        _onEntitySelect: function(settings)
        {
            //console.log(settings);
            var typeNames=this._selector.PopupEntityType;
            for(var i in typeNames) {
                var item = settings[typeNames[i]] && settings[typeNames[i]][0] ? settings[typeNames[i]][0] : null;
                if(item) break;
            }
            if(!item)
            {
                return;
            }

            this._data.setId(item['id']);
            //console.log(item);
            this._info = BX.CrmEducationInfo.create(item);
            this.layout();
            //console.log(this);
            BX.onCustomEvent('CrmEntitySelectorChangeValue', [this.getId(), this.getTypeName(), item['id'], this]);
        },
        _showDialogError: function(msg)
        {
            if(this._dlg)
            {
                this._dlg.showError(msg);
            }
        },
        createWindow: function() {
            var butId=this.getSetting('changeButtonId', '');
            var dial=this.getSetting('dialog', '');
            var cont=document.createElement('div');
            cont.id = "crm-"+butId+"-block-content-wrap";
            cont.textContent="fsdfsd";
            cont.className='moveEducation';
            //cont.style.zIndex=5000;
            //console.log(BX.pos(cont).width);
            //cont.style.top=BX.pos(document.getElementById(butId)).top-600+'px';
            //cont.style.left=BX.pos(document.getElementById(butId)).left-15+'px';
            //document.body.appendChild(cont);
            cont.style.display='none';
            var buttonsAr = [
                new BX.PopupWindowButton({
                    text : dial['close'],
                    className : "popup-window-button-accept",
                    events : {
                        click: function() { BX.PopupWindowManager._currentPopup.close(); }
                    }
                })
            ];
            obCrm[butId]= {
                popup: BX.PopupWindowManager.create("CRM-"+butId+"-popup", BX(butId), {
                        content : cont,
                        offsetTop : 2,
                        offsetLeft : -15,
                        zIndex : 5000,
                        buttons : buttonsAr,
                        autoHide : false
                    }
                ),
                div: BX(BX(this.getSetting('containerId', ''))),
                el: BX(butId)
            };
            //console.log(obCrm);
            return null;
        }
    };
}
BX.CrmEducationEditor.items={};

BX.CrmEducationEditor.create=function(id,settings,data,info) {
    var self=new BX.CrmEducationEditor();
    self.initialize(id,settings,data,info);
    this.items[id]=self;
    return self;
};

BX.CrmEducationData = function()
{
    this._id = 0;
    this._name = this._type = '';
};

BX.CrmEducationData.prototype =
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

        if(settings['type'])
        {
            this.setType(settings['type']);
        }
    },
    reset: function()
    {
        this._id = 0;
        this._name = this._type = '';
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
    getType: function()
    {
        return this._type;
    },
    setType: function(val)
    {
        this._type = BX.type.isNotEmptyString(val) ? val : '';
    },
    toJSON: function()
    {
        var result =
        {
            id: this._id,
            name: this._name,
            type: this._type
        };
        return result;
    }
};

BX.CrmEducationData.create = function(settings)
{
    var self = new BX.CrmEducationData();
    self.initialize(settings);
    return self;
};

BX.CrmEducationInfo = function()
{
    this._settings = {};
};

BX.CrmEducationInfo.prototype =
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

BX.CrmEducationInfo.create = function(settings)
{
    var self = new BX.CrmEntityInfo();
    self.initialize(settings);
    return self;
};