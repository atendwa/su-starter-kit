<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Services\DataService;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ApiClient
{
    private array $departmentEndpoints;

    private array $studentEndpoints;

    private array $staffEndpoints;

    private string $baseUrl;

    private array $config;

    public function __construct()
    {
        $this->config = config('data_service');

        $this->baseUrl = $this->getBaseUrl();

        $this->config = $this->config['endpoints'];

        $this->departmentEndpoints = $this->config['departments'];

        $this->studentEndpoints = $this->config['students'];

        $this->staffEndpoints = $this->config['staff'];
    }

    /**
     * @throws Exception
     */
    public function fetchStaffMemberByUsername(string $username): Collection
    {
        $url = $this->staffEndpoints['by_username'] . $username;

        return $this->sendRequest($url);
    }

    /**
     * @throws Exception
     */
    public function fetchStaffMemberByNumber(int $userNumber): Collection
    {
        $url = $this->staffEndpoints['by_number'] . $userNumber;

        return $this->sendRequest($url);
    }

    /**
     * @throws Exception
     */
    public function fetchStudent(int $username): Collection
    {
        $url = $this->studentEndpoints['single'] . $username;

        return $this->sendRequest($url);
    }

    /**
     * @throws Exception
     */
    public function fetchDepartment(int $id): Collection
    {
        $url = $this->departmentEndpoints['single'] . $id;

        return $this->sendRequest($url);
    }

    /**
     * @throws Exception
     */
    public function fetchAllDepartments(): Collection
    {
        return $this->sendRequest($this->departmentEndpoints['all']);
    }

    /**
     * @throws Exception
     */
    public function fetchAllStaff(): Collection
    {
        return $this->sendRequest($this->staffEndpoints['all']);
    }

    /**
     * @throws Exception
     */
    public function fetchAllCurrentStudents(): Collection
    {
        return $this->sendRequest($this->studentEndpoints['all_current']);
    }

    /**
     * @throws Exception
     */
    public function fetchAllStudentsWithOpenAccounts(): Collection
    {
        $url = $this->studentEndpoints['all_with_open_accounts'];

        return $this->sendRequest($url);
    }

    private function getBaseUrl(): string
    {
        return match (app()->isProduction()) {
            true => $this->config['url']['live'],
            false => $this->config['url']['test'],
        };
    }

    /**
     * @throws Exception
     */
    private function sendRequest(string $url): Collection
    {
        return Http::baseUrl($this->baseUrl)->get($url)->collect();
    }
}
