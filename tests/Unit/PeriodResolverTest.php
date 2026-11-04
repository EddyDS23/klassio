<?php

namespace Tests\Unit;

use App\Models\SchoolClass;
use App\Models\User;
use App\Support\PeriodResolver;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class PeriodResolverTest extends TestCase
{
    use RefreshDatabase;

    public function test_period_is_first_half_for_january_through_june(): void
    {
        $this->assertSame('2026-H1', PeriodResolver::forDate(Carbon::create(2026, 1, 15)));
        $this->assertSame('2026-H1', PeriodResolver::forDate(Carbon::create(2026, 6, 30)));
    }

    public function test_period_is_second_half_for_july_through_december(): void
    {
        $this->assertSame('2026-H2', PeriodResolver::forDate(Carbon::create(2026, 7, 1)));
        $this->assertSame('2026-H2', PeriodResolver::forDate(Carbon::create(2026, 12, 31)));
    }

    public function test_period_starts_a_new_cycle_in_january(): void
    {
        $this->assertSame('2027-H1', PeriodResolver::forDate(Carbon::create(2027, 1, 1)));
    }

    public function test_class_period_is_inferred_from_its_creation_date(): void
    {
        $teacher = User::create([
            'name' => 'Teacher',
            'email' => 'teacher@example.com',
            'password' => bcrypt('password'),
            'role' => 'teacher',
            'status' => 'active',
        ]);

        $class = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Test Class',
            'description' => 'Period tests',
            'code' => strtoupper(fake()->unique()->bothify('CLASS###')),
            'status' => 'active',
        ]);

        $this->assertSame(PeriodResolver::forDate($class->created_at), $class->period());
    }

    public function test_label_describes_the_period_in_spanish(): void
    {
        $this->assertSame('Primer semestre 2026 · Ene–Jun', PeriodResolver::label('2026-H1'));
        $this->assertSame('Segundo semestre 2025 · Jul–Dic', PeriodResolver::label('2025-H2'));
    }

    public function test_label_rejects_invalid_codes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PeriodResolver::label('2026-H3');
    }

    public function test_split_translates_codes_to_year_and_half(): void
    {
        $this->assertSame([2026, 1], PeriodResolver::split('2026-H1'));
        $this->assertSame([2025, 2], PeriodResolver::split('2025-H2'));
        $this->assertNull(PeriodResolver::split('26-H1'));
        $this->assertNull(PeriodResolver::split('invalid'));
    }
}
