<?php

namespace App\Services\Chat;

use App\Models\ChatbotListing;

class BudgetFallbackService
{
    /**
     * @return array<string, mixed>|null
     */
    public function sameScopeMinimum(SearchData $criteria): ?array
    {
        $locations = config('resolution.locations', []);
        $regionName = null;
        foreach ($locations as $loc) {
            if ($loc['canonical_id'] === $criteria->locationId) {
                $regionName = $loc['canonical_name'];
                break;
            }
        }

        $types = config('resolution.property_types', []);
        $typeName = null;
        foreach ($types as $type) {
            if ($type['canonical_id'] === $criteria->propertyTypeId) {
                $typeName = $type['canonical_name'];
                break;
            }
        }

        $query = ChatbotListing::query()
            ->when($regionName, fn ($q) => $q->where('region', $regionName))
            ->when($typeName, fn ($q) => $q->where('property_type', $typeName));

        $count = (clone $query)->count();
        if ($count === 0) {
            return null;
        }

        return [
            'minimum_available_price' => (int) (clone $query)->min('price'),
            'scope_location' => $criteria->locationName,
            'scope_property_type' => $criteria->propertyTypeName,
            'stated_max_budget' => $criteria->maxBudget,
            'budget_window_max' => $criteria->budgetWindowMax,
            'available_listing_count_in_scope' => $count,
            'prompted_for_budget_adjustment' => true,
        ];
    }
}
