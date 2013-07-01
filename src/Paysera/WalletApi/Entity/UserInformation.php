<?php

/**
 * Entity representing confirmed user information
 */
class Paysera_WalletApi_Entity_UserInformation
{
    /**
     * @var string
     */
    protected $email;

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
     * Gets email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets email
     *
     * @param string $email

     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

}
