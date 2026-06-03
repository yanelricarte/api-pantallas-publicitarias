<?php

namespace Tests\Feature;

use App\Models\Display;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisplayOwnershipTest extends TestCase
{
    use RefreshDatabase;

    private function tokenFor(User $user): string
    {
        return auth('api')->login($user);
    }

    public function test_un_usuario_ve_su_propia_pantalla(): void
    {
        $user = User::factory()->create();
        $display = Display::factory()->for($user)->create();

        $this->withToken($this->tokenFor($user))
            ->getJson("/api/displays/{$display->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $display->id]);
    }

    public function test_un_usuario_no_puede_ver_la_pantalla_de_otro(): void
    {
        $owner = User::factory()->create();
        $otro = User::factory()->create();
        $display = Display::factory()->for($owner)->create();

        $this->withToken($this->tokenFor($otro))
            ->getJson("/api/displays/{$display->id}")
            ->assertStatus(403);
    }

    public function test_un_usuario_no_puede_editar_la_pantalla_de_otro(): void
    {
        $owner = User::factory()->create();
        $otro = User::factory()->create();
        $display = Display::factory()->for($owner)->create();

        $this->withToken($this->tokenFor($otro))
            ->putJson("/api/displays/{$display->id}", [
                'name' => 'Hackeada',
                'description' => 'intento de edición ajena',
                'price_per_day' => 1,
                'resolution_height' => 720,
                'resolution_width' => 1280,
                'type' => 'indoor',
            ])
            ->assertStatus(403);

        $this->assertDatabaseMissing('displays', [
            'id' => $display->id,
            'name' => 'Hackeada',
        ]);
    }

    public function test_un_usuario_no_puede_borrar_la_pantalla_de_otro(): void
    {
        $owner = User::factory()->create();
        $otro = User::factory()->create();
        $display = Display::factory()->for($owner)->create();

        $this->withToken($this->tokenFor($otro))
            ->deleteJson("/api/displays/{$display->id}")
            ->assertStatus(403);

        $this->assertDatabaseHas('displays', ['id' => $display->id]);
    }

    public function test_sin_token_no_se_puede_acceder(): void
    {
        $display = Display::factory()->create();

        $this->getJson("/api/displays/{$display->id}")
            ->assertStatus(401);
    }
}
