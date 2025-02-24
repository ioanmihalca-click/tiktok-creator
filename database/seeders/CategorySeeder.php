<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Services\AI\CategoryService;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    protected function createCategoryRecursive($data, $parentId = null)
    {
        $categoryData = [
            'name' => $data['name'],
            'slug' => $this->categoryService->generateUniqueSlug($data['name'], $parentId),
            'description' => $data['description'] ?? null,
        ];

        $category = new Category($categoryData);

        if ($parentId) {
            $parent = Category::findOrFail($parentId);
            $category->appendToNode($parent)->save();
        } else {
            $category->saveAsRoot();
        }

        if (isset($data['subcategories'])) {
            foreach ($data['subcategories'] as $subcategoryData) {
                $this->createCategoryRecursive($subcategoryData, $category->id);
            }
        }

        return $category;
    }

    public function run(): void
    {
        $categories = [
            'emotii-virale' => [
                'name' => 'Emoții Virale',
                'description' => 'Imagine statică cu impact emoțional și narare.',
                'subcategories' => [
                    'declaratii-dragoste' => [
                        'name' => 'Declarații de Dragoste',
                        'description' => 'Imagine romantică (ex. cuplu, apus) cu text și voce emoționantă.',
                        'subcategories' => [
                            'romantice' => ['name' => 'Romantice', 'description' => 'Inimioare, flori, cer roz.'],
                            'impacare' => ['name' => 'De Împăcare', 'description' => 'Mâini care se ating, ton calm.']
                        ]
                    ],
                    'mesaje-motivationale' => [
                        'name' => 'Mesaje Motivaționale',
                        'description' => 'Imagine inspirațională (ex. munte, soare) cu citat narat.',
                        'subcategories' => [
                            'succes' => ['name' => 'Pentru Succes', 'description' => 'Trofee, drumuri lungi, ton energic.'],
                            'dimineata' => ['name' => 'Pentru Dimineață', 'description' => 'Cafea, răsărit, voce caldă.']
                        ]
                    ],
                    'mesaje-amuzante' => [
                        'name' => 'Mesaje Amuzante',
                        'description' => 'Imagine haioasă (ex. animale, meme) cu punchline narat.',
                        'subcategories' => [
                            'meme-uri-romanesti' => ['name' => 'Meme-uri Românești', 'description' => 'Dacia, sarmale, umor local.'],
                            'glume-scurte' => ['name' => 'Glume Scurte', 'description' => 'Obiecte ciudate, text catchy.']
                        ]
                    ]
                ]
            ],

            'divertisment-viral' => [
                'name' => 'Divertisment Viral',
                'description' => 'Imagine statică care distrează instant.',
                'subcategories' => [
                    'nostalgie' => [
                        'name' => 'Nostalgie',
                        'description' => 'Imagine retro (ex. casete, jocuri vechi) cu voce nostalgică.',
                        'subcategories' => [
                            'anii-2000' => ['name' => 'Anii 2000', 'description' => 'Tamagotchi, Nokia, vibe old-school.']
                        ]
                    ],
                    'animale-amuzante' => [
                        'name' => 'Animale Amuzante',
                        'description' => 'Imagine cu animale în ipostaze hilare.',
                        'subcategories' => [
                            'catei' => ['name' => 'Căței Haioși', 'description' => 'Cățel cu ochelari, narare amuzantă.'],
                            'pisici' => ['name' => 'Pisici Ciudate', 'description' => 'Pisică pe tastatură, ton ironic.']
                        ]
                    ]
                ]
            ],

            'educatie-rapida' => [
                'name' => 'Educație Rapidă',
                'description' => 'Imagine informativă cu narare surprinzătoare.',
                'subcategories' => [
                    'curiozitati' => [
                        'name' => 'Curiozități',
                        'description' => 'Imagine wow (ex. cosmos, animale rare) cu fapt narat.',
                        'subcategories' => [
                            'stiinta' => ['name' => 'Știință', 'description' => 'Galaxii, experimente, ton captivant.'],
                            'stiai-ca' => ['name' => 'Știai Că...?', 'description' => 'Obiecte neobișnuite, voce intrigantă.']
                        ]
                    ],
                    'sfaturi-utile' => [
                        'name' => 'Sfaturi Utile',
                        'description' => 'Imagine practică (ex. ustensile, portofel) cu sfat narat.',
                        'subcategories' => [
                            'bucatarie' => ['name' => 'Bucătărie', 'description' => 'Mâncare, truc simplu, ton prietenos.'],
                            'economisire' => ['name' => 'Economisire', 'description' => 'Bani, diagramă, voce clară.']
                        ]
                    ]
                ]
            ],

            'lifehacks-virale' => [
                'name' => 'Life Hacks Virale',
                'description' => 'Imagine cu obiecte zilnice și truc narat.',
                'subcategories' => [
                    '5-second-hacks' => ['name' => '5 Second Hacks', 'description' => 'Obiecte comune (ex. agrafă) cu idee genială.'],
                    'tech-hacks' => ['name' => 'Tech Hacks', 'description' => 'Telefon, laptop, sfat rapid.']
                ]
            ],

            'trenduri-statice' => [
                'name' => 'Trenduri Statice',
                'description' => 'Imagine adaptată la trenduri TikTok, fără mișcare.',
                'subcategories' => [
                    'quiz' => ['name' => 'Quiz Interactiv', 'description' => 'Imagine cu întrebare (ex. „Ce e asta?”) și narare.'],
                    'estetica' => ['name' => 'Estetică Virală', 'description' => 'Cafea, flori, ton relaxant.']
                ]
            ],

            'ocazii-speciale' => [
                'name' => 'Ocazii Speciale',
                'description' => 'Imagine festivă cu mesaj narat.',
                'subcategories' => [
                    'sarbatori' => [
                        'name' => 'Sărbători',
                        'description' => 'Decor tematic (ex. brad, ouă) cu urare.',
                        'subcategories' => [
                            'craciun' => ['name' => 'Crăciun', 'description' => 'Lumințe, cadouri, voce caldă.'],
                            'anul-nou' => ['name' => 'Anul Nou', 'description' => 'Artificii, şampanie, ton entuziast.']
                        ]
                    ],
                    'zile-nastere' => ['name' => 'Zile de Naștere', 'description' => 'Tort, baloane, urare rapidă.']
                ]
            ],

            'drama-gossip' => [
                'name' => 'Drama & Gossip',
                'description' => 'Imagine intrigantă cu poveste narată.',
                'subcategories' => [
                    'povesti-scandal' => ['name' => 'Povești de Scandal', 'description' => 'Scenă dramatică (ex. ceartă) cu twist.'],
                    'secrete' => ['name' => 'Secrete Dezvăluite', 'description' => 'Obiect misterios, ton captivant.']
                ]
            ],

            'spiritualitate-virala' => [
                'name' => 'Spiritualitate Virală',
                'description' => 'Imagine calmantă cu mesaj profund.',
                'subcategories' => [
                    'rugaciuni-scurte' => ['name' => 'Rugăciuni Scurte', 'description' => 'Apus, lumânări, voce liniștită.'],
                    'citate-spirituale' => ['name' => 'Citate Spirituale', 'description' => 'Cer, natură, ton inspirațional.']
                ]
            ],

            'fitness-rapid' => [
                'name' => 'Fitness Rapid',
                'description' => 'Imagine cu tematică sănătoasă și sfat narat.',
                'subcategories' => [
                    'slabit' => ['name' => 'Trucuri de Slăbit', 'description' => 'Măr pe cântar, ton motivant.'],
                    'exercitii' => ['name' => 'Exerciții Simple', 'description' => 'Gantere, poziție yoga, voce clară.']
                ]
            ],

            'diy-simplu' => [
                'name' => 'DIY Simplu',
                'description' => 'Imagine cu materiale sau rezultat și instrucțiuni narate.',
                'subcategories' => [
                    'decor' => ['name' => 'Decor cu 5 Lei', 'description' => 'Floare din hârtie, ton creativ.'],
                    'organizare' => ['name' => 'Hacks de Organizare', 'description' => 'Cutie decorată, voce practică.']
                ]
            ],

            'romania-virala' => [
                'name' => 'România Virală',
                'description' => 'Imagine locală cu umor sau nostalgie narată.',
                'subcategories' => [
                    'viata-sat' => ['name' => 'Viața la Sat', 'description' => 'Căruță, fân, ton amuzant.'],
                    'bucuresti' => ['name' => 'Glume despre București', 'description' => 'Trafic, metrou, voce ironică.']
                ]
            ],

            'estetica-lifestyle' => [
                'name' => 'Estetică & Lifestyle',
                'description' => 'Imagine elegantă cu poveste narată.',
                'subcategories' => [
                    'rutina' => ['name' => 'Rutina Mea', 'description' => 'Mic dejun, flori, ton relaxant.'],
                    'stil' => ['name' => 'Hacks de Stil', 'description' => 'Eșarfă cool, voce inspirațională.']
                ]
            ]
        ];

        foreach ($categories as $categoryData) {
            $this->createCategoryRecursive($categoryData);
        }
    }
}