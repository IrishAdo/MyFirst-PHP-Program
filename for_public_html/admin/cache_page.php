<? 
session_id($session);
session_start();

if ($command=="PAUSE"){
?>
<html>

	<body style="margin:0 0 0 0">
		<IMG src="images/cache_img.gif" border="0" width="200" height="100" alt="Please wait while we retrieve the latest list of images">
	</body>
</html>
<?
} else {
require_once "include.php";
include_once ($module_directory."/libertas.engine.php");

if (empty($page)){
	$page=1;
}
if (empty($command)){
	$command="";
}
$version = phpversion();
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Get system information based on the version of the php engine being used.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
if (substr($version,0,5)=="4.2.4"){
	$domain = $_SERVER["HTTP_HOST"];	
	if (count($_POST)>0){
		$qstring = $_POST;
	} else {
		$qstring = $_GET;
	}
}

$engine = new engine($domain,session_id($session),"","website");
$out = "<xml_document script=\"$SCRIPT_NAME\">\n\t<qstring><![CDATA[".join($qstring,", ")."]]></qstring><modules>\n";
if ($engine->status==1){
	$out.= $engine->call_command("ENGINE_CALL_COMMAND",array($qstring["action"], Array($qstring["filter"])));
}
$engine->call_command("ENGINE_CLOSE",array(""));
$out .= "</modules>	</xml_document>";

$engine->call_command("XML_PARSER_LOAD_XML_STR",array($out));
$engine->call_command("XML_PARSER_LOAD_XSL_FILE",array("stylesheets/filelist.xsl"));
$output= $engine->call_command("XML_PARSER_TRANSFORM");

//
$xml=1;
if (!empty($xml)){
	header("Content-Type: text/xml");
	print $out;
}else{
	print $output;
	print "<script> alert('$session');</script>";
}

}
?>