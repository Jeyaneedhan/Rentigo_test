<?php
/**
 * M_Pages Model
 * A super simple model used for testing or for very basic site pages 
 * that don't need much data.
 */
class M_Pages
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Just a test method to grab all users and see if things are working!
     */
    public function getUsers()
    {
        $this->db->query('SELECT * FROM Users');

        return $this->db->resultSet();
    }
}
