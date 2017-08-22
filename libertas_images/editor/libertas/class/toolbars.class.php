<?php 
// ================================================
// libertas PHP WYSIWYG editor control
// ================================================
// Toolbars class
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-22
// ================================================

// toolbar item type constants
define("LIBERTAS_TBI_IMAGE", "image");
define("LIBERTAS_TBI_BUTTON", "button");
define("LIBERTAS_TBI_DROPDOWN", "dropdown");

// toolbar item
class LIBERTAS_TB_Item
{
  // name
  var $name;
  // language object
  var $lang;
  // editor name
  var $editor;
  // additional item data
  var $data;
  // toolbar theme
  var $theme;
  
  // get items html
  function get()
  {
    return $this->lang->m('title',$this->name);
  }
  
  // show item
  function show()
  {
    echo $this->get();
  }
  
  // constructor
  function LIBERTAS_TB_Item($name, &$lang, $editor, $theme, $attributes='', $data='')
  {
    $this->name = $name;
    $this->lang = $lang;
    $this->editor = $editor;
    $this->theme = $theme;
    if (!is_array($data))
    {
      $this->data = array();
    }
    else
    {
      $this->data = $data;
    }
  }
} // LIBERTAS_TB_Item

// toolbar image
class LIBERTAS_TB_Image extends LIBERTAS_TB_Item
{
  // override get
  function get()
  {
    global $libertas_dir;
    
    if (!empty($this->name))
    {
      $buf = '<img alt="'.$this->lang->m('title',$this->name).'" src="'.$libertas_dir.'lib/themes/'.$this->theme.'/img/tb_'.$this->name.'.gif" '.$this->attributes.'>';
      return $buf;
    }
  }
} // LIBERTAS_TB_Image

// toolbar button
class LIBERTAS_TB_Button extends LIBERTAS_TB_Item
{
  // override get
  function get()
  {
    global $libertas_dir;
    
    if (!empty($this->name))
    {
      $buf = '<img alt="'.$this->lang->m('title',$this->name).'" src="'.$libertas_dir.'lib/themes/'.$this->theme.'/img/tb_'.$this->name.'.gif" onClick="LIBERTAS_'.$this->name.'_click(\''.$this->editor.'\',this)" class="LIBERTAS_'.$this->theme.'_tb_out" onMouseOver="LIBERTAS_'.$this->theme.'_bt_over(this)" onMouseOut="LIBERTAS_'.$this->theme.'_bt_out(this)" onMouseDown="LIBERTAS_'.$this->theme.'_bt_down(this)" onMouseUp="LIBERTAS_'.$this->theme.'_bt_up(this)"  '.$this->attributes.'>';
      return $buf;
    }
  }
} // LIBERTAS_TB_Button

// toolbar dropdown
class LIBERTAS_TB_Dropdown extends LIBERTAS_TB_Item
{
  // override get
  function get()
  {
    global $libertas_dir;
    global $libertas_theme;
    
    if (!empty($this->name))
    {
      $buf = '<select size="1" id="LIBERTAS_'.$this->editor.'_tb_'.$this->name.'" name="LIBERTAS_'.$this->editor.'_tb_'.$this->name.'" align="absmiddle" class="LIBERTAS_'.$this->theme.'_tb_input" onchange="LIBERTAS_'.$this->name.'_change(\''.$this->editor.'\',this)" '.$this->attributes.'>';
      $buf.='<option>'.$this->lang->m('title',$this->name).'</option>';
      while(list($value,$text) = each($this->data))
      {
        $buf.='<option value="'.$value.'">'.$text.'</option>';
      }
      $buf.= '</select>';
      return $buf;
    }
  }
} // LIBERTAS_TB_Button

// toolbars
class LIBERTAS_Toolbars
{
  // array of toolbar data
  var $toolbars;

  // toolbar mode (scheme)
  var $mode;
  
  // dropdown data
  var $dropdown_data;
  
  // accessors
  function setMode($value)
  {
    global $libertas_dir;
    global $libertas_root;
    global $libertas_default_toolbars;
    
    if ($value == '')
    {
      $this->mode = $libertas_default_toolbars;
    }
    else
    {
      $this->mode = $value;
    }
    
    if (!@include($libertas_root.'lib/toolbars/'.$this->mode.'/'.$this->mode.'_toolbar_data.inc.php'))
    {
      // load default toolbar data
      @include($libertas_root.'lib/toolbars/'.$libertas_default_toolbars.'/'.$libertas_default_toolbars.'_toolbar_data.inc.php');
    }
    $this->toolbars = $libertas_toolbar_data;
  }
  
  // language object
  var $lang;
  
  // editor name
  var $editor;
  
  // toolbar theme
  var $theme;
  
  // constructor
  function LIBERTAS_Toolbars(&$lang, $editor, $mode='', $theme='', $dropdown_data='')
  {
    global $libertas_dropdown_data;
    
    $this->lang = $lang;
    $this->editor = $editor;
    $this->setMode($mode);
    $this->theme = $theme;
    if ($dropdown_data != '')
    {
      $this->dropdown_data = $dropdown_data;
    }
    else
    {
      $this->dropdown_data = $libertas_dropdown_data;
    }
  }
  
  // get toolbar html for the specified position (top, left, right, bottom)
  function get($pos, $mode='design')
  {
    if (!empty($this->toolbars[$pos.'_'.$mode]))
    {
      if ($pos == 'top' || $pos == 'bottom')
      {
        // horizontal toolbar
        $tb_pos_start = '<table border="0" cellpadding="0" cellspacing="0" width="100%">';
        $tb_pos_end = '</table>';
        $tb_item_sep = '';
      }
      else
      {
        // vertical toolbar
        $tb_pos_start = '<table border="0" cellpadding="0" cellspacing="0"><tr>';
        $tb_pos_end = '</tr></table>';
        $tb_item_sep = '<br>';
      }
      $buf = $tb_pos_start;
      while (list(,$tb) = each($this->toolbars[$pos.'_'.$mode]))
      {
        if ($pos == 'top' || $pos == 'bottom')
        {
          // horizontal toolbar
          $tb_start = '<tr><td align="'.$tb['settings']['align'].'" valign="'.$tb['settings']['valign'].'" class="LIBERTAS_'.$this->theme.'_toolbar_'.$pos.'">';
          $tb_end = '</td></tr>';
        }
        else
        {
          // vertical toolbar
          $tb_start = '<td align="'.$tb['settings']['align'].'" valign="'.$tb['settings']['valign'].'" class="LIBERTAS_'.$this->theme.'_toolbar_'.$pos.'">';
          $tb_end = '</td>';
        }
      
        $buf .= $tb_start;
        while (list(,$tbitem) = each($tb['data']))
        {
          $buf .= $this->getTbItem($tbitem['name'],$tbitem['type'],$tbitem['attributes'], $tbitem['data']) . $tb_item_sep;
        }
        $buf .= $tb_end;
      }
      $buf .= $tb_pos_end;
    }
    return $buf;
  } // get
  
  // returns toolbar item html based on name and type
  function getTbItem($name, $type, $attributes, $data)
  {
    switch($type)
    {
      case LIBERTAS_TBI_IMAGE:
        $tbi = new LIBERTAS_TB_Image($name, $this->lang, $this->editor, $this->theme, $attributes);
        $buf = $tbi->get();
        break;
      case LIBERTAS_TBI_BUTTON:
        $tbi = new LIBERTAS_TB_Button($name, $this->lang, $this->editor, $this->theme, $attributes);
        $buf = $tbi->get();
        break;
      case LIBERTAS_TBI_DROPDOWN:
        if (!empty($this->dropdown_data[$name]))
        {
          $d_data = $this->dropdown_data[$name];
        }
        else
        {
          $d_data = $data;
        }
        $tbi = new LIBERTAS_TB_Dropdown($name, $this->lang, $this->editor, $this->theme, $attributes, $d_data);
        $buf = $tbi->get();
        break;
      default:
        $tbi = new LIBERTAS_TB_Item($name, $this->lang, $this->editor, $this->theme, $attributes);
        $buf = $tbi->get();
        break;
    }
    return $buf;
  } // getTbItem
  
  // output toolbar html for the specified position (top, left, right, bottom)
  function show($pos)
  {
    echo $this->get($pos);
  } // show
} // class LIBERTAS_Toolbars
?>
