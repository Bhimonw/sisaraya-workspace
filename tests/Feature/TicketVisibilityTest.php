<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

class TicketVisibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        Role::create(['name' => 'pm']);
        Role::create(['name' => 'member']);
        Role::create(['name' => 'bendahara']);
    }

    /** @test */
    public function tiket_untuk_semua_muncul_di_available_tickets()
    {
        // Arrange: Buat 2 user dengan role berbeda
        $pm = User::factory()->create();
        $pm->assignRole('pm');
        
        $member = User::factory()->create();
        $member->assignRole('member');
        
        // Buat tiket untuk SEMUA (target_role NULL, target_user_id NULL)
        $ticket = Ticket::create([
            'title' => 'Tiket Umum untuk Semua',
            'description' => 'Siapa saja bisa ambil',
            'status' => 'todo',
            'context' => 'umum',
            'priority' => 'medium',
            'weight' => 5,
            'target_role' => null,
            'target_user_id' => null,
            'creator_id' => $pm->id,
            'project_id' => null,
        ]);

        // Act & Assert: PM bisa lihat
        $this->actingAs($pm);
        $response = $this->get(route('tickets.mine'));
        $response->assertStatus(200);
        
        $availableTickets = $response->viewData('availableTickets');
        $this->assertTrue(
            $availableTickets->contains('id', $ticket->id),
            'PM harus bisa melihat tiket untuk semua'
        );

        // Act & Assert: Member juga bisa lihat
        $this->actingAs($member);
        $response = $this->get(route('tickets.mine'));
        $response->assertStatus(200);
        
        $availableTickets = $response->viewData('availableTickets');
        $this->assertTrue(
            $availableTickets->contains('id', $ticket->id),
            'Member harus bisa melihat tiket untuk semua'
        );
    }

    /** @test */
    public function tiket_untuk_role_tertentu_hanya_muncul_untuk_user_dengan_role_itu()
    {
        // Arrange
        $pm = User::factory()->create();
        $pm->assignRole('pm');
        
        $bendahara = User::factory()->create();
        $bendahara->assignRole('bendahara');
        
        // Buat tiket khusus untuk PM
        $ticketForPM = Ticket::create([
            'title' => 'Tiket Khusus PM',
            'status' => 'todo',
            'context' => 'umum',
            'target_role' => 'pm',
            'target_user_id' => null,
            'creator_id' => $pm->id,
            'project_id' => null,
        ]);

        // Act & Assert: PM bisa lihat
        $this->actingAs($pm);
        $response = $this->get(route('tickets.mine'));
        $availableTickets = $response->viewData('availableTickets');
        $this->assertTrue($availableTickets->contains('id', $ticketForPM->id));

        // Act & Assert: Bendahara TIDAK bisa lihat
        $this->actingAs($bendahara);
        $response = $this->get(route('tickets.mine'));
        $availableTickets = $response->viewData('availableTickets');
        $this->assertFalse(
            $availableTickets->contains('id', $ticketForPM->id),
            'Bendahara tidak boleh melihat tiket khusus PM'
        );
    }

    /** @test */
    public function tiket_untuk_user_spesifik_hanya_muncul_untuk_user_tersebut()
    {
        // Arrange
        $creator = User::factory()->create();
        $creator->assignRole('pm');
        
        $targetUser = User::factory()->create();
        $targetUser->assignRole('member');
        
        $otherUser = User::factory()->create();
        $otherUser->assignRole('member');
        
        // Buat tiket khusus untuk targetUser
        $ticket = Ticket::create([
            'title' => 'Tiket Khusus untuk User Tertentu',
            'status' => 'todo',
            'context' => 'umum',
            'target_role' => null,
            'target_user_id' => $targetUser->id,
            'creator_id' => $creator->id,
            'project_id' => null,
        ]);

        // Act & Assert: Target user bisa lihat
        $this->actingAs($targetUser);
        $response = $this->get(route('tickets.mine'));
        $availableTickets = $response->viewData('availableTickets');
        $this->assertTrue($availableTickets->contains('id', $ticket->id));

        // Act & Assert: Other user TIDAK bisa lihat
        $this->actingAs($otherUser);
        $response = $this->get(route('tickets.mine'));
        $availableTickets = $response->viewData('availableTickets');
        $this->assertFalse(
            $availableTickets->contains('id', $ticket->id),
            'User lain tidak boleh melihat tiket yang tidak ditargetkan ke mereka'
        );
    }

    /** @test */
    public function tiket_yang_sudah_diklaim_tidak_muncul_di_available()
    {
        // Arrange
        $pm = User::factory()->create();
        $pm->assignRole('pm');
        
        $member = User::factory()->create();
        $member->assignRole('member');
        
        $ticket = Ticket::create([
            'title' => 'Tiket yang Akan Diklaim',
            'status' => 'todo',
            'context' => 'umum',
            'target_role' => null,
            'target_user_id' => null,
            'creator_id' => $pm->id,
            'project_id' => null,
        ]);

        // Act: PM claim tiket
        $ticket->update([
            'claimed_by' => $pm->id,
            'claimed_at' => now(),
        ]);

        // Assert: Member tidak boleh lihat di available
        $this->actingAs($member);
        $response = $this->get(route('tickets.mine'));
        $availableTickets = $response->viewData('availableTickets');
        $this->assertFalse(
            $availableTickets->contains('id', $ticket->id),
            'Tiket yang sudah diklaim tidak boleh muncul di available'
        );

        // Assert: PM lihat di myTickets
        $this->actingAs($pm);
        $response = $this->get(route('tickets.mine'));
        $myTickets = $response->viewData('myTickets');
        $this->assertTrue(
            $myTickets->contains('id', $ticket->id),
            'PM harus melihat tiket yang sudah diklaim di myTickets'
        );
    }
}
