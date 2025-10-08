<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    
    protected $table = 'users';
    protected $primaryKey = 'id_usuario';
    
    protected $fillable = [
        'name',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'telefono',
        'tipo_usuario',
        'password',
        'activo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

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

    // Relaciones
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'id_usuario', 'id_usuario');
    }

    #[Scope] 
    /**
     * Retorna los usuarios por tipo (almacenado como enum en bd)
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tipo
     * @return void
     */
    protected function porTipo(Builder $query, string $tipo){
        $query -> where('usuario', $tipo);
    }

    #[Scope]
    /**
     * Scope para usuarios que tengan una reserva válida
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    protected function conReserva(Builder $query){
        $query -> with('reservas', function($subquery){
            $subquery -> whereDate('fecha_checkin','>=', now());
        });
    }
}
