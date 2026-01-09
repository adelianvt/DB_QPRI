<?php

namespace App\Http\Controllers;

use App\Models\Pengajuan;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'total' => Pengajuan::count(),
            'approved' => Pengajuan::whereHas('status', fn($q)=>$q->where('code','approved'))->count(),
            'waiting' => Pengajuan::whereHas('status', fn($q)=>$q->whereNotIn('code',['approved','rejected','draft']))->count(),
            'rejected' => Pengajuan::whereHas('status', fn($q)=>$q->where('code','rejected'))->count(),
        ]);
    }

    public function pengajuans()
    {
        return view('admin.pengajuans', [
            'pengajuans' => Pengajuan::latest()->paginate(10)
        ]);
    }

    public function show(Pengajuan $pengajuan)
    {
        return view('pengajuan.show', compact('pengajuan'));
    }

    public function edit(Pengajuan $pengajuan)
    {
        return view('admin.edit', compact('pengajuan'));
    }

    public function update(Request $request, Pengajuan $pengajuan)
    {
        $pengajuan->update($request->all());
        return redirect()->route('admin.pengajuans')
            ->with('success','Pengajuan berhasil diupdate');
    }

    public function destroy(Pengajuan $pengajuan)
    {
        $pengajuan->delete();
        return back()->with('success','Pengajuan berhasil dihapus');
    }
}