<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Servicios de Desarrollo
        $desarrolloServices = [
            ['name' => 'Desarrollo Web a Medida', 'sub_category' => 'Full Stack'],
            ['name' => 'Desarrollo de E-commerce', 'sub_category' => null],
            
        ];

        foreach ($desarrolloServices as $service) {
            Service::create([
                'name' => $service['name'],
                'category' => 'Desarrollo',
                'sub_category' => $service['sub_category'],
                'description' => 'Servicio profesional de ' . strtolower($service['name']) . ' con las últimas tecnologías del mercado.',
                'is_active' => true,
            ]);
        }

        // Servicios de Marketing
        $marketingServices = [
            ['name' => 'Posicionamiento Web', 'sub_category' => 'SEO'],
            ['name' => 'Marketing en Redes Sociales', 'sub_category' => null],
            ['name' => 'Email Marketing', 'sub_category' => null],
            ['name' => 'Marketing de Contenidos', 'sub_category' => null],
        ];

        foreach ($marketingServices as $service) {
            Service::create([
                'name' => $service['name'],
                'category' => 'Marketing',
                'sub_category' => $service['sub_category'],
                'description' => 'Estrategia de ' . strtolower($service['name']) . ' para maximizar tu presencia digital.',
                'is_active' => true,
            ]);
        }

        // Servicios de Diseño
        $disenoServices = [
            ['name' => 'Diseño UX/UI', 'sub_category' => 'UX/UI'],
            ['name' => 'Diseño de Marca', 'sub_category' => 'Gráfico'],
            ['name' => 'Diseño Gráfico', 'sub_category' => 'Gráfico'],
        ];

        foreach ($disenoServices as $service) {
            Service::create([
                'name' => $service['name'],
                'category' => 'Diseño',
                'sub_category' => $service['sub_category'],
                'description' => 'Servicio de ' . strtolower($service['name']) . ' orientado a resultados.',
                'is_active' => true,
            ]);
        }

        // Servicios de Posicionamiento
        $posicionamientoServices = [
            ['name' => 'Auditoría SEO Completa', 'sub_category' => 'SEO'],
        ];

        foreach ($posicionamientoServices as $service) {
            Service::create([
                'name' => $service['name'],
                'category' => 'Posicionamiento',
                'sub_category' => $service['sub_category'],
                'description' => 'Mejora tu visibilidad online con ' . strtolower($service['name']) . '.',
                'is_active' => true,
            ]);
        }

        // Algunos servicios inactivos para pruebas
        Service::create([
            'name' => 'Servicio Descontinuado',
            'category' => 'Desarrollo',
            'sub_category' => null,
            'description' => 'Este servicio ya no está disponible.',
            'is_active' => false,
        ]);

        $this->command->info(Service::count() . ' servicios creados exitosamente');
    }
}