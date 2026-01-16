@extends('layouts.app')

@section('content')
@php
$typeLabel = function($val){
    return match(strtolower((string)$val)){
        'internal'  => 'Pengembangan',
        'pengadaan' => 'Pengadaan',
        default     => '-',
    };
};

$badgeClass = function($code){
    return match(strtolower((string)$code)){
        'approved' => 'border-green-600 text-green-600 bg-green-50',
        'rejected' => 'border-red-600 text-red-600 bg-red-50',
        default    => 'border-slate-300 text-slate-600 bg-slate-50',
    };
};

$statusText = function($p){
    return match($p->status?->code){
        'pending_approver1' => 'Waiting GH CRV',
        'pending_iag'       => 'Waiting IAG',
        'pending_approver2' => 'Waiting GH IAG',
        'approved'          => 'Approved',
        'rejected'          => 'Rejected',
        default             => 'Waiting For Approval',
    };
};

/**
 * 🔥 FINAL — SESUAI DATABASE
 */
$canAct = function($p){
    $roleId = auth()->user()->role_id;
    $code   = $p->status?->code;

    // GH CRV
    if ($roleId === 4 && $code === 'pending_approver1') {
        return true;
    }

    // GH IAG
    if ($roleId === 14 && $code === 'pending_approver2') {
        return true;
    }

    return false;
};
@endphp

<div class="qpri-font">

{{-- ===================== PENDING ===================== --}}
<section class="bg-white rounded-lg border border-gray-200 mb-8">
<div class="overflow-x-auto">
<table class="w-full">
<thead class="bg-indigo-600 text-white">
<tr>
  <th class="px-6 py-4">ID</th>
  <th class="px-6 py-4">Name</th>
  <th class="px-6 py-4">Pemilik</th>
  <th class="px-6 py-4">Type</th>
  <th class="px-6 py-4">Created</th>
  <th class="px-6 py-4">Status</th>
</tr>
</thead>

<tbody class="divide-y">
@forelse($pengajuans as $p)
@php $t = data_get($p->meta,'tipe'); @endphp

<tr class="hover:bg-gray-50">
<td class="px-6 py-4">{{ $p->id }}</td>
<td class="px-6 py-4">{{ $p->judul }}</td>
<td class="px-6 py-4">{{ $p->maker?->name }}</td>
<td class="px-6 py-4">{{ $typeLabel($t) }}</td>
<td class="px-6 py-4">{{ $p->created_at->format('Y-m-d') }}</td>

<td class="px-6 py-4 space-y-2">

<span class="inline-flex px-4 py-1.5 text-xs rounded-full border {{ $badgeClass($p->status?->code) }}">
  {{ $statusText($p) }}
</span>

@if($canAct($p))
<div class="flex gap-2">
<form method="POST" action="{{ route('pengajuans.approve', $p) }}">
@csrf
<button class="px-3 py-1 text-xs rounded bg-green-600 text-white">Approve</button>
</form>

<form method="POST" action="{{ route('pengajuans.reject', $p) }}">
@csrf
<button class="px-3 py-1 text-xs rounded bg-red-600 text-white">Reject</button>
</form>
</div>
@endif

</td>
</tr>
@empty
<tr>
<td colspan="6" class="text-center py-8 text-gray-500">
Tidak ada data
</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</section>

</div>
@endsection