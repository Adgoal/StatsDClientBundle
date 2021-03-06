<?php

namespace Liuggio\StatsDClientBundle\Tests\StatsCollector;

use Liuggio\StatsDClientBundle\StatsCollector\ExceptionStatsCollector;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExceptionStatsCollectorTest extends StatsCollectorBase
{
    public function testCollect()
    {
        $e = new \Exception('foo', 500);
        $c = new ExceptionStatsCollector('prefix', $this->mockStatsDFactory('prefix.500'));
        $flattened = FlattenException::create($e);
        $trace = $flattened->getTrace();

        $c->collect(new Request(), new Response(), $e);
    }
}
