document.addEventListener('DOMContentLoaded', function() {
    
    // === ELEMEN UTAMA ===
    const modalBackdrop = document.getElementById('modal-backdrop');
    const modalContainer = document.getElementById('modal-container');
    const btnTambah = document.getElementById('btn-tambah');
    const resultsContainer = document.getElementById('results-container');
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    const toast = document.getElementById('toast-notification');

    // === FUNGSI HELPER ===
    function showToast(message, type = 'success') {
        toast.textContent = message;
        toast.className = 'toast'; 
        toast.classList.add(type);
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // --- FUNGSI MODAL (DIMODIFIKASI) ---
    function openModal(htmlContent) {
        modalContainer.innerHTML = htmlContent;
        modalBackdrop.classList.add('is-visible'); 
        modalContainer.classList.add('is-visible'); 
        
        const closeModalBtn = modalContainer.querySelector('.modal-close-btn');
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeModal);
        }

        // === INI PERBAIKANNYA ===
        // Cari form di dalam modal yang baru kita buat
        const formTambah = modalContainer.querySelector('#form-tambah');
        if (formTambah) {
            // Jika ada form-tambah, pasang listener handleTambahSubmit
            formTambah.addEventListener('submit', handleTambahSubmit);
        }

        const formEdit = modalContainer.querySelector('#form-edit');
        if (formEdit) {
            // Jika ada form-edit, pasang listener handleEditSubmit
            formEdit.addEventListener('submit', handleEditSubmit);
        }
    }

    function closeModal() {
        modalBackdrop.classList.remove('is-visible'); 
        modalContainer.classList.remove('is-visible'); 
        setTimeout(() => {
            modalContainer.innerHTML = ''; 
        }, 300); 
    }

    async function refreshTable() {
        resultsContainer.innerHTML = '<tr><td colspan="4" style="text-align: center; padding: 1rem;">Memuat ulang data...</td></tr>';
        try {
            const keyword = searchInput.value;
            const response = await fetch('live_search.php?keyword=' + encodeURIComponent(keyword));
            if (!response.ok) throw new Error('Gagal mengambil data');
            const html = await response.text();
            resultsContainer.innerHTML = html;
        } catch (error) {
            console.error('Error refreshing table:', error);
            resultsContainer.innerHTML = '<tr><td colspan="4" style="text-align: center; color: red;">Gagal memuat ulang data.</td></tr>';
        }
    }

    // --- 3. PROSES SUBMIT TAMBAH (FUNGSI BARU) ---
    async function handleTambahSubmit(e) {
        e.preventDefault(); // Mencegah submit standar
        const form = e.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Menyimpan...';

        try {
            const response = await fetch('tambah.php', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.status === 'success') {
                showToast(result.message, 'success');
                closeModal();
                await refreshTable();
            } else {
                showToast(result.message, 'error');
            }
        } catch (error) {
            showToast('Terjadi kesalahan koneksi.', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Simpan';
        }
    }

    // --- 4. PROSES SUBMIT EDIT (FUNGSI BARU) ---
    async function handleEditSubmit(e) {
        e.preventDefault(); // Mencegah submit standar
        const form = e.target;
        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Memperbarui...';

        try {
            const response = await fetch('edit.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.status === 'success') {
                showToast(result.message, 'success');
                closeModal();
                await refreshTable();
            } else {
                showToast(result.message, 'error');
                console.error('Server Error:', result.message); // Tampilkan error di console
            }
        } catch (error) {
            showToast('Terjadi kesalahan koneksi.', 'error');
            console.error('Error saat fetch edit:', error); 
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Perbarui';
        }
    }

    // === EVENT LISTENERS ===

    // --- 1. LIVE SEARCH ---
    searchForm.addEventListener('submit', e => e.preventDefault());
    searchInput.addEventListener('keyup', () => {
        refreshTable();
    });

    // --- 2. BUKA MODAL TAMBAH ---
    btnTambah.addEventListener('click', function(e) {
        e.preventDefault();
        const modalHTML = `
            <div class="modal-header">
                <h2>Tambah Istilah Baru</h2>
                <button class="modal-close-btn">&times;</button>
            </div>
            <form id="form-tambah">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="istilah">Istilah</label>
                        <input type="text" id="istilah" name="istilah" required>
                    </div>
                    <div class="form-group">
                        <label for="definisi">Definisi</label>
                        <textarea id="definisi" name="definisi" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        `;
        openModal(modalHTML);
    });

    // --- 3 & 4. PROSES SUBMIT (DIHAPUS) ---
    // Logika submit sekarang ada di handleTambahSubmit dan handleEditSubmit
    // yang dipanggil dari dalam function openModal()

    // --- 5. BUKA MODAL EDIT & HAPUS ---
    resultsContainer.addEventListener('click', async function(e) {
        
        // --- BUKA MODAL EDIT ---
        if (e.target.classList.contains('btn-edit')) {
            const id = e.target.dataset.id;
            openModal(`
                <div class="modal-header"><h2>Memuat Data...</h2></div>
                <div class="modal-body"><p style="text-align:center;">Mengambil data istilah...</p></div>
            `);
            
            try {
                const response = await fetch('edit.php?id=' + id);
                const result = await response.json();
                
                if (result.status === 'success') {
                    const data = result.data;
                    const modalHTML = `
                        <div class="modal-header">
                            <h2>Edit Istilah</h2>
                            <button class="modal-close-btn">&times;</button>
                        </div>
                        <form id="form-edit">
                            <input type="hidden" name="id" value="${data.id}">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="istilah">Istilah</label>
                                    <input type="text" id="istilah" name="istilah" value="${data.istilah}" required>
                                </div>
                                <div class="form-group">
                                    <label for="definisi">Definisi</label>
                                    <textarea id="definisi" name="definisi" required>${data.definisi}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Perbarui</button>
                            </div>
                        </form>
                    `;
                    openModal(modalHTML);
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                showToast(error.message || 'Gagal memuat data.', 'error');
                closeModal();
            }
        }

        // --- BUKA MODAL KONFIRMASI HAPUS ---
        if (e.target.classList.contains('btn-hapus')) {
            const id = e.target.dataset.id;
            const istilah = e.target.closest('tr').querySelector('td[data-label="Istilah"]').textContent;

            const modalHTML = `
                <div class="modal-header">
                    <h2>Konfirmasi Hapus</h2>
                    <button class="modal-close-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Anda yakin ingin menghapus istilah <strong>"${istilah}"</strong>?</p>
                    <p>Tindakan ini tidak dapat dibatalkan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="btn-konfirmasi-hapus" data-id="${id}" class="btn btn-danger">Ya, Hapus</button>
                </div>
            `;
            openModal(modalHTML);
        }
    });

    // --- 6. PROSES HAPUS ---
    modalContainer.addEventListener('click', async function(e) {
        if (e.target.id === 'btn-konfirmasi-hapus') {
            const id = e.target.dataset.id;
            const submitButton = e.target;
            submitButton.disabled = true;
            submitButton.textContent = 'Menghapus...';

            try {
                const response = await fetch('hapus.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'id=' + encodeURIComponent(id)
                });
                const result = await response.json();
                if (result.status === 'success') {
                    showToast(result.message, 'success');
                    closeModal();
                    await refreshTable();
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan koneksi.', 'error');
            }
        }
    });

    // --- 7. TUTUP MODAL SAAT KLIK BACKDROP ---
    modalBackdrop.addEventListener('click', closeModal);

});