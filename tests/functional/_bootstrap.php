<?PHP

use classes\App;
use classes\DB;
use classes\Template;

require_once __DIR__.'/../../vendor/autoload.php';
require dirname(__FILE__) . '/../../local/config.php';

$DIR = dirname(dirname(dirname(__FILE__))) . '/';

$App = new App($DIR, $SUBDIR);
$App->setDB(new DB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME));
$App->loadInputData([], [], ['REQUEST_URI'=>$SUBDIR]);
App::$template = new Template();

App::$debug = true;
App::$db->debug = true;
