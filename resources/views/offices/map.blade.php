{{-- resources/views/offices/map.blade.php --}}
@extends('layouts.admin')

@section('title', 'Offices Map')

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
                    <h3>Offices Map</h3>
                    <a href="{{ route('admin.offices.index') }}" class="btn btn-secondary float-end">Back to List</a>
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
    
    var offices = @json($offices);
    
    offices.forEach(function(office) {
        if (office.latitude && office.longitude) {
            var marker = L.marker([office.latitude, office.longitude]).addTo(map);
            
            var popupContent = '<strong>' + office.name + '</strong><br>' +
                               (office.location ? office.location + '<br>' : '') +
                               '🕐 Open: ' + (office.opening_time || 'N/A') + '<br>' +
                               '🕐 Close: ' + (office.closing_time || 'N/A');
            
            marker.bindPopup(popupContent);
        }
    });
</script>
@endsection