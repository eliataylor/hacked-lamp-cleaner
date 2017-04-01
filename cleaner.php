<?php
require_once('badFlags.php');
require_once('scanDir.php');
$args = $argv;

$dir = (isset($argv[1]) && !empty($argv[1])) ? $argv[1] : '';
$file_types = (isset($argv[2]) && !empty($argv[2])) ? $argv[2] : "html,php";
$file_types = explode(",", $file_types);

$recursive = (isset($argv[3]) && !empty($argv[3])) ? (bool) $argv[3] : FALSE;
$aggressive = (isset($argv[4]) && !empty($argv[4])) ? (bool) $argv[4] : FALSE;

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

$files = scanDir::scan(explode(',', $dir), $file_types, $recursive); // TODO: make recursive to first clean than crawl
foreach($files as $file) {
  $basename = basename($file);
  $basename = substr($basename, 0, strpos($basename, '.'));

  if (stripos($file, 'cleaner.php') > 0) continue;

  $updateFile = file_get_contents($file);
  $haystack = strtolower($updateFile);
  if ($aggressive === TRUE) {
    $haystack = preg_replace('/\s+/', ' ', $haystack); // WARN: ignores whitespace differences
  }

  foreach($badFlags as $badFlag=>$context) {
    $badFlag = strtolower($badFlag);
    if (isset($context['suffix'])) $context['suffix'] = strtolower($context['suffix']);

    if ($aggressive === TRUE)  {
      $badFlag = preg_replace('/\s+/', ' ', $badFlag); // WARN: ignores whitespace
      if (isset($context['suffix'])) $context['suffix'] = preg_replace('/\s+/', ' ', $context['suffix']);
    }

    if (!isset($corrections[$badFlag])) $corrections[$badFlag] = array();

    if (strpos($haystack, $badFlag) > -1) { // WARN: possible false-negatives when aggressive

        if ($context === FALSE) {
          $haystack = str_replace($badFlag, '', $haystack);
          $updateFile = str_ireplace($badFlag, '', $updateFile);

          $badCodeID = md5(preg_replace('/\s+/', ' ', $badFlag)); // always ignore whitespace for file ID
          $badFilename = '__badcode__/' . $badCodeID . '.txt';
          file_put_contents($badFilename, $badFlag);
          if (!isset($corrections[$badFlag][$badCodeID])) $corrections[$badFlag][$badCodeID] = array($file=>0);
          else if (!isset($corrections[$badFlag][$badCodeID][$file])) $corrections[$badFlag][$badCodeID][$file] = 0;
          $corrections[$badFlag][$badCodeID][$file]++;
        } else if (isset($context['suffix'])) {
          while (strpos($haystack, $badFlag) > -1) {
            $remove = substr($haystack, strpos($haystack, $badFlag));
            if (strpos($remove, $context['suffix']) < 0) {
              die('Lost suffix: ' . $suffix . ' on badFlag: ' . $badFlag); // WARN: can happen another badFlag stripped only part of this one
            }
            $remove = substr($remove, 0, strpos($remove, $context['suffix']) + strlen($context['suffix']));
            $haystack = str_replace($remove, '', $haystack);
            $updateFile = str_ireplace($remove, '', $updateFile);

            $badCodeID = md5(preg_replace('/\s+/', ' ', $remove));
            $badFilename = '__badcode__/' . $badCodeID . '.txt';
            file_put_contents($badFilename, $remove);

            if (!isset($corrections[$badFlag][$badCodeID])) $corrections[$badFlag][$badCodeID] = array($file=>0);
            else if (!isset($corrections[$badFlag][$badCodeID][$file])) $corrections[$badFlag][$badCodeID][$file] = 0;

            $corrections[$badFlag][$badCodeID][$file]++;

          }

        } else {
          var_dump($context);
          die('illegal flag value:  ' . $badFlag);
        }
      }
  }
  if ($aggressive === TRUE)  {
    file_put_contents($file, $haystack);
  } else {
    file_put_contents($file, $updateFile);
  }
}

file_put_contents('infected.json', json_encode($corrections));
var_dump($corrections);
