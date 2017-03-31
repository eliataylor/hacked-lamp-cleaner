<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#" lang="en">
<head>
<meta name="author" content="E.A.Taylor" />
<meta name="language" content="en-us" />
<body>
<?php

if (!file_exists('infected.json')) {
 echo('run `php cleaner.php tests true` first');
} else{
  $badFlags = json_decode(file_get_contents('infected.json'), TRUE);
  foreach($badFlags as $badFlag => $badBlocks) {
    echo '<section><h1>Bad Flag</h1><pre>'.$badFlag.'</pre>';
    echo '<h2>Number of variations of code starting with this flag: '.count($badBlocks).'</h2>';
    foreach($badBlocks as $badCodeID => $files) {
      $badFilename = '__badcode__/' . $badCodeID . '.txt';
      $link = '<a target="_blank" href="'.$badFilename.'">'.$badFilename.'</a>';
      echo '<h4 style="text-indent:10px;" >'.$link.'</h4>';
      foreach($files as $file => $count) {
          $badFilename = 'file:///' . getcwd() . '/' . $file; // WARN: php should not executed on file protocol
          $link = '<a target="_blank" href="'.$badFilename.'">'.$badFilename.'</a> contains ' . $count . ' of this badFlag';
          echo '<p style="text-indent:20px;" >'.$link.'</p>';
      }
    }
    echo '</section>';
  }
}

?>
</body>
</html>
