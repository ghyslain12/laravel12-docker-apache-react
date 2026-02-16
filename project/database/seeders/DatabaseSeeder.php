<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Material;
use App\Models\Sale;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Créer 10 utilisateurs + leurs customers associés
        $users = User::factory()->count(10)->create([
            'password' => Hash::make('password'), // mot de passe commun pour tests
        ]);

        $customers = collect();

        foreach ($users as $user) {
            $customer = Customer::create([
                'surnom'  => fake()->userName(),
                'user_id' => $user->id,
            ]);
            $customers->push($customer);
        }

        // 2. Créer 10 matériaux
        $materials = Material::factory()->count(10)->create([
            'designation' => fn() => fake()->unique()->word() . ' ' . fake()->randomElement(['Pro', 'Elite', 'Standard', 'Premium', 'Lite'])
        ]);

        // 3. Créer 10 tickets
        $tickets = Ticket::factory()->count(10)->create([
            'titre'       => fn() => fake()->sentence(4),
            'description' => fn() => fake()->paragraph(2),
        ]);

        // 4. Créer 10 ventes avec relations many-to-many
        foreach (range(1, 10) as $i) {
            $sale = Sale::create([
                'titre'        => fake()->sentence(3),
                'description'  => fake()->optional(0.7)->paragraph(),
                'customer_id'  => $customers->random()->id,
            ]);

            // Attacher 2 à 5 matériaux aléatoires
            $sale->materials()->attach(
                $materials->random(rand(2, 5))->pluck('id')->toArray()
            );

            // Attacher 1 à 4 tickets aléatoires
            $sale->tickets()->attach(
                $tickets->random(rand(1, 4))->pluck('id')->toArray()
            );
        }

        // Optionnel : Créer un admin super user
        User::factory()->create([
            'name'     => 'Admin',
            'email'    => 'admin@example.com',
            'password' => Hash::make('admin123'),
        ]);
        User::factory()->create([
            'name'     => 'gigi',
            'email'    => 'gigi@gigi.com',
            'password' => Hash::make('gigi'),
        ]);

        $this->command->info('Base de données remplie !');
        $this->command->info('→ 10 Users + 10 Customers');
        $this->command->info('→ 10 Materials');
        $this->command->info('→ 10 Tickets');
        $this->command->info('→ 10 Sales (avec materials et tickets attachés)');
    }
}
