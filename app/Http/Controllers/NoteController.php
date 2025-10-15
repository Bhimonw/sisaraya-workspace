<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function index()
    {
        $notes = auth()->user()->notes()
            ->orderBy('is_pinned', 'desc')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('notes.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'color' => 'required|in:yellow,blue,green,red,purple',
        ]);

        $note = auth()->user()->notes()->create($validated);

        return redirect()->route('notes.index')
            ->with('success', 'Catatan berhasil dibuat');
    }

    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'color' => 'required|in:yellow,blue,green,red,purple',
        ]);

        $note->update($validated);

        return redirect()->route('notes.index')
            ->with('success', 'Catatan berhasil diperbarui');
    }

    public function destroy(Note $note)
    {
        $this->authorize('delete', $note);

        $note->delete();

        return redirect()->route('notes.index')
            ->with('success', 'Catatan berhasil dihapus');
    }

    public function togglePin(Note $note)
    {
        $this->authorize('update', $note);

        $note->update(['is_pinned' => !$note->is_pinned]);

        return back()->with('success', $note->is_pinned ? 'Catatan disematkan' : 'Catatan dilepas');
    }
}
