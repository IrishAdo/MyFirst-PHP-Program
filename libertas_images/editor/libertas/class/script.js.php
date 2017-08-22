  // control registration array
  var libertas_editors = new Array();
  
  // onsubmit
  function LIBERTAS_UpdateFields()
  {
    for (i=0; i<libertas_editors.length; i++)
    {
      LIBERTAS_updateField(libertas_editors[i], null);
    }
  }
  
  // adds event handler for the form to update hidden fields
  function LIBERTAS_addOnSubmitHandler(editor)
  {
    thefield = LIBERTAS_getFieldByEditor(editor, null);

    var sTemp = "";
    oForm = document.all[thefield].form;
    if(oForm.onsubmit != null) {
      sTemp = oForm.onsubmit.toString();
      iStart = sTemp.indexOf("{") + 2;
      sTemp = sTemp.substr(iStart,sTemp.length-iStart-2);
    }
    if (sTemp.indexOf("LIBERTAS_UpdateFields();") == -1)
    {
      oForm.onsubmit = new Function("LIBERTAS_UpdateFields();" + sTemp);
    }
  }

  // editor initialization
  function LIBERTAS_editorInit(editor, css_stylesheet, direction)
  {
    // check if the editor completely loaded and schedule to try again if not
    if (this[editor+'_rEdit'].document.readyState != 'complete')
    {
      setTimeout('LIBERTAS_editorInit("'+editor+'", "'+css_stylesheet+'", "'+direction+'");',20);
      return;
    }
    
    this[editor+'_rEdit'].document.designMode = 'On';

    // register the editor 
    libertas_editors[libertas_editors.length] = editor;
    
    // add on submit handler
    LIBERTAS_addOnSubmitHandler(editor);

    
    if (this[editor+'_rEdit'].document.readyState == 'complete')
    {
      this[editor+'_rEdit'].document.createStyleSheet(css_stylesheet);
      this[editor+'_rEdit'].document.body.dir = direction;
    }
  }  
  
  
  function LIBERTAS_showColorPicker(editor,curcolor) {
    return showModalDialog('<?php echo $libertas_dir?>dialogs/colorpicker.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value, curcolor, 
      'dialogHeight:250px; dialogWidth:366px; resizable:no; status:no');  
  }

  function LIBERTAS_bold_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('bold', false, null);
  }

  function LIBERTAS_italic_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
   	this[editor+'_rEdit'].document.execCommand('italic', false, null);
  }

  function LIBERTAS_underline_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('underline', false, null);
  }
  
  function LIBERTAS_left_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('justifyleft', false, null);
  }

  function LIBERTAS_center_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
  	this[editor+'_rEdit'].document.execCommand('justifycenter', false, null);
  }

  function LIBERTAS_right_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
  	this[editor+'_rEdit'].document.execCommand('justifyright', false, null);
  }

  function LIBERTAS_ordered_list_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
  	this[editor+'_rEdit'].document.execCommand('insertorderedlist', false, null);
  }

  function LIBERTAS_bulleted_list_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
  	this[editor+'_rEdit'].document.execCommand('insertunorderedlist', false, null);
  }
  
  function LIBERTAS_fore_color_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     

    var fCol = LIBERTAS_showColorPicker(editor,null);

    if(fCol != null)
      this[editor+'_rEdit'].document.execCommand('forecolor', false, fCol);
  }

  function LIBERTAS_bg_color_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     

    var bCol = LIBERTAS_showColorPicker(editor,null);
    
    if(bCol != null)
    	this[editor+'_rEdit'].document.execCommand('backcolor', false, bCol);
  }

  function LIBERTAS_hyperlink_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
  	var l = this[editor+'_rEdit'].document.execCommand('createlink');
  }
  
  function LIBERTAS_image_insert_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     

    var imgSrc = showModalDialog('<?php echo $libertas_dir?>dialogs/img_library.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value, '', 
      'dialogHeight:420px; dialogWidth:420px; resizable:no; status:no');
    
    if(imgSrc != null)    
    	this[editor+'_rEdit'].document.execCommand('insertimage', false, imgSrc);
  }
  
  function LIBERTAS_image_prop_click(editor, sender)
  {
    var im = LIBERTAS_getImg(editor); // current cell
    
    if (im)
    {
      var iProps = {};
      iProps.src = im.src;
      iProps.alt = im.alt;
      iProps.width = (im.style.width)?im.style.width:im.width;
      iProps.height = (im.style.height)?im.style.height:im.height;
      iProps.border = im.border;
      iProps.align = im.align;
      iProps.hspace = im.hspace;
      iProps.vspace = im.vspace;
  
      var niProps = showModalDialog('<?php echo $libertas_dir?>dialogs/img.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value, iProps, 
        'dialogHeight:200px; dialogWidth:366px; resizable:no; status:no');  
      
      if (niProps)  
      {
        im.src = (niProps.src)?niProps.src:'';
        if (niProps.alt) {
          im.alt = niProps.alt;
        }
        else
        {
          im.removeAttribute("alt");
        }
        im.align = (niProps.align)?niProps.align:'';
        im.width = (niProps.width)?niProps.width:'';
        //im.style.width = (niProps.width)?niProps.width:'';
        im.height = (niProps.height)?niProps.height:'';
        //im.style.height = (niProps.height)?niProps.height:'';
        if (niProps.border) {
          im.border = niProps.border;
        }
        else
        {
          im.removeAttribute("border");
        }
        if (niProps.hspace) {
          im.hspace = niProps.hspace;
        }
        else
        {
          im.removeAttribute("hspace");
        }
        if (niProps.vspace) {
          im.vspace = niProps.vspace;
        }
        else
        {
          im.removeAttribute("vspace");
        }
      }      
      //LIBERTAS_updateField(editor,"");
    } // if im
  }

  function LIBERTAS_hr_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('inserthorizontalrule', false, null);
  }

  function LIBERTAS_copy_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('copy', false, null);
  }

  function LIBERTAS_paste_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('paste', false, null);
  }
  
  function LIBERTAS_cut_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('cut', false, null);
  }

  function LIBERTAS_delete_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('delete', false, null);
  }

  function LIBERTAS_indent_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('indent', false, null);
  }

  function LIBERTAS_unindent_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('outdent', false, null);
  }

  function LIBERTAS_undo_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('undo','',null);
  }

  function LIBERTAS_redo_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     
    this[editor+'_rEdit'].document.execCommand('redo', false, null);
  }
  
  
  function LIBERTAS_getParentTag(editor)
  {
    var trange = this[editor+'_rEdit'].document.selection.createRange();
    return (trange.parentElement());
  }

  // trim functions  
  function LIBERTAS_ltrim(txt)
  {
    var spacers = " \t\r\n";
    while (spacers.indexOf(txt.charAt(0)) != -1)
    {
      txt = txt.substr(1);
    }
    return(txt);
  }
  function LIBERTAS_rtrim(txt)
  {
    var spacers = " \t\r\n";
    while (spacers.indexOf(txt.charAt(txt.length-1)) != -1)
    {
      txt = txt.substr(0,txt.length-1);
    }
    return(txt);
  }
  function LIBERTAS_trim(txt)
  {
    return(LIBERTAS_ltrim(LIBERTAS_rtrim(txt)));
  }


  
  // is selected text a full tags inner html?
  function LIBERTAS_isFoolTag(editor, el)
  {
    var trange = this[editor+'_rEdit'].document.selection.createRange();
    var ttext;
    if (trange != null) ttext = LIBERTAS_trim(trange.htmlText);
    if (ttext != LIBERTAS_trim(el.innerHtml))
      return false;
    else
      return true;
  }
  
  function LIBERTAS_style_change(editor, sender)
  {
    classname = sender.options[sender.selectedIndex].value;
    
    window.frames[editor+'_rEdit'].focus();     

    var el = LIBERTAS_getParentTag(editor);
    if (el != null && el.tagName.toLowerCase() != 'body')
    {
      if (classname != 'default')
        el.className = classname;
      else
        el.removeAttribute('className');
    }
    else if (el.tagName.toLowerCase() == 'body')
    {
      if (classname != 'default')
        this[editor+'_rEdit'].document.body.innerHTML = '<p class="'+classname+'">'+this[editor+'_rEdit'].document.body.innerHTML+'</p>';
      else
        this[editor+'_rEdit'].document.body.innerHTML = '<p>'+this[editor+'_rEdit'].document.body.innerHTML+'</p>';
    }
    sender.selectedIndex = 0;
  }

  function LIBERTAS_font_change(editor, sender)
  {
    fontname = sender.options[sender.selectedIndex].value;
    
    window.frames[editor+'_rEdit'].focus();     

    this[editor+'_rEdit'].document.execCommand('fontname', false, fontname);

    sender.selectedIndex = 0;
  }

  function LIBERTAS_fontsize_change(editor, sender)
  {
    fontsize = sender.options[sender.selectedIndex].value;
    
    window.frames[editor+'_rEdit'].focus();     

    this[editor+'_rEdit'].document.execCommand('fontsize', false, fontsize);

    sender.selectedIndex = 0;
  }

  function LIBERTAS_paragraph_change(editor, sender)
  {
    format = sender.options[sender.selectedIndex].value;
    
    window.frames[editor+'_rEdit'].focus();     

    this[editor+'_rEdit'].document.execCommand('formatBlock', false, format);

    sender.selectedIndex = 0;
  }
    
  function LIBERTAS_table_create_click(editor, sender)
  {
    if (window.frames[editor+'_rEdit'].document.selection.type != "Control")
    {
      // selection is not a control => insert table 
      var nt = showModalDialog('<?php echo $libertas_dir?>dialogs/table.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value, null, 
        'dialogHeight:250px; dialogWidth:366px; resizable:no; status:no');  
       
      if (nt)
      {
        window.frames[editor+'_rEdit'].focus();     
    
        var newtable = document.createElement('TABLE');
        try {
          newtable.width = (nt.width)?nt.width:'';
          newtable.height = (nt.height)?nt.height:'';
          newtable.border = (nt.border)?nt.border:'';
          if (nt.cellPadding) newtable.cellPadding = nt.cellPadding;
          if (nt.cellSpacing) newtable.cellSpacing = nt.cellSpacing;
          newtable.bgColor = (nt.bgColor)?nt.bgColor:'';
          
          // create rows
          for (i=0;i<parseInt(nt.rows);i++)
          {
            var newrow = document.createElement('TR');
            for (j=0; j<parseInt(nt.cols); j++)
            {
              var newcell = document.createElement('TD');
              newrow.appendChild(newcell);
            }
            newtable.appendChild(newrow);
          }
          var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
        	selection.pasteHTML(newtable.outerHTML);      
          LIBERTAS_toggle_borders(editor, window.frames[editor+'_rEdit'].document.body, null);
        }
        catch (excp)
        {
          alert('error');
        }
      }
    }
  }
  
  function LIBERTAS_table_prop_click(editor, sender)
  {
    window.frames[editor+'_rEdit'].focus();     

    var tTable
    // check if table selected
    if (window.frames[editor+'_rEdit'].document.selection.type == "Control")
    { 
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      if (tControl(0).tagName == 'TABLE')
      {
        tTable = tControl(0);
      }
    }
    else
    {
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      tControl = tControl.parentElement();
      while ((tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
      {
        tControl = tControl.parentElement;
      }
      if (tControl.tagName == 'TABLE')
        tTable = tControl;
      else
        return false;
    }

    var tProps = {};
    tProps.width = (tTable.style.width)?tTable.style.width:tTable.width;
    tProps.height = (tTable.style.height)?tTable.style.height:tTable.height;
    tProps.border = tTable.border;
    tProps.cellPadding = tTable.cellPadding;
    tProps.cellSpacing = tTable.cellSpacing;
    tProps.bgColor = tTable.bgColor;

    var ntProps = showModalDialog('<?php echo $libertas_dir?>dialogs/table.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value, tProps, 
      'dialogHeight:250px; dialogWidth:366px; resizable:no; status:no');  
    
    if (ntProps)
    {
      // set new settings
      tTable.width = (ntProps.width)?ntProps.width:'';
      tTable.style.width = (ntProps.width)?ntProps.width:'';
      tTable.height = (ntProps.height)?ntProps.height:'';
      tTable.style.height = (ntProps.height)?ntProps.height:'';
      tTable.border = (ntProps.border)?ntProps.border:'';
      if (ntProps.cellPadding) tTable.cellPadding = ntProps.cellPadding;
      if (ntProps.cellSpacing) tTable.cellSpacing = ntProps.cellSpacing;
      tTable.bgColor = (ntProps.bgColor)?ntProps.bgColor:'';

      LIBERTAS_toggle_borders(editor, tTable, null);
    }

    //LIBERTAS_updateField(editor,"");
  }
  
  // edits table cell properties
  function LIBERTAS_table_cell_prop_click(editor, sender)
  {
    var cd = LIBERTAS_getTD(editor); // current cell
    
    if (cd)
    {
      var cProps = {};
      cProps.width = (cd.style.width)?cd.style.width:cd.width;
      cProps.height = (cd.style.height)?cd.style.height:cd.height;
      cProps.bgColor = cd.bgColor;
      cProps.align = cd.align;
      cProps.vAlign = cd.vAlign;
      cProps.className = cd.className;
      cProps.noWrap = cd.noWrap;
      cProps.styleOptions = new Array();
      if (document.all['LIBERTAS_'+editor+'_tb_style'] != null)
      {
        cProps.styleOptions = document.all['LIBERTAS_'+editor+'_tb_style'].options;
      }
  
      var ncProps = showModalDialog('<?php echo $libertas_dir?>dialogs/td.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value, cProps, 
        'dialogHeight:220px; dialogWidth:366px; resizable:no; status:no');  
      
      if (ncProps)  
      {
        cd.align = (ncProps.align)?ncProps.align:'';
        cd.vAlign = (ncProps.vAlign)?ncProps.vAlign:'';
        cd.width = (ncProps.width)?ncProps.width:'';
        cd.style.width = (ncProps.width)?ncProps.width:'';
        cd.height = (ncProps.height)?ncProps.height:'';
        cd.style.height = (ncProps.height)?ncProps.height:'';
        cd.bgColor = (ncProps.bgColor)?ncProps.bgColor:'';
        cd.className = (ncProps.className)?ncProps.className:'';
        cd.noWrap = ncProps.noWrap;
      }      
    }
    //LIBERTAS_updateField(editor,"");
  }

  // returns current table cell  
  function LIBERTAS_getTD(editor)
  {
    if (window.frames[editor+'_rEdit'].document.selection.type != "Control")
    {
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      tControl = tControl.parentElement();
      while ((tControl.tagName != 'TD') && (tControl.tagName != 'TH') && (tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
      {
        tControl = tControl.parentElement;
      }
      if ((tControl.tagName == 'TD') || (tControl.tagName == 'TH'))
        return(tControl);
      else
        return(null);
    }
    else
    {
      return(null);
    }
  }

  // returns current table row  
  function LIBERTAS_getTR(editor)
  {
    if (window.frames[editor+'_rEdit'].document.selection.type != "Control")
    {
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      tControl = tControl.parentElement();
      while ((tControl.tagName != 'TR') && (tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
      {
        tControl = tControl.parentElement;
      }
      if (tControl.tagName == 'TR')
        return(tControl);
      else
        return(null);
    }
    else
    {
      return(null);
    }
  }
  
  // returns current table  
  function LIBERTAS_getTable(editor)
  {
    if (window.frames[editor+'_rEdit'].document.selection.type == "Control")
    { 
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      if (tControl(0).tagName == 'TABLE')
        return(tControl(0));
      else
        return(null);
    }
    else
    {
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      tControl = tControl.parentElement();
      while ((tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
      {
        tControl = tControl.parentElement;
      }
      if (tControl.tagName == 'TABLE')
        return(tControl);
      else
        return(null);
    }
  }
  
  // returns selected image
  function LIBERTAS_getImg(editor) {
    if (window.frames[editor+'_rEdit'].document.selection.type == "Control")
    { 
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      if (tControl(0).tagName == 'IMG')
        return(tControl(0));
      else
        return(null);
    }
    else
    {
      return(null);
    }
  }

  function LIBERTAS_table_row_insert_click(editor, sender)
  {
    var ct = LIBERTAS_getTable(editor); // current table
    var cr = LIBERTAS_getTR(editor); // current row

    if (ct && cr)
    {
      var newr = ct.insertRow(cr.rowIndex+1);
      for (i=0; i<cr.cells.length; i++)
      {
        if (cr.cells(i).rowSpan > 1)
        {
          // increase rowspan
          cr.cells(i).rowSpan++;
        }
        else
        {
          var newc = cr.cells(i).cloneNode();
          newr.appendChild(newc);
        }
      }
      // increase rowspan for cells that were spanning through current row
      for (i=0; i<cr.rowIndex; i++)
      {
        var tempr = ct.rows(i);
        for (j=0; j<tempr.cells.length; j++)
        {
          if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
            tempr.cells(j).rowSpan++;
        }
      }
    }
  } // insertRow
  
  function LIBERTAS_formCellMatrix(ct)
  {
    var tm = new Array();
    for (i=0; i<ct.rows.length; i++)
      tm[i]=new Array();

    for (i=0; i<ct.rows.length; i++)
    {
      jr=0;
      for (j=0; j<ct.rows(i).cells.length;j++)
      {
        while (tm[i][jr] != undefined) 
          jr++;

        for (jh=jr; jh<jr+(ct.rows(i).cells(j).colSpan?ct.rows(i).cells(j).colSpan:1);jh++)
        {
          for (jv=i; jv<i+(ct.rows(i).cells(j).rowSpan?ct.rows(i).cells(j).rowSpan:1);jv++)
          {
            if (jv==i)
            {
              tm[jv][jh]=ct.rows(i).cells(j).cellIndex;
            }
            else
            {
              tm[jv][jh]=-1;
            }
          }
        }
      }
    }
    return(tm);
  }
  
  function LIBERTAS_table_column_insert_click(editor, sender)
  {
    var ct = LIBERTAS_getTable(editor); // current table
    var cr = LIBERTAS_getTR(editor); // current row
    var cd = LIBERTAS_getTD(editor); // current row

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = LIBERTAS_formCellMatrix(ct);
      
      for (j=0; j<tm[cr.rowIndex].length; j++)
      {
        if (tm[cr.rowIndex][j] == cd.cellIndex)
        {
          realIndex=j;
          break;
        }
      }
      
      // insert column based on real cell matrix
      for (i=0; i<ct.rows.length; i++)
      {
        if (tm[i][realIndex] != -1)
        {
          if (ct.rows(i).cells(tm[i][realIndex]).colSpan > 1)
          {
            ct.rows(i).cells(tm[i][realIndex]).colSpan++;
          }
          else
          {
            var newc = ct.rows(i).insertCell(tm[i][realIndex]+1)
            var nc = ct.rows(i).cells(tm[i][realIndex]).cloneNode();
            newc.replaceNode(nc);
          }
        }
      }
    }
  } // insertColumn
  
  function LIBERTAS_table_cell_merge_right_click(editor, sender)
  {
    var ct = LIBERTAS_getTable(editor); // current table
    var cr = LIBERTAS_getTR(editor); // current row
    var cd = LIBERTAS_getTD(editor); // current row

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = LIBERTAS_formCellMatrix(ct);
      
      for (j=0; j<tm[cr.rowIndex].length; j++)
      {
        if (tm[cr.rowIndex][j] == cd.cellIndex)
        {
          realIndex=j;
          break;
        }
      }
      
      if (cd.cellIndex+1<cr.cells.length)
      {
        ccrs = cd.rowSpan?cd.rowSpan:1;
        cccs = cd.colSpan?cd.colSpan:1;
        ncrs = cr.cells(cd.cellIndex+1).rowSpan?cr.cells(cd.cellIndex+1).rowSpan:1;
        nccs = cr.cells(cd.cellIndex+1).colSpan?cr.cells(cd.cellIndex+1).colSpan:1;
        // check if theres nothing between these 2 cells
        j=realIndex;
        while(tm[cr.rowIndex][j] == cd.cellIndex) j++;
        if (tm[cr.rowIndex][j] == cd.cellIndex+1)
        {
          // proceed only if current and next cell rowspans are equal
          if (ccrs == ncrs)
          {
            // increase colspan of current cell and append content of the next cell to current
            cd.colSpan = cccs+nccs;
            cd.innerHTML += cr.cells(cd.cellIndex+1).innerHTML;
            cr.deleteCell(cd.cellIndex+1);
          }
        }
      }
    }
  } // mergeRight


  function LIBERTAS_table_cell_merge_down_click(editor, sender)
  {
    var ct = LIBERTAS_getTable(editor); // current table
    var cr = LIBERTAS_getTR(editor); // current row
    var cd = LIBERTAS_getTD(editor); // current row

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = LIBERTAS_formCellMatrix(ct);
      
      for (j=0; j<tm[cr.rowIndex].length; j++)
      {
        if (tm[cr.rowIndex][j] == cd.cellIndex)
        {
          crealIndex=j;
          break;
        }
      }
      ccrs = cd.rowSpan?cd.rowSpan:1;
      cccs = cd.colSpan?cd.colSpan:1;
      
      if (cr.rowIndex+ccrs<ct.rows.length)
      {
        ncellIndex = tm[cr.rowIndex+ccrs][crealIndex];
        if (ncellIndex != -1 && (crealIndex==0 || (crealIndex>0 && (tm[cr.rowIndex+ccrs][crealIndex-1]!=tm[cr.rowIndex+ccrs][crealIndex]))))
        {
    
          ncrs = ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).rowSpan?ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).rowSpan:1;
          nccs = ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).colSpan?ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).colSpan:1;
          // proceed only if current and next cell colspans are equal
          if (cccs == nccs)
          {
            // increase rowspan of current cell and append content of the next cell to current
            cd.innerHTML += ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).innerHTML;
            ct.rows(cr.rowIndex+ccrs).deleteCell(ncellIndex);
            cd.rowSpan = ccrs+ncrs;
          }
        }
      }
    }
  } // mergeDown
  
  function LIBERTAS_table_row_delete_click(editor, sender)
  {
    var ct = LIBERTAS_getTable(editor); // current table
    var cr = LIBERTAS_getTR(editor); // current row
    var cd = LIBERTAS_getTD(editor); // current cell

    if (cd && cr && ct)
    {
      // if there's only one row just remove the table
      if (ct.rows.length<=1)
      {
        ct.removeNode(true);
      }
      else
      {
        // get "real" cell position and form cell matrix
        var tm = LIBERTAS_formCellMatrix(ct);
        
        
        // decrease rowspan for cells that were spanning through current row
        for (i=0; i<cr.rowIndex; i++)
        {
          var tempr = ct.rows(i);
          for (j=0; j<tempr.cells.length; j++)
          {
            if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
              tempr.cells(j).rowSpan--;
          }
        }
    
        
        curCI = -1;
        // check for current row cells spanning more than 1 row
        for (i=0; i<tm[cr.rowIndex].length; i++)
        {
          prevCI = curCI;
          curCI = tm[cr.rowIndex][i];
          if (curCI != -1 && curCI != prevCI && cr.cells(curCI).rowSpan>1 && (cr.rowIndex+1)<ct.rows.length)
          {
            ni = i;
            nrCI = tm[cr.rowIndex+1][ni];
            while (nrCI == -1) 
            {
              ni++;
              if (ni<ct.rows(cr.rowIndex+1).cells.length)
                nrCI = tm[cr.rowIndex+1][ni];
              else
                nrCI = ct.rows(cr.rowIndex+1).cells.length;
            }
            
            var newc = ct.rows(cr.rowIndex+1).insertCell(nrCI);
            ct.rows(cr.rowIndex).cells(curCI).rowSpan--;
            var nc = ct.rows(cr.rowIndex).cells(curCI).cloneNode();
            newc.replaceNode(nc);
            // fix the matrix
            cs = (cr.cells(curCI).colSpan>1)?cr.cells(curCI).colSpan:1;
            for (j=i; j<(i+cs);j++)
            {
              tm[cr.rowIndex+1][j] = nrCI;
              nj = j;
            }
            for (j=nj; j<tm[cr.rowIndex+1].length; j++)
            {
              if (tm[cr.rowIndex+1][j] != -1)
                tm[cr.rowIndex+1][j]++;
            }
          }
        }
        // delete row
        ct.deleteRow(cr.rowIndex);
      }
    }
  } // deleteRow
  
  function LIBERTAS_table_column_delete_click(editor, sender)
  {
    var ct = LIBERTAS_getTable(editor); // current table
    var cr = LIBERTAS_getTR(editor); // current row
    var cd = LIBERTAS_getTD(editor); // current cell

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = LIBERTAS_formCellMatrix(ct);

      // if there's only one column delete the table
      if (tm[0].length<=1)  
      {
        ct.removeNode(true);
      }
      else
      {
        for (j=0; j<tm[cr.rowIndex].length; j++)
        {
          if (tm[cr.rowIndex][j] == cd.cellIndex)
          {
            realIndex=j;
            break;
          }
        }
        
        for (i=0; i<ct.rows.length; i++)
        {
          if (tm[i][realIndex] != -1)
          {
            if (ct.rows(i).cells(tm[i][realIndex]).colSpan>1)
              ct.rows(i).cells(tm[i][realIndex]).colSpan--;
            else
              ct.rows(i).deleteCell(tm[i][realIndex]);
          }
        }
      }
    }
  } // deleteColumn
  
  // split cell horizontally
  function LIBERTAS_table_cell_split_horizontal_click(editor, sender)
  {
    var ct = LIBERTAS_getTable(editor); // current table
    var cr = LIBERTAS_getTR(editor); // current row
    var cd = LIBERTAS_getTD(editor); // current cell

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = LIBERTAS_formCellMatrix(ct);
  
      for (j=0; j<tm[cr.rowIndex].length; j++)
      {
        if (tm[cr.rowIndex][j] == cd.cellIndex)
        {
          realIndex=j;
          break;
        }
      }
      
      if (cd.rowSpan>1) 
      {
        // split only current cell
        // find where to insert a cell in the next row
        i = realIndex;
        while (tm[cr.rowIndex+1][i] == -1) i++;
        if (i == tm[cr.rowIndex+1].length) 
          ni = ct.rows(cr.rowIndex+1).cells.length;
        else
          ni = tm[cr.rowIndex+1][i];
          
        var newc = ct.rows(cr.rowIndex+1).insertCell(ni);
        cd.rowSpan--;
        var nc = cd.cloneNode();
        newc.replaceNode(nc);
  
        cd.rowSpan = 1;
      }
      else
      {
        // add new row and make all other cells to span one row more
        ct.insertRow(cr.rowIndex+1);
        for (i=0; i<cr.cells.length; i++)
        {
          if (i != cd.cellIndex)
          {
            rs = cr.cells(i).rowSpan>1?cr.cells(i).rowSpan:1;
            cr.cells(i).rowSpan = rs+1;
          }
        }
  
        for (i=0; i<cr.rowIndex; i++)
        {
          var tempr = ct.rows(i);
          for (j=0; j<tempr.cells.length; j++)
          {
            if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
              tempr.cells(j).rowSpan++;
          }
        }
        
        // clone current cell to new row
        var newc = ct.rows(cr.rowIndex+1).insertCell(0);
        var nc = cd.cloneNode();
        newc.replaceNode(nc);
      }
    }
  } // splitH
  
  function LIBERTAS_table_cell_split_vertical_click(editor, sender)
  {
    var ct = LIBERTAS_getTable(editor); // current table
    var cr = LIBERTAS_getTR(editor); // current row
    var cd = LIBERTAS_getTD(editor); // current cell

    if (cd && cr && ct)
    {
      // get "real" cell position and form cell matrix
      var tm = LIBERTAS_formCellMatrix(ct);
  
      for (j=0; j<tm[cr.rowIndex].length; j++)
      {
        if (tm[cr.rowIndex][j] == cd.cellIndex)
        {
          realIndex=j;
          break;
        }
      }
      
      if (cd.colSpan>1)    
      {
        // split only current cell
        var newc = ct.rows(cr.rowIndex).insertCell(cd.cellIndex+1);
        cd.colSpan--;
        var nc = cd.cloneNode();
        newc.replaceNode(nc);
        cd.colSpan = 1;
      }
      else
      {
        // clone current cell
        var newc = ct.rows(cr.rowIndex).insertCell(cd.cellIndex+1);
        var nc = cd.cloneNode();
        newc.replaceNode(nc);
        
        for (i=0; i<tm.length; i++)
        {
          if (i!=cr.rowIndex && tm[i][realIndex] != -1)
          {
            cs = ct.rows(i).cells(tm[i][realIndex]).colSpan>1?ct.rows(i).cells(tm[i][realIndex]).colSpan:1;
            ct.rows(i).cells(tm[i][realIndex]).colSpan = cs+1;
          }
        }
      }
    }
  } // splitV
  

  // switch to wysiwyg mode
  function LIBERTAS_design_tab_click(editor, sender)
  {
      iText = this[editor+'_rEdit'].document.body.innerText;
      this[editor+'_rEdit'].document.body.innerHTML = iText;
      
      document.all['LIBERTAS_'+editor+'_editor_mode'].value = 'design';

      // turn off html mode toolbars
      document.all['LIBERTAS_'+editor+'_toolbar_top_html'].style.display = 'none';
      document.all['LIBERTAS_'+editor+'_toolbar_left_html'].style.display = 'none';
      document.all['LIBERTAS_'+editor+'_toolbar_right_html'].style.display = 'none';
      document.all['LIBERTAS_'+editor+'_toolbar_bottom_html'].style.display = 'none';

      // turn on design mode toolbars
      document.all['LIBERTAS_'+editor+'_toolbar_top_design'].style.display = 'inline';
      document.all['LIBERTAS_'+editor+'_toolbar_left_design'].style.display = 'inline';
      document.all['LIBERTAS_'+editor+'_toolbar_right_design'].style.display = 'inline';
      document.all['LIBERTAS_'+editor+'_toolbar_bottom_design'].style.display = 'inline';
      
      // turn on invisible borders if needed
      LIBERTAS_toggle_borders(editor,this[editor+'_rEdit'].document.body, null);
      
      this[editor+'_rEdit'].focus();
  }
  
  // switch to html mode
  function LIBERTAS_html_tab_click(editor, sender)
  {
      iHTML = this[editor+'_rEdit'].document.body.innerHTML;
      this[editor+'_rEdit'].document.body.innerText = iHTML;
      
      document.all['LIBERTAS_'+editor+'_editor_mode'].value = 'html';

      // turn off design mode toolbars
      document.all['LIBERTAS_'+editor+'_toolbar_top_design'].style.display = 'none';
      document.all['LIBERTAS_'+editor+'_toolbar_left_design'].style.display = 'none';
      document.all['LIBERTAS_'+editor+'_toolbar_right_design'].style.display = 'none';
      document.all['LIBERTAS_'+editor+'_toolbar_bottom_design'].style.display = 'none';

      // turn on html mode toolbars
      document.all['LIBERTAS_'+editor+'_toolbar_top_html'].style.display = 'inline';
      document.all['LIBERTAS_'+editor+'_toolbar_left_html'].style.display = 'inline';
      document.all['LIBERTAS_'+editor+'_toolbar_right_html'].style.display = 'inline';
      document.all['LIBERTAS_'+editor+'_toolbar_bottom_html'].style.display = 'inline';

      this[editor+'_rEdit'].focus();
  }
  
  function LIBERTAS_getFieldByEditor(editor, field)
  {
    var thefield;
    // get field by editor name if no field passed
    if (field == null || field == "")
    {
      var flds = document.getElementsByName(editor);
      thefield = flds[0].id;
    }
    else
    {
      thefield=field;
    }
    return thefield;
  }
  
  function LIBERTAS_getHtmlValue(editor, thefield)
  {
    var htmlvalue;

    if(document.all['LIBERTAS_'+editor+'_editor_mode'].value == 'design')
    {
      // wysiwyg
      htmlvalue = this[editor+'_rEdit'].document.body.innerHTML;
    }
    else
    {
      // code
      htmlvalue = this[editor+'_rEdit'].document.body.innerText;
    }
    return htmlvalue;
  }
  
  function LIBERTAS_updateField(editor, field)
  {  
    var thefield = LIBERTAS_getFieldByEditor(editor, field);
    
    var htmlvalue = LIBERTAS_getHtmlValue(editor, thefield);

    if (document.all[thefield].value != htmlvalue)
    {
      // something changed
      document.all[thefield].value = htmlvalue;
    }
  }

  function LIBERTAS_confirm(editor,block,message) {
    return showModalDialog('<?php echo $libertas_dir?>dialogs/confirm.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value + '&block=' + block + '&message=' + message, null, 'dialogHeight:100px; dialogWidth:300px; resizable:no; status:no');  
  }
  
  // cleanup html
  function LIBERTAS_cleanup_click(editor, sender)
  {
    if (LIBERTAS_confirm(editor,'cleanup','confirm'))
    {
      window.frames[editor+'_rEdit'].focus();     
  
      var found = true;
      while (found)
      {
        found = false;
        var els = window.frames[editor+'_rEdit'].document.body.all;
        for (i=0; i<els.length; i++)
        {
          // remove tags with urns set
          if (els[i].tagUrn != null && els[i].tagUrn != '')
          {
            els[i].removeNode(false);
            found = true;
          } 
          
          // remove font and span tags
          if (els[i].tagName != null && (els[i].tagName == "FONT" || els[i].tagName == "SPAN" || els[i].tagName == "DIV"))
          {
            els[i].removeNode(false);
            found = true;
          }
        }      
      }
      
      // remove styles
      var els = window.frames[editor+'_rEdit'].document.body.all;
      for (i=0; i<els.length; i++)
      {
        // remove style and class attributes from all tags
        els[i].removeAttribute("className",0);
        els[i].removeAttribute("style",0);
        
      }
    }
  } // LIBERTAS_cleanup_click
  
  // toggle borders worker function
  function LIBERTAS_toggle_borders(editor, root, toggle)
  {
    // get toggle mode (on/off)
    var toggle_mode = toggle;
    if (toggle == null)
    {
      var tgl_borders = document.getElementById("LIBERTAS_"+editor+"_borders");
      if (tgl_borders != null)
      {
        toggle_mode = tgl_borders.value;
      }
      else
      {
        toggle_mode = "on"
      }
    }
    
    var tbls = new Array();
    if (root.tagName == "TABLE")
    {
      tbls[0] = root;
    }
    else
    {
      // get all tables starting from root
      tbls = root.getElementsByTagName("TABLE");
    }
    
    var tbln = 0;
    if (tbls != null) tbln = tbls.length;
    for (ti = 0; ti<tbln; ti++)
    {
      if ((tbls[ti].style.borderWidth == 0 || tbls[ti].style.borderWidth == "0px") &&
          (tbls[ti].border == 0 || tbls[ti].border == "0px") &&
          (toggle_mode == "on"))
      {
        tbls[ti].runtimeStyle.borderWidth = "1px";
        tbls[ti].runtimeStyle.borderStyle = "dashed";
        tbls[ti].runtimeStyle.borderColor = "#aaaaaa";
      } // no border
      else 
      {
        tbls[ti].runtimeStyle.borderWidth = "";
        tbls[ti].runtimeStyle.borderStyle = "";
        tbls[ti].runtimeStyle.borderColor = "";
      }
        
      var cls = tbls[ti].cells;
      // loop through cells
      for (ci = 0; ci<cls.length; ci++)
      {
        if ((tbls[ti].style.borderWidth == 0 || tbls[ti].style.borderWidth == "0px") &&
            (tbls[ti].border == 0 || tbls[ti].border == "0px") && 
            (cls[ci].style.borderWidth == 0 || cls[ci].style.borderWidth == "0px") && 
            (toggle_mode == "on"))
        {
          cls[ci].runtimeStyle.borderWidth = "1px";
          cls[ci].runtimeStyle.borderStyle = "dashed";
          cls[ci].runtimeStyle.borderColor = "#aaaaaa";
        }
        else 
        {
          cls[ci].runtimeStyle.borderWidth = "";
          cls[ci].runtimeStyle.borderStyle = "";
          cls[ci].runtimeStyle.borderColor = "";
        }
      } // cells loop
    } // tables loop
  } // LIBERTAS_toggle_borders
  
  // toggle borders click event 
  function LIBERTAS_toggle_borders_click(editor, sender)
  {
    // get current toggle mode (on/off)
    var toggle_mode;

    var tgl_borders = document.getElementById("LIBERTAS_"+editor+"_borders");
    if (tgl_borders != null)
    {
      toggle_mode = tgl_borders;

      // switch mode    
      if (toggle_mode.value == "on")
      {
        toggle_mode.value = "off";
      }
      else
      {
        toggle_mode.value = "on";
      }

      // call worker function
      LIBERTAS_toggle_borders(editor,this[editor+'_rEdit'].document.body, toggle_mode.value);
    }
  } // LIBERTAS_toggle_borders_click