<?php

namespace App\Helpers;

use Carbon\Carbon;

class TimezoneHelper
{
    /**
     * Obtiene la fecha y hora actual del sistema
     */
    public static function now(): Carbon
    {
        return Carbon::now(config('app.timezone'));
    }

    /**
     * Convierte una fecha UTC a la zona horaria del sistema
     */
    public static function toLocalTime($date): Carbon
    {
        if (!$date instanceof Carbon) {
            $date = Carbon::parse($date);
        }
        
        return $date->setTimezone(config('app.timezone'));
    }

    /**
     * Formatea una fecha al formato local
     */
    public static function formatLocal($date, string $format = 'd/m/Y H:i:s'): string
    {
        return self::toLocalTime($date)->format($format);
    }

    /**
     * Obtiene la zona horaria configurada
     */
    public static function getTimezone(): string
    {
        return config('app.timezone');
    }

    /**
     * Obtiene información de la zona horaria
     */
    public static function getTimezoneInfo(): array
    {
        $timezone = new \DateTimeZone(config('app.timezone'));
        $datetime = new \DateTime('now', $timezone);
        
        return [
            'timezone' => config('app.timezone'),
            'offset' => $timezone->getOffset($datetime),
            'name' => $timezone->getName(),
            'current_time' => $datetime->format('Y-m-d H:i:s T'),
        ];
    }
}