class Session implements SessionHandlerInterface {
        /** @var Session */
        private static $_instance;
        private $savePath;

        public static function start() {
            if( empty(self::$_instance) ) {
                self::$_instance = new self();
                session_set_save_handler(self::$_instance,true);
                session_start();
            }
        }
        public static function save() {
            if( empty(self::$_instance) ) {
                throw new \Exception("You cannot save a session before starting the session");
            }
            self::$_instance->write(session_id(),session_encode());
        }
        public function open($savePath, $sessionName) {
            $this->savePath = $savePath;
            if (!is_dir($this->savePath)) {
                mkdir($this->savePath, 0777);
            }

            return true;
        }
        public function close() {
            return true;
        }
        public function read($id) {
            return (string)@file_get_contents("$this->savePath/sess_$id");
        }
        public function write($id, $data) {
            return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
        }
        public function destroy($id) {
            $file = "$this->savePath/sess_$id";
            if (file_exists($file)) {
                unlink($file);
            }

            return true;
        }
        public function gc($maxlifetime) {
            foreach (glob("$this->savePath/sess_*") as $file) {
                if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                    unlink($file);
                }
            }

            return true;
        }
}
