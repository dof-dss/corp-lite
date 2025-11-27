/**
 * @file
 * Defines Javascript behavior to clean up EUCC cookies that
 * might be hanging around after EUCC domain setting was changed
 * from www.nisra.gov.uk to .nisra.gov.uk (to make EUCC cookies
 * available to subdomains).
 */

(function (Drupal, once, drupalSettings) {

  Drupal.behaviors.euccCleanupLegacyDomainCookies = {
    attach: function (context, settings) {

      once('euccCleanupLegacyDomainCookies', 'html', context).forEach(function () {

        const currentHost = window.location.hostname;
        const legacyDomain = drupalSettings.nisra_common.eucc_cleanup.legacyDomain;
        const euccDomain = drupalSettings.nisra_common.eucc_cleanup.euccDomain;

        const cleanupFlag = "eucc-cleanup-done";

        // Skip if cleanup already ran for this user.
        if (document.cookie.includes(cleanupFlag)) {
          return;
        }

        // Skip if we are not on the legacy domain.
        if (currentHost !== legacyDomain) {
          return;
        }

        // Skip if EUCC domain equals legacy domain.
        if (euccDomain && euccDomain === legacyDomain) {
          console.log('EUCC cleanup: current domain equals legacy domain, skipping.');
          return;
        }

        // Skip if EUCC cookies are not set.
        const hasEuccCookies =
          document.cookie.includes("cookie-agreed=") ||
          document.cookie.includes("cookie-agreed-version=");

        if (!hasEuccCookies) {
          return;
        }

        // Expire legacy domain cookies.
        ["cookie-agreed", "cookie-agreed-version"].forEach((name) => {
          document.cookie =
            `${name}=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/; domain=${legacyDomain}`;
          console.log(`EUCC cleanup: expired legacy cookie '${name}' on ${legacyDomain}`);
        });

        // Set flag so cleanup only runs once per user.
        document.cookie = `${cleanupFlag}=1; path=/; max-age=31536000`;
      });
    }
  };

})(Drupal, once, drupalSettings);
