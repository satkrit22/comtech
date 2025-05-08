/**
 * Admin Dashboard JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
  // Sidebar Toggle
  const sidebarToggle = document.querySelector('.sidebar-toggle');
  const adminSidebar = document.querySelector('.admin-sidebar');
  const adminMain = document.querySelector('.admin-main');
  const sidebarOverlay = document.querySelector('.sidebar-overlay');
  
  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', function() {
      adminSidebar.classList.toggle('collapsed');
      adminMain.classList.toggle('expanded');
    });
  }
  
  // Mobile Sidebar Toggle
  const sidebarToggleMobile = document.querySelector('.sidebar-toggle-mobile');
  
  if (sidebarToggleMobile) {
    sidebarToggleMobile.addEventListener('click', function() {
      adminSidebar.classList.add('active');
      sidebarOverlay.classList.add('active');
    });
  }
  
  // Close sidebar when clicking on overlay
  if (sidebarOverlay) {
    sidebarOverlay.addEventListener('click', function() {
      adminSidebar.classList.remove('active');
      sidebarOverlay.classList.remove('active');
    });
  }
  
  // Table Search
  const tableSearch = document.querySelector('.table-search-input');
  
  if (tableSearch) {
    tableSearch.addEventListener('keyup', function() {
      const searchTerm = this.value.toLowerCase();
      const table = this.closest('.card').querySelector('table');
      const rows = table.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  }
  
  // Confirm Delete
  const deleteButtons = document.querySelectorAll('.delete-btn');
  
  deleteButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
        e.preventDefault();
      }
    });
  });
  
  // Status Badge Coloring
  const statusBadges = document.querySelectorAll('.status-badge');
  
  statusBadges.forEach(badge => {
    const status = badge.textContent.trim().toLowerCase();
    
    if (status === 'active' || status === 'completed' || status === 'delivered') {
      badge.classList.add('badge-success');
    } else if (status === 'pending' || status === 'processing') {
      badge.classList.add('badge-warning');
    } else if (status === 'inactive' || status === 'cancelled') {
      badge.classList.add('badge-danger');
    } else if (status === 'shipped') {
      badge.classList.add('badge-info');
    } else {
      badge.classList.add('badge-secondary');
    }
  });
  
  // Dropdown Toggle
  const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
  
  dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      const dropdown = this.nextElementSibling;
      dropdown.classList.toggle('show');
      
      // Close other dropdowns
      dropdownToggles.forEach(otherToggle => {
        if (otherToggle !== toggle) {
          otherToggle.nextElementSibling.classList.remove('show');
        }
      });
      
      // Close dropdown when clicking outside
      document.addEventListener('click', function closeDropdown(e) {
        if (!toggle.contains(e.target) && !dropdown.contains(e.target)) {
          dropdown.classList.remove('show');
          document.removeEventListener('click', closeDropdown);
        }
      });
    });
  });
  
  // Form Validation
  const forms = document.querySelectorAll('.needs-validation');
  
  forms.forEach(form => {
    form.addEventListener('submit', function(e) {
      if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
      }
      
      form.classList.add('was-validated');
    });
  });
  
  // Image Upload Preview
  const imageUploads = document.querySelectorAll('.image-upload-input');
  
  imageUploads.forEach(input => {
    input.addEventListener('change', function() {
      const preview = this.closest('.image-upload').querySelector('.image-preview');
      
      if (preview && this.files && this.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          preview.src = e.target.result;
          preview.style.display = 'block';
        };
        
        reader.readAsDataURL(this.files[0]);
      }
    });
  });
  
  // Tab Navigation
  const tabLinks = document.querySelectorAll('.tab-link');
  
  tabLinks.forEach(link => {
    link.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Get the tab target
      const tabTarget = this.getAttribute('data-tab');
      
      // Remove active class from all tab links and contents
      document.querySelectorAll('.tab-link').forEach(el => {
        el.classList.remove('active');
      });
      
      document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.remove('active');
      });
      
      // Add active class to current tab link and content
      this.classList.add('active');
      document.getElementById(tabTarget).classList.add('active');
    });
  });
  
  // Sortable Tables
  const sortableHeaders = document.querySelectorAll('.sortable');
  
  sortableHeaders.forEach(header => {
    header.addEventListener('click', function() {
      const table = this.closest('table');
      const index = Array.from(this.parentNode.children).indexOf(this);
      const rows = Array.from(table.querySelectorAll('tbody tr'));
      const direction = this.classList.contains('asc') ? 'desc' : 'asc';
      
      // Remove sort classes from all headers
      table.querySelectorAll('th').forEach(th => {
        th.classList.remove('asc', 'desc');
      });
      
      // Add sort class to current header
      this.classList.add(direction);
      
      // Sort the rows
      rows.sort((a, b) => {
        const aValue = a.children[index].textContent.trim();
        const bValue = b.children[index].textContent.trim();
        
        // Check if values are numbers
        if (!isNaN(aValue) && !isNaN(bValue)) {
          return direction === 'asc' 
            ? parseFloat(aValue) - parseFloat(bValue)
            : parseFloat(bValue) - parseFloat(aValue);
        }
        
        // Sort as strings
        return direction === 'asc'
          ? aValue.localeCompare(bValue)
          : bValue.localeCompare(aValue);
      });
      
      // Reorder the rows
      const tbody = table.querySelector('tbody');
      rows.forEach(row => tbody.appendChild(row));
    });
  });
  
  // Responsive Tables
  const tables = document.querySelectorAll('.table-responsive');
  
  tables.forEach(table => {
    const headerCells = table.querySelectorAll('thead th');
    const dataCells = table.querySelectorAll('tbody td');
    
    // Add data-label attribute to cells for responsive view
    dataCells.forEach((cell, index) => {
      const headerIndex = index % headerCells.length;
      const headerText = headerCells[headerIndex].textContent.trim();
      cell.setAttribute('data-label', headerText);
    });
  });
  
  // Initialize any charts if they exist
  if (typeof Chart !== 'undefined') {
    // Sales Chart
    const salesChartCanvas = document.getElementById('salesChart');
    
    if (salesChartCanvas) {
      const salesChart = new Chart(salesChartCanvas, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
          datasets: [{
            label: 'Sales',
            data: [12, 19, 3, 5, 2, 3, 20, 33, 23, 12, 33, 10],
            borderColor: '#4361ee',
            backgroundColor: 'rgba(67, 97, 238, 0.1)',
            tension: 0.4,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      });
    }
    
    // Products Chart
    const productsChartCanvas = document.getElementById('productsChart');
    
    if (productsChartCanvas) {
      const productsChart = new Chart(productsChartCanvas, {
        type: 'doughnut',
        data: {
          labels: ['Electronics', 'Clothing', 'Food', 'Books', 'Other'],
          datasets: [{
            data: [30, 25, 15, 20, 10],
            backgroundColor: ['#4361ee', '#2ecc71', '#f39c12', '#e74c3c', '#9b59b6']
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false
        }
      });
    }
  }
  
  // Date Range Picker
  if (typeof flatpickr !== 'undefined') {
    const dateRangePickers = document.querySelectorAll('.date-range-picker');
    
    dateRangePickers.forEach(picker => {
      flatpickr(picker, {
        mode: 'range',
        dateFormat: 'Y-m-d'
      });
    });
  }
  
  // Notification Dropdown
  const notificationToggle = document.querySelector('.notification-toggle');
  
  if (notificationToggle) {
    notificationToggle.addEventListener('click', function(e) {
      e.preventDefault();
      this.nextElementSibling.classList.toggle('show');
      
      // Mark notifications as read
      const unreadBadge = this.querySelector('.header-action-badge');
      if (unreadBadge) {
        unreadBadge.style.display = 'none';
      }
    });
  }
});