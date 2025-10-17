<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use App\Notifications\BusinessNeedsApproval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BusinessApprovalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'kewirausahaan']);
        Role::create(['name' => 'pm']);
    }

    /** @test */
    public function kewirausahaan_can_create_business_with_pending_status()
    {
        $user = User::factory()->create();
        $user->assignRole('kewirausahaan');
        $user->givePermissionTo('business.create');

        $response = $this->actingAs($user)->post('/businesses', [
            'name' => 'Usaha Test',
            'description' => 'Deskripsi usaha test',
        ]);

        $this->assertDatabaseHas('businesses', [
            'name' => 'Usaha Test',
            'status' => 'pending',
            'created_by' => $user->id,
        ]);

        $response->assertRedirect(route('businesses.index'));
        $response->assertSessionHas('success', 'Usaha berhasil dibuat. Menunggu persetujuan PM.');
    }

    /** @test */
    public function pm_receives_notification_when_business_created()
    {
        Notification::fake();

        $kewirausahaan = User::factory()->create();
        $kewirausahaan->assignRole('kewirausahaan');
        $kewirausahaan->givePermissionTo('business.create');

        $pm = User::factory()->create();
        $pm->assignRole('pm');

        $this->actingAs($kewirausahaan)->post('/businesses', [
            'name' => 'Usaha Test',
            'description' => 'Test description',
        ]);

        Notification::assertSentTo($pm, BusinessNeedsApproval::class);
    }

    /** @test */
    public function pm_can_approve_pending_business()
    {
        $pm = User::factory()->create();
        $pm->assignRole('pm');
        $pm->givePermissionTo('business.approve');

        $business = Business::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($pm)->post(route('businesses.approve', $business));

        $business->refresh();
        $this->assertEquals('approved', $business->status);
        $this->assertEquals($pm->id, $business->approved_by);
        $this->assertNotNull($business->approved_at);

        $response->assertRedirect(route('businesses.show', $business));
        $response->assertSessionHas('success', 'Usaha berhasil disetujui.');
    }

    /** @test */
    public function pm_can_reject_business_with_reason()
    {
        $pm = User::factory()->create();
        $pm->assignRole('pm');
        $pm->givePermissionTo('business.approve');

        $business = Business::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($pm)->post(route('businesses.reject', $business), [
            'rejection_reason' => 'Tidak sesuai dengan kriteria komunitas',
        ]);

        $business->refresh();
        $this->assertEquals('rejected', $business->status);
        $this->assertEquals($pm->id, $business->approved_by);
        $this->assertNotNull($business->approved_at);
        $this->assertEquals('Tidak sesuai dengan kriteria komunitas', $business->rejection_reason);

        $response->assertRedirect(route('businesses.show', $business));
    }

    /** @test */
    public function non_pm_cannot_approve_business()
    {
        $user = User::factory()->create();
        $user->assignRole('kewirausahaan');

        $business = Business::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($user)->post(route('businesses.approve', $business));

        $response->assertForbidden();
        
        $business->refresh();
        $this->assertEquals('pending', $business->status);
    }

    /** @test */
    public function pm_cannot_approve_already_approved_business()
    {
        $pm = User::factory()->create();
        $pm->assignRole('pm');
        $pm->givePermissionTo('business.approve');

        $business = Business::factory()->create([
            'status' => 'approved',
            'approved_by' => $pm->id,
            'approved_at' => now(),
        ]);

        $response = $this->actingAs($pm)->post(route('businesses.approve', $business));

        $response->assertForbidden();
    }

    /** @test */
    public function rejection_reason_is_required_when_rejecting()
    {
        $pm = User::factory()->create();
        $pm->assignRole('pm');
        $pm->givePermissionTo('business.approve');

        $business = Business::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($pm)->post(route('businesses.reject', $business), [
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors('rejection_reason');
    }

    /** @test */
    public function businesses_can_be_filtered_by_status()
    {
        $user = User::factory()->create();
        $user->givePermissionTo('business.view');

        Business::factory()->create(['status' => 'pending', 'name' => 'Pending Business']);
        Business::factory()->create(['status' => 'approved', 'name' => 'Approved Business']);
        Business::factory()->create(['status' => 'rejected', 'name' => 'Rejected Business']);

        // Filter pending
        $response = $this->actingAs($user)->get(route('businesses.index', ['status' => 'pending']));
        $response->assertSee('Pending Business');
        $response->assertDontSee('Approved Business');

        // Filter approved
        $response = $this->actingAs($user)->get(route('businesses.index', ['status' => 'approved']));
        $response->assertSee('Approved Business');
        $response->assertDontSee('Pending Business');
    }
}
