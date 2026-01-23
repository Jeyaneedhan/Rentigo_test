<?php

/**
 * Rent Optimizer Model
 * This is like a "Smart Pricing" tool for landlords.
 * It looks at other houses in Colombo and suggests a competitive rent price.
 */
class M_RentOptimizer
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * The main logic: Suggest a price based on a property's features.
     */
    public function suggestRent($propertyData)
    {
        // Step 1: Find "Comparable" houses (comps) from our DB and the wider market
        $similarProperties = $this->findSimilarProperties($propertyData);

        if (empty($similarProperties)) {
            return [
                'success' => false,
                'message' => 'Not enough comparable properties found in Colombo. Please enter rent manually based on market research.'
            ];
        }

        // Calculate base rent (median)
        $baseRent = $this->calculateBaseRent($similarProperties);

        // Apply adjustment factors
        $adjustments = $this->calculateAdjustments($propertyData, $similarProperties);

        // Calculate suggested rent
        $suggestedRent = $baseRent;
        foreach ($adjustments as $factor) {
            $suggestedRent *= (1 + ($factor['value'] / 100));
        }

        // Calculate rent range (25th-75th percentile)
        $rentRange = $this->calculateRentRange($similarProperties);

        // Calculate confidence score
        $confidence = $this->calculateConfidence(count($similarProperties), $adjustments);

        return [
            'success' => true,
            'suggested_rent' => round($suggestedRent, -2), // Round to nearest 100
            'market_average' => round($baseRent, -2),
            'rent_range' => [
                'min' => round($rentRange['min'], -2),
                'max' => round($rentRange['max'], -2)
            ],
            'confidence' => $confidence,
            'similar_count' => count($similarProperties),
            'breakdown' => $adjustments,
            'data_sources' => $this->getDataSources($similarProperties)
        ];
    }

    /**
     * ALGORITHM: Search two different tables (actual Rentigo listings + general market data)
     * and calculate a "Similarity Score" for each one.
     */
    private function findSimilarProperties($propertyData)
    {
        // UNION query - search both tables
        $this->db->query('
            SELECT 
                id, address, property_type, bedrooms, bathrooms, sqft, rent, 
                parking, pet_policy, laundry, status, "real" as source,
                (
                    CASE WHEN bedrooms = :bedrooms THEN 30 ELSE 0 END +
                    CASE WHEN property_type = :property_type THEN 25 ELSE 0 END +
                    CASE WHEN bathrooms = :bathrooms THEN 20 ELSE 0 END +
                    CASE 
                        WHEN sqft IS NOT NULL AND :sqft IS NOT NULL 
                        THEN GREATEST(0, 15 - (ABS(sqft - :sqft) / 100)) 
                        ELSE 0 
                    END +
                    CASE WHEN parking = :parking THEN 5 ELSE 0 END +
                    CASE WHEN pet_policy = :pet_policy THEN 3 ELSE 0 END +
                    CASE WHEN laundry = :laundry THEN 2 ELSE 0 END
                ) AS similarity_score
            FROM properties 
            WHERE status IN ("occupied", "available")
            AND rent > 0
            
            UNION ALL
            
            -- We also search a "market_properties" table which contains scraped data from other sites
            SELECT 
                id, address, property_type, bedrooms, bathrooms, sqft, rent, 
                parking, pet_policy, laundry, status, "market" as source,
                (
                    CASE WHEN bedrooms = :bedrooms2 THEN 30 ELSE 0 END +
                    CASE WHEN property_type = :property_type2 THEN 25 ELSE 0 END +
                    CASE WHEN bathrooms = :bathrooms2 THEN 20 ELSE 0 END +
                    CASE 
                        WHEN sqft IS NOT NULL AND :sqft2 IS NOT NULL 
                        THEN GREATEST(0, 15 - (ABS(sqft - :sqft2) / 100)) 
                        ELSE 0 
                    END +
                    CASE WHEN parking = :parking2 THEN 5 ELSE 0 END +
                    CASE WHEN pet_policy = :pet_policy2 THEN 3 ELSE 0 END +
                    CASE WHEN laundry = :laundry2 THEN 2 ELSE 0 END
                ) AS similarity_score
            FROM market_properties 
            WHERE status IN ("occupied", "available")
            AND rent > 0
            
            HAVING similarity_score > 30
            ORDER BY similarity_score DESC
            LIMIT 15
        ');

        // Bind parameters for first query (properties table)
        $this->db->bind(':bedrooms', $propertyData['bedrooms']);
        $this->db->bind(':property_type', $propertyData['property_type']);
        $this->db->bind(':bathrooms', $propertyData['bathrooms']);
        $this->db->bind(':sqft', $propertyData['sqft'] ?? null);
        $this->db->bind(':parking', $propertyData['parking'] ?? '0');
        $this->db->bind(':pet_policy', $propertyData['pet_policy'] ?? 'no');
        $this->db->bind(':laundry', $propertyData['laundry'] ?? 'none');

        // Bind parameters for second query (market_properties table)
        $this->db->bind(':bedrooms2', $propertyData['bedrooms']);
        $this->db->bind(':property_type2', $propertyData['property_type']);
        $this->db->bind(':bathrooms2', $propertyData['bathrooms']);
        $this->db->bind(':sqft2', $propertyData['sqft'] ?? null);
        $this->db->bind(':parking2', $propertyData['parking'] ?? '0');
        $this->db->bind(':pet_policy2', $propertyData['pet_policy'] ?? 'no');
        $this->db->bind(':laundry2', $propertyData['laundry'] ?? 'none');

        return $this->db->resultSet();
    }

    /**
     * Calculate base rent (median of similar properties)
     */
    private function calculateBaseRent($properties)
    {
        $rents = array_map(function ($prop) {
            return (float)$prop->rent;
        }, $properties);

        sort($rents);
        $count = count($rents);
        $middle = floor($count / 2);

        // Return median
        if ($count % 2 == 0) {
            return ($rents[$middle - 1] + $rents[$middle]) / 2;
        } else {
            return $rents[$middle];
        }
    }

    /**
     * REFINEMENT: Adjust the base rent up or down based on extra features.
     * e.g., If the house has extra parking or allows pets, we can usually charge more.
     */
    private function calculateAdjustments($propertyData, $similarProperties)
    {
        $adjustments = [];

        // 1. Square footage adjustment
        if (!empty($propertyData['sqft']) && $propertyData['sqft'] > 0) {
            $sqfts = array_filter(array_map(function ($p) {
                return $p->sqft ?? 0;
            }, $similarProperties));

            if (!empty($sqfts)) {
                $avgSqft = array_sum($sqfts) / count($sqfts);

                if ($avgSqft > 0) {
                    $sqftDiff = (($propertyData['sqft'] - $avgSqft) / $avgSqft) * 100;
                    $sqftAdjustment = max(-15, min(15, $sqftDiff * 0.5));

                    if (abs($sqftAdjustment) > 1) {
                        $adjustments[] = [
                            'factor' => 'Square Footage (' . number_format($propertyData['sqft']) . ' sqft)',
                            'value' => round($sqftAdjustment, 1)
                        ];
                    }
                }
            }
        }

        // 2. Parking adjustment
        if (!empty($propertyData['parking']) && $propertyData['parking'] != '0') {
            $parkingValue = is_numeric($propertyData['parking']) ? (int)$propertyData['parking'] : 1;
            $adjustments[] = [
                'factor' => 'Parking (' . $parkingValue . ' space' . ($parkingValue > 1 ? 's' : '') . ')',
                'value' => $parkingValue * 3
            ];
        }

        // 3. Pet policy adjustment
        if (!empty($propertyData['pet_policy']) && $propertyData['pet_policy'] != 'no') {
            $petValue = $propertyData['pet_policy'] == 'both' ? 5 : 3;
            $petLabel = $propertyData['pet_policy'] == 'both' ? 'Pets Allowed (All)' : 'Pets Allowed (' . ucfirst($propertyData['pet_policy']) . ')';
            $adjustments[] = [
                'factor' => $petLabel,
                'value' => $petValue
            ];
        }

        // 4. Laundry adjustment
        if (!empty($propertyData['laundry']) && $propertyData['laundry'] != 'none') {
            $laundryValues = [
                'shared' => 2,
                'hookups' => 4,
                'in_unit' => 7,
                'included' => 10
            ];

            if (isset($laundryValues[$propertyData['laundry']])) {
                $laundryLabel = ucwords(str_replace('_', ' ', $propertyData['laundry']));
                $adjustments[] = [
                    'factor' => 'Laundry: ' . $laundryLabel,
                    'value' => $laundryValues[$propertyData['laundry']]
                ];
            }
        }

        return $adjustments;
    }

    /**
     * Calculate rent range (25th to 75th percentile)
     */
    private function calculateRentRange($properties)
    {
        $rents = array_map(function ($prop) {
            return (float)$prop->rent;
        }, $properties);

        sort($rents);
        $count = count($rents);

        $q1Index = floor($count * 0.25);
        $q3Index = floor($count * 0.75);

        return [
            'min' => $rents[$q1Index],
            'max' => $rents[$q3Index]
        ];
    }

    /**
     * HONESTY CHECK: How much can we trust this suggestion?
     * If we only found 2 similar houses, the confidence is low.
     */
    private function calculateConfidence($similarCount, $adjustments)
    {
        // Base confidence on number of similar properties
        $countConfidence = min(100, $similarCount * 8);

        // Reduce confidence if too many adjustments
        $adjustmentPenalty = count($adjustments) * 3;

        $confidence = max(60, min(100, $countConfidence - $adjustmentPenalty));

        return round($confidence);
    }

    /**
     * Get data sources breakdown
     */
    private function getDataSources($properties)
    {
        $sources = ['real' => 0, 'market' => 0];

        foreach ($properties as $prop) {
            if (isset($prop->source)) {
                $sources[$prop->source]++;
            }
        }

        return $sources;
    }
}
