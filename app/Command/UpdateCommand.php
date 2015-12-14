<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Models\WallStat;

class UpdateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('wall:update')
            ->setDescription('Update info about posts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);

        $wallStat = new WallStat($this->app['db'], $this->app['config']['group.domain']);
        try {
            $wallStat->update();
            $output->writeln('<info>Updated successfully</info>');
            $output->writeln(sprintf('Time: %f sec.', microtime(true) - $startTime));
        } catch (\Exception $e) {
            $output->writeln('<error>An error occurred: '. $e->getMessage() .'</error>');
        }
    }
}
