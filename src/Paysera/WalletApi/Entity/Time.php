<?php

/**
 * Time in 24hour format
 */
class Paysera_WalletApi_Entity_Time
{
    /**
     * @var int
     */
    protected $hours;

    /**
     * @var int
     */
    protected $minutes;

    /**
     * @param int $hours
     * @param int $minutes
     */
    public function __construct($hours, $minutes)
    {
        $this->hours = $hours;
        $this->minutes = $minutes;

        $this->validate();
    }

    /**
     * Get hours
     *
     * @return int
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Get minutes
     *
     * @return int
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return str_pad($this->hours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($this->minutes, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Validation
     *
     * @throws Paysera_WalletApi_Exception_LogicException
     */
    protected function validate()
    {
        if (is_numeric($this->hours)) {
            $this->hours = (int) $this->hours;
        } else {
            throw new \InvalidArgumentException('Hours must be an integer');
        }

        if (is_numeric($this->minutes)) {
            $this->minutes = (int) $this->minutes;
        } else {
            throw new \InvalidArgumentException('Minutes must be an integer');
        }

        if ($this->hours < 0 || $this->hours > 23) {
            throw new Paysera_WalletApi_Exception_LogicException('Hours must be an integer and provided within [0,23] interval.');
        }

        if ($this->minutes < 0 || $this->minutes > 59) {
            throw new Paysera_WalletApi_Exception_LogicException('Minutes must be an integer provided within [0,59] interval.');
        }
    }
}