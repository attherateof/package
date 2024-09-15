<?php

namespace Easy\Eav\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Easy\Eav\Models\Service\EavService;

class MakeEav extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'easy:make-eav {class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @param \Easy\Eav\Models\Service\EavService $eavService
     * @return void
     */
    public function __construct(private readonly EavService $eavService)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->comment('Creating entity');

        // Get the class name from the argument
        $classPath = $this->argument('class');

        // Check if the class exists
        if (!class_exists($classPath)) {
            $this->error("Class {$classPath} does not exist.");
            
            return SymfonyCommand::FAILURE;
        }

        // Get the EAV_DATA property from the class
        $classInstance = new $classPath;
        
        if (!defined("{$classPath}::EAV_DATA")) {
            $this->error("Class {$classPath} does not have EAV_DATA.");
            
            return SymfonyCommand::FAILURE;
        }

        // Read the EAV data
        $eavData = $classPath::EAV_DATA;

        // Execute the EavService to populate EAV data
        try {
            $this->eavService->populateEavData($eavData);
            $this->info("EAV data populated successfully from {$classPath}.");
        } catch (\Exception $e) {
            $this->error('Error populating EAV data: ' . $e->getMessage());
            
            return SymfonyCommand::SUCCESS;
        }

        return SymfonyCommand::FAILURE;
    }
}
