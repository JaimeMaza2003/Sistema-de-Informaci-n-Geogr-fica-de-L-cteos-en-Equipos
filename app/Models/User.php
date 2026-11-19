<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telefono',
        'nombre_emprendimiento',
        'parroquia',
        'direccion',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function esAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function esEmprendedor(): bool
    {
        return $this->role === 'emprendedor';
    }

    public function esComercio(): bool
    {
        return $this->role === 'comercio';
    }

    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    public function oportunidades()
    {
        return $this->hasMany(Oportunidad::class);
    }

    public function propuestas()
    {
        return $this->hasMany(Propuesta::class);
    }

    public function establecimientos()
    {
        return $this->hasMany(Establecimiento::class);
    }
}
