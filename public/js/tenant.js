// Tenant Dashboard JavaScript - Complete
document.addEventListener("DOMContentLoaded", () => {
  // Sidebar toggle functionality
  const sidebarToggle = document.getElementById("sidebarToggle")
  const mobileMenuToggle = document.getElementById("mobileMenuToggle")
  const sidebar = document.getElementById("sidebar")

  // Desktop sidebar toggle
  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", () => {
      sidebar.classList.toggle("collapsed")
    })
  }

  // Mobile sidebar toggle
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener("click", () => {
      sidebar.classList.toggle("open")
    })
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener("click", (event) => {
    if (window.innerWidth <= 1024) {
      if (!sidebar.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
        sidebar.classList.remove("open")
      }
    }
  })

  // User dropdown functionality
  const userMenuToggle = document.getElementById("userMenuToggle")
  const userDropdown = document.getElementById("userDropdown")

  if (userMenuToggle && userDropdown) {
    userMenuToggle.addEventListener("click", (e) => {
      e.preventDefault()
      userDropdown.style.display = userDropdown.style.display === "block" ? "none" : "block"
    })

    // Close dropdown when clicking outside
    document.addEventListener("click", (event) => {
      if (!userMenuToggle.contains(event.target)) {
        userDropdown.style.display = "none"
      }
    })
  }

  // Responsive sidebar handling
  function handleResize() {
    if (window.innerWidth > 1024) {
      sidebar.classList.remove("open")
    }
  }

  window.addEventListener("resize", handleResize)

  // Form validation enhancement
  const forms = document.querySelectorAll("form")
  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      const requiredFields = form.querySelectorAll("[required]")
      let isValid = true

      requiredFields.forEach((field) => {
        if (!field.value.trim()) {
          field.classList.add("error")
          isValid = false
        } else {
          field.classList.remove("error")
        }
      })

      if (!isValid) {
        e.preventDefault()
        showNotification("Please fill in all required fields.", "error")
      }
    })
  })

  // Add focus states for better accessibility
  document.querySelectorAll("input, select, textarea").forEach((field) => {
    field.addEventListener("focus", function () {
      this.parentElement.classList.add("focused")
    })

    field.addEventListener("blur", function () {
      this.parentElement.classList.remove("focused")
    })
  })
})

// Issue Tracking Functions

function closeIssueModal() {
  const modal = document.getElementById('issueModal')
  if (modal) {
    modal.classList.add('hidden')
  }
}

