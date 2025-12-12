<?php

/**
 * Class SimpleCache
 *
 * This class provides a basic, in-memory cache implementation focusing on
 * clean code principles: Single Responsibility, strong Encapsulation,
 * and clear naming conventions (PSR-1/PSR-12).
 *
 * Clean Code Principles Applied:
 * - SRP: Focused purely on caching (storage, retrieval, expiration, cleanup).
 * - Encapsulation: All state is private, accessed only via public methods.
 * - Clear Naming: Consistent camelCase for properties and methods.
 * - Constants: Magic numbers/strings are encapsulated as class constants.
 * - Data Integrity: Methods return only the requested data, not internal structure.
 * - Helper Methods: Extracted duplicated logic into private methods.
 */
class SimpleCache {

    // === Encapsulated Constants (Replaces global define and magic numbers) ===
    private const SECONDS_PER_HOUR = 3600;
    private const DEFAULT_MAX_ITEMS = 500;

    // === Private State (Strong Encapsulation) ===
    private array $cachedItems = []; // Stores the actual cache entries
    private array $auditLog = [];    // For logging internal events
    private bool $isReady = false;   // Consistent camelCase Naming
    private int $maxItems;           // Descriptive Naming

    public function __construct(int $maxItems = self::DEFAULT_MAX_ITEMS) {
        // Use the descriptive constant if provided parameter is zero or negative
        $this->maxItems = $maxItems > 0 ? $maxItems : self::DEFAULT_MAX_ITEMS;
        $this->isReady = true;
        $this->log('System initialized with max items: ' . $this->maxItems);
    }

    /**
     * Stores an item in the cache.
     * (Method name simplified to 'set' and uses descriptive parameter name)
     *
     * @param string $key The cache key.
     * @param mixed $data The data to store.
     * @param int $durationSeconds Time to live in seconds.
     * @return bool True on successful set.
     */
    public function set(string $key, $data, int $durationSeconds): bool {
        if (!$this->isReady) {
            $this->log("System not ready, failed set for {$key}", 'ERROR');
            return false;
        }

        // Uses a dedicated helper method (eliminates Duplicated Logic)
        $expiryTimestamp = $this->calculateExpiryTimestamp($durationSeconds);

        // Cache entry structure is simple and only includes necessary elements.
        $this->cachedItems[$key] = [
            'data' => $data, // Store data directly
            'expires_at' => $expiryTimestamp,
        ];

        $this->log("Cache set for key: {$key}, expires: " . date('Y-m-d H:i:s', $expiryTimestamp));

        // Cleanup check remains, but uses the dedicated cleanup method (SRP compliance)
        if (count($this->cachedItems) > $this->maxItems) {
            $this->log("Max items threshold hit. Running cleanup.", 'WARNING');
            $this->cleanupExpiredItems();
        }

        return true;
    }

    /**
     * Retrieves an item from the cache.
     * (Method name simplified to 'get', returns only the data, not internal structure)
     *
     * @param string $key The cache key.
     * @return mixed|null The stored data, or null if not found or expired.
     */
    public function get(string $key) {
        if (!isset($this->cachedItems[$key])) {
            $this->log("Cache miss for key: {$key}", 'NOTICE');
            return null;
        }

        $item = $this->cachedItems[$key];

        if ($this->isExpired($item['expires_at'])) {
            $this->log("Cache expired for key: {$key}. Deleting.", 'WARNING');
            $this->delete($key);
            return null;
        }

        $this->log("Cache hit for key: {$key}");
        // Return only the stored data (eliminates Data Clump smell)
        return $item['data'];
    }

    /**
     * Removes a single item from the cache.
     * (Replaces the hidden delete_single_item method with a public, clean one)
     *
     * @param string $key The key of the item to delete.
     * @return bool True if item existed and was removed, false otherwise.
     */
    public function delete(string $key): bool {
        if (isset($this->cachedItems[$key])) {
            unset($this->cachedItems[$key]);
            $this->log("Item explicitly deleted: {$key}");
            return true;
        }
        return false;
    }

    /**
     * Retrieves the internal audit log.
     * (Controlled access via getter for the private state)
     *
     * @return array
     */
    public function getAuditLog(): array {
        return $this->auditLog;
    }

    /**
     * Cleans up all expired items in the cache.
     * (Replaces the misleading run_fast_cleanup_process with clear, descriptive name)
     */
    public function cleanupExpiredItems(): void {
        $cleanupCount = 0;
        $keysToRemove = [];
        $currentTime = time();

        $this->log("Starting cleanup of expired items.");

        foreach ($this->cachedItems as $key => $details) {
            // Simple logic: check if it's expired using a helper
            if ($this->isExpired($details['expires_at'], $currentTime)) {
                $keysToRemove[] = $key;
            }
        }

        foreach ($keysToRemove as $key) {
            unset($this->cachedItems[$key]);
            $cleanupCount++;
        }

        $this->log("Cleanup finished. Removed {$cleanupCount} items.");
    }

    // === Private Helper Methods (Eliminates Duplicated Logic) ===

    /**
     * Checks if a given timestamp is in the past.
     */
    private function isExpired(int $expiryTimestamp, ?int $currentTime = null): bool {
        return $expiryTimestamp < ($currentTime ?? time());
    }

    /**
     * Calculates the expiry timestamp based on a duration in seconds.
     * (Centralized logic for time calculation)
     */
    private function calculateExpiryTimestamp(int $durationSeconds): int {
        return time() + $durationSeconds;
    }

    /**
     * Internal utility for logging activities.
     * (SRP compliance: Centralized logging responsibility)
     */
    private function log(string $message, string $level = 'INFO'): void {
        $entry = [
            'time' => date('Y-m-d H:i:s'),
            'level' => strtoupper($level),
            'message' => $message,
        ];
        $this->auditLog[] = $entry;
    }
}

// === Usage Example (Cleaned up and refers to the new, simplified methods) ===

// $mgr = new SimpleCache(10);
//
// // Set cache item using seconds instead of magic hours multiplier
// $mgr->set('user:123:profile', ['name' => 'Alice', 'role' => 'dev'], 2 * SimpleCache::SECONDS_PER_HOUR);
//
// // query_database_for_complex_key was removed as it was misleading.
// // If key processing is needed, it should be done externally.
//
// // Getting the item returns only the useful data
// $cache_data = $mgr->get('user:123:profile');
// if ($cache_data) {
//     // The caller now receives the data directly, not a 'data' wrapper array
//     echo "Retrieved User: " . $cache_data['name'] . "\n";
// }
//
// // Accessing the audit log through a controlled getter (Good Encapsulation)
// echo "Total Audit Entries: " . count($mgr->getAuditLog()) . "\n";
