<?php

namespace Atendwa\SuStarterKit\Http\Requests\Auth;

use Atendwa\SuStarterKit\Concerns\ErrorParser;
use Atendwa\SuStarterKit\Contracts\Authenticator;
use Exception;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class LoginRequest extends FormRequest
{
    use ErrorParser;

    private string $message;

    private string $field;

    private Authenticator $authenticator;

    public function __construct(Authenticator $authenticator)
    {
        parent::__construct();

        $this->authenticator = $authenticator;
    }

    public function rules(): array
    {
        return [
            'email' => 'email|string|max:100|min:1|required_if:username,null',
            'username' => 'string|max:30|min:1|required_if:email,null',
            'password' => 'string|required|max:30|min:1',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     * @throws Throwable
     */
    public function authenticate(): void
    {
        $this->field = database_driver_is_enabled() ? 'email' : 'username';

        $this->ensureIsNotRateLimited();

        $this->message = trans('auth.failed');

        $key = $this->throttleKey();

        match ($this->attemptLogin()) {
            false => $this->handleFailedLogin($key),
            true => RateLimiter::clear($key),
        };
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        $max = config('authentication.max_login_failures');

        $key = $this->throttleKey();

        match (RateLimiter::tooManyAttempts($key, $max)) {
            true => $this->handleRateLimited($key),
            false => null,
        };
    }

    public function throttleKey(): string
    {
        $key = Str::lower($this->input($this->field)) . '|' . $this->ip();

        return Str::transliterate($key);
    }

    private function handleRateLimited(string $key): void
    {
        event(new Lockout($this));

        $time = [];

        $time['seconds'] = RateLimiter::availableIn($key);

        $time['minutes'] = ceil($time['seconds'] / 60);

        $attributes = [$this->field => trans('auth.throttle', $time)];

        throw ValidationException::withMessages($attributes);
    }

    /**
     * @throws Throwable
     */
    private function attemptLogin(): bool
    {
        try {
            return $this->authenticator->authenticate();
        } catch (Exception $exception) {
            $default = 'Unable to authenticate your credentials at this time.';

            $error = $exception->getMessage();

            $this->message = $this->parseError($error, $default);

            Log::error($error);

            return false;
        }
    }

    private function handleFailedLogin(string $key): void
    {
        RateLimiter::hit($key);

        $attributes = [$this->field => $this->message];

        throw ValidationException::withMessages($attributes);
    }
}
