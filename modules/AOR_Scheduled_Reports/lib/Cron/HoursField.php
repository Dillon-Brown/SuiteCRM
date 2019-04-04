<?php

namespace Cron;

/**
 * Hours field.  Allows: * , / -
 */
class HoursField extends AbstractField
{
    /**
     * @param \DateTime $date
     * @param string $value
     * @return bool
     */
    public function isSatisfiedBy(\DateTime $date, $value)
    {
        return $this->isSatisfied($date->format('H'), $value);
    }

    /**
     * @param \DateTime $date
     * @param bool $invert
     * @return $this|FieldInterface
     */
    public function increment(\DateTime $date, $invert = false)
    {
        // Change timezone to UTC temporarily. This will
        // allow us to go back or forwards and hour even
        // if DST will be changed between the hours.
        $timezone = $date->getTimezone();
        $date->setTimezone(new \DateTimeZone('UTC'));
        if ($invert) {
            $date->modify('-1 hour');
            $date->setTime($date->format('H'), 59);
        } else {
            $date->modify('+1 hour');
            $date->setTime($date->format('H'), 0);
        }
        $date->setTimezone($timezone);

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
