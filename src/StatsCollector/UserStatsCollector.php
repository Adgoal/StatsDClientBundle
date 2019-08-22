<?php
declare(strict_types=1);

namespace Liuggio\StatsDClientBundle\StatsCollector;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use function sprintf;

/**
 * Class UserStatsCollector
 * @package Liuggio\StatsDClientBundle\StatsCollector
 */
class UserStatsCollector extends StatsCollector
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
        if (null === $this->getSecurityContext()) {
            return true;
        }

        $key = sprintf('%s.anonymous', $this->getStatsDataKey());
        try {
            if ($this->getSecurityContext()->isGranted('IS_AUTHENTICATED_FULLY')) {
                $key = sprintf('%s.logged', $this->getStatsDataKey());
            }
        } catch (AuthenticationCredentialsNotFoundException $exception) {
            //do nothing
        }
        $statData = $this->getStatsdDataFactory()->increment($key);
        $this->addStatsData($statData);

        return true;
    }

    /**
     * @param AuthorizationCheckerInterface $security_context
     */
    public function setSecurityContext(AuthorizationCheckerInterface $security_context)
    {
        $this->security_context = $security_context;
    }

    /**
     * @return mixed
     */
    public function getSecurityContext()
    {
        return $this->security_context;
    }
}
