<?php
// Admin login now happens on the main site's login.php (with a
// "Log in as: Customer / Admin" selector), so this just redirects there.
header('Location: ../login.php');
exit;
