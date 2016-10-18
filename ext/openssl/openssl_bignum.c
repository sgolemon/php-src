/*
   +----------------------------------------------------------------------+
   | PHP Version 7                                                        |
   +----------------------------------------------------------------------+
   | Copyright (c) 1997-2016 The PHP Group                                |
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

/* $Id$ */

#ifdef HAVE_CONFIG_H
#include "config.h"
#endif

#include "php.h"
#include "php_openssl.h"
#include "zend_exceptions.h"

#include <openssl/bn.h>

zend_class_entry *php_openssl_bignum_ce;
static zend_object_handlers php_openssl_bignum_handlers;

#define PHP_OPENSSL_BIGNUM_INTDIV (ZEND_VM_LAST_OPCODE + 0x100000)
#define PHP_OPENSSL_BIGNUM_GCD    (ZEND_VM_LAST_OPCODE + 0x100001)

typedef struct _php_openssl_bignum_object {
	BIGNUM *num;
	zend_object std;
} php_openssl_bignum_object;

static inline php_openssl_bignum_object*
php_openssl_bignum_from_zend_object(zend_object *zobj) {
	return (php_openssl_bignum_object*)(
		((char*)zobj) - XtOffsetOf(php_openssl_bignum_object, std)
	);
}

static inline void php_openssl_bignum_type_error(int argnum, zend_uchar type) {
	char *message;
	const char *subtype = "";

	if (type == IS_STRING) {
		subtype = "non-numeric ";
	}

	spprintf(&message, 0, "OpenSSL\\BigNum::%s() expects parameter %d to be "
	                      "an instance of OpenSSL\\BigNum or numeric integer, %s%s given",
	                      get_active_function_name(), argnum,
	                      subtype, zend_get_type_by_const(type));
	zend_throw_exception(zend_ce_type_error, message, 0);
	efree(message);
}

/* {{{ php_openssl_parse_arg */
static BIGNUM* php_openssl_parse_arg(zval *arg, zval *dtor, int argnum) {
	BIGNUM *bn = NULL;

	ZVAL_UNDEF(dtor);
	if (zend_parse_arg_object(arg, &arg, php_openssl_bignum_ce, 0)) {
		return php_openssl_bignum_from_zend_object(Z_OBJ_P(arg))->num;
	}

	{
		zval tmp;
		int parsed_len;
		ZVAL_ZVAL(&tmp, arg, 1, 0);
		convert_to_string(&tmp);
		if ((Z_STRLEN(tmp) >= 2) && !strncmp(Z_STRVAL(tmp), "0x", 2)) {
			parsed_len = 2 + BN_hex2bn(&bn, Z_STRVAL(tmp) + 2);
		} else {
			parsed_len = BN_dec2bn(&bn, Z_STRVAL(tmp));
		}
		if (parsed_len != Z_STRLEN(tmp)) { BN_clear_free(bn); bn = NULL; }
		zval_dtor(&tmp);
	}

	if (bn) {
		object_init_ex(dtor, php_openssl_bignum_ce);
		php_openssl_bignum_from_zend_object(Z_OBJ_P(dtor))->num = bn;
	} else {
		php_openssl_bignum_type_error(argnum, Z_TYPE_P(arg));
	}

	return bn;
}
/* }}} */

/* Methods */

/* {{{ proto void OpenSSL\Bignum::__construct([string $val = '0']) */
ZEND_BEGIN_ARG_INFO(openssl_bignum_construct_arginfo, 0)
	ZEND_ARG_INFO(0, value)
ZEND_END_ARG_INFO();
static PHP_METHOD(BigNum, __construct) {
	php_openssl_bignum_object *objval = php_openssl_bignum_from_zend_object(Z_OBJ_P(getThis()));
	zend_string *val = NULL;
	int parsed_len;

	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "|S", &val) == FAILURE) {
		return;
	}

	if (!val) {
		BN_dec2bn(&(objval->num), "0");
		return;
	}

	if ((ZSTR_LEN(val) >= 2) && !strncmp(ZSTR_VAL(val), "0x", 2)) {
		parsed_len = 2 + BN_hex2bn(&(objval->num), ZSTR_VAL(val) + 2);
	} else {
		parsed_len = BN_dec2bn(&(objval->num), ZSTR_VAL(val));
	}

	if (parsed_len != ZSTR_LEN(val)) {
		php_openssl_bignum_type_error(1, IS_STRING);
	}
}
/* }}} */

/* {{{ proto BigNum OpenSSL\BigNum::createFromBinary(string $bin) */
ZEND_BEGIN_ARG_INFO_EX(openssl_bignum_create_from_binary_arginfo, 0, ZEND_RETURN_VALUE, 1)
	ZEND_ARG_INFO(0, bin)
ZEND_END_ARG_INFO();
static PHP_METHOD(BigNum, createFromBinary) {
	zend_string *bin;
	php_openssl_bignum_object *objval;

	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "S", &bin) == FAILURE) {
		return;
	}

	object_init_ex(return_value, php_openssl_bignum_ce);
	objval = php_openssl_bignum_from_zend_object(Z_OBJ_P(return_value));
	objval->num = BN_bin2bn((const unsigned char*)ZSTR_VAL(bin), ZSTR_LEN(bin), NULL);
}
/* }}} */

/* {{{ do_openssl_bignum_binary_op */
static inline char php_openssl_bignum_op_needs_ctx(int op) {
	return (op == ZEND_MUL) || (op == ZEND_DIV) ||
	       (op == ZEND_MOD) || (op == ZEND_POW) ||
	       (op == PHP_OPENSSL_BIGNUM_INTDIV) ||
	       (op == PHP_OPENSSL_BIGNUM_GCD);
}

ZEND_BEGIN_ARG_INFO_EX(openssl_bignum_binary_op_arginfo, 0, ZEND_RETURN_VALUE, 1)
	ZEND_ARG_INFO(0, val)
ZEND_END_ARG_INFO();
static void do_openssl_bignum_binary_op(INTERNAL_FUNCTION_PARAMETERS, int op) {
	zval tmp, *arg;
	BIGNUM *bna = php_openssl_bignum_from_zend_object(Z_OBJ_P(getThis()))->num, *bnb, *bnr;
	BN_CTX *ctx = NULL;

	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "z", &arg) == FAILURE) { return; }
	if (!(bnb = php_openssl_parse_arg(arg, &tmp, 1))) { return; }

	if (php_openssl_bignum_op_needs_ctx(op)) {
		ctx = BN_CTX_new();
		if (!ctx) {
			php_error(E_ERROR, "Unable to allocate bignum context");
			RETURN_NULL();
		}
	}

	if (op != ZEND_SPACESHIP) {
		object_init_ex(return_value, php_openssl_bignum_ce);
		bnr = php_openssl_bignum_from_zend_object(Z_OBJ_P(return_value))->num;
	}
	switch (op) {
		case ZEND_ADD: BN_add(bnr, bna, bnb); break;
		case ZEND_SUB: BN_sub(bnr, bna, bnb); break;
		case ZEND_MUL: BN_mul(bnr, bna, bnb, ctx); break;
		case PHP_OPENSSL_BIGNUM_INTDIV: BN_div(bnr, NULL, bna, bnb, ctx); break;
		case ZEND_DIV: /* returns tuple(div, rem) */ {
			zval div = *return_value, rem;
			object_init_ex(&rem, php_openssl_bignum_ce);
			BN_div(php_openssl_bignum_from_zend_object(Z_OBJ(div))->num,
			       php_openssl_bignum_from_zend_object(Z_OBJ(rem))->num,
			       bna, bnb, ctx);

			array_init(return_value);
			add_index_zval(return_value, 0, &div);
			add_index_zval(return_value, 1, &rem);
			break;
		}
		case ZEND_MOD: BN_mod(bnr, bna, bnb, ctx); break;
		case ZEND_POW: BN_exp(bnr, bna, bnb, ctx); break;
		case PHP_OPENSSL_BIGNUM_GCD: BN_gcd(bnr, bna, bnb, ctx); break;
		case ZEND_SPACESHIP: RETVAL_LONG(BN_cmp(bna, bnb)); break;

		default:
			php_error_docref(NULL, E_ERROR, "Invalid OpenSSL\\BigNum binary op: %d", op);
			zval_dtor(return_value);
			RETVAL_NULL();
			ZEND_ASSERT(0);
	}

	if (ctx) {
		BN_CTX_free(ctx);
	}
	zval_dtor(&tmp);
}
/* }}} */

/* {{{ proto BigNum OpenSSL\BigNum::add(BigNum $val) */
static PHP_METHOD(BigNum, add) {
	do_openssl_bignum_binary_op(INTERNAL_FUNCTION_PARAM_PASSTHRU, ZEND_ADD);
}
/* }}} */

/* {{{ proto BigNum OpenSSL\BigNum::sub(BigNum $val) */
static PHP_METHOD(BigNum, sub) {
	do_openssl_bignum_binary_op(INTERNAL_FUNCTION_PARAM_PASSTHRU, ZEND_SUB);
}
/* }}} */

/* {{{ proto BigNum OpenSSL\BigNum::mul(BigNum $val) */
static PHP_METHOD(BigNum, mul) {
	do_openssl_bignum_binary_op(INTERNAL_FUNCTION_PARAM_PASSTHRU, ZEND_MUL);
}
/* }}} */

/* {{{ proto array OpenSSL\BigNum::div(BigNum $val) */
static PHP_METHOD(BigNum, div) {
	do_openssl_bignum_binary_op(INTERNAL_FUNCTION_PARAM_PASSTHRU, ZEND_DIV);
}
/* }}} */

/* {{{ proto BigNum OpenSSL\BigNum::intdiv(BigNum $val) */
static PHP_METHOD(BigNum, intdiv) {
	do_openssl_bignum_binary_op(INTERNAL_FUNCTION_PARAM_PASSTHRU, PHP_OPENSSL_BIGNUM_INTDIV);
}
/* }}} */

/* {{{ proto BigNum OpenSSL\BigNum::mod(BigNum $val) */
static PHP_METHOD(BigNum, mod) {
	do_openssl_bignum_binary_op(INTERNAL_FUNCTION_PARAM_PASSTHRU, ZEND_MOD);
}
/* }}} */

/* {{{ proto BigNum OpenSSL\BigNum::pow(BigNum $val) */
static PHP_METHOD(BigNum, pow) {
	do_openssl_bignum_binary_op(INTERNAL_FUNCTION_PARAM_PASSTHRU, ZEND_POW);
}
/* }}} */

/* {{{ proto int OpenSSL\BigNum::cmp(BigNum $val) */
static PHP_METHOD(BigNum, cmp) {
	do_openssl_bignum_binary_op(INTERNAL_FUNCTION_PARAM_PASSTHRU, ZEND_SPACESHIP);
}
/* }}} */

/* {{{ proto BigNum OpenSSL\BigNum::gcd(BigNum $val) */
static PHP_METHOD(BigNum, gcd) {
	do_openssl_bignum_binary_op(INTERNAL_FUNCTION_PARAM_PASSTHRU, PHP_OPENSSL_BIGNUM_GCD);
}
/* }}} */

/* {{{ proto BigNum OpenSSL\BigNum::shl(int $val) */
static PHP_METHOD(BigNum, shl) {
	BIGNUM *a = php_openssl_bignum_from_zend_object(Z_OBJ_P(getThis()))->num;
	zend_long b;
	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "l", &b) == FAILURE) { return; }
	object_init_ex(return_value, php_openssl_bignum_ce);
	BN_lshift(php_openssl_bignum_from_zend_object(Z_OBJ_P(return_value))->num, a, b);
}
/* }}} */

/* {{{ proto BigNum OpenSSL\BigNum::shr(int $val) */
static PHP_METHOD(BigNum, shr) {
	BIGNUM *a = php_openssl_bignum_from_zend_object(Z_OBJ_P(getThis()))->num;
	zend_long b;
	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "l", &b) == FAILURE) { return; }
	object_init_ex(return_value, php_openssl_bignum_ce);
	BN_rshift(php_openssl_bignum_from_zend_object(Z_OBJ_P(return_value))->num, a, b);
}
/* }}} */

/* {{{ proto BigNum OpenSSL\BigNum::powmod(BigNum $exp, BigNum $mod) */
ZEND_BEGIN_ARG_INFO_EX(openssl_bignum_powmod_arginfo, 0, ZEND_RETURN_VALUE, 2)
    ZEND_ARG_INFO(0, exp)
	ZEND_ARG_INFO(0, mod)
ZEND_END_ARG_INFO();
static PHP_METHOD(BigNum, powmod) {
	zval tmpb, tmpc, *exp, *mod;
	BIGNUM *a = php_openssl_bignum_from_zend_object(Z_OBJ_P(getThis()))->num, *b, *c;
	BN_CTX *ctx = BN_CTX_new();

	if (!ctx) {
		php_error(E_ERROR, "Unable to allocate bignum context");
		RETURN_NULL();
	}

	if (zend_parse_parameters_throw(ZEND_NUM_ARGS(), "zz", &exp, &mod) == FAILURE) { return; }
	if (!(b = php_openssl_parse_arg(exp, &tmpb, 1))) { return; }
	if (!(c = php_openssl_parse_arg(mod, &tmpc, 2))) { zval_dtor(&tmpb); return; }

	object_init_ex(return_value, php_openssl_bignum_ce);
	BN_mod_exp(php_openssl_bignum_from_zend_object(Z_OBJ_P(return_value))->num, a, b, c, ctx);

	zval_dtor(&tmpc);
	zval_dtor(&tmpb);
}
/* }}} */

/* Read Methods */

/* {{{ proto string OpenSSL\BigNum::toDec() */
static PHP_METHOD(BigNum, toDec) {
	php_openssl_bignum_object *objval = php_openssl_bignum_from_zend_object(Z_OBJ_P(getThis()));
	char *strval = BN_bn2dec(objval->num);
	RETVAL_STRING(strval);
	OPENSSL_free(strval);
}
/* }}} */

/* {{{ proto string OpenSSL\BigNum::toHex() */
static PHP_METHOD(BigNum, toHex) {
	php_openssl_bignum_object *objval = php_openssl_bignum_from_zend_object(Z_OBJ_P(getThis()));
	char *strval = BN_bn2hex(objval->num);
	RETVAL_STRING(strval);
	OPENSSL_free(strval);
}
/* }}} */

/* {{{ proto string OpenSSL\BigNum::toBin() */
static PHP_METHOD(BigNum, toBin) {
	php_openssl_bignum_object *objval = php_openssl_bignum_from_zend_object(Z_OBJ_P(getThis()));
	zend_string *strval = zend_string_alloc(BN_num_bytes(objval->num), 0);
	BN_bn2bin(objval->num, (unsigned char*)ZSTR_VAL(strval));
	ZSTR_VAL(strval)[ZSTR_LEN(strval)] = 0;
	RETVAL_STR(strval);
}
/* }}} */

/* {{{ proto array OpenSSL\BigNum::__debugInfo() */
static PHP_METHOD(BigNum, __debugInfo) {
	php_openssl_bignum_object *objval = php_openssl_bignum_from_zend_object(Z_OBJ_P(getThis()));
	char *decval = BN_bn2dec(objval->num);
	char *hexval = BN_bn2hex(objval->num);

	array_init(return_value);
	add_assoc_string(return_value, "dec", decval);
	add_assoc_string(return_value, "hex", hexval);

	OPENSSL_free(hexval);
	OPENSSL_free(decval);
}
/* }}} */

static zend_function_entry openssl_bignum_methods[] = {
	PHP_ME(BigNum, __construct, openssl_bignum_construct_arginfo, ZEND_ACC_PUBLIC | ZEND_ACC_CTOR)
	PHP_ME(BigNum, createFromBinary, openssl_bignum_create_from_binary_arginfo, ZEND_ACC_PUBLIC | ZEND_ACC_STATIC)

	PHP_ME(BigNum, add,    openssl_bignum_binary_op_arginfo,  ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, sub,    openssl_bignum_binary_op_arginfo,  ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, mul,    openssl_bignum_binary_op_arginfo,  ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, div,    openssl_bignum_binary_op_arginfo,  ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, intdiv, openssl_bignum_binary_op_arginfo,  ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, mod,    openssl_bignum_binary_op_arginfo,  ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, pow,    openssl_bignum_binary_op_arginfo,  ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, powmod, openssl_bignum_powmod_arginfo,     ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, cmp,    openssl_bignum_binary_op_arginfo,  ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, gcd,    openssl_bignum_binary_op_arginfo,  ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, shl,    openssl_bignum_binary_op_arginfo,  ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, shr,    openssl_bignum_binary_op_arginfo,  ZEND_ACC_PUBLIC)

	PHP_ME(BigNum, toDec, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, toHex, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, toBin, NULL, ZEND_ACC_PUBLIC)

	PHP_MALIAS(BigNum, __toString, toDec, NULL, ZEND_ACC_PUBLIC)
	PHP_ME(BigNum, __debugInfo, NULL, ZEND_ACC_PUBLIC)
	PHP_FE_END
};

/* Handlers */

/* {{{ php_openssl_bignum_do_operation */
static int openssl_bignum_do_operation(zend_uchar op,
                                       zval *return_value, zval *op1, zval *op2) {
	zval result, op1_tmp, op2_tmp;
	BIGNUM *bn1 = php_openssl_parse_arg(op1, &op1_tmp, -1);
	BIGNUM *bn2, *bnr;
	BN_CTX *ctx = NULL;
	int retval = SUCCESS;

	if ((op == ZEND_SR) || (op == ZEND_SL)) {
		ZVAL_ZVAL(&op2_tmp, op2, 1, 0);
		convert_to_long(&op2_tmp);
	} else {
		bn2 = php_openssl_parse_arg(op2, &op2_tmp, -1);
	}

	if (php_openssl_bignum_op_needs_ctx(op)) {
		ctx = BN_CTX_new();
		if (!ctx) {
			php_error(E_ERROR, "Unable to allocate bignum context");
			RETVAL_NULL();
			return FAILURE;
		}
	}

	object_init_ex(&result, php_openssl_bignum_ce);
	bnr = php_openssl_bignum_from_zend_object(Z_OBJ(result))->num;
	switch (op) {
		case ZEND_ADD: BN_add(bnr, bn1, bn2); break;
		case ZEND_SUB: BN_sub(bnr, bn1, bn2); break;
		case ZEND_MUL: BN_mul(bnr, bn1, bn2, ctx); break;
		case ZEND_DIV: BN_div(bnr, NULL, bn1, bn2, ctx); break;
		case ZEND_MOD: BN_mod(bnr, bn1, bn2, ctx); break;
		case ZEND_POW: BN_exp(bnr, bn1, bn2, ctx); break;
		case ZEND_SR:  BN_rshift(bnr, bn1, Z_LVAL(op2_tmp)); break;
		case ZEND_SL:  BN_lshift(bnr, bn1, Z_LVAL(op2_tmp)); break;

		default:
			BN_zero(bnr);
			retval = FAILURE;
	}

	zval_dtor(&op1_tmp);
	zval_dtor(&op2_tmp);

	if (ctx) {
		BN_CTX_free(ctx);
	}

	zval_dtor(return_value);
	if (retval == SUCCESS) {
		ZVAL_ZVAL(return_value, &result, 1, 1);
	} else {
		zval_dtor(&result);
		ZVAL_NULL(return_value);
	}

	return retval;
}
/* }}} */

static zend_object *openssl_object_create(zend_class_entry *ce, php_openssl_bignum_object **pobjval) {
	php_openssl_bignum_object *objval =
		ecalloc(1, sizeof(php_openssl_bignum_object) + zend_object_properties_size(ce));
	zend_object *zobj = &(objval->std);

	zend_object_std_init(zobj, ce);
	zobj->handlers = &php_openssl_bignum_handlers;
	objval->num = BN_new();

	if (pobjval) { *pobjval = objval; }
	return zobj;
}

static zend_object *openssl_object_ctor(zend_class_entry *ce) {
	return openssl_object_create(ce, NULL);
}

static zend_object *openssl_object_clone(zval *obj) {
	zend_object *zold = Z_OBJ_P(obj);
	php_openssl_bignum_object *oldobj = php_openssl_bignum_from_zend_object(zold);
	php_openssl_bignum_object *newobj;
	zend_object *znew = openssl_object_create(Z_OBJCE_P(obj), &newobj);

	newobj->num = BN_dup(oldobj->num);
	zend_objects_clone_members(znew, zold);
	return znew;
}

static void openssl_object_dtor(zend_object *zobj) {
	BN_clear_free(php_openssl_bignum_from_zend_object(zobj)->num);
}

int php_openssl_bignum_minit(INIT_FUNC_ARGS) {
	zend_class_entry ce;

	INIT_CLASS_ENTRY(ce, "OpenSSL\\BigNum", openssl_bignum_methods);
	php_openssl_bignum_ce = zend_register_internal_class(&ce);
	php_openssl_bignum_ce->create_object = openssl_object_ctor;

	memcpy(&php_openssl_bignum_handlers, zend_get_std_object_handlers(), sizeof(zend_object_handlers));
	php_openssl_bignum_handlers.offset = XtOffsetOf(php_openssl_bignum_object, std);
	php_openssl_bignum_handlers.clone_obj = openssl_object_clone;
	php_openssl_bignum_handlers.dtor_obj = openssl_object_dtor;
	php_openssl_bignum_handlers.do_operation = openssl_bignum_do_operation;

	return SUCCESS;
}
