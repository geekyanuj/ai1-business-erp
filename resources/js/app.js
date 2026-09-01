// resources/js/app.js

// ---------------------------
// Core Libraries
// ---------------------------

// jQuery
import $ from 'jquery';
window.$ = window.jQuery = $;



// Axios
import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// ---------------------------
// UI Libraries
// ---------------------------

// Bootstrap JS (includes Popper)
import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

// FontAwesome
import '@fortawesome/fontawesome-free/css/all.min.css';

import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css'; 
window.Choices = Choices; // Expose globally for use in other modules

// Include images that need to be processed by Vite
import '../images/login-banner.webp';