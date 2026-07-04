{{-- resources/views/hubs/map.blade.php --}}
@extends('layouts.admin')

@section('title', 'Hubs Map')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map {
        height: 500px;
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3>Hubs Map</h3>
                    <a href="{{ route('admin.hubs.index') }}" class="btn btn-secondary float-end">Back to List</a>
                </div>
                <div class="card-body">
                    <div id="map"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([20.5937, 78.9629], 5); // Center on India
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    var hubs = @json($hubs);
    
    hubs.forEach(function(hub) {
        if (hub.latitude && hub.longitude) {
            var marker = L.marker([hub.latitude, hub.longitude]).addTo(map);
            
            var popupContent = '<strong>' + hub.name + '</strong><br>' +
                               (hub.location ? hub.location + '<br>' : '') +
                               '🕐 Open: ' + (hub.opening_time || 'N/A') + '<br>' +
                               '🕐 Close: ' + (hub.closing_time || 'N/A');
            
            marker.bindPopup(popupContent);
        }
    });
</script>
@endsection