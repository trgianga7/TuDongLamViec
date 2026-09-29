let isRunning = false;

const actions = [

    {title:"🔐 Đăng nhập Session",desc:"Đăng nhập và lưu Session Dusk",action:"LoginSession",login:true},

    {title:"Thêm Người Dùng",desc:"Import người dùng",action:"ThemNguoiDung"},
    {title:"Thêm Khóa Họp",desc:"Import khóa họp",action:"ThemKhoaHop"},
    {title:"Thêm Kỳ Họp",desc:"Import kỳ họp",action:"ThemKyHop"},
    {title:"Thêm Danh Mục File",desc:"Import danh mục file",action:"ThemDanhMucFile"},
    {title:"Thêm Đơn Vị",desc:"Import đơn vị",action:"ThemDonVi"},
    {title:"Thêm Phòng Họp",desc:"Import phòng họp",action:"ThemPhongHop"},
    {title:"Thêm Giám Sát",desc:"Import giám sát",action:"ThemGiamSat"},
    {title:"Điểm danh",desc:"Tự động điểm danh",action:"TuDongDiemDanh"},
    {title:"Thêm Nội Dung GS",desc:"Import nội dung giám sát",action:"ThemNoiDungGiamSat"},
    {title:"Thêm Kết Quả GS",desc:"Import kết quả giám sát",action:"ThemKetQuaGiamSat"},
    {title:"Thêm Tài Liệu",desc:"Import tài liệu biểu quyết",action:"ThemTaiLieuBieuQuyet"},
    {title:"Thêm Phiếu Ý Kiến",desc:"Import phiếu lấy ý kiến",action:"ThemPhieuLayYKien"},
    {title:"Thêm Cuộc Họp Nội Bộ",desc:"Import cuộc họp nội bộ",action:"ThemCuocHopNoiBo"},
    {title:"Thêm Đối Tượng GS",desc:"Import đối tượng giám sát",action:"ThemDoiTuongGiamSat"},
    {title:"Thêm Chủ Thể",desc:"Import chủ thể",action:"ThemChuThe"},
    {title:"Tự Động Điểm Danh",desc:"Điểm danh đại biểu",action:"TuDongDiemDanh"},
    {title:"Thêm Cuộc Họp",desc:"Import cuộc họp",action:"ThemCuocHop"},
    {title:"DM KN - Khóa Họp",desc:"Danh mục kiến nghị khóa họp",action:"ThemDanhMucKienNghi_KhoaHop"},
    {title:"DM KN - Kỳ Họp",desc:"Danh mục kiến nghị kỳ họp",action:"ThemDanhMucKienNghi_KyHop"},
    {title:"DM KN - Lĩnh Vực",desc:"Danh mục kiến nghị lĩnh vực",action:"ThemDanhMucKienNghi_LinhVuc"},
    {title:"Nghị Quyết Ban Hành",desc:"Import nghị quyết ban hành",action:"ThemNghiQuyetBanHanh"},
    {title:"Kiến nghị cử tri",desc:"Import kiến nghị cử tri",action:"ThemKienNghiCuTri"},
    {title:"Đơn thư khiếu nại",desc:"Import đơn thư khiếu nại",action:"ThemDonThuKhieuNai"},
    {title:"Lịch trình cuộc họp",desc:"Import lịch trình cuộc họp",action:"ThemLichTrinhCuocHop"},
    {title:"Biểu quyết hộ",desc:"Biểu quyết hộ",action:"BieuQuyetHo"},
    {title:"Thêm tiếp công dân",desc:"Thêm tiếp công dân định kỳ hoặc đột xuất",action:"ThemTiepCongDan"},
];

const excels = [
    "NguoiDung.xlsx",
    "KhoaHop.xlsx",
    "KyHop.xlsx",
    "DanhMucFile.xlsx",
    "DonVi.xlsx",
    "PhongHop.xlsx",
    "GiamSat.xlsx",
    "DiemDanh.xlsx",
    "NoiDungGiamSat.xlsx",
    "KetQuaGiamSat.xlsx",
    "TaiLieuBieuQuyet.xlsx",
    "PhieuLayYKien.xlsx",
    "CuocHopNoiBo.xlsx",
    "DoiTuongGiamSat.xlsx",
    "ChuThe.xlsx",
    "CuocHop.xlsx",
    "DanhMucKienNghi_KhoaHop.xlsx",
    "DanhMucKienNghi_KyHop.xlsx",
    "DanhMucKienNghi_LinhVuc.xlsx",
    "NghiQuyetBanHanh.xlsx",
    "KienNghiCuTri.xlsx",
    "DonThuKhieuNai.xlsx",
    "LichTrinhCuocHop.xlsx",
    "BieuQuyetHo.xlsx",
    "TiepCongDan.xlsx"
];

const actionList=document.getElementById("action-list");
const excelList=document.getElementById("excel-list");
const log=document.getElementById("log");
const sessionBox=document.getElementById("session-status");

// Render Action
actions.forEach(item=>{

    const card=document.createElement("div");
    card.className="card";

    card.dataset.title = (item.title + " " + item.desc).toLowerCase();

    card.innerHTML=`
        <h3>${item.title}</h3>
        <p>${item.desc}</p>
        <button class="${item.login?'login-btn':''}"
            onclick="run('${item.action}')">
            ${item.login?'Đăng nhập':'Chạy'}
        </button>
    `;

    actionList.appendChild(card);
});

// Render Excel
excels.forEach(file=>{

    const card=document.createElement("div");
    card.className="card";

    card.dataset.name = file.toLowerCase();

    card.innerHTML=`
        <h3>📊 ${file}</h3>
        <p>Chỉnh sửa dữ liệu nguồn</p>
        <button onclick="openExcel('${file}')">
            Mở Excel
        </button>
    `;

    excelList.appendChild(card);
});

// Switch Tab
function switchTab(tab){

    document.getElementById("tool-page").style.display =
        tab==="tool" ? "block" : "none";

    document.getElementById("excel-page").style.display =
        tab==="excel" ? "block" : "none";

    document.getElementById("tab-tool").classList.toggle("active",tab==="tool");
    document.getElementById("tab-excel").classList.toggle("active",tab==="excel");
}

// Log
function append(text){

    const lines=text.split(/\r?\n/);

    lines.forEach(line=>{

        if(!line.trim()) return;

        if(
            line.includes("TensorFlow") ||
            line.includes("TTY mode") ||
            line.includes("DevTools listening") ||
            line.startsWith("PHPUnit") ||
            line.startsWith("PASS") ||
            line.startsWith("FAIL") ||
            line.startsWith("✓") ||
            line.startsWith("⨯") ||
            line.trim().startsWith("Tests:") ||
            line.trim().startsWith("Duration:")
        ) return;

        const div=document.createElement("div");

        if(line.startsWith("▶"))
            div.className="cmd";
        else if(line.includes("ĐÃ HOÀN THÀNH") || line.includes("Hoàn thành") || line.includes("✅"))
            div.className="ok";
        else if(line.includes("FAILED") || line.includes("LỖI") || line.includes("❌"))
            div.className="error";
        else if(line.includes("Không có"))
            div.className="warn";
        else
            div.className="info";

        div.textContent=line;
        log.appendChild(div);
    });

    log.scrollTop=log.scrollHeight;
}

// Session
async function refreshSession(){

    if(!window.electron?.checkSession){

        sessionBox.innerHTML=`
            <div class="dot" style="background:#ef4444"></div>
            <div>Electron chưa kết nối</div>
        `;

        return;
    }

    const s=await window.electron.checkSession();

    if(s.logged_in){

        sessionBox.innerHTML=`
            <div class="dot" style="background:#22c55e"></div>
            <div>
                <strong>${s.name || "Đã đăng nhập"}</strong><br>
                <small>${s.role || "N/A"} • ${s.login_date || s.time || ""}</small>
            </div>
        `;

    }else{

        sessionBox.innerHTML=`
            <div class="dot" style="background:#ef4444"></div>
            <div><strong>Chưa đăng nhập</strong></div>
        `;
    }
}

// Open Excel
async function openExcel(file){

    if(!window.electron?.openExcel) return;

    await window.electron.openExcel(file);
}

// IPC
if(window.electron){

    window.electron.onLog(async msg=>{

        append(msg);

        if(msg.includes("Session đã được lưu")){
            await refreshSession();
        }

        if(
            msg.includes("✅ Hoàn thành") ||
            msg.includes("❌ Kết thúc")
        ){

            isRunning=false;

            document.querySelectorAll("button")
                .forEach(btn=>btn.disabled=false);

            await refreshSession();
        }

    });

    refreshSession();
}

// Run
async function run(action){

    if(isRunning) return;

    isRunning=true;

    document.querySelectorAll("button")
        .forEach(btn=>btn.disabled=true);

    log.innerHTML="";

    append("▶ Chạy: "+action);
    append("");

    try{

        await window.electron.runAction(action);

    }catch(e){

        append("❌ "+e.message);

        isRunning=false;

        document.querySelectorAll("button")
            .forEach(btn=>btn.disabled=false);
    }
}

//Tìm kiếm ở main menu
function filterActions(){

    const keyword = document
        .getElementById("search-action")
        .value
        .toLowerCase()
        .trim();

    document.querySelectorAll("#action-list .card")
        .forEach(card=>{

            const match = card.dataset.title.includes(keyword);

            card.style.display = match ? "" : "none";
        });
}

// Tìm kiếm file Excel
function filterExcel(){

    const keyword = document
        .getElementById("search-excel")
        .value
        .toLowerCase()
        .trim();

    document.querySelectorAll("#excel-list .card")
        .forEach(card=>{

            const match = card.dataset.name.includes(keyword);

            card.style.display = match ? "" : "none";
        });
}

window.switchTab = switchTab;
window.run = run;
window.filterActions = filterActions;
window.filterExcel = filterExcel;
window.openExcel = openExcel;