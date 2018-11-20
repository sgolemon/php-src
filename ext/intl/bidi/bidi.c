/*
   +----------------------------------------------------------------------+
   | PHP Version 7                                                        |
   +----------------------------------------------------------------------+
   | This source file is subject to version 3.01 of the PHP license,      |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.php.net/license/3_01.txt                                  |
   | If you did not receive a copy of the PHP license and are unable to   |
   | obtain it through the world-wide-web, please send a note to          |
   | license@php.net so we can mail you a copy immediately.               |
   +----------------------------------------------------------------------+
   | Authors: Sara Golemon <pollita@php.net>                              |
   +----------------------------------------------------------------------+
 */

#include "php_bidi.h"
#include "zend_exceptions.h"

#include "../intl_error.h"
#include "../intl_common.h"
#include "../intl_convert.h"

#include <unicode/ubidi.h>
#include <unicode/utf8.h>

zend_class_entry *php_intl_bidi_ce;
static zend_object_handlers bidi_object_handlers;

typedef struct _php_intl_bidi_object {
	UBiDi *bidi;
	UBiDiLevel *embeddingLevels;
	UChar *prologue, *text, *epilogue;
	intl_error error;
	zend_object std;
} php_intl_bidi_object;

static inline php_intl_bidi_object *bidi_object_from_zend_object(zend_object *obj) {
	return ((php_intl_bidi_object*)(obj + 1)) - 1;
}

static inline zend_object *bidi_object_to_zend_object(php_intl_bidi_object *obj) {
	return ((zend_object*)(obj + 1)) - 1;
}

#define THROW_UFAILURE(obj, fname, error) \
	php_intl_bidi_throw_failure(obj, error, \
	                       "IntlBidi::" fname "() returned error " ZEND_LONG_FMT ": %s", \
	                       (zend_long)error, u_errorName(error))

/* {{{ php_intl_bidi_throw_failure */
static inline void php_intl_bidi_throw_failure(php_intl_bidi_object *objval,
                                          UErrorCode error, const char *format, ...) {
	intl_error *err = objval ? &(objval->error) : NULL;
	char message[1024];
	va_list vargs;

	va_start(vargs, format);
	vsnprintf(message, sizeof(message), format, vargs);
	va_end(vargs);

	intl_errors_set(err, error, message, 1);
	zend_throw_exception(zend_ce_exception, message, (zend_long)error);
}
/* }}} */

static inline void php_intl_bidi_invokeConstruction(zval * instance, zend_long maxRunCount, zend_long maxLength) {
	UErrorCode error;
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(instance));

	if (PG(memory_limit) > 0) {
		if (maxLength == 0) {
			maxLength = PG(memory_limit) / 2;
		} else if (maxLength > PG(memory_limit)) {
			php_intl_bidi_throw_failure(NULL, U_ILLEGAL_ARGUMENT_ERROR,
				"IntlBidi::__construct() given maxLength greater than memory_limit");
			return;
		}
	}

	error = U_ZERO_ERROR;

	objval->bidi = ubidi_openSized(maxLength, maxRunCount, &error);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(NULL, "__construct", error);
		return;
	}
}

/* {{{ proto void IntlBidi::__construct([int $maxLength = 0, [int $maxRunCount = 0]]) */
ZEND_BEGIN_ARG_INFO_EX(bidi_ctor_arginfo, 0, ZEND_RETURN_VALUE, 0)
	ZEND_ARG_TYPE_INFO(0, maxLength, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, maxRunCount, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, __construct) {
	zend_long maxLength = 0, maxRunCount = 0;

	ZEND_PARSE_PARAMETERS_START(0, 2)
		Z_PARAM_OPTIONAL
		Z_PARAM_LONG(maxLength)
		Z_PARAM_LONG(maxRunCount)
	ZEND_PARSE_PARAMETERS_END();

	if (maxRunCount < 0) {
		php_intl_bidi_throw_failure(NULL, U_ILLEGAL_ARGUMENT_ERROR,
			"IntlBidi::__construct() expects maxRunCount to be a non-negative value");
		return;
	}

	if (maxLength < 0) {
		php_intl_bidi_throw_failure(NULL, U_ILLEGAL_ARGUMENT_ERROR,
			"IntlBidi::__construct() expects maxLength to be a non-negative value");
		return;
	}

	php_intl_bidi_invokeConstruction(getThis(), maxLength, maxRunCount);
}
/* }}} */

/* {{{ proto self IntlBidi::setInverse(bool inverse) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_setinverse_arginfo, ZEND_RETURN_VALUE, 1, IS_OBJECT, 0)
	ZEND_ARG_TYPE_INFO(0, inverse, _IS_BOOL, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, setInverse) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_bool inverse;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_BOOL(inverse)
	ZEND_PARSE_PARAMETERS_END();

	ubidi_setInverse(objval->bidi, (UBool)inverse);
	RETURN_ZVAL(getThis(), 1, 0);
}
/* }}} */

/* {{{ proto bool IntlBidi::isInverse() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_isinverse_arginfo, ZEND_RETURN_VALUE, 0, _IS_BOOL, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, isInverse) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	if (zend_parse_parameters_none_throw() == FAILURE) { return; }
	RETURN_BOOL(ubidi_isInverse(objval->bidi));
}
/* }}} */

/* {{{ proto self IntlBidi::orderParagraphsLTR(bool ltr) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_orderparaltr_arginfo, ZEND_RETURN_VALUE, 1, IS_OBJECT, 0)
	ZEND_ARG_TYPE_INFO(0, ltr, _IS_BOOL, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, orderParagraphsLTR) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_bool ltr;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_BOOL(ltr)
	ZEND_PARSE_PARAMETERS_END();

	ubidi_orderParagraphsLTR(objval->bidi, (UBool)ltr);
	RETURN_ZVAL(getThis(), 1, 0);
}
/* }}} */

/* {{{ proto bool IntlBidi::isOrderParagraphsLTR() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_isorderparaltr_arginfo, ZEND_RETURN_VALUE, 0, _IS_BOOL, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, isOrderParagraphsLTR) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	if (zend_parse_parameters_none_throw() == FAILURE) { return; }
	RETURN_BOOL(ubidi_isOrderParagraphsLTR(objval->bidi));
}
/* }}} */

/* {{{ proto self IntlBidi::setReorderingMode(int mode) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_setreordermode_arginfo, ZEND_RETURN_VALUE, 1, IS_OBJECT, 0)
	ZEND_ARG_TYPE_INFO(0, mode, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, setReorderingMode) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_long mode;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_LONG(mode)
	ZEND_PARSE_PARAMETERS_END();

	ubidi_setReorderingMode(objval->bidi, (UBiDiReorderingMode)mode);
	RETURN_ZVAL(getThis(), 1, 0);
}
/* }}} */

/* {{{ proto long IntlBidi::getReorderingMode() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getreordermode_arginfo, ZEND_RETURN_VALUE, 0, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getReorderingMode) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	if (zend_parse_parameters_none_throw() == FAILURE) { return; }
	RETURN_LONG(ubidi_getReorderingMode(objval->bidi));
}
/* }}} */

/* {{{ proto self IntlBidi::setReorderingOptions(int opts) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_setreorderopts_arginfo, ZEND_RETURN_VALUE, 1, IS_OBJECT, 0)
	ZEND_ARG_TYPE_INFO(0, opts, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, setReorderingOptions) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_long opts;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_LONG(opts)
	ZEND_PARSE_PARAMETERS_END();

	ubidi_setReorderingOptions(objval->bidi, (UBiDiReorderingOption)opts);
	RETURN_ZVAL(getThis(), 1, 0);
}
/* }}} */

/* {{{ proto long IntlBidi::getReorderingOptions() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getreorderopts_arginfo, ZEND_RETURN_VALUE, 0, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getReorderingOptions) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	if (zend_parse_parameters_none_throw() == FAILURE) { return; }
	RETURN_LONG(ubidi_getReorderingOptions(objval->bidi));
}
/* }}} */

#if ((U_ICU_VERSION_MAJOR_NUM * 10) + U_ICU_VERSION_MINOR_NUM) >= 48
/* {{{ proto self IntlBidi::setContext([string $prologue = ''[, string $epilogue = '']]) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_setctx_arginfo, ZEND_RETURN_VALUE, 0, IS_OBJECT, 0)
	ZEND_ARG_TYPE_INFO(0, prologue, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, epilogue, IS_STRING, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, setContext) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_string *prologue = NULL, *epilogue = NULL;
	UChar *uprologue = NULL, *uepilogue = NULL;
	int32_t uprologue_len = 0, uepilogue_len = 0;

	ZEND_PARSE_PARAMETERS_START(0, 2)
		Z_PARAM_OPTIONAL
		Z_PARAM_STR_EX(prologue, 0, 0)
		Z_PARAM_STR_EX(epilogue, 0, 0)
	ZEND_PARSE_PARAMETERS_END();

	if (prologue && ZSTR_LEN(prologue)) {
		UErrorCode error = U_ZERO_ERROR;
		intl_convert_utf8_to_utf16(&uprologue, &uprologue_len, ZSTR_VAL(prologue), ZSTR_LEN(prologue), &error);
		if (U_FAILURE(error)) {
			THROW_UFAILURE(objval, "setContext", error);
			goto setContext_cleanup;
		}
	}

	if (epilogue && ZSTR_LEN(epilogue)) {
		UErrorCode error = U_ZERO_ERROR;
		intl_convert_utf8_to_utf16(&uepilogue, &uepilogue_len, ZSTR_VAL(epilogue), ZSTR_LEN(epilogue), &error);
		if (U_FAILURE(error)) {
			THROW_UFAILURE(objval, "setContext", error);
			goto setContext_cleanup;
		}
	}

	{
		UErrorCode error = U_ZERO_ERROR;
		ubidi_setContext(objval->bidi, uprologue, uprologue_len, uepilogue, uepilogue_len, &error);
		if (U_FAILURE(error)) {
			THROW_UFAILURE(objval, "setContext", error);
			goto setContext_cleanup;
		}
	}

	/* Preserve prologue/epilogue as set for later use */
	if (objval->prologue) {
		efree(objval->prologue);
	}
	objval->prologue = uprologue;

	if (objval->epilogue) {
		efree(objval->epilogue);
	}
	objval->epilogue = uepilogue;

	RETURN_ZVAL(getThis(), 1, 0);

setContext_cleanup:
	if (uprologue) {
		efree(uprologue);
	}

	if (uepilogue) {
		efree(uepilogue);
	}
}
/* }}} */
#endif /* ICU >= 4.8 */

/* {{{ proto self IntlBidi::setPara(string $paragraph[, int $paraLevel = IntlBidi::DEFAULT_LTR[, string $embeddingLevels]]) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_setpara_arginfo, ZEND_RETURN_VALUE, 1, IS_OBJECT, 0)
	ZEND_ARG_TYPE_INFO(0, paragraph, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, paraLevel, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, embeddingLevels, IS_STRING, 1)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, setPara) {
	zend_string *para, *embeddingLevels = NULL;
	zend_long paraLevel = UBIDI_DEFAULT_LTR;
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	UChar *upara = NULL;
	int32_t upara_len = 0;
	UErrorCode error;

	ZEND_PARSE_PARAMETERS_START(1, 3)
		Z_PARAM_STR(para)
		Z_PARAM_OPTIONAL
		Z_PARAM_LONG(paraLevel)
		Z_PARAM_STR_EX(embeddingLevels, 0, 0)
	ZEND_PARSE_PARAMETERS_END();

	error = U_ZERO_ERROR;
	intl_convert_utf8_to_utf16(&upara, &upara_len, ZSTR_VAL(para), ZSTR_LEN(para), &error);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(objval, "setPara", error);
		goto setPara_cleanup;
	}

	if (embeddingLevels != NULL && ZSTR_LEN(embeddingLevels) > 0) {
		objval->embeddingLevels = (UBiDiLevel*)erealloc(objval->embeddingLevels, ZSTR_LEN(embeddingLevels));
		memcpy(objval->embeddingLevels, ZSTR_VAL(embeddingLevels), ZSTR_LEN(embeddingLevels));
	} else {
		efree(objval->embeddingLevels);
		objval->embeddingLevels = NULL;
	}

	error = U_ZERO_ERROR;
	ubidi_setPara(objval->bidi, upara, upara_len, (UBiDiLevel)paraLevel, objval->embeddingLevels, &error);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(objval, "setPara", error);
		goto setPara_cleanup;
	}

	/* Most recently set paragraph must be retained by us. */
	if (objval->text) {
		efree(objval->text);
	}
	objval->text = upara;
	RETURN_ZVAL(getThis(), 1, 0);

setPara_cleanup:
	if (upara) {
		efree(upara);
	}
}
/* }}} */

/* {{{ proto IntlBidi IntlBidi::setLine(int $start, int $limit) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_setline_arginfo, ZEND_RETURN_VALUE, 2, IS_OBJECT, 0)
	ZEND_ARG_TYPE_INFO(0, start, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, limit, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, setLine) {
	zend_long start, limit;
	php_intl_bidi_object *objval, *lineval;
	UErrorCode error;

	zval retval;
	zend_function * constructor;
	zend_fcall_info fci;
	zend_fcall_info_cache fcc;

	ZEND_PARSE_PARAMETERS_START(2, 2)
		Z_PARAM_LONG(start)
		Z_PARAM_LONG(limit)
	ZEND_PARSE_PARAMETERS_END();


	object_init_ex(return_value, php_intl_bidi_ce);

	constructor = Z_OBJ_HT_P(return_value)->get_constructor(Z_OBJ_P(return_value));

	fci.size = sizeof(fci);
	ZVAL_UNDEF(&fci.function_name);
	fci.object = Z_OBJ_P(return_value);
	fci.retval = &retval;
	fci.param_count = 0;
	fci.params = NULL;
	fci.no_separation = 1;

	fcc.function_handler = constructor;
	fcc.called_scope = Z_OBJCE_P(return_value);
	fcc.object = Z_OBJ_P(return_value);

	zend_call_function(&fci, &fcc);
	zval_ptr_dtor(&retval);

	// 	php_intl_bidi_invokeConstruction(return_value, 0, 0);
	// zval_ptr_dtor(return_value);

	//TODO: keep objval alive while lineval is alive or setPara() gets called on lineval.
	// @see http://icu-project.org/apiref/icu4c/ubidi_8h.html#ac7d96b281cd6ab2d56900bfdc37c808a
	// altough i am not sure about "destroying" or "reusing" (if this is also the case when other functions get called)?
	// it should be right to return a new istance, insteadof overriding a old one, to prevent circular references.
	// also setPara should not be callable while an object gets referenced, or the referenced objects need to be reset.
	// I tried to implement it, but i got stuck on that unset() resets the reference count.


objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	lineval = bidi_object_from_zend_object(Z_OBJ_P(return_value));

	error = U_ZERO_ERROR;
	ubidi_setLine(objval->bidi, start, limit - 1, lineval->bidi, &error);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(objval, "setLine", error);
		goto setLine_cleanup;
	}

	RETURN_ZVAL(return_value, 0, 0);
setLine_cleanup:
	if (return_value) {
		zval_ptr_dtor(return_value);
	}
}
/* }}} */

/* {{{ proto int IntlBidi::getDirection() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getdir_arginfo, ZEND_RETURN_VALUE, 0, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getDirection) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	if (zend_parse_parameters_none_throw() == FAILURE) { return; }
	RETURN_LONG(ubidi_getDirection(objval->bidi));
}
/* }}} */

/* {{{ proto int IntlBidi::getBaseDirection(string $text) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getbasedir_arginfo, ZEND_RETURN_VALUE, 1, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, text, IS_STRING, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getBaseDirection) {
	zend_string *text;
	UChar *utext = NULL;
	int32_t utext_len = 0;
	UErrorCode error;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_STR(text)
	ZEND_PARSE_PARAMETERS_END();

	error = U_ZERO_ERROR;
	intl_convert_utf8_to_utf16(&utext, &utext_len, ZSTR_VAL(text), ZSTR_LEN(text), &error);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(NULL, "getBaseDirection", error);
		goto getBaseDirection_cleanup;
	}

	zend_long result = ubidi_getBaseDirection(utext, utext_len);
	//efree(utext);
	RETURN_LONG(result);

getBaseDirection_cleanup:
	if (utext) {
		efree(utext);
	}
}
/* }}} */

/* {{{ proto int IntlBidi::getParaLevel() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getparalevel_arginfo, ZEND_RETURN_VALUE, 0, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getParaLevel) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	if (zend_parse_parameters_none_throw() == FAILURE) { return; }
	RETURN_LONG(ubidi_getParaLevel(objval->bidi));
}
/* }}} */

/* {{{ proto int IntlBidi::countParagraphs() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_countparas_arginfo, ZEND_RETURN_VALUE, 0, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, countParagraphs) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	if (zend_parse_parameters_none_throw() == FAILURE) { return; }
	RETURN_LONG(ubidi_countParagraphs(objval->bidi));
}
/* }}} */

/* {{{ proto array IntlBidi::getParagraph(int $pos) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getpara_arginfo, ZEND_RETURN_VALUE, 1, IS_ARRAY, 0)
	ZEND_ARG_TYPE_INFO(0, pos, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getParagraph) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_long pos;
	int32_t start = 0, limit = 0, idx;
	UBiDiLevel level = UBIDI_MAX_EXPLICIT_LEVEL;
	UErrorCode error = U_ZERO_ERROR;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_LONG(pos)
	ZEND_PARSE_PARAMETERS_END();

	idx = ubidi_getParagraph(objval->bidi, pos, &start, &limit, &level, &error);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(objval, "getParagraph", error);
		return;
	}

	array_init(return_value);
	add_assoc_long(return_value, "index", idx);
	add_assoc_long(return_value, "start", start);
	add_assoc_long(return_value, "limit", limit);
	add_assoc_long(return_value, "level", level);
}
/* }}} */

/* {{{ proto array IntlBidi::getParagraphByIndex(int $index) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getparabyidx_arginfo, ZEND_RETURN_VALUE, 1, IS_ARRAY, 0)
	ZEND_ARG_TYPE_INFO(0, index, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getParagraphByIndex) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_long idx;
	int32_t start = 0, limit = 0;
	UBiDiLevel level = UBIDI_MAX_EXPLICIT_LEVEL;
	UErrorCode error = U_ZERO_ERROR;

	ZEND_PARSE_PARAMETERS_START(1, 1);
		Z_PARAM_LONG(idx)
	ZEND_PARSE_PARAMETERS_END();

	ubidi_getParagraphByIndex(objval->bidi, idx, &start, &limit, &level, &error);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(objval, "getParagraph", error);
		return;
	}

	array_init(return_value);
	add_assoc_long(return_value, "index", idx);
	add_assoc_long(return_value, "start", start);
	add_assoc_long(return_value, "limit", limit);
	add_assoc_long(return_value, "level", level);
}
/* }}} */

/* {{{ proto int IntlBidi::getLevelAt(int $pos) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getlevelat_arginfo, ZEND_RETURN_VALUE, 1, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, pos, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getLevelAt) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_long pos;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_LONG(pos)
	ZEND_PARSE_PARAMETERS_END();

	RETURN_LONG(ubidi_getLevelAt(objval->bidi, pos));
}
/* }}} */

/* {{{ proto string IntlBidi::getLevels() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getlevels_arginfo, ZEND_RETURN_VALUE, 0, IS_STRING, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getLevels) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_string *ret;
	const UBiDiLevel *levels;
	int32_t len;
	UErrorCode error = U_ZERO_ERROR;

	if (zend_parse_parameters_none_throw() == FAILURE) { return; }

	levels = ubidi_getLevels(objval->bidi, &error);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(objval, "getLevels", error);
		return;
	}
	len = ubidi_getProcessedLength(objval->bidi);
	ret = zend_string_alloc(len, 0);
	memcpy(ZSTR_VAL(ret), levels, len);
	ZSTR_VAL(ret)[len] = 0;
	ZSTR_LEN(ret) = len;
	RETURN_STR(ret);
}
/* }}} */

/* {{{ proto array IntlBidi::getLogicalRun(int $pos) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getlogicalrun_arginfo, ZEND_RETURN_VALUE, 1, IS_ARRAY, 0)
	ZEND_ARG_TYPE_INFO(0, pos, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getLogicalRun) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_long pos;
	int32_t limit = 0;
	UBiDiLevel level = UBIDI_MAX_EXPLICIT_LEVEL;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_LONG(pos)
	ZEND_PARSE_PARAMETERS_END();

	ubidi_getLogicalRun(objval->bidi, pos, &limit, &level);

	array_init(return_value);
	add_assoc_long(return_value, "limit", limit);
	add_assoc_long(return_value, "level", level);
}
/* }}} */

/* {{{ proto int IntlBidi::countRuns() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_countruns_arginfo, ZEND_RETURN_VALUE, 0, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, countRuns) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	UErrorCode error = U_ZERO_ERROR;
	int32_t ret;

	if (zend_parse_parameters_none_throw() == FAILURE) { return; }

	ret = ubidi_countRuns(objval->bidi, &error);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(objval, "countRuns", error);
		return;
	}

	RETURN_LONG(ret);
}
/* }}} */

/* {{{ proto array IntlBidi::getVisualRun(int $index) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getvisualrun_arginfo, ZEND_RETURN_VALUE, 1, IS_ARRAY, 0)
	ZEND_ARG_TYPE_INFO(0, pos, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getVisualRun) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_long idx;
	int32_t start = 0, length = 0;
	UBiDiDirection dir;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_LONG(idx)
	ZEND_PARSE_PARAMETERS_END();

	dir = ubidi_getVisualRun(objval->bidi, idx, &start, &length);

	array_init(return_value);
	add_assoc_long(return_value, "start", start);
	add_assoc_long(return_value, "length", length);
	add_assoc_long(return_value, "direction", dir);
}
/* }}} */

/* {{{ proto int IntlBidi::getVisualIndex(int $index) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getvisualindex_arginfo, ZEND_RETURN_VALUE, 1, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, index, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getVisualIndex) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_long idx;
	int32_t ret;
	UErrorCode error = U_ZERO_ERROR;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_LONG(idx)
	ZEND_PARSE_PARAMETERS_END();

	ret = ubidi_getVisualIndex(objval->bidi, idx, &error);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(objval, "getVisualIndex", error);
		return;
	}

	RETURN_LONG(ret);
}
/* }}} */

/* {{{ proto int IntlBidi::getLogicalIndex(int $index) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getlogicalindex_arginfo, ZEND_RETURN_VALUE, 1, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, index, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getLogicalIndex) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_long idx;
	int32_t ret;
	UErrorCode error = U_ZERO_ERROR;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_LONG(idx)
	ZEND_PARSE_PARAMETERS_END();

	ret = ubidi_getLogicalIndex(objval->bidi, idx, &error);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(objval, "getLogicalIndex", error);
		return;
	}

	RETURN_LONG(ret);
}
/* }}} */

/* {{{ proto array IntlBidi::getLogicalMap() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getlogicalmap_arginfo, ZEND_RETURN_VALUE, 0, IS_ARRAY, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getLogicalMap) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	int32_t len = ubidi_getProcessedLength(objval->bidi), i;
	int32_t *map;
	UErrorCode error = U_ZERO_ERROR;

	if (zend_parse_parameters_none_throw() == FAILURE) { return; }

	map = safe_emalloc(sizeof(int32_t), len, 0);
	ubidi_getLogicalMap(objval->bidi, map, &error);
	if (U_FAILURE(error)) {
		efree(map);
		THROW_UFAILURE(objval, "getLogicalMap", error);
		return;
	}

	array_init(return_value);
	for (i = 0; i < len; ++i) {
		add_index_long(return_value, i, map[i]);
	}

	efree(map);
}
/* }}} */

/* {{{ proto array IntlBidi::getVisualMap() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getvisualmap_arginfo, ZEND_RETURN_VALUE, 0, IS_ARRAY, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getVisualMap) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	int32_t len = ubidi_getResultLength(objval->bidi), i;
	int32_t *map;
	UErrorCode error = U_ZERO_ERROR;

	if (zend_parse_parameters_none_throw() == FAILURE) { return; }

	map = safe_emalloc(sizeof(int32_t), len, 0);
	ubidi_getVisualMap(objval->bidi, map, &error);
	if (U_FAILURE(error)) {
		efree(map);
		THROW_UFAILURE(objval, "getVisualMap", error);
		return;
	}

	array_init(return_value);
	for (i = 0; i < len; ++i) {
		add_index_long(return_value, i, map[i]);
	}

	efree(map);
}
/* }}} */

/* {{{ proto int IntlBidi::getProcessedLength() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getprocessedlen_arginfo, ZEND_RETURN_VALUE, 0, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getProcessedLength) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	if (zend_parse_parameters_none_throw() == FAILURE) { return; }
	RETURN_LONG(ubidi_getProcessedLength(objval->bidi));
}
/* }}} */

/* {{{ proto int IntlBidi::getResultLength() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getresultlen_arginfo, ZEND_RETURN_VALUE, 0, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getResultLength) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	if (zend_parse_parameters_none_throw() == FAILURE) { return; }
	RETURN_LONG(ubidi_getResultLength(objval->bidi));
}
/* }}} */

/* {{{ proto int IntlBidi::getCustomizedClass(string $char) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getcustomclass_arginfo, ZEND_RETURN_VALUE, 1, IS_LONG, 0)
	ZEND_ARG_TYPE_INFO(0, character, IS_STRING, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getCustomizedClass) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_string *text;
	int32_t pos = 0;
	size_t len;
	UChar32 c;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_STR(text)
	ZEND_PARSE_PARAMETERS_END();

	if (ZSTR_LEN(text) > 4) {
		php_intl_bidi_throw_failure(NULL, U_ILLEGAL_ARGUMENT_ERROR,
			"IntlChar::getCustomizedClass() requires precisely one unicode character as input");
		return;
	}

	len = ZSTR_LEN(text);

	U8_NEXT(ZSTR_VAL(text), pos, len, c);
	if ((size_t)pos != len) {
		php_intl_bidi_throw_failure(NULL, U_ILLEGAL_ARGUMENT_ERROR,
			"IntlChar::getCustomizedClass() requires precisely one unicode character as input");
		return;
	}

	RETURN_LONG(ubidi_getCustomizedClass(objval->bidi, c));
}
/* }}} */

/* {{{ proto string IntlBidi::getReordered(int $options) */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getreordered_arginfo, ZEND_RETURN_VALUE, 1, IS_STRING, 0)
	ZEND_ARG_TYPE_INFO(0, options, IS_LONG, 0)
ZEND_END_ARG_INFO();
static PHP_METHOD(IntlBidi, getReordered) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	zend_long options;
	UChar *utext;
	int32_t utext_len;
	zend_string *ret;
	UErrorCode error;

	ZEND_PARSE_PARAMETERS_START(1, 1)
		Z_PARAM_LONG(options)
	ZEND_PARSE_PARAMETERS_END();

	if (options & UBIDI_INSERT_LRM_FOR_NUMERIC) {
		error = U_ZERO_ERROR;
		utext_len = ubidi_getLength(objval->bidi) + (2 * ubidi_countRuns(objval->bidi, &error));
		if (U_FAILURE(error)) {
			THROW_UFAILURE(objval, "getReordered", error);
			return;
		}
	} else if (options & UBIDI_REMOVE_BIDI_CONTROLS) {
		utext_len = ubidi_getLength(objval->bidi);
	} else {
		utext_len = ubidi_getProcessedLength(objval->bidi);
	}

	utext = safe_emalloc(sizeof(UChar), utext_len, sizeof(UChar));
	error = U_ZERO_ERROR;
	utext_len = ubidi_writeReordered(objval->bidi, utext, utext_len + 1, options, &error);
	if (U_FAILURE(error)) {
		efree(utext);
		THROW_UFAILURE(objval, "getReordered", error);
		return;
	}

	// HERE IT CRASHES WHEN RUNNING IntlBidi_getReordered_variant.phpt (not enough memory allocated for the string).
	error = U_ZERO_ERROR;
	ret = intl_convert_utf16_to_utf8(utext, utext_len, &error);
	efree(utext);
	if (U_FAILURE(error)) {
		THROW_UFAILURE(objval, "getReordered", error);
		return;
	}

	RETURN_STR(ret);
}
/* }}} */

// TODO: leave this in ??? this is a new feature.
/* {{{ proto int IntlBidi::getLength() */
ZEND_BEGIN_ARG_WITH_RETURN_TYPE_INFO_EX(bidi_getlength_arginfo, ZEND_RETURN_VALUE, 0, IS_LONG, 0)
ZEND_END_ARG_INFO()
static PHP_METHOD(IntlBidi, getLength) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(Z_OBJ_P(getThis()));
	if (zend_parse_parameters_none_throw() == FAILURE) { return; }
	RETURN_LONG(ubidi_getLength(objval->bidi));
}

static zend_function_entry bidi_methods[] = {
	PHP_ME(IntlBidi, __construct, bidi_ctor_arginfo, ZEND_ACC_CTOR | ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, setInverse, bidi_setinverse_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, isInverse, bidi_isinverse_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, orderParagraphsLTR, bidi_orderparaltr_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, isOrderParagraphsLTR, bidi_isorderparaltr_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, setReorderingMode, bidi_setreordermode_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getReorderingMode, bidi_getreordermode_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, setReorderingOptions, bidi_setreorderopts_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getReorderingOptions, bidi_getreorderopts_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getLength, bidi_getlength_arginfo, ZEND_ACC_PUBLIC)
#if ((U_ICU_VERSION_MAJOR_NUM * 10) + U_ICU_VERSION_MINOR_NUM) >= 48
	PHP_ME(IntlBidi, setContext, bidi_setctx_arginfo, ZEND_ACC_PUBLIC)
#endif
	PHP_ME(IntlBidi, setPara, bidi_setpara_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, setLine, bidi_setline_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getDirection, bidi_getdir_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getBaseDirection, bidi_getbasedir_arginfo, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)
	PHP_ME(IntlBidi, getParaLevel, bidi_getparalevel_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, countParagraphs, bidi_countparas_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getParagraph, bidi_getpara_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getParagraphByIndex, bidi_getparabyidx_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getLevelAt, bidi_getlevelat_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getLevels, bidi_getlevels_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getLogicalRun, bidi_getlogicalrun_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, countRuns, bidi_countruns_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getVisualRun, bidi_getvisualrun_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getVisualIndex, bidi_getvisualindex_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getLogicalIndex, bidi_getlogicalindex_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getLogicalMap, bidi_getlogicalmap_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getVisualMap, bidi_getvisualmap_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getProcessedLength, bidi_getprocessedlen_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getResultLength, bidi_getresultlen_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getCustomizedClass, bidi_getcustomclass_arginfo, ZEND_ACC_PUBLIC)
	PHP_ME(IntlBidi, getReordered, bidi_getreordered_arginfo, ZEND_ACC_PUBLIC)

	PHP_FE_END
};

/***************************************************************************/
/* Module Handlers */

static zend_object *bidi_object_ctor(zend_class_entry *ce) {
	php_intl_bidi_object *objval;

	objval = zend_object_alloc(sizeof(php_intl_bidi_object), ce);

	zend_object_std_init(&objval->std, ce);
	object_properties_init(&objval->std, ce);
	intl_error_init(&(objval->error));

	objval->std.handlers = &bidi_object_handlers;

	return bidi_object_to_zend_object(objval);
}

static void bidi_object_dtor(zend_object *obj) {
	php_intl_bidi_object *objval = bidi_object_from_zend_object(obj);

	if (objval->bidi) { ubidi_close(objval->bidi); }
	if (objval->prologue) { efree(objval->prologue); }
	if (objval->text)     { efree(objval->text); }
	if (objval->epilogue) { efree(objval->epilogue); }
	if (objval->embeddingLevels) { efree(objval->embeddingLevels); }

	intl_error_reset(&(objval->error));
}

PHP_MINIT_FUNCTION(intl_bidi) {
	zend_class_entry ce;

	INIT_CLASS_ENTRY(ce, "IntlBidi", bidi_methods);
	php_intl_bidi_ce = zend_register_internal_class(&ce);

	php_intl_bidi_ce->create_object = bidi_object_ctor;
	memcpy(&bidi_object_handlers, &std_object_handlers, sizeof(zend_object_handlers));
	bidi_object_handlers.offset = XtOffsetOf(php_intl_bidi_object, std);
	bidi_object_handlers.clone_obj = NULL;
	bidi_object_handlers.dtor_obj = bidi_object_dtor;

#define BIDI_CNS(x) \
	zend_declare_class_constant_long(php_intl_bidi_ce, #x, strlen(#x), UBIDI_##x)
	BIDI_CNS(DEFAULT_LTR);
	BIDI_CNS(DEFAULT_RTL);
	BIDI_CNS(MAX_EXPLICIT_LEVEL);
	BIDI_CNS(LEVEL_OVERRIDE);
	BIDI_CNS(MAP_NOWHERE);
	BIDI_CNS(KEEP_BASE_COMBINING);
	BIDI_CNS(DO_MIRRORING);
	BIDI_CNS(INSERT_LRM_FOR_NUMERIC);
	BIDI_CNS(REMOVE_BIDI_CONTROLS);
	BIDI_CNS(OUTPUT_REVERSE);
	/* U_BIDI_CLASS_DEFAULT is deprecated */

	/* enum UBiDiDirection */
	BIDI_CNS(LTR);
	BIDI_CNS(RTL);
	BIDI_CNS(MIXED);
#if ((U_ICU_VERSION_MAJOR_NUM * 10) + U_ICU_VERSION_MINOR_NUM) >= 46
	BIDI_CNS(NEUTRAL);
#endif

	/* enum UBiDiReorderingMode */
	BIDI_CNS(REORDER_DEFAULT);
	BIDI_CNS(REORDER_NUMBERS_SPECIAL);
	BIDI_CNS(REORDER_GROUP_NUMBERS_WITH_R);
	BIDI_CNS(REORDER_RUNS_ONLY);
	BIDI_CNS(REORDER_INVERSE_NUMBERS_AS_L);
	BIDI_CNS(REORDER_INVERSE_LIKE_DIRECT);
	BIDI_CNS(REORDER_INVERSE_FOR_NUMBERS_SPECIAL);
	/* UBIDI_REORDER_COUNT is deprecated */

	/* enum UBiDiReorderingOption */
	BIDI_CNS(OPTION_DEFAULT);
	BIDI_CNS(OPTION_INSERT_MARKS);
	BIDI_CNS(OPTION_REMOVE_CONTROLS);
	BIDI_CNS(OPTION_STREAMING);
#undef BIDI_CNS

	return SUCCESS;
}
