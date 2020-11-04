<?php

/**
 * Pagination Class
 *
 
 * @author     Mark Solly <mark.solly@fsg.com.au>
 */

class Pagination {

    /**
     * @access public
     * @var integer Current Page
     */
    public $currentPage;

    /**
     * @access public
     * @var integer Number of items(newsfeed, posts, ..etc.) to be displayed per page
     */
    public $perPage;

    /**
     * @access public
     * @var integer Total count of items(newsfeed, posts, ..etc.)
     */
    public $totalCount;

    /**
     * This is the constructor for Pagination Object.
     *
     * @access  public
     * @param   integer  $currentPage
     * @param   integer  $totalCount
     * @param   integer  $perPage Number of items per page
     */
    public function __construct($currentPage = 1, $totalCount = 0, $perPage = 0){
        $this->currentPage = (empty($currentPage))? 1: (int)$currentPage;
        $this->totalCount = (empty($totalCount))? 0: (int)$totalCount;
        $this->perPage = (empty($perPage))? Config::get('PAGINATION_DEFAULT_LIMIT'): (int)$perPage;
    }

    /**
     * Creates the pagination object.
     *
     * @access public
     * @param  string  $table
     * @param  array   $conditions  array of data
     * @param  integer $pageNum
     * @return Pagination
     */
    public static function pagination($table, $conditions, $pageNum){

        $db = Database::openConnection();
        $totalCount = $db->countData($table, $conditions);
        return new Pagination((int)$pageNum, $totalCount);
    }

    /**
     * Get the offset.
     *
     * @access public
     * @return integer
     */
    public function getOffset() {
        return ($this->currentPage - 1) * $this->perPage;
    }

    /**
     * Get number of total pages.
     *
     * @access public
     * @return integer
     */
    public function totalPages() {
        return ceil($this->totalCount/$this->perPage);
    }

    /**
     * Get the number of the previous page.
     *
     * @access public
     * @return integer  Number of previous page
     */
    public function previousPage() {
        return $this->currentPage - 1;
    }

    /**
     * Get the number of the next page.
     *
     * @access public
     * @return integer  Number of next page
     */
    public function nextPage() {
        return $this->currentPage + 1;
    }

    /**
     * checks if there is a previous page or not
     *
     * @access public
     * @return boolean
     */
    public function hasPreviousPage() {
        return $this->previousPage() >= 1 ? true : false;
    }

    /**
     * checks if there is a next page or not
     *
     * @access public
     * @return boolean
     */
    public function hasNextPage() {
        return $this->totalPages() >= $this->nextPage()? true : false;
    }

}
