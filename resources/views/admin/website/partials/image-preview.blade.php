@if($record && $record->image)
    <div class="mt-2 mb-2">
        <img src="{{ $record->imageUrl() }}" alt="Current" class="rounded border" style="max-height:120px;object-fit:cover;">
        <div class="form-check mt-2">
            <input type="checkbox" name="remove_image" value="1" class="form-check-input" id="remove_image_{{ $fieldId ?? 'image' }}">
            <label class="form-check-label" for="remove_image_{{ $fieldId ?? 'image' }}">Remove current image</label>
        </div>
    </div>
@endif
