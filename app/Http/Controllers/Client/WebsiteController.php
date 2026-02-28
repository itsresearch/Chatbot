<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Website;
use Illuminate\Http\Request;

class WebsiteController extends Controller
{
    public function index()
    {
        $websites = auth()->user()->websites()
            ->withCount(['conversations', 'visitors'])
            ->latest()
            ->get();

        return view('client.websites.index', compact('websites'));
    }

    public function create()
    {
        return view('client.websites.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:websites,domain',
            'welcome_message' => 'nullable|string|max:500',
            'widget_color' => 'nullable|string|max:7',
        ]);

        $website = auth()->user()->websites()->create([
            'name' => $validated['name'],
            'domain' => $validated['domain'],
            'api_key' => Website::generateApiKey(),
            'welcome_message' => $validated['welcome_message'] ?? 'Hi there! How can I help you today?',
            'widget_color' => $validated['widget_color'] ?? '#ff7a18',
        ]);

        return redirect()->route('client.websites.show', $website)
            ->with('success', 'Website added successfully! Use the embed code to add the chatbot.');
    }

    public function show(Website $website)
    {
        $this->authorizeWebsite($website);

        $website->loadCount(['conversations', 'visitors']);
        $serverUrl = config('app.url');

        return view('client.websites.show', compact('website', 'serverUrl'));
    }

    public function edit(Website $website)
    {
        $this->authorizeWebsite($website);
        return view('client.websites.edit', compact('website'));
    }

    public function update(Request $request, Website $website)
    {
        $this->authorizeWebsite($website);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:websites,domain,' . $website->id,
            'welcome_message' => 'nullable|string|max:500',
            'widget_color' => 'nullable|string|max:7',
        ]);

        $website->update($validated);

        return redirect()->route('client.websites.show', $website)
            ->with('success', 'Website updated successfully.');
    }

    public function destroy(Website $website)
    {
        $this->authorizeWebsite($website);
        $website->delete();

        return redirect()->route('client.websites.index')
            ->with('success', 'Website deleted successfully.');
    }

    public function regenerateKey(Website $website)
    {
        $this->authorizeWebsite($website);
        $website->update(['api_key' => Website::generateApiKey()]);

        return back()->with('success', 'API key regenerated. Update your widget embed code.');
    }

    protected function authorizeWebsite(Website $website): void
    {
        abort_if($website->user_id !== auth()->id(), 403, 'Unauthorized.');
    }
}
