<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar tabla
        Service::truncate();

        // Servicios de Desarrollo
        $desarrolloServices = [
            ['name' => 'Desarrollo Web a Medida', 'subcategory' => 'Full Stack'],
            ['name' => 'Desarrollo de Aplicaciones Móviles', 'subcategory' => 'Mobile'],
            ['name' => 'Desarrollo de APIs REST', 'subcategory' => 'Backend'],
            ['name' => 'Desarrollo Frontend con Vue.js', 'subcategory' => 'Frontend'],
            ['name' => 'Desarrollo de E-commerce', 'subcategory' => null],
        ];

        foreach ($desarrolloServices as $service) {
            Service::create([
                'name' => $service['name'],
                'category' => 'Desarrollo',
                'subcategory' => $service['subcategory'],
                'description' => 'Servicio profesional de ' . strtolower($service['name']) . ' con las últimas tecnologías del mercado.',
                'is_active' => true,
            ]);
        }

        // Servicios de Marketing
        $marketingServices = [
            ['name' => 'Posicionamiento SEO', 'subcategory' => 'SEO'],
            ['name' => 'Campañas Google Ads', 'subcategory' => 'SEM'],
            ['name' => 'Marketing en Redes Sociales', 'subcategory' => null],
            ['name' => 'Email Marketing', 'subcategory' => null],
            ['name' => 'Marketing de Contenidos', 'subcategory' => null],
        ];

        foreach ($marketingServices as $service) {
            Service::create([
                'name' => $service['name'],
                'category' => 'Marketing',
                'subcategory' => $service['subcategory'],
                'description' => 'Estrategia de ' . strtolower($service['name']) . ' para maximizar tu presencia digital.',
                'is_active' => true,
            ]);
        }

        // Servicios de Diseño
        $disenoServices = [
            ['name' => 'Diseño UX/UI', 'subcategory' => 'UX/UI'],
            ['name' => 'Diseño de Marca', 'subcategory' => 'Gráfico'],
            ['name' => 'Diseño Web Responsive', 'subcategory' => 'Web'],
            ['name' => 'Diseño Gráfico Publicitario', 'subcategory' => 'Gráfico'],
            ['name' => 'Prototipado de Interfaces', 'subcategory' => 'UX/UI'],
        ];

        foreach ($disenoServices as $service) {
            Service::create([
                'name' => $service['name'],
                'category' => 'Diseño',
                'subcategory' => $service['subcategory'],
                'description' => 'Servicio de ' . strtolower($service['name']) . ' orientado a resultados.',
                'is_active' => true,
            ]);
        }

        // Servicios de Posicionamiento
        $posicionamientoServices = [
            ['name' => 'Auditoría SEO Completa', 'subcategory' => 'SEO'],
            ['name' => 'Optimización SEO On-Page', 'subcategory' => 'SEO'],
            ['name' => 'Link Building Premium', 'subcategory' => 'SEO'],
            ['name' => 'Gestión de Campañas SEM', 'subcategory' => 'SEM'],
            ['name' => 'Análisis de Palabras Clave', 'subcategory' => 'SEO'],
        ];

        foreach ($posicionamientoServices as $service) {
            Service::create([
                'name' => $service['name'],
                'category' => 'Posicionamiento',
                'subcategory' => $service['subcategory'],
                'description' => 'Mejora tu visibilidad online con ' . strtolower($service['name']) . '.',
                'is_active' => true,
            ]);
        }

        // Algunos servicios inactivos para pruebas
        Service::create([
            'name' => 'Servicio Descontinuado',
            'category' => 'Desarrollo',
            'subcategory' => null,
            'description' => 'Este servicio ya no está disponible.',
            'is_active' => false,
        ]);

        $this->command->info(Service::count() . ' servicios creados exitosamente');
    }
}