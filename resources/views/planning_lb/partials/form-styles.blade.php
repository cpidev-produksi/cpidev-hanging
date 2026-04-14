<style>
/* ============================
   SHARED FORM STYLES
============================= */

.pl-wrap { padding: 1.5rem; max-width: 1100px; margin: 0 auto; }

.pl-form-header { margin-bottom: 1.25rem; }

.pl-back {
  font-size: 13px;
  color: #6b7280;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  margin-bottom: .5rem;
}
.pl-back:hover { color: #374151; }

.pl-title { font-size: 22px; font-weight: 600; color: #1a1a1a; margin: 0; }
.pl-sub   { font-size: 14px; color: #6b7280; margin-top: 4px; }

.pl-form-card {
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  padding: 1.5rem;
  max-width: 640px;
}

/* Grid 2 kolom */
.pl-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
@media (max-width: 560px) {
  .pl-grid { grid-template-columns: 1fr; }
}

/* Field */
.pl-field { display: flex; flex-direction: column; gap: 4px; }

.pl-label {
  font-size: 13px;
  font-weight: 500;
  color: #374151;
}

.pl-input,
.pl-select {
  padding: 8px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  color: #1a1a1a;
  background: #fff;
  width: 100%;
  transition: border-color .15s;
}
.pl-input:focus,
.pl-select:focus {
  outline: none;
  border-color: #1D9E75;
  box-shadow: 0 0 0 3px rgba(29, 158, 117, .1);
}
.pl-input.is-error,
.pl-select.is-error { border-color: #FCA5A5; }

/* Input with suffix */
.pl-input-group {
  display: flex;
  align-items: stretch;
}
.pl-input-group .pl-input {
  border-right: none;
  border-radius: 8px 0 0 8px;
}
.pl-input-suffix {
  display: inline-flex;
  align-items: center;
  padding: 0 12px;
  border: 1px solid #d1d5db;
  border-left: none;
  border-radius: 0 8px 8px 0;
  background: #f9fafb;
  font-size: 13px;
  color: #6b7280;
  white-space: nowrap;
}

/* Error messages */
.pl-error-msg { font-size: 12px; color: #DC2626; }

.pl-errors {
  background: #FEF2F2;
  border: 1px solid #FECACA;
  border-radius: 8px;
  padding: .75rem 1rem;
  font-size: 13px;
  color: #DC2626;
}
.pl-errors ul { padding-left: 1rem; margin: 0; }
.pl-errors li { margin-bottom: 2px; }

/* Footer buttons */
.pl-form-footer {
  display: flex;
  gap: 8px;
  justify-content: flex-end;
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid #f3f4f6;
}

.btn-filter {
  padding: 7px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 13px;
  cursor: pointer;
  background: #f9fafb;
  color: #374151;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
}
.btn-filter:hover { background: #f3f4f6; }

.btn-new {
  padding: 7px 16px;
  border-radius: 8px;
  font-size: 13px;
  cursor: pointer;
  border: none;
  background: #1D9E75;
  color: white;
  font-weight: 500;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
}
.btn-new:hover { background: #168a64; }
</style>