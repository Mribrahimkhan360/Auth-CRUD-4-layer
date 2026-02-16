<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg p-8 w-full max-w-md">
        <h1 class="text-2xl font-bold text-gray-800 text-center mb-6">
            Welcome to login CRUD
        </h1>
        <form action="{{ route('auth.login') }}">
            @csrf
            <div>
                <label for="email" class="block text-gray-700 mb-1">Email</label>
                <input id="email" type="email" name="email" placeholder="Enter your email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-gray-700 mb-1" for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" required
                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

            </div>

            <button type="submit"
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition mt-3">
                Login
            </button>


        </form>
    </div>
</body>
</html>
