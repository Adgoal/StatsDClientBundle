<?php
declare(strict_types=1);

namespace Liuggio\StatsDClientBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Class BaseCommand
 * @package Liuggio\StatsDClientBundle\Command
 */
abstract class BaseCommand extends ContainerAwareCommand
{
    /**
     * @return object
     */
    protected function getDataFactory()
    {
        return $this->getContainer()->get('liuggio_stats_d_client.factory');
    }

    /**
     * @return object
     */
    protected function getClientService()
    {
        return $this->getContainer()->get('liuggio_stats_d_client.service');
    }
}
