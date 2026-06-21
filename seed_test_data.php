<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\ChatbotListing;
use App\Models\ChatbotListingFeature;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Create test user
$user = User::firstOrCreate(
    ['email' => 'test@aqary.com'],
    [
        'name' => 'Test User',
        'password' => Hash::make('password'),
    ]
);

// Create Sanctum token
$user->tokens()->delete();
$token = $user->createToken('test-token');
echo "TOKEN: " . $token->plainTextToken . "\n";

// Seed feature records
$features = [
    ['name' => 'Security', 'slug' => 'security'],
    ['name' => 'Parking', 'slug' => 'parking'],
    ['name' => 'Garden', 'slug' => 'garden'],
    ['name' => 'Pool', 'slug' => 'pool'],
    ['name' => 'Elevator', 'slug' => 'elevator'],
];

foreach ($features as $f) {
    ChatbotListingFeature::firstOrCreate(
        ['slug' => $f['slug']],
        $f
    );
}

// Seed managed aliases (matching config/resolution.php)
$aliases = [
    // Locations
    ['preference_type' => 'location', 'canonical_id' => 1, 'canonical_name' => 'New Cairo', 'alias' => 'new cairo'],
    ['preference_type' => 'location', 'canonical_id' => 1, 'canonical_name' => 'New Cairo', 'alias' => 'tagamoa'],
    ['preference_type' => 'location', 'canonical_id' => 1, 'canonical_name' => 'New Cairo', 'alias' => 'التجمع الخامس'],
    ['preference_type' => 'location', 'canonical_id' => 2, 'canonical_name' => 'Maadi', 'alias' => 'maadi'],
    ['preference_type' => 'location', 'canonical_id' => 2, 'canonical_name' => 'Maadi', 'alias' => 'المعادي'],
    ['preference_type' => 'location', 'canonical_id' => 3, 'canonical_name' => 'Sheikh Zayed', 'alias' => 'sheikh zayed'],
    ['preference_type' => 'location', 'canonical_id' => 3, 'canonical_name' => 'Sheikh Zayed', 'alias' => 'الشيخ زايد'],
    // Property types
    ['preference_type' => 'propertyType', 'canonical_id' => 101, 'canonical_name' => 'Apartment', 'alias' => 'apartment'],
    ['preference_type' => 'propertyType', 'canonical_id' => 101, 'canonical_name' => 'Apartment', 'alias' => 'flat'],
    ['preference_type' => 'propertyType', 'canonical_id' => 101, 'canonical_name' => 'Apartment', 'alias' => 'شقة'],
    ['preference_type' => 'propertyType', 'canonical_id' => 102, 'canonical_name' => 'Villa', 'alias' => 'villa'],
    ['preference_type' => 'propertyType', 'canonical_id' => 102, 'canonical_name' => 'Villa', 'alias' => 'فيلا'],
    ['preference_type' => 'propertyType', 'canonical_id' => 103, 'canonical_name' => 'Townhouse', 'alias' => 'townhouse'],
    // Features
    ['preference_type' => 'features', 'canonical_id' => 201, 'canonical_name' => 'Security', 'alias' => 'security'],
    ['preference_type' => 'features', 'canonical_id' => 201, 'canonical_name' => 'Security', 'alias' => 'امن'],
    ['preference_type' => 'features', 'canonical_id' => 202, 'canonical_name' => 'Parking', 'alias' => 'parking'],
    ['preference_type' => 'features', 'canonical_id' => 203, 'canonical_name' => 'Garden', 'alias' => 'garden'],
];

DB::table('managed_aliases')->truncate();
foreach ($aliases as $alias) {
    DB::table('managed_aliases')->insert([
        'preference_type' => $alias['preference_type'],
        'canonical_id' => $alias['canonical_id'],
        'canonical_name' => $alias['canonical_name'],
        'alias' => $alias['alias'],
        'active' => true,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

// Seed listings
$listingsData = [
    ['title' => 'Modern Apartment in New Cairo', 'price' => 2500000, 'area' => 140, 'bedrooms' => 2, 'bathrooms' => 2, 'furnished_status' => 'Furnished', 'location_id' => 1, 'location_name' => 'New Cairo', 'property_type_id' => 101, 'features' => [201, 202]],
    ['title' => 'Luxury Apartment in New Cairo', 'price' => 3500000, 'area' => 180, 'bedrooms' => 3, 'bathrooms' => 2, 'furnished_status' => 'Semi furnished', 'location_id' => 1, 'location_name' => 'New Cairo', 'property_type_id' => 101, 'features' => [201, 202, 203]],
    ['title' => 'Spacious Villa in Sheikh Zayed', 'price' => 5000000, 'area' => 350, 'bedrooms' => 5, 'bathrooms' => 4, 'furnished_status' => 'Unfurnished', 'location_id' => 3, 'location_name' => 'Sheikh Zayed', 'property_type_id' => 102, 'features' => [201, 203]],
    ['title' => 'Cozy Apartment in Maadi', 'price' => 1800000, 'area' => 100, 'bedrooms' => 2, 'bathrooms' => 1, 'furnished_status' => 'Furnished', 'location_id' => 2, 'location_name' => 'Maadi', 'property_type_id' => 101, 'features' => [202]],
    ['title' => 'Family Townhouse in New Cairo', 'price' => 4200000, 'area' => 250, 'bedrooms' => 4, 'bathrooms' => 3, 'furnished_status' => 'Unfurnished', 'location_id' => 1, 'location_name' => 'New Cairo', 'property_type_id' => 103, 'features' => [201, 203]],
    ['title' => 'Elegant Villa in Maadi', 'price' => 6000000, 'area' => 400, 'bedrooms' => 6, 'bathrooms' => 5, 'furnished_status' => 'Furnished', 'location_id' => 2, 'location_name' => 'Maadi', 'property_type_id' => 102, 'features' => [201, 202, 203, 204]],
];

ChatbotListing::query()->delete();
DB::table('chatbot_listing_feature')->truncate();

$featureModels = ChatbotListingFeature::all()->keyBy('id');

foreach ($listingsData as $data) {
    $featureIds = $data['features'];
    unset($data['features']);

    $listing = ChatbotListing::create($data + [
        'url' => 'https://aqary.test/listings/' . Str::random(8),
        'cover_image_url' => 'https://picsum.photos/seed/' . uniqid() . '/400/300',
        'is_promoted' => false,
        'status' => 'active',
        'payment_type' => 'cash',
        'seller_phone' => '+201001234567',
    ]);

    foreach ($featureIds as $fid) {
        if ($featureModels->has($fid)) {
            $listing->features()->attach($fid);
        }
    }

    echo "Created listing: {$listing->title} (ID: {$listing->id})\n";
}

echo "\nDONE. User ID: {$user->id}\n";
