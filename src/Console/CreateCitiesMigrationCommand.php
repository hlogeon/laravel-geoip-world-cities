<?php

namespace Moharrum\LaravelGeoIPWorldCities\Console;

/*
 * \Moharrum\LaravelGeoIPWorldCities for Laravel 4
 *
 * Copyright (c) 2015 - 2016 LaravelGeoIPWorldCities
 *
 * @copyright  Copyright (c) 2015 - 2016 \Moharrum\LaravelGeoIPWorldCities
 * 
 * @license http://opensource.org/licenses/MIT MIT license
 */

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Moharrum\LaravelGeoIPWorldCities\Helpers\Config;

/**
 * @author Khalid Moharrum <khalid.moharram@gmail.com>
 */
class CreateCitiesMigrationCommand extends Command
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'cities:migration';

    /**
     * The signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cities:migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create the cities table migration file.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        if (File::exists(Config::publishedMigrationRealpath())) {
            if (!$this->confirm('The migration file already exists, overwrite it? [Yes|no]')) {
                $this->line('');

                return $this->info('Okay, no changes made to the file.');
            }
        }

        $inputFile = file_get_contents(Config::localMigrationRealpath());

        $outputFile = fopen(
                        Config::publishedMigrationRealpath(),
                        'w'
                    );

        if ($inputFile && $outputFile) {
            fwrite($outputFile, $inputFile);

            fclose($outputFile);
        } else {
            File::delete(Config::publishedSeederRealpath());

            $this->line('');

            return $this->error(
                        'There was an error creating the migration file, '
                        .'check write permissions for app/database/migrations directory'
                        .PHP_EOL
                        .PHP_EOL
                        .'If you think this is a bug, please submit a bug report '
                        .'at https://github.com/moharrum/laravel-geoip-world-cities/issues'
                    );
        }

        try {
            $this->callSilent('dump-autoload', []);
        } catch (\Exception $ex) {
            $this->line('');

            $this->comment(
                        '`php artisan dump-autoload` failed for unknown reason.'
                        .PHP_EOL
                        .'If you get a file not found error while migrating, run the command manually.'
                    );
        }

        $this->line('');

        $this->info('Okay, migration file created successfully.');
    }
}
