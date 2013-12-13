<?php
/**
 * Holds class PHPUnit_Ilch_DatabaseTestCase.
 *
 * @package ilch_phpunit
 */

use Ilch\Registry as Registry;
use Ilch\Database\Factory as Factory;

/**
 * Base class for database test cases for Ilch.
 *
 * Should be used when using a mock for the database is not possible
 * or an extraordinarily huge effort.
 *
 * @package ilch_phpunit
 */
class PHPUnit_Ilch_DatabaseTestCase extends PHPUnit_Extensions_Database_TestCase
{
    /**
     * A data array which will be used to create a config object for the registry.
     *
     * @var Array
     */
    protected $_configData = array();

    /**
     * Only instantiate pdo once for test clean-up/fixture load
     *
     * @static Static so we can dont have to connect for every test again.
     * @var PDO
     */
    static private $pdo = null;

    /**
     * Instantiated PHPUnit_Extensions_Database_DB_IDatabaseConnection for the tests.
     *
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    private $conn = null;

    /**
     * The db instance to test with.
     *
     * @var Ilch\Database\MySQL
     */
    protected $db = null;

    /**
     * Filling the config object with individual testcase data.
     */
    public function setUp()
    {
        $testHelper = new PHPUnit_Ilch_TestHelper();
        $testHelper->setConfigInRegistry($this->_configData);
        $dbFactory = new Factory();
        $config = Registry::get('config');

        if (getenv('TRAVIS')) {
            $config->set('dbEngineTest', 'Mysql');
            $config->set('dbHostTest', '127.0.0.1');
            $config->set('dbUserTest', 'travis');
            $config->set('dbPasswordTest', '');
            $config->set('dbNameTest', 'ilch2_test');
            $config->set('dbPrefixTest', '');
        }

        $this->db = $dbFactory->getInstanceByConfig(Registry::get('config'));

        /*
         * Deleting all tables from the db and setting up the db using the given schema.
         */
        $sql = 'SHOW TABLES';
        $tableList = $this->db->queryList($sql);

        foreach($tableList as $table)
        {
            $sql = 'DROP TABLE '.$table;
            $this->db->query($sql);
        }

        $this->db->queryMulti(file_get_contents(__DIR__.'/_files/db_schema.sql'));

        parent::setUp();
    }

    /**
     * Creates the db connection to the test database.
     *
     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    final public function getConnection()
    {
        $dbData = array();
        $config = Registry::get('config');

        foreach (array('dbEngine', 'dbHost', 'dbUser', 'dbPassword', 'dbName', 'dbPrefix') as $configKey) {
            /*
             * Using the data for the db from the config.
             * We check if special config variables for this test execution exist.
             * If so we gonna use it.
             */
            if ($config->get($configKey.'Test') !== null) {
                $dbData[$configKey] = $config->get($configKey.'Test');
            } elseif ($config->get($configKey) !== null) {
                $dbData[$configKey] = $config->get($configKey);
            }
        }

        $dsn = strtolower($dbData['dbEngine']).':dbname='.$dbData['dbName'].';host='.$dbData['dbHost'];
        $dbData['dbDsn'] = $dsn;

        if ($this->conn === null) {
            if (self::$pdo === null) {
                self::$pdo = new PDO($dbData['dbDsn'], $dbData['dbUser'], $dbData['dbPassword']);
            }

            $this->conn = $this->createDefaultDBConnection(self::$pdo, $dbData['dbName']);
        }

        return $this->conn;
    }

    /**
     * Creates and returns a dataset object.
     *
     * @return PHPUnit_Extensions_Database_DataSet
     */
    protected function getDataSet() {}
}
