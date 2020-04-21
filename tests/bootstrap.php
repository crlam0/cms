<?PHP

use classes\App;
use classes\Message;
use classes\DB;

require_once dirname(__FILE__) . '/../local/config.php';

$DIR=dirname(dirname(__FILE__)) . '/';

$App = new App($DIR, $SUBDIR);
$App->setDB(new DB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME));

require_once $DIR.'vendor/autoload.php';

$settings['debug'] = false;


