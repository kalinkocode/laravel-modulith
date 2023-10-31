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
class RemovePackage extends Command
{
    use ProgressBar;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:remove {vendor} {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove an existing package.';

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
     *
     * @return void
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
        $this->startProgressBar(4);

        $vendor = $this->argument('vendor');
        $name = $this->argument('name');

        if (strstr($vendor, '/')) {
            [$vendor, $name] = explode('/', $vendor);
        }

        $this->conveyor->vendor($vendor);
        $this->conveyor->package($name);

        // Start removing the package
        $this->info('Removing package '.$this->conveyor->vendor().'\\'.$this->conveyor->package().'...');
        $this->makeProgress();

        // Uninstall the package
        $this->info('Uninstalling package...');
        $this->conveyor->uninstallPackage();
        $this->makeProgress();

        // remove the package directory
        $this->info('Removing packages directory...');
        $this->conveyor->removeDir($this->conveyor->packagePath());
        $this->makeProgress();

        // Remove the vendor directory, if agreed to
        if ($this->confirm('Do you want to remove the vendor directory? [y|N]')) {
            if (count(scandir($this->conveyor->vendorPath())) !== 2) {
                $this->warn('vendor directory is not empty, continuing...');
            } else {
                $this->info('removing vendor directory...');
                $this->conveyor->removeDir($this->conveyor->vendorPath());
            }
        } else {
            $this->info('Continuing...');
        }
        $this->makeProgress();

        // Finished removing the package, end of the progress bar
        $this->finishProgress('Package removed successfully!');
    }
}
