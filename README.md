# hacked-lamp-cleaner
## A PHP script to scan, copy, save, and delete infected files like "eval(base64_decode(" and any other strings


Setup with Bash

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

6. Execute from CLI inside /hacked: `php cleaner.php tests true`

7. Review the files created in the __badcode__ folder to make sure all code includes both your badFlag keys, suffix, and all code in between.

7a. If it does not, adjust your badFlags and run
```
rm -R __badcode__/ | rm infected.json | cp -R __vault__/* tests/ | php cleaner.php tests true
php review.php > review.html
```

7b. If so, run `php cleaner.php / true` to clean the whole server.

### Do not leave this code on your server when done and delete all generated copies.
##### You might want to download your stripped or vault files to better understand what the malicious code was doing while active.
