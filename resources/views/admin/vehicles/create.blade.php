@extends('layouts.app')

@section('title', 'Add New Vehicle | ECROS')

@section('content')
    <div style="max-width: 900px; margin: 0 auto; display: grid; gap: 32px;">
        <div class="section-heading">
            <div>
                <span class="eyebrow">Fleet Management</span>
                <h2>Add New Vehicle</h2>
                <p class="lead">Register a new electric vehicle into the ECROS fleet.</p>
            </div>
        </div>

        <form action="{{ route('admin.vehicles.store') }}" method="POST" enctype="multipart/form-data" class="panel" style="padding: 40px; display: grid; gap: 40px;">
            @csrf
            
            <section>
                <h3 style="font-size: 1.1rem; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 1px solid var(--line);">Vehicle Imagery</h3>
                <div style="display: grid; grid-template-columns: 240px 1fr; gap: 32px; align-items: start;">
                    <div id="image-preview" style="width: 240px; height: 160px; background: var(--bg-soft); border: 2px dashed var(--line); border-radius: 20px; display: grid; place-items: center; color: var(--muted); overflow: hidden;">
                        <div style="text-align: center; padding: 20px;">
                            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-bottom: 8px;"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                            <span style="font-size: 0.75rem; font-weight: 600; display: block;">No photo selected</span>
                        </div>
                    </div>
                    <div style="display: grid; gap: 12px;">
                        <p style="font-size: 0.9rem; color: var(--muted);">Upload a high-quality image of the vehicle. Recommended size: 1200x800px. Supports JPG, PNG.</p>
                        <label class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; width: fit-content;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
                            Choose Photo
                            <input type="file" name="vehicle_photo" accept="image/*" style="display: none;" onchange="previewImage(this)">
                        </label>
                    </div>
                </div>
            </section>

            <section>
                <h3 style="font-size: 1.1rem; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 1px solid var(--line);">Basic Information</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                    <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Vehicle Name</span>
                        <input type="text" name="name" placeholder="e.g. Tesla Model 3 Long Range" style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none;">
                    </label>
                    <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Plate Number</span>
                        <input type="text" name="plate_number" placeholder="ABC-1234" style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none;">
                    </label>
                </div>
                <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px; margin-top: 24px;">
                    <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Description</span>
                    <textarea name="description" rows="3" placeholder="Briefly describe the vehicle's features..." style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none; resize: none;"></textarea>
                </label>
            </section>

            <section>
                <h3 style="font-size: 1.1rem; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 1px solid var(--line);">Technical Specifications</h3>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;">
                    <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Battery Capacity (kWh)</span>
                        <input type="number" name="battery_capacity" value="75" style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none;">
                    </label>
                    <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Max Range (km)</span>
                        <input type="number" name="max_range" value="450" style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none;">
                    </label>
                    <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Current Odometer (km)</span>
                        <input type="number" name="odometer_km" value="0" style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none;">
                    </label>
                </div>
            </section>

            <section>
                <h3 style="font-size: 1.1rem; margin-bottom: 24px; padding-bottom: 12px; border-bottom: 1px solid var(--line);">Pricing & Location</h3>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px;">
                    <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Daily Rate (PHP)</span>
                        <input type="number" name="daily_rate" value="2500" style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none;">
                    </label>
                    <label class="modern-field" style="background: var(--bg-soft); border: 1px solid var(--line); border-radius: 16px; padding: 12px 20px;">
                        <span style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: var(--muted); display: block; margin-bottom: 4px;">Initial Zone</span>
                        <select name="location_zone" style="width: 100%; border: none; background: transparent; font-size: 1rem; font-weight: 600; color: var(--text); outline: none; appearance: none;">
                            <option>Davao City - Central</option>
                            <option>Davao City - North</option>
                            <option>Davao City - South</option>
                        </select>
                    </label>
                </div>
            </section>

            <div style="display: flex; justify-content: flex-end; gap: 16px; padding-top: 24px; border-top: 1px solid var(--line);">
                <a href="{{ route('fleet.index') }}" class="btn btn-secondary" style="padding: 16px 32px;">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding: 16px 48px;">Register Vehicle</button>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                    preview.style.borderStyle = 'solid';
                    preview.style.borderColor = 'var(--primary)';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
