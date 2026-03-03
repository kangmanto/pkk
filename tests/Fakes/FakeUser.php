<?php

declare(strict_types=1);

namespace Tests\Fakes;

use Illuminate\Contracts\Auth\Authenticatable;

final class FakeUser implements Authenticatable
{
    public function __construct(
        public string $role = 'desa_admin',
        public int $area_id = 10,
        public string $area_level = 'desa',
        public string $mode = 'rw',
        public string $name = 'Test User',
        public string $area_name = 'Area Test',
        private int $id = 1,
    ) {
    }

    public function getAuthIdentifierName(): string
    {
        return 'id';
    }

    public function getAuthIdentifier(): int
    {
        return $this->id;
    }

    public function getAuthPassword(): string
    {
        return '';
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getRememberToken(): ?string
    {
        return null;
    }

    public function setRememberToken($value): void
    {
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }
}
