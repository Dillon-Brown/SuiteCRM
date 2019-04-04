<?php

namespace Cron;

/**
 * Minutes field.  Allows: * , / -
 */
class MinutesField extends AbstractField
{
    /**
     * @param \DateTime $date
     * @param string $value
     * @return bool
     */
    public function isSatisfiedBy(\DateTime $date, $value)
    {
        return $this->isSatisfied($date->format('i'), $value);
    }

    /**
     * @param \DateTime $date
     * @param bool $invert
     * @return $this|FieldInterface
     */
    public function increment(\DateTime $date, $invert = false)
    {
        if ($invert) {
            $date->modify('-1 minute');
        } else {
            $date->modify('+1 minute');
        }

        return $this;
    }

    /**
     * @param string $value
     * @return bool
     */
    public function validate($value)
    {
        return (bool) preg_match('/^[\*,\/\-0-9]+$/', $value);
    }
}
