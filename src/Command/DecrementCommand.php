<?php
declare(strict_types=1);

namespace Liuggio\StatsDClientBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to send a decrement command to statsd.
 *
 * @author Pablo Godel <pgodel@gmail.com>
 */
class DecrementCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('statsd:decrement')
            ->setDescription('Decreases a counter by 1 in StatsD')
            ->addArgument('key', InputArgument::REQUIRED, 'The key')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command sends a decrement metric to StatsD:

  <info>%command.full_name%</info>

EOT
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = $this->getDataFactory()->decrement($input->getArgument('key'));
        $this->getClientService()->send($data);
    }
}
