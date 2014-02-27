<?php


class Paysera_WalletApi_Entity_PayCategory
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $parentId;

    /**
     * @var Paysera_WalletApi_Entity_PayCategory
     */
    protected $parent;

    /**
     * @var int
     */
    protected $rootId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $parentId
     *
     * @return $this
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * @param Paysera_WalletApi_Entity_PayCategory $parent
     *
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Paysera_WalletApi_Entity_PayCategory
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return Paysera_WalletApi_Entity_PayCategory
     */
    public function getRoot()
    {
        if ($this->getParent() === null) {
            return $this;
        } else {
            return $this->getParent()->getRoot();
        }
    }

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
