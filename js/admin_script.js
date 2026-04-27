let body = document.body;

let profile = document.querySelector('.header .flex .profile');

document.querySelector('#user-btn').onclick = () =>{
   profile.classList.toggle('active');
   searchForm.classList.remove('active');
}

let searchForm = document.querySelector('.header .flex .search-form');

document.querySelector('#search-btn').onclick = () =>{
   searchForm.classList.toggle('active');
   profile.classList.remove('active');
}

let sideBar = document.querySelector('.side-bar');

document.querySelector('#menu-btn').onclick = () =>{
   sideBar.classList.toggle('active');
   body.classList.toggle('active');
}

document.querySelector('.side-bar .close-side-bar').onclick = () =>{
   sideBar.classList.remove('active');
   body.classList.remove('active');
}

window.onscroll = () =>{
   profile.classList.remove('active');
   searchForm.classList.remove('active');

   if(window.innerWidth < 1200){
      sideBar.classList.remove('active');
      body.classList.remove('active');
   }

}
let toggleBtn = document.querySelector('#toggle-btn');
let darkMode = localStorage.getItem('dark-mode');
let header = document.querySelector('.header');

const enableDarkMode = () => {
   toggleBtn.classList.replace('fa-sun', 'fa-moon');
   document.body.classList.add('dark');
   header.classList.add('header-dark');
   header.classList.remove('header-light');
   localStorage.setItem('dark-mode', 'enabled');
   }

const disableDarkMode = () => {
   toggleBtn.classList.replace('fa-moon', 'fa-sun');
   document.body.classList.remove('dark');
   header.classList.add('header-light');
   header.classList.remove('header-dark');
   localStorage.setItem('dark-mode', 'disabled');
   }

if (darkMode === 'enabled') {
   enableDarkMode();
   } else {
   disableDarkMode();
   }

toggleBtn.onclick = (e) => {
   let darkMode = localStorage.getItem('dark-mode');
   if (darkMode === 'disabled') {
      enableDarkMode();
   } else {
      disableDarkMode();
   }
}


// JavaScript to add the 'active' class based on the current URL
window.addEventListener('DOMContentLoaded', (event) => {
   // Get all navbar links
   const navLinks = document.querySelectorAll('.navbar a');
   
   // Get the current page's URL (e.g., 'home.php', 'courses.php', etc.)
   const currentPage = window.location.pathname.split('/').pop();

   // Loop through the links and add the 'active' class to the matching link
   navLinks.forEach(link => {
       // Get the href attribute of each link (e.g., 'home.php', etc.)
       const linkHref = link.getAttribute('href');
       
       // If the link's href matches the current page, add the 'active' class
       if (linkHref === currentPage) {
           link.classList.add('active');
       } else {
           // Remove 'active' class from other links to ensure only one is active
           link.classList.remove('active');
       }
   });
});

// Loader added by Alaa
window.addEventListener("load", function() {
   document.body.classList.add('loaded');
});