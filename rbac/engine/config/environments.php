<?php
   
  $G_ENVIRONMENTS = array (
    G_PRO_ENV => array ( 
      'dbfile' => PATH_DB . 'production' . PATH_SEP . 'db.php' ,
      'cache' => 1,
      'debug' => 0,
    ) ,
    G_DEV_ENV => array ( 
      'dbfile' => PATH_DB . 'os' . PATH_SEP . 'db.php', 
      'datasource' => 'workflow',
      'cache' => 0,
      'debug' => 1,
    ) ,
    G_TEST_ENV => array ( 
      'dbfile' => PATH_DB . 'test' . PATH_SEP . 'db.php' ,
      'cache' => 0,
      'debug' => 0,
    ) ,
  );

?>
