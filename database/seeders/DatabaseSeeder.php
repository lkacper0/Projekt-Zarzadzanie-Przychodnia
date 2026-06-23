<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Specialization;
use App\Models\Tag;
use App\Models\DoctorProfile;
use App\Models\Service;
use App\Models\AvailabilitySlot;
use App\Models\Appointment;
use App\Models\Review;
use App\Models\PageContent;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $specs = [
            'Internista',
            'Kardiologia',
            'Pediatria',
            'Dermatologia',
            'Neurologia',
            'Ginekologia',
            'Chirurgia',
            'Okulistyka',
            'Ortopedia',
            'Laryngologia',
            'Psychiatria',
            'Endokrynologia',
            'Onkologia'
        ];

        $specializationModels = [];
        foreach ($specs as $specName) {
            $specializationModels[$specName] = Specialization::create(['name' => $specName]);
        }

        $tagsData = ['NFZ', 'Prywatnie', 'Dzieci', 'Teleporada', 'Konsultacja online'];
        $tagModels = [];
        foreach ($tagsData as $tagName) {
            $tagModels[$tagName] = Tag::create(['name' => $tagName]);
        }

        $admin = User::factory()->create([
            'first_name' => 'Anna',
            'last_name' => 'Nowak',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $patientsData = [
            ['first_name' => 'Jan', 'last_name' => 'Kowalski', 'email' => 'pacjent@example.com'],
            ['first_name' => 'Katarzyna', 'last_name' => 'Nowak', 'email' => 'pacjent2@example.com'],
            ['first_name' => 'Michał', 'last_name' => 'Wiśniewski', 'email' => 'pacjent3@example.com'],
            ['first_name' => 'Agnieszka', 'last_name' => 'Wójcik', 'email' => 'pacjent4@example.com'],
            ['first_name' => 'Piotr', 'last_name' => 'Kowalczyk', 'email' => 'pacjent5@example.com'],
            ['first_name' => 'Zofia', 'last_name' => 'Kamińska', 'email' => 'pacjent6@example.com'],
            ['first_name' => 'Łukasz', 'last_name' => 'Lewandowski', 'email' => 'pacjent7@example.com'],
            ['first_name' => 'Joanna', 'last_name' => 'Zielińska', 'email' => 'pacjent8@example.com'],
            ['first_name' => 'Maciej', 'last_name' => 'Szymański', 'email' => 'pacjent9@example.com'],
            ['first_name' => 'Aleksandra', 'last_name' => 'Woźniak', 'email' => 'pacjent10@example.com']
        ];

        $patientModels = [];
        foreach ($patientsData as $pData) {
            $patientModels[] = User::factory()->create([
                'first_name' => $pData['first_name'],
                'last_name' => $pData['last_name'],
                'email' => $pData['email'],
                'role' => 'patient',
            ]);
        }

        $doctorsData = [
            [
                'first_name' => 'Adam',
                'last_name' => 'Lekarski',
                'email' => 'lekarz@example.com',
                'bio' => 'Doświadczony lekarz chorób wewnętrznych.',
                'specs' => ['Internista'],
                'tags' => ['NFZ', 'Teleporada']
            ],
            [
                'first_name' => 'Kamil',
                'last_name' => 'Kardiolog',
                'email' => 'kamil@example.com',
                'bio' => 'Kardiologia to moja pasja.',
                'specs' => ['Kardiologia'],
                'tags' => ['Prywatnie']
            ],
            [
                'first_name' => 'Patryk',
                'last_name' => 'Pediatra',
                'email' => 'patryk@example.com',
                'bio' => 'Leczenie dzieci z uśmiechem.',
                'specs' => ['Pediatria'],
                'tags' => ['Dzieci']
            ],
            [
                'first_name' => 'Ewa',
                'last_name' => 'Dermatolog',
                'email' => 'ewa@example.com',
                'bio' => 'Dbam o zdrową skórę.',
                'specs' => ['Dermatologia'],
                'tags' => ['Teleporada']
            ],
            [
                'first_name' => 'Janusz',
                'last_name' => 'Neurolog',
                'email' => 'janusz@example.com',
                'bio' => 'Specjalista diagnostyki i leczenia schorzeń układu nerwowego.',
                'specs' => ['Neurologia'],
                'tags' => ['NFZ', 'Prywatnie']
            ],
            [
                'first_name' => 'Grażyna',
                'last_name' => 'Ginekolog',
                'email' => 'grazyna@example.com',
                'bio' => 'Kompleksowa opieka ginekologiczna i położnicza.',
                'specs' => ['Ginekologia'],
                'tags' => ['Prywatnie', 'Konsultacja online']
            ],
            [
                'first_name' => 'Wojciech',
                'last_name' => 'Chirurg',
                'email' => 'wojciech@example.com',
                'bio' => 'Chirurg z wieloletnim doświadczeniem w zabiegach małooperacyjnych.',
                'specs' => ['Chirurgia'],
                'tags' => ['NFZ', 'Teleporada']
            ],
            [
                'first_name' => 'Maria',
                'last_name' => 'Okulistka',
                'email' => 'maria@example.com',
                'bio' => 'Zadbaj o swój wzrok. Badania okulistyczne dzieci i dorosłych.',
                'specs' => ['Okulistyka'],
                'tags' => ['Prywatnie', 'NFZ']
            ],
            [
                'first_name' => 'Tomasz',
                'last_name' => 'Ortopeda',
                'email' => 'tomasz@example.com',
                'bio' => 'Leczenie urazów narządu ruchu oraz profilaktyka wad postawy.',
                'specs' => ['Ortopedia'],
                'tags' => ['Prywatnie']
            ],
            [
                'first_name' => 'Anna',
                'last_name' => 'Laryngolog',
                'email' => 'anna_l@example.com',
                'bio' => 'Diagnostyka chorób uszu, nosa, gardła i krtani.',
                'specs' => ['Laryngologia'],
                'tags' => ['NFZ', 'Teleporada']
            ],
            [
                'first_name' => 'Piotr',
                'last_name' => 'Psychiatra',
                'email' => 'piotr_p@example.com',
                'bio' => 'Wsparcie psychoterapeutyczne i psychiatryczne w przyjaznej atmosferze.',
                'specs' => ['Psychiatria'],
                'tags' => ['Prywatnie', 'Konsultacja online']
            ],
            [
                'first_name' => 'Helena',
                'last_name' => 'Endokrynolog',
                'email' => 'helena@example.com',
                'bio' => 'Leczenie zaburzeń hormonalnych, tarczycy i cukrzycy.',
                'specs' => ['Endokrynologia'],
                'tags' => ['NFZ', 'Prywatnie']
            ],
            [
                'first_name' => 'Robert',
                'last_name' => 'Onkolog',
                'email' => 'robert@example.com',
                'bio' => 'Konsultacje onkologiczne i wczesna profilaktyka nowotworowa.',
                'specs' => ['Onkologia'],
                'tags' => ['NFZ']
            ],
            [
                'first_name' => 'Barbara',
                'last_name' => 'Ginekolog',
                'email' => 'barbara@example.com',
                'bio' => 'Profilaktyka i nowoczesna diagnostyka ginekologiczna.',
                'specs' => ['Ginekologia'],
                'tags' => ['NFZ', 'Teleporada']
            ],
            [
                'first_name' => 'Paweł',
                'last_name' => 'Pediatra',
                'email' => 'pawel@example.com',
                'bio' => 'Indywidualne podejście do małego pacjenta od pierwszych dni życia.',
                'specs' => ['Pediatria'],
                'tags' => ['Dzieci', 'Prywatnie']
            ],
            [
                'first_name' => 'Zofia',
                'last_name' => 'Dermatolog',
                'email' => 'zofia@example.com',
                'bio' => 'Dermatologia estetyczna i leczenie chorób skóry.',
                'specs' => ['Dermatologia'],
                'tags' => ['Prywatnie', 'Konsultacja online']
            ],
            [
                'first_name' => 'Andrzej',
                'last_name' => 'Kardiolog',
                'email' => 'andrzej@example.com',
                'bio' => 'Profilaktyka chorób serca, nadciśnienia tętniczego.',
                'specs' => ['Kardiologia'],
                'tags' => ['NFZ', 'Teleporada']
            ],
            [
                'first_name' => 'Natalia',
                'last_name' => 'Internistka',
                'email' => 'natalia@example.com',
                'bio' => 'Konsultacje internistyczne oraz porady profilaktyczne.',
                'specs' => ['Internista'],
                'tags' => ['NFZ', 'Teleporada']
            ],
            [
                'first_name' => 'Michał',
                'last_name' => 'Neurolog',
                'email' => 'michal@example.com',
                'bio' => 'Leczenie bólów kręgosłupa i zaburzeń neurologicznych.',
                'specs' => ['Neurologia'],
                'tags' => ['Prywatnie']
            ]
        ];

        $doctorProfiles = [];
        foreach ($doctorsData as $dData) {
            $dUser = User::factory()->create([
                'first_name' => $dData['first_name'],
                'last_name' => $dData['last_name'],
                'email' => $dData['email'],
                'role' => 'doctor',
            ]);

            $dProfile = DoctorProfile::create([
                'user_id' => $dUser->id,
                'bio' => $dData['bio'],
                'is_accepted' => true,
                'avg_rating' => 0.00,
            ]);

            foreach ($dData['specs'] as $sName) {
                if (isset($specializationModels[$sName])) {
                    $dProfile->specializations()->attach($specializationModels[$sName]->id);
                }
            }

            foreach ($dData['tags'] as $tName) {
                if (isset($tagModels[$tName])) {
                    $dProfile->tags()->attach($tagModels[$tName]->id);
                }
            }

            $doctorProfiles[] = $dProfile;
        }

        $candidatesData = [
            [
                'first_name' => 'Marta',
                'last_name' => 'Kandydatka',
                'email' => 'kandydat@example.com',
                'bio' => 'Chciałabym dołączyć do zespołu jako pediatra.',
                'specs' => ['Pediatria'],
                'tags' => ['Prywatnie', 'Dzieci']
            ],
            [
                'first_name' => 'Damian',
                'last_name' => 'Kandydat',
                'email' => 'damian@example.com',
                'bio' => 'Jestem młodym lekarzem internistą, chętnie podejmę pracę.',
                'specs' => ['Internista'],
                'tags' => ['NFZ']
            ],
            [
                'first_name' => 'Sylwia',
                'last_name' => 'Kandydatka',
                'email' => 'sylwia@example.com',
                'bio' => 'Specjalizuję się w zabiegach dermatologii estetycznej.',
                'specs' => ['Dermatologia'],
                'tags' => ['Prywatnie']
            ],
            [
                'first_name' => 'Grzegorz',
                'last_name' => 'Kandydat',
                'email' => 'grzegorz@example.com',
                'bio' => 'Chirurg ortopeda poszukujący stabilnego zatrudnienia.',
                'specs' => ['Ortopedia'],
                'tags' => ['Prywatnie']
            ]
        ];

        foreach ($candidatesData as $cData) {
            $cUser = User::factory()->create([
                'first_name' => $cData['first_name'],
                'last_name' => $cData['last_name'],
                'email' => $cData['email'],
                'role' => 'patient',
            ]);

            $cProfile = DoctorProfile::create([
                'user_id' => $cUser->id,
                'bio' => $cData['bio'],
                'is_accepted' => false,
                'avg_rating' => 0.00,
            ]);

            foreach ($cData['specs'] as $sName) {
                if (isset($specializationModels[$sName])) {
                    $cProfile->specializations()->attach($specializationModels[$sName]->id);
                }
            }

            foreach ($cData['tags'] as $tName) {
                if (isset($tagModels[$tName])) {
                    $cProfile->tags()->attach($tagModels[$tName]->id);
                }
            }
        }

        $comments = [
            'Wizyta przebiegła bardzo pomyślnie. Lekarz wysłuchał moich problemów i wszystko dokładnie wyjaśnił.',
            'Pełen profesjonalizm i świetne podejście do pacjenta. Bardzo dziękuję za pomoc.',
            'Szybko, sprawnie i konkretnie. Leki zostały dobrze dobrane i czuję się znacznie lepiej.',
            'Bardzo przyjazny gabinet. Lekarz cierpliwy i zaangażowany. Polecam serdecznie.',
            'Fachowe doradztwo i miła atmosfera. Na pewno wrócę w razie potrzeby.',
            'Szczerze polecam tego specjalistę. Pomógł mi z problemem, z którym borykałem się od miesięcy.'
        ];

        foreach ($doctorProfiles as $profile) {
            $specName = $profile->specializations()->first()?->name ?? 'medyczna';
            $service1 = Service::create([
                'doctor_id' => $profile->id,
                'name' => 'Konsultacja ' . mb_strtolower($specName),
                'description' => 'Standardowa wizyta konsultacyjna u specjalisty.',
                'price' => rand(150, 250),
                'duration_minutes' => 30,
            ]);

            $service2 = Service::create([
                'doctor_id' => $profile->id,
                'name' => 'Wizyta kontrolna ' . mb_strtolower($specName),
                'description' => 'Wizyta mająca na celu kontrolę postępów leczenia.',
                'price' => rand(100, 150),
                'duration_minutes' => 20,
            ]);

            for ($i = -5; $i <= -1; $i++) {
                $start = now()->addDays($i)->setTime(rand(9, 15), 0);
                $slot = AvailabilitySlot::create([
                    'doctor_id' => $profile->id,
                    'start_time' => $start,
                    'end_time' => $start->copy()->addMinutes(30),
                    'is_booked' => true,
                ]);

                $patient = $patientModels[array_rand($patientModels)];
                $service = rand(0, 1) === 0 ? $service1 : $service2;

                $appt = Appointment::create([
                    'slot_id' => $slot->id,
                    'patient_id' => $patient->id,
                    'service_id' => $service->id,
                    'status' => 'completed',
                    'medical_note' => 'Pacjent zgłosił się z typowymi objawami. Zalecono leczenie farmakologiczne i odpoczynek.'
                ]);

                if (rand(0, 1) === 0) {
                    Review::create([
                        'appointment_id' => $appt->id,
                        'patient_id' => $patient->id,
                        'doctor_id' => $profile->id,
                        'rating' => rand(4, 5),
                        'comment' => $comments[array_rand($comments)],
                    ]);
                }
            }

            for ($i = 1; $i <= 5; $i++) {
                $start = now()->addDays($i)->setTime(9, 0);
                AvailabilitySlot::create([
                    'doctor_id' => $profile->id,
                    'start_time' => $start,
                    'end_time' => $start->copy()->addMinutes(30),
                    'is_booked' => false,
                ]);

                $start2 = now()->addDays($i)->setTime(10, 30);
                AvailabilitySlot::create([
                    'doctor_id' => $profile->id,
                    'start_time' => $start2,
                    'end_time' => $start2->copy()->addMinutes(30),
                    'is_booked' => false,
                ]);

                $start3 = now()->addDays($i)->setTime(13, 0);
                $slot3 = AvailabilitySlot::create([
                    'doctor_id' => $profile->id,
                    'start_time' => $start3,
                    'end_time' => $start3->copy()->addMinutes(30),
                    'is_booked' => true,
                ]);

                $patient = $patientModels[array_rand($patientModels)];
                $service = rand(0, 1) === 0 ? $service1 : $service2;

                Appointment::create([
                    'slot_id' => $slot3->id,
                    'patient_id' => $patient->id,
                    'service_id' => $service->id,
                    'status' => rand(0, 1) === 0 ? 'confirmed' : 'pending',
                ]);
            }
        }

        foreach ($doctorProfiles as $profile) {
            $avgRating = Review::where('doctor_id', $profile->id)->avg('rating');
            $profile->avg_rating = $avgRating ? round($avgRating, 2) : 0.00;
            $profile->save();
        }

        PageContent::create([
            'key' => 'homepage',
            'value' => '<h1>Strona Główna - ProHealth</h1><p>Witamy w ProHealth – nowoczesnej przychodni medycznej w Rzeszowie. Oferujemy kompleksową opiekę zdrowotną dla dzieci i dorosłych.</p><p>Umów wizytę online i zadbaj o swoje zdrowie razem z nami.</p>'
        ]);

        PageContent::create([
            'key' => 'about',
            'value' => '<h1>O nas - ProHealth</h1><p>ProHealth to nowoczesna przychodnia medyczna zlokalizowana w Rzeszowie, działająca od 2020 roku. Naszą misją jest zapewnienie najwyższej jakości opieki zdrowotnej w przyjaznej i profesjonalnej atmosferze.</p><h2>Nasza historia</h2><p>Przychodnia ProHealth powstała z pasji do medycyny i chęci stworzenia miejsca, w którym każdy pacjent czuje się zaopiekowany. Nasz zespół składa się z doświadczonych lekarzy specjalistów, którzy nieustannie podnoszą swoje kwalifikacje.</p><h2>Nasz zespół</h2><p>Zatrudniamy ponad 20 lekarzy specjalistów z różnych dziedzin medycyny. Każdy z naszych lekarzy posiada wieloletnie doświadczenie kliniczne oraz najwyższe kwalifikacje zawodowe.</p><h2>Nasze wartości</h2><ul><li>Profesjonalizm i najwyższa jakość usług medycznych</li><li>Indywidualne podejście do każdego pacjenta</li><li>Nowoczesne metody diagnostyki i leczenia</li><li>Przyjazna i komfortowa atmosfera</li><li>Ciągłe doskonalenie i rozwój</li></ul><p>Zapraszamy do skorzystania z naszych usług. Razem zadbamy o Twoje zdrowie!</p>'
        ]);

        PageContent::create([
            'key' => 'contact',
            'value' => '<h1>Kontakt - ProHealth</h1><h2>Dane kontaktowe</h2><p><strong>Przychodnia ProHealth</strong></p><p>ul. Medyczna 15<br>35-001 Rzeszów</p><p><strong>Telefon:</strong> +48 17 123 45 67</p><p><strong>E-mail:</strong> kontakt@prohealth.pl</p><p><strong>Rejestracja:</strong> rejestracja@prohealth.pl</p><h2>Godziny otwarcia</h2><ul><li>Poniedziałek - Piątek: 7:00 - 20:00</li><li>Sobota: 8:00 - 14:00</li><li>Niedziela: nieczynne</li></ul><h2>Jak do nas trafić?</h2><p>Przychodnia ProHealth znajduje się w centrum Rzeszowa, w pobliżu Rynku. Do dyspozycji pacjentów dostępny jest bezpłatny parking.</p><p><strong>Dojazd komunikacją miejską:</strong> Autobusy linii 5, 12, 18 - przystanek "Medyczna"</p><h2>Formularz kontaktowy</h2><p>W razie pytań prosimy o kontakt telefoniczny lub mailowy. Odpowiadamy na wszystkie wiadomości w ciągu 24 godzin roboczych.</p>'
        ]);
    }
}
