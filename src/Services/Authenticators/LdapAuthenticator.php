<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Services\Authenticators;

use Atendwa\SuStarterKit\Contracts\Authenticator;
use Atendwa\SuStarterKit\Facades\UserLookup;
use Illuminate\Support\Facades\Auth;
use LdapRecord\Auth\PasswordRequiredException;
use LdapRecord\Auth\UsernameRequiredException;
use LdapRecord\Connection;
use LdapRecord\Container;
use LdapRecord\ContainerException;
use Throwable;

class LdapAuthenticator implements Authenticator
{
    protected array $config;

    protected string $username;

    protected string $password;

    protected Connection $connection;

    protected string $domain;

    protected bool $isStudentLogin;

    protected bool $blockStudents;

    /**
     * @throws ContainerException
     */
    public function __construct()
    {
        $this->config = config('authentication.drivers.ldap');

        $this->username = request()->input('username');

        $this->password = request()->input('password');

        $this->isStudentLogin = is_numeric($this->username);

        $allowsStudents = config('authentication.allow_student_logins');

        $this->blockStudents = both($this->isStudentLogin, ! $allowsStudents);

        $name = tannery($this->isStudentLogin, 'student', 'staff');

        $this->connection = Container::getConnection($name);

        $staff = config('authentication.drivers.ldap.domain.staff');

        $student = config('authentication.drivers.ldap.domain.student');

        $this->domain = tannery($this->isStudentLogin, $student, $staff);
    }

    /**
     * @throws Throwable
     */
    public function authenticate(): bool
    {
        return match ($this->attempt()) {
            true => $this->authenticated(),
            false => $this->failed(),
        };
    }

    /**
     * @throws Throwable
     * @throws ContainerException
     * @throws PasswordRequiredException
     * @throws UsernameRequiredException
     */
    private function attempt(): bool
    {
        throw_if($this->blockStudents, 'Students are not allowed to login!');

        $adUsername = $this->username . '@' . $this->domain;

        return $this->connection->auth()->attempt($adUsername, $this->password);
    }

    /**
     * @throws Throwable
     */
    private function authenticated(): bool
    {
        $user = UserLookup::username($this->username);

        $user = match ($this->isStudentLogin) {
            true => $user->student(),
            false => $user->staff(),
        };

        Auth::loginUsingId($user->fetch()->id);

        return Auth::check();
    }

    /**
     * @throws Throwable
     */
    private function failed(): false
    {
        $message = $this->connection->getLdapConnection();

        $message = $message->getDiagnosticMessage();

        throw_if(str_contains($message, '532'), 'Your password has expired.');

        throw_if(str_contains($message, '533'), 'Your account is disabled.');

        throw_if(str_contains($message, '701'), 'Your account has expired.');

        throw_if(str_contains($message, '775'), 'Your account is locked.');

        throw_if(true, 'Username or password is incorrect.');

        return false;
    }
}
