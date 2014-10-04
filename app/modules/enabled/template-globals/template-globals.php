<?php
namespace Module\TemplateGlobals;

/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 * @internal
 */
function urlbase() {
	return URLBASE;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 * @internal
 */
function dirbase() {
	return DIRBASE;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 * @internal
 */
function dbhost() {
	return DBHOST;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 * @internal
 */
function dbuser() {
	return DBUSER;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 * @internal
 */
function dbpass() {
	return DBPASS;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 * @internal
 */
function dbname() {
	return DBNAME;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 * @internal
 */
function email_from() {
	return EMAIL_FROM;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 * @internal
 */
function email_to() {
	return EMAIL_TO;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 * @internal
 */
function email_cc() {
	return EMAIL_CC;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 * @internal
 */
function email_bcc() {
	return EMAIL_BCC;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 * @internal
 */
function ga_account() {
	return GA_ACCOUNT;
}

\Sleepy\Hook::applyFilter('render_placeholder_urlbase', '\Module\TemplateGlobals\urlbase');
\Sleepy\Hook::applyFilter('render_placeholder_dirbase', '\Module\TemplateGlobals\dirbase');
\Sleepy\Hook::applyFilter('render_placeholder_dbhost', '\Module\TemplateGlobals\dbhost');
\Sleepy\Hook::applyFilter('render_placeholder_dbuser', '\Module\TemplateGlobals\dbuser');
\Sleepy\Hook::applyFilter('render_placeholder_dbpass', '\Module\TemplateGlobals\dbpass');
\Sleepy\Hook::applyFilter('render_placeholder_dbname', '\Module\TemplateGlobals\dbname');
\Sleepy\Hook::applyFilter('render_placeholder_email_from', '\Module\TemplateGlobals\email_from');
\Sleepy\Hook::applyFilter('render_placeholder_email_to', '\Module\TemplateGlobals\email_to');
\Sleepy\Hook::applyFilter('render_placeholder_email_cc', '\Module\TemplateGlobals\email_cc');
\Sleepy\Hook::applyFilter('render_placeholder_email_bcc', '\Module\TemplateGlobals\email_bcc');
\Sleepy\Hook::applyFilter('render_placeholder_ga_account', '\Module\TemplateGlobals\ga_account');