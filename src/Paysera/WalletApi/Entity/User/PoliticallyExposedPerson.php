<?php

class Paysera_WalletApi_Entity_PoliticallyExposedPerson
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $relation;

    /**
     * @var array
     */
    protected $positions;

    /**
     * Creates object, used for fluent interface
     *
     * @return self
     */
    static public function create()
    {
        return new self();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @return array
     */
    public function getPositions()
    {
        return $this->positions;
    }
}
