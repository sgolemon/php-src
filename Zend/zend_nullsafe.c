/*
   +----------------------------------------------------------------------+
   | Zend Engine                                                          |
   +----------------------------------------------------------------------+
   | Copyright (c) Zend Technologies Ltd. (http://www.zend.com)           |
   +----------------------------------------------------------------------+
   | This source file is subject to version 2.00 of the Zend license,     |
   | that is bundled with this package in the file LICENSE, and is        |
   | available through the world-wide-web at the following url:           |
   | http://www.zend.com/license/2_00.txt.                                |
   | If you did not receive a copy of the Zend license and are unable to  |
   | obtain it through the world-wide-web, please send a note to          |
   | license@zend.com so we can mail you a copy immediately.              |
   +----------------------------------------------------------------------+
   | Authors: Sara Golemon <pollita@php.net>                              |
   +----------------------------------------------------------------------+
*/

#include "zend.h"
#include "zend_API.h"

ZEND_API zend_class_entry *zend_ce_nullsafe;
static zend_object_handlers nullsafe_handlers;

static zend_object* nullsafe_ctor(zend_class_entry *ce) {
	zend_object *ret = zend_objects_new(ce);
	ret->handlers = &nullsafe_handlers;
	return ret;
}

static zval *read_prop(zend_object *obj, zend_string *memb, int type, void **cache, zval *rv) {
	ZVAL_NULL(rv);
	return rv;
}

static zval *write_prop(zend_object *obj, zend_string *memb, zval *value, void **cache) {
	/* Passthrough to handle: `$a = $b?->c = $d;` */
	return value;
}

static int has_prop(zend_object *obj, zend_string *memb, int type, void **cache) {
	return 0;
}

static void unset_prop(zend_object *obj, zend_string *memb, void **cache) {}

static zval *get_property_ptr_ptr(zend_object *obj, zend_string *memb, int type, void **cache) {
	/* Force delegation to read/write */
	return NULL;
}

ZEND_BEGIN_ARG_INFO_EX(nullsafe_call_arginfo, 0, ZEND_RETURN_VALUE, 2)
	ZEND_ARG_INFO(0, method)
	ZEND_ARG_ARRAY_INFO(0, args, 0)
ZEND_END_ARG_INFO();

static ZEND_METHOD(Nullsafe, __call) {
	RETURN_NULL();
}

static zend_function_entry nullsafe_methods[] = {
	ZEND_ME(Nullsafe, __call, nullsafe_call_arginfo, ZEND_ACC_PUBLIC)
	ZEND_FE_END
};

void zend_register_nullsafe(void) /* {{{ */
{
	zend_class_entry ce;

	INIT_CLASS_ENTRY(ce, "Nullsafe", nullsafe_methods);
	zend_ce_nullsafe = zend_register_internal_class(&ce);
	zend_ce_nullsafe->create_object = nullsafe_ctor;

	memcpy(&nullsafe_handlers, &std_object_handlers, sizeof(zend_object_handlers));
	nullsafe_handlers.read_property = read_prop;
	nullsafe_handlers.write_property = write_prop;
	nullsafe_handlers.has_property = has_prop;
	nullsafe_handlers.unset_property = unset_prop;
	nullsafe_handlers.get_property_ptr_ptr = get_property_ptr_ptr;
}
/* }}} */
