<?php

# Copyright (c) 2012 John Reese
# Licensed under the MIT license

if ( false === include_once( config_get( 'plugin_path' ) . 'Source/MantisSourcePlugin.class.php' ) ) {
	return;
}

require_once( config_get( 'core_path' ) . 'json_api.php' );

class SourceGithubPlugin extends MantisSourcePlugin {

	const ERROR_INVALID_PRIMARY_BRANCH = 'invalid_branch';

	public function register() {
		$this->name = plugin_lang_get( 'title' );
		$this->description = plugin_lang_get( 'description' );

		$this->version = '0.1';
		$this->requires = array(
			'MantisCore' => '1.2.16',
			'Source' => '0.18',
		);

		$this->author = 'Thomas Ziegler';
		$this->contact = 'zieglert@gmx';
		$this->url = 'http://ziegler.bz';
	}

	public function errors() {
		$t_errors_list = array(
			self::ERROR_INVALID_PRIMARY_BRANCH,
		);
		foreach( $t_errors_list as $t_error ) {
			$t_errors[$t_error] = plugin_lang_get( 'error_' . $t_error );
		}
		return $t_errors;
	}

	/**
	 * A short, unique, lowercase string representing the plugin's source control type.
	 */
	public $type = 'vso';

	/**
	 * Get a long, proper string representing the plugin's source control type.
	 * Should be localized if possible.
	 * @return string Source control name
	 */
	public function show_type() {
		return plugin_lang_get( 'vso' );
	}

	/**
	 * Override this to "true" if there are configuration options for the vcs plugin.
	 */
	public $configuration  = false;

	/**
	 * Get a string representing the given repository and changeset.
	 * @param object Repository
	 * @param object Changeset
	 * @return string Changeset string
	 */
	function show_changeset( $p_repo, $p_changeset ) {
		return $p_repo->type . ' ' . $p_changeset->revision;
	}

	/**
	 * Get a string representing a file for a given repository and changeset.
	 * @param object Repository
	 * @param object Changeset
	 * @param object File
	 * @return string File string
	 */
	function show_file( $p_repo, $p_changeset, $p_file ) {
		return $p_file->filename . ' (' . $p_file->revision . ')';
	}

	/**
	 * Get a URL to a view of the repository at the given changeset.
	 * @param object Repository
	 * @param object Changeset
	 * @return string URL
	 */
	function url_repo( $p_repo, $t_changeset=null ) {
		return $p_repo->url;
	}

	/**
	 * Get a URL to a diff view of the given changeset.
	 * @param object Repository
	 * @param object Changeset
	 * @return string URL
	 */
	function url_changeset( $p_repo, $p_changeset ) {
		return $p_repo->url;
	}

	/**
	 * Get a URL to a view of the given file at the given changeset.
	 * @param object Repository
	 * @param object Changeset
	 * @param object File
	 * @return string URL
	 */
	function url_file( $p_repo, $p_changeset, $p_file ) {
		return $p_repo->url;
	}

	/**
	 * Get a URL to a diff view of the given file at the given changeset.
	 * @param object Repository
	 * @param object Changeset
	 * @param object File
	 * @return string URL
	 */
	function url_diff( $p_repo, $p_changeset, $p_file ) {
		return $p_repo->url;
	}

	/**
	 * Output form elements for custom repository data.
	 * @param object Repository
	 */
	public function update_repo_form( $p_repo ) {
		$t_vso_basic_login = null;
		$t_vso_basic_pwd   = null;
		$t_vso_subdomain    = null;
		$t_vso_reponame    = null;

		if( isset($p_repo->info['vso_basic_login']) ) {
			$t_vso_basic_login = $p_repo->info['vso_basic_login'];
		}
		if( isset($p_repo->info['vso_basic_pwd']) ) {
			$t_vso_basic_pwd = $p_repo->info['vso_basic_pwd'];
		}

		if( isset($p_repo->info['vso_subdomain']) ) {
			$t_vso_subdomain = $p_repo->info['vso_subdomain'];
		}

		if( isset($p_repo->info['vso_reponame']) ) {
			$t_vso_reponame = $p_repo->info['vso_reponame'];
		}

		if( isset($p_repo->info['master_branch']) ) {
			$t_master_branch = $p_repo->info['master_branch'];
		} else {
			$t_master_branch = 'master';
		}
		?>
		<tr <?php echo helper_alternate_class() ?>>
			<td class="category"><?php echo plugin_lang_get( 'vso_basic_login' ) ?></td>
			<td><input name="vso_basic_login" maxlength="250" size="40"
					   value="<?php echo string_attribute( $t_vso_basic_login ) ?>"/></td>
		</tr>
		<tr <?php echo helper_alternate_class() ?>>
			<td class="category"><?php echo plugin_lang_get( 'vso_basic_pwd' ) ?></td>
			<td><input type="password" name="vso_basic_pwd" maxlength="250" size="40"
					   value="<?php echo string_attribute( $t_vso_basic_pwd ) ?>"/></td>
		</tr>
		<tr <?php echo helper_alternate_class() ?>>
			<td class="category"><?php echo plugin_lang_get( 'vso_subdomain' ) ?></td>
			<td><input name="vso_subdomain" maxlength="250" size="40"
					   value="<?php echo string_attribute( $t_vso_subdomain ) ?>"/></td>
		</tr>
		<tr <?php echo helper_alternate_class() ?>>
			<td class="category"><?php echo plugin_lang_get( 'vso_reponame' ) ?></td>
			<td><input name="vso_reponame" maxlength="250" size="40"
					   value="<?php echo string_attribute( $t_vso_reponame ) ?>"/></td>
		</tr>
		<tr>
			<td class="spacer"></td>
		</tr>
		<tr <?php echo helper_alternate_class() ?>>
			<td class="category"><?php echo plugin_lang_get( 'master_branch' ) ?></td>
			<td><input name="master_branch" maxlength="250" size="40"
					   value="<?php echo string_attribute( $t_master_branch ) ?>"/></td>
		</tr>
	<?php
	}

	/**
	 * Process form elements for custom repository data.
	 * @param object Repository
	 */
	public function update_repo( $p_repo ) {}

	/**
	 * Output form elements for configuration options.
	 */
	public function update_config_form() {}

	/**
	 * Process form elements for configuration options.
	 */
	public function update_config() {}

	/**
	 * If necessary, check GPC inputs to determine if the checkin data
	 * is for a repository handled by this VCS type.
	 * @return array Array with "repo"=>Repository, "data"=>...
	 */
	public function precommit() {}

	/**
	 * Translate commit data to Changeset objects for the given repo.
	 * @param object Repository
	 * @param mixed Commit data
	 * @return array Changesets
	 */
	public function commit( $p_repo, $p_data ) {}

	/**
	 * Initiate an import of changeset data for the entire repository.
	 * @param object Repository
	 * @return array Changesets
	 */
	public function import_full( $p_repo ) {}

	/**
	 * Initiate an import of changeset data not yet imported.
	 * @param object Repository
	 * @return array Changesets
	 */
	public function import_latest( $p_repo ) {}

	/**
	 * Initialize contact with the integration framework.
	 * @return object The plugin object
	 */
	final public function integration( $p_event ) {
		return $this;
	}

	/**
	 * Pass the precommit event to the interface without the
	 * event paramater.
	 */
	final public function _precommit( $p_event ) {
		return $this->precommit();
	}
}