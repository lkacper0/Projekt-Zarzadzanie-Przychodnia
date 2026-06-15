<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
            'Chirurgia'
        ];
        
        $specializationModels = [];
        foreach ($specs as $specName) {
            $specializationModels[$specName] = \App\Models\Specialization::create(['name' => $specName]);
        }


        $tagsData = ['NFZ', 'Prywatnie', 'Dzieci', 'Teleporada', 'Konsultacja online'];
        $tagModels = [];
        foreach ($tagsData as $tagName) {
            $tagModels[$tagName] = \App\Models\Tag::create(['name' => $tagName]);
        }

        $patient = User::factory()->create([
            'first_name' => 'Jan',
            'last_name' => 'Kowalski',
            'email' => 'pacjent@example.com',
            'role' => 'patient',
        ]);

        $admin = User::factory()->create([
            'first_name' => 'Anna',
            'last_name' => 'Nowak',
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $doctorUser = User::factory()->create([
            'first_name' => 'Adam',
            'last_name' => 'Lekarski',
            'email' => 'lekarz@example.com',
            'role' => 'doctor',
        ]);
        
        $doctorProfile = \App\Models\DoctorProfile::create([
            'user_id' => $doctorUser->id,
            'bio' => 'Doświadczony lekarz chorób wewnętrznych.',
            'is_accepted' => true,
            'avg_rating' => 4.50,
        ]);

        $doctorProfile->specializations()->attach($specializationModels['Internista']->id);
        $doctorProfile->tags()->attach([$tagModels['NFZ']->id, $tagModels['Teleporada']->id]);

        $candidateUser = User::factory()->create([
            'first_name' => 'Marta',
            'last_name' => 'Kandydatka',
            'email' => 'kandydat@example.com',
            'role' => 'patient',
        ]);

        $candidateProfile = \App\Models\DoctorProfile::create([
            'user_id' => $candidateUser->id,
            'bio' => 'Chciałabym dołączyć do zespołu jako pediatra.',
            'is_accepted' => false,
            'avg_rating' => 0.00,
        ]);

        $candidateProfile->specializations()->attach($specializationModels['Pediatria']->id);
        $candidateProfile->tags()->attach([$tagModels['Prywatnie']->id, $tagModels['Dzieci']->id]);

        $service = \App\Models\Service::create([
            'doctor_id' => $doctorProfile->id,
            'name' => 'Konsultacja internistyczna',
            'description' => 'Standardowa wizyta u internisty',
            'price' => 150.00,
            'duration_minutes' => 30,
        ]);

        $slot = \App\Models\AvailabilitySlot::create([
            'doctor_id' => $doctorProfile->id,
            'start_time' => now()->addDays(1),
            'end_time' => now()->addDays(1)->addMinutes(30),
            'is_booked' => true,
        ]);

        $appointment = \App\Models\Appointment::create([
            'slot_id' => $slot->id,
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'status' => 'completed',
        ]);

        \App\Models\Review::create([
            'appointment_id' => $appointment->id,
            'patient_id' => $patient->id,
            'doctor_id' => $doctorProfile->id,
            'rating' => 5,
            'comment' => 'Bardzo profesjonalne podejście do pacjenta, polecam!',
        ]);

        $doctorUser2 = User::factory()->create([
            'first_name' => 'Kamil',
            'last_name' => 'Kardiolog',
            'email' => 'kamil@example.com',
            'role' => 'doctor',
        ]);
        $doctorProfile2 = \App\Models\DoctorProfile::create([
            'user_id' => $doctorUser2->id,
            'bio' => 'Kardiologia to moja pasja.',
            'is_accepted' => true,
            'avg_rating' => 4.90,
        ]);
        $doctorProfile2->specializations()->attach($specializationModels['Kardiologia']->id);
        $doctorProfile2->tags()->attach([$tagModels['Prywatnie']->id]);

        $doctorUser3 = User::factory()->create([
            'first_name' => 'Patryk',
            'last_name' => 'Pediatra',
            'email' => 'patryk@example.com',
            'role' => 'doctor',
        ]);
        $doctorProfile3 = \App\Models\DoctorProfile::create([
            'user_id' => $doctorUser3->id,
            'bio' => 'Leczenie dzieci z uśmiechem.',
            'is_accepted' => true,
            'avg_rating' => 4.80,
        ]);
        $doctorProfile3->specializations()->attach($specializationModels['Pediatria']->id);
        $doctorProfile3->tags()->attach([$tagModels['Dzieci']->id]);

        $doctorUser4 = User::factory()->create([
            'first_name' => 'Ewa',
            'last_name' => 'Dermatolog',
            'email' => 'ewa@example.com',
            'role' => 'doctor',
        ]);
        $doctorProfile4 = \App\Models\DoctorProfile::create([
            'user_id' => $doctorUser4->id,
            'bio' => 'Dbam o zdrową skórę.',
            'is_accepted' => true,
            'avg_rating' => 4.70,
        ]);
        $doctorProfile4->specializations()->attach($specializationModels['Dermatologia']->id);
        $doctorProfile4->tags()->attach([$tagModels['Teleporada']->id]);

        \App\Models\PageContent::create([
            'key' => 'homepage',
            'value' => '<div><div id="baner"><img src="/media/baner.jpg" alt="baner"></div><div id="bloki"><p>Regularne badania kontrolne ratują życie 🩺</p><p>Seniorzy 80+ są szczególnie narażeni na zawał ❤️</p><p>Dbaj o swoje zdrowie - umawiaj wizyty online! 💻</p><p>Szczepienia chronią przed groźnymi chorobami 💉</p><p>Mierz ciśnienie – nadciśnienie często nie daje objawów 📊</p></div><div id="blok2"><p><strong>Witamy w ProHealth</strong></p><p>Nowoczesnej przychodni, gdzie oferujemy zarówno podstawowe, jak i specjalistyczne konsultacje oraz badania dla Pacjentów indywidualnych, podmiotów medycznych i klientów instytucjonalnych.</p><p>ProHealth to nowa jakość opieki zdrowotnej - profesjonalna obsługa i indywidualne podejście do każdego Pacjenta.</p></div><div id="zdjecie1"><div id="zlewo"><img src="/media/wnetrze.jpg" alt="wnetrze"></div><div id="zprawo">Nasza przychodnia oferuje nowoczesne i komfortowe warunki dla Pacjentów, zapewniając przyjazną atmosferę oraz dostęp do najnowszych technologii medycznych.</div></div><div id="zdjecie2"><div id="zlewo2">W 2025r. nasza przychodnia wyposażyła się w najwocześniejszy aparat EKG. Dzięki, któremu jesteśmy w stanie przeprowadzić szybkie i precyzyjne badania serca.</div><div id="zprawo2"><img src="/media/ekg.jpeg" alt="ekg"></div></div><div id="scianatekstu"><h1>ProHealth - Twoja zaufana przychodnia w Rzeszowie</h1><p>ProHealth to nowoczesna przychodnia medyczna w Rzeszowie, oferująca kompleksową opiekę zdrowotną dla dzieci i dorosłych. Zapewniamy szeroki zakres usług medycznych, nowoczesne zaplecze diagnostyczne oraz doświadczony zespół lekarzy i specjalistów. Naszą misją jest troska o zdrowie Pacjentów oraz zapewnienie szybkiej, rzetelnej i komfortowej opieki w przyjaznej atmosferze.</p><h2>Szeroka oferta usług medycznych</h2><p>W przychodni ProHealth oferujemy kompleksowe świadczenia zdrowotne, obejmujące zarówno profilaktykę, diagnostykę, jak i leczenie. W naszej ofercie znajdują się m.in.:</p><ul><li>konsultacje lekarza rodzinnego i lekarzy specjalistów</li><li>badania diagnostyczne i laboratoryjne</li><li>badania krwi - morfologia, lipidogram, badania tarczycy, poziom glukozy</li><li>badania moczu i kału</li><li>konsultacje profilaktyczne i kontrolne</li></ul><p>Dzięki szerokiej ofercie usług Pacjenci mogą liczyć na kompleksową opiekę medyczną w jednym miejscu. Każdy Pacjent traktowany jest indywidualnie, z pełnym zaangażowaniem i dbałością o komfort wizyty.</p><h2>Badania i diagnostyka - profesjonalizm i wygoda</h2><p>Badania diagnostyczne stanowią kluczowy element skutecznej opieki zdrowotnej. W ProHealth zapewniamy szybki dostęp do badań oraz sprawną obsługę na każdym etapie wizyty. Korzystamy z nowoczesnego sprzętu diagnostycznego, a wyniki badań dostępne są w krótkim czasie. Wszystkie procedury wykonywane są w komfortowych i bezpiecznych warunkach.</p><h2>Przychodnia ProHealth w Rzeszowie</h2><p>ProHealth to nowoczesna przychodnia w Rzeszowie, zaprojektowana z myślą o komforcie Pacjentów. Dogodna lokalizacja oraz elastyczne godziny przyjęć pozwalają łatwo dopasować wizytę do codziennych obowiązków.</p><h2>Dlaczego warto wybrać ProHealth?</h2><ul><li>kompleksowa opieka medyczna w jednym miejscu</li><li>nowoczesne zaplecze diagnostyczne</li><li>szybkie terminy wizyt i badań</li><li>komfortowe warunki leczenia i diagnostyki</li><li>indywidualne podejście do każdego Pacjenta</li></ul><p>Stawiamy na jakość, rzetelność i bezpieczeństwo. Wybierając ProHealth, wybierasz profesjonalną opiekę medyczną, zaufanie i komfort na każdym etapie leczenia.</p></div></div>'
        ]);

        \App\Models\PageContent::create([
            'key' => 'about',
            'value' => '<h1>O nas - ProHealth</h1><p>ProHealth to nowoczesna przychodnia medyczna zlokalizowana w Rzeszowie, działająca od 2020 roku. Naszą misją jest zapewnienie najwyższej jakości opieki zdrowotnej w przyjaznej i profesjonalnej atmosferze.</p><h2>Nasza historia</h2><p>Przychodnia ProHealth powstała z pasji do medycyny i chęci stworzenia miejsca, w którym każdy pacjent czuje się zaopiekowany. Nasz zespół składa się z doświadczonych lekarzy specjalistów, którzy nieustannie podnoszą swoje kwalifikacje.</p><h2>Nasz zespół</h2><p>Zatrudniamy ponad 20 lekarzy specjalistów z różnych dziedzin medycyny. Każdy z naszych lekarzy posiada wieloletnie doświadczenie kliniczne oraz najwyższe kwalifikacje zawodowe.</p><h2>Nasze wartości</h2><ul><li>Profesjonalizm i najwyższa jakość usług medycznych</li><li>Indywidualne podejście do każdego pacjenta</li><li>Nowoczesne metody diagnostyki i leczenia</li><li>Przyjazna i komfortowa atmosfera</li><li>Ciągłe doskonalenie i rozwój</li></ul><p>Zapraszamy do skorzystania z naszych usług. Razem zadbamy o Twoje zdrowie!</p>'
        ]);

        \App\Models\PageContent::create([
            'key' => 'contact',
            'value' => '<h1>Kontakt - ProHealth</h1><h2>Dane kontaktowe</h2><p><strong>Przychodnia ProHealth</strong></p><p>ul. Medyczna 15<br>35-001 Rzeszów</p><p><strong>Telefon:</strong> +48 17 123 45 67</p><p><strong>E-mail:</strong> kontakt@prohealth.pl</p><p><strong>Rejestracja:</strong> rejestracja@prohealth.pl</p><h2>Godziny otwarcia</h2><ul><li>Poniedziałek - Piątek: 7:00 - 20:00</li><li>Sobota: 8:00 - 14:00</li><li>Niedziela: nieczynne</li></ul><h2>Jak do nas trafić?</h2><p>Przychodnia ProHealth znajduje się w centrum Rzeszowa, w pobliżu Rynku. Do dyspozycji pacjentów dostępny jest bezpłatny parking.</p><p><strong>Dojazd komunikacją miejską:</strong> Autobusy linii 5, 12, 18 - przystanek "Medyczna"</p><h2>Formularz kontaktowy</h2><p>W razie pytań prosimy o kontakt telefoniczny lub mailowy. Odpowiadamy na wszystkie wiadomości w ciągu 24 godzin roboczych.</p>'
        ]);
    }
}

