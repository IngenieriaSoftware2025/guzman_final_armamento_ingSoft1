import L from 'leaflet';




var map = L.map('map').setView([14.643435, -90.474453], 17);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
}).addTo(map);


var marker = L.marker([14.643435, -90.474453]).addTo(map);
    marker.bindPopup("Almacen General").openPopup();