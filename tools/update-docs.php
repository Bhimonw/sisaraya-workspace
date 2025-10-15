<?php
// Simple helper to append a changelog entry to docs/CHANGELOG.md
$projectRoot = __DIR__ . '/..';
$changelog = $projectRoot . '/docs/CHANGELOG.md';
if ($argc < 2) {
    echo "Usage: php tools/update-docs.php \"Short summary of change\"\n";
    exit(1);
}
$summary = $argv[1];
$date = date('Y-m-d');
$entry = "\n## {$date}\n- {$summary}\n";
file_put_contents($changelog, $entry, FILE_APPEND | LOCK_EX);
echo "Changelog updated.\n";
