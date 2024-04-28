var clientPC = navigator.userAgent.toLowerCase();var clientVer = parseInt(navigator.appVersion);var is_ie = ((clientPC.indexOf("msie") != -1) && (clientPC.indexOf("opera") == -1));var is_nav = ((clientPC.indexOf('mozilla')!=-1) && (clientPC.indexOf('spoofer')==-1) && (clientPC.indexOf('compatible') == -1) && (clientPC.indexOf('opera')==-1) && (clientPC.indexOf('webtv')==-1) && (clientPC.indexOf('hotjava')==-1));var is_win = ((clientPC.indexOf("win")!=-1) || (clientPC.indexOf("16bit") != -1));var is_mac = (clientPC.indexOf("mac")!=-1);var is_moz = 0;function el(id){if (document.getElementById)return document.getElementById(id);else if (window[id])return window[id];return null;}function selectycopy(field){field.focus();field.select();}
var boardurl = 'http://localhost/tripiante';

// Actualizar comentarios
function actualizar_comentarios() {
  $('#ult_comm').slideUp(1);
  $.ajax({
    type: 'POST',
    url: boardurl + '/web/tp-ActComentarios.php',
    success: function(h) {
      $('#ult_comm').html(h);
      $('#ult_comm').slideDown({duration: 1000, easing: 'easeOutBounce'});
    }
  });
}

function error_avatar(obj) {
  // TO-DO: Cambiar según donde se encuentre el script
  obj.src = boardurl + '/avatar.gif';
}

// Citar comentarios
function citar_comment(id) {
  var user = el('autor_cmnt_' + id).getAttribute('user_comment');
  var cita = el('autor_cmnt_' + id).getAttribute('text_comment');
  var text = ($('#cuerpo_comment').val() != '') ? $('#cuerpo_comment').val() + '\n' : '';

  text += '[quote=' + user + ']' + cita + '[/quote]\n';

  $('#cuerpo_comment').val(text);
  $('#cuerpo_comment').focus();
}

// Categorias
function ir_a_categoria() {
  if ($('#categoria').val() != 'root') {
    document.location.href = boardurl + '/categoria/' + $('#categoria').val() + '/';
  }
}

function ir_a_categoria_com() {
  if ($('#categoria').val()!='root') {
    document.location.href = boardurl + '/comunidades/categoria/' + $('#categoria').val() + '/';
  }
}

/* Editor */
// Botones posts
mySettings = {
  markupSet: [
    { name:'Negrita', key:'B', openWith:'[b]', closeWith:'[/b]' },
    { name:'Cursiva', key:'I', openWith:'[i]', closeWith:'[/i]' },
    { name:'Subrayado', key:'U', openWith:'[u]', closeWith:'[/u]' },
    { separator: '-' },
    {name:'Alinear a la izquierda', key:'', openWith:'[left]', closeWith:'[left]'},
    {name:'Centrar', key:'', openWith:'[center]', closeWith:'[/center]'},
    {name:'Alinear a la derecha', key:'', openWith:'[right]', closeWith:'[/right]'},
      {separator:'-' },
    {name:'Color', dropMenu: [
      {name:'Rojo oscuro', openWith:'[color=darkred]', closeWith:'[/color]' },
      {name:'Rojo', openWith:'[color=red]', closeWith:'[/color]' },
      {name:'Naranja', openWith:'[color=orange]', closeWith:'[/color]' },
      {name:'Marr&oacute;n', openWith:'[color=brown]', closeWith:'[/color]' },
      {name:'Amarillo', openWith:'[color=yellow]', closeWith:'[/color]' },
      {name:'Verde', openWith:'[color=green]', closeWith:'[/color]' },
      {name:'Oliva', openWith:'[color=olive]', closeWith:'[/color]' },
      {name:'Cyan', openWith:'[color=cyan]', closeWith:'[/color]' },
      {name:'Azul', openWith:'[color=blue]', closeWith:'[/color]' },
      {name:'Azul oscuro', openWith:'[color=darkblue]', closeWith:'[/color]' },
      {name:'Indigo', openWith:'[color=indigo]', closeWith:'[/color]' },
      {name:'Violeta', openWith:'[color=violet]', closeWith:'[/color]' },
      {name:'Gris', openWith:'[color=lightgrey]', closeWith:'[/color]' },
      {name:'Verde Amarillento', openWith:'[color=yellowgreen]', closeWith:'[/color]' },
      {name:'Negro', openWith:'[color=black]', closeWith:'[/color]' }
      
    ]},
    {name:'Tama&ntilde;o', dropMenu :[
        {name:'Mi&ntilde;atura', openWith:'[size=7px]', closeWith:'[/size]' },
      {name:'Peque&ntilde;a', openWith:'[size=9px]', closeWith:'[/size]' },
      {name:'Normal', openWith:'[size=12px]', closeWith:'[/size]' },
      {name:'Grande', openWith:'[size=18px]', closeWith:'[/size]' },
      {name:'Enorme', openWith:'[size=24px]', closeWith:'[/size]' }
    ]},
    {name:'Fuente', dropMenu :[
      {name:'Arial', openWith:'[font=Arial]', closeWith:'[/font]' },
      {name:'Courier New', openWith:'[font=Courier New]', closeWith:'[/font]' },
      {name:'Georgia', openWith:'[font=Georgia]', closeWith:'[/font]' },
      {name:'Times New Roman', openWith:'[font=Times New Roman]', closeWith:'[/font]' },
      {name:'Verdana', openWith:'[font=Verdana]', closeWith:'[/font]' },
      {name:'Trebuchet MS', openWith:'[font=Trebuchet MS]', closeWith:'[/font]' },
      {name:'Lucida Sans', openWith:'[font=Lucida Sans]', closeWith:'[/font]' },
      {name:'Comic Sans', openWith:'[font=Comic Sans]', closeWith:'[/font]' }
    ]},
    {separator:'-' },
    {name:'Insertar video de YouTube', beforeInsert:function(h){ markit_yt(h); }},
    {name:'Insertar video de Google Video', beforeInsert:function(h){ markit_gv(h); }},
    {name:'Insertar archivo SWF', beforeInsert:function(h){ markit_swf(h); }},
    {name:'Insertar Imagen', beforeInsert:function(h){ markit_img(h); }},
    {name:'Insertar Link', beforeInsert:function(h){ markit_url(h); }},
    {separator:'-' },
    {name:'Citar', key:'', openWith:'[quote]', closeWith:'[/quote]'},
    {name:'Code', key:'', openWith:'[code]', closeWith:'[/code]'},
  ]
};

// Botones comentarios
mySettings_cmt = {
  nameSpace: "markitcomment",
  markupSet: [
    {name:'Negrita', key:'B', openWith:'[b]', closeWith:'[/b]'},
    {name:'Cursiva', key:'I', openWith:'[i]', closeWith:'[/i]'},
    {name:'Subrayado', key:'U', openWith:'[u]', closeWith:'[/u]'},
    {separator:'-' },
    {name:'Insertar video de YouTube', beforeInsert:function(h){ markit_yt(h); }},
    {name:'Insertar video de Google Video', beforeInsert:function(h){ markit_gv(h); }},
    {name:'Insertar archivo SWF', beforeInsert:function(h){ markit_swf(h); }},
    {name:'Insertar Imagen', beforeInsert:function(h){ markit_img(h); }},
    {name:'Insertar Link', beforeInsert:function(h){ markit_url(h); }},
    {name:'Citar', key:'C', openWith:'[quote]', closeWith:'[/quote]'}
  ]
};

// Funciones botones especiales
function markit_yt(h) {
  if (is_ie) {
    var msg = prompt('', '');
  } else {
    var msg = prompt('Ingrese la URL del video de YouTube.com:\n', '');
  }

  if (msg != null) {
    h.replaceWith = '[youtube]' + msg + '[/youtube]\n';
    h.openWith = '';
    h.closeWith = '';
  } else {
    h.replaceWith = '';
    h.openWith = '';
    h.closeWith = '';
  }
}

function markit_gv(h) {
  if (is_ie) {
    var msg = prompt('Ingrese el ID del video de Google:\n', '');
  } else {
    var msg = prompt('Ingrese el ID del video de Google:', '');
  }

  if (msg != null) {
    h.replaceWith = '[gvideo]' + msg + '[/gvideo]\n';
    h.openWith = '';
    h.closeWith = '';
  } else {
    h.replaceWith = '';
    h.openWith = '';
    h.closeWith = '';
  }
}

function markit_swf(h) {
  if (h.selection!='' && h.selection.substring(0,7)=='http://') {
    h.replaceWith = '[swf]' + h.selection + '[/swf]\n';
    h.openWith = '';
    h.closeWith = '';
  } else {
    var msg = prompt('Ingrese la URL del archivo swf', 'http://');

    if (msg != null) {
      h.replaceWith = '[swf]' + msg + '[/swf]\n';
      h.openWith = '';
      h.closeWith = '';
    } else {
      h.replaceWith = '';
      h.openWith = '';
      h.closeWith = '';
    }
  }
}

function markit_img(h) {
  if (h.selection!='' && h.selection.substring(0,7)=='http://') {
    h.replaceWith = '[img]' + h.selection + '[/img]\n';
    h.openWith = '';
    h.closeWith = '';
  } else {
    var msg = prompt('Ingrese la URL de la imagen', 'http://');

    if (msg != null) {
      h.replaceWith = '[img]' + msg + '[/img]\n';
      h.openWith = '';
      h.closeWith = '';
    } else {
      h.replaceWith = '';
      h.openWith = '';
      h.closeWith = '';
    }
  }
}

function markit_url(h) {
  if (h.selection=='') {
    var msg = prompt('Ingrese la URL que desea postear', 'http://');

    if (msg != null) {
      h.replaceWith = '[url]' + msg + '[/url]';
      h.openWith = '';
      h.closeWith = '';
    } else {
      h.replaceWith = '';
      h.openWith = '';
      h.closeWith = '';
    }
  } else if (h.selection.substring(0,7)=='http://' || h.selection.substring(0,8)=='https://' || h.selection.substring(0,6)=='ftp://') {
    h.replaceWith = '';
    h.openWith='[url]';
    h.closeWith='[/url]';
  } else {
    var msg = prompt('Ingrese la URL que desea postear', 'http://');

    if (msg != null) {
      h.replaceWith = '';
      h.openWith='[url=' + msg + ']';
      h.closeWith='[/url]';
    } else {
      h.replaceWith = '';
      h.openWith = '';
      h.closeWith = '';
    }
  }
}

function print_editor() {
  if ($('#markItUp') && !$('#markItUpMarkItUp').length) {
    $('#markItUp').markItUp(mySettings);
    $('#emoticons a').click(function() {
      emoticon = ' ' + $(this).attr("smile") + ' ';
      $.markItUp({ replaceWith:emoticon });
      return false;
    });
  }

  // Editor de comentarios
  if ($('#cuerpo_comment') && !$('#markItUpCuerpo_comment').length) {
    $('#cuerpo_comment').markItUp(mySettings_cmt);
    $('#MapSmiles area').click(function() {
      emoticon = ' ' + $(this).attr("smile") + ' ';
      $.markItUp({ replaceWith:emoticon });
      return false;
    });
  }
}
/* FIN - Editor */
function replaceText(text, textarea) {
  if (typeof(textarea.caretPos) != "undefined" && textarea.createTextRange) {
    var caretPos = textarea.caretPos;

    caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? text + ' ' : text;
    caretPos.select();
  } else if (typeof(textarea.selectionStart) != "undefined") {
    var begin = textarea.value.substr(0, textarea.selectionStart);
    var end = textarea.value.substr(textarea.selectionEnd);
    var scrollPos = textarea.scrollTop;

    textarea.value = begin + text + end;

    if (textarea.setSelectionRange) {
      textarea.focus();
      textarea.setSelectionRange(begin.length + text.length, begin.length + text.length);
    }

    textarea.scrollTop = scrollPos;
  } else {
    textarea.value += text;
    textarea.focus(textarea.value.length - 1);
  }
}
  
function createXMLHttpRequest() {
  var xmlhttp = null;
  try {
    xmlhttp = window.XMLHttpRequest ? new XMLHttpRequest() : new ActiveXObject("Microsoft.XMLHTTP");
  } catch(e) {
    alert("Tu explorador no soporta este sitema, CasitaWeb te recomienda que uses Firefox (http://www.mozilla-europe.org/es/firefox/)");
  }

  return xmlhttp;
}

var xhr = createXMLHttpRequest();

/* jQuery 1.2.6 */
(function(){var _jQuery=window.jQuery,_$=window.$;var jQuery=window.jQuery=window.$=function(selector,context){return new jQuery.fn.init(selector,context);};var quickExpr=/^[^<]*(<(.|\s)+>)[^>]*$|^#(\w+)$/,isSimple=/^.[^:#\[\.]*$/,undefined;jQuery.fn=jQuery.prototype={init:function(selector,context){selector=selector||document;if (selector.nodeType){this[0]=selector;this.length=1;return this;}if (typeof selector=="string"){var match=quickExpr.exec(selector);if (match&&(match[1]||!context)){if (match[1])selector=jQuery.clean([match[1]],context);else{var elem=document.getElementById(match[3]);if (elem){if (elem.id!=match[3])return jQuery().find(selector);return jQuery(elem);}selector=[];}}else
return jQuery(context).find(selector);}else if (jQuery.isFunction(selector))return jQuery(document)[jQuery.fn.ready?"ready":"load"](selector);return this.setArray(jQuery.makeArray(selector));},jquery:"1.2.6",size:function(){return this.length;},length:0,get:function(num){return num==undefined?jQuery.makeArray(this):this[num];},pushStack:function(elems){var ret=jQuery(elems);ret.prevObject=this;return ret;},setArray:function(elems){this.length=0;Array.prototype.push.apply(this,elems);return this;},each:function(callback,args){return jQuery.each(this,callback,args);},index:function(elem){var ret=-1;return jQuery.inArray(elem&&elem.jquery?elem[0]:elem,this);},attr:function(name,value,type){var options=name;if (name.constructor==String)if (value===undefined)return this[0]&&jQuery[type||"attr"](this[0],name);else{options={};options[name]=value;}return this.each(function(i){for(name in options)jQuery.attr(type?this.style:this,name,jQuery.prop(this,options[name],type,i,name));});},css:function(key,value){if ((key=='width'||key=='height')&&parseFloat(value)<0)value=undefined;return this.attr(key,value,"curCSS");},text:function(text){if (typeof text!="object"&&text!=null)return this.empty().append((this[0]&&this[0].ownerDocument||document).createTextNode(text));var ret="";jQuery.each(text||this,function(){jQuery.each(this.childNodes,function(){if (this.nodeType!=8)ret+=this.nodeType!=1?this.nodeValue:jQuery.fn.text([this]);});});return ret;},wrapAll:function(html){if (this[0])jQuery(html,this[0].ownerDocument).clone().insertBefore(this[0]).map(function(){var elem=this;while(elem.firstChild)elem=elem.firstChild;return elem;}).append(this);return this;},wrapInner:function(html){return this.each(function(){jQuery(this).contents().wrapAll(html);});},wrap:function(html){return this.each(function(){jQuery(this).wrapAll(html);});},append:function(){return this.domManip(arguments,true,false,function(elem){if (this.nodeType==1)this.appendChild(elem);});},prepend:function(){return this.domManip(arguments,true,true,function(elem){if (this.nodeType==1)this.insertBefore(elem,this.firstChild);});},before:function(){return this.domManip(arguments,false,false,function(elem){this.parentNode.insertBefore(elem,this);});},after:function(){return this.domManip(arguments,false,true,function(elem){this.parentNode.insertBefore(elem,this.nextSibling);});},end:function(){return this.prevObject||jQuery([]);},find:function(selector){var elems=jQuery.map(this,function(elem){return jQuery.find(selector,elem);});return this.pushStack(/[^+>] [^+>]/.test(selector)||selector.indexOf("..")>-1?jQuery.unique(elems):elems);},clone:function(events){var ret=this.map(function(){if (jQuery.browser.msie&&!jQuery.isXMLDoc(this)){var clone=this.cloneNode(true),container=document.createElement("div");container.appendChild(clone);return jQuery.clean([container.innerHTML])[0];}else
return this.cloneNode(true);});var clone=ret.find("*").andSelf().each(function(){if (this[expando]!=undefined)this[expando]=null;});if (events===true)this.find("*").andSelf().each(function(i){if (this.nodeType==3)return;var events=jQuery.data(this,"events");for(var type in events)for(var handler in events[type])jQuery.event.add(clone[i],type,events[type][handler],events[type][handler].data);});return ret;},filter:function(selector){return this.pushStack(jQuery.isFunction(selector)&&jQuery.grep(this,function(elem,i){return selector.call(elem,i);})||jQuery.multiFilter(selector,this));},not:function(selector){if (selector.constructor==String)if (isSimple.test(selector))return this.pushStack(jQuery.multiFilter(selector,this,true));else
selector=jQuery.multiFilter(selector,this);var isArrayLike=selector.length&&selector[selector.length-1]!==undefined&&!selector.nodeType;return this.filter(function(){return isArrayLike?jQuery.inArray(this,selector)<0:this!=selector;});},add:function(selector){return this.pushStack(jQuery.unique(jQuery.merge(this.get(),typeof selector=='string'?jQuery(selector):jQuery.makeArray(selector))));},is:function(selector){return!!selector&&jQuery.multiFilter(selector,this).length>0;},hasClass:function(selector){return this.is("."+selector);},val:function(value){if (value==undefined){if (this.length){var elem=this[0];if (jQuery.nodeName(elem,"select")){var index=elem.selectedIndex,values=[],options=elem.options,one=elem.type=="select-one";if (index<0)return null;for(var i=one?index:0,max=one?index+1:options.length;i<max;i++){var option=options[i];if (option.selected){value=jQuery.browser.msie&&!option.attributes.value.specified?option.text:option.value;if (one)return value;values.push(value);}}return values;}else
return(this[0].value||"").replace(/\r/g,"");}return undefined;}if (value.constructor==Number)value+='';return this.each(function(){if (this.nodeType!=1)return;if (value.constructor==Array&&/radio|checkbox/.test(this.type))this.checked=(jQuery.inArray(this.value,value)>=0||jQuery.inArray(this.name,value)>=0);else if (jQuery.nodeName(this,"select")){var values=jQuery.makeArray(value);jQuery("option",this).each(function(){this.selected=(jQuery.inArray(this.value,values)>=0||jQuery.inArray(this.text,values)>=0);});if (!values.length)this.selectedIndex=-1;}else
this.value=value;});},html:function(value){return value==undefined?(this[0]?this[0].innerHTML:null):this.empty().append(value);},replaceWith:function(value){return this.after(value).remove();},eq:function(i){return this.slice(i,i+1);},slice:function(){return this.pushStack(Array.prototype.slice.apply(this,arguments));},map:function(callback){return this.pushStack(jQuery.map(this,function(elem,i){return callback.call(elem,i,elem);}));},andSelf:function(){return this.add(this.prevObject);},data:function(key,value){var parts=key.split(".");parts[1]=parts[1]?"."+parts[1]:"";if (value===undefined){var data=this.triggerHandler("getData"+parts[1]+"!",[parts[0]]);if (data===undefined&&this.length)data=jQuery.data(this[0],key);return data===undefined&&parts[1]?this.data(parts[0]):data;}else
return this.trigger("setData"+parts[1]+"!",[parts[0],value]).each(function(){jQuery.data(this,key,value);});},removeData:function(key){return this.each(function(){jQuery.removeData(this,key);});},domManip:function(args,table,reverse,callback){var clone=this.length>1,elems;return this.each(function(){if (!elems){elems=jQuery.clean(args,this.ownerDocument);if (reverse)elems.reverse();}var obj=this;if (table&&jQuery.nodeName(this,"table")&&jQuery.nodeName(elems[0],"tr"))obj=this.getElementsByTagName("tbody")[0]||this.appendChild(this.ownerDocument.createElement("tbody"));var scripts=jQuery([]);jQuery.each(elems,function(){var elem=clone?jQuery(this).clone(true)[0]:this;if (jQuery.nodeName(elem,"script"))scripts=scripts.add(elem);else{if (elem.nodeType==1)scripts=scripts.add(jQuery("script",elem).remove());callback.call(obj,elem);}});scripts.each(evalScript);});}};jQuery.fn.init.prototype=jQuery.fn;function evalScript(i,elem){if (elem.src)jQuery.ajax({url:elem.src,async:false,dataType:"script"});else
jQuery.globalEval(elem.text||elem.textContent||elem.innerHTML||"");if (elem.parentNode)elem.parentNode.removeChild(elem);}function now(){return+new Date;}jQuery.extend=jQuery.fn.extend=function(){var target=arguments[0]||{},i=1,length=arguments.length,deep=false,options;if (target.constructor==Boolean){deep=target;target=arguments[1]||{};i=2;}if (typeof target!="object"&&typeof target!="function")target={};if (length==i){target=this;--i;}for(;i<length;i++)if ((options=arguments[i])!=null)for(var name in options){var src=target[name],copy=options[name];if (target===copy)continue;if (deep&&copy&&typeof copy=="object"&&!copy.nodeType)target[name]=jQuery.extend(deep,src||(copy.length!=null?[]:{}),copy);else if (copy!==undefined)target[name]=copy;}return target;};var expando="jQuery"+now(),uuid=0,windowData={},exclude=/z-?index|font-?weight|opacity|zoom|line-?height/i,defaultView=document.defaultView||{};jQuery.extend({noConflict:function(deep){window.$=_$;if (deep)window.jQuery=_jQuery;return jQuery;},isFunction:function(fn){return!!fn&&typeof fn!="string"&&!fn.nodeName&&fn.constructor!=Array&&/^[\s[]?function/.test(fn+"");},isXMLDoc:function(elem){return elem.documentElement&&!elem.body||elem.tagName&&elem.ownerDocument&&!elem.ownerDocument.body;},globalEval:function(data){data=jQuery.trim(data);if (data){var head=document.getElementsByTagName("head")[0]||document.documentElement,script=document.createElement("script");script.type="text/javascript";if (jQuery.browser.msie)script.text=data;else
script.appendChild(document.createTextNode(data));head.insertBefore(script,head.firstChild);head.removeChild(script);}},nodeName:function(elem,name){return elem.nodeName&&elem.nodeName.toUpperCase()==name.toUpperCase();},cache:{},data:function(elem,name,data){elem=elem==window?windowData:elem;var id=elem[expando];if (!id)id=elem[expando]=++uuid;if (name&&!jQuery.cache[id])jQuery.cache[id]={};if (data!==undefined)jQuery.cache[id][name]=data;return name?jQuery.cache[id][name]:id;},removeData:function(elem,name){elem=elem==window?windowData:elem;var id=elem[expando];if (name){if (jQuery.cache[id]){delete jQuery.cache[id][name];name="";for(name in jQuery.cache[id])break;if (!name)jQuery.removeData(elem);}}else{try{delete elem[expando];}catch(e){if (elem.removeAttribute)elem.removeAttribute(expando);}delete jQuery.cache[id];}},each:function(object,callback,args){var name,i=0,length=object.length;if (args){if (length==undefined){for(name in object)if (callback.apply(object[name],args)===false)break;}else
for(;i<length;)if (callback.apply(object[i++],args)===false)break;}else{if (length==undefined){for(name in object)if (callback.call(object[name],name,object[name])===false)break;}else
for(var value=object[0];i<length&&callback.call(value,i,value)!==false;value=object[++i]){}}return object;},prop:function(elem,value,type,i,name){if (jQuery.isFunction(value))value=value.call(elem,i);return value&&value.constructor==Number&&type=="curCSS"&&!exclude.test(name)?value+"px":value;},className:{add:function(elem,classNames){jQuery.each((classNames||"").split(/\s+/),function(i,className){if (elem.nodeType==1&&!jQuery.className.has(elem.className,className))elem.className+=(elem.className?" ":"")+className;});},remove:function(elem,classNames){if (elem.nodeType==1)elem.className=classNames!=undefined?jQuery.grep(elem.className.split(/\s+/),function(className){return!jQuery.className.has(classNames,className);}).join(" "):"";},has:function(elem,className){return jQuery.inArray(className,(elem.className||elem).toString().split(/\s+/))>-1;}},swap:function(elem,options,callback){var old={};for(var name in options){old[name]=elem.style[name];elem.style[name]=options[name];}callback.call(elem);for(var name in options)elem.style[name]=old[name];},css:function(elem,name,force){if (name=="width"||name=="height"){var val,props={position:"absolute",visibility:"hidden",display:"block"},which=name=="width"?["Left","Right"]:["Top","Bottom"];function getWH(){val=name=="width"?elem.offsetWidth:elem.offsetHeight;var padding=0,border=0;jQuery.each(which,function(){padding+=parseFloat(jQuery.curCSS(elem,"padding"+this,true))||0;border+=parseFloat(jQuery.curCSS(elem,"border"+this+"Width",true))||0;});val-=Math.round(padding+border);}if (jQuery(elem).is(":visible"))getWH();else
jQuery.swap(elem,props,getWH);return Math.max(0,val);}return jQuery.curCSS(elem,name,force);},curCSS:function(elem,name,force){var ret,style=elem.style;function color(elem){if (!jQuery.browser.safari)return false;var ret=defaultView.getComputedStyle(elem,null);return!ret||ret.getPropertyValue("color")=="";}if (name=="opacity"&&jQuery.browser.msie){ret=jQuery.attr(style,"opacity");return ret==""?"1":ret;}if (jQuery.browser.opera&&name=="display"){var save=style.outline;style.outline="0 solid black";style.outline=save;}if (name.match(/float/i))name=styleFloat;if (!force&&style&&style[name])ret=style[name];else if (defaultView.getComputedStyle){if (name.match(/float/i))name="float";name=name.replace(/([A-Z])/g,"-$1").toLowerCase();var computedStyle=defaultView.getComputedStyle(elem,null);if (computedStyle&&!color(elem))ret=computedStyle.getPropertyValue(name);else{var swap=[],stack=[],a=elem,i=0;for(;a&&color(a);a=a.parentNode)stack.unshift(a);for(;i<stack.length;i++)if (color(stack[i])){swap[i]=stack[i].style.display;stack[i].style.display="block";}ret=name=="display"&&swap[stack.length-1]!=null?"none":(computedStyle&&computedStyle.getPropertyValue(name))||"";for(i=0;i<swap.length;i++)if (swap[i]!=null)stack[i].style.display=swap[i];}if (name=="opacity"&&ret=="")ret="1";}else if (elem.currentStyle){var camelCase=name.replace(/\-(\w)/g,function(all,letter){return letter.toUpperCase();});ret=elem.currentStyle[name]||elem.currentStyle[camelCase];if (!/^\d+(px)?$/i.test(ret)&&/^\d/.test(ret)){var left=style.left,rsLeft=elem.runtimeStyle.left;elem.runtimeStyle.left=elem.currentStyle.left;style.left=ret||0;ret=style.pixelLeft+"px";style.left=left;elem.runtimeStyle.left=rsLeft;}}return ret;},clean:function(elems,context){var ret=[];context=context||document;if (typeof context.createElement=='undefined')context=context.ownerDocument||context[0]&&context[0].ownerDocument||document;jQuery.each(elems,function(i,elem){if (!elem)return;if (elem.constructor==Number)elem+='';if (typeof elem=="string"){elem=elem.replace(/(<(\w+)[^>]*?)\/>/g,function(all,front,tag){return tag.match(/^(abbr|br|col|img|input|link|meta|param|hr|area|embed)$/i)?all:front+"></"+tag+">";});var tags=jQuery.trim(elem).toLowerCase(),div=context.createElement("div");var wrap=!tags.indexOf("<opt")&&[1,"<select multiple='multiple'>","</select>"]||!tags.indexOf("<leg")&&[1,"<fieldset>","</fieldset>"]||tags.match(/^<(thead|tbody|tfoot|colg|cap)/)&&[1,"<table>","</table>"]||!tags.indexOf("<tr")&&[2,"<table><tbody>","</tbody></table>"]||(!tags.indexOf("<td")||!tags.indexOf("<th"))&&[3,"<table><tbody><tr>","</tr></tbody></table>"]||!tags.indexOf("<col")&&[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"]||jQuery.browser.msie&&[1,"div<div>","</div>"]||[0,"",""];div.innerHTML=wrap[1]+elem+wrap[2];while(wrap[0]--)div=div.lastChild;if (jQuery.browser.msie){var tbody=!tags.indexOf("<table")&&tags.indexOf("<tbody")<0?div.firstChild&&div.firstChild.childNodes:wrap[1]=="<table>"&&tags.indexOf("<tbody")<0?div.childNodes:[];for(var j=tbody.length-1;j>=0;--j)if (jQuery.nodeName(tbody[j],"tbody")&&!tbody[j].childNodes.length)tbody[j].parentNode.removeChild(tbody[j]);if (/^\s/.test(elem))div.insertBefore(context.createTextNode(elem.match(/^\s*/)[0]),div.firstChild);}elem=jQuery.makeArray(div.childNodes);}if (elem.length===0&&(!jQuery.nodeName(elem,"form")&&!jQuery.nodeName(elem,"select")))return;if (elem[0]==undefined||jQuery.nodeName(elem,"form")||elem.options)ret.push(elem);else
ret=jQuery.merge(ret,elem);});return ret;},attr:function(elem,name,value){if (!elem||elem.nodeType==3||elem.nodeType==8)return undefined;var notxml=!jQuery.isXMLDoc(elem),set=value!==undefined,msie=jQuery.browser.msie;name=notxml&&jQuery.props[name]||name;if (elem.tagName){var special=/href|src|style/.test(name);if (name=="selected"&&jQuery.browser.safari)elem.parentNode.selectedIndex;if (name in elem&&notxml&&!special){if (set){if (name=="type"&&jQuery.nodeName(elem,"input")&&elem.parentNode)throw"type property can't be changed";elem[name]=value;}if (jQuery.nodeName(elem,"form")&&elem.getAttributeNode(name))return elem.getAttributeNode(name).nodeValue;return elem[name];}if (msie&&notxml&&name=="style")return jQuery.attr(elem.style,"cssText",value);if (set)elem.setAttribute(name,""+value);var attr=msie&&notxml&&special?elem.getAttribute(name,2):elem.getAttribute(name);return attr===null?undefined:attr;}if (msie&&name=="opacity"){if (set){elem.zoom=1;elem.filter=(elem.filter||"").replace(/alpha\([^)]*\)/,"")+(parseInt(value)+''=="NaN"?"":"alpha(opacity="+value*100+")");}return elem.filter&&elem.filter.indexOf("opacity=")>=0?(parseFloat(elem.filter.match(/opacity=([^)]*)/)[1])/100)+'':"";}name=name.replace(/-([a-z])/ig,function(all,letter){return letter.toUpperCase();});if (set)elem[name]=value;return elem[name];},trim:function(text){return(text||"").replace(/^\s+|\s+$/g,"");},makeArray:function(array){var ret=[];if (array!=null){var i=array.length;if (i==null||array.split||array.setInterval||array.call)ret[0]=array;else
while(i)ret[--i]=array[i];}return ret;},inArray:function(elem,array){for(var i=0,length=array.length;i<length;i++)if (array[i]===elem)return i;return-1;},merge:function(first,second){var i=0,elem,pos=first.length;if (jQuery.browser.msie){while(elem=second[i++])if (elem.nodeType!=8)first[pos++]=elem;}else
while(elem=second[i++])first[pos++]=elem;return first;},unique:function(array){var ret=[],done={};try{for(var i=0,length=array.length;i<length;i++){var id=jQuery.data(array[i]);if (!done[id]){done[id]=true;ret.push(array[i]);}}}catch(e){ret=array;}return ret;},grep:function(elems,callback,inv){var ret=[];for(var i=0,length=elems.length;i<length;i++)if (!inv!=!callback(elems[i],i))ret.push(elems[i]);return ret;},map:function(elems,callback){var ret=[];for(var i=0,length=elems.length;i<length;i++){var value=callback(elems[i],i);if (value!=null)ret[ret.length]=value;}return ret.concat.apply([],ret);}});var userAgent=navigator.userAgent.toLowerCase();jQuery.browser={version:(userAgent.match(/.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/)||[])[1],safari:/webkit/.test(userAgent),opera:/opera/.test(userAgent),msie:/msie/.test(userAgent)&&!/opera/.test(userAgent),mozilla:/mozilla/.test(userAgent)&&!/(compatible|webkit)/.test(userAgent)};var styleFloat=jQuery.browser.msie?"styleFloat":"cssFloat";jQuery.extend({boxModel:!jQuery.browser.msie||document.compatMode=="CSS1Compat",props:{"for":"htmlFor","class":"className","float":styleFloat,cssFloat:styleFloat,styleFloat:styleFloat,readonly:"readOnly",maxlength:"maxLength",cellspacing:"cellSpacing"}});jQuery.each({parent:function(elem){return elem.parentNode;},parents:function(elem){return jQuery.dir(elem,"parentNode");},next:function(elem){return jQuery.nth(elem,2,"nextSibling");},prev:function(elem){return jQuery.nth(elem,2,"previousSibling");},nextAll:function(elem){return jQuery.dir(elem,"nextSibling");},prevAll:function(elem){return jQuery.dir(elem,"previousSibling");},siblings:function(elem){return jQuery.sibling(elem.parentNode.firstChild,elem);},children:function(elem){return jQuery.sibling(elem.firstChild);},contents:function(elem){return jQuery.nodeName(elem,"iframe")?elem.contentDocument||elem.contentWindow.document:jQuery.makeArray(elem.childNodes);}},function(name,fn){jQuery.fn[name]=function(selector){var ret=jQuery.map(this,fn);if (selector&&typeof selector=="string")ret=jQuery.multiFilter(selector,ret);return this.pushStack(jQuery.unique(ret));};});jQuery.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(name,original){jQuery.fn[name]=function(){var args=arguments;return this.each(function(){for(var i=0,length=args.length;i<length;i++)jQuery(args[i])[original](this);});};});jQuery.each({removeAttr:function(name){jQuery.attr(this,name,"");if (this.nodeType==1)this.removeAttribute(name);},addClass:function(classNames){jQuery.className.add(this,classNames);},removeClass:function(classNames){jQuery.className.remove(this,classNames);},toggleClass:function(classNames){jQuery.className[jQuery.className.has(this,classNames)?"remove":"add"](this,classNames);},remove:function(selector){if (!selector||jQuery.filter(selector,[this]).r.length){jQuery("*",this).add(this).each(function(){jQuery.event.remove(this);jQuery.removeData(this);});if (this.parentNode)this.parentNode.removeChild(this);}},empty:function(){jQuery(">*",this).remove();while(this.firstChild)this.removeChild(this.firstChild);}},function(name,fn){jQuery.fn[name]=function(){return this.each(fn,arguments);};});jQuery.each(["Height","Width"],function(i,name){var type=name.toLowerCase();jQuery.fn[type]=function(size){return this[0]==window?jQuery.browser.opera&&document.body["client"+name]||jQuery.browser.safari&&window["inner"+name]||document.compatMode=="CSS1Compat"&&document.documentElement["client"+name]||document.body["client"+name]:this[0]==document?Math.max(Math.max(document.body["scroll"+name],document.documentElement["scroll"+name]),Math.max(document.body["offset"+name],document.documentElement["offset"+name])):size==undefined?(this.length?jQuery.css(this[0],type):null):this.css(type,size.constructor==String?size:size+"px");};});function num(elem,prop){return elem[0]&&parseInt(jQuery.curCSS(elem[0],prop,true),10)||0;}var chars=jQuery.browser.safari&&parseInt(jQuery.browser.version)<417?"(?:[\\w*_-]|\\\\.)":"(?:[\\w\u0128-\uFFFF*_-]|\\\\.)",quickChild=new RegExp("^>\\s*("+chars+"+)"),quickID=new RegExp("^("+chars+"+)(#)("+chars+"+)"),quickClass=new RegExp("^([#.]?)("+chars+"*)");jQuery.extend({expr:{"":function(a,i,m){return m[2]=="*"||jQuery.nodeName(a,m[2]);},"#":function(a,i,m){return a.getAttribute("id")==m[2];},":":{lt:function(a,i,m){return i<m[3]-0;},gt:function(a,i,m){return i>m[3]-0;},nth:function(a,i,m){return m[3]-0==i;},eq:function(a,i,m){return m[3]-0==i;},first:function(a,i){return i==0;},last:function(a,i,m,r){return i==r.length-1;},even:function(a,i){return i%2==0;},odd:function(a,i){return i%2;},"first-child":function(a){return a.parentNode.getElementsByTagName("*")[0]==a;},"last-child":function(a){return jQuery.nth(a.parentNode.lastChild,1,"previousSibling")==a;},"only-child":function(a){return!jQuery.nth(a.parentNode.lastChild,2,"previousSibling");},parent:function(a){return a.firstChild;},empty:function(a){return!a.firstChild;},contains:function(a,i,m){return(a.textContent||a.innerText||jQuery(a).text()||"").indexOf(m[3])>=0;},visible:function(a){return"hidden"!=a.type&&jQuery.css(a,"display")!="none"&&jQuery.css(a,"visibility")!="hidden";},hidden:function(a){return"hidden"==a.type||jQuery.css(a,"display")=="none"||jQuery.css(a,"visibility")=="hidden";},enabled:function(a){return!a.disabled;},disabled:function(a){return a.disabled;},checked:function(a){return a.checked;},selected:function(a){return a.selected||jQuery.attr(a,"selected");},text:function(a){return"text"==a.type;},radio:function(a){return"radio"==a.type;},checkbox:function(a){return"checkbox"==a.type;},file:function(a){return"file"==a.type;},password:function(a){return"password"==a.type;},submit:function(a){return"submit"==a.type;},image:function(a){return"image"==a.type;},reset:function(a){return"reset"==a.type;},button:function(a){return"button"==a.type||jQuery.nodeName(a,"button");},input:function(a){return/input|select|textarea|button/i.test(a.nodeName);},has:function(a,i,m){return jQuery.find(m[3],a).length;},header:function(a){return/h\d/i.test(a.nodeName);},animated:function(a){return jQuery.grep(jQuery.timers,function(fn){return a==fn.elem;}).length;}}},parse:[/^(\[) *@?([\w-]+) *([!*$^~=]*) *('?"?)(.*?)\4 *\]/,/^(:)([\w-]+)\("?'?(.*?(\(.*?\))?[^(]*?)"?'?\)/,new RegExp("^([:.#]*)("+chars+"+)")],multiFilter:function(expr,elems,not){var old,cur=[];while(expr&&expr!=old){old=expr;var f=jQuery.filter(expr,elems,not);expr=f.t.replace(/^\s*,\s*/,"");cur=not?elems=f.r:jQuery.merge(cur,f.r);}return cur;},find:function(t,context){if (typeof t!="string")return[t];if (context&&context.nodeType!=1&&context.nodeType!=9)return[];context=context||document;var ret=[context],done=[],last,nodeName;while(t&&last!=t){var r=[];last=t;t=jQuery.trim(t);var foundToken=false,re=quickChild,m=re.exec(t);if (m){nodeName=m[1].toUpperCase();for(var i=0;ret[i];i++)for(var c=ret[i].firstChild;c;c=c.nextSibling)if (c.nodeType==1&&(nodeName=="*"||c.nodeName.toUpperCase()==nodeName))r.push(c);ret=r;t=t.replace(re,"");if (t.indexOf(" ")==0)continue;foundToken=true;}else{re=/^([>+~])\s*(\w*)/i;if ((m=re.exec(t))!=null){r=[];var merge={};nodeName=m[2].toUpperCase();m=m[1];for(var j=0,rl=ret.length;j<rl;j++){var n=m=="~"||m=="+"?ret[j].nextSibling:ret[j].firstChild;for(;n;n=n.nextSibling)if (n.nodeType==1){var id=jQuery.data(n);if (m=="~"&&merge[id])break;if (!nodeName||n.nodeName.toUpperCase()==nodeName){if (m=="~")merge[id]=true;r.push(n);}if (m=="+")break;}}ret=r;t=jQuery.trim(t.replace(re,""));foundToken=true;}}if (t&&!foundToken){if (!t.indexOf(",")){if (context==ret[0])ret.shift();done=jQuery.merge(done,ret);r=ret=[context];t=" "+t.substr(1,t.length);}else{var re2=quickID;var m=re2.exec(t);if (m){m=[0,m[2],m[3],m[1]];}else{re2=quickClass;m=re2.exec(t);}m[2]=m[2].replace(/\\/g,"");var elem=ret[ret.length-1];if (m[1]=="#"&&elem&&elem.getElementById&&!jQuery.isXMLDoc(elem)){var oid=elem.getElementById(m[2]);if ((jQuery.browser.msie||jQuery.browser.opera)&&oid&&typeof oid.id=="string"&&oid.id!=m[2])oid=jQuery('[@id="'+m[2]+'"]',elem)[0];ret=r=oid&&(!m[3]||jQuery.nodeName(oid,m[3]))?[oid]:[];}else{for(var i=0;ret[i];i++){var tag=m[1]=="#"&&m[3]?m[3]:m[1]!=""||m[0]==""?"*":m[2];if (tag=="*"&&ret[i].nodeName.toLowerCase()=="object")tag="param";r=jQuery.merge(r,ret[i].getElementsByTagName(tag));}if (m[1]==".")r=jQuery.classFilter(r,m[2]);if (m[1]=="#"){var tmp=[];for(var i=0;r[i];i++)if (r[i].getAttribute("id")==m[2]){tmp=[r[i]];break;}r=tmp;}ret=r;}t=t.replace(re2,"");}}if (t){var val=jQuery.filter(t,r);ret=r=val.r;t=jQuery.trim(val.t);}}if (t)ret=[];if (ret&&context==ret[0])ret.shift();done=jQuery.merge(done,ret);return done;},classFilter:function(r,m,not){m=" "+m+" ";var tmp=[];for(var i=0;r[i];i++){var pass=(" "+r[i].className+" ").indexOf(m)>=0;if (!not&&pass||not&&!pass)tmp.push(r[i]);}return tmp;},filter:function(t,r,not){var last;while(t&&t!=last){last=t;var p=jQuery.parse,m;for(var i=0;p[i];i++){m=p[i].exec(t);if (m){t=t.substring(m[0].length);m[2]=m[2].replace(/\\/g,"");break;}}if (!m)break;if (m[1]==":"&&m[2]=="not")r=isSimple.test(m[3])?jQuery.filter(m[3],r,true).r:jQuery(r).not(m[3]);else if (m[1]==".")r=jQuery.classFilter(r,m[2],not);else if (m[1]=="["){var tmp=[],type=m[3];for(var i=0,rl=r.length;i<rl;i++){var a=r[i],z=a[jQuery.props[m[2]]||m[2]];if (z==null||/href|src|selected/.test(m[2]))z=jQuery.attr(a,m[2])||'';if ((type==""&&!!z||type=="="&&z==m[5]||type=="!="&&z!=m[5]||type=="^="&&z&&!z.indexOf(m[5])||type=="$="&&z.substr(z.length-m[5].length)==m[5]||(type=="*="||type=="~=")&&z.indexOf(m[5])>=0)^not)tmp.push(a);}r=tmp;}else if (m[1]==":"&&m[2]=="nth-child"){var merge={},tmp=[],test=/(-?)(\d*)n((?:\+|-)?\d*)/.exec(m[3]=="even"&&"2n"||m[3]=="odd"&&"2n+1"||!/\D/.test(m[3])&&"0n+"+m[3]||m[3]),first=(test[1]+(test[2]||1))-0,last=test[3]-0;for(var i=0,rl=r.length;i<rl;i++){var node=r[i],parentNode=node.parentNode,id=jQuery.data(parentNode);if (!merge[id]){var c=1;for(var n=parentNode.firstChild;n;n=n.nextSibling)if (n.nodeType==1)n.nodeIndex=c++;merge[id]=true;}var add=false;if (first==0){if (node.nodeIndex==last)add=true;}else if ((node.nodeIndex-last)%first==0&&(node.nodeIndex-last)/first>=0)add=true;if (add^not)tmp.push(node);}r=tmp;}else{var fn=jQuery.expr[m[1]];if (typeof fn=="object")fn=fn[m[2]];if (typeof fn=="string")fn=eval("false||function(a,i){return "+fn+";}");r=jQuery.grep(r,function(elem,i){return fn(elem,i,m,r);},not);}}return{r:r,t:t};},dir:function(elem,dir){var matched=[],cur=elem[dir];while(cur&&cur!=document){if (cur.nodeType==1)matched.push(cur);cur=cur[dir];}return matched;},nth:function(cur,result,dir,elem){result=result||1;var num=0;for(;cur;cur=cur[dir])if (cur.nodeType==1&&++num==result)break;return cur;},sibling:function(n,elem){var r=[];for(;n;n=n.nextSibling){if (n.nodeType==1&&n!=elem)r.push(n);}return r;}});jQuery.event={add:function(elem,types,handler,data){if (elem.nodeType==3||elem.nodeType==8)return;if (jQuery.browser.msie&&elem.setInterval)elem=window;if (!handler.guid)handler.guid=this.guid++;if (data!=undefined){var fn=handler;handler=this.proxy(fn,function(){return fn.apply(this,arguments);});handler.data=data;}var events=jQuery.data(elem,"events")||jQuery.data(elem,"events",{}),handle=jQuery.data(elem,"handle")||jQuery.data(elem,"handle",function(){if (typeof jQuery!="undefined"&&!jQuery.event.triggered)return jQuery.event.handle.apply(arguments.callee.elem,arguments);});handle.elem=elem;jQuery.each(types.split(/\s+/),function(index,type){var parts=type.split(".");type=parts[0];handler.type=parts[1];var handlers=events[type];if (!handlers){handlers=events[type]={};if (!jQuery.event.special[type]||jQuery.event.special[type].setup.call(elem)===false){if (elem.addEventListener)elem.addEventListener(type,handle,false);else if (elem.attachEvent)elem.attachEvent("on"+type,handle);}}handlers[handler.guid]=handler;jQuery.event.global[type]=true;});elem=null;},guid:1,global:{},remove:function(elem,types,handler){if (elem.nodeType==3||elem.nodeType==8)return;var events=jQuery.data(elem,"events"),ret,index;if (events){if (types==undefined||(typeof types=="string"&&types.charAt(0)=="."))for(var type in events)this.remove(elem,type+(types||""));else{if (types.type){handler=types.handler;types=types.type;}jQuery.each(types.split(/\s+/),function(index,type){var parts=type.split(".");type=parts[0];if (events[type]){if (handler)delete events[type][handler.guid];else
for(handler in events[type])if (!parts[1]||events[type][handler].type==parts[1])delete events[type][handler];for(ret in events[type])break;if (!ret){if (!jQuery.event.special[type]||jQuery.event.special[type].teardown.call(elem)===false){if (elem.removeEventListener)elem.removeEventListener(type,jQuery.data(elem,"handle"),false);else if (elem.detachEvent)elem.detachEvent("on"+type,jQuery.data(elem,"handle"));}ret=null;delete events[type];}}});}for(ret in events)break;if (!ret){var handle=jQuery.data(elem,"handle");if (handle)handle.elem=null;jQuery.removeData(elem,"events");jQuery.removeData(elem,"handle");}}},trigger:function(type,data,elem,donative,extra){data=jQuery.makeArray(data);if (type.indexOf("!")>=0){type=type.slice(0,-1);var exclusive=true;}if (!elem){if (this.global[type])jQuery("*").add([window,document]).trigger(type,data);}else{if (elem.nodeType==3||elem.nodeType==8)return undefined;var val,ret,fn=jQuery.isFunction(elem[type]||null),event=!data[0]||!data[0].preventDefault;if (event){data.unshift({type:type,target:elem,preventDefault:function(){},stopPropagation:function(){},timeStamp:now()});data[0][expando]=true;}data[0].type=type;if (exclusive)data[0].exclusive=true;var handle=jQuery.data(elem,"handle");if (handle)val=handle.apply(elem,data);if ((!fn||(jQuery.nodeName(elem,'a')&&type=="click"))&&elem["on"+type]&&elem["on"+type].apply(elem,data)===false)val=false;if (event)data.shift();if (extra&&jQuery.isFunction(extra)){ret=extra.apply(elem,val==null?data:data.concat(val));if (ret!==undefined)val=ret;}if (fn&&donative!==false&&val!==false&&!(jQuery.nodeName(elem,'a')&&type=="click")){this.triggered=true;try{elem[type]();}catch(e){}}this.triggered=false;}return val;},handle:function(event){var val,ret,namespace,all,handlers;event=arguments[0]=jQuery.event.fix(event||window.event);namespace=event.type.split(".");event.type=namespace[0];namespace=namespace[1];all=!namespace&&!event.exclusive;handlers=(jQuery.data(this,"events")||{})[event.type];for(var j in handlers){var handler=handlers[j];if (all||handler.type==namespace){event.handler=handler;event.data=handler.data;ret=handler.apply(this,arguments);if (val!==false)val=ret;if (ret===false){event.preventDefault();event.stopPropagation();}}}return val;},fix:function(event){if (event[expando]==true)return event;var originalEvent=event;event={originalEvent:originalEvent};var props="altKey attrChange attrName bubbles button cancelable charCode clientX clientY ctrlKey currentTarget data detail eventPhase fromElement handler keyCode metaKey newValue originalTarget pageX pageY prevValue relatedNode relatedTarget screenX screenY shiftKey srcElement target timeStamp toElement type view wheelDelta which".split(" ");for(var i=props.length;i;i--)event[props[i]]=originalEvent[props[i]];event[expando]=true;event.preventDefault=function(){if (originalEvent.preventDefault)originalEvent.preventDefault();originalEvent.returnValue=false;};event.stopPropagation=function(){if (originalEvent.stopPropagation)originalEvent.stopPropagation();originalEvent.cancelBubble=true;};event.timeStamp=event.timeStamp||now();if (!event.target)event.target=event.srcElement||document;if (event.target.nodeType==3)event.target=event.target.parentNode;if (!event.relatedTarget&&event.fromElement)event.relatedTarget=event.fromElement==event.target?event.toElement:event.fromElement;if (event.pageX==null&&event.clientX!=null){var doc=document.documentElement,body=document.body;event.pageX=event.clientX+(doc&&doc.scrollLeft||body&&body.scrollLeft||0)-(doc.clientLeft||0);event.pageY=event.clientY+(doc&&doc.scrollTop||body&&body.scrollTop||0)-(doc.clientTop||0);}if (!event.which&&((event.charCode||event.charCode===0)?event.charCode:event.keyCode))event.which=event.charCode||event.keyCode;if (!event.metaKey&&event.ctrlKey)event.metaKey=event.ctrlKey;if (!event.which&&event.button)event.which=(event.button&1?1:(event.button&2?3:(event.button&4?2:0)));return event;},proxy:function(fn,proxy){proxy.guid=fn.guid=fn.guid||proxy.guid||this.guid++;return proxy;},special:{ready:{setup:function(){bindReady();return;},teardown:function(){return;}},mouseenter:{setup:function(){if (jQuery.browser.msie)return false;jQuery(this).bind("mouseover",jQuery.event.special.mouseenter.handler);return true;},teardown:function(){if (jQuery.browser.msie)return false;jQuery(this).unbind("mouseover",jQuery.event.special.mouseenter.handler);return true;},handler:function(event){if (withinElement(event,this))return true;event.type="mouseenter";return jQuery.event.handle.apply(this,arguments);}},mouseleave:{setup:function(){if (jQuery.browser.msie)return false;jQuery(this).bind("mouseout",jQuery.event.special.mouseleave.handler);return true;},teardown:function(){if (jQuery.browser.msie)return false;jQuery(this).unbind("mouseout",jQuery.event.special.mouseleave.handler);return true;},handler:function(event){if (withinElement(event,this))return true;event.type="mouseleave";return jQuery.event.handle.apply(this,arguments);}}}};jQuery.fn.extend({bind:function(type,data,fn){return type=="unload"?this.one(type,data,fn):this.each(function(){jQuery.event.add(this,type,fn||data,fn&&data);});},one:function(type,data,fn){var one=jQuery.event.proxy(fn||data,function(event){jQuery(this).unbind(event,one);return(fn||data).apply(this,arguments);});return this.each(function(){jQuery.event.add(this,type,one,fn&&data);});},unbind:function(type,fn){return this.each(function(){jQuery.event.remove(this,type,fn);});},trigger:function(type,data,fn){return this.each(function(){jQuery.event.trigger(type,data,this,true,fn);});},triggerHandler:function(type,data,fn){return this[0]&&jQuery.event.trigger(type,data,this[0],false,fn);},toggle:function(fn){var args=arguments,i=1;while(i<args.length)jQuery.event.proxy(fn,args[i++]);return this.click(jQuery.event.proxy(fn,function(event){this.lastToggle=(this.lastToggle||0)%i;event.preventDefault();return args[this.lastToggle++].apply(this,arguments)||false;}));},hover:function(fnOver,fnOut){return this.bind('mouseenter',fnOver).bind('mouseleave',fnOut);},ready:function(fn){bindReady();if (jQuery.isReady)fn.call(document,jQuery);else
jQuery.readyList.push(function(){return fn.call(this,jQuery);});return this;}});jQuery.extend({isReady:false,readyList:[],ready:function(){if (!jQuery.isReady){jQuery.isReady=true;if (jQuery.readyList){jQuery.each(jQuery.readyList,function(){this.call(document);});jQuery.readyList=null;}jQuery(document).triggerHandler("ready");}}});var readyBound=false;function bindReady(){if (readyBound)return;readyBound=true;if (document.addEventListener&&!jQuery.browser.opera)document.addEventListener("DOMContentLoaded",jQuery.ready,false);if (jQuery.browser.msie&&window==top)(function(){if (jQuery.isReady)return;try{document.documentElement.doScroll("left");}catch(error){setTimeout(arguments.callee,0);return;}jQuery.ready();})();if (jQuery.browser.opera)document.addEventListener("DOMContentLoaded",function(){if (jQuery.isReady)return;for(var i=0;i<document.styleSheets.length;i++)if (document.styleSheets[i].disabled){setTimeout(arguments.callee,0);return;}jQuery.ready();},false);if (jQuery.browser.safari){var numStyles;(function(){if (jQuery.isReady)return;if (document.readyState!="loaded"&&document.readyState!="complete"){setTimeout(arguments.callee,0);return;}if (numStyles===undefined)numStyles=jQuery("style, link[rel=stylesheet]").length;if (document.styleSheets.length!=numStyles){setTimeout(arguments.callee,0);return;}jQuery.ready();})();}jQuery.event.add(window,"load",jQuery.ready);}jQuery.each(("blur,focus,load,resize,scroll,unload,click,dblclick,"+"mousedown,mouseup,mousemove,mouseover,mouseout,change,select,"+"submit,keydown,keypress,keyup,error").split(","),function(i,name){jQuery.fn[name]=function(fn){return fn?this.bind(name,fn):this.trigger(name);};});var withinElement=function(event,elem){var parent=event.relatedTarget;while(parent&&parent!=elem)try{parent=parent.parentNode;}catch(error){parent=elem;}return parent==elem;};jQuery(window).bind("unload",function(){jQuery("*").add(document).unbind();});jQuery.fn.extend({_load:jQuery.fn.load,load:function(url,params,callback){if (typeof url!='string')return this._load(url);var off=url.indexOf(" ");if (off>=0){var selector=url.slice(off,url.length);url=url.slice(0,off);}callback=callback||function(){};var type="GET";if (params)if (jQuery.isFunction(params)){callback=params;params=null;}else{params=jQuery.param(params);type="POST";}var self=this;jQuery.ajax({url:url,type:type,dataType:"html",data:params,complete:function(res,status){if (status=="success"||status=="notmodified")self.html(selector?jQuery("<div/>").append(res.responseText.replace(/<script(.|\s)*?\/script>/g,"")).find(selector):res.responseText);self.each(callback,[res.responseText,status,res]);}});return this;},serialize:function(){return jQuery.param(this.serializeArray());},serializeArray:function(){return this.map(function(){return jQuery.nodeName(this,"form")?jQuery.makeArray(this.elements):this;}).filter(function(){return this.name&&!this.disabled&&(this.checked||/select|textarea/i.test(this.nodeName)||/text|hidden|password/i.test(this.type));}).map(function(i,elem){var val=jQuery(this).val();return val==null?null:val.constructor==Array?jQuery.map(val,function(val,i){return{name:elem.name,value:val};}):{name:elem.name,value:val};}).get();}});jQuery.each("ajaxStart,ajaxStop,ajaxComplete,ajaxError,ajaxSuccess,ajaxSend".split(","),function(i,o){jQuery.fn[o]=function(f){return this.bind(o,f);};});var jsc=now();jQuery.extend({get:function(url,data,callback,type){if (jQuery.isFunction(data)){callback=data;data=null;}return jQuery.ajax({type:"GET",url:url,data:data,success:callback,dataType:type});},getScript:function(url,callback){return jQuery.get(url,null,callback,"script");},getJSON:function(url,data,callback){return jQuery.get(url,data,callback,"json");},post:function(url,data,callback,type){if (jQuery.isFunction(data)){callback=data;data={};}return jQuery.ajax({type:"POST",url:url,data:data,success:callback,dataType:type});},ajaxSetup:function(settings){jQuery.extend(jQuery.ajaxSettings,settings);},ajaxSettings:{url:location.href,global:true,type:"GET",timeout:0,contentType:"application/x-www-form-urlencoded",processData:true,async:true,data:null,username:null,password:null,accepts:{xml:"application/xml, text/xml",html:"text/html",script:"text/javascript, application/javascript",json:"application/json, text/javascript",text:"text/plain",_default:"*/*"}},lastModified:{},ajax:function(s){s=jQuery.extend(true,s,jQuery.extend(true,{},jQuery.ajaxSettings,s));var jsonp,jsre=/=\?(&|$)/g,status,data,type=s.type.toUpperCase();if (s.data&&s.processData&&typeof s.data!="string")s.data=jQuery.param(s.data);if (s.dataType=="jsonp"){if (type=="GET"){if (!s.url.match(jsre))s.url+=(s.url.match(/\?/)?"&":"?")+(s.jsonp||"callback")+"=?";}else if (!s.data||!s.data.match(jsre))s.data=(s.data?s.data+"&":"")+(s.jsonp||"callback")+"=?";s.dataType="json";}if (s.dataType=="json"&&(s.data&&s.data.match(jsre)||s.url.match(jsre))){jsonp="jsonp"+jsc++;if (s.data)s.data=(s.data+"").replace(jsre,"="+jsonp+"$1");s.url=s.url.replace(jsre,"="+jsonp+"$1");s.dataType="script";window[jsonp]=function(tmp){data=tmp;success();complete();window[jsonp]=undefined;try{delete window[jsonp];}catch(e){}if (head)head.removeChild(script);};}if (s.dataType=="script"&&s.cache==null)s.cache=false;if (s.cache===false&&type=="GET"){var ts=now();var ret=s.url.replace(/(\?|&)_=.*?(&|$)/,"$1_="+ts+"$2");s.url=ret+((ret==s.url)?(s.url.match(/\?/)?"&":"?")+"_="+ts:"");}if (s.data&&type=="GET"){s.url+=(s.url.match(/\?/)?"&":"?")+s.data;s.data=null;}if (s.global&&!jQuery.active++)jQuery.event.trigger("ajaxStart");var remote=/^(?:\w+:)?\/\/([^\/?#]+)/;if (s.dataType=="script"&&type=="GET"&&remote.test(s.url)&&remote.exec(s.url)[1]!=location.host){var head=document.getElementsByTagName("head")[0];var script=document.createElement("script");script.src=s.url;if (s.scriptCharset)script.charset=s.scriptCharset;if (!jsonp){var done=false;script.onload=script.onreadystatechange=function(){if (!done&&(!this.readyState||this.readyState=="loaded"||this.readyState=="complete")){done=true;success();complete();head.removeChild(script);}};}head.appendChild(script);return undefined;}var requestDone=false;var xhr=window.ActiveXObject?new ActiveXObject("Microsoft.XMLHTTP"):new XMLHttpRequest();if (s.username)xhr.open(type,s.url,s.async,s.username,s.password);else
xhr.open(type,s.url,s.async);try{if (s.data)xhr.setRequestHeader("Content-Type",s.contentType);if (s.ifModified)xhr.setRequestHeader("If-Modified-Since",jQuery.lastModified[s.url]||"Thu, 01 Jan 1970 00:00:00 GMT");xhr.setRequestHeader("X-Requested-With","XMLHttpRequest");xhr.setRequestHeader("Accept",s.dataType&&s.accepts[s.dataType]?s.accepts[s.dataType]+", */*":s.accepts._default);}catch(e){}if (s.beforeSend&&s.beforeSend(xhr,s)===false){s.global&&jQuery.active--;xhr.abort();return false;}if (s.global)jQuery.event.trigger("ajaxSend",[xhr,s]);var onreadystatechange=function(isTimeout){if (!requestDone&&xhr&&(xhr.readyState==4||isTimeout=="timeout")){requestDone=true;if (ival){clearInterval(ival);ival=null;}status=isTimeout=="timeout"&&"timeout"||!jQuery.httpSuccess(xhr)&&"error"||s.ifModified&&jQuery.httpNotModified(xhr,s.url)&&"notmodified"||"success";if (status=="success"){try{data=jQuery.httpData(xhr,s.dataType,s.dataFilter);}catch(e){status="parsererror";}}if (status=="success"){var modRes;try{modRes=xhr.getResponseHeader("Last-Modified");}catch(e){}if (s.ifModified&&modRes)jQuery.lastModified[s.url]=modRes;if (!jsonp)success();}else
jQuery.handleError(s,xhr,status);complete();if (s.async)xhr=null;}};if (s.async){var ival=setInterval(onreadystatechange,13);if (s.timeout>0)setTimeout(function(){if (xhr){xhr.abort();if (!requestDone)onreadystatechange("timeout");}},s.timeout);}try{xhr.send(s.data);}catch(e){jQuery.handleError(s,xhr,null,e);}if (!s.async)onreadystatechange();function success(){if (s.success)s.success(data,status);if (s.global)jQuery.event.trigger("ajaxSuccess",[xhr,s]);}function complete(){if (s.complete)s.complete(xhr,status);if (s.global)jQuery.event.trigger("ajaxComplete",[xhr,s]);if (s.global&&!--jQuery.active)jQuery.event.trigger("ajaxStop");}return xhr;},handleError:function(s,xhr,status,e){if (s.error)s.error(xhr,status,e);if (s.global)jQuery.event.trigger("ajaxError",[xhr,s,e]);},active:0,httpSuccess:function(xhr){try{return!xhr.status&&location.protocol=="file:"||(xhr.status>=200&&xhr.status<300)||xhr.status==304||xhr.status==1223||jQuery.browser.safari&&xhr.status==undefined;}catch(e){}return false;},httpNotModified:function(xhr,url){try{var xhrRes=xhr.getResponseHeader("Last-Modified");return xhr.status==304||xhrRes==jQuery.lastModified[url]||jQuery.browser.safari&&xhr.status==undefined;}catch(e){}return false;},httpData:function(xhr,type,filter){var ct=xhr.getResponseHeader("content-type"),xml=type=="xml"||!type&&ct&&ct.indexOf("xml")>=0,data=xml?xhr.responseXML:xhr.responseText;if (xml&&data.documentElement.tagName=="parsererror")throw"parsererror";if (filter)data=filter(data,type);if (type=="script")jQuery.globalEval(data);if (type=="json")data=eval("("+data+")");return data;},param:function(a){var s=[];if (a.constructor==Array||a.jquery)jQuery.each(a,function(){s.push(encodeURIComponent(this.name)+"="+encodeURIComponent(this.value));});else
for(var j in a)if (a[j]&&a[j].constructor==Array)jQuery.each(a[j],function(){s.push(encodeURIComponent(j)+"="+encodeURIComponent(this));});else
s.push(encodeURIComponent(j)+"="+encodeURIComponent(jQuery.isFunction(a[j])?a[j]():a[j]));return s.join("&").replace(/%20/g,"+");}});jQuery.fn.extend({show:function(speed,callback){return speed?this.animate({height:"show",width:"show",opacity:"show"},speed,callback):this.filter(":hidden").each(function(){this.style.display=this.oldblock||"";if (jQuery.css(this,"display")=="none"){var elem=jQuery("<"+this.tagName+" />").appendTo("body");this.style.display=elem.css("display");if (this.style.display=="none")this.style.display="block";elem.remove();}}).end();},hide:function(speed,callback){return speed?this.animate({height:"hide",width:"hide",opacity:"hide"},speed,callback):this.filter(":visible").each(function(){this.oldblock=this.oldblock||jQuery.css(this,"display");this.style.display="none";}).end();},_toggle:jQuery.fn.toggle,toggle:function(fn,fn2){return jQuery.isFunction(fn)&&jQuery.isFunction(fn2)?this._toggle.apply(this,arguments):fn?this.animate({height:"toggle",width:"toggle",opacity:"toggle"},fn,fn2):this.each(function(){jQuery(this)[jQuery(this).is(":hidden")?"show":"hide"]();});},slideDown:function(speed,callback){return this.animate({height:"show"},speed,callback);},slideUp:function(speed,callback){return this.animate({height:"hide"},speed,callback);},slideToggle:function(speed,callback){return this.animate({height:"toggle"},speed,callback);},fadeIn:function(speed,callback){return this.animate({opacity:"show"},speed,callback);},fadeOut:function(speed,callback){return this.animate({opacity:"hide"},speed,callback);},fadeTo:function(speed,to,callback){return this.animate({opacity:to},speed,callback);},animate:function(prop,speed,easing,callback){var optall=jQuery.speed(speed,easing,callback);return this[optall.queue===false?"each":"queue"](function(){if (this.nodeType!=1)return false;var opt=jQuery.extend({},optall),p,hidden=jQuery(this).is(":hidden"),self=this;for(p in prop){if (prop[p]=="hide"&&hidden||prop[p]=="show"&&!hidden)return opt.complete.call(this);if (p=="height"||p=="width"){opt.display=jQuery.css(this,"display");opt.overflow=this.style.overflow;}}if (opt.overflow!=null)this.style.overflow="hidden";opt.curAnim=jQuery.extend({},prop);jQuery.each(prop,function(name,val){var e=new jQuery.fx(self,opt,name);if (/toggle|show|hide/.test(val))e[val=="toggle"?hidden?"show":"hide":val](prop);else{var parts=val.toString().match(/^([+-]=)?([\d+-.]+)(.*)$/),start=e.cur(true)||0;if (parts){var end=parseFloat(parts[2]),unit=parts[3]||"px";if (unit!="px"){self.style[name]=(end||1)+unit;start=((end||1)/e.cur(true))*start;self.style[name]=start+unit;}if (parts[1])end=((parts[1]=="-="?-1:1)*end)+start;e.custom(start,end,unit);}else
e.custom(start,val,"");}});return true;});},queue:function(type,fn){if (jQuery.isFunction(type)||(type&&type.constructor==Array)){fn=type;type="fx";}if (!type||(typeof type=="string"&&!fn))return queue(this[0],type);return this.each(function(){if (fn.constructor==Array)queue(this,type,fn);else{queue(this,type).push(fn);if (queue(this,type).length==1)fn.call(this);}});},stop:function(clearQueue,gotoEnd){var timers=jQuery.timers;if (clearQueue)this.queue([]);this.each(function(){for(var i=timers.length-1;i>=0;i--)if (timers[i].elem==this){if (gotoEnd)timers[i](true);timers.splice(i,1);}});if (!gotoEnd)this.dequeue();return this;}});var queue=function(elem,type,array){if (elem){type=type||"fx";var q=jQuery.data(elem,type+"queue");if (!q||array)q=jQuery.data(elem,type+"queue",jQuery.makeArray(array));}return q;};jQuery.fn.dequeue=function(type){type=type||"fx";return this.each(function(){var q=queue(this,type);q.shift();if (q.length)q[0].call(this);});};jQuery.extend({speed:function(speed,easing,fn){var opt=speed&&speed.constructor==Object?speed:{complete:fn||!fn&&easing||jQuery.isFunction(speed)&&speed,duration:speed,easing:fn&&easing||easing&&easing.constructor!=Function&&easing};opt.duration=(opt.duration&&opt.duration.constructor==Number?opt.duration:jQuery.fx.speeds[opt.duration])||jQuery.fx.speeds.def;opt.old=opt.complete;opt.complete=function(){if (opt.queue!==false)jQuery(this).dequeue();if (jQuery.isFunction(opt.old))opt.old.call(this);};return opt;},easing:{linear:function(p,n,firstNum,diff){return firstNum+diff*p;},swing:function(p,n,firstNum,diff){return((-Math.cos(p*Math.PI)/2)+0.5)*diff+firstNum;}},timers:[],timerId:null,fx:function(elem,options,prop){this.options=options;this.elem=elem;this.prop=prop;if (!options.orig)options.orig={};}});jQuery.fx.prototype={update:function(){if (this.options.step)this.options.step.call(this.elem,this.now,this);(jQuery.fx.step[this.prop]||jQuery.fx.step._default)(this);if (this.prop=="height"||this.prop=="width")this.elem.style.display="block";},cur:function(force){if (this.elem[this.prop]!=null&&this.elem.style[this.prop]==null)return this.elem[this.prop];var r=parseFloat(jQuery.css(this.elem,this.prop,force));return r&&r>-10000?r:parseFloat(jQuery.curCSS(this.elem,this.prop))||0;},custom:function(from,to,unit){this.startTime=now();this.start=from;this.end=to;this.unit=unit||this.unit||"px";this.now=this.start;this.pos=this.state=0;this.update();var self=this;function t(gotoEnd){return self.step(gotoEnd);}t.elem=this.elem;jQuery.timers.push(t);if (jQuery.timerId==null){jQuery.timerId=setInterval(function(){var timers=jQuery.timers;for(var i=0;i<timers.length;i++)if (!timers[i]())timers.splice(i--,1);if (!timers.length){clearInterval(jQuery.timerId);jQuery.timerId=null;}},13);}},show:function(){this.options.orig[this.prop]=jQuery.attr(this.elem.style,this.prop);this.options.show=true;this.custom(0,this.cur());if (this.prop=="width"||this.prop=="height")this.elem.style[this.prop]="1px";jQuery(this.elem).show();},hide:function(){this.options.orig[this.prop]=jQuery.attr(this.elem.style,this.prop);this.options.hide=true;this.custom(this.cur(),0);},step:function(gotoEnd){var t=now();if (gotoEnd||t>this.options.duration+this.startTime){this.now=this.end;this.pos=this.state=1;this.update();this.options.curAnim[this.prop]=true;var done=true;for(var i in this.options.curAnim)if (this.options.curAnim[i]!==true)done=false;if (done){if (this.options.display!=null){this.elem.style.overflow=this.options.overflow;this.elem.style.display=this.options.display;if (jQuery.css(this.elem,"display")=="none")this.elem.style.display="block";}if (this.options.hide)this.elem.style.display="none";if (this.options.hide||this.options.show)for(var p in this.options.curAnim)jQuery.attr(this.elem.style,p,this.options.orig[p]);}if (done)this.options.complete.call(this.elem);return false;}else{var n=t-this.startTime;this.state=n/this.options.duration;this.pos=jQuery.easing[this.options.easing||(jQuery.easing.swing?"swing":"linear")](this.state,n,0,1,this.options.duration);this.now=this.start+((this.end-this.start)*this.pos);this.update();}return true;}};jQuery.extend(jQuery.fx,{speeds:{slow:600,fast:200,def:400},step:{scrollLeft:function(fx){fx.elem.scrollLeft=fx.now;},scrollTop:function(fx){fx.elem.scrollTop=fx.now;},opacity:function(fx){jQuery.attr(fx.elem.style,"opacity",fx.now);},_default:function(fx){fx.elem.style[fx.prop]=fx.now+fx.unit;}}});jQuery.fn.offset=function(){var left=0,top=0,elem=this[0],results;if (elem)with(jQuery.browser){var parent=elem.parentNode,offsetChild=elem,offsetParent=elem.offsetParent,doc=elem.ownerDocument,safari2=safari&&parseInt(version)<522&&!/adobeair/i.test(userAgent),css=jQuery.curCSS,fixed=css(elem,"position")=="fixed";if (elem.getBoundingClientRect){var box=elem.getBoundingClientRect();add(box.left+Math.max(doc.documentElement.scrollLeft,doc.body.scrollLeft),box.top+Math.max(doc.documentElement.scrollTop,doc.body.scrollTop));add(-doc.documentElement.clientLeft,-doc.documentElement.clientTop);}else{add(elem.offsetLeft,elem.offsetTop);while(offsetParent){add(offsetParent.offsetLeft,offsetParent.offsetTop);if (mozilla&&!/^t(able|d|h)$/i.test(offsetParent.tagName)||safari&&!safari2)border(offsetParent);if (!fixed&&css(offsetParent,"position")=="fixed")fixed=true;offsetChild=/^body$/i.test(offsetParent.tagName)?offsetChild:offsetParent;offsetParent=offsetParent.offsetParent;}while(parent&&parent.tagName&&!/^body|html$/i.test(parent.tagName)){if (!/^inline|table.*$/i.test(css(parent,"display")))add(-parent.scrollLeft,-parent.scrollTop);if (mozilla&&css(parent,"overflow")!="visible")border(parent);parent=parent.parentNode;}if ((safari2&&(fixed||css(offsetChild,"position")=="absolute"))||(mozilla&&css(offsetChild,"position")!="absolute"))add(-doc.body.offsetLeft,-doc.body.offsetTop);if (fixed)add(Math.max(doc.documentElement.scrollLeft,doc.body.scrollLeft),Math.max(doc.documentElement.scrollTop,doc.body.scrollTop));}results={top:top,left:left};}function border(elem){add(jQuery.curCSS(elem,"borderLeftWidth",true),jQuery.curCSS(elem,"borderTopWidth",true));}function add(l,t){left+=parseInt(l,10)||0;top+=parseInt(t,10)||0;}return results;};jQuery.fn.extend({position:function(){var left=0,top=0,results;if (this[0]){var offsetParent=this.offsetParent(),offset=this.offset(),parentOffset=/^body|html$/i.test(offsetParent[0].tagName)?{top:0,left:0}:offsetParent.offset();offset.top-=num(this,'marginTop');offset.left-=num(this,'marginLeft');parentOffset.top+=num(offsetParent,'borderTopWidth');parentOffset.left+=num(offsetParent,'borderLeftWidth');results={top:offset.top-parentOffset.top,left:offset.left-parentOffset.left};}return results;},offsetParent:function(){var offsetParent=this[0].offsetParent;while(offsetParent&&(!/^body|html$/i.test(offsetParent.tagName)&&jQuery.css(offsetParent,'position')=='static'))offsetParent=offsetParent.offsetParent;return jQuery(offsetParent);}});jQuery.each(['Left','Top'],function(i,name){var method='scroll'+name;jQuery.fn[method]=function(val){if (!this[0])return;return val!=undefined?this.each(function(){this==window||this==document?window.scrollTo(!i?val:jQuery(window).scrollLeft(),i?val:jQuery(window).scrollTop()):this[method]=val;}):this[0]==window||this[0]==document?self[i?'pageYOffset':'pageXOffset']||jQuery.boxModel&&document.documentElement[method]||document.body[method]:this[0][method];};});jQuery.each(["Height","Width"],function(i,name){var tl=i?"Left":"Top",br=i?"Right":"Bottom";jQuery.fn["inner"+name]=function(){return this[name.toLowerCase()]()+num(this,"padding"+tl)+num(this,"padding"+br);};jQuery.fn["outer"+name]=function(margin){return this["inner"+name]()+num(this,"border"+tl+"Width")+num(this,"border"+br+"Width")+(margin?num(this,"margin"+tl)+num(this,"margin"+br):0);};});})();
jQuery.extend(jQuery.easing,{easeOutBounce:function(x,t,b,c,d){if ((t/=d)<(1/2.75)){return c*(7.5625*t*t)+b;}else if (t<(2/2.75)){return c*(7.5625*(t-=(1.5/2.75))*t+.75)+b;}else if (t<(2.5/2.75)){return c*(7.5625*(t-=(2.25/2.75))*t+.9375)+b;}else{return c*(7.5625*(t-=(2.625/2.75))*t+.984375)+b;}}});
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if (!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if (k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('(3($){$.24.T=3(f,g){E k,v,A,K;v=A=K=7;k={C:\'\',12:\'\',U:\'\',1j:\'\',1A:o,25:\'26\',1k:\'~/2Q/1B.1C\',1b:\'\',27:\'28\',1l:o,1D:\'\',1E:\'\',1F:{},1G:{},1H:{},1I:{},29:[{}]};$.V(k,f,g);2(!k.U){$(\'2R\').1c(3(a,b){1J=$(b).14(0).2S.2T(/(.*)2U\\.2V(\\.2W)?\\.2X$/);2(1J!==2a){k.U=1J[1]}})}4 F.1c(3(){E d,u,15,16,p,G,L,P,17,1m,w,1n,M,18;d=$(F);u=F;15=[];18=7;16=p=0;G=-1;k.1b=1d(k.1b);k.1k=1d(k.1k);3 1d(a,b){2(b){4 a.W(/("|\')~\\//g,"$1"+k.U)}4 a.W(/^~\\//,k.U)}3 2b(){C=\'\';12=\'\';2(k.C){C=\'C="\'+k.C+\'"\'}8 2(d.1K("C")){C=\'C="T\'+(d.1K("C").2c(0,1).2Y())+(d.1K("C").2c(1))+\'"\'}2(k.12){12=\'N="\'+k.12+\'"\'}d.1L(\'<z \'+12+\'"></z>\');d.1L(\'<z \'+C+\' N="T"></z>\');d.1L(\'<z N="2Z"></z>\');d.2d("2e");17=$(\'<z N="30"></z>\').2f(d);$(1M(k.29)).1N(17);1m=$(\'<z N="31"></z>\').1O(d);2(k.1l===o&&$.X.32!==o){1l=$(\'<z N="33"></z>\').1O(d).1e("34",3(e){E h=d.2g(),y=e.2h,1o,1p;1o=3(e){d.2i("2g",35.36(20,e.2h+h-y)+"37");4 7};1p=3(e){$("1C").1P("2j",1o).1P("1q",1p);4 7};$("1C").1e("2j",1o).1e("1q",1p)});1m.2k(1l)}d.2l(1Q).38(1Q);d.1e("1R",3(e,a){2(a.1r!==7){14()}2(u===$.T.2m){Y(a)}});d.1f(3(){$.T.2m=F})}3 1M(b){E c=$(\'<Z></Z>\'),i=0;$(\'B:2n > Z\',c).2i(\'39\',\'q\');$(b).1c(3(){E a=F,t=\'\',1s,B,j;1s=(a.19)?(a.1S||\'\')+\' [3a+\'+a.19+\']\':(a.1S||\'\');19=(a.19)?\'2o="\'+a.19+\'"\':\'\';2(a.2p){B=$(\'<B N="3b">\'+(a.2p||\'\')+\'</B>\').1N(c)}8{i++;2q(j=15.6-1;j>=0;j--){t+=15[j]+"-"}B=$(\'<B N="2r 2r\'+t+(i)+\' \'+(a.3c||\'\')+\'"><a 3d="" \'+19+\' 1s="\'+1s+\'">\'+(a.1S||\'\')+\'</a></B>\').1e("3e",3(){4 7}).2s(3(){4 7}).1q(3(){2(a.2t){3f(a.2t)()}Y(a);4 7}).2n(3(){$(\'> Z\',F).3g();$(D).3h(\'2s\',3(){$(\'Z Z\',17).2u()})},3(){$(\'> Z\',F).2u()}).1N(c);2(a.2v){15.3i(i);$(B).2d(\'3j\').2k(1M(a.2v))}}});15.3k();4 c}3 2w(c){2(c){c=c.3l();c=c.W(/\\(\\!\\(([\\s\\S]*?)\\)\\!\\)/g,3(x,a){E b=a.1T(\'|!|\');2(K===o){4(b[1]!==2x)?b[1]:b[0]}8{4(b[1]===2x)?"":b[0]}});c=c.W(/\\[\\!\\[([\\s\\S]*?)\\]\\!\\]/g,3(x,a){E b=a.1T(\':!:\');2(18===o){4 7}1U=3m(b[0],(b[1])?b[1]:\'\');2(1U===2a){18=o}4 1U});4 c}4""}3 H(a){2($.3n(a)){a=a(P)}4 2w(a)}3 1g(a){I=H(L.I);1a=H(L.1a);Q=H(L.Q);O=H(L.O);2(Q!==""){q=I+Q+O}8 2(l===\'\'&&1a!==\'\'){q=I+1a+O}8{q=I+(a||l)+O}4{q:q,I:I,Q:Q,1a:1a,O:O}}3 Y(a){E b,j,n,i;P=L=a;14();$.V(P,{1t:"",U:k.U,u:u,l:(l||\'\'),p:p,v:v,A:A,K:K});H(k.1D);H(L.1D);2(v===o&&A===o){H(L.3o)}$.V(P,{1t:1});2(v===o&&A===o){R=l.1T(/\\r?\\n/);2q(j=0,n=R.6,i=0;i<n;i++){2($.3p(R[i])!==\'\'){$.V(P,{1t:++j,l:R[i]});R[i]=1g(R[i]).q}8{R[i]=""}}m={q:R.3q(\'\\n\')};11=p;b=m.q.6+(($.X.1V)?n:0)}8 2(v===o){m=1g(l);11=p+m.I.6;b=m.q.6-m.I.6-m.O.6;b-=1u(m.q)}8 2(A===o){m=1g(l);11=p;b=m.q.6;b-=1u(m.q)}8{m=1g(l);11=p+m.q.6;b=0;11-=1u(m.q)}2((l===\'\'&&m.Q===\'\')){G+=1W(m.q);11=p+m.I.6;b=m.q.6-m.I.6-m.O.6;G=d.J().1h(p,d.J().6).6;G-=1W(d.J().1h(0,p))}$.V(P,{p:p,16:16});2(m.q!==l&&18===7){2y(m.q);1X(11,b)}8{G=-1}14();$.V(P,{1t:\'\',l:l});2(v===o&&A===o){H(L.3r)}H(L.1E);H(k.1E);2(w&&k.1A){1Y()}A=K=v=18=7}3 1W(a){2($.X.1V){4 a.6-a.W(/\\n*/g,\'\').6}4 0}3 1u(a){2($.X.2z){4 a.6-a.W(/\\r*/g,\'\').6}4 0}3 2y(a){2(D.l){E b=D.l.1Z();b.2A=a}8{d.J(d.J().1h(0,p)+a+d.J().1h(p+l.6,d.J().6))}}3 1X(a,b){2(u.2B){2($.X.1V&&$.X.3s>=9.5&&b==0){4 7}1i=u.2B();1i.3t(o);1i.2C(\'21\',a);1i.3u(\'21\',b);1i.3v()}8 2(u.2D){u.2D(a,a+b)}u.1v=16;u.1f()}3 14(){u.1f();16=u.1v;2(D.l){l=D.l.1Z().2A;2($.X.2z){E a=D.l.1Z(),1w=a.3w();1w.3x(u);p=-1;3y(1w.3z(a)){1w.2C(\'21\');p++}}8{p=u.2E}}8{p=u.2E;l=d.J().1h(p,u.3A)}4 l}3 1B(){2(!w||w.3B){2(k.1j){w=3C.2F(\'\',\'1B\',k.1j)}8{M=$(\'<2G N="3D"></2G>\');2(k.25==\'26\'){M.1O(1m)}8{M.2f(17)}w=M[M.6-1].3E||3F[M.6-1]}}8 2(K===o){2(M){M.3G()}w.2H();w=M=7}2(!k.1A){1Y()}}3 1Y(){2(w.D){3H{22=w.D.2I.1v}3I(e){22=0}w.D.2F();w.D.3J(2J());w.D.2H();w.D.2I.1v=22}2(k.1j){w.1f()}}3 2J(){2(k.1b!==\'\'){$.2K({2L:\'3K\',2M:7,2N:k.1b,28:k.27+\'=\'+3L(d.J()),2O:3(a){23=1d(a,1)}})}8{2(!1n){$.2K({2M:7,2N:k.1k,2O:3(a){1n=1d(a,1)}})}23=1n.W(/<!-- 3M -->/g,d.J())}4 23}3 1Q(e){A=e.A;K=e.K;v=(!(e.K&&e.v))?e.v:7;2(e.2L===\'2l\'){2(v===o){B=$("a[2o="+3N.3O(e.1x)+"]",17).1y(\'B\');2(B.6!==0){v=7;B.3P(\'1q\');4 7}}2(e.1x===13||e.1x===10){2(v===o){v=7;Y(k.1H);4 k.1H.1z}8 2(A===o){A=7;Y(k.1G);4 k.1G.1z}8{Y(k.1F);4 k.1F.1z}}2(e.1x===9){2(G!==-1){14();G=d.J().6-G;1X(G,0);G=-1;4 7}8{Y(k.1I);4 k.1I.1z}}}}2b()})};$.24.3Q=3(){4 F.1c(3(){$$=$(F).1P().3R(\'2e\');$$.1y(\'z\').1y(\'z.T\').1y(\'z\').Q($$)})};$.T=3(a){E b={1r:7};$.V(b,a);2(b.1r){4 $(b.1r).1c(3(){$(F).1f();$(F).2P(\'1R\',[b])})}8{$(\'u\').2P(\'1R\',[b])}}})(3S);',62,241,'||if|function|return||length|false|else|||||||||||||selection|string||true|caretPosition|block||||textarea|ctrlKey|previewWindow|||div|shiftKey|li|id|document|var|this|caretOffset|prepare|openWith|val|altKey|clicked|iFrame|class|closeWith|hash|replaceWith|lines||markItUp|root|extend|replace|browser|markup|ul||start|nameSpace||get|levels|scrollPosition|header|abort|key|placeHolder|previewParserPath|each|localize|bind|focus|build|substring|range|previewInWindow|previewTemplatePath|resizeHandle|footer|template|mouseMove|mouseUp|mouseup|target|title|line|fixIeBug|scrollTop|rangeCopy|keyCode|parent|keepDefault|previewAutoRefresh|preview|html|beforeInsert|afterInsert|onEnter|onShiftEnter|onCtrlEnter|onTab|miuScript|attr|wrap|dropMenus|appendTo|insertAfter|unbind|keyPressed|insertion|name|split|value|opera|fixOperaBug|set|refreshPreview|createRange||character|sp|phtml|fn|previewPosition|after|previewParserVar|data|markupSet|null|init|substr|addClass|markItUpEditor|insertBefore|height|clientY|css|mousemove|append|keydown|focused|hover|accesskey|separator|for|markItUpButton|click|call|hide|dropMenu|magicMarkups|undefined|insert|msie|text|createTextRange|moveStart|setSelectionRange|selectionStart|open|iframe|close|documentElement|renderPreview|ajax|type|async|url|success|trigger|templates|script|src|match|jquery|markitup|pack|js|toUpperCase|markItUpContainer|markItUpHeader|markItUpFooter|safari|markItUpResizeHandle|mousedown|Math|max|px|keyup|display|Ctrl|markItUpSeparator|className|href|contextmenu|eval|show|one|push|markItUpDropMenu|pop|toString|prompt|isFunction|beforeMultiInsert|trim|join|afterMultiInsert|version|collapse|moveEnd|select|duplicate|moveToElementText|while|inRange|selectionEnd|closed|window|markItUpPreviewFrame|contentWindow|frame|remove|try|catch|write|POST|encodeURIComponent|content|String|fromCharCode|triggerHandler|markItUpRemove|removeClass|jQuery'.split('|'),0,{}))
$(document).ready(function(){print_editor();});


function foco(elemento) {
  elemento.style.border = "1px solid #76C8FF";
}

function no_foco(elemento) {
  elemento.style.border="1px solid #DAE8F8";
}

/*Votar posts*/
function votar_post(id, puntos) {
  $('#cargando_opciones').css('display', 'block');
  $('#cargando_opciones2').css('display', 'none');

  $.ajax({
    type: 'GET',
    url: boardurl + '/votar-post/',
    data: 'post=' + id + '&puntos=' + puntos,
    success: function(h) {
      $('#cargando_opciones').css('display', 'none');
      $("#span_opciones1").slideUp("slow", function() {
        // Error
        if (h.charAt(0) == 0) {
        $('#span_opciones1').addClass('status_error');
        } else if (h.charAt(0)==1) { // OK
          $('#span_opciones1').addClass('status_ok');
          $('#cant_pts_post_dos').html(parseInt($('#cant_pts_post_dos').html()) + parseInt(puntos));
          $('#cant_pts_post').html(parseInt($('#cant_pts_post').html()) + parseInt(puntos));
        }

        $('#span_opciones1').css('text-align', 'center');
        $('#span_opciones1').removeClass('size10');
        $('#span_opciones1').html(h.substring(3));
        $("#span_opciones1").slideDown("slow");
      });
    },
    error: function() {
      $('#span_opciones1').addClass('status_error');
      $('#span_opciones1').css('text-align', 'center');
      $('#span_opciones1').removeClass('size10');
      $('#span_opciones1').html('Error al intentar procesar lo solicitado');
      $("#span_opciones1").slideDown("slow");
    }
  });
}
/* Votar img */
function votar_img(id, puntos) {
  $('#cargando_opciones').css('display', 'block');
  $('#cargando_opciones2').css('display', 'none');
  $.ajax({
    type: 'GET',
    url: boardurl + '/votar-img/',
    data: 'imagen=' + id + '&puntos=' + puntos,
    success: function(h) {
      $('#cargando_opciones').css('display', 'none');
      $("#span_opciones1").slideUp("slow", function() {
        // Error
        if (h.charAt(0) == 0) {
          $('#span_opciones1').addClass('status_error');
        } else if (h.charAt(0) == 1) { // OK
          $('#span_opciones1').addClass('status_ok');
          $('#cant_pts_post_dos').html(parseInt($('#cant_pts_post_dos').html()) + parseInt(puntos));
          $('#cant_pts_post').html(parseInt($('#cant_pts_post').html()) + parseInt(puntos));
        }

        $('#span_opciones1').css('text-align', 'center');
        $('#span_opciones1').removeClass('size10');
        $('#span_opciones1').html(h.substring(3));
        $("#span_opciones1").slideDown("slow");
      });
    },
    error: function() {
      $('#span_opciones1').addClass('status_error');
      $('#span_opciones1').css('text-align', 'center');
      $('#span_opciones1').removeClass('size10');
      $('#span_opciones1').html('Error al intentar procesar lo solicitado');
      $("#span_opciones1").slideDown("slow");
    }
  });
}

/* Agregar post a favoritos */
function add_favoritos(id) {
  $('#cargando_opciones').css('display', 'block');
  $('#cargando_opciones2').css('display', 'none');
  $.ajax({
    type: 'GET',
    url: boardurl + '/favs-agregar.php',
    data: 'tipo=posts&post=' + id,
    success: function(h) {
      var original = $('#span_opciones2').html();

      $('#cargando_opciones').css('display', 'none');
      $("#span_opciones2").slideUp("slow", function() {
        if (h.charAt(0) == 0) {
          $('#span_opciones2').addClass('status_error');
        } else if (h.charAt(0) == 1) {
          $('#cant_favs_post').html(parseInt($('#cant_favs_post').html()) + 1);
          $('#span_opciones2').addClass('status_ok');
        }

        $('#span_opciones2').html(h.substring(3));
        $("#span_opciones2").slideDown("slow", function() {
          if (h.charAt(0) == 1) {
            sleep(1000);
          } else {
            sleep(2500);
          }

          $("#span_opciones2").slideUp("slow", function() {
            $('#span_opciones2').removeClass('status_error');
            $('#span_opciones2').removeClass('status_ok');
            $('#span_opciones2').html(original);
            $("#span_opciones2").slideDown("slow");
          });
        });
      });
    },
    error: function() {
      alert('Error al intentar procesar lo solicitado');
    }
  });
}

function add_favoritos_img(id) {
  $('#cargando_opciones').css('display', 'block');
  $('#cargando_opciones2').css('display', 'none');
  $.ajax({
    type: 'GET',
    url: boardurl + '/favs-agregar.php',
    data: 'tipo=imagen&post=' + id,
    success: function(h) {
      var original = $('#span_opciones2').html();

      $('#cargando_opciones').css('display', 'none');
      $("#span_opciones2").slideUp("slow", function() {
        if (h.charAt(0) == 0) {
          $('#span_opciones2').addClass('status_error');
        } else if (h.charAt(0) == 1) {
          $('#cant_favs_post').html(parseInt($('#cant_favs_post').html()) + 1);
          $('#span_opciones2').addClass('status_ok');
        }

        $('#span_opciones2').html(h.substring(3));
        $("#span_opciones2").slideDown("slow", function() {
          if (h.charAt(0) == 1) {
            sleep(1000);
          } else {
            sleep(2500);
          }

          $("#span_opciones2").slideUp("slow", function() {
            $('#span_opciones2').removeClass('status_error');
            $('#span_opciones2').removeClass('status_ok');
            $('#span_opciones2').html(original);
            $("#span_opciones2").slideDown("slow");
          });
        });
      });
    },
    error: function() {
      alert('Error al intentar procesar lo solicitado');
    }
  });
}

// Agregar comentario post
function add_comment(id) {
  if ($('#cuerpo_comment').val() == '') {
    $('#cuerpo_comment').focus();
    return;
  }

  $('.msg_add_comment').hide();
  $('#button_add_comment').attr('disabled', 'true');
  $('#gif_cargando_add_comment').css('display', 'block');
  $('#gif_cargando_add_comment2').css('display', 'none');
  $.ajax({
    type: 'POST',
    url: boardurl + '/comentario/enviar/',
    data: 'cuerpo_comment=' + encodeURIComponent($('#cuerpo_comment').val()) + '&id=' + id,
    success: function(h) {
      $('#gif_cargando_add_comment').css('display', 'none');

      if (h.charAt(0) == 0) {
        $('.msg_add_comment').html(h.substring(3));
        $('.msg_add_comment').addClass('noesta');
        $('.msg_add_comment').css('margin-bottom', '8px');
        $('.msg_add_comment').show('slow');
        $('#button_add_comment').removeAttr('disabled');
      } else if (h.charAt(0) == 1) {
        $('#return_agregar_comentario').html(h.substring(3));
        $('#cuerpo_comment').val('');
        $('#return_agregar_comentario').slideDown('slow', function() {
          $('.agregar_comentario').slideUp('slow');
        });

        if ($('#no_comentarios')) {
          $('#no_comentarios').slideUp('slow');
        }
      }
    },
    error: function() {
      $('#gif_cargando_add_comment').css('display', 'none');
      $('.msg_add_comment').html('Error al intentar procesar lo solicitado');
      $('.msg_add_comment').show('slow');
      $('#button_add_comment').removeAttr('disabled');
    }
  });
}

// Agregar comentario imagen
function add_comment_img(id) {
  if ($('#cuerpo_comment').val() == '') {
    $('#cuerpo_comment').focus();
    return;
  }

  $('.msg_add_comment').hide();
  $('#button_add_comment').attr('disabled', 'true');
  $('#gif_cargando_add_comment').css('display', 'block');
  $('#gif_cargando_add_comment2').css('display', 'none');

  $.ajax({
    type: 'POST',
    url: boardurl + '/comentario-img/enviar/',
    data: 'cuerpo_comment=' + encodeURIComponent($('#cuerpo_comment').val()) + '&id=' + id,
    success: function(h) {
      $('#gif_cargando_add_comment').css('display', 'none');

      if (h.charAt(0) == 0) {
        $('.msg_add_comment').html(h.substring(3));
        $('.msg_add_comment').addClass('noesta');
        $('.msg_add_comment').css('margin-bottom', '8px');
        $('.msg_add_comment').show('slow');
        $('#button_add_comment').removeAttr('disabled');
      } else if (h.charAt(0) == 1) {
        $('#return_agregar_comentario').html(h.substring(3));
        $('#cuerpo_comment').val('');
        $('#return_agregar_comentario').slideDown('slow', function() {
          $('.agregar_comentario').slideUp('slow');
        });

        if ($('#no_comentarios')) {
          $('#no_comentarios').slideUp('slow');
        }
      }
    },
    error: function() {
      $('#gif_cargando_add_comment').css('display', 'none');
      $('.msg_add_comment').html('Error al intentar procesar lo solicitado');
      $('.msg_add_comment').show('slow');
      $('#button_add_comment').removeAttr('disabled');
    }
  });
}

function ignorar(id) {
  $('#gif_cargando_ign').show('fast');

  $.ajax({
    type: 'GET',
    url: boardurl + '/permitir-mp/',
    data: 'action=eliminar&user=' + id,
    success: function(h) {
      $('#gif_cargando_ign').hide();

      if (h.charAt(0) == 0) {
        alert(h.substring(3));
      } else {
        $('#ac_no').css('display', 'none');
        $('#ac_no3').css('display', 'none');
        $('#ac_no2').css('display', 'block');
      }
    },
    error: function() {
      $('#gif_cargando_ign').hide();
      alert('Error al intentar procesar lo solicitado');
    }
  });
}

function ignorar2(id) {
  $('#gif_cargando_ign').show('fast');

  $.ajax({
    type: 'GET',
    url: boardurl + '/permitir-mp/',
    data: 'action=agregar&user=' + id,
    success: function(h) {
      $('#gif_cargando_ign').hide();

      if (h.charAt(0) == 0) {
        alert(h.substring(3));
      } else {
        $('#ac_no').css('display', 'none');
        $('#ac_no2').css('display', 'none');
        $('#ac_no3').css('display', 'block');
      }
    },
    error: function() {
      $('#gif_cargando_ign').hide();
      alert('Error al intentar procesar lo solicitado');
    }
  });
}

/* Agregar comentario muro */
function add_muro(id) {
  if ($('#muro').val() == '') {
    $('#muro').focus();
    return;
  }

  $('.msg_add_muro').hide();
  $('#button_add_muro').attr('disabled', 'true');
  $('#gif_cargando_add_muro').css('display', 'block');

  $.ajax({
    type: 'POST',
    url: boardurl + '/enviar-muro/',
    data: 'muro=' + encodeURIComponent($('#muro').val()) + '&user=' + id,
    success: function(h) {
      $('#gif_cargando_add_muro').css('display', 'none');

      if (h.charAt(0) == 0) {
        $('.msg_add_muro').html(h.substring(3));
        $('.msg_add_muro').addClass('noesta');
        $('.msg_add_muro').show('slow');
        $('#button_add_muro').removeAttr('disabled');
      } else {
        $('#return_agregar_muro').html(h.substring(3));
        $('#return_agregar_muro').slideDown('slow');
        $('#cantmuro').html(parseInt($('#cantmuro').html()) + 1);
        $('#button_add_muro').removeAttr('enable');
        $('#muro').val('Escribe algo...');

        if ($('#no_muro')) {
          $('#no_muro').slideUp('slow');
        }
      }
    },
    error: function() {
      $('#gif_cargando_add_muro').css('display', 'none');
      $('.msg_add_muro').html('Error al intentar procesar lo solicitado');
      $('.msg_add_muro').show('slow');
      $('#button_add_muro').removeAttr('disabled');
    }
  });
}

function add_quehago(id) {
  if ($('#quehago').val() == '') {
    $('#quehago').focus();
    return;
  }

  $('.msg_add_muro').hide();
  $('#button_add_muro').attr('disabled', 'true');
  $('#gif_cargando_add_muro').css('display', 'block');

  $.ajax({
    type: 'POST',
    url: boardurl + '/enviar-quehago/',
    data: 'quehago=' + encodeURIComponent($('#quehago').val()),
    success: function(h) {
      $('#gif_cargando_add_muro').css('display', 'none');

      if (h.charAt(0) == 0) {
        $('.msg_add_muro').html(h.substring(3));
        $('.msg_add_muro').addClass('noesta');
        $('.msg_add_muro').show('slow');
        $('#button_add_muro').removeAttr('disabled');
      } else {
        $('#return_agregar_muro').html(h.substring(3));
        $('#return_agregar_muro').slideDown('slow');
        $('#cantmuro').html(parseInt($('#cantmuro').html()) + 1);
        $('#button_add_muro').removeAttr('enable');
        $('#quehago').val('\xbfQu\u00E9 est\u00E1s haciendo ahora?');

        if ($('#no_muro')) {
          $('#no_muro').slideUp('slow');
        }
      }
    },
    error: function() {
      $('#gif_cargando_add_muro').css('display', 'none');
      $('.msg_add_muro').html('Error al intentar procesar lo solicitado');
      $('.msg_add_muro').show('slow');
      $('#button_add_muro').removeAttr('disabled');
    }
  });
}

function objetoAjax() {
  var xmlhttp = false;

  try {
    xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
  } catch(e) {
    try {
      xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    } catch(e) {
      xmlhttp = false;
    }
  }

  if (!xmlhttp && typeof XMLHttpRequest!='undefined') {
    xmlhttp = new XMLHttpRequest();
  }

  return xmlhttp;
}

function comentarioscom(tema, nropagina) {
  divContenido = document.getElementById('comentarios');
  divContenido2 = document.getElementById('carando');
  ajax = objetoAjax();
  ajax.open("GET", boardurl + "/web/tp-comunidadesComenCar.php?tema=" + tema + "&pag=" + nropagina);
  divContenido2.style.display = 'block';
  ajax.onreadystatechange = function() {
    if (ajax.readyState == 4) {
      divContenido2.style.display = 'none';
      divContenido.innerHTML = ajax.responseText;
    }
  }

  ajax.send(null);
}
 
function ComuMemPerfil(user, pag) {
  divContenido = document.getElementById('ComuMemPerfil');
  ajax = objetoAjax();
  ajax.open("GET", boardurl + "/web/tp-ComuMemPerfil.php?user=" + user + "&pag=" + pag);
  ajax.onreadystatechange = function() {
    if (ajax.readyState == 4) {
      divContenido.innerHTML = ajax.responseText;
    }
  }

  ajax.send(null);
}
 
function ComuTemPerfil(user, pag) {
  divContenido = document.getElementById('ComuTemPerfil');
  ajax = objetoAjax();
  ajax.open("GET", boardurl + "/web/tp-ComuTemPerfil.php?user=" + user + "&pag=" + pag);
  ajax.onreadystatechange = function() {
    if (ajax.readyState == 4) {
      divContenido.innerHTML = ajax.responseText;
    }
  }

  ajax.send(null);
}

function ComuCrePerfil(user, pag) {
  divContenido = document.getElementById('ComuCrePerfil');
  ajax = objetoAjax();
  ajax.open("GET", boardurl + "/web/tp-ComuCrePerfil.php?user=" + user + "&pag=" + pag);
  ajax.onreadystatechange = function() {
    if (ajax.readyState == 4) {
      divContenido.innerHTML = ajax.responseText;
    }
  }

  ajax.send(null);
}
 
 
function sleep(milliseconds) {
  var start = new Date().getTime();

  for(var i = 0; i < 1e7; i++) {
    if ((new Date().getTime() - start) > milliseconds) {
      break;
    }
  }
}

var com = {
  /* Crear shortnames */
  crear_shortname_key: function(val) {
    $('#preview_shortname').html(val).removeClass('error').removeClass('ok');
    $('#msg_crear_shortname').html('');
  },
  crear_shortname_check_cache: new Array(),
  crear_shortname_check: function(val) {
    if (val=='') {
      return;
    }

    // Verificar si ya se buscó
    for (i = 0; i < this.crear_shortname_check_cache.length; i++) {
      if (this.crear_shortname_check_cache[i][0] === val) { //Lo tengo
        if (this.crear_shortname_check_cache[i][1] === '1') { //Disponible
          $('#preview_shortname').removeClass('error').addClass('ok');
          $('#msg_crear_shortname').html(this.crear_shortname_check_cache[i][2]).removeClass('error').addClass('ok');
        } else { //No disponible
          $('#preview_shortname').removeClass('ok').addClass('error');
          $('#msg_crear_shortname').html(this.crear_shortname_check_cache[i][2]).removeClass('ok').addClass('error');
        }

        return;
      }
    }

    $('.gif_cargando#shortname').css('display', 'block');

    $.ajax({
      type: 'POST',
      url: boardurl + '/web/tp-comunidadesChekLink.php',
      data: 'shortname='+encodeURIComponent(val),
      success: function(h){
        com.crear_shortname_check_cache[com.crear_shortname_check_cache.length] = new Array(val, h.charAt(0), h.substring(3)); //Guardo los datos de verificacion

        $('.gif_cargando#shortname').css('display', 'none');

        switch(h.charAt(0)) {
          case '0': //Error
            $('#preview_shortname').removeClass('ok').addClass('error');
            $('#msg_crear_shortname').html(h.substring(3)).removeClass('ok').addClass('error');
            break;
          case '1': //OK
            $('#preview_shortname').removeClass('error').addClass('ok');
            $('#msg_crear_shortname').html(h.substring(3)).removeClass('error').addClass('ok');
            break;
        }
      },
      error: function() {
        $('.gif_cargando#shortname').css('display', 'none');
        $('#msg_crear_shortname').html('Error al intentar procesar lo solicitado').removeClass('ok').addClass('error');
      }
    });
  },
  tema_votar: function(voto, tema) {
    $.ajax({
      type: 'POST',
      url: boardurl + '/web/tp-comunidadesVotarTema.php',
      data: 'voto=' + voto + '&tema=' + tema,
      success: function(h) {
        switch(h.charAt(0)) {
          case '0': //Error
            $('#votos_total2').html(h.substring(3)).removeClass('ok').addClass('error');
            break;
          case '1': //OK
            $('#votos_total').html(h.substring(3));
            break;
        }
      },
      error: function() {
        $('#votos_total2').html('Error al intentar procesar lo solicitado').removeClass('ok').addClass('error');
      }
    });
  }
};

function actualizar_comentarios_com() {
  $('#ult_comm').slideUp(1);

  $.ajax({
    type: 'POST',
    url: boardurl + '/web/tp-comunidadesActCom.php',
    success: function(h) {
      $('#ult_comm').html(h);
      $('#ult_comm').slideDown({ duration: 1000, easing: 'easeOutBounce' });
    }
  });
}

function actualizar_comentarios_com_id(id) {
  $('#ult_comm').slideUp(1);

  $.ajax({
    type: 'POST',
    url: boardurl + '/web/tp-comunidadesActComc.php?id=' + id,
    success: function(h) {
      $('#ult_comm').html(h);
      $('#ult_comm').slideDown({ duration: 1000, easing: 'easeOutBounce' });
    }
  });
}

function ComComentar(tema, psr) {
  if ($('#cuerpo_comment').val() == '') {
    $('#cuerpo_comment').focus();
    return;
  }

  $('.msg_comentar').hide();
  $('#gif_cargando_comentar').css('display', 'block');

  $.ajax({
    type: 'POST',
    url: boardurl + '/web/tp-comunidadesComentar.php',
    data: 'comentario=' + encodeURIComponent($('#cuerpo_comment').val()) + '&tema=' + tema,
    success: function(h) {
      $('#gif_cargando_comentar').css('display', 'none');

      if (h.charAt(0) == 0) {
        $('.msg_comentar').html(h.substring(3));
        $('.msg_comentar').addClass('noesta');
        $('.msg_comentar').show('slow');
      }

      if (h.charAt(0) == 1) {
        comentarioscom(tema, psr);
        $('#cuerpo_comment').val('');
        $('#nrocoment').html(parseInt($('#nrocoment').html()) + 1);
      }
    },
    error: function() {
      $('#gif_cargando_comentar').css('display', 'none');
      $('.msg_comentar').html('Error al intentar procesar lo solicitado');
      $('.msg_comentar').show('slow');
    }
  });
}