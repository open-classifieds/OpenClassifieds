function getElementsByClassDustin(searchClass,node,tag) { //by http://www.dustindiaz.com/getelementsbyclass/
    var classElements = new Array();
    if ( node == null )
            node = document;
    if ( tag == null )
            tag = '*';
    var els = node.getElementsByTagName(tag);
    var elsLen = els.length;
    var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
    for (i = 0, j = 0; i < elsLen; i++) {
            if ( pattern.test(els[i].className) ) {
                    classElements[j] = els[i];
                    j++;
            }
    }
    return classElements;
}

function getElementsByClass(searchClass,node,tag) { 
	//if is Netscape we use the native procedure
	if (navigator.appName=="Netscape") return document.getElementsByClassName(searchClass);
	else return getElementsByClassDustin(searchClass,node,tag);
}


function ShowTab(id){
	if (document.getElementById("sub"+id)==null) id=0;//some menus do not have submenus
	
	//hidding the boxes
	var eBox = getElementsByClass("sub",document.getElementById('submenu_left'),"div");//all the boxes
	for (i in eBox) hide(eBox[i].id);
	//show the box
	show('sub'+id);
	
	//change tab class
	var eTab = getElementsByClass("nav_selected",document.getElementById('nav'),"a");//all the tabs
	for (i in eTab) eTab[i].className ='nav';
	//set on the tab
	document.getElementById('nav'+id).className ='nav_selected';

}