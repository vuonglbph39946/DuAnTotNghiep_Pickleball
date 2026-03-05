<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\Schema;

echo "Columns: " . implode(', ', Schema::getColumnListing('permissions')) . "\n";
echo "First 5 Permissions:\n";
foreach (Permission::take(5)->get() as $p) {
    echo json_encode($p->toArray()) . "\n";
}
