<!DOCTYPE html>
<html>
<head>
</head>
<body>

<script language='javascript' type='text/javascript'>
    function LoginButtonClick() {
        const email_box = document.getElementById('email');
        const pass_box = document.getElementById('password');
        const email = email_box.value;
        const pass = pass_box.value;
        let request = new XMLHttpRequest();
        request.open('POST', '/api/login');
        request.responseType = 'json';
        request.onload = function () {
            location.reload();
        };
        request.setRequestHeader('Content-Type', 'application/json');
        request.send(JSON.stringify({
            'email':email,
            'password':pass
        }));
    }
    function LogoutButtonClick() {
        let request = new XMLHttpRequest();
        request.open('POST', '/api/logout');
        request.onload = function () {
            location.reload();
        };
        request.send();
    }
</script>

<div>login form</div>
<form action='/api/login' method='POST'>
    @csrf
    <label>
        email
        <input id='email' name='email' type='email'>
    </label>
    <br>
    <label>
        password
        <input id='password' name='password' type='password'>
    </label>
    <button type='button' onclick='LoginButtonClick()'>送信</button>
</form>
@auth
    <div>現在ログイン中です。</div>
    <form action='/api/logout' method='POST'>
        @csrf
        <button type='button' onclick='LogoutButtonClick()'>ログアウト</button>
    </form>
@endauth
@guest
    <div>現在ログインしていません。</div>
@endguest
</body>
</html>
