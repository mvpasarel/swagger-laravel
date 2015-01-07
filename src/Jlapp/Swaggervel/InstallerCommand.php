<?php

namespace Jlapp\Swaggervel;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class InstallerCommand extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'swaggervel:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'pushes views to public folder';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $this->info("Pushing swagger-ui assets to public folder");
        $this->call('publish:assets', array('package' => 'jlapp/swaggervel'));
    }

}
