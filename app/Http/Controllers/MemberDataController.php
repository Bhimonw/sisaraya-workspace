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
            'skills.*.nama_skill' => ['required_with:skills', 'string', 'max:255'],
            'skills.*.tingkat_keahlian' => ['required_with:skills.*.nama_skill', 'in:pemula,menengah,mahir,expert'],
            'skills.*.deskripsi' => ['nullable', 'string'],

            'modals' => ['nullable', 'array'],
            'modals.*.jenis' => ['required_with:modals', 'in:uang,alat'],
            'modals.*.nama_item' => ['required_with:modals', 'string', 'max:255'],
            'modals.*.jumlah_uang' => ['nullable', 'numeric', 'min:0'],
            'modals.*.deskripsi' => ['nullable', 'string'],
            'modals.*.dapat_dipinjam' => ['nullable', 'boolean'],

            'links' => ['nullable', 'array'],
            'links.*.nama' => ['required_with:links', 'string', 'max:255'],
            'links.*.bidang' => ['nullable', 'string', 'max:255'],
            'links.*.url' => ['nullable', 'url', 'max:500'],
            'links.*.contact' => ['nullable', 'string', 'max:255'],
        ], [
            'skills.*.nama_skill.required_with' => 'Nama keahlian wajib diisi',
            'skills.*.tingkat_keahlian.required_with' => 'Tingkat keahlian wajib dipilih',
            'skills.*.tingkat_keahlian.in' => 'Tingkat keahlian tidak valid',
            'modals.*.jenis.required_with' => 'Jenis modal wajib dipilih',
            'modals.*.jenis.in' => 'Jenis modal tidak valid',
            'modals.*.nama_item.required_with' => 'Nama item wajib diisi',
            'modals.*.jumlah_uang.numeric' => 'Jumlah uang harus berupa angka',
            'modals.*.jumlah_uang.min' => 'Jumlah uang tidak boleh negatif',
            'links.*.nama.required_with' => 'Nama orang/pemilik wajib diisi',
            'links.*.url.url' => 'Format URL tidak valid',
        ]);

        $user = Auth::user();
        $dataAdded = false;

        try {
            // Store skills
            if (!empty($validated['skills'])) {
                foreach ($validated['skills'] as $skill) {
                    $user->skills()->create($skill);
                    $dataAdded = true;
                }
            }

            // Store modals
            if (!empty($validated['modals'])) {
                foreach ($validated['modals'] as $modal) {
                    // Convert dapat_dipinjam to boolean
                    $modal['dapat_dipinjam'] = isset($modal['dapat_dipinjam']) ? true : false;
                    $user->modals()->create($modal);
                    $dataAdded = true;
                }
            }

            // Store links
            if (!empty($validated['links'])) {
                foreach ($validated['links'] as $link) {
                    $user->links()->create($link);
                    $dataAdded = true;
                }
            }

            // Only notify if data was actually added
            if ($dataAdded) {
                $this->notifySekretaris($user, 'Data baru ditambahkan');
                return redirect()->route('member-data.index')
                    ->with('status', 'Data berhasil disimpan dan telah dikirim ke sekretaris!');
            }

            return redirect()->route('member-data.index')
                ->with('status', 'Tidak ada data yang ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('member-data.index')
                ->with('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }

    /**
     * Update specific entry
     */
    public function update(Request $request, string $type, int $id)
    {
        $user = Auth::user();

        try {
            switch ($type) {
                case 'skill':
                    $item = MemberSkill::where('user_id', $user->id)->findOrFail($id);
                    $validated = $request->validate([
                        'nama_skill' => ['required', 'string', 'max:255'],
                        'tingkat_keahlian' => ['required', 'in:pemula,menengah,mahir,expert'],
                        'deskripsi' => ['nullable', 'string'],
                    ], [
                        'nama_skill.required' => 'Nama keahlian wajib diisi',
                        'tingkat_keahlian.required' => 'Tingkat keahlian wajib dipilih',
                        'tingkat_keahlian.in' => 'Tingkat keahlian tidak valid',
                    ]);
                    break;

                case 'modal':
                    $item = MemberModal::where('user_id', $user->id)->findOrFail($id);
                    $validated = $request->validate([
                        'jenis' => ['required', 'in:uang,alat'],
                        'nama_item' => ['required', 'string', 'max:255'],
                        'jumlah_uang' => ['nullable', 'numeric', 'min:0'],
                        'deskripsi' => ['nullable', 'string'],
                        'dapat_dipinjam' => ['nullable', 'boolean'],
                    ], [
                        'jenis.required' => 'Jenis modal wajib dipilih',
                        'nama_item.required' => 'Nama item wajib diisi',
                        'jumlah_uang.numeric' => 'Jumlah uang harus berupa angka',
                        'jumlah_uang.min' => 'Jumlah uang tidak boleh negatif',
                    ]);
                    // Convert checkbox to boolean
                    $validated['dapat_dipinjam'] = isset($validated['dapat_dipinjam']) ? true : false;
                    break;

                case 'link':
                    $item = MemberLink::where('user_id', $user->id)->findOrFail($id);
                    $validated = $request->validate([
                        'nama' => ['required', 'string', 'max:255'],
                        'bidang' => ['nullable', 'string', 'max:255'],
                        'url' => ['nullable', 'url', 'max:500'],
                        'contact' => ['nullable', 'string', 'max:255'],
                    ], [
                        'nama.required' => 'Nama orang/pemilik wajib diisi',
                        'url.url' => 'Format URL tidak valid',
                    ]);
                    break;

                default:
                    abort(404);
            }

            $item->update($validated);

            $this->notifySekretaris($user, 'Data diperbarui');

            return back()->with('status', 'Data berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
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
