<?php
/***************************************************************
 *  Copyright notice
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class ext_update {
	protected $messageArray = array();

	public function access() {
		return t3lib_div::compat_version('6.0');
	}

	/**
	 * Main update function called by the extension manager.
	 *
	 * @return string
	 */
	public function main() {
		$this->processUpdates();
		return $this->generateOutput();
	}

	/**
	 * Generates output by using flash messages
	 *
	 * @return string
	 */
	protected function generateOutput() {
		$output = '';
		foreach ($this->messageArray as $messageItem) {
			$flashMessage = t3lib_div::makeInstance(
					't3lib_FlashMessage',
					$messageItem[2],
					$messageItem[1],
					$messageItem[0]);
			$output .= $flashMessage->render();
		}

		return $output;
	}
	
	/**
	 * The actual update function. Add your update task in here.
	 *
	 * @return void
	 */
	protected function processUpdates() {
		$this->addDatabaseField('fe_users', 'tx_odsajaxmailsubscription_rid', "varchar(8) DEFAULT '' NOT NULL");
		$this->addDatabaseField('tt_address', 'tx_odsajaxmailsubscription_rid', "varchar(8) DEFAULT '' NOT NULL");
	}
	
	/**
	 * Add a field to database table
	 *
	 * @param  string $table
	 * @param  string $field
	 * @param  string $options
	 * @return int
	 */
	protected function addDatabaseField($table, $field, $options) {
		$title = 'Modify table "' . $table . '": Add field ' . $field;
		$message = '';
		$status = NULL;

		$currentTableFields = $GLOBALS['TYPO3_DB']->admin_get_fields($table);

		if ($currentTableFields[$field]) {
			$message = 'Field ' . $table . ':' . $field . ' already exists.';
			$status = t3lib_FlashMessage::OK;
		} else {
			$sql = 'ALTER TABLE ' . $table . ' ADD ' . $field . ' ' . $options;
			if ($GLOBALS['TYPO3_DB']->admin_query($sql) === FALSE) {
				$message = ' SQL ERROR: ' .  $GLOBALS['TYPO3_DB']->sql_error();
				$status = t3lib_FlashMessage::ERROR;
			} else {
				$message = 'OK!';
				$status = t3lib_FlashMessage::OK;
			}
		}

		$this->messageArray[] = array($status, $title, $message);
		return $status;
	}
}
?>