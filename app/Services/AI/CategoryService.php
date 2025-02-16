<?php

namespace App\Services\AI;

use Exception;

class CategoryService
{
    private array $categories;

    public function __construct()
    {
        $this->initializeCategories();
    }

    private function initializeCategories(): void
    {
        $this->categories = [
            'emotii-sentimente' => [
                'name' => 'Emoții și Sentimente',
                'subcategories' => [
                    'declaratii-dragoste' => [
                        'name' => 'Declarații de Dragoste',
                        'subcategories' => [
                            'romantice' => ['name' => 'Romantice', 'subcategories' => []],
                            'pentru-iubit' => ['name' => 'Pentru iubit/iubită', 'subcategories' => []],
                            'pentru-sot' => ['name' => 'Pentru soț/soție', 'subcategories' => []],
                            'pentru-parinti' => ['name' => 'Pentru părinți', 'subcategories' => []],
                            'pentru-copii' => ['name' => 'Pentru copii', 'subcategories' => []],
                            'pentru-prieteni' => ['name' => 'Pentru prieteni', 'subcategories' => []],
                            'aniversari' => ['name' => 'La aniversări', 'subcategories' => []],
                            'impacare' => ['name' => 'De împăcare', 'subcategories' => []]
                        ]
                    ],
                    'mesaje-motivationale' => [
                        'name' => 'Mesaje de Încurajare/Motivaționale',
                        'subcategories' => [
                            'depasire-obstacole' => ['name' => 'Pentru depășirea obstacolelor', 'subcategories' => []],
                            'pentru-succes' => ['name' => 'Pentru succes', 'subcategories' => []],
                            'pentru-studenti' => ['name' => 'Pentru studenți/elevi', 'subcategories' => []],
                            'pentru-sportivi' => ['name' => 'Pentru sportivi', 'subcategories' => []],
                            'pentru-antreprenori' => ['name' => 'Pentru antreprenori', 'subcategories' => []],
                            'pierdere-greutate' => ['name' => 'Pentru pierderea în greutate', 'subcategories' => []],
                            'dezvoltare-personala' => ['name' => 'Pentru dezvoltare personală', 'subcategories' => []],
                            'pentru-dimineata' => ['name' => 'Pentru dimineața', 'subcategories' => []],
                            'pentru-seara' => ['name' => 'Pentru seară', 'subcategories' => []]
                        ]
                    ],
                    'mesaje-multumire' => [
                        'name' => 'Mesaje de Mulțumire',
                        'subcategories' => [
                            'pentru-profesori' => ['name' => 'Pentru profesori', 'subcategories' => []],
                            'pentru-medici' => ['name' => 'Pentru medici', 'subcategories' => []],
                            'pentru-familie' => ['name' => 'Pentru familie', 'subcategories' => []],
                            'pentru-prieteni-multumire' => ['name' => 'Pentru prieteni', 'subcategories' => []],
                            'generale-multumire' => ['name' => 'Generale', 'subcategories' => []]
                        ]
                    ],
                    'mesaje-amuzante' => [
                        'name' => 'Mesaje Amuzante/Hazlii',
                        'subcategories' => [
                            'glume-scurte' => ['name' => 'Glume scurte', 'subcategories' => []],
                            'bancuri' => ['name' => 'Bancuri', 'subcategories' => []],
                            'povesti-amuzante' => ['name' => 'Povești amuzante', 'subcategories' => []],
                            'citate-amuzante' => ['name' => 'Citate amuzante', 'subcategories' => []]
                        ]
                    ]
                ]
            ],
            'spiritualitate-religie' => [
                'name' => 'Spiritualitate și Religie',
                'subcategories' => [
                    'rugaciuni' => [
                        'name' => 'Rugăciuni',
                        'subcategories' => [
                            'rugaciuni-dimineata' => ['name' => 'Rugăciuni de dimineață', 'subcategories' => []],
                            'rugaciuni-seara' => ['name' => 'Rugăciuni de seară', 'subcategories' => []],
                            'rugaciuni-sanatate' => ['name' => 'Rugăciuni pentru sănătate', 'subcategories' => []],
                            'rugaciuni-protectie' => ['name' => 'Rugăciuni pentru protecție', 'subcategories' => []],
                            'rugaciuni-multumire' => ['name' => 'Rugăciuni de mulțumire', 'subcategories' => []],
                            'rugaciuni-diverse' => ['name' => 'Rugăciuni pentru diverse nevoi', 'subcategories' => []],
                            'rugaciuni-scurte' => ['name' => 'Rugăciuni scurte', 'subcategories' => []]
                        ]
                    ],
                    'citate-religioase' => [
                        'name' => 'Citate Religioase/Spirituale',
                        'subcategories' => [
                            'biblie' => ['name' => 'Din Biblie', 'subcategories' => []],
                            'sfinti' => ['name' => 'De la sfinți/părinți ai bisericii', 'subcategories' => []],
                            'alte-traditii' => ['name' => 'Din alte tradiții spirituale', 'subcategories' => []]
                        ]
                    ],
                    'meditatii' => [
                        'name' => 'Meditații Ghidate',
                        'subcategories' => [
                            'meditatie-relaxare' => ['name' => 'Pentru relaxare', 'subcategories' => []],
                            'meditatie-stres' => ['name' => 'Pentru reducerea stresului', 'subcategories' => []],
                            'meditatie-somn' => ['name' => 'Pentru somn', 'subcategories' => []],
                            'meditatie-scurte' => ['name' => 'Scurte', 'subcategories' => []]
                        ]
                    ]
                ]
            ],
            'citate-intelepciune' => [
                'name' => 'Citate și Înțelepciune',
                'subcategories' => [
                    'citate-celebre' => [
                        'name' => 'Citate Celebre',
                        'subcategories' => [
                            'citate-filozofice' => ['name' => 'Filozofice', 'subcategories' => []],
                            'citate-viata' => ['name' => 'Despre viață', 'subcategories' => []],
                            'citate-iubire' => ['name' => 'Despre iubire', 'subcategories' => []],
                            'citate-succes' => ['name' => 'Despre succes', 'subcategories' => []],
                            'citate-fericire' => ['name' => 'Despre fericire', 'subcategories' => []],
                            'autori-romani' => ['name' => 'De la autori români', 'subcategories' => []],
                            'autori-internationali' => ['name' => 'De la autori internaționali', 'subcategories' => []]
                        ]
                    ],
                    'proverbe' => [
                        'name' => 'Proverbe și Zicători',
                        'subcategories' => [
                            'proverbe-romanesti' => ['name' => 'Românești', 'subcategories' => []],
                            'proverbe-internationale' => ['name' => 'Internaționale', 'subcategories' => []]
                        ]
                    ],
                    'aforisme' => [
                        'name' => 'Aforisme',
                        'subcategories' => [
                            'aforisme-scurte' => ['name' => 'Scurte și percutante', 'subcategories' => []]
                        ]
                    ]
                ]
            ],
            'educatie-informare' => [
                'name' => 'Educație și Informare',
                'subcategories' => [
                    'curiozitati' => [
                        'name' => 'Curiozități',
                        'subcategories' => [
                            'curiozitati-stiinta' => ['name' => 'Despre știință', 'subcategories' => []],
                            'curiozitati-istorie' => ['name' => 'Despre istorie', 'subcategories' => []],
                            'curiozitati-geografie' => ['name' => 'Despre geografie', 'subcategories' => []],
                            'curiozitati-animale' => ['name' => 'Despre animale', 'subcategories' => []],
                            'curiozitati-corp' => ['name' => 'Despre corpul uman', 'subcategories' => []],
                            'curiozitati-tehnologie' => ['name' => 'Despre tehnologie', 'subcategories' => []]
                        ]
                    ],
                    'sfaturi-utile' => [
                        'name' => 'Sfaturi Utile',
                        'subcategories' => [
                            'sfaturi-studiu' => ['name' => 'Pentru studiu', 'subcategories' => []],
                            'sfaturi-gatit' => ['name' => 'Pentru gătit', 'subcategories' => []],
                            'sfaturi-organizare' => ['name' => 'Pentru organizare', 'subcategories' => []],
                            'sfaturi-calatorii' => ['name' => 'Pentru călătorii', 'subcategories' => []],
                            'sfaturi-ingrijire' => ['name' => 'Pentru îngrijirea personală', 'subcategories' => []],
                            'sfaturi-economie' => ['name' => 'Pentru economisirea banilor', 'subcategories' => []]
                        ]
                    ],
                    'mini-lectii' => [
                        'name' => 'Mini-Lecții',
                        'subcategories' => [
                            'lectii-gramatica' => ['name' => 'De gramatică', 'subcategories' => []],
                            'lectii-cultura' => ['name' => 'De cultură generală', 'subcategories' => []],
                            'lectii-limbi' => ['name' => 'De limbi străine', 'subcategories' => []]
                        ]
                    ]
                ]
            ],
            'divertisment' => [
                'name' => 'Divertisment',
                'subcategories' => [
                    'povesti-scurte' => [
                        'name' => 'Povești Scurte',
                        'subcategories' => [
                            'povesti-groaza' => ['name' => 'De groază', 'subcategories' => []],
                            'povesti-dragoste' => ['name' => 'De dragoste', 'subcategories' => []],
                            'povesti-sf' => ['name' => 'SF', 'subcategories' => []],
                            'fabule' => ['name' => 'Fabule', 'subcategories' => []],
                            'poezii-scurte' => ['name' => 'Poezii scurte', 'subcategories' => []]
                        ]
                    ]
                ]
            ],
            'ocazii-speciale' => [
                'name' => 'Ocazii Speciale',
                'subcategories' => [
                    'sarbatori' => [
                        'name' => 'Mesaje de Sărbători',
                        'subcategories' => [
                            'mesaje-craciun' => ['name' => 'De Crăciun', 'subcategories' => []],
                            'mesaje-paste' => ['name' => 'De Paște', 'subcategories' => []],
                            'mesaje-anul-nou' => ['name' => 'De Anul Nou', 'subcategories' => []],
                            'mesaje-1-8-martie' => ['name' => 'De 1 Martie/8 Martie', 'subcategories' => []],
                            'mesaje-valentines' => ['name' => 'De Ziua Îndrăgostiților', 'subcategories' => []]
                        ]
                    ],
                    'evenimente' => [
                        'name' => 'Evenimente Speciale',
                        'subcategories' => [
                            'zile-nastere' => ['name' => 'Mesaje de zile de naștere', 'subcategories' => []],
                            'nunta-botez' => ['name' => 'Mesaje de nuntă, botez', 'subcategories' => []]
                        ]
                    ]
                ]
            ],
            'diverse' => [
                'name' => 'Diverse',
                'subcategories' => [
                    'recenzii' => [
                        'name' => 'Recenzii Scurte',
                        'subcategories' => [
                            'recenzii-carti' => ['name' => 'De cărți', 'subcategories' => []],
                            'recenzii-filme' => ['name' => 'De filme', 'subcategories' => []]
                        ]
                    ],
                    'provocari' => [
                        'name' => 'Provocări',
                        'subcategories' => [
                            'provocari-sigure' => ['name' => 'Idei de provocări sigure', 'subcategories' => []]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function getCategories(): array
    {
        return $this->categories ?? [];
    }

    public function getCategoryBySlug(string $slug): ?string
    {
        return $this->findCategoryName($this->categories, $slug);
    }

    private function findCategoryName(array $categories, string $slug): ?string
    {
        foreach ($categories as $currentSlug => $category) {
            if ($currentSlug === $slug && isset($category['name'])) {
                return $category['name'];
            }

            if (isset($category['subcategories'])) {
                $found = $this->findCategoryName($category['subcategories'], $slug);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }

    public function getCategoryFullPath(string $slug): ?string
    {
        return $this->findCategoryPath($this->categories, $slug);
    }

    private function findCategoryPath(array $categories, string $slug, array $path = []): ?string
    {
        foreach ($categories as $currentSlug => $category) {
            $currentPath = [...$path];
            if (isset($category['name'])) {
                $currentPath[] = $category['name'];
            }

            if ($currentSlug === $slug) {
                return implode('/', $currentPath);
            }

            if (isset($category['subcategories'])) {
                $found = $this->findCategoryPath($category['subcategories'], $slug, $currentPath);
                if ($found !== null) {
                    return $found;
                }
            }
        }

        return null;
    }
}
