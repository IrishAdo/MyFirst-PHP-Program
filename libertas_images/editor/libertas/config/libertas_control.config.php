<?php 
// ================================================
// libertas PHP WYSIWYG editor control
// ================================================
// Configuration file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-27
// ================================================

// directory where libertas files are located
$libertas_dir = '/libertas_images/editor/libertas/';

// base url for images
$libertas_base_url = '/libertas_images/editor/libertas/';
//$libertas_base_url = 'http://professor/libertas_images/editor/libertas/';

$libertas_locale = "/home/system/cms/locale/";

if (!ereg('/$', $_SERVER['DOCUMENT_ROOT']))
  $libertas_root = $_SERVER['DOCUMENT_ROOT'].$libertas_dir;
else
  $libertas_root = $_SERVER['DOCUMENT_ROOT'].substr($libertas_dir,1,strlen($libertas_dir)-1);

$libertas_root = "/home/system/cms/libertas_images/editor/libertas/";


$libertas_default_toolbars = 'default';
$libertas_default_theme = 'default';
$libertas_default_lang = 'en';
$libertas_default_css_stylesheet = $libertas_dir.'wysiwyg.css';

// add javascript inline or via separate file
$libertas_inline_js = false;

// default dropdown content
$libertas_dropdown_data['style']['default'] = 'Normal';

$libertas_dropdown_data['font']['Arial,Helvetica,Verdana, Sans Serif'] = 'Arial';
$libertas_dropdown_data['font']['Courier, Courier New'] = 'Courier';
$libertas_dropdown_data['font']['Tahoma, Verdana, Arial, Helvetica, Sans Serif'] = 'Tahoma';
$libertas_dropdown_data['font']['Times New Roman, Times, Serif'] = 'Times';
$libertas_dropdown_data['font']['Verdana, Tahoma, Arial, Helvetica, Sans Serif'] = 'Verdana';

$libertas_dropdown_data['fontsize']['1'] = '1';
$libertas_dropdown_data['fontsize']['2'] = '2';
$libertas_dropdown_data['fontsize']['3'] = '3';
$libertas_dropdown_data['fontsize']['4'] = '4';
$libertas_dropdown_data['fontsize']['5'] = '5';
$libertas_dropdown_data['fontsize']['6'] = '6';

$libertas_dropdown_data['paragraph']['Normal'] = 'Normal';
$libertas_dropdown_data['paragraph']['Heading 1'] = 'Heading 1';
$libertas_dropdown_data['paragraph']['Heading 2'] = 'Heading 2';
$libertas_dropdown_data['paragraph']['Heading 3'] = 'Heading 3';
$libertas_dropdown_data['paragraph']['Heading 4'] = 'Heading 4';
$libertas_dropdown_data['paragraph']['Heading 5'] = 'Heading 5';
$libertas_dropdown_data['paragraph']['Heading 6'] = 'Heading 6';

// image library related config

// allowed extentions for uploaded image files
$libertas_valid_imgs = array('gif', 'jpg', 'jpeg', 'png');

// allow upload in image library
$libertas_upload_allowed = true;

// image libraries
$libertas_imglibs = array(
  array(
    'value'   => 'you/need/to/change/this/',
    'text'    => 'Not configured',
  ),
  array(
    'value'   => 'you/need/to/change/this/too/',
    'text'    => 'Not configured',
  ),
);


?>
