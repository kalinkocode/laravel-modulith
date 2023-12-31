<?php

declare(strict_types=1);

namespace KCode\Modulith\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use KCode\Modulith\Conveyor;
use KCode\Modulith\ProgressBar;
use KCode\Modulith\Wrapping;

/**
 * Get an existing package from a remote git repository with its VCS.
 *
 * @author KalinkoCode
 **/
class GitPackage extends Command
{
    use ProgressBar;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:git
                            {url : The url of the git repository}
                            {vendor? : The vendor part of the namespace}
                            {name? : The name of package for the namespace}
                            {--submodule : Add as a submodule instead just cloning}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve an existing package with git.';

    /**
     * Packages roll off of the conveyor.
     *
     * @var object \KalinkoCode\Packager\Conveyor
     */
    protected $conveyor;

    /**
     * Packages are packed in wrappings to personalise them.
     *
     * @var object \KalinkoCode\Packager\Wrapping
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

        // Common variables
        $source = $this->argument('url');
        $origin = rtrim(strtolower($source), '/');
        $submodule = $this->option('submodule') !== null;

        if ($this->argument('vendor') === null || $this->argument('name') === null) {
            $this->setGitVendorAndPackage($origin);
        } else {
            $this->conveyor->vendor($this->argument('vendor'));
            $this->conveyor->package($this->argument('name'));
        }

        // Start creating the package
        $this->info('Creating package '.$this->conveyor->vendor().'\\'.$this->conveyor->package().'...');
        $this->conveyor->checkIfPackageExists();
        $this->makeProgress();

        // Create the package directory
        $this->info('Creating packages directory...');
        $this->conveyor->makeDir($this->conveyor->packagesPath());

        // Clone the repository
        $gitcommand = $submodule ? 'git submodule add' : 'git clone';
        $this->info('Cloning repository...');
        exec("{$gitcommand} -q {$source} ".$this->conveyor->packagePath(), $output, $exit_code);

        if ($exit_code != 0) {
            $this->error('Unable to clone repository');
            $this->warn('Please check credentials and try again');

            return;
        }

        $this->makeProgress();

        // Create the vendor directory
        $this->info('Creating vendor...');
        $this->conveyor->makeDir($this->conveyor->vendorPath());
        $this->makeProgress();

        $this->info('Installing package...');
        $this->conveyor->installPackage();
        $this->makeProgress();

        // Finished creating the package, end of the progress bar
        $this->finishProgress('Package cloned successfully!');
    }

    protected function setGitVendorAndPackage($origin)
    {
        $pieces = explode('/', $origin);

        if (Str::contains($origin, 'https')) {
            $vendor = $pieces[3];
            $package = $pieces[4];
        } else {
            $vendor = explode(':', $pieces[0])[1];
            $package = rtrim($pieces[1], '.git');
        }

        $this->conveyor->vendor($vendor);
        $this->conveyor->package($package);
    }
}
