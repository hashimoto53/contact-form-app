<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CsvExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_export_csv(): void
    {
        $response = $this->get('/contacts/export');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_export_csv(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['content' => 'カテゴリ1']);

        Contact::create([
            'category_id' => $category->id,
            'first_name' => 'CSV太郎',
            'last_name' => 'テスト',
            'gender' => 1,
            'email' => 'csv@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => 'CSVテスト',
        ]);

        $response = $this->actingAs($user)->get('/contacts/export');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('CSV太郎', $response->streamedContent());
    }
}
