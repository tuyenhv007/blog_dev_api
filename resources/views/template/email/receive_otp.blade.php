<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
          integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N"
          crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"
            integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+"
            crossorigin="anonymous"></script>
    <title>Document</title>
</head>
<body>
    <div>
        <h2 style="color: #2d3748">KÍCH HOẠT TÀI KHOẢN</h2>
        <p style="font-size: 16px;">
            Mã OTP để kích hoạt tài khoản trên blog.develop của bạn là: <br>
            <span style="color: #f27d00;">{{ $data }}</span>
        </p>
        <p>(<span class="text-danger">*</span>) <i>Lưu ý: Mã OTP chỉ có hiệu lực trong vòng 10 phút</i></p>
    </div>
</body>
</html>
