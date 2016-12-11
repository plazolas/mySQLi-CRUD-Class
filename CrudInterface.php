<?php
/**
 * Oswald Plazola
 */

namespace Database;

/**
 * Interface for mysqli crud.
 *
 * Classes implementing this interface may be registered by name or instance.
 */
interface Crud
{
     /**
     * Constructor
     *
     * Reads config file and opens database connection.
     */
    function __construct();
    
    /**
     * close
     *
     * Close database connection.
     *
     * @return void
     */
    public function close();
    
    /**
     * Create
     *
     * Inserts a row on table.
     *
     * @param stdClass $Obj
     * @return (int) id autoincrement for the new row
     */
    public function create(stdClass $Obj);
    
    /**
     * Set
     *
     * Updates a row on table.
     *
     * @param stdClass $Obj
     * @return int id          id of autoincrement for the new row
     */
    public function set(stdClass $Obj);
    
    /**
     * Set
     *
     * Updates a row on table.
     *
     * @param stdClass $Obj
     * @return int              id of autoincrement for the new row or 0 if no row found
     */
    public function set(stdClass $Obj);
    
    /**
     * Delete
     *
     * Deletes a row from table.
     *
     * @param int $id           id of row to be deleted
     * @return boolean          true always
     */
    public function delete(int $id);
    
    /**
     * Get
     *
     * gets a row from table.
     *
     * @param  int $id           id of row
     * @return array             value paired array of row or empty array if row not found
     */
    public function get(int $id) {
    
     /**
     * getCollection
     *
     * gets all rows from table.
     *
     * @return array of arrays
     */
    public function getCollection ();

}
