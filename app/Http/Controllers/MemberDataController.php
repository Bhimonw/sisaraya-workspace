<?php

namespace App\Http\Controllers;

use App\Models\MemberSkill;
use App\Models\MemberModal;
use App\Models\MemberLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberDataController extends Controller
{
    /**
     * Display member's own data
     */
    public function index()
    {
        $user = Auth::user();
        $skills = $user->skills()->get();
        $modals = $user->modals()->get();
        $links = $user->links()->get();

        return view('member-data.index', compact('user', 'skills', 'modals', 'links'));
    }

    /**
     * Show form for entering member data
     */
    public function create()
    {
        return view('member-data.form');
    }

    /**
     * Store member data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'skills' => ['nullable', 'array'],
            'skills.*.nama_skill' => ['required', 'string', 'max:255'],
            'skills.*.tingkat_keahlian' => ['required', 'in:pemula,menengah,mahir,expert'],
            'skills.*.deskripsi' => ['nullable', 'string'],

            'modals' => ['nullable', 'array'],
            'modals.*.jenis' => ['required', 'in:uang,alat'],
            'modals.*.nama_item' => ['required', 'string', 'max:255'],
            'modals.*.jumlah_uang' => ['nullable', 'numeric', 'min:0'],
            'modals.*.deskripsi' => ['nullable', 'string'],
            'modals.*.dapat_dipinjam' => ['boolean'],

            'links' => ['nullable', 'array'],
            'links.*.nama' => ['required', 'string', 'max:255'],
            'links.*.bidang' => ['nullable', 'string', 'max:255'],
            'links.*.url' => ['nullable', 'url', 'max:500'],
            'links.*.contact' => ['nullable', 'string', 'max:255'],
        ]);

        $user = Auth::user();

        // Store skills
        if (!empty($validated['skills'])) {
            foreach ($validated['skills'] as $skill) {
                $user->skills()->create($skill);
            }
        }

        // Store modals
        if (!empty($validated['modals'])) {
            foreach ($validated['modals'] as $modal) {
                $user->modals()->create($modal);
            }
        }

        // Store links
        if (!empty($validated['links'])) {
            foreach ($validated['links'] as $link) {
                $user->links()->create($link);
            }
        }

        // Notify sekretaris
        $this->notifySekretaris($user, 'Data baru ditambahkan');

        return redirect()->route('member-data.index')
            ->with('status', 'Data berhasil disimpan dan telah dikirim ke sekretaris!');
    }

    /**
     * Update specific entry
     */
    public function update(Request $request, string $type, int $id)
    {
        $user = Auth::user();

        switch ($type) {
            case 'skill':
                $item = MemberSkill::where('user_id', $user->id)->findOrFail($id);
                $validated = $request->validate([
                    'nama_skill' => ['required', 'string', 'max:255'],
                    'tingkat_keahlian' => ['required', 'in:pemula,menengah,mahir,expert'],
                    'deskripsi' => ['nullable', 'string'],
                ]);
                break;

            case 'modal':
                $item = MemberModal::where('user_id', $user->id)->findOrFail($id);
                $validated = $request->validate([
                    'jenis' => ['required', 'in:uang,alat'],
                    'nama_item' => ['required', 'string', 'max:255'],
                    'jumlah_uang' => ['nullable', 'numeric', 'min:0'],
                    'deskripsi' => ['nullable', 'string'],
                    'dapat_dipinjam' => ['boolean'],
                ]);
                break;

            case 'link':
                $item = MemberLink::where('user_id', $user->id)->findOrFail($id);
                $validated = $request->validate([
                    'nama' => ['required', 'string', 'max:255'],
                    'bidang' => ['nullable', 'string', 'max:255'],
                    'url' => ['nullable', 'url', 'max:500'],
                    'contact' => ['nullable', 'string', 'max:255'],
                ]);
                break;

            default:
                abort(404);
        }

        $item->update($validated);

        $this->notifySekretaris($user, 'Data diperbarui');

        return back()->with('status', 'Data berhasil diperbarui!');
    }

    /**
     * Delete specific entry
     */
    public function destroy(string $type, int $id)
    {
        $user = Auth::user();

        switch ($type) {
            case 'skill':
                $item = MemberSkill::where('user_id', $user->id)->findOrFail($id);
                break;
            case 'modal':
                $item = MemberModal::where('user_id', $user->id)->findOrFail($id);
                break;
            case 'link':
                $item = MemberLink::where('user_id', $user->id)->findOrFail($id);
                break;
            default:
                abort(404);
        }

        $item->delete();

        return back()->with('status', 'Data berhasil dihapus!');
    }

    /**
     * Send notification to sekretaris role
     */
    private function notifySekretaris($user, $message)
    {
        $sekretaris = User::role('sekretaris')->get();

        foreach ($sekretaris as $s) {
            $s->notify(new \App\Notifications\MemberDataNotification($user, $message));
        }
    }
}
