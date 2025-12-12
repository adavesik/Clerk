<?php
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