<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Q-PRI</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-indigo-600 min-h-screen flex items-center justify-center">

  <form method="POST" action="{{ route('login') }}"
        class="bg-white p-8 rounded-lg w-96 shadow">
    @csrf

    <h1 class="text-xl font-semibold mb-6 text-center">Login</h1>

    <input type="email" name="email" placeholder="Email"
           class="w-full border px-4 py-2 rounded mb-4" required>

    <input type="password" name="password" placeholder="Password"
           class="w-full border px-4 py-2 rounded mb-4" required>

    <button class="w-full bg-indigo-600 text-white py-2 rounded">
      Login
    </button>
  </form>

</body>
</html>
