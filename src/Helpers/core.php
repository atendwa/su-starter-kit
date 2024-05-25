<?php

declare(strict_types=1);

use Atendwa\SuStarterKit\Exceptions\MissingUsername;
use Atendwa\SuStarterKit\Models\Department;
use Atendwa\SuStarterKit\Models\User;
use Atendwa\SuStarterKit\Support\Flash;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;

if (! function_exists('either')) {
    function either(?bool $first = null, ?bool $second = null): bool
    {
        return $first || $second;
    }
}

if (! function_exists('both')) {
    function both(?bool $first = null, ?bool $second = null): bool
    {
        return $first && $second;
    }
}

if (! function_exists('is_route_current')) {
    function is_route_current(string $route, bool $checkUrl = false): bool
    {
        $check = Route::is($route);

        if (! $checkUrl) {
            return $check;
        }

        $isChildRoute = str_contains(url()->current(), '/' . $route . '/');

        return either($check, $isChildRoute);
    }
}

if (! function_exists('is_a_match')) {
    function is_a_match(string $first, string $second): bool
    {
        return $first === $second;
    }
}

if (! function_exists('database_driver_is_enabled')) {
    function database_driver_is_enabled(): bool
    {
        return is_a_match(config('authentication.driver'), 'database');
    }
}

if (! function_exists('can')) {
    function can(string $permission): bool
    {
        return auth()->user()->can($permission);
    }
}

if (! function_exists('tannery')) {
    function tannery(mixed $condition, mixed $true, mixed $false): mixed
    {
        return $condition ? $true : $false;
    }
}

if (! function_exists('flash')) {
    function flash(
        ?string $title = null,
        ?string $message = null,
        string $type = 'success',
        bool $autoClose = true,
        int $seconds = 10,
    ): void {
        (new Flash($title, $message, $type, $autoClose, $seconds))->index();
    }
}

if (! function_exists('get_users_by_role')) {
    function get_users_by_role(
        string $role,
        bool $returnFirst = false,
        ?array $select = null
    ): Collection|User {
        $query = User::select($select ?? ['id', 'email', 'name'])->whereHas(
            'roles',
            static fn ($query) => $query->whereName($role)
        );

        return match ($returnFirst) {
            true => $query->first(),
            default => $query->get()
        };
    }
}

if (! function_exists('get_users_by_permission')) {
    function get_users_by_permission(
        string $permission,
        bool $returnFirst = false,
        ?array $select = null
    ): Collection|User {
        $query = User::select($select ?? ['id', 'email', 'name'])->whereHas(
            'permissions',
            static fn ($query) => $query->whereName($permission)
        );

        return match ($returnFirst) {
            true => $query->first(),
            default => $query->get()
        };
    }
}

if (! function_exists('get_department_by_shortname')) {
    /**
     * @throws Throwable
     */
    function get_department_by_shortname(string $code = ''): ?Department
    {
        $message = 'Department shortname is required';

        throw_if(mb_strlen($code) === 0, new Exception($message));

        return Department::whereCode($code)->first();
    }
}

if (! function_exists('get_user_by_username')) {
    /**
     * @throws Throwable
     */
    function get_user_by_username(string $username = ''): ?User
    {
        throw_if(mb_strlen($username) === 0, new MissingUsername());

        return User::whereUsername($username)->first();
    }
}
