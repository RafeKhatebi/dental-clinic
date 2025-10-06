// Keyboard Shortcuts
document.addEventListener("keydown", (e) => {
  if ((e.ctrlKey || e.metaKey) && e.key === "k") {
    e.preventDefault();
    const searchInput = document.querySelector(
      'input[type="search"], input[name="search"]'
    );
    if (searchInput) searchInput.focus();
  }

  if ((e.ctrlKey || e.metaKey) && e.key === "n") {
    e.preventDefault();
    const addBtn = document.querySelector('a[href*="add.php"]');
    if (addBtn) window.location.href = addBtn.href;
  }

  if ((e.ctrlKey || e.metaKey) && e.key === "s") {
    e.preventDefault();
    const form = document.querySelector("form");
    if (form) form.requestSubmit();
  }

  if ((e.ctrlKey || e.metaKey) && e.key === "p") {
    e.preventDefault();
    window.print();
  }

  if (e.key === "Escape") {
    const modal = document.querySelector(".confirm-dialog.active");
    if (modal) modal.classList.remove("active");
    const userMenu = document.getElementById("user-menu");
    if (userMenu && !userMenu.classList.contains("hidden"))
      userMenu.classList.add("hidden");
  }

  if ((e.ctrlKey || e.metaKey) && e.key === "/") {
    e.preventDefault();
    showShortcutsHelp();
  }
});

function showShortcutsHelp() {
  const shortcuts = [
    { key: "Ctrl+K", desc: "جستجو سریع / Quick Search" },
    { key: "Ctrl+N", desc: "رکورد جدید / New Record" },
    { key: "Ctrl+S", desc: "ذخیره فرم / Save Form" },
    { key: "Ctrl+P", desc: "چاپ / Print" },
    { key: "Esc", desc: "بستن / Close" },
    { key: "Ctrl+/", desc: "راهنمای کلیدها / Shortcuts Help" },
  ];

  const html = `
        <div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 24px; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); z-index: 10001; max-width: 500px;">
            <h3 style="font-size: 18px; font-weight: bold; margin-bottom: 16px;">میانبرهای کیبورد / Keyboard Shortcuts</h3>
            <div>
                ${shortcuts
                  .map(
                    (s) => `
                    <div style="display: flex; justify-content: space-between; padding: 8px; background: #f9fafb; border-radius: 6px; margin-bottom: 8px;">
                        <kbd style="background: #374151; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">${s.key}</kbd>
                        <span style="color: #6b7280; font-size: 14px;">${s.desc}</span>
                    </div>
                `
                  )
                  .join("")}
            </div>
            <button onclick="this.parentElement.remove(); document.querySelector('.shortcuts-overlay').remove();" style="margin-top: 16px; width: 100%; background: #3b82f6; color: white; padding: 8px; border-radius: 6px; border: none; cursor: pointer;">بستن / Close</button>
        </div>
        <div class="shortcuts-overlay" onclick="this.previousElementSibling.remove(); this.remove();" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 10000;"></div>
    `;

  const div = document.createElement("div");
  div.innerHTML = html;
  document.body.appendChild(div);
}

window.exportToPDF = function () {
  window.print();
};

window.exportToExcel = function (tableId, filename = "export") {
  const table =
    document.getElementById(tableId) || document.querySelector("table");
  if (!table) {
    showToast("جدولی برای خروجی یافت نشد", "error");
    return;
  }

  let csv = [];
  const rows = table.querySelectorAll("tr");

  rows.forEach((row) => {
    const cols = row.querySelectorAll("td, th");
    const csvRow = [];
    cols.forEach((col) => csvRow.push(col.innerText));
    csv.push(csvRow.join(","));
  });

  const csvContent = csv.join("\n");
  const blob = new Blob(["\ufeff" + csvContent], {
    type: "text/csv;charset=utf-8;",
  });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = filename + ".csv";
  link.click();

  showToast("فایل با موفقیت دانلود شد", "success");
};
