//import './styles/app_core.scss';

require('bootstrap');
const $ = require('jquery');



// intégration du theme
require('./styles/themeHyper/js/vendorCore/bootstrap.bundle.js');
require('./styles/themeHyper/js/vendor/jquery-jvectormap-1.2.2.min.js');
require('./styles/themeHyper/js/vendor/jquery-jvectormap-world-mill-en.js');
require('./styles/themeHyper/js/vendorCore/daterangepicker.js');
require('./styles/themeHyper/js/vendorCore/select2.min.js');
require('./styles/themeHyper/js/vendor/jquery.dataTables.min.js');
require('./styles/themeHyper/js/vendor/dataTables.bootstrap5.js');
require('./styles/themeHyper/js/vendor/dataTables.responsive.min.js');
require('./styles/themeHyper/js/vendor/responsive.bootstrap5.min.js');

$(document).ready(function() {
    $('[data-toggle="popover"]').popover();
});