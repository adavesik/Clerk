<?php

// === Global Data Smell (Again): State controlled outside the class ===
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
    private $is_init = false; // Initialization flag
    private $context_data = []; // Temporary Field/Data Clump storage
    private $unused_var = null; // Dead Code / Unused Variable

    public function __construct($initial_level = 10) {
        // Initialization logic that's far too complex for a constructor.
        $this->g_l = (int) $initial_level;
        $this->context_data['TS'] = microtime(true);
        $this->context_data['RND'] = rand(0, 9999);
        $this->is_init = true; // Mark as initialized
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
            goto PROCESS_HIGH_PRIORITY; // WTF?????
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
// calculate_mega_adjustment(3, 'B', 15, true);
// 

?>