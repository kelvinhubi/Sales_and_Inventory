<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'Role' => 'manager',
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('Test User', $user->name);
        $this->assertEquals('test@example.com', $user->email);
    }

    public function test_user_password_is_hashed()
    {
        $user = User::create([
            'name' => 'Hash Test',
            'email' => 'hash@example.com',
            'password' => Hash::make('plainpassword'),
            'Role' => 'owner',
        ]);

        $this->assertNotEquals('plainpassword', $user->password);
        $this->assertTrue(Hash::check('plainpassword', $user->password));
    }

    public function test_user_has_correct_attributes()
    {
        $user = User::create([
            'name' => 'Attribute Test',
            'email' => 'attr@example.com',
            'password' => Hash::make('password'),
            'Role' => 'manager',
        ]);

        $this->assertNotNull($user->id);
        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
        $this->assertEquals('manager', $user->Role);
    }
}
