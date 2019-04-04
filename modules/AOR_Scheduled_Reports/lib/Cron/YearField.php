<?php

namespace Cron;

/**
 * Year field.  Allows: * , / -
 */
class YearField extends AbstractField
{
    /**
     * @param \DateTime $date
     * @param string $value
     * @return bool
     */
    public function isSatisfiedBy(\DateTime $date, $value)
    {
        return $this->isSatisfied($date->format('Y'), $value);
    }

    /**
     * @param \DateTime $date
     * @param bool $invert
     * @return $this|FieldInterface
     */
    public function increment(\DateTime $date, $invert = false)
    {
        if ($invert) {
            $date->modify('-1 year');
            $date->setDate($date->format('Y'), 12, 31);
            $date->setTime(23, 59, 0);
        } else {
            $date->modify('+1 year');
            $date->setDate($date->format('Y'), 1, 1);
            $date->setTime(0, 0, 0);
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
