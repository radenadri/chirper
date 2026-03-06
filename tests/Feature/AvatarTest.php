<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Volt;
use Tests\TestCase;

class AvatarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_profile_page_shows_avatar_upload_component(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response
            ->assertOk()
            ->assertSeeVolt('profile.update-avatar-form');
    }

    public function test_user_can_upload_avatar(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $file = UploadedFile::fake()->image('avatar.jpg', 100, 100);

        Volt::test('profile.update-avatar-form')
            ->set('avatar', $file)
            ->call('saveAvatar')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertNotNull($user->avatar_path);
        $this->assertTrue(Storage::disk('public')->exists($user->avatar_path));
    }

    public function test_user_can_remove_avatar(): void
    {
        $user = User::factory()->withAvatar()->create();

        $this->actingAs($user);

        Volt::test('profile.update-avatar-form')
            ->call('removeAvatar')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertNull($user->avatar_path);
    }

    public function test_replacing_avatar_deletes_old_file(): void
    {
        $user = User::factory()->withAvatar()->create();
        $oldPath = $user->avatar_path;

        $this->actingAs($user);

        $file = UploadedFile::fake()->image('new-avatar.jpg', 100, 100);

        Volt::test('profile.update-avatar-form')
            ->set('avatar', $file)
            ->call('saveAvatar')
            ->assertHasNoErrors();

        $user->refresh();

        $this->assertFalse(Storage::disk('public')->exists($oldPath));
        $this->assertTrue(Storage::disk('public')->exists($user->avatar_path));
    }

    public function test_avatar_url_returns_uploaded_path_when_avatar_exists(): void
    {
        $user = User::factory()->withAvatar()->create();

        $url = $user->avatarUrl(80);

        $this->assertStringContainsString('/storage/', $url);
        $this->assertStringContainsString($user->avatar_path, $url);
    }

    public function test_avatar_url_falls_back_to_gravatar_when_no_upload(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $url = $user->avatarUrl(80);

        $this->assertStringContainsString('gravatar.com', $url);
        $this->assertStringContainsString(md5('test@example.com'), $url);
    }

    public function test_upload_rejects_non_image_file(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        Volt::test('profile.update-avatar-form')
            ->set('avatar', $file)
            ->call('saveAvatar')
            ->assertHasErrors('avatar');
    }

    public function test_upload_rejects_file_exceeding_2mb(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $file = UploadedFile::fake()->image('large.jpg')->size(2049);

        Volt::test('profile.update-avatar-form')
            ->set('avatar', $file)
            ->call('saveAvatar')
            ->assertHasErrors('avatar');
    }

    public function test_unauthenticated_user_cannot_access_profile_with_avatar_upload(): void
    {
        $response = $this->get('/profile');

        $response->assertRedirect();
    }

    public function test_chirps_list_renders_avatar(): void
    {
        $user = User::factory()->create();
        $user->chirps()->create(['message' => 'Test chirp']);
        $this->actingAs($user);

        $response = $this->get(route('chirps'));

        $response->assertOk();
        $this->assertStringContainsString('gravatar.com', $response->getContent());
    }
}
