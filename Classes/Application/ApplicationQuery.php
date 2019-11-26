<?php
namespace PAGEmachine\Ats\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class ApplicationQuery implements \JsonSerializable
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
    protected $limit = 20;

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
    protected $statusValues = [
        10,
        50
    ];

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
     * @var int|null
     */
    protected $job = null;

    /**
     * @return int|null
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param int $job
     */
    public function setJob($job)
    {
        $this->job = $job;
    }


    /**
     * @var string
     */
    protected $search;

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param string $search
     * @return void
     */
    public function setSearch($search)
    {
        $this->search = $search;
    }


    /**
     * @var bool
     */
    protected $onlyDeadlineExceeded;

    /**
     * @return bool
     */
    public function getOnlyDeadlineExceeded()
    {
        return $this->onlyDeadlineExceeded;
    }

    /**
     * @param bool $onlyDeadlineExceeded
     */
    public function setOnlyDeadlineExceeded($onlyDeadlineExceeded)
    {
        $this->onlyDeadlineExceeded = $onlyDeadlineExceeded;
    }


    /**
     * @var string
     */
    protected $deadlineTime = 0;

    /**
     * @return string
     */
    public function getDeadlineTime()
    {
        return $this->deadlineTime;
    }

    /**
     * @param string $deadlineTime
     */
    public function setDeadlineTime($deadlineTime)
    {
        $this->deadlineTime = $deadlineTime;
    }

    public function jsonSerialize()
    {
        $query = [
            'orderBy' => $this->orderBy,
            'orderDirection' => $this->orderDirection,
            'limit' => $this->limit,
            'offset' => $this->offset,
            'statusValues' => $this->statusValues,
            'job' => $this->job,
            'search' => $this->search,
            'onlyDeadlineExceeded' => $this->onlyDeadlineExceeded ? 1 : 0,
            'deadlineTime' => $this->deadlineTime,
        ];
        return $query;
    }

    public function __construct($queryParams = [])
    {
        if (!empty($queryParams)) {
            $this->orderBy = $queryParams['orderBy'] ?: $this->orderBy;
            $this->orderDirection = $queryParams['orderDirection'] ?: $this->orderDirection;
            $this->limit = (int)$queryParams['limit'] ?: $this->limit;
            $this->offset = (int)$queryParams['offset'] ?: $this->offset;
            $this->statusValues = $queryParams['statusValues'] ?: $this->statusValues;
            $this->job = (int)$queryParams['job'] ?: $this->job;
            $this->search = $queryParams['search'] ?: $this->search;
            $this->onlyDeadlineExceeded = (int)$queryParams['onlyDeadlineExceeded'] == 1 ? true : false;
            $this->deadlineTime = (int)$queryParams['deadlineTime'] ?: $this->deadlineTime;
        }
    }
}
