# UNL Events

Join us at [https://unlwdn.slack.com](https://unlwdn.slack.com)

## Manual Install

1. run `git submodule init`
2. run `git submodule update`
3. run `cp config.sample.php config.inc.php`
4. run `cp www/sample.htaccess www/.htaccess`
5. run `composer install` Don't get composer through brew, as it is outdated in there. Instead get it at the composer website.
6. run `wget -r -nH -np -l 15 --cut-dirs=1 --reject "index.html*,*.LCK" http://wdn.unl.edu/wdn/ -P www/wdn/` to get the latest WDN stuff.
7. This misses an empty file that the code looks for. Run `touch www/wdn/templates_4.0/includes/wdnResources.html`
8. You need to compile the stuff. First, `npm install less-plugin-clean-css` This dependency is missing for some reason.
9. Now `make`. Your assets should now be compiled.
10. Set up a database by running `data/20230819-events-schema.sql`. This will create the db tables and insert any standard data.
11. customize config.inc.php and www/.htaccess to your environment.
