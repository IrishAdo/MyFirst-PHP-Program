<?php 
  header('Content-Type: application/x-javascript');
?>
// toolbar button effects
function LIBERTAS_default_bt_over(ctrl)
{
//  ctrl.className = "LIBERTAS_default_tb_over";
  var imgfile = ctrl.src.substr(0, ctrl.src.length-4) + "_over.gif";
  ctrl.src = imgfile;
}
function LIBERTAS_default_bt_out(ctrl)
{
//  ctrl.className = "LIBERTAS_default_tb_out";
  var imgfile = ctrl.src.substr(0, ctrl.src.length-9) + ".gif";
  ctrl.src = imgfile;
}
function LIBERTAS_default_bt_down(ctrl)
{
  ctrl.className = "LIBERTAS_default_tb_down";
}
function LIBERTAS_default_bt_up(ctrl)
{
  ctrl.className = "LIBERTAS_default_tb_out";
}

