<?php

// add your custom header here.
?>
<script type="text/javascript">
(function() {
    function suppressGenericError() {
        if (typeof appAlert !== 'undefined' && typeof AppLanugage !== 'undefined') {
            var orig = appAlert.error;
            if (typeof orig !== 'function') return false;
            appAlert.error = function(message, options) {
                if (message === AppLanugage.somethingWentWrong) return;
                return orig.apply(appAlert, arguments);
            };
            return true;
        }
        return false;
    }
    function run(attempt) {
        attempt = attempt || 0;
        if (suppressGenericError() || attempt >= 15) return;
        setTimeout(function() { run(attempt + 1); }, 100);
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() { run(0); });
    } else {
        run(0);
    }
})();
</script>