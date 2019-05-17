<?php

namespace SuiteCRM\Robo;

use Robo\Tasks;

/**
 * Class RoboFile
 * @package SuiteCRM\Robo
 */
class RoboFile extends Tasks
{
    public function run()
    {
        $this->io()->progressStart(100);
        for ($i = 0; $i < 10; $i++) {
            $this->io()->progressAdvance(10);
            sleep(1);
        }
        $this->io()->progressFinish();
    }
}
