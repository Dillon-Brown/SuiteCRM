<?php

class DotListWizardMenu
{
    private $html;

    /**
     * DotListWizardMenu constructor.
     * @param $mod_strings
     * @param $steps
     * @param bool $showLinks
     */
    public function __construct($mod_strings, $steps, $showLinks = false)
    {
        $nav_html = '';

        $i = 0;
        if (isset($steps) && !empty($steps)) {
            foreach ($steps as $name => $step) {
                $nav_html .= $this->getWizardMenuItemHTML(++$i, $name, $showLinks ? $step : false);
            }
        }

        $nav_html = $this->getWizardMenuHTML($nav_html);

        $this->html = $nav_html;
    }

    /**
     * @param $i
     * @param $label
     * @param bool $link
     * @return string
     */
    private function getWizardMenuItemHTML($i, $label, $link = false)
    {
        if ($i >= 4) {
            parse_str($link, $args);
            if (empty($args['marketing_id'])) {
                $link = false;
            }
        }

        if ($link != false) {
            $html = '<li id="nav_step'.$i.'" class="nav-steps" data-nav-step="'.$i.'" data-nav-url="'.$link.'"><div>'.$label.'</div></li>';
        } else {
            $html = '<li id="nav_step'.$i.'" class="nav-steps" data-nav-step="'.$i.'"  data-nav-url=""><div>'.$label.'</div></li>';
        }
        return $html;
    }

    /**
     * @param $innerHTML
     * @return false|string
     */
    private function getWizardMenuHTML($innerHTML)
    {
        $html = file_get_contents(__DIR__.DIRECTORY_SEPARATOR.'tpls'.DIRECTORY_SEPARATOR.'progressStepsStyle.html');
        $html .=
'<div class="progression-container">
    <ul class="progression">
    '.$innerHTML.'
    </ul>
</div>';
        return $html;
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return $this->html;
    }
}
