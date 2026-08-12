<?php

namespace Tests\Feature;

use App\Models\User;
use App\Mail\SmtpTestMail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
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

    public function test_super_admin_can_send_smtp_test_email(): void
    {
        Mail::fake();
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_active' => true, 'email_verified_at' => now()]);
        \App\Models\AppSetting::put('mail.password', 'app-password', true);

        $this->actingAs($superAdmin)
            ->post('/admin/settings/smtp/test', ['test_email' => 'target@example.com'])
            ->assertSessionHas('success');

        Mail::assertSent(SmtpTestMail::class, fn (SmtpTestMail $mail) => $mail->hasTo('target@example.com'));
    }

    public function test_smtp_test_requires_a_valid_email(): void
    {
        $superAdmin = User::factory()->create(['role' => 'super_admin', 'is_active' => true, 'email_verified_at' => now()]);

        $this->actingAs($superAdmin)
            ->from('/admin/settings')
            ->post('/admin/settings/smtp/test', ['test_email' => 'bukan-email'])
            ->assertRedirect('/admin/settings')
            ->assertSessionHasErrors('test_email');
    }
}
