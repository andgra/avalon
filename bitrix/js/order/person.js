if (!obOrder)
{
    var obOrder = {};
}
(function() {


    var BX = window.BX;
    if(BX.OrderPerson)
        return;


    BX.OrderPerson = function(id,containerId,type,obSelected,params)
    {
        this.id= id;
        this.bInit= false;
        this.waitDiv= null;
        this.waitPopup= null;
        this.type= type;
        this.list= {};
        this.listContact= {};
        this.obSelected= typeof (obSelected)!=='undefined'?obSelected:{};;
        this.containerId=containerId;
        this.popup= null;
        this.params=typeof (params)!=='undefined'?params:{};
    };

    BX.OrderPerson.Set=function(id,containerId,type,obSelected,params)
    {

        if (obOrder[id])
        {
            obOrder[id].Clear();
            delete obOrder[id];
        }

        obOrder[id] = new BX.OrderPerson(id,containerId,type,obSelected,params);
        obOrder[id].Init();
        return obOrder[id];
    };

    BX.OrderPerson.prototype.Clear = function()
    {
        if (this.popup)
        {
            this.popup.destroy();
        }

        var cont = BX(this.containerId);

        var spanTitle = BX.findChild(cont, {tag: 'span', class: 'order-person-info-title'}, true);
        if (spanTitle)
        {
            BX.cleanNode(spanTitle);
        }

        var descBox = BX.findChild(cont, {tag: 'div', class: 'order-person-info-desc'}, true);
        if (descBox)
        {
            BX.cleanNode(descBox);
        }

        var inpValue = BX.findChild(cont, {tag: 'input', class: 'order-person-input-value'}, true);
        if (inpValue)
        {
            inpValue.value='';
        }
    };

    BX.OrderPerson.prototype.Init = function() {

        if (this.bInit)
            return;

        this.bInit = true;

        //console.log(this);

        this.PlaceSelector();

        var self = this;
        this.popup = BX.PopupWindowManager.create("BXOrderPerson_" + self.id, null, {
            autoHide: false,
            zIndex: 0,
            offsetLeft: 0,
            offsetTop: 0,
            draggable: {restrict: true},
            closeByEsc: true,
            titleBar: {
                content: BX.create("span", {
                    html: BX.message('js_order_person_title_create_'+self.type),
                    'props': {'className': 'order-person-title-bar'}
                })
            },
            closeIcon: {right: "12px", top: "10px"},
            buttons: [
                new BX.PopupWindowButton({
                    text: BX.message('js_order_person_create'),
                    className: "popup-window-button-accept",
                    events: {
                        click: function () {
                            self.Save();
                        }
                    }
                }),
                new BX.PopupWindowButtonLink({
                    text: BX.message('js_order_person_close'),
                    className: "popup-window-button-link-cancel",
                    events: {
                        click: function () {
                            this.popupWindow.close();
                        }
                    }
                })
            ],
            content: '<div class="order-person-container"></div>',
            events: {
                onAfterPopupShow: function () {
                    var button=BX.findChild(self.popup.buttonsContainer,{tag:'span',className:'popup-window-button-accept'},true);
                    if(self.obSelected['id']!='') {
                        button.style.display='none';
                        self.popup.titleBar.innerText=BX.message('js_order_person_title_select_'+self.type);
                    } else {
                        console.log(button);
                        button.innerText=BX.message('js_order_person_create');
                    }
                    self.showWait(this.contentContainer);
                    BX.ajax(
                        {
                            url:'/bitrix/tools/order/person.php',
                            data:
                            {
                                id: self.id,
                                mode:'layout',
                                type:self.type,
                                obSelected: self.obSelected
                            },
                            dataType:'json',
                            method:'POST',
                            onsuccess:
                                BX.delegate(function (result)
                                    {
                                        this.setContent(result['layout']);
                                        eval(result['script']);
                                        self.list=result['list'];
                                        if(result['listContact'])
                                            self.listContact=result['listContact'];
                                        //console.log(result);
                                        console.log(self);
                                        self.closeWait();
                                    },
                                    this)
                        }
                    );

                    BX.onCustomEvent(self, "onAfterPopupShow", []);
                },
                onPopupClose: function () {
                    //self.ClearSelection();
                }

            }
        });
    }

    BX.OrderPerson.prototype.ShowForm = function()
    {

        this.popup.params.zIndex = (BX.WindowManager? BX.WindowManager.GetZIndex() : 0);
        this.popup.show();
    };

    BX.OrderPerson.prototype.showWait = function(div,notClearCont)
    {
        if(this.popup && this.popup.contentContainer && !notClearCont) {
            var cont=BX.findChild(this.popup.contentContainer,{tag:'div',className:'order-person-container'});
            if(cont) {
                cont.innerHTML = '';
            }
        }
        this.waitDiv = this.waitDiv || div;
        div = BX(div || this.waitDiv);

        if (!this.waitPopup)
        {
            this.waitPopup = new BX.PopupWindow('ur_wait_'+this.id, div, {
                autoHide: true,
                lightShadow: true,
                zIndex: (BX.WindowManager? BX.WindowManager.GetZIndex() : 2),
                content: BX.create('DIV', {props: {className: 'ur-wait'}})
            });
        }
        else
        {
            this.waitPopup.setBindElement(div);
        }

        var height = div.offsetHeight, width = div.offsetWidth;
        if (height > 0 && width > 0)
        {
            this.waitPopup.setOffset({
                offsetTop: -parseInt(height/2+15),
                offsetLeft: parseInt(width/2-15)
            });

            this.waitPopup.show();
        }

        return this.waitPopup;
    };

    BX.OrderPerson.prototype.closeWait = function()
    {
        if(this.waitPopup)
            this.waitPopup.close();
    };


    BX.OrderPerson.prototype.SearchList = function()
    {
        var inpId=BX(this.id+'_id')?BX(this.id+'_id').value:'';
        if(this.type=='physical') {
            var data = {
                id: inpId,
                last_name: BX(this.id + '_last_name').value,
                name: BX(this.id + '_name').value,
                second_name: BX(this.id + '_second_name').value,
                phone: BX(this.id + '_phone').value,
                email: BX(this.id + '_email').value
            };
        } else if(this.type=='agent') {
            var legal=BX(this.id + '_legal').value;
            if(legal=='N') {
                var data = {
                    id: inpId,
                    legal:legal,
                    last_name: BX(this.id + '_last_name').value,
                    name: BX(this.id + '_name').value,
                    second_name: BX(this.id + '_second_name').value,
                    phone: BX(this.id + '_phone').value,
                    email: BX(this.id + '_email').value
                };
            }else if(legal=='Y') {
                var data = {
                    id: inpId,
                    legal:legal,
                    title: BX(this.id + '_title').value,
                    phone: BX(this.id + '_phone').value,
                    email: BX(this.id + '_email').value,
                    contact_id: inpId?BX(this.id + '_contact_id').value:'',
                    contact_last_name: inpId?BX(this.id + '_contact_last_name').value:'',
                    contact_name: inpId?BX(this.id + '_contact_name').value:'',
                    contact_second_name: inpId?BX(this.id + '_contact_second_name').value:'',
                    contact_phone: inpId?BX(this.id + '_contact_phone').value:'',
                    contact_email: inpId?BX(this.id + '_contact_email').value:''
                };
            }
        }
        var self=this;
        BX.ajax(
            {
                url:'/bitrix/tools/order/person.php',
                data:
                {
                    id: self.id,
                    mode:'search_list',
                    type: self.type,
                    params: self.params,
                    create:self.obSelected['id']==''?1:0,
                    obSelected: self.obSelected,
                    inputs: data
                },
                dataType:'json',
                method:'POST',
                onsuccess:
                    function(result)
                    {
                        //console.log(result);
                        if(result['refresh']==true) {
                            var list=BX.findChild(self.popup.contentContainer,{tag:'div',className:'order-person-list-container'},true);
                            list.innerHTML=result['listHtml'];
                            self.list=result['list'];
                            var listContact=BX.findChild(self.popup.contentContainer,{tag:'div',className:'order-person-list-contact-container'},true);
                            if(listContact) {
                                listContact.innerHTML=result['listContactHtml'];
                                self.listContact=result['listContact'];
                            }
                        }
                    }
            }
        );

    };

    BX.OrderPerson.prototype.ShowError = function(msg) {
        var errorsCont=BX.findChild(this.popup.contentContainer,{tag:'div',className:'order-person-errors-container'},true);
        errorsCont.appendChild(BX.create('p',{text: msg}));
    };

    BX.OrderPerson.prototype.ClearErrors = function() {
        var errorsCont=BX.findChild(this.popup.contentContainer,{tag:'div',className:'order-person-errors-container'},true);
        BX.cleanNode(errorsCont);
    };

    BX.OrderPerson.prototype.SelectItem = function(elId)
    {
        if(this.list && this.list[elId]) {
            var el=this.list[elId];
            if(this.type=='physical') {
                this.obSelected = {
                    id: el['ID'],
                    title: el['FULL_NAME'],
                    last_name: el['LAST_NAME'],
                    name: el['NAME'],
                    second_name: el['SECOND_NAME'],
                    phone: el['PHONE'],
                    email: el['EMAIL'],
                    url: el['URL']
                };
            } else if(this.type=='agent') {
                if(el['LEGAL']=='N') {
                    if(!el['LAST_NAME'] && !el['NAME'] && !el['SECOND_NAME']) {
                        el['FULL_NAME']=el['TITLE'];
                        var arrName=el['TITLE'].split(' ');
                        el['LAST_NAME']=arrName[0];
                        el['NAME']=arrName[1];
                        el['SECOND_NAME']=arrName[2];
                    }
                    this.obSelected = {
                        id: el['ID'],
                        legal: el['LEGAL'],
                        title: el['FULL_NAME'],
                        last_name: el['LAST_NAME'],
                        name: el['NAME'],
                        second_name: el['SECOND_NAME'],
                        phone: el['PHONE'],
                        email: el['EMAIL'],
                        url: el['URL']
                    };
                } else if(el['LEGAL']=='Y') {
                    this.obSelected = {
                        id: el['ID'],
                        legal: el['LEGAL'],
                        title: el['TITLE'],
                        phone: el['PHONE'],
                        email: el['EMAIL'],
                        url: el['URL'],
                        contact_id: el['CONTACT_ID'],
                        contact_title: el['CONTACT_FULL_NAME'],
                        contact_last_name: el['CONTACT_LAST_NAME'],
                        contact_name: el['CONTACT_NAME'],
                        contact_second_name: el['CONTACT_SECOND_NAME'],
                        contact_phone: el['CONTACT_PHONE'],
                        contact_email: el['CONTACT_EMAIL'],
                        contact_url: el['CONTACT_URL']
                    };
                }
            }
            //console.log(this.obSelected);
            this.PlaceSelector();
        }
        if(this.popup) {
            this.popup.close();
        }
    };

    BX.OrderPerson.prototype.Save = function()
    {

        if(this.type=='physical') {
            var data = {
                id: BX(this.id+'_id').value,
                title: BX(this.id+'_last_name').value+' '+BX(this.id+'_name').value+' '+BX(this.id+'_second_name').value,
                last_name: BX(this.id + '_last_name').value,
                name: BX(this.id + '_name').value,
                second_name: BX(this.id + '_second_name').value,
                phone: BX(this.id + '_phone').value,
                email: BX(this.id + '_email').value
            };
        } else if(this.type=='agent') {
            var legal=BX(this.id + '_legal').value;
            if(legal=='N') {
                var data = {
                    id: BX(this.id+'_id').value,
                    legal:legal,
                    title: BX(this.id+'_last_name').value+' '+BX(this.id+'_name').value+' '+BX(this.id+'_second_name').value,
                    last_name: BX(this.id + '_last_name').value,
                    name: BX(this.id + '_name').value,
                    second_name: BX(this.id + '_second_name').value,
                    phone: BX(this.id + '_phone').value,
                    email: BX(this.id + '_email').value
                };
            }else if(legal=='Y') {
                var data = {
                    id: BX(this.id+'_id').value,
                    legal:legal,
                    title: BX(this.id + '_title').value,
                    phone: BX(this.id + '_phone').value,
                    email: BX(this.id + '_email').value,
                    contact_id: BX(this.id+'_contact_id').value,
                    contact_title: BX(this.id+'_contact_last_name').value+' '+BX(this.id+'_contact_name').value+' '+BX(this.id+'_contact_second_name').value,
                    contact_last_name: BX(this.id + '_contact_last_name').value,
                    contact_name: BX(this.id + '_contact_name').value,
                    contact_second_name: BX(this.id + '_contact_second_name').value,
                    contact_phone: BX(this.id + '_contact_phone').value,
                    contact_email: BX(this.id + '_contact_email').value
                };
            }
        }
        this.ClearErrors();
        var self = this;
        self.showWait(this.contentContainer,true);
        BX.ajax(
            {
                url:'/bitrix/tools/order/person.php',
                data:
                {
                    id: self.id,
                    mode:'save',
                    type: self.type,
                    params: self.params,
                    fields: data
                },
                method: 'POST',
                dataType: 'json',
                timeout:60,
                onsuccess: function(result)
                {
                    console.log(result);
                    self.closeWait();
                    if(result['error'])
                    {
                        self.ShowError(result['error']);
                    }
                    else if(!result['complete'])
                    {
                        self.ShowError('BX.OrderPerson: Could not find contact data!');
                    }
                    else
                    {
                        self.obSelected = result['data'];
                        self.PlaceSelector();
                        self.popup.close();
                    }
                },
                onfailure: function(result)
                {
                    self.closeWait();
                    self.ShowError(result['error'] ? result['error'] : 'unknownError');
                }
            }
        );
    };

    BX.OrderPerson.prototype.PlaceSelector = function()
    {
        if(this.obSelected) {
            var data=this.obSelected;
            //console.log(data);
            var cont = BX(this.containerId);
            var spanTitle = BX.findChild(cont, {tag: 'span', class: 'order-person-info-title'}, true);
            BX.cleanNode(spanTitle);
            var inpValue = BX.findChild(cont, {tag: 'input', class: 'order-person-input-value'}, true);

            var descBox = BX.findChild(cont, {tag: 'div', class: 'order-person-info-desc'}, true);
            BX.cleanNode(descBox);

            if(descBox!=null) {
                var addDesc,addDescIcon,bold;
                if(data['phone'] && data['phone']!='') {
                    addDesc = document.createElement('span');
                    addDesc.className = 'order-offer-info-descrip-tem order-offer-info-descrip-tel';
                    addDesc.appendChild(document.createTextNode("Tel: " + data['phone']));
                    addDescIcon = document.createElement('a');
                    addDescIcon.className = "order-offer-info-descrip-icon";
                    addDescIcon.href = "callto:" + data['phone'];
                    addDesc.appendChild(addDescIcon);

                    descBox.appendChild(addDesc);
                    descBox.appendChild(document.createElement('br'));
                }

                if(data['email'] && data['email']!='') {
                    addDesc = document.createElement('span');
                    addDesc.className = 'order-offer-info-descrip-tem order-offer-info-descrip-email';
                    addDesc.appendChild(document.createTextNode("Email: " + data['email']));
                    addDescIcon = document.createElement('a');
                    addDescIcon.className = "order-offer-info-descrip-icon";
                    addDescIcon.href = "mailto:" + data['email'];
                    addDesc.appendChild(addDescIcon);

                    descBox.appendChild(addDesc);
                    descBox.appendChild(document.createElement('br'));
                }

                if(data['contact_title'] && data['contact_title']!='') {
                    descBox.appendChild(document.createElement('br'));
                    addDesc = document.createElement('span');
                    bold = document.createElement('b');
                    addDesc.className = 'order-offer-info-descrip-tem';
                    bold.appendChild(document.createTextNode(BX.message('js_order_person_contact_desc')));
                    addDesc.appendChild(bold);
                    descBox.appendChild(addDesc);
                    descBox.appendChild(document.createElement('br'));


                    addDesc = document.createElement('span');
                    addDesc.className = 'order-offer-info-descrip-tem';
                    if(data['contact_id'] && data['contact_id']!='') {
                        addDesc.style.marginBottom='2px';
                        addDesc.appendChild(BX.create(
                            'a',
                            {
                                props: {
                                    href: data['contact_url'],
                                    target: '_blank'
                                },
                                text: data['contact_title']
                            }
                        ));
                    } else {
                        addDesc.appendChild(document.createTextNode(data['contact_title']));
                    }
                    descBox.appendChild(addDesc);
                    descBox.appendChild(document.createElement('br'));

                    if(data['contact_phone'] && data['contact_phone']!='') {
                        addDesc = document.createElement('span');
                        addDesc.className = 'order-offer-info-descrip-tem order-offer-info-descrip-tel';
                        addDesc.appendChild(document.createTextNode("Tel: " + data['contact_phone']));
                        addDescIcon = document.createElement('a');
                        addDescIcon.className = "order-offer-info-descrip-icon";
                        addDescIcon.href = "callto:" + data['contact_phone'];
                        addDesc.appendChild(addDescIcon);

                        descBox.appendChild(addDesc);
                        descBox.appendChild(document.createElement('br'));
                    }

                    if(data['contact_email'] && data['contact_email']!='') {
                        addDesc = document.createElement('span');
                        addDesc.className = 'order-offer-info-descrip-tem order-offer-info-descrip-email';
                        addDesc.appendChild(document.createTextNode("Email: " + data['contact_email']));
                        addDescIcon = document.createElement('a');
                        addDescIcon.className = "order-offer-info-descrip-icon";
                        addDescIcon.href = "mailto:" + data['contact_email'];
                        addDesc.appendChild(addDescIcon);

                        descBox.appendChild(addDesc);
                        descBox.appendChild(document.createElement('br'));
                    }
                }
            }



            inpValue.value = data['id'];

            var buttonBox = BX.findChild(cont, {tag: 'div', class: 'order-person-buttons-wrap'}, true);
            var buttonA=BX.findChild(buttonBox, {tag: 'a'}, true);
            if(data['id'] && data['id']!='') {
                spanTitle.appendChild(BX.create(
                    'a',
                    {
                        props: {
                            href: data['url'],
                            target: '_blank'
                        },
                        text: data['title']
                    }
                ));
                buttonA.text=BX.message('js_order_person_edit');
            } else {
                spanTitle.appendChild(document.createTextNode(data['title']));
                buttonA.text=BX.message('js_order_person_create');
            }
        }
    };

    BX.OrderPerson.prototype.ChooseFromList = function(elId)
    {
        var prefix='';
        var el=null;
        if(this.list && this.list[elId]) {
            el = this.list[elId];
        } else if(this.listContact && this.listContact[elId]) {
            el = this.listContact[elId];
            prefix='_contact';
        }
        if(el) {
            //var cont=BX.findChild(this.popup.contentContainer,{tag:'div',className:'order-person-container'});
            //var createCont=BX.findChild(cont,{tag:'div',className:'order-person-create-container'},true);
            var id=BX(this.id+prefix+'_id');
            if(id) {
                if(id.value!='') {
                    var trOld=BX(this.id+'_list_tr_'+id.value);
                    if(trOld) trOld.style.backgroundColor='';
                }
                id.value=el['ID'];
            }
            var trNew=BX(this.id+'_list_tr_'+elId);
            if(trNew) trNew.style.backgroundColor='#A1F43D';
            var title=BX(this.id+prefix+'_title');
            if(title) {
                title.value=el['TITLE'];
                title.setAttribute('disabled','');
            }
            var lName=BX(this.id+prefix+'_last_name');
            if(lName) {
                lName.value=el['LAST_NAME'];
                lName.setAttribute('disabled','');
            }
            var name=BX(this.id+prefix+'_name');
            if(name) {
                name.value=el['NAME'];
                name.setAttribute('disabled','');
            }
            var sName=BX(this.id+prefix+'_second_name');
            if(sName) {
                sName.value=el['SECOND_NAME'];
                sName.setAttribute('disabled','');
            }
            var phone=BX(this.id+prefix+'_phone');
            if(phone) phone.value=el['PHONE'];
            var email=BX(this.id+prefix+'_email');
            if(email) email.value=el['EMAIL'];

            var button=BX.findChild(this.popup.buttonsContainer,{tag:'span',className:'popup-window-button-accept'},true);
            button.innerText=BX.message('js_order_person_select');
        }
    };

    BX.OrderPerson.prototype.Unchoose = function(elem)
    {
        var elId=BX.findChild(elem.parentNode,{tag:'input'}).value;
        var prefix='';
        var el=null;
        if(this.list && this.list[elId]) {
            el = this.list[elId];
        } else if(this.listContact && this.listContact[elId]) {
            el = this.listContact[elId];
            prefix='_contact';
        }
        if(el) {
            var id=BX(this.id+prefix+'_id');
            if(id) {
                if(id.value!='') {
                    var trOld=BX(this.id+'_list_tr_'+id.value);
                    if(trOld) trOld.style.backgroundColor='';
                }
                id.value='';
            }
            var title=BX(this.id+prefix+'_title');
            if(title) {
                title.removeAttribute('disabled');
            }
            var lName=BX(this.id+prefix+'_last_name');
            if(lName) {
                lName.removeAttribute('disabled');
            }
            var name=BX(this.id+prefix+'_name');
            if(name) {
                name.removeAttribute('disabled');
            }
            var sName=BX(this.id+prefix+'_second_name');
            if(sName) {
                sName.removeAttribute('disabled');
            }
            var button=BX.findChild(this.popup.buttonsContainer,{tag:'span',className:'popup-window-button-accept'},true);
            button.innerText=BX.message('js_order_person_create');
        }
    };

})();
