<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $password = Hash::make('password123');

        $users = [
            ['id' => 1,  'name' => 'Martina Solà Ferrer',      'email' => 'admin1@gmail.com',      'role' => 'admin'],
            ['id' => 2,  'name' => 'Bernat Puig Camps',         'email' => 'admin2@gmail.com',      'role' => 'admin'],
            ['id' => 3,  'name' => 'Núria Vidal Esteve',        'email' => 'admin3@gmail.com',      'role' => 'admin'],
            ['id' => 4,  'name' => 'Dr. Arnau Bosch Mas',       'email' => 'doctor1@gmail.com',     'role' => 'doctor'],
            ['id' => 5,  'name' => 'Dra. Laia Tort Comas',      'email' => 'doctor2@gmail.com',     'role' => 'doctor'],
            ['id' => 6,  'name' => 'Dr. Oriol Nadal Font',      'email' => 'doctor3@gmail.com',     'role' => 'doctor'],
            ['id' => 7,  'name' => 'Pau Roca Ginés',            'email' => 'technician1@gmail.com', 'role' => 'technician'],
            ['id' => 8,  'name' => 'Júlia Mir Santana',         'email' => 'technician2@gmail.com', 'role' => 'technician'],
            ['id' => 9,  'name' => 'Marc Llull Ribera',         'email' => 'technician3@gmail.com', 'role' => 'technician'],
            ['id' => 10, 'name' => 'Carla Rovira Pons',         'email' => 'secretary1@gmail.com',  'role' => 'secretary'],
            ['id' => 11, 'name' => 'Toni Gasull Marí',          'email' => 'secretary2@gmail.com',  'role' => 'secretary'],
            ['id' => 12, 'name' => 'Aina Fuster Molina',        'email' => 'secretary3@gmail.com',  'role' => 'secretary'],
        ];

        DB::table('users')->upsert(
            array_map(fn (array $user): array => [
                ...$user,
                'password' => $password,
                'last_update_password' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ], $users),
            ['id'],
            ['name', 'email', 'role', 'password', 'last_update_password', 'updated_at'],
        );

        DB::table('workers')->upsert(
            array_map(fn (array $user): array => [
                'id' => $user['id'],
                'user_id' => $user['id'],
                'nss' => sprintf('100000000%03d', $user['id']),
                'address' => sprintf('Carrer del Treballador %d', $user['id']),
                'dni' => sprintf('%08d%s', $user['id'], chr(64 + $user['id'])),
                'license_number' => in_array($user['role'], ['doctor', 'technician'], true)
                    ? sprintf('100000%03d', $user['id'])
                    : null,
                'phone' => 600000000 + $user['id'],
                'created_at' => $now,
                'updated_at' => $now,
            ], $users),
            ['id'],
            ['user_id', 'nss', 'address', 'dni', 'license_number', 'phone', 'updated_at'],
        );

        DB::table('patients')->upsert([
            [
                'id' => 1,
                'name' => 'Sílvia Torrents Pla',
                'birth_date' => '1988-01-15 00:00:00',
                'nts' => 'NTSS0000000001',
                'address' => 'Carrer del Pacient 1',
                'dni' => '10000001P',
                'phone' => 610000001,
                'email' => 'patient1@gmail.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'name' => 'Guillem Saura Colom',
                'birth_date' => '1992-05-22 00:00:00',
                'nts' => 'NTSS0000000002',
                'address' => 'Carrer del Pacient 2',
                'dni' => '10000002P',
                'phone' => 610000002,
                'email' => 'patient2@gmail.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'name' => 'Montserrat Cos Grau',
                'birth_date' => '1979-11-03 00:00:00',
                'nts' => 'NTSS0000000003',
                'address' => 'Carrer del Pacient 3',
                'dni' => '10000003P',
                'phone' => 610000003,
                'email' => 'patient3@gmail.com',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ], ['id'], ['name', 'birth_date', 'nts', 'address', 'dni', 'phone', 'email', 'updated_at']);
    }
}
