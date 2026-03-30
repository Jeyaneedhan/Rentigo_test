/**
 * shared.js — Rentigo Shared JavaScript Utilities
 * Loaded on every authenticated page (admin, landlord, manager, tenant).
 * Contains functions that were previously duplicated across all role-specific JS files.
 */

// ============================================================
// UTILITY: debounce
// Delays calling a function until after N ms of inactivity.
// ============================================================
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// ============================================================
// UTILITY: showNotification
// Displays a temporary toast notification at the top-right.
// @param {string} message  - Text to display
// @param {string} type     - "success" | "warning" | "error" | "info"
// ============================================================
function showNotification(message, type = 'info') {
    // Remove any existing notifications first
    document.querySelectorAll('.notification').forEach(n => n.remove());

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;

    const icons = {
        success: 'fa-check-circle',
        warning: 'fa-exclamation-triangle',
        error: 'fa-times-circle',
        info: 'fa-info-circle'
    };

    notification.innerHTML = `
    <div style="display:flex;align-items:center;gap:0.5rem;">
      <i class="fas ${icons[type] || icons.info}"></i>
      <span>${message}</span>
      <button onclick="this.parentElement.parentElement.remove()"
        style="margin-left:auto;background:none;border:none;color:white;cursor:pointer;font-size:1.2rem;padding:0;">&times;</button>
    </div>
  `;

    Object.assign(notification.style, {
        position: 'fixed',
        top: '20px',
        right: '20px',
        padding: '1rem 1.5rem',
        borderRadius: '0.5rem',
        color: 'white',
        fontWeight: '500',
        zIndex: '9999',
        opacity: '0',
        transform: 'translateY(-20px)',
        transition: 'all 0.3s ease',
        maxWidth: '400px',
        boxShadow: '0 4px 12px rgba(0,0,0,0.15)'
    });

    const colors = { success: '#10b981', warning: '#f59e0b', error: '#ef4444', info: '#3b82f6' };
    notification.style.backgroundColor = colors[type] || colors.info;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.opacity = '1';
        notification.style.transform = 'translateY(0)';
    }, 100);

    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.style.opacity = '0';
            notification.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                if (document.body.contains(notification)) document.body.removeChild(notification);
            }, 300);
        }
    }, 4000);
}

// ============================================================
// STAT CARD DROPDOWNS
// Used on admin, landlord, manager, and tenant dashboards.
// Each page sets a global STAT_ENDPOINT before shared.js runs.
// ============================================================

/**
 * Toggle a stat card's dropdown menu.
 * @param {string} statType - e.g. "properties", "revenue"
 */
function toggleStatDropdown(statType) {
    const dropdown = document.getElementById(`stat-dropdown-${statType}`);
    if (!dropdown) return;

    closeAllStatDropdowns();
    dropdown.classList.toggle('active');
    dropdown.classList.toggle('show');

    if (typeof event !== 'undefined') event.stopPropagation();
}

/**
 * Close all open stat card dropdowns.
 */
function closeAllStatDropdowns() {
    document.querySelectorAll('.stat-dropdown').forEach(d => {
        d.classList.remove('active');
        d.classList.remove('show');
    });
}

/**
 * Handle period selection from a stat card dropdown.
 * @param {string} statType  - Stat card identifier
 * @param {string} period    - "all" | "month" | "year"
 * @param {Event}  evt       - Click event
 */
function selectStatPeriod(statType, period, evt) {
    if (evt) evt.stopPropagation();

    const dropdown = document.getElementById(`stat-dropdown-${statType}`);
    if (dropdown) {
        dropdown.querySelectorAll('.stat-dropdown-item').forEach(item => {
            item.classList.remove('selected');
            if (item.dataset.period === period) item.classList.add('selected');
        });
    }

    closeAllStatDropdowns();
    fetchStatData(statType, period);
}

/**
 * Fetch updated stat value via AJAX.
 * Requires URLROOT and STAT_ENDPOINT to be set by the embedding page.
 * @param {string} statType - Stat card identifier
 * @param {string} period   - "all" | "month" | "year"
 */
async function fetchStatData(statType, period) {
    // STAT_ENDPOINT must be set by the page before shared.js functions run.
    // e.g. in admin_footer.php: const STAT_ENDPOINT = URLROOT + '/admin/getStatData';
    if (typeof STAT_ENDPOINT === 'undefined' || typeof URLROOT === 'undefined') {
        console.error('shared.js: STAT_ENDPOINT or URLROOT not defined on this page.');
        return;
    }

    const valueEl = document.getElementById(`stat-value-${statType}`);
    const subtitleEl = document.getElementById(`stat-subtitle-${statType}`);
    if (!valueEl) return;

    valueEl.classList.add('loading');

    try {
        // Tenant issue stat cards are served by Issues controller.
        const endpoint = String(statType).startsWith('tenant_issues_')
            ? `${URLROOT}/issues/getStatData`
            : STAT_ENDPOINT;

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ stat_type: statType, period: period })
        });

        const data = await response.json();

        if (data.error) {
            console.error('fetchStatData error:', data.error);
            return;
        }

        // Support both response shapes used across the codebase
        valueEl.textContent = data.formatted ?? data.value ?? '';

        if (subtitleEl && data.subtitle) {
            subtitleEl.textContent = data.subtitle;
        }
    } catch (err) {
        console.error('fetchStatData fetch error:', err);
        showNotification('Error loading stat data', 'error');
    } finally {
        valueEl.classList.remove('loading');
    }
}

// Close stat dropdowns when clicking outside a stat header
document.addEventListener('click', function (e) {
    if (!e.target.closest('.stat-header')) {
        closeAllStatDropdowns();
    }
});
