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
            'emotii-sentimente' => [
                'name' => 'Emoții și Sentimente',
                'description' => 'Exprimă emoții și sentimente prin videoclipuri.',
                'subcategories' => [
                    'declaratii-dragoste' => [
                        'name' => 'Declarații de Dragoste',
                        'description' => 'Declarații de dragoste pentru diverse ocazii și persoane.',
                        'subcategories' => [
                            'romantice' => ['name' => 'Romantice', 'description' => 'Declarații romantice.'],
                            'pentru-iubit' => ['name' => 'Pentru iubit/iubită', 'description' => 'Declarații pentru iubit/iubită.'],
                            'pentru-sot' => ['name' => 'Pentru soț/soție', 'description' => 'Declarații pentru soț/soție.'],
                            'pentru-parinti' => ['name' => 'Pentru părinți', 'description' => 'Declarații pentru părinți.'],
                            'pentru-copii' => ['name' => 'Pentru copii', 'description' => 'Declarații pentru copii.'],
                            'pentru-prieteni' => ['name' => 'Pentru prieteni', 'description' => 'Declarații pentru prieteni.'],
                            'aniversari' => ['name' => 'La aniversări', 'description' => 'Declarații pentru aniversări.'],
                            'impacare' => ['name' => 'De împăcare', 'description' => 'Declarații pentru împăcare.']
                        ]
                    ],
                    'mesaje-motivationale' => [
                        'name' => 'Mesaje Motivaționale',
                        'description' => 'Mesaje pentru a motiva și încuraja.',
                        'subcategories' => [
                            'depasire-obstacole' => ['name' => 'Pentru depășirea obstacolelor', 'description' => 'Mesaje pentru a depăși obstacolele.'],
                            'pentru-succes' => ['name' => 'Pentru succes', 'description' => 'Mesaje pentru a obține succesul.'],
                            'pentru-studenti' => ['name' => 'Pentru studenți/elevi', 'description' => 'Mesaje pentru studenți/elevi.'],
                            'pentru-sportivi' => ['name' => 'Pentru sportivi', 'description' => 'Mesaje pentru sportivi.'],
                            'pentru-antreprenori' => ['name' => 'Pentru antreprenori', 'description' => 'Mesaje pentru antreprenori.'],
                            'pierdere-greutate' => ['name' => 'Pentru pierderea în greutate', 'description' => 'Mesaje pentru a pierde în greutate.'],
                            'dezvoltare-personala' => ['name' => 'Pentru dezvoltare personală', 'description' => 'Mesaje pentru dezvoltare personală.'],
                            'pentru-dimineata' => ['name' => 'Pentru dimineața', 'description' => 'Mesaje pentru începutul zilei.'],
                            'pentru-seara' => ['name' => 'Pentru seară', 'description' => 'Mesaje pentru sfârșitul zilei.']
                        ]
                    ],
                    'mesaje-multumire' => [
                        'name' => 'Mesaje de Mulțumire',
                        'description' => 'Mesaje pentru a exprima recunoștința.',
                        'subcategories' => [
                            'pentru-profesori' => ['name' => 'Pentru profesori', 'description' => 'Mesaje de mulțumire pentru profesori.'],
                            'pentru-medici' => ['name' => 'Pentru medici', 'description' => 'Mesaje de mulțumire pentru medici.'],
                            'pentru-familie' => ['name' => 'Pentru familie', 'description' => 'Mesaje de mulțumire pentru familie.'],
                            'pentru-prieteni-multumire' => ['name' => 'Pentru prieteni', 'description' => 'Mesaje de mulțumire pentru prieteni.'],
                            'generale-multumire' => ['name' => 'Generale', 'description' => 'Mesaje de mulțumire generale.']
                        ]
                    ],
                    // 'mesaje-amuzante' => [
                    //     'name' => 'Mesaje Amuzante/Hazlii',
                    //     'description' => 'Mesaje pentru a aduce zâmbetul pe buze.',
                    //     'subcategories' => [
                    //         'glume-scurte' => ['name' => 'Glume scurte', 'description' => 'Glume scurte și amuzante.'],
                    //         'bancuri' => ['name' => 'Bancuri', 'description' => 'Bancuri.'],
                    //         'povesti-amuzante' => ['name' => 'Povești amuzante', 'description' => 'Povești amuzante.'],
                    //         'citate-amuzante' => ['name' => 'Citate amuzante', 'description' => 'Citate amuzante.'],
                    //     ]
                    // ]
                ]
            ],
            'spiritualitate-religie' => [
                'name' => 'Spiritualitate și Religie',
                'description' => 'Conținut legat de spiritualitate și religie.',
                'subcategories' => [
                    'rugaciuni' => [
                        'name' => 'Rugăciuni',
                        'description' => 'Rugăciuni pentru diverse nevoi.',
                        'subcategories' => [
                            'rugaciuni-dimineata' => ['name' => 'Rugăciuni de dimineață', 'description' => 'Rugăciuni pentru începutul zilei.'],
                            'rugaciuni-seara' => ['name' => 'Rugăciuni de seară', 'description' => 'Rugăciuni pentru sfârșitul zilei.'],
                            'rugaciuni-sanatate' => ['name' => 'Rugăciuni pentru sănătate', 'description' => 'Rugăciuni pentru sănătate.'],
                            'rugaciuni-protectie' => ['name' => 'Rugăciuni pentru protecție', 'description' => 'Rugăciuni pentru protecție.'],
                            'rugaciuni-multumire' => ['name' => 'Rugăciuni de mulțumire', 'description' => 'Rugăciuni de mulțumire.'],
                            'rugaciuni-diverse' => ['name' => 'Rugăciuni pentru diverse nevoi', 'description' => 'Rugăciuni pentru diverse nevoi.'],
                            'rugaciuni-scurte' => ['name' => 'Rugăciuni scurte', 'description' => 'Rugăciuni scurte.']
                        ]
                    ],
                    'citate-religioase' => [
                        'name' => 'Citate Religioase/Spirituale',
                        'description' => 'Citate din surse religioase și spirituale.',
                        'subcategories' => [
                            'biblie' => ['name' => 'Din Biblie', 'description' => 'Citate din Biblie.'],
                            'sfinti' => ['name' => 'De la sfinți/părinți ai bisericii', 'description' => 'Citate de la sfinți/părinți ai bisericii.'],
                            'alte-traditii' => ['name' => 'Din alte tradiții spirituale', 'description' => 'Citate din alte tradiții spirituale.']
                        ]
                    ],
                ]
            ],

            'gaming-tehnologie' => [
                'name' => 'Gaming & Tehnologie',
                'description' => 'Conținut despre jocuri video și tehnologie populară.',
                'subcategories' => [
                    'gaming-tips' => [
                        'name' => 'Gaming Tips & Tricks',
                        'description' => 'Sfaturi și trucuri pentru jocuri populare.',
                        'subcategories' => [
                            'minecraft-tips' => ['name' => 'Minecraft Tips', 'description' => 'Sfaturi pentru Minecraft.'],
                            'fortnite-tips' => ['name' => 'Fortnite Tips', 'description' => 'Sfaturi pentru Fortnite.'],
                            'roblox-tips' => ['name' => 'Roblox Tips', 'description' => 'Sfaturi pentru Roblox.'],
                            'mobile-games-tips' => ['name' => 'Mobile Games', 'description' => 'Sfaturi pentru jocuri mobile populare.'],

                        ]
                    ],
                    'tech-hacks-avansate' => [
                        'name' => 'Tech Hacks Avansate',
                        'description' => 'Trucuri avansate pentru dispozitive tehnologice.',
                        'subcategories' => [
                            'smartphone-hacks' => ['name' => 'Smartphone Hacks', 'description' => 'Trucuri pentru smartphone.'],
                            'aplicatii-utile' => ['name' => 'Aplicații Utile', 'description' => 'Prezentări scurte ale unor aplicații utile.'],
                            'setari-rapide' => ['name' => 'Setări Rapide', 'description' => 'Configurări rapide pentru diverse dispozitive.'],
                            'securitate-online' => ['name' => 'Securitate Online', 'description' => 'Sfaturi pentru siguranța online.']
                        ]
                    ],
                    'tech-reviews' => [
                        'name' => 'Tech Mini-Reviews',
                        'description' => 'Recenzii scurte de produse tehnologice.',
                        'subcategories' => [
                            'gadgets-mici' => ['name' => 'Gadget-uri Mici', 'description' => 'Recenzii pentru gadget-uri accesibile.'],
                            'accesorii-tech' => ['name' => 'Accesorii Tech', 'description' => 'Recenzii pentru accesorii tehnologice.'],
                        ]
                    ],
                    'ai-tools' => [
                        'name' => 'AI & Tools',
                        'description' => 'Prezentare instrumente și aplicații AI populare.',
                        'subcategories' => [
                            'ai-art' => [
                                'name' => 'AI Art & Design',
                                'description' => 'Instrumente de generare artă cu AI.',
                                'subcategories' => [
                                    'text-to-image' => ['name' => 'Text-to-Image', 'description' => 'Generare de imagini din descrieri text.'],
                                    'image-editing' => ['name' => 'Editare Imagini AI', 'description' => 'Retușare și transformare de imagini folosind AI.'],
                                    'ai-animation' => ['name' => 'Animație AI', 'description' => 'Crearea animațiilor și efectelor vizuale cu AI.']
                                ]
                            ],
                            'ai-scris' => [
                                'name' => 'AI pentru Scris',
                                'description' => 'Instrumente de scriere și editare cu AI.',
                                'subcategories' => [
                                    'asistenti-scriere' => ['name' => 'Asistenți de Scriere', 'description' => 'AI pentru crearea și editarea textelor.'],
                                    'copywriting' => ['name' => 'Copywriting AI', 'description' => 'Generare text marketing și reclame.'],
                                    'corectare-text' => ['name' => 'Corectare Text', 'description' => 'Îmbunătățirea gramaticii și stilului.']
                                ]
                            ],
                            'ai-audio-video' => [
                                'name' => 'AI Audio & Video',
                                'description' => 'Generare și editare audio/video cu AI.',
                                'subcategories' => [
                                    'text-to-speech' => ['name' => 'Text-to-Speech', 'description' => 'Convertirea textului în voce naturală.'],
                                    'muzica-ai' => ['name' => 'Muzică generată', 'description' => 'Generare melodii și sunete cu AI.'],
                                    'video-editing' => ['name' => 'Editare Video AI', 'description' => 'Automatizări și efecte video cu AI.']
                                ]
                            ],
                            'ai-productivitate' => [
                                'name' => 'AI pentru Productivitate',
                                'description' => 'AI pentru eficientizarea muncii.',
                                'subcategories' => [
                                    'asistenti-virtuali' => ['name' => 'Asistenți Virtuali', 'description' => 'Chatboți și asistenți AI pentru diverse sarcini.'],
                                    'automatizare' => ['name' => 'Automatizare Workflow', 'description' => 'Automatizarea proceselor repetitive cu AI.'],
                                    'analiza-date' => ['name' => 'Analiză Date', 'description' => 'Procesare și vizualizare date cu AI.']
                                ]
                            ],
                            'tendinte-ai' => [
                                'name' => 'Tendințe în AI',
                                'description' => 'Noutăți și inovații în domeniul AI.',
                                'subcategories' => [
                                    'noi-tehnologii' => ['name' => 'Noi Tehnologii', 'description' => 'Cele mai recente inovații în AI.'],
                                    'impactul-ai' => ['name' => 'Impactul AI', 'description' => 'Cum schimbă AI diverse domenii.'],
                                    'viitorul-ai' => ['name' => 'Viitorul AI', 'description' => 'Predicții despre evoluția AI.']
                                ]
                            ]
                        ]
                    ]
                ]
            ],

            'citate-intelepciune' => [
                'name' => 'Citate și Înțelepciune',
                'description' => 'Citate celebre, proverbe și aforisme.',
                'subcategories' => [
                    'citate-celebre' => [
                        'name' => 'Citate Celebre',
                        'description' => 'Citate celebre de la personalități marcante.',
                        'subcategories' => [
                            'citate-filozofice' => ['name' => 'Filozofice', 'description' => 'Citate filozofice.'],
                            'citate-viata' => ['name' => 'Despre viață', 'description' => 'Citate despre viață.'],
                            'citate-iubire' => ['name' => 'Despre iubire', 'description' => 'Citate despre iubire.'],
                            'citate-succes' => ['name' => 'Despre succes', 'description' => 'Citate despre succes.'],
                            'citate-fericire' => ['name' => 'Despre fericire', 'description' => 'Citate despre fericire.'],
                            'autori-romani' => ['name' => 'De la autori români', 'description' => 'Citate de la autori români.'],
                            'autori-internationali' => ['name' => 'De la autori internaționali', 'description' => 'Citate de la autori internaționali.']
                        ]
                    ],
                    'proverbe' => [
                        'name' => 'Proverbe și Zicători',
                        'description' => 'Proverbe și zicători populare.',
                        'subcategories' => [
                            'proverbe-romanesti' => ['name' => 'Românești', 'description' => 'Proverbe românești.'],
                            'proverbe-internationale' => ['name' => 'Internaționale', 'description' => 'Proverbe internaționale.']
                        ]
                    ],

                ]
            ],


            'divertisment' => [
                'name' => 'Divertisment',
                'description' => 'Conținut pentru divertisment și relaxare.',
                'subcategories' => [
                    'povesti-scurte' => [
                        'name' => 'Povești Scurte',
                        'description' => 'Povești scurte de diverse genuri.',
                        'subcategories' => [
                            'povesti-groaza' => ['name' => 'De groază', 'description' => 'Povești de groază.'],
                            'povesti-dragoste' => ['name' => 'De dragoste', 'description' => 'Povești de dragoste.'],
                            'povesti-sf' => ['name' => 'SF', 'description' => 'Povești științifico-fantastice.'],
                            'fabule' => ['name' => 'Fabule', 'description' => 'Fabule cu morală.'],
                        ]
                    ],

                    'poezii' => [
                        'name' => 'Poezii',
                        'description' => 'Poezii scurte și lungi.',
                        'subcategories' => [
                            'poezii-scurte' => ['name' => 'Poezii scurte', 'description' => 'Poezii scurte.'],
                        ]
                    ]
                ]
            ],

            'educatie-informare' => [
                'name' => 'Educație și Informare',
                'description' => 'Prezintă informații educative și utile.',
                'subcategories' => [
                    'curiozitati' => [
                        'name' => 'Curiozități',
                        'description' => 'Prezintă informații interesante și neobișnuite.',
                        'subcategories' => [
                            'curiozitati-stiinta' => ['name' => 'Despre știință', 'description' => 'Curiozități despre știință.'],
                            'curiozitati-istorie' => ['name' => 'Despre istorie', 'description' => 'Curiozități despre istorie.'],
                            'curiozitati-geografie' => ['name' => 'Despre geografie', 'description' => 'Curiozități despre geografie.'],
                            'curiozitati-animale' => ['name' => 'Despre animale', 'description' => 'Curiozități despre animale.'],
                            'curiozitati-corp' => ['name' => 'Despre corpul uman', 'description' => 'Curiozități despre corpul uman.'],
                            'curiozitati-tehnologie' => ['name' => 'Despre tehnologie', 'description' => 'Curiozități despre tehnologie.']
                        ]
                    ],
                    'sfaturi-utile' => [
                        'name' => 'Sfaturi Utile',
                        'description' => 'Oferă sfaturi practice în diverse domenii.',
                        'subcategories' => [
                            'sfaturi-studiu' => ['name' => 'Pentru studiu', 'description' => 'Sfaturi pentru a învăța mai eficient.'],
                            'sfaturi-gatit' => ['name' => 'Pentru gătit', 'description' => 'Sfaturi pentru gătit.'],
                            'sfaturi-organizare' => ['name' => 'Pentru organizare', 'description' => 'Sfaturi pentru organizare.'],
                            'sfaturi-calatorii' => ['name' => 'Pentru călătorii', 'description' => 'Sfaturi pentru călătorii.'],
                            'sfaturi-ingrijire' => ['name' => 'Pentru îngrijirea personală', 'description' => 'Sfaturi pentru îngrijirea personală.'],
                            'sfaturi-economie' => ['name' => 'Pentru economisirea banilor', 'description' => 'Sfaturi pentru a economisi bani.']
                        ]
                    ],
                    'mini-lectii' => [
                        'name' => 'Mini-Lecții',
                        'description' => 'Prezintă lecții scurte pe diverse teme.',
                        'subcategories' => [
                            'lectii-gramatica' => ['name' => 'De gramatică', 'description' => 'Mini-lecții de gramatică.'],
                            'lectii-cultura' => ['name' => 'De cultură generală', 'description' => 'Mini-lecții de cultură generală.'],
                            'lectii-istorie-românia' => ['name' => 'Istoria României', 'description' => 'Mini-lecții de istoria României.'],
                            'lectii-geografie' => ['name' => 'De geografie', 'description' => 'Mini-lecții de geografie.'],

                        ]
                    ],

                    'meditatii' => [
                        'name' => 'Meditații Ghidate',
                        'description' => 'Oferă meditații ghidate pentru relaxare și bunăstare.',
                        'subcategories' => [
                            'meditatie-relaxare' => ['name' => 'Pentru relaxare', 'description' => 'Meditații pentru relaxare.'],
                            'meditatie-stres' => ['name' => 'Pentru reducerea stresului', 'description' => 'Meditații pentru reducerea stresului.'],
                            'meditatie-somn' => ['name' => 'Pentru somn', 'description' => 'Meditații pentru un somn mai bun.'],
                            'meditatie-scurte' => ['name' => 'Scurte', 'description' => 'Meditații scurte.'],
                        ]
                    ],

                ]
            ],

            'ocazii-speciale' => [
                'name' => 'Ocazii Speciale',
                'description' => 'Mesaje pentru ocazii speciale.',
                'subcategories' => [
                    'sarbatori' => [
                        'name' => 'Mesaje de Sărbători',
                        'description' => 'Mesaje pentru diverse sărbători.',
                        'subcategories' => [
                            'mesaje-craciun' => ['name' => 'De Crăciun', 'description' => 'Mesaje de Crăciun.'],
                            'mesaje-paste' => ['name' => 'De Paște', 'description' => 'Mesaje de Paște.'],
                            'mesaje-anul-nou' => ['name' => 'De Anul Nou', 'description' => 'Mesaje de Anul Nou.'],
                            'mesaje-1-8-martie' => ['name' => 'De 1 Martie/8 Martie', 'description' => 'Mesaje de 1 Martie/8 Martie.'],
                            'mesaje-valentines' => ['name' => 'De Ziua Îndrăgostiților', 'description' => 'Mesaje de Ziua Îndrăgostiților.']
                        ]
                    ],
                    'evenimente' => [
                        'name' => 'Evenimente Speciale',
                        'description' => 'Mesaje pentru evenimente speciale.',
                        'subcategories' => [
                            'zile-nastere' => ['name' => 'Mesaje de zile de naștere', 'description' => 'Mesaje de zile de naștere.'],
                            'nunta-botez' => ['name' => 'Mesaje de nuntă, botez', 'description' => 'Mesaje de nuntă sau botez.']
                        ]
                    ]
                ]
            ],

            'lifehacks-virale' => [
                'name' => 'Life Hacks Virale',
                'description' => 'Prezintă trucuri și sfaturi utile pentru viața de zi cu zi.',
                'subcategories' => [
                    '5-second-hacks' => ['name' => '5 Second Hacks', 'description' => 'Obiecte comune (ex. agrafă) cu idee genială.'],
                    'tech-hacks' => ['name' => 'Tech Hacks', 'description' => 'Telefon, laptop, sfat rapid.']
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

            'liste' => [
                'name' => 'Liste (Top-uri)',
                'description' => 'Creează videoclipuri cu liste (Top 5, Top 10, etc.).',
                'subcategories' => [
                    'top-5-filme' => ['name' => 'Top 5 Filme', 'description' => 'Top 5 filme preferate.'],
                    'top-10-carti' => ['name' => 'Top 10 Cărți', 'description' => 'Top 10 cărți preferate.'],
                    'top-3-destinatii' => ['name' => 'Top 3 Destinații', 'description' => 'Top 3 destinații de călătorie preferate.'],


                ],
            ],


        ];

        foreach ($categories as $categoryData) {
            $this->createCategoryRecursive($categoryData);
        }
    }
}
