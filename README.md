# hacked-lamp-cleaner
## A PHP script to scan, copy, save, and delete infected files like "eval(base64_decode(" and any other strings

```
php cleaner.php /path/to/clean html,php,extensions recursive|not aggressive|not
```

####Examples:
```
php cleaner.php / html,php true true ` // clean all html and php files from root while removing whitespace from all files
php cleaner.php /folder false false` // clean only files inside /folder strictly matching whitespace
```


####Testing:

1. Set your flags inside badFlags.php with the prefix and suffix of any offensive code to strip out

2. Create a folder called "hacked" anywhere on your infected server or locally for testing
`mkdir hacked`

3. Copy all index.php and index.html files on the infected server to test this script
```
rsync -rav -e ssh --include '*/' --include='index.php' --exclude='*' username@server.com:/* tests/
rsync -rav -e ssh --include '*/' --include='index.html' --exclude='*' username@server.com:/* tests/
```

4. Delete the empty directories this recursive rsync command creates: `find tests/ -type d -empty -delete`

5. Make a permanent copy of those files since /tests is overwritten
```
mkdir __vault__
cp -R tests/* __vault__/
```

6. Execute from CLI inside /hacked: `php cleaner.php tests html,php true | php review.php > review.html`

7. Open `review.html` in any browser to review files changed and bad code striped

7a. While testing, after adjusting your badFlags, re-run fresh:
```
rm -R __badcode__/ | rm infected.json | cp -R __vault__/* tests/ | php cleaner.php tests html,php true
php review.php > review.html
```

#### Delete all of things from your server when done: `rm -R __badcode__/`
