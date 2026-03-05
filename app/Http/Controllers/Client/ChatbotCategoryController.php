<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ChatbotCategory;
use Illuminate\Http\Request;

class ChatbotCategoryController extends Controller
{
    /**
     * Display categories for the selected website.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $websites = $user->websites()->where('is_active', true)->get();

        // Resolve selected website (query param → session → first website)
        $selectedWebsiteId = $request->get('website_id')
            ?? session('chatbot_website_id')
            ?? $websites->first()?->id;

        // Persist in session
        if ($selectedWebsiteId) {
            session(['chatbot_website_id' => (int) $selectedWebsiteId]);
        }

        $categories = collect();
        if ($selectedWebsiteId && $websites->contains('id', $selectedWebsiteId)) {
            $categories = ChatbotCategory::forWebsite($selectedWebsiteId)
                ->ordered()
                ->withCount('services')
                ->get();
        }

        return view('client.chatbot.categories.index', compact(
            'websites',
            'selectedWebsiteId',
            'categories'
        ));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $selectedWebsiteId = session('chatbot_website_id');
        abort_if(!$selectedWebsiteId, 403, 'Please select a website first.');
        $this->authorizeWebsiteId($selectedWebsiteId);

        return view('client.chatbot.categories.create', compact('selectedWebsiteId'));
    }

    /**
     * Store new category.
     */
    public function store(Request $request)
    {
        $selectedWebsiteId = session('chatbot_website_id');
        abort_if(!$selectedWebsiteId, 403, 'Please select a website first.');
        $this->authorizeWebsiteId($selectedWebsiteId);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $validated['website_id'] = $selectedWebsiteId;

        ChatbotCategory::create($validated);

        return redirect()
            ->route('client.chatbot.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show single category with its services.
     */
    public function show(ChatbotCategory $category)
    {
        $this->authorizeCategory($category);

        $category->load(['services' => fn ($q) => $q->ordered()->withCount('subServices'), 'website']);

        return view('client.chatbot.categories.show', compact('category'));
    }

    /**
     * Edit form.
     */
    public function edit(ChatbotCategory $category)
    {
        $this->authorizeCategory($category);

        return view('client.chatbot.categories.edit', compact('category'));
    }

    /**
     * Update category.
     */
    public function update(Request $request, ChatbotCategory $category)
    {
        $this->authorizeCategory($category);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $category->update($validated);

        return redirect()
            ->route('client.chatbot.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete category (cascades to services & sub-services).
     */
    public function destroy(ChatbotCategory $category)
    {
        $this->authorizeCategory($category);
        $category->delete();

        return redirect()
            ->route('client.chatbot.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /* ── Authorization helpers ───────────────────── */

    protected function authorizeCategory(ChatbotCategory $category): void
    {
        abort_if(
            !in_array($category->website_id, auth()->user()->websiteIds()),
            403,
            'Unauthorized.'
        );
    }

    protected function authorizeWebsiteId(int $websiteId): void
    {
        abort_if(
            !in_array($websiteId, auth()->user()->websiteIds()),
            403,
            'Unauthorized.'
        );
    }
}
