<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Commands;

use Atendwa\SuStarterKit\Facades\ApiClient;
use Atendwa\SuStarterKit\Facades\DepartmentLookup;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use function Laravel\Prompts\info;
use function Laravel\Prompts\progress;
use Throwable;

//use function Laravel\Prompts\confirm;

class SeedDepartments extends Command
{
    protected $signature = 'su-starter-kit:seed-departments {--all : Seed all departments}';

    protected $description = 'Persists departments from the data service';

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        info('Fetching departments...');

        $departments = ApiClient::fetchAllDepartments();

        info('Retrieved ' . count($departments) . ' departments.');

        $departments = $departments->filter(
            static fn ($department) => $department['hodUsername'] !== null
        );

        info(count($departments) . ' valid departments.');

        $parentDepartments = $departments->filter(
            static fn ($department) => ! $department['subDepartment']
        );

        info(count($parentDepartments) . ' parent departments.');

        $this->processParentDepartments($parentDepartments);
    }

    private function processParentDepartments(Collection $departments): void
    {
        progress(
            'Creating parent departments...',
            $departments,
            fn ($payload) => $this->attemptPersist($payload)
        );
    }

    /**
     * @throws Throwable
     */
    private function attemptPersist(array $payload): void
    {
        Log::info('Storing ' . $payload['shortName'] . '...');

        try {
            DB::transaction(
                static fn () => DepartmentLookup::payload(collect($payload))->store()
            );
        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
