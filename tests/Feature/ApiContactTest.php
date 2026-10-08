<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_index_returns_paginated_contacts(): void
    {
        $category = Category::create(['content' => 'APIカテゴリ']);
        Contact::factory()->count(5)->create(['category_id' => $category->id]);

        $response = $this->getJson('/api/v1/contacts');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    public function test_api_show_returns_contact_detail_or_404(): void
    {
        $category = Category::create(['content' => 'APIカテゴリ']);
        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => 'API詳細',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'apishow@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => '内容',
        ]);

        // 正常取得
        $this->getJson("/api/v1/contacts/{$contact->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $contact->id);

        // 存在しないID
        $this->getJson('/api/v1/contacts/99999')
            ->assertStatus(404);
    }

    public function test_api_store_creates_contact(): void
    {
        $category = Category::create(['content' => 'APIカテゴリ']);
        $tag = Tag::create(['name' => 'APIタグ']);

        $data = [
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '新規',
            'gender' => 1,
            'email' => 'apistore@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => 'APIからの新規投稿です。',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->postJson('/api/v1/contacts', $data);

        $response->assertStatus(201)
            ->assertJsonPath('data.email', 'apistore@example.com');

        $this->assertDatabaseHas('contacts', ['email' => 'apistore@example.com']);
    }

    public function test_api_store_validation_fails(): void
    {
        $response = $this->postJson('/api/v1/contacts', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'gender', 'email', 'tel', 'address', 'category_id', 'detail']);
    }

    public function test_api_update_modifies_contact(): void
    {
        $category = Category::create(['content' => 'APIカテゴリ']);
        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '更新前',
            'gender' => 1,
            'email' => 'before@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => '内容',
        ]);

        $data = [
            'category_id' => $category->id,
            'first_name' => '二郎',
            'last_name' => '更新後',
            'gender' => 1,
            'email' => 'after@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => '更新後の内容です。',
        ];

        $response = $this->putJson("/api/v1/contacts/{$contact->id}", $data);

        $response->assertStatus(200)
            ->assertJsonPath('data.email', 'after@example.com');
    }

    public function test_api_destroy_deletes_contact(): void
    {
        $category = Category::create(['content' => 'APIカテゴリ']);
        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '削除',
            'last_name' => '対象',
            'gender' => 1,
            'email' => 'apidelete@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => '内容',
        ]);

        $response = $this->deleteJson("/api/v1/contacts/{$contact->id}");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }
}
