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

// Additional positioning fix after Tawk.to loads
Tawk_API.onLoad = function(){
    // Force Tawk.to positioning to appear above AI Assistant
    setTimeout(function() {
        var tawkWidget = document.getElementById('tawkchat-minified-container');
        if (tawkWidget) {
            tawkWidget.style.bottom = '140px !important';
            tawkWidget.style.zIndex = '10001 !important';
        }

        var tawkContainer = document.getElementById('tawkchat-container');
        if (tawkContainer) {
            tawkContainer.style.bottom = '140px !important';
            tawkContainer.style.zIndex = '10001 !important';
        }
    }, 1000);
};
</script>

<style>
/* Force Tawk.to positioning above AI Assistant */
#tawkchat-minified-container {
    bottom: 140px !important;
    right: 20px !important;
    z-index: 10001 !important;
}

#tawkchat-container {
    bottom: 140px !important;
    right: 20px !important;
    z-index: 10001 !important;
}

/* Mobile responsive for Tawk.to */
@media (max-width: 768px) {
    #tawkchat-minified-container {
        bottom: 120px !important;
        right: 15px !important;
    }

    #tawkchat-container {
        bottom: 120px !important;
        right: 15px !important;
    }
}
</style>
<!--End of Tawk.to Script-->
