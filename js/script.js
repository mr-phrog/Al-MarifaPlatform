
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

document.querySelectorAll('input[type="number"]').forEach(InputNumber => {
   InputNumber.oninput = () =>{
      if(InputNumber.value.length > InputNumber.maxLength) InputNumber.value = InputNumber.value.slice(0, InputNumber.maxLength);
   }
});

window.onscroll = () =>{
   profile.classList.remove('active');
   searchForm.classList.remove('active');

   if(window.innerWidth < 1200){
      sideBar.classList.remove('active');
      body.classList.remove('active');
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


// Highlight 'all' category when a user go to another page and come back
document.addEventListener("DOMContentLoaded", function() {
   // Get the saved category from localStorage
   const savedCategory = localStorage.getItem("activeCategory");

   // Check if the user is coming from another page
   const referrer = document.referrer;
   const currentPage = window.location.href;

   if (referrer && referrer !== currentPage) {
       // Reset the active category to "all"
       localStorage.setItem("activeCategory", "all");
   }

   // Find the corresponding link and add the active class
   const activeLink = document.querySelector(`.category-link[data-category="${savedCategory}"]`);
   if (activeLink) {
       activeLink.classList.add("active");
   }

   document.querySelectorAll('.category-link').forEach(function(link) {
       link.addEventListener('click', function(event) {
           // Prevent the page from refreshing
           event.preventDefault();

           // Remove active class from all links
           document.querySelectorAll('.category-link').forEach(function(item) {
               item.classList.remove('active');
           });

           // Add active class to the clicked link
           this.classList.add('active');

           // Save the active category to localStorage
           const category = this.getAttribute("data-category");
           localStorage.setItem("activeCategory", category);

           // Simulate navigation (optional, based on your needs)
           window.location.href = this.getAttribute("href");
       });
   });
});

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

// Loader added by Alaa
window.addEventListener("load", function() {
   document.body.classList.add('loaded');
});



// Smart Assistant starts here

const typingForm = document.querySelector(".typing-form");
const chatList = document.querySelector(".chat-list");

let userMessage = null;
let isResponseGenerating = false;

// API configuration
const API_KEY = "Enter_Your_API_KEY"; // You can create your own API Key through https://aistudio.google.com/app/
const API_URL = `https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=${API_KEY}`; // you can use gemini-1.5-flash for faster responses

// Load user-specific chats from the server
const loadUserChats = async () => {
   try {
      const response = await fetch('load_chats.php');
      const data = await response.text();

      if (chatList && data) {
         chatList.innerHTML = data;
         document.body.classList.toggle("hide-g-header", !!data);
         chatList.scrollTo(0, chatList.scrollHeight); // Scroll to the bottom
      }
   } catch (error) {
      console.error("Error loading chats:", error);
   }
};

// Save chat to the server
const saveChatToServer = async (message, messageType) => {
   const formattedMessage = message.replace(/\n/g, '<br>'); // Replace newlines with <br>
   try {
      await fetch('save_chat.php', {
         method: "POST",
         headers: { "Content-Type": "application/json" },
         body: JSON.stringify({ message: formattedMessage, message_type: messageType })
      });
   } catch (error) {
      console.error("Error saving chat:", error);
   }
};

const createMessageElement = (content, ...classes) => {
   const div = document.createElement("div");
   div.classList.add("g-message", ...classes);
   div.innerHTML = content; // Set as HTML to preserve formatting
   return div;
};

// Show typing effect by displaying words one by one
const showTypingEffect = (text, textElement, incomingMessageDiv) => {
   const words = text.split(' ');
   let currentWordIndex = 0;

   const typingInterval = setInterval(() => {
      textElement.innerText += (currentWordIndex === 0 ? '' : ' ') + words[currentWordIndex++];
      incomingMessageDiv.querySelector(".icon").classList.add("hide");

      if (currentWordIndex === words.length) {
         clearInterval(typingInterval);
         isResponseGenerating = false;
         incomingMessageDiv.querySelector(".icon").classList.remove("hide");
         saveChatToServer(textElement.innerText, "incoming"); // Save incoming message to server
      }
      chatList.scrollTo(0, chatList.scrollHeight); // Scroll to the bottom
   }, 75);
};

// Call loadUserChats on page load
window.addEventListener('load', loadUserChats);

// Fetch response from API based on user message
const generateAPIResponse = async (incomingMessageDiv) => {
   const textElement = incomingMessageDiv.querySelector(".text");

   try {
      const response = await fetch(API_URL, {
         method: "POST",
         headers: { "Content-Type": "application/json" },
         body: JSON.stringify({
            contents: [{
               role: "user",
               parts: [{ text: userMessage }]
            }]
         })
      });

      if (!response.ok) {
         throw new Error(response.statusText);
      }

      const data = await response.json();
      if (!response.ok) throw new Error(data.error.message);

      const apiResponse = data?.candidates[0].content.parts[0].text.replace(/\*\*(.*?)\*\*/g, '$1');
      showTypingEffect(apiResponse, textElement, incomingMessageDiv);
   } catch (error) {
      isResponseGenerating = false;
      const currentLang = localStorage.getItem('lang') || 'en';
      const errorMessage = currentLang === 'ar'
         ? "فشل في الاتصال بالإنترنت أو تجاوزت الحد الأقصى لعدد الرسائل اليومية."
         : "Failed to connect to the internet or you have reached the maximum usage of messages per day.";

      // Display error message on the page
      textElement.innerText = errorMessage;
      textElement.classList.add("error");

      // Save error message to the database
      saveChatToServer(errorMessage, "incoming");
   } finally {
      incomingMessageDiv.classList.remove("loading");
   }
};


// Show a loading animation while waiting for API response
const showLoadingAnimation = () => {
   const html = '<div class="g-message-content"> <img src="images/gemini.svg" alt="Gemini Image" class="avatar"> <p class="text"></p> <div class="loading-indicator"> <div class="loading-bar"></div> <div class="loading-bar"></div> <div class="loading-bar"></div> </div> </div> <span onclick="copyMessage(this)" class="icon material-symbols-rounded">content_copy</span>';

   const incomingMessageDiv = createMessageElement(html, "incoming", "loading");
   chatList.appendChild(incomingMessageDiv);

   chatList.scrollTo(0, chatList.scrollHeight); // Scroll to the bottom
   generateAPIResponse(incomingMessageDiv);
}

// Copy message text to the clipboard
const copyMessage = (copyIcon) => {
   const messageText = copyIcon.parentElement.querySelector(".text").innerText;

   navigator.clipboard.writeText(messageText);
   copyIcon.innerText = "done"; // Show tick icon
   setTimeout(() => copyIcon.innerText = "content_copy", 1000); // Revert icon after 1 second
}

// Handle sending outgoing message
const handleOutgoingChat = () => {
   userMessage = typingForm.querySelector(".typing-input").value.trim() || userMessage;
   if (!userMessage || isResponseGenerating) return; // Exit if there is no message

   isResponseGenerating = true;

   const html = '<div class="g-message-content"><img src="' + userImageUrl + '" alt="User Image" class="avatar"><p class="text"></p></div>';

   const outgoingMessageDiv = createMessageElement(html, "outgoing");
   outgoingMessageDiv.querySelector(".text").innerText = userMessage;
   chatList.appendChild(outgoingMessageDiv);

   saveChatToServer(userMessage, "outgoing"); // Save outgoing message to server

   typingForm.reset(); // Clear input field
   chatList.scrollTo(0, chatList.scrollHeight); // Scroll to the bottom
   document.body.classList.add("hide-g-header"); // Hide the g-header once chat starts
   setTimeout(showLoadingAnimation, 500); // Show loading animation after a delay
}

// Set userMessage and handle outgoing chat when a suggestion is clicked
document.addEventListener("DOMContentLoaded", () => {
   const suggestions = document.querySelectorAll(".suggestion-list .suggestion");
   suggestions.forEach(suggestion => {
      suggestion.addEventListener("click", () => {
         userMessage = suggestion.querySelector(".text").innerText;
         handleOutgoingChat();
      });
   });

   const deleteChatButton = document.querySelector("#delete-chat-button");

   if (deleteChatButton) {
      deleteChatButton.addEventListener("click", async () => {
         const currentLang = localStorage.getItem('lang') || 'en';
         const confirmMessage = currentLang === 'ar' 
            ? "هل تريد بالتأكيد حذف جميع الرسائل؟" 
            : "Are you sure you want to delete all messages?";
   
         if (confirm(confirmMessage)) {
            try {
               await fetch('delete_chats.php', { method: "POST" });
               if (chatList) {
                  chatList.innerHTML = ""; // Clear the chat list content
                  document.body.classList.remove("hide-g-header"); // Reset the header visibility
               }
            } catch (error) {
               console.error("Error deleting chats:", error);
            }
         }
      });
   } else {
      console.info("Delete chat button not found. This message is for informational purposes and is not an error.");
   }
   
   if (typingForm) {
      typingForm.addEventListener("submit", (e) => {
         e.preventDefault();
         handleOutgoingChat();
      });
   }
   
});

// Submit the comments through enter key

   // Get the textarea and the submit button elements
   const commentBox = document.getElementById('commentBox');
   const submitButton = document.getElementById('submitButton');

   // Add an event listener for the keydown event
   commentBox.addEventListener('keydown', function(event) {
      // Check if the key pressed is Enter (key code 13)
      if (event.key === 'Enter') {
         // Prevent adding a new line
         event.preventDefault();
         // Trigger a click event on the submit button to submit the form
         submitButton.click();
      }
   });

      // Get the textarea and the submit button elements
      const updateBox = document.getElementById('updateBox');
      const updateButton = document.getElementById('updateButton');

      // Add an event listener for the keydown event
      updateBox.addEventListener('keydown', function(event) {
         // Check if the key pressed is Enter (key code 13)
         if (event.key === 'Enter') {
            // Prevent adding a new line
            event.preventDefault();
            // Trigger a click event on the submit button to submit the form
            updateButton.click();
         }
      });


