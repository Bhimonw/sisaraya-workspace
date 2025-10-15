import './bootstrap';
// import './calendar'; // Disabled - using server-side PHP calendar

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

window.Alpine = Alpine;

Alpine.start();

window.moveTicket = async function(ticketId, status) {
	const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
	await fetch(`/tickets/${ticketId}/move`, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': token,
			'Accept': 'application/json'
		},
		body: JSON.stringify({ status })
	});
};
