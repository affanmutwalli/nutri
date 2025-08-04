<!--Start of Tawk.to Script-->
<script type="text/javascript">
var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();

// Configure Tawk.to positioning to appear above AI Assistant
Tawk_API.customStyle = {
    visibility : {
        desktop : {
            position : 'br',
            xOffset : 20,
            yOffset : 140  // Position Tawk.to higher (140px from bottom)
        },
        mobile : {
            position : 'br',
            xOffset : 15,
            yOffset : 120
        }
    }
};

(function(){
var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
s1.async=true;
s1.src='https://embed.tawk.to/687cd82326d3e2191999a21f/1j0jqoipl';
s1.charset='UTF-8';
s1.setAttribute('crossorigin','*');
s0.parentNode.insertBefore(s1,s0);
})();

// Hide Tawk.to widget after it loads
Tawk_API.onLoad = function(){
    // Hide all Tawk.to visible elements
    setTimeout(function() {
        // Hide the minimized chat button
        var tawkWidget = document.getElementById('tawkchat-minified-container');
        if (tawkWidget) {
            tawkWidget.style.display = 'none';
            tawkWidget.style.visibility = 'hidden';
            tawkWidget.style.opacity = '0';
        }

        // Hide any other Tawk.to elements
        var tawkElements = document.querySelectorAll('[id*="tawk"], [class*="tawk"], iframe[src*="tawk.to"]');
        tawkElements.forEach(function(element) {
            if (!element.classList.contains('tawk-open')) {
                element.style.display = 'none';
                element.style.visibility = 'hidden';
                element.style.opacity = '0';
            }
        });
    }, 1000);

    // Also hide after a longer delay to catch any delayed elements
    setTimeout(function() {
        var tawkWidget = document.getElementById('tawkchat-minified-container');
        if (tawkWidget) {
            tawkWidget.style.display = 'none';
            tawkWidget.style.visibility = 'hidden';
        }
    }, 3000);
};
</script>

<style>
/* Hide all Tawk.to visible elements while keeping functionality */
#tawkchat-minified-container,
#tawkchat-container-minimized,
.tawk-min-container,
.tawk-button,
div[id*="tawk"],
iframe[src*="tawk.to"] {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
}

/* Hide any Tawk.to widget elements */
[class*="tawk"] {
    display: none !important;
    visibility: hidden !important;
}

/* Keep chat window functional when opened programmatically */
#tawkchat-container.tawk-open {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    bottom: 140px !important;
    right: 20px !important;
    z-index: 10001 !important;
}

/* Mobile responsive for opened chat */
@media (max-width: 768px) {
    #tawkchat-container.tawk-open {
        bottom: 120px !important;
        right: 15px !important;
    }
}
</style>
<!--End of Tawk.to Script-->
