<?php
declare(strict_types=1);

namespace Liuggio\StatsDClientBundle\StatsCollector;

use Exception;
use Liuggio\StatsdClient\Entity\StatsdDataInterface;
use Liuggio\StatsdClient\Factory\StatsdDataFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use function str_replace;
use function strlen;
use function strstr;
use function strtolower;
use function substr;
use function trim;

/**
 * Class StatsCollector
 * @package Liuggio\StatsDClientBundle\StatsCollector
 */
abstract class StatsCollector implements StatsCollectorInterface
{
    /**
     * @var string
     */
    protected $statsDataKey;
    /**
     * @var StatsdDataInterface[]
     */
    protected $statsData;
    /**
     * @var StatsdDataFactoryInterface
     */
    protected $StatsdDataFactory;
    /**
     * @var bool
     */
    protected $onlyOnMasterResponse;
    /**
     * @var StatsdDataFactoryInterface|null
     */
    private $statsdDataFactory;

    /**
     * StatsCollector constructor.
     * @param string $stat_key
     * @param StatsdDataFactoryInterface|null $stats_data_factory
     * @param bool $only_on_master_response
     */
    public function __construct($stat_key = __CLASS__, StatsdDataFactoryInterface $stats_data_factory = null, $only_on_master_response = false)
    {
        $this->setStatsDataKey($stat_key);
        $this->statsdDataFactory = $stats_data_factory;
        $this->setOnlyOnMasterResponse($only_on_master_response);
    }

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
        return true;
    }

    /**
     * @return mixed
     */
    public function getStatsData()
    {
        if (null === $this->statsData) {
            return [];
        }

        return $this->statsData;
    }

    /**
     * @param StatsdDataInterface $statsData
     *
     */
    public function addStatsData(StatsdDataInterface $statsData)
    {
        $this->statsData[] = $statsData;
    }

    /**
     * @param string $key
     */
    public function setStatsDataKey($key)
    {
        $this->statsDataKey = $key;
    }

    /**
     * @return string
     */
    public function getStatsDataKey()
    {
        return $this->statsDataKey;
    }

    /**
     * @param StatsdDataFactoryInterface $StatsdDataFactory
     */
    public function setStatsdDataFactory(StatsdDataFactoryInterface $StatsdDataFactory)
    {
        $this->statsdDataFactory = $StatsdDataFactory;
    }

    /**
     * @return StatsdDataFactoryInterface|null
     */
    public function getStatsdDataFactory()
    {
        return $this->statsdDataFactory;
    }

    /**
     * @param bool $onlyOnMasterResponse
     */
    public function setOnlyOnMasterResponse($onlyOnMasterResponse)
    {
        $this->onlyOnMasterResponse = $onlyOnMasterResponse;
    }

    /**
     * @return bool
     */
    public function getOnlyOnMasterResponse()
    {
        return $this->onlyOnMasterResponse;
    }

    /**
     * Extract the first word, its maximum length is limited to $maxLenght chars.
     *
     * @param string $string
     *
     * @param int $maxLength
     * @return mixed
     */
    protected function extractFirstWord($string, $maxLength = 25)
    {
        $string = str_replace(['"', "'"], '', $string);
        $string = trim($string);
        $length = (strlen($string) > $maxLength) ? $maxLength : strlen($string);
        $string = strtolower(strstr(substr(trim($string), 0, $length), ' ', true));

        return $string;
    }
}
