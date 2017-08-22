//module_shipping_weight_matrix.js
var list_of_weights = Array();
var mydhtml			= new DhtmlScript(); // include html generation code builder 
var weight_setup=0;
var weight_lookup = Array();
var show = 0;
function generateGrid(){
	sz="";
	weight_list.sort(mysort);
	list_of_weights_sz="";
	list_of_weights.length =0;
	for(i=0; i < weight_list.length ; i++){
		for(j=0; j < weight_list[i]["grid"].length ; j++){
			if(list_of_weights_sz.indexOf(" "+weight_list[i]["grid"][j]["kg"]+",")==-1){
				list_of_weights_sz += " "+weight_list[i]["grid"][j]["kg"]+","
				list_of_weights[list_of_weights.length] = weight_list[i]["grid"][j]["kg"];
			}
		}
	}
	var f = get_form()
	f.weight_list_data.value = list_of_weights_sz;
	draw();
}

function toggle_country_group(t,ignore, v){
	found=0;
//	alert(t.value);
	for(i=0; i < weight_list.length ; i++){
		if (weight_list[i]["country_identifier"] == t.value){
			found=i;
			break;
		}
	}
	if (!t.checked){//unselect = false
		confirm_answer = confirm("Are you sure you want to remove this country?");
		if(confirm_answer){
			// remove entries 
			for(i=0; i < weight_list.length ; i++){
				if (weight_list[i]["country_identifier"] == t.value){
					weight_list.splice(i,1);
					i--;
				}
			}
		} else {
			t.checked=true;
		}
	} else {
		// add to weight list
		country_label_ptr = document.getElementById(t.id+"_label");
		country_label = country_label_ptr.innerHTML;
		i = weight_list.length;
		weight_list[i] = new Array();
		weight_list[i]["country_identifier"]	= t.value;
		weight_list[i]["country_code"]			= t.value.split("::")[0];
		weight_list[i]["country"]				= country_label;
		weight_list[i]["grid"]					= new Array();
		z=0;
		//alert(weight_list_kg.join("::")+" "+weight_lookup.join("::"));
		for(j=1;j<weight_lookup.length;j++){
			weight_list[i]["grid"][z]			= new Array();
			weight_list[i]["grid"][z]["price"]	= 0;
			weight_list[i]["grid"][z]["kg"]		= weight_list_kg[j];//
			z++;
		}
		weight_list[i]["grid"][z]			= new Array();
		weight_list[i]["grid"][z]["price"]	= 0;
		weight_list[i]["grid"][z]["kg"]		= weight_list_kg[0];//
	}
	weight_list.sort(mysort);
	draw();
}

function draw(){
//	weight_list.sort(mysort);
	var widthOfweights = 80
	var tab											= document.getElementById("weightgrid");
//	alert(tab.rows.length);
	for(d=tab.rows.length; d > 0 ; d--){
		tab.deleteRow(d - 1);
	}
//	alert(tab.rows.length);
	weight_copy = new Array();
	for(i=0;i<weight_list_kg.length;i++){
		weight_copy[weight_copy.length] = weight_list_kg[i];
	}
	//alert(weight_list_kg.join(":"));
	weight_list_kg.sort(mysortweight);
	//alert(weight_list_kg.join(":"));
	if(weight_setup==0){
		weight_setup=1;
		weight_lookup = new Array();
		if(weight_list_kg.length>1){
			for(i=0;i<weight_list_kg.length;i++){
				for(j=0;j<weight_copy.length;j++){
					if(weight_copy[j] == weight_list_kg[i]){
						weight_lookup[weight_lookup.length] = j;
					}
				}	
			}
			show=1;
		} else {
			show=0;
		}
	} else {
		show=1;
	}
	if(show==0){
		var newRow										= tab.insertRow();
		var newCell										= mydhtml.createTag({'tag':'td', 'scope':'col', "width":"250px","innerHTML":"Please define your first weight", "align":"left"});
		newRow.appendChild(newCell);
			var newCell									= mydhtml.createTag({'tag':'td', 'scope':'row', "width":"350px","innerHTML":"<input type='text' name='initialweight' value=''></input><input type='button' onclick='add_first();' value='Add first Weight' class='bt'></input>", "align":"left"});
		newRow.appendChild(newCell);
	
	} else {
		var newRow										= tab.insertRow();
		var newCell										= mydhtml.createTag({'tag':'td', 'scope':'col', "width":"250px","innerHTML":"Country","className":"bt", "align":"left"});
		newRow.appendChild(newCell);
		for(j=1;j<weight_list_kg.length;j++){
//			alert(weight_list_kg[j]);
			hiddenweight = "<input type='hidden' name='weight[]' value='"+weight_list_kg[j]+"'>";
			if(weight_list_kg[j]==-1){
				var newCell								= mydhtml.createTag({'tag':'td', 'scope':'col', "width":widthOfweights+"px","innerHTML":"<small>"+weight_list_kg[j-1]+"kg +</small>"+hiddenweight,"className":"bt", "align":"left"});
			} else {
				var newCell								= mydhtml.createTag({'tag':'td', 'scope':'col', "width":widthOfweights+"px","innerHTML":"<a href='javascript:remove_weight("+weight_list_kg[j]+");'><img alt='remove the weight classification \""+weight_list_kg[j]+" kg\"' src='/libertas_images/general/buttons/actions/no.gif' border='0' align='right' /></a><small>Up To "+weight_list_kg[j]+"kg</small>"+hiddenweight,"className":"bt", "align":"left"});
			}
			newRow.appendChild(newCell);
		}
		for(j=0;j<1;j++){ // display greater than
			hiddenweight = "<input type='hidden' name='weight[]' value='"+weight_list_kg[j]+"'>";
			var newCell								= mydhtml.createTag({'tag':'td', 'scope':'col', "width":widthOfweights+"px","innerHTML":"<small>"+weight_list_kg[weight_list_kg.length -1]+"kg +</small>"+hiddenweight,"className":"bt", "align":"left"});
			newRow.appendChild(newCell);
		}
		var newCell								= mydhtml.createTag({'tag':'td', 'id':'wentry', 'scope':'col', "width":"auto","innerHTML":"<input type='button' value='Add new' onclick='add_new();' class='bt'/>","align":"left"});
		newRow.appendChild(newCell);
//		alert(weight_lookup.join("::"));
//		alert(weight_list_kg.join("::"));
		for(i=0; i < weight_list.length ; i++){
			var newRow									= tab.insertRow(-1);
			padding="";
			cid_list = weight_list[i]["country_identifier"].split("::");
			if(cid_list[1]!=-1){
				var newCell									= mydhtml.createTag({'tag':'td', 'scope':'row', "width":"250px","innerHTML":"<input type='hidden' name='country_identifier[]' value='"+weight_list[i]["country_identifier"]+"'> - "+weight_list[i]["country"], "align":"left"});
			} else {
				var newCell									= mydhtml.createTag({'tag':'td', 'scope':'row', "width":"250px","innerHTML":"<input type='hidden' name='country_identifier[]' value='"+weight_list[i]["country_identifier"]+"'><strong> All "+weight_list[i]["country"]+" countries</strong>", "align":"left"});
			}
			newRow.appendChild(newCell);
//			alert("weight lookup length :: "+weight_lookup.length);
			for(j=1;j<weight_lookup.length;j++){//weight_list_kg, weight_lookup
//				alert(weight_lookup[j]);
				inner  = "<input type='text' size='5' maxlength='8' name='weight_";
				inner += weight_list[i]["country_identifier"]+"_"+weight_list[i]["grid"][weight_lookup[j]]["kg"];
				inner += "' value='"
				try {
					inner += weight_list[i]["grid"][weight_lookup[j]]["price"];// + " "+weight_lookup[j]+" "+weight_list[i]["grid"][weight_lookup[j]]["kg"];
				} catch(e){
					inner += "0";
				}
				inner += "' style='width:"+widthOfweights+"px'>";
				var newCell								= mydhtml.createTag({'tag':'td', "width":widthOfweights+"px","innerHTML":inner});
				newRow.appendChild(newCell);
			}
			for(j=0;j<1;j++){// display greater than (-1)
				inner  = "<input type='text' size='5' maxlength='8' name='weight_";
//				alert("["+i+"]["+j+"]["+weight_lookup[j]+"]");
//				alert("[cl::"+weight_list[i]["country_identifier"]+"]");
//				alert("[gl::"+weight_list[i]["grid"].length+"]");
				inner += weight_list[i]["country_identifier"]+"_"+weight_list[i]["grid"][weight_lookup[j]]["kg"];
				inner += "' value='"
				try {
					inner += weight_list[i]["grid"][weight_lookup[j]]["price"];
				} catch(e){
					inner += "0";
				}
				inner += "' style='width:"+widthOfweights+"px'>";
				var newCell								= mydhtml.createTag({'tag':'td', "width":widthOfweights+"px","innerHTML":inner});
				newRow.appendChild(newCell);
			}
		}
	}
	//alert(tab.innerHTML);
}

function mysort(a,b){
	aa = a["country_identifier"].split("::");
	ba = b["country_identifier"].split("::");
	if (aa[0] == ba[0])
		return (aa[1]-ba[1]);
	else if (aa[0] < ba[0])
		return -1;
	return 1;
}
function mysortweight(a,b){
	return a - b;
}

function add_new(){
	outputcell = document.getElementById('wentry');
	outputcell.innerHTML ="<input type='text' size='5' name='new_weight'><input type='button' value='Ok' onclick='save_new();' class='bt'/><input type='button' value='Cancel' onclick='cancel_new();' class='bt'/>";
}
function save_new(){
	var f = get_form();
	weight_list_kg[weight_list_kg.length] = f.new_weight.value;
//	outputcell = document.getElementById('wentry');
//	outputcell.innerHTML ="<input type='button' value='Add new' onclick='add_new();' class='bt'/>";
		for (index=0; index < weight_list.length; index++){
			// remove the weight classification for each country
			gid = weight_list[index]["grid"].length;
			weight_list[index]["grid"][gid] = new Array()
			weight_list[index]["grid"][gid]["price"]=0;
			weight_list[index]["grid"][gid]["kg"]=f.new_weight.value;
		}	
draw();
}
function cancel_new(){
	outputcell = document.getElementById('wentry');
	outputcell.innerHTML ="<input type='button' value='Add new' onclick='add_new();' class='bt'/>";
}

function remove_weight(kg){
	ok = confirm("You are about to remove the weight classification '"+kg+" kg'\nAre you sure you want to remove this classification"	);
	if(ok){
		for(j=0;j<weight_list_kg.length;j++){
			if(weight_list_kg[j] == kg){
				weight_list_kg.splice(j,1);
				break;
			}
		}
		for (index=0; index < weight_list.length; index++){
			// remove the weight classification for each country
			for(wIndex=0; wIndex < weight_list[index]["grid"].length; wIndex++){
				if(weight_list[index]["grid"][wIndex]["kg"] == kg){
					weight_list[index]["grid"].splice(wIndex,1);
					break;
				}
			}
		}	
		draw();
	}
//alert(ok);

}

function add_first(){
	val = document.getElementById("initialweight").value;
	weight_list_kg = Array(-1,val);
	for (index=0; index < weight_list.length; index++){
		// remove the weight classification for each country
		weight_list[index]["grid"]= new Array();
		weight_list[index]["grid"][0] = Array();
		weight_list[index]["grid"][0]["price"] = '0';
		weight_list[index]["grid"][0]["kg"] = -1;
		gid = weight_list[index]["grid"].length;
		weight_list[index]["grid"][gid] = new Array()
		weight_list[index]["grid"][gid]["price"]=0;
		weight_list[index]["grid"][gid]["kg"]=val;
	}	

	draw();
}