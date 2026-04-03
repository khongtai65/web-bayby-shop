<?php
/**
 * Device Detection Helper
 */

function isMobileDevice() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $mobilePatterns = array(
        '/iPhone/',
        '/iPad/',
        '/iPod/',
        '/Android/',
        '/BlackBerry/',
        '/WebOS/',
        '/Opera Mini/',
        '/Mobile/',
        '/Tablet/',
        '/Windows Phone/',
        '/IEMobile/',
        '/Kindle/',
        '/Silk-Accelerated/'
    );
    
    foreach ($mobilePatterns as $pattern) {
        if (preg_match($pattern, $userAgent)) {
            return true;
        }
    }
    
    // Check for tablet size queries (if cookie is set for mobile view)
    if (isset($_COOKIE['force_mobile']) && $_COOKIE['force_mobile'] === 'true') {
        return true;
    }
    
    return false;
}

function getCSSFile() {
    return isMobileDevice() ? 'css-kieu-dang/mobile.css' : 'css-kieu-dang/kieu-dang.css';
}

function getDeviceType() {
    return isMobileDevice() ? 'mobile' : 'desktop';
}
?>
