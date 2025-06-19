import L from 'leaflet';




var map = L.map('map').setView([14.57690, -90.53573], 17);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);


var marker = L.marker([14.57690, -90.53573]).addTo(map);
    marker.bindPopup("Brigada de Comunicaciones").openPopup();