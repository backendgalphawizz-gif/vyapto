@extends('layouts.admin')

@section('title', 'Business Setup')

@push('css_or_js')

<link href="{{ asset('public/assets/back-end/css/custom.css')}}" rel="stylesheet">

<meta name="csrf-token" content="{{ csrf_token() }}">

@endpush

@section('content')

<div class="main-section">

    <div class="pb-2">

        <h2 class="h1 mb-0 text-capitalize d-flex align-items-center gap-2">

            <img src="{{asset('/public/assets/back-end/img/business-setup.png')}}" alt="">

            Business Setup

        </h2>

    </div>

    @php

    $settings = \App\Models\Setting::pluck('value','type');
    $companyName = $settings['company_name'] ?? '';

    $companyPhone = $settings['company_phone'] ?? '';

    $companyEmail = $settings['company_email'] ?? '';

    $companyAddress = $settings['company_address'] ?? '';
    $companyStartTime = $settings['company_start_time'] ?? '';
    $companyHalfTime = $settings['company_half_time'] ?? '';
    $companyEndTime = $settings['company_end_time'] ?? '';
    $latitude = $settings['company_latitude'] ?? '';
    $longitude = $settings['company_longitude'] ?? '';
    $logo = $settings['company_web_logo'] ?? null;

    @endphp

    <form action="{{ route('settings.update-info') }}" method="POST" enctype="multipart/form-data" id="websiteInfoForm" novalidate>

        @csrf

        <!-- Company Information -->

        <div class="card mb-3">

            <div class="card-header">

                <h5 class="mb-0 text-capitalize d-flex gap-1">

                    <i class="tio-user-big"></i>

                    Company Information
                </h5>

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="title-color" for="company_name">Company Name <span class="text-danger">*</span></label>

                            <input class="form-control website-validate @error('company_name') is-invalid @enderror"
                                   id="company_name"
                                   name="company_name"
                                   value="{{ old('company_name', $companyName) }}"
                                   maxlength="200"
                                   minlength="2"
                                   autocomplete="organization"
                                   required
                                   data-rules="company_name">

                            @error('company_name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback js-client-error d-none" data-for="company_name" role="alert"></div>
                            @enderror

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="title-color" for="company_phone">Phone <span class="text-danger">*</span></label>

                            <input class="form-control website-validate @error('company_phone') is-invalid @enderror"
                                   type="tel"
                                   id="company_phone"
                                   name="company_phone"
                                   value="{{ old('company_phone', $companyPhone) }}"
                                   maxlength="20"
                                   inputmode="tel"
                                   autocomplete="tel"
                                   placeholder="e.g. 9876543210"
                                   required
                                   data-rules="phone">

                            @error('company_phone')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback js-client-error d-none" data-for="company_phone" role="alert"></div>
                            @enderror

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="title-color" for="company_email">Email <span class="text-danger">*</span></label>

                            <input class="form-control website-validate @error('company_email') is-invalid @enderror"
                                   type="email"
                                   id="company_email"
                                   name="company_email"
                                   value="{{ old('company_email', $companyEmail) }}"
                                   maxlength="120"
                                   autocomplete="email"
                                   placeholder="company@example.com"
                                   required
                                   data-rules="email">

                            @error('company_email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback js-client-error d-none" data-for="company_email" role="alert"></div>
                            @enderror

                        </div>

                    </div>

                    <div class="col-md-4">

                        <div class="form-group">

                            <label class="title-color" for="company_address">Company Address <span class="text-danger">*</span></label>

                            <input class="form-control website-validate @error('company_address') is-invalid @enderror"
                                   id="company_address"
                                   name="company_address"
                                   value="{{ old('company_address', $companyAddress) }}"
                                   maxlength="500"
                                   minlength="5"
                                   autocomplete="street-address"
                                   required
                                   data-rules="address">

                            @error('company_address')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback js-client-error d-none" data-for="company_address" role="alert"></div>
                            @enderror

                        </div>

                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color" for="latitude">Latitude</label>
                            <input class="form-control website-validate @error('latitude') is-invalid @enderror"
                                   type="text"
                                   inputmode="decimal"
                                   id="latitude"
                                   name="latitude"
                                   value="{{ old('latitude', $latitude) }}"
                                   placeholder="-90 to 90"
                                   maxlength="12"
                                   data-rules="latitude">
                            <small class="text-muted">Optional. Decimal between -90 and 90.</small>
                            @error('latitude')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback js-client-error d-none" data-for="latitude" role="alert"></div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color" for="longitude">Longitude</label>
                            <input class="form-control website-validate @error('longitude') is-invalid @enderror"
                                   type="text"
                                   inputmode="decimal"
                                   id="longitude"
                                   name="longitude"
                                   value="{{ old('longitude', $longitude) }}"
                                   placeholder="-180 to 180"
                                   maxlength="13"
                                   data-rules="longitude">
                            <small class="text-muted">Optional. Decimal between -180 and 180.</small>
                            @error('longitude')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback js-client-error d-none" data-for="longitude" role="alert"></div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color" for="company_start_time">Start Time</label>
                            <input type="time" class="form-control website-validate @error('company_start_time') is-invalid @enderror"
                                   id="company_start_time"
                                   name="company_start_time"
                                   value="{{ old('company_start_time', $companyStartTime) }}"
                                   data-rules="time_start">
                            @error('company_start_time')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback js-client-error d-none" data-for="company_start_time" role="alert"></div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color" for="company_half_time">Half Time</label>
                            <input type="time" class="form-control website-validate @error('company_half_time') is-invalid @enderror"
                                   id="company_half_time"
                                   name="company_half_time"
                                   value="{{ old('company_half_time', $companyHalfTime) }}"
                                   data-rules="time_half">
                            @error('company_half_time')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback js-client-error d-none" data-for="company_half_time" role="alert"></div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="title-color" for="company_end_time">End Time</label>
                            <input type="time" class="form-control website-validate @error('company_end_time') is-invalid @enderror"
                                   id="company_end_time"
                                   name="company_end_time"
                                   value="{{ old('company_end_time', $companyEndTime) }}"
                                   data-rules="time_end">
                            @error('company_end_time')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback js-client-error d-none" data-for="company_end_time" role="alert"></div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="invalid-feedback d-none mb-2" id="timeOrderError" role="alert"></div>
                    </div>

                </div>

            </div>

        </div>

        <!-- Website Logo -->

        <div class="row">

            <div class="col-xxl-4 col-sm-6 mb-3">

                <div class="card h-100">

                    <div class="card-header">

                        <h5 class="mb-0 text-capitalize d-flex align-items-center gap-2">

                            <img src="{{asset('/public/assets/back-end/img/header-logo.png')}}" alt="">

                            Website Logo

                        </h5>

                        <span class="badge badge-soft-info">(250x60 px)</span>

                    </div>

                    <div class="card-body d-flex flex-column justify-content-around">

                        <center>

                            <img height="60" id="viewerWL"

                                src="{{ $logo ? asset('storage/company/'.$logo) : asset('assets/admin/images/no-image.png') }}">

                        </center>

                        <div class="mt-4 position-relative">

                            <input type="file" name="company_web_logo" id="customFileUploadWL"

                                class="custom-file-input website-file"

                                accept="image/png,image/jpeg,image/jpg,image/gif,image/webp">

                            <label class="custom-file-label" for="customFileUploadWL">Choose File</label>

                            <div class="invalid-feedback d-none mt-1" id="logoFileError" role="alert"></div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="d-flex justify-content-end">

            <button type="submit" class="btn btn-primary px-4" id="websiteInfoSubmit">Submit</button>

        </div>

    </form>

</div>

@endsection

@push('scripts')

<script>
(function () {
    'use strict';

    var MAX_LOGO_BYTES = 2 * 1024 * 1024;
    var PHONE_RE = /^[+]?[\d\s\-()]{8,20}$/;

    /** Single-character allow lists for keydown (no typing disallowed chars) */
    var KEY_ALLOW = {
        company_name: /^[a-zA-Z0-9\s&.,'()\-\/+#]$/,
        phone: /^[0-9+\-() ]$/,
        email: /^[a-zA-Z0-9@._+\-]$/,
        address: /^[a-zA-Z0-9\s&.,'()\-\/+#:;"]$/,
        latitude: /^[0-9.\-]$/,
        longitude: /^[0-9.\-]$/
    };

    function qs(sel, root) { return (root || document).querySelector(sel); }
    function qsa(sel, root) { return Array.prototype.slice.call((root || document).querySelectorAll(sel)); }

    function hasUnicodeProps() {
        try { return new RegExp('\\p{L}', 'u').test('a'); } catch (e) { return false; }
    }

    /** Strip / normalize pasted or remaining invalid characters */
    function sanitizeValue(rules, value) {
        if (value == null) return '';
        var s = String(value);
        switch (rules) {
            case 'company_name':
                if (hasUnicodeProps()) {
                    s = s.replace(/[^\p{L}\p{N}\s&.,'()\-\/+#]/gu, '');
                } else {
                    s = s.replace(/[^A-Za-z0-9\s&.,'()\-\/+#]/g, '');
                }
                return s;
            case 'phone':
                s = s.replace(/[^0-9+\-() ]/g, '');
                var plus = s.indexOf('+');
                if (plus > 0) s = s.replace(/\+/g, '');
                else if (plus === 0) s = '+' + s.slice(1).replace(/\+/g, '');
                return s.slice(0, 20);
            case 'email':
                return s.replace(/[^a-zA-Z0-9@._+\-]/g, '').slice(0, 120);
            case 'address':
                if (hasUnicodeProps()) {
                    s = s.replace(/[^\p{L}\p{N}\s&.,'()\-\/+#:;"]/gu, '');
                } else {
                    s = s.replace(/[^A-Za-z0-9\s&.,'()\-\/+#:;"]/g, '');
                }
                return s.slice(0, 500);
            case 'latitude':
            case 'longitude':
                s = s.replace(/[^\d.\-]/g, '');
                var neg = s.charAt(0) === '-';
                s = s.replace(/-/g, '');
                if (neg) s = '-' + s;
                var dot = s.indexOf('.');
                if (dot !== -1) {
                    s = s.slice(0, dot + 1) + s.slice(dot + 1).replace(/\./g, '');
                }
                return s.slice(0, 13);
            default:
                return s;
        }
    }

    function allowKeydown(e, rules) {
        if (e.ctrlKey || e.metaKey || e.altKey) return true;
        if (e.key.length !== 1) return true;
        var re = KEY_ALLOW[rules];
        if (!re) return true;
        if (hasUnicodeProps() && (rules === 'company_name' || rules === 'address')) {
            if (rules === 'company_name') {
                return /[\p{L}\p{N}\s&.,'()\-\/+#]/u.test(e.key);
            }
            return /[\p{L}\p{N}\s&.,'()\-\/+#:;"]/u.test(e.key);
        }
        return re.test(e.key);
    }

    function onKeydownFilter(e) {
        var rules = e.target.getAttribute('data-rules');
        if (!rules || !KEY_ALLOW[rules]) return;
        if (allowKeydown(e, rules)) return;
        e.preventDefault();
    }

    function onInputSanitize(e) {
        var input = e.target;
        var rules = input.getAttribute('data-rules');
        if (!rules) return;
        var next = sanitizeValue(rules, input.value);
        if (next !== input.value) {
            var pos = input.selectionStart;
            input.value = next;
            if (typeof pos === 'number') {
                try { input.setSelectionRange(Math.min(pos, next.length), Math.min(pos, next.length)); } catch (err) {}
            }
        }
        runValidator(input);
        if (input.id && input.id.indexOf('company_') === 0 && input.type === 'time') {
            validateTimeOrder();
        }
    }

    function getClientErrorEl(input) {
        var g = input.closest('.form-group');
        if (!g) return null;
        return g.querySelector('.js-client-error[data-for="' + input.name + '"]');
    }

    function validateCompanyName(v) {
        v = (v || '').trim();
        if (!v) return 'Company name is required.';
        if (v.length < 2) return 'Company name must be at least 2 characters.';
        if (v.length > 200) return 'Company name must not exceed 200 characters.';
        return '';
    }

    function validatePhone(v) {
        v = (v || '').trim();
        if (!v) return 'Phone number is required.';
        var digits = v.replace(/\D/g, '');
        if (digits.length < 8 || digits.length > 15) return 'Enter a valid phone number (8–15 digits).';
        if (!PHONE_RE.test(v)) return 'Use only digits, spaces, +, -, and parentheses.';
        return '';
    }

    function validateEmail(v) {
        v = (v || '').trim();
        if (!v) return 'Email is required.';
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!re.test(v)) return 'Enter a valid email address.';
        if (v.length > 120) return 'Email must not exceed 120 characters.';
        return '';
    }

    function validateAddress(v) {
        v = (v || '').trim();
        if (!v) return 'Address is required.';
        if (v.length < 5) return 'Address must be at least 5 characters.';
        if (v.length > 500) return 'Address must not exceed 500 characters.';
        return '';
    }

    function validateLatitude(v) {
        v = (v || '').trim();
        if (v === '') return '';
        var n = parseFloat(v);
        if (isNaN(n)) return 'Latitude must be a valid number.';
        if (n < -90 || n > 90) return 'Latitude must be between -90 and 90.';
        return '';
    }

    function validateLongitude(v) {
        v = (v || '').trim();
        if (v === '') return '';
        var n = parseFloat(v);
        if (isNaN(n)) return 'Longitude must be a valid number.';
        if (n < -180 || n > 180) return 'Longitude must be between -180 and 180.';
        return '';
    }

    function timeToMinutes(t) {
        if (!t || typeof t !== 'string') return null;
        var p = t.split(':');
        if (p.length < 2) return null;
        var h = parseInt(p[0], 10), m = parseInt(p[1], 10);
        if (isNaN(h) || isNaN(m)) return null;
        return h * 60 + m;
    }

    function validateTimeOrder() {
        var start = qs('#company_start_time');
        var half = qs('#company_half_time');
        var end = qs('#company_end_time');
        var errBox = qs('#timeOrderError');
        if (!start || !half || !end || !errBox) return true;

        var vs = start.value, vh = half.value, ve = end.value;
        if (!vs || !vh || !ve) {
            errBox.classList.add('d-none');
            errBox.textContent = '';
            return true;
        }
        var ms = timeToMinutes(vs), mh = timeToMinutes(vh), me = timeToMinutes(ve);
        if (ms === null || mh === null || me === null) return true;

        if (me >= ms) {
            if (mh < ms || mh > me) {
                errBox.textContent = 'Half time must be between start time and end time.';
                errBox.classList.remove('d-none');
                return false;
            }
        } else {
            if (!(mh >= ms || mh <= me)) {
                errBox.textContent = 'Half time must fall within the working window (including overnight shifts).';
                errBox.classList.remove('d-none');
                return false;
            }
        }
        errBox.classList.add('d-none');
        errBox.textContent = '';
        return true;
    }

    function runValidator(input) {
        var rules = input.getAttribute('data-rules');
        var v = input.value;
        var msg = '';

        switch (rules) {
            case 'company_name': msg = validateCompanyName(v); break;
            case 'phone': msg = validatePhone(v); break;
            case 'email': msg = validateEmail(v); break;
            case 'address': msg = validateAddress(v); break;
            case 'latitude': msg = validateLatitude(v); break;
            case 'longitude': msg = validateLongitude(v); break;
            case 'time_start':
            case 'time_half':
            case 'time_end':
                msg = '';
                break;
            default:
                msg = '';
        }

        var el = getClientErrorEl(input);
        if (msg) {
            input.classList.add('is-invalid');
            if (el) {
                el.textContent = msg;
                el.classList.remove('d-none');
            }
        } else if (el) {
            el.textContent = '';
            el.classList.add('d-none');
            input.classList.remove('is-invalid');
        }
        return !msg;
    }

    function validateLogoFile(input) {
        var err = qs('#logoFileError');
        if (!err) return true;
        err.classList.add('d-none');
        err.textContent = '';
        if (!input.files || !input.files[0]) return true;

        var f = input.files[0];
        if (f.size > MAX_LOGO_BYTES) {
            err.textContent = 'Image must be 2 MB or smaller.';
            err.classList.remove('d-none');
            return false;
        }
        var ok = /^image\/(png|jpeg|jpg|gif|webp)$/i.test(f.type);
        if (!ok) {
            err.textContent = 'Allowed types: PNG, JPG, GIF, WebP.';
            err.classList.remove('d-none');
            return false;
        }
        return true;
    }

    qsa('.website-validate').forEach(function (input) {
        input.addEventListener('keydown', onKeydownFilter);
        input.addEventListener('input', onInputSanitize);
        input.addEventListener('paste', function (ev) {
            var rules = ev.target.getAttribute('data-rules');
            if (!rules) return;
            ev.preventDefault();
            var paste = (ev.clipboardData || window.clipboardData).getData('text');
            var merged = ev.target.value.slice(0, ev.target.selectionStart) + paste + ev.target.value.slice(ev.target.selectionEnd);
            ev.target.value = sanitizeValue(rules, merged);
            ev.target.dispatchEvent(new Event('input', { bubbles: true }));
        });
        input.addEventListener('blur', function () {
            runValidator(input);
            if (input.id && input.id.indexOf('company_') === 0 && input.type === 'time') {
                validateTimeOrder();
            }
        });
    });

    var fileInput = qs('#customFileUploadWL');
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            validateLogoFile(fileInput);
        });
    }

    var form = qs('#websiteInfoForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            var ok = true;
            qsa('.website-validate').forEach(function (input) {
                if (!runValidator(input)) ok = false;
            });
            if (!validateTimeOrder()) ok = false;
            if (fileInput && !validateLogoFile(fileInput)) ok = false;

            if (!ok) {
                e.preventDefault();
                e.stopPropagation();
                var first = form.querySelector('.is-invalid');
                if (first && first.focus) first.focus();
            }
        });
    }
})();

$(document).ready(function() {
    $("#customFileUploadWL").on('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#viewerWL').attr('src', e.target.result);
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
});
</script>

@endpush
