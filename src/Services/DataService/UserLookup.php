<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Services\DataService;

use Atendwa\SuStarterKit\Actions\StoreStaff;
use Atendwa\SuStarterKit\Actions\StoreStudent;
use Atendwa\SuStarterKit\Exceptions\MissingUsername;
use Atendwa\SuStarterKit\Exceptions\UserNotFound;
use Atendwa\SuStarterKit\Facades\ApiClient;
use Atendwa\SuStarterKit\Models\User;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserLookup
{
    private ?Collection $payload = null;

    private ?string $username = '';

    private string $type = 'staff';

    private ?int $number = null;

    private bool $search = true;

    private bool $shouldSetDepartment = true;

    private ?User $user = null;

    private StoreStudent $storeStudent;

    private StoreStaff $storeStaff;

    public function __construct()
    {
        $this->storeStudent = new StoreStudent();

        $this->storeStaff = new StoreStaff();
    }

    public function search(bool $search): self
    {
        $this->search = $search;

        return $this;
    }

    public function username(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function number(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function shouldSetDepartment(bool $shouldSetDepartment): self
    {
        $this->shouldSetDepartment = $shouldSetDepartment;

        return $this;
    }

    public function staff(): self
    {
        $this->type = 'staff';

        return $this;
    }

    public function student(): self
    {
        $this->type = 'student';

        return $this;
    }

    public function payload(Collection $payload): self
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function fetch(): ?User
    {
        $this->user = get_user_by_username($this->username);

        $code = tannery($this->user, 1, null);

        $canSearch = both($this->search, filled($this->username));

        $canSearch = both($canSearch, ! $this->user);

        $code = tannery($canSearch, 2, $code);

        $dontSearch = both(! $this->search, ! $this->user);

        $code = tannery($dontSearch, 3, $code);

        return match ($code) {
            1 => tap($this->user, fn () => $this->reset()),
            2 => tap($this->attemptSearch(), fn () => $this->reset()),
            3 => null,
            default => $this->resetAndThrow(),
        };
    }

    private function reset(): void
    {
        $this->shouldSetDepartment = true;

        $this->number = null;

        $this->username = '';

        $this->payload = null;

        $this->type = 'staff';

        $this->search = true;

        $this->user = null;
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    private function attemptSearch(): User
    {
        $this->payload = $this->payload ?? $this->loadPayload();

        throw_if($this->payload->isEmpty(), new UserNotFound());

        return $this->attemptPersist();
    }

    /**
     * @throws Exception
     */
    private function loadPayload(): Collection
    {
        return match ($this->type) {
            'student' => ApiClient::fetchStudent($this->username),
            default => $this->fetchStaff(),
        };
    }

    /**
     * @throws Exception
     */
    private function fetchStaff(): ?Collection
    {
        return match ($this->number) {
            null => ApiClient::fetchStaffMemberByUsername($this->username),
            default => ApiClient::fetchStaffMemberByNumber($this->number),
        };
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    private function attemptPersist(): User
    {
        return DB::transaction(function () {
            return match ($this->type) {
                'student' => $this->storeStudent->execute($this->payload),
                'staff' => $this->storeStaff->execute(
                    $this->payload,
                    $this->shouldSetDepartment,
                )
            };
        });
    }

    /**
     * @throws Throwable
     */
    private function resetAndThrow(): null
    {
        $username = $this->username;

        $this->reset();

        throw_if(! $username, new MissingUsername());

        return null;
    }
}
