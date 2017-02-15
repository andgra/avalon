if (!obOrder)
{
    var obOrder = {};
}
(function() {


    var BX = window.BX;
    if(BX.OrderStructure)
        return;


    BX.OrderStructure = function(id,containerId,obSelected,params)
    {
        this.id= id;
        this.bInit= false;
        this.waitDiv= null;
        this.waitPopup= null;
        this.obSelected= typeof (obSelected)!=='undefined'?obSelected:{};
        this.obClicked= {};
        this.containerId=containerId;
        this.popup= null;
        this.params=typeof (params)!=='undefined'?params:{};
        this.list={};
    };

    BX.OrderStructure.Set=function(id,containerId,obSelected,params)
    {

        if (obOrder[id])
        {
            obOrder[id].Clear();
            delete obOrder[id];
        }
        obOrder[id] = new BX.OrderStructure(id,containerId,obSelected,params);
        obOrder[id].Init();
        return obOrder[id];
    };

    BX.OrderStructure.prototype.Clear = function()
    {
        if (this.popup)
        {
            this.popup.destroy();
        }

        var cont = BX(this.containerId);

        var spanTitle = BX.findChild(cont, {tag: 'span', class: 'order-structure-info-title'}, true);
        if (spanTitle)
        {
            BX.cleanNode(spanTitle);
        }

        var inpType = BX.findChild(cont, {tag: 'input', class: 'order-structure-input-type'}, true);
        if (inpType)
        {
            inpType.value='';
        }

        var inpValue = BX.findChild(cont, {tag: 'input', class: 'order-structure-input-value'}, true);
        if (inpValue)
        {
            inpValue.value='';
        }
    };

    BX.OrderStructure.prototype.Init = function() {

        if (this.bInit)
            return;

        this.bInit = true;

        //console.log(this);
        this.SelectItem();

        var self = this;
        this.popup = BX.PopupWindowManager.create("BXOrderStructure_" + self.id, null, {
            autoHide: false,
            zIndex: 0,
            offsetLeft: 0,
            offsetTop: 0,
            draggable: {restrict: true},
            closeByEsc: true,
            titleBar: {
                content: BX.create("span", {
                    html: BX.message('js_order_structure_title'),
                    'props': {'className': 'order-structure-title-bar'}
                })
            },
            closeIcon: {right: "12px", top: "10px"},
            buttons: [
                new BX.PopupWindowButtonLink({
                    text: BX.message('js_order_structure_close'),
                    className: "popup-window-button-link-cancel",
                    events: {
                        click: function () {
                            this.popupWindow.close();
                        }
                    }
                })
            ],
            content: '<div class="order-structure-container"></div>',
            events: {
                onAfterPopupShow: function () {
                    self.showWait(this.contentContainer);
                    BX.ajax(
                        {
                            url:'/bitrix/tools/order/structure.php',
                            data:
                            {
                                lang: BX.message('LANGUAGE_ID'),
                                site_id: BX.message('SITE_ID') || '',
                                arParams: self.params,
                                id: self.id,
                                mode:'layout',
                                obSelected: self.obSelected
                            },
                            dataType:'json',
                            method:'POST',
                            onsuccess:
                                BX.delegate(function (result)
                                    {
                                        this.setContent(result['layout']);
                                        for(var ent in result['list']) {
                                            self.list[ent]=result['list'][ent];
                                        }
                                        $(".order-structure-section-title").click(function(){
                                            if($(this).hasClass('order-structure-disabled'))
                                                return false;
                                            var container=$(this).next();
                                            //console.log(container.get());
                                            if(container.css('display')=='none') {
                                                //slideUp all other blocks
                                                var siblings=container.siblings('.order-structure-block-container');
                                                siblings.slideUp(400);
                                                siblings.prev().children('.order-structure-vertical-drop').removeClass('order-structure-vertical-opened');

                                                container.slideDown(400);
                                                $(this).children('.order-structure-vertical-drop').addClass('order-structure-vertical-opened');
                                            } else {
                                                container.slideUp(400);
                                                $(this).children('.order-structure-vertical-drop').removeClass('order-structure-vertical-opened');
                                            }
                                        });


                                        $(".ul-dropfree").find("li:has(ul)").prepend('<div class="drop"></div>');
                                        $(".ul-dropfree div.drop").click(function(){
                                            if($(this).nextAll("ul").css('display')=='none') {
                                                $(this).nextAll("ul").slideDown(400);
                                                $(this).css({'background-position':"-11px 0"});
                                            } else {
                                                $(this).nextAll("ul").slideUp(400);
                                                $(this).css({'background-position':"0 0"});
                                            }
                                        });
                                        $(".ul-dropfree").find("ul").slideUp(0).parents("li").children("div.drop").css({'background-position':"0 0"});
                                        console.log(result);
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
    };

    BX.OrderStructure.prototype.ShowForm = function()
    {

        this.popup.params.zIndex = (BX.WindowManager? BX.WindowManager.GetZIndex() : 0);
        this.popup.show();
    };

    BX.OrderStructure.prototype.showWait = function(div)
    {
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

    BX.OrderStructure.prototype.closeWait = function()
    {
        if(this.waitPopup)
            this.waitPopup.close();
    };

    BX.OrderStructure.prototype.SelectDirection = function(id)
    {
        this.showWait(this.popup.contentContainer);
        var self=this;
        BX.ajax(
            {
                url:'/bitrix/tools/order/structure.php',
                data:
                {
                    lang: BX.message('LANGUAGE_ID'),
                    site_id: BX.message('SITE_ID') || '',
                    arParams: self.params,
                    id: self.id,
                    mode:'direction',
                    direction: id,
                    obSelected: self.obSelected
                },
                dataType:'json',
                method:'POST',
                onsuccess:
                    function(result)
                    {
                        console.log(result);
                        for(var ent in result['list']) {
                            self.list[ent]=result['list'][ent];
                        }
                        var cont=BX.findChild(self.popup.contentContainer,{tag:'div',className:'order-structure-container'});
                        var directionCont=BX.findChild(cont,{tag:'div',className:'order-structure-direction-container'},true);
                        var nomenCont=BX.findChild(cont,{tag:'div',className:'order-structure-nomen-container'},true);
                        if(nomenCont) {
                            var nomenList=BX.findChild(nomenCont,{tag:'div',className:'order-structure-list'},true);

                            BX.findChild(BX.findPreviousSibling(nomenCont),
                                {tag:'span',className:'order-structure-selected-item-title'},true)
                                .innerHTML='';

                            BX.findChild(nomenCont,
                                {tag:'input',className:'order-search-inp'},true)
                                .value='';

                            BX.findPreviousSibling(nomenCont).click();

                            nomenList.innerHTML=result['nomen'];

                        }
                        var groupCont=BX.findChild(cont,{tag:'div',className:'order-structure-group-container'},true);
                        if(groupCont) {
                            var groupList=BX.findChild(groupCont,{tag:'div',className:'order-structure-list'},true);

                            BX.findChild(groupCont,
                                {tag:'input',className:'order-search-inp'},true)
                                .value='';
                            groupList.innerHTML=result['group'];

                        }

                        BX.findChild(BX.findPreviousSibling(directionCont),
                            {tag:'span',className:'order-structure-selected-item-title'},true)
                            .innerHTML=self.list['direction'][id]['TITLE'];
                        //eval(result['script']);
                        self.closeWait();
                    }
            }
        );

    };

    BX.OrderStructure.prototype.SelectNomen = function(directId,id)
    {
        this.showWait(this.popup.contentContainer);
        var self=this;
        BX.ajax(
            {
                url:'/bitrix/tools/order/structure.php',
                data:
                {
                    lang: BX.message('LANGUAGE_ID'),
                    site_id: BX.message('SITE_ID') || '',
                    arParams: self.params,
                    id: self.id,
                    mode:'nomen',
                    direction: directId,
                    nomen: id,
                    obSelected: self.obSelected
                },
                dataType:'json',
                method:'POST',
                onsuccess:
                    function(result)
                    {
                        //console.log(result);
                        for(var ent in result['list']) {
                            self.list[ent]=result['list'][ent];
                        }
                        var cont=BX.findChild(self.popup.contentContainer,{tag:'div',className:'order-structure-container'});
                        var directionCont=BX.findChild(cont,{tag:'div',className:'order-structure-direction-container'},true);
                        var nomenCont=BX.findChild(cont,{tag:'div',className:'order-structure-nomen-container'},true);
                        var groupCont=BX.findChild(cont,{tag:'div',className:'order-structure-group-container'},true);
                        if(groupCont) {
                            var groupList=BX.findChild(groupCont,{tag:'div',className:'order-structure-list'},true);

                            BX.findChild(groupCont,
                                {tag:'input',className:'order-search-inp'},true)
                                .value='';
                            groupList.innerHTML=result['group'];

                            BX.findPreviousSibling(groupCont).click();
                        }



                        /*BX.findChild(BX.findPreviousSibling(directionCont),
                            {tag:'span',className:'order-structure-selected-item-title'},true)
                            .innerHTML=self.list['direction'][directId]['TITLE'];*/

                        BX.findChild(BX.findPreviousSibling(nomenCont),
                            {tag:'span',className:'order-structure-selected-item-title'},true)
                            .innerHTML=self.list['nomen'][id]['TITLE'];
                        self.closeWait();
                    }
            }
        );

    };

    BX.OrderStructure.prototype.SearchNomen = function(str)
    {
        this.showWait(this.popup.contentContainer);
        var self=this;
        BX.ajax(
            {
                url:'/bitrix/tools/order/structure.php',
                data:
                {
                    lang: BX.message('LANGUAGE_ID'),
                    site_id: BX.message('SITE_ID') || '',
                    arParams: self.params,
                    id: self.id,
                    mode:'search_nomen',
                    search: str,
                    obSelected: self.obSelected,
                    list: this.list['nomen']
                },
                dataType:'json',
                method:'POST',
                onsuccess:
                    function(result)
                    {
                        /*for(var ent in result['list']) {
                            self.list[ent]=result['list'][ent];
                        }*/
                        console.log(result);
                        var cont=BX.findChild(self.popup.contentContainer,{tag:'div',className:'order-structure-container'});
                        var nomenCont=BX.findChild(cont,{tag:'div',className:'order-structure-nomen-container'},true);
                        var nomenList=BX.findChild(nomenCont,{tag:'div',className:'order-structure-list'},true);

                        nomenList.innerHTML=result['nomen'];
                        self.closeWait();
                    }
            }
        );

    };

    BX.OrderStructure.prototype.SearchGroup = function(str)
    {
        this.showWait(this.popup.contentContainer);
        var self=this;
        BX.ajax(
            {
                url:'/bitrix/tools/order/structure.php',
                data:
                {
                    lang: BX.message('LANGUAGE_ID'),
                    site_id: BX.message('SITE_ID') || '',
                    arParams: self.params,
                    id: self.id,
                    mode:'search_group',
                    search: str,
                    obSelected: self.obSelected,
                    gList: this.list['group'],
                    fGList: this.list['formed_group']
                },
                dataType:'json',
                method:'POST',
                onsuccess:
                    function(result)
                    {
                        /*for(var ent in result['list']) {
                            self.list[ent]=result['list'][ent];
                        }*/
                        //console.log(result);
                        var cont=BX.findChild(self.popup.contentContainer,{tag:'div',className:'order-structure-container'});
                        var groupCont=BX.findChild(cont,{tag:'div',className:'order-structure-group-container'},true);
                        var groupList=BX.findChild(groupCont,{tag:'div',className:'order-structure-list'},true);

                        groupList.innerHTML=result['group'];

                        self.closeWait();
                    }
            }
        );

    };

    BX.OrderStructure.prototype.SelectItem = function(arrSel)
    {
        if(arrSel && arrSel['value'] && arrSel['type']) {
            this.obSelected = arrSel;
        } else {
            this.list[this.obSelected['type']]={};
            this.list[this.obSelected['type']][this.obSelected['value']]={};
            this.list[this.obSelected['type']][this.obSelected['value']]['TITLE']=this.obSelected['title'];
        }

        if(this.obSelected['value'] && this.obSelected['type']) {
            var cont = BX(this.containerId);
            var spanTitle = BX.findChild(cont, {tag: 'span', class: 'order-structure-info-title'}, true);
            var inpType = BX.findChild(cont, {tag: 'input', class: 'order-structure-input-type'}, true);
            var inpValue = BX.findChild(cont, {tag: 'input', class: 'order-structure-input-value'}, true);

            if(this.popup) {
                BX.removeClass(BX.findChild(this.popup.contentContainer, {
                    tag: 'a',
                    class: 'order-structure-a-selected'
                }, true), 'order-structure-a-selected');
                BX.addClass(BX('order_structure_btn_' + this.obSelected['type'] + '_' + this.obSelected['value']), 'order-structure-a-selected');
            }
            inpType.value = this.obSelected['type'];
            inpValue.value = this.obSelected['value'];

            BX.cleanNode(spanTitle);
            if(!this.params['selectable'] || this.params['selectable'].length>1)
                spanTitle.appendChild(document.createTextNode(BX.message('js_order_structure_title_' + this.obSelected['type']) + " | "));

            spanTitle.appendChild(BX.create(
                'a',
                {
                    props: {
                        href: BX.message('js_order_structure_path_to_' + this.obSelected['type']).replace('#' + this.obSelected['type'] + '_id#', this.obSelected['value']),
                        target: '_blank'
                    },
                    text: this.list[this.obSelected['type']][this.obSelected['value']]['TITLE']
                }
            ));
        }
        if(this.popup)
            this.popup.close();
    };

})();
