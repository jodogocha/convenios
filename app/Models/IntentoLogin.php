<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntentoLogin extends Model
{
    use HasFactory;

    protected $table = 'intentos_login';
    public $timestamps = false;

    /**
     * CAMPOS CORREGIDOS PARA COINCIDIR CON LA MIGRACIÓN REAL
     */
    protected $fillable = [
        'email_o_username',     // MIGRACIÓN USA: email_o_username
        'ip_address',           // MIGRACIÓN USA: ip_address
        'exitoso',
        'mensaje',
        'fecha_intento',        // MIGRACIÓN USA: fecha_intento
    ];

    protected $casts = [
        'exitoso' => 'boolean',
        'fecha_intento' => 'datetime',
    ];

    /**
     * Método estático para registrar - CORREGIDO CON CAMPOS REALES
     */
    public static function registrar(
        string $emailOUsername,
        bool $exitoso,
        ?string $mensaje = null
    ): void {
        try {
            self::create([
                'email_o_username' => $emailOUsername,  // CAMPO REAL DE MIGRACIÓN
                'ip_address' => request()->ip(),        // CAMPO REAL DE MIGRACIÓN
                'exitoso' => $exitoso,
                'mensaje' => $mensaje,
                'fecha_intento' => now(),               // CAMPO REAL DE MIGRACIÓN
            ]);
        } catch (\Exception $e) {
            \Log::error('Error registrando intento de login: ' . $e->getMessage());
        }
    }

    /**
     * Registrar intento exitoso
     */
    public static function registrarExitoso(string $emailOUsername): void
    {
        self::registrar($emailOUsername, true, 'Login exitoso');
    }

    /**
     * Registrar intento fallido
     */
    public static function registrarFallido(string $emailOUsername, string $razon): void
    {
        self::registrar($emailOUsername, false, $razon);
    }

    /**
     * Obtener intentos fallidos recientes por IP - USANDO CAMPOS REALES
     */
    public static function intentosFallidosRecentesPorIp(string $ip, int $minutos = 15): int
    {
        try {
            return self::where('ip_address', $ip)      // CAMPO REAL
                ->where('exitoso', false)
                ->where('fecha_intento', '>=', now()->subMinutes($minutos))  // CAMPO REAL
                ->count();
        } catch (\Exception $e) {
            \Log::error('Error consultando intentos fallidos por IP: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Obtener intentos fallidos recientes por email/username - USANDO CAMPOS REALES
     */
    public static function intentosFallidosRecentesPorUsuario(string $emailOUsername, int $minutos = 15): int
    {
        try {
            return self::where('email_o_username', $emailOUsername)  // CAMPO REAL
                ->where('exitoso', false)
                ->where('fecha_intento', '>=', now()->subMinutes($minutos))  // CAMPO REAL
                ->count();
        } catch (\Exception $e) {
            \Log::error('Error consultando intentos fallidos por usuario: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Verificar si una IP está bloqueada - USANDO CAMPOS REALES
     */
    public static function ipBloqueada(string $ip, int $maxIntentos = 10, int $minutos = 15): bool
    {
        return self::intentosFallidosRecentesPorIp($ip, $minutos) >= $maxIntentos;
    }

    // Scopes útiles - USANDO CAMPOS REALES
    public function scopeExitosos($query)
    {
        return $query->where('exitoso', true);
    }

    public function scopeFallidos($query)
    {
        return $query->where('exitoso', false);
    }

    public function scopePorIp($query, $ip)
    {
        return $query->where('ip_address', $ip);       // CAMPO REAL
    }

    public function scopePorEmailOUsername($query, $emailOUsername)
    {
        return $query->where('email_o_username', $emailOUsername);  // CAMPO REAL
    }

    public function scopeRecientes($query, int $minutos = 60)
    {
        return $query->where('fecha_intento', '>=', now()->subMinutes($minutos));  // CAMPO REAL
    }

    public function scopeOrdenadosPorFecha($query)
    {
        return $query->orderBy('fecha_intento', 'desc');  // CAMPO REAL
    }

    /**
     * Limpiar intentos antiguos
     */
    public static function limpiarAntiguos(int $dias = 30): int
    {
        try {
            return self::where('fecha_intento', '<', now()->subDays($dias))->delete();  // CAMPO REAL
        } catch (\Exception $e) {
            \Log::error('Error limpiando intentos antiguos: ' . $e->getMessage());
            return 0;
        }
    }
}