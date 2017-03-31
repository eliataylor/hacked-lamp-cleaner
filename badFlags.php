<?php

$badFlags = array(
  'error_reporting(0);ini_set("display_errors", 0);$localpath=getenv("SCRIPT_NAME");$absolutepath=getenv("SCRIPT_FILENAME");$root_path=substr($absolutepath,0,strpos($absolutepath,$localpath));'=>false,
  '$root_path=substr($absolutepath,0,strpos($absolutepath,$localpath));'=>false,
  'include_once($root_path."/d730d81e7o133a51c2bddc5c68874ce.zip")'=>false,
  '_once($root_path."/d730d81e7o133a51c2bddc5c68874ce.zip")'=>false,
  'error_reporting(0);ini_set("display_errors",0);$localpath=getenv("SCRIPT_NAME");$absolutepath=getenv("SCRIPT_FILENAME");'=>false,
  'eval(base64_decode("'=>array('suffix'=>'"))'),
  'eval(base64_decode(\''=>array('suffix'=>'\'))'),
  '@include \'\x2f'=>array('suffix'=>'\''),
  '@include "\x2f'=>array('suffix'=>'"'),
  'include \'\x2f'=>array('suffix'=>'\''),
  'include "\x2f'=>array('suffix'=>'"'),
  "___bdec('"=>array('suffix'=>"')"),
  "___bdec(\""=>array('suffix'=>"\""),
  "\$xml = \$root_path . '/xm1rpc.php';"=>array('suffix'=>'return $output; }')
);
