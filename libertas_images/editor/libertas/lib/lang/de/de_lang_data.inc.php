<?php
// =========================================================
// libertas PHP WYSIWYG editor control
// =========================================================
// German language file
// =========================================================
// Developed: Alan Mendelevich, alan@solmetra.lt
// German translation: Simon Schmitz, schmitz@unitedfuor.com
// Corrections: Matthias Höschele, matthias.hoeschele@gmx.net
// Copyright: Solmetra (c)2003 All rights reserved.
// ---------------------------------------------------------
//                                www.solmetra.com
// =========================================================
// v.1.0, 2003-04-10
// =========================================================

// charset to be used in dialogs
$libertas_lang_charset = 'iso-8859-1';

// language text data array
// first dimension - block, second - exact phrase
// alternative text for toolbar buttons and title for dropdowns - 'title'

$libertas_lang_data = array(
  'cut' => array(
    'title' => 'Ausschneiden'
  ),
  'copy' => array(
    'title' => 'Kopieren'
  ),
  'paste' => array(
    'title' => 'Einfügen'
  ),
  'undo' => array(
    'title' => 'Rückgängig'
  ),
  'redo' => array(
    'title' => 'Wiederherstellen'
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink'
  ),
  'image_insert' => array(
    'title' => 'Bild einfügen',
    'select' => 'Auswählen',
    'cancel' => 'Abbrechen',
    'library' => 'Bibliothek',
    'preview' => 'Vorschau',
    'images' => 'Bild',
    'upload' => 'Bild Hochladen',
    'upload_button' => 'Hochladen',
    'error' => 'Fehler',
    'error_no_image' => 'Wählen Sie bitte ein Bild',
    'error_uploading' => 'Ein Fehler trat bei der Übertragung der Datei auf.  Bitte Versuchen Sie es später noch einmal.',
    'error_wrong_type' => 'Falscher Bilddatei Typ',
    'error_no_dir' => 'Bibliothek ist physikalisch vorhanden',
  ),
  'image_prop' => array(
    'title' => 'Bildeigenschaften',
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
    'source' => 'Quelle',
    'alt' => 'Alternativer Text',
    'align' => 'Ausrichtung',
    'left' => 'Links',
    'right' => 'Rechts',
    'top' => 'Oberseite',
    'middle' => 'Mitte',
    'bottom' => 'Unterseite',
    'absmiddle' => 'Absolute Mitte',
    'texttop' => 'TextTop',
    'baseline' => 'Grundlinie',
    'width' => 'Breite',
    'height' => 'Höhe',
    'border' => 'Rand',
    'hspace' => 'Horizontaler Abstand',
    'vspace' => 'Vertikaler Abstand',
    'error' => 'Fehler',
    'error_width_nan' => 'Die Breite ist keine Zahl',
    'error_height_nan' => 'Die Höhe ist keine Zahl',
    'error_border_nan' => 'Der Rand ist keine Zahl',
    'error_hspace_nan' => 'Horizontaler Abstand ist keine Zahl',
    'error_vspace_nan' => 'Vertikaler Abstand ist keine Zahl',
  ),
  'hr' => array(
    'title' => 'Horizontale Linie'
  ),
  'table_create' => array(
    'title' => 'Tabelle erstellen'
  ),
  'table_prop' => array(
    'title' => 'Tabelleneigenschaften',
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
    'rows' => 'Zeilen',
    'columns' => 'Spalten',
    'width' => 'Breite',
    'height' => 'Höhe',
    'border' => 'Rand',
    'pixels' => 'Pixel',
    'cellpadding' => 'Zellauffüllung',
    'cellspacing' => 'Zellabstand',
    'bg_color' => 'Hintergrundfarbe',
    'error' => 'Fehler',
    'error_rows_nan' => 'Die Zeilenanzahl ist keine Zahl',
    'error_columns_nan' => 'Die Spaltenanzahl ist keine Zahl',
    'error_width_nan' => 'Die Breite ist keine Zahl',
    'error_height_nan' => 'Die Höhe ist keine Zahl',
    'error_border_nan' => 'Die Randbreite ist keine Zahl',
    'error_cellpadding_nan' => 'Zellauffüllung ist keine Zahl',
    'error_cellspacing_nan' => 'Zellabstand ist keine Zahl',
  ),
  'table_cell_prop' => array(
    'title' => 'Zelleigenschaften',
    'horizontal_align' => 'Horizontale Ausrichtung',
    'vertical_align' => 'Vertikale Ausrichtung',
    'width' => 'Breite',
    'height' => 'Höhe',
    'css_class' => 'CSS Klasse',
    'no_wrap' => 'Zeilenumbruch verhindern',
    'bg_color' => 'Hintergrundfarbe',
    'ok' => '   OK   ',
    'cancel' => 'Abbrechen',
    'left' => 'Links',
    'center' => 'Zentriert',
    'right' => 'Rechts',
    'top' => 'Oberseite',
    'middle' => 'Mitte',
    'bottom' => 'Unterseite',
    'baseline' => 'Grundlinie',
    'error' => 'Fehler',
    'error_width_nan' => 'Die Breite ist keine Zahl',
    'error_height_nan' => 'Die Höhe ist keine Zahl',
    
  ),
  'table_row_insert' => array(
    'title' => 'Zeile einfügen'
  ),
  'table_column_insert' => array(
    'title' => 'Spalte einfügen'
  ),
  'table_row_delete' => array(
    'title' => 'Zeile löschen'
  ),
  'table_column_delete' => array(
    'title' => 'Spalte löschen'
  ),
  'table_cell_merge_right' => array(
    'title' => 'Zelle verbinden nach rechts.'
  ),
  'table_cell_merge_down' => array(
    'title' => 'Zelle verbinden nach unten.'
  ),
  'table_cell_split_horizontal' => array(
    'title' => 'Zelle horizontal aufteilen'
  ),
  'table_cell_split_vertical' => array(
    'title' => 'Zelle vertikal aufteilen'
  ),
  'style' => array(
    'title' => 'Style'
  ),
  'font' => array(
    'title' => 'Schrift'
  ),
  'fontsize' => array(
    'title' => 'Grösse'
  ),
  'paragraph' => array(
    'title' => 'Punkt'
  ),
  'bold' => array(
    'title' => 'Fett'
  ),
  'italic' => array(
    'title' => 'Kursiv'
  ),
  'underline' => array(
    'title' => 'Unterstrichen'
  ),
  'ordered_list' => array(
    'title' => 'Nummerierung'
  ),
  'bulleted_list' => array(
    'title' => 'Aufzählung'
  ),
  'indent' => array(
    'title' => 'Einzug vergrössern'
  ),
  'unindent' => array(
    'title' => 'Einzug verkleinern'
  ),
  'left' => array(
    'title' => 'Links'
  ),
  'center' => array(
    'title' => 'Zentriert'
  ),
  'right' => array(
    'title' => 'Rechts'
  ),
  'fore_color' => array(
    'title' => 'Schriftfarbe'
  ),
  'bg_color' => array(
    'title' => 'Hintergrundfarbe'
  ),
  'design_tab' => array(
    'title' => 'Zum WYSIWYG (Design) Modus wechseln'
  ),
  'html_tab' => array(
    'title' => 'Zum HTML (Quelltext) Modus wechseln'
  ),
  'colorpicker' => array(
    'title' => 'Farbpipette',
    'ok' => '   OK   ',
    'cancel' => 'Abbruch',
  ),
  // <<<<<<<<< NEW >>>>>>>>>
  'cleanup' => array(
    'title' => 'HTML cleanup (remove styles)',
    'confirm' => 'Performing this action will remove all styles, fonts and useless tags from the current content. Some or all your formatting may be lost.',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'toggle_borders' => array(
    'title' => 'Toggle borders',
  ),
  'hyperlink' => array(
    'title' => 'Hyperlink',
    'url' => 'URL',
    'name' => 'Name',
    'target' => 'Target',
    'title_attr' => 'Title',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'table_row_prop' => array(
    'title' => 'Row properties',
    'horizontal_align' => 'Horizontal align',
    'vertical_align' => 'Vertical align',
    'css_class' => 'CSS class',
    'no_wrap' => 'No wrap',
    'bg_color' => 'Background color',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
    'left' => 'Left',
    'center' => 'Center',
    'right' => 'Right',
    'top' => 'Top',
    'middle' => 'Middle',
    'bottom' => 'Bottom',
    'baseline' => 'Baseline',
  ),
  'symbols' => array(
    'title' => 'Special characters',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'templates' => array(
    'title' => 'Templates',
  ),
  'page_prop' => array(
    'title' => 'Page properties',
    'title_tag' => 'Title',
    'charset' => 'Charset',
    'background' => 'Background image',
    'bgcolor' => 'Background color',
    'text' => 'Text color',
    'link' => 'Link color',
    'vlink' => 'Visited link color',
    'alink' => 'Active link color',
    'leftmargin' => 'Left margin',
    'topmargin' => 'Top margin',
    'css_class' => 'CSS class',
    'ok' => '   OK   ',
    'cancel' => 'Cancel',
  ),
  'preview' => array(
    'title' => 'Preview',
  ),
  'image_popup' => array(
    'title' => 'Image popup',
  ),
  'zoom' => array(
    'title' => 'Zoom',
  ),
);
?>

