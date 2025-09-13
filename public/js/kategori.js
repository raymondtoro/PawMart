// ==========================
// kategori.js
// ==========================

// ===== Utility =====
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
  document.querySelectorAll('.modal').forEach(m => m.style.display = 'none');
  document.body.style.overflow = '';
}

// ===== Tambah Modal =====
document.getElementById('openTambahModal')?.addEventListener('click', () => openModal('tambahModal'));
document.getElementById('closeTambahModal')?.addEventListener('click', () => closeModal('tambahModal'));

// ===== Edit Modal =====
const editModal = document.getElementById('editModal');
const editForm = document.getElementById('editForm');
const editKategoriInput = document.getElementById('editKategoriInput');

document.querySelectorAll('.icon-btn.edit').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const row = e.target.closest('tr');
    const id = row.dataset.id;
    const nama = row.querySelector('.nama-kategori').textContent;
    editKategoriInput.value = nama;
    editForm.action = `${CATEGORY_BASE_URL}/${id}`;
    openModal(editModal);
  });
});

document.getElementById('closeEditModal')?.addEventListener('click', () => closeModal(editModal));

// ===== Delete Modal =====
const deleteModal = document.getElementById('deleteModal');
const deleteForm = document.getElementById('deleteForm');

document.querySelectorAll('.icon-btn.delete').forEach(btn => {
  btn.addEventListener('click', (e) => {
    const row = e.target.closest('tr');
    const id = row.dataset.id;
    deleteForm.action = `${CATEGORY_BASE_URL}/${id}`;
    openModal(deleteModal);
  });
});

document.getElementById('closeDeleteModal')?.addEventListener('click', () => closeModal(deleteModal));

// ===== Success Modal =====
const successModal = document.getElementById('successModal');
const successMessage = document.getElementById('successMessage');

function showSuccess(message) {
  if (!successModal || !successMessage) return;
  closeAllModals();
  successMessage.textContent = message;
  openModal(successModal);
  setTimeout(() => closeModal(successModal), 2000);
}

// ===== Handle Laravel session success (on page reload) =====
if (CATEGORY_SUCCESS) {
  window.addEventListener('load', () => showSuccess(CATEGORY_SUCCESS));
}

// 🚨 REMOVE submit event handlers — they cause double modal 🚨

// ===== Close when clicking outside =====
window.addEventListener('click', (e) => {
  [editModal, deleteModal, document.getElementById('tambahModal'), successModal].forEach(modal => {
    if (modal && e.target === modal) closeModal(modal);
  });
});
