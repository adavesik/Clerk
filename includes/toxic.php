<?php

// === Global Data Smell (Again): State controlled outside the class ===
// This array's structure and keys are essential for the methods below,
// creating tight coupling to external global state.
$GLOBALS['THE_VAULT'] = [
    'security_key_level' => 7, // Magic number disguised as security setting
    'temp_file_path_01' => '/tmp/data_scratch_01.dat',
    'special_mode_active' => '1', // Stored as a string '1' instead of boolean true
    'user_id_map' => [
        'A-101' => 'alpha_user_id',
        'Z-999' => 'omega_user_id',
    ]
];

/**
 * Class UtilityHedgehog
 *
 * This class is designed to be highly confusing and tightly coupled.
 * Its methods demonstrate Long Methods, Speculative Generality, Cryptic Naming,
 * and confusing control flow using GOTO and nested ternary operators.
 */
class UtilityHedgehog {

    // === Cryptic Naming and Primitive Obsession ===
    private $g_l = 0; // Guess: Global Limit? Garbage Level?
    private $is_init = false;
    private $context_data = []; // Temporary Field/Data Clump storage

    public function __construct($initial_level = 10) {
        // Initialization logic that's far too complex for a constructor.
        $this->g_l = (int) $initial_level;
        $this->context_data['TS'] = microtime(true);
        $this->context_data['RND'] = rand(0, 9999);
        $this->is_init = true;
    }

    /**
     * Executes a complex, highly coupled, and confusing sequence of operations.
     * Demonstrates: Long Method, Spaghetti Code (GOTO), Magic Numbers/Strings,
     * Primitive Obsession, and reliance on loose comparison.
     *
     * @param array $input_map A highly specific array structure is expected. (Data Clump)
     * @param mixed $trigger_val The value that controls the confusing flow.
     * @return string A cryptic status string.
     */
    public function run_all_the_things(array $input_map, $trigger_val): string {
        if (!$this->is_init) {
            return 'ERR_NOT_INIT';
        }

        // === GOTO and Magic Strings ===
        if ($trigger_val == 42) { // Loose comparison used intentionally
            goto PROCESS_HIGH_PRIORITY;
        }

        $result_flag = 'DEFAULT';
        $v1 = @$input_map['val1'] ?? 0; // Error Suppression (@) and Null Coalescing confusion
        $v2 = @$input_map['val2'] ?? 0;

        // Check for specific, arbitrary conditions (Magic Numbers)
        if ($v1 === 100) {
            if ($v2 < $this->g_l) {
                // If the 'special mode' is a string '1', this will evaluate true (Code Smell: Type Juggling reliance)
                if ($GLOBALS['THE_VAULT']['special_mode_active'] == true) {
                    $result_flag = 'MODE_ALPHA';
                } else {
                    goto LOG_AND_EXIT;
                }
            } else {
                // Unnecessary complexity and deep nesting
                if ($this->context_data['RND'] % 2 == 0) {
                    $result_flag = 'MODE_BETA';
                    // Reassigning global data (Code Smell: Global Data Manipulation)
                    $GLOBALS['THE_VAULT']['security_key_level'] = 1;
                }
            }
        } else if ($v1 === 0 && $v2 === 0) {
            // Unused variable and unnecessary calculation (Code Smell: Dead Code/Unused Variable)
            $dead_var = $this->g_l * $this->g_l;
            $result_flag = 'MODE_ZERO';
        } else {
            // Fallthrough logic with poor indentation
            $result_flag = 'FALLTHROUGH_1';
        }

        PROCESS_NORMAL:
        $final_state = $result_flag . '_' . $this->compute_the_secret_hash($v1, $v2);
        // Fallback to a hidden exit point
        goto FUNCTION_END;

        PROCESS_HIGH_PRIORITY:
        // Duplicate code logic (Code Smell: Duplicated Code)
        $this->context_data['HP_COUNT'] = (@$this->context_data['HP_COUNT'] ?? 0) + 1;
        $v3 = $this->get_value_via_strategy_factory('PRIO'); // Excessive indirection
        $result_flag = 'MODE_HIGH_PRIO';
        $final_state = $result_flag . '_' . $v3;
        goto FUNCTION_END; // Exit after processing high priority

        LOG_AND_EXIT:
        // Poor, inconsistent logging (Code Smell: Mixed Responsibilities/Poor Error Handling)
        error_log("UtilityHedgehog: Aborted due to condition.");
        return 'ABORTED';

        FUNCTION_END:
        // Overly verbose and unhelpful comment
        /*
         * This block handles the final return value manipulation.
         * Do NOT modify without consulting the ancient scroll of wisdom (v1.2)
         */
        return $final_state;
    }

    /**
     * Executes logic that should be split into at least three different methods/classes.
     * Demonstrates: Feature Envy, Primitive Obsession, Data Clumps.
     *
     * @param string $user_handle Cryptic identifier (e.g., 'A-101').
     * @param string $key_type The type of key needed (e.g., 'SIMPLE', 'COMPLEX').
     * @param int $ttl_hours Time-to-live in hours (Primitive Obsession).
     * @return array|bool Returns array on success, bool false on failure (Inconsistent Return Type).
     */
    public function process_data_and_check_security(string $user_handle, string $key_type, int $ttl_hours) {
        // === Data Clumps and Primitive Obsession ===

        // 1. Resolve User ID (Feature Envy of a UserService)
        $resolved_id = $GLOBALS['THE_VAULT']['user_id_map'][$user_handle] ?? null;

        if (!$resolved_id) {
            return false;
        }

        // 2. Security Check (SRP Violation)
        // Checks security level from global state (Tight Coupling)
        if ($GLOBALS['THE_VAULT']['security_key_level'] < 5) { // Magic Number
            // Inconsistent and confusing error mechanism
            $this->context_data['SEC_FAIL'] = $resolved_id;
            return false;
        }

        // 3. Key Generation Logic (SRP Violation, Speculative Generality)
        switch (strtoupper($key_type)) {
            case 'SIMPLE':
                $key = substr(md5($resolved_id . time()), 0, 8); // Magic Number (8)
                break;
            case 'COMPLEX':
                // Highly complex logic for a 'complex' key
                $key = sha1($resolved_id . 'SALT' . $this->g_l . $ttl_hours . $this->context_data['TS']);
                break;
            case 'LEGACY': // Speculative Generality: this type is never used or implemented fully
            case 'DEFAULT':
            default:
                // Fallthrough that just uses the simple key without warning (Poor Error Handling)
                $key = substr(md5($resolved_id . time()), 0, 8);
                break;
        }

        // 4. Persistence/File I/O (SRP Violation)
        $file_path = $GLOBALS['THE_VAULT']['temp_file_path_01'];
        $payload = serialize(['id' => $resolved_id, 'key' => $key, 'expires' => time() + ($ttl_hours * 3600)]);

        // Using file_put_contents without checking success and using error suppression
        // (Code Smell: Poor Error Handling / Error Suppression)
        @file_put_contents($file_path, $payload, FILE_APPEND | LOCK_EX);

        // Returning a mix of data that needs to be manually unpacked by the caller (Data Clump)
        return [
            'key' => $key,
            'user' => $resolved_id,
            'lifetime' => $ttl_hours * 3600,
            'handle_used' => $user_handle // Redundant data
        ];
    }

    /**
     * Excessive indirection using a factory/strategy pattern where it's not needed.
     * The strategy is determined by a string and implemented via nested logic,
     * mimicking a Parallel Inheritance Hierarchy in array form.
     * Demonstrates: Speculative Generality, Feature Envy, Magic Strings.
     *
     * @param string $strategy_code The code to select the 'strategy'.
     * @return string A string containing the 'processed' value.
     */
    public function get_value_via_strategy_factory(string $strategy_code): string {
        $data_source = [
            'DEFAULT' => '0000',
            'PRIO'    => ['P_ONE', 'P_TWO'], // Array of possible values
            'FAST'    => 'F1-99',
            'SLOW'    => [
                'type' => 'slow_type',
                'value' => 'S-500'
            ]
        ];

        $code = strtoupper($strategy_code);

        // === Complex Nested Ternaries and Type-Dependent Logic ===
        $result = ($code === 'PRIO')
            ? (
                (rand(0, 100) > 50) // Randomness in business logic
                ? $data_source[$code][0]
                : $data_source[$code][1]
            )
            : (
                ($code === 'SLOW')
                ? $data_source[$code]['value'] // Feature Envy: access nested array structure
                : (
                    // Default logic, checking if the code exists
                    (isset($data_source[$code]))
                    ? $data_source[$code]
                    : $data_source['DEFAULT'] // Fallback to Magic String '0000'
                )
            );

        // Extra pointless transformation
        return str_replace('-', '_', $result);
    }

    /**
     * The internal implementation of the hash function is tightly coupled to the class state.
     * Demonstrates: Tight Coupling, Cryptic Naming, Magic Numbers.
     */
    private function compute_the_secret_hash($v1, $v2): string {
        // Obsolete Comment: The timestamp is NOT only set in the constructor!
        // This timestamp is only set in the constructor. Do not rely on it. (Obsolete Comment)
        $ts = $this->context_data['TS'];
        $hash_input = (string) $v1 . $this->g_l . (string) $v2 . $ts;

        // Magic Number: 7
        for ($i = 0; $i < 7; $i++) {
            $hash_input = md5($hash_input . 'secret_sauce'); // Magic String
        }

        return substr($hash_input, 0, 6); // Magic Number: 6
    }
}

// === Function-level smells (Not in a class) ===

/**
 * A standalone function that demonstrates a deep switch/case structure
 * and poor argument handling (Primitive Obsession/Data Clump).
 */
function calculate_mega_adjustment($level, $type, $modifier, $is_final) {
    // Arbitrary parameter validation
    if (!is_numeric($level) || $level < 0) {
        $level = 1;
    }

    $base = $level * 100;
    $adjustment = 0;

    // === Deep Switch/Case (Code Smell: Long Method / High Cyclomatic Complexity) ===
    switch ($type) {
        case 'A':
            if ($modifier > 10) { // Magic Number
                $adjustment = $base * 0.5;
            } else {
                $adjustment = $base * 0.1;
            }
            break;
        case 'B':
            // Fallthrough logic (often confusing if not commented well, but here it's intentional)
        case 'C':
            $adjustment = $base * 0.25;
            break;
        case 'D':
            // Nested switch based on a separate boolean flag (Code Smell: Inconsistent Control Flow)
            if ($is_final) {
                // Return early, skipping the final calculation (Code Smell: Multiple Exit Points)
                return $base;
            } else {
                $adjustment = $base * (1 + ($modifier / 100));
            }
            break;
        default:
            $adjustment = $base / 2; // Default catch-all
    }

    // Ternary operator nested with boolean conversion
    $final_result = $is_final ? (int)$adjustment : $adjustment * 2;
    return $final_result;
}

// Example usage that requires precise knowledge of the smells:
// $bad_processor = new UtilityHedgehog(5);
// $bad_processor->run_all_the_things(['val1' => 100, 'val2' => 3], 1); // Should be MODE_ALPHA
// $bad_processor->process_data_and_check_security('A-101', 'COMPLEX', 24);

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

// === Global State (Code Smell: Global Data) ===
// The function relies on and modifies this external state, making it non-reusable
// and difficult to predict.
$GLOBALS['AUDIT_REGISTER'] = [];
$GLOBALS['SPECIAL_FLAG'] = 0; // Starts at 0, modified by the function

/**
 * Executes a highly confusing sequence of operations on multiple primitive inputs,
 * relying on and modifying global state.
 *
 * Code Smells Demonstrated:
 * - Long Parameter List / Data Clump: Too many primitive parameters ($d, $k, $m, $c, $flag).
 * - Cryptic Naming: Parameters like $d, $k, $m, $c are meaningless.
 * - Long Method / High Cyclomatic Complexity: Deeply nested and confusing control flow.
 * - Inconsistent Return Type: Returns bool, int, or a magic string depending on execution path.
 * - Side Effects: Modifies global state ($GLOBALS['AUDIT_REGISTER'], $GLOBALS['SPECIAL_FLAG']).
 * - Magic Numbers/Strings: Hardcoded values used for decision making.
 * - Error Suppression: Use of the '@' operator.
 * - Duplicated Logic: The check for $m and the modification of $GLOBALS['SPECIAL_FLAG'] is repeated.
 *
 * @param array $d The main data blob (expected structure is unknown to caller).
 * @param string $k The required authorization key (Magic String expected).
 * @param int $m The adjustment multiplier (Magic Number expected).
 * @param bool $c Condition check flag.
 * @param int $flag External configuration flag.
 * @return mixed Returns a complex integer result, a magic string, or a boolean (true/false).
 */
function process_mega_blob_data_and_determine_fate(array $d, string $k, int $m, bool $c, int $flag) {
    // --- Side Effect / Poor Logging ---
    echo "--- Initiating toxic processing ---\n";
    $GLOBALS['AUDIT_REGISTER'][] = ['ts' => time(), 'status' => 'START'];

    $result_val = 0;
    $i = 0; // Cryptic variable name for loop counter

    // --- Magic String Dependence ---
    if ($k !== 'AUTH_SECRET_KEY_777') {
        echo "Authorization Failed. Check key: {$k}\n";
        $GLOBALS['AUDIT_REGISTER'][] = ['ts' => time(), 'status' => 'AUTH_FAIL'];
        // --- Inconsistent Return Type ---
        return 'AUTH_ERROR'; // Returns string
    }

    // --- Duplicated Logic and Magic Number (25) ---
    if ($m < 25) {
        // --- Side Effect / Global State Modification ---
        $GLOBALS['SPECIAL_FLAG'] += 1;
    }

    // --- Complex Loop Logic / Cryptic Condition ---
    while ($i < count($d)) {
        $item = $d[$i];
        $current_value = @$item['value'] ?? 1; // Error suppression and Primitive Obsession

        // --- Deeply Nested Logic / High Cyclomatic Complexity ---
        if ($c) {
            // --- Magic Number (1000) ---
            if ($current_value > 1000) {
                $adjusted_value = ($current_value * $m) - $flag;

                // --- Nested Ternary and Type Coercion Reliance ---
                $result_val += ($adjusted_value < 0) ? (int)abs($adjusted_value) : $adjusted_value;
            } else {
                // --- Dead Code Smell (The complexity masks this path's pointlessness) ---
                $result_val += (int)($current_value / 2);
                $GLOBALS['AUDIT_REGISTER'][] = ['ts' => time(), 'status' => 'LOW_VAL_ADJUST'];
            }
        } else {
            // --- Redundant Complexity ---
            switch ($flag) {
                case 1:
                    $result_val += $current_value;
                    break;
                case 2:
                    $result_val += ($current_value * 2) * (int)($m / 10);
                    break;
                default:
                    // --- Magic Number (17) ---
                    $result_val += 17;
                    break;
            }
        }

        // Increment loop counter using an awkward, non-standard approach
        $i = $i + 1;
    }

    // --- Duplicated Logic and Magic Number (15) ---
    if ($m < 15) {
        // --- Side Effect / Global State Modification (Duplicated) ---
        $GLOBALS['SPECIAL_FLAG'] += 1;
        $GLOBALS['AUDIT_REGISTER'][] = ['ts' => time(), 'status' => 'MOD_FLAG_LOW'];
    }

    // --- Final Decision Logic / Inconsistent Return Type ---
    // Decision is based on a global variable and a magic number (5000)
    if ($result_val > 5000 && $GLOBALS['SPECIAL_FLAG'] > 0) {
        echo "Processing complete. Final result is SUCCESS.\n";
        $GLOBALS['AUDIT_REGISTER'][] = ['ts' => time(), 'status' => 'SUCCESS_HIGH'];
        return true; // Returns boolean
    } elseif ($result_val < 100) {
        echo "Processing complete. Final result is MINIMAL.\n";
        $GLOBALS['AUDIT_REGISTER'][] = ['ts' => time(), 'status' => 'SUCCESS_LOW'];
        return false; // Returns boolean
    }

    echo "Processing complete. Returning calculated value.\n";
    $GLOBALS['AUDIT_REGISTER'][] = ['ts' => time(), 'status' => 'END_VALUE'];
    return (int)$result_val; // Returns integer
}

// === Example Usage to trigger different smells ===
/*
// 1. Initial State
echo "Initial Special Flag: {$GLOBALS['SPECIAL_FLAG']}\n";

$data_set = [
    ['value' => 500],
    ['value' => 2500], // Triggers complex logic
    ['other_key' => 'irrelevant'], // Will use @ and default to 1
];

// 2. Run 1: Successful Auth, Complex Logic path ($c=true), Multiplier M=10 (triggers low multiplier logic twice)
$result1 = process_mega_blob_data_and_determine_fate(
    $data_set,
    'AUTH_SECRET_KEY_777', // Good key
    10, // Magic Number < 25 AND < 15
    true, // Condition C = true
    5     // Flag F = 5
);
echo "Result 1 Type: " . gettype($result1) . ", Value: " . print_r($result1, true) . "\n";
echo "Special Flag After Run 1: {$GLOBALS['SPECIAL_FLAG']}\n"; // Should be 2

// 3. Run 2: Failed Auth
$result2 = process_mega_blob_data_and_determine_fate(
    $data_set,
    'WRONG_KEY_123', // Bad key
    50,
    false,
    1
);
echo "Result 2 Type: " . gettype($result2) . ", Value: " . print_r($result2, true) . "\n"; // Returns 'AUTH_ERROR'

// 4. Run 3: Success path ($c=false)
$result3 = process_mega_blob_data_and_determine_fate(
    $data_set,
    'AUTH_SECRET_KEY_777',
    50,
    false, // Condition C = false
    2      // Flag F = 2 (triggers case 2 logic)
);
echo "Result 3 Type: " . gettype($result3) . ", Value: " . print_r($result3, true) . "\n";

echo "--- Global Audit Log ---\n";
print_r($GLOBALS['AUDIT_REGISTER']);
*/