# Cleaner script for hacked LAMP servers
## scan / copy / replace / review injected malicious code -  `"eval(base64_decode(..."` - from any files your server

```
php cleaner.php /path/to/clean html,php,extensions recursive|not aggressive|not
```
#### Examples:
```
php cleaner.php / html,php 1 1 // clean all .html and .php files from root while removing whitespace from all files
php cleaner.php /folder html 0 // clean only .html files, only inside /folder, strictly matching whitespace
```


## CONFIGURE & TEST:

1. Checkout the code inside of a /hacked folder
```
git clone https://github.com/eliataylor/hacked-lamp-cleaner.git hacked --depth=1
cd hacked
```


2. Configure your Bad Flags.
Each row's key is matched against every file searched. If its value is FALSE, we just remove the key; else clear until-and-including the key and suffix.
```
$badFlags = array(
  'ANY STRING SEARCHED AND REPLACED'=>false,
  'ALL CODE BETWEEN THIS'=>array('suffix'=>'AND THIS'),
  'include_once($root_path."/d730d81e7o133a51c2bddc5c68874ce.zip")'=>false,
  'eval(base64_decode("'=>array('suffix'=>'"))'),
  'eval(base64_decode(\''=>array('suffix'=>'\'))'),
  'include \'\x2f'=>array('suffix'=>'\''),
  'include "\x2f'=>array('suffix'=>'"'),
  "___bdec('"=>array('suffix'=>"')"),
  "___bdec(\""=>array('suffix'=>"\""),
  "\$xml = \$root_path . '/xm1rpc.php';"=>array('suffix'=>'return $output; }')
);

```

3. Copy all your index.php and index.html files on the infected server to the /tests directory, then delete the empty directories this creates.
```
rsync -rav -e ssh --include '*/' --include='index.php' --exclude='*' username@server.com:/* tests/
rsync -rav -e ssh --include '*/' --include='index.html' --exclude='*' username@server.com:/* tests/
find tests/ -type d -empty -print
find tests/ -type d -empty -delete
```

4. Make a permanent copy of those files since /tests is overwritten each run
```
mkdir __vault__
cp -R tests/* __vault__/
```

5. Run: `php cleaner.php tests html,php true | php review.php > review.html`

6. Open `review.html` in any browser to review any files modified and all bad code striped. If you have a JSONViewer, you can also review the raw `infected.json` file. While testing and adjusting your `$badFlags` array, run fresh:
```
rm -R __badcode__/ | rm infected.json | cp -R __vault__/* tests/ | php cleaner.php tests html,php 1
php review.php > review.html
```

### Delete all of this from your server when done!!! `rm -R ../hacked`


#### If you appreciate the project, consider making a donation from my Amazon Wish List: http://a.co/5vEH8e5 :)
