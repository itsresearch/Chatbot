<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index()
    {
        $clients = User::where('role', 'client')
            ->withCount(['websites'])
            ->latest()
            ->paginate(15);

        return view('superadmin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('superadmin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => 'client',
            'is_active' => true,
        ]);

        return redirect()->route('superadmin.clients.index')
            ->with('success', 'Client created successfully.');
    }

    public function show(User $client)
    {
        abort_if($client->role !== 'client', 404);

        $client->load('websites');
        $websiteIds = $client->websiteIds();

        $stats = [
            'totalWebsites' => $client->websites->count(),
            'totalConversations' => \App\Models\Conversation::whereIn('website_id', $websiteIds)->count(),
            'totalMessages' => \App\Models\Message::whereIn('conversation_id',
                \App\Models\Conversation::whereIn('website_id', $websiteIds)->pluck('id')
            )->count(),
        ];

        return view('superadmin.clients.show', compact('client', 'stats'));
    }

    public function edit(User $client)
    {
        abort_if($client->role !== 'client', 404);
        return view('superadmin.clients.edit', compact('client'));
    }

    public function update(Request $request, User $client)
    {
        abort_if($client->role !== 'client', 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($client->id)],
            'company_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $client->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'company_name' => $validated['company_name'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => $request->boolean('is_active', $client->is_active),
        ]);

        if (!empty($validated['password'])) {
            $client->update(['password' => Hash::make($validated['password'])]);
        }

        return redirect()->route('superadmin.clients.show', $client)
            ->with('success', 'Client updated successfully.');
    }

    public function destroy(User $client)
    {
        abort_if($client->role !== 'client', 404);
        $client->delete();

        return redirect()->route('superadmin.clients.index')
            ->with('success', 'Client deleted successfully.');
    }

    public function toggleStatus(User $client)
    {
        abort_if($client->role !== 'client', 404);
        $client->update(['is_active' => !$client->is_active]);

        return back()->with('success', 'Client status updated.');
    }
}
