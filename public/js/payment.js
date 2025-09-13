(() => {
  const methodModal = document.getElementById('methodModal');
  const codeModal   = document.getElementById('codeModal');
  const errorModal  = document.getElementById('errorModal');

  const chooseBtn   = document.getElementById('chooseMethodBtn');
  const confirmBtn  = document.getElementById('confirmMethodBtn');
  const primaryBtn  = document.getElementById('primaryActionBtn');
  const agreeTerms  = document.getElementById('agreeTerms');

  const methodPreview = document.getElementById('methodPreview');
  const codeTitle     = document.getElementById('codeTitle');
  const codeMeta      = document.getElementById('codeMeta');
  const codeValue     = document.getElementById('codeValue');
  const copyBtn       = document.getElementById('copyCodeBtn');
  const finishBtn     = document.getElementById('finishBtn');
  const errorMessage  = document.getElementById('errorMessage');
  const finalizeForm  = document.getElementById('finalizeForm');

  let selectedMethod = null;

  /** ---------------- Modal helpers ---------------- */
  const openModal  = el => el.setAttribute('aria-hidden', 'false');
  const closeModal = el => el.setAttribute('aria-hidden', 'true');

  document.querySelectorAll('[data-close]').forEach(btn =>
    btn.addEventListener('click', () => {
      closeModal(methodModal);
      closeModal(codeModal);
      closeModal(errorModal);
    })
  );

  /** ---------------- Parse & Label Payment ---------------- */
  const parseMethod = val => {
    const [type, provider] = val.split(':');
    return { type, provider };
  };

  const methodLabel = ({ type, provider }) => {
    if (type === 'saldo') return 'Saldo PawMart';
    if (type === 'ewallet') return `E-Wallet • ${provider}`;
    if (type === 'bank') return `Transfer Bank • ${provider}`;
    return 'Metode Pembayaran';
  };

  /** ---------------- Generate Payment Code ---------------- */
  const generateCode = ({ type, provider }) => {
    const randDigits = n => Array.from({ length: n }, () => Math.floor(Math.random() * 10)).join('');
    if (type === 'saldo') return 'SAL-' + randDigits(8);
    if (type === 'ewallet') return provider.toUpperCase().slice(0, 2) + '-' + randDigits(10);
    const prefix = provider === 'BCA' ? '3901' : (provider === 'BRI' ? '2626' : '8888');
    return prefix + randDigits(12);
  };

  /** ---------------- Update Checkout Preview ---------------- */
  const updatePreview = () => {
    if (!selectedMethod) {
      methodPreview.querySelector('.method-title').textContent = 'Metode belum dipilih';
      methodPreview.querySelector('.method-sub').textContent = 'Silakan pilih metode pembayaran';
      primaryBtn.textContent = 'Pilih Metode';
      return;
    }
    methodPreview.querySelector('.method-title').textContent = methodLabel(selectedMethod);
    methodPreview.querySelector('.method-sub').textContent = 'Siap untuk membuat pesanan';
    primaryBtn.textContent = `Buat Pesanan • ${window.PAWMART.totalText}`;
  };

  /** ---------------- Open Modals ---------------- */
  chooseBtn.addEventListener('click', () => openModal(methodModal));
  methodPreview.addEventListener('click', e => {
    if (e.target.id !== 'chooseMethodBtn') openModal(methodModal);
  });

  /** ---------------- Confirm Payment Method ---------------- */
  confirmBtn.addEventListener('click', () => {
    const checked = document.querySelector('input[name="payMethod"]:checked');
    if (!checked) {
      errorMessage.textContent = 'Pilih salah satu metode pembayaran.';
      openModal(errorModal);
      return;
    }
    selectedMethod = parseMethod(checked.value);
    updatePreview();
    closeModal(methodModal);
  });

  /** ---------------- Main Checkout Action ---------------- */
  primaryBtn.addEventListener('click', () => {
    if (!selectedMethod) {
      openModal(methodModal);
      return;
    }

    if (!agreeTerms.checked) {
      errorMessage.textContent = 'Mohon centang persetujuan syarat & ketentuan.';
      openModal(errorModal);
      return;
    }

    // Generate payment code
    const code = generateCode(selectedMethod);
    codeTitle.textContent = selectedMethod.type === 'saldo' ? 'Kode Konfirmasi Saldo' :
                            selectedMethod.type === 'ewallet' ? 'Kode Pembayaran E-Wallet' :
                            'Virtual Account';
    codeMeta.textContent  = methodLabel(selectedMethod);
    codeValue.textContent = code;
    document.getElementById('codeHelp').textContent =
      selectedMethod.type === 'bank' ? 'Gunakan kode VA di aplikasi bank sesuai metode yang dipilih.' :
      selectedMethod.type === 'ewallet' ? 'Salin kode lalu bayar melalui aplikasi e-wallet Anda.' :
      'Pembayaran menggunakan saldo PawMart akan dipotong otomatis.';

    // Show code modal
    openModal(codeModal);
  });

  /** ---------------- Finalize Transaction via AJAX ---------------- */
 finishBtn.addEventListener('click', async e => {
  e.preventDefault();
  closeModal(codeModal);

  try {
    const formData = new FormData(finalizeForm);
    formData.set('metode_transaksi', `${selectedMethod.type}:${selectedMethod.provider}`);

    const res = await fetch(finalizeForm.action, {
      method: 'POST',
      headers: { 'X-CSRF-TOKEN': window.PAWMART.csrf },
      body: formData
    });

    const data = await res.json();

    if (!data.success) {
      throw new Error(data.message || 'Pesanan gagal dibuat.');
    }

    if (data.redirect_url) {
      window.location.href = data.redirect_url;
    } else {
      alert(data.message || 'Pesanan berhasil dibuat!');
    }
  } catch (err) {
    errorMessage.textContent = err.message;
    openModal(errorModal);
  }
});


  /** ---------------- Copy Payment Code ---------------- */
  copyBtn.addEventListener('click', async () => {
    try {
      await navigator.clipboard.writeText(codeValue.textContent.trim());
      copyBtn.innerHTML = "<i class='bx bx-check'></i> Disalin";
      setTimeout(() => copyBtn.innerHTML = "<i class='bx bx-copy'></i> Salin", 1500);
    } catch {
      errorMessage.textContent = 'Gagal menyalin. Salin manual: ' + codeValue.textContent;
      openModal(errorModal);
    }
  });

  // Initial state
  updatePreview();
})();
