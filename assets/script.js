
function formatTanggalIndo(dateString) {
    const bulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    const date = new Date(dateString);
    return `${date.getDate()} ${bulan[date.getMonth()]} ${date.getFullYear()}`;
}


function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}


function validateNIM(nim) {
    const re = /^[0-9]{7,15}$/;
    return re.test(nim);
}


function validatePhone(phone) {
    const re = /^[0-9]{10,15}$/;
    return re.test(phone);
}


function showLoading(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.innerHTML = '<div class="spinner"></div>';
    }
}


function showError(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger';
    alertDiv.textContent = message;
    
    
    const main = document.querySelector('main');
    if (main) {
        main.insertBefore(alertDiv, main.firstChild);
        
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}


function showSuccess(message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-success';
    alertDiv.textContent = message;
    
    
    const main = document.querySelector('main');
    if (main) {
        main.insertBefore(alertDiv, main.firstChild);
        
        
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
}


function confirmDelete(message = 'Yakin ingin menghapus data ini?') {
    return confirm(message);
}


document.addEventListener('DOMContentLoaded', function() {
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                } else {
                    field.classList.remove('error');
                }
            });

            if (!isValid) {
                e.preventDefault();
                showError('Mohon lengkapi semua field yang wajib diisi');
            }
        });
    });

    
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
});


document.addEventListener('keypress', function(e) {
    if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
        const form = e.target.closest('form');
        if (form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.click();
            }
        }
    }
});/* ========================================
   SISTEM EVENT KAMPUS - script.js (Improved)
   ======================================== */

(function () {
  // ==============================
  // Helpers
  // ==============================
  function $(selector, scope = document) {
    return scope.querySelector(selector);
  }

  function $all(selector, scope = document) {
    return Array.from(scope.querySelectorAll(selector));
  }

  function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text ?? "";
    return div.innerHTML;
  }

  // ==============================
  // Date format (Indonesia)
  // ==============================
  window.formatTanggalIndo = function (dateString) {
    const bulan = [
      "Januari", "Februari", "Maret", "April", "Mei", "Juni",
      "Juli", "Agustus", "September", "Oktober", "November", "Desember",
    ];

    const date = new Date(dateString);
    if (isNaN(date.getTime())) return "-";

    return `${date.getDate()} ${bulan[date.getMonth()]} ${date.getFullYear()}`;
  };

  // ==============================
  // Validators
  // ==============================
  window.validateEmail = function (email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(String(email).trim());
  };

  window.validateNIM = function (nim) {
    const re = /^[0-9]{7,15}$/;
    return re.test(String(nim).trim());
  };

  window.validatePhone = function (phone) {
    const re = /^[0-9]{10,15}$/;
    return re.test(String(phone).trim());
  };

  // ==============================
  // Loading helper
  // ==============================
  window.showLoading = function (elementId, text = "Memuat...") {
    const element = document.getElementById(elementId);
    if (!element) return;

    element.innerHTML = `
      <div class="loading">
        <div class="spinner"></div>
        <p>${escapeHtml(text)}</p>
      </div>
    `;
  };

  // ==============================
  // Modern Alert System
  // ==============================
  let alertTimeout = null;

  function removeExistingAlerts() {
    $all(".alert-modern").forEach((a) => a.remove());
  }

  function createAlert(type, message, options = {}) {
    const {
      duration = 4500,   // auto hide (ms)
      closeable = true,  // show close button
      scrollToTop = true // auto scroll to alert
    } = options;

    removeExistingAlerts();

    const main = $("main") || document.body;

    const alertDiv = document.createElement("div");
    alertDiv.className = `alert-modern ${type}`;
    alertDiv.innerHTML = `
      <div class="alert-modern__icon">
        ${type === "success" ? "✅" : type === "danger" ? "❌" : type === "warning" ? "⚠️" : "ℹ️"}
      </div>

      <div class="alert-modern__content">
        <div class="alert-modern__title">
          ${
            type === "success"
              ? "Berhasil"
              : type === "danger"
              ? "Gagal"
              : type === "warning"
              ? "Perhatian"
              : "Info"
          }
        </div>
        <div class="alert-modern__message">${escapeHtml(message)}</div>
      </div>

      ${
        closeable
          ? `<button class="alert-modern__close" aria-label="Close">✖</button>`
          : ""
      }
    `;

    main.insertBefore(alertDiv, main.firstChild);

    if (scrollToTop) {
      alertDiv.scrollIntoView({ behavior: "smooth", block: "start" });
    }

    // Close button
    const closeBtn = $(".alert-modern__close", alertDiv);
    if (closeBtn) {
      closeBtn.addEventListener("click", () => {
        alertDiv.classList.add("hide");
        setTimeout(() => alertDiv.remove(), 250);
      });
    }

    // Auto hide
    if (alertTimeout) clearTimeout(alertTimeout);
    alertTimeout = setTimeout(() => {
      if (!alertDiv.isConnected) return;
      alertDiv.classList.add("hide");
      setTimeout(() => alertDiv.remove(), 250);
    }, duration);
  }

  window.showError = function (message, options = {}) {
    createAlert("danger", message, options);
  };

  window.showSuccess = function (message, options = {}) {
    createAlert("success", message, options);
  };

  window.showInfo = function (message, options = {}) {
    createAlert("info", message, options);
  };

  window.showWarning = function (message, options = {}) {
    createAlert("warning", message, options);
  };

  // ==============================
  // Confirm delete
  // ==============================
  window.confirmDelete = function (message = "Yakin ingin menghapus data ini?") {
    return confirm(message);
  };

  // ==============================
  // Smooth anchor scroll
  // ==============================
  function initSmoothAnchorScroll() {
    $all('a[href^="#"]').forEach((anchor) => {
      anchor.addEventListener("click", function (e) {
        const href = this.getAttribute("href");
        if (!href || href === "#") return;

        const target = document.querySelector(href);
        if (!target) return;

        e.preventDefault();
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      });
    });
  }

  // ==============================
  // Form validation (data-validate)
  // ==============================
  function initFormValidation() {
    const forms = $all("form[data-validate]");

    forms.forEach((form) => {
      form.addEventListener("submit", function (e) {
        const requiredFields = $all("[required]", form);
        let isValid = true;

        requiredFields.forEach((field) => {
          const val = String(field.value || "").trim();

          // Required check
          if (!val) {
            isValid = false;
            field.classList.add("error");
            return;
          } else {
            field.classList.remove("error");
          }

          // Optional: specific validations based on ID/name
          const key = (field.name || field.id || "").toLowerCase();

          if (key.includes("email") && !window.validateEmail(val)) {
            isValid = false;
            field.classList.add("error");
          }

          if (key === "nim" && !window.validateNIM(val)) {
            isValid = false;
            field.classList.add("error");
          }

          if ((key.includes("hp") || key.includes("phone")) && !window.validatePhone(val)) {
            isValid = false;
            field.classList.add("error");
          }
        });

        if (!isValid) {
          e.preventDefault();
          window.showError("Mohon lengkapi semua field dengan benar.");
        }
      });
    });
  }

  // ==============================
  // Enter key submit control
  // ==============================
  function initEnterSubmitHandler() {
    document.addEventListener("keydown", function (e) {
      if (e.key !== "Enter") return;

      const tag = (e.target.tagName || "").toUpperCase();
      const isTextArea = tag === "TEXTAREA";
      const isButton = tag === "BUTTON";
      const isInput = tag === "INPUT";

      // jangan block kalau textarea
      if (isTextArea) return;

      // kalau button default ya biarin
      if (isButton) return;

      // kalau input type tertentu jangan dipaksa submit
      if (isInput) {
        const type = (e.target.getAttribute("type") || "text").toLowerCase();
        const blockTypes = ["submit", "button", "file"];
        if (blockTypes.includes(type)) return;
      }

      const form = e.target.closest("form");
      if (!form) return;

      const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
      if (submitBtn) {
        e.preventDefault();
        submitBtn.click();
      }
    });
  }

  document.addEventListener("DOMContentLoaded", function () {
    initSmoothAnchorScroll();
    initFormValidation();
    initEnterSubmitHandler();
  });
})();
