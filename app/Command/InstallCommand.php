<?php

namespace App\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('wall:install')
            ->setDescription('Create tables in database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Silverslice\EasyDb\Database $db */
        $db = $this->app['db'];

        $db->query('
            CREATE TABLE IF NOT EXISTS posts (
              id int(11) NOT NULL PRIMARY KEY,
              created_at int(11) NOT NULL,
              signer_id int(11) NOT NULL,
              text text NOT NULL,
              likes_count int(11) NOT NULL,

              KEY signer_id (signer_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ');

        $output->writeln('<info>Created posts table</info>');

        $db->query('
            CREATE TABLE IF NOT EXISTS profiles (
              id int(11) NOT NULL PRIMARY KEY,
              first_name varchar(255) NOT NULL,
              last_name varchar(255) NOT NULL

            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ');

        $output->writeln('<info>Created profiles table</info>');

        $db->query('
            CREATE TABLE IF NOT EXISTS updates (
              id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              updated_at int(11) NOT NULL

            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ');

        $output->writeln('<info>Created updates statistics table</info>');
    }
}
