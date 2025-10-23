@extends('layouts.app')

@section('content')

{{-- Define Alpine.js functions BEFORE they are used --}}
<script>
function projectChatPopup(projectId) {
    return {
        projectId: projectId,
        showChat: false,
        messages: [],
        newMessage: '',
        loading: false,
        sending: false,
        lastId: 0,
        pollInterval: null,
        unreadCount: 0,
        
        init() {
            console.log('Project chat popup initialized for project:', this.projectId);
            // Start background polling for notifications even when chat is closed
            this.startBackgroundPolling();
        },
        
        toggleChat() {
            this.showChat = !this.showChat;
            if (this.showChat) {
                this.unreadCount = 0; // Reset unread when opening
                this.loadInitialMessages();
            } else {
                // Don't stop polling, keep checking for new messages
            }
        },
        
        async loadInitialMessages() {
            this.loading = true;
            try {
                const response = await fetch(`/api/projects/${this.projectId}/chat/messages/initial`);
                if (!response.ok) throw new Error('Failed to load messages');
                
                const data = await response.json();
                this.messages = data.messages;
                this.lastId = data.last_id;
                
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            } catch (error) {
                console.error('Error loading messages:', error);
                alert('Gagal memuat pesan. Silakan coba lagi.');
            } finally {
                this.loading = false;
            }
        },
        
        async pollNewMessages() {
            try {
                const response = await fetch(`/api/projects/${this.projectId}/chat/messages?last_id=${this.lastId}`);
                if (!response.ok) return;
                
                const data = await response.json();
                if (data.messages.length > 0) {
                    this.messages = [...this.messages, ...data.messages];
                    this.lastId = data.last_id;
                    
                    // Update unread count if chat is closed
                    if (!this.showChat) {
                        this.unreadCount += data.messages.length;
                    }
                    
                    this.$nextTick(() => {
                        if (this.showChat) {
                            this.scrollToBottom();
                        }
                    });
                }
            } catch (error) {
                console.error('Error polling messages:', error);
            }
        },
        
        async sendMessage() {
            if (!this.newMessage.trim() || this.sending) return;
            
            this.sending = true;
            const messageText = this.newMessage.trim();
            this.newMessage = '';
            
            try {
                const response = await fetch(`/api/projects/${this.projectId}/chat/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: messageText })
                });
                
                if (!response.ok) throw new Error('Failed to send message');
                
                const data = await response.json();
                this.messages.push(data.message);
                this.lastId = data.message.id;
                
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Gagal mengirim pesan. Silakan coba lagi.');
                this.newMessage = messageText; // Restore message
            } finally {
                this.sending = false;
            }
        },
        
        startBackgroundPolling() {
            this.pollInterval = setInterval(() => {
                this.pollNewMessages();
            }, 5000); // Poll every 5 seconds
        },
        
        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },
        
        handleScroll() {
            // Future: implement load more on scroll to top
        }
    }
}

function targetUserFilter() {
    return {
        searchQuery: '',
        roleFilter: 'all',
        visibleCount: 0,
        selectedCount: 0,
        
        init() {
            this.updateCounts();
            this.$watch('searchQuery', () => this.updateCounts());
            this.$watch('roleFilter', () => this.updateCounts());
            // Listen for selection changes
            window.addEventListener('target-user-changed', () => this.updateCounts());
        },
        
        isVisible(memberName, isAdmin, hasPermanentRole, hasEventRole) {
            // Search filter
            const matchesSearch = !this.searchQuery || 
                                memberName.toLowerCase().includes(this.searchQuery.toLowerCase());
            
            // Role filter
            let matchesRole = true;
            if (this.roleFilter === 'admin') {
                matchesRole = isAdmin;
            } else if (this.roleFilter === 'permanent') {
                matchesRole = hasPermanentRole;
            } else if (this.roleFilter === 'event') {
                matchesRole = hasEventRole;
            }
            // 'all' matches everything
            
            return matchesSearch && matchesRole;
        },
        
        updateCounts() {
            const items = document.querySelectorAll('.target-user-item');
            let visible = 0;
            let selected = 0;
            
            items.forEach(item => {
                const memberName = item.dataset.memberName;
                const isAdmin = item.dataset.isAdmin === 'true';
                const hasPermanent = item.dataset.hasPermanent === 'true';
                const hasEvent = item.dataset.hasEvent === 'true';
                
                if (this.isVisible(memberName, isAdmin, hasPermanent, hasEvent)) {
                    visible++;
                    const checkbox = item.querySelector('.target-user-checkbox');
                    if (checkbox && checkbox.checked) {
                        selected++;
                    }
                }
            });
            
            this.visibleCount = visible;
            this.selectedCount = selected;
        },
        
        selectAllVisible() {
            const items = document.querySelectorAll('.target-user-item');
            
            items.forEach(item => {
                const memberName = item.dataset.memberName;
                const isAdmin = item.dataset.isAdmin === 'true';
                const hasPermanent = item.dataset.hasPermanent === 'true';
                const hasEvent = item.dataset.hasEvent === 'true';
                
                if (this.isVisible(memberName, isAdmin, hasPermanent, hasEvent)) {
                    const checkbox = item.querySelector('.target-user-checkbox');
                    if (checkbox && !checkbox.checked) {
                        checkbox.click(); // Trigger Alpine's x-model
                    }
                }
            });
        }
    }
}

function memberManagement() {
    return {
        showAddMember: false,
        showManageMembers: false,
        selectedMembers: [],
        searchQuery: '',
        projectRoleFilter: 'all',
        visibleMembersCount: 0,
        
        init() {
            // Update visible count initially
            this.updateVisibleCount();
            // Watch for filter changes
            this.$watch('searchQuery', () => this.updateVisibleCount());
            this.$watch('projectRoleFilter', () => this.updateVisibleCount());
        },
        
        isMemberVisible(memberName, memberRole) {
            // Search filter
            const matchesSearch = !this.searchQuery || 
                                memberName.toLowerCase().includes(this.searchQuery.toLowerCase());
            
            // Role filter
            const matchesRole = this.projectRoleFilter === 'all' || 
                               this.projectRoleFilter === memberRole;
            
            return matchesSearch && matchesRole;
        },
        
        updateVisibleCount() {
            // Count visible member cards
            const cards = document.querySelectorAll('.member-card');
            let visible = 0;
            
            cards.forEach(card => {
                const memberName = card.dataset.memberName;
                const memberRole = card.dataset.memberRole;
                
                if (this.isMemberVisible(memberName, memberRole)) {
                    visible++;
                }
            });
            
            this.visibleMembersCount = visible;
        },
        
        toggleMemberSelection(memberId, isChecked, isPermanent) {
            if (isPermanent) return; // Don't allow selection of permanent role members
            
            if (isChecked) {
                if (!this.selectedMembers.includes(memberId)) {
                    this.selectedMembers.push(memberId);
                }
            } else {
                this.selectedMembers = this.selectedMembers.filter(id => id !== memberId);
            }
        },
        
        toggleSelectAll(isChecked) {
            // Only select checkboxes in member cards (not in add member form)
            const checkboxes = document.querySelectorAll('.member-card input[type="checkbox"][value]:not([disabled])');
            this.selectedMembers = [];
            
            checkboxes.forEach(cb => {
                if (cb.hasAttribute('value')) {
                    cb.checked = isChecked;
                    if (isChecked) {
                        const memberId = parseInt(cb.value);
                        if (!this.selectedMembers.includes(memberId)) {
                            this.selectedMembers.push(memberId);
                        }
                    }
                }
            });
        },
        
        async bulkChangeRole(newRole) {
            if (this.selectedMembers.length === 0) {
                alert('Pilih minimal 1 member terlebih dahulu');
                return;
            }
            
            const roleLabel = newRole === 'admin' ? 'Admin Project' : 'Member';
            if (!confirm(`Ubah ${this.selectedMembers.length} member menjadi ${roleLabel}?`)) {
                return;
            }
            
            try {
                const response = await fetch('{{ route("projects.members.bulkUpdateRole", $project) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        member_ids: this.selectedMembers,
                        role: newRole
                    })
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Gagal mengubah role members');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        },
        
        async bulkDeleteMembers() {
            if (this.selectedMembers.length === 0) {
                alert('Pilih minimal 1 member terlebih dahulu');
                return;
            }
            
            if (!confirm(`Hapus ${this.selectedMembers.length} member dari project ini?`)) {
                return;
            }
            
            try {
                const response = await fetch('{{ route("projects.members.bulkDelete", $project) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        member_ids: this.selectedMembers
                    })
                });
                
                if (response.ok) {
                    window.location.reload();
                } else {
                    alert('Gagal menghapus members');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            }
        }
    }
}

function addMemberFilter() {
    return {
        searchQuery: '',
        selectedRoles: [],
        selectedCount: 0,
        visibleCount: 0,
        // Load roles from config via RoleHelper
        availableRoles: @json(\App\Helpers\RoleHelper::getRolesForAlpine()),

        init() {
            // Count users per role
            this.countRoles();
            // Update counts initially
            this.updateCounts();
            // Listen for member changes
            window.addEventListener('add-member-changed', () => this.updateCounts());
            // Watch for search and filter changes
            this.$watch('searchQuery', () => this.updateCounts());
            this.$watch('selectedRoles', () => this.updateCounts());
        },

        countRoles() {
            const rows = document.querySelectorAll('.add-member-row');
            rows.forEach(row => {
                const roles = JSON.parse(row.dataset.userRoles || '[]');
                roles.forEach(role => {
                    const roleObj = this.availableRoles.find(r => r.value === role);
                    if (roleObj) roleObj.count++;
                });
            });
        },

        isVisible(userName, userRoles) {
            // Search filter
            const matchesSearch = !this.searchQuery || 
                                userName.toLowerCase().includes(this.searchQuery.toLowerCase());
            
            // Role filter
            const matchesRole = this.selectedRoles.length === 0 || 
                               userRoles.some(role => this.selectedRoles.includes(role));
            
            return matchesSearch && matchesRole;
        },

        updateCounts() {
            // Count visible members
            const rows = document.querySelectorAll('.add-member-row');
            let visible = 0;
            let selected = 0;
            
            rows.forEach(row => {
                const userName = row.dataset.userName;
                const userRoles = JSON.parse(row.dataset.userRoles || '[]');
                const isVisible = this.isVisible(userName, userRoles);
                
                if (isVisible) {
                    visible++;
                    const checkbox = row.querySelector('.add-member-checkbox');
                    if (checkbox && checkbox.checked) {
                        selected++;
                    }
                }
            });
            
            this.visibleCount = visible;
            this.selectedCount = selected;
        },

        toggleAllMembers() {
            const selectAllCheckbox = document.getElementById('select-all-add-members');
            const memberCheckboxes = document.querySelectorAll('.add-member-checkbox');
            
            selectAllCheckbox.checked = !selectAllCheckbox.checked;
            const isChecked = selectAllCheckbox.checked;
            
            // Only toggle visible checkboxes
            memberCheckboxes.forEach(checkbox => {
                const row = checkbox.closest('.add-member-row');
                const isVisible = row && window.getComputedStyle(row).display !== 'none';
                
                if (isVisible && checkbox.checked !== isChecked) {
                    checkbox.click(); // Trigger click to maintain Alpine.js state
                }
            });
        }
    }
}
</script>

<div class="relative" x-data="{ 
    activeTab: 'overview',
    showTicketModal: false,
    selectedTicket: null,
    showTicket(ticket) {
        this.selectedTicket = ticket;
        this.showTicketModal = true;
    },
    init() {
        console.log('Alpine initialized with:', {
            activeTab: this.activeTab,
            showTicketModal: this.showTicketModal,
            selectedTicket: this.selectedTicket
        });
        // Watch for tab changes to refresh calendar
        this.$watch('activeTab', (value) => {
            if (value === 'overview') {
                // Trigger calendar refresh after a short delay
                setTimeout(() => {
                    if (window.projectCalendar) {
                        console.log('[TAB] Tab switched to overview - refreshing calendar');
                        // Tui Calendar doesn't need render() call
                        // Just log that tab is visible
                    }
                }, 200);
            }
        });
    }
}">
    @include('projects.partials.project-header', ['project' => $project])

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="flex border-b border-gray-200 overflow-x-auto">
            {{-- Overview Tab --}}
            <button @click="activeTab = 'overview'" 
                    :class="activeTab === 'overview' ? 'border-b-2 border-violet-600 text-violet-600' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 font-medium text-sm whitespace-nowrap transition-all duration-200">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <span>Overview & Tickets</span>
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-gray-100">{{ $project->tickets->count() }}</span>
                </div>
            </button>

            {{-- Members Tab (Visible to All) --}}
            <button @click="activeTab = 'members'" 
                    :class="activeTab === 'members' ? 'border-b-2 border-violet-600 text-violet-600' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 font-medium text-sm whitespace-nowrap transition-all duration-200">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span>Member</span>
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-gray-100">{{ $project->members->count() }}</span>
                </div>
            </button>

            {{-- Events Tab (Visible to All) --}}
            <button @click="activeTab = 'events'" 
                    :class="activeTab === 'events' ? 'border-b-2 border-violet-600 text-violet-600' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 font-medium text-sm whitespace-nowrap transition-all duration-200">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Event Proyek</span>
                    <span class="ml-1 px-2 py-0.5 text-xs rounded-full bg-gray-100">{{ $project->events->count() }}</span>
                </div>
            </button>

            {{-- Project Settings Tab (PM or Admin) --}}
            @if($project->canManage(Auth::user()))
            <button @click="activeTab = 'settings'" 
                    :class="activeTab === 'settings' ? 'border-b-2 border-violet-600 text-violet-600' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 font-medium text-sm whitespace-nowrap transition-all duration-200">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Kelola Proyek</span>
                </div>
            </button>
            @endif

            {{-- Create Ticket Tab (PM or Admin) --}}
            @if($project->canManage(Auth::user()))
            <button @click="activeTab = 'create-ticket'" 
                    :class="activeTab === 'create-ticket' ? 'border-b-2 border-violet-600 text-violet-600' : 'text-gray-600 hover:text-gray-800'"
                    class="px-6 py-3 font-medium text-sm whitespace-nowrap transition-all duration-200">
                <div class="flex items-center gap-2">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Buat Tiket Baru</span>
                </div>
            </button>
            @endif
        </div>
    </div>

    {{-- Tab Content --}}
    <div>
        {{-- OVERVIEW TAB --}}
        <div x-show="activeTab === 'overview'" x-transition>
        
        {{-- Grid Layout: Main Content (Left 2/3) + Sidebar (Right 1/3) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Tiket, Kanban, Event (2 columns = 2/3 width) --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Tiket Tersedia Section --}}
                @php
                    $availableTickets = $project->tickets->filter(function($ticket) {
                        // Tampilkan tiket yang:
                        // 1. Belum di-claim dan bisa di-claim oleh user, ATAU
                        // 2. Sudah di-claim oleh current user (untuk mulai/selesai)
                        return (!$ticket->isClaimed() && $ticket->canBeClaimedBy(auth()->user())) 
                            || ($ticket->claimed_by === auth()->id());
                    });
                @endphp
                
                @include('projects.partials.available-tickets-section', ['availableTickets' => $availableTickets])

                {{-- Kanban Board untuk Member (Tiket Saya) - Enhanced Design --}}
                @cannot('update', $project)
                    @include('projects.partials.kanban-member', ['project' => $project])
                @endcannot

                {{-- Tiket Umum Section - Enhanced Design --}}
                @include('projects.partials.general-tickets-section', ['project' => $project])

                {{-- Kanban Section untuk PM/Admin --}}
                @can('update', $project)
                    @include('projects.partials.kanban-admin', ['project' => $project])
                @endcan
                
                @include('projects.partials.events-quick-view', ['project' => $project])

            </div>{{-- End Left Column --}}
            
            {{-- Right Sidebar: Kalender (1 column = 1/3 width) - Sticky --}}
            <div class="lg:col-span-1">
                <div class="lg:sticky lg:top-6">
                
                {{-- Kalender Proyek Widget --}}
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-3">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="font-semibold text-white">Kalender Proyek</h3>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        {{-- Info Box --}}
                        <div class="text-xs space-y-1 mb-4 p-3 bg-blue-50 border-l-4 border-blue-500 rounded">
                            <p class="font-medium text-blue-900">Kalender Proyek Ini:</p>
                            <ul class="text-[11px] text-blue-800 space-y-0.5 ml-2">
                                <li>• <strong>Timeline Proyek</strong> - Rentang waktu proyek (highlight biru muda)</li>
                                <li>• <strong>Event Proyek</strong> - Acara dan kegiatan proyek ini</li>
                                <li>• <strong>Tiket Proyek</strong> - Deadline tiket proyek ini</li>
                            </ul>
                            <p class="text-[11px] text-blue-700 mt-2 italic">
                                * Hanya menampilkan data dari proyek "{{ $project->name }}"
                            </p>
                        </div>
                        
                        {{-- Simple PHP Calendar (No JS Library!) --}}
                        <div class="bg-white rounded-lg border border-gray-200" x-data="calendarNavigation({{ $calendar['year'] }}, {{ $calendar['monthNum'] }}, {{ $project->id }})">
                            {{-- Calendar Header --}}
                            <div class="flex items-center justify-between p-4 border-b">
                                <button @click="prevMonth()" 
                                        :disabled="loading"
                                        class="p-2 hover:bg-gray-100 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                    </svg>
                                </button>
                                <div class="text-center">
                                    <h3 class="font-semibold text-lg" x-text="monthName + ' ' + year"></h3>
                                    <template x-if="loading">
                                        <p class="text-xs text-gray-500 mt-1">Memuat...</p>
                                    </template>
                                </div>
                                <button @click="nextMonth()" 
                                        :disabled="loading"
                                        class="p-2 hover:bg-gray-100 rounded transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            </div>
                            
                            {{-- Calendar Grid Container --}}
                            <div id="calendar-grid-container">
                                @include('projects.partials.calendar-grid', ['calendar' => $calendar, 'project' => $project])
                            </div>
                        </div>
                        
                        {{-- Legend --}}
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-xs font-semibold text-gray-900 mb-2">Legenda Kalender</p>
                            
                            {{-- Timeline Highlight --}}
                            @if($project->start_date && $project->end_date)
                            <div class="mb-3 pb-3 border-b border-gray-200">
                                <p class="text-xs font-semibold text-indigo-900 mb-2">Rentang Timeline Proyek:</p>
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-6 rounded bg-indigo-50 border border-indigo-300 relative">
                                            <div class="absolute top-0 left-0 right-0 h-1 bg-indigo-400"></div>
                                        </div>
                                        <span class="text-gray-700">Background biru muda + garis atas</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="px-2 py-0.5 bg-white rounded text-[10px] font-bold text-indigo-700 border-l-4 border-indigo-600">
                                            Mulai
                                        </div>
                                        <span class="text-gray-700">Tanggal mulai proyek</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="px-2 py-0.5 bg-white rounded text-[10px] font-bold text-indigo-700 border-r-4 border-indigo-600">
                                            Selesai
                                        </div>
                                        <span class="text-gray-700">Tanggal selesai proyek</span>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            {{-- Event & Ticket Colors --}}
                            <div class="mb-3">
                                <p class="text-xs font-semibold text-gray-900 mb-2">Event & Tiket:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-purple-500"></div>
                                        <span class="text-gray-700">Event</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-gray-500"></div>
                                        <span class="text-gray-700">To Do</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-blue-500"></div>
                                        <span class="text-gray-700">Doing</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-green-500"></div>
                                        <span class="text-gray-700">Done</span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Project Status Colors (in timeline) --}}
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-900 mb-2">Warna Status Timeline:</p>
                                <div class="grid grid-cols-2 gap-2 text-xs">
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-gray-600"></div>
                                        <span class="text-gray-700">Perencanaan</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-indigo-600"></div>
                                        <span class="text-gray-700">Aktif</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-yellow-600"></div>
                                        <span class="text-gray-700">Ditunda</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-3 h-3 rounded bg-green-600"></div>
                                        <span class="text-gray-700">Selesai</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                </div>{{-- End Sticky Container --}}
            </div>{{-- End Right Sidebar --}}
        </div>{{-- End Grid Layout --}}

        {{-- Bottom Section: Evaluasi (Full Width) --}}
        <div class="mt-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-violet-600 to-purple-600 px-4 py-3">
                    <div class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="font-semibold text-white">Evaluasi Proyek</h3>
                    </div>
                </div>
                
                <div class="p-6">
                    {{-- Form untuk Researcher menambahkan evaluasi --}}
                    @if(auth()->user()->hasRole('researcher'))
                    <div class="mb-6 bg-gradient-to-br from-purple-50 to-indigo-50 rounded-xl p-6 border-2 border-purple-200">
                        <h4 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Tambah Catatan Evaluasi
                        </h4>
                        
                        <form action="{{ route('evaluations.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <input type="hidden" name="evaluable_type" value="App\Models\Project">
                            <input type="hidden" name="evaluable_id" value="{{ $project->id }}">
                            
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                                    Catatan Evaluasi <span class="text-red-500">*</span>
                                </label>
                                <textarea 
                                    name="notes" 
                                    id="notes" 
                                    rows="4" 
                                    required
                                    placeholder="Tuliskan catatan evaluasi, analisis, atau rekomendasi untuk proyek ini..."
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"></textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select 
                                    name="status" 
                                    id="status" 
                                    required
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                                    <option value="draft">Draft (Hanya Researcher yang bisa lihat)</option>
                                    <option value="published">Published (Semua anggota bisa lihat)</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="flex justify-end">
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white font-medium rounded-lg hover:from-purple-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all shadow-md hover:shadow-lg">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Simpan Evaluasi
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                    
                    @php
                        // Filter evaluations based on user role
                        if (auth()->user()->hasRole('researcher')) {
                            // Researcher can see all evaluations
                            $evaluations = $project->evaluations()->orderBy('created_at', 'desc')->get();
                        } else {
                            // Other users only see published evaluations
                            $evaluations = $project->evaluations()
                                ->where('status', 'published')
                                ->orderBy('created_at', 'desc')
                                ->get();
                        }
                    @endphp
                    
                    @if($evaluations->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($evaluations as $evaluation)
                            <div class="border-l-4 border-violet-500 pl-4 py-3 bg-violet-50 rounded-r hover:shadow-md transition">
                                <p class="text-sm text-gray-800 leading-relaxed mb-2">{{ $evaluation->notes }}</p>
                                <div class="flex items-center justify-between gap-3 text-xs text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium">{{ $evaluation->researcher->name }}</span>
                                        <span>•</span>
                                        <span>{{ $evaluation->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($evaluation->status === 'draft')
                                        <span class="px-2 py-1 bg-gray-200 text-gray-700 text-xs rounded-full">Draft</span>
                                    @endif
                                </div>
                                
                                {{-- Actions for researcher who created this evaluation --}}
                                @if(auth()->user()->hasRole('researcher') && $evaluation->researcher_id === auth()->id())
                                <div class="mt-3 pt-3 border-t border-violet-200 flex gap-2">
                                    <button 
                                        @click="$dispatch('open-edit-evaluation-modal', { evaluation: {{ json_encode($evaluation) }} })"
                                        class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                        Edit
                                    </button>
                                    <form action="{{ route('evaluations.destroy', $evaluation) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus evaluasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-600 hover:text-red-800 font-medium">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12 text-gray-500">
                            <svg class="h-16 w-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm font-medium text-gray-400">Belum ada evaluasi</p>
                            <p class="text-xs text-gray-400 mt-1">Catatan evaluasi dari Researcher akan muncul di sini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>{{-- End Evaluasi Section --}}

        {{-- Rating Section (Only for completed projects) --}}
        @if($project->status === 'completed')
        <div class="mt-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 px-4 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <h3 class="font-semibold text-white">Rating Proyek</h3>
                        </div>
                        @php
                            $averageRating = $project->averageRating();
                            $totalRatings = $project->ratings()->count();
                        @endphp
                        <div class="flex items-center gap-2 bg-white/20 px-3 py-1 rounded-full">
                            <svg class="h-4 w-4 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-white font-bold">{{ $averageRating }}</span>
                            <span class="text-white/80 text-xs">({{ $totalRatings }} rating)</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    @php
                        // Check if user was ever a member (including past members who left)
                        $wasEverMember = $project->wasEverMember(auth()->user());
                        $isOwner = $project->owner_id === auth()->id();
                        $canRate = $wasEverMember || $isOwner;
                        $userRating = $project->ratings()->where('user_id', auth()->id())->first();
                    @endphp
                    
                    @if($canRate)
                    {{-- Form Rating --}}
                    <div class="mb-6 bg-gradient-to-br from-amber-50 to-orange-50 rounded-xl p-6 border-2 border-amber-200">
                        <h4 class="text-base font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5 text-amber-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            {{ $userRating ? 'Edit Rating Anda' : 'Berikan Rating' }}
                        </h4>
                        
                        <form action="{{ route('projects.ratings.store', $project) }}" method="POST" class="space-y-4" x-data="{ selectedRating: {{ $userRating ? $userRating->rating : 0 }} }">
                            @csrf
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    Rating <span class="text-red-500">*</span>
                                </label>
                                <div class="flex gap-2">
                                    @for($i = 1; $i <= 5; $i++)
                                    <button 
                                        type="button"
                                        @click="selectedRating = {{ $i }}"
                                        class="transition-all transform hover:scale-110">
                                        <svg class="h-10 w-10 transition-colors" 
                                             :class="selectedRating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" :value="selectedRating" required>
                                @error('rating')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                    Komentar (Opsional)
                                </label>
                                <textarea 
                                    name="comment" 
                                    id="comment" 
                                    rows="3" 
                                    placeholder="Bagikan pengalaman Anda dalam proyek ini..."
                                    class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">{{ $userRating ? $userRating->comment : '' }}</textarea>
                                @error('comment')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="flex justify-between items-center pt-2">
                                @if($userRating)
                                <form action="{{ route('projects.ratings.destroy', $project) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus rating Anda?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-medium">
                                        Hapus Rating
                                    </button>
                                </form>
                                @else
                                <div></div>
                                @endif
                                
                                <button 
                                    type="submit" 
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-lg hover:from-amber-600 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 transition-all shadow-md hover:shadow-lg">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $userRating ? 'Update Rating' : 'Simpan Rating' }}
                                </button>
                            </div>
                        </form>
                    </div>
                    @endif
                    
                    {{-- Display all ratings --}}
                    @php
                        $ratings = $project->ratings()->with('user')->latest()->get();
                    @endphp
                    
                    @if($ratings->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($ratings as $rating)
                            <div class="border-l-4 border-amber-500 pl-4 py-3 bg-amber-50 rounded-r hover:shadow-md transition">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-gray-900">{{ $rating->user->name }}</span>
                                        <div class="flex">
                                            @for($i = 1; $i <= 5; $i++)
                                            <svg class="h-4 w-4 {{ $i <= $rating->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                 fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $rating->created_at->diffForHumans() }}</span>
                                </div>
                                @if($rating->comment)
                                <p class="text-sm text-gray-700 leading-relaxed">{{ $rating->comment }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="h-12 w-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-400">Belum ada rating</p>
                            <p class="text-xs text-gray-400 mt-1">Jadilah yang pertama memberikan rating untuk proyek ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>{{-- End Rating Section --}}
        @endif

        </div>{{-- End Overview Tab --}}

        {{-- PROJECT SETTINGS TAB --}}
        @include('projects.partials.settings-tab', ['project' => $project])

        {{-- CREATE TICKET TAB --}}
        @if($project->canManage(Auth::user()))
        <div x-show="activeTab === 'create-ticket'" x-transition>
        <div class="bg-white rounded-2xl shadow-lg border-2 border-violet-100 overflow-hidden" x-data="{ context: 'proyek', showCreateModal: false }">
            {{-- Modern Header with Gradient --}}
            <div class="bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-600 px-8 py-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                        <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Buat Tiket Baru</h2>
                        <p class="text-violet-100 text-sm mt-1">Tambahkan tiket untuk mengorganisir pekerjaan di project ini</p>
                    </div>
                </div>
            </div>

            {{-- Form Content --}}
            <form action="{{ route('projects.tickets.store', $project) }}" method="POST" class="p-8">
                @csrf
                <div class="space-y-6">
                    {{-- Context Selection with Modern Cards --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="h-4 w-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                            </svg>
                            Tipe Tiket
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="relative flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md"
                                   :class="context === 'proyek' ? 'border-violet-500 bg-violet-50' : 'border-gray-200 bg-white hover:border-violet-200'">
                                <input type="radio" name="context" value="proyek" x-model="context" checked class="sr-only" />
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                         :class="context === 'proyek' ? 'bg-violet-500 text-white' : 'bg-gray-100 text-gray-400'">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold text-sm text-gray-900">📁 Proyek</div>
                                        <div class="text-xs text-gray-500">Tiket terikat pada proyek ini</div>
                                    </div>
                                </div>
                                <svg x-show="context === 'proyek'" class="h-5 w-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </label>
                            
                            <label class="relative flex items-center p-4 rounded-xl border-2 cursor-pointer transition-all duration-200 hover:shadow-md"
                                   :class="context === 'event' ? 'border-amber-500 bg-amber-50' : 'border-gray-200 bg-white hover:border-amber-200'">
                                <input type="radio" name="context" value="event" x-model="context" class="sr-only" />
                                <div class="flex items-center gap-3 flex-1">
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                         :class="context === 'event' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-400'">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1">
                                        <div class="font-semibold text-sm text-gray-900 flex items-center gap-1.5">
                                            <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Event
                                        </div>
                                        <div class="text-xs text-gray-500">Tiket terikat pada event tertentu</div>
                                    </div>
                                </div>
                                <svg x-show="context === 'event'" class="h-5 w-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </label>
                        </div>
                    </div>

                    {{-- Event Selection (only show if context is 'event') --}}
                    <div x-show="context === 'event'" x-collapse class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="h-4 w-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Pilih Event
                            <span class="text-red-500">*</span>
                        </label>
                        <select name="project_event_id" 
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-amber-500 focus:ring-4 focus:ring-amber-100 transition-all duration-200 text-gray-900 appearance-none bg-white bg-no-repeat bg-right pr-10"
                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-size: 1.5rem;">
                            <option value="">-- Pilih Event --</option>
                            @foreach($project->events as $event)
                                <option value="{{ $event->id }}">{{ $event->title }} ({{ $event->start_date->format('d M Y') }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Title & Status Row --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                Judul Tiket
                                <span class="text-red-500">*</span>
                            </label>
                            <input name="title" 
                                   placeholder="Contoh: Review Desain Landing Page" 
                                   required 
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-gray-900 placeholder-gray-400" />
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Status Awal
                                <span class="text-red-500">*</span>
                            </label>
                            <select name="status" 
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-gray-900 appearance-none bg-white bg-no-repeat bg-right pr-10"
                                    style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-size: 1.5rem;">
                                <option value="todo" selected>To Do - Belum dikerjakan</option>
                                <option value="doing">Doing - Sedang dikerjakan</option>
                                <option value="done">Done - Selesai</option>
                                <option value="blackout">Blackout - Ditunda/dibatalkan</option>
                            </select>
                        </div>
                    </div>
                    
                    {{-- Description --}}
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                            <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                            </svg>
                            Deskripsi
                            <span class="text-xs text-gray-500 font-normal">(Opsional)</span>
                        </label>
                        <textarea name="description" 
                                  placeholder="Jelaskan detail pekerjaan, deliverables, atau catatan khusus..." 
                                  rows="4" 
                                  class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-gray-900 placeholder-gray-400 resize-none"></textarea>
                    </div>
                    
                    {{-- Priority & Weight Row --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-900 flex items-center gap-1">
                                <svg class="h-4 w-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                Prioritas
                                <span class="text-red-500">*</span>
                            </label>
                            <select name="priority" 
                                    required 
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-gray-900 appearance-none bg-white bg-no-repeat bg-right pr-10"
                                    style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-size: 1.5rem;">
                                <option value="low">🟢 Rendah</option>
                                <option value="medium" selected>🟡 Sedang</option>
                                <option value="high">🟠 Tinggi</option>
                                <option value="urgent">🔴 Mendesak</option>
                            </select>
                        </div>
                        
                        <div x-data="{ 
                            weight: 5,
                            getLabel() {
                                if (this.weight <= 3) return { text: 'Ringan', color: 'text-green-600', bg: 'bg-green-50', border: 'border-green-200', emoji: '🪶' };
                                if (this.weight <= 6) return { text: 'Sedang', color: 'text-yellow-600', bg: 'bg-yellow-50', border: 'border-yellow-200', emoji: '⚖️' };
                                return { text: 'Berat', color: 'text-red-600', bg: 'bg-red-50', border: 'border-red-200', emoji: '🏋️' };
                            }
                        }" class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-900 flex items-center gap-1">
                                <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
                                </svg>
                                Bobot
                                <span class="text-red-500">*</span>
                            </label>
                            
                            {{-- Weight Display --}}
                            <div class="p-3 rounded-xl border-2 transition-all duration-200"
                                 :class="getLabel().bg + ' ' + getLabel().border">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-gray-600">Level:</span>
                                    <div class="flex items-center gap-1.5">
                                        <span x-text="getLabel().emoji" class="text-lg"></span>
                                        <span x-text="weight" 
                                              class="text-xl font-bold"
                                              :class="getLabel().color"></span>
                                        <span class="text-xs font-bold px-2 py-0.5 rounded-full"
                                              :class="getLabel().bg + ' ' + getLabel().color"
                                              x-text="getLabel().text"></span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Slider --}}
                            <div class="flex items-center gap-3 px-1">
                                <span class="text-xs font-medium text-gray-500">1</span>
                                <input type="range" 
                                       name="weight" 
                                       min="1" 
                                       max="10" 
                                       value="5" 
                                       x-model="weight"
                                       class="flex-1 h-2 bg-gray-200 rounded-full appearance-none cursor-pointer slider-modern">
                                <span class="text-xs font-medium text-gray-500">10</span>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Target User Selection --}}
                    <div class="border-t pt-4" x-data="targetUserFilter()">
                        <label class="block text-sm font-semibold text-gray-900 mb-2">
                            <svg class="h-5 w-5 inline-block mr-1 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Target User (Opsional - dapat pilih lebih dari 1)
                        </label>
                        <p class="text-xs text-gray-500 mb-3">Pilih satu atau beberapa user spesifik, atau biarkan kosong untuk semua member</p>
                        
                        {{-- Search and Filter Bar --}}
                        <div class="mb-3 space-y-2">
                            <!-- Search Input -->
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" 
                                       x-model="searchQuery"
                                       placeholder="Cari nama member..."
                                       class="w-full pl-10 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 transition">
                            </div>
                            
                            <!-- Role Filter Pills & Actions -->
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-medium text-gray-600">Filter:</span>
                                <button type="button"
                                        @click="roleFilter = 'all'"
                                        :class="roleFilter === 'all' ? 'bg-violet-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                                        class="px-3 py-1 text-xs font-medium rounded-full transition">
                                    Semua
                                </button>
                                <button type="button"
                                        @click="roleFilter = 'admin'"
                                        :class="roleFilter === 'admin' ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
                                        class="px-3 py-1 text-xs font-medium rounded-full transition">
                                    Admin Project
                                </button>
                                <button type="button"
                                        @click="roleFilter = 'permanent'"
                                        :class="roleFilter === 'permanent' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'"
                                        class="px-3 py-1 text-xs font-medium rounded-full transition">
                                    Role Permanent
                                </button>
                                <button type="button"
                                        @click="roleFilter = 'event'"
                                        :class="roleFilter === 'event' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
                                        class="px-3 py-1 text-xs font-medium rounded-full transition">
                                    Role Event
                                </button>
                                
                                <!-- Select All Visible -->
                                <button type="button"
                                        @click="selectAllVisible()"
                                        class="ml-auto px-3 py-1 text-xs font-medium bg-violet-100 text-violet-700 hover:bg-violet-200 rounded-full transition">
                                    <svg class="inline h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Pilih Semua
                                </button>
                                
                                <!-- Counter -->
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">
                                    <span x-text="selectedCount"></span> / <span x-text="visibleCount"></span>
                                </span>
                            </div>
                        </div>
                        
                        <div class="max-h-60 overflow-y-auto border-2 border-gray-200 rounded-xl divide-y divide-gray-100">
                            @php
                                $projectMembers = $project->members->sortBy('name');
                                $allRoles = \App\Models\Ticket::getAllRoles();
                            @endphp
                            
                            @forelse($projectMembers as $member)
                                @php
                                    // Get ALL member's permanent roles from Spatie (user can have multiple roles)
                                    $permanentRoles = $member->roles->pluck('name')->toArray();
                                    
                                    // Get member's event roles for this project
                                    $eventRolesArray = $member->pivot->event_roles ? json_decode($member->pivot->event_roles, true) : [];
                                    
                                    $isAdmin = $member->pivot->role === 'admin';
                                    $hasPermanentRole = !empty($permanentRoles);
                                    $hasEventRole = !empty($eventRolesArray);
                                @endphp
                                
                                <label class="target-user-item flex items-center p-3 hover:bg-violet-50 cursor-pointer transition group"
                                       x-show="isVisible({{ json_encode($member->name) }}, {{ json_encode($isAdmin) }}, {{ json_encode($hasPermanentRole) }}, {{ json_encode($hasEventRole) }})"
                                       x-data="{ selected: false }"
                                       x-init="$watch('selected', () => $dispatch('target-user-changed'))"
                                       data-member-name="{{ $member->name }}"
                                       data-is-admin="{{ $isAdmin ? 'true' : 'false' }}"
                                       data-has-permanent="{{ $hasPermanentRole ? 'true' : 'false' }}"
                                       data-has-event="{{ $hasEventRole ? 'true' : 'false' }}">
                                    <input type="checkbox" 
                                           x-model="selected"
                                           name="target_user_ids[]" 
                                           value="{{ $member->id }}" 
                                           class="target-user-checkbox mr-3 w-4 h-4 text-violet-600 border-gray-300 rounded focus:ring-violet-500">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-violet-500 to-blue-500 flex items-center justify-center text-white font-semibold text-sm flex-shrink-0">
                                                {{ strtoupper(substr($member->name, 0, 1)) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="font-medium text-gray-900 truncate">{{ $member->name }}</div>
                                                <div class="text-xs text-gray-500 truncate">{{ $member->email }}</div>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-1 ml-10">
                                            {{-- Permanent Roles Badges (Multiple) - Using Helper --}}
                                            @foreach($permanentRoles as $roleKey)
                                                <span class="{{ role_badge_class($roleKey) }}">
                                                    {{ role_label($roleKey) }}
                                                </span>
                                            @endforeach
                                            
                                            {{-- Event Roles Badges (Multiple) - Using Helper with amber color --}}
                                            @foreach($eventRolesArray as $roleKey)
                                                <span class="text-xs px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full border border-amber-200 font-medium">
                                                    {{ role_label($roleKey) }}
                                                </span>
                                            @endforeach
                                            
                                            {{-- Project Role Badge --}}
                                            @if($member->pivot->role === 'admin')
                                                <span class="text-xs px-2 py-0.5 bg-emerald-100 text-emerald-700 rounded-full border border-emerald-200 font-medium">
                                                    Admin Project
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @empty
                                <div class="p-6 text-center text-gray-500 text-sm">
                                    <svg class="h-12 w-12 mx-auto mb-2 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                    Belum ada member di project ini
                                </div>
                            @endforelse
                        </div>
                        
                        <div class="mt-3 p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="flex items-start gap-2">
                                <svg class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-blue-900">Tentang Target User:</div>
                                    <ul class="text-xs text-blue-800 mt-1 space-y-1">
                                        <li>• <strong>Pilih user:</strong> Tiket hanya dapat diklaim oleh user terpilih</li>
                                        <li>• <strong>Kosong:</strong> Tiket dapat diklaim oleh semua member project</li>
                                        <li>• <strong>Multiple:</strong> Bisa pilih lebih dari 1 user</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Target Role & Due Date --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="h-4 w-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Target Role (Opsional)
                                <span class="text-xs font-normal text-gray-500">- jika tidak pilih user spesifik</span>
                            </label>
                            @php
                                // Collect all unique permanent roles from project members
                                $permanentRolesInProject = collect();
                                foreach($project->members as $member) {
                                    // Get ALL roles from this member (user can have multiple permanent roles)
                                    $memberRoles = $member->roles->pluck('name');
                                    $permanentRolesInProject = $permanentRolesInProject->merge($memberRoles);
                                }
                                $permanentRolesInProject = $permanentRolesInProject->unique()->sort();
                                
                                // Collect all unique event roles from project members
                                $eventRolesInProject = collect();
                                foreach($project->members as $member) {
                                    $roles = $member->pivot->event_roles ? json_decode($member->pivot->event_roles, true) : [];
                                    if (!empty($roles)) {
                                        $eventRolesInProject = $eventRolesInProject->merge($roles);
                                    }
                                }
                                $eventRolesInProject = $eventRolesInProject->unique()->sort();
                                
                                $allRolesReference = \App\Models\Ticket::getAllRoles();
                            @endphp
                            <select name="target_role" 
                                    class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 transition-all duration-200 text-gray-900 appearance-none bg-white"
                                    style="background-image: url('data:image/svg+xml;charset=UTF-8,%3csvg xmlns=%27http://www.w3.org/2000/svg%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27currentColor%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3e%3cpolyline points=%276 9 12 15 18 9%27%3e%3c/polyline%3e%3c/svg%3e'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em 1.25em; padding-right: 2.5rem;">
                                <option value="">Semua Role</option>
                                
                                @if($permanentRolesInProject->count() > 0)
                                <optgroup label="Role Permanent ({{ $permanentRolesInProject->count() }} role)">
                                    @foreach($permanentRolesInProject as $roleKey)
                                        @php
                                            $memberCount = $project->members->filter(function($m) use ($roleKey) {
                                                return $m->hasRole($roleKey);
                                            })->count();
                                        @endphp
                                        <option value="{{ $roleKey }}">
                                            {{ $allRolesReference[$roleKey] ?? ucfirst($roleKey) }} ({{ $memberCount }} member)
                                        </option>
                                    @endforeach
                                </optgroup>
                                @endif
                                
                                @if($eventRolesInProject->count() > 0)
                                <optgroup label="Role Event ({{ $eventRolesInProject->count() }} role)">
                                    @foreach($eventRolesInProject as $roleKey)
                                        @php
                                            $memberCount = $project->members->filter(function($m) use ($roleKey) {
                                                $roles = $m->pivot->event_roles ? json_decode($m->pivot->event_roles, true) : [];
                                                return in_array($roleKey, $roles);
                                            })->count();
                                        @endphp
                                        <option value="{{ $roleKey }}">
                                            {{ $allRolesReference[$roleKey] ?? ucfirst($roleKey) }} ({{ $memberCount }} member)
                                        </option>
                                    @endforeach
                                </optgroup>
                                @endif
                                
                                @if($permanentRolesInProject->count() === 0 && $eventRolesInProject->count() === 0)
                                <option value="" disabled>Tidak ada role tersedia di project ini</option>
                                @endif
                            </select>
                            <p class="text-xs text-gray-500">
                                Hanya menampilkan role yang ada pada anggota project ini
                            </p>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-900 flex items-center gap-2">
                                <svg class="h-4 w-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Due Date
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="due_date" 
                                   required
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-rose-500 focus:ring-4 focus:ring-rose-100 transition-all duration-200 text-gray-900" />
                            <p class="text-xs text-gray-500">Batas waktu penyelesaian tiket</p>
                        </div>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" 
                            class="group relative flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:via-purple-700 hover:to-pink-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Buat Tiket</span>
                    </button>
                </div>
            </form>
        </div>
        </div>{{-- End Create Ticket Tab --}}
        @endif
    
        {{-- MEMBERS TAB --}}
        @include('projects.partials.members-tab', ['project' => $project])
    
        {{-- EVENTS TAB --}}
        @include('projects.partials.events-tab', ['project' => $project])
    
    {{-- Modal Detail Tiket - MOVED INSIDE x-data scope --}}
    
    @include('projects.partials.ticket-modal')    </div>{{-- End Tab Content --}}

</div>{{-- End Alpine x-data --}}

@endsection

{{-- Modal Edit Evaluation (for Researcher) --}}
<div x-data="{ 
    showEditModal: false, 
    editEvaluation: null 
}" 
@open-edit-evaluation-modal.window="showEditModal = true; editEvaluation = $event.detail.evaluation">
    <template x-if="showEditModal">
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" 
             @click.self="showEditModal = false">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
                 @click.stop>
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">Edit Evaluasi</h3>
                    <button @click="showEditModal = false" 
                            class="text-white hover:text-gray-200 transition-colors">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <form :action="'/evaluations/' + editEvaluation.id" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="edit_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Catatan Evaluasi <span class="text-red-500">*</span>
                        </label>
                        <textarea 
                            name="notes" 
                            id="edit_notes" 
                            rows="6" 
                            required
                            x-model="editEvaluation.notes"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition"></textarea>
                    </div>
                    
                    <div>
                        <label for="edit_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select 
                            name="status" 
                            id="edit_status" 
                            required
                            x-model="editEvaluation.status"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition">
                            <option value="draft">Draft (Hanya Researcher yang bisa lihat)</option>
                            <option value="published">Published (Semua anggota bisa lihat)</option>
                        </select>
                    </div>
                    
                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                        <button 
                            type="button"
                            @click="showEditModal = false" 
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            class="px-6 py-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-colors font-medium shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

<script>
// Calendar Navigation Component
function calendarNavigation(currentYear, currentMonth, projectId) {
    return {
        year: currentYear,
        month: currentMonth,
        projectId: projectId,
        loading: false,
        monthNames: [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ],
        
        get monthName() {
            return this.monthNames[this.month - 1];
        },
        
        prevMonth() {
            this.month--;
            if (this.month < 1) {
                this.month = 12;
                this.year--;
            }
            this.loadCalendar();
        },
        
        nextMonth() {
            this.month++;
            if (this.month > 12) {
                this.month = 1;
                this.year++;
            }
            this.loadCalendar();
        },
        
        async loadCalendar() {
            this.loading = true;
            
            try {
                const url = `/api/projects/${this.projectId}/calendar?month=${this.month}&year=${this.year}`;
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error('Failed to load calendar');
                }
                
                const html = await response.text();
                
                // Update calendar grid with smooth transition
                const container = document.getElementById('calendar-grid-container');
                container.style.opacity = '0.5';
                
                setTimeout(() => {
                    container.innerHTML = html;
                    container.style.opacity = '1';
                }, 150);
                
            } catch (error) {
                console.error('Error loading calendar:', error);
                alert('Gagal memuat kalender. Silakan coba lagi.');
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>

{{-- Calendar rendered server-side with PHP - No JavaScript needed! --}}



