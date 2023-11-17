<?php

declare(strict_types=1);

namespace KCode\Modulith\Commands;

use Illuminate\Console\Command;
use KCode\Modulith\Conveyor;
use KCode\Modulith\ProgressBar;
use KCode\Modulith\Wrapping;

/**
 * remove an existing package.
 *
 * @author KalinkoCode
 **/
class DisablePackage extends Command
{
    use ProgressBar;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:disable {vendor} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a package from composer.json and the providers config.';

    /**
     * Packages roll off of the conveyor.
     *
     * @var object \KCode\Modulith\Conveyor
     */
    protected $conveyor;

    /**
     * Packages are packed in wrappings to personalise them.
     *
     * @var object \KCode\Modulith\Wrapping
     */
    protected $wrapping;

    /**
     * Create a new command instance.
     */
    public function __construct(Conveyor $conveyor, Wrapping $wrapping)
    {
        parent::__construct();
        $this->conveyor = $conveyor;
        $this->wrapping = $wrapping;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Start the progress bar
        $this->startProgressBar(2);

        // Defining vendor/package
        $this->conveyor->vendor($this->argument('vendor'));
        $this->conveyor->package($this->argument('name'));

        // Start removing the package
        $this->info('Disabling package '.$this->conveyor->vendor().'\\'.$this->conveyor->package().'...');
        $this->makeProgress();

        // Uninstall the package
        $this->info('Uninstalling package...');
        $this->conveyor->uninstallPackage();
        $this->makeProgress();

        // Finished removing the package, end of the progress bar
        $this->finishProgress('Package disabled successfully!');
    }
}
