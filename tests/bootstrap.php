<?PHP

use Classes\App;
use Classes\Message;

require_once dirname(__FILE__) . '/../include/config/config.local.php';

$DIR=dirname(dirname(__FILE__)) . '/';

$App = new App($DIR, $SUBDIR);
$App->connectDB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME);

require_once $DIR.'vendor/autoload.php';

$settings['debug'] = false;


