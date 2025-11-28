/**
 * @file
 * Defines Javascript behavior to clean up EUCC cookies that
 * might be hanging around after EUCC domain setting was changed
 * from www.nisra.gov.uk to .nisra.gov.uk (to make EUCC cookies
 * available to subdomains).
 */

(function (Drupal, once, drupalSettings) {

  Drupal.behaviors.euccCleanupWwwDomainCookies = {
    attach: function (context, settings) {

      once('euccCleanupWwwDomainCookies', 'html', context).forEach(function () {

        const cleanupDomain = "www.nisra.gov.uk";
        const cleanupFlag = "eucc-cleanup-done";

        // Skip if current hostname does not match cleanupDomain.
        if (window.location.hostname !== cleanupDomain) {
          return;
        }

        // Skip if cleanup already ran.
        if (document.cookie.includes(cleanupFlag)) {
          return;
        }

        // Skip if EUCC cookies do not exist.
        const hasEuccCookies =
          document.cookie.includes("cookie-agreed=") ||
          document.cookie.includes("cookie-agreed-version=");

        if (!hasEuccCookies) {
          return;
        }

        // Expire cookies.
        ["cookie-agreed", "cookie-agreed-version"].forEach((name) => {
          document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/`;
          document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/; domain=${cleanupDomain}`;
          console.log(`Expired ${name} for domain ${cleanupDomain}`);
        });

        // Set flag so it runs once
        document.cookie = `${cleanupFlag}=1; path=/; max-age=31536000`;
      });
    }
  };

})(Drupal, once, drupalSettings);
