<?php
class Database {
  /* These are for the Vagrant */
  private static $dbName = 'customers' ;
  private static $dbHost = 'localhost' ;
  private static $dbUsername = 'root';
  private static $dbUserPassword = '12345678';
  /* These are for Csis server
  private static $dbName = 'jpdaws355wi19' ;
  private static $dbHost = '10.8.30.49' ;
  private static $dbUsername = 'jpdaws355wi19';
  private static $dbUserPassword = 'grainpaincheeselog'; */

  private static $cont  = null;

    public function __construct() {
        die('Init function is not allowed');
    }

    public static function connect() {
       // One connection through whole application
       if ( null == self::$cont ) {
           try {
               self::$cont =  new PDO( "mysql:host=".self::$dbHost.";"."dbname=".self::$dbName, self::$dbUsername, self::$dbUserPassword);
           }
           catch(PDOException $e) {
               die($e->getMessage());
           }
       }
       return self::$cont;
    }

    public static function disconnect() {
        self::$cont = null;
    }
}
?>
