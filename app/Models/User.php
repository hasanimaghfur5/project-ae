<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'peran', 'no_telepon', 'alamat'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function pesanan(): HasMany
    {
        return $this->hasMany(Pesanan::class, 'id_pengguna', 'id');
    }

    public function isOwner(): bool
    {
        return $this->peran === 'owner';
    }

    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isKasir(): bool
    {
        return $this->peran === 'kasir';
    }

    public function isPelanggan(): bool
    {
        return $this->peran === 'pelanggan';
    }

    public function isStaf(): bool
    {
        return in_array($this->peran, ['owner', 'admin', 'kasir'], true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
