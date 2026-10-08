<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_admin(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_admin(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_admin_search_and_pagination(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['content' => 'テスト']);

        Contact::factory()->count(10)->create([
            'category_id' => $category->id,
            'first_name' => '検索太郎',
        ]);

        $response = $this->actingAs($user)->get('/admin?keyword=検索太郎');

        $response->assertStatus(200);
        $response->assertSee('検索太郎');
    }

    public function test_admin_show_contact_detail(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['content' => '特定カテゴリ']);
        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '詳細太郎',
            'last_name' => '佐藤',
            'gender' => 1,
            'email' => 'detail@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => '詳細画面のテスト',
        ]);

        $response = $this->actingAs($user)->get("/admin/contacts/{$contact->id}");

        $response->assertStatus(200);
        $response->assertSee('詳細太郎');
        $response->assertSee('特定カテゴリ');
    }

    public function test_admin_delete_contact(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['content' => 'テスト']);
        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '削除太郎',
            'last_name' => '鈴木',
            'gender' => 1,
            'email' => 'delete@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => '削除データ',
        ]);

        $response = $this->actingAs($user)->delete("/admin/contacts/{$contact->id}");

        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
