<?php
namespace PAGEmachine\Ats\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class ApplicationQuery
{

    /**
     * @var string
     */
    protected $orderBy = 'uid';

    /**
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param string $orderBy
     * @return void
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }


    /**
     * @var string
     */
    protected $orderDirection = 'asc';

    /**
     * @return string
     */
    public function getOrderDirection()
    {
        return $this->orderDirection;
    }

    /**
     * @param string $orderDirection
     * @return void
     */
    public function setOrderDirection($orderDirection)
    {
        $this->orderDirection = $orderDirection;
    }


    /**
     * @var int
     */
    protected $limit = 10;

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return void
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }


    /**
     * @var int
     */
    protected $offset = 0;

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param int $offset
     * @return void
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    }


    /**
     * @var array
     */
    protected $statusValues;

    /**
     * @return array
     */
    public function getStatusValues()
    {
        return $this->statusValues;
    }

    /**
     * @param array $statusValues
     * @return void
     */
    public function setStatusValues($statusValues = [])
    {
        $this->statusValues = $statusValues;
    }

    /**
     * Returns active status values for querying.
     *
     * @return array
     */
    public function getActiveStatusValues()
    {
        return array_keys(array_filter($this->statusValues));
    }

    /**
     * Creates a query from given AJAX request.
     * This is implemented to specifically fit to DataTables query strings.
     *
     * @param  array  $queryParams
     * @return ApplicationQuery
     */
    public static function createFromRequest(array $queryParams = [])
    {
        $query = new ApplicationQuery();

        // Ordering
        if ($queryParams['order'] && in_array($queryParams['order'][0]['dir'], ['asc', 'desc']))
        {
            $query->setOrderBy($queryParams['columns'][$queryParams['order'][0]['column']]['data']);
            $query->setOrderDirection($queryParams['order'][0]['dir']);
        }

        // Pagination
        if ($queryParams['start'] && $queryParams['length']) {
            $query->setOffset((int) $queryParams['start']);
            $query->setLimit($queryParams['length']);
        }

        // Status values
        if ($queryParams['statusValues']) {
            $query->setStatusValues($queryParams['statusValues']);
        }

        return $query;
    }
}
