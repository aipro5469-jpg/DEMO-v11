<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$app->make(Kernel::class)->bootstrap();

\Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

echo "--- DEBUG START ---\n";

$resources = \BezhanSalleh\FilamentShield\Facades\FilamentShield::getResources();
echo "Resources count: " . count($resources) . "\n";

if (count($resources) > 0) {
    // Find AdAsset resource for example
    $adAssetResource = null;
    foreach ($resources as $res) {
        if (str_contains($res['model'], 'AdAsset')) {
            $adAssetResource = $res;
            break;
        }
    }

    if ($adAssetResource) {
        echo "Found AdAsset Resource: " . json_encode($adAssetResource) . "\n";
        $options = \BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents::getResourcePermissionOptions($adAssetResource);
        echo "AdAsset Options Keys: " . json_encode(array_keys($options)) . "\n";
    } else {
        echo "AdAsset Resource not found, using first one.\n";
        $first = $resources[0];
        echo "First Resource: " . json_encode($first) . "\n";
        $options = \BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents::getResourcePermissionOptions($first);
        echo "First Resource Options Keys: " . json_encode(array_keys($options)) . "\n";
    }
}

$role = \Spatie\Permission\Models\Role::where('name', 'viewer')->first();
if ($role) {
    echo "Viewer Role Permissions: " . json_encode($role->permissions->pluck('name')) . "\n";
} else {
    echo "Viewer Role not found.\n";
}

echo "--- DEBUG END ---\n";
