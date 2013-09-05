<?php


/**
 * Base search result
 */
abstract class Paysera_WalletApi_Entity_Search_Result implements IteratorAggregate
{
    /**
     * @var integer
     */
    protected $total;

    /**
     * @var integer
     */
    protected $offset;

    /**
     * @var integer
     */
    protected $limit;

    /**
     * Gets limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Gets offset
     *
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Gets total
     *
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Retrieve an external iterator
     *
     * @return Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->getResultList());
    }

    /**
     * @return array
     */
    abstract public function getResultList();
}