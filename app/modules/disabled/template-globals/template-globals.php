<?php
namespace TemplateGlobals;

/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 */
function urlbase() {
	return URLBASE;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 */
function dirbase() {
	return DIRBASE;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 */
function dbhost() {
	return DBHOST;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 */
function dbuser() {
	return DBUSER;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 */
function dbpass() {
	return DBPASS;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 */
function dbname() {
	return DBNAME;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 */
function email_from() {
	return EMAIL_FROM;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 */
function email_to() {
	return EMAIL_TO;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 */
function email_cc() {
	return EMAIL_CC;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 */
function email_bcc() {
	return EMAIL_BCC;
}
/**
 * Replaces Global placeholder
 *
 * @return string value of the global
 */
function ga_account() {
	return GA_ACCOUNT;
}

\Sleepy\Hook::applyFilter('render_placeholder_urlbase', '\TemplateGlobals\urlbase');
\Sleepy\Hook::applyFilter('render_placeholder_dirbase', '\TemplateGlobals\dirbase');
\Sleepy\Hook::applyFilter('render_placeholder_dbhost', '\TemplateGlobals\dbhost');
\Sleepy\Hook::applyFilter('render_placeholder_dbuser', '\TemplateGlobals\dbuser');
\Sleepy\Hook::applyFilter('render_placeholder_dbpass', '\TemplateGlobals\dbpass');
\Sleepy\Hook::applyFilter('render_placeholder_dbname', '\TemplateGlobals\dbname');
\Sleepy\Hook::applyFilter('render_placeholder_email_from', '\TemplateGlobals\email_from');
\Sleepy\Hook::applyFilter('render_placeholder_email_to', '\TemplateGlobals\email_to');
\Sleepy\Hook::applyFilter('render_placeholder_email_cc', '\TemplateGlobals\email_cc');
\Sleepy\Hook::applyFilter('render_placeholder_email_bcc', '\TemplateGlobals\email_bcc');
\Sleepy\Hook::applyFilter('render_placeholder_ga_account', '\TemplateGlobals\ga_account');