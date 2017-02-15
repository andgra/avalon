
function OrderSelectEntityInit()
{
	BX.Access.Init({other:{disabled:true}});
}

function OrderSelectEntity()
{
	//BX.Access.SetSelected(arOrderSelected, "orderPerms");
	var t={};
	t[assignedSelected]=true;
	BX.Access.SetSelected(t, "orderPerms");
	BX.Access.obAlreadySelected=t;
	BX.Access.ShowForm({ bind: "orderPerms", callback: OrderPermAddRow});
	BX.Access.obProviderNames = arProviderNames;
}

function GetProviderName(provider,id) {
	var name='';
	if(BX.Access.obProviderNames[provider]) {
		name = BX.Access.obProviderNames[provider].name;
		if(name=='' && BX.Access.obProviderNames[provider]['prefixes']) {
			for(var pNum in BX.Access.obProviderNames[provider]['prefixes']) {
				var re=new RegExp(BX.Access.obProviderNames[provider]['prefixes'][pNum]['pattern'], "g");
				if(re.test(id)) {
					name=BX.Access.obProviderNames[provider]['prefixes'][pNum]['prefix'];
				}
			}
		}
	}

	return name;
}
function OrderPermAddRow(obSelected)
{
	for(var provider in obSelected)
	{
		for(var id in obSelected[provider])
		{
			var el = BX(fieldID.toLowerCase()+'_wrap');
			var div = BX.findChild(el, {tag:'div',className:'order-link-pre-div'}, true, false);
			div.id = '';
			if (GetProviderName(provider,id) != '')
				div.innerHTML = '<b>'+GetProviderName(provider,id)+
					':</b> <a href="javascript:void(0)" name="orderUserSelect" onclick="OrderSelectEntity(); return false">'+obSelected[provider][id].name+'</a>';
			else
				div.innerHTML = '<a href="javascript:void(0)" name="orderUserSelect" onclick="OrderSelectEntity(); return false">'+obSelected[provider][id].name+'</a>';
			var input = BX.findChild(el, {tag:'input',property: {'type':'hidden','name':fieldID+'_VALUE'}}, true, false);
			input.value = id;
			assignedSelected=id;
		}
	}
}

BX.Finder.onAddItem = function(provider, type, element)
{
	elementId = BX(element).getAttribute('rel');

	if (BX.Finder.selectedElement[elementId])
	{
		if (BX.Finder.context == 'access')
		{
			for (var i = 0; i < BX.Finder.selectedElement[elementId].length; i++)
			{
				BX.removeClass(BX.Finder.selectedElement[elementId][i], 'bx-finder-box-item-selected');
			}
			BX.Access.RemoveSelection(provider, elementId);
		}
		else
			BX.Finder.onDeleteItem({'provider': provider, 'id': elementId});

		return false;
	}

	if (!BX.Finder.selectedElement[elementId])
		BX.Finder.selectedElement[elementId] = [];

	BX.Finder.selectedElement[elementId].push(element);

	BX.addClass(element, 'bx-finder-box-item-selected');

	if (type == 1)
	{
		elementTextBox = BX.findChild(element, { className : "bx-finder-box-item-text" }, true);
	}
	else if (type == 2)
	{
		elementTextBox = BX.findChild(element, { className : "bx-finder-box-item-t2-text" }, true);
	}
	else if (type == 3)
	{
		elementTextBox = BX.findChild(element, { className : "bx-finder-box-item-t3-name" }, true);
	}
	else if (type == 4)
	{
		elementTextBox = BX.findChild(element, { className : "bx-finder-box-item-t3-name" }, true);
	}
	else if (type == 5)
	{
		elementTextBox = BX.findChild(element, { className : "bx-finder-box-item-t5-name" }, true);
	}
	else if (type == 'structure')
	{
		elementTextBox = BX.findChild(element, { className : "bx-finder-company-department-employee-name" }, true);
	}
	else if (type == 'structure-checkbox')
	{
		elementTextBox = BX.findChild(element, { className : "bx-finder-company-department-check-text" }, true);
	}

	if (type == 'structure-checkbox')
		elementText = elementTextBox.getAttribute('rel');
	else
		elementText = elementTextBox.innerHTML;

	if (BX.Finder.context == 'access')
		BX.Access.AddSelection({'provider': provider, 'id': elementId, 'name': elementText});

	BX.Access.SaveLRU();

	BX.Access.SaveSelected();

	if(BX.Access.callback)
		BX.Access.callback(BX.Access.obSelected);

	BX.Access.popup.close();

	return false;
};