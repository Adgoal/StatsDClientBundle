<?php
declare(strict_types=1);

namespace Liuggio\StatsDClientBundle\StatsCollector;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function microtime;
use function round;

/**
 * Class TimeStatsCollector
 * @package Liuggio\StatsDClientBundle\StatsCollector
 */
class TimeStatsCollector extends StatsCollector
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
        $startTime = $request->server->get('REQUEST_TIME_FLOAT', $request->server->get('REQUEST_TIME'));

        $time = microtime(true) - $startTime;
        $time = round($time * 1000);

        $statData = $this->getStatsdDataFactory()->timing($this->getStatsDataKey(), $time);
        $this->addStatsData($statData);

        return true;
    }
}