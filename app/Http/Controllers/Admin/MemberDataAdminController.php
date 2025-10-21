<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MemberSkill;
use App\Models\MemberModal;
use App\Models\MemberLink;
use Illuminate\Http\Request;

class MemberDataAdminController extends Controller
{
    /**
     * Display all members with their data (sekretaris only)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $users = User::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            })
            ->with(['skills', 'modals', 'links', 'roles'])
            ->withCount(['skills', 'modals', 'links'])
            ->paginate(20);

        return view('admin.member-data.index', compact('users', 'search'));
    }

    /**
     * Display specific member's detailed data
     */
    public function show(User $user)
    {
        $skills = $user->skills()->get();
        $modals = $user->modals()->get();
        $links = $user->links()->get();

        return view('admin.member-data.show', compact('user', 'skills', 'modals', 'links'));
    }

    /**
     * Export member data to CSV
     */
    public function export()
    {
        $users = User::with(['skills', 'modals', 'links', 'roles'])->get();

        $filename = 'member-data-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // CSV Headers
        fputcsv($handle, ['Nama', 'Username', 'Phone', 'WhatsApp', 'Role', 'Skills', 'Modal', 'Links']);

        foreach ($users as $user) {
            $skills = $user->skills->pluck('nama_skill')->implode(', ');
            $modals = $user->modals->pluck('nama_item')->implode(', ');
            $links = $user->links->pluck('nama')->implode(', ');
            $roles = $user->roles->pluck('name')->implode(', ');

            fputcsv($handle, [
                $user->name,
                $user->username,
                $user->phone ?? '-',
                $user->whatsapp ?? '-',
                $roles,
                $skills ?: '-',
                $modals ?: '-',
                $links ?: '-',
            ]);
        }

        fclose($handle);
        exit;
    }
}
