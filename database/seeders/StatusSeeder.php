<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Status;

class StatusSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'code'  => 'pending_approver1',
                'label' => 'Waiting by GH CRV',
            ],
            [
                'code'  => 'pending_iag',
                'label' => 'Waiting by IAG',
            ],
            [
                'code'  => 'pending_approver2',
                'label' => 'Waiting by GH IAG',
            ],
            [
                'code'  => 'approved',
                'label' => 'Approved',
            ],
            [
                'code'  => 'rejected',
                'label' => 'Rejected',
            ],
        ];

        foreach ($rows as $r) {
            Status::updateOrCreate(
                ['code' => $r['code']],
                ['label' => $r['label']]
            );
        }
    }
}