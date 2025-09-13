// ==========================
// promosi.js
// ==========================

// ===== Utility Functions =====
function openModal(modalOrId) {
  const modal = typeof modalOrId === 'string' ? document.getElementById(modalOrId) : modalOrId;
  if (!modal) return;
  modal.style.display = 'flex';
  document.body.style.overflow = 'hidden';
}

function closeModal(modalOrId) {
  const modal = typeof modalOrId === 'string' ? document.getElementById(modalOrId) : modalOrId;
  if (!modal) return;
  modal.style.display = 'none';
  document.body.style.overflow = '';
}

function closeAllModals() {
  document.querySelectorAll('.modal').forEach(m => (m.style.display = 'none'));
  document.body.style.overflow = '';
}

// ===== Tambah =====
document.getElementById('openTambahModal')?.addEventListener('click', () =>
  openModal('tambahModal')
);

// ===== Edit =====
const editModal = document.getElementById('editModal');
const editForm = document.getElementById('editForm');
const editJudulInput = document.getElementById('editJudulInput');
const editDeskripsiInput = document.getElementById('editDeskripsiInput');
const editDiskonInput = document.getElementById('editDiskonInput');

function openEditModal(id, judul, deskripsi = '', diskon = '') {
  editJudulInput.value = judul || '';
  editDeskripsiInput.value = deskripsi || '';
  editDiskonInput.value = diskon || '';
  editForm.action = `${BASE_URL}/${id}`;
  openModal(editModal);
}

// ===== Delete =====
const deleteModal = document.getElementById('deleteModal');
const deleteForm = document.getElementById('deleteForm');

function openDeleteModal(id) {
  deleteForm.action = `${BASE_URL}/${id}`;
  openModal(deleteModal);
}

// ===== Success Modal =====
const successModal = document.getElementById('successModal');
const successMessage = document.getElementById('successMessage');

function showSuccess(message) {
  if (!successModal || !successMessage) return;
  closeAllModals();
  successMessage.textContent = message;
  openModal(successModal);
  setTimeout(() => closeModal(successModal), 2000);
  sessionStorage.setItem('promoSuccessShown', '1');
}

// ===== Handle session success (from Laravel) =====
window.addEventListener('DOMContentLoaded', () => {
  if (PROMO_SUCCESS) {
    // prevent double modal after reload
    if (sessionStorage.getItem('promoSuccessShown') !== '1') {
      showSuccess(PROMO_SUCCESS);
    }
    sessionStorage.removeItem('promoSuccessShown');
  }
});

// ===== Attach form events (for AJAX, optional) =====
document.getElementById('tambahForm')?.addEventListener('submit', () => {
  closeAllModals();
  showSuccess('🎉 Promosi berhasil ditambahkan!');
});

editForm?.addEventListener('submit', () => {
  closeAllModals();
  showSuccess('✏️ Promosi berhasil diperbarui!');
});

deleteForm?.addEventListener('submit', () => {
  closeAllModals();
  showSuccess('🗑️ Promosi berhasil dihapus!');
});

// ===== Close modals when clicking outside =====
window.addEventListener('click', (e) => {
  [editModal, deleteModal, successModal].forEach((modal) => {
    if (modal && e.target === modal) closeModal(modal);
  });
});
