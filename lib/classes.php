<?php

class BaseKit
{
    
    public function sqliPrevent($str)
    {
        $str = mysql_real_escape_string($str);
        $str = stripslashes($str);
        $str = trim($str);
        return $str;
    }
    
    public function specialEncrypt($char)
    {
        $char = md5($char);
        $char = sha1($char);
        $char = hash('sha256', $char);
        return $char;
    }
    
    public function dbConnect($uname, $pass, $host, $db)
    {
        $username = $uname;
        $password = $pass;
        $hostname = $host;
        $database = $db;
        
        ini_set('session.gc_maxlifetime', 3600 * 3);
        
        $dbhandle = mysql_connect($hostname, $username, $password) or die("Unable to connect to MySQL");
        
        
        $selected_db = mysql_select_db($database, $dbhandle) or die("Could not select database");
    }
    
    public function imgUpload($dir, $filename)
    {
        
        $target_dir  = $dir;
        $target_file = $target_dir . basename($_FILES[$filename]["name"]);
        $uploadOk    = 1;
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded. | Error: " . mysql_error();
        } else {
            if (move_uploaded_file($_FILES[$filename]["tmp_name"], $target_file)) {
                echo "The file " . basename($_FILES[$filename]["name"]) . " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file. | Error: " . mysql_error();
            }
        }
        
    }
    
    public function insertDataDB($tbnwfs, $vals)
    {
        $sql    = "INSERT INTO {$tbnwfs} {$vals}";
        $result = mysql_query($sql);
        if ($result) {
            return 1;
        } else {
            return 0;
        }
    }
    
    public function login($field1, $field2, $tblname, $dbfield1, $dbfield2)
    {
        if (isset($_POST[$field1]) and isset($_POST[$field2])) {
            $f1    = mysql_real_escape_string($_POST[$field1]);
            $f2    = md5(mysql_real_escape_string($_POST[$field2]));
            $query = "SELECT * FROM {$tblname} WHERE {$dbfield1}='$f1' and {$dbfield2}='$f2'";
            
            $result = mysql_query($query) or die(mysql_error());
            
            $count = mysql_num_rows($result);
            
            if ($count == 1) {
                session_start();
                $_SESSION[$f1] = $f1;
                echo "Success!";
            }
            
            else {
                echo "Invalid Credentials!";
            }
            
        }
    }
    
    public function validateRecaptcha($re_sec)
    {
        $recaptcha_secret = $re_sec;
        $response         = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $_POST['g-recaptcha-response']);
        $responseKey      = json_decode($response, true);
        
        if ($responseKey['success'] == 1) {
            return 1;
        } else {
            return 0;
        }
    }
    
    public function beautifulMail($title, $greeting, $mc, $mailTo, $sub, $from)
    {
        $to      = $mailTo;
        $subject = $sub;
        
        $message = "<html> 
                <head></head> 
                <body style='width: 40%;background: #eee;margin: 0;font-family: 'Calibri',sans-serif;'> 
                <h3 style='width:100%;text-align:center;font-size:25px'>{$title}</h3> 
                <p style='text-align:justify;font-size:18px;width:90%;font-weight:300;margin-left:4%;line-height:40px'>{$greeting}</p> 
                <p style='text-align:justify;font-size:18px;width:90%;font-weight:300;margin-left:4%;line-height:40px'>{$mc}</p> 
                </body> 
                </html>";
        
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        
        $headers .= 'From: {$from}' . "\r\n";
        
        mail($to, $subject, $message, $headers);
    }
    
    public function xssPrevent($input)
    {
        
        $input = str_replace(array(
            '&amp;',
            '&lt;',
            '&gt;'
        ), array(
            '&amp;amp;',
            '&amp;lt;',
            '&amp;gt;'
        ), $input);
        $input = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $input);
        $input = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $input);
        $input = html_entity_decode($input, ENT_COMPAT, 'UTF-8');
        
        $input = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $input);
        
        $input = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $input);
        $input = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $input);
        $input = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $input);
        
        // For IE: <span style="width: expression(alert('Ping!'));"></span>
        $input = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $input);
        $input = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $input);
        $input = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $input);
        
        $input = preg_replace('#</*\w+:\w[^>]*+>#i', '', $input);
        
        do {
            $old_input = $input;
            $input     = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $input);
        } while ($old_input !== $input);
        
        return $input;
        
    }
    
    
    public function verifyEmail($emailadd)
    {
        $ok = 1;
        if (filter_var($emailadd, FILTER_VALIDATE_EMAIL)) {
            $ok = 1;
        } else {
            $ok = 0;
        }
        
        if ($ok == 1) {
            $nn = split("@", $emailadd);
            
            $usname = $nn[0];
            $dname  = $nn[1];
            
            if (checkdnsrr($dname, "MX")) {
                return 1;
            } else {
                return 0;
            }
        }
        
        else {
            return 0;
        }
        
    }

    
}

?>