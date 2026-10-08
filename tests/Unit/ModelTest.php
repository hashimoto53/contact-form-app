<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_contacts(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);
        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => 'テスト内容',
        ]);

        $this->assertTrue($category->contacts->contains($contact));
    }

    public function test_contact_belongs_to_category_and_syncs_tags(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);
        $tag1 = Tag::create(['name' => 'タグ1']);
        $tag2 = Tag::create(['name' => 'タグ2']);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => 'テスト内容',
        ]);

        $this->assertEquals($category->id, $contact->category->id);

        $contact->tags()->sync([$tag1->id, $tag2->id]);
        $this->assertCount(2, $contact->fresh()->tags);
    }

    public function test_tag_belongs_to_many_contacts(): void
    {
        $category = Category::create(['content' => 'テストカテゴリ']);
        $tag = Tag::create(['name' => '共有タグ']);

        $contact = Contact::create([
            'category_id' => $category->id,
            'first_name' => '太郎',
            'last_name' => '山田',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都',
            'detail' => 'テスト内容',
        ]);

        $contact->tags()->attach($tag->id);
        $this->assertTrue($tag->contacts->contains($contact));
    }
}
