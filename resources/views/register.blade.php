@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="text-center my-4">Register User</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('register.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
            @csrf

            <!-- Name -->
            <div class="col-md-6">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}">
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Email -->
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Phone -->
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" placeholder="+380XXXXXXXXX" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Position -->
            <div class="col-md-6">
                <label class="form-label">Position</label>
                <select name="position_id" class="form-select @error('position_id') is-invalid @enderror">
                    <option value="">Select position</option>
                    @foreach($positions as $position)
                        <option value="{{ $position['id'] }}" {{ old('position_id') == $position['id'] ? 'selected' : '' }}>
                            {{ $position['name'] }}
                        </option>
                    @endforeach
                </select>
                @error('position_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <!-- Photo -->
            <div class="col-md-6">
                <label class="form-label">Photo (JPG, 70x70px min, max 5MB)</label>
                <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror">
                @error('photo') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
        </form>
    </div>
@endsection
