<?php
/**
 * PDO Database Class
 * THE DATA ENGINE: This class handles all the talking to MySQL.
 * We use a specialized type of connection called PDO (PHP Data Objects).
 * 
 * Why PDO? Because it's SECURE. It uses "Prepared Statements" which 
 * makes it almost impossible for hackers to do SQL Injection.
 */
class Database
{
    // Grabbing database credentials from our config file
    private $host = DB_HOST;
    private $user = DB_USER;
    private $password = DB_PASSWORD;
    private $dbname = DB_NAME;

    // dbh = database handler, stmt = statement, error = error message
    private $dbh;
    private $stmt;
    private $error;

    public function __construct()
    {
        // DSN = Data Source Name, basically the connection string
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname;

        // These options make our connection persistent and show errors properly
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        // Try to connect to the database
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->password, $options);
        } catch (PDOException $e) {
            // If connection fails, show what went wrong
            $this->error = $e->getMessage();
            echo $this->error;
        }
    }

    /**
     * Step 1: Prepare the SQL query string.
     */
    public function query($sql)
    {
        $this->stmt = $this->dbh->prepare($sql);
    }

    /**
     * Step 2: Bind the actual values into the query.
     * This is the security magic! Instead of putting $name directly in the SQL, 
     * we use a placeholder (like :name) and bind the value here.
     */
    public function bind($param, $value, $type = NULL)
    {
        if (is_null($type)) {
            // Auto-detect the data type if not specified
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    // If we're not sure, treat it as a string (safest option)
                    $type = PDO::PARAM_STR;
            }
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    // Actually run the prepared statement
    public function execute()
    {
        return $this->stmt->execute();
    }

    // Get multiple rows from the database as an array of objects
    public function resultSet()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Get just one row from the database as an object
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    // Count how many rows were affected by the query
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }

    /**
     * Get the ID of the record we JUST created.
     * This is helpful if you save a property and need to know its new ID immediately.
     */
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }
}
