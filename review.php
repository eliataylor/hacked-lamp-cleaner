<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://ogp.me/ns/fb#" lang="en">
<head>
<meta name="author" content="E.A.Taylor" />
<meta name="language" content="en-us" />
<style type="text/css">
  p, li { margin:0 }
</style>
<body>
<?php

if (!file_exists('infected.json')) {
 echo('run `php cleaner.php tests true` first');
} else{
  $badFlags = json_decode(file_get_contents('infected.json'), TRUE);
  foreach($badFlags as $badFlag => $badBlocks) {
    if (count($badBlocks) < 1) {
      echo '<section><p>No matches found. Check badFlags or ignore whitespace</p><pre>'.$badFlag.'</pre></section>';
    }  else {
      echo '<section><h1>Bad Flag</h1><pre>'.$badFlag.'</pre>';
      echo '<h2>Number of variations of code starting with this flag: '.count($badBlocks).'</h2>';
      foreach($badBlocks as $badCodeID => $files) {
        $badFilename = '__badcode__/' . $badCodeID . '.txt';
        $link = $badFlag . ' &nbsp;&nbsp; <a target="_blank" href="'.$badFilename.'">'.$badFilename.'</a>';
        echo '<h4 style="text-indent:10px;" >'.$link.'</h4>';
        foreach($files as $file => $count) {
            $modified = 'file:///' . getcwd() . '/' . $file; // WARN: php should not executed on file protocol
            $original = str_replace('/hacked/tests/', '/hacked/__vault__/', $modified);
            $link = '<li><p>'.$count.' Modified <a target="_blank" href="'.$modified.'">'.$file.'</a></p>';
            if (file_exists($original)) {
              $link .= ' <p>Original <a target="_blank" href="'.$original.'">'.$original.'</a></p>';
            }
            $link .=  '</li>';
            echo '<ul>'.$link.'</ul>';
        }
      }
      echo '</section>';
    }
  }
}

?>
</body>
</html>
