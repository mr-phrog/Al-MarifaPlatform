document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded event triggered');
    var savedLang = localStorage.getItem('lang');
    var stylesheet = document.getElementById('style');
    if (savedLang === 'en') {
        console.log('Setting language to English');
        stylesheet.setAttribute('href', 'css/style-en.css');
        document.documentElement.lang = 'en';
        switchText('en');
    } else {
        console.log('Setting language to Arabic');
        stylesheet.setAttribute('href', 'css/style-ar.css');
        document.documentElement.lang = 'ar';
        switchText('ar');
    }

    // Set the value of the hidden language input field
    // var langInput = document.getElementById('lang');
    // if (langInput) {
    //     langInput.value = savedLang || 'en';
    // } else {
    //     console.warn("Language input element not found.");
    // }

    // // Attach the confirmDelete function to the button (This is was a bug so I comment it 25/8/2024)
    // const deleteButton = document.querySelector('.inline-delete-btn');
    // if (deleteButton) {
    //     deleteButton.addEventListener('click', function(event) {
    //         confirmAction(event, 'delete_comment');
    //     });
    // }

    // Attach the confirmLogout function to the logout link (was a bug duplicating logout message! )
    // const logoutLink = document.querySelector('.delete-btn');
    // if (logoutLink) {
    //     logoutLink.addEventListener('click', function(event) {
    //         confirmAction(event, 'logout');
    //     });
    // }
});

function switchLanguage() {
    var stylesheet = document.getElementById('style');
    if (stylesheet.getAttribute('href') === 'css/style-ar.css') {
        console.log('Switching to English');
        stylesheet.setAttribute('href', 'css/style-en.css');
        document.documentElement.lang = 'en';
        localStorage.setItem('lang', 'en');
        switchText('en');
    } else {
        console.log('Switching to Arabic');
        stylesheet.setAttribute('href', 'css/style-ar.css');
        document.documentElement.lang = 'ar';
        localStorage.setItem('lang', 'ar');
        switchText('ar');
    }
    // Trigger the fade-out and fade-in effect "Added by Alaa 26/8/2024"
    document.body.classList.add('language-transition');
    setTimeout(() => {
        document.body.classList.remove('language-transition');
    }, 500); // Adjust the timeout to match the transition duration
}

function switchText(lang) {
    console.log('Switching text to:', lang);

    // Update text for elements with data-en and data-ar attributes
    document.querySelectorAll('[data-en]').forEach(function(element) {
        if (element.tagName.toLowerCase() === 'input' && element.type === 'submit') {
            // Update button text
            element.value = lang === 'en' ? element.getAttribute('data-en') : element.getAttribute('data-ar');
        } else if (element.tagName.toLowerCase() !== 'input' && element.tagName.toLowerCase() !== 'textarea') {
            // Update other text elements
            const newText = lang === 'en' ? element.getAttribute('data-en') : element.getAttribute('data-ar');
            const span = element.querySelector('span');
            const link = element.querySelector('a');
            if (span) {
                element.innerHTML = `${newText} : <span>${span.innerHTML}</span>`;
            } else if (link) {
                const linkText = lang === 'en' ? link.getAttribute('data-en') : link.getAttribute('data-ar');
                element.innerHTML = `${newText} <a href="${link.href}" data-ar="${link.getAttribute('data-ar')}" data-en="${link.getAttribute('data-en')}">${linkText}</a>`;
            } else {
                element.textContent = newText;
            }
        }
    });

    // Update placeholders for input elements
    document.querySelectorAll('input[data-en]').forEach(function(input) {
        input.placeholder = lang === 'en' ? input.getAttribute('data-en') : input.getAttribute('data-ar');
    });

    // Update placeholders for textarea elements
    document.querySelectorAll('textarea[data-en]').forEach(function(textarea) {
        textarea.placeholder = lang === 'en' ? textarea.getAttribute('data-en') : textarea.getAttribute('data-ar');
    });
}

// Function to handle the confirmation message
function confirmAction(event, actionType) {
    const currentLang = localStorage.getItem('lang') || 'en';
    const messages = {
        delete_comment: {
            ar: 'هل تريد حذف هذا التعليق؟',
            en: 'delete this comment?'
        },
        logout: {
            ar: 'هل تريد تسجيل الخروج من هذا الموقع؟',
            en: 'logout from this website?'
        },
        remove_like: {
            ar:'هل تريد إزالة الاعجاب من الفيديو؟',
            en:'Do you want to remove like from this video?'
        }
    };
    const confirmMessage = messages[actionType][currentLang];
    if (!confirm(confirmMessage)) {
        event.preventDefault();
    }
}
