<?PHP

require_once 'include/config/config.local.php';
require_once 'include/lib_sql.php';
require_once 'include/lib_messages.php';
require_once 'include/lib_functions.php';
require_once 'include/lib_templates.php';

$setting['debug'] = true;

$DIR=dirname(dirname(__FILE__)) . '/';
$INC_DIR=$DIR.'include/';

var_dump($DB);


$query='SELECT * FROM settings';
$result=my_query($query,true);
while ($row = $result->fetch_array()) {
    $settings[$row['title']] = $row['value'];
}


function add_to_debug ($message) {
    global $settings, $DEBUG;
    if($settings['debug']){
        $time = microtime(true) - $DEBUG['start_time'];
        $time = sprintf('%.4F', $time);

        $DEBUG['log'][] = $time . "\t" . $message;
    }
}

