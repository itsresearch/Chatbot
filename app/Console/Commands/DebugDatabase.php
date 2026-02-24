<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Website;
use App\Models\Visitor;

class DebugDatabase extends Command
{
    protected $signature = 'debug:database';
    protected $description = 'Show all messages and conversations in the database';

    public function handle()
    {
        $this->info('=== DATABASE DEBUG ===');
        
        $this->info("\n### Websites ###");
        Website::all()->each(function ($w) {
            $this->info("ID: {$w->id}, Name: {$w->name}, API Key: {$w->api_key}");
        });
        
        $this->info("\n### Visitors ###");
        Visitor::all()->each(function ($v) {
            $this->info("ID: {$v->id}, Token: {$v->visitor_token}, Website: {$v->website_id}");
        });
        
        $this->info("\n### Conversations ###");
        Conversation::with('messages', 'visitor', 'website')->get()->each(function ($c) {
            $this->info("ID: {$c->id}, Visitor: {$c->visitor?->visitor_token}, Website: {$c->website?->name}, Status: {$c->status}, Messages: {$c->messages->count()}");
            $c->messages->each(function ($m) {
                $this->info("  └─ [{$m->sender_type}] " . substr($m->message, 0, 50) . "...");
            });
        });
        
        $this->info("\n### Summary ###");
        $this->info("Total Websites: " . Website::count());
        $this->info("Total Visitors: " . Visitor::count());
        $this->info("Total Conversations: " . Conversation::count());
        $this->info("Total Messages: " . Message::count());
    }
}
