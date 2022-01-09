<?PHP

use classes\App;
use classes\DB;

require_once __DIR__.'/../../vendor/autoload.php';
require dirname(__FILE__) . '/../../local/config.php';

$DIR = dirname(dirname(dirname(__FILE__))) . '/';

$App = new App($DIR, $SUBDIR);
$App->setDB(new DB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME));

// $App->loadInputData([],[],[]);
// App::$settings['debug'] = true;
// App::$db->debug = App::$settings['debug'];
