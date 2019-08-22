<?php
declare(strict_types=1);

namespace Liuggio\StatsDClientBundle\StatsCollector;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class VisitorStatsCollector
 * @package Liuggio\StatsDClientBundle\StatsCollector
 */
class VisitorStatsCollector extends StatsCollector
{
    /**
     * Collects data for the given Response.
     *
     * @param Request    $request   A Request instance
     * @param Response   $response  A Response instance
     * @param Exception $exception An exception instance if the request threw one
     *
     * @return bool
     */
    public function collect(Request $request, Response $response, Exception $exception = null)
    {
        $statData = $this->getStatsdDataFactory()->increment($this->getStatsDataKey());
        $this->addStatsData($statData);

        return true;
    }
}
