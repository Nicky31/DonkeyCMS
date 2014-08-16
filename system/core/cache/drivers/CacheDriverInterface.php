<?php

/*
 * Interface implémentée par les drivers de cache 
 */

 interface CacheDriverInterface
 {
    // Renvoit le contenu d'un cache s'il existe sur le support, FALSE dans le cas contraire
    public function getCache($name);
    // Renvoit une boolean selon l'existence et la validité du cache
    public function cacheExists($name);
    // Détruit le cache indiqué. Renvoit TRUE si le cache indiqué existait bien, FALSE dans le cas contraire.
    public function removeCache($name);
    // Met en cache de nouvelles données sur le support, édite le contenu + lifeTime si des données mises en cachent existaient déjà sous ce nom
    public function addCache($name, $datas);
 }