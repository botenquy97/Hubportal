<?php
header('Content-Type: application/json');

$results = [
    'current_dir' => getcwd(),
    'files_in_root' => scandir('.'),
    'uploads_exists' => is_dir('uploads'),
    'uploads_writable' => is_writable('uploads'),
    'uploads_contents' => is_dir('uploads') ? scandir('uploads') : null,
    'php_user' => get_current_user(),
];

echo json_encode($results, JSON_PRETTY_PRINT);
?>
