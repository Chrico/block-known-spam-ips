<?php # -*- coding: utf-8 -*-
/**
 * Plugin Name: Block Known Spam IPs
 * Description: Rejects comments from IP addresses already found in your spam list.
 * Plugin URI:  https://github.com/Chrico/block-known-spam-ips
 * Version:     1.0
 * Author:      Christian Brückner
 * Author URI:  https://www.chrico.info
 * Licence:     GPL 2
 * License URI: http://opensource.org/licenses/GPL-2.0
 */

add_filter( 'preprocess_comment', 'chrico_block_known_spam_ips' );

/**
 * Look up comment IP in DB and reject if spam from this IP was found.
 *
 * @wp-hook preprocess_comment
 *
 * @param   array $commentdata
 *
 * @return  array|void Dies on success, returns the comment data otherwise
 */
function chrico_block_known_spam_ips( $commentdata ) {
	global $wpdb;

	// Use the same code as WordPress, because that is what we find in our database.
	$ip = preg_replace( '/[^0-9a-fA-F:., ]/', '', $_SERVER[ 'REMOTE_ADDR' ] );

	$sql = "SELECT comment_author_IP FROM %s WHERE comment_author_IP = '%s' AND comment_approved = 'spam' LIMIT 1";
	$sql = $wpdb->prepare( $sql, $wpdb->comments, $ip );

	$match = $wpdb->get_results( $sql );

	if ( empty ( $match ) ) // nothing found
	{
		return $commentdata;
	}

	// you could send a redirect here instead.
	exit; // spammer detected
}