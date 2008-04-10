<?php
/**
 * class.database.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 */

interface iDatabase {
  public function generateDropTableSQL($sTable);
  public function generateCreateTableSQL($sTable, $aColumns);
  public function generateDropColumnSQL($sTable, $sColumn);
  public function generateAddColumnSQL($sTable, $sColumn, $aParameters);
  public function generateChangeColumnSQL($sTable, $sColumn, $aParameters);
  public function close();
}

class database_base implements iDatabase {
	protected $sType;
	protected $sServer;
	protected $sUser;
	protected $sPass;
	protected $sDataBase;
	protected $oConnection;
	protected $sQuoteCharacter = '';
	protected $sEndLine = ';';
	public function __construct($sType = DB_ADAPTER, $sServer = DB_HOST, $sUser = DB_USER, $sPass = DB_PASS, $sDataBase = DB_NAME) {
		$this->sType           = $sType;
		$this->sServer         = $sServer;
		$this->sUser           = $sUser;
		$this->sPass           = $sPass;
		$this->sDataBase       = $sDataBase;
		$this->oConnection     = null;
		$this->sQuoteCharacter = '';
	}
	public function generateDropTableSQL($sTable) {
		$sSQL = 'DROP TABLE IF EXISTS ' . $this->sQuoteCharacter . $sTable . $this->sQuoteCharacter . $this->sEndLine;
		return $sSQL;
	}
	public function generateCreateTableSQL($sTable, $aColumns) {
	}
	public function generateDropColumnSQL($sTable, $sColumn) {
	}
	public function generateAddColumnSQL($sTable, $sColumn, $aParameters) {
	}
	public function generateChangeColumnSQL($sTable, $sColumn, $aParameters) {
	}
	public function executeQuery($sQuery) {
	}
	public function close() {
	}
}