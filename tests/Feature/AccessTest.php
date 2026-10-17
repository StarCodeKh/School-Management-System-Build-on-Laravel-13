<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AccessTest extends TestCase
{
    use RefreshDatabase;

    public static function protectedPostRoutes(): array
    {
        return [
            ['student/add/save'], ['student/update'], ['student/delete'],
            ['subject/save'], ['subject/update'], ['subject/delete'],
            ['teacher/delete'],
            ['invoice/add/save'], ['invoice/update/save'], ['invoice/delete'],
            ['change/password'],
        ];
    }

    #[DataProvider('protectedPostRoutes')]
    public function test_guests_cannot_change_data(string $uri): void
    {
        $this->withoutMiddleware(ValidateCsrfToken::class)
            ->post('/'.$uri)
            ->assertRedirect('/login');
    }

    public function test_guest_cannot_delete_a_student(): void
    {
        $student = Student::create([
            'first_name' => 'Sok', 'last_name' => 'Dara', 'gender' => 'Male', 'date_of_birth' => '2010-01-01',
            'roll' => '1', 'blood_group' => 'O+', 'religion' => 'Buddhism', 'email' => 'sok@example.com',
            'class' => '10', 'section' => 'A', 'admission_id' => 'ADM-1', 'phone_number' => '012345678',
        ]);

        $this->withoutMiddleware(ValidateCsrfToken::class)
            ->post('/student/delete', ['id' => $student->id]);

        $this->assertModelExists($student);
    }

    public function test_user_can_change_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/change/password', [
            'current_password' => 'password',
            'new_password' => 'NewPassword@123',
            'new_confirm_password' => 'NewPassword@123',
        ])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('NewPassword@123', $user->fresh()->password));
    }

    public function test_change_password_rejects_wrong_current_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/change/password', [
            'current_password' => 'wrong-password',
            'new_password' => 'NewPassword@123',
            'new_confirm_password' => 'NewPassword@123',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }

    public function test_visitor_cannot_register_as_admin(): void
    {
        $this->post('/register', [
            'name' => 'Intruder',
            'email' => 'intruder@example.com',
            'role_name' => Role::SuperAdmin->value,
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
        ])->assertSessionHasErrors('role_name');

        $this->assertDatabaseMissing('users', ['email' => 'intruder@example.com']);
    }
}
