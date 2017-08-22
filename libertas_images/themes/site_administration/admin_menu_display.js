function launch_menu(home_label, domain_admin, help_label, debug){
	stm_bm(["Libertas_Solutions",400,"","/libertas_images/themes/1x1.gif",0,"","",0,1,showdelay,showdelay,hidedelay,1,0,0,""],this);
		begin(0,0,"Horizontally",0,"p0",1);
			add_entry1(home_label,"","p0i0");
			begin(0,-1,"vertically",0,"p1",0,4);
				href="http://"+domain_admin+"admin/index.php";
				add_entry("Digital Desktop",href,"p0i1","p0i0");
				href="http://"+domain_admin+"index.php";
				add_entry("Return to Site",href,"p0i2","p0i0");
				add_entry("<hr/>","","p0i2","p0i0");
				href="http://"+domain_admin+"admin/index.php?command=ENGINE_LOGOUT";
				add_entry("Exit (logout)",href,"p0i2","p0i0");
			stm_ep();
			prevValue="";
			prevsub ="";
			men = 1;
			pos=0;
			menus =new Array();
			for(i=0 ; i < menu_list.length ; i++){
				if(menus[menu_list[i][5]]+""=="undefined"){
					menus[menu_list[i][5]] = 0;
				}
				show=0;
				if(all==0){
					menu_list[i][9]=0;
					for(z=0;z < access_list.length;z++){
						if (access_list[z]==menu_list[i][6]){
							if(menu_list[i][9]==0){
								show=1;
								menu_list[i][9]=1;
							}
						} else if (new String(menu_list[i][2]).indexOf(access_list[z])!=-1 || access_list[z]==menu_list[i][4]){
							if(menu_list[i][9]==0){
								menu_list[i][9]=1;
								menus[menu_list[i][5]] ++;
							}
						}
						
					}		
				} else {
					menus[menu_list[i][5]] ++;
					menu_list[i][9]=1;
				}
			}
			for(i=0 ; i < menu_list.length ; i++){
				if(menu_list[i][9]==1){	
					if(menu_list[i][7][0] != prevValue){
						if(prevValue != ""){
							stm_ep();
						}
						prevValue= menu_list[i][7][0];
						add_entry1(menu_list[i][7][0],"","p0i"+men);
	 					begin(0,-1,"undefined",0,"p0"+men,0, 4, 0);
					}
					if(menu_list[i][6]==1){
						href="http://"+domain + base_href + "admin/index.php?command="+menu_list[i][3]+mysessiondata;
						add_entry(menu_list[i][7][1], href, "p"+men+"i"+pos, "p0"+men, 0);
						pos++;
					} else {
						if(path_list.indexOf(menu_list[i][5])==-1){
							counter = menus[menu_list[i][5]];//menu_list[i][6];
							add_entry(menu_list[i][7][1], "", "p"+men+"i"+pos, "p0"+men, 1);
							begin(0,-1,"undefined",0,"p"+men+"s"+pos, 1, 2, 1);
							prevsub="p"+men+"s"+pos;
							path_list += menu_list[i][5]+",";
							pos++;
						}
						href="http://"+domain + base_href + "admin/index.php?command=" + menu_list[i][3] + mysessiondata;
						add_entry(menu_list[i][1], href, "p"+men+"i"+pos, prevsub, 0);
						pos++;
						counter--;
						if(counter==0){
							stm_ep();				
						}
					}
				}
			}
			stm_ep();
			add_entry1(help_label,"","p0i99");
				begin(0,0,"undefined",0,"p99",0,4);
					add_entry("About Us","http://www.libertas-solutions.com", "p99i1", "p0i99");
					add_entry("<hr/>","", "p99i1", "p0i99");
					href="http://"+domain + base_href + "admin/index.php?command=ENGINE_VERSIONS" + mysessiondata;
					add_entry("Module Versions",href, "p99i1", "p0i99",0);
					if(debug==1){
						add_entry("Development","", "p199i1", "p0i99",1);
						begin(0,-1,"undefined",0,"p19901", 1, 2, 1);
							add_entry("PHP Info",		"http://"+domain + base_href + "admin/phpinfo.php", "p19901i0", "p19901");
							add_entry("System Debug",	"http://"+domain + base_href + "admin/index.php?command=SYSPREFS_DEBUG_ADMIN", "p19901i1", "p19901");
							add_entry("Regenerate Menu","http://"+domain + base_href + "admin/index.php?command=ENGINE_REGEN_MENUS", "p19901i2", "p19901");
						stm_ep();
					}
				stm_ep();
		stm_ep();
	stm_em();
}
function add_entry1(label_str,url_str,current_id){
	stm_ai(current_id,
		[0,label_str,"","",-1,-1,0,url_str,"_self","","","","",0,0,0,
		"",
		"",
		0 ,0 ,0 ,0 ,0 ,"",1,"#B6BDD2",0,
		"","",3,3,1,1,"#F4F7Fb #000000 #849eBB #999999","#F4F7Fb","#333333","#333333","0.9em verdana","0.9em verdana"]);
}
function add_entry(label_str,url_str,parent_id,current_id, has_children){
	if (has_children+"" == "undefined"){
		has_children = 0;
	}
	if (has_children == 1){
		image_width   = 10;
		image_height  = 10;
		image_source1 = "/libertas_images/themes/site_administration/arrow_r.gif";
		image_source2 = "/libertas_images/themes/site_administration/arrow_r.gif";
	} else {
		image_width   = 0;
		image_height  = 0;
		image_source1 = "";
		image_source2 = "";
	}
	
	if (label_str=="<hr/>"){
		stm_aix(parent_id,current_id,
			[0,label_str,"","",-1,-1,0,url_str,"_self","","","","",0,0,0,
			"","",
			0,0,0,0,1,colour_button_off,1,colour_button_off,0,
			"","",3,3,1,1,"#ffffff","#ebebeb","#ebebeb","#ebebeb","0.9em Verdana","0.9em Verdana"]);
	} else {
		stm_aix(parent_id,current_id,
		[0,label_str,"","",-1,-1,0,url_str,"_self","","","","",0,0,0,
		image_source1, image_source1, image_width, image_height ,0 ,0 ,1 ,colour_button_off,0,"#B6BDD2",0,
		"","",3,3,1,1,"#ffffff","#0A246A","#333333","#333333","0.9em Verdana","0.9em Verdana"]);
	}
}
function begin(x,y, mydirection,img,nme, trans, pos){
	arrow_image="";
	if (pos+"" == "undefined"){
		pos=2;
	}
	if (trans+"" == "undefined"){
		trans = 0  
	}
	if (trans==0) {
		buttoncolor =colour_button_off;
	} else {
		buttoncolor ="transparent";
	}
	iconwidth =10;
	imagewidth=10;
	
	if (mydirection+""=="undefined" || mydirection+""=="vertically"){
		stm_bp(nme,[1,pos,0,0,spacing,padding,iconwidth,imagewidth,total_visibility, "",-2,"",-2,90,3,3,"#000000",
		buttoncolor, backgroundimage, 0, 1, 1, "#000000"]);
	}else{
		stm_bp(nme,[0,pos,0,0,spacing,padding, iconwidth, imagewidth, total_visibility , "",-2,"",-2,90,0,0,"#000000",
		buttoncolor, backgroundimage, 0, 1, 1,"#f4f7fb #f4f7fb #849EBB #849EBB"]);
	}
	
}
