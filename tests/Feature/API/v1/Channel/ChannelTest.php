<?php

namespace Test\Feature\API\v1\Channel;

use App\Http\Controllers\API\V01\Channel\ChannelController;
use App\Models\Channel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ChannelTest extends TestCase
{
    use RefreshDatabase;

    public function registerRolesAndPermissions()
    {
        $roleInDatabase = Role::where('name', config('permission.default_roles')[0]);
        if ($roleInDatabase->count() < 1) {
            foreach (config('permission.default_roles') as $role) {
                Role::create(['name' => $role]);
            }
        } else {
//            That is, these roles were pre-built.
        }

        $permissionInDatabase = Permission::where('name', config('permission.default_permissions')[0]);
        if ($permissionInDatabase->count() < 1) {
            foreach (config('permission.default_permissions') as $permission) {
                Permission::create(['name' => $permission]);
            }
        } else {
//            That is, these permissions were pre-built.
        }
    }

    /**
     * Test all Channel list should be accessible.
     */
    public function test_all_channels_list_should_be_accessible()
    {
        $response = $this->get(route('channels.index'));

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Test create channel
     */
    public function test_create_channel_should_be_validated()
    {
        $this->registerRolesAndPermissions();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $user->givePermissionTo('channel management');
        $response = $this->postJson(route('channels.store'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function test_create_new_channel()
    {
        $this->registerRolesAndPermissions();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $user->givePermissionTo('channel management');
        $response = $this->postJson(route('channels.store'), [
            'name' => 'laravel'
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    /**
     * Test Update Channel
     */
    public function test_channel_update_should_be_validated()
    {
        $this->registerRolesAndPermissions();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $user->givePermissionTo('channel management');
        $response = $this->json('PUT', route('channels.update'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function test_channel_update()
    {
        $this->registerRolesAndPermissions();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $user->givePermissionTo('channel management');

        $channel = Channel::factory(['name' => 'laravel'])->create();
        $response = $this->json('PUT', route('channels.update'), [
            'id' => $channel->id,
            'name' => 'react.js'
        ]);
        $updatedChannel = Channel::find($channel->id);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals('react.js', $updatedChannel->name);
    }

    /**
     * Test Destroy channel
     */
    public function test_channel_destroy_should_be_validated()
    {
        $this->registerRolesAndPermissions();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $user->givePermissionTo('channel management');

        $response = $this->json('DELETE', route('channels.destroy'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     */
    public function test_channel_destroy()
    {
        $this->registerRolesAndPermissions();

        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $user->givePermissionTo('channel management');
        $channel = Channel::factory()->create();
        $response = $this->json('DELETE', route('channels.destroy'), ['id' => $channel->id]);

        $response->assertStatus(Response::HTTP_OK);

        $this->assertTrue(Channel::find($channel->id) == null);
    }
}
