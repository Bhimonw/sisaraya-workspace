import Calendar from '@toast-ui/calendar';
import '@toast-ui/calendar/dist/toastui-calendar.min.css';

document.addEventListener('DOMContentLoaded', function() {
    console.log('📅 Calendar.js - Initializing Tui Calendar...');
    
    const calendarEl = document.getElementById('calendar');
    
    if (calendarEl && calendarEl.dataset.projectId) {
        console.log('✅ Calendar element found with project ID:', calendarEl.dataset.projectId);
        
        const projectId = calendarEl.dataset.projectId;
        
        // Wait for Alpine.js to finish rendering
        setTimeout(() => {
            console.log('🚀 Creating calendar instance...');
            
            const calendar = new Calendar(calendarEl, {
                defaultView: 'month',
                useDetailPopup: true,
                useFormPopup: false,
                isReadOnly: true,
                month: {
                    startDayOfWeek: 1, // Monday
                    dayNames: ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'],
                    visibleWeeksCount: 6,
                },
                calendars: [
                    {
                        id: 'events',
                        name: 'Event Proyek',
                        backgroundColor: '#8b5cf6',
                        borderColor: '#8b5cf6',
                    },
                    {
                        id: 'tickets-todo',
                        name: 'Tiket To Do',
                        backgroundColor: '#6b7280',
                        borderColor: '#6b7280',
                    },
                    {
                        id: 'tickets-doing',
                        name: 'Tiket Doing',
                        backgroundColor: '#3b82f6',
                        borderColor: '#3b82f6',
                    },
                    {
                        id: 'tickets-done',
                        name: 'Tiket Done',
                        backgroundColor: '#22c55e',
                        borderColor: '#22c55e',
                    },
                ],
                template: {
                    monthDayName(model) {
                        return model.label;
                    },
                },
            });
            
            console.log('✅ Calendar instance created');
            
            // Fetch events from API
            console.log('📡 Fetching events from API...');
            fetch(`/api/calendar/project/${projectId}/events`)
                .then(response => {
                    console.log('📥 API Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Events loaded:', data.length, 'events');
                    
                    // Convert to Tui Calendar format
                    const tuiEvents = data.map(event => {
                        // Determine calendar ID based on event type
                        let calendarId = 'events';
                        if (event.extendedProps && event.extendedProps.type === 'Tiket') {
                            const status = event.extendedProps.status || 'todo';
                            calendarId = `tickets-${status}`;
                        }
                        
                        return {
                            id: event.id,
                            calendarId: calendarId,
                            title: event.title,
                            start: event.start,
                            end: event.end || event.start,
                            category: 'time',
                            isReadOnly: true,
                            backgroundColor: event.backgroundColor || '#3b82f6',
                            borderColor: event.borderColor || '#3b82f6',
                            raw: event.extendedProps || {},
                        };
                    });
                    
                    console.log('📌 Adding events to calendar:', tuiEvents.length);
                    calendar.createEvents(tuiEvents);
                    console.log('✨ Calendar rendered with events!');
                })
                .catch(error => {
                    console.error('❌ Error loading events:', error);
                    console.log('📅 Calendar rendered without events');
                });
            
            // Make calendar globally accessible
            window.projectCalendar = calendar;
            
            // Update size after delay
            setTimeout(() => {
                if (window.projectCalendar) {
                    console.log('📏 Calendar ready');
                }
            }, 500);
            
        }, 300);
    } else {
        console.log('ℹ️ No calendar element or missing project ID');
    }
});

export default Calendar;
