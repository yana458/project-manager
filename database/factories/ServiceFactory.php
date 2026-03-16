<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $categories = [
            'Desarrollo' => [
                'subcategories' => [null, 'Frontend', 'Backend', 'Full Stack', 'Mobile'],
                'services' => [
                    'Desarrollo de aplicaciones web',
                    'Desarrollo de aplicaciones móviles',
                    'Desarrollo de APIs REST',
                    'Desarrollo e-commerce',
                    'Mantenimiento de software',
                ]
            ],
            'Marketing' => [
                'subcategories' => ['SEO', 'SEM', null],
                'services' => [
                    'Estrategia de marketing digital',
                    'Gestión de redes sociales',
                    'Email marketing',
                    'Marketing de contenidos',
                    'Publicidad en Google Ads',
                ]
            ],
            'Diseño' => [
                'subcategories' => [null, 'UX/UI', 'Gráfico', 'Web'],
                'services' => [
                    'Diseño de interfaces',
                    'Diseño de marca',
                    'Diseño gráfico',
                    'Diseño web responsive',
                    'Prototipado UX',
                ]
            ],
            'Posicionamiento' => [
                'subcategories' => ['SEO', 'SEM'],
                'services' => [
                    'Auditoría SEO',
                    'Optimización SEO on-page',
                    'Link building',
                    'Campañas SEM',
                    'Análisis de palabras clave',
                ]
            ],
        ];

        $category = $this->faker->randomElement(array_keys($categories));
        $categoryData = $categories[$category];

        return [
            'name' => $this->faker->randomElement($categoryData['services']),
            'category' => $category,
            'subcategory' => $this->faker->randomElement($categoryData['subcategories']),
            'description' => $this->faker->paragraph(3),
            'is_active' => $this->faker->boolean(90), // 90% activos
        ];
    }
}
