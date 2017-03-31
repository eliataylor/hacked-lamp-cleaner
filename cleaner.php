<?php
require_once('badFlags.php');
require_once('scanDir.php');
$args = $argv;

$dir = (isset($argv[1]) && !empty($argv[1])) ? $argv[1] : '';
$recursive = (isset($argv[2]) && !empty($argv[2])) ? (bool)$argv[2] : FALSE;
$aggressive = (isset($argv[3]) && !empty($argv[3])) ? (bool)$argv[3] : FALSE;
$file_ext = array("html","php");

$context = explode('/', rtrim(getcwd(), '/'));
$context = end($context);
if ($context != 'hacked') {
  die('create a folder called hacked and run from within it so we only have 1 __badcode__ folder created');
}


if (!is_dir('__badcode__')) {
  mkdir('__badcode__');
}
if (!file_exists('infected.json')) {
    file_put_contents('infected.json', '');
    $corrections = array();
} else {
  $corrections = file_get_contents('infected.json');
  $corrections = json_decode($corrections, TRUE);
}

$files = scanDir::scan(explode(',', $dir), $file_ext, $recursive); // TODO: make recursive to first clean than crawl
foreach($files as $file) {
  $basename = basename($file);
  $basename = substr(basename($file), 0, strpos($basename, '.'));

  if (stripos($file, 'cleaner.php') > 0) continue;

  $haystack = file_get_contents($file);
  if ($aggressive === TRUE)  $haystack = preg_replace('/\s+/', ' ', $haystack); // WARN: ignores whitespace differences

  foreach($badFlags as $badFlag=>$context) {
    if ($aggressive === TRUE)  {
      $badFlag = preg_replace('/\s+/', ' ', $badFlag); // WARN: ignores whitespace
      if ($context) $context['suffix'] = preg_replace('/\s+/', ' ', $context['suffix']);
    }
    if (!isset($corrections[$badFlag])) $corrections[$badFlag] = array();

    if (stripos($haystack, $badFlag) > -1) {

        if ($context === FALSE) {
          $haystack = str_replace($badFlag, '', $haystack);
          $badCodeID = md5($badFlag);
          $badFilename = '__badcode__/' . $badCodeID . '.txt';
          file_put_contents($badFilename, $badFlag);
          if (!isset($corrections[$badFlag][$badCodeID])) $corrections[$badFlag][$badCodeID] = array($file=>0);
          else if (!isset($corrections[$badFlag][$badCodeID][$file])) $corrections[$badFlag][$badCodeID][$file] = 0;
          $corrections[$badFlag][$badCodeID][$file]++;
        } else {
          $parts = explode($badFlag, $haystack); // WARN: explode is not case insensitive

          foreach($parts as $b) {
            if (stripos($b, $context['suffix']) < 0) {
              die('Missing suffix: ' . $suffix . ' on bagFlag: ' . $badFlag);
            }
            $badCodeID = substr($b, 0, strpos($b, $context['suffix']));
            $badCodeID = preg_replace('/\s+/', ' ', $badCodeID);
            if (strlen($badCodeID) < 2) { // empty
              $badCodeID = $badFlag . $context['suffix'];
            }
            $badCodeID = md5($badCodeID);
            $badFilename = '__badcode__/' . $badCodeID . '.txt';

            if (!isset($corrections[$badFlag][$badCodeID])) $corrections[$badFlag][$badCodeID] = array($file=>0);
            else if (!isset($corrections[$badFlag][$badCodeID][$file])) $corrections[$badFlag][$badCodeID][$file] = 0;

            $corrections[$badFlag][$badCodeID][$file]++;

            $remove = $badFlag; // remove badCode with badFlag and suffix
            $remove .= substr($b, 0, strpos($b, $context['suffix']) + strlen($context['suffix'])); // and suffix
            $haystack = str_replace($remove, '', $haystack);
            file_put_contents($badFilename, $remove);


          }
        }
    }
  }
  file_put_contents($file, $haystack);
}

file_put_contents('infected.json', json_encode($corrections));
var_dump($corrections);
