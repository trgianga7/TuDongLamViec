const { app, BrowserWindow } = require("electron");
const path = require("path");

require("./ipc.cjs");

function createWindow() {
    const win = new BrowserWindow({
        width: 1500,
        height: 950,
        webPreferences: {
            preload: path.join(__dirname, "preload.cjs"),
            contextIsolation: true,
            nodeIntegration: false
        }
    });

    win.loadURL("http://127.0.0.1:8000/dusk");
}

app.whenReady().then(createWindow);

app.on("window-all-closed", () => {
    if (process.platform !== "darwin") app.quit();
});