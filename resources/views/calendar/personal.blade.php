@extends('layouts.app')

@section('content')
<div class="py-6 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h1 class="text-2xl font-bold text-gray-800">Kalender Pribadi</h1>
            </div>
            <button onclick="openActivityModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                ➕ Tambah Kegiatan
            </button>
        </div>

        <!-- Info Box -->
        <div class="mb-6 p-4 bg-blue-50 border-l-4 border-blue-500 rounded">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-blue-500 mt-0.5 mr-2 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-900">
                        Kalender ini menampilkan:
                    </p>
                    <ul class="mt-2 text-sm text-blue-800 list-disc list-inside space-y-1">
                        <li><strong>Tiket Saya</strong> - Tiket yang sudah Anda klaim (warna sesuai status)</li>
                        <li><strong>Tiket Tersedia</strong> - Tiket yang bisa Anda klaim (warna oranye)</li>
                        <li><strong>Timeline Proyek</strong> - Periode proyek yang Anda ikuti (background)</li>
                        <li><strong>Event Proyek</strong> - Event dari proyek yang Anda ikuti (ungu)</li>
                        <li><strong>Kegiatan Pribadi</strong> - Jadwal pribadi Anda (beragam warna)</li>
                    </ul>
                    <p class="mt-3 text-sm text-blue-800">
                        <strong>Info:</strong> Kegiatan yang ditandai "Public" akan terlihat oleh anggota lain di Kalender Umum
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Calendar -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div id="personal-calendar" class="w-full"></div>
            </div>
        </div>
        
        <!-- Legend -->
        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Legenda</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Tiket Status -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Status Tiket Saya</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-gray-500"></div>
                            <span>To Do</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-blue-500"></div>
                            <span>Doing</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-green-500"></div>
                            <span>Done</span>
                        </div>
                    </div>
                </div>

                <!-- Tiket Tersedia -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Tiket Tersedia</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-orange-500"></div>
                            <span>Tiket yang bisa diklaim</span>
                        </div>
                    </div>
                </div>

                <!-- Event & Proyek -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Event & Timeline Proyek</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-purple-500"></div>
                            <span>Event Proyek</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded bg-gray-300 opacity-50"></div>
                            <span>Timeline Proyek</span>
                        </div>
                    </div>
                </div>

                <!-- Kegiatan Pribadi -->
                <div>
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Jenis Kegiatan Pribadi</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #3b82f6;"></div>
                            <span>Pribadi</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #10b981;"></div>
                            <span>Keluarga</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #f59e0b;"></div>
                            <span>Pekerjaan Luar</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #8b5cf6;"></div>
                            <span>Pendidikan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded" style="background-color: #ef4444;"></div>
                            <span>Kesehatan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Create/Edit Personal Activity --}}
<div id="activityModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 id="modalTitle" class="text-xl font-bold">Tambah Kegiatan Pribadi</h2>
                <button onclick="closeActivityModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="activityForm" onsubmit="saveActivity(event)">
                <input type="hidden" id="activityId" name="activity_id">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Kegiatan *</label>
                        <input type="text" id="title" name="title" required 
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Misal: Konsultasi Dokter, Kuliah, dll">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Keterangan tambahan (opsional)"></textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai *</label>
                            <input type="datetime-local" id="start_time" name="start_time" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai *</label>
                            <input type="datetime-local" id="end_time" name="end_time" required
                                class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                        <input type="text" id="location" name="location"
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Lokasi kegiatan (opsional)">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                        <select id="type" name="type" required
                            class="w-full border border-gray-300 rounded px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="personal">Pribadi</option>
                            <option value="family">Keluarga</option>
                            <option value="work_external">Pekerjaan Luar</option>
                            <option value="study">Pendidikan</option>
                            <option value="health">Kesehatan</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="is_public" name="is_public" value="1" checked
                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="is_public" class="text-sm text-gray-700">
                            <strong>Tampilkan ke semua anggota</strong><br>
                            <span class="text-xs text-gray-500">Jika dicentang, semua anggota tetap bisa melihat jadwal ini di kalender dashboard untuk koordinasi</span>
                        </label>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2 mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded flex items-center justify-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Simpan
                    </button>
                    <button type="button" onclick="closeActivityModal()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">
                        Batal
                    </button>
                    <button type="button" id="deleteBtn" onclick="deleteActivity()" class="ml-auto bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded hidden flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- FullCalendar CSS from CDN --}}
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />

<style>
/* Force calendar visibility and table display */
#personal-calendar {
    min-height: 600px;
    background: white;
    width: 100%;
    display: block !important;
    visibility: visible !important;
}

#personal-calendar * {
    visibility: visible !important;
}

/* Force all table elements to display */
.fc table { display: table !important; }
.fc thead { display: table-header-group !important; }
.fc tbody { display: table-row-group !important; }
.fc tr { display: table-row !important; }
.fc th, .fc td { display: table-cell !important; }

.fc-scrollgrid { 
    display: table !important; 
    width: 100% !important;
}

.fc-scrollgrid-section { display: table-row-group !important; }
.fc-scrollgrid-section > td { display: table-cell !important; }

.fc-daygrid-body {
    width: 100% !important;
    display: table-row-group !important;
}

.fc-daygrid-day {
    height: 80px !important;
    min-height: 80px !important;
    display: table-cell !important;
    border: 1px solid #e5e7eb !important;
}

.fc-daygrid-day-number {
    display: block !important;
    font-size: 1rem;
    padding: 4px;
    color: #374151;
}

.fc-col-header-cell {
    display: table-cell !important;
    padding: 10px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
}

.fc-view-harness {
    min-height: 500px;
    display: block !important;
}

.fc-scroller {
    overflow: visible !important;
}
</style>

{{-- FullCalendar JS from CDN --}}
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/locales/id.js'></script>

<script>
let calendar;
let currentActivity = null;

document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('personal-calendar');
    
    if (calendarEl) {
        console.log('🔍 Initializing Personal Calendar...');
        
        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            height: 700,
            contentHeight: 650,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            eventSources: [
                {
                    url: '/api/calendar/user/events', // Tickets & Events
                    color: '#3b82f6'
                },
                {
                    url: '/api/personal-activities', // Personal activities
                    color: '#10b981'
                }
            ],
            eventClick: function(info) {
                const eventId = info.event.id;
                const props = info.event.extendedProps;
                
                // Check if it's a personal activity (can be edited)
                if (eventId && eventId.toString().startsWith('activity-')) {
                    const activityId = eventId.replace('activity-', '');
                    editActivity(activityId, info.event);
                } else if (props.url) {
                    // Navigate to ticket/project URL
                    if (confirm('Ingin melihat detail ' + props.type + '?\n\n' + info.event.title)) {
                        window.location.href = props.url;
                    }
                } else {
                    // Show event details in alert
                    showEventDetails(info.event);
                }
            },
            dateClick: function(info) {
                // Quick add activity by clicking date
                openActivityModal(info.dateStr);
            },
            editable: false,
            selectable: true,
            locale: 'id',
            buttonText: {
                today: 'Hari Ini',
                month: 'Bulan',
                week: 'Minggu',
                day: 'Hari'
            },
            viewDidMount: function(info) {
                console.log('🎨 Personal calendar view mounted:', info.view.type);
            }
        });
        
        console.log('🚀 Rendering personal calendar...');
        calendar.render();
        
        // Force update size after render
        setTimeout(() => {
            if (calendar) {
                calendar.updateSize();
                console.log('📏 Personal calendar size updated');
            }
        }, 100);
        
        // Additional force render
        setTimeout(() => {
            if (calendar) {
                calendar.render();
                calendar.updateSize();
                console.log('🔄 Personal calendar force re-rendered');
            }
        }, 1000);
        
        console.log('✅ Personal calendar initialized');
    }
});

function openActivityModal(date = null) {
    document.getElementById('modalTitle').textContent = 'Tambah Kegiatan Pribadi';
    document.getElementById('activityForm').reset();
    document.getElementById('activityId').value = '';
    document.getElementById('deleteBtn').classList.add('hidden');
    document.getElementById('is_public').checked = true;
    
    if (date) {
        const dateObj = new Date(date);
        const localDate = new Date(dateObj.getTime() - dateObj.getTimezoneOffset() * 60000);
        const dateStr = localDate.toISOString().slice(0, 16);
        document.getElementById('start_time').value = dateStr;
        
        const endDate = new Date(localDate.getTime() + 3600000); // +1 hour
        document.getElementById('end_time').value = endDate.toISOString().slice(0, 16);
    }
    
    document.getElementById('activityModal').classList.remove('hidden');
}

function closeActivityModal() {
    document.getElementById('activityModal').classList.add('hidden');
    currentActivity = null;
}

function editActivity(activityId, event) {
    const props = event.extendedProps;
    
    // Check if user owns this activity
    if (!props.isOwn) {
        showEventDetails(event);
        return;
    }
    
    document.getElementById('modalTitle').textContent = 'Edit Kegiatan Pribadi';
    document.getElementById('activityId').value = activityId;
    document.getElementById('title').value = event.title.replace(` (${props.userName})`, '');
    document.getElementById('description').value = props.description || '';
    document.getElementById('location').value = props.location || '';
    document.getElementById('type').value = props.type;
    document.getElementById('is_public').checked = props.isPublic;
    
    // Set datetime
    const start = new Date(event.start);
    const end = new Date(event.end);
    document.getElementById('start_time').value = new Date(start.getTime() - start.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    document.getElementById('end_time').value = new Date(end.getTime() - end.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    
    document.getElementById('deleteBtn').classList.remove('hidden');
    document.getElementById('activityModal').classList.remove('hidden');
}

function saveActivity(event) {
    event.preventDefault();
    
    const activityId = document.getElementById('activityId').value;
    const formData = {
        title: document.getElementById('title').value,
        description: document.getElementById('description').value,
        start_time: document.getElementById('start_time').value,
        end_time: document.getElementById('end_time').value,
        location: document.getElementById('location').value,
        type: document.getElementById('type').value,
        is_public: document.getElementById('is_public').checked ? 1 : 0,
    };
    
    const url = activityId ? `/personal-activities/${activityId}` : '/personal-activities';
    const method = activityId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeActivityModal();
            calendar.refetchEvents();
        } else {
            alert('Error: ' + (data.message || 'Gagal menyimpan'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menyimpan');
    });
}

function deleteActivity() {
    const activityId = document.getElementById('activityId').value;
    
    if (!activityId || !confirm('Yakin ingin menghapus kegiatan ini?')) {
        return;
    }
    
    fetch(`/personal-activities/${activityId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeActivityModal();
            calendar.refetchEvents();
        } else {
            alert('Error: ' + (data.message || 'Gagal menghapus'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus');
    });
}

function showEventDetails(event) {
    const props = event.extendedProps;
    let details = '📋 ' + event.title + '\n\n';
    
    if (props.type) {
        details += '🏷️ Jenis: ' + props.type + '\n';
    }
    if (props.status) {
        details += '📊 Status: ' + props.status + '\n';
    }
    if (props.project_name) {
        details += '📦 Proyek: ' + props.project_name + '\n';
    }
    if (props.target_role) {
        details += '👥 Target: ' + props.target_role + '\n';
    }
    if (props.location) {
        details += '📍 Lokasi: ' + props.location + '\n';
    }
    if (event.start) {
        const start = new Date(event.start);
        details += '🕐 Mulai: ' + start.toLocaleString('id-ID') + '\n';
    }
    if (event.end && !props.type?.includes('Proyek')) {
        const end = new Date(event.end);
        details += '🕐 Selesai: ' + end.toLocaleString('id-ID') + '\n';
    }
    if (props.description) {
        details += '\n📝 Deskripsi:\n' + props.description;
    }
    
    if (props.url) {
        details += '\n\n💡 Klik OK untuk melihat detail lengkap';
        if (confirm(details)) {
            window.location.href = props.url;
        }
    } else {
        alert(details);
    }
}
</script>
@endsection
