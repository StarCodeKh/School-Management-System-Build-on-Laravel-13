<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'Super Admin';
    case Admin = 'Admin';
    case NormalUser = 'Normal User';
    case Teacher = 'Teachers';
    case Student = 'Student';
    case Staff = 'Staff';
    case Client = 'Client';

    /** Display name shown in the UI. */
    public function label(): string
    {
        return match ($this) {
            self::Teacher => 'Teacher',
            default => $this->value,
        };
    }

    /** Roles allowed to manage other users. */
    public function isAdmin(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin], true);
    }

    /**
     * Roles a visitor may choose on the public register page.
     *
     * @return array<int, self>
     */
    public static function registrable(): array
    {
        return array_values(array_filter(self::cases(), fn (self $role) => ! $role->isAdmin()));
    }
}
