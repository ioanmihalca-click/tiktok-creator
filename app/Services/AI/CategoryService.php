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
        // EMOȚII ȘI SENTIMENTE
        'emotii-sentimente' => [  // Păstrăm
            'name' => 'Emoții și Sentimente',
            'subcategories' => [
                'declaratii-dragoste' => [ // Păstrăm - Se potrivește cu formatul citat/narare
                    'name' => 'Declarații de Dragoste',
                     'script_type' => 'citat/narativ',
                     'image_type' => 'romantică/personală',
                     'text_type' => 'citat/autor/dedicație',
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
                'mesaje-motivationale' => [ // Păstrăm - Se potrivește cu formatul citat/narare
                    'name' => 'Mesaje de Încurajare/Motivaționale',
                    'script_type' => 'citat/narativ',
                    'image_type' => 'inspiratoare/motivațională',
                    'text_type' => 'citat/autor',
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
                'mesaje-multumire' => [ // Păstrăm - Se potrivește cu formatul citat/narare
                    'name' => 'Mesaje de Mulțumire',
                    'script_type' => 'citat/narativ',
                    'image_type' => 'caldă/personală',
                    'text_type' => 'mesaj/dedicație',
                    'subcategories' => [
                        'pentru-profesori' => ['name' => 'Pentru profesori', 'subcategories' => []],
                        'pentru-medici' => ['name' => 'Pentru medici', 'subcategories' => []],
                        'pentru-familie' => ['name' => 'Pentru familie', 'subcategories' => []],
                        'pentru-prieteni-multumire' => ['name' => 'Pentru prieteni', 'subcategories' => []],
                        'generale-multumire' => ['name' => 'Generale', 'subcategories' => []]
                    ]
                ],
                'mesaje-amuzante' => [  //Pastram, se potriveste cu formatul 'gluma'
                    'name' => 'Mesaje Amuzante/Hazlii',
                    'script_type' => 'gluma',
                    'image_type' => 'amuzanta/contrast',
                    'text_type' => 'gluma',
                    'subcategories' => [
                        'glume-scurte' => ['name' => 'Glume scurte', 'subcategories' => []],
                        'bancuri' => ['name' => 'Bancuri', 'subcategories' => []],
                        'povesti-amuzante' => ['name' => 'Povești amuzante', 'subcategories' => []],
                        'citate-amuzante' => ['name' => 'Citate amuzante', 'subcategories' => []]
                    ]
                ]
            ]
        ],

        // SPIRITUALITATE ȘI RELIGIE
        'spiritualitate-religie' => [ // Păstrăm
            'name' => 'Spiritualitate și Religie',
            'subcategories' => [
                'rugaciuni' => [  // Păstrăm - Se potrivește cu formatul narativ/citat
                    'name' => 'Rugăciuni',
                    'script_type' => 'narativ/citat',
                    'image_type' => 'religioasă/spirituală',
                    'text_type' => 'rugăciune/titlu',
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
                'citate-religioase' => [ // Păstrăm - Se potrivește cu formatul citat
                    'name' => 'Citate Religioase/Spirituale',
                     'script_type' => 'citat',
                    'image_type' => 'religioasă/spirituală',
                    'text_type' => 'citat/autor',
                    'subcategories' => [
                        'biblie' => ['name' => 'Din Biblie', 'subcategories' => []],
                        'sfinti' => ['name' => 'De la sfinți/părinți ai bisericii', 'subcategories' => []],
                        'alte-traditii' => ['name' => 'Din alte tradiții spirituale', 'subcategories' => []]
                    ]
                ],
                'meditatii' => [ // **Mutare/Modificare**: Mutăm sub "Educatie si Informare" ca un tip de tutorial.
                    'name' => 'Meditații Ghidate',
                    'script_type' => 'instructiuni', // Modificat
                    'image_type' => 'relaxantă/spirituală', //Modificat
                    'text_type' => 'pași/afirmații',  //Modificat
                    'subcategories' => [
                        'meditatie-relaxare' => ['name' => 'Pentru relaxare', 'subcategories' => []],
                        'meditatie-stres' => ['name' => 'Pentru reducerea stresului', 'subcategories' => []],
                        'meditatie-somn' => ['name' => 'Pentru somn', 'subcategories' => []],
                        'meditatie-scurte' => ['name' => 'Scurte', 'subcategories' => []]
                    ]
                ]
            ]
        ],

        // CITATE ȘI ÎNȚELEPCIUNE
        'citate-intelepciune' => [ // Păstrăm - Se potrivește cu formatul citat
            'name' => 'Citate și Înțelepciune',
            'script_type' => 'citat',
            'image_type' => 'inspiratoare/abstractă',
            'text_type' => 'citat/autor',
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
                'proverbe' => [ // Proverbele sunt tot citate. Pastram
                    'name' => 'Proverbe și Zicători',
                    'subcategories' => [
                        'proverbe-romanesti' => ['name' => 'Românești', 'subcategories' => []],
                        'proverbe-internationale' => ['name' => 'Internaționale', 'subcategories' => []]
                    ]
                ],
                'aforisme' => [ // Aforismele sunt tot citate. Pastram
                    'name' => 'Aforisme',
                    'subcategories' => [
                        'aforisme-scurte' => ['name' => 'Scurte și percutante', 'subcategories' => []]
                    ]
                ]
            ]
        ],

        // EDUCAȚIE ȘI INFORMARE
        'educatie-informare' => [ // Păstrăm
            'name' => 'Educație și Informare',
            'subcategories' => [
                'curiozitati' => [ // Păstrăm - Se potrivește cu formatul explicație
                    'name' => 'Curiozități',
                    'script_type' => 'informativ',
                    'image_type' => 'ilustrativa',
                    'text_type' => 'titlu/date',
                    'subcategories' => [
                        'curiozitati-stiinta' => ['name' => 'Despre știință', 'subcategories' => []],
                        'curiozitati-istorie' => ['name' => 'Despre istorie', 'subcategories' => []],
                        'curiozitati-geografie' => ['name' => 'Despre geografie', 'subcategories' => []],
                        'curiozitati-animale' => ['name' => 'Despre animale', 'subcategories' => []],
                        'curiozitati-corp' => ['name' => 'Despre corpul uman', 'subcategories' => []],
                        'curiozitati-tehnologie' => ['name' => 'Despre tehnologie', 'subcategories' => []]
                    ]
                ],
                'sfaturi-utile' => [ // Păstrăm - Se potrivește cu formatul tutorial
                    'name' => 'Sfaturi Utile',
                    'script_type' => 'instructiuni',
                    'image_type' => 'demonstratie',
                    'text_type' => 'pasi/explicatii',
                    'subcategories' => [
                        'sfaturi-studiu' => ['name' => 'Pentru studiu', 'subcategories' => []],
                        'sfaturi-gatit' => ['name' => 'Pentru gătit', 'subcategories' => []],
                        'sfaturi-organizare' => ['name' => 'Pentru organizare', 'subcategories' => []],
                        'sfaturi-calatorii' => ['name' => 'Pentru călătorii', 'subcategories' => []],
                        'sfaturi-ingrijire' => ['name' => 'Pentru îngrijirea personală', 'subcategories' => []],
                        'sfaturi-economie' => ['name' => 'Pentru economisirea banilor', 'subcategories' => []]
                    ]
                ],
                'mini-lectii' => [  // Păstrăm - Se potrivește cu formatul tutorial
                    'name' => 'Mini-Lecții',
                    'script_type' => 'instructiuni',
                    'image_type' => 'ilustrativa/demonstratie',
                    'text_type' => 'pasi/explicatii',
                    'subcategories' => [
                        'lectii-gramatica' => ['name' => 'De gramatică', 'subcategories' => []],
                        'lectii-cultura' => ['name' => 'De cultură generală', 'subcategories' => []],
                        'lectii-limbi' => ['name' => 'De limbi străine', 'subcategories' => []]
                    ]
                ],
                // Adăugăm aici subcategoria "meditatii" de la "Spiritualitate"
                 'meditatii' => [
                    'name' => 'Meditații Ghidate',
                    'script_type' => 'instructiuni',
                    'image_type' => 'relaxantă/spirituală',
                    'text_type' => 'pași/afirmații',
                    'subcategories' => [
                        'meditatie-relaxare' => ['name' => 'Pentru relaxare', 'subcategories' => []],
                        'meditatie-stres' => ['name' => 'Pentru reducerea stresului', 'subcategories' => []],
                        'meditatie-somn' => ['name' => 'Pentru somn', 'subcategories' => []],
                        'meditatie-scurte' => ['name' => 'Scurte', 'subcategories' => []]
                    ]
                ]
            ]
        ],

        // DIVERTISMENT
        'divertisment' => [ // Păstrăm
            'name' => 'Divertisment',
            'subcategories' => [
                'povesti-scurte' => [  // Păstrăm - Se potrivește cu formatul narativ
                    'name' => 'Povești Scurte',
                    'script_type' => 'narativ',
                    'image_type' => 'ilustrativa',
                    'text_type' => 'dialog/naratiune',
                    'subcategories' => [
                        'povesti-groaza' => ['name' => 'De groază', 'subcategories' => []],
                        'povesti-dragoste' => ['name' => 'De dragoste', 'subcategories' => []],
                        'povesti-sf' => ['name' => 'SF', 'subcategories' => []],
                        'fabule' => ['name' => 'Fabule', 'subcategories' => []],
                         // Eliminăm 'poezii-scurte' de aici. Poeziile se potrivesc mai bine la o categorie separata
                    ]
                ]
            ]
        ],
        // Poezii - Categorie separata
        'poezii' => [
            'name' => 'Poezii',
            'script_type' => 'versuri',
            'image_type' => 'ilustrativa/abstracta',
            'text_type' => 'versuri/titlu',
            'subcategories' => [
                 'poezii-scurte' => ['name' => 'Poezii scurte', 'subcategories' => []],
                 'poezii-lungi' => ['name' => 'Poezii lungi', 'subcategories' => []] //Adaugat
            ],
        ],

        // OCAZII SPECIALE
        'ocazii-speciale' => [ // Păstrăm -  Se potrivește cu formatul citat/narare (mesaje)
            'name' => 'Ocazii Speciale',
            'script_type' => 'citat/narativ',  // Adăugat script_type
            'image_type' => 'festivă/personală', // Adăugat image_type
            'text_type' => 'mesaj/dedicație',    // Adăugat text_type
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

        // MESERII -  Mutare / Modificare:  Mutăm la "Educație și Informare" ca "Prezentări Meserii"
        'prezentari-meserii' => [
            'name' => 'Prezentări Meserii',
            'script_type' => 'informativ/descriere',
            'image_type' => 'persoana/unelte/loc-de-munca',
            'text_type' => 'titlu/descriere/date',
            'subcategories' => [
                'profesor' => [
                    'name' => 'Profesor',
                    'subcategories' => []
                ],
                'medic' => [
                    'name' => 'Medic',
                    'subcategories' => []
                ],
                'bucatar' => [
                    'name' => 'Bucătar',
                    'subcategories' => []
                ],
                'sofer' => [
                    'name' => 'Șofer',
                    'subcategories' => []
                ],
                'vanzator' => [
                    'name' => 'Vânzător',
                    'subcategories' => []
                ],
                'mecanic' => [
                    'name' => 'Mecanic Auto',
                    'subcategories' => []
                ],
                'constructor' => [
                    'name' => 'Constructor',
                    'subcategories' => []
                ]
            ]
        ],

        // DIVERSE
        'diverse' => [ // Păstrăm, dar vom adăuga mai multe subcategorii relevante
            'name' => 'Diverse',
            'subcategories' => [
                'recenzii' => [ // Pastram recenziile, se potrivesc cu formatul opinie
                    'name' => 'Recenzii Scurte',
                    'script_type' => 'opinie',
                    'image_type' => 'produs/coperta/etc.',
                    'text_type' => 'pro-contra/rating',
                    'subcategories' => [
                        'recenzii-carti' => ['name' => 'De cărți', 'subcategories' => []],
                        'recenzii-filme' => ['name' => 'De filme', 'subcategories' => []],
                        'recenzii-produse' => ['name' => 'De produse', 'subcategories' => []], // Adăugat
                        'recenzii-jocuri' => ['name' => 'De jocuri', 'subcategories' => []]  // Adăugat
                    ]
                ],
                'provocari' => [ // Provocările se potrivesc, DAR trebuie să fie provocări care se pot face cu script+imagine+text
                    'name' => 'Provocări',
                    'script_type' => 'instructiuni/descriere', // Instrucțiuni dacă e o provocare nouă, descriere dacă e una existentă
                    'image_type' => 'ilustrativa/demonstratie',
                    'text_type' => 'titlu/hashtag/reguli',
                    'subcategories' => [
                        //  'provocari-sigure' => ['name' => 'Idei de provocări sigure', 'subcategories' => []]  -- Prea general, eliminăm
                        'provocari-creative-foto' => ['name' => 'Provocări foto', 'subcategories' => []],  // Provocări legate de fotografie
                        'provocari-scriere' => ['name' => 'Provocări de scriere', 'subcategories' => []], // Provocări legate de scris
                        'provocari-desen' => ['name' => 'Provocări de desen', 'subcategories' => []] //Daca se pot integra cumva
                    ]
                ],
                'liste' => [ // Adăugăm o subcategorie nouă: Liste (Top 5, Top 10, etc.)
                    'name' => 'Liste (Top-uri)',
                    'script_type' => 'informativ', // Prezentarea elementelor din listă
                    'image_type' => 'colaj/ilustrativa', // Colaj cu elementele din listă, sau o imagine ilustrativă
                    'text_type' => 'titlu/numar/descriere', // Titlul listei, numărul elementului, descriere scurtă
                    'subcategories' => [
                        'top-5-filme' => ['name' => 'Top 5 Filme', 'subcategories' => []],
                        'top-10-carti' => ['name' => 'Top 10 Cărți', 'subcategories' => []],
                        'top-3-destinatii' => ['name' => 'Top 3 Destinații', 'subcategories' => []],
                    ],
                ],
            ]
        ],

        // CATEGORII NOI (din sugestiile anterioare, adaptate)

        'tutoriale' => [  // Pastram
            'name' => 'Tutoriale (Cum să...)',
            'description' => 'Creează videoclipuri instructive, pas cu pas.',
            'script_type' => 'instructiuni',
            'image_type' => 'demonstratie',
            'text_type' => 'pasi/explicatii',
            'subcategories' => [
                'tech' => ['name' => 'Tech', 'subcategories' => []],
                'diy' => ['name' => 'DIY', 'subcategories' => []],
                'beauty' => ['name' => 'Beauty', 'subcategories' => []],
                'cooking' => ['name' => 'Gătit', 'subcategories' => []],
                'software' => ['name' => 'Software', 'subcategories' => []],
            ],
        ],
        'explicatii' => [ // Pastram
            'name' => 'Explicații (Știai că...?)',
            'description' => 'Prezintă informații interesante într-un format concis.',
            'script_type' => 'informativ',
            'image_type' => 'ilustrativa',
            'text_type' => 'titlu/date',
            'subcategories' => [
                'stiinta' => ['name' => 'Știință', 'subcategories' => []],
                'istorie' => ['name' => 'Istorie', 'subcategories' => []],
                'cultura' => ['name' => 'Cultură Generală', 'subcategories' => []],
                'finante' => ['name' => 'Finanțe Personale', 'subcategories' => []],
            ],
        ],
        'citate-motivationale' => [ // Pastram
            'name' => 'Citate Motivaționale',
            'description' => 'Inspiră-ți urmăritorii cu citate puternice.',
            'script_type' => 'citat',
            'image_type' => 'inspiratoare',
            'text_type' => 'citat/autor',
            'subcategories' => [
                'viata' => ['name' => 'Despre Viață', 'subcategories' => []],
                'succes' => ['name' => 'Despre Succes', 'subcategories' => []],
                'iubire' => ['name' => 'Despre Iubire', 'subcategories' => []],
                'dezvoltare-personala' => ['name' => 'Dezvoltare Personală', 'subcategories' => []],
            ],
        ],
        'stiri-scurte' => [ // Pastram
            'name' => 'Știri Scurte',
            'description' => 'Prezintă știri concise și captivante.',
            'script_type' => 'informativ',
            'image_type' => 'relevanta',
            'text_type' => 'titlu/sursa',
            'subcategories' => [
                'locale' => ['name' => 'Locale', 'subcategories' => []],
                'nationale' => ['name' => 'Naționale', 'subcategories' => []],
                'internationale' => ['name' => 'Internaționale', 'subcategories' => []],
                'tech' => ['name' => 'Tech', 'subcategories' => []],
            ],
        ],
        'glume-vizuale' => [ // Pastram
            'name' => 'Glume Vizuale',
            'description' => 'Creează videoclipuri amuzante cu glume scurte.',
            'script_type' => 'gluma',
            'image_type' => 'amuzanta/contrast',
            'text_type' => 'gluma',
            'subcategories' => [
                'scurte' => ['name' => 'Glume Scurte', 'subcategories' => []],
                'vizuale' => ['name' => 'Glume Vizuale', 'subcategories' => []],
                'romanesti' => ['name' => 'Glume Românești', 'subcategories' => []],
            ],
        ],
        'povestiri-scurte' => [ // Pastram
            'name' => 'Povești Scurte',
            'description' => 'Spune povești captivante în format video.',
            'script_type' => 'narativ',
            'image_type' => 'ilustrativa',
            'text_type' => 'dialog/naratiune',
            'subcategories' => [
                'microfictiune' => ['name' => 'Microficțiune', 'subcategories' => []],
                'fabule' => ['name' => 'Fabule (cu morală)', 'subcategories' => []],
                'personale' => ['name' => 'Experiențe Personale', 'subcategories' => []],
            ]
        ],
          'recenzii-fulger' => [ // Pastram
            'name' => 'Recenzii Fulger',
            'description' => 'Prezintă rapid părerea ta despre produse, filme, cărți, etc.',
            'script_type' => 'opinie',
            'image_type' => 'produs',
            'text_type' => 'pro-contra/rating',
            'subcategories' => [
                'produse' => ['name' => 'Produse', 'subcategories' => []],
                'filme' => ['name' => 'Filme', 'subcategories' => []],
                'carti' => ['name' => 'Cărți', 'subcategories' => []],
                'jocuri' => ['name' => 'Jocuri', 'subcategories' => []],
                'aplicatii' => ['name' => 'Aplicații', 'subcategories' => []],
            ],
        ],
        'provocari-video' => [ // Provocari *care se pot face cu script+imagine+text*
            'name' => 'Provocări (Challenges)',
            'description' => 'Creează sau participă la provocări video populare.',
            'script_type' => 'instructiuni/descriere', // Adaptat
            'image_type' => 'ilustrativa/demonstratie', // Adaptat
            'text_type' => 'titlu/hashtag/reguli',  // Adaptat
            'subcategories' => [
                // 'dans' => ['name' => 'Dans', 'subcategories' => []],  -- Eliminăm dans, nu se potrivește *direct*
                // 'creative' => ['name' => 'Creative', 'subcategories' => []], -- Prea general, eliminăm
                // 'audio' => ['name' => 'Audio', 'subcategories' => []], -- Eliminăm audio (deocamdată)
                'creative-foto' => ['name' => 'Foto', 'subcategories' => []],
                'creative-scriere' => ['name' => 'Scriere', 'subcategories' => []],
                'creative-desen' => ['name' => 'Desen', 'subcategories' => []], //Daca se pot integra cumva
            ]
        ],
        'lifehacks-rapide' => [ // Pastram, dar ne asiguram ca sunt lifehacks *care se pot prezenta vizual*
            'name' => 'Life Hacks',
            'description' => 'Prezintă trucuri și sfaturi utile pentru viața de zi cu zi.',
            'script_type' => 'instructiuni',
            'image_type' => 'demonstratie',
            'text_type' => 'pasi/explicatii',
            'subcategories' => [
                'casa' => ['name' => 'Pentru Casă', 'subcategories' => []],
                'bucatarie' => ['name' => 'Bucătărie', 'subcategories' => []],
                'tech' => ['name' => 'Tech', 'subcategories' => []],
                'calatorii' => ['name' => 'Călătorii', 'subcategories' => []],
            ],
        ],
         'challenge-uri' => [ // Provocari *care se pot face cu script+imagine+text*
            'name' => 'Challenge-uri Adaptate',
            'description' => 'Challenge-uri care se pot face cu imagini și text.',
            'script_type' => 'instructiuni/descriere',
            'image_type' => 'ilustrativa/demonstratie',
            'text_type' => 'titlu/hashtag/reguli',
            'subcategories' => [
                'photo-challenges' => [
                    'name' => 'Provocări Foto',
                    'subcategories' => [
                        'outfit-challenge' => ['name' => 'Outfit of the Day', 'subcategories' => []],
                        'before-after' => ['name' => 'Înainte și După', 'subcategories' => []],
                        'flat-lay' => ['name' => 'Flat Lay Challenge', 'subcategories' => []]
                    ]
                ],
                'text-challenges' => [
                    'name' => 'Provocări Text',
                    'subcategories' => [
                        'quote-challenge' => ['name' => 'Citatul Zilei', 'subcategories' => []],
                        'gratitude-challenge' => ['name' => 'Jurnal de Recunoștință', 'subcategories' => []],
                        'story-challenge' => ['name' => 'Poveste în 3 Propoziții', 'subcategories' => []]
                    ]
                ]
            ]
        ],

        'educatie-accelerata' => [ // Pastram, dar ne asiguram ca sunt informatii *care se pot prezenta vizual*
            'name' => 'Știi că...?',
            'description' => 'Informații interesante și rapide.',
            'script_type' => 'informativ',
            'image_type' => 'ilustrativa',
            'text_type' => 'titlu/date',
            'subcategories' => [
                'istorie-pe-scurt' => [
                    'name' => 'Istorie pe Scurt',
                    'subcategories' => [
                        'figuri-istorice' => ['name' => 'Figuri Istorice în 15 sec', 'subcategories' => []],
                        'razboaie-uitate' => ['name' => 'Războaie Necunoscute', 'subcategories' => []]
                    ]
                ],
                'stiinta-simplificata' => [
                    'name' => 'Știință pe Înțelesul Tuturor',
                    'subcategories' => [
                        'fenomene-naturale' => ['name' => 'Fenomene Naturale', 'subcategories' => []],
                        'inventii-revolutionare' => ['name' => 'Invenții care au Schimbat Lumea', 'subcategories' => []]
                    ]
                ]
            ]
        ],

        'lifehacks-ro' => [  // Pastram, dar ne asiguram ca sunt lifehacks *care se pot prezenta vizual*
            'name' => 'Life Hacks Local',
            'description' => 'Trucuri utile, cu specific românesc.',
            'script_type' => 'instructiuni',
            'image_type' => 'demonstratie',
            'text_type' => 'pasi/explicatii',
            'subcategories' => [
                'economie-casnica' => [
                    'name' => 'Economie Casnică',
                    'subcategories' => [
                        'retete-buget' => ['name' => 'Rețete cu Buget Redus', 'subcategories' => []],
                        'diy-cu-reciclare' => ['name' => 'DIY cu Materiale Reciclate', 'subcategories' => []]
                    ]
                ],
                'tech-hacks' => [
                    'name' => 'Hack-uri Tehnologice',
                    'subcategories' => [
                        'aplicatii-utile' => ['name' => 'Aplicații Necunoscute Utile', 'subcategories' => []],
                        'setari-secrete' => ['name' => 'Setări Secrete pe Telefon', 'subcategories' => []],
                        'trucuri-pc' => ['name' => 'Trucuri PC', 'subcategories' => []],
                    ]
                ],
                'sfaturi-calatorie-ro' => ['name' => 'Sfaturi Călătorie RO', 'subcategories' => []],
            ]
        ],

        'business-content' => [ // Păstrăm, dar cu accent pe *vizualizarea* informațiilor
            'name' => 'Marketing Digital',
            'description' => 'Conținut pentru promovarea afacerilor.',
            'script_type' => 'informativ/opinie',
            'image_type' => 'grafice/ilustratii',
            'text_type' => 'titlu/date/sfaturi',
            'subcategories' => [
                'idei-promovare' => [
                    'name' => 'Strategii de Promovare',
                    'subcategories' => [
                        'videoclipuri-virale' => ['name' => 'Structuri Videoclipuri Virale', 'subcategories' => []],
                        'storytelling-efectiv' => ['name' => 'Tehnici Storytelling', 'subcategories' => []],
                        'exemple-succes' => ['name' => 'Exemple de Succes', 'subcategories' => []],
                    ]
                ],
                'analiza-trenduri' => [
                    'name' => 'Analiză Trenduri',
                    'subcategories' => [
                        'hashtag-uri-locale' => ['name' => 'Hashtag-uri Populare RO', 'subcategories' => []],
                        'strategii-sezoane' => ['name' => 'Conținut Sezonier', 'subcategories' => []],
                        'instrumente-analiza' => ['name' => 'Instrumente de Analiză', 'subcategories' => []],
                    ]
                ]
            ]
        ],

        'cultura-pop-ro' => [ // Păstrăm, dar cu accent pe *vizualizarea* informațiilor
            'name' => 'Cultură Pop RO',
            'description' => 'Conținut despre cultura populară românească.',
            'script_type' => 'informativ/opinie',
            'image_type' => 'ilustrativa/arhivă',
            'text_type' => 'titlu/date/explicatii',
            'subcategories' => [
                'memorii-communism' => [
                    'name' => 'Memorii Comuniste',
                    'subcategories' => [
                        'obiecte-uitate' => ['name' => 'Obiecte de Cultură Vintage', 'subcategories' => []],
                        'slang-anii-80' => ['name' => 'Slang din Epoca de Aur', 'subcategories' => []],
                        'muzica-veche-ro' => ['name' => 'Muzică Veche Românească', 'subcategories' => []],
                    ]
                ],
                'fenomene-internet' => [
                    'name' => 'Fenomene Internet',
                    'subcategories' => [
                        'virale-romanesti' => ['name' => 'Clipuri Virale Românești', 'subcategories' => []],
                        'influenceri-locali' => ['name' => 'Analiză Influenceri', 'subcategories' => []],
                         'meme-uri-romanesti' => ['name' => 'Meme-uri Românești', 'subcategories' => []],
                    ]
                ]
            ]
        ],

        'asmr-ro' => [ // Eliminăm ASMR - Nu se potrivește cu formatul curent (doar imagine, fără sunete speciale)
            'name' => 'ASMR Autohton',
            'description' => 'Sunete relaxante, cu specific românesc.',
            'script_type' => 'descriere/naratiune',
            'image_type' => 'prim-plan/detaliu',
            'text_type' => 'titlu/descriere',
            'subcategories' => [
                'sunete-traditionale' => [
                    'name' => 'Sunete Tradiționale',
                    'subcategories' => [
                        'artizanat-local' => ['name' => 'Procese Artizanale', 'subcategories' => []],
                        'gatit-traditional' => ['name' => 'Sunete din Bucătărie', 'subcategories' => []],
                        'natura-romaneasca' => ['name' => 'Natura Românească', 'subcategories' => []],
                    ]
                ],
                'ritualuri-zilnice' => [
                    'name' => 'Ritualuri Zilnice',
                    'subcategories' => [
                        'pregatire-dimineata' => ['name' => 'Rutine de Dimineață', 'subcategories' => []],
                        'relaxare-seara' => ['name' => 'Tehnici de Relaxare', 'subcategories' => []]
                    ]
                ]
            ]
        ],

        'gaming-shorts' => [ // Eliminăm Gaming - Nu se potrivește *direct* cu formatul curent (fără clipuri video)
            'name' => 'Gaming Rapid',
            'description' => 'Momente scurte și intense din jocuri.',
            'script_type' => 'narativ/exclamatii',
            'image_type' => 'gameplay',
            'text_type' => 'titlu/comentariu',
            'subcategories' => [
                'clipuri-epice' => [
                    'name' => 'Momente Epice',
                    'subcategories' => [
                        'speedruns' => ['name' => 'Speedruns Românești', 'subcategories' => []],
                        'glitch-uri-amuzante' => ['name' => 'Glitch-uri Distractive', 'subcategories' => []],
                        'fail-uri-amuzante' => ['name' => 'Fail-uri Amuzante', 'subcategories' => []],
                    ]
                ],
                'retro-gaming' => [
                    'name' => 'Retro Gaming',
                    'subcategories' => [
                        'console-uitate' => ['name' => 'Istorie Console', 'subcategories' => []],
                        'jocuri-cult' => ['name' => 'Jocuri Cult', 'subcategories' => []]
                    ]
                ],
                 'tutoriale-gaming' => ['name' => 'Tutoriale Gaming', 'subcategories' => []],
            ]
        ],

        'ai-tools' => [//Pastram, dar cu accent ca se poate face doar prezentarea uneltelor, nu si folosirea lor
            'name' => 'Unelte AI',
            'description' => 'Prezentări și tutoriale pentru unelte AI.',
            'script_type' => 'informativ/instructiuni',
            'image_type' => 'captura-ecran/ilustratie',
            'text_type' => 'titlu/pasi/explicatii',
            'subcategories' => [
                'tutoriale-rapide' => [
                    'name' => 'Tutoriale Express',
                    'subcategories' => [
                        'editare-video' => ['name' => 'Editare Video cu AI', 'subcategories' => []],
                        'generare-texte' => ['name' => 'Scriere Creativă AI', 'subcategories' => []],
                         'generare-imagini-ai' => ['name' => 'Generare Imagini AI', 'subcategories' => []],
                    ]
                ],
                'comparatii' => [
                    'name' => 'Comparații Tool-uri',
                    'subcategories' => [
                        'top-5-unealta' => ['name' => 'Top 5 pe Categorii', 'subcategories' => []],
                        'ro-vs-global' => ['name' => 'Soluții RO vs. Internaționale', 'subcategories' => []]
                    ]
                ]
            ]
        ],
    ];
}


   
    
public function getCategories(): array
{
    return $this->categories ?? [];
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