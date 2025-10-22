<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectRating;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectRatingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run seeders needed for roles/permissions
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    public function test_members_can_rate_completed_projects(): void
    {
        // Create a project owner
        $owner = User::factory()->create();
        $owner->assignRole('pm');
        
        // Create a project member
        $member = User::factory()->create();
        $member->assignRole('researcher');
        
        // Create a completed project
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'status' => 'completed',
        ]);
        
        // Add member to project
        $project->members()->attach($member->id, ['role' => 'member']);
        
        // Member submits a rating
        $response = $this->actingAs($member)->post(route('projects.ratings.store', $project), [
            'rating' => 5,
            'comment' => 'Excellent project!',
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('project_ratings', [
            'project_id' => $project->id,
            'user_id' => $member->id,
            'rating' => 5,
            'comment' => 'Excellent project!',
        ]);
    }

    public function test_non_members_cannot_rate_projects(): void
    {
        // Create a project owner
        $owner = User::factory()->create();
        $owner->assignRole('pm');
        
        // Create a non-member
        $nonMember = User::factory()->create();
        $nonMember->assignRole('researcher');
        
        // Create a completed project
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'status' => 'completed',
        ]);
        
        // Non-member tries to submit a rating
        $response = $this->actingAs($nonMember)->post(route('projects.ratings.store', $project), [
            'rating' => 5,
            'comment' => 'Trying to rate...',
        ]);
        
        // Either 403 Forbidden or 302 Redirect (depending on authorization implementation)
        $this->assertContains($response->status(), [302, 403], 'Expected redirect or forbidden response');
        
        // Main assertion: rating should NOT be saved
        $this->assertDatabaseMissing('project_ratings', [
            'project_id' => $project->id,
            'user_id' => $nonMember->id,
        ]);
    }

    public function test_cannot_rate_non_completed_projects(): void
    {
        // Create a project owner
        $owner = User::factory()->create();
        $owner->assignRole('pm');
        
        // Create an active (not completed) project
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'status' => 'active',
        ]);
        
        // Owner tries to rate their own active project
        $response = $this->actingAs($owner)->post(route('projects.ratings.store', $project), [
            'rating' => 5,
            'comment' => 'Great work so far!',
        ]);
        
        // Either 403 Forbidden or 302 Redirect (depending on authorization implementation)
        $this->assertContains($response->status(), [302, 403], 'Expected redirect or forbidden response');
        
        // Main assertion: rating should NOT be saved for non-completed project
        $this->assertDatabaseMissing('project_ratings', [
            'project_id' => $project->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_rating_must_be_between_1_and_5(): void
    {
        // Create a project owner
        $owner = User::factory()->create();
        $owner->assignRole('pm');
        
        // Create a completed project
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'status' => 'completed',
        ]);
        
        // Try to submit invalid rating (0)
        $response = $this->actingAs($owner)->post(route('projects.ratings.store', $project), [
            'rating' => 0,
            'comment' => 'Invalid rating',
        ]);
        
        $response->assertSessionHasErrors('rating');
        
        // Try to submit invalid rating (6)
        $response = $this->actingAs($owner)->post(route('projects.ratings.store', $project), [
            'rating' => 6,
            'comment' => 'Invalid rating',
        ]);
        
        $response->assertSessionHasErrors('rating');
    }

    public function test_user_can_update_their_rating(): void
    {
        // Create a project owner
        $owner = User::factory()->create();
        $owner->assignRole('pm');
        
        // Create a completed project
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'status' => 'completed',
        ]);
        
        // Submit initial rating
        $this->actingAs($owner)->post(route('projects.ratings.store', $project), [
            'rating' => 3,
            'comment' => 'Good project',
        ]);
        
        // Update the rating
        $response = $this->actingAs($owner)->post(route('projects.ratings.store', $project), [
            'rating' => 5,
            'comment' => 'Actually, excellent project!',
        ]);
        
        $response->assertRedirect();
        
        // Verify only one rating exists and it's updated
        $this->assertEquals(1, ProjectRating::where('project_id', $project->id)->count());
        $this->assertDatabaseHas('project_ratings', [
            'project_id' => $project->id,
            'user_id' => $owner->id,
            'rating' => 5,
            'comment' => 'Actually, excellent project!',
        ]);
    }

    public function test_user_can_delete_their_rating(): void
    {
        // Create a project owner
        $owner = User::factory()->create();
        $owner->assignRole('pm');
        
        // Create a completed project
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'status' => 'completed',
        ]);
        
        // Submit a rating
        $this->actingAs($owner)->post(route('projects.ratings.store', $project), [
            'rating' => 4,
            'comment' => 'Good project',
        ]);
        
        // Delete the rating
        $response = $this->actingAs($owner)->delete(route('projects.ratings.destroy', $project));
        
        $response->assertRedirect();
        $this->assertDatabaseMissing('project_ratings', [
            'project_id' => $project->id,
            'user_id' => $owner->id,
        ]);
    }

    public function test_average_rating_calculated_correctly(): void
    {
        // Create a project owner
        $owner = User::factory()->create();
        $owner->assignRole('pm');
        
        // Create members
        $member1 = User::factory()->create();
        $member1->assignRole('researcher');
        $member2 = User::factory()->create();
        $member2->assignRole('researcher');
        
        // Create a completed project
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'status' => 'completed',
        ]);
        
        // Add members to project
        $project->members()->attach($member1->id, ['role' => 'member']);
        $project->members()->attach($member2->id, ['role' => 'member']);
        
        // Submit ratings: owner=5, member1=4, member2=3
        ProjectRating::create(['project_id' => $project->id, 'user_id' => $owner->id, 'rating' => 5]);
        ProjectRating::create(['project_id' => $project->id, 'user_id' => $member1->id, 'rating' => 4]);
        ProjectRating::create(['project_id' => $project->id, 'user_id' => $member2->id, 'rating' => 3]);
        
        // Verify average: (5+4+3)/3 = 4.0
        $this->assertEquals(4.0, $project->fresh()->averageRating());
    }

    public function test_past_members_can_still_rate_completed_projects(): void
    {
        // Create a project owner
        $owner = User::factory()->create();
        $owner->assignRole('pm');
        
        // Create a member
        $member = User::factory()->create();
        $member->assignRole('researcher');
        
        // Create a completed project
        $project = Project::factory()->create([
            'owner_id' => $owner->id,
            'status' => 'completed',
        ]);
        
        // Add member to project
        $project->members()->attach($member->id, ['role' => 'member']);
        
        // Remove member from project (soft delete)
        $project->members()->updateExistingPivot($member->id, ['deleted_at' => now()]);
        
        // Past member should still be able to rate
        $response = $this->actingAs($member)->post(route('projects.ratings.store', $project), [
            'rating' => 5,
            'comment' => 'Great project, even though I left!',
        ]);
        
        $response->assertRedirect();
        $this->assertDatabaseHas('project_ratings', [
            'project_id' => $project->id,
            'user_id' => $member->id,
            'rating' => 5,
        ]);
    }
}
