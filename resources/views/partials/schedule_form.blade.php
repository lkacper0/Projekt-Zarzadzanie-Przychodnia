<div class="sch-form-card">
    <h2 class="card-title">Dodaj dostępność</h2>
    <p style="color: #64748b; margin-top: -8px; margin-bottom: 20px;">
        Wybierz zakres dat, przedział godzinowy i długość pojedynczego slotu wizyty.
    </p>

    <form action="{{ $formAction }}" method="POST" class="sch-form">
        @csrf

        @if(!empty($doctors))
            <div class="form-group" style="margin-bottom: 20px;">
                <label for="doctor_id">Lekarz</label>
                <select id="doctor_id" class="form-control" required
                        onchange="window.location.href='{{ url('/admin/godziny-pracy') }}?doctor_id=' + this.value">
                    @foreach($doctors as $doc)
                        <option value="{{ $doc->id }}" {{ ($selectedDoctorId ?? null) == $doc->id ? 'selected' : '' }}>
                            dr {{ $doc->user->first_name }} {{ $doc->user->last_name }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="doctor_id" value="{{ $selectedDoctorId }}">
            </div>
        @endif

        <div class="sch-form-row">
            <div class="form-group">
                <label for="date_from">Data od</label>
                <input type="date" name="date_from" id="date_from" class="form-control"
                       value="{{ old('date_from', date('Y-m-d')) }}"
                       min="{{ date('Y-m-d') }}" required>
            </div>

            <div class="form-group">
                <label for="date_to">Data do</label>
                <input type="date" name="date_to" id="date_to" class="form-control"
                       value="{{ old('date_to', date('Y-m-d', strtotime('+7 days'))) }}"
                       min="{{ date('Y-m-d') }}" required>
            </div>

            <div class="form-group">
                <label for="start_time">Od</label>
                <input type="time" name="start_time" id="start_time" class="form-control"
                       value="{{ old('start_time', '08:00') }}" required>
            </div>

            <div class="form-group">
                <label for="end_time">Do</label>
                <input type="time" name="end_time" id="end_time" class="form-control"
                       value="{{ old('end_time', '16:00') }}" required>
            </div>

            <div class="form-group">
                <label for="slot_minutes">Slot (min)</label>
                <select name="slot_minutes" id="slot_minutes" class="form-control">
                    @foreach([10, 15, 20, 30, 45, 60] as $mins)
                        <option value="{{ $mins }}" {{ old('slot_minutes', 15) == $mins ? 'selected' : '' }}>{{ $mins }} min</option>
                    @endforeach
                </select>
            </div>
        </div>


        <div style="margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Generuj sloty</button>
        </div>
    </form>
</div>
