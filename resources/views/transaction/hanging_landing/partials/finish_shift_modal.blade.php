<div id="finishShiftModal" class="lst-modal" style="display:none">
  <div class="lst-modal-content">
    <div class="lst-modal-header">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#E85D2F" stroke-width="2">
        <path d="M12 9v2M12 15h.01M3 12a9 9 0 1 0 18 0 9 9 0 0 0-18 0z"/>
      </svg>
      <h3>Konfirmasi Selesai Shift</h3>
    </div>
    <div class="lst-modal-body">
      <p><strong>⚠️ Peringatan Penting!</strong></p>
      <p>Anda akan menyelesaikan shift <strong id="modal-shift"></strong> di lokasi <strong id="modal-location"></strong>.</p>
      <p>Target planning belum terpenuhi. Tindakan ini akan:</p>
      <ul>
        <li>Menandai shift ini sebagai SELESAI</li>
        <li>Antrian yang belum diproses akan diabaikan</li>
        <li>Data tidak dapat diubah kembali</li>
      </ul>
      <div class="lst-modal-warning">
        <strong>Pastikan:</strong>
        <ol>
          <li>Semua proses yang sudah jalan sudah selesai</li>
          <li>Anda memiliki wewenang untuk melakukan ini</li>
          <li>Manajemen sudah menyetujui</li>
        </ol>
      </div>
      
      <div class="lst-modal-input">
        <label>Catatan (opsional):</label>
        <textarea id="finish-notes" rows="3" placeholder="Alasan shift difinish..."></textarea>
      </div>
      
      <div class="lst-modal-confirm">
        <input type="text" id="confirm-text" placeholder='Ketik "SELESAI" untuk melanjutkan'>
      </div>
    </div>
    <div class="lst-modal-footer">
      <button type="button" class="lst-modal-cancel" onclick="closeFinishShiftModal()">Batal</button>
      <button type="button" class="lst-modal-submit" onclick="submitFinishShift()" disabled id="submit-finish-btn">
        Ya, Finish Shift
      </button>
    </div>
  </div>
</div>

<script>
// Gunakan window object untuk menghindari double declaration
if (typeof window.finishShiftData === 'undefined') {
  window.finishShiftData = null;
}

window.confirmFinishShift = function(location, shift, date) {
  window.finishShiftData = { location, shift, date };
  document.getElementById('modal-shift').textContent = shift;
  document.getElementById('modal-location').textContent = location;
  document.getElementById('finishShiftModal').style.display = 'flex';
  
  // Reset input
  const confirmInput = document.getElementById('confirm-text');
  const notesInput = document.getElementById('finish-notes');
  const submitBtn = document.getElementById('submit-finish-btn');
  
  if (confirmInput) confirmInput.value = '';
  if (notesInput) notesInput.value = '';
  if (submitBtn) submitBtn.disabled = true;
  
  // Add event listener for confirmation text
  if (confirmInput) {
    const handleConfirm = () => {
      if (submitBtn) submitBtn.disabled = confirmInput.value !== 'SELESAI';
    };
    confirmInput.oninput = handleConfirm;
  }
};

window.closeFinishShiftModal = function() {
  document.getElementById('finishShiftModal').style.display = 'none';
  window.finishShiftData = null;
};

window.submitFinishShift = function() {
  if (!window.finishShiftData) return;
  
  const notes = document.getElementById('finish-notes')?.value || '';
  const form = document.createElement('form');
  form.method = 'POST';
  form.action = `{{ url('hanging/finish-shift') }}/${window.finishShiftData.location}/${window.finishShiftData.shift}/${window.finishShiftData.date}`;
  
  const csrf = document.createElement('input');
  csrf.type = 'hidden';
  csrf.name = '_token';
  csrf.value = '{{ csrf_token() }}';
  form.appendChild(csrf);
  
  const notesInput = document.createElement('input');
  notesInput.type = 'hidden';
  notesInput.name = 'notes';
  notesInput.value = notes;
  form.appendChild(notesInput);
  
  document.body.appendChild(form);
  form.submit();
};

// Close modal on background click
document.addEventListener('DOMContentLoaded', function() {
  const modal = document.getElementById('finishShiftModal');
  if (modal) {
    modal.addEventListener('click', function(e) {
      if (e.target === this) window.closeFinishShiftModal();
    });
  }
});
</script>

<style>
/* Finish Shift Button - tambahkan jika belum ada */
.lst-finish-shift-wrap {
  margin-top: 20px;
  padding-top: 16px;
  border-top: 2px dashed #E85D2F;
  text-align: center;
}

.lst-btn-finish-shift {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: linear-gradient(135deg, #E85D2F 0%, #C0392B 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 0.9rem;
  font-weight: 800;
  cursor: pointer;
  transition: all 0.2s;
  box-shadow: 0 4px 12px rgba(232,93,47,0.3);
}

.lst-btn-finish-shift:hover {
  transform: translateY(-2px);
  filter: brightness(1.05);
  box-shadow: 0 6px 16px rgba(232,93,47,0.4);
}

/* Modal Styles */
.lst-modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.lst-modal-content {
  background: white;
  border-radius: 16px;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 20px 35px rgba(0,0,0,0.2);
}

.lst-modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid #E2E5EE;
  display: flex;
  align-items: center;
  gap: 12px;
}

.lst-modal-header h3 {
  margin: 0;
  color: #E85D2F;
  font-size: 1.25rem;
}

.lst-modal-body {
  padding: 20px 24px;
}

.lst-modal-body p {
  margin: 0 0 12px 0;
}

.lst-modal-body ul, .lst-modal-body ol {
  margin: 12px 0;
  padding-left: 20px;
}

.lst-modal-body li {
  margin: 6px 0;
  color: #4B5563;
}

.lst-modal-warning {
  background: #FEF3C7;
  border-left: 4px solid #F59F00;
  padding: 12px 16px;
  margin: 16px 0;
  border-radius: 8px;
}

.lst-modal-input {
  margin: 16px 0;
}

.lst-modal-input textarea {
  width: 100%;
  padding: 10px;
  border: 1.5px solid #E2E5EE;
  border-radius: 8px;
  font-family: inherit;
  font-size: 0.875rem;
  resize: vertical;
}

.lst-modal-confirm {
  margin: 16px 0;
}

.lst-modal-confirm input {
  width: 100%;
  padding: 10px;
  border: 2px solid #E2E5EE;
  border-radius: 8px;
  font-family: monospace;
  font-size: 0.875rem;
  text-align: center;
}

.lst-modal-footer {
  padding: 16px 24px;
  border-top: 1px solid #E2E5EE;
  display: flex;
  gap: 12px;
  justify-content: flex-end;
}

.lst-modal-cancel, .lst-modal-submit {
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: 700;
  cursor: pointer;
  border: none;
  font-size: 0.875rem;
}

.lst-modal-cancel {
  background: #F3F4F6;
  color: #4B5563;
}

.lst-modal-submit {
  background: #E85D2F;
  color: white;
}

.lst-modal-submit:disabled {
  background: #D1D5DB;
  cursor: not-allowed;
}

.lst-modal-submit:not(:disabled):hover {
  filter: brightness(0.95);
  transform: translateY(-1px);
}
</style>