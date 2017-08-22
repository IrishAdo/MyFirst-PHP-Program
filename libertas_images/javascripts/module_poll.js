/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-														M O D U L E _ P O L L . J S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 		A file to allow the inclusion of cache functionality on a page original source taken from the WYSIWYG Editor and adapted to run in 
- standalone mode.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-				$Revision: 1.2 $, 
-				$Date: 2004/04/23 15:39:05 $
-				$Author: adrian $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	What modules this is used in :- 
-			This file is used in RSS_ADMIN to select the channel Image
-
- Formatting used
-
-	all methods of the class will use the following format two under scores followed by the initials of the class in question followed by a 
-	single underscore followed by the function name 
-	
-	in otherwords the source for the function CacheScript.toString() would be found in the function __CS_toString();
-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-																L O C A L E
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
var LOCALE_NO_SUBMISSION_ALLOWED = "You can not submit information on this poll in 'Preview' mode";
var LOCALE_VOTE_NOW = "vote";
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-																 C L A S S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function poll_displayer(){
	this.form 			= get_form();
	this.pView 			= document.getElementById("pollPreview");
	this.poll_question	= "";
	this.poll_answers	= new Array();



	this.display 	= __PD_display;
	this.get_poll	= __PD_get;
	
}

function __PD_get(){
	if (this.form.poll_question+""=="undefined") {
		this.poll_question		= this.form.poll_label.value;
	} else {
		this.poll_question		= this.form.poll_question.value;
	}
	this.poll_answers[0]	= this.form.poll_answer1.value
	this.poll_answers[1]	= this.form.poll_answer2.value
	this.poll_answers[2]	= this.form.poll_answer3.value
	this.poll_answers[3]	= this.form.poll_answer4.value
	this.poll_answers[4]	= this.form.poll_answer5.value
	this.poll_answers[5]	= this.form.poll_answer6.value
	this.poll_answers[6]	= this.form.poll_answer7.value
	this.poll_answers[7]	= this.form.poll_answer8.value
	this.poll_answers[8]	= this.form.poll_answer9.value
	this.poll_answers[9]	= this.form.poll_answer10.value
}

function __PD_display(){
	this.get_poll();
	output ="<p><strong>"+this.poll_question+"</strong><br>";
	for(var i =0; i<10 ; i++){
		if (this.poll_answers[i]!=''){
			output += "<input type='radio' > "+this.poll_answers[i]+"<br>";
		}
	}
	output += "<input type='button' class='bt' value='"+LOCALE_VOTE_NOW+"' xonclick='alert(LOCALE_NO_SUBMISSION_ALLOWED)'><br>";
	output += "</p>";
	this.pView.innerHTML  = output;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-																generic function calls
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function preview_poll(){
	mypoll.display();
}
var mypoll = new poll_displayer();


