<?php
require_once "idiorm.php";

// Database configuration..
ORM::configure(array(
	"connection_string" => "mysql:host=db_hostname_here;dbname=db_name_here",
	"username" => "db_username_here",
	"password" => "db_password_here"
));

require_once "VisitorLogInc.php";

class VisitorLog {

	public $database_table = "visitors"; // Set to 'false` to disable..

    public function __construct() {
        if($this->database_table) {
            try {
                ORM::raw_execute("SELECT * FROM " .$this->database_table. " LIMIT 1;");
            } catch (Exception $e) {
                $sql = "CREATE TABLE " .$this->database_table. " (
                    id INT(9) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    ip varchar(64) NOT NULL,
					location varchar(1024) NOT NULL,
                    agent varchar(1024) NOT NULL,
                    browser varchar(1024) NOT NULL,
                    os varchar(1024) NOT NULL,
                    device varchar(1024) NOT NULL,
                    ref varchar(1024) NOT NULL,
                    time int(11) NOT NULL
                )";
                if(ORM::raw_execute($sql)) {
                    echo "Table created.";
                }
            }
        }
    }

    private function get_ip() {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        }
        elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        }
        else {
            $ip = $remote;
        }
        return $ip;
    }
	
	private function get_location() {
		return $curPageName = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1); 
	}
	
	private function get_user_agent() {
		return $_SERVER['HTTP_USER_AGENT'];
	}
	
	public function start() {
		
		$browser = new VeryIdiot\BrowserDetection;
		$device = new VeryIdiot\MobileDetect();
		
		if($device->isMobile())
			$device_type = 'Mobile';
		elseif($device->isTablet())
			$device_type = ' Tablet';
		else
			$device_type = 'PC';
			
		if($device->isiOS())
			$os = 'iOS';
		elseif($device->isAndroidOS())
			$os = 'Android';
		elseif($device->isLinux())
			$os = 'Linux';
		else
			$os = 'Windows';

	if(isset($_SERVER['HTTP_REFERER']))
		$ref = $_SERVER['HTTP_REFERER'];
	else
		$ref = '';
		
		$data = array(
			"ip" => $this->get_ip(),
			"location" => $this->get_location(),
			"agent" => $_SERVER['HTTP_USER_AGENT'],
			"browser" => $browser->getName(),
			"os" => $os,
			"device" => $device_type,
			"ref" => $ref,
			"time" => time()
		);
		
		//var_dump($data);
		$this->log_visitor($data);
	}	
	
	private function log_visitor($data) {
		if($data) {
			$visitor = ORM::for_table($this->database_table)->create();
			$visitor->ip = $data["ip"];
			$visitor->time = $data["time"];
			$visitor->location = $data["location"];
			$visitor->agent = $data["agent"];
			$visitor->browser = $data["browser"];
			$visitor->os = $data["os"];
			$visitor->device = $data["device"];
			$visitor->ref = $data["ref"];
			$visitor->save();
		}
		else return false;
	}
	
	public function pull() {
		$visitors = ORM::for_table($this->database_table)->order_by_desc("id")->find_many();
		return $visitors;
	}
}

$VisitorLog = new VisitorLog;
$VisitorLog->start();
