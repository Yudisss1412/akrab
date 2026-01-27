<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class StoreLocationValidationTest extends TestCase
{
    private function validateStoreLocation($latitude, $longitude)
    {
        // Validasi rentang latitude (-90 sampai 90)
        if ($latitude < -90 || $latitude > 90) {
            return false;
        }

        // Validasi rentang longitude (-180 sampai 180)
        if ($longitude < -180 || $longitude > 180) {
            return false;
        }

        // Rentang koordinat Indonesia:
        // Latitude: sekitar -11.083333 hingga 6.066667
        // Longitude: sekitar 95.318249 hingga 140.416667
        $indonesiaMinLat = -11.083333;
        $indonesiaMaxLat = 6.066667;
        $indonesiaMinLng = 95.318249;
        $indonesiaMaxLng = 140.416667;

        return (
            $latitude >= $indonesiaMinLat &&
            $latitude <= $indonesiaMaxLat &&
            $longitude >= $indonesiaMinLng &&
            $longitude <= $indonesiaMaxLng
        );
    }

    public function test_validates_valid_coordinates_within_indonesia(): void
    {
        // Koordinat Jakarta: -6.200000, 106.816666
        $result = $this->validateStoreLocation(-6.200000, 106.816666);
        $this->assertTrue($result);

        // Koordinat Surabaya: -7.257472, 112.752090
        $result = $this->validateStoreLocation(-7.257472, 112.752090);
        $this->assertTrue($result);
    }

    public function test_rejects_coordinates_outside_indonesia_boundary(): void
    {
        // Koordinat Tokyo: 35.6762, 139.6503
        $result = $this->validateStoreLocation(35.6762, 139.6503);
        $this->assertFalse($result);

        // Koordinat London: 51.5074, -0.1278
        $result = $this->validateStoreLocation(51.5074, -0.1278);
        $this->assertFalse($result);
    }

    public function test_rejects_invalid_latitude_values(): void
    {
        // Latitude lebih dari 90 derajat
        $result = $this->validateStoreLocation(91.000000, 106.816666);
        $this->assertFalse($result);

        // Latitude kurang dari -90 derajat
        $result = $this->validateStoreLocation(-91.000000, 106.816666);
        $this->assertFalse($result);
    }

    public function test_rejects_invalid_longitude_values(): void
    {
        // Longitude lebih dari 180 derajat
        $result = $this->validateStoreLocation(-6.200000, 181.000000);
        $this->assertFalse($result);

        // Longitude kurang dari -180 derajat
        $result = $this->validateStoreLocation(-6.200000, -181.000000);
        $this->assertFalse($result);
    }

    public function test_accepts_extreme_valid_indonesian_coordinates(): void
    {
        // Ujung barat Indonesia - Sabang: 5.937291, 95.318249
        $result = $this->validateStoreLocation(5.937291, 95.318249);
        $this->assertTrue($result);

        // Ujung timur Indonesia - Merauke: -8.516667, 140.416667
        $result = $this->validateStoreLocation(-8.516667, 140.416667);
        $this->assertTrue($result);

        // Ujung utara Indonesia - Pulau We: 6.066667, 118.7
        $result = $this->validateStoreLocation(6.066667, 118.7);
        $this->assertTrue($result);

        // Ujung selatan Indonesia - Pulau Ndana: -11.083333, 123.666667
        $result = $this->validateStoreLocation(-11.083333, 123.666667);
        $this->assertTrue($result);
    }
}