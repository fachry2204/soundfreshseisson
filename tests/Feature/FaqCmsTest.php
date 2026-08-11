<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_editor_can_publish_faq_and_change_is_audited(): void
    {
        $editor = User::factory()->create(['role' => 'content_editor', 'email_verified_at' => now()]);
        $this->assertSame('content_editor', $editor->role);
        $this->assertTrue((bool) $editor->is_active);
        $this->actingAs($editor)->post('/admin/faqs', ['question' => 'Pertanyaan?', 'answer' => 'Jawaban yang jelas.', 'sort_order' => 1, 'is_active' => true])->assertRedirect();
        $this->assertDatabaseHas('faqs', ['question' => 'Pertanyaan?', 'is_active' => true]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'faq.created', 'actor_id' => $editor->id]);
    }

    public function test_viewer_cannot_modify_faq(): void
    {
        $viewer = User::factory()->create(['role' => 'viewer', 'email_verified_at' => now()]);
        $this->actingAs($viewer)->post('/admin/faqs', ['question' => 'Tidak boleh', 'answer' => 'No', 'sort_order' => 1])->assertForbidden();
    }
}
