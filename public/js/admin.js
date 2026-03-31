// Admin Dashboard JavaScript - Complete Updated Version with Financial Management
document.addEventListener("DOMContentLoaded", () => {
  // Sidebar toggle functionality
  const sidebarToggle = document.getElementById("sidebarToggle")
  const mobileMenuToggle = document.getElementById("mobileMenuToggle")
  const sidebar = document.getElementById("sidebar")

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("collapsed")
    })
  }

  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener("click", () => {
      sidebar.classList.toggle("mobile-open")
    })
  }

  // Close mobile menu when clicking outside
  document.addEventListener("click", (e) => {
    if (window.innerWidth <= 768) {
      if (!sidebar.contains(e.target) && !mobileMenuToggle?.contains(e.target)) {
        sidebar.classList.remove("mobile-open")
      }
    }
  })

  // Table row selection
  const selectAllCheckbox = document.querySelector(".select-all")
  const rowCheckboxes = document.querySelectorAll(".row-select")

  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener("change", function () {
      rowCheckboxes.forEach((checkbox) => {
        checkbox.checked = this.checked
      })
    })
  }

  // Individual row selection
  rowCheckboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      const checkedCount = document.querySelectorAll(".row-select:checked").length
      const totalCount = rowCheckboxes.length

      if (selectAllCheckbox) {
        selectAllCheckbox.checked = checkedCount === totalCount
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount
      }
    })
  })

  // Initialize specific modules
  initializeManagerManagement()
  initializePropertiesManagement()
  initializeFinancialManagement()
  initializeUI()
  initializeKeyboardShortcuts()
})

// Tab Switching Function - Global scope for onclick handlers
function switchTab(tabName) {
  // Hide all tab contents
  const allTabs = document.querySelectorAll('.tab-content')
  allTabs.forEach(tab => {
    tab.classList.remove('active')
  })

  // Remove active class from all tab buttons
  const allButtons = document.querySelectorAll('.tab-btn')
  allButtons.forEach(btn => {
    btn.classList.remove('active')
  })

  // Show selected tab content
  const selectedTab = document.getElementById(tabName + '-tab')
  if (selectedTab) {
    selectedTab.classList.add('active')
  }

  // Add active class to clicked button
  const clickedButton = document.getElementById(tabName + '-btn')
  if (clickedButton) {
    clickedButton.classList.add('active')
  }

  console.log('Switched to tab:', tabName)
}

// Manager Management Functions
function initializeManagerManagement() {
  // Search functionality for managers
  const searchInput = document.getElementById('searchManagers')
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const searchTerm = this.value.toLowerCase()
      const rows = document.querySelectorAll('.managers-table tbody tr')

      rows.forEach(row => {
        const text = row.textContent.toLowerCase()
        row.style.display = text.includes(searchTerm) ? '' : 'none'
      })
    })
  }

  // Filter functionality for managers
  const filterDropdown = document.getElementById('filterManagers')
  if (filterDropdown) {
    filterDropdown.addEventListener('change', function () {
      const filterValue = this.value.toLowerCase()
      const rows = document.querySelectorAll('.managers-table tbody tr')

      rows.forEach(row => {
        if (filterValue === '') {
          row.style.display = ''
        } else {
          const statusSpan = row.querySelector('[class*="status-"]')
          if (statusSpan) {
            const status = statusSpan.textContent.toLowerCase()
            row.style.display = status.includes(filterValue) ? '' : 'none'
          }
        }
      })
    })
  }
}

// Properties Management Functions
function initializePropertiesManagement() {
  // Search functionality
  const searchInput = document.getElementById('searchProperties')
  if (searchInput) {
    searchInput.addEventListener('input', function () {
      const searchTerm = this.value.toLowerCase()
      const rows = document.querySelectorAll('.data-table tbody tr')

      rows.forEach(row => {
        const text = row.textContent.toLowerCase()
        row.style.display = text.includes(searchTerm) ? '' : 'none'
      })
    })
  }

  // Filter functionality
  const filterDropdown = document.getElementById('filterProperties')
  if (filterDropdown) {
    filterDropdown.addEventListener('change', function () {
      const filterValue = this.value.toLowerCase()
      const rows = document.querySelectorAll('.data-table tbody tr')

      rows.forEach(row => {
        if (filterValue === '') {
          row.style.display = ''
        } else {
          const statusBadge = row.querySelector('.status-badge')
          if (statusBadge) {
            const status = statusBadge.textContent.toLowerCase()
            row.style.display = status.includes(filterValue) ? '' : 'none'
          }
        }
      })
    })
  }
}

// Property actions are handled by server-side links in the views (Properties controller).
// No JS override needed here.

// Transaction approve/reject are handled by server-side PHP routes.

// Search functionality for general use
function performSearch(query) {
  console.log("Searching for:", query)
  const rows = document.querySelectorAll('.data-table tbody tr, .managers-table tbody tr, .transactions-table tbody tr')

  rows.forEach(row => {
    const text = row.textContent.toLowerCase()
    row.style.display = text.includes(query.toLowerCase()) ? '' : 'none'
  })
}

// showNotification(), getNotificationIcon(), debounce() — moved to shared.js

// Initialize tooltips and other UI enhancements
function initializeUI() {
  // Add hover effects to stat cards
  document.querySelectorAll(".stat-card").forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-2px)"
      this.style.boxShadow = "0 4px 12px rgba(0, 0, 0, 0.1)"
    })

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0)"
      this.style.boxShadow = "none"
    })
  })

  // Enhanced search with debounce
  const searchInputs = document.querySelectorAll(".search-input, .search-input-global")
  searchInputs.forEach(input => {
    input.addEventListener(
      "input",
      debounce(function () {
        performSearch(this.value)
      }, 300),
    )
  })

  // Status badge interactions
  document.querySelectorAll(".status-badge").forEach((badge) => {
    badge.addEventListener("click", function () {
      console.log("Status clicked:", this.textContent)
    })
  })

  // Enhanced table row hover effects
  document.querySelectorAll(".data-table tbody tr, .managers-table tbody tr, .transactions-table tbody tr").forEach((row) => {
    row.addEventListener("mouseenter", function () {
      this.style.backgroundColor = "var(--light-color)"
      this.style.transform = "translateY(-1px)"
      this.style.boxShadow = "0 2px 8px rgba(0, 0, 0, 0.05)"
    })

    row.addEventListener("mouseleave", function () {
      this.style.backgroundColor = ""
      this.style.transform = ""
      this.style.boxShadow = ""
    })
  })

  // Initialize tab functionality if tabs exist
  initializeTabFunctionality()
}

// Tab functionality initialization
function initializeTabFunctionality() {
  const tabButtons = document.querySelectorAll('.tab-btn')

  tabButtons.forEach(button => {
    // Only add event listener if button doesn't already have onclick
    if (!button.onclick && !button.getAttribute('onclick')) {
      button.addEventListener('click', function () {
        const tabName = this.id.replace('-btn', '')
        switchTab(tabName)
      })
    }
  })
}

// Keyboard shortcuts
function initializeKeyboardShortcuts() {
  document.addEventListener('keydown', function (e) {
    // Ctrl/Cmd + K for search
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
      e.preventDefault()
      const searchInput = document.querySelector('.search-input, #transactionSearch, #searchManagers, #searchProperties')
      if (searchInput) {
        searchInput.focus()
        searchInput.select()
      }
    }

    // Escape to close modals/notifications
    if (e.key === 'Escape') {
      // Close any open notifications
      document.querySelectorAll('.notification').forEach(n => n.remove())

      // Close any open modals
      document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.classList.add('hidden')
      })

      // Close any open dropdowns
      document.querySelectorAll('.user-dropdown').forEach(d => {
        d.style.display = 'none'
      })
    }
  })
}

// ===== STAT CARD DROPDOWN FUNCTIONS =====
// toggleStatDropdown(), closeAllStatDropdowns(), selectStatPeriod(), fetchStatData()
// — all moved to shared.js. STAT_ENDPOINT is set in admin_footer.php.

// Close dropdowns when clicking outside — handled in shared.js

// Global functions for window object (for backwards compatibility)
window.adminJS = {
  // Tab functions
  switchTab,

  // Stat dropdown functions (from shared.js)
  toggleStatDropdown,
  closeAllStatDropdowns,
  selectStatPeriod,
  fetchStatData,

  // Utility functions (from shared.js)
  showNotification,
  performSearch
}

// Export for ES6 modules if needed
if (typeof module !== 'undefined' && module.exports) {
  module.exports = window.adminJS
}