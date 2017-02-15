if (!obOrder)
{
    var obOrder = {};
}

ORDER = function(orderID, div, el, name, element, prefix, multiple, entityType, localize, disableMarkup)
{
    this.orderID = orderID;
    this.div = div;
    this.el = el;
    this.name = name;
    this.PopupEntityType = entityType;
    this.PopupTabs = {};
    this.PopupElement =  element;
    this.PopupPrefix = prefix;
    this.PopupMultiple = multiple;
    this.PopupBlock = {};
    this.PopupSearch = {};
    this.PopupSearchInput = null;

    this.PopupTabsIndex = 0;
    this.PopupTabsIndexId = '';
    this.PopupLocalize = localize;

    this.popup = null;
    this.onSaveListeners = [];
    this.disableMarkup = !!disableMarkup; //disable call 'PopupCreateValue' on save
    this.onBeforeSearchListeners = [];
};

ORDER.prototype.Init = function()
{
    this.popupShowMarkup();

    this.PopupTabs = BX.findChildren(BX("order-"+this.orderID+"_"+this.name+"-tabs"), {className : "order-block-cont-tabs"});
    if(this.PopupTabs.length > 0)
    {
        this.PopupTabsIndex = 0;
        this.PopupTabsIndexId = this.PopupTabs[0].id;
    }

    this.PopupItem = {};
    this.PopupItemSelected = {};
    for (var i in this.PopupElement)
        this.PopupAddItem(this.PopupElement[i]);

    this.PopupBlock = BX.findChildren(BX("order-"+this.orderID+"_"+this.name+"-blocks"), {className : "order-block-cont-block"});
    this.PopupSearch = BX.findChildren(BX("order-"+this.orderID+"_"+this.name+"-block-cont-search"), {className : "order-block-cont-search-tab"});
    this.PopupSearchInput = BX("order-"+this.orderID+"_"+this.name+"-search-input");

    for(var i = 0; i<this.PopupTabs.length; i++)
        eval('BX.bind(this.PopupTabs[i], "click", function(event){ ORDER.PopupShowBlock("' + this.orderID + '", this); BX.PreventDefault(event); })');

    for(var i = 0; i<this.PopupSearch.length; i++)
        eval('BX.bind(this.PopupSearch[i], "click", function(event){ ORDER.PopupShowSearchBlock("' + this.orderID + '", this); BX.PreventDefault(event); })');

    eval('BX.bind(this.PopupSearchInput, "keyup", function(event){ ORDER.SearchChange("' + this.orderID + '")})');
    this.PopupSave();
};

ORDER.prototype.Clear = function()
{
    if (this.popup)
    {
        this.popup.destroy();
    }

    var inputBox = BX("order-"+this.orderID+"_"+this.name+"-input-box");
    if (inputBox)
    {
        this.div.removeChild(inputBox);
        BX.remove(inputBox);
    }

    var textBox = BX("order-"+this.orderID+"_"+this.name+"-text-box");
    if (textBox)
    {
        BX.remove(textBox);
    }

    var htmlBox = BX("order-"+this.orderID+"_"+this.name+"-html-box");
    if (htmlBox)
    {
        BX.remove(htmlBox);
    }
};

ORDER.Set = function(el, name, subIdName, element, prefix, multiple, entityType, localize, disableMarkup)
{
    var orderID =  el.id + subIdName;
    if (obOrder[orderID])
    {
        obOrder[orderID].Clear();
        delete obOrder[orderID];
    }

    obOrder[orderID] = new ORDER(orderID, ORDER.GetWrapperDivPa(el), el, name, element, prefix, multiple,  entityType, localize, disableMarkup);
    obOrder[orderID].Init();
    return orderID;
};

ORDER.GetElementForm = function (pn)
{
    return BX.findParent(pn, { "tagName":"FORM" });
};

ORDER.GetWrapperDivPr = function (pn, name)
{
    return BX.findPreviousSibling(pn, { "tagName": "DIV", "property": { "name": "order-"+ name +"-box" } });
};

ORDER.GetWrapperDivN = function (pn, name)
{
    return BX.findNextSibling(pn, { "tagName": "DIV", "property": { "name": "order-"+ name +"-box" } });
};

ORDER.GetWrapperDivPa = function (pn, name)
{
    while(pn.nodeName != 'DIV' && pn.name != 'order-'+name+'-box')
        pn = pn.parentNode;

    return pn.parentNode;
};

ORDER.prototype.Open = function (params)
{
    if(!BX.type.isPlainObject(params))
    {
        params = {};
    }

    var titleBar = BX.type.isPlainObject(params["titleBar"]) ? params["titleBar"] : null;
    var closeIcon = BX.type.isPlainObject(params["closeIcon"]) ? params["closeIcon"] : null;
    var closeByEsc = BX.type.isBoolean(params["closeByEsc"]) ? params["closeByEsc"] : false;

    if (BX.PopupWindowManager._currentPopup !== null
        && BX.PopupWindowManager._currentPopup.uniquePopupId == "ORDER-"+this.orderID+"-popup")
    {
        BX.PopupWindowManager._currentPopup.close();
    }
    else
    {
        var buttonsAr = [];
        if (this.PopupMultiple)
        {
            buttonsAr = [
                new BX.PopupWindowButton({
                    text : this.PopupLocalize['ok'],
                    className : "popup-window-button-accept",
                    events : {
                        click: BX.delegate(this._handleAcceptBtnClick, this)
                    }
                }),

                new BX.PopupWindowButtonLink({
                    text : this.PopupLocalize['cancel'],
                    className : "popup-window-button-link-cancel",
                    events : {
                        click: function() { this.popupWindow.close(); }
                    }
                })
            ];
        }
        else
        {
            buttonsAr = [
                new BX.PopupWindowButton({
                    text : this.PopupLocalize['close'],
                    className : "popup-window-button-accept",
                    events : {
                        click: function() { this.popupWindow.close(); }
                    }
                })
            ];
        }
        this.popup = BX.PopupWindowManager.create("ORDER-"+this.orderID+"-popup", this.el, {
                content : BX("order-"+this.orderID+"_"+this.name+"-block-content-wrap"),
                titleBar: titleBar,
                closeIcon: closeIcon,
                closeByEsc: closeByEsc,
                offsetTop : 2,
                offsetLeft : -15,
                zIndex : 5000,
                buttons : buttonsAr,
                autoHide : !this.PopupMultiple
            }
        );
        //console.log(this);
        this.popup.show();
        BX.focus(this.PopupSearchInput);
    }
    return false;
};

ORDER.PopupSave2 = function(orderID)
{
    if (!obOrder[orderID])
        return false;

    obOrder[orderID].PopupSave();
};

ORDER.prototype._handleAcceptBtnClick = function()
{
    this.PopupSave();
    this.popup.close();
};

ORDER.prototype.AddOnSaveListener = function(listener)
{
    if(typeof(listener) != 'function')
    {
        return;
    }

    var ary = this.onSaveListeners;
    for(var i = 0; i < ary.length; i++)
    {
        if(ary[i] == listener)
        {
            return;
        }
    }
    ary.push(listener);
};

ORDER.prototype.RemoveOnSaveListener = function(listener)
{
    var ary = this.onSaveListeners;
    for(var i = 0; i < ary.length; i++)
    {
        if(ary[i] == listener)
        {
            ary.splice(i, 1);
            break;
        }
    }
};

ORDER.prototype.AddOnBeforeSearchListener = function(listener)
{
    if(typeof(listener) != 'function')
    {
        return;
    }

    var ary = this.onBeforeSearchListeners;
    for(var i = 0; i < ary.length; i++)
    {
        if(ary[i] == listener)
        {
            return;
        }
    }
    ary.push(listener);
};

ORDER.prototype.RemoveOnBeforeSearchListener = function(listener)
{
    var ary = this.onBeforeSearchListeners;
    for(var i = 0; i < ary.length; i++)
    {
        if(ary[i] == listener)
        {
            ary.splice(i, 1);
            break;
        }
    }
};

ORDER.prototype.PopupSave = function()
{
    var arElements = {};
    for (var i in this.PopupEntityType)
    {
        var elements = BX.findChildren(BX("order-"+this.orderID+"_"+this.name+"-block-"+this.PopupEntityType[i]+"-selected"), {className: "order-block-cont-block-item"});
        if (elements !== null)
        {
            var el = 0;
            arElements[this.PopupEntityType[i]] = {};
            for(var e=0; e<elements.length; e++)
            {
                var elementIdLength = "selected-order-"+this.orderID+"_"+this.name+"-block-item-";
                var elementId = elements[e].id.substr(elementIdLength.length);
                //console.log(this);

                var data =  {
                    'id' : this.PopupItem[elementId]['id'],
                    'type' : this.PopupEntityType[i],
                    'place' : this.PopupItem[elementId]['place'],
                    'title' : this.PopupItem[elementId]['title'],
                    'url' : this.PopupItem[elementId]['url']
                };

                if(typeof(this.PopupItem[elementId]['customData']) != 'undefined')
                {
                    data['customData'] = this.PopupItem[elementId]['customData'];
                }
                if(typeof(this.PopupItem[elementId]['advancedInfo']) != 'undefined')
                {
                    data['advancedInfo'] = this.PopupItem[elementId]['advancedInfo'];
                }

                arElements[this.PopupEntityType[i]][el] = data;

                el++;
            }
        }
    }

    //console.log(arElements);
    var ary = this.onSaveListeners;
    //console.log(ary[0]);

    if(ary.length > 0)
    {
        for(var j = 0; j < ary.length; j++)
        {
            try
            {
                ary[j](arElements);
            }
            catch(ex)
            {
            }
        }
    }

    if(!this.disableMarkup)
    {
        this.PopupCreateValue(arElements);
    }
};

ORDER.prototype.ClearSelectItems = function()
{
    this.PopupItemSelected = {};
};

ORDER.PopupShowBlock = function(orderID, element, search)
{
    if (!obOrder[orderID])
        return false;

    for(var i=0; i<obOrder[orderID].PopupTabs.length; i++)
    {
        if(obOrder[orderID].PopupTabs[i] == element)
        {
            obOrder[orderID].PopupTabsIndex=i;
            obOrder[orderID].PopupTabsIndexId = obOrder[orderID].PopupTabs[i].id;
        }
        obOrder[orderID].PopupBlock[i].style.display="none";
        BX.removeClass(obOrder[orderID].PopupTabs[i],"selected");
    }
    if(!search)
    {
        BX.addClass(element, "selected");
        obOrder[orderID].PopupSearchInput.value = "";
        BX('order-'+orderID+'_'+obOrder[orderID].name+'-block-search').innerHTML = '';
    }
    else
        BX.addClass(obOrder[orderID].PopupTabs[obOrder[orderID].PopupTabsIndex], "selected");

    obOrder[orderID].PopupBlock[obOrder[orderID].PopupTabsIndex].style.display="block";
    BX('order-'+orderID+'_'+obOrder[orderID].name+'-block-search').style.display="none";
    BX.removeClass(obOrder[orderID].PopupSearch[1], "selected");
    BX.addClass(obOrder[orderID].PopupSearch[0], "selected");

    BX.focus(obOrder[orderID].PopupSearchInput);
};

ORDER.PopupShowSearchBlock = function(orderID, element)
{
    if (!obOrder[orderID])
        return false;

    for(var i=0; i<obOrder[orderID].PopupBlock.length; i++)
        obOrder[orderID].PopupBlock[i].style.display="none";

    var search=true;
    if(element == obOrder[orderID].PopupSearch[0])
    {
        ORDER.PopupShowBlock(orderID, BX(obOrder[orderID].OrderPopupTabsIndexId), search);
        return false;
    }

    BX('order-'+obOrder[orderID].orderID+"_"+obOrder[orderID].name+'-block-search').style.display="block";
    BX.removeClass(obOrder[orderID].PopupSearch[0], "selected");
    BX.addClass(element, "selected");

    BX.focus(obOrder[orderID].PopupSearchInput);
};

ORDER.PopupSelectItem = function(orderID, element, tab, unsave, select)
{
    if (!obOrder[orderID])
        return false;

    var flag=element;
    if(flag.check)
    {
        if (select === undefined || select == false)
            ORDER.PopupUnselectItem(orderID, element.id, "selected-"+element.id);
        return false;
    }

    elementIdLength = "order-"+orderID+'_'+obOrder[orderID].name+"-block-item-";
    elementId = element.id.substr(elementIdLength.length);
    var addOrderItems=document.createElement('span');
    addOrderItems.className = "order-block-cont-block-item";
    addOrderItems.id="selected-"+element.id;

    var addOrderDelBut=document.createElement('i');
    var addOrderLink=document.createElement('a');
    addOrderLink.href=obOrder[orderID].PopupItem[elementId]['url'];
    addOrderLink.target="_blank";

    var blockWrap;
    if (tab === null)
    {
        if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+'_'+obOrder[orderID].name+"-tab-contact")
            blockWrap=BX("order-"+orderID+'_'+obOrder[orderID].name+"-block-contact-selected");

        if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+'_'+obOrder[orderID].name+"-tab-reg")
            blockWrap=BX("order-"+orderID+'_'+obOrder[orderID].name+"-block-reg-selected");

        if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+'_'+obOrder[orderID].name+"-tab-app")
            blockWrap=BX("order-"+orderID+'_'+obOrder[orderID].name+"-block-app-selected");

        if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+'_'+obOrder[orderID].name+"-tab-division")
            blockWrap=BX("order-"+orderID+'_'+obOrder[orderID].name+"-block-division-selected");

        if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+'_'+obOrder[orderID].name+"-tab-course")
            blockWrap=BX("order-"+orderID+'_'+obOrder[orderID].name+"-block-course-selected");

        if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+'_'+obOrder[orderID].name+"-tab-group")
            blockWrap=BX("order-"+orderID+'_'+obOrder[orderID].name+"-block-group-selected");

        if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+'_'+obOrder[orderID].name+"-tab-nomen")
            blockWrap=BX("order-"+orderID+'_'+obOrder[orderID].name+"-block-nomen-selected");

        if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+'_'+obOrder[orderID].name+"-tab-user")
            blockWrap=BX("order-"+orderID+'_'+obOrder[orderID].name+"-block-user-selected");
    }
    else
        blockWrap=BX("order-"+orderID+'_'+obOrder[orderID].name+"-block-"+tab+"-selected");

    if (obOrder[orderID].PopupMultiple)
    {
        blockTitle = BX.findChild(blockWrap, { className : "order-block-cont-right-title-count"}, true);
        blockTitle.innerHTML = parseInt(blockTitle.innerHTML)+1;
        BX.addClass(element, "order-block-cont-item-selected");
        BX.addClass(blockWrap, "order-added-item");
        flag.check=1;
    }
    else
    {
        for (var i in obOrder[orderID].PopupEntityType)
        {
            BX.removeClass(BX("order-"+orderID+'_'+obOrder[orderID].name+"-block-"+obOrder[orderID].PopupEntityType[i]+"-selected"), "order-added-item");
            elements = BX.findChildren(BX("order-"+orderID+'_'+obOrder[orderID].name+"-block-"+obOrder[orderID].PopupEntityType[i]+"-selected"), {className: "order-block-cont-block-item"});
            if (elements !== null)
                for (var i in elements)
                    BX.remove(elements[i]);

        }
    }
    blockWrap.appendChild(addOrderItems).appendChild(addOrderDelBut);

    blockWrap.appendChild(addOrderItems).appendChild(addOrderLink).innerHTML=BX.util.htmlspecialchars(obOrder[orderID].PopupItem[elementId]['title']);
    //console.log(element);

    eval('BX.bind(addOrderDelBut, "click", function(event) {ORDER.PopupUnselectItem("'+orderID+'", element.id, "selected-"+element.id); BX.PreventDefault(event);})');

    obOrder[orderID].PopupItemSelected[elementId] = element;

    if (!obOrder[orderID].PopupMultiple && (unsave === undefined || unsave == false))
    {
        obOrder[orderID].PopupSave();
        BX.PopupWindowManager._currentPopup.close();
    }
};

ORDER.PopupUnselectItem = function(orderID, element, selected)
{
    if (!obOrder[orderID])
        return false;

    if (obOrder[orderID].PopupMultiple)
    {
        if(BX(selected).parentNode.getElementsByTagName('span').length == 3)
            BX.removeClass(BX(selected).parentNode, "order-added-item");

        blockTitle = BX.findChild(BX(selected).parentNode, { className : "order-block-cont-right-title-count"}, true);
        blockTitle.innerHTML = parseInt(blockTitle.innerHTML)-1;

        obj = BX(element);
        if (obj !== null)
        {
            obj.check=0;
            BX.removeClass(obj, "order-block-cont-item-selected");
        }
    }
    elementIdLength = "order-"+orderID+'_'+obOrder[orderID].name+"-block-item-";
    elementId = element.substr(elementIdLength.length);
    delete obOrder[orderID].PopupItemSelected[elementId];

    BX.remove(BX(selected));
};

ORDER.prototype.SetPopupItems = function(place, items)
{
    this.PopupItem = {};
    this.PopupItemSelected = {};

    var placeHolder = BX('order-' + this.orderID + '_' + this.name + '-block-' + place);
    BX.cleanNode(placeHolder);

    for (var i = 0; i < items.length; i++)
    {
        var item = items[i];
        item['place'] = place;
        //item['selected'] = 'Y';
        this.PopupAddItem(item);
    }
};

ORDER.prototype.PopupSetItem = function(id)
{
    ar = id.toString().split('_');
    if (ar[1] !== undefined)
    {
        entityShortName = ar[0];
        entityId = ar[1];

        if (entityShortName == 'L')
            entityType = 'lead';
        else if (entityShortName == 'C')
            entityType = 'contact';
        else if (entityShortName == 'CO')
            entityType = 'company';
        else if (entityShortName == 'D')
            entityType = 'deal';
        else if (entityShortName == 'Q')
            entityType = 'quote';
    }
    else
    {
        for (var i in this.PopupEntityType)
            entityType = this.PopupEntityType[i];
        entityId = id;
    }

    var order = this;

    BX.ajax({
        url: '/bitrix/components/bitrix/order.'+entityType+'.list/list.ajax.php',
        method: 'POST',
        dataType: 'json',
        data: {'MODE' : 'SEARCH', 'VALUE' : entityId, 'MULTI' : (order.PopupPrefix? 'Y': 'N')},
        onsuccess: function(data)
        {
            for (var i in data) {
                data[i]['selected'] = 'Y';
                order.PopupAddItem(data[i]);
            }
            order.PopupSave();
        },
        onfailure: function(data)
        {
        }
    });
};

ORDER.prototype.PopupAddItem = function(arParam)
{
    if (arParam['place'] === undefined || arParam['place'] == '')
        arParam['place'] = arParam['type'];

    bElementSelected = false;
    if (this.PopupItemSelected[arParam['id']+'-'+arParam['place']] !== undefined)
        bElementSelected = true;
    //console.log(arParam);
    itemBody = document.createElement("span");
    itemBody.id = 'order-'+this.orderID+"_"+this.name+'-block-item-'+arParam['id']+'-'+arParam['place'];
    itemBody.className = "order-block-cont-item"+(bElementSelected? " order-block-cont-item-selected": "");
    itemBody.check=bElementSelected? 1: 0;

    /*if (arParam['type'] == 'contact' || arParam['type'] == 'company')
     {
     itemAvatar = document.createElement("span");
     itemAvatar.className = "order-avatar";

     if (arParam['image'] !== undefined && arParam['image'] != '')
     {
     itemAvatar.style.background = 'url("' + arParam['image'] + '") no-repeat';
     }

     itemBody.appendChild(itemAvatar);
     }*/

    itemTitle = document.createElement("ins");
    itemTitle.appendChild(document.createTextNode(arParam['title']));
    itemId = document.createElement("var");
    itemId.className = "order-block-cont-var-id";
    itemId.appendChild(document.createTextNode(arParam['id']));
    itemUrl = document.createElement("var");
    itemUrl.className = "order-block-cont-var-url";
    itemUrl.appendChild(document.createTextNode(arParam['url']));


    var bodyBox = document.createElement("span");
    bodyBox.className = "order-block-cont-contact-info";
    bodyBox.style.float = "left";
    bodyBox.appendChild(itemTitle);
    if(arParam['place']!='contact') {
        var itemDesc = document.createElement("span");
        itemDesc.innerHTML = this.prepareDescriptionHtml(arParam['desc']);
        bodyBox.appendChild(itemDesc);
    }
    bodyBox.appendChild(itemId);
    bodyBox.appendChild(itemUrl);
    itemBody.appendChild(bodyBox);
    //itemBody.appendChild(document.createElement("i"));

    bDefinedItem = false;
    if (arParam['place'] != 'search' && this.PopupItem[arParam['id']+'-'+arParam['place']] !== undefined)
        bDefinedItem = true;
    else
        this.PopupItem[arParam['id']+'-'+arParam['place']] = arParam;

    var placeHolder = BX("order-"+this.orderID+"_"+this.name+"-block-"+arParam['place']);

    if (placeHolder !== null)
    {
        if (!bDefinedItem)
            placeHolder.appendChild(itemBody);

        ORDER._bindPopupItem(this.orderID, itemBody, arParam["type"]);

        if (arParam['selected'] !== undefined && arParam['selected'] == 'Y')
            ORDER.PopupSelectItem(this.orderID, itemBody, arParam['type'], true, true);
    }
};
ORDER._bindPopupItem = function(ownerId, itemBody, type)
{
    BX.bind(
        itemBody,
        "click",
        function(e){ ORDER.PopupSelectItem(ownerId, itemBody, type); return BX.PreventDefault(e); });
};
ORDER.prototype.prepareDescriptionHtml = function(str)
{
    if(!str.replace)
    {
        return str;
    }

    //Escape tags and quotes
    return str.replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
};
ORDER.SearchChange = function(orderID)
{
    if (!obOrder[orderID])
        return false;

    var searchValue = obOrder[orderID].PopupSearchInput.value;
    /*if (searchValue == '')
     return false;*/
    //console.log(obOrder[orderID]);
    var entityType = '';
    if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+"_"+obOrder[orderID].name+"-tab-division")
        entityType = 'division';
    else if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+"_"+obOrder[orderID].name+"-tab-course")
        entityType = 'course';
    else if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+"_"+obOrder[orderID].name+"-tab-group")
        entityType = 'group';
    else if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+"_"+obOrder[orderID].name+"-tab-contact")
        entityType = 'contact';
    else if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+"_"+obOrder[orderID].name+"-tab-reg")
        entityType = 'reg';
    else if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+"_"+obOrder[orderID].name+"-tab-nomen")
        entityType = 'nomen';
    else if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+"_"+obOrder[orderID].name+"-tab-app")
        entityType = 'app';
    else if(obOrder[orderID].PopupTabsIndexId=="order-"+orderID+"_"+obOrder[orderID].name+"-tab-user")
        entityType = 'user';


    ORDER.PopupShowSearchBlock(orderID, obOrder[orderID].PopupSearch[1]);

    setTimeout(function() {
        if (BX('order-'+orderID+"_"+obOrder[orderID].name+'-block-search').innerHTML == ''
            && obOrder[orderID].PopupTabsIndexId=="order-"+orderID+"_"+obOrder[orderID].name+"-tab-"+entityType) {
            var spanWait = document.createElement('div');
            spanWait.className="order-block-cont-search-wait";
            spanWait.innerHTML=obOrder[orderID].PopupLocalize['wait'];
            BX('order-'+orderID+"_"+obOrder[orderID].name+'-block-search').appendChild(spanWait);
        }
    }, 3000);
    var data=[];
    for(var i in obOrder[orderID].PopupElement) {
        if((obOrder[orderID].PopupElement[i]['title'].toLowerCase().search(searchValue.toLowerCase())!=-1
            || obOrder[orderID].PopupElement[i]['desc'].toLowerCase().search(searchValue.toLowerCase())!=-1)
            && obOrder[orderID].PopupElement[i]['type']==entityType) {
            data.push(obOrder[orderID].PopupElement[i]);
        }
    }
    /*BX.ajax({
     url: postUrl,
     method: 'POST',
     dataType: 'json',
     data: postData,
     onsuccess: function(data)
     {*/
    //console.log(data);

    if (obOrder[orderID].PopupTabsIndexId!="order-"+orderID+"_"+obOrder[orderID].name+"-tab-"+entityType)
        return false;

    BX('order-'+orderID+"_"+obOrder[orderID].name+'-block-search').className = 'order-block-cont-block order-block-cont-block-'+entityType;
    BX('order-'+orderID+"_"+obOrder[orderID].name+'-block-search').innerHTML = '';
    el = 0;
    for (var i in data) {
        data[i]['place'] = 'search';
        obOrder[orderID].PopupAddItem(data[i]);
        el++;
    }
    if (el == 0)
    {
        var spanWait = document.createElement('div');
        spanWait.className="order-block-cont-search-no-result";
        spanWait.innerHTML=obOrder[orderID].PopupLocalize['noresult'];
        BX('order-'+orderID+"_"+obOrder[orderID].name+'-block-search').appendChild(spanWait);
    }
    /*},
     onfailure: function(data)
     {

     }
     });*/
};

ORDER.prototype.PopupCreateValue = function(arElements)
{
    var inputBox = BX("order-"+this.orderID+"_"+this.name+"-input-box");
    var textBox = BX("order-"+this.orderID+"_"+this.name+"-text-box");

    if(!inputBox || !textBox)
    {
        return;
    }

    inputBox.innerHTML = '';

    var textBoxNew = document.createElement('DIV');
    textBoxNew.id = textBox.id;
    textBox.parentNode.replaceChild(textBoxNew, textBox);
    textBox = textBoxNew;

    var tableObject = document.createElement('table');
    tableObject.className = "field_order";
    tableObject.cellPadding = "0";
    tableObject.cellSpacing = "0";
    var tbodyObject = document.createElement('TBODY');

    var iEl = 0;
    for (var type in arElements)
    {
        var rowObject = document.createElement("TR");
        rowObject.className = "orderPermTableTrHeader";

        if (this.PopupEntityType.length > 1)
        {
            var cellObject = document.createElement("TD");
            cellObject.className = "field_order_entity_type";
            cellObject.appendChild(document.createTextNode(this.PopupLocalize[type]+":"));
            rowObject.appendChild(cellObject);
        }
        cellObject = document.createElement("TD");
        cellObject.className = "field_order_entity";

        var iTypeEl = 0;
        for (var i in arElements[type])
        {
            var addInput=document.createElement('input');
            addInput.type = 'text';
            addInput.name = this.name+(this.PopupMultiple? '[]': '');
            addInput.value = arElements[type][i]['id'];

            inputBox.appendChild(addInput);

            var addOrderLink=document.createElement('a');
            addOrderLink.href=arElements[type][i]['url'];
            addOrderLink.target="_blank";
            addOrderLink.appendChild(document.createTextNode(arElements[type][i]['title']));
            cellObject.appendChild(addOrderLink);

            var addOrderDeleteLink=document.createElement('span');
            addOrderDeleteLink.className="order-element-item-delete";
            addOrderDeleteLink.id="deleted-order-"+this.orderID+'_'+this.name+"-block-item-"+arElements[type][i]['id']+'-'+arElements[type][i]['place'];
            eval('BX.bind(addOrderDeleteLink, "click", function(event) { ORDER.PopupUnselectItem("'+this.orderID+'", this.id.substr(8), "selected-"+this.id.substr(8)); ORDER.PopupSave2("'+this.orderID+'");})');
            cellObject.appendChild(addOrderDeleteLink);

            iTypeEl++;
            iEl++;
        }

        if(iTypeEl > 0)
        {
            rowObject.appendChild(cellObject);
            tbodyObject.appendChild(rowObject);
        }

    }
    var rowObject = document.createElement("TR");
    rowObject.className = "orderDescription";

    if (iEl == 0)
    {
        var addInput=document.createElement('input');
        addInput.type = 'text';
        addInput.name = this.name+(this.PopupMultiple? '[]': '');
        addInput.value = '';
        inputBox.appendChild(addInput);
    }
    tableObject.appendChild(tbodyObject);
    textBox.appendChild(tableObject);

    if(this.el)
    {
        if (iEl>0)
        {
            this.el.innerHTML = this.PopupLocalize['edit'];
        }
        else
        {
            BX.cleanNode(textBox, false);

            if(BX.browser.IsIE())
            {
                // HACK: empty DIV has height in IE7 - make it collapse to zero.
                textBox.style.fontSize = '0px';
                textBox.style.lineHeight = '0px';
            }
            this.el.innerHTML = this.PopupLocalize['add'];
        }
    }
};

ORDER.prototype.popupShowMarkup = function()
{
    var layer1 = document.createElement("div");
    layer1.id = "order-"+this.orderID+"_"+this.name+"-block-content-wrap";
    layer1.className = "order-block-content";
    var table1 = document.createElement('table');
    table1.className = "order-box-layout";
    if (!this.PopupMultiple)
        table1.className = table1.className+" order-single-column";
    table1.cellSpacing = "0";

    var table1body = document.createElement('tbody');
    var table1bodyTr1 = document.createElement("TR");
    var table1bodyTd1 = document.createElement("TD");
    table1bodyTd1.className = "order-block-cont-left";

    var layer4 = document.createElement("div");
    layer4.id = "order-"+this.orderID+"_"+this.name+"-tabs";
    layer4.className = "order-block-cont-tabs-wrap";
    if (this.PopupEntityType.length == 1)
        layer4.className = layer4.className+" order-single-entity";

    var firstTab = true;
    for (var i in this.PopupEntityType) {
        var tab1 = document.createElement("span");
        tab1.className = "order-block-cont-tabs"+(firstTab? " selected": '');
        tab1.id = "order-"+this.orderID+"_"+this.name+"-tab-"+this.PopupEntityType[i];
        var tab1span = document.createElement("span");
        var tab1span1 = document.createElement("span");
        tab1span1.appendChild(document.createTextNode(this.PopupLocalize[this.PopupEntityType[i]]));
        tab1span.appendChild(tab1span1);
        tab1.appendChild(tab1span);
        layer4.appendChild(tab1);
        firstTab = false;
    }

    table1bodyTd1.appendChild(layer4);

    layer4 = document.createElement("div");
    layer4.id = "order-"+this.orderID+"_"+this.name+"-block-cont-search";
    layer4.className = "order-block-cont-search";

    var input = document.createElement("input");
    input.type = "text";
    input.id = "order-"+this.orderID+"_"+this.name+"-search-input";
    layer4.appendChild(input);

    var search1 = document.createElement("span");
    search1.className = "order-block-cont-search-tab selected";
    search1.style.display="none";
    search1.appendChild(document.createElement("span"));

    /*var search1a = document.createElement("a");
     search1a.href="#";
     search1a.appendChild(document.createTextNode(this.PopupLocalize['last']));
     search1.appendChild(search1a);*/

    search1.appendChild(document.createElement("span"));
    layer4.appendChild(search1);

    /*var search1 = document.createElement("span");
     search1.className = "order-block-cont-search-tab";
     search1.appendChild(document.createElement("span"));

     var search1a = document.createElement("a");
     search1a.href="#";
     search1a.appendChild(document.createTextNode(this.PopupLocalize['search']));
     search1.appendChild(search1a);

     search1.appendChild(document.createElement("span"));
     layer4.appendChild(search1);*/

    table1bodyTd1.appendChild(layer4);

    layer4 = document.createElement("div");
    layer4.className = "popup-window-hr popup-window-buttons-hr";
    layer4.appendChild(document.createElement("b"));
    table1bodyTd1.appendChild(layer4);

    layer4 = document.createElement("div");
    layer4.id = "order-"+this.orderID+"_"+this.name+"-blocks";
    layer4.className = "order-block-cont-blocks-wrap";

    firstTab = true;
    for (var i in this.PopupEntityType) {
        var layer5 = document.createElement("div");
        layer5.id = "order-"+this.orderID+"_"+this.name+"-block-"+this.PopupEntityType[i];
        layer5.className = "order-block-cont-block order-block-cont-block-"+this.PopupEntityType[i];
        layer5.style.display = firstTab? "block": "none";
        layer4.appendChild(layer5);
        firstTab = false;
    }
    layer5 = document.createElement("div");
    layer5.id = "order-"+this.orderID+"_"+this.name+"-block-search";
    layer5.className = "order-block-cont-block";
    layer5.style.display = "none";
    layer4.appendChild(layer5);

    layer5 = document.createElement("div");
    layer5.id = "order-"+this.orderID+"_"+this.name+"-block-declared";
    layer5.className = "order-block-cont-block";
    layer5.style.display = "none";
    layer4.appendChild(layer5);

    table1bodyTd1.appendChild(layer4);
    table1bodyTr1.appendChild(table1bodyTd1);
    var table1bodyTd2 = document.createElement("TD");
    table1bodyTd2.className = "order-block-cont-right";

    var layer2 = document.createElement("div");
    layer2.className = "order-block-cont-right-wrap-item";

    for (var i in this.PopupEntityType) {
        var layer3 = document.createElement("div");
        layer3.className = "order-block-cont-right-item";
        layer3.id = "order-"+this.orderID+"_"+this.name+"-block-"+this.PopupEntityType[i]+"-selected";
        var layer3cont = document.createElement("span");
        layer3cont.className = "order-block-cont-right-title";
        layer3cont.appendChild(document.createTextNode(this.PopupLocalize[this.PopupEntityType[i]]));
        layer3cont.appendChild(document.createTextNode(' ('));
        var spanDigit = document.createElement("span");
        spanDigit.className = "order-block-cont-right-title-count";
        spanDigit.appendChild(document.createTextNode('0'));
        layer3cont.appendChild(spanDigit);
        layer3cont.appendChild(document.createTextNode(')'));
        layer3.appendChild(layer3cont);
        layer2.appendChild(layer3);
    }

    table1bodyTd2.appendChild(layer2);
    table1bodyTr1.appendChild(table1bodyTd2);
    table1body.appendChild(table1bodyTr1);
    table1.appendChild(table1body);
    layer1.appendChild(table1);


    var textBoxId = "order-"+this.orderID+"_"+this.name+"-text-box";
    if(BX(textBoxId))
    {
        throw  "Already exists " + textBoxId;
    }

    var orderBox = this.div;
    var textBox = document.createElement("div");
    orderBox.insertBefore(textBox, orderBox.firstChild);
    textBox.id = "order-"+this.orderID+"_"+this.name+"-text-box";

    var inputBox = document.createElement("div");
    inputBox.id = "order-"+this.orderID+"_"+this.name+"-input-box";
    inputBox.style.display = "none";
    orderBox.appendChild(inputBox);

    var placeHolder = document.createElement("div");
    orderBox.appendChild(placeHolder);
    placeHolder.id = "order-"+this.orderID+"_"+this.name+"-html-box";
    placeHolder.className = "order-place-holder";
    placeHolder.appendChild(layer1);
};
