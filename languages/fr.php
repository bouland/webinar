<?php

	/**
	* Elgg meeting plugin language pack
	*
	* @package ElggGroups
	**/

	$french = array (
	

		'webinar:meeting:access_id' => 'Accès',
		'webinar:meeting:adminPwd' => 'Mot de passe administrateur',
		'webinar:meeting:attendee:title' => "Les participants du meeting",
		'webinar:meeting:default:adminPwd' => 'admin',
		'webinar:meeting:default:description' => '',
		'webinar:meeting:default:title' => 'Meeting virtuel du groupe %s',
		'webinar:meeting:default:userPwd' => 'user',
		'webinar:meeting:default:welcome' => 'Bienvenue au meeting du groupe %s',
		'webinar:meeting:delete:success' => 'Meeting supprimé',
		'webinar:meeting:description' => 'Description',
		'webinar:meeting:edit:save' => 'Enregistrer',
		'webinar:meeting:enable' => 'Activer les meetings virtuel',
		'webinar:meeting:group:menu:new' => 'Créer un meeting virtuel',
		'webinar:meeting:index'  => 'Tous les meetings virtuel du groupe %s',
		'webinar:meeting:isDone' => "Le meeting virtuel est terminé",
		'webinar:meeting:list:attendee' => "Les participants",
		'webinar:meeting:list:registered' => "Les inscrits",
		'webinar:meeting:logoutURL' => 'URL de retour de meeting',
		'webinar:meeting:menu:attend' => "Rejoindre",
		'webinar:meeting:menu:delete' => 'Supprimer',
		'webinar:meeting:menu:edit' => 'Editer',
		'webinar:meeting:menu:new' => 'Créer un nouveau meeting',
		'webinar:meeting:menu:start' => 'Démarrer',
		'webinar:meeting:menu:stop' => 'Stopper',
		'webinar:meeting:menu:subscribe' => "S'inscrire",
		'webinar:meeting:menu:unsubscribe' => "Se désinscrire",
		'webinar:meeting:menu:view' => "Voir les meeting du groupe",
		'webinar:meeting:new' => 'Nouveau meeting virtuel',
		'webinar:meeting:new:river' => 'Nouveau meeting  virtuel dans le groupe %s',
		'webinar:meeting:notify:new' => '[webinar]',
		'webinar:meeting:notrunning' => "PB le meeting n'existe pas sur le serveur",
		'webinar:meeting:profilegroup' => 'Meetings du groupe',
		'webinar:meeting:registered:title' => "Les inscrits au meeting virtuel",
		'webinar:meeting:salt' => 'Security Salt du serveur BigBlueButton',
		'webinar:meeting:serverURL' => 'URL du serveur BigBlueButton',
		'webinar:meeting:settings' => "Erreur : demander à l'admin de vérifier les settings du plugin",
		'webinar:meeting:slot' => " Si oui, choisir un créneau libre : ",
		'webinar:meeting:slot:default' => "Créer un rendez-vous dans l'agenda ?",
		'webinar:meeting:start:failed' => 'Meeting action start failed',
		'webinar:meeting:start:running' => 'Le meeting est déjà lancé',
		'webinar:meeting:start:salterror' => 'Erreur de checksum. le security Salt est il correct ?',
		'webinar:meeting:start:timeout' => "Impossible de joindre le serveur BBB. vérifier l'url PUIS si le service BigBlueButton est démarré.",
		'webinar:meeting:status' => 'état',
		'webinar:meeting:status:cancel' => "annulé",
		'webinar:meeting:status:done' => "terminé",
		'webinar:meeting:status:running' => "en cours",
		'webinar:meeting:status:title' => "Le meeting est ",
		'webinar:meeting:status:upcoming' => "à venir",
		'webinar:meeting:stop:failed' => 'Meeting action stop failed',
		'webinar:meeting:stop:norunning' => "le meeting n'est pas démarré sur le serveur",
		'webinar:meeting:subscribe:duplicate' => "Vous avez déjà fait cette action",
		'webinar:meeting:subscribe:success' => "Votre inscription est enregistrée",
		'webinar:meeting:tags' => 'Mots clés séparés par des virgules',
		'webinar:meeting:title' => 'Titre',
		'webinar:meeting:unsubscribe:impossible' => "Vous n'etiez pas inscrit",
		'webinar:meeting:unsubscribe:success' => "Vous êtes bien désinscrit",
		'webinar:meeting:userPwd' => 'Mot de passe utilisateur',
		'webinar:meeting:welcomeString' => "Message d'accueil",
		'webinar:meeting:write_access_id' => 'Accès en écriture',
		'webinar:settings:help:serverSalt' => 'Par exemple : 667074052cc5e0b27d036b00fd7c7c3c',
		'webinar:settings:help:serverURL' => 'par exemple : http://d2toast.inrialpes.fr/bigbluebutton/',
		'webinar:settings:label:server' => 'Serveur Big Blue Button',
		'webinar:settings:label:serverSalt' => 'Security Salt',
		'webinar:settings:label:serverURL' => 'URL',
		'item:object:meeting' => "Meetings virtuel",
		'webinar:meeting:river:create' => "%s a créé le meeting virtuel",
		'webinar:meeting:river:start' => "Le meeting virtuel %s vient de démarrer !!",
		'webinar:meeting:river:registered:create' => "%s s'est inscrit au meeting",
		'webinar:meeting:river:attendee:create' => "%s participe au meeting",
	
	);
					
	add_translation("fr",$french);

?>
