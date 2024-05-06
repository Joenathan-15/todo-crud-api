<?php

use App\Models\Todo;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('can create', function () {
    $data = [
        "content" => fake()->sentence(1),
    ];
    Sanctum::actingAs(
        User::factory()->create()
    );
    $this->post("/api/todo", $data)->assertOk();
});

test("can fetch", function () {
    Sanctum::actingAs(
        User::factory()->create()
    );
    $this->get("/api/todo")->assertOk();
});

test("can patch", function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $todo = Todo::factory()->create([
        "created_by" => $user->id
    ]);
    $this->patch("/api/todo/" . $todo->id)->assertOk();
});

test("can delete", function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);
    $todo = Todo::factory()->create([
        "created_by" => $user->id
    ]);
    $this->delete("/api/todo/" . $todo->id)->assertOk();
});
