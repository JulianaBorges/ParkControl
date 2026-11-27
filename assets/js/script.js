document.addEventListener('DOMContentLoaded', () => {
    // Filtro local da tabela de veículos estacionados.
    const parkedSearch = document.getElementById('parkedSearch');
    if (parkedSearch) {
        setupTableSearch(parkedSearch, 'parkedTable', 'parkedEmpty');
    }

    // Filtro local da tabela de histórico.
    const historySearch = document.getElementById('historySearch');
    if (historySearch) {
        setupTableSearch(historySearch, 'historyTable', 'historyEmpty');
    }

    // Abre/fecha o menu lateral em telas pequenas.
    const mobileMenu = document.getElementById('mobileMenu');
    const sidebar = document.getElementById('sidebar');
    if (mobileMenu && sidebar) {
        mobileMenu.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }
});

function setupTableSearch(input, tableId, emptyId) {
    const tableBody = document.getElementById(tableId);
    const emptyState = document.getElementById(emptyId);
    if (!tableBody || !emptyState) return;

    input.addEventListener('input', () => {
        const term = input.value.trim().toLowerCase();
        const rows = Array.from(tableBody.querySelectorAll('tr'));
        let visibleCount = 0;

        rows.forEach((row) => {
            const plate = row.dataset.plate || '';
            const visible = plate.includes(term);
            row.hidden = !visible;
            if (visible) visibleCount++;
        });

        emptyState.hidden = visibleCount !== 0;
    });
}
