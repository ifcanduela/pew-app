<?php

# session_start() is called because PHPUnit sends headers before running tests,
# breaking the Session class constructor
ob_start();
