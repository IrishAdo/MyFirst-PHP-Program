/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- F O R M   B U I L D E R   J A V A S C R I P T   F I L E 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-
- Author: Adrian Sweeney
- Company: Libertas Solutions
- Copyright: 2003
- Created: 12th August 2003
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Modifications: 1st Jan 2004 - 23rd Jan 2004
-	Added extra tabs and functionality (preview, Advanced, Confirm) and tidied up the layout of the form 
-	builder.
- Modified: 23rd Jan 2004
- 	Adding better property definitions to the form builder includes ability to specify that the field's 
-	values are to result in email target addresses.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description:-
-
- This is the javascript for the form builder module it will allow you do to the following 
-
- 1. Build up an XML representation of form,
- 2. Preview is form as you work,
- 3. Edit the form Quickly and easily
- 4. Move the field up and down the list
- 5. Allow the form to have dirrent destinations
- 6. Use (Text, Textarea, Radio, Check Box and Select box) elements
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- No copying, modifing, reproducing without the consent of Libertas Solutions
- Libertas Solutions does not supply any warranty with the use of this software.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	Modified $Date: 2004/08/25 07:34:37 $
-	$Revision: 1.2 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- F O R M  F I E L D   O B J E C T 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
- this is the object for the fields of the form builder
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function form_field(field_type){
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Properties
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	this.name					= "";
	this.value					= "";
	this.fieldName				= "";
	this.fieldValue				= "";
	this.type 					= field_type;
	this.label 					= "";
	this.field_property			= new Array();
	this.isPropertyArray		= false;
	this.layout_direction		= "vertical";
	this.options				= new Array();
	this.isRequired 			= false;
	this.session_hide			= false;
	this.attributes				= new Array();
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Methods
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	this.clone 					= __field_clone;
	this.clone_field_property	= __clone_field_property;
	this.clone_attributes		= __clone_attributes
	this.toString				= __field_to_string;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __field_clone(data)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will clone a field supplied as parameter "data"
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __field_clone(data){
	this.name				= data.name;
	this.fieldName			= data.fieldName;
	this.fieldValue			= data.fieldValue;
	this.type 				= data.type;
	this.label 				= data.label;
	this.options			= data.options;
	this.isPropertyArray	= data.isPropertyArray;
	this.layout_direction	= data.layout_direction;
	this.isRequired 		= data.isRequired;
	this.session_hide		= data.session_hide;
	this.clone_attributes(data.attributes);
	if (this.type!="splitter" && this.type!="row_splitter" && data.field_property!=null){
		this.clone_field_property(data.field_property, this.isPropertyArray);
	} else {
		this.isPropertyArray=null;
	}
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __clone_attributes(info)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- this function will create a duplicate of a fields attributes list
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __clone_attributes(info){
	if (info instanceof Array){
		for (var i=0;i<info.length;i++){
			this.attributes[i] = new Array();
			if (info[i].length+''!='undefined'){
				for (index=0; index<info[i].length; index++){
					this.attributes[i][index] = info[i][index];
				}
			}
		}
	}
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __clone_field_property(info, bool)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- this function will create a duplicate of a fields property list
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __clone_field_property(info, bool){
	for (property in info){
		if (bool){
			this.field_property[property] = new Array();
			for (index in info[property]){
				this.field_property[property][index] = info[property][index];
			}
		}else{
			this.field_property[property] = info[property];
		}
	}
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: move_option(from , to )
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-  This function will move an entry up or down the list by one step.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function move_option(from, to){
	if (from<to){
		start	= from;
		finish	= to;
		pos = +1;
	} else {
		start	= to;
		finish	= from;
		pos = +1;
	}
	temp = new Array(field.field_property[start][0], field.field_property[start][1], field.field_property[start][2]);
	for (index = start; index< finish ; index++){
		field.field_property[index] = new Array(
			field.field_property[index+pos][0],
			field.field_property[index+pos][1],
			field.field_property[index+pos][2]
		);
	}
	field.field_property[finish] = new Array(
		temp[0],
		temp[1],
		temp[2]
	);
	next_page(current_page);
}

function __field_to_string(){
	str  = ":----------------------------:\n"
	str += ": Field Properties           :\n"
	str += ":----------------------------:\n"
	str += "[name]:"+this.name+"\n";
	str += "[value]:"+this.value+"\n";
	str += "[fieldName]:"+this.fieldName+"\n";
	str += "[fieldValue]:"+this.fieldValue+"\n";
	str += "[type]:"+this.type+"\n";
	str += "[label]:"+this.label+"\n";
	str += "[field_property]:{\n"
	for(var i =0 ; i< this.field_property.length;i++){
		str += "\t{"+this.field_property[i].join(",")+"}\n";
	}
	str += "}\n";
	str += "[field_attributes]:{\n"
	for(var i =0 ; i< this.attributes.length;i++){
		str += "\t{"+this.attributes[i].join(",")+"}\n";
	}
	str += "}\n";
	str += "[isPropertyArray]:"+this.isPropertyArray+"\n";
	str += "[layout_direction]:"+this.layout_direction+"\n";
	str += "[options]:"+this.options+"\n";
	str += "[isRequired]:"+this.isRequired+"\n";
	str += "[session_hide]:"+this.session_hide+"\n";
	return str;
}