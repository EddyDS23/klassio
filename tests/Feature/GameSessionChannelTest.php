<?php

namespace Tests\Feature;

use App\Models\Activity;
use App\Models\GameSession;
use App\Models\GameSessionPresence;
use App\Models\Participation;
use App\Models\SchoolClass;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Broadcast;
use ReflectionProperty;
use Tests\TestCase;

class GameSessionChannelTest extends TestCase
{
    use RefreshDatabase;

    private function user(string $role, string $email): User
    {
        return User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => bcrypt('password'),
            'role' => $role,
            'status' => 'active',
        ]);
    }

    private function activity(User $teacher, string $mode = 'individual'): Activity
    {
        $class = SchoolClass::create([
            'teacher_id' => $teacher->id,
            'name' => 'Channel test',
            'description' => 'Broadcast authorization',
            'code' => strtoupper(fake()->unique()->bothify('CHAN###')),
            'status' => 'active',
        ]);

        return Activity::create([
            'class_id' => $class->id,
            'teacher_id' => $teacher->id,
            'title' => 'Channel activity',
            'description' => 'Test',
            'type' => 'matching',
            'mode' => $mode,
            'max_score' => 100,
            'attempts' => 1,
            'status' => 'published',
        ]);
    }

    private function createSession(Activity $activity): GameSession
    {
        return GameSession::create([
            'activity_id' => $activity->id,
            'code' => 'KLS'.fake()->unique()->numerify('###'),
            'status' => GameSession::STATUS_PLAYING,
            'max_players' => 4,
        ]);
    }

    private function participation(
        GameSession $session,
        Activity $activity,
        ?User $student = null,
        ?Team $team = null,
        string $status = 'started',
    ): Participation {
        return Participation::create([
            'game_session_id' => $session->id,
            'activity_id' => $activity->id,
            'student_id' => $student?->id,
            'team_id' => $team?->id,
            'attempt' => 1,
            'status' => $status,
            'score' => 0,
        ]);
    }

    private function authorizeChannel(?User $user, int $sessionId): mixed
    {
        $broadcaster = Broadcast::connection();
        $channels = (new ReflectionProperty(Broadcaster::class, 'channels'))
            ->getValue($broadcaster);

        return $channels['game-session.{id}']($user, $sessionId);
    }

    public function test_individual_participant_can_authorize_the_session_channel(): void
    {
        $teacher = $this->user('teacher', 'teacher@example.com');
        $student = $this->user('student', 'student@example.com');
        $activity = $this->activity($teacher);
        $session = $this->createSession($activity);
        $this->participation($session, $activity, $student);

        $this->assertTrue($this->authorizeChannel($student, $session->id));
    }

    public function test_team_member_must_join_before_authorizing_but_non_member_cannot(): void
    {
        $teacher = $this->user('teacher', 'teacher@example.com');
        $member = $this->user('student', 'member@example.com');
        $teammate = $this->user('student', 'teammate@example.com');
        $outsider = $this->user('student', 'outsider@example.com');
        $activity = $this->activity($teacher, 'team');
        $session = $this->createSession($activity);
        $team = Team::create(['activity_id' => $activity->id, 'name' => 'Team One']);
        TeamMember::create(['team_id' => $team->id, 'student_id' => $member->id]);
        TeamMember::create(['team_id' => $team->id, 'student_id' => $teammate->id]);
        $this->participation($session, $activity, team: $team);

        $participation = $session->participations()->firstOrFail();
        GameSessionPresence::create([
            'game_session_id' => $session->id,
            'participation_id' => $participation->id,
            'student_id' => $member->id,
            'status' => GameSessionPresence::STATUS_ACTIVE,
            'joined_at' => now(),
        ]);

        $this->assertTrue($this->authorizeChannel($member, $session->id));
        $this->assertFalse($this->authorizeChannel($teammate, $session->id));

        GameSessionPresence::create([
            'game_session_id' => $session->id,
            'participation_id' => $participation->id,
            'student_id' => $teammate->id,
            'status' => GameSessionPresence::STATUS_ACTIVE,
            'joined_at' => now(),
        ]);

        $this->assertTrue($this->authorizeChannel($teammate, $session->id));
        $this->assertFalse($this->authorizeChannel($outsider, $session->id));
    }

    public function test_channel_rejects_other_sessions_activities_and_unauthenticated_users(): void
    {
        $teacher = $this->user('teacher', 'teacher@example.com');
        $student = $this->user('student', 'student@example.com');
        $activity = $this->activity($teacher);
        $session = $this->createSession($activity);
        $this->participation($session, $activity, $student);

        $otherActivity = $this->activity($teacher);
        $otherSession = $this->createSession($otherActivity);

        $this->assertFalse($this->authorizeChannel($student, $otherSession->id));
        $this->assertFalse($this->authorizeChannel(null, $session->id));
    }

    public function test_only_the_activity_teacher_can_authorize_as_teacher(): void
    {
        $teacher = $this->user('teacher', 'teacher@example.com');
        $otherTeacher = $this->user('teacher', 'other@example.com');
        $activity = $this->activity($teacher);
        $session = $this->createSession($activity);

        $this->assertTrue($this->authorizeChannel($teacher, $session->id));
        $this->assertFalse($this->authorizeChannel($otherTeacher, $session->id));
    }
}
