// Landlord Dashboard JavaScript
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
      if (!sidebar.contains(e.target) && !mobileMenuToggle.contains(e.target)) {
        sidebar.classList.remove("mobile-open")
      }
    }
  })

  // Close stat dropdowns when clicking outside
  document.addEventListener("click", (e) => {
    if (!e.target.closest(".stat-header")) {
      closeAllStatDropdowns()
    }
  })

  // Initialize tooltips and other UI enhancements
  initializeUI()
})

// Initialize UI enhancements
function initializeUI() {
  // Add hover effects to cards
  document.querySelectorAll(".stat-card").forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-2px)"
      this.style.boxShadow = "0 4px 12px rgba(0, 0, 0, 0.1)"
    })

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0)"
      this.style.boxShadow = "0 1px 3px rgba(0, 0, 0, 0.1)"
    })
  })

  // Add hover effects to property cards
  document.querySelectorAll(".property-card").forEach((card) => {
    card.addEventListener("mouseenter", function () {
      this.style.transform = "translateY(-2px)"
      this.style.boxShadow = "0 4px 12px rgba(0, 0, 0, 0.15)"
    })

    card.addEventListener("mouseleave", function () {
      this.style.transform = "translateY(0)"
      this.style.boxShadow = "0 1px 3px rgba(0, 0, 0, 0.1)"
    })
  })
}

// Notification system
function showNotification(message, type = "info") {
  const notification = document.createElement("div")
  notification.className = `notification notification-${type}`
  notification.textContent = message

  Object.assign(notification.style, {
    position: "fixed",
    top: "20px",
    right: "20px",
    padding: "1rem 1.5rem",
    borderRadius: "0.5rem",
    color: "white",
    fontWeight: "500",
    zIndex: "9999",
    opacity: "0",
    transform: "translateY(-20px)",
    transition: "all 0.3s ease",
  })

  const colors = {
    success: "#10b981",
    warning: "#f59e0b",
    error: "#ef4444",
    info: "#3b82f6",
  }
  notification.style.backgroundColor = colors[type] || colors.info

  document.body.appendChild(notification)

  setTimeout(() => {
    notification.style.opacity = "1"
    notification.style.transform = "translateY(0)"
  }, 100)

  setTimeout(() => {
    notification.style.opacity = "0"
    notification.style.transform = "translateY(-20px)"
    setTimeout(() => {
      if (document.body.contains(notification)) {
        document.body.removeChild(notification)
      }
    }, 300)
  }, 3000)
}

// Utility functions
function debounce(func, wait) {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// ===== STAT CARD DROPDOWN FUNCTIONS =====

/**
 * Toggle the dropdown menu for a stat card
 * @param {string} statType - The stat card type (properties, leases, income, maintenance)
 */
function toggleStatDropdown(statType) {
  const dropdown = document.getElementById(`stat-dropdown-${statType}`)
  if (!dropdown) return

  // Close all other dropdowns first
  closeAllStatDropdowns(statType)

  // Toggle the clicked dropdown
  dropdown.classList.toggle("active")
}

/**
 * Close all stat dropdowns except the specified one
 * @param {string} exceptStatType - Optional stat type to exclude from closing
 */
function closeAllStatDropdowns(exceptStatType = null) {
  const allDropdowns = document.querySelectorAll(".stat-dropdown")
  allDropdowns.forEach((dropdown) => {
    const id = dropdown.id
    const statType = id.replace("stat-dropdown-", "")
    if (statType !== exceptStatType) {
      dropdown.classList.remove("active")
    }
  })
}

/**
 * Handle period selection from stat dropdown
 * @param {string} statType - The stat card type
 * @param {string} period - The selected period (all, month, year)
 * @param {Event} event - The click event
 */
function selectStatPeriod(statType, period, event) {
  event.stopPropagation()

  const dropdown = document.getElementById(`stat-dropdown-${statType}`)
  if (!dropdown) return

  // Update selected state in dropdown
  const items = dropdown.querySelectorAll(".stat-dropdown-item")
  items.forEach((item) => {
    item.classList.remove("selected")
    if (item.dataset.period === period) {
      item.classList.add("selected")
    }
  })

  // Close the dropdown
  dropdown.classList.remove("active")

  // Fetch new data
  fetchStatData(statType, period)
}

/**
 * Fetch stat data from server via AJAX
 * @param {string} statType - The stat card type
 * @param {string} period - The selected period
 */
async function fetchStatData(statType, period) {
  const valueElement = document.getElementById(`stat-value-${statType}`)
  const subtitleElement = document.getElementById(`stat-subtitle-${statType}`)

  if (!valueElement) return

  // Show loading state
  valueElement.classList.add("loading")

  try {
    const response = await fetch(`${URLROOT}/landlord/getStatData`, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        stat_type: statType,
        period: period,
      }),
    })

    const data = await response.json()

    if (data.success) {
      // Update the stat value
      valueElement.textContent = data.formatted

      // Update the subtitle if provided
      if (subtitleElement && data.subtitle) {
        subtitleElement.textContent = data.subtitle
      }
    } else {
      console.error("Failed to fetch stat data:", data.message)
      showNotification("Failed to update stat", "error")
    }
  } catch (error) {
    console.error("Error fetching stat data:", error)
    showNotification("Error loading data", "error")
  } finally {
    // Remove loading state
    valueElement.classList.remove("loading")
  }
}