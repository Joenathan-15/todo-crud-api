<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test("can Login",function(){
    Sanctum::actingAs(
        User::factory()->create()
    );

    $this->get("/api/user")->assertOk();
});
