<?php

use function Pest\Laravel\postJson;

test("can register", function () {
    $data = [
        "password" => fake()->password(minLength: 5),
        "email" => fake()->unique()->email(),
        "name" => fake()->name()
    ];
    postJson("/api/register", $data)->assertOk();
});
