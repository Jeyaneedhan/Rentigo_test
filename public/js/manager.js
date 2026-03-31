// Property Manager JavaScript Functions


// Tab Functions
function showTab(tabName) {
  // Hide all tab contents
  const tabContents = document.querySelectorAll(".tab-content")
  tabContents.forEach((content) => content.classList.remove("active"))

  // Remove active class from all tab buttons
  const tabButtons = document.querySelectorAll(".tab-button")
  tabButtons.forEach((button) => button.classList.remove("active"))

  // Show selected tab content
  const targetTab = document.getElementById(tabName + "-tab")
  if (targetTab) {
    targetTab.classList.add("active")
  }

  // Add active class to clicked button
  if (event && event.target) {
    event.target.classList.add("active")
  }
}

function showIssueTab(tabName) {
  showTab(tabName)
  // Additional issue-specific logic can be added here
}

// Search and Filter Functions
function initializeSearch() {
  const providerSearch = document.getElementById("providerSearch")

  if (providerSearch) {
    providerSearch.addEventListener("input", function () {
      filterProviders(this.value)
    })
  }
}

function filterProviders(searchTerm) {
  const providerCards = document.querySelectorAll(".provider-card")

  providerCards.forEach((card) => {
    const text = card.textContent.toLowerCase()
    if (text.includes(searchTerm.toLowerCase())) {
      card.style.display = ""
    } else {
      card.style.display = "none"
    }
  })
}

function initializeDropdowns() {
  const userMenuToggle = document.getElementById("userMenuToggle")
  const userDropdown = document.getElementById("userDropdown")

  if (userMenuToggle && userDropdown) {
    userMenuToggle.addEventListener("click", (e) => {
      e.stopPropagation()
      userDropdown.classList.toggle("active")
    })
  }

  // Close dropdown when clicking outside
  document.addEventListener("click", () => {
    if (userDropdown) userDropdown.classList.remove("active")
  })
}

// Close modals when clicking outside
function initializeModalHandlers() {
  const modals = document.querySelectorAll(".modal")

  modals.forEach((modal) => {
    modal.addEventListener("click", function (e) {
      if (e.target === this) {
        this.classList.remove("active")
      }
    })
  })

  // Close modals with Escape key
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      const activeModal = document.querySelector(".modal.active")
      if (activeModal) {
        activeModal.classList.remove("active")
      }
    }
  })
}

// Initialize all functions when DOM is loaded
document.addEventListener("DOMContentLoaded", () => {
  initializeSearch()
  initializeModalHandlers()
  initializeDropdowns()
})
