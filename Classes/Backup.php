<?php
namespace LocalbrandingDe\BackupBundle\Classes;
use \mysqli;


class Backup {
    
    private $cloudDomain;
    private $config;
    
    function __construct() {
        
        
        $this->config = include(dirname(__FILE__).'/../Resources/contao/config/lb_config.php');
        $this->cloudDomain = $this->config['cloudDomain'];
        
    }
    private function logBackup()
    {
        $insert=['tstamp'=>time(),"lastBackup"=>time()];
        \Database::getinstance()->prepare('INSERT  tl_lb_backupLog %s ')->set($insert)->execute();
    }
    
    private function isBackupTime()
    {
        $backupInterval=intval($this->config['backupInterval']);
        $ratio=24*$backupInterval;
        $lastBackup=\Database::getinstance()->prepare('SELECT * FROM tl_lb_backupLog ORDER BY tstamp DESC LIMIT 1')->execute();
        if ($lastBackup->lastBackup <= strtotime('-'.$ratio.' hours')) {
        return true;
        } else {
            return false;
        }
    }
    
    private function zipFile($filePath,$zipName)
    {
        
        

        $zip = new \ZipArchive();
        $zip->open(strval($zipName).'.zip', \ZipArchive::CREATE |\ ZipArchive::OVERWRITE);
        $zip->addFile($filePath);
        $zip->close();
        
    }
    
    
    private function zipFolder($folderPath,$zipName)
    {


        // Get real path for our folder
        $rootPath = realpath($folderPath);
        
        // Initialize archive object
        $zip = new \ZipArchive();
        $zip->open(strval($zipName).'.zip', \ZipArchive::CREATE |\ ZipArchive::OVERWRITE);
        
        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($rootPath),
            \RecursiveIteratorIterator::LEAVES_ONLY
            );
        
        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);
                
                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }
        
        // Zip archive will be created only after closing object
        $zip->close();
    }
    
    public function doBackup()
    {
        if(!$this->isBackupTime())
        {
            exit;
        }
        $host= "db3817.mydbserver.com";
        $database_port=" 3306";
        $user= "p535946d2";
        $pass= "leoX_5524";
        $dbname= "usr_p535946_2";
        $tables = '*';
        $cloudLink= new TeamFile();
        //Call the core function

            $link = mysqli_connect($host,$user,$pass, $dbname);
            
            // Check connection
            if (mysqli_connect_errno())
            {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
                exit;
            }
            
            mysqli_query($link, "SET NAMES 'utf8'");
            
            //get all of the tables
            if($tables == '*')
            {
                $tables = array();
                $result = mysqli_query($link, 'SHOW TABLES');
                while($row = mysqli_fetch_row($result))
                {
                    $tables[] = $row[0];
                }
            }
            else
            {
                $tables = is_array($tables) ? $tables : explode(',',$tables);
            }
            
            $return = '';
            //cycle through
            foreach($tables as $table)
            {
                $result = mysqli_query($link, 'SELECT * FROM '.$table);
                $num_fields = mysqli_num_fields($result);
                $num_rows = mysqli_num_rows($result);
                
                $return.= 'DROP TABLE IF EXISTS '.$table.';';
                $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE '.$table));
                $return.= "\n\n".$row2[1].";\n\n";
                $counter = 1;
                
                //Over tables
                for ($i = 0; $i < $num_fields; $i++)
                { 
                    //Over rows
                    while($row = mysqli_fetch_row($result))
                    {
                        if($counter == 1){
                            $return.= 'INSERT INTO '.$table.' VALUES(';
                        } else{
                            $return.= '(';
                        }
                        
                        //Over fields
                        for($j=0; $j<$num_fields; $j++)
                        {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = str_replace("\n","\\n",$row[$j]);
                            if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                            if ($j<($num_fields-1)) { $return.= ','; }
                        }
                        
                        if($num_rows == $counter){
                            $return.= ");\n";
                        } else{
                            $return.= "),\n";
                        }
                        ++$counter;
                    }
                }
                $return.="\n\n\n";
            }

            //save file

            $fileName = 'backup_lb-team-'.date('Y-m-d_H-i').'.sql';
            $handle = fopen($fileName,'w+');
            fwrite($handle,$return);
            if(fclose($handle)){
             //   echo "Done, the file name is: ".$fileName;
                $path=$this->config['backupDir'];
                
                
                $context= [ "relationBase" => "backup", "uploadMemberID" => 1, "extension" => "sql","uploadDate"=>  date('Y-m-d H:i:s')];
                $this->zipFile($fileName,$fileName);
                $cloudLink->saveFile($path,$fileName.".zip",$context);
                $this->logBackup();
                exit;
            }
        
    }
  
    
    
    
}