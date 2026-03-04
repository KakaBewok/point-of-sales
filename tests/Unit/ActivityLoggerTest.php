<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ActivityLoggerTest extends TestCase
{
    use RefreshDatabase;

    public function test_info_log_writes_to_activity_channel(): void
    {
        Log::shouldReceive('channel')
            ->with('activity')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                return str_contains($message, 'test_action')
                    && $context['action'] === 'test_action'
                    && $context['entity_type'] === 'product'
                    && $context['entity_id'] === 1;
            });

        ActivityLogger::info('test_action', 'product', 1);
    }

    public function test_warning_log_uses_warning_level(): void
    {
        Log::shouldReceive('channel')
            ->with('activity')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return $context['action'] === 'suspicious_action';
            });

        ActivityLogger::warning('suspicious_action', 'user', 42);
    }

    public function test_error_log_uses_error_level(): void
    {
        Log::shouldReceive('channel')
            ->with('activity')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message, $context) {
                return $context['action'] === 'system_failure';
            });

        ActivityLogger::error('system_failure', 'transaction', 99, ['reason' => 'timeout']);
    }

    public function test_auth_method_logs_login(): void
    {
        Log::shouldReceive('channel')
            ->with('activity')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                return $context['action'] === 'login_success'
                    && $context['entity_type'] === 'user';
            });

        ActivityLogger::auth('login_success', 1);
    }

    public function test_auth_failed_uses_warning_level(): void
    {
        Log::shouldReceive('channel')
            ->with('activity')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('warning')
            ->once()
            ->withArgs(function ($message, $context) {
                return $context['action'] === 'login_failed';
            });

        ActivityLogger::auth('login_failed', null, ['email' => 'bad@example.com']);
    }

    public function test_bulk_method_includes_affected_count(): void
    {
        Log::shouldReceive('channel')
            ->with('activity')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                return $context['action'] === 'product_bulk_deleted'
                    && $context['metadata']['affected_count'] === 3
                    && count($context['metadata']['affected_ids']) === 3;
            });

        ActivityLogger::bulk('product_bulk_deleted', 'product', [1, 2, 3]);
    }

    public function test_includes_user_context_when_authenticated(): void
    {
        $user = User::factory()->admin()->create();
        $this->actingAs($user);

        Log::shouldReceive('channel')
            ->with('activity')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) use ($user) {
                return $context['user_id'] === $user->id
                    && $context['user_name'] === $user->name
                    && $context['user_role'] === 'admin';
            });

        ActivityLogger::info('test', 'test', 1);
    }

    public function test_no_sensitive_data_logged(): void
    {
        Log::shouldReceive('channel')
            ->with('activity')
            ->once()
            ->andReturnSelf();

        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                // Should not contain password keys
                $json = json_encode($context);
                return !str_contains($json, 'password')
                    && !str_contains($json, 'token');
            });

        ActivityLogger::info('login_success', 'user', 1);
    }
}
