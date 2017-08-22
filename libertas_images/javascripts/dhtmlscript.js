/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-														C A C H E S C R I P T . J S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 		A file to allow the inclusion of cache functionality on a page original source taken from the WYSIWYG Editor and adapted to run in 
- standalone mode.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-				$Revision: 1.2 $, 
-				$Date: 2004/12/08 18:47:51 $
-				$Author: aldur $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-
-
- Formatting used
-
-	all methods of the class will use the following format two under scores followed by the initials of the class in question followed by a 
-	single underscore followed by the function name 
-	
-	in otherwords the source for the function CacheScript.toString() would be found in the function __CS_toString();
-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-														C A C H E S C R I P T   C L A S S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function DhtmlScript(){ 
	// no properties just functions
	// methods of class
	this.createTag				= __DS_createTag;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	FN:: __DS_createTag(parameters);
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __DS_createTag(parameters){
	var myTag="";
	if (parameters['tag']){
		myTag = document.createElement(parameters['tag']);
		for(var i in parameters){
			if (i!="tag"){
//				alert(i);
				if (i=="innerHTML"){
					myTag.innerHTML 		= parameters[i];
				} else {
					myTag.setAttribute(i	,parameters[i]);
				}
			}
			if (i=="myoptions"){
				for(var index in parameters[i]){
					myTag.options[myTag.options.length] = new Option(parameters[i][index]["label"], parameters[i][index]["value"]);
					if (parameters[i][index]["selected"]){
						myTag.options[myTag.options.length-1].selected = true;
					}
				}
			}
		}
	} else {
		myTag = null;
	}
	return myTag;
}
