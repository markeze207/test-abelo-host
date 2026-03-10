<?php
namespace App\Seeders;

use App\Core\QueryBuilder;
use App\Builders\CategoryBuilder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $this->command("Начинаю заполнение категорий...");

        // Очищаем таблицу
        $this->truncate('categories');

        $categories = [
            [
                'name' => 'Новости',
                'description' => 'Свежие новости из мира технологий и разработки'
            ],
            [
                'name' => 'PHP',
                'description' => 'Всё о PHP: новые версии, лучшие практики, советы'
            ],
            [
                'name' => 'JavaScript',
                'description' => 'Современный JavaScript, фреймворки и библиотеки'
            ],
            [
                'name' => 'Базы данных',
                'description' => 'MySQL, PostgreSQL, MongoDB и другие СУБД'
            ],
            [
                'name' => 'Инструменты',
                'description' => 'Полезные инструменты для разработчика'
            ],
            [
                'name' => 'Карьера',
                'description' => 'Советы по карьере в IT, собеседования, резюме'
            ],

            [
                'name' => 'Python',
                'description' => 'Разработка на Python: веб, анализ данных, автоматизация'
            ],
            [
                'name' => 'Frontend',
                'description' => 'HTML, CSS, адаптивная верстка, UI/UX'
            ],
            [
                'name' => 'DevOps',
                'description' => 'CI/CD, контейнеризация, оркестрация, мониторинг'
            ],
            [
                'name' => 'Алгоритмы и структуры данных',
                'description' => 'Подготовка к собеседованиям, оптимизация кода'
            ],
            [
                'name' => 'Go',
                'description' => 'Язык программирования Go: особенности, применение'
            ],
            [
                'name' => 'Мобильная разработка',
                'description' => 'iOS, Android, кроссплатформенные решения'
            ],
            [
                'name' => 'Безопасность',
                'description' => 'Информационная безопасность, шифрование, защита данных'
            ],
            [
                'name' => 'Искусственный интеллект',
                'description' => 'Машинное обучение, нейросети, AI в разработке'
            ],
            [
                'name' => 'Книги и ресурсы',
                'description' => 'Рекомендации книг, курсов, полезных ресурсов для разработчиков'
            ],
            [
                'name' => 'Архив',
                'description' => 'Старые, но полезные статьи'
            ]
        ];

        $queryBuilder = new QueryBuilder('categories');
        $inserted = 0;

        foreach ($categories as $categoryData) {
            try {
                $builder = new CategoryBuilder();

                $data = $builder
                    ->withName($categoryData['name'])
                    ->withDescription($categoryData['description'])
                    ->withTimestamps()
                    ->build();

                // Убираем лишние поля которых нет в таблице
                unset($data['meta_title']);
                unset($data['meta_description']);
                unset($data['updated_at']);

                $id = $queryBuilder->insert($data);

                if ($id) {
                    $inserted++;
                    $this->command("  + Категория '{$data['name']}' создана (ID: $id, slug: {$data['slug']})");
                }

            } catch (\Exception $e) {
                $this->command("  ! Ошибка при создании категории '{$categoryData['name']}': " . $e->getMessage());
            }
        }

        $this->command("✓ Заполнение категорий завершено. Добавлено: $inserted");
    }
}