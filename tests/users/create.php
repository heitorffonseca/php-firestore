<?php


require_once __DIR__ . '/../../vendor/autoload.php';

use App\Services\FirestoreService;
use Faker\Factory;

$service = new FirestoreService();
$service->collection('users');

for ($i = 1; $i < 50; $i++) {
    $faker = Factory::create('pt_BR');

    $gender = $faker->randomElement(['male', 'female']);

    $data = [
        'name' => $faker->name($gender),
        'email' => $faker->safeEmail,
        'username' => $faker->userName,
        'phone' => $faker->phoneNumber,
        'gender' => $gender,
        'address' => $faker->address,
    ];

    $service->newDocument($data);
}
