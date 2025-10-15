<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\VoteOption;
use App\Models\VoteResponse;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    public function index()
    {
        $activeVotes = Vote::where('status', 'active')
            ->where(function($q) {
                $q->whereNull('closes_at')
                  ->orWhere('closes_at', '>', now());
            })
            ->with(['creator', 'options', 'responses'])
            ->latest()
            ->get();

        $closedVotes = Vote::where('status', 'closed')
            ->orWhere(function($q) {
                $q->where('closes_at', '<=', now());
            })
            ->with(['creator', 'options', 'responses'])
            ->latest()
            ->get();

        return view('votes.index', compact('activeVotes', 'closedVotes'));
    }

    public function create()
    {
        return view('votes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'allow_multiple' => 'boolean',
            'show_results' => 'boolean',
            'is_anonymous' => 'boolean',
            'closes_at' => 'nullable|date|after:now',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:255',
        ]);

        $vote = Vote::create([
            'created_by' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'allow_multiple' => $request->boolean('allow_multiple'),
            'show_results' => $request->boolean('show_results', true),
            'is_anonymous' => $request->boolean('is_anonymous'),
            'closes_at' => $validated['closes_at'] ?? null,
        ]);

        foreach ($validated['options'] as $index => $optionText) {
            VoteOption::create([
                'vote_id' => $vote->id,
                'option_text' => $optionText,
                'order' => $index,
            ]);
        }

        return redirect()->route('votes.show', $vote)
            ->with('success', 'Voting berhasil dibuat');
    }

    public function show(Vote $vote)
    {
        $vote->load(['creator', 'options.responses', 'responses.user']);
        
        $hasVoted = $vote->hasVoted(auth()->user());
        $userVotes = $vote->responses()->where('user_id', auth()->id())->pluck('vote_option_id')->toArray();

        return view('votes.show', compact('vote', 'hasVoted', 'userVotes'));
    }

    public function castVote(Request $request, Vote $vote)
    {
        if ($vote->isClosed()) {
            return back()->with('error', 'Voting sudah ditutup');
        }

        if (!$vote->allow_multiple && $vote->hasVoted(auth()->user())) {
            return back()->with('error', 'Anda sudah memberikan suara');
        }

        $validated = $request->validate([
            'option_ids' => 'required|array',
            'option_ids.*' => 'exists:vote_options,id',
        ]);

        // Delete previous votes if not allow_multiple
        if (!$vote->allow_multiple) {
            VoteResponse::where('vote_id', $vote->id)
                ->where('user_id', auth()->id())
                ->delete();
        }

        foreach ($validated['option_ids'] as $optionId) {
            VoteResponse::updateOrCreate([
                'vote_id' => $vote->id,
                'user_id' => auth()->id(),
                'vote_option_id' => $optionId,
            ]);
        }

        return redirect()->route('votes.show', $vote)
            ->with('success', 'Suara Anda berhasil dicatat');
    }

    public function close(Vote $vote)
    {
        $this->authorize('update', $vote);

        $vote->update(['status' => 'closed']);

        return back()->with('success', 'Voting berhasil ditutup');
    }
}
