<?php
// BASH COMMAND SETUP

// 1. Create a folder called "hacked" anywhere on your infected server or locally for testing
// $mkdir hacked

// 2. Copy all index.php and index.html files on the infected server to test this script
// $rsync -rav -e ssh --include '*/' --include='index.php' --exclude='*' username@server.com:/* tests/
// $rsync -rav -e ssh --include '*/' --include='index.html' --exclude='*' username@server.com:/* tests/

// 3. Delete the empty directoies this recursive rsync command creates
// $find tests/ -type d -empty -delete

// 4. make a permanent copy of those files since /test is overwritten
// $cp -R tests/* __vault__

// 5å. Execute CLI
// $php cleaner.php tests true  (first param is the directory, second is to run recursively or not)


require_once('scanDir.php');
$args = $argv;

$dir = (isset($argv[1]) && !empty($argv[1])) ? $argv[1] : '';
$recursive = (isset($argv[2]) && !empty($argv[2])) ? (bool)$argv[2] : false;
$file_ext = array("html","php");

$context = explode('/', rtrim(getcwd(), '/'));
$context = end($context);
if ($context != 'hacked') {
  die('create a folder called hacked and run from within it');
}

if (!is_dir('__stripped__')) {
  mkdir('__stripped__');
}

/*
if ($dir === 'tests') {
  $shell_result_output = shell_exec('cp -R tests/* __vault__');
  echo ' BACKED UP test files to __vault__';
}
*/

$files = scanDir::scan(explode(',', $dir), $file_ext, $recursive);
$corrections = array();

foreach($files as $file) {
  $basename = basename($file);
  if (stripos($file, 'cleaner.php') > 0) continue;
  $haystack = file_get_contents($file);

  $badFlags = array(
    "eval(base64_decode("=>array('suffix'=>')'),
    'path=substr($absolutepath,0,strpos($absolutepath,$localpath));include_once($root_path."/d730d81e7o133a51c2bddc5c68874ce.zip")'=>array('suffix'=>';'),
    '@include "\x2f'=>array('suffix'=>'";')
  );
  foreach($badFlags as $badFlag=>$context) {
    if (!isset($corrections[$badFlag])) $corrections[$badFlag] = array();
    if (stripos($haystack, $badFlag) > -1) {
        $parts = explode($badFlag, $haystack);
        foreach($parts as $b) {
          $badCode = substr($b, 0, strpos($b, $context['suffix']));
          $badFilename = $basename;
          $badFilename .= '-' . md5($badCode);
          file_put_contents('__stripped__/' . $badFilename . '.txt', $badCode);
          $haystack = str_replace($badFlag . $badCode . $context['suffix'], '', $haystack);
        }
        file_put_contents($file, $haystack);
        $corrections[$badFlag][] = $file . ' stripped ' . $badFlag;
    }
  }
}

var_dump($corrections);

?>
