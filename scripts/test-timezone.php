#!/usr/bin/env php
<?php
/**
 * Timezone Test Script
 * Verifies that timezone is correctly configured
 */

echo "===========================================\n";
echo "TIMEZONE VERIFICATION TEST\n";
echo "===========================================\n\n";

echo "[TEST 1] Default PHP timezone:\n";
echo "  Timezone: " . date_default_timezone_get() . "\n";
echo "  Current time: " . date('Y-m-d H:i:s') . "\n";
echo "  Offset: " . date('P') . "\n\n";

// Simulate what happens in the backup script
date_default_timezone_set('Africa/Johannesburg');

echo "[TEST 2] After setting to Africa/Johannesburg:\n";
echo "  Timezone: " . date_default_timezone_get() . "\n";
echo "  Current time: " . date('Y-m-d H:i:s') . "\n";
echo "  Offset: " . date('P') . "\n\n";

// Show comparison
$utcTime = new DateTime('now', new DateTimeZone('UTC'));
$sastTime = new DateTime('now', new DateTimeZone('Africa/Johannesburg'));

echo "[COMPARISON]\n";
echo "  UTC Time:  " . $utcTime->format('Y-m-d H:i:s') . "\n";
echo "  SAST Time: " . $sastTime->format('Y-m-d H:i:s') . " (UTC+2)\n\n";

echo "===========================================\n";
echo "✓ SAST should be 2 hours ahead of UTC\n";
echo "✓ Current SAST time should match your local time\n";
echo "===========================================\n";
