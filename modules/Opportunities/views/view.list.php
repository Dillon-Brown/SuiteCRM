<?php

require_once('include/MVC/View/views/view.list.php');
require_once('modules/Opportunities/OpportunitiesListViewSmarty.php');

class OpportunitiesViewList extends ViewList
{





    public function preDisplay()
    {
        $this->lv = new OpportunitiesListViewSmarty();
    }
}
