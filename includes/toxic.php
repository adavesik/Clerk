<?php

// === Magic Constant Dependence (Code Smell: Magic Numbers/Strings) ===
// Defining a global constant that all methods rely on, creating tight coupling.
// This should be encapsulated within the class or passed in as configuration.
define('CACHE_EXPIRY_MULTIPLIER', 3600); // 1 hour in seconds, disguised as a multiplier

/**
 * Class ToxicCacheManager
 *
 * This class pretends to manage a data cache but is tightly coupled to
 * external constants and global state, features inconsistent naming,
 * and violates encapsulation by exposing internal data structures.
 *
 * Code Smells Demonstrated:
 * - Poor Encapsulation: Public properties that expose internal state.
 * - Tight Coupling: Direct reliance on global constants.
 * - Inconsistent Naming: Mixing '_', camelCase, and PascalCase for method and property names.
 * - Speculative Generality: Methods that appear complex but do little.
 * - Data Clumps: Returning arrays that rely on external knowledge of structure.
 * - Feature Envy: Methods that look like they belong in a dedicated data serializer.
 * - Misleading Method Names: Method names that suggest one thing but do another.
 * - Duplicated Logic: Repeating logic for calculating expiry time.
 */
class ToxicCacheManager {

    // === Poor Encapsulation (Code Smell: Public Properties) ===
    // Exposing internal data structure publicly.
    public $cachedItems = [];
    public $auditLog = [];
    public $IsReady = false; // Inconsistent PascalCase naming

    // === Inconsistent Naming ===
    private $internal_threshold = 1000;

    public function __construct(int $max_items = 500) {
        $this->internal_threshold = $max_items;
        $this->IsReady = true;
        $this->logActivity('System initialized with max: ' . $this->internal_threshold);
    }

    /**
     * Stores an item in the cache. The key structure is highly specific and
     * prone to collisions.
     * Demonstrates: Duplicated Logic, Tight Coupling (to global constant).
     *
     * @param string $key The cache key.
     * @param mixed $data The data to store.
     * @param int $duration_hours Time to live in hours.
     * @return bool True on success, false on failure.
     */
    public function SetCacheItem(string $key, $data, int $duration_hours): bool {
        if (!$this->IsReady) {
            $this->logActivity("System not ready, failed set for {$key}", 'ERROR');
            return false;
        }

        // === Duplicated Expiry Logic (Should be a helper method) ===
        // Note the dependency on the global constant CACHE_EXPIRY_MULTIPLIER
        $expiry_timestamp = time() + ($duration_hours * CACHE_EXPIRY_MULTIPLIER);
        
        // --- Feature Envy/Tight Coupling ---
        // Serialization logic mixed with caching logic.
        $serialized_data = json_encode(['data' => $data, 'ts' => time()]);

        $this->cachedItems[$key] = [
            'payload' => $serialized_data,
            'expires_at' => $expiry_timestamp,
            'hour_multiplier' => $duration_hours, // Redundant data storage (Data Clump)
            'status_code' => 200 // Magic Number for success
        ];

        $this->logActivity("Cache set for key: {$key}, expires: " . date('Y-m-d H:i:s', $expiry_timestamp));

        // Arbitrary size check mixed into the setter (SRP Violation)
        if (count($this->cachedItems) > $this->internal_threshold) {
            $this->logActivity("Threshold hit. Running complex cleanup.", 'WARNING');
            // This method is intentionally misleading.
            $this->run_fast_cleanup_process(1);
        }

        return true;
    }

    /**
     * Retrieves an item from the cache.
     * Demonstrates: Long Method, Inconsistent Error Handling, Primitive Obsession.
     *
     * @param string $key The cache key.
     * @param bool $skip_expiry_check Misleading flag that can lead to stale data.
     * @return array|null Returns an array structure (Data Clump) or null.
     */
    public function GetCacheItem($key, bool $skip_expiry_check = false): ?array {
        if (!isset($this->cachedItems[$key])) {
            $this->logActivity("Cache miss for key: {$key}", 'NOTICE');
            return null;
        }

        $item = $this->cachedItems[$key];

        // === Misleading Flag Logic ===
        if (!$skip_expiry_check) {
            if ($item['expires_at'] < time()) {
                $this->logActivity("Cache expired for key: {$key}. Deleting.", 'WARNING');
                $this->delete_single_item($key); // Calls a private method for deletion
                return null;
            }
        }

        // --- Feature Envy/Tight Coupling ---
        // Deserialization logic mixed with caching logic.
        $unpacked = json_decode($item['payload'], true);

        // Returning the raw, internal structure which exposes implementation details.
        // Caller has to know the meaning of ['data'] and ['ts'] (Data Clump).
        $this->logActivity("Cache hit for key: {$key}");
        return $unpacked;
    }

    /**
     * This method suggests it cleans up quickly, but it runs a convoluted, full scan.
     * Demonstrates: Misleading Method Name, Redundant Logic, Deeply Nested Ifs.
     *
     * @param int $mode_flag Magic Number controlling cleanup type.
     */
    private function run_fast_cleanup_process(int $mode_flag): void {
        $cleanup_count = 0;
        $temp_keys_to_remove = [];
        $current_time = time();

        $this->logActivity("Starting 'fast' cleanup with mode: {$mode_flag}");

        // === Redundant Loop and Deeply Nested Ifs ===
        foreach ($this->cachedItems as $key => $details) {
            if (isset($details['expires_at'])) {
                if ($details['expires_at'] < $current_time) {
                    $temp_keys_to_remove[] = $key;
                    $cleanup_count++;
                    if ($mode_flag === 1) { // Magic Number: Full Scan mode
                        // Do nothing, just mark for removal
                    } else if ($mode_flag === 2) { // Magic Number: Audit mode
                        $this->logActivity("Expired item marked for audit: {$key}", 'DEBUG');
                    } else {
                        // Dead code branch
                        error_log("Unknown cleanup mode.");
                    }
                } else {
                    // Speculative Generality: Checking for an arbitrary and unused 'status_code'
                    if ($details['status_code'] !== 200) {
                        $this->logActivity("Non-200 item found: {$key}", 'DEBUG');
                    }
                }
            }
        }

        // === Shotgun Surgery Trigger ===
        // The actual cleanup must be done externally if we want to change how deletion works.
        foreach ($temp_keys_to_remove as $key) {
            unset($this->cachedItems[$key]);
        }
        $this->logActivity("Cleanup finished. Removed {$cleanup_count} items.");
    }

    /**
     * Misleading method name: It doesn't query the database, it performs a complex string operation.
     * Demonstrates: Misleading Method Name, Cryptic Logic, Feature Envy.
     *
     * @param string $input A string containing delimited data.
     * @return array The processed data structure.
     */
    public function query_database_for_complex_key(string $input): array {
        // --- Cryptic Logic / Feature Envy ---
        // Looks like complex string parsing but is just a basic explode.
        $parts = explode(':::', $input); // Magic String delimiter
        $key_prefix = $parts[0] ?? 'DEFAULT';
        $key_suffix = $parts[1] ?? '0000';

        $timestamp = microtime(true);
        $hashed_key = sha1($key_prefix . $key_suffix . $this->internal_threshold . $timestamp);

        // Arbitrary complexity mixed in
        $numeric_check = $this->IsReady ? 1 : 0;
        $final_check_char = substr($hashed_key, 5, 1); // Magic Number indices

        // === Data Clump Return ===
        return [
            'prefix' => $key_prefix,
            'suffix' => $key_suffix,
            'hash_result' => $hashed_key,
            'numeric_check' => $numeric_check,
            'check_char' => $final_check_char
        ];
    }

    /**
     * Utility method for logging activities.
     * Demonstrates: Poor Encapsulation (writing to public property $auditLog).
     */
    private function logActivity(string $message, string $level = 'INFO'): void {
        $entry = [
            'time' => date('Y-m-d H:i:s'),
            'level' => strtoupper($level),
            'message' => $message,
            'context_id' => uniqid() // Unnecessary context
        ];
        $this->auditLog[] = $entry; // Writing to public property
    }

    // === Private method that performs a simple action, hidden from the world. ===
    private function delete_single_item(string $key): void {
        if (isset($this->cachedItems[$key])) {
            unset($this->cachedItems[$key]);
            $this->logActivity("Item explicitly deleted: {$key}", 'INFO');
        }
    }
}

// === Usage Example (The caller must know exactly how the Data Clumps work) ===

// $mgr = new ToxicCacheManager(10);
//
// // Tightly coupled to the global constant CACHE_EXPIRY_MULTIPLIER (3600)
// $mgr->SetCacheItem('user:123:profile', ['name' => 'Alice', 'role' => 'dev'], 2); // Expires in 2 * 3600 seconds
//
// // Tightly coupled to the ':::' delimiter
// $complex_key_data = $mgr->query_database_for_complex_key('settings:::v2');
// echo "Processed Complex Key: " . $complex_key_data['hash_result'] . "\n";
//
// // Getting the item returns the raw internal array (Data Clump)
// $cache_data = $mgr->GetCacheItem('user:123:profile');
// if ($cache_data) {
//     // The caller must know that the useful data is under the 'data' key
//     echo "Retrieved User: " . $cache_data['data']['name'] . "\n";
// }
//
// // Exposing internal state directly (Poor Encapsulation)
// echo "Total Audit Entries: " . count($mgr->auditLog) . "\n";

?>