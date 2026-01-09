<?php

namespace App\Support;

class RoleLabel
{
    public static function label(?string $roleName): string
    {
        $r = strtolower(trim($roleName ?? ''));

        return match ($r) {
            'maker'     => 'CRV',
            'approver'  => 'ADMIN',
            'approver2' => 'GH IAG',
            'admin'     => 'GH CRV',
            default     => ucfirst($r ?: '-'),
        };
    }
}