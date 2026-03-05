<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ChatbotCategory;
use App\Models\ChatbotService;
use Illuminate\Http\Request;

class ChatbotServiceController extends Controller
{
    /**
     * Create form.
     */
    public function create(ChatbotCategory $category)
    {
        $this->authorizeCategory($category);

        return view('client.chatbot.services.create', compact('category'));
    }

    /**
     * Store new service.
     */
    public function store(Request $request, ChatbotCategory $category)
    {
        $this->authorizeCategory($category);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $category->services()->create($validated);

        return redirect()
            ->route('client.chatbot.categories.show', $category)
            ->with('success', 'Service created successfully.');
    }

    /**
     * Show service with sub-services.
     */
    public function show(ChatbotService $service)
    {
        $this->authorizeService($service);

        $service->load(['category.website', 'subServices' => fn ($q) => $q->ordered()]);

        return view('client.chatbot.services.show', compact('service'));
    }

    /**
     * Edit form.
     */
    public function edit(ChatbotService $service)
    {
        $this->authorizeService($service);
        $service->load('category');

        return view('client.chatbot.services.edit', compact('service'));
    }

    /**
     * Update service.
     */
    public function update(Request $request, ChatbotService $service)
    {
        $this->authorizeService($service);

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $service->update($validated);

        return redirect()
            ->route('client.chatbot.categories.show', $service->category_id)
            ->with('success', 'Service updated successfully.');
    }

    /**
     * Delete service.
     */
    public function destroy(ChatbotService $service)
    {
        $this->authorizeService($service);
        $categoryId = $service->category_id;
        $service->delete();

        return redirect()
            ->route('client.chatbot.categories.show', $categoryId)
            ->with('success', 'Service deleted successfully.');
    }

    /* ── Authorization helpers ───────────────────── */

    protected function authorizeService(ChatbotService $service): void
    {
        $service->loadMissing('category');
        abort_if(
            !in_array($service->category->website_id, auth()->user()->websiteIds()),
            403,
            'Unauthorized.'
        );
    }

    protected function authorizeCategory(ChatbotCategory $category): void
    {
        abort_if(
            !in_array($category->website_id, auth()->user()->websiteIds()),
            403,
            'Unauthorized.'
        );
    }
}
