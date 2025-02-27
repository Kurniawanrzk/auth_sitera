<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Traits\HasUuid;
use App\Models\{
    BankSampahUnit,
    Nasabah,
    Perusahaan,
    Pemerintah
};
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasUuid;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'password',
        'role',
        'id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'id' => 'string',
    ];


    public function getJWTIdentifier() {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array {
        return [];
    }

      // Relasi berdasarkan role
      public function nasabah()
      {
          return $this->hasOne(Nasabah::class);
      }
  
      public function bankSampahUnit()
      {
          return $this->hasOne(BankSampahUnit::class);
      }
  
      public function perusahaan()
      {
          return $this->hasOne(Perusahaan::class);
      }
  
      public function pemerintah()
      {
          return $this->hasOne(Pemerintah::class);
      }
  
      // Helper method untuk get profile berdasarkan role
      public function profile()
      {
          return match($this->role) {
              'nasabah' => $this->nasabah,
              'bsu' => $this->bankSampahUnit,
              'perusahaan' => $this->perusahaan,
              'pemerintah' => $this->pemerintah,
              default => null,
          };
      }
}
