<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Penjual Baru</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h1 { text-align: center; color: #333; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .btn { background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background-color: #0056b3; }
        .error { color: red; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tambah Penjual Baru</h1>
        <form method="POST" action="{{ route('sellers.store') }}">
            @csrf

            <div class="form-group">
                <label for="store_name">Nama Toko</label>
                <input type="text" id="store_name" name="store_name" value="{{ old('store_name') }}" required>
                @if ($errors->has('store_name'))
                    <div class="error">{{ $errors->first('store_name') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="owner_name">Nama Pemilik</label>
                <input type="text" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" required>
                @if ($errors->has('owner_name'))
                    <div class="error">{{ $errors->first('owner_name') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @if ($errors->has('email'))
                    <div class="error">{{ $errors->first('email') }}</div>
                @endif
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                @if ($errors->has('password'))
                    <div class="error">{{ $errors->first('password') }}</div>
                @endif
            </div>

            <button type="submit" class="btn">Tambah Penjual</button>
        </form>
    </div>
</body>
</html>