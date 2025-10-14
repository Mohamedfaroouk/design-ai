<?php

namespace App\Console\Commands;

use App\Models\Store;
use App\Services\Store\Salla\SallaAuthService;
use App\Services\Store\Zid\ZidAuthService;
use App\Services\Store\WordPress\WordPressAuthService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshStoreTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stores:refresh-tokens
                            {--platform= : Refresh only specific platform (salla, zid, wordpress)}
                            {--force : Force refresh all tokens regardless of expiration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh access tokens for stores that are about to expire';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting token refresh process...');

        $query = Store::query();

        // Filter by platform if specified
        if ($platform = $this->option('platform')) {
            $query->where('platform', $platform);
            $this->info("Filtering by platform: {$platform}");
        }

        // Get stores needing refresh or all if forced
        if ($this->option('force')) {
            $this->warn('Force mode enabled - refreshing ALL tokens');
            $stores = $query->whereNotNull('refresh_token')->get();
        } else {
            $stores = $query->needingRefresh()->get();
        }

        if ($stores->isEmpty()) {
            $this->info('No stores need token refresh.');
            return Command::SUCCESS;
        }

        $this->info("Found {$stores->count()} store(s) needing token refresh.");

        $refreshed = 0;
        $failed = 0;

        $progressBar = $this->output->createProgressBar($stores->count());
        $progressBar->start();

        foreach ($stores as $store) {
            try {
                $authService = $this->getAuthService($store->platform->value);

                $this->newLine();
                $this->line("Refreshing token for store: {$store->store_name} ({$store->platform->value})");

                $authService->refreshToken($store);

                $refreshed++;
                $this->info("✓ Successfully refreshed token for: {$store->store_name}");

                Log::info('Store token refreshed', [
                    'store_id' => $store->id,
                    'platform' => $store->platform->value,
                    'store_name' => $store->store_name,
                ]);
            } catch (\Exception $e) {
                $failed++;
                $this->error("✗ Failed to refresh token for: {$store->store_name}");
                $this->error("  Error: {$e->getMessage()}");

                Log::error('Failed to refresh store token', [
                    'store_id' => $store->id,
                    'platform' => $store->platform->value,
                    'store_name' => $store->store_name,
                    'error' => $e->getMessage(),
                ]);
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('=== Token Refresh Summary ===');
        $this->info("Total processed: {$stores->count()}");
        $this->info("Successfully refreshed: {$refreshed}");

        if ($failed > 0) {
            $this->error("Failed: {$failed}");
        } else {
            $this->info("Failed: {$failed}");
        }

        return $failed > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    /**
     * Get the appropriate auth service for the platform
     */
    protected function getAuthService(string $platform)
    {
        return match ($platform) {
            'salla' => app(SallaAuthService::class),
            'zid' => app(ZidAuthService::class),
            'wordpress' => app(WordPressAuthService::class),
            default => throw new \Exception("Unknown platform: {$platform}"),
        };
    }
}
