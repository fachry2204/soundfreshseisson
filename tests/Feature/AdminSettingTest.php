<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_open_settings(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_active' => true, 'email_verified_at' => now()]);

        $this->actingAs($superAdmin)->get('/admin/settings')->assertOk();
    }

    public function test_regular_admin_cannot_open_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_active' => true, 'email_verified_at' => now()]);

        $this->actingAs($admin)->get('/admin/settings')->assertForbidden();
    }
}
