<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Services\AI\CategoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
                        'name' => 'Mesaje de Încurajare/Motivaționale',
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
                            'pentru-seara' => ['name' => 'Pentru seară', 'description' => 'Mesaje pentru sfârșitul zilei.'],
                            'citate-zilnice' => [
                                'name' => 'Citate Zilnice',
                                'description' => 'Text inspirațional pe fundal abstract cu narare emoțională.'
                            ],
                            'lectii-carti' => [
                                'name' => 'Lecții din Cărți Best-Seller',
                                'description' => 'Imagini cu coperta cărții + texte cheie.'
                            ],
                            'cum-sa' => [
                                'name' => 'Cum Să...',
                                'description' => 'Ghiduri vizuale cu pași simpli pe fundal minimalist.'
                            ]
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
                    'mesaje-amuzante' => [
                        'name' => 'Mesaje Amuzante/Hazlii',
                        'description' => 'Mesaje pentru a aduce zâmbetul pe buze.',
                        'subcategories' => [
                            'glume-scurte' => ['name' => 'Glume scurte', 'description' => 'Glume scurte și amuzante.'],
                            'bancuri' => ['name' => 'Bancuri', 'description' => 'Bancuri.'],
                            'povesti-amuzante' => ['name' => 'Povești amuzante', 'description' => 'Povești amuzante.'],
                            'citate-amuzante' => ['name' => 'Citate amuzante', 'description' => 'Citate amuzante.'],
                            'meme-uri-romanesti' => ['name' => 'Meme-uri Românești', 'description' => 'Meme-uri românești.']
                        ]
                    ]
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
                    'aforisme' => [
                        'name' => 'Aforisme',
                        'description' => 'Aforisme scurte și percutante.',
                        'subcategories' => [
                            'aforisme-scurte' => ['name' => 'Scurte și percutante', 'description' => 'Aforisme scurte.']
                        ]
                    ]
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
                            'microfictiune' => ['name' => 'Microficțiune', 'description' => 'Povești foarte scurte.'],
                            'personale' => ['name' => 'Experiențe Personale', 'description' => 'Povești personale.']
                        ]
                    ],
                    'glume' => [
                        'name' => 'Glume și Umor',
                        'description' => 'Conținut amuzant și distractiv.',
                        'subcategories' => [
                            'glume-scurte' => ['name' => 'Glume scurte', 'description' => 'Glume scurte și amuzante.'],
                            'bancuri' => ['name' => 'Bancuri', 'description' => 'Bancuri.'],
                            'povesti-amuzante' => ['name' => 'Povești amuzante', 'description' => 'Povești amuzante.'],
                            'citate-amuzante' => ['name' => 'Citate amuzante', 'description' => 'Citate amuzante.'],
                            'meme-uri-romanesti' => ['name' => 'Meme-uri Românești', 'description' => 'Meme-uri românești.']
                        ]
                    ],
                    'poezii' => [
                        'name' => 'Poezii',
                        'description' => 'Poezii scurte și lungi.',
                        'subcategories' => [
                            'poezii-scurte' => ['name' => 'Poezii scurte', 'description' => 'Poezii scurte.'],
                            'poezii-lungi' => ['name' => 'Poezii lungi', 'description' => 'Poezii lungi.']
                        ]
                    ],
                    'storytelling-vizual' => [
                        'name' => 'Storytelling Vizual',
                        'subcategories' => [
                            'poveste-3-frame' => [
                                'name' => 'Povestea Mea în 3 Frame-uri',
                                'description' => 'Narațiune continuă cu imagini simbolice.'
                            ],
                            'viata-ca' => [
                                'name' => 'Viața ca...',
                                'description' => 'Metafore vizuale amuzante (ex: "un film horror").'
                            ],
                            'invata-din' => [
                                'name' => 'Ce Am Învățat din...',
                                'description' => 'Reflecții cu imagini abstracte.'
                            ]
                        ]
                    ],
                    'cultura-pop' => [
                        'name' => 'Cultură Pop & Nostalgie',
                        'subcategories' => [
                            'anii-2000' => [
                                'name' => 'Anii 2000 vs. Acum',
                                'description' => 'Comparații vizuale cu text nostalgic.'
                            ],
                            'filme-iconice' => [
                                'name' => 'Filme Iconice în 1 Frame',
                                'description' => 'Captură memorabilă + replică cultă.'
                            ]
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
                            'curiozitati-tehnologie' => ['name' => 'Despre tehnologie', 'description' => 'Curiozități despre tehnologie.'],
                            'stiai-ca' => [
                                'name' => 'Știai Că...?',
                                'description' => 'Date șocante cu imagini ilustrative.'
                            ],
                            'secrete' => [
                                'name' => 'Secrete despre...',
                                'description' => 'Revelații pe teme specifice (ex: spațiu).'
                            ],
                            'myth-fact' => [
                                'name' => 'Myth vs. Fact',
                                'description' => 'Comparații vizuale cu split-screen.'
                            ]
                        ]
                    ],
                    'educatie-vizuala' => [
                        'name' => 'Educație Vizuală',
                        'subcategories' => [
                            'istorie-rapida' => [
                                'name' => 'Istorie în 20 de Secunde',
                                'description' => 'Timeline vizual cu narare rapidă.'
                            ],
                            'functionare' => [
                                'name' => 'Cum Funcționează...',
                                'description' => 'Infografice animate (ex: fulgere).'
                            ],
                            'limbi-straine' => [
                                'name' => 'Fraze Cheie în Limbi Străine',
                                'description' => 'Text bilingv pe fundal cultural.'
                            ]
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
                            'lectii-limbi' => ['name' => 'De limbi străine', 'description' => 'Mini-lecții de limbi străine.']
                        ]
                    ],
                    'meditatii' => [
                        'name' => 'Meditații Ghidate',
                        'description' => 'Oferă meditații ghidate pentru relaxare și bunăstare.',
                        'subcategories' => [
                            'meditatie-relaxare' => ['name' => 'Pentru relaxare', 'description' => 'Meditații pentru relaxare.'],
                            'meditatie-stres' => ['name' => 'Pentru reducerea stresului', 'description' => 'Meditații pentru reducerea stresului.'],
                            'meditatie-somn' => ['name' => 'Pentru somn', 'description' => 'Meditații pentru un somn mai bun.'],
                            'meditatie-scurte' => ['name' => 'Scurte', 'description' => 'Meditații scurte.']
                        ]
                    ],
                    'prezentari-meserii' => [
                        'name' => 'Prezentări Meserii',
                        'description' => 'Prezintă diferite meserii într-un format atractiv.',
                        'subcategories' => [
                            'profesor' => ['name' => 'Profesor', 'description' => 'Prezentarea meseriei de profesor.'],
                            'medic' => ['name' => 'Medic', 'description' => 'Prezentarea meseriei de medic.'],
                            'bucatar' => ['name' => 'Bucătar', 'description' => 'Prezentarea meseriei de bucătar.'],
                            'sofer' => ['name' => 'Șofer', 'description' => 'Prezentarea meseriei de șofer.'],
                            'vanzator' => ['name' => 'Vânzător', 'description' => 'Prezentarea meseriei de vânzător.'],
                            'mecanic' => ['name' => 'Mecanic Auto', 'description' => 'Prezentarea meseriei de mecanic auto.'],
                            'constructor' => ['name' => 'Constructor', 'description' => 'Prezentarea meseriei de constructor.'],
                            'fotograf' => ['name' => 'Fotograf', 'description' => 'Prezentarea meseriei de fotograf.'],
                            'designer-grafic' => ['name' => 'Designer Grafic', 'description' => 'Prezentarea meseriei de designer grafic.'],
                            'florar' => ['name' => 'Florar', 'description' => 'Prezentarea meseriei de florar.']
                        ]
                    ]
                ]
            ],

            'tutoriale' => [
                'name' => 'Tutoriale (Cum să...)',
                'description' => 'Creează videoclipuri instructive, pas cu pas. Folosește un script cu instrucțiuni clare, o imagine care demonstrează procesul și text care explică pașii.',
                'subcategories' => [
                    'tech' => ['name' => 'Tech', 'description' => 'Tutoriale despre tehnologie (ex: telefoane, calculatoare, gadget-uri).'],
                    'diy' => ['name' => 'DIY', 'description' => 'Tutoriale despre proiecte de tip "Do It Yourself".'],
                    'beauty' => ['name' => 'Beauty', 'description' => 'Tutoriale despre machiaj, îngrijirea pielii, etc.'],
                    'cooking' => ['name' => 'Gătit', 'description' => 'Tutoriale despre gătit (rețete).'],
                    'software' => ['name' => 'Software', 'description' => 'Tutoriale despre utilizarea programelor.'],
                    'ai-tools' => ['name' => 'Unelte AI', 'description' => 'Tutoriale despre utilizarea uneltelor AI.']
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

            'lifehacks-rapide' => [
                'name' => 'Life Hacks',
                'description' => 'Prezintă trucuri și sfaturi utile pentru viața de zi cu zi.',
                'subcategories' => [
                    'casa' => ['name' => 'Pentru Casă', 'description' => 'Life hacks pentru casă.'],
                    'bucatarie' => ['name' => 'Bucătărie', 'description' => 'Life hacks pentru bucătărie.'],
                    'tech' => ['name' => 'Tech', 'description' => 'Life hacks pentru tehnologie.'],
                    'calatorii' => ['name' => 'Călătorii', 'description' => 'Life hacks pentru călătorii.'],
                    'hacks-rapide' => [
                        'name' => '5 Second Hacks',
                        'description' => 'Trucuri cu obiecte cotidiene.'
                    ],
                    'economisire' => [
                        'name' => 'Cum Să Economisești Bani',
                        'description' => 'Grafice simple cu statistici.'
                    ],
                    'ai-tools' => [
                        'name' => 'Unelte AI Necunoscute',
                        'description' => 'Demo-uri cu capturi de ecran.'
                    ]
                ]
            ],

            'stiri-scurte' => [
                'name' => 'Știri Scurte',
                'description' => 'Prezintă știri concise și captivante. Folosește un script informativ, o imagine relevantă și text cu titlul și sursa știrii.',
                'subcategories' => [
                    'locale' => ['name' => 'Locale', 'description' => 'Știri locale.'],
                    'nationale' => ['name' => 'Naționale', 'description' => 'Știri naționale.'],
                    'internationale' => ['name' => 'Internaționale', 'description' => 'Știri internaționale.'],
                    'tech' => ['name' => 'Tech', 'description' => 'Știri despre tehnologie.']
                ]
            ],

            'challenge-uri' => [
                'name' => 'Challenge-uri Adaptate',
                'description' => 'Participă la provocări video care se potrivesc cu formatul aplicației (imagine + text).',
                'subcategories' => [
                    'photo-challenges' => [
                        'name' => 'Provocări Foto',
                        'description' => 'Provocări care implică crearea de fotografii.',
                        'subcategories' => [
                            'outfit-challenge' => ['name' => 'Outfit of the Day', 'description' => 'Prezintă-ți ținuta zilei.'],
                            'before-after' => ['name' => 'Înainte și După', 'description' => 'Arată transformări (ex: renovări, machiaj, etc.).'],
                            'flat-lay' => ['name' => 'Flat Lay Challenge', 'description' => 'Creează o compoziție cu obiecte așezate pe o suprafață plană.'],
                            'peisaje-urbane' => ['name' => 'Peisaje Urbane', 'description' => 'Fotografiază peisaje urbane interesante.'],
                            'natura-moarta'  => ['name' => 'Natură Moartă', 'description' => 'Fotografiază compoziții cu obiecte inanimate.'],
                            'portrete-creative' => ['name' => 'Portrete Creative', 'description' => 'Realizează portrete creative.']
                        ]
                    ],
                    'text-challenges' => [
                        'name' => 'Provocări Text',
                        'description' => 'Provocări care implică scrierea de texte.',
                        'subcategories' => [
                            'quote-challenge' => ['name' => 'Citatul Zilei', 'description' => 'Alege un citat și creează un videoclip inspirat de acesta.'],
                            'gratitude-challenge' => ['name' => 'Jurnal de Recunoștință', 'description' => 'Exprimă-ți recunoștința pentru ceva.'],
                            'story-challenge' => ['name' => 'Poveste în 3 Propoziții', 'description' => 'Spune o poveste în doar trei propoziții.'],
                            'haiku-challenge' => ['name' => 'Haiku Challenge', 'description' => 'Scrie un haiku (poem scurt de 3 versuri).'],
                            'acrostih-challenge' => ['name' => 'Acrostih Challenge', 'description' => 'Scrie un acrostih (poezie în care prima literă a fiecărui vers formează un cuvânt).'],
                            'completeaza-proverbul' => [
                                'name' => 'Completează Proverbul',
                                'description' => 'Joc interactiv cu imagini și audio.'
                            ],
                            'ghicitoare' => [
                                'name' => 'Ghicitoare Vizuală',
                                'description' => 'Imagini cryptică cu provocare în narare.'
                            ]
                        ]
                    ]
                ]
            ],

            'recenzii' => [
                'name' => 'Recenzii Scurte',
                'description' => 'Prezintă-ți părerea despre cărți, filme, produse, etc., într-un format concis.',
                'subcategories' => [
                    'recenzii-carti' => ['name' => 'De cărți', 'description' => 'Recenzii de cărți.'],
                    'recenzii-filme' => ['name' => 'De filme', 'description' => 'Recenzii de filme.'],
                    'recenzii-produse' => ['name' => 'De produse', 'description' => 'Recenzii de produse.'],
                    'recenzii-jocuri' => ['name' => 'De jocuri', 'description' => 'Recenzii de jocuri.']
                ]
            ],

            'liste' => [
                'name' => 'Liste (Top-uri)',
                'description' => 'Creează videoclipuri cu liste (Top 5, Top 10, etc.).',
                'subcategories' => [
                    'top-5-filme' => ['name' => 'Top 5 Filme', 'description' => 'Top 5 filme preferate.'],
                    'top-10-carti' => ['name' => 'Top 10 Cărți', 'description' => 'Top 10 cărți preferate.'],
                    'top-3-destinatii' => ['name' => 'Top 3 Destinații', 'description' => 'Top 3 destinații de călătorie preferate.']
                ]
            ],
            'business-content' => [
                'name' => 'Marketing Digital',
                'description' => 'Conținut pentru promovarea afacerilor.',
                'subcategories' => [
                    'idei-promovare' => [
                        'name' => 'Strategii de Promovare',
                        'description' => 'Idei și strategii de promovare online.',
                        'subcategories' => [
                            'videoclipuri-virale' => ['name' => 'Structuri Videoclipuri Virale', 'description' => 'Structuri pentru videoclipuri virale.'],
                            'storytelling-efectiv' => ['name' => 'Tehnici Storytelling', 'description' => 'Tehnici de storytelling.'],
                            'exemple-succes' => ['name' => 'Exemple de Succes', 'description' => 'Exemple de campanii de marketing de succes.'],
                        ]
                    ],
                    'analiza-trenduri' => [
                        'name' => 'Analiză Trenduri',
                        'description' => 'Analiza trendurilor în marketing.',
                        'subcategories' => [
                            'hashtag-uri-locale' => ['name' => 'Hashtag-uri Populare RO', 'description' => 'Hashtag-uri populare în România.'],
                            'strategii-sezoane' => ['name' => 'Conținut Sezonier', 'description' => 'Strategii de conținut sezonier.'],
                            'instrumente-analiza' => ['name' => 'Instrumente de Analiză', 'description' => 'Instrumente de analiză a trendurilor.'],
                        ]
                    ]
                ]
            ],

            'asmr-relaxare' => [
                'name' => 'ASMR & Relaxare',
                'description' => 'Conținut pentru relaxare și liniște.',
                'subcategories' => [
                    'liniste-vizuala' => [
                        'name' => 'Liniște Vizuală',
                        'description' => 'Imagini din natură cu text meditativ.'
                    ],
                    'spatii-minimaliste' => [
                        'name' => 'Spații Minimaliste',
                        'description' => 'Design interior estetic cu citate.'
                    ],
                    'sunete-calme' => [
                        'name' => 'Sunete Calme',
                        'description' => 'Videoclipuri cu sunete relaxante (ex: ploaie, foc).'
                    ]
                ]
            ],

            'controversial-dezbateri' => [
                'name' => 'Controversial & Dezbateri',
                'description' => 'Teme provocatoare pentru discuții aprinse.',
                'subcategories' => [
                    'ai-vs-uman' => [
                        'name' => 'AI vs. Uman',
                        'description' => 'Provocări existențiale cu imagini abstracte.'
                    ],
                    'stiri-parodiate' => [
                        'name' => 'Știri Parodiate',
                        'description' => 'Satiră socială cu text pe fundal de ziar.'
                    ],
                    'dileme-etice' => [
                        'name' => 'Dileme Etice',
                        'description' => 'Întrebări provocatoare cu imagini simbolice.'
                    ]
                ]
            ]
        ];
        foreach ($categories as $categoryData) {
            $this->createCategoryRecursive($categoryData);
        }
    }
}
