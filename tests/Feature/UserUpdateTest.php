<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:', 'app.url' => 'http://localhost']);
        DB::purge('sqlite');
        app('url')->forceRootUrl('http://localhost');
        (require database_path('migrations/2014_10_12_000000_create_users_table.php'))->up();
        (require database_path('migrations/2024_07_05_125042_create_permission_tables.php'))->up();
        Role::create(['name' => 'Anterior', 'guard_name' => 'web']);
        Role::create(['name' => 'Nuevo', 'guard_name' => 'web']);
    }

    public function test_role_changes_preserve_password_when_empty_or_omitted(): void
    {
        $user = User::create(['name' => 'Usuario', 'email' => 'usuario@example.test', 'password' => Hash::make('Original123')]);
        $hash = $user->password;
        foreach ([['password' => ''], []] as $password) {
            $user->syncRoles(['Anterior']);
            $this->actingAs($user)->put(route('users.update', $user), array_merge([
                'name' => $user->name, 'email' => $user->email, 'roles' => ['Nuevo'],
            ], $password))->assertRedirect()->assertSessionHasNoErrors();
            $user->refresh();
            $this->assertSame($hash, $user->password);
            $this->assertTrue($user->hasRole('Nuevo'));
            $this->assertFalse($user->hasRole('Anterior'));
        }
    }

    public function test_entered_password_is_updated(): void
    {
        $user = User::create(['name' => 'Usuario', 'email' => 'usuario@example.test', 'password' => Hash::make('Original123')]);
        $this->actingAs($user)->put(route('users.update', $user), [
            'name' => $user->name, 'email' => $user->email, 'roles' => ['Nuevo'], 'password' => 'NuevaClave123',
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NuevaClave123', $user->fresh()->password));
        $this->assertFalse(Hash::check('Original123', $user->fresh()->password));
    }
}
