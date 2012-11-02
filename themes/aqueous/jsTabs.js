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
	if (document.getElementById("subjs"+id)==null) id=0;//some menus do not have submenus
	
	//hidding the boxes
	var eBox = getElementsByClass("subjs",document.getElementById('subnav'),"li");//all the boxes
	for (i in eBox) hide(eBox[i].id);
	//show the box
	show('subjs'+id);
	
	//change tab class
	var eTab = getElementsByClass("active",document.getElementById('nav'),"a");//all the tabs
	for (i in eTab) eTab[i].className ='';
	//set on the tab
	document.getElementById('navjs'+id).className ='active';

}