<?php
class Database
{
    /* These are for the Vagrant 
    private static $_dbName = 'customers' ;
    private static $_dbHost = 'localhost' ;
    private static $_dbUsername = 'root';
    private static $_dbUserPassword = '12345678';*/
    /* These are for Csis server*/
    private static $_dbName = 'jpdaws355wi19' ;
    private static $_dbHost = '10.8.30.49' ;
    private static $_dbUsername = 'jpdaws355wi19';
    private static $_dbUserPassword = 'grainpaincheeselog';

    private static $_cont  = null;

    public function __construct()
    {
        die('Init function is not allowed');
    }

    public static function connect()
    {
        // One connection through whole application
        if ( null == self::$_cont ) {
            try
            {
                self::$_cont =  new PDO( "mysql:host=".self::$_dbHost.";"."dbname=".self::$_dbName, self::$_dbUsername, self::$_dbUserPassword);
            }
            catch(PDOException $e)
            {
                die($e->getMessage());
            }
        }
        return self::$_cont;
    }

    public static function disconnect() {
        self::$_cont = null;
    }
}
?>
