<?PHP

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

use classes\App;
use classes\Message;
use classes\DB;

require_once dirname(__FILE__) . '/../local/config.php';

$DIR=dirname(dirname(__FILE__)) . '/';

$App = new App($DIR, $SUBDIR);
$App->setDB(new DB($DBHOST, $DBUSER, $DBPASSWD, $DBNAME));

App::$logger = new Logger('main');
App::$logger->pushHandler(new StreamHandler(App::$DIR . 'var/log/test.log', Logger::ERROR));

require_once App::$DIR.'vendor/autoload.php';

App::$settings['debug'] = false;
