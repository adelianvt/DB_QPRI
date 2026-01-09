<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <style>
    .qpri-bg{
      background: linear-gradient(
        to bottom,
        #ffffff 0%,
        #ffffff 18%,
        #eef2ff 40%,
        #c7d2fe 70%,
        #a5b4fc 100%
      );
    }
    .qpri-box{
      width: 520px;
      max-width: 92vw;
      padding: 44px 56px;
      border: 2px dashed rgba(59,130,246,.55);
      border-radius: 10px;
      background: transparent;
    }
    .qpri-card{
      width: 440px;
      max-width: 92vw;
    }
    .qpri-input{
      height: 52px;
      border-radius: 10px;
      border: 1px solid rgba(148,163,184,.9);
      background: #fff;
      padding: 0 22px;
      outline: none;
    }
    .qpri-input:focus{
      border-color: rgba(99,102,241,.9);
      box-shadow: 0 0 0 3px rgba(99,102,241,.18);
    }
    .qpri-btn{
      width: 170px;
      height: 48px;
      border-radius: 10px;
      background: #4f6ef7;
      box-shadow: 0 10px 18px rgba(79,110,247,.25);
    }
    .qpri-btn:hover{ background:#3f5ef3; }
  </style>
</head>

<body class="min-h-screen qpri-bg flex items-center justify-center">
  <div class="qpri-card">
    <div class="qpri-box px-10 py-10">

      <div class="text-center text-[16px] font-semibold text-gray-700 mb-7">
        Login
      </div>

      <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
          <label class="block text-[12px] font-semibold text-gray-600 mb-2">NIP / Email</label>
          <input
            type="text"
            name="nip"
            value="{{ old('nip') }}"
            placeholder="Type here"
            class="qpri-input w-full"
            required
            autofocus
            autocomplete="username"
          >
        </div>

        <div>
          <label class="block text-[12px] font-semibold text-gray-600 mb-2">Password</label>
          <input
            type="password"
            name="password"
            placeholder="Typing |"
            class="qpri-input w-full"
            required
            autocomplete="current-password"
          >
          <div class="flex justify-end mt-2">
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}"
                 class="text-[11px] text-gray-600 hover:underline">
                Forgot password ?
              </a>
            @endif
          </div>
        </div>

        @if ($errors->any())
          <div class="text-sm text-red-600">
            {{ $errors->first() }}
          </div>
        @endif

        <div class="pt-3 flex justify-center">
          <button type="submit" class="qpri-btn text-white font-medium">
            Login
          </button>
        </div>

      </form>
    </div>
  </div>
</body>
</html>