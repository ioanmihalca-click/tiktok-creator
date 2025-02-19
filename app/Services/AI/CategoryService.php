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
            'tutoriale' => [
                'name' => 'Tutoriale (Cum să...)',
                'description' => 'Creează videoclipuri instructive, pas cu pas.  Folosește un script cu instrucțiuni clare, o imagine care demonstrează procesul și text care explică pașii.', // Descriere detaliată
                // 'script_type' => 'instructiuni',  // Mutat în comentariu
                // 'image_type' => 'demonstratie',    // Mutat în comentariu
                // 'text_type' => 'pasi/explicatii',  // Mutat în comentariu
                'subcategories' => [
                    'tech' => ['name' => 'Tech', 'description' => 'Tutoriale despre tehnologie (ex: telefoane, calculatoare, gadget-uri).'],
                    'diy' => ['name' => 'DIY', 'description' => 'Tutoriale despre proiecte de tip "Do It Yourself".'],
                    'beauty' => ['name' => 'Beauty', 'description' => 'Tutoriale despre machiaj, îngrijirea pielii, etc.'],
                    'cooking' => ['name' => 'Gătit', 'description' => 'Tutoriale despre gătit (rețete).'],
                    'software' => ['name' => 'Software', 'description' => 'Tutoriale despre utilizarea programelor.'],
                    'ai-tools' => ['name' => 'Unelte AI', 'description' => 'Tutoriale despre utilizarea uneltelor AI.'],
                ],
            ],

            'stiri-scurte' => [
                'name' => 'Știri Scurte',
                'description' => 'Prezintă știri concise și captivante. Folosește un script informativ, o imagine relevantă și text cu titlul și sursa știrii.',
                // 'script_type' => 'informativ',
                // 'image_type' => 'relevanta',
                // 'text_type' => 'titlu/sursa',
                'subcategories' => [
                    'locale' => ['name' => 'Locale', 'description' => 'Știri locale.'],
                    'nationale' => ['name' => 'Naționale', 'description' => 'Știri naționale.'],
                    'internationale' => ['name' => 'Internaționale', 'description' => 'Știri internaționale.'],
                    'tech' => ['name' => 'Tech', 'description' => 'Știri despre tehnologie.'],
                ],
            ],

            'challenge-uri' => [
                'name' => 'Challenge-uri Adaptate',
                'description' => 'Participă la provocări video care se potrivesc cu formatul aplicației (imagine + text).',
                // 'script_type' => 'instructiuni/descriere',
                // 'image_type' => 'ilustrativa/demonstratie',
                // 'text_type' => 'titlu/hashtag/reguli',
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
                            'portrete-creative' => ['name' => 'Portrete Creative', 'description' => 'Realizează portrete creative.'],
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
                        ]
                    ]
                ]
            ],

            'recenzii' => [
                'name' => 'Recenzii Scurte',
                'description' => 'Prezintă-ți părerea despre cărți, filme, produse, etc., într-un format concis.',
                // 'script_type' => 'opinie',
                // 'image_type' => 'produs/coperta/etc.',
                // 'text_type' => 'pro-contra/rating',
                'subcategories' => [
                    'recenzii-carti' => ['name' => 'De cărți', 'description' => 'Recenzii de cărți.'],
                    'recenzii-filme' => ['name' => 'De filme', 'description' => 'Recenzii de filme.'],
                    'recenzii-produse' => ['name' => 'De produse', 'description' => 'Recenzii de produse.'],
                    'recenzii-jocuri' => ['name' => 'De jocuri', 'description' => 'Recenzii de jocuri.'],
                ]
            ],

            'liste' => [
                'name' => 'Liste (Top-uri)',
                'description' => 'Creează videoclipuri cu liste (Top 5, Top 10, etc.).',
                // 'script_type' => 'informativ',
                // 'image_type' => 'colaj/ilustrativa',
                // 'text_type' => 'titlu/numar/descriere',
                'subcategories' => [
                    'top-5-filme' => ['name' => 'Top 5 Filme', 'description' => 'Top 5 filme preferate.'],
                    'top-10-carti' => ['name' => 'Top 10 Cărți', 'description' => 'Top 10 cărți preferate.'],
                    'top-3-destinatii' => ['name' => 'Top 3 Destinații', 'description' => 'Top 3 destinații de călătorie preferate.'],
                ],
            ],
            'educatie-informare' => [
                'name' => 'Educație și Informare',
                'description' => 'Prezintă informații educative și utile.',
                // 'script_type' => 'informativ',  // General
                // 'image_type' => 'ilustrativa',   // General
                // 'text_type' => 'titlu/date/explicatii', // General
                'subcategories' => [
                    'curiozitati' => [
                        'name' => 'Curiozități',
                        'description' => 'Prezintă informații interesante și neobișnuite.',
                        // 'script_type' => 'informativ',
                        // 'image_type' => 'ilustrativa',
                        // 'text_type' => 'titlu/date',
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
                        // 'script_type' => 'instructiuni',
                        // 'image_type' => 'demonstratie',
                        // 'text_type' => 'pasi/explicatii',
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
                        // 'script_type' => 'instructiuni',
                        // 'image_type' => 'ilustrativa/demonstratie',
                        // 'text_type' => 'pasi/explicatii',
                        'subcategories' => [
                            'lectii-gramatica' => ['name' => 'De gramatică', 'description' => 'Mini-lecții de gramatică.'],
                            'lectii-cultura' => ['name' => 'De cultură generală', 'description' => 'Mini-lecții de cultură generală.'],
                            'lectii-limbi' => ['name' => 'De limbi străine', 'description' => 'Mini-lecții de limbi străine.']
                        ]
                    ],
                    'meditatii' => [
                        'name' => 'Meditații Ghidate',
                        'description' => 'Oferă meditații ghidate pentru relaxare și bunăstare.',
                        // 'script_type' => 'instructiuni',
                        // 'image_type' => 'relaxantă/spirituală',
                        // 'text_type' => 'pași/afirmații',
                        'subcategories' => [
                            'meditatie-relaxare' => ['name' => 'Pentru relaxare', 'description' => 'Meditații pentru relaxare.'],
                            'meditatie-stres' => ['name' => 'Pentru reducerea stresului', 'description' => 'Meditații pentru reducerea stresului.'],
                            'meditatie-somn' => ['name' => 'Pentru somn', 'description' => 'Meditații pentru un somn mai bun.'],
                            'meditatie-scurte' => ['name' => 'Scurte', 'description' => 'Meditații scurte.'],
                        ]
                    ],
                    'prezentari-meserii' => [
                        'name' => 'Prezentări Meserii',
                        'description' => 'Prezintă diferite meserii într-un format atractiv.',
                        // 'script_type' => 'informativ/descriere',
                        // 'image_type' => 'persoana/unelte/loc-de-munca',
                        // 'text_type' => 'titlu/descriere/date',
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
                            'florar' => ['name' => 'Florar', 'description' => 'Prezentarea meseriei de florar.'],
                        ]
                    ],
                ]
            ],

            'emotii-sentimente' => [
                'name' => 'Emoții și Sentimente',
                'description' => 'Exprimă emoții și sentimente prin videoclipuri.',
                // 'script_type' => 'citat/narativ/versuri',
                // 'image_type' => 'personală/ilustrativă',
                // 'text_type'  => 'citat/autor/dedicație/versuri',
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
                    'mesaje-amuzante' => [
                        'name' => 'Mesaje Amuzante/Hazlii',
                        'description' => 'Mesaje pentru a aduce zâmbetul pe buze.',
                        // 'script_type' => 'gluma',  // Mutat in comentariu
                        // 'image_type' => 'amuzanta/contrast', // Mutat in comentariu
                        // 'text_type' => 'gluma',  // Mutat in comentariu
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
                // 'script_type' => 'citat/narativ/versuri',
                // 'image_type' => 'religioasă/spirituală/ilustrativă',
                // 'text_type' => 'citat/autor/verset/rugăciune',
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
                // 'script_type' => 'citat',
                // 'image_type' => 'inspiratoare/abstractă',
                // 'text_type' => 'citat/autor',
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
                    ]
                ]
            ],

            'ocazii-speciale' => [
                'name' => 'Ocazii Speciale',
                'description' => 'Mesaje pentru ocazii speciale.',
                // 'script_type' => 'citat/narativ',
                // 'image_type' => 'festivă/personală',
                // 'text_type' => 'mesaj/dedicație',
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
                // 'script_type' => 'instructiuni',
                // 'image_type' => 'demonstratie',
                // 'text_type' => 'pasi/explicatii',
                'subcategories' => [
                    'casa' => ['name' => 'Pentru Casă', 'description' => 'Life hacks pentru casă.'],
                    'bucatarie' => ['name' => 'Bucătărie', 'description' => 'Life hacks pentru bucătărie.'],
                    'tech' => ['name' => 'Tech', 'description' => 'Life hacks pentru tehnologie.'],
                    'calatorii' => ['name' => 'Călătorii', 'description' => 'Life hacks pentru călătorii.'],
                ],
            ],

            'lifehacks-ro' => [
                'name' => 'Life Hacks Local',
                'description' => 'Trucuri utile, cu specific românesc.',
                // 'script_type' => 'instructiuni',
                // 'image_type' => 'demonstratie',
                // 'text_type' => 'pasi/explicatii',
                'subcategories' => [
                    'economie-casnica' => [
                        'name' => 'Economie Casnică',
                        'description' => 'Life hacks pentru economie casnică.',
                        'subcategories' => [
                            'retete-buget' => ['name' => 'Rețete cu Buget Redus', 'description' => 'Rețete cu buget redus.'],
                            'diy-cu-reciclare' => ['name' => 'DIY cu Materiale Reciclate', 'description' => 'Proiecte DIY cu materiale reciclate.']
                        ]
                    ],
                    'tech-hacks' => [
                        'name' => 'Hack-uri Tehnologice',
                        'description' => 'Life hacks pentru tehnologie.',
                        'subcategories' => [
                            'aplicatii-utile' => ['name' => 'Aplicații Necunoscute Utile', 'description' => 'Aplicații utile, mai puțin cunoscute.'],
                            'setari-secrete' => ['name' => 'Setări Secrete pe Telefon', 'description' => 'Setări secrete pe telefon.'],
                            'trucuri-pc' => ['name' => 'Trucuri PC', 'description' => 'Trucuri pentru PC.'],
                        ]
                    ],
                    'sfaturi-calatorie-ro' => ['name' => 'Sfaturi Călătorie RO', 'description' => 'Sfaturi pentru călătorii în România.'],
                ]
            ],

            'business-content' => [
                'name' => 'Marketing Digital',
                'description' => 'Conținut pentru promovarea afacerilor.',
                // 'script_type' => 'informativ/opinie',
                // 'image_type' => 'grafice/ilustratii',
                // 'text_type' => 'titlu/date/sfaturi',
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
            'cultura-pop-ro' => [
                'name' => 'Cultură Pop RO',
                'description' => 'Conținut despre cultura populară românească.',
                // 'script_type' => 'informativ/opinie/narativ',
                // 'image_type' => 'ilustrativa/arhivă/personală',
                // 'text_type' => 'titlu/date/explicatii/versuri',
                'subcategories' => [
                    'fenomene-internet' => [
                        'name' => 'Fenomene Internet',
                        'description' => 'Fenomene de pe internetul românesc.',
                        'subcategories' => [
                            'virale-romanesti' => ['name' => 'Clipuri Virale Românești', 'description' => 'Clipuri video virale din România.'],
                            'meme-uri-romanesti' => ['name' => 'Meme-uri Românești', 'description' => 'Meme-uri românești.'],
                        ]
                    ]
                ]
            ],
            'explicatii' => [
                'name' => 'Explicații (Știai că...?)',
                'description' => 'Prezintă informații interesante într-un format concis.',
                'subcategories' => [
                    'stiinta' => ['name' => 'Știință', 'description' => 'Explicații despre știință.'],
                    'istorie' => ['name' => 'Istorie', 'description' => 'Explicații despre istorie.'],
                    'cultura' => ['name' => 'Cultură Generală', 'description' => 'Explicații despre cultură generală.'],
                    'finante' => ['name' => 'Finanțe Personale', 'description' => 'Explicații despre finanțe personale.'],
                ],
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
                    'florar' => ['name' => 'Florar', 'description' => 'Prezentarea meseriei de florar.'],
                ]
            ]
        ];
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getCategoryBySlug(string $slug): ?array
    {
        return $this->findCategory($this->categories, $slug);
    }

    private function findCategory(array $categories, string $slug): ?array
    {
        foreach ($categories as $currentSlug => $category) {
            if ($currentSlug === $slug) {
                return $category;
            }

            if (isset($category['subcategories'])) {
                $found = $this->findCategory($category['subcategories'], $slug);
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
