To try my CMS follow this simple steps:

1. clone https://github.com/crlam0/cms repository
2. extract files from 'sample-files.zip' as is
3. rename 'local/config.php-sample' to 'local/config.php' and put your settings of database.
4. rename 'local/settings.php-sample' to 'local/settings.php' and change default values if needed.
5. run 'bin/app db:restore' and choose 'dump-sample.sql'
6. Enjoy !

For access admin panel use 'admin/' url, user 'admin', password 'adminadmin'
For enable debug mode run 'bin/app debug:on'


'sample-files.zip' conaints package.json, SASS files and another stuff for build theme bundle. 
Just run 'npm install' and 'npm run dev' for build a bundle. Source JS, SASS, images located in 
'theme/assets/' directory.

