<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     * Accessible by: Owner (Manage), Cashier (Read Only - via separate route/view or logic here)
     * For now, this is the Management page for Owners.
     */
    public function index()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('announcements.index', compact('announcements'));
    }

    /**
     * Tampilkan halaman papan pengumuman untuk semua user (Public/Wall).
     */
    public function list()
    {
        $announcements = Announcement::latest()->get();
        return view('pos.announcements.index', compact('announcements'));
    }

    public function create()
    {
        return view('announcements.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'label' => 'required|string|in:Info,Promo,Penting',
            'is_active' => 'boolean'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $request->has('is_active');

        Announcement::create($validated);

        return redirect()->route('announcements.manage.index')->with('success', 'Pengumuman berhasil dibuat.');
    }

    public function edit(Announcement $announcement)
    {
        return view('announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'label' => 'required|string|in:Info,Promo,Penting',
            'is_active' => 'boolean'
        ]);
        
        $validated['is_active'] = $request->has('is_active');

        $announcement->update($validated);

        return redirect()->route('announcements.manage.index')->with('success', 'Pengumuman diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return redirect()->route('announcements.manage.index')->with('success', 'Pengumuman dihapus.');
    }
}
