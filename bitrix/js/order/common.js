
if(typeof(cssQuery) !== "function")
{
	eval(function(p,a,c,k,e,d){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)d[e(c)]=k[c]||e(c);k=[function(e){return d[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('7 x=6(){7 1D="2.0.2";7 C=/\\s*,\\s*/;7 x=6(s,A){33{7 m=[];7 u=1z.32.2c&&!A;7 b=(A)?(A.31==22)?A:[A]:[1g];7 1E=18(s).1l(C),i;9(i=0;i<1E.y;i++){s=1y(1E[i]);8(U&&s.Z(0,3).2b("")==" *#"){s=s.Z(2);A=24([],b,s[1])}1A A=b;7 j=0,t,f,a,c="";H(j<s.y){t=s[j++];f=s[j++];c+=t+f;a="";8(s[j]=="("){H(s[j++]!=")")a+=s[j];a=a.Z(0,-1);c+="("+a+")"}A=(u&&V[c])?V[c]:21(A,t,f,a);8(u)V[c]=A}m=m.30(A)}2a x.2d;5 m}2Z(e){x.2d=e;5[]}};x.1Z=6(){5"6 x() {\\n  [1D "+1D+"]\\n}"};7 V={};x.2c=L;x.2Y=6(s){8(s){s=1y(s).2b("");2a V[s]}1A V={}};7 29={};7 19=L;x.15=6(n,s){8(19)1i("s="+1U(s));29[n]=12 s()};x.2X=6(c){5 c?1i(c):o};7 D={};7 h={};7 q={P:/\\[([\\w-]+(\\|[\\w-]+)?)\\s*(\\W?=)?\\s*([^\\]]*)\\]/};7 T=[];D[" "]=6(r,f,t,n){7 e,i,j;9(i=0;i<f.y;i++){7 s=X(f[i],t,n);9(j=0;(e=s[j]);j++){8(M(e)&&14(e,n))r.z(e)}}};D["#"]=6(r,f,i){7 e,j;9(j=0;(e=f[j]);j++)8(e.B==i)r.z(e)};D["."]=6(r,f,c){c=12 1t("(^|\\\\s)"+c+"(\\\\s|$)");7 e,i;9(i=0;(e=f[i]);i++)8(c.l(e.1V))r.z(e)};D[":"]=6(r,f,p,a){7 t=h[p],e,i;8(t)9(i=0;(e=f[i]);i++)8(t(e,a))r.z(e)};h["2W"]=6(e){7 d=Q(e);8(d.1C)9(7 i=0;i<d.1C.y;i++){8(d.1C[i]==e)5 K}};h["2V"]=6(e){};7 M=6(e){5(e&&e.1c==1&&e.1f!="!")?e:23};7 16=6(e){H(e&&(e=e.2U)&&!M(e))28;5 e};7 G=6(e){H(e&&(e=e.2T)&&!M(e))28;5 e};7 1r=6(e){5 M(e.27)||G(e.27)};7 1P=6(e){5 M(e.26)||16(e.26)};7 1o=6(e){7 c=[];e=1r(e);H(e){c.z(e);e=G(e)}5 c};7 U=K;7 1h=6(e){7 d=Q(e);5(2S d.25=="2R")?/\\.1J$/i.l(d.2Q):2P(d.25=="2O 2N")};7 Q=6(e){5 e.2M||e.1g};7 X=6(e,t){5(t=="*"&&e.1B)?e.1B:e.X(t)};7 17=6(e,t,n){8(t=="*")5 M(e);8(!14(e,n))5 L;8(!1h(e))t=t.2L();5 e.1f==t};7 14=6(e,n){5!n||(n=="*")||(e.2K==n)};7 1e=6(e){5 e.1G};6 24(r,f,B){7 m,i,j;9(i=0;i<f.y;i++){8(m=f[i].1B.2J(B)){8(m.B==B)r.z(m);1A 8(m.y!=23){9(j=0;j<m.y;j++){8(m[j].B==B)r.z(m[j])}}}}5 r};8(![].z)22.2I.z=6(){9(7 i=0;i<1z.y;i++){o[o.y]=1z[i]}5 o.y};7 N=/\\|/;6 21(A,t,f,a){8(N.l(f)){f=f.1l(N);a=f[0];f=f[1]}7 r=[];8(D[t]){D[t](r,A,f,a)}5 r};7 S=/^[^\\s>+~]/;7 20=/[\\s#.:>+~()@]|[^\\s#.:>+~()@]+/g;6 1y(s){8(S.l(s))s=" "+s;5 s.P(20)||[]};7 W=/\\s*([\\s>+~(),]|^|$)\\s*/g;7 I=/([\\s>+~,]|[^(]\\+|^)([#.:@])/g;7 18=6(s){5 s.O(W,"$1").O(I,"$1*$2")};7 1u={1Z:6(){5"\'"},P:/^(\'[^\']*\')|("[^"]*")$/,l:6(s){5 o.P.l(s)},1S:6(s){5 o.l(s)?s:o+s+o},1Y:6(s){5 o.l(s)?s.Z(1,-1):s}};7 1s=6(t){5 1u.1Y(t)};7 E=/([\\/()[\\]?{}|*+-])/g;6 R(s){5 s.O(E,"\\\\$1")};x.15("1j-2H",6(){D[">"]=6(r,f,t,n){7 e,i,j;9(i=0;i<f.y;i++){7 s=1o(f[i]);9(j=0;(e=s[j]);j++)8(17(e,t,n))r.z(e)}};D["+"]=6(r,f,t,n){9(7 i=0;i<f.y;i++){7 e=G(f[i]);8(e&&17(e,t,n))r.z(e)}};D["@"]=6(r,f,a){7 t=T[a].l;7 e,i;9(i=0;(e=f[i]);i++)8(t(e))r.z(e)};h["2G-10"]=6(e){5!16(e)};h["1x"]=6(e,c){c=12 1t("^"+c,"i");H(e&&!e.13("1x"))e=e.1n;5 e&&c.l(e.13("1x"))};q.1X=/\\\\:/g;q.1w="@";q.J={};q.O=6(m,a,n,c,v){7 k=o.1w+m;8(!T[k]){a=o.1W(a,c||"",v||"");T[k]=a;T.z(a)}5 T[k].B};q.1Q=6(s){s=s.O(o.1X,"|");7 m;H(m=s.P(o.P)){7 r=o.O(m[0],m[1],m[2],m[3],m[4]);s=s.O(o.P,r)}5 s};q.1W=6(p,t,v){7 a={};a.B=o.1w+T.y;a.2F=p;t=o.J[t];t=t?t(o.13(p),1s(v)):L;a.l=12 2E("e","5 "+t);5 a};q.13=6(n){1d(n.2D()){F"B":5"e.B";F"2C":5"e.1V";F"9":5"e.2B";F"1T":8(U){5"1U((e.2A.P(/1T=\\\\1v?([^\\\\s\\\\1v]*)\\\\1v?/)||[])[1]||\'\')"}}5"e.13(\'"+n.O(N,":")+"\')"};q.J[""]=6(a){5 a};q.J["="]=6(a,v){5 a+"=="+1u.1S(v)};q.J["~="]=6(a,v){5"/(^| )"+R(v)+"( |$)/.l("+a+")"};q.J["|="]=6(a,v){5"/^"+R(v)+"(-|$)/.l("+a+")"};7 1R=18;18=6(s){5 1R(q.1Q(s))}});x.15("1j-2z",6(){D["~"]=6(r,f,t,n){7 e,i;9(i=0;(e=f[i]);i++){H(e=G(e)){8(17(e,t,n))r.z(e)}}};h["2y"]=6(e,t){t=12 1t(R(1s(t)));5 t.l(1e(e))};h["2x"]=6(e){5 e==Q(e).1H};h["2w"]=6(e){7 n,i;9(i=0;(n=e.1F[i]);i++){8(M(n)||n.1c==3)5 L}5 K};h["1N-10"]=6(e){5!G(e)};h["2v-10"]=6(e){e=e.1n;5 1r(e)==1P(e)};h["2u"]=6(e,s){7 n=x(s,Q(e));9(7 i=0;i<n.y;i++){8(n[i]==e)5 L}5 K};h["1O-10"]=6(e,a){5 1p(e,a,16)};h["1O-1N-10"]=6(e,a){5 1p(e,a,G)};h["2t"]=6(e){5 e.B==2s.2r.Z(1)};h["1M"]=6(e){5 e.1M};h["2q"]=6(e){5 e.1q===L};h["1q"]=6(e){5 e.1q};h["1L"]=6(e){5 e.1L};q.J["^="]=6(a,v){5"/^"+R(v)+"/.l("+a+")"};q.J["$="]=6(a,v){5"/"+R(v)+"$/.l("+a+")"};q.J["*="]=6(a,v){5"/"+R(v)+"/.l("+a+")"};6 1p(e,a,t){1d(a){F"n":5 K;F"2p":a="2n";1a;F"2o":a="2n+1"}7 1m=1o(e.1n);6 1k(i){7 i=(t==G)?1m.y-i:i-1;5 1m[i]==e};8(!Y(a))5 1k(a);a=a.1l("n");7 m=1K(a[0]);7 s=1K(a[1]);8((Y(m)||m==1)&&s==0)5 K;8(m==0&&!Y(s))5 1k(s);8(Y(s))s=0;7 c=1;H(e=t(e))c++;8(Y(m)||m==1)5(t==G)?(c<=s):(s>=c);5(c%m)==s}});x.15("1j-2m",6(){U=1i("L;/*@2l@8(@\\2k)U=K@2j@*/");8(!U){X=6(e,t,n){5 n?e.2i("*",t):e.X(t)};14=6(e,n){5!n||(n=="*")||(e.2h==n)};1h=1g.1I?6(e){5/1J/i.l(Q(e).1I)}:6(e){5 Q(e).1H.1f!="2g"};1e=6(e){5 e.2f||e.1G||1b(e)};6 1b(e){7 t="",n,i;9(i=0;(n=e.1F[i]);i++){1d(n.1c){F 11:F 1:t+=1b(n);1a;F 3:t+=n.2e;1a}}5 t}}});19=K;5 x}();',62,190,'|||||return|function|var|if|for||||||||pseudoClasses||||test|||this||AttributeSelector|||||||cssQuery|length|push|fr|id||selectors||case|nextElementSibling|while||tests|true|false|thisElement||replace|match|getDocument|regEscape||attributeSelectors|isMSIE|cache||getElementsByTagName|isNaN|slice|child||new|getAttribute|compareNamespace|addModule|previousElementSibling|compareTagName|parseSelector|loaded|break|_0|nodeType|switch|getTextContent|tagName|document|isXML|eval|css|_1|split|ch|parentNode|childElements|nthChild|disabled|firstElementChild|getText|RegExp|Quote|x22|PREFIX|lang|_2|arguments|else|all|links|version|se|childNodes|innerText|documentElement|contentType|xml|parseInt|indeterminate|checked|last|nth|lastElementChild|parse|_3|add|href|String|className|create|NS_IE|remove|toString|ST|select|Array|null|_4|mimeType|lastChild|firstChild|continue|modules|delete|join|caching|error|nodeValue|textContent|HTML|prefix|getElementsByTagNameNS|end|x5fwin32|cc_on|standard||odd|even|enabled|hash|location|target|not|only|empty|root|contains|level3|outerHTML|htmlFor|class|toLowerCase|Function|name|first|level2|prototype|item|scopeName|toUpperCase|ownerDocument|Document|XML|Boolean|URL|unknown|typeof|nextSibling|previousSibling|visited|link|valueOf|clearCache|catch|concat|constructor|callee|try'.split('|'),0,{}));
}

if(typeof(BX.OrderUserSearchPopup) === 'undefined')
{
	BX.OrderUserSearchPopup = function()
	{
		this._id = '';
		this._search_input = null;
		this._data_input = null;
		this._componentName = '';
		this._componentContainer = null;
		this._componentObj = null;
		this._serviceContainer = null;
		this._zIndex = 0;
		this._dlg = null;
		this._dlgDisplayed = false;
		this._currentUser = {};

		this._searchKeyHandler = BX.delegate(this._handleSearchKey, this);
		this._searchFocusHandler = BX.delegate(this._handleSearchFocus, this);
		this._externalClickHandler = BX.delegate(this._handleExternalClick, this);
		this._userSelectorInitCounter = 0;
	};

	BX.OrderUserSearchPopup.prototype =
	{
		//initialize: function(id, search_input, data_input, componentName, user, serviceContainer, zIndex)
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : ('order_user_search_popup_' + Math.random());

			if(!settings)
			{
				settings = {};
			}

			if(!BX.type.isElementNode(settings['searchInput']))
			{
				throw  "BX.OrderUserSearchPopup: 'search_input' is not defined!";
			}
			this._search_input = settings['searchInput'];

			if(!BX.type.isElementNode(settings['dataInput']))
			{
				throw  "BX.OrderUserSearchPopup: 'data_input' is not defined!";
			}
			this._data_input = settings['dataInput'];

			if(!BX.type.isNotEmptyString(settings['componentName']))
			{
				throw  "BX.OrderUserSearchPopup: 'componentName' is not defined!";
			}

			this._currentUser = settings['user'] ? settings['user'] : {};
			this._componentName = settings['componentName'];
			this._componentContainer = BX(this._componentName + '_selector_content');

			this._initializeUserSelector();
			this._adjustUser();

			this._serviceContainer = settings['serviceContainer'] ? settings['serviceContainer'] : document.body;
			this.setZIndex(settings['zIndex']);
		},
		_initializeUserSelector: function()
		{
			var objName = 'O_' + this._componentName;
			if(!window[objName])
			{
				if(this._userSelectorInitCounter === 10)
				{
					throw "BX.OrderUserSearchPopup: Could not find '"+ objName +"' user selector!";
				}

				this._userSelectorInitCounter++;
				window.setTimeout(BX.delegate(this._initializeUserSelector, this), 200);
				return;
			}

			this._componentObj = window[objName];
			this._componentObj.onSelect = BX.delegate(this._handleUserSelect, this);
			this._componentObj.searchInput = this._search_input;

			if(this._currentUser)
			{
				this._componentObj.setSelected([ this._currentUser ]);
			}

			BX.bind(this._search_input, 'keyup', this._searchKeyHandler);
			BX.bind(this._search_input, 'focus', this._searchFocusHandler);
			BX.bind(document, 'click', this._externalClickHandler);
		},
		open: function()
		{
			this._componentContainer.style.display = '';
			this._dlg = new BX.PopupWindow(
				this._id,
				this._search_input,
				{
					autoHide: false,
					draggable: false,
					closeByEsc: true,
					offsetLeft: 0,
					offsetTop: 0,
					zIndex: this._zIndex,
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
								this._componentContainer.parentNode.removeChild(this._componentContainer);
								this._serviceContainer.appendChild(this._componentContainer);
								this._componentContainer.style.display = 'none';
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
			//var container = BX.findParent(this._search_input, { className: 'webform-field-textbox' });
			if(parseInt(this._currentUser['id']) > 0)
			{
				this._data_input.value = this._currentUser['id'];
				this._search_input.value = this._currentUser['name'] ? this._currentUser.name : this._currentUser['id'];
				//BX.removeClass(container, 'webform-field-textbox-empty');
			}
			else
			{
				this._data_input.value = this._search_input.value = '';
				//BX.addClass(container, 'webform-field-textbox-empty');
			}
		},
		getZIndex: function()
		{
			return this._zIndex;
		},
		setZIndex: function(zIndex)
		{
			if(typeof(zIndex) === 'undefined' || zIndex === null)
			{
				zIndex = 0;
			}

			var i = parseInt(zIndex);
			this._zIndex = !isNaN(i) ? i : 0;
		},
		close: function()
		{
			if(this._dlg)
			{
				this._dlg.close();
			}
		},
		select: function(user)
		{
			this._currentUser = user;
			this._adjustUser();
			if(this._componentObj)
			{
				this._componentObj.setSelected([ user ]);
			}
		},
		_onBeforeDelete: function()
		{
			if(BX.type.isElementNode(this._search_input))
			{
				BX.unbind(this._search_input, 'keyup', this._searchKeyHandler);
				BX.unbind(this._search_input, 'focus', this._searchFocusHandler);
			}
			BX.unbind(document, 'click', this._externalClickHandler);

			if(this._componentContainer)
			{
				BX.remove(this._componentContainer);
				this._componentContainer = null;
			}
		},
		_handleExternalClick: function(e)
		{
			if(!e)
			{
				e = window.event;
			}

			if(!this._dlgDisplayed)
			{
				return;
			}

			var target = null;
			if(e)
			{
				if(e.target)
				{
					target = e.target;
				}
				else if(e.srcElement)
				{
					target = e.srcElement;
				}
			}

			if(target !== this._search_input &&
				!BX.findParent(target, { attribute:{ id: this._componentName + '_selector_content' } }))
			{
				this._adjustUser();
				this.close();
			}
		},
		_handleSearchKey: function(e)
		{
			if(!this._dlg || !this._dlgDisplayed)
			{
				this.open();
			}

			this._componentObj.search();
		},
		_handleSearchFocus: function(e)
		{
			if(!this._dlg || !this._dlgDisplayed)
			{
				this.open();
			}

			this._componentObj._onFocus(e);
		},
		_handleUserSelect: function(user)
		{
			this._currentUser = user;
			this._adjustUser();
			this.close();
		}
	};

	BX.OrderUserSearchPopup.items = {};

	BX.OrderUserSearchPopup.create = function(id, settings, delay)
	{
		if(isNaN(delay))
		{
			delay = 0;
		}

		if(delay > 0)
		{
			window.setTimeout(
				function(){ BX.OrderUserSearchPopup.create(id, settings, 0); },
				delay
			);
			return null;
		}

		var self = new BX.OrderUserSearchPopup();
		self.initialize(id, settings);
		this.items[id] = self;
		return self;
	};

	BX.OrderUserSearchPopup.createIfNotExists = function(id, settings)
	{
		var self = this.items[id];
		if(typeof(self) !== 'undefined')
		{
			self.initialize(id, settings);
		}
		else
		{
			self = new BX.OrderUserSearchPopup();
			self.initialize(id, settings);
			this.items[id] = self;
		}
		return self;
	};

	BX.OrderUserSearchPopup.deletePopup = function(id)
	{
		var item = this.items[id];
		if(typeof(item) === 'undefined')
		{
			return false;
		}

		item._onBeforeDelete();
		delete this.items[id];
		return true;
	}
}

if(typeof(BX.OrderNotifier) === "undefined")
{
	BX.OrderNotifier = function()
	{
		this._sender = null;
		this._listeners = [];
	};

	BX.OrderNotifier.prototype =
	{
		initialize: function(sender)
		{
			this._sender = sender;
		},
		addListener: function(listener)
		{
			if(!BX.type.isFunction(listener))
			{
				return;
			}

			for(var i = 0; i < this._listeners.length; i++)
			{
				if(this._listeners[i] === listener)
				{
					return;
				}
			}

			this._listeners.push(listener);
		},
		removeListener: function(listener)
		{
			if(!BX.type.isFunction(listener))
			{
				return;
			}

			for(var i = 0; i < this._listeners.length; i++)
			{
				if(this._listeners[i] === listener)
				{
					this._listeners.splice(i, 1);
					return;
				}
			}
		},
		notify: function(params)
		{
			//Make copy of listeners to process addListener/removeListener while notification under way.
			var ary = [];
			for(var i = 0; i < this._listeners.length; i++)
			{
				ary.push(this._listeners[i]);
			}

			if(!BX.type.isArray(params))
			{
				params = [];
			}

			params.splice(0, 0, this._sender);

			for(var j = 0; j < ary.length; j++)
			{
				try
				{
					ary[j].apply(this._sender, params);
				}
				catch(ex)
				{
				}
			}
		},
		getListenerCount: function()
		{
			return this._listeners.length;
		}
	};

	BX.OrderNotifier.create = function(sender)
	{
		var self = new BX.OrderNotifier();
		self.initialize(sender);
		return self;
	}
}

if(typeof(BX.OrderSelectorItem) === "undefined")
{
	BX.OrderSelectorItem = function()
	{
		this._parent = null;
		this._settings = {};
		this._onSelectNotifier = null;
	};

	BX.OrderSelectorItem.prototype =
	{
		initialize: function(settings)
		{
			this._settings = settings;
			this._onSelectNotifier = BX.OrderNotifier.create(this);
			var events = this.getSetting("events");
			if(events && events['select'])
			{
				this._onSelectNotifier.addListener(events['select']);
			}
		},
		getSetting: function(name, defaultval)
		{
			var s = this._settings;
			return typeof(s[name]) != "undefined" ? s[name] : defaultval;
		},
		getValue: function()
		{
			return this.getSetting("value", "");
		},
		getText: function()
		{
			var text = this.getSetting("text");
			return BX.type.isNotEmptyString(text) ? text : this.getValue();
		},
		isEnabled: function()
		{
			return this.getSetting("enabled", true);
		},
		isDefault: function()
		{
			return this.getSetting("default", false);
		},
		createMenuItem: function()
		{
			return(
			{
				"text":  this.getText(),
				"onclick": BX.delegate(this._onClick, this)
			});
		},
		addOnSelectListener: function(listener)
		{
			this._onSelectNotifier.addListener(listener);
		},
		removeOnSelectListener: function(listener)
		{
			this._onSelectNotifier.removeListener(listener);
		},
		_onClick: function()
		{
			this._onSelectNotifier.notify();
		}
	};

	BX.OrderSelectorItem.create = function(settings)
	{
		var self = new BX.OrderSelectorItem();
		self.initialize(settings);
		return self;
	};
}

if(typeof(BX.OrderSelector) === "undefined")
{
	BX.OrderSelector = function()
	{
		this._id = "";
		this._selectedValue = "";
		this._settings = {};
		this._outerWrapper = this._wrapper = this._container = this._view = null;
		this._items = [];
		this._onSelectNotifier = null;
		this._popup = null;
		this._isPopupShown = false;
	};

	BX.OrderSelector.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : ("order_selector_" + Math.random().toString().substring(2));
			this._settings = settings ? settings : {};
			this._container = this.getSetting("container", null);
			this._selectedValue = this.getSetting("selectedValue", "");

			var itemData = this.getSetting("items");
			itemData = BX.type.isArray(itemData) ? itemData : [];
			this._items = [];
			for(var i = 0; i < itemData.length; i++)
			{
				var item = BX.OrderSelectorItem.create(itemData[i]);
				item.addOnSelectListener(BX.delegate(this._onItemSelect, this));
				this._items.push(item);
			}

			this._onSelectNotifier = BX.OrderNotifier.create(this);
		},
		getId: function()
		{
			return this._id;
		},
		getSetting: function (name, defaultval)
		{
			var s = this._settings;
			return typeof(s[name]) != "undefined" ? s[name] : defaultval;
		},
		isEnabled: function()
		{
			return this.getSetting('enabled', true);
		},
		layout: function(container)
		{
			if(BX.type.isDomNode(container))
			{
				this._container = container;
			}
			else if(this._container)
			{
				container = this._container;
			}

			if(!container)
			{
				return;
			}

			var isEnabled = this.isEnabled();

			var layout = this.getSetting('layout');
			if(!layout)
			{
				layout = {};
			}

			var outerWrapper = this._outerWrapper = BX.create(
				"DIV",
				{
					"attrs":
					{
						"className": "order-selector-container",
						"id": this._id
					}
				}
			);

			if(layout['position'] === 'first')
			{
				container.insertBefore(outerWrapper, BX.firstChild(container));
			}
			else if(layout['insertBefore'])
			{
				container.insertBefore(outerWrapper, BX.findChild(container, layout['insertBefore']));
			}
			else
			{
				container.appendChild(outerWrapper);
			}

			var title = this.getSetting("title", "");
			if(BX.type.isNotEmptyString(title))
			{
				outerWrapper.appendChild(
					BX.create(
						"SPAN",
						{
							"attrs":
							{
								"className": "order-selector-title"
							},
							"text": title + ':'
						}
					)
				);
			}

			var wrapper = this._wrapper = BX.create(
				"DIV",
				{
					"attrs":
					{
						"className": "order-selector-wrapper"
					}
				}
			);
			outerWrapper.appendChild(wrapper);

			var onClickHandler = BX.delegate(this._onClick, this);

			var innerWrapper = BX.create(
				"DIV",
				{
					"attrs":
					{
						"className": "order-selector-inner-wrapper"
					}
				}
			);
			if(isEnabled)
			{
				BX.bind(innerWrapper, "click", onClickHandler);
			}
			wrapper.appendChild(innerWrapper);

			var selectItem = this._findItemByValue(this._selectedValue);
			if(!selectItem)
			{
				selectItem = this.getDefaultItem();
			}

			var view = this._view = BX.create(
				"SPAN",
				{
					"attrs":
					{
						"className": "order-selector-view"
					},
					"text": selectItem ? selectItem.getText() : ""
				}
			);
			innerWrapper.appendChild(view);

			if(isEnabled)
			{
				innerWrapper.appendChild(
					BX.create(
						"A",
						{
							"attrs":
							{
								"className": "order-selector-arrow"
							},
							"events":
							{
								"click": onClickHandler
							},
							"html": "&nbsp;"
						}
					)
				);
			}
		},
		clearLayout: function()
		{
			if(!this._outerWrapper)
			{
				return;
			}

			BX.remove(this._outerWrapper);
			this._outerWrapper = null;
		},
		getItems: function()
		{
			return this._items;
		},
		selectValue: function(value)
		{
			this.selectItem(this._findItemByValue(value));
		},
		selectItem: function(item)
		{
			if(!item)
			{
				return;
			}

			this._selectedValue = item.getValue();
			if(this._view)
			{
				this._view.innerHTML = BX.util.htmlspecialchars(item.getText());
			}
		},
		getSelectedValue: function()
		{
			return this._selectedValue;
		},
		getSelectedItem: function()
		{
			return this._findItemByValue(this._selectedValue);
		},
		getDefaultItem: function()
		{
			var items = this.getItems();
			for(var i = 0; i < items.length; i++)
			{
				var item = items[i];
				if(item.isDefault())
				{
					return item;
				}
			}

			return null;
		},
		showPopup: function()
		{
			if(this._isPopupShown)
			{
				return;
			}

			var menuItems = [];
			for(var i = 0; i < this._items.length; i++)
			{
				var item = this._items[i];
				if(item.isEnabled())
				{
					menuItems.push(item.createMenuItem());
				}
			}

			BX.PopupMenu.show(
				this._id,
				this._wrapper,
				menuItems,
				{
					"offsetTop": 0,
					"offsetLeft": 0,
					"events":
					{
						"onPopupShow": BX.delegate(this._onPopupShow, this),
						"onPopupClose": BX.delegate(this._onPopupClose, this),
						"onPopupDestroy": BX.delegate(this._onPopupDestroy, this)
					}
				}
			);
			this._popup = BX.PopupMenu.currentItem;
		},
		addOnSelectListener: function(listener)
		{
			this._onSelectNotifier.addListener(listener);
		},
		removeOnSelectListener: function(listener)
		{
			this._onSelectNotifier.removeListener(listener);
		},
		_findItemByValue: function(value)
		{
			var items = this.getItems();
			for(var i = 0; i < items.length; i++)
			{
				var item = items[i];
				if(value === item.getValue())
				{
					return item;
				}
			}

			return null;
		},
		_onClick: function(e)
		{
			e = e ? e : window.event;
			BX.PreventDefault(e);
			if(this.isEnabled())
			{
				this.showPopup();
			}
		},
		_onItemSelect: function(item)
		{
			this.selectItem(item);

			if(this._popup)
			{
				if(this._popup.popupWindow)
				{
					this._popup.popupWindow.close();
				}
			}

			this._onSelectNotifier.notify([item]);
		},
		_onPopupShow: function()
		{
			this._isPopupShown = true;
		},
		_onPopupClose: function()
		{
			if(this._popup)
			{
				if(this._popup.popupWindow)
				{
					this._popup.popupWindow.destroy();
				}
			}
		},
		_onPopupDestroy: function()
		{
			this._isPopupShown = false;
			this._popup = null;

			if(typeof(BX.PopupMenu.Data[this._id]) !== "undefined")
			{
				delete(BX.PopupMenu.Data[this._id]);
			}
		}
	};

	BX.OrderSelector.create = function(id, settings)
	{
		var self = new BX.OrderSelector();
		self.initialize(id, settings);
		this.items[self.getId()] = self;
		return self;
	};

	BX.OrderSelector.deleteItem = function(id)
	{
		if(this.items[id])
		{
			this.items[id].clearLayout();
			delete this.items[id];
		}
	};

	BX.OrderSelector.items = {};
}

if(typeof(BX.OrderInterfaceFormUtil) === "undefined")
{
	BX.OrderInterfaceFormUtil = function(){};
	BX.OrderInterfaceFormUtil.disableThemeSelection = function(formId)
	{
		var form = window["bxForm_" + formId];
		var menu = form ? form.settingsMenu : null;
		if(!menu)
		{
			return;
		}

		for(var i = 0; i < menu.length; i++)
		{
			if(menu[i] && menu[i].ICONCLASS === "form-themes")
			{
				menu.splice(i, 1);
				break;
			}
		}

		if(menu.length === 0)
		{
			var btn = BX.findChild(BX("form_" + formId), { "tag":"A", "class": "bx-context-button bx-form-menu" }, true);
			if(btn)
			{
				btn.style.display = "none";
			}
		}
	};

	BX.OrderInterfaceFormUtil.showFormRow = function(show, element)
	{
		var row = BX.findParent(element, {'tag': 'TR'});
		if(row)
		{
			row.style.display = !!show ? '' : 'none';
		}
	}
}

if(typeof(BX.OrderParamBag) === "undefined")
{
	BX.OrderParamBag = function()
	{
		this._params = {};
	};

	BX.OrderParamBag.prototype =
	{
		initialize: function(params)
		{
			this._params = params ? params : {};
		},
		getParam: function(name, defaultvalue)
		{
			var p = this._params;
			return typeof(p[name]) != "undefined" ? p[name] : defaultvalue;
		},
		getIntParam: function(name, defaultvalue)
		{
			if(typeof(defaultvalue) === "undefined")
			{
				defaultvalue = 0;
			}
			var p = this._params;
			return typeof(p[name]) != "undefined" ? parseInt(p[name]) : defaultvalue;
		},
		getBooleanParam: function(name, defaultvalue)
		{
			if(typeof(defaultvalue) === "undefined")
			{
				defaultvalue = 0;
			}
			var p = this._params;
			return typeof(p[name]) != "undefined" ? !!p[name] : defaultvalue;
		},
		setParam: function(name, value)
		{
			this._params[name] = value;
		},
		clear: function()
		{
			this._params = {};
		}
	};

	BX.OrderParamBag.create = function(params)
	{
		var self = new BX.OrderParamBag();
		self.initialize(params);
		return self;
	}
}

if(typeof(BX.OrderSubscriber) === "undefined")
{
	BX.OrderSubscriber = function()
	{
		this._id = "";
		this._element = null;
		this._eventName = "";
		this._callback = null;
		this._settings = null;
		this._handler = BX.delegate(this._onElementEvent, this);
	};

	BX.OrderSubscriber.prototype =
	{
		initialize: function(id, element, eventName, callback, settings)
		{
			this._id = id;
			this._element = element;
			this._eventName = eventName;
			this._callback = callback;
			this._settings = settings ? settings : BX.OrderParamBag.create(null);
		},
		getSetting: function(name, defaultvalue)
		{
			return this._settings.getParam(name, defaultvalue);
		},
		setSetting: function(name, value)
		{
			return this._settings.setParam(name, value);
		},
		getId: function()
		{
			return this._id;
		},
		getElement: function()
		{
			return this._element;
		},
		getEventName: function()
		{
			return this._eventName;
		},
		getCallback: function()
		{
			return this._callback;
		},
		subscribe: function()
		{
			BX.bind(this.getElement(), this.getEventName(), this._handler);
		},
		unsubscribe: function()
		{
			BX.unbind(this.getElement(), this.getEventName(), this._handler);
		},
		_onElementEvent: function(e)
		{
			var callback = this.getCallback();
			if(BX.type.isFunction(callback))
			{
				callback(this, { "event": e });
			}

			return this.getSetting("preventDefault", false) ? BX.PreventDefault(e) : true;
		}
	};

	BX.OrderSubscriber.items = {};
	BX.OrderSubscriber.create = function(id, element, eventName, callback, settings)
	{
		var self = new BX.OrderSubscriber();
		self.initialize(id, element, eventName, callback, settings);
		this.items[id] = self;
		return self;
	};

	BX.OrderSubscriber.subscribe = function(id, element, eventName, callback, settings)
	{
		var self = this.create(id, element, eventName, callback, settings);
		self.subscribe();
		return self;
	}
}

if(typeof(BX.OrderMultiFieldViewer) === "undefined")
{
	BX.OrderMultiFieldViewer = function()
	{
		this._id = '';
		this._shown = false;
		this._layout = '';
		this._typeName = '';
	};

	BX.OrderMultiFieldViewer.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};
			this._layout = this.getSetting('layout', 'grid').toLowerCase();
			this._typeName = this.getSetting('typeName', '');

		},
		getSetting: function (name, defaultval)
		{
			return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
		},
		show: function()
		{
			if(this._shown)
			{
				return;
			}

			var tab = BX.create('TABLE');

			tab.cellSpacing = '0';
			tab.cellPadding = '0';
			tab.border = '0';


			var className = 'bx-order-grid-multi-field-viewer';
			var enableSip = false;
			var items = this.getSetting('items', []);
			for(var i = 0; i < items.length; i++)
			{
				var item = items[i];

				var r = tab.insertRow(-1);
				var valueCell = r.insertCell(-1);

				var itemHtml = item['value'];
				var itemClassName = "order-detail-info-item-text";
				if(this._typeName === "PHONE" && BX.type.isNotEmptyString(item['sipCallHtml']))
				{
					if(!enableSip)
					{
						enableSip = true;
					}
					itemHtml += item['sipCallHtml'];
					itemClassName += " order-detail-info-item-handset";
				}
				valueCell.appendChild(BX.create('SPAN', { attrs: { className: itemClassName }, html: itemHtml }));
				var typeCell = r.insertCell(-1);
				typeCell.appendChild(
					BX.create(
						'SPAN',
						{
							attrs: { className: 'order-multi-field-value-type' },
							text: BX.type.isNotEmptyString(item['type']) ? item['type'] : ''
						}
					)
				);
			}

			if(enableSip)
			{
				className += ' bx-order-grid-multi-field-viewer-tel-sip';
			}

			tab.className = className;

			var dlg = BX.OrderMultiFieldViewer.dialogs[this._id] ? BX.OrderMultiFieldViewer.dialogs[this._id] : null;
			if(!dlg)
			{
				var anchor = this.getSetting('anchor');
				if(!BX.type.isElementNode(anchor))
				{
					anchor = BX(this.getSetting('anchorId', ''));
				}

				var topmost = !!this.getSetting('topmost', false);
				dlg = new BX.PopupWindow(
					this._id,
					anchor,
					{
						autoHide: true,
						draggable: false,
						offsetLeft: 0,
						offsetTop: 0,
						bindOptions: { forceBindPosition: true },
						closeByEsc: true,
						zIndex: topmost ? -10 : -14,
						className: 'order-item-popup-num-block',
						events:
						{
							onPopupShow: BX.delegate(
								function()
								{
									this._shown = true;
								},
								this
							),
							onPopupClose: BX.delegate(
								function()
								{
									this._shown = false;
									BX.OrderMultiFieldViewer.dialogs[this._id].destroy();
								},
								this
							),
							onPopupDestroy: BX.delegate(
								function()
								{
									delete(BX.OrderMultiFieldViewer.dialogs[this._id]);
								},
								this
							)
						},
						content: tab
					}
				);
				BX.OrderMultiFieldViewer.dialogs[this._id] = dlg;
			}

			dlg.show();
		},
		close: function()
		{
			if(this._shown && typeof(BX.OrderMultiFieldViewer.dialogs[this._id]) !== 'undefined')
			{
				BX.OrderMultiFieldViewer.dialogs[this._id].close();
			}
		}
	};
	BX.OrderMultiFieldViewer.items = {};
	BX.OrderMultiFieldViewer.create = function(id, settings)
	{
		var self = new BX.OrderMultiFieldViewer();
		self.initialize(id, settings);
		this.items[id] = self;
		return self;
	};
	BX.OrderMultiFieldViewer.ensureCreated = function(id, settings)
	{
		return this.items[id] ? this.items[id] : this.create(id, settings);
	};
	BX.OrderMultiFieldViewer.dialogs = {};
}

if(typeof(BX.OrderSipManager) === "undefined")
{
	BX.OrderSipManager = function()
	{
		this._id = "";
		this._settings = null;
		this._serviceUrls = {};
		this._recipientInfos = {};
	};

	BX.OrderSipManager.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : BX.OrderParamBag.create(null);
		},
		getId: function()
		{
			return this._id;
		},
		getSetting: function(name, defaultvalue)
		{
			return this._settings.getParam(name, defaultvalue);
		},
		setSetting: function(name, value)
		{
			return this._settings.setParam(name, value);
		},
		openPreCallDialog: function(recipient, params, anchor, callback)
		{
			if(!recipient || typeof(recipient) !== "object")
			{
				return;
			}

			if(!params || typeof(params) !== "object")
			{
				params = {};
			}

			var entityType = BX.type.isNotEmptyString(params["ENTITY_TYPE"]) ? params["ENTITY_TYPE"] : "";
			var entityId = BX.type.isNotEmptyString(params["ENTITY_ID"]) ? params["ENTITY_ID"] : "";
			var dlgId = entityType + '_' + entityId.toString();

			var dlg = BX.OrderPreCallDialog.create(dlgId,
				BX.OrderParamBag.create(
					{
						recipient: recipient,
						params: params,
						anchor: anchor,
						closeCallback: callback
					}
				)
			);
			dlg.show();
		},
		setServiceUrl: function(entityTypeName, serviceUrl)
		{
			if(BX.type.isNotEmptyString(entityTypeName) && BX.type.isNotEmptyString(serviceUrl))
			{
				this._serviceUrls[entityTypeName] = serviceUrl;
			}
		},
		getServiceUrl: function(entityTypeName)
		{
			return BX.type.isNotEmptyString(entityTypeName)
			&& this._serviceUrls.hasOwnProperty(entityTypeName)
				? this._serviceUrls[entityTypeName] : "";
		},
		makeCall: function(recipient, params)
		{
			var number = BX.type.isNotEmptyString(recipient["number"]) ? recipient["number"] : "";
			if(number == "")
			{
				return;
			}

			var entityTypeName = BX.type.isNotEmptyString(params["ENTITY_TYPE"]) ? params["ENTITY_TYPE"] : "";
			var entityId = BX.type.isNotEmptyString(params["ENTITY_ID"]) ? parseInt(params["ENTITY_ID"]) : 0;
			if(!(entityTypeName !== "" && entityId > 0))
			{
				entityTypeName = BX.type.isNotEmptyString(recipient["entityTypeName"]) ? recipient["entityTypeName"] : "";
				if(entityTypeName !== "")
				{
					entityTypeName = "ORDER_" + entityTypeName.toUpperCase();
				}
				params["ENTITY_TYPE"] = entityTypeName;
				params["ENTITY_ID"] = typeof(recipient["entityId"]) !== "undefined" ? parseInt(recipient["entityId"]) : 0;
			}

			var handlers = [];
			BX.onCustomEvent(
				window,
				'ORDER_SIP_MANAGER_MAKE_CALL',
				[this, recipient, params, handlers]
			);

			if(BX.type.isArray(handlers) && handlers.length > 0)
			{
				for(var i = 0; i < handlers.length; i++)
				{
					var handler = handlers[i];
					if(BX.type.isFunction(handler))
					{
						try
						{
							handler(recipient, params);
						}
						catch(ex)
						{
						}
					}
				}
			}
			else if(typeof(window["BXIM"]) !== "undefined")
			{
				window["BXIM"].phoneTo(number, params);
			}
		},
		startCall: function(recipient, params, enablePreCallDialog, anchor)
		{
			enablePreCallDialog = !!enablePreCallDialog;
			if(enablePreCallDialog)
			{
				var enableInfoLoading = typeof(recipient["enableInfoLoading"]) ? recipient["enableInfoLoading"] : false;
				if(enableInfoLoading)
				{
					var entityType = BX.type.isNotEmptyString(params["ENTITY_TYPE"]) ? params["ENTITY_TYPE"] : "";
					var entityId = BX.type.isNotEmptyString(params["ENTITY_ID"]) ? params["ENTITY_ID"] : "";
					var key = entityType + '_' + entityId.toString();
					if(this._recipientInfos.hasOwnProperty(key))
					{
						var info = this._recipientInfos[key];
						recipient["title"] = BX.type.isNotEmptyString(info["title"]) ? info["title"] : "";
						recipient["legend"] = BX.type.isNotEmptyString(info["legend"]) ? info["legend"] : "";
						recipient["imageUrl"] = BX.type.isNotEmptyString(info["imageUrl"]) ? info["imageUrl"] : "";
						recipient["showUrl"] = BX.type.isNotEmptyString(info["showUrl"]) ? info["showUrl"] : "";
					}
					else
					{
						var serviceUrl = this.getServiceUrl(
							BX.type.isNotEmptyString(params["ENTITY_TYPE"]) ? params["ENTITY_TYPE"] : ""
						);

						if(serviceUrl !== "")
						{
							var loader = BX.OrderSipRecipientInfoLoader.create(
								BX.OrderParamBag.create(
									{
										serviceUrl: serviceUrl,
										recipient: recipient,
										params: params,
										anchor: anchor,
										callback: BX.delegate(this._onRecipientInfoLoad, this)
									}
								)
							);
							loader.process();
							return;
						}
					}
				}

				this.openPreCallDialog(recipient, params, anchor, BX.delegate(this._onPreCallDialogClose, this));
			}
			else
			{
				this.makeCall(recipient, params);
			}
		},
		getMessage: function(name)
		{
			return BX.OrderSipManager.messages && BX.OrderSipManager.messages.hasOwnProperty(name) ? BX.OrderSipManager.messages[name] : "";
		},
		_onPreCallDialogClose: function(dlg, recipient, params, settings)
		{
			if(!params || typeof(params) !== "object")
			{
				params = {};
			}
			this.makeCall(recipient, params);
		},
		_onRecipientInfoLoad: function(loader, recipient, params, anchor, info)
		{
			var entityType = BX.type.isNotEmptyString(params["ENTITY_TYPE"]) ? params["ENTITY_TYPE"] : "";
			var entityId = BX.type.isNotEmptyString(params["ENTITY_ID"]) ? params["ENTITY_ID"] : "";
			var key = entityType + '_' + entityId.toString();
			this._recipientInfos[key] = info;

			recipient["title"] = BX.type.isNotEmptyString(info["title"]) ? info["title"] : "";
			recipient["legend"] = BX.type.isNotEmptyString(info["legend"]) ? info["legend"] : "";
			recipient["imageUrl"] = BX.type.isNotEmptyString(info["imageUrl"]) ? info["imageUrl"] : "";
			recipient["showUrl"] = BX.type.isNotEmptyString(info["showUrl"]) ? info["showUrl"] : "";

			this.openPreCallDialog(recipient, params, anchor, BX.delegate(this._onPreCallDialogClose, this));
		}
	};

	BX.OrderSipManager.items = {};
	BX.OrderSipManager.create = function(id, settings)
	{
		var self = new BX.OrderSipManager();
		self.initialize(id, settings);
		this.items[id] = self;
		return self;
	};
	BX.OrderSipManager.current = null;
	BX.OrderSipManager.getCurrent = function()
	{
		if(!this._current)
		{
			this._current = this.create("_CURRENT", null);
		}

		return this._current;
	};
	BX.OrderSipManager.startCall = function(recipient, params, enablePreCallDialog, anchor)
	{
		this.getCurrent().startCall(recipient, params, enablePreCallDialog, anchor);
	};
	BX.OrderSipManager.resolveSipEntityTypeName = function(typeName)
	{
		return BX.type.isNotEmptyString(typeName) ? ("ORDER_" + typeName.toUpperCase()) : "";
	}
}

if(typeof(BX.OrderSipRecipientInfoLoader) === "undefined")
{
	BX.OrderSipRecipientInfoLoader = function()
	{
		this._settings = null;
		this._serviceUrl = null;
		this._recipient = null;
		this._params = null;
		this._anchor = null;
		this._callBack = null;
	};

	BX.OrderSipRecipientInfoLoader.prototype =
	{
		initialize: function(settings)
		{
			this._settings = settings ? settings : BX.OrderParamBag.create(null);

			this._serviceUrl = this.getSetting("serviceUrl", "");

			this._recipient = this.getSetting("recipient");
			if(!this._recipient)
			{
				this._recipient = {};
			}

			this._params = this.getSetting("params");
			if(!this._params)
			{
				this._params = {};
			}

			this._anchor = this.getSetting("anchor", null);

			this._callBack = this.getSetting("callback");
			if(!BX.type.isFunction(this._callBack))
			{
				this._callBack = null;
			}
		},
		getSetting: function(name, defaultvalue)
		{
			return this._settings.getParam(name, defaultvalue);
		},
		setSetting: function(name, value)
		{
			return this._settings.setParam(name, value);
		},
		process: function()
		{
			var params = this._params;
			var entityTypeName = BX.type.isNotEmptyString(params["ENTITY_TYPE"]) ? params["ENTITY_TYPE"] : "";
			var entityId = typeof(params["ENTITY_ID"]) !== "undefined" ? parseInt(params["ENTITY_ID"]) : 0;
			var serviceUrl = this._serviceUrl;
			var callBack = this._callBack;

			if(entityTypeName  === "" || entityId <= 0 || serviceUrl === "")
			{
				if(BX.type.isFunction(this._callBack))
				{
					callBack(this, this._recipient, this._params, this._anchor, {});
				}
				return;
			}

			BX.ajax(
				{
					url: serviceUrl,
					method: "POST",
					dataType: "json",
					data:
					{
						"MODE" : "GET_ENTITY_SIP_INFO",
						"ENITY_TYPE" : entityTypeName,
						"ENITY_ID" : entityId
					},
					onsuccess: BX.delegate(this._onSuccess, this)
					//onfailure: function(data){}
				}
			);
		},
		_onSuccess: function(result)
		{
			var callBack = this._callBack;
			if(!BX.type.isFunction(callBack))
			{
				return;
			}

			var data = typeof(result["DATA"]) !== "undefined" ? result["DATA"] : {};
			var title = BX.type.isNotEmptyString(data["TITLE"]) ? data["TITLE"] : "";
			var legend = BX.type.isNotEmptyString(data["LEGEND"]) ? data["LEGEND"] : "";
			var imageUrl = BX.type.isNotEmptyString(data["IMAGE_URL"]) ? data["IMAGE_URL"] : "";
			var showUrl = BX.type.isNotEmptyString(data["SHOW_URL"]) ? data["SHOW_URL"] : "";

			try
			{
				callBack(
					this,
					this._recipient,
					this._params,
					this._anchor,
					{ title: title, legend: legend, showUrl: showUrl, imageUrl: imageUrl }
				);
			}
			catch(ex)
			{
			}
		}
	};

	BX.OrderSipRecipientInfoLoader.create = function(settings)
	{
		var self = new BX.OrderSipRecipientInfoLoader();
		self.initialize(settings);
		return self;
	};
}

if(typeof(BX.OrderPreCallDialog) === "undefined")
{
	BX.OrderPreCallDialog = function()
	{
		this._id = "";
		this._settings = null;
		this._recipient = null;
		this._params = null;
		this._anchor = null;
		this._dlg = null;
		this._isShown = false;
		this._makeCallButton = null;
		this._closeCallBack = null;
		this._onMakeCallButtonClickHandler = BX.delegate(this._onMakeCallButtonClick, this);
	};

	BX.OrderPreCallDialog.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : BX.OrderParamBag.create(null);

			this._recipient = this.getSetting("recipient");
			if(!this._recipient)
			{
				this._recipient = {};
			}

			this._params = this.getSetting("params");
			if(!this._params)
			{
				this._params = {};
			}

			this._anchor = this.getSetting("anchor", null);

			this._closeCallBack = this.getSetting("closeCallback");
			if(!BX.type.isFunction(this._closeCallBack))
			{
				this._closeCallBack = null;
			}
		},
		getSetting: function(name, defaultvalue)
		{
			return this._settings.getParam(name, defaultvalue);
		},
		setSetting: function(name, value)
		{
			return this._settings.setParam(name, value);
		},
		getId: function()
		{
			return this._id;
		},
		getMessage: function(name)
		{
			return BX.OrderSipManager.messages && BX.OrderSipManager.messages.hasOwnProperty(name) ? BX.OrderSipManager.messages[name] : "";
		},
		show: function()
		{
			if(this._isShown)
			{
				return;
			}

			this._dlg = BX.PopupWindowManager.create(
				this._id.toLowerCase() + "-pre-call",
				this._anchor,
				{
					content: this._preparePreCallDialogContent(),
					closeIcon: true,
					closeByEsc: true,
					lightShadow: true,
					angle:{ offset: 5 },
					zIndex: 200, //For balloons
					events:
					{
						onPopupClose: BX.delegate(this._onDialogClose, this)
					}
				}
			);

			if(!this._dlg.isShown())
			{
				this._dlg.show();
			}
			this._isShown = this._dlg.isShown();
		},
		close: function()
		{
			if(!this._isShown)
			{
				return;
			}

			if(this._dlg)
			{
				this._dlg.close();
				this._isShown = this._dlg.isShown();
			}
			else
			{
				this._isShown = false;
			}
		},
		_preparePreCallDialogContent: function()
		{
			var recipient = this._recipient;

			var container = BX.create(
				"DIV",
				{ attrs: { className: "order-tel-popup" } }
			);

			var userWrapper = BX.create(
				"DIV",
				{ attrs: { className: "order-tel-popup-user" } }
			);
			container.appendChild(userWrapper);

			var userAvatar = BX.create(
				"DIV",
				{ attrs: { className: "order-tel-avatar" } }
			);
			var imageUrl = BX.type.isNotEmptyString(recipient["imageUrl"]) ? recipient["imageUrl"] : "";
			if(imageUrl !== "")
			{
				userAvatar.style.background = "url(" + imageUrl + ") no-repeat 3px 3px";
			}

			userWrapper.appendChild(userAvatar);
			userWrapper.appendChild(
				BX.create("DIV", { attrs: { className: "order-tel-user-alignment" } })
			);

			var title = BX.type.isNotEmptyString(recipient["title"]) ? recipient["title"] : this.getMessage("unknownRecipient");
			var legend = BX.type.isNotEmptyString(recipient["legend"]) ? recipient["legend"] : "";
			var showUrl = BX.type.isNotEmptyString(recipient["showUrl"]) ? recipient["showUrl"] : "#";
			userWrapper.appendChild(
				BX.create("DIV",
					{
						attrs: { className: "order-tel-user-data" },
						children:
							[
								BX.create("A",
									{
										attrs: { className: "order-tel-user-name", target: "_blank", href: showUrl },
										text: title
									}
								),
								BX.create("DIV",
									{
										attrs: { className: "order-tel-user-organ" },
										text: legend
									}
								)
							]
					}
				)
			);

			var number = BX.type.isNotEmptyString(recipient["number"]) ? recipient["number"] : "-";
			var chkBxId = this._id.toLowerCase() + "_enable_recordind";

			var settingsWrapper = BX.create(
				"DIV",
				{
					attrs: { className: "order-tel-popup-num-block" },
					children:
						[
							BX.create("DIV",
								{
									attrs: { className: "order-tel-popup-num" },
									text: number
								}
							)
						]
				}
			);
			container.appendChild(settingsWrapper);

			var buttonWrapper = BX.create(
				"DIV",
				{ attrs: { className: "order-tel-popup-footer" } }
			);
			container.appendChild(buttonWrapper);

			this._makeCallButton = BX.create("SPAN",
				{
					attrs: { className: "order-tel-popup-call-btn" },
					text: this.getMessage("makeCall")
				}
			);
			BX.bind(this._makeCallButton, "click", this._onMakeCallButtonClickHandler);
			buttonWrapper.appendChild(this._makeCallButton);

			return container;
		},
		_onMakeCallButtonClick: function(e)
		{
			if(!this._isShown)
			{
				return;
			}

			if(this._dlg)
			{
				this._dlg.close();
			}
			this._isShown = this._dlg ? this._dlg.isShown() : false;

			BX.unbind(this._makeCallButton, "click", this._onMakeCallButtonClickHandler);

			if(this._closeCallBack)
			{
				try
				{
					this._closeCallBack(this, this._recipient, this._params, {});
				}
				catch(ex)
				{
				}
			}
		},
		_onDialogClose: function(e)
		{
			if(this._dlg)
			{
				this._dlg.destroy();
				this._dlg = null;
			}

			this._isShown = false;
		}
	};

	BX.OrderPreCallDialog.create = function(id, settings)
	{
		var self = new BX.OrderPreCallDialog();
		self.initialize(id, settings);
		return self;
	};
}

if(typeof(BX.OrderBizprocDispatcher) == "undefined")
{
	BX.OrderBizprocDispatcher = function()
	{
		this._id = "";
		this._settings = {};
		this._container = null;
		this._wrapper = null;
		this._serviceUrl = "";
		this._entityTypeName = "";
		this._entityId = 0;
		this._formId = "";
		this._tabId = "tab_bizproc";
		this._currentPage = "";
		this._formManager = null;

		this._isRequestRunning = false;
		this._isLoaded = false;

		this._waiter = null;
		this._scrollHandler = BX.delegate(this._onWindowScroll, this);
		this._formManagerHandler = BX.delegate(this._onFormManagerCreate, this);
	};

	BX.OrderBizprocDispatcher.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : "order_bp_disp_" + Math.random().toString().substring(2);
			this._settings = settings ? settings : {};

			this._container = BX(this.getSetting("containerID", ""));
			if(!this._container)
			{
				throw "BX.OrderBizprocDispatcher. Could not find container.";
			}
			this._wrapper = BX.findParent(this._container, { "tagName": "DIV", "className": "bx-edit-tab-inner" });

			this._serviceUrl = this.getSetting("serviceUrl", "");
			if(!BX.type.isNotEmptyString(this._serviceUrl))
			{
				throw "BX.OrderBizprocDispatcher. Could not find service url.";
			}

			this._entityTypeName = this.getSetting("entityTypeName", "");
			if(!BX.type.isNotEmptyString(this._entityTypeName))
			{
				throw "BX.OrderBizprocDispatcher. Could not find entity type name.";
			}

			this._entityId = parseInt(this.getSetting("entityID", 0));
			if(!BX.type.isNumber(this._entityId) || this._entityId <= 0)
			{
				throw "BX.OrderBizprocDispatcher. Could not find entity id.";
			}

			this._formId = this.getSetting("formID", "");
			if(!BX.type.isNotEmptyString(this._formId))
			{
				throw "BX.OrderBizprocDispatcher. Could not find form id.";
			}

			var formManager = window["bxForm_" + this._formId];
			if(formManager)
			{
				this.setFormManager(formManager);
			}
			else
			{
				BX.addCustomEvent(window, "OrderInterfaceFormCreated", this._formManagerHandler);
			}

			this._currentPage = this.getSetting("currentPage", "");
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
					this.loadIndex();
				}
				else
				{
					BX.bind(window, "scroll", this._scrollHandler);
				}
			}
		},
		loadIndex: function()
		{
			if(this._isLoaded)
			{
				return;
			}

			if(this._currentPage === "index")
			{
				return;
			}

			var result = this._startRequest(
				"INDEX",
				{
					"FORM_ID": this.getSetting("formID", ""),
					"PATH_TO_ENTITY_SHOW": this.getSetting("pathToEntityShow", "")
				}
			);

			if(result)
			{
				this._currentPage = "index";
			}
		},
		_startRequest: function(action, params)
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
						"ACTION" : action,
						"ENTITY_TYPE_NAME": this._entityTypeName,
						"ENTITY_ID": this._entityId,
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
				this.loadIndex();
			}
		},
		_onWindowScroll: function(e)
		{
			if(!this._isLoaded && !this._isRequestRunning && this.isContanerInClientRect())
			{
				BX.unbind(window, "scroll", this._scrollHandler);
				this.loadIndex();
			}
		}
	};

	BX.OrderBizprocDispatcher.items = {};
	BX.OrderBizprocDispatcher.create = function(id, settings)
	{
		var self = new BX.OrderBizprocDispatcher();
		self.initialize(id, settings);
		this.items[self.getId()] = self;
		return self;
	};
}

if(typeof(BX.OrderLongRunningProcessState) == "undefined")
{
	BX.OrderLongRunningProcessState =
	{
		intermediate: 0,
		running: 1,
		completed: 2,
		stoped: 3,
		error: 4
	};
}

if(typeof(BX.OrderLongRunningProcessDialog) == "undefined")
{
	BX.OrderLongRunningProcessDialog = function()
	{
		this._id = "";
		this._settings = {};
		this._serviceUrl = "";
		this._params = {};
		this._dlg = null;
		this._buttons = {};
		this._summary = null;
		this._isShown = false;
		this._state = BX.OrderLongRunningProcessState.intermediate;
		this._cancelRequest = false;
		this._requestIsRunning = false;
	};
	BX.OrderLongRunningProcessDialog.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : "order_long_run_proc_" + Math.random().toString().substring(2);
			this._settings = settings ? settings : {};

			this._serviceUrl = this.getSetting("serviceUrl", "");
			if(!BX.type.isNotEmptyString(this._serviceUrl))
			{
				throw "BX.OrderLongRunningProcess. Could not find service url.";
			}

			this._action = this.getSetting("action", "");
			if(!BX.type.isNotEmptyString(this._action))
			{
				throw "BX.OrderLongRunningProcess. Could not find action.";
			}

			this._params = this.getSetting("params");
			if(!this._params)
			{
				this._params = {};
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
			return BX.OrderLongRunningProcessDialog.messages && BX.OrderLongRunningProcessDialog.messages.hasOwnProperty(name) ? BX.OrderLongRunningProcessDialog.messages[name] : "";
		},
		getState: function()
		{
			return this._state;
		},
		getServiceUrl: function()
		{
			return this._serviceUrl;
		},
		getAction: function()
		{
			return this._action;
		},
		getParams: function()
		{
			return this._params;
		},
		show: function()
		{
			if(this._isShown)
			{
				return;
			}

			this._dlg = BX.PopupWindowManager.create(
				this._id.toLowerCase(),
				this._anchor,
				{
					autoHide: false,
					bindOptions: { forceBindPosition: false },
					buttons: this._prepareDialogButtons(),
					//className: "",
					closeByEsc: false,
					closeIcon: false,
					content: this._prepareDialogContent(),
					draggable: true,
					events: { onPopupClose: BX.delegate(this._onDialogClose, this) },
					offsetLeft: 0,
					offsetTop: 0,
					titleBar: { content: this._prepareDialogTitle() }
				}
			);
			this._dlg.popupContainer.className = "bx-order-dialog-wrap bx-order-dialog-long-run-proc";
			if(!this._dlg.isShown())
			{
				this._dlg.show();
			}
			this._isShown = this._dlg.isShown();
		},
		close: function()
		{
			if(!this._isShown)
			{
				return;
			}

			if(this._dlg)
			{
				this._dlg.close();
			}
			this._isShown = false;
		},
		_prepareDialogTitle: function()
		{
			var title =  this.getSetting("title", "");
			return BX.create(
				"DIV",
				{
					attrs: { className: "bx-order-dialog-tittle-wrap" },
					children:
						[
							BX.create(
								"SPAN",
								{
									text: title !== "" ? title : "Processing",
									props: { className: "bx-order-dialog-title-text" }
								}
							)
						]
				}
			);
		},
		_prepareDialogContent: function()
		{
			this._summary = BX.create(
				"DIV",
				{
					attrs: { className: "bx-order-dialog-long-run-proc-summary" },
					text: this.getSetting("summary", "")
				}
			);
			return BX.create(
				"DIV",
				{
					attrs: { className: "bx-order-dialog-long-run-proc-popup" },
					children: [ this._summary ]
				}
			);
		},
		_prepareDialogButtons: function()
		{
			this._buttons = {};

			var startButtonText = this.getMessage("startButton");
			this._buttons["start"] = new BX.PopupWindowButton(
				{
					text: startButtonText !== "" ? startButtonText : "Start",
					className: "popup-window-button-accept",
					events:
					{
						click : BX.delegate(this._handleStartButtonClick, this)
					}
				}
			);

			var stopButtonText = this.getMessage("stopButton");
			this._buttons["stop"] = new BX.PopupWindowButton(
				{
					text: stopButtonText !== "" ? stopButtonText : "Stop",
					className: "popup-window-button-accept-disabled",
					events:
					{
						click : BX.delegate(this._handleStopButtonClick, this)
					}
				}
			);

			var closeButtonText = this.getMessage("closeButton");
			this._buttons["close"] = new BX.PopupWindowButtonLink(
				{
					text: closeButtonText !== "" ? closeButtonText : "Close",
					className: "popup-window-button-link-cancel",
					events:
					{
						click : BX.delegate(this._handleCloseButtonClick, this)
					}
				}
			);

			return [ this._buttons["start"], this._buttons["stop"], this._buttons["close"] ];
		},
		_onDialogClose: function(e)
		{
			if(this._dlg)
			{
				this._dlg.destroy();
				this._dlg = null;
			}

			this._setState(BX.OrderLongRunningProcessState.intermediate);
			this._buttons = {};
			this._summary = null;

			this._isShown = false;

			BX.onCustomEvent(this, 'ON_CLOSE', [this]);
		},
		_handleStartButtonClick: function()
		{
			if(this._state === BX.OrderLongRunningProcessState.intermediate || this._state === BX.OrderLongRunningProcessState.stoped)
			{
				this._startRequest();
			}
		},
		_handleStopButtonClick: function()
		{
			if(this._state === BX.OrderLongRunningProcessState.running)
			{
				this._cancelRequest = true;
			}
		},
		_handleCloseButtonClick: function()
		{
			if(this._state !== BX.OrderLongRunningProcessState.running)
			{
				this._dlg.close();
			}
		},
		_lockButton: function(bid, lock)
		{
			var btn = typeof(this._buttons[bid]) !== "undefined" ? this._buttons[bid] : null;
			if(!btn)
			{
				return;
			}

			if(!!lock)
			{
				BX.removeClass(btn.buttonNode, "popup-window-button-accept");
				BX.addClass(btn.buttonNode, "popup-window-button-accept-disabled");
			}
			else
			{
				BX.removeClass(btn.buttonNode, "popup-window-button-accept-disabled");
				BX.addClass(btn.buttonNode, "popup-window-button-accept");
			}
		},
		_showButton: function(bid, show)
		{
			var btn = typeof(this._buttons[bid]) !== "undefined" ? this._buttons[bid] : null;
			if(btn)
			{
				btn.buttonNode.style.display = !!show ? "" : "none";
			}
		},
		_setSummary: function(text)
		{
			if(this._summary)
			{
				this._summary.innerHTML = BX.util.htmlspecialchars(text);
			}
		},
		_setState: function(state)
		{
			if(this._state === state)
			{
				return;
			}

			this._state = state;
			if(state === BX.OrderLongRunningProcessState.intermediate || state === BX.OrderLongRunningProcessState.stoped)
			{
				this._lockButton("start", false);
				this._lockButton("stop", true);
				this._showButton("close", true);
			}
			else if(state === BX.OrderLongRunningProcessState.running)
			{
				this._lockButton("start", true);
				this._lockButton("stop", false);
				this._showButton("close", false);
			}
			else if(state === BX.OrderLongRunningProcessState.completed || state === BX.OrderLongRunningProcessState.error)
			{
				this._lockButton("start", true);
				this._lockButton("stop", true);
				this._showButton("close", true);
			}

			BX.onCustomEvent(this, 'ON_STATE_CHANGE', [this]);
		},
		_startRequest: function()
		{
			if(this._requestIsRunning)
			{
				return;
			}
			this._requestIsRunning = true;

			this._setState(BX.OrderLongRunningProcessState.running);
			BX.ajax(
				{
					url: this._serviceUrl,
					method: "POST",
					dataType: "json",
					data:
					{
						"ACTION" : this._action,
						"PARAMS": this._params
					},
					onsuccess: BX.delegate(this._onRequestSuccsess, this),
					onfailure: BX.delegate(this._onRequestFailure, this)
				}
			);
		},
		_onRequestSuccsess: function(result)
		{
			this._requestIsRunning = false;

			if(!result)
			{
				this._setSummary(this.getMessage("requestError"));
				this._setState(BX.OrderLongRunningProcessState.error);
				return;
			}

			if(BX.type.isNotEmptyString(result["ERROR"]))
			{
				this._setState(BX.OrderLongRunningProcessState.error);
				this._setSummary(result["ERROR"]);
				return;
			}

			var status = BX.type.isNotEmptyString(result["STATUS"]) ? result["STATUS"] : "";
			var summary = BX.type.isNotEmptyString(result["SUMMARY"]) ? result["SUMMARY"] : "";
			if(status === "PROGRESS")
			{
				if(summary !== "")
				{
					this._setSummary(summary);
				}

				if(this._cancelRequest)
				{
					this._setState(BX.OrderLongRunningProcessState.stoped);
					this._cancelRequest = false;
				}
				else
				{
					window.setTimeout(
						BX.delegate(this._startRequest, this),
						100
					);
				}
				return;
			}

			if(status === "NOT_REQUIRED" || status === "COMPLETED")
			{
				this._setState(BX.OrderLongRunningProcessState.completed);
				if(summary !== "")
				{
					this._setSummary(summary);
				}
			}
			else
			{
				this._setSummary(this.getMessage("requestError"));
				this._setState(BX.OrderLongRunningProcessState.error);
			}

			if(this._cancelRequest)
			{
				this._cancelRequest = false;
			}
		},
		_onRequestFailure: function(result)
		{
			this._requestIsRunning = false;

			this._setSummary(this.getMessage("requestError"));
			this._setState(BX.OrderLongRunningProcessState.error);
		}
	};
	if(typeof(BX.OrderLongRunningProcessDialog.messages) == "undefined")
	{
		BX.OrderLongRunningProcessDialog.messages = {};
	}
	BX.OrderLongRunningProcessDialog.items = {};
	BX.OrderLongRunningProcessDialog.create = function(id, settings)
	{
		var self = new BX.OrderLongRunningProcessDialog();
		self.initialize(id, settings);
		this.items[self.getId()] = self;
		return self;
	};
}
if(typeof(BX.OrderEntityType) == "undefined")
{
	BX.OrderEntityType = function()
	{
	};

	BX.OrderEntityType.enumeration =
	{
		undefined: 0,
		PHYSICAL: 1,
		contact: 2,
		agent: 3,
		direction: 4,
		nomen: 5,
		course: 6,
		group: 7,
		reg: 8,
		app: 9,
		user: 10
	};
	BX.OrderEntityType.names =
	{
		undefined: "",
		PHYSICAL: "PHYSICAL",
		contact: "CONTACT",
		agent: "AGENT",
		direction: "DIRECTION",
		nomen: "NOMEN",
		course: "COURSE",
		group: "GROUP",
		reg: "REG",
		app: "APP",
		user: "USER"
	};
	BX.OrderEntityType.isDefined = function(typeId)
	{
		if(!BX.type.isNumber(typeId))
		{
			typeId = parseInt(typeId);
			if(isNaN(typeId))
			{
				typeId = 0;
			}
		}

		return typeId >= 0 && typeId <= 5;
	};
	BX.OrderEntityType.resolveName = function(typeId)
	{
		if(!BX.type.isNumber(typeId))
		{
			typeId = parseInt(typeId);
			if(isNaN(typeId))
			{
				typeId = 0;
			}
		}

		if(typeId === BX.OrderEntityType.enumeration.PHYSICAL)
		{
			return BX.OrderEntityType.names.PHYSICAL;
		}
		else if(typeId === BX.OrderEntityType.enumeration.contact)
		{
			return BX.OrderEntityType.names.contact;
		}
		else if(typeId === BX.OrderEntityType.enumeration.agent)
		{
			return BX.OrderEntityType.names.agent;
		}
		else if(typeId === BX.OrderEntityType.enumeration.direction)
		{
			return BX.OrderEntityType.names.direction;
		}
		else if(typeId === BX.OrderEntityType.enumeration.nomen)
		{
			return BX.OrderEntityType.names.nomen;
		}
		else if(typeId === BX.OrderEntityType.enumeration.course)
		{
			return BX.OrderEntityType.names.course;
		}
		else if(typeId === BX.OrderEntityType.enumeration.group)
		{
			return BX.OrderEntityType.names.group;
		}
		else if(typeId === BX.OrderEntityType.enumeration.reg)
		{
			return BX.OrderEntityType.names.reg;
		}
		else if(typeId === BX.OrderEntityType.enumeration.app)
		{
			return BX.OrderEntityType.names.app;
		}
		else if(typeId === BX.OrderEntityType.enumeration.user)
		{
			return BX.OrderEntityType.names.user;
		}
		else
		{
			return "";
		}
	};

	BX.OrderEntityType.getCaption = function(typeId)
	{
		var name = this.resolveName(typeId);
		return (this.captions.hasOwnProperty(name) ? this.captions[name] : name);
	};

	BX.OrderEntityType.getCaptionByName = function(name)
	{
		name = name.toUpperCase();
		return (this.captions.hasOwnProperty(name) ? this.captions[name] : name);
	};

	if(typeof(BX.OrderEntityType.captions) === "undefined")
	{
		BX.OrderEntityType.captions = {};
	}

	if(typeof(BX.OrderEntityType.categoryCaptions) === "undefined")
	{
		BX.OrderEntityType.categoryCaptions = {};
	}
}
if(typeof(BX.OrderDuplicateManager) == "undefined")
{
	BX.OrderDuplicateManager = function()
	{
		this._id = "";
		this._settings = {};
		this._entityTypeName = "";
		this._processDialogs = {};
	};
	BX.OrderDuplicateManager.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : "order_dp_mgr_" + Math.random().toString().substring(2);
			this._settings = settings ? settings : {};

			this._entityTypeName = this.getSetting("entityTypeName", "");
			if(!BX.type.isNotEmptyString(this._entityTypeName))
			{
				throw "BX.OrderDuplicateManager. Could not find entity type name.";
			}

			this._entityTypeName = this._entityTypeName.toUpperCase();
		},
		getId: function()
		{
			return this._id;
		},
		getEntityTypeName: function()
		{
			return this._entityTypeName;
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
			return BX.OrderDuplicateManager.messages && BX.OrderDuplicateManager.messages.hasOwnProperty(name) ? BX.OrderDuplicateManager.messages[name] : "";
		},
		rebuildIndex: function()
		{
			var serviceUrl = this.getSetting("serviceUrl", "");
			if(!BX.type.isNotEmptyString(serviceUrl))
			{
				throw "BX.OrderDuplicateManager. Could not find service url.";
			}

			var entityTypeNameC = this._entityTypeName.toLowerCase().replace(/(?:^)\S/, function(c){ return c.toUpperCase(); });
			var key = "rebuild" + entityTypeNameC + "Index";

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
						serviceUrl: serviceUrl,
						action:"REBUILD_DUPLICATE_INDEX",
						params:{ "ENTITY_TYPE_NAME": this._entityTypeName },
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
					//ON_LEAD_INDEX_REBUILD_COMPLETE, ON_COMPANY_INDEX_REBUILD_COMPLETE, ON_CONTACT_INDEX_REBUILD_COMPLETE
					BX.onCustomEvent(this, "ON_" + this._entityTypeName + "_INDEX_REBUILD_COMPLETE", [this]);
				}
			}
		}
	};
	if(typeof(BX.OrderDuplicateManager.messages) == "undefined")
	{
		BX.OrderDuplicateManager.messages = {};
	}
	BX.OrderDuplicateManager.items = {};
	BX.OrderDuplicateManager.create = function(id, settings)
	{
		var self = new BX.OrderDuplicateManager();
		self.initialize(id, settings);
		this.items[self.getId()] = self;
		return self;
	};
}
if(typeof(BX.OrderDupController) == "undefined")
{
	BX.OrderDupController = function()
	{
		this._id = "";
		this._settings = {};
		this._entityTypeName = "";
		this._enable = true;
		this._groups = {};
		this._requestIsRunning = false;
		this._request = null;
		this._searchData = {};
		this._searchSummary = null;
		this._warningDialog = null;
		this._submits = [];
		this._lastSummaryGroupId = "";
		this._lastSummaryFieldId = "";
		this._lastSubmit = null;
		this._onFormSubmitHandler = BX.delegate(this._onFormSubmit, this);
	};
	BX.OrderDupController.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : "order_dp_ctrl_" + Math.random().toString().substring(2);
			this._settings = settings ? settings : {};

			this._serviceUrl = this.getSetting("serviceUrl", "");
			if(!BX.type.isNotEmptyString(this._serviceUrl))
			{
				throw "BX.OrderDupController. Could not find service url.";
			}

			var submits = this.getSetting("submits", []);
			if(BX.type.isArray(submits))
			{
				for(var i = 0; i < submits.length; i++)
				{
					var submit = BX(submits[i]);
					if(BX.type.isElementNode(submit))
					{
						this._submits.push(submit);
						BX.bind(submit, "click", this._onFormSubmitHandler);
					}
				}
			}

			this._entityTypeName = this.getSetting("entityTypeName", "");

			var groups = this.getSetting("groups", null);
			var group = null;
			if(groups)
			{
				for(var key in groups)
				{
					if(!groups.hasOwnProperty(key))
					{
						continue;
					}

					group = groups[key];
					var type = BX.type.isNotEmptyString(group["groupType"]) ? group["groupType"] : "";
					var ctrl = null;
					try
					{
						if(type === "single")
						{
							ctrl = BX.OrderDupCtrlSingleField.create(key, group);
						}
						else if(type === "fullName")
						{
							ctrl = BX.OrderDupCtrlFullName.create(key, group);
						}
						else if(type === "communication")
						{
							ctrl = BX.OrderDupCtrlCommunication.create(key, group);
						}
					}
					catch(ex)
					{
					}

					if(ctrl)
					{
						this.addGroup(ctrl);
					}
				}
			}

			this._afterInitialize();

			var groupParams = [];
			for(var groupId in this._groups)
			{
				if(!this._groups.hasOwnProperty(groupId))
				{
					continue;
				}

				group = this._groups[groupId];
				var params = group.prepareSearchParams();
				if(!params)
				{
					continue;
				}

				params["GROUP_ID"] = groupId;
				params["HASH_CODE"] = group.getSearchHashCode();
				params["FIELD_ID"] = group.getDefaultSearchSummaryFieldId();

				groupParams.push(params);
			}

			if(groupParams.length > 0)
			{
				this._search({ "GROUPS": groupParams });
			}
		},
		_afterInitialize: function()
		{
		},
		getId: function()
		{
			return this._id;
		},
		getEntityTypeName: function()
		{
			return this._entityTypeName;
		},
		isEnabled: function()
		{
			return this._enable;
		},
		enable: function(enable)
		{
			this._enable = !!enable;
		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		setSetting: function (name, val)
		{
			this._settings[name] = val;
		},
		addGroup: function(group)
		{
			this._groups[group.getId()] = group;
			group.setController(this);
			return group;
		},
		getGroup: function(groupId)
		{
			return this._groups.hasOwnProperty(groupId) ? this._groups[groupId] : null;
		},
		getDuplicateData: function()
		{
			return this._searchData;
		},
		hasDuplicates: function()
		{
			for(var key in this._searchData)
			{
				if(!this._searchData.hasOwnProperty(key))
				{
					continue;
				}

				var data = this._searchData[key];
				if(data.hasOwnProperty("items") && data["items"].length > 0)
				{
					return true;
				}
			}
			return false;
		},
		processGroupChange: function(group, field)
		{
			var groupId =  group.getId();

			var params = group.prepareSearchParams();
			if(!params)
			{
				if(typeof(this._searchData[groupId]) !== "undefined")
				{
					delete this._searchData[groupId];
					this._refreshSearchSummary(groupId, field.getId());
				}
				return;
			}

			var hashCode = group.getSearchHashCode();
			if(hashCode !== this._getGroupSearchHashCode(groupId))
			{
				params["GROUP_ID"] = groupId;
				if(field)
				{
					params["FIELD_ID"] = field.getId();
				}

				params["HASH_CODE"] = hashCode;
				this._search({ "GROUPS": [ params ] });
			}
		},
		_search: function(params)
		{
			if(this._requestIsRunning)
			{
				this._stopSearchRequest();
			}
			params["ENTITY_TYPE_NAME"] = this._entityTypeName;
			this._startSearchRequest(params);
		},
		_startSearchRequest: function(params)
		{
			if(this._requestIsRunning)
			{
				return;
			}

			BX.showWait();
			this._requestIsRunning = true;
			this._request = BX.ajax(
				{
					url: this._serviceUrl,
					method: "POST",
					dataType: "json",
					data:
					{
						"ACTION" : "FIND_DUPLICATES",
						"PARAMS": params
					},
					onsuccess: BX.delegate(this._onSearchRequestSuccsess, this),
					onfailure: BX.delegate(this._onSearchRequestFailure, this)
				}
			);
		},
		_stopSearchRequest: function()
		{
			if(!this._requestIsRunning)
			{
				return;
			}
			this._requestIsRunning = false;
			if(this._request)
			{
				this._request.abort();
				this._request = null;
			}

			BX.closeWait();
		},
		_onSearchRequestSuccsess: function(result)
		{
			BX.closeWait();
			this._requestIsRunning = false;

			if(!result)
			{
				//var error = getMessage("generalError");
				//Show error
				return;
			}

			if(BX.type.isNotEmptyString(result["ERROR"]))
			{
				//var error = result["ERROR"];
				//Show error
				return;
			}

			var lastGroupId = "";
			var lastFieldId = "";
			var groupResults = BX.type.isArray(result["GROUP_RESULTS"]) ? result["GROUP_RESULTS"] : [];
			for(var i = 0; i < groupResults.length; i++)
			{
				var groupResult = groupResults[i];
				var groupId = typeof(groupResult["GROUP_ID"]) !== "undefined" ? groupResult["GROUP_ID"] : "";
				if(!BX.type.isNotEmptyString(groupId))
				{
					return;
				}

				var group = this.getGroup(groupId);
				if(!group)
				{
					return;
				}

				if(typeof(this._searchData[groupId]) === "undefined")
				{
					this._searchData[groupId] = {};
				}

				var items = BX.type.isArray(groupResult["DUPLICATES"]) ? groupResult["DUPLICATES"] : [];
				if(items.length > 0)
				{
					this._searchData[groupId]["items"] = BX.type.isArray(groupResult["DUPLICATES"]) ? groupResult["DUPLICATES"] : [];

					this._searchData[groupId]["totalText"] =
						BX.type.isNotEmptyString(groupResult["ENTITY_TOTAL_TEXT"]) ? groupResult["ENTITY_TOTAL_TEXT"] : "";

					var hash = 0;
					if(typeof(groupResult["HASH_CODE"]) !== "undefined")
					{
						hash = parseInt(groupResult["HASH_CODE"]);
						if(isNaN(hash))
						{
							hash = 0;
						}
					}
					this._searchData[groupId]["hash"] = hash;

					if(BX.type.isNotEmptyString(groupResult["FIELD_ID"]))
					{
						lastGroupId = groupId;
						lastFieldId = groupResult["FIELD_ID"];
					}
				}
				else
				{
					delete this._searchData[groupId];
				}
			}
			this._refreshSearchSummary(lastGroupId, lastFieldId);
		},
		_refreshSearchSummary: function(groupId, fieldId)
		{
			if(!BX.type.isNotEmptyString(groupId))
			{
				groupId = "";
			}

			if(!BX.type.isNotEmptyString(fieldId))
			{
				fieldId = "";
			}

			if(this.hasDuplicates())
			{
				var anchorField = null;
				if(groupId === "" || fieldId === "")
				{
					groupId = this._lastSummaryGroupId;
					fieldId = this._lastSummaryFieldId;
				}
				if(groupId !== "" && fieldId !== "")
				{
					var group = this.getGroup(groupId);
					if(group)
					{
						anchorField = group.getField(fieldId);
					}

					this._lastSummaryGroupId = groupId;
					this._lastSummaryFieldId = fieldId;
				}
				this._showSearchSummary(anchorField);
			}
			else
			{
				this._closeSearchSummary();
			}
		},
		_onSearchRequestFailure: function(result)
		{
			BX.closeWait();
			this._requestIsRunning = false;
			//var error = getMessage("generalError");
			//Show error
		},
		_onFormSubmit: function(e)
		{
			if(!this.hasDuplicates())
			{
				return true;
			}

			var submit = null;
			if(e)
			{
				if(e.target)
				{
					submit = e.target;
				}
				else if(e.srcElement)
				{
					submit = e.srcElement;
				}
			}

			if(BX.type.isElementNode(submit))
			{
				this._lastSubmit = submit;
			}

			window.setTimeout(BX.delegate(this._openWarningDialog, this), 100);
			return BX.PreventDefault(e);
		},
		_openWarningDialog: function()
		{
			this._warningDialog = BX.OrderDuplicateWarningDialog.create(
				this._id + "_warn",
				{
					"controller": this,
					"onClose": BX.delegate(this._onWarningDialogClose, this),
					"onCancel": BX.delegate(this._onWarningDialogCancel, this),
					"onAccept": BX.delegate(this._onWarningDialogAccept, this)
				}
			);
			this._warningDialog.show();
		},
		_getGroupSearchData: function(groupId)
		{
			return this._searchData.hasOwnProperty(groupId) ? this._searchData[groupId] : null;
		},
		_getGroupSearchHashCode: function(groupId)
		{
			var data = this._getGroupSearchData(groupId);
			return (data && data.hasOwnProperty("hash")) ? data["hash"] : 0;
		},
		_showSearchSummary: function(anchorField)
		{
			this._closeSearchSummary();

			var anchor = null;
			if(anchorField)
			{
				anchor = anchorField ? anchorField.getElementTitle() : null;
				if(!anchor)
				{
					anchor = anchorField.getElement();
				}
			}

			this._searchSummary = BX.OrderDuplicateSummaryPopup.create(
				this._id + "_summary",
				{
					"controller": this,
					"anchor": anchor
				}
			);
			this._searchSummary.show();
		},
		_isSearchSummaryShown: function()
		{
			return this._searchSummary && this._searchSummary.isShown();
		},
		_closeSearchSummary: function()
		{
			if(this._searchSummary)
			{
				this._searchSummary.close();
				this._searchSummary = null;
			}
		},
		_onWarningDialogClose: function(dlg)
		{
			if(this._warningDialog === dlg)
			{
				this._warningDialog = null;
			}
		},
		_onWarningDialogCancel: function(dlg)
		{
			if(this._warningDialog === dlg)
			{
				this._warningDialog.close();
			}
		},
		_onWarningDialogAccept: function(dlg)
		{
			if(this._warningDialog === dlg)
			{
				this._warningDialog.close();

				for(var i = 0; i < this._submits.length; i++)
				{
					BX.unbind(this._submits[i], "click", this._onFormSubmitHandler);
				}

				if(BX.type.isElementNode(this._lastSubmit))
				{
					this._lastSubmit.click();
				}
				else
				{
					var form = BX(this.getSetting("form", ""));
					if(BX.type.isElementNode(form))
					{
						form.submit();
					}
				}
			}
		}
	};
	BX.OrderDupController.create = function(id, settings)
	{
		var self = new BX.OrderDupController();
		self.initialize(id, settings);
		return self;
	};
}
/*if(typeof(BX.OrderLeadDupController) == "undefined")
 {
 BX.OrderLeadDupController = function()
 {
 BX.OrderLeadDupController.superclass.constructor.apply(this);
 if(this._entityTypeName !== "LEAD")
 {
 this._entityTypeName = "LEAD";
 }
 };
 BX.extend(BX.OrderLeadDupController, BX.OrderDupController);

 BX.OrderLeadDupController.prototype._afterInitialize = function()
 {
 };
 BX.OrderLeadDupController.create = function(id, settings)
 {
 var self = new BX.OrderLeadDupController();
 self.initialize(id, settings);
 return self;
 }
 }*/
if(typeof(BX.OrderDupCtrlField) == "undefined")
{
	BX.OrderDupCtrlField = function()
	{
		this._id = "";
		this._group = null;
		this._element = null;
		this._elementTitle = null;
		this._value = "";
		this._hasFosus = false;
		this._elementTimeoutId = 0;
		this._elementTimeoutHandler = BX.delegate(this._onElementTimeout, this);
		this._elementKeyUpHandler = BX.delegate(this._onElementKeyUp, this);
		this._elementFocusHandler = BX.delegate(this._onElementFocus, this);
		this._elementBlurHandler = BX.delegate(this._onElementBlur, this);
		this._initialized = false;
	};
	BX.OrderDupCtrlField.prototype =
	{
		initialize: function(id, element, elementTitle)
		{
			if(!BX.type.isNotEmptyString(id))
			{
				throw "BX.OrderDupCtrlField. Invalid parameter 'id': is not defined.";
			}
			this._id = id;

			if(!BX.type.isElementNode(element))
			{
				throw "BX.OrderDupCtrlField. Invalid parameter 'element': is not defined.";
			}
			this._element = element;
			this._value = element.value;

			BX.bind(this._element, "keyup", this._elementKeyUpHandler);
			BX.bind(this._element, "focus", this._elementFocusHandler);
			BX.bind(this._element, "blur", this._elementBlurHandler);

			if(BX.type.isElementNode(elementTitle))
			{
				this._elementTitle = elementTitle;
			}

			this._initialized = true;
		},
		release: function()
		{
			BX.unbind(this._element, "keyup", this._elementKeyUpHandler);
			BX.unbind(this._element, "focus", this._elementFocusHandler);
			BX.unbind(this._element, "blur", this._elementBlurHandler);
			this._element = null;

			this._initialized = false;
		},
		getId: function()
		{
			return this._id;
		},
		getGroup: function()
		{
			return this._group;
		},
		setGroup: function(group)
		{
			this._group = group;
		},
		hasFocus: function()
		{
			return this._hasFosus;
		},
		getElement: function()
		{
			return this._element;
		},
		getElementTitle: function()
		{
			return this._elementTitle;
		},
		getValue: function()
		{
			return this._element.value;
		},
		_onElementKeyUp: function(e)
		{
			var c = e.keyCode;
			if(c === 13 || c === 27 || (c >=37 && c <= 40) || (c >=112 && c <= 123))
			{
				return;
			}

			if(this._value === this._element.value)
			{
				return;
			}
			this._value = this._element.value;

			if(this._elementTimeoutId > 0)
			{
				window.clearTimeout(this._elementTimeoutId);
				this._elementTimeoutId = 0;
			}
			this._elementTimeoutId = window.setTimeout(this._elementTimeoutHandler, 1500);

			if(!this._hasFosus)
			{
				this._hasFosus = true;
			}
		},
		_onElementFocus: function(e)
		{
			this._hasFosus = true;
			if(this._group)
			{
				this._group.processFieldFocusGain(this);
			}
		},
		_onElementBlur: function(e)
		{
			if(this._elementTimeoutId > 0)
			{
				window.clearTimeout(this._elementTimeoutId);
				this._elementTimeoutId = 0;
			}

			this._hasFosus = false;
			if(this._group)
			{
				this._group.processFieldFocusLoss(this);
			}
		},
		_onElementTimeout: function()
		{
			if(this._elementTimeoutId <= 0)
			{
				return;
			}

			this._elementTimeoutId = 0;
			if(this._group)
			{
				this._group.processFieldDelay(this);
			}
		}
	};
	BX.OrderDupCtrlField.create = function(id, element, elementTitle)
	{
		var self = new BX.OrderDupCtrlField();
		self.initialize(id, element, elementTitle);
		return self;
	}
}
if(typeof(BX.OrderDupCtrlFieldGroup) == "undefined")
{
	BX.OrderDupCtrlFieldGroup = function()
	{
		this._id = "";
		this._settings = {};
		this._controller = null;
		this._fields = {};
	};
	BX.OrderDupCtrlFieldGroup.prototype =
	{
		initialize: function(id, settings)
		{
			if(!BX.type.isNotEmptyString(id))
			{
				throw "BX.OrderDupCtrlFieldGroup. Invalid parameter 'id': is not defined.";
			}
			this._id = id;

			this._settings = settings ? settings : {};
			this._afterInitialize();
		},
		_afterInitialize: function()
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
		setSetting: function (name, val)
		{
			this._settings[name] = val;
		},
		getController: function()
		{
			return this._controller;
		},
		setController: function(controller)
		{
			this._controller = controller;
		},
		addField: function(field)
		{
			this._fields[field.getId()] = field;
			field.setGroup(this);
			return field;
		},
		getField: function(fieldId)
		{
			return this._fields.hasOwnProperty(fieldId) ? this._fields[fieldId] : null;
		},
		getFieldValues: function()
		{
			var result = [];
			for(var key in this._fields)
			{
				if(this._fields.hasOwnProperty(key))
				{
					var value = BX.util.trim(this._fields[key].getValue());
					if(value !== "")
					{
						result.push(value);
					}
				}
			}
			return result;
		},
		clearFields: function()
		{
			for(var key in this._fields)
			{
				if(this._fields.hasOwnProperty(key))
				{
					this._fields[key].release();
				}
			}
			this._fields = {};
		},
		getSummaryTitle: function()
		{
			return this.getSetting("groupSummaryTitle", "");
		},
		prepareSearchParams: function()
		{
			return null;
		},
		getSearchHashCode: function()
		{
			return 0;
		},
		getDefaultSearchSummaryFieldId: function()
		{
			return "";
		},
		processFieldDelay: function(field)
		{
		},
		processFieldFocusGain: function(field)
		{
		},
		processFieldFocusLoss: function(field)
		{
		}
	};
}
if(typeof(BX.OrderDupCtrlSingleField) == "undefined")
{
	BX.OrderDupCtrlSingleField = function()
	{
		BX.OrderDupCtrlSingleField.superclass.constructor.apply(this);
		this._paramName = "";
		this._field = null;
	};
	BX.extend(BX.OrderDupCtrlSingleField, BX.OrderDupCtrlFieldGroup);
	BX.OrderDupCtrlSingleField.prototype._afterInitialize = function()
	{
		this._paramName = this.getSetting("parameterName", "");
		if(!BX.type.isNotEmptyString(this._paramName))
		{
			throw "BX.OrderDupCtrlSingleField. Could not find parameter name.";
		}

		var element = BX(this.getSetting("element", null));
		if(BX.type.isDomNode(element))
		{
			this._field = this.addField(BX.OrderDupCtrlField.create(this._paramName, element, BX(this.getSetting("elementCaption", null))));
		}
	};
	BX.OrderDupCtrlSingleField.prototype.getValue = function()
	{
		return this._field ? BX.util.trim(this._field.getValue()) : "";
	};
	BX.OrderDupCtrlSingleField.prototype.prepareSearchParams = function()
	{
		var value = this.getValue();
		if(value === "")
		{
			return null;
		}

		var result = {};
		result[this._paramName] = value;
		return result;
	};
	BX.OrderDupCtrlSingleField.prototype.getSearchHashCode = function()
	{
		var value = this.getValue();
		if(value === "")
		{
			return 0;
		}
		return BX.util.hashCode(value);
	};
	BX.OrderDupCtrlSingleField.prototype.getDefaultSearchSummaryFieldId = function()
	{
		return this._field ? this._field.getId() : ""
	};
	BX.OrderDupCtrlSingleField.prototype.processFieldDelay = function(field)
	{
		this._fireChangeEvent(field);
	};
	BX.OrderDupCtrlSingleField.prototype.processFieldFocusLoss = function(field)
	{
		this._fireChangeEvent(field);
	};
	BX.OrderDupCtrlSingleField.prototype._fireChangeEvent = function(field)
	{
		if(this._controller)
		{
			this._controller.processGroupChange(this, field);
		}
	};
	BX.OrderDupCtrlSingleField.create = function(id, settings)
	{
		var self = new BX.OrderDupCtrlSingleField();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderDupCtrlFullName) == "undefined")
{
	BX.OrderDupCtrlFullName = function()
	{
		BX.OrderDupCtrlFullName.superclass.constructor.apply(this);
		this._nameField = null;
		this._secondNameField = null;
		this._lastNameField = null;
	};

	BX.extend(BX.OrderDupCtrlFullName, BX.OrderDupCtrlFieldGroup);
	BX.OrderDupCtrlFullName.prototype._afterInitialize = function()
	{
		var element = BX(this.getSetting("name", null));
		if(BX.type.isDomNode(element))
		{
			this._nameField = this.addField(BX.OrderDupCtrlField.create("NAME", element, BX(this.getSetting("nameCaption", null))));
		}
		element = BX(this.getSetting("secondName", null));
		if(BX.type.isDomNode(element))
		{
			this._secondNameField = this.addField(BX.OrderDupCtrlField.create("SECOND_NAME", element, BX(this.getSetting("secondNameCaption", null))));
		}
		element = BX(this.getSetting("lastName", null));
		if(BX.type.isDomNode(element))
		{
			this._lastNameField = this.addField(BX.OrderDupCtrlField.create("LAST_NAME", element, BX(this.getSetting("lastNameCaption", null))));
		}
	};
	BX.OrderDupCtrlFullName.prototype.getName = function()
	{
		return this._nameField ? BX.util.trim(this._nameField.getValue()) : "";
	};
	BX.OrderDupCtrlFullName.prototype.getSecondName = function()
	{
		return this._secondNameField ? BX.util.trim(this._secondNameField.getValue()) : "";
	};
	BX.OrderDupCtrlFullName.prototype.getLastName = function()
	{
		return this._lastNameField ? BX.util.trim(this._lastNameField.getValue()) : "";
	};
	BX.OrderDupCtrlFullName.prototype.prepareSearchParams = function()
	{
		var lastName = this.getLastName();
		if(lastName === "")
		{
			return null;
		}

		var result = { "LAST_NAME": lastName };
		var name = this.getName();
		if(name !== "")
		{
			result["NAME"] = name;
		}
		var secondName = this.getSecondName();
		if(secondName !== "")
		{
			result["SECOND_NAME"] = secondName;
		}

		return result;
	};
	BX.OrderDupCtrlFullName.prototype.getSearchHashCode = function()
	{
		var lastName = this.getLastName();
		if(lastName === "")
		{
			return 0;
		}

		var key = lastName.toLowerCase();
		var name = this.getName();
		if(name !== "")
		{
			key += "$" + name.toLowerCase();
		}

		var secondName = this.getSecondName();
		if(secondName !== "")
		{
			key += "$" + secondName.toLowerCase();
		}

		return BX.util.hashCode(key);
	};
	BX.OrderDupCtrlFullName.prototype.getDefaultSearchSummaryFieldId = function()
	{
		return this._lastNameField ? this._lastNameField.getId() : ""
	};
	BX.OrderDupCtrlFullName.prototype.processFieldDelay = function(field)
	{
		this._fireChangeEvent(field);
	};
	BX.OrderDupCtrlFullName.prototype.processFieldFocusLoss = function(field)
	{
		this._fireChangeEvent(field);
	};
	BX.OrderDupCtrlFullName.prototype._fireChangeEvent = function(field)
	{
		if(this._controller)
		{
			this._controller.processGroupChange(this, field);
		}
	};
	BX.OrderDupCtrlFullName.create = function(id, settings)
	{
		var self = new BX.OrderDupCtrlFullName();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderDupCtrlCommunication) == "undefined")
{
	BX.OrderDupCtrlCommunication = function()
	{
		this._communicationType = "";
		this._container = null;
		this._editorCreateItemHandler = BX.delegate(this.onCommunicaionEditorItemCreate, this);
		this._editorDeleteItemHandler = BX.delegate(this.onCommunicaionEditorItemDelete, this);
		this._firstField = null;
		this._lastField = null;

		BX.OrderDupCtrlCommunication.superclass.constructor.apply(this);
	};
	BX.extend(BX.OrderDupCtrlCommunication, BX.OrderDupCtrlFieldGroup);
	BX.OrderDupCtrlCommunication.prototype._afterInitialize = function()
	{
		this._communicationType = this.getSetting("communicationType", "");
		if(!BX.type.isNotEmptyString(this._communicationType))
		{
			throw "BX.OrderDupCtrlCommunication. Could not find communication type.";
		}

		this._editorId = this.getSetting("editorId", "");
		if(!BX.type.isNotEmptyString(this._editorId))
		{
			throw "BX.OrderDupCtrlCommunication. Could not find editor Id.";
		}

		this._container = this.getSetting("container", null);
		if(BX.type.isNotEmptyString(this._container))
		{
			this._container = BX(this._container);
		}
		if(!BX.type.isElementNode(this._container))
		{
			this._container = BX(this._editorId);
		}
		if(!BX.type.isElementNode(this._container))
		{
			throw "BX.OrderDupCtrlCommunication. Could not find container.";
		}

		BX.addCustomEvent(window, "OrderFieldMultiEditorItemCreated", this._editorCreateItemHandler);
		BX.addCustomEvent(window, "OrderFieldMultiEditorItemDeleted", this._editorDeleteItemHandler);

		this._initializeFields();
	};
	BX.OrderDupCtrlCommunication.prototype._initializeFields = function()
	{
		this.clearFields();

		var caption = BX(this.getSetting("editorCaption", null));
		var inputs = BX.findChildren(this._container, { tagName: "input", className: "bx-order-edit-input" }, true);
		var length = inputs.length;
		for(var i = 0; i < length; i++)
		{
			var field = this.addField(BX.OrderDupCtrlField.create("VALUE_" + (i + 1).toString(), inputs[i], caption));
			if(i === 0)
			{
				this._firstField = field;
			}
			if(i === (length - 1))
			{
				this._lastField = field;
			}
		}
	};
	BX.OrderDupCtrlCommunication.prototype.prepareSearchParams = function()
	{
		var rawValues = this.getFieldValues();
		var length = rawValues.length;
		if(length === 0)
		{
			return null;
		}

		var result = {};
		if(this._communicationType !== "PHONE")
		{
			result[this._communicationType] = rawValues;
			return result;
		}

		var values = [];
		for(var i = 0; i < length; i++)
		{
			var value = rawValues[i];
			if(value.length >= 5)
			{
				values.push(value);
			}
		}

		if(values.length === 0)
		{
			return null;
		}

		result["PHONE"] = values;
		return result;
	};
	BX.OrderDupCtrlCommunication.prototype.getSearchHashCode = function()
	{
		var values = this.getFieldValues();
		return (values.length > 0 ? BX.util.hashCode(values.join("$")) : 0);
	};
	BX.OrderDupCtrlCommunication.prototype.getDefaultSearchSummaryFieldId = function()
	{
		return this._firstField ? this._firstField.getId() : ""
	};
	BX.OrderDupCtrlCommunication.prototype.processFieldDelay = function(field)
	{
		if(this._controller)
		{
			this._controller.processGroupChange(this, field);
		}
	};
	BX.OrderDupCtrlCommunication.prototype.processFieldFocusLoss = function(field)
	{
		if(this._controller)
		{
			this._controller.processGroupChange(this, field);
		}
	};
	BX.OrderDupCtrlCommunication.prototype.onCommunicaionEditorItemCreate = function(sender, editorId)
	{
		if(this._editorId !== editorId)
		{
			return;
		}

		this._initializeFields();

		//if(this._controller)
		//{
		//	this._controller.processGroupChange(this, field);
		//}
	};
	BX.OrderDupCtrlCommunication.prototype.onCommunicaionEditorItemDelete = function(sender, editorId)
	{
		if(this._editorId !== editorId)
		{
			return;
		}

		this._initializeFields();

		if(this._controller)
		{
			this._controller.processGroupChange(this, this._lastField);
		}
	};
	BX.OrderDupCtrlCommunication.create = function(id, settings)
	{
		var self = new BX.OrderDupCtrlCommunication();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderDuplicateSummaryItem) == "undefined")
{
	BX.OrderDuplicateSummaryItem = function()
	{
		this._id = "";
		this._settings = {};
		this._groupId = "";
		this._controller = null;
		this._container = null;
		//this._popup = null;
	};
	BX.OrderDuplicateSummaryItem.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};

			this._controller = this.getSetting("controller", null);
			if(!this._controller)
			{
				throw "BX.OrderDuplicateListPopup. Parameter 'controller' is not found.";
			}

			this._container = this.getSetting("container", null);
			if(!this._controller)
			{
				throw "BX.OrderDuplicateSummaryItem. Parameter 'container' is not found.";
			}

			this._link = this.getSetting("link", null);
			if(!this._link)
			{
				throw "BX.OrderDuplicateSummaryItem. Parameter 'link' is not found.";
			}
			BX.bind(this._link, "click", BX.delegate(this._onLinkClick, this));

			this._groupId = this.getSetting("groupId", null);
		},
		getSetting: function (name, defaultval)
		{
			return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
		},
		setSetting: function (name, val)
		{
			this._settings[name] = val;
		},
		_onLinkClick: function(e)
		{
			if(this._groupId !== "")
			{
				var popup = BX.OrderDuplicateListPopup.create(
					this._id,
					{
						controller: this._controller,
						groupId: this._groupId
					}
				);
				popup.show();
			}
		}
	};
	BX.OrderDuplicateSummaryItem.create = function(id, settings)
	{
		var self = new BX.OrderDuplicateSummaryItem();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderDuplicateSummaryPopup) == "undefined")
{
	BX.OrderDuplicateSummaryPopup = function()
	{
		this._id = "";
		this._settings = {};
		this._controller = null;
		this._items = {};
		this._popup = null;
	};
	BX.OrderDuplicateSummaryPopup.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};

			this._controller = this.getSetting("controller", null);
			if(!this._controller)
			{
				throw "BX.OrderDuplicateSummaryPopup. Parameter 'controller' is not found.";
			}
		},
		getSetting: function (name, defaultval)
		{
			return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
		},
		setSetting: function (name, val)
		{
			this._settings[name] = val;
		},
		getId: function()
		{
			return this._id;
		},
		show: function()
		{
			if(this.isShown())
			{
				return;
			}

			var id = this.getId();
			if(BX.OrderDuplicateSummaryPopup.windows[id])
			{
				BX.OrderDuplicateSummaryPopup.windows[id].destroy();
			}

			var anchor = this.getSetting("anchor", null);
			this._popup = new BX.PopupWindow(
				id,
				anchor,
				{
					autoHide: false,
					draggable: true,
					bindOptions: { forceBindPosition: false },
					closeByEsc: false,
					events:
					{
						//onPopupShow: BX.delegate(this._onPopupShow, this),
						onPopupClose: BX.delegate(this._onPopupClose, this),
						onPopupDestroy: BX.delegate(this._onPopupDestroy, this)
					},
					content: this._prepareContent(),
					className : "order-tip-popup",
					angle: { position: "right" },
					lightShadow : true
				}
			);

			BX.OrderDuplicateSummaryPopup.windows[id] = this._popup;
			this._popup.show();

			//move to left
			var anchorPos = BX.pos(anchor);
			var anglePos = BX.pos(this._popup.angle.element);
			var popupPos = BX.pos(this._popup.popupContainer);

			var offsetX = this._popup.popupContainer.offsetWidth + anglePos.width + 5;
			var offsetY = anchorPos.height + (anglePos.height + this._popup.angle.element.offsetTop) / 2;

			if(offsetX < popupPos.left && offsetY < popupPos.top)
			{
				this._popup.move(-offsetX, -offsetY);
			}
		},
		close: function()
		{
			if(!(this._popup && this._popup.isShown()))
			{
				return;
			}

			this._popup.close();
		},
		isShown: function()
		{
			return this._popup && this._popup.isShown();
		},
		getMessage: function(name)
		{
			return BX.OrderDuplicateSummaryPopup.messages && BX.OrderDuplicateSummaryPopup.messages.hasOwnProperty(name) ? BX.OrderDuplicateSummaryPopup.messages[name] : "";
		},
		_prepareContent: function()
		{
			this._items = {};
			var infos = {};
			var data = this._controller.getDuplicateData();
			var groupId;
			for(groupId in data)
			{
				if(!data.hasOwnProperty(groupId))
				{
					continue;
				}

				var groupData = data[groupId];
				if(BX.type.isNotEmptyString(groupData["totalText"]))
				{
					infos[groupId] = { total: groupData["totalText"] };
				}
			}

			//order-tip-popup-cont
			var wrapper = BX.create(
				"DIV",
				{
					attrs: { className: "order-tip-popup-cont" }
				}
			);

			var titleIsAdded = false;
			for(groupId in infos)
			{
				if(!infos.hasOwnProperty(groupId))
				{
					continue;
				}

				var group = this._controller.getGroup(groupId);
				if(!group)
				{
					continue;
				}

				var itemLink = BX.create(
					"SPAN",
					{
						attrs: { className: "order-tip-popup-link" },
						text: infos[groupId]["total"]
					}
				);

				var itemContainer =
					BX.create("DIV",
						{
							attrs: { className: "order-tip-popup-item" }
						}
					);

				if(!titleIsAdded)
				{
					itemContainer.appendChild(
						BX.create("SPAN",
							{
								text: this.getMessage("title") + " "
							}
						)
					);
					titleIsAdded = true;
				}

				itemContainer.appendChild(itemLink);
				itemContainer.appendChild(
					BX.create("SPAN",
						{
							text: " " + group.getSummaryTitle()
						}
					)
				);
				wrapper.appendChild(itemContainer);

				this._items[groupId] = BX.OrderDuplicateSummaryItem.create(
					groupId,
					{
						controller: this._controller,
						container: itemContainer,
						link: itemLink,
						groupId: groupId
					}
				);
			}
			return wrapper;
		},
		_onPopupClose: function()
		{
			if(this._popup)
			{
				this._popup.destroy();
			}
		},
		_onPopupDestroy: function()
		{
			if(this._popup)
			{
				this._popup = null;
			}
		}
	};
	BX.OrderDuplicateSummaryPopup.windows = {};
	if(typeof(BX.OrderDuplicateSummaryPopup.messages) == "undefined")
	{
		BX.OrderDuplicateSummaryPopup.messages = {};
	}
	BX.OrderDuplicateSummaryPopup.create = function(id, settings)
	{
		var self = new BX.OrderDuplicateSummaryPopup();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderDuplicateWarningDialog) == "undefined")
{
	BX.OrderDuplicateWarningDialog = function()
	{
		this._id = "";
		this._settings = {};
		this._controller = null;
		this._popup = null;
		this._contentWrapper = null;
	};
	BX.OrderDuplicateWarningDialog.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};

			this._controller = this.getSetting("controller", null);
			if(!this._controller)
			{
				throw "BX.OrderDuplicateWarningDialog. Parameter 'controller' is not found.";
			}
		},
		getSetting: function (name, defaultval)
		{
			return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
		},
		setSetting: function (name, val)
		{
			this._settings[name] = val;
		},
		getId: function()
		{
			return this._id;
		},
		show: function()
		{
			if(this.isShown())
			{
				return;
			}

			var id = this.getId();
			if(BX.OrderDuplicateWarningDialog.windows[id])
			{
				BX.OrderDuplicateWarningDialog.windows[id].destroy();
			}

			var anchor = this.getSetting("anchor", null);
			this._popup = new BX.PopupWindow(
				id,
				anchor,
				{
					autoHide: false,
					draggable: true,
					bindOptions: { forceBindPosition: false },
					closeByEsc: true,
					closeIcon : {
						marginRight:"4px",
						marginTop:"9px"
					},
					titleBar: { content: this._prepareTitleBarContent() },
					events:
					{
						onPopupShow: BX.delegate(this._onPopupShow, this),
						onPopupClose: BX.delegate(this._onPopupClose, this),
						onPopupDestroy: BX.delegate(this._onPopupDestroy, this)
					},
					content: this._prepareContent(),
					className : "order-tip-popup",
					lightShadow : true,
					buttons: [
						new BX.PopupWindowButton(
							{
								text : this.getMessage("acceptButtonTitle"),
								className : "popup-window-button-create",
								events:
								{
									click: BX.delegate(this._onAcceptButtonClick, this)
								}
							}
						),
						new BX.PopupWindowButtonLink(
							{
								text : this.getMessage("cancelButtonTitle"),
								className : "webform-button-link-cancel",
								events:
								{
									click: BX.delegate(this._onCancelButtonClick, this)
								}
							}
						)
					]
				}
			);

			BX.OrderDuplicateWarningDialog.windows[id] = this._popup;
			this._popup.show();
			this._contentWrapper.tabIndex = "1";
			this._contentWrapper.focus();
		},
		close: function()
		{
			if(!(this._popup && this._popup.isShown()))
			{
				return;
			}

			this._popup.close();
		},
		isShown: function()
		{
			return this._popup && this._popup.isShown();
		},
		getMessage: function(name)
		{
			return BX.OrderDuplicateWarningDialog.messages && BX.OrderDuplicateWarningDialog.messages.hasOwnProperty(name) ? BX.OrderDuplicateWarningDialog.messages[name] : "";
		},
		_prepareContent: function()
		{
			this._contentWrapper = BX.OrderDuplicateRenderer.prepareListContent(this._controller.getDuplicateData());
			return this._contentWrapper;
		},
		_prepareTitleBarContent: function()
		{
			return(
				BX.create(
					"SPAN",
					{
						attrs:
						{
							className: "order-cont-info-popup-title"
						},
						text: this.getMessage("title")
					}
				)
			);
		},
		_onCancelButtonClick: function()
		{
			var handler = this.getSetting("onCancel", null);
			if(BX.type.isFunction(handler))
			{
				handler(this);
			}
		},
		_onAcceptButtonClick: function()
		{
			var handler = this.getSetting("onAccept", null);
			if(BX.type.isFunction(handler))
			{
				handler(this);
			}
		},
		_onPopupShow: function()
		{
			if(!this._contentWrapper)
			{
				return;
			}

			var userWrappers = BX.findChildren(
				this._contentWrapper,
				{ className: "order-info-popup-user"  },
				true
			);
			if(userWrappers)
			{
				for(var i = 0; i < userWrappers.length; i++)
				{
					var element = userWrappers[i];
					BX.tooltip(element.getAttribute("data-userid"), element, "");
				}
			}

			BX.bind(this._contentWrapper, "keyup", BX.delegate(this._onKeyUp, this))
		},
		_onPopupClose: function()
		{
			var handler = this.getSetting("onClose", null);
			if(BX.type.isFunction(handler))
			{
				handler(this);
			}

			if(this._popup)
			{
				this._popup.destroy();
			}
		},
		_onPopupDestroy: function()
		{
			if(this._popup)
			{
				this._popup = null;
			}
		},
		_onKeyUp: function(e)
		{
			var c = e.keyCode;
			if(c === 13)
			{
				var handler = this.getSetting("onAccept", null);
				if(BX.type.isFunction(handler))
				{
					handler(this);
				}
			}
		}
	};
	BX.OrderDuplicateWarningDialog.windows = {};
	if(typeof(BX.OrderDuplicateWarningDialog.messages) === "undefined")
	{
		BX.OrderDuplicateWarningDialog.messages = {};
	}
	BX.OrderDuplicateWarningDialog.create = function(id, settings)
	{
		var self = new BX.OrderDuplicateWarningDialog();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderDuplicateListPopup) === "undefined")
{
	BX.OrderDuplicateListPopup = function()
	{
		this._id = "";
		this._settings = {};
		this._controller = null;
		this._groupId = "";
		this._popup = null;
		this._contentWrapper = null;
	};
	BX.OrderDuplicateListPopup.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};

			this._controller = this.getSetting("controller", null);
			if(!this._controller)
			{
				throw "BX.OrderDuplicateListPopup. Parameter 'controller' is not found.";
			}

			this._groupId = this.getSetting("groupId", null);
		},
		getSetting: function (name, defaultval)
		{
			return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
		},
		setSetting: function (name, val)
		{
			this._settings[name] = val;
		},
		getId: function()
		{
			return this._id;
		},
		show: function()
		{
			if(this.isShown())
			{
				return;
			}

			var id = this.getId();
			if(BX.OrderDuplicateListPopup.windows[id])
			{
				BX.OrderDuplicateListPopup.windows[id].destroy();
			}

			var anchor = this.getSetting("anchor", null);
			this._popup = new BX.PopupWindow(
				id,
				anchor,
				{
					autoHide: true,
					draggable: false,
					bindOptions: { forceBindPosition: false },
					closeByEsc: true,
					closeIcon :
					{
						marginRight:"-2px",
						marginTop:"3px"
					},
					events:
					{
						onPopupShow: BX.delegate(this._onPopupShow, this),
						onPopupClose: BX.delegate(this._onPopupClose, this),
						onPopupDestroy: BX.delegate(this._onPopupDestroy, this)
					},
					content: this._prepareContent(),
					lightShadow : true,
					className : "order-tip-popup"
				}
			);

			BX.OrderDuplicateListPopup.windows[id] = this._popup;
			this._popup.show();
		},
		close: function()
		{
			if(!(this._popup && this._popup.isShown()))
			{
				return;
			}

			this._popup.close();
		},
		isShown: function()
		{
			return this._popup && this._popup.isShown();
		},
		getMessage: function(name)
		{
			return BX.OrderDuplicateListPopup.messages && BX.OrderDuplicateListPopup.messages.hasOwnProperty(name) ? BX.OrderDuplicateListPopup.messages[name] : "";
		},
		_prepareContent: function()
		{
			this._contentWrapper = BX.OrderDuplicateRenderer.prepareListContent(
				this._controller.getDuplicateData(),
				{
					groupId: this._groupId,
					classes: [ "order-cont-info-popup-light" ]
				}
			);
			return this._contentWrapper;
		},
		_onPopupShow: function()
		{
			if(!this._contentWrapper)
			{
				return;
			}

			var userWrappers = BX.findChildren(
				this._contentWrapper,
				{ className: "order-info-popup-user"  },
				true
			);
			if(userWrappers)
			{
				for(var i = 0; i < userWrappers.length; i++)
				{
					var element = userWrappers[i];
					BX.tooltip(element.getAttribute("data-userid"), element, "");
				}
			}
		},
		_onPopupClose: function()
		{
			var handler = this.getSetting("onClose", null);
			if(BX.type.isFunction(handler))
			{
				handler(this);
			}

			if(this._popup)
			{
				this._popup.destroy();
			}
		},
		_onPopupDestroy: function()
		{
			if(this._popup)
			{
				this._popup = null;
			}
		}
	};
	BX.OrderDuplicateListPopup.windows = {};
	if(typeof(BX.OrderDuplicateListPopup.messages) == "undefined")
	{
		BX.OrderDuplicateListPopup.messages = {};
	}
	BX.OrderDuplicateListPopup.create = function(id, settings)
	{
		var self = new BX.OrderDuplicateListPopup();
		self.initialize(id, settings);
		return self;
	};
}
if(typeof(BX.OrderDuplicateRenderer) === "undefined")
{
	BX.OrderDuplicateRenderer = function()
	{
	};
	BX.OrderDuplicateRenderer._onCommunicationBlockClick = function(e)
	{
		var element = null;
		if(e)
		{
			if(e.target)
			{
				element = e.target;
			}
			else if(e.srcElement)
			{
				element = e.srcElement;
			}
		}

		if(BX.type.isElementNode(element))
		{
			if(BX.hasClass(element, "order-info-popup-block-main"))
			{
				BX.removeClass(element, "order-info-popup-block-main");
			}

			var wrapper = BX.findParent(element, { className:"order-info-popup-block" });
			if(BX.type.isElementNode(wrapper) && !BX.hasClass(wrapper, "order-info-popup-block-open"))
			{
				BX.addClass(wrapper, "order-info-popup-block-open");
			}

			BX.unbind(element, "click", BX.OrderDuplicateRenderer._onCommunicationBlockClickHandler);
		}
	};
	BX.OrderDuplicateRenderer._onCommunicationBlockClickHandler = BX.delegate(BX.OrderDuplicateRenderer._onCommunicationBlockClick, BX.OrderDuplicateRenderer);
	BX.OrderDuplicateRenderer._prepareCommunications = function(comms)
	{
		if(!BX.type.isArray(comms) || comms.length === 0)
		{
			return null;
		}

		var qty = comms.length;
		if(qty === 1)
		{
			return BX.util.htmlspecialchars(comms[0]);
		}

		var wrapper = BX.create(
			"DIV",
			{
				attrs: { className: "order-info-popup-block" }
			}
		);

		var first = BX.create(
			"DIV",
			{
				attrs: { className: "order-info-popup-block-main" },
				text: comms[0]
			}
		);

		wrapper.appendChild(first);
		BX.bind(first, "click", this._onCommunicationBlockClickHandler);

		var innerWrapper = BX.create(
			"DIV",
			{
				attrs: { className: "order-info-popup-block-inner" }
			}
		);

		for(var i = 1; i < qty; i++)
		{
			innerWrapper.appendChild(
				BX.create(
					"DIV",
					{
						text: comms[i]
					}
				)
			);
		}
		wrapper.appendChild(innerWrapper);
		return wrapper;
	};
	BX.OrderDuplicateRenderer.prepareListContent = function(data, params)
	{
		if(!params)
		{
			params = {};
		}
		var targetGroupId = BX.type.isNotEmptyString(params["groupId"]) ? params["groupId"] : "";

		var infoByType = {};
		for(var groupId in data)
		{
			if(!data.hasOwnProperty(groupId))
			{
				continue;
			}

			if(targetGroupId !== "" && targetGroupId !== groupId)
			{
				continue;
			}

			var groupData = data[groupId];
			var items = BX.type.isArray(groupData["items"]) ? groupData["items"] : [];
			var itemQty = items.length;
			for(var i = 0; i < itemQty; i++)
			{
				var item = items[i];
				var entities = BX.type.isArray(item["ENTITIES"]) ? item["ENTITIES"] : [];
				var entityQty = entities.length;
				for(var j = 0; j < entityQty; j++)
				{
					var entity = entities[j];
					var entityTypeID = BX.type.isNotEmptyString(entity["ENTITY_TYPE_ID"]) ? parseInt(entity["ENTITY_TYPE_ID"]) : 0;
					if(!BX.OrderEntityType.isDefined(entityTypeID))
					{
						continue;
					}

					var entityTypeName = BX.OrderEntityType.resolveName(entityTypeID);
					if(typeof(infoByType[entityTypeName]) === "undefined")
					{
						infoByType[entityTypeName] = [entity];
					}
					else
					{
						var entityID = BX.type.isNotEmptyString(entity["ENTITY_ID"]) ? parseInt(entity["ENTITY_ID"]) : 0;
						var isExists = false;
						for(var n = 0; n < infoByType[entityTypeName].length; n++)
						{
							var curEntity = infoByType[entityTypeName][n];
							var curEntityID = BX.type.isNotEmptyString(curEntity["ENTITY_ID"]) ? parseInt(curEntity["ENTITY_ID"]) : 0;
							if(curEntityID === entityID)
							{
								isExists = true;
								break;
							}
						}

						if(!isExists)
						{
							infoByType[entityTypeName].push(entity);
						}
					}
				}
			}
		}

		var wrapper = BX.create(
			"DIV",
			{
				attrs: { className: "order-cont-info-popup"}
			}
		);

		var wrapperClasses = typeof(params["classes"]) !== "undefined" ? params["classes"] : null;
		if(BX.type.isArray(wrapperClasses))
		{
			for(var m = 0; m < wrapperClasses.length; m++)
			{
				BX.addClass(wrapper, wrapperClasses[m]);
			}
		}

		var table = BX.create(
			"TABLE",
			{
				attrs: { className: "order-cont-info-table" }
			}
		);
		wrapper.appendChild(table);

		var hasNotCompleted = false;
		var hasCompleted = false;

		for(var key in infoByType)
		{
			if(!infoByType.hasOwnProperty(key))
			{
				continue;
			}

			var ttleRow = table.insertRow(-1);
			ttleRow.className = "order-cont-info-table-title";
			var ttlCell = ttleRow.insertCell(-1);
			ttlCell.colspan = 4;
			ttlCell.innerHTML = BX.util.htmlspecialchars(BX.OrderEntityType.categoryCaptions[key]);

			var infos = infoByType[key];
			var infoQty = infos.length;
			for(var k = 0; k < infoQty; k++)
			{
				var info = infos[k];
				var infoRow = table.insertRow(-1);
				var captionRow = infoRow.insertCell(-1);

				if(BX.type.isNotEmptyString(info["URL"]))
				{
					captionRow.appendChild(
						BX.create(
							"A",
							{
								attrs: { href: info["URL"], target: "_blank" },
								text: BX.type.isNotEmptyString(info["TITLE"]) ? info["TITLE"] : "[Untitled]"
							}
						)
					);
				}
				else
				{
					captionRow.innerHTML = BX.type.isNotEmptyString(info["TITLE"])
						? BX.util.htmlspecialchars(info["TITLE"]) : "[Untitled]";
				}

				//Emails
				var hasEmails = false;
				var emailCell = infoRow.insertCell(-1);
				var emails = BX.type.isArray(info["EMAIL"]) ? this._prepareCommunications(info["EMAIL"]) : null;
				if(BX.type.isElementNode(emails))
				{
					emailCell.appendChild(emails);
					hasEmails = true;
				}
				else if(BX.type.isNotEmptyString(emails))
				{
					emailCell.innerHTML = emails;
					hasEmails = true;
				}
				else if(!hasNotCompleted)
				{
					hasNotCompleted = true;
				}

				//Phones
				var hasPhones = false;
				var phoneCell = infoRow.insertCell(-1);
				phoneCell.className = "order-cont-info-table-tel";
				var phones = BX.type.isArray(info["PHONE"]) ? this._prepareCommunications(info["PHONE"]) : null;
				if(BX.type.isElementNode(phones))
				{
					phoneCell.appendChild(phones);
					hasPhones = true;
				}
				else if(BX.type.isNotEmptyString(phones))
				{
					phoneCell.innerHTML = phones;
					hasPhones = true;
				}
				else if(!hasNotCompleted)
				{
					hasNotCompleted = true;
				}

				if(hasEmails && hasPhones && !hasCompleted)
				{
					hasCompleted = true;
				}

				var responsibleCell = infoRow.insertCell(-1);
				var responsibleID = BX.type.isNotEmptyString(info["RESPONSIBLE_ID"]) ? parseInt(info["RESPONSIBLE_ID"]) : 0;
				if(responsibleID > 0)
				{
					var userWrapper = BX.create(
						"DIV",
						{
							attrs: { className: "order-info-popup-user" }
						}
					);
					responsibleCell.appendChild(userWrapper);
					userWrapper.className = "order-info-popup-user";
					userWrapper.setAttribute("data-userid", responsibleID.toString());

					var styles = {};
					if(BX.type.isNotEmptyString(info["RESPONSIBLE_PHOTO_URL"]))
					{
						styles["background"] = "url(" + info["RESPONSIBLE_PHOTO_URL"] + ") repeat scroll center center";
					}

					userWrapper.appendChild(
						BX.create(
							"SPAN",
							{
								attrs: { className: "order-info-popup-user-img" },
								style: styles
							}
						)
					);

					userWrapper.appendChild(
						BX.create(
							"A",
							{
								attrs:
								{
									target: "_blank",
									href: BX.type.isNotEmptyString(info["RESPONSIBLE_URL"]) ? info["RESPONSIBLE_URL"] : "#",
									className: "order-info-popup-user-name"
								},
								text: BX.type.isNotEmptyString(info["RESPONSIBLE_FULL_NAME"]) ? info["RESPONSIBLE_FULL_NAME"] : ("[" + responsibleID + "]")
							}
						)
					);
				}
			}
		}

		if(!hasCompleted)
		{
			BX.addClass(table, "order-cont-info-table-empty");
		}
		return wrapper;
	}
}

if(typeof(BX.NotificationPopup) == "undefined")
{
	BX.NotificationPopup = function()
	{
		this._id = "";
		this._settings = {};
		this._popup = null;
		this._contentWrapper = null;
		this._title = "";
		this._timeout = 3000;
		this._timeoutId = null;
		this._messages = [];
	};
	BX.NotificationPopup.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};

			this._messages = this.getSetting("messages", null);
			if(!BX.type.isArray(this._messages) || this._messages.length === 0)
			{
				throw "BX.NotificationPopup. Parameter 'messages' is not defined or empty.";
			}

			var timeout = parseInt(this.getSetting("timeout", 3000));
			if(isNaN(timeout) || timeout <= 0)
			{
				timeout = 3000;
			}
			this._timeout = timeout;
		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		setSetting: function (name, val)
		{
			this._settings[name] = val;
		},
		getId: function()
		{
			return this._id;
		},
		show: function()
		{
			if(this.isShown())
			{
				return;
			}

			var id = this.getId();
			if(BX.NotificationPopup.windows[id])
			{
				BX.NotificationPopup.windows[id].destroy();
			}

			this._popup = new BX.PopupWindow(
				id,
				null,
				{
					autoHide: true,
					draggable: false,
					zIndex: 10200,
					className: "bx-notification-popup",
					closeByEsc: true,
					events:
					{
						onPopupClose: BX.delegate(this.onPopupClose, this),
						onPopupDestroy: BX.delegate(this.onPopupDestroy, this)
					},
					content: this.prepareContent()
				}
			);

			BX.NotificationPopup.windows[id] = this._popup;
			this._popup.show();

			this._timeoutId = setTimeout(BX.delegate(this.close, this), this._timeout);

			BX.bind(this._contentWrapper, "mouseover", BX.delegate(this._onMouseOver, this));
			BX.bind(this._contentWrapper, "mouseout", BX.delegate(this._onMouseOut, this));
		},
		_onMouseOver: function(e)
		{
			if(this._timeoutId !== null)
			{
				clearTimeout(this._timeoutId);
			}
		},
		_onMouseOut: function(e)
		{
			this._timeoutId = setTimeout(BX.delegate(this.close, this), this._timeout);
		},
		close: function()
		{
			if(!(this._popup && this._popup.isShown()))
			{
				return;
			}

			this._popup.close();
		},
		isShown: function()
		{
			return this._popup && this._popup.isShown();
		},
		prepareContent: function()
		{
			this._contentWrapper = BX.create("DIV", { attrs: { className: "bx-notification" } });
			this._contentWrapper.appendChild(BX.create("SPAN", { attrs: { className: "bx-notification-aligner" } }));
			for(var i = 0; i < this._messages.length; i++)
			{
				this._contentWrapper.appendChild(
					BX.create("SPAN", { props: { className: "bx-notification-text" }, text: this._messages[i] })
				);
			}
			this._contentWrapper.appendChild(BX.create("DIV", { props: { className: "bx-notification-footer" } }));
			return this._contentWrapper;
		},
		onPopupClose: function()
		{
			if(this._popup)
			{
				this._popup.destroy();
			}
		},
		onPopupDestroy: function()
		{
			if(this._popup)
			{
				this._popup = null;
			}

			if(this._contentWrapper)
			{
				this._contentWrapper = null;
			}
		}
	};
	BX.NotificationPopup.windows = {};
	BX.NotificationPopup.create = function(id, settings)
	{
		var self = new BX.NotificationPopup();
		self.initialize(id, settings);
		return self;
	};
	BX.NotificationPopup.show = function(id, settings)
	{
		this.create(id, settings).show();
	}
}

if(typeof(BX.OrderInterfaceMode) === "undefined")
{
	BX.OrderInterfaceMode = { edit: 1, view: 2 };
}

if(typeof(BX.GridAjaxLoader) === "undefined")
{
	BX.GridAjaxLoader = function()
	{
		this._id = "";
		this._settings = {};
		this._url = "";
		this._method = "";
		this._data = {};
		this._dataType = "html";
		this._ajaxId = "";
		this._ajaxInsertHandler = BX.delegate(this._onAjaxInsert, this);
	};

	BX.GridAjaxLoader.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};
			this._url = this.getSetting("url", "");
			this._method = this.getSetting("method", "GET");
			this._data = this.getSetting("data", {});
			this._dataType = this.getSetting("dataType", "html");
			this._ajaxId = this.getSetting("ajaxId", "");
			this._urlAjaxIdRegex = /bxajaxid\s*=\s*([a-z0-9]+)/i;
			//Page number expression : first param is url-parameter name and second param is page number.
			this._urlPageNumRegexes =
				[
					/(PAGEN_[0-9]+)\s*=\s*([0-9]+)/i, //Standard page navigation
					/(page)\s*=\s*([0-9]+)/i //Optimized ORDER page navigation
				];

			BX.addCustomEvent(window, "onAjaxInsertToNode", this._ajaxInsertHandler);
		},
		release: function()
		{
			BX.removeCustomEvent(window, "onAjaxInsertToNode", this._ajaxInsertHandler);
		},
		getSetting: function(name, defaultvalue)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultvalue;
		},
		getId: function()
		{
			return this._id;
		},
		reload: function(url, callback)
		{
			if(!BX.type.isNotEmptyString(url))
			{
				url = this._url;
			}
			url = BX.util.add_url_param(url, { "bxajaxid": this._ajaxId });

			var cfg = { url: url, dataType: this._dataType };
			if(this._method === "POST")
			{
				cfg["method"] = "POST";
				cfg["data"] = this._data;
			}
			else
			{
				cfg["method"] = "GET";
			}

			if(BX.type.isFunction(callback))
			{
				cfg["onsuccess"] = callback;
			}

			BX.ajax(cfg);
		},
		loadPage: function(pageParam, pageNumber)
		{
			var urlParams = { "bxajaxid": this._ajaxId };
			urlParams[pageParam] = pageNumber;
			var cfg =
			{
				url: BX.util.add_url_param(this._url, urlParams),
				dataType: this._dataType
			};

			if(this._method === "POST")
			{
				cfg["method"] = "POST";
				cfg["data"] = this._data;
			}
			else
			{
				cfg["method"] = "GET";
			}

			cfg["onsuccess"] = BX.delegate(this._onPageLoadSuccess, this);
			BX.ajax(cfg);
		},
		setupForm: function(form, url)
		{
			if(!BX.type.isNotEmptyString(url))
			{
				url = this._url;
			}
			url = BX.util.add_url_param(url, { "bxajaxid": this._ajaxId });
			form.action = url;

			BX.util.addObjectToForm(this._data, form);
		},
		_onAjaxInsert: function(params)
		{
			if(typeof(params.eventArgs) === "undefined")
			{
				return;
			}

			var m = this._urlAjaxIdRegex.exec(params.url);
			if(BX.type.isArray(m) && m.length > 1 && m[1] === this._ajaxId)
			{
				var l = this._urlPageNumRegexes.length;
				for(var i = 0; i < l; i++)
				{
					m = this._urlPageNumRegexes[i].exec(params.url);
					if(!(BX.type.isArray(m) && m.length > 2))
					{
						continue;
					}

					this.loadPage(m[1], m[2]);

					params.eventArgs.cancel = true;
					return;
				}
			}
		},
		_onPageLoadSuccess: function(data)
		{
			var node = BX('comp_' + this._ajaxId);
			if(node)
			{
				node.innerHTML = data;
			}
		}
	};

	BX.GridAjaxLoader.items = {};
	BX.GridAjaxLoader.create = function(id, settings)
	{
		var self = new BX.GridAjaxLoader();
		self.initialize(id, settings);
		this.items[id] = self;
		return self;
	};
	BX.GridAjaxLoader.remove = function(id)
	{
		if(typeof(this.items[id]) === "undefined")
		{
			return;
		}

		this.items[id].release();
		delete this.items[id];
	};
}

if(typeof(BX.AddressFormatSelector) === "undefined")
{
	BX.AddressFormatSelector = function()
	{
		this._id = "";
		this._settings = {};
		this._controlPrefix = "";
		this._descrContainer = null;
		this._typeInfos = {};
	};

	BX.AddressFormatSelector.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};

			this._controlPrefix = this.getSetting("controlPrefix");
			this._typeInfos = this.getSetting("typeInfos", {});
			for(var key in this._typeInfos)
			{
				if(!this._typeInfos.hasOwnProperty(key))
				{
					continue;
				}

				var element = BX(this._controlPrefix + key.toLowerCase());
				if(element)
				{
					BX.bind(element, "change", BX.delegate(this._onControlChange, this));
				}
			}
			this._descrContainer = BX(this.getSetting("descrContainerId"));
		},
		getId: function()
		{
			return this._id;
		},
		getSetting: function(name, defaultvalue)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultvalue;
		},
		_onControlChange: function(e)
		{
			if(!e)
			{
				e = window.event;
			}

			var target = BX.getEventTarget(e);
			if(target && BX.type.isNotEmptyString(this._typeInfos[target.value]) && this._descrContainer)
			{
				this._descrContainer.innerHTML = this._typeInfos[target.value];
			}
		}
	};

	BX.AddressFormatSelector.create = function(id, settings)
	{
		var self = new BX.AddressFormatSelector();
		self.initialize(id, settings);
		return self;
	};
}

if(typeof(BX.OrderLongRunningProcessManager) == "undefined")
{
	BX.OrderLongRunningProcessManager = function()
	{
		this._id = "";
		this._settings = {};
		this._serviceUrl = "";
		this._actionName = "";
		this._dialog = null;
	};
	BX.OrderLongRunningProcessManager.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = BX.type.isNotEmptyString(id) ? id : "order_lrp_mgr_" + Math.random().toString().substring(2);
			this._settings = settings ? settings : {};

			this._serviceUrl = this.getSetting("serviceUrl", "");
			if(!BX.type.isNotEmptyString(this._serviceUrl))
			{
				throw "BX.OrderLongRunningProcessManager. Could not find 'serviceUrl' parameter in settings.";
			}

			this._actionName = this.getSetting("actionName", "");
			if(!BX.type.isNotEmptyString(this._actionName))
			{
				throw "BX.OrderLongRunningProcessManager. Could not find 'actionName' parameter in settings.";
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
			var m = BX.OrderLongRunningProcessManager.messages;
			return m.hasOwnProperty(name) ? m[name] : name;
		},
		getServiceUrl: function()
		{
			return this._serviceUrl;
		},
		getActionName: function()
		{
			return this._actionName;
		},
		run: function()
		{
			if(!this._dialog)
			{
				var title = this.getSetting("dialogTitle", this.getMessage("dialogTitle"));
				var summary = this.getSetting("dialogSummary", this.getMessage("dialogSummary"));
				this._dialog = BX.OrderLongRunningProcessDialog.create(
					this.getId(),
					{
						serviceUrl: this.getServiceUrl(),
						action: this.getActionName(),
						title: title,
						summary: summary
					}
				);
			}

			BX.addCustomEvent(this._dialog, "ON_STATE_CHANGE", BX.delegate(this._onProcessStateChange, this));
			this._dialog.show();
		},
		_onProcessStateChange: function(sender)
		{
			if(sender === this._dialog)
			{
				if(this._dialog.getState() === BX.OrderLongRunningProcessState.completed)
				{
					BX.onCustomEvent(this, "ON_LONG_RUNNING_PROCESS_COMPLETE", [this]);
				}
			}
		}
	};
	if(typeof(BX.OrderLongRunningProcessManager.messages) == "undefined")
	{
		BX.OrderLongRunningProcessManager.messages = {};
	}
	BX.OrderLongRunningProcessManager.items = {};
	BX.OrderLongRunningProcessManager.create = function(id, settings)
	{
		var self = new BX.OrderLongRunningProcessManager();
		self.initialize(id, settings);
		this.items[self.getId()] = self;
		return self;
	};
}

if(typeof(BX.OrderLongRunningProcessPanel) === "undefined")
{
	BX.OrderLongRunningProcessPanel = function()
	{
		this._id = "";
		this._settings = {};
		this._prefix = "";
		this._hasLayout = false;
		this._active = false;
		this._container = null;
		this._wrapper = null;
		this._link = null;
		this._manager = null;
		this._clickHandler = BX.delegate(this.onClick, this);
		this._processCompleteHandler = BX.delegate(this.onProcessComplete, this);
	};

	BX.OrderLongRunningProcessPanel.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};

			this._container = BX(this.getSetting("containerId"));
			if(!this._container)
			{
				throw "OrderLongRunningProcessPanel: Could not find container.";
			}

			this._active = !!this.getSetting("active", false);
			this._prefix = this.getSetting("prefix");
			this._message = this.getSetting("message");

			this._manager = BX.OrderLongRunningProcessManager.create(this._id, this.getSetting("manager"));
		},
		layout: function()
		{
			if(this._hasLayout)
			{
				return;
			}

			this._wrapper = BX.create("DIV", { props: { className: "order-view-message" } });
			this._container.appendChild(this._wrapper);

			if(!this._active)
			{
				this._wrapper.style.display = "none";
			}

			var linkId = (this._prefix !== "" ? this._prefix : this._id) + "_link";
			var html = this._message.replace(/#ID#/gi, linkId).replace(/#URL#/gi, "#");
			this._wrapper.appendChild(BX.create("SPAN", { html: html }));

			this._link = BX(linkId);
			if(this._link)
			{
				BX.bind(this._link, "click", this._clickHandler);
			}

			this._hasLayout = true;
		},
		cleanLayout: function()
		{
			if(!this._hasLayout)
			{
				return;
			}

			if(this._link)
			{
				BX.unbind(this._link, "click", this._clickHandler);
				this._link = null;
			}

			BX.cleanNode(this._wrapper, true);
			this._wrapper = null;

			this._hasLayout = false;
		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		isActive: function()
		{
			return this._active;
		},
		setActive: function(active)
		{
			active = !!active;
			if(this._active === active)
			{
				return;
			}

			this._active = active;
			this._wrapper.style.display = active ? "" : "none";
		},
		onClick: function(e)
		{
			BX.addCustomEvent(this._manager, "ON_LONG_RUNNING_PROCESS_COMPLETE", this._processCompleteHandler);
			this._manager.run();
			return BX.PreventDefault(e);
		},
		onProcessComplete: function(mgr)
		{
			if(mgr !== this._manager)
			{
				return;
			}

			BX.removeCustomEvent(this._manager, "ON_LONG_RUNNING_PROCESS_COMPLETE", this._processCompleteHandler);
			this.setActive(false);
		}
	};

	BX.OrderLongRunningProcessPanel.create = function(id, settings)
	{
		var self = new BX.OrderLongRunningProcessPanel();
		self.initialize(id, settings);
		return self;
	};
}

if(typeof(BX.InterfaceFilterFieldInfoProvider) === "undefined")
{
	BX.InterfaceFilterFieldInfoProvider = function()
	{
		this._id = "";
		this._settings = {};
		this._infos = null;
		this._setFildsHandler = BX.delegate(this.onSetFilterFields, this);
		this._getFildsHandler = BX.delegate(this.onGetFilterFields, this);
	};

	BX.InterfaceFilterFieldInfoProvider.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};
			this._infos = this.getSetting("infos", null);

			BX.onCustomEvent(window, "InterfaceFilterFieldInfoProviderCreate", [this]);
		},
		getId: function()
		{
			return this._id;
		},
		getSetting: function (name, defaultval)
		{
			return typeof(this._settings[name]) != "undefined" ? this._settings[name] : defaultval;
		},
		registerFilter: function(filter)
		{
			BX.addCustomEvent(filter, "AFTER_SET_FILTER_FIELDS", this._setFildsHandler);
			BX.addCustomEvent(filter, "AFTER_GET_FILTER_FIELDS", this._getFildsHandler);
		},
		getFieldInfos: function()
		{
			return this._infos;
		},
		onSetFilterFields: function(sender, form, fields)
		{
			var infos = this._infos;
			if(!BX.type.isArray(infos))
			{
				return;
			}

			var isSettingsContext = form.name.indexOf('flt_settings') === 0;

			var count = infos.length;
			var paramName = '';
			for(var i = 0; i < count; i++)
			{
				var info = infos[i];
				var id = BX.type.isNotEmptyString(info['id']) ? info['id'] : '';
				var type = BX.type.isNotEmptyString(info['typeName']) ? info['typeName'].toUpperCase() : '';
				var params = info['params'] ? info['params'] : {};

				if(type === 'USER')
				{
					var data = params['data'] ? params['data'] : {};
					this.setElementByFilter(
						data[isSettingsContext ? 'settingsElementId' : 'elementId'],
						data['paramName'],
						fields
					);

					var search = params['search'] ? params['search'] : {};
					this.setElementByFilter(
						search[isSettingsContext ? 'settingsElementId' : 'elementId'],
						search['paramName'],
						fields
					);
				}
			}
		},
		onGetFilterFields: function(sender, form, fields)
		{
			var infos = this._infos;
			if(!BX.type.isArray(infos))
			{
				return;
			}

			var isSettingsContext = form.name.indexOf('flt_settings') === 0;
			var count = infos.length;
			for(var i = 0; i < count; i++)
			{
				var info = infos[i];
				var id = BX.type.isNotEmptyString(info['id']) ? info['id'] : '';
				var type = BX.type.isNotEmptyString(info['typeName']) ? info['typeName'].toUpperCase() : '';
				var params = info['params'] ? info['params'] : {};

				if(type === 'USER')
				{
					var data = params['data'] ? params['data'] : {};
					this.setFilterByElement(
						data[isSettingsContext ? 'settingsElementId' : 'elementId'],
						data['paramName'],
						fields
					);

					var search = params['search'] ? params['search'] : {};
					this.setFilterByElement(
						search[isSettingsContext ? 'settingsElementId' : 'elementId'],
						search['paramName'],
						fields
					);
				}
			}
		},
		setElementByFilter: function(elementId, paramName, filter)
		{
			var element = BX.type.isNotEmptyString(elementId) ? BX(elementId) : null;
			if(BX.type.isElementNode(element))
			{
				element.value = BX.type.isNotEmptyString(paramName) && filter[paramName] ? filter[paramName] : '';
			}
		},
		setFilterByElement: function(elementId, paramName, filter)
		{
			var element = BX.type.isNotEmptyString(elementId) ? BX(elementId) : null;
			if(BX.type.isElementNode(element) && BX.type.isNotEmptyString(paramName))
			{
				filter[paramName] = element.value;
			}
		}
	};
	BX.InterfaceFilterFieldInfoProvider.items = {};
	BX.InterfaceFilterFieldInfoProvider.create = function(id, settings)
	{
		var self = new BX.InterfaceFilterFieldInfoProvider();
		self.initialize(id, settings);
		this.items[self.getId()] = self;
		return self;
	};
}

if(typeof(BX.OrderConversionSchemeSelector) === "undefined")
{
	BX.OrderConversionSchemeSelector = function()
	{
		this._id = "";
		this._settings = {};
		this._entityId = 0;
		this._scheme = "";

		this._isMenuShown = false;
		this._menuId = "";
		this._container = null;
		this._containerClickHandler = BX.delegate(this.onContainerClick, this);
		this._label = null;
		this._button = null;
		this._buttonClickHandler = BX.delegate(this.onButtonClick, this);
		this._menuIiemClickHandler = BX.delegate(this.onMenuItemClick, this);
		this._menuCloseHandler = BX.delegate(this.onMenuClose, this);
		this._hint = null;
	};
	BX.OrderConversionSchemeSelector.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};

			this._entityId = parseInt(this.getSetting("entityId", 0));
			if(!BX.type.isNumber(this._entityId))
			{
				throw "BX.OrderConversionSchemeSelector: entity id is not found!";
			}

			this._scheme = this.getSetting("scheme", "");

			this._container = BX(this.getSetting("containerId"));
			if(!BX.type.isElementNode(this._container))
			{
				throw "BX.OrderConversionSchemeSelector: container element is not found!";
			}
			BX.bind(this._container, "click", this._containerClickHandler);

			this._menuId = 'order_menu_popup_' + this._id.toLowerCase();
			this._button = BX(this.getSetting("buttonId"));
			if(!BX.type.isElementNode(this._button))
			{
				throw "BX.OrderConversionSchemeSelector: button element is not found!";
			}
			BX.bind(this._button, "click", this._buttonClickHandler);

			var labelId = this.getSetting("labelId", "");
			if(BX.type.isNotEmptyString(labelId))
			{
				this._label = BX(labelId);
			}

			if(this.getSetting("enableHint", false))
			{
				this.createHint(this.getSetting("hintMessages", null));
			}

			this.doInitialize();
		},
		doInitialize: function()
		{
		},
		release: function()
		{
			this.closeMenu();

			BX.unbind(this._container, "click", this._containerClickHandler);
			BX.unbind(this._button, "click", this._buttonClickHandler);
		},
		getSetting: function (name, defaultval)
		{
			return typeof(this._settings[name]) != "undefined" ? this._settings[name] : defaultval;
		},
		getId: function()
		{
			return this._id;
		},
		getScheme: function()
		{
			return this._scheme;
		},
		setScheme: function(scheme)
		{
			this._scheme = scheme;
			this.processSchemeChange();
		},
		processSchemeChange: function()
		{
			if(this._label)
			{
				this._label.innerHTML = BX.util.htmlspecialchars(this.getSchemeDescription(this._scheme));
			}

			window.setTimeout(BX.delegate(this.convert, this), 250);
		},
		getSchemeDescription: function(scheme)
		{
			return "[" + scheme + "]";
		},
		showMenu: function()
		{
			if(this._isMenuShown)
			{
				return;
			}

			var menuItems = [];
			var items = this.prepareItems();
			for(var i = 0; i < items.length; i++)
			{
				var item = items[i];

				menuItems.push(
					{
						text: item["text"],
						value: item["value"],
						href : "#",
						className: "order-convert-item",
						onclick: this._menuIiemClickHandler
					}
				);
			}

			if(typeof(BX.PopupMenu.Data[this._menuId]) !== "undefined")
			{
				BX.PopupMenu.Data[this._menuId].popupWindow.destroy();
				delete BX.PopupMenu.Data[this._menuId];
			}

			var anchor = this._button;
			var anchorPos = BX.pos(anchor);

			BX.PopupMenu.show(
				this._menuId,
				anchor,
				menuItems,
				{
					autoHide: true,
					offsetLeft: (anchorPos["width"] / 2),
					angle: { position: "top", offset: 0 },
					events: { onPopupClose : this._menuCloseHandler }
				}
			);

			this._isMenuShown = true;
		},
		closeMenu: function()
		{
			if(!this._isMenuShown)
			{
				return;
			}

			BX.PopupMenu.destroy(this._menuId);
			this._isMenuShown = false;
		},
		prepareItems: function()
		{
		},
		prepageConfig: function()
		{
		},
		processContainerClick: function()
		{
			this.convert();
		},
		processMenuItemClick: function(item)
		{
			this.setScheme(item["value"]);
			this.closeMenu();
		},
		createHint: function(messages)
		{
			if(!messages)
			{
				return;
			}

			this._hint = BX.PopupWindowManager.create(this._id + "_hint",
				this._container,
				{
					offsetTop : -8,
					autoHide : true,
					closeByEsc : false,
					angle: { position: "bottom", offset: 24 },
					events: { onPopupClose : BX.delegate(this.onHintClose, this) },
					content : BX.create("DIV",
						{
							attrs: { className: "order-conv-selector-popup-contents" },
							children:
								[
									BX.create("SPAN",
										{ attrs: { className: "order-popup-title" }, text: messages["title"]  }
									),
									BX.create("P", { text: messages["content"] }),
									BX.create("P",
										{
											children:
												[
													BX.create("A",
														{
															props: { href: "#" },
															text: messages["disabling"],
															events: { "click": BX.delegate(this.onDisableHint, this)  }
														}
													)
												]
										}
									)
								]
						}
					)
				}
			);
			this._hint.show();
		},
		onDisableHint: function(e)
		{
			if(this._hint)
			{
				this._hint.close();
				BX.userOptions.save(
					"order.interface.toobar",
					"conv_scheme_selector",
					"enable_" + this.getId().toLowerCase() + "_hint",
					"N",
					false
				);
			}
			return BX.PreventDefault(e);
		},
		onHintClose: function()
		{
			if(this._hint)
			{
				this._hint.destroy();
				this._hint = null;
			}
		},
		onButtonClick: function(e)
		{
			this.showMenu();
		},
		onContainerClick: function(e)
		{
			this.processContainerClick();
		},
		onMenuItemClick: function(e, item)
		{
			this.processMenuItemClick(item);
			return BX.PreventDefault(e);
		},
		onMenuClose: function()
		{
			this._isMenuShown = false;
		},
		convert: function()
		{
		}
	};
}

if(typeof(BX.OrderLeadConversionScheme) === "undefined")
{
	BX.OrderLeadConversionScheme =
	{
		undefined: "",
		dealcontactcompany: "DEAL_CONTACT_COMPANY",
		dealcontact: "DEAL_CONTACT",
		dealcompany: "DEAL_COMPANY",
		deal: "DEAL",
		contactcompany: "CONTACT_COMPANY",
		contact: "CONTACT",
		company: "COMPANY",
		getListItems: function(ids)
		{
			var results = [];
			for(var i = 0; i < ids.length; i++)
			{
				var id = ids[i];
				results.push({ value: id, text: this.getDescription(id) });
			}

			return results;
		},
		getDescription: function(id)
		{
			var m = this.messages;
			return m.hasOwnProperty(id) ? m[id] : id;
		},
		fromConfig: function(config)
		{
			var scheme = this.undefined;
			var isDealActive = this.isEntityActive(config, "deal");
			var isContactActive = this.isEntityActive(config, "contact");
			var isCompanyActive = this.isEntityActive(config, "company");

			if(isDealActive)
			{
				if(isContactActive && isCompanyActive)
				{
					scheme = this.dealcontactcompany;
				}
				else if(isContactActive)
				{
					scheme = this.dealcontact;
				}
				else if(isCompanyActive)
				{
					scheme = this.dealcompany;
				}
				else
				{
					scheme = this.deal;
				}
			}
			else if(isContactActive && isCompanyActive)
			{
				scheme = this.contactcompany;
			}
			else if(isContactActive)
			{
				scheme = this.contact;
			}
			else if(isCompanyActive)
			{
				scheme = this.company;
			}

			return scheme;
		},
		toConfig: function(scheme, config)
		{
			this.markEntityAsActive(
				config,
				"deal",
				scheme === this.dealcontactcompany || scheme === this.dealcontact || scheme === this.dealcompany || scheme === this.deal
			);

			this.markEntityAsActive(
				config,
				"contact",
				scheme === this.dealcontactcompany || scheme === this.dealcontact || scheme === this.contactcompany || scheme === this.contact
			);

			this.markEntityAsActive(
				config,
				"company",
				scheme === this.dealcontactcompany || scheme === this.dealcompany || scheme === this.contactcompany || scheme === this.company
			);
		},
		createConfig: function(scheme)
		{
			var config = {};
			this.toConfig(scheme, config);
			return config;
		},
		isEntityActive: function(config, entityTypeName)
		{
			var params = typeof(config[entityTypeName]) !== "undefined" ? config[entityTypeName] : {};
			return BX.type.isNotEmptyString(params["active"]) && params["active"] === "Y"
		},
		markEntityAsActive: function(config, entityTypeName, active)
		{
			if(typeof(config[entityTypeName]) === "undefined")
			{
				config[entityTypeName] = {};
			}
			config[entityTypeName]["active"] = active ? "Y" : "N";
		}
	};
	if(typeof(BX.OrderLeadConversionScheme.messages) === "undefined")
	{
		BX.OrderLeadConversionScheme.messages = {};
	}
}

if(typeof(BX.OrderDealConversionScheme) === "undefined")
{
	BX.OrderDealConversionScheme =
	{
		undefined: "",
		invoice: "INVOICE",
		quote: "QUOTE",
		getListItems: function(ids)
		{
			var results = [];
			for(var i = 0; i < ids.length; i++)
			{
				var id = ids[i];
				results.push({ value: id, text: this.getDescription(id) });
			}

			return results;
		},
		getDescription: function(id)
		{
			var m = this.messages;
			return m.hasOwnProperty(id) ? m[id] : id;
		},
		fromConfig: function(config)
		{
			var scheme = this.undefined;
			if(this.isEntityActive(config, "invoice"))
			{
				scheme = this.invoice;
			}
			else if(this.isEntityActive(config, "quote"))
			{
				scheme = this.quote;
			}
			return scheme;
		},
		toConfig: function(scheme, config)
		{
			this.markEntityAsActive(config, "invoice", scheme === this.invoice);
			this.markEntityAsActive(config, "quote", scheme === this.quote);
		},
		createConfig: function(scheme)
		{
			var config = {};
			this.toConfig(scheme, config);
			return config;
		},
		isEntityActive: function(config, entityTypeName)
		{
			var params = typeof(config[entityTypeName]) !== "undefined" ? config[entityTypeName] : {};
			return BX.type.isNotEmptyString(params["active"]) && params["active"] === "Y"
		},
		markEntityAsActive: function(config, entityTypeName, active)
		{
			if(typeof(config[entityTypeName]) === "undefined")
			{
				config[entityTypeName] = {};
			}
			config[entityTypeName]["active"] = active ? "Y" : "N";
		}
	};
	if(typeof(BX.OrderDealConversionScheme.messages) === "undefined")
	{
		BX.OrderDealConversionScheme.messages = {};
	}
}

if(typeof(BX.OrderQuoteConversionScheme) === "undefined")
{
	BX.OrderQuoteConversionScheme =
	{
		undefined: "",
		deal: "DEAL",
		invoice: "INVOICE",
		getListItems: function(ids)
		{
			var results = [];
			for(var i = 0; i < ids.length; i++)
			{
				var id = ids[i];
				results.push({ value: id, text: this.getDescription(id) });
			}

			return results;
		},
		getDescription: function(id)
		{
			var m = this.messages;
			return m.hasOwnProperty(id) ? m[id] : id;
		},
		fromConfig: function(config)
		{
			var scheme = this.undefined;
			if(this.isEntityActive(config, "deal"))
			{
				scheme = this.deal;
			}
			else if(this.isEntityActive(config, "invoice"))
			{
				scheme = this.invoice;
			}
			return scheme;
		},
		toConfig: function(scheme, config)
		{
			this.markEntityAsActive(config, "deal", scheme === this.deal);
			this.markEntityAsActive(config, "invoice", scheme === this.invoice);
		},
		createConfig: function(scheme)
		{
			var config = {};
			this.toConfig(scheme, config);
			return config;
		},
		isEntityActive: function(config, entityTypeName)
		{
			var params = typeof(config[entityTypeName]) !== "undefined" ? config[entityTypeName] : {};
			return BX.type.isNotEmptyString(params["active"]) && params["active"] === "Y"
		},
		markEntityAsActive: function(config, entityTypeName, active)
		{
			if(typeof(config[entityTypeName]) === "undefined")
			{
				config[entityTypeName] = {};
			}
			config[entityTypeName]["active"] = active ? "Y" : "N";
		}
	};
	if(typeof(BX.OrderQuoteConversionScheme.messages) === "undefined")
	{
		BX.OrderQuoteConversionScheme.messages = {};
	}
}

if(typeof(BX.OrderEntityConverterMode) === "undefined")
{
	BX.OrderEntityConverterMode =
	{
		intermediate: 0,
		schemeSetup: 1,
		syncSetup: 2,
		request: 3
	}
}

if(typeof(BX.OrderEntityConverter) === "undefined")
{
	BX.OrderEntityConverter = function()
	{
		this._id = "";
		this._settings = {};
		this._config = {};
		this._contextData = null;
		this._mode = BX.OrderEntityConverterMode.intermediate;
		this._entityId = 0;
		this._originUrl = "";
		this._syncEditor = null;
		this._syncEditorClosingListener = BX.delegate(this.onSyncEditorClose, this);
		this._enableSync = false;
		this._requestIsRunning = false;
	};
	BX.OrderEntityConverter.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};

			this._config = this.getSetting("config", {});
			this._serviceUrl = this.getSetting("serviceUrl", "");
		},
		getSetting: function(name, defaultval)
		{
			return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
		},
		setSetting: function(name, val)
		{
			this._settings[name] = val;
		},
		getId: function()
		{
			return this._id;
		},
		getMessage: function(name)
		{
			return name;
		},
		getConfig: function()
		{
			return this._config;
		},
		setupSynchronization: function(fieldNames)
		{
			this._mode = BX.OrderEntityConverterMode.syncSetup;
			if(this._syncEditor)
			{
				this._syncEditor.setConfig(this._config);
				this._syncEditor.setFieldNames(fieldNames);
			}
			else
			{
				this._syncEditor = BX.OrderEntityFieldSynchronizationEditor.create(
					this._id + "_config",
					{
						converter: this,
						config: this._config,
						title: this.getMessage("dialogTitle"),
						fieldNames: fieldNames,
						legend: this.getMessage("syncEditorLegend"),
						fieldListTitle: this.getMessage("syncEditorFieldListTitle"),
						entityListTitle: this.getMessage("syncEditorEntityListTitle"),
						continueButton: this.getMessage("continueButton"),
						cancelButton: this.getMessage("cancelButton")
					}
				);
				this._syncEditor.addClosingListener(this._syncEditorClosingListener);
			}
			this._syncEditor.show();
		},
		convert: function(entityId, config, originUrl, contextData)
		{
			if(!BX.type.isPlainObject(config))
			{
				return;
			}

			this._entityId = entityId;
			this._config = config;
			this._contextData = BX.type.isPlainObject(contextData) ? contextData : null;
			this._originUrl = originUrl;
			this.startRequest();
		},
		onSyncEditorClose: function(sender, args)
		{
			this._mode = BX.OrderEntityConverterMode.intermediate;

			if(!(BX.type.isBoolean(args["isCanceled"]) && args["isCanceled"] === false))
			{
				return;
			}

			this._enableSync = true;
			this._config = this._syncEditor.getConfig();
			this._contextData = null;
			this.startRequest();
		},
		startRequest: function()
		{
			if(this._requestIsRunning)
			{
				return;
			}
			this._requestIsRunning = true;
			BX.showWait();

			BX.ajax(
				{
					url: this._serviceUrl,
					method: "POST",
					dataType: "json",
					data: {
						"MODE": "CONVERT",
						"ENTITY_ID": this._entityId,
						"ENABLE_SYNCHRONIZATION": this._enableSync ? "Y" : "N",
						"CONFIG": this._config,
						"CONTEXT": this._contextData,
						"ORIGIN_URL": this._originUrl
					},
					onsuccess: BX.delegate(this.onRequestSuccsess, this),
					onfailure: BX.delegate(this.onRequestFailure, this)
				}
			);
			this._mode = BX.OrderEntityConverterMode.request;
		},
		onRequestSuccsess: function(result)
		{
			BX.closeWait();
			this._requestIsRunning = false;
			this._mode = BX.OrderEntityConverterMode.intermediate;

			if(BX.type.isPlainObject(result["ERROR"]))
			{
				this.showError(result["ERROR"]);
				return;
			}

			var data;
			if(BX.type.isPlainObject(result["REQUIRED_ACTION"]))
			{
				var action = result["REQUIRED_ACTION"];
				var name = BX.type.isNotEmptyString(action["NAME"]) ? action["NAME"] : "";
				data = BX.type.isPlainObject(action["DATA"]) ? action["DATA"] : {};
				if(name === "SYNCHRONIZE")
				{
					if(BX.type.isPlainObject(data["CONFIG"]))
					{
						this._config = data["CONFIG"];
					}

					this.setupSynchronization(BX.type.isArray(data["FIELD_NAMES"]) ? data["FIELD_NAMES"] : []);
				}
				return;
			}

			if(BX.type.isPlainObject(result["DATA"]))
			{
				data = result["DATA"];
				if(BX.type.isNotEmptyString(data["URL"]))
				{
					window.location.href = data["URL"];
				}
				else
				{
					window.location.reload();
				}
			}
		},
		onRequestFailure: function(result)
		{
			BX.closeWait();
			this._requestIsRunning = false;
			this._mode = BX.OrderEntityConverterMode.intermediate;
		},
		showError: function(error)
		{
			if(BX.type.isPlainObject(error))
			{
				alert(BX.type.isNotEmptyString(error["MESSAGE"]) ? error["MESSAGE"] : this.getMessage("generalError"));
			}
		}
	};
	BX.OrderEntityConverter.create = function(id, settings)
	{
		var self = new BX.OrderEntityConverter();
		self.initialize(id, settings);
		return self;
	};
}

if(typeof(BX.OrderLeadConverter) === "undefined")
{
	BX.OrderLeadConverter = function()
	{
		BX.OrderLeadConverter.superclass.constructor.apply(this);
		this._entitySelectorId = "lead_converter";
		this._entitySelectHandler = BX.delegate(this.onEntitySelect, this);
		this._entitySelectCallback = null;
	};
	BX.extend(BX.OrderLeadConverter, BX.OrderEntityConverter);
	BX.OrderLeadConverter.prototype.getMessage = function(name)
	{
		var m = BX.OrderLeadConverter.messages;
		return m.hasOwnProperty(name) ? m[name] : name;
	};
	if(typeof(BX.OrderLeadConverter.messages) === "undefined")
	{
		BX.OrderLeadConverter.messages = {};
	}
	BX.OrderLeadConverter.prototype.openEntitySelector = function(callback)
	{
		this._entitySelectCallback = BX.type.isFunction(callback) ? callback : null;

		var selectorId = this._entitySelectorId;
		if(typeof(obOrder[selectorId]) === "undefined")
		{
			obOrder[selectorId] = new ORDER(
				selectorId,
				null,
				null,
				selectorId,
				[],
				false,
				true,
				[ "contact", "company" ],
				{
					"contact": this.getMessage("contact"),
					"company": this.getMessage("company"),
					"ok": this.getMessage("selectButton"),
					"cancel": BX.message("JS_CORE_WINDOW_CANCEL"),
					"close": BX.message("JS_CORE_WINDOW_CLOSE"),
					"wait": BX.message("JS_CORE_LOADING"),
					"noresult": this.getMessage("noresult"),
					"search" : this.getMessage("search"),
					"last" : this.getMessage("last")
				},
				true
			);
			obOrder[selectorId].Init();
			obOrder[selectorId].AddOnSaveListener(this._entitySelectHandler);
		}

		obOrder[selectorId].Open(
			{
				closeIcon: { top: "10px", right: "15px" },
				closeByEsc: true,
				titleBar:
				{
					content:
						BX.create(
							'DIV',
							{
								attrs: { className: "bx-order-dialog-tittle-wrap" },
								children:
									[
										BX.create(
											'SPAN',
											{
												text: this.getMessage("entitySelectorTitle"),
												props: { className: "bx-order-dialog-title-text" }
											}
										)
									]
							}
						)
				}
			}
		);
	};
	BX.OrderLeadConverter.prototype.onEntitySelect = function(settings)
	{
		var selectorId = this._entitySelectorId;
		obOrder[selectorId].RemoveOnSaveListener(this._entitySelectHandler);
		obOrder[selectorId].Clear();
		delete obOrder[selectorId];

		if(!this._entitySelectCallback)
		{
			return;
		}

		var type;
		var data = null;
		for(type in settings)
		{
			if(settings.hasOwnProperty(type)
				&& BX.type.isPlainObject(settings[type])
				&& BX.type.isPlainObject(settings[type][0]))
			{
				var setting = settings[type][0];
				var entityId = typeof(setting["id"]) ? parseInt(setting["id"]) : 0;
				if(entityId > 0)
				{
					if(data === null)
					{
						data = {};
					}
					data[type] = entityId;
				}
			}
		}

		if(data === null)
		{
			this._entitySelectCallback({ config: null, data: null });
		}
		else
		{
			var config = { deal: { active: "N" }, contact: { active: "N" }, company: { active: "N" } };
			for(type in data)
			{
				if(data.hasOwnProperty(type) && typeof(config[type]) !== "undefined")
				{
					config[type]["active"] = "Y";
				}
			}
			this._entitySelectCallback({ config: config, data: data });
		}
	};
	BX.OrderLeadConverter.create = function(id, settings)
	{
		var self = new BX.OrderLeadConverter();
		self.initialize(id, settings);
		return self;
	};
	BX.OrderLeadConverter.current = null;
	if(typeof(BX.OrderLeadConverter.settings === "undefined"))
	{
		BX.OrderLeadConverter.settings = {};
	}
	if(typeof(BX.OrderLeadConverter.permissions === "undefined"))
	{
		BX.OrderLeadConverter.permissions = { contact: false, company: false, deal: false };
	}
	BX.OrderLeadConverter.getCurrent = function()
	{
		if(!this.current)
		{
			this.current = BX.OrderLeadConverter.create("current", this.settings);
		}
		return this.current;
	};
}

if(typeof(BX.OrderDealConverter) === "undefined")
{
	BX.OrderDealConverter = function()
	{
		BX.OrderDealConverter.superclass.constructor.apply(this);
	};
	BX.extend(BX.OrderDealConverter, BX.OrderEntityConverter);
	BX.OrderDealConverter.prototype.getMessage = function(name)
	{
		var m = BX.OrderDealConverter.messages;
		return m.hasOwnProperty(name) ? m[name] : name;
	};
	if(typeof(BX.OrderDealConverter.messages) === "undefined")
	{
		BX.OrderDealConverter.messages = {};
	}
	BX.OrderDealConverter.create = function(id, settings)
	{
		var self = new BX.OrderDealConverter();
		self.initialize(id, settings);
		return self;
	};
	BX.OrderDealConverter.current = null;
	if(typeof(BX.OrderDealConverter.settings === "undefined"))
	{
		BX.OrderDealConverter.settings = {};
	}
	if(typeof(BX.OrderDealConverter.permissions === "undefined"))
	{
		BX.OrderDealConverter.permissions = { invoice: false, quote: false };
	}
	BX.OrderDealConverter.getCurrent = function()
	{
		if(!this.current)
		{
			this.current = BX.OrderDealConverter.create("current", this.settings);
		}
		return this.current;
	};
}

if(typeof(BX.OrderQuoteConverter) === "undefined")
{
	BX.OrderQuoteConverter = function()
	{
		BX.OrderQuoteConverter.superclass.constructor.apply(this);
	};
	BX.extend(BX.OrderQuoteConverter, BX.OrderEntityConverter);
	BX.OrderQuoteConverter.prototype.getMessage = function(name)
	{
		var m = BX.OrderQuoteConverter.messages;
		return m.hasOwnProperty(name) ? m[name] : name;
	};
	if(typeof(BX.OrderQuoteConverter.messages) === "undefined")
	{
		BX.OrderQuoteConverter.messages = {};
	}
	BX.OrderQuoteConverter.create = function(id, settings)
	{
		var self = new BX.OrderQuoteConverter();
		self.initialize(id, settings);
		return self;
	};
	BX.OrderQuoteConverter.current = null;
	if(typeof(BX.OrderQuoteConverter.settings === "undefined"))
	{
		BX.OrderQuoteConverter.settings = {};
	}
	if(typeof(BX.OrderQuoteConverter.permissions === "undefined"))
	{
		BX.OrderQuoteConverter.permissions = { invoice: false, quote: false };
	}
	BX.OrderQuoteConverter.getCurrent = function()
	{
		if(!this.current)
		{
			this.current = BX.OrderQuoteConverter.create("current", this.settings);
		}
		return this.current;
	};
}

if(typeof(BX.OrderEntityFieldSynchronizationEditor) === "undefined")
{
	BX.OrderEntityFieldSynchronizationEditor = function()
	{
		this._id = "";
		this._settings = {};
		this._converter = null;
		this._config = {};
		this._fieldNames = [];
		this._closingNotifier = null;
		this._contentWrapper = null;
		this._fieldWrapper = null;
		this._foldButton = null;
		this._foldButtonClickHandler = BX.delegate(this.onFoldButtonClick, this);
		this._checkBoxes = {};
		this._resizer = null;
		this._popup = null;
	};
	BX.OrderEntityFieldSynchronizationEditor.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};
			this._converter = this.getSetting("converter");

			this._config = this.getSetting("config", {});
			this._fieldNames = this.getSetting("fieldNames", []);
			this._closingNotifier = BX.OrderNotifier.create(this);
		},
		getSetting: function (name, defaultval)
		{
			return typeof(this._settings[name]) != 'undefined' ? this._settings[name] : defaultval;
		},
		setSetting: function (name, val)
		{
			this._settings[name] = val;
		},
		getId: function()
		{
			return this._id;
		},
		getConfig: function()
		{
			return this._config;
		},
		setConfig: function(config)
		{
			this._config = config;
		},
		getFieldNames: function()
		{
			return this._fieldNames;
		},
		setFieldNames: function(fieldNames)
		{
			this._fieldNames = fieldNames;
		},
		show: function()
		{
			if(this.isShown())
			{
				return;
			}

			var id = this.getId();
			if(BX.OrderEntityFieldSynchronizationEditor.windows[id])
			{
				BX.OrderEntityFieldSynchronizationEditor.windows[id].destroy();
				delete BX.OrderEntityFieldSynchronizationEditor.windows[id];
			}

			var anchor = this.getSetting("anchor", null);
			this._popup = new BX.PopupWindow(
				id,
				anchor,
				{
					autoHide: false,
					draggable: true,
					zIndex: 100,
					bindOptions: { forceBindPosition: false },
					closeByEsc: true,
					closeIcon :
					{
						marginRight:"-2px",
						marginTop:"3px"
					},
					events:
					{
						onPopupShow: BX.delegate(this.onPopupShow, this),
						onPopupClose: BX.delegate(this.onPopupClose, this),
						onPopupDestroy: BX.delegate(this.onPopupDestroy, this)
					},
					titleBar: { content: this.prepareTitle(this.getSetting("title")) },
					content: this.prepareContent(),
					buttons: this.prepareButtons(),
					lightShadow : true,
					className : "order-tip-popup"
				}
			);

			BX.OrderEntityFieldSynchronizationEditor.windows[id] = this._popup;
			this._popup.show();
		},
		close: function()
		{
			if(!(this._popup && this._popup.isShown()))
			{
				return;
			}

			this._popup.close();
		},
		isShown: function()
		{
			return this._popup && this._popup.isShown();
		},
		addClosingListener: function(listener)
		{
			this._closingNotifier.addListener(listener);
		},
		removeClosingListener: function(listener)
		{
			this._closingNotifier.removeListener(listener);
		},
		getMessage: function(name)
		{
			var m = BX.OrderEntityFieldSynchronizationEditor.messages;
			return m.hasOwnProperty(name) ? m.messages[name] : name;
		},
		prepareTitle: function(text)
		{
			return (
				BX.create(
					'DIV',
					{
						attrs: { className: 'bx-order-dialog-tittle-wrap' },
						children: [ BX.create('SPAN', { text: text, props: { className: 'bx-order-dialog-title-text' } }) ]
					}
				)
			);
		},
		prepareButtons: function()
		{
			return(
				[
					new BX.PopupWindowButton(
						{
							text: this.getSetting("continueButton"),
							className: "popup-window-button-accept",
							events: { click: BX.delegate(this.onContinueBtnClick, this) }
						}
					),
					new BX.PopupWindowButtonLink(
						{
							text: this.getSetting("cancelButton"),
							className: "popup-window-button-link-cancel",
							events: { click: BX.delegate(this.onCancelBtnClick, this) }
						}
					)
				]
			);
		},
		prepareContent: function()
		{
			this._contentWrapper = BX.create("DIV", { attrs: { className: "order-popup-setting-fields" } });

			var fieldList = BX.create("UL", { attrs: { className: "order-p-s-f-items-list" } });
			for(var i = 0; i < this._fieldNames.length; i++)
			{
				fieldList.appendChild(
					BX.create("LI", { attrs: { className: "order-p-s-f-item" }, text: this._fieldNames[i] })
				);
			}

			var fieldWrapper = this._fieldWrapper = BX.create("DIV", { attrs: { className: "order-p-s-f-block-wrap order-p-s-f-block-hide" } });
			this._contentWrapper.appendChild(fieldWrapper);

			var fieldContainer = BX.create("DIV",
				{
					attrs: { className: "order-p-s-f-top-block" },
					children:
						[
							BX.create("DIV",
								{
									attrs: { className: "order-p-s-f-title" },
									text: this.getSetting("fieldListTitle") + ":"
								}
							),
							fieldList
						]
				}
			);

			var foldButton = this._foldButton = BX.create("DIV", { attrs: { className: "order-p-s-f-open-btn" } });
			if(fieldList.children.length > 6)
			{
				BX.bind(foldButton, "click", this._foldButtonClickHandler);
			}
			else
			{
				fieldWrapper.classList.toggle('order-p-s-f-block-open');
			}

			var innerFieldWrapper = BX.create("DIV",
				{
					attrs: { className: "order-p-s-f-block-hide-inner" },
					children:
						[
							BX.create("DIV", { attrs: { className: "order-p-s-f-text" }, text: this.getSetting("legend") }),
							fieldContainer,
							foldButton
						]
				}
			);

			fieldWrapper.appendChild(innerFieldWrapper);
			this._resizer = BX.AnimatedResize.create(innerFieldWrapper, fieldWrapper);

			var entityWrapper = BX.create("DIV", { attrs: { className: "order-p-s-f-block-wrap" } });
			this._contentWrapper.appendChild(entityWrapper);
			entityWrapper.appendChild(
				BX.create("DIV",
					{
						attrs: { className: "order-p-s-f-title" },
						text: this.getSetting("entityListTitle") + ":"
					}
				)
			);

			var id = this.getId();
			this._checkBoxes = {};
			var entityList = BX.create("UL", { attrs: { className: "order-p-s-f-checkbox-items-list" } });
			for(var entityTypeName in this._config)
			{
				if(!this._config.hasOwnProperty(entityTypeName))
				{
					continue;
				}

				var entityConfig = this._config[entityTypeName];
				var enableSync = BX.type.isNotEmptyString(entityConfig["enableSync"]) && entityConfig["enableSync"] === "Y";
				if(!enableSync)
				{
					continue;
				}

				var inputId = id + "_" + entityTypeName;
				var checkbox = BX.create("INPUT", { props: { id: inputId, type: "checkbox", checked: true } });
				this._checkBoxes[entityTypeName] = checkbox;

				var label = BX.create("LABEL",
					{
						props: { htmlFor: inputId },
						text: BX.OrderEntityType.getCaptionByName(entityTypeName)
					}
				);

				entityList.appendChild(
					BX.create("LI",
						{ attrs: { className: "order-p-s-f-checkbox-item" }, children: [ checkbox, label ] }
					)
				);
			}
			entityWrapper.appendChild(entityList);
			return this._contentWrapper;
		},
		saveConfig: function()
		{
			for(var entityTypeName in this._checkBoxes)
			{
				if(this._checkBoxes.hasOwnProperty(entityTypeName) && this._config.hasOwnProperty(entityTypeName))
				{
					this._config[entityTypeName]["enableSync"] = this._checkBoxes[entityTypeName].checked ? "Y" : "N";
				}
			}
		},
		onFoldButtonClick: function()
		{
			this._fieldWrapper.classList.toggle("order-p-s-f-block-open");
			this._resizer.run();
		},
		onContinueBtnClick: function()
		{
			this.saveConfig();
			this._closingNotifier.notify([{ isCanceled: false }]);
			this.close();
		},
		onCancelBtnClick: function()
		{
			this._closingNotifier.notify([{ isCanceled: true }]);
			this.close();
		},
		onPopupShow: function()
		{
		},
		onPopupClose: function()
		{
			if(this._popup)
			{
				this._contentWrapper = null;
				this._popup.destroy();
			}
		},
		onPopupDestroy: function()
		{
			if(!this._popup)
			{
				return;
			}

			this._fieldWrapper = null;
			this._foldButton = null;
			this._contentWrapper = null;
			this._checkBoxes = {};
			this._resizer = null;
			this._popup = null;
			delete BX.OrderEntityFieldSynchronizationEditor.windows[this.getId()];
		}
	};
	BX.OrderEntityFieldSynchronizationEditor.windows = {};
	if(typeof(BX.OrderEntityFieldSynchronizationEditor.messages) == "undefined")
	{
		BX.OrderEntityFieldSynchronizationEditor.messages = {};
	}
	BX.OrderEntityFieldSynchronizationEditor.create = function(id, settings)
	{
		var self = new BX.OrderEntityFieldSynchronizationEditor();
		self.initialize(id, settings);
		return self;
	};
}

if(typeof(BX.AnimatedResize) === "undefined")
{
	BX.AnimatedResize = function()
	{
		this._innerBlock = null;
		this._mainBlock = null;
		this._isOpen = false;
	};

	BX.AnimatedResize.prototype =
	{
		initialize: function(innerBlock, mainBlock)
		{
			this._innerBlock = innerBlock;
			this._mainBlock = mainBlock;
		},
		run: function()
		{
			this._isOpen = this._mainBlock.offsetHeight == this._innerBlock.offsetHeight;
			this.ease(this._isOpen
				? { start : this._innerBlock.offsetHeight, finish : 0 }
				: { start: this._mainBlock.offsetHeight, finish: this._innerBlock.offsetHeight }
			);
			this._isOpen = !this._isOpen;
		},
		step: function(state)
		{
			this._mainBlock.style.height = state.height + "px";
		},
		complete: function()
		{
			if(this._isOpen)
			{
				this._mainBlock.style.height = "auto";
			}
		},
		ease: function (params)
		{
			(new BX.easing(
				{
					duration : 300,
					start : { height : params["start"] },
					finish : { height : params["finish"] },
					transition : BX.easing.makeEaseOut(BX.easing.transitions.circ),
					step : BX.delegate(this.step, this),
					complete :BX.delegate(this.complete, this)
				}
			)).animate();
		}
	};
	BX.AnimatedResize.create = function(innerBlock, mainBlock)
	{
		var self = new BX.AnimatedResize();
		self.initialize(innerBlock, mainBlock);
		return self;
	}
}

if(typeof(BX.OrderLeadConversionSchemeSelector) === "undefined")
{
	BX.OrderLeadConversionSchemeSelector = function()
	{
		BX.OrderLeadConversionSchemeSelector.superclass.constructor.apply(this);
	};
	BX.extend(BX.OrderLeadConversionSchemeSelector, BX.OrderConversionSchemeSelector);
	BX.OrderLeadConversionSchemeSelector.prototype.prepareItems = function()
	{
		var isDealPermitted = BX.OrderLeadConverter.permissions["deal"];
		var isContactPermitted = BX.OrderLeadConverter.permissions["contact"];
		var isCompanyPermitted = BX.OrderLeadConverter.permissions["company"];

		var schemes = [];
		if(isDealPermitted)
		{
			if(isContactPermitted && isCompanyPermitted)
			{
				schemes.push(BX.OrderLeadConversionScheme.dealcontactcompany);
			}
			if(isContactPermitted)
			{
				schemes.push(BX.OrderLeadConversionScheme.dealcontact);
			}
			if(isCompanyPermitted)
			{
				schemes.push(BX.OrderLeadConversionScheme.dealcompany);
			}

			schemes.push(BX.OrderLeadConversionScheme.deal);
		}
		if(isContactPermitted && isCompanyPermitted)
		{
			schemes.push(BX.OrderLeadConversionScheme.contactcompany);
		}
		if(isContactPermitted)
		{
			schemes.push(BX.OrderLeadConversionScheme.contact);
		}
		if(isCompanyPermitted)
		{
			schemes.push(BX.OrderLeadConversionScheme.company);
		}

		var items = BX.OrderLeadConversionScheme.getListItems(schemes);
		items.push({ value: "CUSTOM", text: BX.OrderLeadConverter.getCurrent().getMessage("openEntitySelector") });
		return items;
	};
	BX.OrderLeadConversionSchemeSelector.prototype.prepageConfig = function()
	{
		return BX.OrderLeadConversionScheme.createConfig(this._scheme);
	};
	BX.OrderLeadConversionSchemeSelector.prototype.getSchemeDescription = function(scheme)
	{
		return BX.OrderLeadConversionScheme.getDescription(scheme);
	};
	BX.OrderLeadConversionSchemeSelector.prototype.processMenuItemClick = function(item)
	{
		var value = item["value"];
		if(value === "CUSTOM")
		{
			BX.OrderLeadConverter.getCurrent().openEntitySelector(BX.delegate(this.onEntitySelect, this));
		}
		else
		{
			this.setScheme(value);
		}
		this.closeMenu();
	};
	BX.OrderLeadConversionSchemeSelector.prototype.onEntitySelect = function(result)
	{
		if(!BX.type.isPlainObject(result))
		{
			return;
		}

		BX.OrderLeadConverter.getCurrent().convert(
			this._entityId,
			result["config"],
			this.getSetting("originUrl"),
			result["data"]
		);
	};
	BX.OrderLeadConversionSchemeSelector.prototype.convert = function()
	{
		BX.OrderLeadConverter.getCurrent().convert(
			this._entityId,
			this.prepageConfig(),
			this.getSetting("originUrl")
		);
	};
	BX.OrderLeadConversionSchemeSelector.create = function(id, settings)
	{
		var self = new BX.OrderLeadConversionSchemeSelector();
		self.initialize(id, settings);
		return self;
	};
}

if(typeof(BX.OrderDealConversionSchemeSelector) === "undefined")
{
	BX.OrderDealConversionSchemeSelector = function()
	{
		BX.OrderDealConversionSchemeSelector.superclass.constructor.apply(this);
	};
	BX.extend(BX.OrderDealConversionSchemeSelector, BX.OrderConversionSchemeSelector);
	BX.OrderDealConversionSchemeSelector.prototype.prepareItems = function()
	{
		var schemes = [];
		if(BX.OrderDealConverter.permissions["invoice"])
		{
			schemes.push(BX.OrderDealConversionScheme.invoice);
		}
		if(BX.OrderDealConverter.permissions["quote"])
		{
			schemes.push(BX.OrderDealConversionScheme.quote);
		}
		return BX.OrderDealConversionScheme.getListItems(schemes);
	};
	BX.OrderDealConversionSchemeSelector.prototype.prepageConfig = function()
	{
		return BX.OrderDealConversionScheme.createConfig(this._scheme);
	};
	BX.OrderDealConversionSchemeSelector.prototype.getSchemeDescription = function(scheme)
	{
		return BX.OrderDealConversionScheme.getDescription(scheme);
	};
	BX.OrderDealConversionSchemeSelector.prototype.convert = function()
	{
		BX.OrderDealConverter.getCurrent().convert(
			this._entityId,
			this.prepageConfig(),
			this.getSetting("originUrl", "")
		);
	};
	BX.OrderDealConversionSchemeSelector.create = function(id, settings)
	{
		var self = new BX.OrderDealConversionSchemeSelector();
		self.initialize(id, settings);
		return self;
	};
}

if(typeof(BX.OrderQuoteConversionSchemeSelector) === "undefined")
{
	BX.OrderQuoteConversionSchemeSelector = function()
	{
		BX.OrderQuoteConversionSchemeSelector.superclass.constructor.apply(this);
	};
	BX.extend(BX.OrderQuoteConversionSchemeSelector, BX.OrderConversionSchemeSelector);
	BX.OrderQuoteConversionSchemeSelector.prototype.prepareItems = function()
	{
		var schemes = [];
		if(BX.OrderQuoteConverter.permissions["deal"])
		{
			schemes.push(BX.OrderQuoteConversionScheme.deal);
		}
		if(BX.OrderQuoteConverter.permissions["invoice"])
		{
			schemes.push(BX.OrderQuoteConversionScheme.invoice);
		}
		return BX.OrderQuoteConversionScheme.getListItems(schemes);
	};
	BX.OrderQuoteConversionSchemeSelector.prototype.prepageConfig = function()
	{
		return BX.OrderQuoteConversionScheme.createConfig(this._scheme);
	};
	BX.OrderQuoteConversionSchemeSelector.prototype.getSchemeDescription = function(scheme)
	{
		return BX.OrderQuoteConversionScheme.getDescription(scheme);
	};
	BX.OrderQuoteConversionSchemeSelector.prototype.convert = function()
	{
		BX.OrderQuoteConverter.getCurrent().convert(
			this._entityId,
			this.prepageConfig(),
			this.getSetting("originUrl", "")
		);
	};
	BX.OrderQuoteConversionSchemeSelector.items = {};
	BX.OrderQuoteConversionSchemeSelector.create = function(id, settings)
	{
		var self = new BX.OrderQuoteConversionSchemeSelector();
		self.initialize(id, settings);
		this.items[self.getId()] = self;
		return self;
	};
}

if(typeof(BX.OrderDataLoader) == "undefined")
{
	BX.OrderDataLoader = function()
	{
		this._id = "";
		this._settings = {};
		this._params = {};
		this._serviceUrl = "";
		this._requestIsRunning = false;
		this._button = null;
		this._wrapper = null;
		this._buttonClickHandler = BX.delegate(this.onButtonClick, this);
	};
	BX.OrderDataLoader.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};

			this._serviceUrl = this.getSetting("serviceUrl");
			if(!BX.type.isNotEmptyString(this._serviceUrl))
			{
				throw "BX.OrderDataLoader: service url not found!";
			}

			this._action = this.getSetting("action");
			if(!BX.type.isNotEmptyString(this._action))
			{
				throw "BX.OrderDataLoader: action not found!";
			}

			this._params = this.getSetting("params", {});

			this._button = BX(this.getSetting("button"));
			if(!BX.type.isElementNode(this._button))
			{
				throw "BX.OrderDataLoader: button element not found!";
			}
			BX.bind(this._button, "click", this._buttonClickHandler);

			this._wrapper = BX(this.getSetting("wrapper"));
			if(!BX.type.isElementNode(this._wrapper))
			{
				throw "BX.OrderDataLoader: wrapper element not found!";
			}

		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		onButtonClick: function(e)
		{
			this.startRequest();
			return BX.PreventDefault(e);
		},
		startRequest: function()
		{
			if(this._requestIsRunning)
			{
				return;
			}
			this._requestIsRunning = true;
			BX.showWait();

			BX.ajax(
				{
					url: this._serviceUrl,
					method: "POST",
					dataType: "json",
					data:
					{
						"ACTION": this._action,
						"PARAMS": this._params
					},
					onsuccess: BX.delegate(this.onRequestSuccsess, this),
					onfailure: BX.delegate(this.onRequestFailure, this)
				}
			);
		},
		onRequestSuccsess: function(result)
		{
			BX.closeWait();
			this._requestIsRunning = false;

			if(BX.type.isPlainObject(result["ERROR"]))
			{
				this.showError(result["ERROR"]);
				return;
			}

			if(BX.type.isPlainObject(result["DATA"]))
			{
				var data = result["DATA"];
				if(BX.type.isNotEmptyString(data["HTML"]))
				{
					this._wrapper.innerHTML = data["HTML"];
				}
				else if(BX.type.isNotEmptyString(data["TEXT"]))
				{
					this._wrapper.innerHTML = BX.util.htmlspecialchars(data["TEXT"]);
				}
			}
		},
		onRequestFailure: function(result)
		{
			BX.closeWait();
			this._requestIsRunning = false;
		},
		showError: function(error)
		{
			if(BX.type.isPlainObject(error) && BX.type.isNotEmptyString(error["MESSAGE"]))
			{
				alert(error["MESSAGE"]);
			}
		}
	};
	BX.OrderDataLoader.create = function(id, settings)
	{
		var self = new BX.OrderDataLoader();
		self.initialize(id, settings);
		return self;
	}
}

if(typeof(BX.OrderRemoteAction))
{
	BX.OrderRemoteAction = function()
	{
		this._id = "";
		this._settings = {};
		this._serviceUrl = "";
		this._redirectUrl = "";
	};
	BX.OrderRemoteAction.prototype =
	{
		initialize: function(id, settings)
		{
			this._id = id;
			this._settings = settings ? settings : {};

			this._serviceUrl = this.getSetting("serviceUrl", "");
			if(!BX.type.isNotEmptyString(this._serviceUrl))
			{
				throw "BX.OrderDataLoader: service url not found!";
			}

			this._redirectUrl = this.getSetting("redirectUrl", "");
		},
		getId: function()
		{
			return this._id;
		},
		getSetting: function (name, defaultval)
		{
			return this._settings.hasOwnProperty(name) ? this._settings[name] : defaultval;
		},
		execute: function(redirectUrl)
		{
			if(BX.type.isNotEmptyString(redirectUrl))
			{
				this._redirectUrl = redirectUrl;
			}

			BX.ajax(
				{
					method: "POST",
					dataType: "html",
					url: this._serviceUrl,
					data: this.getSetting("data", {}),
					onsuccess: BX.delegate(this.onActionSuccess, this)
				}
			);
		},
		onActionSuccess: function(data)
		{
			if(BX.type.isNotEmptyString(this._redirectUrl))
			{
				document.location.href = this._redirectUrl;
			}
		}
	};
	BX.OrderRemoteAction.items = {};
	BX.OrderRemoteAction.create = function(id, settings)
	{
		var self = new BX.OrderRemoteAction();
		self.initialize(id, settings);
		this.items[self.getId()] = self;
		return self;
	}
}