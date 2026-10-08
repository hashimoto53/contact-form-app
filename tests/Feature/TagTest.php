<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_manage_tags(): void
    {
        $tag = Tag::create(['name' => '未認証タグ']);

        $this->post('/admin/tags', ['name' => '新規'])->assertRedirect('/login');
        $this->get("/admin/tags/{$tag->id}/edit")->assertRedirect('/login');
        $this->put("/admin/tags/{$tag->id}", ['name' => '更新'])->assertRedirect('/login');
        $this->delete("/admin/tags/{$tag->id}")->assertRedirect('/login');
    }

    public function test_authenticated_user_can_crud_tags(): void
    {
        $user = User::factory()->create();

        // タグ作成
        $response = $this->actingAs($user)->post('/admin/tags', ['name' => '新機能']);
        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['name' => '新機能']);

        $tag = Tag::where('name', '新機能')->first();

        // 編集画面表示
        $this->actingAs($user)->get("/admin/tags/{$tag->id}/edit")->assertStatus(200);

        // タグ更新
        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", ['name' => '更新済み機能']);
        $response->assertRedirect('/admin');
        $this->assertDatabaseHas('tags', ['name' => '更新済み機能']);

        // タグ削除
        $response = $this->actingAs($user)->delete("/admin/tags/{$tag->id}");
        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_tag_validation_unique_and_required(): void
    {
        $user = User::factory()->create();
        Tag::create(['name' => '重複タグ']);

        // 空入力エラー
        $this->actingAs($user)->post('/admin/tags', ['name' => ''])
            ->assertSessionHasErrors(['name']);

        // 重複エラー
        $this->actingAs($user)->post('/admin/tags', ['name' => '重複タグ'])
            ->assertSessionHasErrors(['name']);
    }
}
