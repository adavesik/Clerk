// === Global state has been entirely removed and encapsulated within the class ===
// The refactored code uses no global variables.

/**
 * Class DataProcessor
 *
 * Provides clean, encapsulated logic for processing data blobs.
 * All magic values are replaced by constants, and logic is split into
 * focused, pure helper methods.
 */
class DataProcessor {

    // === Encapsulated Constants (Replaces Magic Numbers/Strings) ===
    private const AUTH_KEY = 'AUTH_SECRET_KEY_777';
    private const THRESHOLD_HIGH_VALUE = 1000;
    private const THRESHOLD_SPECIAL_FLAG_A = 25;
    private const THRESHOLD_SPECIAL_FLAG_B = 15;
    private const THRESHOLD_RESULT_SUCCESS = 5000;
    private const THRESHOLD_RESULT_MINIMAL = 100;
    private const DEFAULT_CASE_INCREMENT = 17;

    private array $auditRegister = [];
    private int $specialFlag = 0;

    // === Public Getter for Audit Log ===
    public function getAuditRegister(): array {
        return $this->auditRegister;
    }

    // === Public Getter for Special Flag ===
    public function getSpecialFlag(): int {
        return $this->specialFlag;
    }

    /**
     * Executes the main data processing logic.
     *
     * Clean Code Principles Applied:
     * - Clear Naming: Parameters are renamed to descriptive, meaningful words.
     * - Consistent Return Type: Always returns a standardized Result structure.
     * - No Side Effects: Internal state is modified via controlled helper methods.
     * - Single Responsibility: Relies on helper methods for processing and output decision.
     *
     * @param array $dataSet The main data array, expecting 'value' keys.
     * @param string $authKey The authorization key provided by the user.
     * @param int $multiplier The adjustment multiplier.
     * @param bool $useComplexLogic Flag to switch between processing modes.
     * @param int $configFlag External configuration for secondary processing.
     * @return array Standardized result structure containing status, value, and log.
     */
    public function processDataAndDetermineFate(
        array $dataSet,
        string $authKey,
        int $multiplier,
        bool $useComplexLogic,
        int $configFlag
    ): array {
        $this->log('START', "Initiating clean processing.");
        $this->resetState(); // Ensure a clean run

        if (!$this->checkAuthorization($authKey)) {
            return $this->buildResult('AUTH_ERROR', 0, "Authorization Failed.");
        }

        // --- Extracted Logic: Special Flag Adjustment ---
        $this->updateSpecialFlag($multiplier);

        // --- Extracted Logic: Core Data Loop ---
        $calculatedValue = $this->calculateTotalValue($dataSet, $multiplier, $useComplexLogic, $configFlag);

        // --- Final Decision Logic ---
        return $this->determineFinalResult($calculatedValue);
    }

    // === Private Helper Methods (Pure functions where possible) ===

    /**
     * Performs authorization check.
     */
    private function checkAuthorization(string $key): bool {
        if ($key !== self::AUTH_KEY) {
            $this->log('AUTH_FAIL', "Check key: {$key}");
            return false;
        }
        return true;
    }

    /**
     * Updates the special flag based on multiplier thresholds (formerly Duplicated Logic).
     */
    private function updateSpecialFlag(int $multiplier): void {
        if ($multiplier < self::THRESHOLD_SPECIAL_FLAG_A) {
            $this->specialFlag += 1;
        }
        if ($multiplier < self::THRESHOLD_SPECIAL_FLAG_B) {
            $this->specialFlag += 1;
            $this->log('MOD_FLAG_LOW', 'Special flag adjusted due to low multiplier.');
        }
    }

    /**
     * Executes the core data iteration and calculation logic (Single Responsibility).
     */
    private function calculateTotalValue(
        array $dataSet,
        int $multiplier,
        bool $useComplexLogic,
        int $configFlag
    ): int {
        $resultVal = 0;

        foreach ($dataSet as $item) {
            // Eliminated @ (Error Suppression). Use null coalescing with explicit default.
            $currentValue = $item['value'] ?? 1;

            if (!is_numeric($currentValue)) {
                $this->log('DATA_WARN', "Non-numeric value encountered, treating as 1.");
                $currentValue = 1;
            }

            if ($useComplexLogic) {
                $resultVal += $this->calculateComplexAdjustment($currentValue, $multiplier, $configFlag);
            } else {
                $resultVal += $this->calculateSimpleAdjustment($currentValue, $multiplier, $configFlag);
            }
        }

        return (int)$resultVal;
    }

    /**
     * Handles the complex logic path (formerly $c = true).
     */
    private function calculateComplexAdjustment(int $value, int $multiplier, int $configFlag): int {
        if ($value > self::THRESHOLD_HIGH_VALUE) {
            $adjustedValue = ($value * $multiplier) - $configFlag;
            // Simplified ternary logic
            return (int)abs($adjustedValue);
        } else {
            // Formerly Dead Code Smell, now documented as simple adjustment
            $this->log('LOW_VAL_ADJUST', 'Value below high threshold, applying half-value adjustment.');
            return (int)($value / 2);
        }
    }

    /**
     * Handles the simpler switch-case logic path (formerly $c = false).
     */
    private function calculateSimpleAdjustment(int $value, int $multiplier, int $configFlag): int {
        switch ($configFlag) {
            case 1:
                return $value;
            case 2:
                // Type safe calculation
                return ($value * 2) * (int)($multiplier / 10);
            default:
                return self::DEFAULT_CASE_INCREMENT;
        }
    }

    /**
     * Determines the final status and builds the result array (Consistent Return Type).
     */
    private function determineFinalResult(int $finalValue): array {
        if ($finalValue > self::THRESHOLD_RESULT_SUCCESS && $this->specialFlag > 0) {
            $this->log('SUCCESS_HIGH', 'Final result is SUCCESS.', "Processing complete.");
            return $this->buildResult('SUCCESS', $finalValue, 'High value success.');
        } elseif ($finalValue < self::THRESHOLD_RESULT_MINIMAL) {
            $this->log('SUCCESS_LOW', 'Final result is MINIMAL.', "Processing complete.");
            return $this->buildResult('MINIMAL', $finalValue, 'Result too low.');
        }

        $this->log('END_VALUE', 'Returning calculated value.', "Processing complete.");
        return $this->buildResult('VALUE', $finalValue, 'Calculated value returned.');
    }

    /**
     * Standardized structure for method output.
     */
    private function buildResult(string $status, int $value, string $message): array {
        return [
            'status' => $status,
            'value' => $value,
            'message' => $message,
            'special_flag_count' => $this->specialFlag,
            'audit_log' => $this->auditRegister,
        ];
    }

    /**
     * Internal logging utility.
     */
    private function log(string $status, string $message, string $echoMessage = null): void {
        if ($echoMessage !== null) {
            echo "--- $echoMessage ---\n";
        }
        $this->auditRegister[] = [
            'ts' => time(),
            'status' => $status,
            'message' => $message
        ];
    }

    /**
     * Resets internal state for a fresh run.
     */
    private function resetState(): void {
        $this->auditRegister = [];
        $this->specialFlag = 0;
    }
}

// === Example Usage (Reflects the new, clean Class API) ===

$processor = new DataProcessor();

$data_set = [
    ['value' => 500],
    ['value' => 2500], // Triggers complex logic
    ['other_key' => 'irrelevant'], // Will default to 1
];

// 1. Run 1: Successful Auth, Complex Logic path, Multiplier M=10 (triggers low multiplier logic twice)
$result1 = $processor->processDataAndDetermineFate(
    $data_set,
    'AUTH_SECRET_KEY_777',
    10,
    true,
    5
);
echo "--- Result 1 ---\n";
echo "Status: " . $result1['status'] . ", Value: " . $result1['value'] . "\n";
echo "Special Flag After Run 1: {$result1['special_flag_count']}\n"; // Should be 2

// 2. Run 2: Failed Auth
$result2 = $processor->processDataAndDetermineFate(
    $data_set,
    'WRONG_KEY_123',
    50,
    false,
    1
);
echo "--- Result 2 ---\n";
echo "Status: " . $result2['status'] . ", Message: " . $result2['message'] . "\n";

// 3. Run 3: Success path (Simple logic path)
$result3 = $processor->processDataAndDetermineFate(
    $data_set,
    'AUTH_SECRET_KEY_777',
    50,
    false,
    2
);
echo "--- Result 3 ---\n";
echo "Status: " . $result3['status'] . ", Value: " . $result3['value'] . "\n";

echo "--- Last Run Audit Log (Example: Result 3) ---\n";
print_r($result3['audit_log']);