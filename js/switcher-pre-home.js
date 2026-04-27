document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded event triggered');
    var savedLang = localStorage.getItem('lang');
    var stylesheet = document.getElementById('style');
    if (savedLang === 'en') {
        console.log('Setting language to English');
        stylesheet.setAttribute('href', 'css/pre-home-en.css');
        document.documentElement.lang = 'en';
        switchText('en');
    } else {
        console.log('Setting language to Arabic');
        stylesheet.setAttribute('href', 'css/pre-home-ar.css');
        document.documentElement.lang = 'ar';
        switchText('ar');
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Set the value of the hidden language input field
    var savedLang = localStorage.getItem('lang') || 'en';
    document.getElementById('lang').value = savedLang;
});

function switchLanguage() {
    var stylesheet = document.getElementById('style');
    if (stylesheet.getAttribute('href') === 'css/pre-home-ar.css') {
        console.log('Switching to English');
        stylesheet.setAttribute('href', 'css/pre-home-en.css');
        document.documentElement.lang = 'en';
        localStorage.setItem('lang', 'en');
        switchText('en');
    } else {
        console.log('Switching to Arabic');
        stylesheet.setAttribute('href', 'css/pre-home-ar.css');
        document.documentElement.lang = 'ar';
        localStorage.setItem('lang', 'ar');
        switchText('ar');
    }
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
