(function () {
    const STORAGE_KEY = 'portal_ride_image';

    function initRideForm(options) {
        options = options || {};
        const input = document.getElementById(options.inputId || 'ride_image');
        const label = input ? input.closest('.ride-upload-label') : null;
        const preview = label ? label.querySelector('.ride-image-preview') : null;
        const hint = label ? label.querySelector('.ride-upload-hint') : null;

        if (!input || !label) {
            return;
        }

        function updatePreview(dataUrl, fileName) {
            if (preview) {
                preview.src = dataUrl;
                preview.classList.remove('d-none');
            }
            if (hint) {
                hint.classList.add('d-none');
            }
            if (fileName) {
                const span = label.querySelector('.ride-upload-filename');
                if (span) {
                    span.textContent = fileName;
                    span.classList.remove('d-none');
                }
            }
            label.classList.add('has-file');
        }

        function saveFile(file) {
            const reader = new FileReader();
            reader.onload = function (event) {
                sessionStorage.setItem(STORAGE_KEY, JSON.stringify({
                    data: event.target.result,
                    name: file.name,
                    type: file.type,
                }));
                updatePreview(event.target.result, file.name);
            };
            reader.readAsDataURL(file);
        }

        function restoreFileToInput(dataUrl, name, type) {
            fetch(dataUrl)
                .then(function (response) { return response.blob(); })
                .then(function (blob) {
                    const file = new File([blob], name, { type: type });
                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    input.files = transfer.files;
                })
                .catch(function () {});
        }

        function restoreFromStorage() {
            const raw = sessionStorage.getItem(STORAGE_KEY);
            if (!raw) {
                return;
            }

            try {
                const stored = JSON.parse(raw);
                if (!stored.data) {
                    return;
                }

                updatePreview(stored.data, stored.name);
                restoreFileToInput(stored.data, stored.name, stored.type);
            } catch (error) {
                sessionStorage.removeItem(STORAGE_KEY);
            }
        }

        input.addEventListener('change', function () {
            if (input.files && input.files[0]) {
                saveFile(input.files[0]);
            }
        });

        if (options.clearOnSuccess) {
            sessionStorage.removeItem(STORAGE_KEY);
        } else if (options.restoreOnError) {
            restoreFromStorage();
        }
    }

    window.initRideForm = initRideForm;
})();
