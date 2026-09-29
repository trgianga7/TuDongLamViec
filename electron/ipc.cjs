const { ipcMain, BrowserWindow, shell } = require("electron");
const { spawn } = require("child_process");
const path = require("path");
const fs = require("fs");

let running = false;

// ===== RUN DUSK =====
try { ipcMain.removeHandler("run-dusk"); } catch {}

ipcMain.handle("run-dusk", async (_, action) => {

    if (running) {
        const win = BrowserWindow.getAllWindows()[0];
        if (win && !win.isDestroyed()) {
            win.webContents.send(
                "dusk-log",
                "⚠ Đang có tác vụ khác chạy.\n"
            );
        }
        return false;
    }

    running = true;

    const project = path.join(__dirname, "..");

    const args =
        action === "LoginSession"
            ? ["artisan", "dusk", "--filter=LoginTest"]
            : ["artisan", "dusk", "--filter=ExampleTest"];

    const php = spawn("php", args, {
        cwd: project,
        shell: true,
        env: {
            ...process.env,
            DUSK_ACTION: action
        },
        stdio: ["ignore", "pipe", "pipe"]
    });

    const send = (text) => {
        const win = BrowserWindow.getAllWindows()[0];

        if (win && !win.isDestroyed()) {
            win.webContents.send("dusk-log", text);
        }
    };

    php.stdout.setEncoding("utf8");
    php.stderr.setEncoding("utf8");

    php.stdout.on("data", send);
    php.stderr.on("data", send);

    php.on("close", (code) => {

        running = false;

        if (code === 0) {
            send("\n✅ Hoàn thành\n");
        } else {
            send(`\n❌ Kết thúc với mã lỗi ${code}\n`);
        }
    });

    php.on("error", (err) => {

        running = false;

        send(`\n❌ LỖI KHỞI CHẠY: ${err.message}\n`);
    });

    return true;
});

// ===== CHECK SESSION =====
try { ipcMain.removeHandler("check-session"); } catch {}

ipcMain.handle("check-session", async () => {

    const file = path.join(
        __dirname,
        "..",
        "tests",
        "Browser",
        "session.json"
    );

    if (!fs.existsSync(file)) {
        return { logged_in: false };
    }

    try {
        return JSON.parse(fs.readFileSync(file, "utf8"));
    } catch {
        return { logged_in: false };
    }
});

// ===== OPEN EXCEL =====
try { ipcMain.removeHandler("open-excel"); } catch {}

ipcMain.handle("open-excel", async (_, file) => {

    const full = path.join(
        __dirname,
        "..",
        "tests",
        "Browser",
        "Excel",
        file
    );

    const result = await shell.openPath(full);

    return result === "";
});