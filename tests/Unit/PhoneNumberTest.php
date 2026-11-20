<?php

namespace Tests\Unit;

use App\Support\PhoneNumber;
use PHPUnit\Framework\TestCase;

class PhoneNumberTest extends TestCase
{
    public function test_normalize_keeps_local_format(): void
    {
        $this->assertSame('081234567890', PhoneNumber::normalize('081234567890'));
    }

    public function test_normalize_handles_international_and_separators(): void
    {
        $this->assertSame('081234567890', PhoneNumber::normalize('+62 812-3456-7890'));
        $this->assertSame('081234567890', PhoneNumber::normalize('6281234567890'));
        $this->assertSame('081234567890', PhoneNumber::normalize('(0812) 3456 7890'));
    }

    public function test_normalize_rejects_invalid_input(): void
    {
        $this->assertNull(PhoneNumber::normalize('0812'));
        $this->assertNull(PhoneNumber::normalize('1234567890'));
        $this->assertNull(PhoneNumber::normalize('08123456789012'));
        $this->assertNull(PhoneNumber::normalize('+62'));
        $this->assertNull(PhoneNumber::normalize(''));
        $this->assertNull(PhoneNumber::normalize(null));
    }

    public function test_to_international(): void
    {
        $this->assertSame('6281234567890', PhoneNumber::toInternational('081234567890'));
    }
}
