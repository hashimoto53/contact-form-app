<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_page_displays_categories_and_tags(): void
    {
        $category = Category::create(['content' => '商品のお届けについて']);
        $tag = Tag::create(['name' => '質問']);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('categories');
        $response->assertViewHas('tags');
        $response->assertSee('商品のお届けについて');
        $response->assertSee('質問');
    }

    public function test_thanks_page_displays_successfully(): void
    {
        $response = $this->get('/thanks');
        $response->assertStatus(200);
    }

    public function test_confirm_page_with_valid_data(): void
    {
        $category = Category::create(['content' => '商品のお届けについて']);

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
        ];

        $response = $this->post('/contacts/confirm', $data);

        $response->assertStatus(200);
        $response->assertViewIs('contact.confirm');
        $response->assertSee('山田');
        $response->assertSee('太郎');
    }

    public function test_confirm_fails_with_invalid_data(): void
    {
        $response = $this->post('/contacts/confirm', []);
        $response->assertSessionHasErrors(['first_name', 'last_name', 'gender', 'email', 'tel', 'address', 'category_id', 'detail']);
    }

    public function test_store_contact_creates_record_and_redirects_to_thanks(): void
    {
        $category = Category::create(['content' => '商品のお届けについて']);
        $tag = Tag::create(['name' => '重要']);

        $data = [
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区1-1-1',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'お問い合わせ内容です。',
            'tag_ids' => [$tag->id],
        ];

        $response = $this->post('/contacts', $data);

        $response->assertRedirect('/thanks');
        $this->assertDatabaseHas('contacts', ['email' => 'test@example.com']);
        $this->assertDatabaseHas('contact_tag', ['tag_id' => $tag->id]);
    }
}
