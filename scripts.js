// Function to switch between the 5 modules
function showSection(sectionId) {
  // Hide all sections first
  let sections = document.getElementsByClassName('module-section');
  for (let i = 0; i < sections.length; i++) {
    sections[i].style.display = 'none';
  }
  
  // Show the requested section
  document.getElementById(sectionId).style.display = 'block';
}

// Simulate a login (Shows the Dashboard and Navigation Bar)
function login() {
  document.getElementById('nav-menu').style.display = 'block';
  showSection('dashboard');
}

// Simulate a logout (Hides Navigation Bar and goes back to Login)
function logout() {
  document.getElementById('nav-menu').style.display = 'none';
  showSection('login');
}

function order(item) {
  document.getElementById("output").innerText = "Item Punched: " + item;
}