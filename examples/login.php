 <?php



 require '../lib/classes.php';

 $basekit = new BaseKit;
$basekit->dbconnect("root","","localhost","myhost");



$basekit->login("username","password","members","username","password");





?>
