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

#include "php.h"
#include "php_intl.h"

#include <unicode/ucsdet.h>

zend_class_entry *IntlCharsetDetector_ce_ptr;
static zend_object_handlers ucsdet_handlers;

typedef struct _php_ucsdet_object {
	UCharsetDetector *detector;
	zend_string *text;
	zend_string *encoding; /* declared encoding */
	zend_object std;
} php_ucsdet_object;

static inline php_ucsdet_object *ucsdet_get_object(zend_object *object) {
	return (php_ucsdet_object*)( ((char*)object) - ucsdet_handlers.offset );
}

static inline php_ucsdet_object *ucsdet_fetch_object(zval *object) {
	return ucsdet_get_object(Z_OBJ_P(object));
}

/* ----------------------------------------------------------------------- */

/* PPP php_ucsdet_do_set_text */
static zend_bool php_ucsdet_do_set_text(php_ucsdet_object *object, zend_string *text) {
	UErrorCode status = U_ZERO_ERROR;

	text = zend_string_copy(text);
	ucsdet_setText(object->detector, ZSTR_VAL(text), ZSTR_LEN(text), &status);
	if (U_FAILURE(status)) {
		intl_error_set(NULL, status, "Unable to set text", 0);
		return 0;
	}
	if (object->text) {
		zend_string_release(object->text);
	}
	object->text = text;
	return 1;
} /* }}} */

/* PPP php_ucsdet_do_set_declared_encoding */
static zend_bool php_ucsdet_do_set_declared_encoding(php_ucsdet_object *object, zend_string *encoding) {
	UErrorCode status = U_ZERO_ERROR;

	encoding = zend_string_copy(encoding);
	ucsdet_setText(object->detector, ZSTR_VAL(encoding), ZSTR_LEN(encoding), &status);
	if (U_FAILURE(status)) {
		intl_error_set(NULL, status, "Unable to set declared encoding", 0);
		return 0;
	}
	if (object->encoding) {
		zend_string_release(object->encoding);
	}
	object->encoding = encoding;
	return 1;
} /* }}} */

/* {{{ ucsdet_create */
static void php_ucsdet_create(INTERNAL_FUNCTION_PARAMETERS, zend_bool ctor) {
	zend_string *text = NULL;

	if (zend_parse_parameters(ZEND_NUM_ARGS(), "|S!", &text) == FAILURE) {
		return;
	}

	if (!ctor) {
		object_init_ex(return_value, IntlCharsetDetector_ce_ptr);
	}

	if (text) {
		if (php_ucsdet_do_set_text(
		    ucsdet_fetch_object(ctor ? getThis() : return_value),
		    text)) {
			/* success */
			return;
		}

		if (ctor) {
			/* TODO: Throw an exception */
		} else {
			zval_dtor(return_value);
			RETURN_FALSE;
		}
	}
} /* }}} */

/* {{{ proto IntlCharsetDetector ucsdet_create([string $text = NULL]) */
PHP_FUNCTION(ucsdet_create) {
	php_ucsdet_create(INTERNAL_FUNCTION_PARAM_PASSTHRU, 0);
}

/* {{{ proto void IntlCharsetDetector::__construct([string $text = NULL]) */
static PHP_METHOD(IntlCharsetDetector, __construct) {
	php_ucsdet_create(INTERNAL_FUNCTION_PARAM_PASSTHRU, 1);
} /* }}} */

/* {{{ proto bool IntlCharsetDetector::setText(string $text)
       proto bool ucsdet_set_text(IntlCharsetDetector $cs, string $text) */
PHP_FUNCTION(ucsdet_set_text) {
	zval *zobj;
	zend_string *text;

	if (zend_parse_method_parameters(ZEND_NUM_ARGS(), getThis(), "OS",
		&zobj, IntlCharsetDetector_ce_ptr, &text) == FAILURE) {
		return;
	}

	RETURN_BOOL(php_ucsdet_do_set_text(ucsdet_fetch_object(zobj), text));
} /* }}} */

/* {{{ proto bool IntlCharsetDetector::setDeclaredEncoding(string $encoding)
       proto bool ucsdet_set_declared_encoding(IntlCharsetDetector $cs, string $encoding) */
PHP_FUNCTION(ucsdet_set_declared_encoding) {
	zval *zobj;
	zend_string *encoding;

	if (zend_parse_method_parameters(ZEND_NUM_ARGS(), getThis(), "OS",
		&zobj, IntlCharsetDetector_ce_ptr, &encoding) == FAILURE) {
		return;
	}

	RETURN_BOOL(php_ucsdet_do_set_declared_encoding(ucsdet_fetch_object(zobj), encoding));
} /* }}} */

/* {{{ php_ucsdet_charset_match */
#define RETURN_UNDEF() { ZVAL_UNDEF(return_value); return; }
static void php_ucsdet_charset_match(zval *return_value, const UCharsetMatch *match) {
	UErrorCode status;
	const char *name, *language;
	int32_t confidence;

	status = U_ZERO_ERROR;
	name = ucsdet_getName(match, &status);
	if (U_FAILURE(status) || !name) {
		intl_error_set(NULL, status, "ucsdet_detect: Unable to get charset name", 0);
		RETURN_UNDEF();
	}

	status = U_ZERO_ERROR;
	confidence = ucsdet_getConfidence(match, &status);
	if (U_FAILURE(status) || !confidence) {
		intl_error_set(NULL, status, "ucsdet_detect: Unable to get charset confidence", 0);
		RETURN_UNDEF();
	}

	status = U_ZERO_ERROR;
	language = ucsdet_getLanguage(match, &status);
	if (U_FAILURE(status) || !language) {
		intl_error_set(NULL, status, "ucsdet_detect: Unable to get charset language", 0);
		RETURN_UNDEF();
	}

	array_init(return_value);
	add_assoc_string(return_value, "name", (char*)name);
	add_assoc_long(return_value, "confidence", confidence);
	add_assoc_string(return_value, "language", (char*)language);
} /* }}} */

/* {{{ proto CSMatch IntlCharsetDetector::detect()
       proto CSMatch ucsdet_detect(IntlCharsetDetector $cs) */
PHP_FUNCTION(ucsdet_detect) {
	zval *zobj;
	const UCharsetMatch *match;
	UErrorCode status = U_ZERO_ERROR;

	if (zend_parse_method_parameters(ZEND_NUM_ARGS(), getThis(), "O", &zobj) == FAILURE) {
		return;
	}

	match = ucsdet_detect(ucsdet_fetch_object(zobj)->detector, &status);
	if (U_FAILURE(status) || !match) {
		intl_error_set(NULL, status, "ucsdet_detect: Unable to detect charset", 0);
		RETURN_FALSE;
	}

	php_ucsdet_charset_match(return_value, match);
} /* }}} */

/* {{{ proto array<CSMatch> IntlCharsetDetector::detect()
       proto array<CSMatch> ucsdet_detect(IntlCharsetDetector $cs) */
PHP_FUNCTION(ucsdet_detect_all) {
	zval *zobj;
	const UCharsetMatch **matches;
	int32_t match_count = 0, i;
	UErrorCode status = U_ZERO_ERROR;

	if (zend_parse_method_parameters(ZEND_NUM_ARGS(), getThis(), "O", &zobj) == FAILURE) {
		return;
	}

	matches = ucsdet_detectAll(ucsdet_fetch_object(zobj)->detector, &match_count, &status);
	if (U_FAILURE(status) || !matches) {
		intl_error_set(NULL, status, "ucsdet_detect_all: Unable to detect charset", 0);
		RETURN_FALSE;
	}

	array_init(return_value);
	for (i = 0; i < match_count; ++i) {
		zval match;
		php_ucsdet_charset_match(&match, matches[i]);
		if (Z_TYPE(match) == IS_UNDEF) {
			zval_dtor(return_value);
			RETURN_FALSE;
		}
		add_next_index_zval(return_value, &match);
	}
} /* }}} */

/* {{{ proto array<string> IntlCharsetDetector::getAllDetectableCharsets()
       proto array<string> ucsdet_get_all_detectable_charsets(IntlCharsetTetector $cs) */
PHP_FUNCTION(ucsdet_get_all_detectable_charsets) {
	zval *zobj;
	UEnumeration *charsets;
	UErrorCode status = U_ZERO_ERROR;
	const char *elem;
	int32_t elem_len;

	if (zend_parse_method_parameters(ZEND_NUM_ARGS(), getThis(), "O", &zobj) == FAILURE) {
		return;
	}

	charsets = ucsdet_getAllDetectableCharsets(ucsdet_fetch_object(zobj)->detector, &status);
	if (!charsets || U_FAILURE(status)) {
		intl_error_set(NULL, status, "ucsdet_get_all_detectable_charsets: Unable to enum charsets", 0);
		RETURN_FALSE;
	}

	array_init(return_value);
	status = U_ZERO_ERROR;
	while ((elem = uenum_next(charsets, &elem_len, &status))) {
		if (U_FAILURE(status)) {
			intl_error_set(NULL, status, "ucsdet_get_all_detectable_charsets: Error while enumerating charsets", 0);
			zval_dtor(return_value);
			RETVAL_FALSE;
			break;
		}
		add_next_index_stringl(return_value, elem, elem_len);
		status = U_ZERO_ERROR;
	}
	uenum_close(charsets);
} /* }}} */

/* {{{ ptoto bool IntlCharsetDetector::isInputFilterEnabled()
       proto bool ucsdet_is_input_filter_enabled(IntlCharsetDetector $cs) */
PHP_FUNCTION(ucsdet_is_input_filter_enabled) {
	zval *zobj;

	if (zend_parse_method_parameters(ZEND_NUM_ARGS(), getThis(), "O", &zobj) == FAILURE) {
		return;
	}

	RETURN_BOOL((zend_bool)ucsdet_isInputFilterEnabled(ucsdet_fetch_object(zobj)->detector));
} /* }}} */

/* {{{ proto bool IntlCharsetDetected::enableInputFilter(bool $enable)
       proto bool ucsdet_enable_input_filter(IntlCharsetDetector $cs, bool $enable) */
PHP_FUNCTION(ucsdet_enable_input_filter) {
	zval *zobj;
	zend_bool enable;

	if (zend_parse_method_parameters(ZEND_NUM_ARGS(), getThis(), "Ob", &zobj, &enable) == FAILURE) {
		return;
	}

	RETURN_BOOL((zend_bool)ucsdet_enableInputFilter(ucsdet_fetch_object(zobj)->detector, (UBool)enable));
} /* }}} */

/* ----------------------------------------------------------------------- */

ZEND_BEGIN_ARG_INFO_EX( ainfo_ucsdet_set_text, 0, ZEND_RETURN_VALUE, 0 )
	ZEND_ARG_INFO( 0, text )
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX( ainfo_ucsdet_set_declared_encoding, 0, ZEND_RETURN_VALUE, 1 )
	ZEND_ARG_INFO( 0, encoding )
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX( ainfo_ucsdet_enable_input_filter, 0, ZEND_RETURN_VALUE, 1 )
	ZEND_ARG_INFO( 1, enable )
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX( ainfo_ucsdet_0args, 0, ZEND_RETURN_VALUE, 0)
ZEND_END_ARG_INFO()

static zend_function_entry ucsdet_methods[] = {
	PHP_ME(IntlCharsetDetector, __construct, ainfo_ucsdet_set_text, ZEND_ACC_PUBLIC|ZEND_ACC_CTOR)

	PHP_ME_MAPPING(setText, ucsdet_set_text, ainfo_ucsdet_set_text, ZEND_ACC_PUBLIC)
	PHP_ME_MAPPING(setDeclaredEncoding, ucsdet_set_declared_encoding, ainfo_ucsdet_set_declared_encoding, ZEND_ACC_PUBLIC)

	PHP_ME_MAPPING(detect, ucsdet_detect, ainfo_ucsdet_0args, ZEND_ACC_PUBLIC)
	PHP_ME_MAPPING(detectAll, ucsdet_detect_all, ainfo_ucsdet_0args, ZEND_ACC_PUBLIC)

	PHP_ME_MAPPING(isInputFilterEnabled, ucsdet_is_input_filter_enabled, ainfo_ucsdet_0args, ZEND_ACC_PUBLIC)
	PHP_ME_MAPPING(enableInputFilter, ucsdet_enable_input_filter, ainfo_ucsdet_enable_input_filter, ZEND_ACC_PUBLIC)

	PHP_FE_END
};

/* ----------------------------------------------------------------------- */

static zend_object *ucsdet_ctor(zend_class_entry *ce) {
        php_ucsdet_object *ucsobj =
		emalloc(sizeof(php_ucsdet_object) + zend_object_properties_size(ce));
	zend_object *object = &(ucsobj->std);
	UErrorCode status = U_ZERO_ERROR;

	ucsobj->detector = ucsdet_open(&status);
	if (U_FAILURE(status)) {
		/* TODO: Throw exception */
	}
	ucsobj->text = NULL;
	ucsobj->encoding = NULL;

        zend_object_std_init(object, ce);
        object->handlers = &ucsdet_handlers;
        return object;
}

static void ucsdet_dtor(zend_object *object) {
	php_ucsdet_object *ucsobj = ucsdet_get_object(object);
	if (ucsobj->detector) {
		ucsdet_close(ucsobj->detector);
		ucsobj->detector = NULL;
	}
	if (ucsobj->text) {
		zend_string_release(ucsobj->text);
		ucsobj->text = NULL;
	}
	if (ucsobj->encoding) {
		zend_string_release(ucsobj->encoding);
		ucsobj->encoding = NULL;
	}
}

/* ----------------------------------------------------------------------- */

int php_ucsdet_minit(INIT_FUNC_ARGS) {
	zend_class_entry ce;

	INIT_CLASS_ENTRY(ce, "IntlCharsetDetector", ucsdet_methods);
	IntlCharsetDetector_ce_ptr = zend_register_internal_class(&ce);
	IntlCharsetDetector_ce_ptr->create_object = ucsdet_ctor;

	memcpy(&ucsdet_handlers, zend_get_std_object_handlers(), sizeof(zend_object_handlers));
	ucsdet_handlers.offset = XtOffsetOf(php_ucsdet_object, std);
	ucsdet_handlers.dtor_obj = ucsdet_dtor;
	ucsdet_handlers.clone_obj = NULL;

	return SUCCESS;
}

/*
 * Local variables:
 * tab-width: 4
 * c-basic-offset: 4
 * End:
 * vim600: fdm=marker
 * vim: noet sw=4 ts=4
 */
