@extends('layouts.admin')
@section('title', 'Add New Office')

@section('content')
<div class="main-section">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">Add New Office</h4>
        <a href="{{ route('admin.offices.index') }}" class="btn btn-outline-secondary btn-sm rounded-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Offices
        </a>
    </div>

    <div class="card shadow-sm rounded border mb-4">
        <div class="card-body p-4">
            <form action="{{ route('admin.offices.store') }}" method="POST" class="row g-3">
                @csrf

                <div class="col-12">
                    <label for="name" class="form-label fw-semibold">Office Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                        id="name" name="name" value="{{ old('name') }}" required
                        pattern="^[A-Za-z ]+$" maxlength="100"
                        title="Only letters and spaces are allowed">
                    @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Only letters and spaces are allowed.</div>
                </div>

                <div class="col-12">
                    <label for="location" class="form-label fw-semibold">Address/Location</label>
                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                        id="location" name="location" value="{{ old('location') }}">
                    @error('location')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="latitude" class="form-label fw-semibold">Latitude</label>
                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                        id="latitude" name="latitude" value="{{ old('latitude') }}">
                    @error('latitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="longitude" class="form-label fw-semibold">Longitude</label>
                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                        id="longitude" name="longitude" value="{{ old('longitude') }}">
                    @error('longitude')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="opening_time" class="form-label fw-semibold">Opening Time</label>
                    <input type="time" class="form-control @error('opening_time') is-invalid @enderror"
                        id="opening_time" name="opening_time" value="{{ old('opening_time') }}">
                    @error('opening_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="closing_time" class="form-label fw-semibold">Closing Time</label>
                    <input type="time" class="form-control @error('closing_time') is-invalid @enderror"
                        id="closing_time" name="closing_time" value="{{ old('closing_time') }}">
                    @error('closing_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end mt-2">
                    <a href="{{ route('admin.offices.index') }}" class="btn btn-light border">Cancel</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2-circle me-1"></i> Create Office
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var nameInput = document.getElementById('name');
    var locationInput = document.getElementById('location');
    var latitudeInput = document.getElementById('latitude');
    var longitudeInput = document.getElementById('longitude');
    var placesApiKey = 'AIzaSyATFmOddaFc49svmImgb3Gf1_7q1W2WUTk';
    var autocompleteEndpoint = 'https://places.googleapis.com/v1/places:autocomplete';
    var detailsEndpoint = 'https://places.googleapis.com/v1/places/';
    var debounceTimer = null;
    var sessionToken = (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : String(Date.now());
    var activeIndex = -1;
    var suggestions = [];
    var dropdown = null;

    if (!nameInput || !locationInput || !latitudeInput || !longitudeInput) {
        return;
    }

    nameInput.addEventListener('input', function () {
        var sanitized = this.value.replace(/[^A-Za-z ]+/g, '').replace(/\s{2,}/g, ' ');
        if (this.value !== sanitized) {
            this.value = sanitized;
        }
    });

    locationInput.setAttribute('autocomplete', 'off');

    function ensureDropdown() {
        if (dropdown) {
            return;
        }

        dropdown = document.createElement('div');
        dropdown.id = 'location-suggestions';
        dropdown.style.position = 'absolute';
        dropdown.style.zIndex = '1050';
        dropdown.style.backgroundColor = '#fff';
        dropdown.style.border = '1px solid #dee2e6';
        dropdown.style.borderRadius = '0.375rem';
        dropdown.style.width = locationInput.offsetWidth + 'px';
        dropdown.style.maxHeight = '220px';
        dropdown.style.overflowY = 'auto';
        dropdown.style.display = 'none';
        dropdown.style.boxShadow = '0 6px 18px rgba(0,0,0,.08)';

        document.body.appendChild(dropdown);
        positionDropdown();
    }

    function positionDropdown() {
        if (!dropdown) {
            return;
        }

        var rect = locationInput.getBoundingClientRect();
        dropdown.style.top = (window.scrollY + rect.bottom + 4) + 'px';
        dropdown.style.left = (window.scrollX + rect.left) + 'px';
        dropdown.style.width = rect.width + 'px';
    }

    function hideDropdown() {
        if (dropdown) {
            dropdown.style.display = 'none';
        }
        activeIndex = -1;
    }

    function setActive(index) {
        if (!dropdown) {
            return;
        }

        var items = dropdown.querySelectorAll('.place-suggestion-item');
        items.forEach(function (item, i) {
            item.style.backgroundColor = i === index ? '#f8f9fa' : '#fff';
        });
        activeIndex = index;
    }

    function renderSuggestions(list) {
        ensureDropdown();
        suggestions = list;
        dropdown.innerHTML = '';
        activeIndex = -1;

        if (!list.length) {
            hideDropdown();
            return;
        }

        list.forEach(function (item, index) {
            var option = document.createElement('div');
            option.className = 'place-suggestion-item';
            option.style.padding = '8px 10px';
            option.style.cursor = 'pointer';
            option.style.borderBottom = '1px solid #f1f3f5';
            option.textContent = item.text;
            option.addEventListener('mouseenter', function () {
                setActive(index);
            });
            option.addEventListener('mousedown', function (event) {
                event.preventDefault();
            });
            option.addEventListener('click', function () {
                selectSuggestion(item);
            });
            dropdown.appendChild(option);
        });

        dropdown.style.display = 'block';
        positionDropdown();
    }

    function fetchSuggestions(inputText) {
        return fetch(autocompleteEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Goog-Api-Key': placesApiKey,
                'X-Goog-FieldMask': 'suggestions.placePrediction.placeId,suggestions.placePrediction.text'
            },
            body: JSON.stringify({
                input: inputText,
                sessionToken: sessionToken,
                includedRegionCodes: ['in']
            })
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok) {
                        var errMsg = data && data.error && data.error.message
                            ? data.error.message
                            : 'Autocomplete request failed';
                        throw new Error('Autocomplete API (' + response.status + '): ' + errMsg);
                    }
                    return data;
                });
            })
            .then(function (data) {
                var raw = (data && data.suggestions) ? data.suggestions : [];
                return raw
                    .map(function (entry) {
                        var prediction = entry.placePrediction || null;
                        if (!prediction || !prediction.placeId || !prediction.text || !prediction.text.text) {
                            return null;
                        }
                        return {
                            placeId: prediction.placeId,
                            text: prediction.text.text
                        };
                    })
                    .filter(Boolean);
            })
            .catch(function (error) {
                console.error(error);
                return [];
            });
    }

    function selectSuggestion(item) {
        locationInput.value = item.text;
        hideDropdown();

        fetch(detailsEndpoint + encodeURIComponent(item.placeId), {
            method: 'GET',
            headers: {
                'X-Goog-Api-Key': placesApiKey,
                'X-Goog-FieldMask': 'formattedAddress,location'
            }
        })
            .then(function (response) {
                return response.json().then(function (data) {
                    if (!response.ok) {
                        var errMsg = data && data.error && data.error.message
                            ? data.error.message
                            : 'Place details request failed';
                        throw new Error('Place Details API (' + response.status + '): ' + errMsg);
                    }
                    return data;
                });
            })
            .then(function (place) {
                if (place && place.formattedAddress) {
                    locationInput.value = place.formattedAddress;
                }

                if (place && place.location) {
                    latitudeInput.value = place.location.latitude || '';
                    longitudeInput.value = place.location.longitude || '';
                }
            })
            .catch(function (error) {
                console.error(error);
                latitudeInput.value = '';
                longitudeInput.value = '';
            });
    }

    locationInput.addEventListener('input', function () {
        var value = locationInput.value.trim();
        latitudeInput.value = '';
        longitudeInput.value = '';

        clearTimeout(debounceTimer);

        if (value.length < 3) {
            hideDropdown();
            return;
        }

        debounceTimer = setTimeout(function () {
            fetchSuggestions(value).then(renderSuggestions);
        }, 250);
    });

    locationInput.addEventListener('keydown', function (event) {
        if (!dropdown || dropdown.style.display === 'none') {
            return;
        }

        var itemsCount = suggestions.length;
        if (!itemsCount) {
            return;
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            setActive((activeIndex + 1) % itemsCount);
            return;
        }

        if (event.key === 'ArrowUp') {
            event.preventDefault();
            setActive((activeIndex - 1 + itemsCount) % itemsCount);
            return;
        }

        if (event.key === 'Enter') {
            if (activeIndex >= 0 && suggestions[activeIndex]) {
                event.preventDefault();
                selectSuggestion(suggestions[activeIndex]);
            }
            return;
        }

        if (event.key === 'Escape') {
            hideDropdown();
        }
    });

    locationInput.addEventListener('focus', positionDropdown);
    window.addEventListener('resize', positionDropdown);
    window.addEventListener('scroll', positionDropdown, true);
    document.addEventListener('click', function (event) {
        if (event.target !== locationInput && dropdown && !dropdown.contains(event.target)) {
            hideDropdown();
        }
    });
});
</script>
@endpush