<?php

use App\Enums\OrganizationType;
use App\Enums\OrganizationSubType;

use Faker\Generator as Faker;

$factory->define(App\Organization::class, function (Faker $faker) {
    $type = OrganizationType::any();
    return [
        'name_en' => $faker->unique()->company,
        'address' => $faker->unique()->address,
        'zipcode' => $faker->postcode,
        'type' => $type,
        'subtype' => $type == OrganizationType::OTHER ? OrganizationSubType::any() : null,
    ];
});
