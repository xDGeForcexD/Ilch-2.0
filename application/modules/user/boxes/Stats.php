<?php
/**
 * @copyright Ilch 2.0
 * @package ilch
 */

namespace User\Boxes;
defined('ACCESS') or die('no direct access');

class Stats extends \Ilch\Box
{
    public function render()
    {
        $date = new \Ilch\Date();
        $visitMapper = new \User\Mappers\Visit();
        $this->getView()->set('visitsToday', $visitMapper->getVisitsCount($date->format('Y-m-d')));
        $date->modify('-1 day');
        $this->getView()->set('visitsYesterday', $visitMapper->getVisitsCount($date->format('Y-m-d')));
        $this->getView()->set('visitsTotal', $visitMapper->getVisitsCount());
    }
}

