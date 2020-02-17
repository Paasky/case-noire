<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Location;
use Doctrine\DBAL\DBALException;
use Illuminate\Console\Command;

class ToggleSpatial extends Command
{
    protected $signature = 'toggle-spatial';

    protected $description = 'Toggles spatial DB fields to string and back, used before & after ´ide-helper:models´';

    public function handle()
    {
        $columnsByTable = [
            Location::table() => ['coords'],
            Agent::table() => ['coords'],
        ];

        $convertedPointToText = false;

        foreach ($columnsByTable as $table => $columns) {
            foreach ($columns as $column) {
                try {
                    \DB::connection()->getDoctrineColumn($table, $column);
                } catch (DBALException $e) {
                    if (!stristr($e->getMessage(), 'Unknown database type point requested')) {
                        throw $e;
                    }

                    // convert point to string
                    $sql = "ALTER TABLE `$table` CHANGE `$column` `coords` TEXT NULL DEFAULT NULL;";
                    $this->line($sql);
                    \DB::statement($sql);
                    $convertedPointToText = true;
                    continue;
                }

                // convert string to point
                $sql = "ALTER TABLE `$table` CHANGE `$column` `coords` POINT NULL DEFAULT NULL;";
                $this->line($sql);
                \DB::statement($sql);
            }
        }

        if ($convertedPointToText) {
            $this->info('Now run ´php artisan ide-helper:models --write --reset && php artisan toggle-spatial´');
        } else {
            $this->info('Ready! Now edit docblocks to set the correct types for ' . json_encode($columnsByTable));
        }
    }
}
