<?PHP

require_once dirname(__FILE__) . '/../include/config/config.local.php';

$DIR=dirname(dirname(__FILE__)) . '/';
$INC_DIR=$DIR.'include/';

require_once $DIR.'vendor/autoload.php';
require_once $INC_DIR . 'lib_sql.php';
require_once $INC_DIR . 'lib_messages.php';
require_once $INC_DIR . 'lib_functions.php';
require_once $INC_DIR . 'lib_templates.php';

// require_once dirname(__FILE__) . '/../include/common.php';

$settings['debug'] = false;

// var_dump($DB);

function add_to_debug ($message) {
    global $settings, $DEBUG;
    if($settings['debug']){
        $time = microtime(true) - $DEBUG['start_time'];
        $time = sprintf('%.4F', $time);

        $DEBUG['log'][] = $time . "\t" . $message;
    }
}

