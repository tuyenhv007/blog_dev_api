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
        <p style="font-size: 16px;">
            <span style="color: #f27d00; font-weight: bold;">{{ $data }}</span> -
            <span style="color: #500050;">là mã OTP để kích hoạt tài khoản của bạn trên blog.develop.</span>
        </p>
        <p>
            <i>(<span style="color: red;">*</span>) Lưu ý: <br>
                - Mã OTP chỉ có hiệu lực trong vòng 05 phút; <br>
                - Nếu mã OTP quá hạn, vui lòng sử dụng tính năng "Gửi lại OTP".
            </i>
        </p>
        <p>Trân trọng!</p>
    </div>
</body>
</html>
