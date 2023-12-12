<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use App\Logs;
use Browser;

/**
 * Manages backups and import
 * 
 */
class BackupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private $ipaddress;

    /**
     * Get IP address of the user
     *
     * @return void
     */
    public function get_ip(){
        
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN'; 

            return $ipaddress;
    }

    /**
     * Quick export of database
     *
     * @param Request $request
     * @return void
     */
    public function export(Request $request){
        // Database configuration
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $database_name = 'dbexecom';
        // $username = 'skms';
        // $password = '$km$@NRCP@1933';
        // $database_name = 'dbexecom';

        // Get connection object and set the charset
        $conn = mysqli_connect($host, $username, $password, $database_name);
        $conn->set_charset('utf8');

        $tables = array();

        $method = $request->input('export_method', TRUE);

        // custom data only
        if($method == 2){
            $tables = $request->input('table_data', TRUE);
        }else{
            // Get All Table Names From the Database
            $sql = "SHOW TABLES";
            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_row($result)) {
                $tables[] = $row[0];
            }
        }
        
        $sqlScript = "";
        foreach ($tables as $table) {
            
            // Prepare SQLscript for creating table structure
            $query = "SHOW CREATE TABLE $table";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_row($result);
            
            if($method == 1){
                $sqlScript .= "\n\n" . $row[1] . ";\n\n";
            }
            
            
            $query = "SELECT * FROM $table";
            $result = mysqli_query($conn, $query);
            
            $columnCount = mysqli_num_fields($result);
            
            // Prepare SQLscript for dumping data for each table
            for ($i = 0; $i < $columnCount; $i ++) {
                while ($row = mysqli_fetch_row($result)) {
                    $sqlScript .= "INSERT IGNORE INTO $table VALUES(";
                    for ($j = 0; $j < $columnCount; $j ++) {
                        $row[$j] = $row[$j];
                        
                        if (isset($row[$j])) {
                            $sqlScript .= '\'' . addslashes($row[$j]) . '\'';
                        } else {
                            $sqlScript .= '\'\'';
                        }
                        if ($j < ($columnCount - 1)) {
                            $sqlScript .= ',';
                        }
                    }
                    $sqlScript .= ");\n";
                }
            }
            
            $sqlScript .= "\n"; 
        }

        if(!empty($sqlScript))
        {
            // Save the SQL script to a backup file
            $backup_file_name = $database_name . '_backup_' . time() . '.sql';
            $fileHandler = fopen($backup_file_name, 'w+');
            $number_of_lines = fwrite($fileHandler, $sqlScript);
            fclose($fileHandler); 

            // Download the SQL backup file to the browser
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($backup_file_name));
            ob_clean();
            flush();
            readfile($backup_file_name);
            exec('rm ' . $backup_file_name);

            $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
            $browser = Browser::browserName();
            $ip = $this->get_ip();

            $logs = array('log_user_id' => Auth::id(), 
                      'log_ip_address' => $ip,
                      'log_user_agent' => $os,
                      'log_browser' => $browser,
                      'log_email' =>  Auth::user()->email, 
                      'log_description' => 'Created backup of EXECOM database. ('.$backup_file_name.')', 
                      'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'::'. __FUNCTION__);

            Logs::create($logs); 
        }
    }

    /**
     * Import sql file (INSERT IGNORE)
     *
     * @param Request $request
     * @return void
     */
    public function import(Request $request){

        // Name of the data file
        $filename = $request->file('file');
        $file_name = $request->file('file')->getClientOriginalName();

        // MySQL host
        $mysqlHost = 'localhost';
        $mysqlUser = 'root';
        $mysqlPassword = '';
        $mysqlDatabase = 'dbexecom';
        // $mysqlUser = 'skms';
        // $mysqlPassword = '$km$@NRCP@1933';
        // $mysqlDatabase = 'dbexecom';

        $conn = mysqli_connect($mysqlHost, $mysqlUser, $mysqlPassword , $mysqlDatabase);

        $query = '';
        $sqlScript = file($filename);
        foreach ($sqlScript as $line)	{

            $startWith = substr(trim($line), 0 ,2);
            $endWith = substr(trim($line), -1 ,1);

            if (empty($line) || $startWith == '--' || $startWith == '/*' || $startWith == '//') {
                continue;
            }

            $query = $query . $line;
            if ($endWith == ';') {
                mysqli_query($conn,$query) or die('<div class="error-response sql-import-response">Problem in executing the SQL query <b>' . $query. '</b></div>');
                $query= '';		
            }
        }

        $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
        $browser = Browser::browserName();
        $ip = $this->get_ip();

        $logs = array('log_user_id' => Auth::id(), 
                  'log_ip_address' => $ip,
                  'log_user_agent' => $os,
                  'log_browser' => $browser,
                  'log_email' =>  Auth::user()->email, 
                  'log_description' => 'Imported backup of EXECOM database. ('.$file_name.')', 
                  'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'::'. __FUNCTION__);

        Logs::create($logs); 
        
        return 1;

    }

     /**
     * Quick export of database
     *
     * @param Request $request
     * @return void
     */
    public function export_logs(){
        // Database configuration
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $database_name = 'dbexecom';
        // $username = 'skms';
        // $password = '$km$@NRCP@1933';
        // $database_name = 'dbexecom';

        // Get connection object and set the charset
        $conn = mysqli_connect($host, $username, $password, $database_name);
        $conn->set_charset('utf8');
        
        $sqlScript = "";

        // Prepare SQLscript for creating table structure
        $query = "SHOW CREATE TABLE tbllogs";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_row($result);
        
      
        $sqlScript .= "\n\n" . $row[1] . ";\n\n";
        
        $query = "SELECT * FROM tbllogs";
        $result = mysqli_query($conn, $query);
        
        $columnCount = mysqli_num_fields($result);
        
        // Prepare SQLscript for dumping data for each table
        for ($i = 0; $i < $columnCount; $i ++) {
            while ($row = mysqli_fetch_row($result)) {
                $sqlScript .= "INSERT IGNORE INTO tbllogs VALUES(";
                for ($j = 0; $j < $columnCount; $j ++) {
                    $row[$j] = $row[$j];
                    
                    if (isset($row[$j])) {
                        $sqlScript .= '\'' . addslashes($row[$j]) . '\'';
                    } else {
                        $sqlScript .= '\'\'';
                    }
                    if ($j < ($columnCount - 1)) {
                        $sqlScript .= ',';
                    }
                }
                $sqlScript .= ");\n";
            }
        }
        
        $sqlScript .= "\n"; 

        if(!empty($sqlScript))
        {
            // Save the SQL script to a backup file
            $backup_file_name = $database_name .'_activity_logs' . '_backup_' . time() . '.sql';
            $fileHandler = fopen($backup_file_name, 'w+');
            $number_of_lines = fwrite($fileHandler, $sqlScript);
            fclose($fileHandler); 

            // Download the SQL backup file to the browser
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($backup_file_name));
            ob_clean();
            flush();
            readfile($backup_file_name);
            exec('rm ' . $backup_file_name);

            $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
            $browser = Browser::browserName();
            $ip = $this->get_ip();
            
            $logs = array('log_user_id' => Auth::id(), 
                      'log_ip_address' => $ip,
                      'log_user_agent' => $os,
                      'log_browser' => $browser,
                      'log_email' =>  Auth::user()->email, 
                      'log_description' => 'Created backup of Activity Logs ('.$backup_file_name.')', 
                      'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'::'. __FUNCTION__);

            Logs::create($logs); 
        }
    }

      /**
     * Quick export of database
     *
     * @param Request $request
     * @return void
     */
    public function clear_with_export_logs(){
        // Database configuration
        $host = 'localhost';
        $username = 'root';
        $password = '';
        $database_name = 'dbexecom';
        // $username = 'skms';
        // $password = '$km$@NRCP@1933';
        // $database_name = 'dbexecom';

        // Get connection object and set the charset
        $conn = mysqli_connect($host, $username, $password, $database_name);
        $conn->set_charset('utf8');
        
        $sqlScript = "";

        // Prepare SQLscript for creating table structure
        $query = "SHOW CREATE TABLE tbllogs";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_row($result);
        
      
        $sqlScript .= "\n\n" . $row[1] . ";\n\n";
        
        $query = "SELECT * FROM tbllogs";
        $result = mysqli_query($conn, $query);
        
        $columnCount = mysqli_num_fields($result);
        
        // Prepare SQLscript for dumping data for each table
        for ($i = 0; $i < $columnCount; $i ++) {
            while ($row = mysqli_fetch_row($result)) {
                $sqlScript .= "INSERT IGNORE INTO tbllogs VALUES(";
                for ($j = 0; $j < $columnCount; $j ++) {
                    $row[$j] = $row[$j];
                    
                    if (isset($row[$j])) {
                        $sqlScript .= '\'' . addslashes($row[$j]) . '\'';
                    } else {
                        $sqlScript .= '\'\'';
                    }
                    if ($j < ($columnCount - 1)) {
                        $sqlScript .= ',';
                    }
                }
                $sqlScript .= ");\n";
            }
        }
        
        $sqlScript .= "\n"; 

        if(!empty($sqlScript))
        {
            // Save the SQL script to a backup file
            $backup_file_name = $database_name .'_activity_logs' . '_backup_' . time() . '.sql';
            $fileHandler = fopen($backup_file_name, 'w+');
            $number_of_lines = fwrite($fileHandler, $sqlScript);
            fclose($fileHandler); 

            // Download the SQL backup file to the browser
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($backup_file_name));
            ob_clean();
            flush();
            readfile($backup_file_name);
            exec('rm ' . $backup_file_name);
            
            Logs::truncate();

            $os = Browser::platformFamily() . ' ' . Browser::platFormVersion();
            $browser = Browser::browserName();
            $ip = $this->get_ip();
            
            $logs = array('log_user_id' => Auth::id(), 
                      'log_ip_address' => $ip,
                      'log_user_agent' => $os,
                      'log_browser' => $browser,
                      'log_email' =>  Auth::user()->email, 
                      'log_description' => 'Clear and created backup of Activity Logs ('.$backup_file_name.')', 
                      'log_controller' => str_replace('App\Http\Controllers\\','', __CLASS__) .'::'. __FUNCTION__);

            Logs::create($logs); 
        }
    }

}
