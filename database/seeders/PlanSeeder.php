<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::updateOrCreate(['slug' => 'explorador-mensual'], [
            'name' => 'Explorador mensual',
            'description' => 'Acceso completo a actividades, materias y retos diarios.',
            'price_cents' => 999,
            'currency' => 'usd',
            'interval' => 'month',
            'stripe_price_id' => env('STRIPE_PRICE_MONTHLY'),
            'features' => ['Una actividad gratis por materia', 'Todas las actividades', 'Retos diarios', 'Racha y progreso completo'],
            'active' => true,
        ]);
    }
}