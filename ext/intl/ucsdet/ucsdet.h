/*
   +----------------------------------------------------------------------+
   | PHP Version 7                                                        |
   +----------------------------------------------------------------------+
   | Copyright (c) 2009 The PHP Group                                     |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Author: Sara Golemon <pollita@php.net>                               |
   +----------------------------------------------------------------------+
 */
/* $Id$ */

#ifdef HAVE_CONFIG_H
# include "config.h"
#endif

#include <php.h>

extern zend_class_entry *IntlCharsetDetector_ce_ptr;

PHP_FUNCTION(ucsdet_create);
PHP_FUNCTION(ucsdet_set_text);
PHP_FUNCTION(ucsdet_set_declared_encoding);

PHP_FUNCTION(ucsdet_detect);
PHP_FUNCTION(ucsdet_detect_all);

PHP_FUNCTION(ucsdet_get_all_detectable_charsets);

PHP_FUNCTION(ucsdet_is_input_filter_enabled);
PHP_FUNCTION(ucsdet_enable_input_filter);

int php_ucsdet_minit(INIT_FUNC_ARGS);

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: fdm=marker
 * vim: noet sw=4 ts=4
 */
