<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;

use Silex\Application;

abstract class ContainerAwareCommand extends Command
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app, $name = null)
    {
        parent::__construct($name);
        $this->app = $app;
    }
}
