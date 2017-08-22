<?php 
// ================================================
// libertas PHP WYSIWYG editor control
// ================================================
// Default toolbar data file
// ================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// Copyright: Solmetra (c)2003 All rights reserved.
// ------------------------------------------------
//                                www.solmetra.com
// ================================================
// v.1.0, 2003-03-22
// ================================================

// array to hold toolbar definitions
// first dimension - toolbar location (top, left, right, bottom)
// second dimension - toolbar row/column
// third dimension - settings/data
// fourth dimension - setting/toolbar item
// toolbar item: name - item name, type - item type (button, dropdown, separator, etc.)

$libertas_toolbar_data = array(
  'top_design' => array(
      array(
        'settings' => array(
          'align' => 'left',
          'valign' => 'top'
        ),
        'data' => array (
            array(
              'name' => 'cut',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'copy',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'paste',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => LIBERTAS_TBI_IMAGE
            ),
            array(
              'name' => 'undo',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'redo',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => LIBERTAS_TBI_IMAGE
            ),
            array(
              'name' => 'hyperlink',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'image_insert',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'image_prop',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'hr',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => LIBERTAS_TBI_IMAGE
            ),
            array(
              'name' => 'table_create',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'table_prop',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'table_cell_prop',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'table_row_insert',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'table_column_insert',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'table_row_delete',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'table_column_delete',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'table_cell_merge_right',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'table_cell_merge_down',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'table_cell_split_horizontal',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'table_cell_split_vertical',
              'type' => LIBERTAS_TBI_BUTTON
            )
        ) // data
      ),
      array(
        'settings' => array(
          'align' => 'left',
          'valign' => 'top'
        ),
        'data' => array (
            array(
              'name' => 'style',
              'type' => LIBERTAS_TBI_DROPDOWN
            ),
            array(
              'name' => 'vertical_separator',
              'type' => LIBERTAS_TBI_IMAGE
            ),
            array(
              'name' => 'bold',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'italic',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'underline',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => LIBERTAS_TBI_IMAGE
            ),
            array(
              'name' => 'ordered_list',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'bulleted_list',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => LIBERTAS_TBI_IMAGE
            ),
            array(
              'name' => 'indent',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'unindent',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => LIBERTAS_TBI_IMAGE
            ),
            array(
              'name' => 'left',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'center',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'right',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => LIBERTAS_TBI_IMAGE
            ),
            array(
              'name' => 'fore_color',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'bg_color',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => LIBERTAS_TBI_IMAGE
            ),
            array(
              'name' => 'cleanup',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'toggle_borders',
              'type' => LIBERTAS_TBI_BUTTON
            ),
        ) // data
      ),
  ),

  'top_html' => array(
      array(
        'settings' => array(
          'align' => 'left',
          'valign' => 'top'
        ),
        'data' => array (
            array(
              'name' => 'cut',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'copy',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'paste',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'vertical_separator',
              'type' => LIBERTAS_TBI_IMAGE
            ),
            array(
              'name' => 'undo',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'redo',
              'type' => LIBERTAS_TBI_BUTTON
            ),
        ) // data
      ),
  ),
  
  'bottom_design' => array(
      array(
        'settings' => array(
          'align' => 'right',
          'valign' => 'top'
        ),
        'data' => array (
            array(
              'name' => 'design_tab_on',
              'type' => LIBERTAS_TBI_IMAGE
            ),
            array(
              'name' => 'html_tab',
              'type' => LIBERTAS_TBI_BUTTON
            ),
        ) // data
      )
  ),

  'bottom_html' => array(
      array(
        'settings' => array(
          'align' => 'right',
          'valign' => 'top'
        ),
        'data' => array (
            array(
              'name' => 'design_tab',
              'type' => LIBERTAS_TBI_BUTTON
            ),
            array(
              'name' => 'html_tab_on',
              'type' => LIBERTAS_TBI_IMAGE
            ),
        ) // data
      )
  ),
);
?>
