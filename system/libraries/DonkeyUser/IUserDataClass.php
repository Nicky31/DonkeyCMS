<?php
/*
 * Interface de toutes les classes utilisateurs personalisées 
 */

interface IUserDataClass
{
	// Renvoit le nom de la table contenant les données personalisées
	public static function datasTableName();

	/*
	 * Renvoit un tableau associatif associant les colonnes de la table de données
	 * aux valeurs de l'objet courant
	 * $array['nomColonne'] = 'valeur';
	 */
	public function columnsBinds();	

	public function __construct($datas);
}