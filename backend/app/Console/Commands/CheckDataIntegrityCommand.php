<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;

final class CheckDataIntegrityCommand extends Command
{
    protected $signature = 'garden:check-integrity';

    protected $description = 'Detect invalid morph values, orphaned records, and ownership inconsistencies.';

    private int $issues = 0;

    public function handle(): int
    {
        $this->checkMorphRecords('task_templates', 'targetable', ['site', 'zone', 'plant']);
        $this->checkMorphRecords('tasks', 'targetable', ['site', 'zone', 'plant']);
        $this->checkMorphRecords('work_logs', 'loggable', ['site', 'zone', 'plant']);
        $this->checkMorphRecords('photos', 'imageable', ['site', 'zone', 'plant', 'work_log']);

        $this->checkMissingParents('readings', 'device_id', 'devices');
        $this->checkMissingParents('chemical_applications', 'work_log_id', 'work_logs');
        $this->checkMissingParents('alerts', 'alert_rule_id', 'alert_rules');
        $this->checkMissingParents('weather_daily', 'site_id', 'sites');

        if ($this->issues === 0) {
            $this->info('Data integrity check passed.');

            return self::SUCCESS;
        }

        $this->error("Data integrity check failed: {$this->issues} issue(s) found.");

        return self::FAILURE;
    }

    /**
     * @param  list<string>  $allowedTypes
     */
    private function checkMorphRecords(string $table, string $columnPrefix, array $allowedTypes): void
    {
        $typeColumn = "{$columnPrefix}_type";
        $idColumn = "{$columnPrefix}_id";
        $morphMap = Relation::morphMap();

        DB::table($table)
            ->select(['id', 'user_id', $typeColumn, $idColumn])
            ->orderBy('id')
            ->each(function (object $record) use ($allowedTypes, $morphMap, $table, $columnPrefix, $typeColumn, $idColumn): void {
                $type = $record->{$typeColumn};
                $targetId = (int) $record->{$idColumn};

                if (! in_array($type, $allowedTypes, true) || ! array_key_exists($type, $morphMap)) {
                    $this->issue("{$table}#{$record->id}: invalid {$typeColumn} [{$type}].");

                    return;
                }

                $targetModel = $morphMap[$type];
                $targetTable = (new $targetModel)->getTable();
                $target = DB::table($targetTable)->select(['id', 'user_id'])->find($targetId);

                if ($target === null) {
                    $this->issue("{$table}#{$record->id}: orphaned {$columnPrefix} target {$type}:{$targetId}.");

                    return;
                }

                if ((int) $target->user_id !== (int) $record->user_id) {
                    $this->issue("{$table}#{$record->id}: {$columnPrefix} target owner does not match user_id.");
                }
            });
    }

    private function checkMissingParents(string $childTable, string $foreignKey, string $parentTable): void
    {
        DB::table($childTable)
            ->leftJoin($parentTable, "{$parentTable}.id", '=', "{$childTable}.{$foreignKey}")
            ->whereNull("{$parentTable}.id")
            ->select("{$childTable}.id")
            ->orderBy("{$childTable}.id")
            ->each(function (object $record) use ($childTable, $foreignKey, $parentTable): void {
                $this->issue("{$childTable}#{$record->id}: missing {$parentTable} parent for {$foreignKey}.");
            });
    }

    private function issue(string $message): void
    {
        $this->issues++;
        $this->error($message);
    }
}
