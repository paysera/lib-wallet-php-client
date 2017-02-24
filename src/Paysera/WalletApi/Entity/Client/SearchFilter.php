<?php

class Paysera_WalletApi_Entity_Client_SearchFilter extends Paysera_WalletApi_Entity_Search_Filter
{
    /**
     * @var string
     */
    private $projectId;

    /**
     * @return string
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     *
     * @return $this
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;

        return $this;
    }
}
