<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dusk Menu</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container" style="margin-top:40px">

    <h3>Điều khiển Dusk</h3>

    <div class="form-group">
        <label>Chọn chức năng</label>

        <select id="job" class="form-control">
            <option value="tailieu">Thêm tài liệu biểu quyết</option>
            <option value="ketqua">Thêm kết quả giám sát</option>
        </select>
    </div>

    <button id="run" class="btn btn-success">
        Chạy
    </button>

    <hr>

    <pre id="log"
         style="height:500px;overflow:auto;background:#111;color:#0f0;padding:15px"></pre>

</div>

<script src="/js/ThongBaoDusk.js"></script>

</body>
</html>