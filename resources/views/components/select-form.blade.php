<div class="mb-3">
    <label for="{{ $id }}" class="form-label">{{ $label }}</label>
    <select name="{{ $name }}" id="{{ $id }}" class="form-select">
        <option value="">-- Pilih --</option>
        @foreach($options as $option)
            <option value="{{ $option->id }}"
                @selected(old($name, $selected) == $option->id)>
                {{ $option->name }}
            </option>
        @endforeach
    </select>
</div>
