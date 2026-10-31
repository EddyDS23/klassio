<?php

namespace Tests\Unit;

use App\Models\SchoolClass;
use App\Models\User;
use App\Support\PeriodResolver;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
