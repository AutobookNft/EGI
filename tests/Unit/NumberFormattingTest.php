<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * 🧪 Test per Helper di Formattazione Numeri
 *
 * Testa le funzioni di formattazione abbreviata dei numeri per layout mobile
 * - formatNumberAbbreviated()
 * - formatPriceAbbreviated()
 *
 * @package Tests\Unit
 * @author Padmin D. Curtis (implementato da GitHub Copilot)
 * @version 1.0.0
 */
class NumberFormattingTest extends TestCase {
    /**
     * Test basic number formatting without abbreviation
     */
    public function test_small_numbers_no_abbreviation(): void {
        $this->assertEquals('0', formatNumberAbbreviated(0));
        $this->assertEquals('99', formatNumberAbbreviated(99));
        $this->assertEquals('999', formatNumberAbbreviated(999));
    }

    /**
     * Test thousands formatting (K)
     */
    public function test_thousands_formatting(): void {
        $this->assertEquals('1K', formatNumberAbbreviated(1000));
        $this->assertEquals('1,2K', formatNumberAbbreviated(1234));
        $this->assertEquals('12,3K', formatNumberAbbreviated(12345));
        $this->assertEquals('123K', formatNumberAbbreviated(123456));
        $this->assertEquals('1000K', formatNumberAbbreviated(999999)); // 999999 diviso 1000 = 999.999 ≈ 1000K
    }

    /**
     * Test millions formatting (M)
     */
    public function test_millions_formatting(): void {
        $this->assertEquals('1M', formatNumberAbbreviated(1000000));
        $this->assertEquals('1,2M', formatNumberAbbreviated(1234567));
        $this->assertEquals('12,3M', formatNumberAbbreviated(12345678));
        $this->assertEquals('123M', formatNumberAbbreviated(123456789));
        $this->assertEquals('1000M', formatNumberAbbreviated(999999999)); // 999999999 diviso 1M ≈ 1000M
    }

    /**
     * Test billions formatting (B)
     */
    public function test_billions_formatting(): void {
        $this->assertEquals('1B', formatNumberAbbreviated(1000000000));
        $this->assertEquals('1,2B', formatNumberAbbreviated(1234567890));
        $this->assertEquals('12,3B', formatNumberAbbreviated(12345678901));
        $this->assertEquals('123B', formatNumberAbbreviated(123456789012));
    }

    /**
     * Test trillions formatting (T)
     */
    public function test_trillions_formatting(): void {
        $this->assertEquals('1T', formatNumberAbbreviated(1000000000000));
        $this->assertEquals('1,2T', formatNumberAbbreviated(1234567890123));
    }

    /**
     * Test negative numbers
     */
    public function test_negative_numbers(): void {
        $this->assertEquals('-1K', formatNumberAbbreviated(-1000));
        $this->assertEquals('-1,2M', formatNumberAbbreviated(-1234567));
        $this->assertEquals('-999', formatNumberAbbreviated(-999));
    }

    /**
     * Test null and empty values
     */
    public function test_null_and_empty_values(): void {
        $this->assertEquals('0', formatNumberAbbreviated(null));
        $this->assertEquals('0', formatNumberAbbreviated(''));
        $this->assertEquals('0', formatNumberAbbreviated('0'));
    }

    /**
     * Test decimal configuration
     */
    public function test_decimal_configuration(): void {
        // Default: 1 decimal
        $this->assertEquals('1,2K', formatNumberAbbreviated(1234));

        // 0 decimals
        $this->assertEquals('1K', formatNumberAbbreviated(1234, 0));

        // 2 decimals
        $this->assertEquals('1,23K', formatNumberAbbreviated(1234, 2));
    }

    /**
     * Test show zero decimals option
     */
    public function test_show_zero_decimals_option(): void {
        // Default: no .0 for round numbers
        $this->assertEquals('1K', formatNumberAbbreviated(1000));

        // Force showing .0
        $this->assertEquals('1,0K', formatNumberAbbreviated(1000, 1, true));
    }

    /**
     * Test string inputs
     */
    public function test_string_inputs(): void {
        $this->assertEquals('1,2K', formatNumberAbbreviated('1234'));
        $this->assertEquals('1,2M', formatNumberAbbreviated('1234567'));
    }

    /**
     * Test price formatting basic functionality
     */
    public function test_price_formatting_basic(): void {
        $this->assertEquals('€ 0', formatPriceAbbreviated(null));
        $this->assertEquals('€ 0', formatPriceAbbreviated(''));
        $this->assertEquals('€ 999', formatPriceAbbreviated(999));
        $this->assertEquals('€ 1,2K', formatPriceAbbreviated(1234));
        $this->assertEquals('€ 1,2M', formatPriceAbbreviated(1234567));
    }

    /**
     * Test price formatting with different configurations
     */
    public function test_price_formatting_configurations(): void {
        // 0 decimals
        $this->assertEquals('€ 1K', formatPriceAbbreviated(1234, 0));

        // 2 decimals
        $this->assertEquals('€ 1,23K', formatPriceAbbreviated(1234, 2));

        // Show zero decimals
        $this->assertEquals('€ 1,0K', formatPriceAbbreviated(1000, 1, true));
    }

    /**
     * Test realistic EGI scenario values
     */
    public function test_realistic_egi_values(): void {
        // Common EGI price ranges
        $this->assertEquals('€ 250', formatPriceAbbreviated(250));      // Small EGI
        $this->assertEquals('€ 1,5K', formatPriceAbbreviated(1500));    // Medium EGI
        $this->assertEquals('€ 12,5K', formatPriceAbbreviated(12500));  // Premium EGI
        $this->assertEquals('€ 150K', formatPriceAbbreviated(150000));  // High-end EGI
        $this->assertEquals('€ 1,2M', formatPriceAbbreviated(1200000)); // Ultra premium
    }

    /**
     * Test edge cases for different thresholds
     */
    public function test_threshold_edge_cases(): void {
        // Right at thresholds
        $this->assertEquals('999', formatNumberAbbreviated(999));
        $this->assertEquals('1K', formatNumberAbbreviated(1000));
        $this->assertEquals('1000K', formatNumberAbbreviated(999999)); // 999999 diviso 1000 ≈ 1000K
        $this->assertEquals('1M', formatNumberAbbreviated(1000000));
        $this->assertEquals('1000M', formatNumberAbbreviated(999999999)); // 999999999 diviso 1M ≈ 1000M
        $this->assertEquals('1B', formatNumberAbbreviated(1000000000));
    }
}
