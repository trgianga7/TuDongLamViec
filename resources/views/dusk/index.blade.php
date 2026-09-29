<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tool Tự Động Làm Việc</title>
    @vite('resources/css/index.css')
    <meta name="viewport" content="width=device-width, initial-scale=1">

</head>

<body>

<div class="container">

    <div class="tabs">
        <button id="tab-tool" class="tab active" onclick="switchTab('tool')">
            🛠 Chức năng
        </button>

        <button id="tab-excel" class="tab" onclick="switchTab('excel')">
            📄 Excel
        </button>
    </div>

    <!-- ================= TOOL PAGE ================= -->

    <div id="tool-page">

        <div class="layout">

            <div class="panel">

                <h2>Danh sách chức năng</h2>

                <div id="session-status" class="status">
                    <div class="dot"></div>
                    <div>Đang kiểm tra session...</div>
                </div>

                <div class="search-box">
                    <input type="text" id="search-action"
                        placeholder="Tìm chức năng..."
                        oninput="filterActions()">
                </div>

                <div id="action-list" class="grid"></div>

            </div>

            <div class="panel">

                <h2>Realtime Log</h2>

                <div id="log" class="terminal">Tool đã sẵn sàng...
</div>

            </div>

        </div>

    </div>

    <!-- ================= EXCEL PAGE ================= -->

    <div id="excel-page" style="display:none">

        <div class="panel">

            <h2>Danh sách file Excel</h2>
            
            <div class="search-box">
                <input type="text"
                    id="search-excel"
                    placeholder="Tìm file Excel..."
                    oninput="filterExcel()">
            </div>

            <div id="excel-list" class="excel-grid"></div>

        </div>

    </div>

</div>

@vite('resources/js/index.js')

</body>
</html>