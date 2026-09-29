const { contextBridge, ipcRenderer } = require("electron");

contextBridge.exposeInMainWorld("electron", {
    runAction: (action) => ipcRenderer.invoke("run-dusk", action),
    onLog: (callback) => ipcRenderer.on("dusk-log", (_, msg) => callback(msg)),
    checkSession: () => ipcRenderer.invoke("check-session"),
    openExcel: (file) => ipcRenderer.invoke("open-excel", file)
});

