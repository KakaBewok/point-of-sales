<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Structured Activity Logger for monitoring, debugging, and auditing.
 * 
 * Produces JSON-structured log entries with consistent fields:
 * - user_id, action, entity_type, entity_id, timestamp, metadata
 * 
 * ELK-stack ready: each entry is a single JSON object on the 'activity' channel.
 */
class ActivityLogger
{
    /**
     * Log an informational activity (normal operations).
     */
    public static function info(string $action, string $entityType = '', mixed $entityId = null, array $metadata = []): void
    {
        self::log('info', $action, $entityType, $entityId, $metadata);
    }

    /**
     * Log a warning activity (suspicious behavior).
     */
    public static function warning(string $action, string $entityType = '', mixed $entityId = null, array $metadata = []): void
    {
        self::log('warning', $action, $entityType, $entityId, $metadata);
    }

    /**
     * Log an error activity (system failure).
     */
    public static function error(string $action, string $entityType = '', mixed $entityId = null, array $metadata = []): void
    {
        self::log('error', $action, $entityType, $entityId, $metadata);
    }

    /**
     * Core structured log method.
     */
    protected static function log(string $level, string $action, string $entityType, mixed $entityId, array $metadata): void
    {
        $user = Auth::user();

        $context = [
            'user_id'     => $user?->id,
            'user_name'   => $user?->name,
            'user_role'   => $user?->role,
            'action'      => $action,
            'entity_type' => $entityType ?: null,
            'entity_id'   => $entityId,
            'ip_address'  => request()?->ip(),
            'user_agent'  => request()?->userAgent(),
            'timestamp'   => now()->toIso8601String(),
            'metadata'    => $metadata ?: null,
        ];

        // Remove null values for cleaner logs
        $context = array_filter($context, fn ($v) => $v !== null);

        $message = "[{$action}]" 
            . ($entityType ? " {$entityType}" : '') 
            . ($entityId ? "#{$entityId}" : '');

        Log::channel('activity')->{$level}($message, $context);
    }

    // ─── Convenience Methods ───────────────────────────────────

    /**
     * Log authentication events.
     */
    public static function auth(string $action, ?int $userId = null, array $metadata = []): void
    {
        self::log(
            $action === 'login_failed' ? 'warning' : 'info',
            $action,
            'user',
            $userId ?? Auth::id(),
            $metadata
        );
    }

    /**
     * Log entity CRUD operations.
     */
    public static function crud(string $action, string $entityType, mixed $entityId, array $metadata = []): void
    {
        self::info($action, $entityType, $entityId, $metadata);
    }

    /**
     * Log a bulk operation.
     */
    public static function bulk(string $action, string $entityType, array $ids, array $metadata = []): void
    {
        self::info($action, $entityType, null, array_merge($metadata, [
            'affected_ids' => $ids,
            'affected_count' => count($ids),
        ]));
    }

    /**
     * Log settings changes.
     */
    public static function settings(string $action, array $metadata = []): void
    {
        self::info($action, 'settings', null, $metadata);
    }

    /**
     * Log transaction events.
     */
    public static function transaction(string $action, mixed $transactionId, array $metadata = []): void
    {
        self::info($action, 'transaction', $transactionId, $metadata);
    }
}
