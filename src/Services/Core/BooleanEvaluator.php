<?php

declare(strict_types=1);

namespace Atendwa\SuStarterKit\Services\Core;

class BooleanEvaluator
{
    protected ?bool $result = null;

    public function initial(bool $initial): self
    {
        $this->result = $initial;

        return $this;
    }

    public function both(bool $value): self
    {
        $this->result = both($this->result, $value);

        return $this;
    }

    public function either(bool $value): self
    {
        $this->result = either($this->result, $value);

        return $this;
    }

    public function andThenOr(bool $first, bool $second): self
    {
        $this->result = either($this->result, both($first, $second));

        return $this;
    }

    public function orThenAnd(bool $first, bool $second): self
    {
        $this->result = both($this->result, either($first, $second));

        return $this;
    }

    public function result(): bool
    {
        $result = $this->result;

        $this->result = null;

        return (bool) $result;
    }
}
